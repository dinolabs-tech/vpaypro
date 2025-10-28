<?php
include('models/dashboard.php');
include('models/low_stock_alerts.php'); // Include the low stock alerts model


// Fetch low stock products
$low_stock_products = getLowStockProducts($conn, $user_role, $user_country, $user_state);
?>

<!DOCTYPE html>
<html lang="en">
<?php include('components/head.php'); ?>

<body>
  <div class="wrapper">
    <?php include('components/sidebar.php'); ?>

    <div class="main-panel">
      <?php include('components/navbar.php'); ?>

      <div class="container">
        <div class="page-inner">
          <div
            class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            <div>
              <h3 class="fw-bold mb-3">Dashboard</h3>
              <nav>
                <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                  <li class="breadcrumb-item active">Dashboard</li>
                </ol>
              </nav>
            </div>
            <div class="ms-md-auto py-2 py-md-0">
              <?php if ($_SESSION['role'] == 'Superuser') {?>
              <a href="developer.php" class="btn btn-label-info btn-round me-2">Developer Tools</a>
              <!-- <a href="#" class="btn btn-primary btn-round">Add Customer</a> -->
               <?php } ?>
            </div>
          </div>
          <section class="section dashboard">
            <?php if ($_SESSION['role'] == 'Superuser' || $_SESSION['role'] == 'CEO') { ?>
              <div class="row">
                <!-- sales sum -->
                <div class="col-sm-6 col-md-3">
                  <div class="card card-stats card-round card-success">
                    <div class="card-body">
                      <div class="card-head-row card-tools-still-right">
                        <h4 class="card-title">Sales&nbsp;|&nbsp;<span><small><?php echo $label; ?></small></span></h4>
                        <div class="card-tools">
                          <div class="dropdown">
                            <button
                              class="btn btn-icon btn-clean me-0"
                              type="button"
                              id="dropdownMenuButton"
                              data-bs-toggle="dropdown"
                              aria-haspopup="true"
                              aria-expanded="false">
                              <i class="fas fa-ellipsis-h"></i>
                            </button>
                            <div
                              class="dropdown-menu"
                              aria-labelledby="dropdownMenuButton">
                              <?php $rfilter = isset($_GET['rfilter']) ? $_GET['rfilter'] : 'today'; ?>
                              <a class="dropdown-item" href="?filter=today&rfilter=<?php echo $rfilter; ?>">Today</a>
                              <a class="dropdown-item" href="?filter=month&rfilter=<?php echo $rfilter; ?>">This Month</a>
                              <a class="dropdown-item" href="?filter=year&rfilter=<?php echo $rfilter; ?>">This Year</a>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div>
                        <h3><strong><?php echo '₦' . number_format($total_sales ?: '0'); ?></strong></h3>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- revenue  -->
                <div class="col-sm-6 col-md-3">
                  <div class="card card-stats card-round card-primary">
                    <div class="card-body">
                      <div class="card-head-row card-tools-still-right">
                        <h4 class="card-title">Revenue&nbsp;|&nbsp;<span><small><?php echo $revenue_label; ?></small></span></h4>
                        <div class="card-tools">
                          <div class="dropdown">
                            <button
                              class="btn btn-icon btn-clean me-0"
                              type="button"
                              id="dropdownMenuButton"
                              data-bs-toggle="dropdown"
                              aria-haspopup="true"
                              aria-expanded="false">
                              <i class="fas fa-ellipsis-h"></i>
                            </button>
                            <div
                              class="dropdown-menu"
                              aria-labelledby="dropdownMenuButton">

                              <?php $filter = isset($_GET['filter']) ? $_GET['filter'] : 'today'; ?>
                              <li><a class="dropdown-item" href="?rfilter=today&filter=<?php echo $filter; ?>">Today</a></li>
                              <li><a class="dropdown-item" href="?rfilter=month&filter=<?php echo $filter; ?>">This Month</a></li>
                              <li><a class="dropdown-item" href="?rfilter=year&filter=<?php echo $filter; ?>">This Year</a></li>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div>
                        <h3><strong><?php echo '₦' . number_format($total_revenue ?: 0); ?></strong></h3>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- inventory quantity  -->
                <div class="col-sm-6 col-md-3">
                  <div class="card card-stats card-round card-warning">
                    <div class="card-body">
                      <div class="card-head-row card-tools-still-right">
                        <h4 class="card-title">Inventory&nbsp;|&nbsp;<span><small>Quantity</small></span></h4>
                      </div>
                      <div>
                        <h3><strong><?php echo number_format($inventory_quantity ?: 0); ?></strong></h3>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- inventory sum  -->
                <div class="col-sm-6 col-md-3">
                  <div class="card card-stats card-round card-secondary">
                    <div class="card-body">
                      <div class="card-head-row card-tools-still-right">
                        <h4 class="card-title">Inventory&nbsp;|&nbsp;<span><small>Sum</small></span></h4>
                      </div>
                      <div>
                        <h3><strong><?php echo number_format($inventory_sum ?: 0); ?></strong></h3>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            <?php } ?>

            <?php if ($_SESSION['role'] == 'Superuser' || $_SESSION['role'] == 'CEO') { ?>
              <div class="row">
                <!--total customer balance  -->
                <div class="col-sm-6 col-md-4">
                  <div class="card card-stats card-round card-info">
                    <div class="card-body">
                      <div class="card-head-row card-tools-still-right">
                        <h4 class="card-title">Total Customer Balance</h4>
                      </div>
                      <div class="row">
                        <div class="d-flex align-items-center">
                          <div class="col-md-2 pe-3">
                            <div class="icon-big">
                              <i class="icon-wallet"></i>
                            </div>
                          </div>
                          <div class="col-md-10">
                            <h3><strong><?php echo '₦' . number_format($total_customer_balance ?: 0, 2); ?></strong></h3>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- active customer balance  -->
                <div class="col-sm-6 col-md-4">
                  <div class="card card-stats card-round card-success">
                    <div class="card-body">
                      <div class="card-head-row card-tools-still-right">
                        <h4 class="card-title">Active Customer Balance</h4>
                      </div>
                      <div class="row">
                        <div class="d-flex align-items-center">
                          <div class="col-md-2 pe-3">
                            <div class="icon-big">
                              <i class="fas fa-user-check"></i>
                            </div>
                          </div>
                          <div class="col-md-10">
                            <h3><strong><?php echo '₦' . number_format($active_customer_balance ?: 0, 2); ?></strong></h3>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- inactive customer balance  -->
                <div class="col-sm-6 col-md-4">
                  <div class="card card-stats card-round card-danger">
                    <div class="card-body">
                      <div class="card-head-row card-tools-still-right">
                        <h4 class="card-title">Inactive Customer Balance</h4>
                      </div>
                      <div class="row">
                        <div class="d-flex align-items-center">
                          <div class="col-md-2 pe-3">
                            <div class="icon-big">
                              <i class="fas fa-user"></i> <!--style="color:#FF7F7F;" -->
                            </div>
                          </div>
                          <div class="col-md-10">
                            <h3><strong><?php echo '₦' . number_format($inactive_customer_balance ?: 0, 2); ?></strong></h3>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row">
                <!--active customer count -->
                <div class="col-sm-6 col-md-6">
                  <div class="card card-stats card-round card-secondary">
                    <div class="card-body">
                      <div class="card-head-row card-tools-still-right">
                        <h4 class="card-title">Active Customer</h4>
                      </div>
                      <div class="row">
                        <div class="d-flex align-items-center">
                          <div class="col-md-2 pe-3">
                            <div class="icon-big">
                              <i class="fas fa-user"></i>
                            </div>
                          </div>
                          <div class="col-md-10">
                            <h3><strong><?php echo number_format($active_customer_count ?: 0); ?></strong></h3>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- inactive customer count -->
                <div class="col-sm-6 col-md-6">
                  <div class="card card-stats card-round card-black">
                    <div class="card-body">
                      <div class="card-head-row card-tools-still-right">
                        <h4 class="card-title">Inactive Customer</h4>
                      </div>
                      <div class="row">
                        <div class="d-flex align-items-center">
                          <div class="col-md-2 pe-3">
                            <div class="icon-big">
                              <i class="fas fa-user-minus"></i>
                            </div>
                          </div>
                          <div class="col-md-10">
                            <h3><strong><?php echo number_format($inactive_customer_count ?: 0); ?></strong></h3>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            <?php } ?>

            <div class="row">
              <!-- left side  -->
              <div class="col-lg-8">
                <div class="row">
                  <!-- Website Metrics Pie Chart -->
                  <div class="col-12">
                    <div class="card card-round">
                      <div class="card-header">
                        <div class="card-head-row">
                          <h4 class="card-title">Website Metrics&nbsp;|&nbsp;<span><small><?php echo htmlspecialchars($filter); ?></small></span></h4>
                          <div class="card-tools">
                            <div class="dropdown">
                              <button
                                class="btn btn-icon btn-clean me-0"
                                type="button"
                                id="dropdownMenuButton"
                                data-bs-toggle="dropdown"
                                aria-haspopup="true"
                                aria-expanded="false">
                                <i class="fas fa-ellipsis-h"></i>
                              </button>
                              <div
                                class="dropdown-menu"
                                aria-labelledby="dropdownMenuButton">
                                <li><a class="dropdown-item" href="?filter=Today">Today</a></li>
                                <li><a class="dropdown-item" href="?filter=This Month">This Month</a></li>
                                <li><a class="dropdown-item" href="?filter=This Year">This Year</a></li>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="card-body">
                        <div class="chart-container" style="min-height: 375px">
                          <canvas id="websiteMetricsPieChart"></canvas>
                        </div>
                        <script>
                          document.addEventListener("DOMContentLoaded", () => {
                            const metrics = <?php echo json_encode($dashboard_metrics, JSON_NUMERIC_CHECK); ?>;
                            const ctx = document.getElementById('websiteMetricsPieChart').getContext('2d');
                            new Chart(ctx, {
                              type: 'pie',
                              data: {
                                labels: ['Sales', 'Revenue', 'Inventory Qty', 'Inventory Value'],
                                datasets: [{
                                  data: [
                                    metrics.total_sales_units,
                                    metrics.total_revenue_amt,
                                    metrics.total_inventory_qty,
                                    metrics.total_inventory_value
                                  ],
                                  backgroundColor: [
                                    'rgba(255, 99, 132, 0.8)',
                                    'rgba(54, 162, 235, 0.8)',
                                    'rgba(255, 206, 86, 0.8)',
                                    'rgba(75, 192, 192, 0.8)'
                                  ],
                                  borderColor: [
                                    'rgba(255, 99, 132, 1)',
                                    'rgba(54, 162, 235, 1)',
                                    'rgba(255, 206, 86, 1)',
                                    'rgba(75, 192, 192, 1)'
                                  ],
                                  borderWidth: 1
                                }]
                              },
                              options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                  legend: {
                                    position: 'top',
                                  },
                                  tooltip: {
                                    callbacks: {
                                      label: function(context) {
                                        let label = context.label || '';
                                        if (label) {
                                          label += ': ';
                                        }
                                        if (context.parsed !== null) {
                                          label += new Intl.NumberFormat('en-US', { style: 'currency', currency: 'NGN' }).format(context.parsed);
                                        }
                                        return label;
                                      }
                                    }
                                  }
                                }
                              }
                            });
                          });
                        </script>
                      </div>
                    </div>
                  </div>
                  <!-- End Website Metrics Pie Chart -->

                  <!-- recent sales  -->
                  <div class="col-12">
                    <div class="card card-round">
                      <div class="card-header">
                        <div class="card-head-row card-tools-still-right">
                          <h4 class="card-title">Recent Sales&nbsp;|&nbsp;<span><small>Today</small></span></h4>
                          <!-- <div class="card-tools">
                            <div class="dropdown">
                              <button
                                class="btn btn-icon btn-clean me-0"
                                type="button"
                                id="dropdownMenuButton"
                                data-bs-toggle="dropdown"
                                aria-haspopup="true"
                                aria-expanded="false">
                                <i class="fas fa-ellipsis-h"></i>
                              </button>
                              <div
                                class="dropdown-menu"
                                aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item" href="?filter=today&rfilter=<?php echo $rfilter; ?>">Today</a>
                                <a class="dropdown-item" href="?filter=month&rfilter=<?php echo $rfilter; ?>">This Month</a>
                                <a class="dropdown-item" href="?filter=year&rfilter=<?php echo $rfilter; ?>">This Year</a>
                              </div>
                            </div>
                          </div> -->
                        </div>
                      </div>
                      <div class="card-body">
                        <div class="table-responsive">
                          <table
                            id="basic-datatables"
                            class="display table table-hover">
                            <thead>
                              <tr>
                                <th>Product</th>
                                <th>Units</th>
                                <th>Amount</th>
                                <th>Date</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php if (!empty($transactions)): ?>
                                <?php foreach ($transactions as $transaction): ?>
                                  <tr>
                                    <td><?php echo htmlspecialchars($transaction['productname']); ?></td>
                                    <td><?php echo htmlspecialchars($transaction['productname']); ?></td>
                                    <td><?php echo htmlspecialchars($transaction['amount']); ?></td>
                                    <td><?php echo htmlspecialchars($transaction['transactiondate']); ?></td>
                                  </tr>
                                <?php endforeach; ?>
                              <?php else: ?>
                                <tr>
                                  <td colspan="4">No records found</td>
                                </tr>
                              <?php endif ?>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- top selling  -->
                  <div class="col-12">
                    <div class="card card-round">
                      <div class="card-header">
                        <div class="card-head-row card-tools-still-right">
                          <h4 class="card-title">Top Selling&nbsp;|&nbsp;<span><small>Today</small></span></h4>
                          <!-- <div class="card-tools">
                            <div class="dropdown">
                              <button
                                class="btn btn-icon btn-clean me-0"
                                type="button"
                                id="dropdownMenuButton"
                                data-bs-toggle="dropdown"
                                aria-haspopup="true"
                                aria-expanded="false">
                                <i class="fas fa-ellipsis-h"></i>
                              </button>
                              <div
                                class="dropdown-menu"
                                aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item" href="?filter=today&rfilter=<?php echo $rfilter; ?>">Today</a>
                                <a class="dropdown-item" href="?filter=month&rfilter=<?php echo $rfilter; ?>">This Month</a>
                                <a class="dropdown-item" href="?filter=year&rfilter=<?php echo $rfilter; ?>">This Year</a>
                              </div>
                            </div>
                          </div> -->
                        </div>
                      </div>
                      <div class="card-body">
                        <div class="table-responsive">
                          <!-- Projects table -->
                          <table class="table align-items-center mb-0" id="mutli-filter-select">
                            <thead class="thead">
                              <tr>
                                <th scope="col">Product</th>
                                <th scope="col">Units</th>
                                <th scope="col">Price</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php if (!empty($topselling_transactions)): ?>
                                <?php foreach ($topselling_transactions as $transaction): ?>
                                  <tr>
                                    <td><?php echo htmlspecialchars($transaction['productname']); ?></td>
                                    <td><?php echo htmlspecialchars($transaction['units']); ?></td>
                                    <td><?php echo htmlspecialchars($transaction['amount']); ?></td>
                                  </tr>
                                <?php endforeach; ?>
                              <?php else: ?>
                                <tr>
                                  <td colspan="4">No records found</td>
                                </tr>
                              <?php endif; ?>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- end left side  -->

              <!-- right side  -->
              <!-- out of stock product  -->
              <div class="col-lg-4">
                <div class="row">
                  <div class="col-12">
                    <div class="card card-round">
                      <div class="card-header">
                        <div class="card-head-row card-tools-still-right">
                          <h4 class="card-title">Products&nbsp;|&nbsp;<span><small>Out of Stock</small></span></h4>
                        </div>
                      </div>
                      <div class="card-body p-0">
                        <div class="table-responsive">
                          <table class="table align-items-center mb-0">
                            <thead class="thead">
                              <tr>
                                <th scope="col">Product</th>
                                <th scope="col">Qty.</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php if (!empty($out_of_stock)): ?>
                                <?php foreach ($out_of_stock as $transaction): ?>
                                  <tr>
                                    <td><?php echo htmlspecialchars($transaction['productname']); ?></td>
                                    <td><?php echo htmlspecialchars($transaction['quantity']); ?></td>
                                  </tr>
                                <?php endforeach; ?>
                              <?php else: ?>
                                <tr>
                                  <td colspan="2">No records found</td>
                                </tr>
                              <?php endif; ?>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- low stock products  -->
                  <div class="col-12">
                    <div class="card card-round">
                      <div class="card-header">
                        <div class="card-head-row card-tools-still-right">
                          <h4 class="card-title">Products&nbsp;|&nbsp;<span><small>Low Stock Alerts</small></span></h4>
                        </div>
                      </div>
                      <div class="card-body p-0">
                        <div class="table-responsive">
                          <table class="table align-items-center mb-0">
                            <thead class="thead">
                              <tr>
                                <th scope="col">Product</th>
                                <th scope="col">Current Qty.</th>
                                <th scope="col">Reorder Level</th>
                                <th scope="col">Reorder Qty.</th>
                                <th scope="col">Branch</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php if (!empty($low_stock_products)): ?>
                                <?php foreach ($low_stock_products as $product): ?>
                                  <tr>
                                    <td><?= htmlspecialchars($product['productname']) ?></td>
                                    <td><?= htmlspecialchars($product['qty'] ?? 0) ?></td>
                                    <td><?= htmlspecialchars($product['reorder_level']) ?></td>
                                    <td><?= htmlspecialchars($product['reorder_qty']) ?></td>
                                    <td><?= htmlspecialchars($product['branch_name'] ?? 'N/A') ?></td>
                                  </tr>
                                <?php endforeach; ?>
                              <?php else: ?>
                                <tr>
                                  <td colspan="5">No low stock products found</td>
                                </tr>
                              <?php endif; ?>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- active customers  -->
                  <?php if ($_SESSION['role'] == 'Superuser' || $_SESSION['role'] == 'CEO') { ?>
                    <div class="col-12">
                      <div class="card card-round">
                        <div class="card-header">
                          <div class="card-head-row card-tools-still-right">
                            <h4 class="card-title">Active Customers</h4>
                          </div>
                        </div>
                        <div class="card-body p-2">
                          <div class="table-responsive">
                            <table class="table align-items-center mb-0" id="multi-filter-select">
                              <thead class="thead">
                                <tr>
                                  <th scope="col">Name</th>
                                  <th scope="col">Email</th>
                                  <th scope="col">Balance</th>
                                </tr>
                              </thead>
                              <tbody>
                                <?php if (!empty($active_customers)): ?>
                                  <?php foreach ($active_customers as $customer): ?>
                                    <tr>
                                      <td><?php echo htmlspecialchars($customer['name']); ?></td>
                                      <td><?php echo htmlspecialchars($customer['email']); ?></td>
                                      <td><?php echo '₦' . number_format($customer['balance'], 2); ?></td>
                                    </tr>
                                  <?php endforeach; ?>
                                <?php else: ?>
                                  <tr>
                                    <td colspan="3">No active customers found</td>
                                  </tr>
                                <?php endif; ?>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- inactive customers  -->
                    <div class="col-12">
                      <div class="card card-round">
                        <div class="card-header">
                          <div class="card-head-row card-tools-still-right">
                            <h4 class="card-title">Inactive Customers</h4>
                          </div>
                        </div>
                        <div class="card-body p-0">
                          <div class="table-responsive">
                            <table class="table align-items-center mb-0">
                              <thead class="thead">
                                <tr>
                                  <th scope="col">Name</th>
                                  <th scope="col">Email</th>
                                  <th scope="col">Balance</th>
                                </tr>
                              </thead>
                              <tbody>
                                <?php if (!empty($inactive_customers)): ?>
                                  <?php foreach ($inactive_customers as $customer): ?>
                                    <tr>
                                      <td><?php echo htmlspecialchars($customer['name']); ?></td>
                                      <td><?php echo htmlspecialchars($customer['email']); ?></td>
                                      <td><?php echo '₦' . number_format($customer['balance'], 2); ?></td>
                                    </tr>
                                  <?php endforeach; ?>
                                <?php else: ?>
                                  <tr>
                                    <td colspan="3">No inactive customers found</td>
                                  </tr>
                                <?php endif; ?>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- low balance customers  -->
                    <div class="col-12">
                      <div class="card card-round">
                        <div class="card-header">
                          <div class="card-head-row card-tools-still-right">
                            <h4 class="card-title">Low Balance Customers</h4>
                          </div>
                        </div>
                        <div class="card-body p-0">
                          <div class="table-responsive">
                            <table class="table align-items-center mb-0">
                              <thead class="thead">
                                <tr>
                                  <th scope="col">Name</th>
                                  <th scope="col">Email</th>
                                  <th scope="col">Balance</th>
                                </tr>
                              </thead>
                              <tbody>
                                <?php if (!empty($low_balance_customers)): ?>
                                  <?php foreach ($low_balance_customers as $customer): ?>
                                    <tr>
                                      <td><?php echo htmlspecialchars($customer['name']); ?></td>
                                      <td><?php echo htmlspecialchars($customer['email']); ?></td>
                                      <td><?php echo '₦' . number_format($customer['balance'], 2); ?></td>
                                    </tr>
                                  <?php endforeach; ?>
                                <?php else: ?>
                                  <tr>
                                    <td colspan="3">No low balance customers found</td>
                                  </tr>
                                <?php endif; ?>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                    </div>
                  <?php } ?>
                  <!-- end right side  -->
                </div>
              </div>
            </div>

        </div>
      </div>

      <?php include('components/footer.php'); ?>
    </div>

    <!-- Custom template | don't include it in your project! -->

    <!-- End Custom template -->
  </div>
  <?php include('components/script.php'); ?>

</body>

</html>
