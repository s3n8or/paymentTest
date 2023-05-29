<?php

/**
 * Convenience function for getting a connected mysqli object
 * 
 * @param array $configs Configs dictionary that has db configs
 * 
 * @return mysqli
 * 
 * @throws Exception If the connection fails an exception is thrown
 */
function getMysqli(array $configs): mysqli {
    $host = $configs['host'];
    $userName = $configs['userName'];
    $password = $configs['password'];
    $databaseName = $configs['databaseName'];
    $connection = new mysqli($host, $userName, $password, $databaseName);

    if ($connection->connect_error) {
        throw new Exception($connection->connect_error);
    } else if (is_null($connection)) {
        throw new Exception('mysqli connection is null on creation');
    }

    return $connection;
}
