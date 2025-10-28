<?php
$school_id = school_id();
$check_data = $this->db->get_where('teachers', array('school_id' => $school_id));
if ($check_data->num_rows() > 0): ?>

    <table id="basic-datatable" class="table table-striped dt-responsive nowrap table-modern" width="100%">
        <thead>
            <tr>
                <th><i class="mdi mdi-account-circle-outline thead-icon"></i><?php echo get_phrase('photo'); ?></th>
                <th><i class="mdi mdi-account-multiple-outline thead-icon"></i><?php echo get_phrase('name'); ?></th>
                <th><i class="mdi mdi-text-box-outline thead-icon"></i><?php echo get_phrase('designation'); ?></th>
                <th><?php echo get_phrase('options'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $teachers = $this->db->get_where('teachers', array('school_id' => $school_id))->result_array();
            foreach ($teachers as $teacher) {
            ?>
                <tr>
                    <td><img class="rounded-circle" width="50" height="50" src="<?php echo $this->user_model->get_user_image($teacher['user_id']); ?>"></td>
                    <td><?php echo $this->db->get_where('users', array('id' => $teacher['user_id']))->row('name'); ?></td>
                    <td class="modern-td">
                        <span class="desktop-description"
                            data-bs-toggle="tooltip"
                            data-bs-placement="top"
                            title="<?php echo htmlspecialchars($teacher['designation']); ?>">
                            <?php echo strlen($teacher['designation']) > 30 ? substr($teacher['designation'], 0, 30) . '...' : $teacher['designation']; ?>
                        </span>
                        <span class="d-inline d-md-none ms-2">
                            <?php echo strlen($teacher['designation']) > 13 ? substr($teacher['designation'], 0, 13) . '...' : $teacher['designation']; ?>
                        </span>

                        <button type="button" class="btn btn-sm mobile-description-btn"
                            data-description="<?php echo htmlspecialchars($teacher['designation']); ?>"
                            onclick="showDescriptionPopup(this)">
                            <i class="mdi mdi-eye-outline"></i>
                        </button>
                    </td>
                    <td>
                        <div class="dropdown text-center">
                            <button type="button" class="btn btn-sm btn-icon btn-rounded btn-outline-secondary dropdown-btn1 dropdown-btn dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false"><i class="mdi mdi-dots-vertical"></i></button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="javascript:void(0);" class="dropdown-item" onclick="largeModal('<?php echo site_url('modal/popup/teacher/profile/'.$teacher['id'])?>', '<?php echo $this->db->get_where('schools', array('id' => $school_id))->row('name'); ?>')"><?php echo get_phrase('profile'); ?></a>
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item" onclick="rightModal('<?php echo site_url('modal/popup/teacher/permission_overview/' . $teacher['id'] . '/' . $teacher['user_id']); ?>', '<?php echo get_phrase('assigned_permissions'); ?>')"><?php echo get_phrase('permissions'); ?></a>
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item" onclick="rightModal('<?php echo site_url('modal/popup/teacher/edit/' . $teacher['user_id']); ?>', &quot;<?php echo get_phrase('update_teacher'); ?>&quot;)"><?php echo get_phrase('edit'); ?></a>
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item" onclick="confirmModal('<?php echo route('teacher/delete/' . $teacher['user_id']); ?>', showAllTeachers )"><?php echo get_phrase('delete'); ?></a>
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