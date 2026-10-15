CREATE DATABASE IF NOT EXISTS marketplace_katering CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE marketplace_katering;

-- Import this schema only if you are NOT running php artisan migrate.
-- Laravel's default users table is assumed to exist before the role column is added.

ALTER TABLE users ADD COLUMN role VARCHAR(255) NOT NULL DEFAULT 'customer';

CREATE TABLE merchants (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL UNIQUE,
  company_name VARCHAR(255) NOT NULL,
  address VARCHAR(255) NULL,
  phone VARCHAR(255) NULL,
  description TEXT NULL,
  location VARCHAR(255) NULL,
  food_type VARCHAR(255) NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  CONSTRAINT merchants_user_fk FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE menus (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  merchant_id BIGINT UNSIGNED NOT NULL,
  name VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  photo VARCHAR(255) NULL,
  price DECIMAL(12,2) NOT NULL,
  is_available TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  CONSTRAINT menus_merchant_fk FOREIGN KEY (merchant_id) REFERENCES merchants(id) ON DELETE CASCADE
);

CREATE TABLE orders (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  customer_id BIGINT UNSIGNED NOT NULL,
  merchant_id BIGINT UNSIGNED NOT NULL,
  delivery_date DATE NOT NULL,
  delivery_address VARCHAR(255) NOT NULL,
  notes TEXT NULL,
  status VARCHAR(255) NOT NULL DEFAULT 'pending',
  total DECIMAL(12,2) NOT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  CONSTRAINT orders_customer_fk FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT orders_merchant_fk FOREIGN KEY (merchant_id) REFERENCES merchants(id) ON DELETE CASCADE
);

CREATE TABLE order_items (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id BIGINT UNSIGNED NOT NULL,
  menu_id BIGINT UNSIGNED NULL,
  menu_name VARCHAR(255) NOT NULL,
  quantity INT UNSIGNED NOT NULL,
  unit_price DECIMAL(12,2) NOT NULL,
  subtotal DECIMAL(12,2) NOT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  CONSTRAINT order_items_order_fk FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  CONSTRAINT order_items_menu_fk FOREIGN KEY (menu_id) REFERENCES menus(id) ON DELETE SET NULL
);

CREATE TABLE invoices (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id BIGINT UNSIGNED NOT NULL UNIQUE,
  invoice_number VARCHAR(255) NOT NULL UNIQUE,
  issued_at TIMESTAMP NOT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  CONSTRAINT invoices_order_fk FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);
