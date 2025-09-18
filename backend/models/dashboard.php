<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
include './database/db_connection.php';
session_start();


// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}


// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch branch_id, country, state, and role for the logged-in user
$user_branch_id = null;
$user_country = null;
$user_state = null;
$user_role = $_SESSION['role'] ?? null;

if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT l.branch_id, b.country, b.state, l.role FROM login l LEFT JOIN branches b ON l.branch_id = b.branch_id WHERE l.id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_data = $result->fetch_assoc();
    if ($user_data) {
        $user_branch_id = $user_data['branch_id'];
        $user_country = $user_data['country'];
        $user_state = $user_data['state'];
        $user_role = $user_data['role']; // Ensure role is up-to-date from DB
    }
    $stmt->close();
}

// Helper function to prepare and execute queries with optional filtering
function executeFilteredQuery($conn, $baseSql, $user_role, $user_country, $user_state, $dateCondition = "", $additionalJoins = "", $additionalWhere = "", $groupBy = "", $orderBy = "", $locationTableAlias = "b") {
    $sql = $baseSql;
    $queryParams = [];
    $queryTypes = "";

    if ($user_role !== 'Superuser' && $user_role !== 'CEO') {
        $sql .= $additionalJoins;
        $sql .= " WHERE 1=1"; // Start WHERE clause
        if ($dateCondition) {
            $sql .= " " . $dateCondition;
        }
        if ($additionalWhere) {
            $sql .= " AND " . $additionalWhere;
        }
        if ($user_country !== null) {
            $sql .= " AND " . $locationTableAlias . ".country = ?";
            $queryTypes .= "s";
            $queryParams[] = $user_country;
        }
        if ($user_state !== null) {
            $sql .= " AND " . $locationTableAlias . ".state = ?";
            $queryTypes .= "s";
            $queryParams[] = $user_state;
        }
    } else {
        // For Superuser/CEO, still include joins if needed for columns, but no filtering
        $sql .= $additionalJoins;
        if ($dateCondition || $additionalWhere) { // Only add WHERE if there's any condition
            $sql .= " WHERE 1=1";
            if ($dateCondition) {
                $sql .= " " . $dateCondition;
            }
            if ($additionalWhere) {
                $sql .= " AND " . $additionalWhere;
            }
        }
    }

    if ($groupBy) {
        $sql .= " " . $groupBy;
    }
    if ($orderBy) {
        $sql .= " " . $orderBy;
    }

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        error_log("Prepare failed: " . $conn->error . " for SQL: " . $sql);
        return false;
    }

    if (!empty($queryParams)) {
        $bind_params = [];
        $bind_params[] = $queryTypes;
        foreach ($queryParams as $key => $value) {
            $bind_params[] = &$queryParams[$key];
        }
        call_user_func_array([$stmt, 'bind_param'], $bind_params);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result;
}


// 1. get sum of total sales
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'today';
$today = date("Y-m-d");
$currentMonth = date("m");
$currentYear = date("Y");

$dateConditionSales = "";
switch ($filter) {
    case 'month':
        $dateConditionSales = "AND MONTH(td.transactiondate) = '$currentMonth' AND YEAR(td.transactiondate) = '$currentYear'";
        $label = "This Month";
        break;
    case 'year':
        $dateConditionSales = "AND YEAR(td.transactiondate) = '$currentYear'";
        $label = "This Year";
        break;
    case 'today':
    default:
        $dateConditionSales = "AND DATE(td.transactiondate) = '$today'";
        $label = "Today";
        break;
}

$baseSqlSales = "SELECT SUM(td.amount) AS total FROM transactiondetails td LEFT JOIN login l ON td.cashier = l.id";
$additionalJoinsSales = " LEFT JOIN branches b ON l.branch_id = b.branch_id";
$additionalWhereSales = "td.description = 'Sales'";

$result = executeFilteredQuery($conn, $baseSqlSales, $user_role, $user_country, $user_state, $dateConditionSales, $additionalJoinsSales, $additionalWhereSales);
$total_sales = ($result && $result->num_rows > 0) ? $result->fetch_assoc()['total'] : 0;

// ============================================================================================

// 2. get sum of total revenue
$rfilter = isset($_GET['rfilter']) ? $_GET['rfilter'] : 'today';
$dateConditionRevenue = "";
switch ($rfilter) {
    case 'month':
        $dateConditionRevenue = "AND MONTH(td.transactiondate) = '$currentMonth' AND YEAR(td.transactiondate) = '$currentYear'";
        $revenue_label = "This Month";
        break;
    case 'year':
        $dateConditionRevenue = "AND YEAR(td.transactiondate) = '$currentYear'";
        $revenue_label = "This Year";
        break;
    case 'today':
    default:
        $dateConditionRevenue = "AND DATE(td.transactiondate) = '$today'";
        $revenue_label = "Today";
        break;
}

$baseSqlRevenue = "SELECT SUM(td.profit) AS total FROM transactiondetails td LEFT JOIN login l ON td.cashier = l.id";
$additionalJoinsRevenue = " LEFT JOIN branches b ON l.branch_id = b.branch_id";
$additionalWhereRevenue = "(td.description = 'Sales' OR td.description = 'Refund')";

$result = executeFilteredQuery($conn, $baseSqlRevenue, $user_role, $user_country, $user_state, $dateConditionRevenue, $additionalJoinsRevenue, $additionalWhereRevenue);
$total_revenue = ($result && $result->num_rows > 0) ? $result->fetch_assoc()['total'] : 0;


// =========================== Fetch Sales transactions =====================================================
$transactions = [];
$baseSqlTransactions = "SELECT td.productname, td.units, td.amount, td.transactiondate FROM transactiondetails td LEFT JOIN login l ON td.cashier = l.id";
$additionalJoinsTransactions = " LEFT JOIN branches b ON l.branch_id = b.branch_id";
$additionalWhereTransactions = "td.description = 'Sales'";
$orderByTransactions = " ORDER BY td.transactiondate DESC";

$result = executeFilteredQuery($conn, $baseSqlTransactions, $user_role, $user_country, $user_state, "", $additionalJoinsTransactions, $additionalWhereTransactions, "", $orderByTransactions);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $transactions[] = $row;
    }
} else {
    error_log("Failed to fetch Sales transactions: " . $conn->error);
}


// ===============Fetches top selling transactions  ==========================
$topselling_transactions = [];
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'Today';

$dateConditionTopSelling = "";
switch ($filter) {
    case 'Today':
        $dateConditionTopSelling = "AND DATE(td.transactiondate) = CURDATE()";
        break;
    case 'This Month':
        $dateConditionTopSelling = "AND MONTH(td.transactiondate) = MONTH(CURDATE())";
        break;
    case 'This Year':
        $dateConditionTopSelling = "AND YEAR(td.transactiondate) = YEAR(CURDATE())";
        break;
    default:
        $dateConditionTopSelling = "";
        break;
}

$baseSqlTopSelling = "SELECT td.productname, SUM(td.units) AS units, SUM(td.amount) AS amount, td.transactiondate 
                      FROM transactiondetails td LEFT JOIN login l ON td.cashier = l.id";
$additionalJoinsTopSelling = " LEFT JOIN branches b ON l.branch_id = b.branch_id";
$additionalWhereTopSelling = "td.description='Sales'";
$groupByTopSelling = " GROUP BY td.productname";
$orderByTopSelling = " ORDER BY units DESC";

$result = executeFilteredQuery($conn, $baseSqlTopSelling, $user_role, $user_country, $user_state, $dateConditionTopSelling, $additionalJoinsTopSelling, $additionalWhereTopSelling, $groupByTopSelling, $orderByTopSelling);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $topselling_transactions[] = $row;
    }
} else {
    error_log("Failed to fetch Top Selling transactions: " . $conn->error);
}


// ================= fetches inventory quantity ================
$baseSqlInventoryQuantity = "SELECT COUNT(p.productid) AS total FROM product p JOIN branch_product_inventory bpi ON p.productid = bpi.productid";
$additionalJoinsInventoryQuantity = " JOIN branches b ON bpi.branch_id = b.branch_id";

$result = executeFilteredQuery($conn, $baseSqlInventoryQuantity, $user_role, $user_country, $user_state, "", $additionalJoinsInventoryQuantity);
$inventory_quantity = ($result && $result->num_rows > 0) ? $result->fetch_assoc()['total'] : 0;


// ===================== fetches out of stock products ==================
$out_of_stock = [];
$baseSqlOutOfStock = "SELECT p.productname, bpi.quantity FROM product p JOIN branch_product_inventory bpi ON p.productid = bpi.productid";
$additionalJoinsOutOfStock = " JOIN branches b ON bpi.branch_id = b.branch_id";
$additionalWhereOutOfStock = "bpi.quantity = 0";
$orderByOutOfStock = " ORDER BY p.productname ASC";

$result = executeFilteredQuery($conn, $baseSqlOutOfStock, $user_role, $user_country, $user_state, "", $additionalJoinsOutOfStock, $additionalWhereOutOfStock, "", $orderByOutOfStock);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $out_of_stock[] = $row;
    }
} else {
    error_log("Failed to fetch Out of Stock products: " . $conn->error);
}


// ============= donought chart =================
// 1) Read the filter (default to “Today”)
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'Today';

// 2) Build the WHERE‐clause for sales only
$dateConditionDonut = "";
switch ($filter) {
    case 'Today':
        $dateConditionDonut = "AND DATE(td.transactiondate) = CURDATE()";
        break;
    case 'This Month':
        $dateConditionDonut = "AND MONTH(td.transactiondate) = MONTH(CURDATE())";
        break;
    case 'This Year':
        $dateConditionDonut = "AND YEAR(td.transactiondate) = YEAR(CURDATE())";
        break;
    default:
        $dateConditionDonut = "";  // no filter
}

// 3) Fetch filtered sales & revenue
$sales_revenue = [
    'total_sales_units' => 0,
    'total_revenue_amt' => 0
];
$baseSqlDonutSales = "
    SELECT 
      SUM(td.amount)  AS total_sales_units,
      SUM(td.profit) AS total_revenue_amt
    FROM transactiondetails td
    LEFT JOIN login l ON td.cashier = l.id
";
$additionalJoinsDonutSales = " LEFT JOIN branches b ON l.branch_id = b.branch_id";
$additionalWhereDonutSales = "(td.description = 'Sales' OR td.description = 'Refund')";

$result = executeFilteredQuery($conn, $baseSqlDonutSales, $user_role, $user_country, $user_state, $dateConditionDonut, $additionalJoinsDonutSales, $additionalWhereDonutSales);

if ($result) {
    $row = $result->fetch_assoc();
    $sales_revenue['total_sales_units'] = $row['total_sales_units'] ?? 0;
    $sales_revenue['total_revenue_amt'] = $row['total_revenue_amt'] ?? 0;
} else {
    error_log("Error fetching sales/revenue for donut chart: " . $conn->error);
}

// 4) Fetch inventory totals
$inventory = [
    'total_inventory_qty'   => 0,
    'total_inventory_value' => 0
];
$baseSqlDonutInventory = "
    SELECT
      COUNT(p.productid) AS total_inventory_qty,
      SUM(p.unitprice * bpi.quantity) AS total_inventory_value
    FROM product p
    JOIN branch_product_inventory bpi ON p.productid = bpi.productid
";
$additionalJoinsDonutInventory = " JOIN branches b ON bpi.branch_id = b.branch_id";

$result = executeFilteredQuery($conn, $baseSqlDonutInventory, $user_role, $user_country, $user_state, "", $additionalJoinsDonutInventory);

if ($result) {
    $row = $result->fetch_assoc();
    $inventory['total_inventory_qty'] = $row['total_inventory_qty'] ?? 0;
    $inventory['total_inventory_value'] = $row['total_inventory_value'] ?? 0;
} else {
    error_log("Error fetching inventory for donut chart: " . $conn->error);
}

// 5) Merge and hand off to the view
$dashboard_metrics = array_merge($sales_revenue, $inventory);

// Assign inventory_sum for the dashboard card
$inventory_sum = $inventory['total_inventory_value'];

// ===================== fetches active customers ==================
$active_customers = [];
$baseSqlActiveCustomers = "SELECT name, email, balance FROM customers c"; // Alias customers as 'c'
$additionalWhereActiveCustomers = "account_status='active'";
$orderByActiveCustomers = " ORDER BY name ASC";

$result = executeFilteredQuery($conn, $baseSqlActiveCustomers, $user_role, $user_country, $user_state, "", "", $additionalWhereActiveCustomers, "", $orderByActiveCustomers, "c");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $active_customers[] = $row;
    }
} else {
    error_log("Failed to fetch active customers: " . $conn->error);
}

// ===================== fetches inactive customers ==================
$inactive_customers = [];
$baseSqlInactiveCustomers = "SELECT name, email, balance FROM customers c"; // Alias customers as 'c'
$additionalWhereInactiveCustomers = "account_status='disabled'";
$orderByInactiveCustomers = " ORDER BY name ASC";

$result = executeFilteredQuery($conn, $baseSqlInactiveCustomers, $user_role, $user_country, $user_state, "", "", $additionalWhereInactiveCustomers, "", $orderByInactiveCustomers, "c");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $inactive_customers[] = $row;
    }
} else {
    error_log("Failed to fetch inactive customers: " . $conn->error);
}

// ===================== fetches low balance customers ==================
$low_balance_customers = [];
$baseSqlLowBalanceCustomers = "SELECT name, email, balance FROM customers c"; // Alias customers as 'c'
$additionalWhereLowBalanceCustomers = "balance <= 1000";
$orderByLowBalanceCustomers = " ORDER BY balance ASC";

$result = executeFilteredQuery($conn, $baseSqlLowBalanceCustomers, $user_role, $user_country, $user_state, "", "", $additionalWhereLowBalanceCustomers, "", $orderByLowBalanceCustomers, "c");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $low_balance_customers[] = $row;
    }
} else {
    error_log("Failed to fetch low balance customers: " . $conn->error);
}

// ===================== customer metrics ==================
$baseSqlTotalBalance = "SELECT SUM(balance) AS total FROM customers c"; // Alias customers as 'c'
$result = executeFilteredQuery($conn, $baseSqlTotalBalance, $user_role, $user_country, $user_state, "", "", "", "", "", "c");
$total_customer_balance = ($result && $result->num_rows > 0) ? $result->fetch_assoc()['total'] : 0;

$baseSqlActiveBalance = "SELECT SUM(balance) AS total FROM customers c"; // Alias customers as 'c'
$additionalWhereActiveBalance = "account_status='active'";
$result = executeFilteredQuery($conn, $baseSqlActiveBalance, $user_role, $user_country, $user_state, "", "", $additionalWhereActiveBalance, "", "", "c");
$active_customer_balance = ($result && $result->num_rows > 0) ? $result->fetch_assoc()['total'] : 0;

$baseSqlInactiveBalance = "SELECT SUM(balance) AS total FROM customers c"; // Alias customers as 'c'
$additionalWhereInactiveBalance = "account_status='disabled'";
$result = executeFilteredQuery($conn, $baseSqlInactiveBalance, $user_role, $user_country, $user_state, "", "", $additionalWhereInactiveBalance, "", "", "c");
$inactive_customer_balance = ($result && $result->num_rows > 0) ? $result->fetch_assoc()['total'] : 0;

$baseSqlActiveCount = "SELECT COUNT(*) AS total FROM customers c"; // Alias customers as 'c'
$additionalWhereActiveCount = "account_status='active'";
$result = executeFilteredQuery($conn, $baseSqlActiveCount, $user_role, $user_country, $user_state, "", "", $additionalWhereActiveCount, "", "", "c");
$active_customer_count = ($result && $result->num_rows > 0) ? $result->fetch_assoc()['total'] : 0;

$baseSqlInactiveCount = "SELECT COUNT(*) AS total FROM customers c"; // Alias customers as 'c'
$additionalWhereInactiveCount = "account_status='disabled'";
$result = executeFilteredQuery($conn, $baseSqlInactiveCount, $user_role, $user_country, $user_state, "", "", $additionalWhereInactiveCount, "", "", "c");
$inactive_customer_count = ($result && $result->num_rows > 0) ? $result->fetch_assoc()['total'] : 0;
?>
