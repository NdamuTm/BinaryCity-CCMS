<?php

$host = "localhost";
$dbname = "client_contact_system";
$username = "ccms_user";
$password = "BCity@23";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}