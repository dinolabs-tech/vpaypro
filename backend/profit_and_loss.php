<?php
include('database/db_connection.php');
include('models/profit_and_loss.php');

session_start();

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
          <div>
            <h3 class="fw-bold mb-3">Profit and Loss Report</h3>
            <nav>
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                <li class="breadcrumb-item active">Profit and Loss Report</li>
              </ol>
            </nav>
          </div>

          <section class="section">
            <div class="row">
              <div class="col-lg-12">

                <div class="card">
                  <div class="card-body">
                    <h5 class="card-title">Profit and Loss Report</h5>

                    <form method="GET">
                      <div class="row mb-3 align-items-end">
                        <div class="col-md-6 mb-2">
                          <select class="form-select" id="group_by" name="group_by">
                            <option value="">Group By</option>
                            <option value="day">Day</option>
                            <option value="month">Month</option>
                            <option value="year">Year</option>
                          </select>
                        </div>
                        <div class="col-md-6">
                          <button type="submit" class="btn btn-primary rounded">Generate Report</button>
                        </div>
                      </div>

                    </form>

                    <?php

                    global $conn;

                    $group_by = isset($_GET['group_by']) ? $_GET['group_by'] : null;

                    // Generate the profit and loss report
                    $profit_and_loss_data = generate_profit_and_loss($conn, $group_by);
                    ?>
                    <div class="table-responsive">
                      <table class="table table-bordered" id="basic-datatables">
                        <thead>
                          <tr>
                            <th>Date</th>
                            <th>Revenue</th>
                            <th>Expenses</th>
                            <th>Net Profit</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          if ($group_by) {
                            foreach ($profit_and_loss_data as $date => $profit_and_loss) {
                              echo "<tr>";
                              echo "<td>" . $date . "</td>";
                              echo "<td>" . number_format($profit_and_loss['revenue'], 2) . "</td>";
                              echo "<td>" . number_format($profit_and_loss['expenses'], 2) . "</td>";
                              echo "<td>" . number_format($profit_and_loss['net_profit'], 2) . "</td>";
                              echo "</tr>";
                            }
                          } else {
                            echo "<tr>";
                            echo "<td>Total</td>";
                            echo "<td>" . number_format($profit_and_loss_data['revenue'], 2) . "</td>";
                            echo "<td>" . number_format($profit_and_loss_data['expenses'], 2) . "</td>";
                            echo "<td>" . number_format($profit_and_loss_data['net_profit'], 2) . "</td>";
                            echo "</tr>";
                          }
                          ?>
                        </tbody>
                      </table>
                    </div>

                  </div>
                </div>

              </div>
            </div>
          </section>
        </div>
      </div>

      <?php include('components/footer.php'); ?>
    </div>
  </div>
  <?php include('components/script.php'); ?>
</body>

</html>