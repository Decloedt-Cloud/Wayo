<div class="row">
	<!-- <div class="col-md-6">
		<div class="card">
			<div class="card-body">
				<div id="calendar"></div>
			</div>
		</div>
	</div> -->
	<div class="col-md-12">
		<div class="card">
			<div class="card-body">
				<?php

					$user_id = $this->session->userdata('user_id');
					$student_datas = $this->db->get_where('students', array('user_id' => $user_id))->result_array();
					if($student_datas){
						$school_ids = array();		
						foreach ($student_datas as $student_data) {
							$school_ids[] = $student_data['school_id'];
						}
						$this->db->where_in('school_id', $school_ids);

						
					}
					$announcements = $this->db->get('announcement')->num_rows();

				?>
				<?php if($announcements > 0): ?>
					<table id="basic-datatable" class="table table-striped dt-responsive nowrap" width="100%">
						<thead>
							<tr>
								<th><i class="mdi mdi-calendar-range-outline me-2 thead-icon"></i><?php echo get_phrase('event_title'); ?></th>
								<th><i class="mdi mdi-calendar-start thead-icon"></i><?php echo get_phrase('from'); ?></th>
								<th><i class="mdi mdi-calendar-end thead-icon"></i><?php echo get_phrase('to'); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach($student_datas as $student_data){
								$enrols_datas = $this->db->get_where('enrols', array('student_id' => $student_data['id'],'school_id' => $student_data['school_id']))->num_rows();
								$school_name = $this->db->get_where('schools', array('id' => $student_data['school_id']))->row('name');
								if($enrols_datas > 0){
								$announcements = $this->db->get_where('announcement', array('school_id' => $student_data['school_id'], 'session' => active_session()))->result_array();
								
								foreach($announcements as $event_calendar){
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
								</tr>
							<?php } ?>
							<?php } ?>
							<?php }  ?>
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
					<?php include APPPATH.'views/backend/empty.php'; ?>
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