<?php
// email_notifications.php

include('models/email_templates.php');
include('./database/db_connection.php'); // Ensure db connection is included

// Initialize EmailTemplate model
$emailTemplateModel = new EmailTemplate($conn);

$message = '';
$templates = [];

// Handle form submission for adding/editing templates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'add_template') {
        $name = $_POST['template_name'];
        $subject = $_POST['subject'];
        $body = $_POST['body'];
        $trigger = $_POST['event_trigger'];

        if ($emailTemplateModel->createTemplate($name, $subject, $body, $trigger)) {
            $message = "Email template added successfully.";
        } else {
            $message = "Failed to add email template.";
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'edit_template') {
        $templateId = $_POST['template_id'];
        $name = $_POST['template_name'];
        $subject = $_POST['subject'];
        $body = $_POST['body'];
        $trigger = $_POST['event_trigger'];

        if ($emailTemplateModel->updateTemplate($templateId, $name, $subject, $body, $trigger)) {
            $message = "Email template updated successfully.";
        } else {
            $message = "Failed to update email template.";
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'delete_template') {
        $templateId = $_POST['template_id'];
        if ($emailTemplateModel->deleteTemplate($templateId)) {
            $message = "Email template deleted successfully.";
        } else {
            $message = "Failed to delete email template.";
        }
    }
}

// Fetch all templates for display
$templates = $emailTemplateModel->getAllTemplates();

?>

<!DOCTYPE html>
<html lang="en">

<?php include('components/head.php'); ?>

<body class="main bg-dark text-white">
  <!-- ======= Header ======= -->
  <?php include('components/header.php'); ?>
  <!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <?php include('components/sidebar.php'); ?>
  <!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1 class="text-white">Email Notification Settings</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
          <li class="breadcrumb-item active">Email Notifications</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Email Templates</h5>

              <?php if ($message): ?>
                <div class="alert alert-info">
                  <?= htmlspecialchars($message) ?>
                </div>
              <?php endif; ?>

              <!-- Add New Template Form -->
              <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addTemplateModal">
                Add New Template
              </button>

              <!-- Email Templates Table -->
              <?php if ($templates && $templates->num_rows > 0): ?>
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>Template Name</th>
                      <th>Subject</th>
                      <th>Event Trigger</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php while($row = $templates->fetch_assoc()): ?>
                    <tr>
                      <td><?= htmlspecialchars($row['template_name']) ?></td>
                      <td><?= htmlspecialchars($row['subject']) ?></td>
                      <td><?= htmlspecialchars($row['event_trigger']) ?></td>
                      <td>
                        <button type="button" class="btn btn-sm btn-info edit-btn" data-bs-toggle="modal" data-bs-target="#editTemplateModal"
                                data-id="<?= $row['template_id'] ?>"
                                data-name="<?= htmlspecialchars($row['template_name']) ?>"
                                data-subject="<?= htmlspecialchars($row['subject']) ?>"
                                data-body="<?= htmlspecialchars($row['body']) ?>"
                                data-trigger="<?= htmlspecialchars($row['event_trigger']) ?>">
                          Edit
                        </button>
                        <form method="POST" style="display:inline-block;">
                          <input type="hidden" name="action" value="delete_template">
                          <input type="hidden" name="template_id" value="<?= $row['template_id'] ?>">
                          <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                      </td>
                    </tr>
                    <?php endwhile; ?>
                  </tbody>
                </table>
              <?php else: ?>
                <p>No email templates found.</p>
              <?php endif; ?>

            </div>
          </div>
        </div>
      </div>
    </section>

  </main><!-- End #main -->

  <!-- Add Template Modal -->
  <div class="modal fade" id="addTemplateModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title text-dark">Add New Email Template</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form method="POST" id="addTemplateForm">
            <input type="hidden" name="action" value="add_template">
            <div class="mb-3">
              <input type="text" class="form-control" id="template_name" name="template_name" placeholder="Template Name" required>
            </div>
            <div class="mb-3">
              <input type="text" class="form-control" id="subject" name="subject" placeholder="Subject" required>
            </div>
            <div class="mb-3">
              <textarea class="form-control" id="body" name="body" rows="10" placeholder="Body" required></textarea>
            </div>
            <div class="mb-3">
              <label for="event_trigger" class="form-label text-dark">Event Trigger</label>
              <input type="text" class="form-control" id="event_trigger" name="event_trigger" required placeholder="e.g., new_order, password_reset">
            </div>
            <button type="submit" class="btn btn-primary">Save Template</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Edit Template Modal -->
  <div class="modal fade" id="editTemplateModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Email Template</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form method="POST" id="editTemplateForm">
            <input type="hidden" name="action" value="edit_template">
            <input type="hidden" name="template_id" id="edit_template_id">
            <div class="mb-3">
              <label for="edit_template_name" class="form-label">Template Name</label>
              <input type="text" class="form-control" id="edit_template_name" name="template_name" required>
            </div>
            <div class="mb-3">
              <label for="edit_subject" class="form-label">Subject</label>
              <input type="text" class="form-control" id="edit_subject" name="subject" required>
            </div>
            <div class="mb-3">
              <label for="edit_body" class="form-label">Body</label>
              <textarea class="form-control" id="edit_body" name="body" rows="10" required></textarea>
            </div>
            <div class="mb-3">
              <label for="edit_event_trigger" class="form-label">Event Trigger</label>
              <input type="text" class="form-control" id="edit_event_trigger" name="event_trigger" required placeholder="e.g., new_order, password_reset">
            </div>
            <button type="submit" class="btn btn-primary">Update Template</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script>
    // JavaScript to populate the edit modal
    document.addEventListener('DOMContentLoaded', function() {
      var editButtons = document.querySelectorAll('.edit-btn');
      editButtons.forEach(function(button) {
        button.addEventListener('click', function() {
          var id = this.getAttribute('data-id');
          var name = this.getAttribute('data-name');
          var subject = this.getAttribute('data-subject');
          var body = this.getAttribute('data-body');
          var trigger = this.getAttribute('data-trigger');

          document.getElementById('edit_template_id').value = id;
          document.getElementById('edit_template_name').value = name;
          document.getElementById('edit_subject').value = subject;
          document.getElementById('edit_body').value = body;
          document.getElementById('edit_event_trigger').value = trigger;
        });
      });
    });
  </script>

  <!-- ======= Footer ======= -->
  <?php include('components/footer.php'); ?>
  <?php include('components/scripts.php'); ?>

</body>

</html>
