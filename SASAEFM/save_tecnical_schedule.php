<?php
session_start();
require_once('./bdd/bdd-connexion.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once "vendor/autoload.php";
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo "<script> alert('Erreur : Aucune donnée à enregistrer.'); location.replace('./reservation_salle_technique.php'); </script>";
    exit;
}

// Extraction des données POST
extract($_POST);
// Récupérer la valeur de start_time depuis le formulaire
$start_time = $_POST['start_datetime'];
// Créer un objet DateTime à partir de start_time
$start_time_obj = new DateTime($start_time);

// Récupérer la valeur de end_datetime depuis le formulaire
$end_time = $start_time_obj->add(new DateInterval('PT3H'));
// Convertir l'objet DateTime en string
$end_time = $end_time->format('Y-m-d H:i:s');
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
        echo "<script> alert('Une erreur s'est produite. <br> Erreur : " . $con->error . "  <br>  SQL : " . $sql_check . "'); location.replace('./reservation_salle_technique.php'); </script>";

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
        echo "<script> alert('Une erreur s'est produite. <br> Erreur : " . $con->error . "  <br>  SQL : " . $sql_check . "'); location.replace('./reservation_salle_technique.php'); </script>";

        exit;
    }

    $sql = "UPDATE `schedule_list_technical` SET `author` = '$author', `deceased_name` = '$deceased_name',`start_time` = '$start_time', `end_time` = '$end_time',`tecnical_option`='$option' WHERE `id` = '$id'";
}

// Exécution de la requête SQL
$save = $con->query($sql);
// Récupération de l'ID de la dernière insertion
$sql = "SELECT MAX(id) AS dernier_id FROM schedule_list_technical";
$result_id = $con->query($sql);
$result_id_info = $result_id->fetch_assoc();
// Récupérez l'ID
$id = $result_id_info['dernier_id'];

if ($save) {
    $sql_reservation_info = "SELECT `author`, `tecnical_option`, `start_time`, `end_time` FROM `schedule_list_technical` WHERE `id` = '$id'";
    $result_reservation_info = $con->query($sql_reservation_info);

    // Vérifiez si la requête s'est exécutée avec succès
    if ($result_reservation_info) {
        $row_reservation_info = $result_reservation_info->fetch_assoc();

        // Mettez à jour les variables avec les données de la base de données
        $author = $row_reservation_info['author'];
        $tecnical_option = $row_reservation_info['tecnical_option'];
        $startTime = date('Y-m-d H:i', strtotime($reservationInfo['start_time']));
        $endTime = date('Y-m-d H:i', strtotime($reservationInfo['end_time']));
    } else {
        // En cas d'erreur lors de l'exécution de la requête
        echo "<script> alert('Une erreur s'est produite. <br> Erreur : " . $con->error . "'); location.replace('./reservation_salle_technique.php'); </script>";
    }
    // Récupérer l'e-mail de l'utilisateur depuis la table "users" (en supposant une colonne "email")
    $sql_user = "SELECT email FROM users WHERE user = '$author'";
    $result_user = $con->query($sql_user);
    // Requête pour récupérer les informations de réservation
    if ($result_user) {
        $row_user = $result_user->fetch_assoc();
        $user_email = $row_user['email'];

        // Configuration SMTP
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->CharSet = 'UTF-8';
        $mail->SMTPAuth = true;
        $mail->Host = 'smtp.ionos.fr';
        $mail->Port = 465; // Utilisez 465 pour SSL
        $mail->Username = 'sasaefm@sasaefm.fr'; // Votre adresse e-mail Ionos
        $mail->Password = 'D5y.FMqbyzjJb.*'; // Votre mot de passe e-mail Ionos
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

        try {
            // Propriétés de l'e-mail pour l'utilisateur
            $mail->setFrom('sasaefm@sasaefm.fr', 'SASAEFM');
            $mail->addAddress($user_email, 'SASAEFM');
            $mail->isHTML(true);
            $mail->Subject = 'Confirmation de réservation';
            $mail->Body = "Bonjour $author,<br><br>Votre réservation de la salle technique pour $tecnical_option du $start_time au $end_time est confirmé.";
            $mail->AltBody = 'Texte alternatif au format texte brut.';


            // Envoyer l'e-mail à l'utilisateur
            $mail->send();
        } catch (Exception $e) {
            echo "<script> alert('Une erreur s'est produite. <br> Erreur d'envoi : " . $mail->ErrorInfo . "'); location.replace('./reservation_salle_technique.php'); </script>";
        } // Propriétés de l'e-mail pour SASAEFM
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->SMTPAuth = true;
        $mail->CharSet = 'UTF-8';
        $mail->Host = 'smtp.ionos.fr';
        $mail->Port = 465; // Utilisez 465 pour SSL
        $mail->Username = 'sasaefm@sasaefm.fr'; // Votre adresse e-mail Ionos
        $mail->Password = 'D5y.FMqbyzjJb.*'; // Votre mot de passe e-mail Ionos
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

        try {
            $mail->setFrom('sasaefm@sasaefm.fr', 'SASAEFM');
            $mail->addAddress('sasaefm@sasaefm.fr', 'Destinataire SASAEFM');
            $mail->isHTML(true);
            $mail->Subject = 'Nouvelle réservation';
            $mail->Body = "Bonjour <br><br>Une nouvelle réservation a été demandée par $author pour l'option $tecnical_option du $start_time au $end_time. Veuillez vérifier les détails.";
            $mail->AltBody = 'Texte alternatif au format texte brut.';

            // Envoyer l'e-mail
            $mail->send();

            // Vérifier le résultat de l'envoi
        } catch (Exception $e) {
            echo " <script> alert('Une erreur s'est produite. <br> Erreur d'envoi : " . $mail->ErrorInfo . "'); location.replace('./reservation_salle_technique.php'); </script>";
        }

        // Redirection en cas de succès
        echo "<script>alert('Événement enregistré avec succès et e-mail de confirmation envoyé.'); location.replace('./reservation_salle_technique.php');;</script>";
    } else {
        // En cas d'erreur lors de la récupération de l'e-mail de l'utilisateur
        echo "<script> alert('Une erreur s'est produite. <br> Erreur : " . $con->error . "  <br>  SQL : " . $sql_user . "'); location.replace('./reservation_salle_technique.php'); </script>";
    }
} else {
    // En cas d'erreur lors de l'enregistrement de la réservation
    echo "<script> alert('Une erreur s'est produite. <br> Erreur : " . $con->error . "  <br>  SQL : " . $sql_user . "'); location.replace('./reservation_salle_technique.php'); </script>";
}

// Fermeture de la connexion à la base de données
$con->close();
