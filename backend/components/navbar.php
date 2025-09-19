      <div class="main-header">
        <div class="main-header-logo">
          <!-- Logo Header -->
          <div class="logo-header" data-background-color="dark">
            <a href="index.php" class="logo">
              <!-- <img
                src="assets/img/kaiadmin/logo_light.svg"
                alt="navbar brand"
                class="navbar-brand"
                height="20" /> -->
            </a>
            <div class="nav-toggle">
              <button class="btn btn-toggle toggle-sidebar">
                <i class="gg-menu-right"></i>
              </button>
              <button class="btn btn-toggle sidenav-toggler">
                <i class="gg-menu-left"></i>
              </button>
            </div>
            <button class="topbar-toggler more">
              <i class="gg-more-vertical-alt"></i>
            </button>
          </div>
          <!-- End Logo Header -->
        </div>
        <!-- Navbar Header -->
        <nav
          class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
          <div class="container-fluid">

            <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">

              <li class="nav-item topbar-user dropdown hidden-caret">
                <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
                  <?php
                  if ($_SESSION['role'] == 'Customer') {
                    echo '<img src="assets/img/' . (!empty($_SESSION['profile_picture']) ? htmlspecialchars($_SESSION['profile_picture']) : 'profile-img.jpg') . '" alt="Profile" class="rounded-circle" style="width: 40px;height: 40px;">';
                  } elseif ($_SESSION['role'] == 'Supplier') {
                    echo '<img src="assets/img/' . (!empty($_SESSION['profile_picture']) ? htmlspecialchars($_SESSION['profile_picture']) : 'profile-img.jpg') . '" alt="Profile" class="rounded-circle" style="width: 40px;height: 40px;">';
                  } else {
                    echo '<img src="assets/img/profile_pictures/' . (!empty($_SESSION['profile_picture']) ? htmlspecialchars($_SESSION['profile_picture']) : 'profile-img.jpg') . '" alt="Profile" class="rounded-circle" style="width: 40px;height: 40px;">';
                  }
                  ?>

                  <span class="d-none d-md-block dropdown-toggle ps-2 pe-3 ">
                    <?php
                    if ($_SESSION['role'] == 'Customer') {
                      echo htmlspecialchars($_SESSION['name']);
                    } elseif ($_SESSION['role'] == 'Supplier') {
                      echo htmlspecialchars($_SESSION['name']);
                    } else {
                      echo htmlspecialchars($_SESSION['staffname']);
                    }
                    ?>
                  </span>
                </a>
                <ul class="dropdown-menu dropdown-user animated fadeIn">
                  <div class="dropdown-user-scroll scrollbar-outer">
                    <li>
                      <div class="user-box">
                        <!-- <div class="avatar-lg"> -->
                        <!-- <img
                            src="assets/img/profile.jpg"
                            alt="image profile"
                            class="avatar-img rounded" /> -->
                        <!-- </div> -->
                        <div class="u-text">
                          <h4>
                              <?php
                              if ($_SESSION['role'] == 'Customer') {
                                echo htmlspecialchars($_SESSION['name']);
                              } elseif ($_SESSION['role'] == 'Supplier') {
                                echo htmlspecialchars($_SESSION['name']);
                              } else {
                                echo htmlspecialchars($_SESSION['staffname']);
                              }
                              ?>
                              <span>(<?= htmlspecialchars($_SESSION['role']) ?>)</span>
                          </h4>
                          <!-- <p class="text-muted">hello@example.com</p> -->
                          <!-- <a
                              href="profile.html"
                              class="btn btn-xs btn-secondary btn-sm">View Profile</a> -->
                        </div>
                      </div>
                    </li>
                    <li>
                      <div class="dropdown-divider"></div>
                      <?php if ($_SESSION['role'] == 'Customer') { ?>
                        <a class="dropdown-item" href="customer_profile.php"><i class="fas fa-user"></i> My Profile</a>
                      <?php  } elseif ($_SESSION['role'] == 'Supplier') { ?>
                        <a class="dropdown-item" href="supplier_profile.php"><i class="fas fa-user"></i> My Profile</a>
                      <?php } else { ?>
                        <a class="dropdown-item" href="profile.php"><i class="fas fa-user"></i> My Profile</a>
                      <?php } ?>
                      <!-- <a class="dropdown-item" href="#">My Balance</a> -->
                      <!-- <a class="dropdown-item" href="#">Inbox</a> -->
                      <!-- <div class="dropdown-divider"></div> -->
                      <!-- <a class="dropdown-item" href="#">Account Setting</a> -->
                      <div class="dropdown-divider"></div>
                      <a class="dropdown-item" href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </li>
                  </div>
                </ul>
              </li>
            </ul>
          </div>
        </nav>
        <!-- End Navbar -->
      </div>