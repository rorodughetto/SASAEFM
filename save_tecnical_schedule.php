<?php
session_start();
require_once('./bdd/bdd-connexion.php');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo "<script> alert('Erreur : Aucune donnée à enregistrer.'); location.replace('./reservation_salle_technique.php'); </script>";
    exit;
}

// Extraction des données POST
extract($_POST);
// Récupérer la valeur de start_time depuis le formulaire
$start_time = $_POST['start_datetime'];

// Récupérer la valeur de end_datetime depuis le formulaire
$end_time = $_POST['end_datetime'];
;
$allday = isset($allday);

// Vérification de l'existence de l'ID
if (empty($id)) {
    // Si l'ID est vide, il s'agit d'une insertion

    // Vérifier si la salle est déjà réservée pour la même période
    $sql_check = "SELECT COUNT(*) as count FROM `schedule_list_technical` where
                  ((`start_time` >= '$start_time' AND `start_time` < '$end_time') OR
                   (`end_time` > '$start_time' AND `end_time` <= '$end_time') OR
                   ('$start_time' >= `start_time` AND '$start_time' < `end_time`))";

    $result_check = $con->query($sql_check);

    if ($result_check) {
        $row = $result_check->fetch_assoc();
        if ($row['count'] > 0) {
            echo "<script> alert('La salle est déjà réservée pour cette période.'); location.replace('./reservation_salle_technique.php'); </script>";
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
    $sql = "INSERT INTO `schedule_list_technical` (`author`, `deceased_name`, `start_time`, `end_time`,`tecnical_option`) VALUES ('$author', '$deceased_name', '$start_time', '$end_time','$option')";
} else {
    // Sinon, il s'agit d'une mise à jour

    // Vérifier d'abord s'il y a un conflit avec une autre réservation
    $sql_check = "SELECT COUNT(*) as count FROM `schedule_list_technical` WHERE
                  ((`start_time` >= '$start_time' AND `start_time` < '$end_time') OR
                   (`end_time` > '$start_time' AND `end_time` <= '$end_time') OR
                   ('$start_time' >= `start_time` AND '$start_time' < `end_time`)) AND `id` != '$id'";

    $result_check = $con->query($sql_check);

    if ($result_check) {
        $row = $result_check->fetch_assoc();
        if ($row['count'] > 0) {
            echo "<script> alert('La salle est déjà réservée pour cette période.'); location.replace('./reservation_salle_technique.php'); </script>";
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

    $sql = "UPDATE `schedule_list_technical` SET `author` = '$author', `deceased_name` = '$deceased_name',`start_time` = '$start_time', `end_time` = '$end_time',`tecnical_option`='$option' WHERE `id` = '$id'";
}

// Exécution de la requête SQL
$save = $con->query($sql);
// ...

// Exécution de la requête SQL
$save = $con->query($sql);

if ($save) {
    // Envoyer un e-mail de confirmation ici

    // Récupérer l'e-mail de l'utilisateur depuis la table "users" (assumant que vous avez une colonne "email" dans cette table)
    $sql_user = "SELECT email FROM users WHERE user = '$author'";
    $result_user = $con->query($sql_user);

    if ($result_user) {
        $row_user = $result_user->fetch_assoc();
        $user_email = $row_user['email'];

        // Envoyer l'e-mail de confirmation
        $to = $user_email;
        $subject = 'Confirmation de réservation SASAEFM';
        $message = 'Votre réservation a été confirmée avec succès.';
        $headers = 'From: SASAEFM@email.com' . "\r\n" .
            'Reply-To: SASAEFM@email.com' . "\r\n" .
            'Content-Type: text/html; charset=UTF-8' . "\r\n";

        mail($to, $subject, $message, $headers);

        // Redirection en cas de succès
        echo "<script>alert('Événement enregistré avec succès et e-mail de confirmation envoyé.'); location.replace('./reservation_salle.php');</script>";
    } else {
        // En cas d'erreur lors de la récupération de l'e-mail de l'utilisateur
        echo "<pre>";
        echo "Une erreur s'est produite lors de la récupération de l'e-mail de l'utilisateur.<br>";
        echo "Erreur : " . $con->error . "<br>";
        echo "SQL : " . $sql_user . "<br>";
        echo "</pre>";
    }
} else {
    // En cas d'erreur lors de l'enregistrement de la réservation
    echo "<pre>";
    echo "Une erreur s'est produite lors de l'enregistrement de la réservation.<br>";
    echo "Erreur : " . $con->error . "<br>";
    echo "SQL : " . $sql . "<br>";
    echo "</pre>";
}

// Fermeture de la connexion à la base de données
$con->close();
