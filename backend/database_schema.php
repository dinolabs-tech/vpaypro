<?php
// Include the database connection file
require_once 'database/db_connection.php';

function createTable($conn, $sql, $tableName)
{
    if ($conn->query($sql) === TRUE) {
        // echo "Table '$tableName' created successfully or already exists.<br>";
        // return true;
    } else {
        echo "Error creating table '$tableName': " . $conn->error . "<br>";
        error_log("Error creating table '$tableName': " . $conn->error);
        return false;
    }
}

// Array of table schemas
$tables = [
    [
        'name' => 'login',
        'sql' => "
            CREATE TABLE IF NOT EXISTS `login` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `staffname` VARCHAR(100) NOT NULL,
                `username` VARCHAR(100) NOT NULL,
                `password` VARCHAR(100) NOT NULL,
                `reset_token` varchar(255) DEFAULT NULL,
                `reset_token_expiry` datetime DEFAULT NULL,
                `email` VARCHAR(100) NOT NULL,
                `address` VARCHAR(225) NOT NULL,
                `mobile` VARCHAR(100) NOT NULL,
                `country` VARCHAR(100) NOT NULL,
                `state` VARCHAR(100) NOT NULL,
                `profile_picture` VARCHAR(255) DEFAULT 'default.jpg',
                `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
                `role` VARCHAR(100) NOT NULL,
                `branch_id` INT DEFAULT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;"
    ],
    [
        'name' => 'product',
        'sql' => "
            CREATE TABLE IF NOT EXISTS `product` (
                `productid` INT(222) NOT NULL AUTO_INCREMENT,
                `productname` VARCHAR(222) NOT NULL,
                `sku` VARCHAR(222) NOT NULL,
                `location` VARCHAR(222) NOT NULL,
                `unitprice` VARCHAR(222) NOT NULL,
                `sellprice` VARCHAR(222) NOT NULL,
                `total` VARCHAR(222) NOT NULL,
                `description` VARCHAR(222) NOT NULL,
                `reorder_level` VARCHAR(222) NOT NULL,
                `reorder_qty` VARCHAR(222) NOT NULL,
                `profit` VARCHAR(1000) NOT NULL,
                `discount` INT(11) NOT NULL DEFAULT 0,
                `image_url` VARCHAR(255) DEFAULT NULL,
                `country` VARCHAR(255) NOT NULL,
                `state` VARCHAR(255) NOT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`productid`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;"
    ],
    [
        'name' => 'reviews',
        'sql' => "
        CREATE TABLE IF NOT EXISTS `reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `review_text` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
    ],
    [
        'name' => 'posts',
        'sql' => "
             CREATE TABLE IF NOT EXISTS `posts` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL,
    `content` text NOT NULL,
    `author_id` int(11) NOT NULL,
    `category_id` int(11) NOT NULL,
    `image_path` varchar(255) DEFAULT NULL,
    `views` INT DEFAULT 0,
    `likes` INT DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `author_id` (`author_id`),
    KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
    ],
    [
        'name' => 'comments',
        'sql' => "
             CREATE TABLE IF NOT EXISTS `comments` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `post_id` int(11) NOT NULL,
            `name` varchar(255) NOT NULL,
            `email` varchar(255) NOT NULL,
            `content` text NOT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY (`id`),
            KEY `post_id` (`post_id`),
            CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;"
    ],
    [
        'name' => 'sub',
        'sql' => "
            CREATE TABLE IF NOT EXISTS `sub` (
                `id` VARCHAR(111) NOT NULL,
                `expdate` VARCHAR(111) NOT NULL,
                `package` VARCHAR(111) NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;"
    ],
    [
        'name' => 'wishlist',
        'sql' => "
            CREATE TABLE IF NOT EXISTS `wishlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
    ],
    [
        'name' => 'suppliers',
        'sql' => "
            CREATE TABLE IF NOT EXISTS `suppliers` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(222) NOT NULL,
                `product` VARCHAR(222) NOT NULL,
                `companyname` VARCHAR(222) NOT NULL,
                `phone` VARCHAR(222) NOT NULL,
                `email` VARCHAR(222) NOT NULL,
                `address` VARCHAR(222) NOT NULL,
                `country` VARCHAR(222) NOT NULL,
                `state` VARCHAR(222) NOT NULL,
                `profile_picture` VARCHAR(222) NOT NULL,
                `password` VARCHAR(222) NOT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;"
    ],
    [
        'name' => 'transactiondetails',
        'sql' => "
            CREATE TABLE IF NOT EXISTS `transactiondetails` (
                `transactionID` VARCHAR(222) NOT NULL,
                `productid` VARCHAR(200) NOT NULL,
                `productname` VARCHAR(222) NOT NULL,
                `description` VARCHAR(222) NOT NULL,
                `units` INT(11) NOT NULL,
                `amount` INT(11) NOT NULL,
                `transactiondate` DATETIME NOT NULL,
                `profit` INT(11) NOT NULL,
                `cashier` VARCHAR(100) NOT NULL,
                `status` VARCHAR(11) NOT NULL,
                `discount` INT(11) NOT NULL,
                `refund` INT(11) DEFAULT NULL,
                `credit` INT(11) DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;"
    ],
    [
        'name' => 'branches',
        'sql' => "
            CREATE TABLE IF NOT EXISTS `branches` (
                `branch_id` INT AUTO_INCREMENT PRIMARY KEY,
                `branch_name` VARCHAR(255) NOT NULL,
                `country` VARCHAR(255) NOT NULL,
                `state` VARCHAR(255) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;"
    ],
    [
        'name' => 'expenses',
        'sql' => "
            CREATE TABLE IF NOT EXISTS `expenses` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `description` VARCHAR(255) NOT NULL,
                `amount` DECIMAL(10, 2) NOT NULL,
                `date` DATE NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;"
    ],
    [
        'name' => 'orders',
        'sql' => "
            CREATE TABLE IF NOT EXISTS `orders` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `customer_id` VARCHAR(255) NOT NULL,
                `order_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `status` ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') NOT NULL DEFAULT 'pending',
                `total_amount` DECIMAL(10, 2) NOT NULL,
                `delivery_address` TEXT NOT NULL,
                `delivery_fee` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
                `country` VARCHAR(100),
                `state` VARCHAR(100)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
    ],
    [
        'name' => 'order_items',
        'sql' => "
            CREATE TABLE IF NOT EXISTS `order_items` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `order_id` INT NOT NULL,
                `product_id` INT NOT NULL,
                `quantity` INT NOT NULL,
                `price` DECIMAL(10, 2) NOT NULL,
                FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`),
                FOREIGN KEY (`product_id`) REFERENCES `product`(`productid`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
    ],
    [
        'name' => 'deliveries',
        'sql' => "
            CREATE TABLE IF NOT EXISTS `deliveries` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `order_id` INT NOT NULL,
                `status` ENUM('pending', 'shipped', 'delivered', 'failed') NOT NULL DEFAULT 'pending',
                `delivery_personnel` VARCHAR(255),
                `estimated_delivery_date` DATE,
                `actual_delivery_date` DATETIME,
                `delivery_code` VARCHAR(255) NULL,
                `country` VARCHAR(100),
                `state` VARCHAR(100),
                FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
    ],
    [
        'name' => 'settings',
        'sql' => "
            CREATE TABLE IF NOT EXISTS `settings` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `setting_key` VARCHAR(255) NOT NULL UNIQUE,
                `setting_value` VARCHAR(255) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
    ],
    [
        'name' => 'audit_logs',
        'sql' => "
            CREATE TABLE IF NOT EXISTS `audit_logs` (
                `log_id` INT AUTO_INCREMENT PRIMARY KEY,
                `user_id` INT NOT NULL,
                `action` VARCHAR(255) NOT NULL,
                `details` TEXT,
                `timestamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
    ],
    [
        'name' => 'customer_groups',
        'sql' => "
            CREATE TABLE IF NOT EXISTS `customer_groups` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(255) NOT NULL UNIQUE,
                `description` TEXT,
                `discount_percentage` DECIMAL(5, 2) DEFAULT 0.00,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
    ],
    [
        'name' => 'customer_group_members',
        'sql' => "
            CREATE TABLE IF NOT EXISTS `customer_group_members` (
                `group_id` INT NOT NULL,
                `customer_id` INT NOT NULL,
                PRIMARY KEY (`group_id`, `customer_id`),
                FOREIGN KEY (`group_id`) REFERENCES `customer_groups`(`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
    ],
    [
        'name' => 'customers',
        'sql' => "
            CREATE TABLE IF NOT EXISTS `customers` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `customer_id` VARCHAR(255) NOT NULL UNIQUE,
                `name` VARCHAR(255) NOT NULL,
                `email` VARCHAR(255) NOT NULL UNIQUE,
                `phone` VARCHAR(100) NOT NULL,
                `password` VARCHAR(255) NOT NULL,
                `address` TEXT NOT NULL,
                `country` VARCHAR(100) NOT NULL,
                `state` VARCHAR(100),
                `balance` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
                `profile_picture` VARCHAR(255) DEFAULT 'default.jpg',
                `account_status` ENUM('active', 'disabled') NOT NULL DEFAULT 'active',
                `reset_token` VARCHAR(255) DEFAULT NULL,
                `reset_token_expiry` DATETIME DEFAULT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `two_factor_secret` VARCHAR(255) DEFAULT NULL,
                `two_factor_code` VARCHAR(6) DEFAULT NULL,
                `two_factor_expires_at` DATETIME DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
    ],
    [
        'name' => 'customer_transactions',
        'sql' => "
            CREATE TABLE IF NOT EXISTS `customer_transactions` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `customer_id` VARCHAR(255) NOT NULL,
                `transaction_type` ENUM('funding', 'purchase') NOT NULL,
                `amount` DECIMAL(10, 2) NOT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `processed_by_user_id` INT NULL,                
                FOREIGN KEY (`customer_id`) REFERENCES `customers`(`customer_id`),
                FOREIGN KEY (`processed_by_user_id`) REFERENCES `login`(`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
    ],
    [
        'name' => 'email_templates',
        'sql' => "
            CREATE TABLE IF NOT EXISTS `email_templates` (
                `template_id` INT AUTO_INCREMENT PRIMARY KEY,
                `template_name` VARCHAR(255) NOT NULL,
                `subject` VARCHAR(255) NOT NULL,
                `body` TEXT NOT NULL,
                `event_trigger` VARCHAR(255) NOT NULL UNIQUE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
    ],
    [
        'name' => 'payment_gateways',
        'sql' => "
            CREATE TABLE IF NOT EXISTS `payment_gateways` (
                `gateway_id` INT AUTO_INCREMENT PRIMARY KEY,
                `gateway_name` VARCHAR(255) NOT NULL,
                `api_key` VARCHAR(255) NULL,
                `api_secret` VARCHAR(255) NULL,
                `flutterwave_public_key` VARCHAR(255) NULL,
                `flutterwave_secret_key` VARCHAR(255) NULL,
                `is_active` BOOLEAN DEFAULT FALSE,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
    ],
    [
        'name' => 'tax_rates',
        'sql' => "
            CREATE TABLE IF NOT EXISTS `tax_rates` (
                `tax_rate_id` INT AUTO_INCREMENT PRIMARY KEY,
                `country` VARCHAR(100) NOT NULL,
                `state` VARCHAR(100),
                `tax_rate` DECIMAL(5, 2) NOT NULL,
                `is_active` BOOLEAN DEFAULT TRUE,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
    ],
    [
        'name' => 'tax_rules',
        'sql' => "
            CREATE TABLE IF NOT EXISTS `tax_rules` (
                `tax_rule_id` INT AUTO_INCREMENT PRIMARY KEY,
                `rule_name` VARCHAR(255) NOT NULL,
                `tax_rate_id` INT NOT NULL,
                `applies_to_product_type` VARCHAR(100),
                `applies_to_order_total` DECIMAL(10, 2),
                `is_active` BOOLEAN DEFAULT TRUE,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (`tax_rate_id`) REFERENCES `tax_rates`(`tax_rate_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
    ],
    [
        'name' => 'categories',
        'sql' => "
            CREATE TABLE IF NOT EXISTS `categories` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(255) NOT NULL UNIQUE,
                `description` TEXT DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
    ],
    [
        'name' => 'product_categories',
        'sql' => "
            CREATE TABLE IF NOT EXISTS `product_categories` (
                `product_id` INT NOT NULL,
                `category_id` INT NOT NULL,
                PRIMARY KEY (`product_id`, `category_id`),
                FOREIGN KEY (`product_id`) REFERENCES `product`(`productid`),
                FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
    ],
    [
        'name' => 'blog_categories',
        'sql' => "
            CREATE TABLE IF NOT EXISTS `blog_categories` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(50) NOT NULL,
            `description` text DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `name` (`name`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
    ],
    [
        'name' => 'users',
        'sql' => "
            CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL,
  `google_id` varchar(255) NOT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `contact` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
    ],
    [
        'name' => 'product_variations',
        'sql' => "
            CREATE TABLE IF NOT EXISTS `product_variations` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `product_id` INT NOT NULL,
                `variation_name` VARCHAR(255) NOT NULL,
                `variation_value` VARCHAR(255) NOT NULL,
                `price_modifier` DECIMAL(10, 2) DEFAULT 0.00,
                `sku` VARCHAR(255) UNIQUE,
                `stock_level` INT DEFAULT 0,
                FOREIGN KEY (`product_id`) REFERENCES `product`(`productid`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
    ],
    [
        'name' => 'purchase_orders',
        'sql' => "
            CREATE TABLE IF NOT EXISTS `purchase_orders` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `supplier_id` INT NOT NULL,
                `order_date` DATE NOT NULL,
                `expected_delivery_date` DATE,
                `status` VARCHAR(50) DEFAULT 'Pending',
                `total_amount` DECIMAL(10, 2) DEFAULT 0.00,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (`supplier_id`) REFERENCES `suppliers`(`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
    ],
    [
        'name' => 'purchase_order_items',
        'sql' => "
            CREATE TABLE IF NOT EXISTS `purchase_order_items` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `purchase_order_id` INT NOT NULL,
                `product_id` INT NOT NULL,
                `quantity` INT NOT NULL,
                `unit_price` DECIMAL(10, 2) NOT NULL,
                `subtotal` DECIMAL(10, 2) NOT NULL,
                FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders`(`id`) ON DELETE CASCADE,
                FOREIGN KEY (`product_id`) REFERENCES `product`(`productid`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
    ],
    [
        'name' => 'stock_transfers',
        'sql' => "
            CREATE TABLE IF NOT EXISTS `stock_transfers` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `from_branch_id` INT NOT NULL,
                `to_branch_id` INT NOT NULL,
                `product_id` INT NOT NULL,
                `quantity` INT NOT NULL,
                `transfer_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `notes` TEXT,
                FOREIGN KEY (`from_branch_id`) REFERENCES `branches`(`branch_id`) ON DELETE CASCADE,
                FOREIGN KEY (`to_branch_id`) REFERENCES `branches`(`branch_id`) ON DELETE CASCADE,
                FOREIGN KEY (`product_id`) REFERENCES `product`(`productid`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
    ],
    [
        'name' => 'session_logs',
        'sql' => "
            CREATE TABLE IF NOT EXISTS session_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NULL,
            event_type VARCHAR(50) NOT NULL,  -- 'login', 'logout', 'timeout', 'hijack'
            ip_address VARCHAR(45) NOT NULL,
            user_agent TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
    ],
    [
        'name' => 'branch_product_inventory',
        'sql' => "
            CREATE TABLE IF NOT EXISTS branch_product_inventory (
                inventory_id INT AUTO_INCREMENT PRIMARY KEY,
                branch_id INT NOT NULL,
                productid INT NOT NULL,
                quantity INT NOT NULL DEFAULT 0,
                last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (branch_id) REFERENCES branches(branch_id) ON DELETE CASCADE,
                FOREIGN KEY (productid) REFERENCES product(productid) ON DELETE CASCADE,
                UNIQUE (branch_id, productid)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;"
    ]
];

// Execute table creation
foreach ($tables as $table) {
    createTable($conn, $table['sql'], $table['name']);

    // Special handling for settings table to insert default delivery fee
    if ($table['name'] === 'settings') {
        $conn->query("INSERT INTO settings (setting_key, setting_value) VALUES ('delivery_fee', '1000') ON DUPLICATE KEY UPDATE setting_key=setting_key");
    }
}

// Close the connection
// $conn->close();
