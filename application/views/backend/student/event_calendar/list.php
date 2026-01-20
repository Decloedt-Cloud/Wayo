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

<style>
    .attendance-summary {
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
        color: white;
        border-radius: 16px;
        padding: 2rem;
        text-align: center;
        margin-bottom: 2rem;
        box-shadow: var(--shadow-md);
        position: relative;
        overflow: hidden;
    }
    
    .attendance-summary::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 150px;
        height: 150px;
        background: white;
        opacity: 0.1;
        border-radius: 50%;
        transform: translate(30%, -30%);
    }

    .attendance-summary h4 {
        color: white;
        font-weight: 700;
        margin-bottom: 0.5rem;
        font-family: 'Outfit', sans-serif;
        position: relative;
        z-index: 1;
    }
    .attendance-summary h5 {
        color: rgba(255,255,255,0.9);
        font-size: 1rem;
        margin-bottom: 0.5rem;
        position: relative;
        z-index: 1;
    }
    
    .modern-table-wrapper {
        background: white;
        border-radius: 16px;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
        padding: 1.5rem; /* Added padding for Datatable controls */
        overflow-x: hidden; /* Prevent double scrollbar with datatables */
    }
    
    .modern-table {
        width: 100% !important; /* Force full width for datatable */
        margin-bottom: 0;
        border-collapse: separate; /* For border-radius on rows if needed */
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
        border-top: none !important; /* Remove datatables top border */
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
    
    .status-icon {
        font-size: 0.8rem;
    }

    /* CUSTOM DATATABLES STYLING FOR MODERN DESIGN */
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
    
    .dataTables_wrapper .dataTables_paginate {
        padding-top: 1rem !important;
    }

</style>

<div class="modern-table-wrapper">
    <?php if($announcements > 0): ?>
        <table id="basic-datatable" class="table modern-table table-responsive-sm">
            <thead>
                <tr>
                    <th><i class="mdi mdi-calendar-range-outline me-2 thead-icon"></i><?php echo get_phrase('event_title'); ?></th>
                    <th><i class="mdi mdi-calendar-start thead-icon"></i><?php echo get_phrase('from'); ?></th>
                    <th><i class="mdi mdi-calendar-end thead-icon"></i><?php echo get_phrase('to'); ?></th>
                    <th class="text-center"><i class="mdi mdi-eye-outline thead-icon"></i><?php echo get_phrase('details'); ?></th>
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
                        <td>
                            <span class="d-none d-md-inline"
                                data-bs-toggle="tooltip"
                                data-bs-placement="top"
                                title="<?php echo htmlspecialchars($event_calendar['title']); ?>">
                                <?php echo strlen($event_calendar['title']) > 50 ? substr($event_calendar['title'], 0, 50) . '...' : $event_calendar['title']; ?>
                            </span>
                            <span class="d-inline d-md-none">
                                <?php echo strlen($event_calendar['title']) > 20 ? substr($event_calendar['title'], 0, 20) . '...' : $event_calendar['title']; ?>
                            </span>
                        </td>
                        <td><?php echo date('D, d M Y', strtotime($event_calendar['starting_date'])); ?></td>
                        <td><?php echo date('D, d M Y', strtotime($event_calendar['ending_date'])); ?></td>
                        <td class="text-center">
                            <button type="button" class="modern-btn btn-sm" style="padding: 0.2rem 0.5rem;"
                                data-description="<?php echo htmlspecialchars($event_calendar['title']); ?>"
                                onclick="showDescriptionPopup(this)">
                                <i class="mdi mdi-eye-outline"></i>
                            </button>
                        </td>
                    </tr>
                <?php } ?>
                <?php } ?>
                <?php }  ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="empty-state">
            <img src="<?php echo base_url('assets/backend/images/empty_box.png'); ?>" alt="No Events">
            <h3><?php echo get_phrase('no_events_found'); ?></h3>
            <p><?php echo get_phrase('there_are_no_events_scheduled_at_the_moment'); ?></p>
        </div>
    <?php endif; ?>
</div>

<!-- Popup Structure -->
<div id="description-popup-overlay" class="description-popup-overlay"></div>
<div id="description-popup" class="description-popup">
    <div class="description-popup-header">
        <h4><?php echo get_phrase('event_details'); ?></h4>
        <button onclick="hideDescriptionPopup()" class="description-popup-close">&times;</button>
    </div>
    <div id="description-popup-content" class="description-popup-content">
        <!-- Content injected via JS -->
    </div>
</div>

<style>
/* Reusing Popup Styles but refined if needed, or keeping them if they are global or sufficient */
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