<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);


include './database/db_connection.php';
session_start();


// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch branch_id, country, state, and role for the logged-in user
$loggedInUserBranchId = null;
$loggedInUserCountry = null;
$loggedInUserState = null;
$loggedInUserRole = $_SESSION['role'] ?? null;

if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT l.branch_id, b.country, b.state, l.role FROM login l LEFT JOIN branches b ON l.branch_id = b.branch_id WHERE l.id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $userData = $result->fetch_assoc();
    if ($userData) {
        $loggedInUserBranchId = $userData['branch_id'];
        $loggedInUserCountry = $_SESSION['country'];
        $loggedInUserState = $_SESSION['state'];
        $loggedInUserRole = $userData['role'];
    }
    $stmt->close();
}

// Fetch all users
$suppliers = [];
$sql = "SELECT * FROM login WHERE role != 'Superuser' AND role != 'CEO'"; // Exclude Superuser and CEO from general list

$queryParams = [];
$queryTypes = "";

if ($loggedInUserRole !== 'Superuser' && $loggedInUserRole !== 'CEO') {
    if ($loggedInUserCountry !== null) {
        $sql .= " AND country = ?";
        $queryTypes .= "s";
        $queryParams[] = $loggedInUserCountry;
    }
    if ($loggedInUserState !== null) {
        $sql .= " AND state = ?";
        $queryTypes .= "s";
        $queryParams[] = $loggedInUserState;
    }
}

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}

if (!empty($queryParams)) {
    $bind_params = [];
    $bind_params[] = $queryTypes;
    foreach ($queryParams as $key => $value) {
        $bind_params[] = &$queryParams[$key];
    }
    call_user_func_array([$stmt, 'bind_param'], $bind_params);
}

$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $suppliers[] = $row;
    }
} else {
    die("Error fetching Suppliers: " . $conn->error);
}

// Fetch all branches
$branches = [];
$branchQuery = "SELECT branch_id, branch_name FROM branches ORDER BY branch_name ASC";
$branchResult = $conn->query($branchQuery);
if ($branchResult) {
    while ($row = $branchResult->fetch_assoc()) {
        $branches[] = $row;
    }
} else {
    // Log error if branches cannot be fetched, but don't die, as it might be a new setup
    error_log("Error fetching branches: " . $conn->error);
}

// Fetch all distinct countries for dropdown
$countries = [];
$countryQuery = "SELECT DISTINCT country AS name FROM branches ORDER BY name ASC";
$countryResult = $conn->query($countryQuery);
if ($countryResult) {
    while ($row = $countryResult->fetch_assoc()) {
        $countries[] = $row;
    }
} else {
    error_log("Error fetching countries: " . $conn->error);
}

// Fetch all distinct states for dropdown
$states = [];
$stateQuery = "SELECT DISTINCT state AS name FROM branches ORDER BY name ASC";
$stateResult = $conn->query($stateQuery);
if ($stateResult) {
    while ($row = $stateResult->fetch_assoc()) {
        $states[] = $row;
    }
} else {
    error_log("Error fetching states: " . $conn->error);
}


// =============== EDIT MODE ==========================
$editMode = false;
$staffToEdit = [
    'staffid' => '',
    'staffname' => '',
    'username' => '',
    'password' => '',
    'role' => '',
    'branch_id' => '', // Added for branch
    'country' => '',   // Added for country
    'state' => ''      // Added for state
];

if (isset($_GET['id'])) {
    $editId = $_GET['id'];
    // Select country and state as well
    $stmt = $conn->prepare("SELECT *, country, state FROM login WHERE id = ?");
    $stmt->bind_param("i", $editId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $staffToEdit = $result->fetch_assoc();
        $editMode = true;
    }
    $stmt->close();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $staffName = trim($_POST['name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $role = trim($_POST['role'] ?? '');
    $branchId = trim($_POST['branch_id'] ?? null); // Get branch_id from form
    $country = trim($_POST['country'] ?? ''); // Get country from form
    $state = trim($_POST['state'] ?? '');     // Get state from form

    // Validate inputs
    $errors = [];
    if ($staffName === '') $errors[] = 'Staff name is required.';
    if ($username === '') $errors[] = 'Username is required.';
    if ($branchId === null || $branchId === '') $errors[] = 'Branch is required.'; // Validate branch
    if ($country === '') $errors[] = 'Country is required.'; // Validate country
    if ($state === '') $errors[] = 'State/Province is required.'; // Validate state

    // Password validation only for new users or if password is provided during edit
    $isEditMode = isset($_POST['edit_id']) && is_numeric($_POST['edit_id']);
    if (!$isEditMode || ($isEditMode && !empty($password))) {
        if ($password === '') {
            $errors[] = 'Password is required.';
        }
    }

    if (!empty($errors)) {
        foreach ($errors as $err) {
            echo "<p style='color:red;'>Error: {$err}</p>";
        }
        // If in edit mode, repopulate staffToEdit for display
        if ($isEditMode) {
            $editId = $_POST['edit_id'];
            // Select country and state as well for repopulation
            $stmt = $conn->prepare("SELECT *, country, state FROM login WHERE id = ?");
            $stmt->bind_param("i", $editId);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $staffToEdit = $result->fetch_assoc();
                // Manually set branch_id, country, and state if they were submitted but caused an error
                if ($branchId !== null && $branchId !== '') {
                    $staffToEdit['branch_id'] = $branchId;
                }
                $staffToEdit['country'] = $country;
                $staffToEdit['state'] = $state;
            }
            $stmt->close();
        }
        exit; // Stop execution if there are errors
    }

    $hashedPassword = !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : null;

    // Check if it's an edit
    if ($isEditMode) {
        $editId = $_POST['edit_id'];
        $sql = "UPDATE login SET staffname=?, username=?, role=?, branch_id=?, country=?, state=?";
        $queryTypes = "ssssss";
        $queryParams = [$staffName, $username, $role, $branchId, $country, $state];

        if ($hashedPassword !== null) {
            $sql .= ", password=?";
            $queryTypes .= "s";
            $queryParams[] = $hashedPassword;
        }
        $sql .= " WHERE id=?";
        $queryTypes .= "i";
        $queryParams[] = $editId;

        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("Prepare failed: " . $conn->error);
        }
        $bind_params = [];
        $bind_params[] = $queryTypes;
        foreach ($queryParams as $key => $value) {
            $bind_params[] = &$queryParams[$key];
        }
        call_user_func_array([$stmt, 'bind_param'], $bind_params);

    } else {
        // Updated SQL to include branch_id, country, and state
        $sql = "INSERT INTO login (staffname, username, password, role, branch_id, country, state) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        // Updated bind_param to include branch_id, country, and state
        $stmt->bind_param("sssssss", $staffName, $username, $hashedPassword, $role, $branchId, $country, $state);
    }

    if ($stmt->execute()) {
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        echo "<p style='color:red;'>Error saving Staff: " . $stmt->error . "</p>";
        // If there was an error during save, and it was an edit, repopulate staffToEdit
        if (isset($_POST['edit_id']) && is_numeric($_POST['edit_id'])) {
            $editId = $_POST['edit_id'];
            $stmt_repopulate = $conn->prepare("SELECT * FROM login WHERE id = ?");
            $stmt_repopulate->bind_param("i", $editId);
            $stmt_repopulate->execute();
            $result_repopulate = $stmt_repopulate->get_result();
            if ($result_repopulate->num_rows > 0) {
                $staffToEdit = $result_repopulate->fetch_assoc();
                // Manually set branch_id if it was submitted but caused an error
                if ($branchId !== null && $branchId !== '') {
                    $staffToEdit['branch_id'] = $branchId;
                }
            }
            $stmt_repopulate->close();
        }
    }

    $stmt->close();
}

$conn->close();
?>
