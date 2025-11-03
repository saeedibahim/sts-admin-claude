<?php
/**
 * Generate Password Hashes
 * Run this script once to generate password hashes for the sample users
 *
 * Usage: php generate_password_hashes.php
 */

echo "===========================================\n";
echo "STS Dashboard - Password Hash Generator\n";
echo "===========================================\n\n";

// Generate password hashes
$adminPassword = 'Admin@123';
$partnerPassword = 'Partner@123';

$adminHash = password_hash($adminPassword, PASSWORD_DEFAULT);
$partnerHash = password_hash($partnerPassword, PASSWORD_DEFAULT);

echo "Admin User:\n";
echo "  Username: admin\n";
echo "  Password: $adminPassword\n";
echo "  Hash: $adminHash\n\n";

echo "Partner User:\n";
echo "  Username: partner\n";
echo "  Password: $partnerPassword\n";
echo "  Hash: $partnerHash\n\n";

echo "===========================================\n";
echo "Copy these hashes to sample-data.sql\n";
echo "===========================================\n\n";

// Optionally, create SQL directly
echo "SQL INSERT statement:\n\n";
echo "INSERT INTO users (username, email, password_hash, full_name, role, is_active) VALUES\n";
echo "('admin', 'admin@sts-agency.com', '$adminHash', 'Admin User', 'Admin', TRUE),\n";
echo "('partner', 'partner@sts-agency.com', '$partnerHash', 'Partner User', 'Admin', TRUE);\n\n";

echo "✓ Password hashes generated successfully!\n";
?>
