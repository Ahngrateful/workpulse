<?php
// Include Composer's autoloader
require '../vendor/autoload.php';

// Use the ZKTeco class
use Jmrashed\Zkteco\Lib\ZKTeco;

// Device configuration
define('DEVICE_IP', '192.168.1.11');
define('DEVICE_PORT', 4370); // Default ZKTeco UDP port

// Function to initialize and connect to the device
function connectToDevice() {
    $zk = new ZKTeco(DEVICE_IP, DEVICE_PORT);
    if (!$zk->connect()) {
        throw new Exception("Failed to connect to device at " . DEVICE_IP . ":" . DEVICE_PORT . ". Please check the IP, port, and network connectivity.");
    }
    return $zk;
}

// Function to disconnect from the device
function disconnectFromDevice($zk) {
    $zk->disconnect();
}

// Test connection
try {
    $zk = connectToDevice();
    echo "Successfully connected to ZKTeco device at " . DEVICE_IP;
    disconnectFromDevice($zk);
} catch (Exception $e) {
    echo "Connection Error: " . $e->getMessage();
}