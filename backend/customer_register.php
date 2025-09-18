<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);


include 'database/db_connection.php';
session_start();


function generateCustomerIdBase32() {
    $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // No O, I, 0, 1
    $length = 10; // 10 chars = ~50 bits of randomness

    $id = '';
    for ($i = 0; $i < $length; $i++) {
        $id .= $characters[random_int(0, strlen($characters) - 1)];
    }

    // Group for readability
    return 'CUST-' . substr($id, 0, 4) . '-' . substr($id, 4, 3) . '-' . substr($id, 7);
}


// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $country = $_POST['country'];
    $state = $_POST['state'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $customer_id = generateCustomerIdBase32(); // Generate unique customer ID

    $sql = "INSERT INTO customers (customer_id, name, email, phone, address, country, state, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssssssss", $customer_id, $name, $email, $phone, $address, $country, $state, $password);
        if ($stmt->execute()) {
            // Redirect to login page after successful registration
            header("Location: index.php");
            exit();
        } else {
            $error = "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error = "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<?php include('components/head.php'); ?>

<body>

  <main>
    <div class="container">

      <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

              <div class="d-flex justify-content-center py-4">
                <a href="index.php" class="logo d-flex align-items-center w-auto">
                  <!-- <img src="assets/img/logo.png" alt=""> -->
                  <span class="d-none d-lg-block">VPayPro</span>
                </a>
              </div><!-- End Logo -->

              <div class="card mb-3">

                <div class="card-body">

                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Create a Customer Account</h5>
                    <p class="text-center small">Enter your personal details to create account</p>
                  </div>

                  <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                  <?php endif; ?>

                  <form class="row g-3 needs-validation" novalidate method="POST">
                    <div class="col-12">
                      <input type="text" name="name" class="form-control" id="yourName" placeholder="Your Name" required>
                      <div class="invalid-feedback">Please, enter your name!</div>
                    </div>

                    <div class="col-12">
                      <input type="email" name="email" class="form-control" id="yourEmail" placeholder="Your Email" required>
                      <div class="invalid-feedback">Please enter a valid Email adddress!</div>
                    </div>

                    <div class="col-12">
                      <input type="text" name="phone" class="form-control" id="yourPhone" placeholder="Phone" required>
                      <div class="invalid-feedback">Please enter your phone number!</div>
                    </div>

                    <div class="col-12">
                      <textarea name="address" class="form-control" id="yourAddress" placeholder="Address" required></textarea>
                      <div class="invalid-feedback">Please enter your address!</div>
                    </div>

                    <div class="col-12">
                      <input type="text" name="country" class="form-control" id="yourCountry" placeholder="Country" required>
                      <div class="invalid-feedback">Please enter your country!</div>
                    </div>

                    <div class="col-12">
                      <input type="text" name="state" class="form-control" id="yourState" placeholder="State" required>
                      <div class="invalid-feedback">Please enter your state!</div>
                    </div>

                    <div class="col-12">
                      <input type="password" name="password" class="form-control" id="yourPassword" placeholder="Password" required>
                      <div class="invalid-feedback">Please enter your password!</div>
                    </div>

                    <div class="col-12">
                      <button class="btn btn-primary w-100" type="submit">Create Account</button>
                    </div>
                    <div class="col-12">
                      <p class="small mb-0">Already have an account? <a href="index.php">Log in</a></p>
                    </div>
                  </form>

                </div>
              </div>

            </div>
          </div>
        </div>

      </section>

    </div>
  </main><!-- End #main -->

  <?php include('components/script.php'); ?>

</body>

</html>
