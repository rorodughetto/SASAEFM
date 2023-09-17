<?php
$host     = 'localhost';
$username = 'EYQUEMRO';
$password = '1402romainE*';
$dbname   = 'SASAEFM';

$con = new mysqli($host, $username, $password, $dbname);

if ($con->connect_error) {
	die("Impossible de se connecter à la base de données : " . $con->connect_error);
}
