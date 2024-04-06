<?php

// Requête SQL pour récupérer les salles
$query_salon = "SELECT id, salon_name FROM salon";
$result_salon = $con->query($query_salon);

$query_cellule = "SELECT id, cellule_name FROM cellule";
$result_cellule = $con->query($query_cellule);

// Vérifier si la requête a réussi
if (!$result_salon) {
	die("Erreur lors de l'exécution de la requête : " . $con->error);
}
if (!$result_cellule) {
	die("Erreur lors de l'exécution de la requête : " . $con->error);
}
// Récupérer les données des salon dans un tableau
$salon = array();

while ($row = $result_salon->fetch_assoc()) {
	$salon[] = $row;
}

$cellule = array();

while ($row = $result_cellule->fetch_assoc()) {
	$cellule[] = $row;
}
