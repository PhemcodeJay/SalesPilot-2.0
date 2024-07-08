-- Create Database if not exists
CREATE DATABASE IF NOT EXISTS salespilot;
USE salespilot;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT FALSE,
    role ENUM('admin', 'sales', 'inventory') DEFAULT 'sales',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Activation Codes Table
CREATE TABLE IF NOT EXISTS activation_codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    activation_code VARCHAR(100) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Password Resets Table
CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    reset_code VARCHAR(100) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Categories Table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Suppliers Table
CREATE TABLE IF NOT EXISTS suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    contact_name VARCHAR(100),
    contact_email VARCHAR(100),
    contact_phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Products Table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(255),
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    cost DECIMAL(10, 2) NOT NULL,
    stockqty INT NOT NULL,
    supplyqty INT NOT NULL,
    inventory_qty INT AS (stock_qty + supply_qty) STORED,
    name ENUM('goods', 'service', 'digital') NOT NULL DEFAULT 'goods',
    category_id INT,
    supplier_id INT,
    image_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id)
) ENGINE=InnoDB;

-- Customers Table
CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Staffs Table
CREATE TABLE IF NOT EXISTS staffs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20),
    position ENUM('manager', 'sales', 'inventory') DEFAULT 'sales',
    hire_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Sales Table
CREATE TABLE IF NOT EXISTS sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    customer_id INT NOT NULL,
    staff_id INT NOT NULL,
    quantity INT NOT NULL,
    name ENUM('goods', 'service', 'digital') NOT NULL DEFAULT 'goods',
    payment_status ENUM('full payment', 'pending', 'initial deposit') NOT NULL DEFAULT 'pending',
    total_price DECIMAL(10, 2) NOT NULL,
    sale_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (staff_id) REFERENCES staffs(id)
) ENGINE=InnoDB;

-- Inventory Table
CREATE TABLE IF NOT EXISTS inventory (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB;

-- Sales Analytics Table
CREATE TABLE IF NOT EXISTS sales_analytics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    total_sales DECIMAL(10, 2) NOT NULL,
    total_quantity INT NOT NULL,
    total_profit DECIMAL(10, 2) NOT NULL,
    total_expenses DECIMAL(10, 2) NOT NULL,
    net_profit DECIMAL(10, 2) NOT NULL,
    most_sold_product_id INT,
    available_stock INT NOT NULL,
    revenue DECIMAL(10, 2) NOT NULL,
    profit_margin DECIMAL(5, 2) NOT NULL,
    revenue_by_product DECIMAL(10, 2) NOT NULL,
    year_over_year_growth DECIMAL(5, 2) NOT NULL,
    cost_of_selling DECIMAL(10, 2) NOT NULL,
    report_date DATE NOT NULL,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (most_sold_product_id) REFERENCES products(id)
) ENGINE=InnoDB;

-- Inventory Metrics Table
CREATE TABLE IF NOT EXISTS inventory_metrics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    total_stocked INT NOT NULL,
    available_stock INT NOT NULL,
    inventory_turnover_rate DECIMAL(5, 2) NOT NULL,
    stock_to_sales_ratio DECIMAL(5, 2) NOT NULL,
    sell_through_rate DECIMAL(5, 2) NOT NULL,
    gross_margin_by_product DECIMAL(10, 2) NOT NULL,
    net_margin_by_product DECIMAL(10, 2) NOT NULL,
    gross_margin DECIMAL(10, 2) NOT NULL,
    net_margin DECIMAL(10, 2) NOT NULL,
    report_date DATE NOT NULL,
    FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB;

-- Expenses Table
CREATE TABLE IF NOT EXISTS expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    description TEXT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    expense_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Report Table for Consolidated Metrics
CREATE TABLE IF NOT EXISTS reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    report_date DATE NOT NULL,
    revenue DECIMAL(10, 2) NOT NULL,
    profit_margin DECIMAL(5, 2) NOT NULL,
    revenue_by_product DECIMAL(10, 2) NOT NULL,
    year_over_year_growth DECIMAL(5, 2) NOT NULL,
    cost_of_selling DECIMAL(10, 2) NOT NULL,
    inventory_turnover_rate DECIMAL(5, 2) NOT NULL,
    stock_to_sales_ratio DECIMAL(5, 2) NOT NULL,
    sell_through_rate DECIMAL(5, 2) NOT NULL,
    gross_margin_by_product DECIMAL(10, 2) NOT NULL,
    net_margin_by_product DECIMAL(10, 2) NOT NULL,
    gross_margin DECIMAL(10, 2) NOT NULL,
    net_margin DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Trigger for Updating Inventory Metrics
DELIMITER //
CREATE TRIGGER update_inventory_metrics AFTER INSERT ON sales
FOR EACH ROW
BEGIN
    -- Update available stock in inventory_metrics
    UPDATE inventory_metrics
    SET available_stock = available_stock - NEW.quantity
    WHERE product_id = NEW.product_id;
    
    -- Update total_sales and total_quantity in sales_analytics
    UPDATE sales_analytics
    SET total_sales = total_sales + NEW.total_price,
        total_quantity = total_quantity + NEW.quantity,
        total_profit = total_profit + (NEW.total_price - (NEW.quantity * p.cost)),
        net_profit = total_profit - total_expenses
    WHERE product_id = NEW.product_id;
    
    -- Update most_sold_product_id in sales_analytics if necessary
    UPDATE sales_analytics
    SET most_sold_product_id = NEW.product_id
    WHERE product_id = NEW.product_id
    AND NEW.quantity > (
        SELECT MAX(quantity)
        FROM sales
        WHERE DATE(sale_date) = NEW.sale_date
        GROUP BY product_id
    );
END;
//
DELIMITER ;
