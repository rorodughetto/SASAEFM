<?php
$host     = 'localhost';
$username = 'root';
$password = '1402romainE*';
$dbname   = 'dbs12409278';

$con = new mysqli($host, $username, $password, $dbname);

if ($con->connect_error) {
	die("Impossible de se connecter à la base de données : " . $con->connect_error);
}
