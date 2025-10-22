<?php
$school_id = school_id();
$check_data = $this->db->get_where('users', array('school_id' => $school_id, 'role' => 'accountant'));
if ($check_data->num_rows() > 0): ?>

	<table id="basic-datatable" class="table table-striped dt-responsive nowrap table-modern" width="100%">
		<thead>
			<tr>
				<th><i class="mdi mdi-account-multiple-outline thead-icon"></i><?php echo get_phrase('name'); ?></th>
				<th><i class="mdi mdi-email-outline thead-icon"></i><?php echo get_phrase('email'); ?></th>
				<th><?php echo get_phrase('options'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			$users = $this->db->get_where('users', array('school_id' => $school_id, 'role' => 'accountant'))->result_array();
			foreach ($users as $user) {
			?>
				<tr>
					<td><?php echo $user['name']; ?></td>
					<td class="modern-td">

						<span class="desktop-description"
							data-bs-toggle="tooltip"
							data-bs-placement="top"
							title="<?php echo htmlspecialchars($user['email']); ?>">
							<?php echo strlen($user['email']) > 30 ? substr($user['email'], 0, 30) . '...' : $user['email']; ?>
						</span>
						<span class="d-inline d-md-none ms-2">
							<?php echo strlen($user['email']) > 13 ? substr($user['email'], 0, 13) . '...' : $user['email']; ?>
						</span>

						<button type="button" class="btn btn-sm mobile-description-btn"
							data-description="<?php echo htmlspecialchars($user['email']); ?>"
							onclick="showDescriptionPopup(this)">
							<i class="mdi mdi-eye-outline"></i>
						</button>
					</td>

					<td>
						<div class="dropdown text-center">
							<button type="button" class="btn btn-sm btn-icon btn-rounded btn-outline-secondary dropdown-btn1 dropdown-btn dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false"><i class="mdi mdi-dots-vertical"></i></button>
							<div class="dropdown-menu dropdown-menu-end">
								<!-- item-->
								<a href="javascript:void(0);" class="dropdown-item" onclick="rightModal('<?php echo site_url('modal/popup/accountant/edit/' . $user['id']) ?>', '<?php echo get_phrase('update_accountant'); ?>');"><?php echo get_phrase('edit'); ?></a>
								<!-- item-->
								<a href="javascript:void(0);" class="dropdown-item" onclick="confirmModal('<?php echo route('accountant/delete/' . $user['id']); ?>', showAllAccountants )"><?php echo get_phrase('delete'); ?></a>
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