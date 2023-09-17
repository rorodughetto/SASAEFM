<?php include("./bdd/bdd-connexion.php");

//Nous allons démarrer la session avant toute chose
if (isset($con)) {
	session_start();
	if (isset($_POST['boutton-valider'])) { // Si on clique sur le boutton , alors :
		//Nous allons verifiér les informations du formulaire
		if (isset($_POST['user']) && isset($_POST['password'])) { //On verifie ici si l'utilisateur a rentré des informations
			//Nous allons mettres l'email et le mot de passe dans des variables
			$user = $_POST['user'];
			$password = $_POST['password'];
			$erreur = "";
			//requete pour selectionner  l'utilisateur qui a pour email et mot de passe les identifiants qui ont été entrées
			$req = mysqli_query($con, "SELECT * FROM users WHERE user = '$user' AND password ='$password' ");
			$num_ligne = mysqli_num_rows($req); //Compter le nombre de ligne ayant rapport a la requette SQL
			if ($num_ligne > 0) {
				header("Location:calendar.php"); //Si le nombre de ligne est > 0 , on sera redirigé vers la page bienvenu
				// Nous allons créer une variable de type session qui vas contenir l'email de l'utilisateur
				$_SESSION['utilisateur'] = $user;
			} else { //si non
				$erreur = "Utilisateur ou Mots de passe incorrectes !";
			}
		}
	}
} else {
	// La variable $con n'a pas été correctement incluse
	// Vous devrez peut-être vérifier le chemin du fichier bdd-connexion.php ou les erreurs de syntaxe.
	echo "Erreur : La variable \$con n'a pas été incluse correctement.";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="./css/connexion.css">
	<title>Formulaire de connexion</title>

</head>

<body>
	<section>
		<h1> Connexion</h1>
		<?php
		if (isset($erreur)) { // si la variable $erreur existe , on affiche le contenu ;
			echo "<p class= 'Erreur'>" . $erreur . "</p>";
		}
		?>
		<form action="" method="POST">
			<!--on ne mets plus rien au niveau de l'action , pour pouvoir envoyé les données  dans la même page -->
			<label>Utilisateur</label>
			<input type="text" name="user">
			<label>Mot de Passe</label>
			<input type="password" name="password">
			<input type="submit" value="Valider" name="boutton-valider">
		</form>
	</section>
</body>

</html>