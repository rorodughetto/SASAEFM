<?php
include("./bdd/bdd-connexion.php");
include("./bdd/bdd-selectable.php");
//On demare la session sur sur cette page
session_start();
// Définissez le temps d'expiration de la session (par exemple, 5 minutes).
// Vérifier si l'utilisateur est connecté.
if (!isset($_SESSION['utilisateur'])) {
	// Si la session n'est pas définie, rediriger vers une page d'erreur.
	header('Location: index.php'); // Remplacez "page_d_erreur.php" par l'URL de la page d'erreur.
	exit;
}

?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<title>Menu de Réservation</title>

	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="./css/accueil.css">

</head>
</head>

<body>
	<div class="header">
		<p class="message welcome-message">Bonjour <?php echo $_SESSION['utilisateur']; ?></p>
		<?php
		$user = $_SESSION['utilisateur'];
		// Vérifiez la valeur de "super" pour décider d'afficher ou non le bouton d'administration utilisateur.
		$sqlSuper = "SELECT `super` FROM `users` WHERE `user` = '$user'";

		$resultSuper = $con->query($sqlSuper);
		$rowSuper = $resultSuper->fetch_assoc();

		if ($rowSuper['super'] == 0) {
			// Afficher le bouton d'administration utilisateur avec la classe de style
			echo '<form action="administration.php" method="post">
			<button type="submit" class="button">Administration</button>
		</form>';
		}
		?>
		<form action="deconnexion.php" method="post">
			<button type="submit" class="logout-button">Déconnexion</button>
		</form>
	</div>

	<h1>Menu de Réservation</h1>

	<div class="button-container">
		<!-- Bouton pour la réservation de salle -->
		<form action="reservation_salle.php" method="post">
			<button type="submit">Réservation de salle</button>
		</form>

		<!-- Bouton pour la réservation de salle technique -->
		<form action="reservation_salle_technique.php" method="post">
			<button type="submit">Réservation de salle technique</button>
		</form>
	</div>
	<div class="texte-milieu">
		<br>

		La SAS AEFM a développé un outil de gestion de réservation sur le complexe funéraire au repos de l'âme.<br>
		<br>
		1) Avant réception de la famille, veuillez effectuer une préréservation.<br><br>
		2) Notifier à votre transporteur le numéro de cellule réservé.<br><br>
		3) Dans un délai de 72h, veuillez confirmer définitivement les modalités de séjour de votre défunt sur le site
		avec envoi de la fiche d'admission, et du certificat de décès à PF33@PF33.fr et sasaefm@gmail.com.<br>
		<br>Nathalie EYQUEM, Présidente SAS AEFM
	</div>


</body>

</html>