# Maison Hotel Operations

A PHP and MySQL hotel maintenance and operations application for managing rooms, cleaning, inspections, maintenance requests, work orders, repairs, equipment, inventory, lost & found, staff schedules, staff records, and reports.

## Requirements

- PHP 8.1+ with PDO MySQL enabled
- MySQL 8+ or MariaDB with InnoDB support
- Apache (for example, XAMPP)

## Run locally with XAMPP

1. Copy the project folder into `C:\xampp\htdocs\hotel_maintainance_department`.
2. Start Apache and MySQL from the XAMPP Control Panel.
3. Open phpMyAdmin, select **Import**, and import `database/hotel_maintenance.sql`.
4. If your MySQL connection is not the default local XAMPP setup, configure `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, and `DB_PASSWORD` in the PHP environment.
5. Visit `http://localhost/hotel_maintainance_department/`.
6. Create an account. The first registered account becomes the administrator; later registrations receive the staff role.

The default database connection is `127.0.0.1:3306`, database `hotel_maintenance`, user `root`, and an empty password, matching a standard local XAMPP installation.

## Features

- Session-based sign-in and registration with password hashing
- Responsive dashboard and operational navigation
- Searchable create, read, update, and delete workflows for operational records
- Inventory stock-in/stock-out transactions update on-hand quantities atomically and reject stock-outs that exceed available quantity
- Summary reports, low-stock reporting, repair history, and print-friendly output
- CSRF-protected form submissions and prepared SQL statements

## Notes

This starter app is intended for a trusted, locally hosted hotel operations environment. Configure HTTPS, least-privilege database credentials, backups, and your organization's access policies before deploying it publicly.
