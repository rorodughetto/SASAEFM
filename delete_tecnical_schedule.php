<?php
session_start();
require_once('./bdd/bdd-connexion.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once "vendor/autoload.php";

// Récupérez l'ID de la réservation à supprimer depuis la requête GET (ou POST, selon la méthode de votre formulaire)
$id = $_GET['id'];
$id_utilisateur = $_SESSION['utilisateur'];

// Récupérez l'auteur de la réservation associée à l'ID
$sql = "SELECT * FROM `schedule_list_technical` WHERE `id` = '$id'";
$result = $con->query($sql);
$sqlSuper = "SELECT `super` FROM `users` WHERE `user` = '$id_utilisateur'";
$resultSuper = $con->query($sqlSuper);
$rowSuper = $resultSuper->fetch_assoc();

if ($result) {
    $row = $result->fetch_assoc();

    $tecnical_option = $row['tecnical_option'];

    $startTime = date('d M Y', strtotime($reservationInfo['start_time']));
    $endTime = date('d M Y', strtotime($reservationInfo['end_time']));

    $name = $row['deceased_name'];
    $author = $row['author'];

    // Vérifiez si l'utilisateur en cours correspond à l'auteur de la réservation
    if ($_SESSION['utilisateur'] === $author || $rowSuper['super']  == 0) {
        // L'utilisateur en cours est autorisé à supprimer la réservation
        // Exécutez la requête de suppression ici
        $deleteSql = "DELETE FROM `schedule_list_technical` WHERE `id` = '$id'";
        $deleteResult = $con->query($deleteSql);
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
                $mail->Subject = 'Suppression de réservation';
                $mail->Body = "Bonjour $author,<br><br>Votre réservation a été supprimée avec succès.<br><br>Informations de réservation :<br> - Option : $tecnical_option<br> - Date de début : $start_time<br> - Date de fin : $end_time<br> - Nom du défunt : $name";
                $mail->AltBody = 'Texte alternatif au format texte brut.';


                // Envoyer l'e-mail à l'utilisateur
                $mail->send();
            } catch (Exception $e) {
                echo " <script> alert('Erreur d'envoi : " . $mail->ErrorInfo . "'); location.replace('./reservation_salle_technique.php'); </script>"; // Erreur d'envoi : " . $mail->ErrorInfo;
            }
            try {
                // Propriétés de l'e-mail pour l'utilisateur
                $mail->setFrom('sasaefm@sasaefm.fr', 'SASAEFM');
                $mail->addAddress('sasaefm@sasaefm.fr', 'SASAEFM');

                $mail->isHTML(true);
                $mail->Subject = 'Suppression de réservation';
                $mail->Body = "Bonjour,<br><br>La réservation de $author a été supprimée avec succès.<br><br>Informations de réservation :<br> - Option : $tecnical_option<br> - Date de début : $start_time<br> - Date de fin : $end_time<br> - Nom du défunt : $name";
                $mail->AltBody = 'Texte alternatif au format texte brut.';


                // Envoyer l'e-mail à l'utilisateur
                $mail->send();
            } catch (Exception $e) {
                echo "<script> alert('Erreur d'envoi : " . $mail->ErrorInfo . "'); location.replace('./reservation_salle_technique.php'); </script>"; // Erreur d'envoi : " . $mail->ErrorInfo;
            }
        }
        if ($deleteResult) {
            // La suppression a réussi
            echo "<script> alert('Réservation supprimée avec succès.'); location.replace('./reservation_salle_technique.php'); </script>";
        } else {
            // En cas d'erreur lors de la suppression
            echo "<script> alert('Une erreur s\'est produite lors de la suppression de la réservation.'); location.replace('./reservation_salle_technique.php'); </script>";
        }
    } else {
        // L'utilisateur en cours n'est pas autorisé à supprimer cette réservation
        echo "<script> alert('Vous n\'êtes pas autorisé à supprimer cette réservation. '); location.replace('./reservation_salle_technique.php'); </script>";
    }
} else {
    // En cas d'erreur lors de la récupération de l'auteur de la réservation
    echo "<script> alert('Une erreur s\'est produite lors de la récupération des données de réservation.'); location.replace('./reservation_salle_technique.php'); </script>";
}

// Fermeture de la connexion à la base de données
$con->close();
