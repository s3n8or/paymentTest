<?php

/**
 * Helper method that fetches a .env file that contains sensitive information and returns an array of the config values
 * 
 * @return array
 */
function getConfigs(): array {
    $fileName = '.env';

    $env = fopen($fileName, 'r');
    $contents = fread($env, filesize($fileName));
    fclose($env);

    return json_decode($contents, true);
}
