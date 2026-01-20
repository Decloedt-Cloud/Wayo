<?php
$school_id = school_id();
$check_data = $this->db->get_where('teachers', array('school_id' => $school_id));
?>

<style>
    .modern-table-wrapper {
        background: white;
        border-radius: 16px;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
        padding: 1.5rem;
        overflow-x: hidden;
    }
    
    .modern-table {
        width: 100% !important;
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }
    .modern-table thead th {
        background: var(--bg-main);
        color: var(--text-muted);
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        padding: 1rem;
        border-bottom: 1px solid var(--border-color);
        white-space: nowrap;
        border-top: none !important;
    }
    .modern-table tbody td {
        padding: 0.75rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid var(--border-color);
        color: var(--text-dark);
        font-weight: 500;
        font-size: 0.9rem;
        border-top: none !important;
    }
    .modern-table tbody tr:last-child td {
        border-bottom: none;
    }
    .modern-table tbody tr:hover td {
        background-color: var(--bg-main);
    }

    /* Modern Button */
    .modern-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.4rem 0.8rem;
        border-radius: 8px;
        font-weight: 500;
        font-size: 0.85rem;
        border: none;
        transition: all 0.2s ease;
        cursor: pointer;
        background: var(--primary);
        color: white;
        box-shadow: 0 2px 4px rgba(99, 102, 241, 0.2);
    }
    .modern-btn:hover {
        background: var(--primary-dark);
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(99, 102, 241, 0.3);
        color: white;
    }

    /* CUSTOM DATATABLES STYLING */
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
        outline: none;
        transition: all 0.2s;
        color: var(--text-dark);
    }
    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-lighter);
    }
    
    .dataTables_wrapper .dataTables_length select {
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 0.4rem 2rem 0.4rem 0.8rem;
        font-size: 0.9rem;
        color: var(--text-dark);
        outline: none;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        border-radius: 8px !important;
        border: 1px solid transparent !important;
        color: var(--text-dark) !important;
        font-size: 0.85rem;
        padding: 0.4rem 0.8rem !important;
        margin: 0 2px;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: var(--bg-main) !important;
        color: var(--primary) !important;
        border: 1px solid var(--border-color) !important;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: var(--primary) !important;
        color: white !important;
        border: 1px solid var(--primary) !important;
        box-shadow: 0 2px 4px rgba(99, 102, 241, 0.3);
    }
    
    .dataTables_wrapper .dataTables_info {
        color: var(--text-muted) !important;
        font-size: 0.85rem;
        padding-top: 1rem !important;
    }

    /* Popup Styling */
    .description-popup-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        display: none;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .description-popup-overlay.is-visible {
        display: block;
        opacity: 1;
    }
    .description-popup {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(0.9);
        background: white;
        width: 90%;
        max-width: 500px;
        border-radius: 16px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        z-index: 1001;
        display: none;
        opacity: 0;
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        overflow: hidden;
    }
    .description-popup.is-visible {
        display: block;
        opacity: 1;
        transform: translate(-50%, -50%) scale(1);
    }
    .description-popup-header {
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
        color: white;
        padding: 1rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .description-popup-header h4 {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 600;
        color: white;
        font-family: 'Outfit', sans-serif;
    }
    .description-popup-close {
        background: none;
        border: none;
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
        padding: 0;
        line-height: 1;
        opacity: 0.8;
        transition: opacity 0.2s;
    }
    .description-popup-close:hover {
        opacity: 1;
    }
    .description-popup-content {
        padding: 1.5rem;
        color: var(--text-dark);
        font-size: 1rem;
        line-height: 1.6;
        max-height: 60vh;
        overflow-y: auto;
    }
</style>

<div class="modern-table-wrapper">
    <?php if($check_data->num_rows() > 0):?>
    <table id="basic-datatable" class="table modern-table table-responsive-sm">
        <thead>
            <tr>
                <th><i class="mdi mdi-account-multiple-outline thead-icon me-2"></i><?php echo get_phrase('name'); ?></th>
                <th><i class="mdi mdi-text-box-outline thead-icon me-2"></i><?php echo get_phrase('designation'); ?></th>
                <th class="text-center"><i class="mdi mdi-eye-outline thead-icon me-2"></i><?php echo get_phrase('details'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $teachers = $this->db->get_where('teachers', array('school_id' => $school_id))->result_array();
            foreach($teachers as $teacher){
                ?>
                <tr>
                    <td><?php echo $this->db->get_where('users', array('id' => $teacher['user_id']))->row('name'); ?></td>
                    <td>
                        <span class="d-none d-md-inline"
                            data-bs-toggle="tooltip"
                            data-bs-placement="top"
                            title="<?php echo htmlspecialchars($teacher['designation']); ?>">
                            <?php echo strlen($teacher['designation']) > 50 ? substr($teacher['designation'], 0, 50) . '...' : $teacher['designation']; ?>
                        </span>
                        <span class="d-inline d-md-none">
                            <?php echo strlen($teacher['designation']) > 20 ? substr($teacher['designation'], 0, 20) . '...' : $teacher['designation']; ?>
                        </span>
                    </td>
                    <td class="text-center">
                        <button type="button" class="modern-btn btn-sm"
                            data-description="<?php echo htmlspecialchars($teacher['designation']); ?>"
                            onclick="showDescriptionPopup(this)">
                            <i class="mdi mdi-eye-outline"></i>
                        </button>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <?php else: ?>
        <div class="empty-state">
            <img src="<?php echo base_url('assets/backend/images/empty_box.png'); ?>" alt="No Data">
            <h3><?php echo get_phrase('no_teachers_found'); ?></h3>
            <p><?php echo get_phrase('there_are_no_teachers_listed_yet'); ?></p>
        </div>
    <?php endif; ?>
</div>

<!-- Popup Structure -->
<div id="description-popup-overlay" class="description-popup-overlay"></div>
<div id="description-popup" class="description-popup">
    <div class="description-popup-header">
        <h4><?php echo get_phrase('designation_details'); ?></h4>
        <button onclick="hideDescriptionPopup()" class="description-popup-close">&times;</button>
    </div>
    <div id="description-popup-content" class="description-popup-content">
        <!-- Content injected via JS -->
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

  // Fermer la popup en cliquant sur le fond noir
  overlay.addEventListener('click', hideDescriptionPopup);
</script>