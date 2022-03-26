<?php

// DATABASE INFORMATION

$server = 'localhost';
$username = 'fortress';
$password = 'A00285763a';
$database = 'inventory';

// new connection
$connection = new mysqli($server, $username, $password, $database);
// Check connection
if ($connection->connect_error) {
  die("Connection failed: " . $connection->connect_error);
}

// GET values in URL field
if (isset($_GET) && $_GET != 0) {
        foreach ($_GET as $param_name => $param_val) {
                $$param_name = $param_val;
        }
}

// POST values
if (isset($_POST) && $_POST != 0) {
        foreach ($_POST as $param_name => $param_val) {
                $$param_name = $param_val;
        }
}

?>
