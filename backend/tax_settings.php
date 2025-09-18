<?php
// tax_settings.php

include('models/tax_rates.php');
include('models/tax_rules.php');
include('./database/db_connection.php'); // Ensure db connection is included

// Initialize models
$taxRateModel = new TaxRate($conn);
$taxRuleModel = new TaxRule($conn);

$message = '';
$taxRates = [];
$taxRules = [];

// Handle form submission for tax rates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['action']) && $_POST['action'] === 'add_tax_rate') {
    $country = $_POST['country'];
    $state = $_POST['state'] ?? null; // State can be null
    $taxRate = $_POST['tax_rate'];
    $isActive = isset($_POST['is_active']) ? 1 : 0;

    if ($taxRateModel->createTaxRate($country, $state, $taxRate, $isActive)) {
      $message = "Tax rate added successfully.";
    } else {
      $message = "Failed to add tax rate.";
    }
  } elseif (isset($_POST['action']) && $_POST['action'] === 'edit_tax_rate') {
    $taxRateId = $_POST['tax_rate_id'];
    $country = $_POST['country'];
    $state = $_POST['state'] ?? null;
    $taxRate = $_POST['tax_rate'];
    $isActive = isset($_POST['is_active']) ? 1 : 0;

    if ($taxRateModel->updateTaxRate($taxRateId, $country, $state, $taxRate, $isActive)) {
      $message = "Tax rate updated successfully.";
    } else {
      $message = "Failed to update tax rate.";
    }
  } elseif (isset($_POST['action']) && $_POST['action'] === 'delete_tax_rate') {
    $taxRateId = $_POST['tax_rate_id'];
    if ($taxRateModel->deleteTaxRate($taxRateId)) {
      $message = "Tax rate deleted successfully.";
    } else {
      $message = "Failed to delete tax rate.";
    }
  }

  // Handle form submission for tax rules
  if (isset($_POST['action']) && $_POST['action'] === 'add_tax_rule') {
    $ruleName = $_POST['rule_name'];
    $taxRateId = $_POST['tax_rate_id'];
    $appliesToProductType = $_POST['applies_to_product_type'] ?? null;
    $appliesToOrderTotal = $_POST['applies_to_order_total'] ?? null;
    $isActive = isset($_POST['is_active']) ? 1 : 0;

    if ($taxRuleModel->createTaxRule($ruleName, $taxRateId, $appliesToProductType, $appliesToOrderTotal, $isActive)) {
      $message = ($message ? $message . " " : "") . "Tax rule added successfully.";
    } else {
      $message = ($message ? $message . " " : "") . "Failed to add tax rule.";
    }
  } elseif (isset($_POST['action']) && $_POST['action'] === 'edit_tax_rule') {
    $taxRuleId = $_POST['tax_rule_id'];
    $ruleName = $_POST['rule_name'];
    $taxRateId = $_POST['tax_rate_id'];
    $appliesToProductType = $_POST['applies_to_product_type'] ?? null;
    $appliesToOrderTotal = $_POST['applies_to_order_total'] ?? null;
    $isActive = isset($_POST['is_active']) ? 1 : 0;

    if ($taxRuleModel->updateTaxRule($taxRuleId, $ruleName, $taxRateId, $appliesToProductType, $appliesToOrderTotal, $isActive)) {
      $message = ($message ? $message . " " : "") . "Tax rule updated successfully.";
    } else {
      $message = ($message ? $message . " " : "") . "Failed to update tax rule.";
    }
  } elseif (isset($_POST['action']) && $_POST['action'] === 'delete_tax_rule') {
    $taxRuleId = $_POST['tax_rule_id'];
    if ($taxRuleModel->deleteTaxRule($taxRuleId)) {
      $message = ($message ? $message . " " : "") . "Tax rule deleted successfully.";
    } else {
      $message = ($message ? $message . " " : "") . "Failed to delete tax rule.";
    }
  }
}

// Fetch all tax rates and rules for display
$taxRates = $taxRateModel->getAllTaxRates();
$taxRules = $taxRuleModel->getAllTaxRules();

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
            <h3 class="fw-bold mb-3">Tax Settings</h3>
            <nav>
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                <li class="breadcrumb-item active">Tax Settings</li>
              </ol>
            </nav>
          </div>

          <section class="section">
            <div class="row">
              <div class="col-lg-12">
                <div class="card">
                  <div class="card-body">
                    <h5 class="card-title">Tax Rates</h5>

                    <?php if ($message && strpos($message, 'Tax rate') !== false): ?>
                      <div class="alert alert-info">
                        <?= htmlspecialchars($message) ?>
                      </div>
                    <?php endif; ?>

                    <!-- Add New Tax Rate Form -->
                    <button type="button" class="btn btn-primary mb-3 rounded" data-bs-toggle="modal" data-bs-target="#addTaxRateModal">
                      <i class="fas fa-plus"></i>
                    </button>

                    <!-- Tax Rates Table -->
                    <?php if ($taxRates && $taxRates->num_rows > 0): ?>
                      <table class="table table-bordered" id="basic-datatables">
                        <thead>
                          <tr>
                            <th>Country</th>
                            <th>State</th>
                            <th>Tax Rate (%)</th>
                            <th>Status</th>
                            <th>Actions</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php while ($row = $taxRates->fetch_assoc()): ?>
                            <tr>
                              <td><?= htmlspecialchars($row['country']) ?></td>
                              <td><?= htmlspecialchars($row['state'] ?? 'N/A') ?></td>
                              <td><?= htmlspecialchars($row['tax_rate']) ?></td>
                              <td><?= $row['is_active'] ? 'Active' : 'Inactive' ?></td>
                              <td>
                                <button type="button" class="btn btn-sm btn-info edit-tax-rate-btn rounded" data-bs-toggle="modal" data-bs-target="#editTaxRateModal"
                                  data-id="<?= $row['tax_rate_id'] ?>"
                                  data-country="<?= htmlspecialchars($row['country']) ?>"
                                  data-state="<?= htmlspecialchars($row['state']) ?>"
                                  data-rate="<?= htmlspecialchars($row['tax_rate']) ?>"
                                  data-isactive="<?= $row['is_active'] ?>">
                                  <i class="fas fa-edit"></i>
                                </button>
                                <form method="POST" style="display:inline-block;">
                                  <input type="hidden" name="action" value="delete_tax_rate">
                                  <input type="hidden" name="tax_rate_id" value="<?= $row['tax_rate_id'] ?>">
                                  <button type="submit" class="btn btn-sm btn-danger rounded"><i class="fas fa-trash"></i></button>
                                </form>
                              </td>
                            </tr>
                          <?php endwhile; ?>
                        </tbody>
                      </table>
                    <?php else: ?>
                      <p>No tax rates found.</p>
                    <?php endif; ?>

                  </div>
                </div>
              </div>

              <!-- <div class="col-lg-6">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Tax Rules</h5>

              <?php if ($message && strpos($message, 'Tax rule') !== false): ?>
                <div class="alert alert-info">
                  <?= htmlspecialchars($message) ?>
                </div>
              <?php endif; ?>

              <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addTaxRuleModal">
                Add New Tax Rule
              </button>

              <?php if ($taxRules && $taxRules->num_rows > 0): ?>
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>Rule Name</th>
                      <th>Tax Rate</th>
                      <th>Applies to Product Type</th>
                      <th>Applies to Order Total</th>
                      <th>Status</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php while ($row = $taxRules->fetch_assoc()): ?>
                    <tr>
                      <td><?= htmlspecialchars($row['rule_name']) ?></td>
                      <td>
                        <?php
                        $rateInfo = $taxRateModel->getTaxRateById($row['tax_rate_id']);
                        echo htmlspecialchars($rateInfo['country'] . ' - ' . ($rateInfo['state'] ?? 'N/A') . ' (' . $rateInfo['tax_rate'] . '%)');
                        ?>
                      </td>
                      <td><?= htmlspecialchars($row['applies_to_product_type'] ?? 'N/A') ?></td>
                      <td><?= htmlspecialchars($row['applies_to_order_total'] ?? 'N/A') ?></td>
                      <td><?= $row['is_active'] ? 'Active' : 'Inactive' ?></td>
                      <td>
                        <button type="button" class="btn btn-sm btn-info edit-tax-rule-btn" data-bs-toggle="modal" data-bs-target="#editTaxRuleModal"
                                data-id="<?= $row['tax_rule_id'] ?>"
                                data-rulename="<?= htmlspecialchars($row['rule_name']) ?>"
                                data-rateid="<?= $row['tax_rate_id'] ?>"
                                data-producttype="<?= htmlspecialchars($row['applies_to_product_type']) ?>"
                                data-ordertotal="<?= htmlspecialchars($row['applies_to_order_total']) ?>"
                                data-isactive="<?= $row['is_active'] ?>">
                          Edit
                        </button>
                        <form method="POST" style="display:inline-block;">
                          <input type="hidden" name="action" value="delete_tax_rule">
                          <input type="hidden" name="tax_rule_id" value="<?= $row['tax_rule_id'] ?>">
                          <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                      </td>
                    </tr>
                    <?php endwhile; ?>
                  </tbody>
                </table>
              <?php else: ?>
                <p>No tax rules found.</p>
              <?php endif; ?>

            </div>
          </div>
        </div> -->
            </div>
          </section>

          <!-- Add Tax Rate Modal -->
          <div class="modal fade" id="addTaxRateModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Add New Tax Rate</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <form method="POST" id="addTaxRateForm">
                    <input type="hidden" name="action" value="add_tax_rate">
                    <div class="mb-3">
                      <label for="add_country" class="form-label">Country</label>
                      <select class="form-select" id="add_country" name="country" required>
                        <option value="">Select Country</option>
                        <option value="Nigeria">Nigeria</option>
                        <option value="Ghana">Ghana</option>
                        <option value="United States">United States</option>
                        <!-- Add more countries as needed -->
                      </select>
                    </div>
                    <div class="mb-3">
                      <label for="add_state" class="form-label">State/Province (Optional)</label>
                      <select class="form-select" id="add_state" name="state">
                        <option value="">Select State/Province</option>
                      </select>
                    </div>
                    <div class="mb-3">
                      <label for="tax_rate" class="form-label">Tax Rate (%)</label>
                      <input type="number" step="0.01" class="form-control" id="tax_rate" name="tax_rate" required>
                    </div>
                    <div class="mb-3 form-check">
                      <input type="checkbox" class="form-check-input" id="is_active_rate" name="is_active" value="1" checked>
                      <label class="form-check-label" for="is_active_rate">Active</label>
                    </div>
                    <button type="submit" class="btn btn-primary rounded"><i class="fas fa-plus"></i></button>
                  </form>
                </div>
              </div>
            </div>
          </div>

          <!-- Edit Tax Rate Modal -->
          <div class="modal fade" id="editTaxRateModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Edit Tax Rate</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <form method="POST" id="editTaxRateForm">
                    <input type="hidden" name="action" value="edit_tax_rate">
                    <input type="hidden" name="tax_rate_id" id="edit_tax_rate_id">
                    <div class="mb-3">
                      <label for="edit_country" class="form-label">Country</label>
                      <select class="form-select" id="edit_country" name="country" required>
                        <option value="">Select Country</option>
                        <option value="Nigeria">Nigeria</option>
                        <option value="Ghana">Ghana</option>
                        <option value="United States">United States</option>
                        <!-- Add more countries as needed -->
                      </select>
                    </div>
                    <div class="mb-3">
                      <label for="edit_state" class="form-label">State/Province (Optional)</label>
                      <select class="form-select" id="edit_state" name="state">
                        <option value="">Select State/Province</option>
                      </select>
                    </div>
                    <div class="mb-3">
                      <label for="edit_tax_rate" class="form-label">Tax Rate (%)</label>
                      <input type="number" step="0.01" class="form-control" id="edit_tax_rate" name="tax_rate" required>
                    </div>
                    <div class="mb-3 form-check">
                      <input type="checkbox" class="form-check-input" id="edit_is_active_rate" name="is_active" value="1">
                      <label class="form-check-label" for="edit_is_active_rate">Active</label>
                    </div>
                    <button type="submit" class="btn btn-primary rounded"><i class="fas fa-save"></i></button>
                  </form>
                </div>
              </div>
            </div>
          </div>

          <!-- Add Tax Rule Modal -->
          <div class="modal fade" id="addTaxRuleModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Add New Tax Rule</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <form method="POST" id="addTaxRuleForm">
                    <input type="hidden" name="action" value="add_tax_rule">
                    <div class="mb-3">
                      <label for="rule_name" class="form-label">Rule Name</label>
                      <input type="text" class="form-control" id="rule_name" name="rule_name" required>
                    </div>
                    <div class="mb-3">
                      <label for="tax_rate_id" class="form-label">Tax Rate</label>
                      <select class="form-select" id="tax_rate_id" name="tax_rate_id" required>
                        <option value="">-- Select Tax Rate --</option>
                        <?php
                        // Populate tax rate dropdown
                        if ($taxRates && $taxRates->num_rows > 0) {
                          while ($rate = $taxRates->fetch_assoc()) {
                            echo "<option value='" . $rate['tax_rate_id'] . "'>" . htmlspecialchars($rate['country'] . ' - ' . ($rate['state'] ?? 'N/A') . ' (' . $rate['tax_rate'] . '%)') . "</option>";
                          }
                        }
                        ?>
                      </select>
                    </div>
                    <div class="mb-3">
                      <label for="applies_to_product_type" class="form-label">Applies to Product Type (Optional)</label>
                      <input type="text" class="form-control" id="applies_to_product_type" name="applies_to_product_type" placeholder="e.g., physical, digital">
                    </div>
                    <div class="mb-3">
                      <label for="applies_to_order_total" class="form-label">Applies to Order Total (Optional)</label>
                      <input type="number" step="0.01" class="form-control" id="applies_to_order_total" name="applies_to_order_total">
                    </div>
                    <div class="mb-3 form-check">
                      <input type="checkbox" class="form-check-input" id="is_active_rule" name="is_active" value="1" checked>
                      <label class="form-check-label" for="is_active_rule">Active</label>
                    </div>
                    <button type="submit" class="btn btn-primary rounded"><i class="fas fa-save"></i></button>
                  </form>
                </div>
              </div>
            </div>
          </div>

          <!-- Edit Tax Rule Modal -->
          <div class="modal fade" id="editTaxRuleModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Edit Tax Rule</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <form method="POST" id="editTaxRuleForm">
                    <input type="hidden" name="action" value="edit_tax_rule">
                    <input type="hidden" name="tax_rule_id" id="edit_tax_rule_id">
                    <div class="mb-3">
                      <label for="edit_rule_name" class="form-label">Rule Name</label>
                      <input type="text" class="form-control" id="edit_rule_name" name="rule_name" required>
                    </div>
                    <div class="mb-3">
                      <label for="edit_tax_rate_id" class="form-label">Tax Rate</label>
                      <select class="form-select" id="edit_tax_rate_id" name="tax_rate_id" required>
                        <option value="">-- Select Tax Rate --</option>
                        <?php
                        // Populate tax rate dropdown for editing
                        // Re-fetch tax rates to ensure the dropdown is populated correctly
                        $taxRatesForEdit = $taxRateModel->getAllTaxRates();
                        if ($taxRatesForEdit && $taxRatesForEdit->num_rows > 0) {
                          while ($rate = $taxRatesForEdit->fetch_assoc()) {
                            echo "<option value='" . $rate['tax_rate_id'] . "'>" . htmlspecialchars($rate['country'] . ' - ' . ($rate['state'] ?? 'N/A') . ' (' . $rate['tax_rate'] . '%)') . "</option>";
                          }
                        }
                        ?>
                      </select>
                    </div>
                    <div class="mb-3">
                      <label for="edit_applies_to_product_type" class="form-label">Applies to Product Type (Optional)</label>
                      <input type="text" class="form-control" id="edit_applies_to_product_type" name="applies_to_product_type" placeholder="e.g., physical, digital">
                    </div>
                    <div class="mb-3">
                      <label for="edit_applies_to_order_total" class="form-label">Applies to Order Total (Optional)</label>
                      <input type="number" step="0.01" class="form-control" id="edit_applies_to_order_total" name="applies_to_order_total">
                    </div>
                    <div class="mb-3 form-check">
                      <input type="checkbox" class="form-check-input" id="edit_is_active_rule" name="is_active" value="1">
                      <label class="form-check-label" for="edit_is_active_rule">Active</label>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Tax Rule</button>
                  </form>
                </div>
              </div>
            </div>
          </div>

          <script>
            // JavaScript to populate the edit tax rate modal
            document.addEventListener('DOMContentLoaded', function() {
              const countryStateMap = {
                "Nigeria": ["Abia", "Adamawa", "Akwa Ibom", "Anambra", "Bauchi", "Bayelsa", "Benue", "Borno", "Cross River", "Delta", "Ebonyi", "Edo", "Ekiti", "Enugu", "Gombe", "Imo", "Jigawa", "Kaduna", "Kano", "Katsina", "Kebbi", "Kogi", "Kwara", "Lagos", "Nasarawa", "Niger", "Ogun", "Ondo", "Osun", "Oyo", "Plateau", "Rivers", "Sokoto", "Taraba", "Yobe", "Zamfara", "Federal Capital Territory"],
                "Ghana": ["Greater Accra", "Ashanti", "Central", "Eastern", "Northern", "Volta", "Western", "Brong-Ahafo", "Upper East", "Upper West"],
                "United States": ["Alabama", "Alaska", "Arizona", "Arkansas", "California", "Colorado", "Connecticut", "Delaware", "Florida", "Georgia", "Hawaii", "Idaho", "Illinois", "Indiana", "Iowa", "Kansas", "Kentucky", "Louisiana", "Maine", "Maryland", "Massachusetts", "Michigan", "Minnesota", "Mississippi", "Missouri", "Montana", "Nebraska", "Nevada", "New Hampshire", "New Jersey", "New Mexico", "New York", "North Carolina", "North Dakota", "Ohio", "Oklahoma", "Oregon", "Pennsylvania", "Rhode Island", "South Carolina", "South Dakota", "Tennessee", "Texas", "Utah", "Vermont", "Virginia", "Washington", "West Virginia", "Wisconsin", "Wyoming"]
              };

              function populateStates(countrySelectElement, stateSelectElement, selectedState = null) {
                const selectedCountry = countrySelectElement.value;
                stateSelectElement.innerHTML = '<option value="">Select State/Province</option>'; // Reset states

                if (selectedCountry && countryStateMap[selectedCountry]) {
                  countryStateMap[selectedCountry].forEach(state => {
                    const option = document.createElement('option');
                    option.value = state;
                    option.textContent = state;
                    stateSelectElement.appendChild(option);
                  });
                }
                if (selectedState) {
                  stateSelectElement.value = selectedState;
                }
              }

              // For Add Tax Rate Modal
              const addCountrySelect = document.getElementById('add_country');
              const addStateSelect = document.getElementById('add_state');
              if (addCountrySelect && addStateSelect) {
                addCountrySelect.addEventListener('change', function() {
                  populateStates(addCountrySelect, addStateSelect);
                });
              }

              // For Edit Tax Rate Modal
              const editCountrySelect = document.getElementById('edit_country');
              const editStateSelect = document.getElementById('edit_state');
              if (editCountrySelect && editStateSelect) {
                editCountrySelect.addEventListener('change', function() {
                  populateStates(editCountrySelect, editStateSelect);
                });
              }

              // Modify the existing edit-tax-rate-btn click listener to handle dropdowns
              var editRateButtons = document.querySelectorAll('.edit-tax-rate-btn');
              editRateButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                  var id = this.getAttribute('data-id');
                  var country = this.getAttribute('data-country');
                  var state = this.getAttribute('data-state');
                  var rate = this.getAttribute('data-rate');
                  var isActive = this.getAttribute('data-isactive');

                  document.getElementById('edit_tax_rate_id').value = id;
                  document.getElementById('edit_tax_rate').value = rate;
                  var isActiveCheckbox = document.getElementById('edit_is_active_rate');
                  isActiveCheckbox.checked = (isActive == 1);

                  // Set country and populate states for edit modal
                  editCountrySelect.value = country;
                  // Use a timeout to ensure states are populated before setting value
                  setTimeout(() => {
                    populateStates(editCountrySelect, editStateSelect, state === 'N/A' ? '' : state);
                  }, 50); // Small delay
                });
              });

              // JavaScript to populate the edit tax rule modal
              var editRuleButtons = document.querySelectorAll('.edit-tax-rule-btn');
              editRuleButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                  var id = this.getAttribute('data-id');
                  var ruleName = this.getAttribute('data-rulename');
                  var rateId = this.getAttribute('data-rateid');
                  var productType = this.getAttribute('data-producttype');
                  var orderTotal = this.getAttribute('data-ordertotal');
                  var isActive = this.getAttribute('data-isactive');

                  document.getElementById('edit_tax_rule_id').value = id;
                  document.getElementById('edit_rule_name').value = ruleName;
                  document.getElementById('edit_tax_rate_id').value = rateId;
                  document.getElementById('edit_applies_to_product_type').value = productType === 'N/A' ? '' : productType;
                  document.getElementById('edit_applies_to_order_total').value = orderTotal === 'N/A' ? '' : orderTotal;
                  var isActiveCheckbox = document.getElementById('edit_is_active_rule');
                  isActiveCheckbox.checked = (isActive == 1);
                });
              });
            });
          </script>
        </div>
      </div>

      <?php include('components/footer.php'); ?>
    </div>
  </div>
  <?php include('components/script.php'); ?>
</body>

</html>