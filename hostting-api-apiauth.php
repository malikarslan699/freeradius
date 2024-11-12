<?php
// Database configuration
$host = '193.203.184.67';
$db = 'u176398115_etelvpnlite';
$user = 'u176398115_etelvpnlite';
$pass = 'Etel@6699';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "1"; // 1 indicates failure to connect to the database
    exit;
}
// Get input data
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
// Validate input
if (empty($username) || empty($password)) {
    echo "1"; // 1 indicates missing username or password
    exit;
}
// Prepare the SQL query to find the user
$query = "SELECT * FROM account WHERE username = :username AND password = :password";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':username', $username);
$stmt->bindParam(':password', $password);
$stmt->execute();
// Check if user exists
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if ($user) {
    // Current date as a DateTime object in dd/mm/yyyy format
    $current_date = DateTime::createFromFormat('d/m/Y', date('d/m/Y'));
    
    // Initial Activation Check
    if ($user['isActive'] == 0 && $user['status'] == 'ON') {
        $days = (int)$user['days'];
        
        // Set start_date to today and calculate end_date by adding $days
        $start_date = $current_date->format('d/m/Y');
        $end_date = clone $current_date;
        $end_date->modify("+$days days");
        $formatted_end_date = $end_date->format('d/m/Y');
        // Update account with start_date, end_date, isActive = 1, and status remains 'ON'
        $update_query = "UPDATE account SET start_date = :start_date, end_date = :end_date, isActive = 1 WHERE id = :id";
        $update_stmt = $pdo->prepare($update_query);
        $update_stmt->bindParam(':start_date', $start_date);
        $update_stmt->bindParam(':end_date', $formatted_end_date);
        $update_stmt->bindParam(':id', $user['id']);
        $update_stmt->execute();
        echo "0"; // 0 indicates successful first-time authentication and activation
        exit;
    }
    
    // Convert end_date from the database to a DateTime object for comparison
    $end_date = DateTime::createFromFormat('d/m/Y', $user['end_date']);
    // Authentication Check
    if ($user['isActive'] == 1 && $user['status'] == 'ON' && ($end_date === false || $end_date >= $current_date)) {
        echo "0"; // 0 indicates successful authentication
    } else {
        // Conditions for failure: expired end_date or status OFF
        echo "1"; // 1 indicates rejection
    }
} else {
    echo "1"; // 1 indicates invalid username or password
}
