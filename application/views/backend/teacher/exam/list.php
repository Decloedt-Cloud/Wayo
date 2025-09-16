<?php
$school_id = school_id();
$exams = $this->db->get_where('exams', array('school_id' => $school_id, 'session' => active_session()))->result_array();

if (count($exams) > 0): ?>
    <table id="basic-datatable" class="table table-striped dt-responsive nowrap table-modern" width="100%">
        <thead>
            <tr>
                <th><i class="mdi mdi-account-multiple-outline thead-icon"></i><?php echo get_phrase('exam_name'); ?></th>
                <th><i class="mdi mdi-calendar-start thead-icon"></i><?php echo get_phrase('starting_date'); ?></th>
                <th><i class="mdi mdi-calendar-end thead-icon"></i><?php echo get_phrase('ending_date'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($exams as $exam): ?>
                <tr>
                    <td class="modern-td">

                        <span class="desktop-description"
                            data-bs-toggle="tooltip"
                            data-bs-placement="top"
                            title="<?php echo htmlspecialchars($exam['name']); ?>">
                            <?php echo strlen($exam['name']) > 30 ? substr($exam['name'], 0, 30) . '...' :  $exam['name']; ?>
                        </span>
                        <span class="d-inline d-md-none ms-2">
                            <?php echo strlen($exam['name']) > 13 ? substr($exam['name'], 0, 13) . '...' :  $exam['name']; ?>
                        </span>

                        <button type="button" class="btn btn-sm mobile-description-btn"
                            data-description="<?php echo htmlspecialchars($exam['name']); ?>"
                            onclick="showDescriptionPopup(this)">
                            <i class="mdi mdi-eye-outline"></i>
                        </button>
                    </td>
                    <td><?php echo date('D, d-M-Y', $exam['starting_date']); ?></td>
                    <td><?php echo date('D, d-M-Y', $exam['ending_date']); ?></td>
                </tr>
            <?php endforeach; ?>
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