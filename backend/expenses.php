<?php
include('models/expenses.php');


if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

include './database/db_connection.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ✅ AJAX-based Deletion Handling
if (
    isset($_GET['delete_id']) &&
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
) {
    header('Content-Type: application/json');
    $id = intval($_GET['delete_id']); // sanitize input

    $sql_expenses = "DELETE FROM expenses WHERE id = $id";

    if ($conn->query($sql_expenses) === TRUE) {
        echo json_encode(['status' => 'success', 'message' => 'Expense deleted successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error deleting expense: ' . $conn->error]);
    }

    $conn->close();
    exit();
}
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
                    <h3 class="fw-bold mb-3">Expenses</h3>
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                            <li class="breadcrumb-item active">Expenses</li>
                        </ol>
                    </nav>
                </div>

                <section class="section">
                    <div class="row">
                        <div class="col-lg-12">

                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Enter Expense Details</h5>

                                    <!-- Expense Form -->
                                    <form action="expenses.php" method="post">
                                        <input type="hidden" name="id" id="edit-id" value="">
                                        <div class="row mb-3">
                                            <label for="description" class="col-sm-2 col-form-label">Description</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="edit-description" name="description" required>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="amount" class="col-sm-2 col-form-label">Amount</label>
                                            <div class="col-sm-10">
                                                <input type="number" class="form-control" id="edit-amount" name="amount" step="0.01" required>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="date" class="col-sm-2 col-form-label">Date</label>
                                            <div class="col-sm-10">
                                                <input type="date" class="form-control" id="edit-date" name="date" required>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-sm-10 offset-sm-2">
                                                <button type="submit" class="btn btn-primary rounded"><i class="fas fa-save"></i> Save</button>
                                            </div>
                                        </div>
                                    </form><!-- End Expense Form -->

                                </div>
                            </div>

                        </div>

                        <div class="col-lg-12">

                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Expenses List</h5>
                                </div>
                                <div class="card-body">

                                    <div class="table-responsive">
                                        <!-- Expenses List Table -->
                                        <table class="table table-bordered datatable" id="basic-datatables">
                                            <thead>
                                            <tr>
                                                <th>Description</th>
                                                <th>Amount</th>
                                                <th>Date</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            $expenses = get_expenses($conn);
                                            foreach ($expenses as $expense) { ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($expense['description']); ?></td>
                                                    <td><?php echo htmlspecialchars(number_format($expense['amount'], 2)); ?></td>
                                                    <td><?php echo htmlspecialchars($expense['date']); ?></td>
                                                    <td>
                                                        <button type='button' class='btn btn-sm btn-primary rounded edit-expense mb-2'
                                                                data-id='<?php echo $expense['id']; ?>'
                                                                data-description='<?php echo htmlspecialchars($expense['description'], ENT_QUOTES); ?>'
                                                                data-amount='<?php echo $expense['amount']; ?>'
                                                                data-date='<?php echo $expense['date']; ?>'>
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button type='button' class='btn btn-sm btn-danger rounded delete-expense mb-2'
                                                                data-id='<?php echo $expense['id']; ?>'>
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            </tbody>
                                        </table><!-- End Expenses List Table -->
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
<script>
    // Delete Expense
    const deleteButtons = document.querySelectorAll('.delete-expense');
    deleteButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            const id = button.dataset.id;
            if (confirm('Are you sure you want to delete this expense?')) {
                fetch('expenses.php?delete_id=' + id, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            alert(data.message);
                            window.location.reload(); // ✅ Reloads the page
                        } else {
                            alert("Error: " + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while deleting the expense.');
                    });
            }
        });
    });

    // Edit Expense
    const editButtons = document.querySelectorAll('.edit-expense');
    editButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            const id = button.dataset.id;
            const description = button.dataset.description;
            const amount = button.dataset.amount;
            const date = button.dataset.date;

            document.getElementById('edit-id').value = id;
            document.getElementById('edit-description').value = description;
            document.getElementById('edit-amount').value = amount;
            document.getElementById('edit-date').value = date;
        });
    });
</script>
</body>
</html>
