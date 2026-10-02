<?php
/**
 * Sujan Top-Up - Canonical database connection.
 *
 * This is the single source of truth for the MySQL connection. All legacy
 * dbcon.php / db_connection.php files are thin shims that include this file.
 *
 * Note: mysqli reporting is intentionally set to OFF to preserve the legacy
 * code's expectations (prepare() returning false instead of throwing), which
 * keeps old error handling working under PHP 8.x.
 */

declare(strict_types=1);

require_once __DIR__ . '/app.php';

mysqli_report(MYSQLI_REPORT_OFF);

$DB_HOST = (string) env('DB_HOST', '127.0.0.1');
$DB_PORT = (int) env('DB_PORT', 3306);
$DB_NAME = (string) env('DB_NAME', 'sujan_topup_demo');
$DB_USER = (string) env('DB_USER', 'root');
$DB_PASS = (string) env('DB_PASSWORD', '');

$conn = @new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, $DB_PORT);

if ($conn->connect_errno) {
    error_log('DB connection failed: ' . $conn->connect_error);

    if (APP_DEBUG) {
        die('Database connection failed: ' . $conn->connect_error);
    }

    http_response_code(503);
    die('Database connection failed. Check your .env configuration and that the database is running.');
}

$conn->set_charset('utf8mb4');
