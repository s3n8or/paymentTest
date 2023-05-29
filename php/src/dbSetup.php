<?php

require_once('configs.php');
require_once('database.php');

$configs = getConfigs();
$mysqli;

try {
    $mysqli = getMysqli($configs);
} catch (Exception $e) {
    echo "server failured";
}

setupUsersTable($mysqli);
setupSessionsTable($mysqli);

/**
 * Helper function to separate out the user table setup
 * 
 * @param mysqli $mysqli A connection to a MySQL server
 */
function setupUsersTable(mysqli $mysqli) {
    // Check if users already exists, end if it does
    $sql = 'DESCRIBE users';

    if ($mysqli->query($sql)) {
        echo 'users already exists<br>';
        return;
    }

    // Create users table
    $sql = <<<SQL
CREATE TABLE users (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    username TEXT NOT NULL,
    email TEXT NOT NULL,
    password TEXT NOT NULL
);
SQL;

    if ($mysqli->query($sql) === true) {
        echo 'users successfully created<br>';
    } else {
        echo 'Error creating users: ' . $mysqli->error . '<br>';
    }
}

/**
 * Helper function to separate out the sessions table setup
 * 
 * @param mysqli $mysqli A connection to a MySQL server
 */
function setupSessionsTable(mysqli $mysqli) {
    // Check if sessions already exists, end if it does
    $sql = 'DESCRIBE sessions';

    if ($mysqli->query($sql)) {
        echo 'sessions already exists<br>';
        return;
    }

    // Create sessions table
    $sql = <<<SQL
CREATE TABLE sessions (
    sessionId TEXT NOT NULL,
    userId INT NOT NULL,
    createdAt DATETIME NOT NULL,
    UNIQUE(userId),
    FOREIGN KEY(userId) REFERENCES users(id)
);
SQL;

    if ($mysqli->query($sql) === true) {
        echo 'sessions successfully created<br>';
    } else {
        echo 'Error creating sessions: ' . $mysqli->error . '<br>';
    }
}

?>