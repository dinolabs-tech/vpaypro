<!-- Sidebar -->
<div class="sidebar" data-background-color="dark">
  <div class="sidebar-logo">
    <!-- Logo Header -->
    <div class="logo-header" data-background-color="dark">
      <!-- <a href="index.php" class="logo">
        <img
          src="assets/img/kaiadmin/logo_light.svg"
          alt="navbar brand"
          class="navbar-brand"
          height="20" />
      </a> -->
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
  <div class="sidebar-wrapper scrollbar scrollbar-inner">
    <div class="sidebar-content">
      <ul class="nav nav-secondary">
        <li class="nav-item">
          <a href="../index.php">
            <i class="fas fa-globe"></i>
            <p>Visit Site</p>
          </a>
        </li>
        <?php if ($_SESSION['role'] == 'Superuser' || $_SESSION['role'] == 'CEO' || $_SESSION['role'] == 'Administrator') { ?>
          <li class="nav-item">
            <a href="dashboard.php">
              <i class="fas fa-tachometer-alt"></i>
              <p>Dashboard</p>
            </a>
          </li>
        <?php } ?>

        <?php if ($_SESSION['role'] == 'Superuser' || $_SESSION['role'] == 'Administrator' || $_SESSION['role'] == 'Sales Manager' || $_SESSION['role'] == 'Cashier') { ?>
          <li class="nav-item">
            <a href="pos.php">
              <i class="fas fa-coins"></i>
              <p>POS</p>
            </a>
          </li>
        <?php } ?>

        <?php if ($_SESSION['role'] == 'Superuser' || $_SESSION['role'] == 'CEO') { ?>
          <li class="nav-item">
            <a href="manage_discounts.php">
              <i class="fas fa-percent"></i>
              <p>Manage Discounts</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="transactions.php">
              <i class="fas fa-newspaper"></i>
              <p>Transactions</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="expenses.php">
              <i class="fas fa-receipt"></i>
              <p>Expenses</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="profit_and_loss.php">
              <i class="fas fa-columns"></i>
              <p>Profit and Loss Reports</p>
            </a>
          </li>
        <?php } ?>

        <?php if ($_SESSION['role'] == 'Superuser' || $_SESSION['role'] == 'CEO' || $_SESSION['role'] == 'Administrator' || $_SESSION['role'] == 'Inventory Manager' || $_SESSION['role'] == 'Sales Manager') { ?>
          <hr class="page-divider">
          <li class="nav-section">
            <span class="sidebar-mini-icon">
              <i class="fa fa-ellipsis-h"></i>
            </span>
            <h4 class="text-section">PRODUCT & INVENTORY MANAGEMENT</h4>
          </li>
          <hr class="page-divider">
          <li class="nav-item">
            <a href="inventory.php">
              <i class="fas fa-box"></i>
              <p>Inventory</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="admin_categories.php">
              <i class="fas fa-tags"></i>
              <p>Categories</p>
            </a>
          </li>
        <?php } ?>

        <?php if ($_SESSION['role'] == 'Superuser' || $_SESSION['role'] == 'CEO' || $_SESSION['role'] == 'Administrator' || $_SESSION['role'] == 'Inventory Manager') { ?>
          <li class="nav-item">
            <a href="suppliers.php">
              <i class="fas fa-truck"></i>
              <p>Suppliers</p>
            </a>
          </li>
        <?php } ?>

        <?php if ($_SESSION['role'] == 'Superuser' || $_SESSION['role'] == 'Administrator' || $_SESSION['role'] == 'CEO' || $_SESSION['role'] == 'Inventory Manager') { ?>
          <li class="nav-item">
            <a href="admin_purchase_orders.php">
              <i class="fas fa-receipt"></i>
              <p>Purchase Orders</p>
            </a>
          </li>
        <?php } ?>

        <?php if ($_SESSION['role'] == 'Superuser' || $_SESSION['role'] == 'Supplier') { ?>
          <li class="nav-item">
            <a href="supplier_dashboard.php">
              <i class="fas fa-tachometer-alt"></i>
              <p>Dashboard</p>
            </a>
          </li>

          <li class="nav-item">
            <a href="supplier_received_orders.php">
              <i class="fas fa-receipt"></i>
              <p>Received Orders</p>
            </a>
          </li>
        <?php } ?>

        <?php if ($_SESSION['role'] == 'Superuser' || $_SESSION['role'] == 'Administrator' || $_SESSION['role'] == 'CEO' || $_SESSION['role'] == 'Inventory Manager') { ?>
          <li class="nav-item">
            <a href="admin_stock_transfers.php">
              <i class="fas fa-exchange-alt"></i>
              <p>Stock Transfers</p>
            </a>
          </li>
        <?php } ?>

        <?php if ($_SESSION['role'] == 'Superuser' || $_SESSION['role'] == 'Administrator' || $_SESSION['role'] == 'CEO' || $_SESSION['role'] == 'Inventory Manager' || $_SESSION['role'] == 'Sales Manager' || $_SESSION['role'] == 'Delivery') { ?>
          <hr class="page-divider">
          <li class="nav-section">
            <span class="sidebar-mini-icon">
              <i class="fa fa-ellipsis-h"></i>
            </span>
            <h4 class="text-section">ORDER & DELIVERIES</h4>
          </li>
          <hr class="page-divider">
          <li class="nav-item">
            <a href="admin_orders.php">
              <i class="fas fa-box-open"></i>
              <p>Manage Orders</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="admin_deliveries.php">
              <i class="fas fa-truck-moving"></i>
              <p>Manage Deliveries</p>
            </a>
          </li>
        <?php } ?>

        <?php if ($_SESSION['role'] == 'Superuser' || $_SESSION['role'] == 'CEO') { ?>
          <hr class="page-divider">
          <li class="nav-section">
            <span class="sidebar-mini-icon">
              <i class="fa fa-ellipsis-h"></i>
            </span>
            <h4 class="text-section">CUSTOMER MANAGEMENT</h4>
          </li>
          <hr class="page-divider">
          <li class="nav-item">
            <a href="admin_check_balance.php">
              <i class="fas fa-user-check"></i>
              <p>Check Customer Balance</p>
            </a>
          </li>
        <?php } ?>

        <?php if ($_SESSION['role'] == 'Superuser' || $_SESSION['role'] == 'Administrator' || $_SESSION['role'] == 'CEO' || $_SESSION['role'] == 'Inventory Manager' || $_SESSION['role'] == 'Sales Manager') { ?>
          <li class="nav-item">
            <a href="admin_check_transactions.php">
              <i class="fas fa-credit-card"></i>
              <p>Check Customer <br>Transactions</p>
            </a>
          </li>
        <?php } ?>

        <?php if ($_SESSION['role'] == 'Superuser' || $_SESSION['role'] == 'CEO') { ?>
          <hr class="page-divider">
          <li class="nav-section">
            <span class="sidebar-mini-icon">
              <i class="fa fa-ellipsis-h"></i>
            </span>
            <h4 class="text-section">USER & BRANCH MANAGEMENT</h4>
          </li>
          <hr class="page-divider">
          <li class="nav-item">
            <a href="users.php">
              <i class="fas fa-user-circle"></i>
              <p>User Control</p>
            </a>
          </li>
        <?php } ?>

        <?php if ($_SESSION['role'] == 'Superuser' || $_SESSION['role'] == 'CEO') { ?>
          <li class="nav-item">
            <a href="admin_branches.php">
              <i class="fas fa-building"></i>
              <p>Manage Branches</p>
            </a>
          </li>
        <?php } ?>

        <?php if ($_SESSION['role'] == 'Superuser' || $_SESSION['role'] == 'CEO') { ?>
          <hr class="page-divider">
          <li class="nav-section">
            <span class="sidebar-mini-icon">
              <i class="fa fa-ellipsis-h"></i>
            </span>
            <h4 class="text-section">SYSTEM SETTINGS</h4>
          </li>
          <hr class="page-divider">
          <li class="nav-item">
            <a href="audit_trails.php">
              <i class="fas fa-list"></i>
              <p>Audit Trails</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="payment_gateways.php">
              <i class="fas fa-credit-card"></i>
              <p>Payment Gateways</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="tax_settings.php">
              <i class="fas fa-calculator"></i>
              <p>Tax Settings</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="admin_settings.php">
              <i class="fas fa-cogs"></i>
              <p>General Setting</p>
            </a>
          </li>
          <?php if ($_SESSION['role'] == 'Superuser') { ?>
            <li class="nav-item">
              <a href="session_logs.php">
                <i class="fas fa-building"></i>
                <p>Session Logs</p>
              </a>
            </li>
          <?php } ?>
        <?php } ?>
      </ul>
    </div>
  </div>
</div>
<!-- End Sidebar -->