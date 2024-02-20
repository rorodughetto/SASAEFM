<?php
session_start();
require_once('./bdd/bdd-connexion.php');
require './PHPMailer/Exception.php';
require './PHPMailer/PHPMailer.php';
require './PHPMailer/SMTP.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;




if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo "<script> alert('Erreur : Aucune donnée à enregistrer.'); location.replace('./reservation_salle.php'); </script>";
    exit;
}

// Extraction des données POST
extract($_POST);
// Récupérer la valeur de start_datetime depuis le formulaire
$startDatetime = $_POST['start_datetime'];

// Convertir la valeur en un objet DateTime
$startDatetimeObj = new DateTime($startDatetime);

// Ajouter 5 jours à start_datetime
$startDatetimeObj->add(new DateInterval('P5D')); // 'P5D' représente 5 jours


// Réinitialiser l'heure à minuit
$startDatetimeObj->setTime(0, 0, 0);

// Récupérer la valeur calculée de end_datetime
$end_datetime = $startDatetimeObj->format('Y-m-d H:i:s');
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
            echo "<script> alert('La salle est déjà réservée pour cette période.'); location.replace('./reservation_salle.php'); </script>";
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
    $sql = "INSERT INTO `schedule_list` (`author`, `deceased_name`, `room_name`, `start_datetime`, `end_datetime`) VALUES ('$author', '$deceased_name', '$room_name', '$start_datetime', '$end_datetime')";
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
            echo "<script> alert('La salle est déjà réservée pour cette période.'); location.replace('./reservation_salle.php'); </script>";
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
    // Récupérer l'e-mail de l'utilisateur depuis la table "users" (assumant que vous avez une colonne "email" dans cette table)
    $sql_user = "SELECT email FROM users WHERE user = '$author'";
    $result_user = $con->query($sql_user);

    if ($result_user) {
        $row_user = $result_user->fetch_assoc();
        $user_email = $row_user['email'];

        // Sujet et corps de l'e-mail de confirmation
        $sujet = 'Confirmation de réservation SASAEFM';
        $message = 'Votre réservation a été confirmée avec succès.';

        // Adresse e-mail de l'expéditeur
        $expediteur = 'eyqurmr@gmail.fr'; // Remplacez par votre adresse e-mail

        // Créer une instance de PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Configuration du serveur SMTP
            $mail->isSMTP();
              $mail->Host = 'smtp.gmail.com';// Remplacez par le serveur SMTP approprié
            $mail->Port = 587; // Port SMTP
            $mail->SMTPAuth = true;
            $mail->Username = 'eyqurmr@gmail.fr'; // Votre adresse e-mail SMTP
            $mail->Password = '1402romain'; // Votre mot de passe SMTP
            $mail->SMTPSecure = 'tls';

            // Destinataire, sujet, corps de l'e-mail, etc.
            $mail->setFrom($expediteur, 'Votre Nom');
            $mail->addAddress($user_email, 'Nom du destinataire');
            $mail->Subject = $sujet;
            $mail->Body = $message;

            // Envoyer l'e-mail
            $mail->send();
            echo 'Événement enregistré avec succès et e-mail de confirmation envoyé.';
        } catch (Exception $e) {
            echo 'Événement enregistré avec succès, mais échec de l\'envoi de l\'e-mail de confirmation.';
        }
    } else {
        // En cas d'erreur lors de la récupération de l'e-mail de l'utilisateur
        echo 'Erreur lors de la récupération de l\'e-mail de l\'utilisateur.';
    }
} else {
    // En cas d'erreur lors de l'enregistrement de la réservation
    echo 'Une erreur s\'est produite lors de l\'enregistrement de la réservation.';
}

// Fermeture de la connexion à la base de données
$con->close();
