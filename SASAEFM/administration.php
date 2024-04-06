<?php
session_start();
require_once('./bdd/bdd-connexion.php'); // Incluez votre fichier de connexion à la base de données ici
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once "vendor/autoload.php";
// Récupérez l'ID de l'utilisateur connecté (assurez-vous que cette variable est correcte)
$id_utilisateur = $_SESSION['utilisateur'];

// Requête pour récupérer la valeur de "super" de l'utilisateur connecté dans la base de données
$sqlSuper = "SELECT `super` FROM `users` WHERE `user` = '$id_utilisateur'";
$resultSuper = $con->query($sqlSuper);

if ($resultSuper) {
	$rowSuper = $resultSuper->fetch_assoc();
	$super = $rowSuper['super'];

	// Vérifiez la valeur de "super"
	if ($super == 0) {
		// L'utilisateur a des droits d'administration
		// Vous pouvez continuer le traitement ici
	} else {
		// L'utilisateur n'a pas les droits d'administration
		// Vous pouvez rediriger l'utilisateur vers une autre page ou afficher un message d'erreur
		// Détruisez la session actuelle.
		session_destroy();

		// Redirigez l'utilisateur vers la page de connexion ou une autre page appropriée.
		header('Location: index.php');
		// Vous pouvez également rediriger l'utilisateur vers une autre page en utilisant header('Location: nom_de_la_page.php');
		exit;
	}
} else {
	// En cas d'erreur lors de la requête SQL
	echo "Erreur lors de la récupération des droits d'administration.";
	exit;
}

// Fonction pour valider le mot de passe
function validerMotDePasse($motDePasse)
{
	// Vérifier la longueur minimale (au moins 8 caractères)
	if (strlen($motDePasse) < 8) {
		return false;
	}

	// Vérifier s'il y a au moins une lettre majuscule
	if (!preg_match("/[A-Z]/", $motDePasse)) {
		return false;
	}

	// Vérifier s'il y a au moins un chiffre
	if (!preg_match("/[0-9]/", $motDePasse)) {
		return false;
	}

	// Vérifier s'il y a au moins un caractère spécial
	if (!preg_match("/[@#$%^&+=!?*()\-_.,;:]/", $motDePasse)) {
		return false;
	}

	return true;
}



/// Traitement pour la création d'un utilisateur
if (isset($_POST['create_user'])) {
	// Récupérez les données de l'utilisateur depuis le formulaire
	$nouvel_utilisateur = $_POST['new_user'];
	$nouvel_email = $_POST['new_email'];
	$nouveau_mot_de_passe = $_POST['new_password'];
	$confirmation_mot_de_passe = $_POST['confirm_password'];


	// Récupérer l'email depuis le formulaire
	$nouvel_email = $_POST['new_email'];

	// Vérifier si l'email n'existe pas déjà dans la base de données
	$check_email_sql = "SELECT COUNT(*) AS email_count FROM users WHERE email = '$nouvel_email'";
	$check_email_result = $con->query($check_email_sql);

	if ($check_email_result) {
		$email_count = $check_email_result->fetch_assoc()['email_count'];

		if ($email_count == 0) {
			// L'email n'existe pas, vous pouvez continuer le traitement
			// Votre code existant pour vérifier le mot de passe et insérer l'utilisateur dans la base de données
			if (validerMotDePasse($nouveau_mot_de_passe)) {
				// Vérifiez si les mots de passe correspondent
				if ($nouveau_mot_de_passe === $confirmation_mot_de_passe) {
					// Les mots de passe correspondent, vous pouvez hacher le mot de passe et l'insérer dans la base de données
					$hashed_password = password_hash($nouveau_mot_de_passe, PASSWORD_DEFAULT);

					// Vérifiez d'abord si l'utilisateur existe déjà dans la base de données
					$check_sql = "SELECT COUNT(*) AS user_count FROM users WHERE user = '$nouvel_utilisateur'";
					$check_result = $con->query($check_sql);

					if ($check_result) {
						$user_count = $check_result->fetch_assoc()['user_count'];

						if ($user_count == 0) {
							// L'utilisateur n'existe pas, vous pouvez insérer les données dans la base de données
							$insert_sql = "INSERT INTO users (user, password, email, super) VALUES ('$nouvel_utilisateur', '$hashed_password', '$nouvel_email', '1')";
							$insert_result = $con->query($insert_sql);
						} else {
							echo "<script>alert('Cet utilisateur existe déjà dans la base de données.');</script>";
						}
					} else {
						echo "<script>alert(' Une erreur s'est produite lors de la vérification de l'existence de l'utilisateur.');</script>";
					}
				} else {
					// Les mots de passe ne correspondent pas, affichez un message d'erreur à l'utilisateur
					echo "<script>alert('Les mots de passe ne correspondent pas. Veuillez réessayer.');</script>";
				}
			} else {
				// Le mot de passe ne répond pas aux critères minimaux, affichez un message d'erreur à l'utilisateur
				echo "<script>alert('Le mot de passe doit avoir au moins 8 caractères, inclure au moins une lettre majuscule, un chiffre et un caractère spécial.');</script>";
			}
		} else {
			echo "<script>alert('Cet email existe déjà dans la base de données.');</script>";
		}
	} else {
		echo "<script>alert(' Une erreur s'est produite lors de la vérification de l'existence de l'email.');</script>";
	}

	if ($insert_result) {
		echo "<script>alert('L\'utilisateur \"$nouvel_utilisateur\" a été ajouté avec succès.');</script>";
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

		// Propriétés de l'e-mail pour l'utilisateur
		$mail->setFrom('sasaefm@sasaefm.fr', 'SASAEFM');
		$mail->addAddress($nouvel_email, 'Utilisateur');
		$mail->isHTML(true);
		$mail->isHTML(true);
		$mail->Subject = 'Confirmation de création de compte';
		$mail->Body = "Bonjour $nouvel_utilisateur,<br><br>Votre compte d'accès au site de réservation du complexe funéraire 'Au repos de l'âme' situé 405 avenue de Mérignac a été crée avec succès. <br> Voici vos informations personnelles :<br> Nom d'utilisateur : $nouvel_utilisateur <br> Mot de passe : $nouveau_mot_de_passe <br><br> Merci de conserver ces informations en lieu sûr.";
		$mail->AltBody = 'Texte alternatif au format texte brut.';

		// Envoyer l'e-mail à l'utilisateur
		$mail->send();
	} else {
		echo "<script>alert('Une erreur s'est produite lors de l'ajout de l'utilisateur.')</script>;";
	}
}





// Traitement pour la suppression d'un utilisateur
if (isset($_POST['delete_user'])) {
	$user_to_delete = $_POST['user_to_delete'];

	if ($user_to_delete !== "") {
		// Requête pour récupérer l'adresse e-mail de l'utilisateur à supprimer
		$sql = "SELECT email FROM users WHERE user = '$user_to_delete'";

		$result = $con->query($sql);

		if ($result) {
			// Vérifiez s'il y a des résultats
			if ($result->num_rows > 0) {
				// Récupérez l'adresse e-mail de l'utilisateur
				$row = $result->fetch_assoc();
				$email_utilisateur_a_supprimer = $row['email'];
			} else {
				echo "Aucun utilisateur trouvé avec le nom d'utilisateur spécifié.";
			}
		} else {
			echo "Erreur lors de la requête : " . $conn->error;
		}
		// Supprimez l'utilisateur de la base de données (vous pouvez utiliser une requête DELETE FROM)
		$sqlDeleteUser = "DELETE FROM users WHERE user = '$user_to_delete'";
		$deleteResult = $con->query($sqlDeleteUser);

		if ($deleteResult) {
			// L'utilisateur a été supprimé avec succès
			echo "<script>alert('L\'utilisateur \"$user_to_delete\" a été supprimé avec succès.');</script>";
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

			// Propriétés de l'e-mail pour l'utilisateur
			$mail->setFrom('sasaefm@sasaefm.fr', 'SASAEFM');
			$mail->addAddress($email_utilisateur_a_supprimer, 'Utilisateur');
			$mail->isHTML(true);
			$mail->isHTML(true);
			$mail->Subject = 'Suppression  de compte';
			$mail->Body = "Bonjour $user_to_delete,<br><br>Votre compte a été supprimé.";
			$mail->AltBody = 'Texte alternatif au format texte brut.';

			// Envoyer l'e-mail à l'utilisateur
			$mail->send();
		} else {
			// En cas d'erreur lors de la suppression
			echo "<script>alert('Une erreur s'est produite lors de la suppression de l'utilisateur: '$user_to_delete'.')</script>;";
		}
	}
	$con->close();
}

// Récupérez la liste des utilisateurs depuis la base de données (vous pouvez utiliser une requête SELECT)

// Affichez le formulaire de création d'utilisateur
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="./css/administration.css">
	<title>Page d'Administration</title>
</head>

<body>
	<div class="header">
		<p class="message welcome-message">Bonjour <?php echo $_SESSION['utilisateur']; ?></p>
		<form action="accueil.php" method="post">
			<button type="submit" class="logout-button">Retour</button>
		</form>
	</div>
	<h1>Page d'Administration</h1>
	<section>
		<!-- Formulaire de création d'utilisateur -->
		<h2>Créer un nouvel utilisateur</h2>
		<form action="administration.php" method="post">
			<input type="text" name="new_user" placeholder="Nom d'utilisateur" required>
			<input type="email" name="new_email" placeholder="email" required>
			<input type="password" name="new_password" placeholder="Mot de passe" required>
			<input type="password" name="confirm_password" placeholder="Confirmer le mot de passe" required>
			<button type="submit" name="create_user">Créer</button>
		</form>

		<!-- Formulaire de suppression d'utilisateur -->
	</section>
	<section>
		<h2>Supprimer un utilisateur</h2>
		<form action="administration.php" method="post">
			<select name="user_to_delete">
				<option value="">Sélectionner un utilisateur</option>
				<?php
				// Récupérez la liste des utilisateurs depuis la base de données avec super = 1
				$sql = "SELECT user FROM users WHERE super = 1"; // Ajoutez la condition WHERE super = 1
				$result = $con->query($sql);

				if ($result) {
					while ($row = $result->fetch_assoc()) {
						$user = $row['user'];
						echo "<option value='$user'>$user</option>";
					}
				}
				?>
			</select>
			<button type="submit" name="delete_user">Supprimer</button>
		</form>
	</section>

	<!-- Affichez la liste des utilisateurs ici -->
</body>

</html>