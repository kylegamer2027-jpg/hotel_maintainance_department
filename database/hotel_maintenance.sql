CREATE DATABASE IF NOT EXISTS hotel_maintenance
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE hotel_maintenance;

CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(140) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin', 'staff') NOT NULL DEFAULT 'staff',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS staff (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(140) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  phone VARCHAR(40) NULL,
  department VARCHAR(100) NOT NULL,
  job_title VARCHAR(120) NOT NULL,
  status ENUM('Active', 'On leave', 'Inactive') NOT NULL DEFAULT 'Active',
  hire_date DATE NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_staff_department (department),
  INDEX idx_staff_status (status)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS rooms (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  room_number VARCHAR(20) NOT NULL UNIQUE,
  floor SMALLINT NOT NULL,
  room_type VARCHAR(80) NOT NULL,
  status ENUM('Available', 'Occupied', 'Needs cleaning', 'Out of service') NOT NULL DEFAULT 'Available',
  notes TEXT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_rooms_status (status)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS cleaning_tasks (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  room_id INT UNSIGNED NOT NULL,
  assigned_staff_id INT UNSIGNED NULL,
  scheduled_at DATETIME NOT NULL,
  status ENUM('Scheduled', 'In progress', 'Completed', 'Cancelled') NOT NULL DEFAULT 'Scheduled',
  notes TEXT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_cleaning_room FOREIGN KEY (room_id) REFERENCES rooms(id) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_cleaning_staff FOREIGN KEY (assigned_staff_id) REFERENCES staff(id) ON UPDATE CASCADE ON DELETE SET NULL,
  INDEX idx_cleaning_schedule (scheduled_at, status)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS inspections (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  room_id INT UNSIGNED NOT NULL,
  inspector_id INT UNSIGNED NULL,
  inspection_date DATE NOT NULL,
  cleanliness TINYINT UNSIGNED NOT NULL,
  safety TINYINT UNSIGNED NOT NULL,
  status ENUM('Passed', 'Needs follow-up', 'Failed') NOT NULL DEFAULT 'Passed',
  notes TEXT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_inspections_room FOREIGN KEY (room_id) REFERENCES rooms(id) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_inspections_staff FOREIGN KEY (inspector_id) REFERENCES staff(id) ON UPDATE CASCADE ON DELETE SET NULL,
  CHECK (cleanliness BETWEEN 1 AND 5),
  CHECK (safety BETWEEN 1 AND 5),
  INDEX idx_inspections_date (inspection_date)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS maintenance_requests (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  room_id INT UNSIGNED NOT NULL,
  requested_by VARCHAR(140) NOT NULL,
  assigned_staff_id INT UNSIGNED NULL,
  issue TEXT NOT NULL,
  priority ENUM('Low', 'Normal', 'High', 'Urgent') NOT NULL DEFAULT 'Normal',
  status ENUM('Open', 'In progress', 'On hold', 'Resolved', 'Closed') NOT NULL DEFAULT 'Open',
  request_date DATE NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_requests_room FOREIGN KEY (room_id) REFERENCES rooms(id) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_requests_staff FOREIGN KEY (assigned_staff_id) REFERENCES staff(id) ON UPDATE CASCADE ON DELETE SET NULL,
  INDEX idx_requests_status_priority (status, priority),
  INDEX idx_requests_date (request_date)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS work_orders (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  request_id INT UNSIGNED NULL,
  room_id INT UNSIGNED NOT NULL,
  assigned_staff_id INT UNSIGNED NULL,
  title VARCHAR(180) NOT NULL,
  description TEXT NULL,
  priority ENUM('Low', 'Normal', 'High', 'Urgent') NOT NULL DEFAULT 'Normal',
  status ENUM('Open', 'In progress', 'On hold', 'Completed', 'Cancelled') NOT NULL DEFAULT 'Open',
  due_date DATE NULL,
  completed_at DATETIME NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_work_request FOREIGN KEY (request_id) REFERENCES maintenance_requests(id) ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT fk_work_room FOREIGN KEY (room_id) REFERENCES rooms(id) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_work_staff FOREIGN KEY (assigned_staff_id) REFERENCES staff(id) ON UPDATE CASCADE ON DELETE SET NULL,
  INDEX idx_work_status_due (status, due_date)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS repairs (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  work_order_id INT UNSIGNED NULL,
  room_id INT UNSIGNED NOT NULL,
  technician_id INT UNSIGNED NULL,
  repair_date DATE NOT NULL,
  description TEXT NOT NULL,
  cost DECIMAL(10,2) UNSIGNED NOT NULL DEFAULT 0.00,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_repairs_work_order FOREIGN KEY (work_order_id) REFERENCES work_orders(id) ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT fk_repairs_room FOREIGN KEY (room_id) REFERENCES rooms(id) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_repairs_staff FOREIGN KEY (technician_id) REFERENCES staff(id) ON UPDATE CASCADE ON DELETE SET NULL,
  INDEX idx_repairs_date (repair_date)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS equipment (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(140) NOT NULL,
  category VARCHAR(100) NOT NULL,
  serial_number VARCHAR(120) NULL,
  location VARCHAR(140) NULL,
  `condition` ENUM('Good', 'Fair', 'Needs repair', 'Out of service') NOT NULL DEFAULT 'Good',
  purchase_date DATE NULL,
  next_service_date DATE NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_equipment_service (next_service_date)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS inventory (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(160) NOT NULL,
  sku VARCHAR(80) NULL UNIQUE,
  quantity INT UNSIGNED NOT NULL DEFAULT 0,
  unit VARCHAR(40) NOT NULL DEFAULT 'unit',
  reorder_level INT UNSIGNED NOT NULL DEFAULT 0,
  location VARCHAR(140) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_inventory_reorder (quantity, reorder_level)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS inventory_transactions (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  inventory_id INT UNSIGNED NOT NULL,
  type ENUM('Stock in', 'Stock out') NOT NULL,
  quantity INT UNSIGNED NOT NULL,
  transaction_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  notes VARCHAR(255) NULL,
  CONSTRAINT fk_inventory_transaction_item FOREIGN KEY (inventory_id) REFERENCES inventory(id) ON UPDATE CASCADE ON DELETE RESTRICT,
  INDEX idx_inventory_transactions_date (transaction_date)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS lost_found (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  item_name VARCHAR(160) NOT NULL,
  description TEXT NULL,
  location_found VARCHAR(140) NULL,
  found_date DATE NOT NULL,
  reported_by VARCHAR(140) NULL,
  status ENUM('In storage', 'Claimed', 'Returned', 'Donated') NOT NULL DEFAULT 'In storage',
  claimed_by VARCHAR(140) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_lost_found_status_date (status, found_date)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS schedules (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  staff_id INT UNSIGNED NOT NULL,
  shift_date DATE NOT NULL,
  start_time TIME NOT NULL,
  end_time TIME NOT NULL,
  department VARCHAR(100) NOT NULL,
  notes TEXT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_schedules_staff FOREIGN KEY (staff_id) REFERENCES staff(id) ON UPDATE CASCADE ON DELETE RESTRICT,
  INDEX idx_schedule_date_staff (shift_date, staff_id)
) ENGINE=InnoDB;
