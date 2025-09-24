<?php include('models/login.php'); ?>

<!DOCTYPE html>
<html lang="en">
<?php include('components/head.php'); ?>

<body>

  <div class="container">
    <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

            <div class="d-flex justify-content-center py-4">
              <a href="index.php" class="logo d-flex align-items-center w-auto">
                <!-- <img src="assets/img/logo.png" alt=""> -->
                <span class="d-none d-lg-block text-dark">VPayPro</span>
              </a>
            </div> <!-- End Logo -->

            <div class="card mb-3">

              <div class="card-body">

                <div class="pt-4 pb-2">
                  <h5 class="card-title text-center pb-0 fs-4">Login to Your Account</h5>
                  <p class="text-center small">Enter your username & password to login</p>
                </div>

                <?php if (!empty($login_error)): ?>
                  <div class="alert alert-danger bg-danger text-light border-0 alert-dismissible fade show" role="alert">
                    <p class="error"><?php echo htmlspecialchars($login_error); ?></p>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>
                <?php endif; ?>

                <?php if (isset($_GET['message'])): ?>
                  <div class="alert alert-warning bg-warning text-dark border-0 alert-dismissible fade show" role="alert">
                    <p><?php echo htmlspecialchars($_GET['message']); ?></p>
                    <button type="button" class="btn-close btn-close-dark" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>
                <?php endif; ?>

                <form method="post" action="index.php" class="row g-3 needs-validation" novalidate>

                  <div class="col-12">
                    <div class="input-group has-validation">
                      <span class="input-group-text" id="inputGroupPrepend">@</span>
                      <input placeholder="Username or Email" type="text" name="username" class="form-control" id="yourUsername" required>
                      <div class="invalid-feedback">Please enter your username or email.</div>
                    </div>
                  </div>

                  <div class="col-12">
                    <div class="input-group has-validation" style="height:6vh;">
                      <span class="input-group-text" id="inputGroupPrepend" style="font-size:25px; height:6vh;">*</span>
                      <input placeholder="Password" type="password" name="password" class="form-control" id="yourPassword" required>
                      <div class="invalid-feedback">Please enter your password!</div>
                    </div>
                  </div>


                  <div class="col-12">
                    <button class="btn btn-primary w-100" type="submit"><i class="bi bi-box-arrow-in-right"></i> Login</button>
                  </div>
                  <div class="col-12">
                    <p class="small mb-0">
                      <a href="forgot_password.php">Forgot Password?</a>
                    </p>
                  </div>
                  <div class="col-12">
                    <p class="small mb-0">Don't have a customer account? <a href="customer_register.php">Create an account</a></p>
                  </div>
                  <div class="col-12 text-center">
                    <p class="small mb-0">
                      <a href="../index.php">Go back to Site</a>
                    </p>
                  </div>
                </form>

              </div>
            </div>

          </div>
        </div>
      </div>
    </section>

  </div>

</body>

</html>