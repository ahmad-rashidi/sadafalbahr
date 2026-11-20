<?php
declare(strict_types=1);

/**
 * Copy this file to local.php and fill in Hostinger values.
 * Never upload local.example.php as local.php without changing the password.
 *
 * Hostinger hPanel → Databases → MySQL Databases
 */

$sab_db_host = 'localhost';
$sab_db_name = 'CHANGE_TO_YOUR_HOSTINGER_DB_NAME';
$sab_db_user = 'CHANGE_TO_YOUR_HOSTINGER_DB_USER';
$sab_db_pass = 'CHANGE_TO_YOUR_HOSTINGER_DB_PASSWORD';

// null = auto (empty at domain root, /sadafalbahr on local XAMPP)
// ''   = site is in public_html (normal Hostinger)
// '/sadafalbahr' = site is in a subfolder
$sab_base_path = null;
