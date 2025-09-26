<?php
// session_logs.php
include './database/db_connection.php';
session_start();


// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Superuser') {
  header("Location: index.php");
  exit();
}


// Fetch logs from DB
$sql = "SELECT s.id, s.user_id, l.staffname AS name, c.name as customer_name, s.event_type, s.ip_address, s.user_agent, s.created_at
        FROM session_logs s
        LEFT JOIN login l ON s.user_id = l.id
        LEFT JOIN customers c ON s.user_id = c.id
        ORDER BY s.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<?php include('components/head.php'); ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<body>
  <div class="wrapper">
    <?php include('components/sidebar.php'); ?>

    <div class="main-panel">
      <?php include('components/navbar.php'); ?>

      <div class="container">
        <div class="page-inner">
          <div>
            <h3 class="fw-bold mb-3">Session Logs</h3>
            <nav>
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                <li class="breadcrumb-item active">Session Logs</li>
              </ol>
            </nav>
          </div>

          <section class="section">
            <div class="row">
              <div class="col-md-12">
                <div class="card card-round">
                  <div class="card-header">
                    <div class="card-head-row">
                      <div class="card-title">Filter&nbsp;|&nbsp;<span>Activity Logs</span></div>
                    </div>
                  </div>
                  <div class="card-body pb-0">
                    <div class="mb-4 mt-2">


                      <div class="filters g-3">
                        <div class="row g-2">

                          <div class="col-md-3 mb-3">
                            <button id="toggleRefresh" class="btn btn-primary px-5">⏸ Pause Auto-Refresh</button>
                          </div>

                          <div class="col-md-4 mb-3">
                            <select id="eventFilter" class="form-control form-select">
                              <option value="">Select Event Type</option>
                              <option value="login">Login</option>
                              <option value="logout">Logout</option>
                              <option value="timeout">Timeout</option>
                              <option value="hijack">Hijack</option>
                            </select>
                          </div>

                          <div class="col-md-5 mb-3">
                            <input type="text" id="dateRange" placeholder="Select Date Range" class="form-control">
                          </div>
                        </div>
                      </div>


                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Product Table -->
            <div class="row mt-4">
              <div class="col-md-12">
                <div class="card card-round">
                  <div class="card-header">
                    <div class="card-head-row">
                      <span class="card-title text-white">Session Activity Logs</span>
                    </div>
                    <br>
                    <div class="table-responsive">
                      <table id="basic-datatables" class="table table-bordered display">
                        <thead>
                          <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Event Type</th>
                            <th>IP Address</th>
                            <th>User Agent</th>
                            <th>Date/Time</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php while ($row = $result->fetch_assoc()) : ?>
                            <tr>
                              <td><?= htmlspecialchars($row['id']) ?></td>
                              <td><?= htmlspecialchars($row['customer_name'] ?? $row['name']) ?></td>
                              <td><?= htmlspecialchars(ucfirst($row['event_type'])) ?></td>
                              <td><?= htmlspecialchars($row['ip_address']) ?></td>
                              <td><?= htmlspecialchars(substr($row['user_agent'], 0, 60)) ?>...</td>
                              <td><?= htmlspecialchars($row['created_at']) ?></td>
                            </tr>
                          <?php endwhile; ?>
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
 

  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
  <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

  <script>
    $(document).ready(function() {
      var table = $('#logsTable').DataTable({
        dom: 'Bfrtip',
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
        order: [
          [5, 'desc']
        ],
        pageLength: 10,
        ajax: {
          url: 'fetch_logs.php',
          dataSrc: ''
        },
        columns: [{
            data: 'id'
          },
          {
            data: 'username'
          },
          {
            data: 'event_type'
          },
          {
            data: 'ip_address'
          },
          {
            data: 'user_agent'
          },
          {
            data: 'created_at'
          }
        ]
      });

      // Event type filter
      $('#eventFilter').on('change', function() {
        table.column(2).search(this.value).draw();
      });

      // Date range filter
      var startDate, endDate;
      $('#dateRange').daterangepicker({
        opens: 'left',
        autoUpdateInput: false
      }, function(start, end) {
        startDate = start;
        endDate = end;
        $('#dateRange').val(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
        table.draw();
      });

      $.fn.dataTable.ext.search.push(function(settings, data) {
        var min = startDate ? startDate.startOf('day') : null;
        var max = endDate ? endDate.endOf('day') : null;
        var date = moment(data[5]);

        if ((min === null && max === null) ||
          (min === null && date <= max) ||
          (min <= date && max === null) ||
          (min <= date && date <= max)) {
          return true;
        }
        return false;
      });

      // Auto-refresh every 5 seconds
      var refreshEnabled = true;
      var refreshInterval = setInterval(function() {
        if (refreshEnabled) {
          table.ajax.reload(null, false); // false = keep current page & pagination
        }
      }, 5000);

      // Pause/Resume button
      $('#toggleRefresh').on('click', function() {
        refreshEnabled = !refreshEnabled;
        $(this).text(refreshEnabled ? "⏸ Pause Auto-Refresh" : "▶ Resume Auto-Refresh");
      });
    });
  </script>

   <?php include('components/script.php'); ?>
</body>

</html>