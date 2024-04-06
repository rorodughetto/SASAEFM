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
$currentDate = date('Y-m-d\TH:i', strtotime('now'));

?>

</html>
<!DOCTYPE html>
<html>

<head>
	<meta charset='utf-8' />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Page de réservation</title>
	<link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css"
		integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous" />
	<link rel="stylesheet" type="text/css" href="./css/reservation_salle_technique.css">
	<link rel="stylesheet" href="./css/bootstrap.min.css">
	<link rel="stylesheet" href="./fullcalendar/lib/main.min.css">
	<script src="./js/jquery-3.6.0.min.js"></script>
	<script src="./js/bootstrap.min.js"></script>
	<script src="./fullcalendar/lib/main.min.js"></script>

</head>

<body class="bg-light">
	<!-- Modifiez cette ligne pour ajouter la classe CSS personnalisée -->
	<div class="header">
		<p class="message welcome-message">Bonjour <?php echo $_SESSION['utilisateur']; ?></p>
		<form action="accueil.php" method="post">
			<button type="submit" class="accueil-button">Accueil</button>
		</form>
		<form action="deconnexion.php" method="post">
			<button type="submit" class="logout-button">Déconnexion</button>
		</form>
	</div>


	<div class="container py-5" id="page-container">
		<div class="row">
			<div class="col-md-9">
				<div id="calendar"></div>
			</div>
			<div class="col-md-3">
				<div class="cardt rounded-0 shadow">
					<div class="card-header bg-gradient bg-primary text-light">
						<h5 class="card-title">Réservation salle technique</h5>
					</div>
					<div class="card-body">
						<div class="container-fluid">
							<form action="save_tecnical_schedule.php" method="post" id="schedule-form">
								<input type="hidden" name="id" value="">
								<div class="form-group mb-2">
									<label for="deceased_name" class="control-label">Nom du défunt</label>
									<input type="text" class="form-control form-control-sm rounded-0"
										name="deceased_name" id="deceased_name" required>
								</div>

								<div class="form-group mb-2">
									<label for="start_datetime" class="control-label">Début</label>
									<input type="datetime-local" class="form-control form-control-sm rounded-0"
										value="<?php echo date('Y-m-d\TH:00', strtotime('+2 hour')); ?>"
										name="start_datetime" id="start_datetime"
										required>
								</div>

								<div class="form-group mb-2">
									<label for="option" class="control-label">Option</label>
									<div class="form-check">
										<input class="form-check-input" type="radio" name="option"
											id="option_soin_habillage" value="Soin et habillage" required checked>
										<label class="form-check-label" for="option_soin_habillage">
											Soin et habillage
										</label>
									</div>
									<div class="form-check">
										<input class="form-check-input" type="radio" name="option"
											id="option_rite_musulman" value="Toilette rituelle" required>
										<label class="form-check-label" for="option_rite_musulman">
											Toilette rituelle
										</label>
									</div>

								</div>

							</form>
						</div>

					</div>
					<div class="card-footer">
						<div class="text-center">
							<button class="btn btn-primary btn-sm rounded-0" type="submit" form="schedule-form"><i
									class="fa fa-save"></i>Enregistrer</button>
							<button class="btn btn-default border btn-sm rounded-0" type="reset" form="schedule-form"><i
									class="fa fa-reset"></i>Annuler</button>
						</div>
					</div>

				</div>
				<div class='alert alert-info'>Réservation sur créneau de 3h à compter de l'heure mentionner !
				</div>
			</div>
		</div>
	</div>
	<!-- Event Details Modal -->
	<div class="modal fade" tabindex="-1" data-bs-backdrop="static" id="event-details-modal">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content rounded-0">
				<div class="modal-header rounded-0">
					<h5 class="modal-title">Détail de l'événement</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body rounded-0">
					<div class="container-fluid">
						<dl>
							<dt class="text-muted">Demandeur</dt>
							<dd id="author" class=""></dd>
							<dt class="text-muted">Nom du défunt</dt>
							<dd id="deceased_name" class=""></dd>
							<dt class="text-muted">Début</dt>
							<dd id="start_time" class=""></dd>
							<dt class="text-muted">Fin</dt>
							<dd id="end_time" class=""></dd>
							<dt class="text-muted">Option</dt>
							<dd id="tecnical_option" class=""></dd>
						</dl>
					</div>
				</div>

				<div class="modal-footer rounded-0">
					<div class="text-end">

						<button type="button" class="btn btn-danger btn-sm rounded-0" id="delete"
							data-id="">Supprimer</button>
						<button type="button" class="btn btn-secondary btn-sm rounded-0"
							data-bs-dismiss="modal">Fermer</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Event Details Modal -->

	<?php
	$schedules = $con->query("SELECT * FROM `schedule_list_technical`");
	$sched_res = [];
	foreach ($schedules->fetch_all(MYSQLI_ASSOC) as $row) {
		$row['sdate'] = date("d M Yh:i A", strtotime($row['start_time']));
		$row['edate'] = date("d M Yh:i A", strtotime($row['end_time']));

		$sched_res[$row['id']] = $row;
	}
	?>
	<?php
	if (isset($con)) $con->close();
	?>
</body>
<script>
var scheds = $.parseJSON('<?= json_encode($sched_res) ?>')
</script>


<script src="./js/script_reservation_salle_technique.js"></script>


</html>