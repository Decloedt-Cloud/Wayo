<div class="row">
	<div class="col-md-6">
		<div class="card">
			<div class="card-body">
				<div id="calendar"></div>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="alert alert-warning" role="alert">
			<i class="dripicons-information me-2"></i> <?php echo get_phrase('this_events_will_get_appeared_at'); ?> <strong><?php echo get_phrase('user'); ?> ( <?php echo get_phrase('backend'); ?> ) <?php echo get_phrase('panel_events'); ?></strong>.
		</div>
		<div class="card">
			<div class="card-body">
				<?php
				// $school_id = school_id();
				$user_id = $this->session->userdata('user_id');
				$school_id = $this->db->get_where('users', array('id' => $user_id))->row('school_id');

				?>
				<?php $query = $this->db->get_where('event_calendars', array('school_id' => $school_id, 'session' => active_session())); ?>
				<?php if ($query->num_rows() > 0): ?>
					<table id="basic-datatable" class="table table-striped dt-responsive nowrap table-modern" width="100%">
						<thead>
							<tr>
								<th><i class="mdi mdi-calendar-range-outline me-2 thead-icon"></i><?php echo get_phrase('event_title'); ?></th>
								<th><i class="mdi mdi-calendar-start thead-icon"></i><?php echo get_phrase('from'); ?></th>
								<th><i class="mdi mdi-calendar-end thead-icon"></i><?php echo get_phrase('to'); ?></th>
								<th><i class="mdi mdi-dots-vertical thead-icon thead-icon"></i><?php echo get_phrase('options'); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php
							$event_calendars = $this->db->get_where('event_calendars', array('school_id' => $school_id, 'session' => active_session()))->result_array();
							foreach ($event_calendars as $event_calendar) {
							?>
								<tr>
									<td class="modern-td">

										<span class="desktop-description"
											data-bs-toggle="tooltip"
											data-bs-placement="top"
											title="<?php echo htmlspecialchars($event_calendar['title']); ?>">
											<?php echo strlen($event_calendar['title']) > 30 ? substr($event_calendar['title'], 0, 30) . '...' : $event_calendar['title']; ?>
										</span>
										<span class="d-inline d-md-none ms-2">
											<?php echo strlen($event_calendar['title']) > 13 ? substr($event_calendar['title'], 0, 13) . '...' : $event_calendar['title']; ?>
										</span>

										<button type="button" class="btn btn-sm mobile-description-btn"
											data-description="<?php echo htmlspecialchars($event_calendar['title']); ?>"
											onclick="showDescriptionPopup(this)">
											<i class="mdi mdi-eye-outline"></i>
										</button>
									</td>
									<td><?php echo date('D, d M Y', strtotime($event_calendar['starting_date'])); ?></td>
									<td><?php echo date('D, d M Y', strtotime($event_calendar['ending_date'])); ?></td>
									<td>
										<div class="dropdown text-center">
											<button type="button" class="btn btn-sm btn-icon btn-rounded btn-outline-secondary dropdown-btn1 dropdown-btn dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false"><i class="mdi mdi-dots-vertical"></i></button>
											<div class="dropdown-menu dropdown-menu-end">
												<!-- item-->
												<a href="javascript:void(0);" class="dropdown-item" onclick="rightModal('<?php echo site_url('modal/popup/event_calendar/edit/' . $event_calendar['id']); ?>',&quot;<?php echo get_phrase('update_event'); ?>&quot;)"><?php echo get_phrase('edit'); ?></a>
												<!-- item-->
												<a href="javascript:void(0);" class="dropdown-item" onclick="confirmModal('<?php echo route('event_calendar/delete/' . $event_calendar['id']); ?>', showAllEvents)"><?php echo get_phrase('delete'); ?></a>
											</div>
										</div>
									</td>
								</tr>
							<?php } ?>
							<div id="description-popup-overlay" class="description-popup-overlay"></div>
							<div id="description-popup" class="description-popup">
								<div class="description-popup-header">
									<h4><?php echo get_phrase('contenu_complet'); ?></h4>
									<button onclick="hideDescriptionPopup()" class="description-popup-close">&times;</button>
								</div>
								<div id="description-popup-content" class="description-popup-content">
									<!-- Le contenu sera injecté ici par JavaScript -->
								</div>
							</div>
						</tbody>
					</table>
				<?php else: ?>
					<?php include APPPATH . 'views/backend/empty.php'; ?>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
<script>
	// Récupérer les éléments de la popup une seule fois
	const popup = document.getElementById('description-popup');
	const overlay = document.getElementById('description-popup-overlay');
	const popupContent = document.getElementById('description-popup-content');

	/**
	 * Affiche la popup avec la description
	 * @param {HTMLElement} button - Le bouton sur lequel on a cliqué
	 */
	function showDescriptionPopup(button) {
		// Récupérer le texte depuis l'attribut data-description
		const descriptionText = button.dataset.description;

		// Mettre le texte dans la popup
		// On utilise innerText pour la sécurité, mais si votre description contient du HTML, utilisez innerHTML
		popupContent.innerText = descriptionText;

		// Afficher la popup et l'overlay
		popup.classList.add('is-visible');
		overlay.classList.add('is-visible');
	}

	/**
	 * Cache la popup
	 */
	function hideDescriptionPopup() {
		popup.classList.remove('is-visible');
		overlay.classList.remove('is-visible');
	}

	// Bonus : Fermer la popup en cliquant sur le fond noir
	overlay.addEventListener('click', hideDescriptionPopup);
</script>