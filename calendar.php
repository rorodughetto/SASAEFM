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
	<link rel="stylesheet" type="text/css" href="./css/calendar.css">
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


	<div class="container py-5" id="page-container">
		<div class="row">
			<div class="col-md-9">
				<div id="calendar"></div>
			</div>
			<div class="col-md-3">
				<div class="cardt rounded-0 shadow">
					<div class="card-header bg-gradient bg-primary text-light">
						<h5 class="card-title">Réservation</h5>
					</div>
					<div class="card-body">
						<div class="container-fluid">
							<form action="save_schedule.php" method="post" id="schedule-form">
								<input type="hidden" name="id" value="">
								<div class="form-group mb-2">
									<label for="room_name" class="control-label">Salle</label>
									<select class="form-control form-control-sm rounded-0" name="room_name"
										id="room_name" required>
										<option value="">--Sélectionner une salle--</option>
										<?php foreach ($salles as $salle) { ?>
										<option value="<?php echo $salle['room_name']; ?>">
											<?php echo $salle['room_name']; ?>
										</option>
										<?php } ?>
									</select>
								</div>
								<div class="form-group mb-2">
									<label for="deceased_name" class="control-label">Nom du défunt</label>
									<input type="text" class="form-control form-control-sm rounded-0"
										name="deceased_name" id="deceased_name" required>
								</div>

								<div class="form-group mb-2">
									<label for="start_datetime" class="control-label">Début</label>
									<input type="datetime-local" class="form-control form-control-sm rounded-0"
										name="start_datetime" id="start_datetime" required>
								</div>
								<div class="form-group mb-2">
									<label for="end_datetime" class="control-label">Fin</label>
									<input type="datetime-local" class="form-control form-control-sm rounded-0"
										name="end_datetime" id="end_datetime" required>
								</div>
								<div class="form-group mb-2">
									<label for="technical_room_reservation" class="control-label">Réservation salle
										technique</label>
									<div class="row">
										<div class="col">
											<div class="form-check">
												<input type="radio" class="form-check-input"
													name="technical_room_reservation_choice"
													id="technical_room_reservation_yes" value="Oui">
												<label class="form-check-label"
													for="technical_room_reservation_yes">Oui</label>
											</div>

										</div>
										<div class="col">
											<div class="form-check">
												<input type="radio" class="form-check-input"
													name="technical_room_reservation_choice"
													id="technical_room_reservation_no" value="Non" checked>
												<label class="form-check-label"
													for="technical_room_reservation_no">Non</label>
											</div>

										</div>

									</div>

									<!-- Modifiez la section du formulaire comme suit -->
									<div class="form-group mb-2 reservation-fields"
										id="technical-room-reservation-time">
										<label for="technical_room_reservation_time" class="control-label">Date et
											Heure</label>
										<input type="datetime-local" class="form-control form-control-sm rounded-0"
											name="technical_room_reservation_time" id="technical_room_reservation_time"
											required>
									</div>

									<!-- Modifiez la partie JavaScript comme suit -->
									<script>
									$(function() {
										var technicalReservationChoice = $('input[name="technical_room_reservation_choice"]');
										var technicalReservationTime = $('#technical-room-reservation-time');

										// Masquez le champ d'horaire de la salle technique initialement
										technicalReservationTime.hide();

										// Gérez les changements de choix "Oui" ou "Non"
										technicalReservationChoice.change(function() {
											if (this.value === 'Oui') {
												technicalReservationTime.show(); // Affichez le champ si "Oui" est sélectionné
												technicalReservationTime.find('input').prop('required',
													true); // Rendez le champ requis
											} else {
												technicalReservationTime.hide(); // Masquez le champ si "Non" est sélectionné
												technicalReservationTime.find('input').prop('required',
													false); // Retirez la validation requise
											}
										});

										// Assurez-vous que le comportement initial est correct lors du chargement de la page
										if (technicalReservationChoice.filter(':checked').val() === 'Oui') {
											technicalReservationTime.show();
											technicalReservationTime.find('input').prop('required', true);
										} else {
											technicalReservationTime.hide();
											technicalReservationTime.find('input').prop('required', false);
										}
									});
									</script>


									<div class="form-group mb-2">
										<label for="ritual_toilet" class="control-label">Toilette rituelle</label>
										<div class="row">
											<div class="col">
												<div class="form-check">
													<input type="radio" class="form-check-input" name="ritual_toilet"
														id="ritual_toilet_yes" value="Oui">
													<label class="form-check-label" for="ritual_toilet_yes">Oui</label>
												</div>

											</div>
											<div class="col">
												<div class="form-check">
													<input type="radio" class="form-check-input" name="ritual_toilet"
														id="ritual_toilet_no" value="Non" checked>
													<label class="form-check-label" for="ritual_toilet_no">Non</label>
												</div>

											</div>
										</div>


										<div class="form-group mb-2">
											<label for="care" class="control-label">Soins</label>
											<div class="row">
												<div class="col">
													<div class="form-check">
														<input type="radio" class="form-check-input" name="care"
															id="care_yes" value="Oui">
														<label class="form-check-label" for="care_yes">Oui</label>
													</div>
												</div>
												<div class="col">
													<div class="form-check">
														<input type="radio" class="form-check-input" name="care"
															id="care_no" value="Non" checked>
														<label class="form-check-label" for="care_no">Non</label>
													</div>

												</div>
											</div>

											<div class="form-group mb-2">
												<label for="toilet_and_dressing" class="control-label">Toilette et
													Habillage</label>
												<div class="row">
													<div class="col">
														<div class="form-check">
															<input type="radio" class="form-check-input"
																name="toilet_and_dressing" id="toilet_and_dressing_yes"
																value="Oui">
															<label class="form-check-label"
																for="toilet_and_dressing_yes">Oui</label>
														</div>
													</div>
													<div class="col">
														<div class="form-check">

															<input type="radio" class="form-check-input"
																name="toilet_and_dressing" id="toilet_and_dressing_no"
																value="Non" checked>
															<label class="form-check-label"
																for="toilet_and_dressing_no">Non</label>
														</div>

													</div>
												</div>
											</div>
							</form>



						</div>
					</div>
					<div class="card-footer">
						<div class="text-center">
							<button class="btn btn-primary btn-sm rounded-0" type="submit" form="schedule-form"><i
									class="fa fa-save"></i> Enregistrer</button>
							<button class="btn btn-default border btn-sm rounded-0" type="reset" form="schedule-form"><i
									class="fa fa-reset"></i> Annuler</button>
						</div>
					</div>
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
							<dt class="text-muted">Salle</dt>
							<dd id="room_name" class=""></dd>
							<dt class="text-muted">Début</dt>
							<dd id="start_datetime" class=""></dd>
							<dt class="text-muted">Fin</dt>
							<dd id="end_datetime" class=""></dd>
							<dt class="text-muted">Réservation salle technique</dt>
							<dd id="technical_room_reservation" class=""></dd>
							<dt class="text-muted conditional-display">Date et Heure</dt>
							<dd id="technical_room_reservation_time" class="conditional-display"></dd>

							<script>
							$(document).ready(function() {
								// Sélectionnez l'élément <dd> correspondant à la réservation de la salle technique
								const technicalRoomReservation = $('#technical_room_reservation');

								// Sélectionnez les éléments avec la classe "conditional-display"
								const conditionalElements = $('.conditional-display');

								// Fonction pour masquer ou afficher les éléments en fonction de la réservation de la salle technique
								function toggleElements() {
									if (technicalRoomReservation.text().trim().toLowerCase() === 'non') {
										conditionalElements.hide();
									} else {
										conditionalElements.show();
									}
								}

								// Appelez la fonction pour gérer l'affichage initial
								toggleElements();

								// Gérez les changements lorsque le contenu de l'élément réservation de la salle technique change
								technicalRoomReservation.on('DOMSubtreeModified', function() {
									toggleElements();
								});
							});
							</script>

							<dt class="text-muted">Toilette et Habillage</dt>
							<dd id="toilet_and_dressing" class=""></dd>
							<dt class="text-muted">Soins</dt>
							<dd id="care" class=""></dd>
							<dt class="text-muted">Toilette rituelle</dt>
							<dd id="ritual_toilet" class=""></dd>

							<!-- Fin des nouvelles données -->

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
	$schedules = $con->query("SELECT * FROM `schedule_list`");
	$sched_res = [];
	foreach ($schedules->fetch_all(MYSQLI_ASSOC) as $row) {
		$row['sdate'] = date("F d, Y h:i A", strtotime($row['start_datetime']));
		$row['edate'] = date("F d, Y h:i A", strtotime($row['end_datetime']));
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


<script src="./js/script.js"></script>


</html>