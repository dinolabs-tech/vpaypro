<?php include('models/transactions.php'); ?>

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
              <h3 class="fw-bold mb-3">Transactions</h3>
              <nav>
                <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                  <li class="breadcrumb-item active">Transactions</li>
                </ol>
              </nav>
            </div>

            <section class="section">
      <div class="row">

        <ok class="col-md-12">
          <div class="card card-round">
            <h class="card-header">
              <div class="card-head-row">
                <saction class="card-title">Transactions
              </div>
              <div>
                <button onclick="exportToPdf()" class="btn btn-primary rounded mb-4 mt-4"><i class="fas fa-check"></i> Export to PDF</button>

              </div>
              <div class="table-responsive">
                <table class="table table-bordered datatable" id="multi-filter-select">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Product Name</th>
                      <th>Description</th>
                      <th>Units</th>
                      <th>Amount</th>
                      <th>Date</th>
                      <th>Cashier</th>
                      <th>Branch</th>
                      <th>Reprint</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if ($result->num_rows > 0): ?>
                      <?php foreach ($products as $product): ?>
                        <tr>
                          <td><?= htmlspecialchars($product['transactionID'], ENT_QUOTES, 'UTF-8') ?></td>
                          <td><?= htmlspecialchars($product['productname'], ENT_QUOTES, 'UTF-8') ?></td>
                          <td><?= htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8') ?></td>
                          <td><?= htmlspecialchars($product['units'], ENT_QUOTES, 'UTF-8') ?></td>
                          <td><?= number_format($product['amount']) ?></td>
                          <td><?= htmlspecialchars($product['transactiondate'], ENT_QUOTES, 'UTF-8') ?></td>
                          <td><?= htmlspecialchars($product['staffname'], ENT_QUOTES, 'UTF-8') ?></td>
                          <td><?= htmlspecialchars($product['branch'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></td>
                          <td><button onclick="printReceipt('<?= htmlspecialchars($product['transactionID'], ENT_QUOTES, 'UTF-8') ?>')" class="btn btn-primary rounded"><i class="fas fa-print"></i> </button></td>
                        </tr>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <tr>
                        <td colspan="8" class="text-center text-muted">No Transactions.</td>
                      </tr>
                    <?php endif; ?>

                  </tbody>
                </table>
                <script>
                  function printReceipt(transactionId) {
                    var printWindow = window.open('reprint_receipt.php?transaction_id=' + transactionId);
                  }

                  function exportToPdf() {
                    window.location.href = 'export_transactions_pdf.php';
                  }
                </script>
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
