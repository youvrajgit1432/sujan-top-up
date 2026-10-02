<?php
/**
 * Compatibility shim.
 *
 * Legacy code includes "dbcon.php" (and admin/sign variants). This file now
 * delegates to the single canonical connection in config/database.php so
 * credentials live in exactly one place.
 */

require_once __DIR__ . '/config/database.php';
