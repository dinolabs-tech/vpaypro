<?php

function generate_profit_and_loss($conn, $group_by = null) {
  $sql_revenue = "SELECT ";
  $sql_expenses = "SELECT ";

  switch ($group_by) {
    case 'day':
      $sql_revenue .= "DATE(transactiondate) AS date, SUM(amount) AS total_revenue 
                       FROM transactiondetails 
                       WHERE description = 'Sales' AND DATE(transactiondate) = CURDATE() 
                       GROUP BY DATE(transactiondate)";
      
      $sql_expenses .= "DATE(date) AS date, SUM(amount) AS total_expenses 
                        FROM expenses 
                        WHERE DATE(date) = CURDATE() 
                        GROUP BY DATE(date)";
      break;
  
    case 'month':
      $sql_revenue .= "MONTH(transactiondate) AS month, YEAR(transactiondate) AS year, SUM(amount) AS total_revenue 
                       FROM transactiondetails 
                       WHERE description = 'Sales' 
                       AND MONTH(transactiondate) = MONTH(CURDATE()) 
                       AND YEAR(transactiondate) = YEAR(CURDATE()) 
                       GROUP BY MONTH(transactiondate), YEAR(transactiondate)";
      
      $sql_expenses .= "MONTH(date) AS month, YEAR(date) AS year, SUM(amount) AS total_expenses 
                        FROM expenses 
                        WHERE MONTH(date) = MONTH(CURDATE()) 
                        AND YEAR(date) = YEAR(CURDATE()) 
                        GROUP BY MONTH(date), YEAR(date)";
      break;
  
    case 'year':
      $sql_revenue .= "YEAR(transactiondate) AS year, SUM(amount) AS total_revenue 
                       FROM transactiondetails 
                       WHERE description = 'Sales' 
                       AND YEAR(transactiondate) = YEAR(CURDATE()) 
                       GROUP BY YEAR(transactiondate)";
      
      $sql_expenses .= "YEAR(date) AS year, SUM(amount) AS total_expenses 
                        FROM expenses 
                        WHERE YEAR(date) = YEAR(CURDATE()) 
                        GROUP BY YEAR(date)";
      break;
  
    default:
      $sql_revenue .= "SUM(amount) AS total_revenue 
                       FROM transactiondetails 
                       WHERE description = 'Sales'";
      
      $sql_expenses .= "SUM(amount) AS total_expenses 
                        FROM expenses";
      break;
  }
  

  // Fetch revenue data
  $result_revenue = $conn->query($sql_revenue);
  $revenue_data = array();
  if ($result_revenue) {
    while ($row_revenue = $result_revenue->fetch_assoc()) {
      if ($group_by == 'day') {
        $revenue_data[$row_revenue['date']] = $row_revenue['total_revenue'];
      } elseif ($group_by == 'month') {
        $revenue_data[$row_revenue['year'] . '-' . $row_revenue['month']] = $row_revenue['total_revenue'];
      } elseif ($group_by == 'year') {
        $revenue_data[$row_revenue['year']] = $row_revenue['total_revenue'];
      } else {
        $revenue_data['total'] = $row_revenue['total_revenue'];
      }
    }
  }

  // Fetch expenses data
  $result_expenses = $conn->query($sql_expenses);
  $expenses_data = array();
  if ($result_expenses) {
    while ($row_expenses = $result_expenses->fetch_assoc()) {
      if ($group_by == 'day') {
        $expenses_data[$row_expenses['date']] = $row_expenses['total_expenses'];
      } elseif ($group_by == 'month') {
        $expenses_data[$row_expenses['year'] . '-' . $row_expenses['month']] = $row_expenses['total_expenses'];
      } elseif ($group_by == 'year') {
        $expenses_data[$row_expenses['year']] = $row_expenses['total_expenses'];
      } else {
        $expenses_data['total'] = $row_expenses['total_expenses'];
      }
    }
  }

  // Prepare profit and loss data
  $profit_and_loss_data = array();
  if ($group_by) {
    $dates = array_unique(array_merge(array_keys($revenue_data), array_keys($expenses_data)));
    foreach ($dates as $date) {
      $revenue = isset($revenue_data[$date]) ? $revenue_data[$date] : 0;
      $expenses = isset($expenses_data[$date]) ? $expenses_data[$date] : 0;
      $net_profit = $revenue - $expenses;
      $profit_and_loss_data[$date] = array(
        'revenue' => $revenue,
        'expenses' => $expenses,
        'net_profit' => $net_profit
      );
    }
  } else {
    $total_revenue = isset($revenue_data['total']) ? $revenue_data['total'] : 0;
    $total_expenses = isset($expenses_data['total']) ? $expenses_data['total'] : 0;
    $net_profit = $total_revenue - $total_expenses;
    $profit_and_loss_data = array(
      'revenue' => $total_revenue,
      'expenses' => $total_expenses,
      'net_profit' => $net_profit
    );
  }

  // Return the profit and loss data
  return $profit_and_loss_data;
}

?>
