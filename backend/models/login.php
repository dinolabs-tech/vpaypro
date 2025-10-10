<?php

$login_error = '';

// Include AuditLog model and instantiate
include_once './database/db_connection.php'; // Ensure db connection is included
include_once 'models/audit_logs.php'; // Include the AuditLog model
require_once '../backend/phpmailer/PHPMailerAutoload.php'; // For sending emails
require_once '../backend/email_sender.php'; // Custom email sender functions
// session_start();
$auditLogModel = new AuditLog($conn); // Instantiate the AuditLog class

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if Superuser exists, if not create one
$check_superuser = $conn->prepare("SELECT id FROM login WHERE role = 'Superuser' LIMIT 1");
$check_superuser->execute();
$check_superuser->store_result();

if ($check_superuser->num_rows == 0) {
    // Superuser doesn't exist, create one
    $stmt_superuser = $conn->prepare("INSERT INTO login (staffname, username, password, role) VALUES (?, ?, ?, ?)");
    $staffname = "Dinolabs Superuser";
    $username = "dinolabs";
    $password = "dinolabs"; // Note: In production, you should hash this password
    // Uncomment the next line to hash the password
    // For simplicity, we are not hashing the password here, but you should do it in production
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $role = "Superuser";
    $stmt_superuser->bind_param("ssss", $staffname, $username, $hashedPassword, $role);
    $stmt_superuser->execute();
    $stmt_superuser->close();
}
$check_superuser->close();

// Existing login logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_GET['action']) && $_GET['action'] == 'verify_2fa') {
        // 2FA verification logic
        $entered_code = $_POST['two_factor_code'];

        if (!isset($_SESSION['2fa_customer_id']) || !isset($_SESSION['2fa_email'])) {
            $login_error = "2FA session expired. Please log in again.";
            // header("Location: index.php"); // Redirect to login page
            // exit;
        } else {
            $customer_id = $_SESSION['2fa_customer_id'];
            $customer_email = $_SESSION['2fa_email'];

            // Fetch customer details from the database
            $stmt = $conn->prepare("SELECT * FROM customers WHERE id = ? AND email = ?");
            $stmt->bind_param("is", $customer_id, $customer_email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $customer = $result->fetch_assoc();
                $stored_code = $customer['two_factor_code'];
                $expires_at = strtotime($customer['two_factor_expires_at']);
                $current_time = time();

                if ($entered_code === $stored_code && $current_time < $expires_at) {
                    // 2FA code is valid and not expired, log the user in
                    $_SESSION['loggedin'] = TRUE;
                    $_SESSION['user_id'] = $customer['id'];
                    $_SESSION['customer_id'] = $customer['customer_id'];
                    $_SESSION['role'] = 'Customer';
                    $_SESSION['name'] = $customer['name'];
                    $_SESSION['email'] = $customer['email'];
                    $_SESSION['phone'] = $customer['phone'];
                    $_SESSION['address'] = $customer['address'];
                    $_SESSION['country'] = $customer['country'];
                    $_SESSION['state'] = $customer['state'];
                    $_SESSION['profile_picture'] = $customer['profile_picture'];

                    // Clear 2FA specific session variables
                    unset($_SESSION['2fa_customer_id']);
                    unset($_SESSION['2fa_email']);

                    // Clear 2FA code from the database
                    $clear_stmt = $conn->prepare("UPDATE customers SET two_factor_code = NULL, two_factor_expires_at = NULL WHERE id = ?");
                    $clear_stmt->bind_param("i", $customer['id']);
                    $clear_stmt->execute();
                    $clear_stmt->close();

                    $auditLogModel->logAction($customer['id'], 'Customer Login (2FA Verified)', $customer['name'] . ' logged in with 2FA.');
                    header("Location: customer_dashboard.php"); // Redirect to customer dashboard
                    exit;
                } else if ($current_time >= $expires_at) {
                    $login_error = "2FA code has expired. Please log in again to receive a new code.";
                    // Optionally clear the expired code from the database
                    $clear_stmt = $conn->prepare("UPDATE customers SET two_factor_code = NULL, two_factor_expires_at = NULL WHERE id = ?");
                    $clear_stmt->bind_param("i", $customer['id']);
                    $clear_stmt->execute();
                    $clear_stmt->close();
                } else {
                    $login_error = "Invalid 2FA code.";
                }
            } else {
                $login_error = "An error occurred during 2FA verification. Please try logging in again.";
            }
            $stmt->close();
        }
    } else {
        $username = $_POST['username'];
        $password = $_POST['password'];

    // Check staff login first
    $stmt_staff = $conn->prepare("SELECT id, staffname, username, password, email, address, mobile, country, state, profile_picture, status, role, branch_id, created_at, updated_at FROM login WHERE username=?");
    $stmt_staff->bind_param("s", $username);
    $stmt_staff->execute();
    $stmt_staff->store_result();

    if ($stmt_staff->num_rows > 0) {
        $stmt_staff->bind_result($id, $staffname, $username_db, $hashed_password_db, $email, $address, $mobile, $country, $state, $profile_picture, $status, $role, $branch_id, $created_at, $updated_at);
        $stmt_staff->fetch();

        if (password_verify($password, $hashed_password_db)) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $id;
            $_SESSION['role'] = $role;
            $_SESSION['staffname'] = $staffname;
            $_SESSION['country'] = $country;
            $_SESSION['state'] = $state;
            $_SESSION['profile_picture'] = $profile_picture;
            $_SESSION['branch_id'] = $branch_id;

            // Regenerate session ID for security
            // Log login

            // Log successful staff login
            $auditLogModel->logAction($id, $_SESSION['role'] . ' Login', $_SESSION['staffname'] . ' logged in.');

            // Redirect based on role
            if ($role == 'Administrator' || $role == 'Superuser' || $role == 'CEO') {
                header("Location: dashboard.php");
            } elseif ($role == 'Cashier') {
                header("Location: pos.php");
            } elseif ($role == 'Delivery') {
                header("Location: admin_orders.php");
            } elseif ($role == 'Inventory Manager') {
                header("Location: inventory.php");
            } elseif ($role == 'Sales Manager') {
                header("Location: pos.php");
            // } elseif ($role == 'Supplier') {
            //     header("Location: supplier_dashboard.php");
            }
            exit();
        }
    }
    $stmt_staff->close();

    // If not a staff, check customer login
    $stmt_customer = $conn->prepare("SELECT id, customer_id, name, email, phone, address, country, state, profile_picture, password, created_at FROM customers WHERE email = ?");
    $stmt_customer->bind_param("s", $username); // Customers use email as username
    $stmt_customer->execute();
    $stmt_customer->store_result();

    if ($stmt_customer->num_rows == 1) {
        $stmt_customer->bind_result($id, $customer_id, $name, $email_from_db, $phone, $address, $country, $state, $profile_picture, $hashed_password, $registration_date);
        if ($stmt_customer->fetch()) {
            if (password_verify($password, $hashed_password)) {
                session_regenerate_id(true);
                $_SESSION['loggedin'] = TRUE;
                $_SESSION['user_id'] = $id;
                $_SESSION['customer_id'] = $customer_id;
                $_SESSION['role'] = 'Customer'; // Set role for customer
                $_SESSION['name'] = $name; // Set name for customer
                $_SESSION['email'] = $email_from_db;
                $_SESSION['phone'] = $phone;
                $_SESSION['address'] = $address;
                $_SESSION['country'] = $country;
                $_SESSION['state'] = $state;
                $_SESSION['profile_picture'] = $profile_picture;


                // Regenerate session ID for security
                // Log login
                // $log_event('login');

                // Log successful customer login
                $auditLogModel->logAction($id, 'Customer Login', $_SESSION['name'] . ' logged in.');

                // Generate 2FA code
                $two_factor_code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                $two_factor_expires_at = date('Y-m-d H:i:s', strtotime('+10 minutes')); // Code valid for 10 minutes

                // Store 2FA code and expiration in the database
                $update_stmt = $conn->prepare("UPDATE customers SET two_factor_code = ?, two_factor_expires_at = ? WHERE id = ?");
                $update_stmt->bind_param("ssi", $two_factor_code, $two_factor_expires_at, $id);
                $update_stmt->execute();
                $update_stmt->close();

                // Send 2FA code to the customer's email
                $subject = "Your 2FA Verification Code";
                $body = "Your 2FA verification code is: <strong>" . $two_factor_code . "</strong>. It will expire in 10 minutes.";
                sendEmail($email_from_db, $name, $subject, $body);

                // Set session variable to indicate 2FA is pending
                $_SESSION['2fa_customer_id'] = $id;
                $_SESSION['2fa_email'] = $email_from_db;

                header("Location: index.php?action=verify_2fa"); // Redirect to backend 2FA verification
                exit();
            }
        }
    }
    $stmt_customer->close();


    // If not a staff, not a customer, check supplier login
    $stmt_supplier = $conn->prepare("SELECT id, name, email, phone, address, country, state, profile_picture, password, created_at FROM suppliers WHERE email = ?");
    $stmt_supplier->bind_param("s", $username); // Suppliers use email as username
    $stmt_supplier->execute();
    $stmt_supplier->store_result();

    if ($stmt_supplier->num_rows == 1) {
        $stmt_supplier->bind_result($id, $name, $email_from_db, $phone, $address, $country, $state, $profile_picture, $hashed_password, $registration_date);
        if ($stmt_supplier->fetch()) {
            if (password_verify($password, $hashed_password)) {
                session_regenerate_id(true);
                $_SESSION['loggedin'] = TRUE;
                $_SESSION['user_id'] = $id;
                $_SESSION['role'] = 'Supplier'; // Set role for Supplier
                $_SESSION['name'] = $name; // Set name for customer
                $_SESSION['email'] = $email_from_db;
                $_SESSION['phone'] = $phone;
                $_SESSION['address'] = $address;
                $_SESSION['country'] = $country;
                $_SESSION['state'] = $state;
                $_SESSION['profile_picture'] = $profile_picture;

                // Regenerate session ID for security
                // Log login
                // $log_event('login');

                // Log successful customer login
                $auditLogModel->logAction($id, 'Supplier Login', $_SESSION['name'] . ' logged in.');

                header('Location: supplier_dashboard.php');
                exit();
            }
        }
    }
    $stmt_supplier->close();



    // If neither login is successful
    $login_error = "Invalid username/email or password.";
    $conn->close();
    } // Closing brace for the else block
}
