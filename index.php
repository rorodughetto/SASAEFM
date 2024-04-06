<?php include("./bdd/bdd-connexion.php");

//Nous allons démarrer la session avant toute chose
if (isset($con)) {
	session_start();
	if (isset($_POST['boutton-valider'])) { // Si on clique sur le bouton, alors :
		// Nous allons vérifier les informations du formulaire
		if (isset($_POST['user']) && isset($_POST['password'])) { // On vérifie ici si l'utilisateur a rentré des informations
			// Nous allons mettre l'email et le mot de passe dans des variables
			$user = $_POST['user'];
			$password = $_POST['password'];
			$erreur = "";

			// Requête pour sélectionner l'utilisateur par son nom d'utilisateur
			$req = mysqli_query($con, "SELECT * FROM users WHERE user = '$user'");

			if ($req) {
				$num_ligne = mysqli_num_rows($req); // Compter le nombre de lignes ayant rapport à la requête SQL

				if ($num_ligne > 0) {
					// Utilisateur trouvé, maintenant vérifions le mot de passe
					$row = mysqli_fetch_assoc($req);
					$hashed_password = $row['password']; // Récupérer le mot de passe haché depuis la base de données

					if (password_verify($password, $hashed_password)) {
						// Le mot de passe correspond, nous pouvons rediriger l'utilisateur
						header("Location:accueil.php");
						// Nous allons créer une variable de type session qui va contenir le nom d'utilisateur de l'utilisateur
						$_SESSION['utilisateur'] = $user;
					} else {
						$erreur = "Utilisateur ou mot de passe incorrect !";
					}
				} else {
					$erreur = "Utilisateur ou mot de passe incorrect !";
				}
			} else {
				$erreur = "Erreur lors de la requête SQL.";
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
			<div class="password">
				<input type="password" id="myInput" name="password">
				<!-- An element to toggle between password visibility -->
				<input type="checkbox" onclick="myFunction()">
			</div>
			<script>
			function myFunction() {
				var x = document.getElementById("myInput");
				if (x.type === "password") {
					x.type = "text";
				} else {
					x.type = "password";
				}
			}
			</script>
			<input type="submit" value="Valider" name="boutton-valider">
		</form>
	</section>
</body>

</html>