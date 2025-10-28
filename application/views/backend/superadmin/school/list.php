<?php
$school_id = school_id();
$check_data = $this->db->get_where('users', array('role' => 'admin'));
if ($check_data->num_rows() > 0): ?>


    <table id="basic-datatable" class="table table-striped dt-responsive nowrap table-modern" width="100%">
        <thead>
            <tr>
                <th><i class="mdi mdi-account-multiple-outline thead-icon"></i><?php echo get_phrase('name'); ?></th>
                <th><i class="mdi mdi-map-marker-outline thead-icon"></i><?php echo get_phrase('address'); ?></th>
                <th><i class="mdi mdi-phone-outline thead-icon"></i><?php echo get_phrase('phone'); ?></th>
                <th><i class="mdi mdi-text-box-outline thead-icon"></i><?php echo get_phrase('description'); ?></th>
                <th><i class="mdi mdi-tag-outline thead-icon"></i><?php echo get_phrase('category'); ?></th>
                <th><?php echo get_phrase('options'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $admins = $this->db->get_where('schools', array('Etat' => 1, 'status' => 1, 'id !=' => 1))->result_array();
            foreach ($admins as $admin) {
            ?>
                <tr>
                    <td><?php echo $admin['name']; ?></td>

                    <td class="modern-td">
                        <span class="desktop-description"
                            data-bs-toggle="tooltip"
                            data-bs-placement="top"
                            title="<?php echo $admin['address']; ?>">
                            <?php echo strlen($admin['address']) > 30 ? substr($admin['address'], 0, 30) . '...' : $admin['address']; ?>
                        </span>
                        <!-- Texte affiché après l'icône œil -->
                        <span class="d-inline d-md-none ms-2">
                            <?php echo strlen($admin['address']) > 13 ? substr($admin['address'], 0, 13) . '...' : $admin['address']; ?>
                        </span>
                        <button type="button" class="btn btn-sm  mobile-description-btn"
                            data-description="<?php echo htmlspecialchars($admin['address']); ?>"
                            onclick="showDescriptionPopup(this)">
                            <i class="mdi mdi-eye-outline"></i>
                        </button>
                    </td>
                    <td><?php echo $admin['phone']; ?></td>

                    <td class="modern-td">

                        <span class="desktop-description"
                            data-bs-toggle="tooltip"
                            data-bs-placement="top"
                            title="<?php echo htmlspecialchars($admin['description']); ?>">
                            <?php echo strlen($admin['description']) > 30 ? substr($admin['description'], 0, 30) . '...' : $admin['description']; ?>
                        </span>
                        <span class="d-inline d-md-none ms-2">
                            <?php echo strlen($admin['description']) > 13 ? substr($admin['description'], 0, 13) . '...' : $admin['description']; ?>
                        </span>

                        <button type="button" class="btn btn-sm mobile-description-btn"
                            data-description="<?php echo htmlspecialchars($admin['description']); ?>"
                            onclick="showDescriptionPopup(this)">
                            <i class="mdi mdi-eye-outline"></i>
                        </button>
                    </td>

                    <td><?php echo $admin['category']; ?></td>

                    <td>
                        <div class="dropdown text-center">
                            <button type="button" class="btn btn-sm btn-icon btn-rounded btn-outline-secondary dropdown-btn1 dropdown-btn dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false"><i class="mdi mdi-dots-vertical"></i></button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item" onclick="rightModal('<?php echo site_url('modal/popup/school/edit/' . $admin['id']); ?>',  &quot;<?php echo get_phrase('update_school'); ?>&quot;)"><?php echo get_phrase('edit'); ?></a>
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item" onclick="confirmModal('<?php echo route('school_crud/delete/' . $admin['id']); ?>', showAllSchools )"><?php echo get_phrase('delete'); ?></a>
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