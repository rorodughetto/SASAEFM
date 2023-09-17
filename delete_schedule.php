<?php
session_start();
require_once('./bdd/bdd-connexion.php');

// Récupérez l'ID de la réservation à supprimer depuis la requête GET (ou POST, selon la méthode de votre formulaire)
$id = $_GET['id'];

// Récupérez l'auteur de la réservation associée à l'ID
$sql = "SELECT `author` FROM `schedule_list` WHERE `id` = '$id'";
$result = $con->query($sql);

if ($result) {
    $row = $result->fetch_assoc();
    $author = $row['author'];

    // Vérifiez si l'utilisateur en cours correspond à l'auteur de la réservation
    if ($_SESSION['utilisateur'] === $author) {
        // L'utilisateur en cours est autorisé à supprimer la réservation
        // Exécutez la requête de suppression ici
        $deleteSql = "DELETE FROM `schedule_list` WHERE `id` = '$id'";
        $deleteResult = $con->query($deleteSql);

        if ($deleteResult) {
            // La suppression a réussi
            echo "<script> alert('Réservation supprimée avec succès.'); location.replace('./calendar.php'); </script>";
        } else {
            // En cas d'erreur lors de la suppression
            echo "<script> alert('Une erreur s\'est produite lors de la suppression de la réservation.'); location.replace('./calendar.php'); </script>";
        }
    } else {
        // L'utilisateur en cours n'est pas autorisé à supprimer cette réservation
        echo "<script> alert('Vous n\'êtes pas autorisé à supprimer cette réservation.'); location.replace('./calendar.php'); </script>";
    }
} else {
    // En cas d'erreur lors de la récupération de l'auteur de la réservation
    echo "<script> alert('Une erreur s\'est produite lors de la récupération des données de réservation.'); location.replace('./calendar.php'); </script>";
}

// Fermeture de la connexion à la base de données
$con->close();
