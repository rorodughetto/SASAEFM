<?php
// Détruisez la session actuelle.
session_destroy();

// Redirigez l'utilisateur vers la page de connexion ou une autre page appropriée.
header('Location: index.php');
exit;
