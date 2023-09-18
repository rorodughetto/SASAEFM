<?php
session_start();
require_once('./bdd/bdd-connexion.php');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo "<script> alert('Erreur : Aucune donnée à enregistrer.'); location.replace('./calendar.php'); </script>";
    exit;
}

// Extraction des données POST
extract($_POST);
$allday = isset($allday);

// Vérification de l'existence de l'ID
if (empty($id)) {
    // Si l'ID est vide, il s'agit d'une insertion

    // Vérifier si la salle est déjà réservée pour la même période
    $sql_check = "SELECT COUNT(*) as count FROM `schedule_list` WHERE `room_name` = '$room_name' AND
                  ((`start_datetime` >= '$start_datetime' AND `start_datetime` < '$end_datetime') OR
                   (`end_datetime` > '$start_datetime' AND `end_datetime` <= '$end_datetime') OR
                   ('$start_datetime' >= `start_datetime` AND '$start_datetime' < `end_datetime`))";

    $result_check = $con->query($sql_check);

    if ($result_check) {
        $row = $result_check->fetch_assoc();
        if ($row['count'] > 0) {
            echo "<script> alert('La salle est déjà réservée pour cette période.'); location.replace('./calendar.php'); </script>";
            exit;
        }
    } else {
        echo "<pre>";
        echo "Une erreur s'est produite.<br>";
        echo "Erreur : " . $con->error . "<br>";
        echo "SQL : " . $sql_check . "<br>";
        echo "</pre>";
        exit;
    }

    $author = $_SESSION['utilisateur'];
    $sql = "INSERT INTO `schedule_list` (`author`, `deceased_name`, `room_name`, `start_datetime`, `end_datetime`, `toilet_and_dressing`, `care`, `ritual_toilet`, `technical_room_reservation`, `technical_room_reservation_time`) VALUES ('$author', '$deceased_name', '$room_name', '$start_datetime', '$end_datetime', '$toilet_and_dressing', '$care', '$ritual_toilet', '$technical_room_reservation_choice', '$technical_room_reservation_time')";
} else {
    // Sinon, il s'agit d'une mise à jour

    // Vérifier d'abord s'il y a un conflit avec une autre réservation
    $sql_check = "SELECT COUNT(*) as count FROM `schedule_list` WHERE `room_name` = '$room_name' AND
                  ((`start_datetime` >= '$start_datetime' AND `start_datetime` < '$end_datetime') OR
                   (`end_datetime` > '$start_datetime' AND `end_datetime` <= '$end_datetime') OR
                   ('$start_datetime' >= `start_datetime` AND '$start_datetime' < `end_datetime`)) AND `id` != '$id'";

    $result_check = $con->query($sql_check);

    if ($result_check) {
        $row = $result_check->fetch_assoc();
        if ($row['count'] > 0) {
            echo "<script> alert('La salle est déjà réservée pour cette période.'); location.replace('./calendar.php'); </script>";
            exit;
        }
    } else {
        echo "<pre>";
        echo "Une erreur s'est produite.<br>";
        echo "Erreur : " . $con->error . "<br>";
        echo "SQL : " . $sql_check . "<br>";
        echo "</pre>";
        exit;
    }

    $sql = "UPDATE `schedule_list` SET `author` = '$author', `deceased_name` = '$deceased_name', `room_name` = '$room_name', `start_datetime` = '$start_datetime', `end_datetime` = '$end_datetime' WHERE `id` = '$id'";
}

// Exécution de la requête SQL
$save = $con->query($sql);

if ($save) {
    // Redirection en cas de succès
    echo "<script>alert('Événement enregistré avec succès.'); location.replace('./calendar.php');</script>";
} else {
    // En cas d'erreur
    echo "<pre>";
    echo "Une erreur s'est produite.<br>";
    echo "Erreur : " . $con->error . "<br>";
    echo "SQL : " . $sql . "<br>";
    echo "</pre>";
}

// Fermeture de la connexion à la base de données
$con->close();
