<?php
session_start();
require_once('./bdd/bdd-connexion.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once "vendor/autoload.php";

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo "<script>alert('Erreur : Aucune donnée à enregistrer.'); location.replace('./reservation_salle.php');</script>";
    exit;
}

// Extraction des données POST
extract($_POST);

// Récupérer la valeur de start_datetime depuis le formulaire
$start_datetime = $_POST['start_datetime'];
// Récupérer la valeur de end_datetime depuis le formulaire
$end_datetime = $_POST['end_datetime'];

$allday = isset($allday);

// Vérification de l'existence de l'ID
if (empty($id)) {
    // Si l'ID est vide, il s'agit d'une insertion

    // Vérifier si la salle est déjà réservée pour la même période
    $sql_check
        = "SELECT COUNT(*) as count FROM `schedule_list` WHERE `room_name`='$room_name' AND
              (
                (`start_datetime` >= '$start_datetime' AND `start_datetime` < '$end_datetime') OR
                (`end_datetime` > '$start_datetime' AND `end_datetime` <= '$end_datetime') OR
                ('$start_datetime' >= `start_datetime` AND '$start_datetime' < `end_datetime`)
              )";

    $result_check = $con->query($sql_check);

    if ($result_check) {
        $row = $result_check->fetch_assoc();
        if ($row['count'] > 0) {
            echo "<script>alert('La salle est déjà réservée pour cette période.'); location.replace('./reservation_salle.php');</script>";
            exit;
        }
    } else {
        echo "<script>alert('Une erreur s'est produite. <br> Erreur : " . $con->error . "  <br>  SQL : " . $sql_check . "'); location.replace('./reservation_salle.php');</script>";

        exit;
    }

    $author = $_SESSION['utilisateur'];
    $sql = "INSERT INTO `schedule_list` (`author`, `deceased_name`, `room_name`, `start_datetime`, `end_datetime`) VALUES ('$author', '$deceased_name', '$room_name', '$start_datetime', '$end_datetime')";
} else {
    // Sinon, il s'agit d'une mise à jour

    // Vérifier d'abord s'il y a un conflit avec une autre réservation
    $sql_check = "SELECT COUNT(*) as count FROM `schedule_list` WHERE
                  ((`start_datetime` >= '$start_datetime' AND `start_datetime` < '$end_datetime') OR
                   (`end_datetime` > '$start_datetime' AND `end_datetime` <= '$end_datetime') OR
                   ('$start_datetime' >= `start_datetime` AND '$start_datetime' < `end_datetime')) AND `id` != '$id' AND 'room_name'=='$room_name'";
    $result_check = $con->query($sql_check);

    if ($result_check) {
        $row = $result_check->fetch_assoc();
        if ($row['count'] > 0) {
            echo "<script>alert('La salle est déjà réservée pour cette période.'); location.replace('./reservation_salle.php');</script>";
            exit;
        }
    } else {
        echo "<script>location.replace('./reservation_salle.php');</script>";
        exit;
    }

    $sql = "UPDATE `schedule_list` SET `author` = '$author', `deceased_name` = '$deceased_name', `room_name` = '$room_name', `start_datetime` = '$start_datetime', `end_datetime` = '$end_datetime' WHERE `id` = '$id'";
}

// Exécution de la requête SQL
$save = $con->query($sql);
// Récupération de l'ID de la dernière insertion
$sql = "SELECT MAX(id) AS dernier_id FROM schedule_list";
$result_id = $con->query($sql);
$result_id_info = $result_id->fetch_assoc();
// Récupérez l'ID
$id = $result_id_info['dernier_id'];
if ($save) {

    $sql_reservation_info = "SELECT `author`, `room_name`, `start_datetime`, `end_datetime` FROM `schedule_list` WHERE `id` = '$id'";
    $result_reservation_info = $con->query($sql_reservation_info);

    // Vérifiez si la requête s'est exécutée avec succès
    if ($result_reservation_info) {
        $row_reservation_info = $result_reservation_info->fetch_assoc();

        // Mettez à jour les variables avec les données de la base de données
        $author = $row_reservation_info['author'];
        $room_name = $row_reservation_info['room_name'];
        $start_datetime = date('d M Y H:i', strtotime($row_reservation_info['start_datetime']));
        $end_datetime = date('d M Y H:i', strtotime($row_reservation_info['end_datetime']));
    } else {
        // En cas d'erreur lors de l'exécution de la requête
        echo "<script> alert(' Une erreur s'est produite lors de la récupération des informations de réservation : " . $con->error . "'); location.replace('./reservation_salle.php');</script>";
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
            $mail->addAddress($user_email, 'Utilisateur');
            $mail->isHTML(true);
            $mail->Subject = 'Confirmation de réservation';
            $mail->Body = "Bonjour $author,<br><br>Votre réservation : $room_name a été confirmée du $start_datetime au $end_datetime.<br>Nous vous remercions d'informer votre transporteur précisément de l'emplacement réservé au défunt.<br> D'autre part,veuillez nous adresser la fiche d'admission en précisant les prestations validées avec la famille sous un délai de 72h.";
            $mail->AltBody = 'Texte alternatif au format texte brut.';


            // Envoyer l'e-mail à l'utilisateur
            $mail->send();
        } catch (Exception $e) {
            echo " <script> alert(' Erreur d'envoi : " . $mail->ErrorInfo . "');location.replace('./reservation_salle.php');</script>";
        }

        // Propriétés de l'e-mail pour SASAEFM
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
            $mail->Body = "Bonjour <br><br>Une nouvelle réservation a été demandée par $author pour $room_name du $start_datetime au $end_datetime. Veuillez vérifier les détails.";
            $mail->AltBody = 'Texte alternatif au format texte brut.';

            // Envoyer l'e-mail
            $mail->send();
        } catch (Exception $e) {
            echo " <script> alert(' Erreur d'envoi : " . $mail->ErrorInfo . "');
            location.replace('./reservation_salle.php');</script>";
        }

        // Redirection en cas de succès
        echo "<script>alert('Événement enregistré avec succès et e-mail de confirmation envoyé.');location.replace('./reservation_salle.php');</script>";
    } else {
        // En cas d'erreur lors de la récupération de l'e-mail de l'utilisateur
        echo "<script>alert('Erreur : " . $con->error . "');location.replace('./reservation_salle.php');</script>";
    }
} else {
    // En cas d'erreur lors de l'enregistrement de la réservation
    echo "<script>alert('Erreur : " . $con->error . " <br> SQL : " . $sql . "');location.replace('./reservation_salle.php');</script>";
}

// Fermeture de la connexion à la base de données
$con->close();
