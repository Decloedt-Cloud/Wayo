<?php
$school_id = school_id(); $addons = db()->table('addons')->get()->getResultArray();
if (count($addons) > 0): ?>
<table id="basic-datatable" class="table table-striped dt-responsive nowrap table-modern" width="100%">
    <thead>
        <tr>
            <th><i class="mdi mdi-account-multiple-outline thead-icon"></i><?php echo get_phrase('name'); ?></th>
            <th><i class="mdi mdi-checkbox-marked-circle-outline thead-icon"></i><?php echo get_phrase('status'); ?></th>
            <th><i class="mdi mdi-information-outline thead-icon"></i><?php echo get_phrase('version'); ?></th>
            <th><?php echo get_phrase('options'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($addons as $addon): ?>
            <tr>
                   <td class="modern-td">

                        <span class="desktop-description"
                            data-bs-toggle="tooltip"
                            data-bs-placement="top"
                            title="<?php echo htmlspecialchars($addon['name']); ?>">
                            <?php echo strlen($addon['name']) > 30 ? substr($addon['name'], 0, 30) . '...' : $addon['name']; ?>
                        </span>
                        <span class="d-inline d-md-none ms-2">
                            <?php echo strlen($addon['name']) > 13 ? substr($addon['name'], 0, 13) . '...' : $addon['name']; ?>
                        </span>

                        <button type="button" class="btn btn-sm mobile-description-btn"
                            data-description="<?php echo htmlspecialchars($addon['name']); ?>"
                            onclick="showDescriptionPopup(this)">
                            <i class="mdi mdi-eye-outline"></i>
                        </button>
                    </td>
                
                <td>
                    <?php if ($addon['status']): ?>
                        <span class="badge bg-success"><?php echo get_phrase('active'); ?></span>
                    <?php else: ?>
                        <span class="badge bg-danger"><?php echo get_phrase('deactivated'); ?></span>
                    <?php endif; ?>
                </td>
                <td><?php echo $addon['version']; ?></td>
                <td>
                    <div class="dropdown text-center">
                        <button type="button" class="btn btn-sm btn-icon btn-rounded btn-outline-secondary dropdown-btn1 dropdown-btn dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false"><i class="mdi mdi-dots-vertical"></i></button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <?php if ($addon['status']): ?>
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item" onclick="confirmModal('<?php echo route('addon_manager/deactive/'.$addon['id']); ?>', showAllAddons)"><?php echo get_phrase('deactive'); ?></a>
                            <?php else: ?>
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item" onclick="confirmModal('<?php echo route('addon_manager/activate/'.$addon['id']); ?>', showAllAddons)"><?php echo get_phrase('activate'); ?></a>
                            <?php endif; ?>

                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item" onclick="confirmModal('<?php echo route('addon_manager/delete/'.$addon['id']); ?>', showAllAddons)"><?php echo get_phrase('remove'); ?></a>
                        </div>
                    </div>
                </td>
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
    <?php include APPPATH.'Views/backend/empty.php'; ?>
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