<?php

// Requête SQL pour récupérer les salles
$query = "SELECT id, room_name FROM room";
$result = $con->query($query);

// Vérifier si la requête a réussi
if (!$result) {
	die("Erreur lors de l'exécution de la requête : " . $con->error);
}

// Récupérer les données des salles dans un tableau
$salles = array();

while ($row = $result->fetch_assoc()) {
	$salles[] = $row;
}
