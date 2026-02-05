<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/responsive.min.css">

<style>
/* ========== MODERN DASHBOARD STYLES ========== */
.modern-dashboard {
  /* MONOCHROMATIC THEME (INDIGO) */
  --primary: #6366f1;
  --primary-light: #818cf8;
  --primary-lighter: #e0e7ff;
  --primary-dark: #4338ca;
  --secondary: #10b981; /* Green for success */
  --bg-main: #f8fafc;
  --bg-card: #ffffff;
  --text-dark: #1e293b;
  --text-muted: #64748b;
  --border-color: #e2e8f0;
  --shadow-sm: 0 1px 2px rgba(0,0,0,0.05);
  --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.07), 0 2px 4px -1px rgba(0,0,0,0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.08), 0 4px 6px -2px rgba(0,0,0,0.04);
  
  font-family: 'DM Sans', sans-serif;
  background: var(--bg-main);
  min-height: 100vh;
  padding: 1.5rem;
  margin: -15px -15px 0 -15px;
}

/* Header */
.dash-header {
  margin-bottom: 2rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 1rem;
}

.dash-header h1 {
  font-family: 'Outfit', sans-serif;
  font-size: 1.875rem;
  font-weight: 700;
  color: var(--text-dark);
  margin: 0;
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.dash-header h1 .icon-box {
  width: 48px;
  height: 48px;
  background: linear-gradient(135deg, var(--primary), var(--primary-light));
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.25rem;
  box-shadow: 0 4px 14px rgba(99, 102, 241, 0.4);
}

.dash-header .date-badge {
  background: var(--bg-card);
  padding: 0.625rem 1rem;
  border-radius: 50px;
  font-size: 0.875rem;
  color: var(--text-muted);
  box-shadow: var(--shadow-sm);
  border: 1px solid var(--border-color);
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.dash-header .date-badge i {
  color: var(--primary);
}

/* Modern Card */
.modern-card {
  background: var(--bg-card);
  border-radius: 20px;
  box-shadow: var(--shadow-md);
  border: 1px solid var(--border-color);
  overflow: hidden;
  transition: all 0.3s ease;
  margin-bottom: 1.5rem;
}

.modern-card:hover {
  box-shadow: var(--shadow-lg);
}

.modern-card-header {
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid var(--border-color);
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: linear-gradient(180deg, #fafbfc, transparent);
}

.modern-card-header h3 {
  font-family: 'Outfit', sans-serif;
  font-size: 1.0625rem;
  font-weight: 600;
  color: var(--text-dark);
  margin: 0;
  display: flex;
  align-items: center;
  gap: 0.625rem;
}

.modern-card-header h3 i {
  color: var(--primary);
  font-size: 1.125rem;
}

.modern-card-body {
  padding: 1.5rem;
}

/* Modern Alert */
.modern-alert {
  background: linear-gradient(135deg, var(--primary-lighter), #c7d2fe);
  border: none;
  border-radius: 16px;
  padding: 1rem 1.5rem;
  margin-bottom: 1.5rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  box-shadow: var(--shadow-md);
}

.modern-alert .alert-icon {
  width: 40px;
  height: 40px;
  background: var(--primary);
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  flex-shrink: 0;
}

.modern-alert .alert-content strong {
  color: var(--primary-dark);
  display: block;
  margin-bottom: 0.25rem;
}

.modern-alert .alert-content span {
  color: var(--text-dark);
  font-size: 0.875rem;
  opacity: 0.9;
}

/* Table Modern */
.table-modern thead th {
    border-top: none;
    border-bottom: 1px solid var(--border-color);
    color: var(--text-muted);
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
    padding: 1rem;
    background-color: #f8fafc;
}
.table-modern tbody td {
    padding: 1rem;
    vertical-align: middle;
    color: var(--text-dark);
    border-bottom: 1px solid var(--border-color);
    font-size: 0.9rem;
}
.table-modern tbody tr:last-child td {
    border-bottom: none;
}
.dataTables_wrapper .dataTables_paginate .paginate_button.current, 
.dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
    background: var(--primary) !important;
    color: white !important;
    border-color: var(--primary) !important;
}

/* Utilities */
.dataTables_filter {
    display: none !important;
}
.daterangepicker {
    z-index: 1050;
}
</style>

<div class="modern-dashboard">
    <!-- Header -->
    <div class="dash-header">
        <h1>
            <div class="icon-box"><i class="mdi mdi-video"></i></div>
            <?php echo get_phrase('Recordings'); ?>
        </h1>
        <div class="date-badge">
            <i class="mdi mdi-calendar-today"></i> <?php echo date('d M, Y'); ?>
        </div>
    </div>

    <!-- Info Alert -->
    <div class="modern-alert">
        <div class="alert-icon"><i class="mdi mdi-information-variant"></i></div>
        <div class="alert-content">
            <strong><?php echo get_phrase('Note'); ?></strong>
            <span><?php echo get_phrase("If you can't find your recording, we are currently preparing it, and it will be ready soon."); ?></span>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="modern-card">
        <div class="modern-card-header">
            <h3><i class="mdi mdi-filter-variant"></i> <?php echo get_phrase('Filter Recordings'); ?></h3>
        </div>
        <div class="modern-card-body">
            <form id="filterForm">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="row align-items-end g-3">
                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <label for="meeting_name" class="form-label fw-semibold text-muted"><?php echo get_phrase('Meeting Name'); ?></label>
                        <input type="text" class="form-control" id="meeting_name" name="meeting_name" placeholder="Ex: Réunion pédagogique" value="<?= htmlspecialchars($filters['meeting_name'] ?? '') ?>">
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <label for="date_range" class="form-label fw-semibold text-muted"><?php echo get_phrase('Date Range'); ?></label>
                        <input type="text" class="form-control" id="date_range" name="date_range" placeholder="Select date or range" value="<?= htmlspecialchars($filters['date_range'] ?? '') ?>">
                    </div>
                    <div class="col-lg-4 col-md-12 col-sm-12 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100"><?php echo get_phrase('Apply'); ?></button>
                        <button type="button" id="clearFilters" class="btn btn-outline-secondary w-100"><?php echo get_phrase('Clear'); ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Card -->
    <div class="modern-card">
        <div class="modern-card-header">
            <h3><i class="mdi mdi-history"></i> <?php echo get_phrase('Recording History'); ?></h3>
        </div>
        <div class="modern-card-body">
            <div class="table-responsive">
                <table id="basic-datatable" class="table table-striped dt-responsive nowrap table-modern w-100">
                    <thead>
                        <tr>
                            <th><?php echo get_phrase('Name'); ?></th>
                            <th><?php echo get_phrase('Creation Date'); ?></th>
                            <th><?php echo get_phrase('Duration'); ?></th>
                            <th><?php echo get_phrase('Action'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recordings as $recording): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($recording['name']); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($recording['created_at'])); ?></td>
                                <td><?php echo htmlspecialchars($recording['formatted_duration']); ?></td>
                                <td>
                                     <div class="dropdown text-center">
                                        <button type="button" class="btn btn-sm btn-outline-primary btn-rounded btn-icon" data-bs-toggle="dropdown" aria-expanded="false"><i class="mdi mdi-dots-vertical"></i></button>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a href="<?php echo htmlspecialchars($recording['recording_url']); ?>" target="_blank" class="dropdown-item text-primary">
                                                <?php echo get_phrase('View'); ?>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<script>
function initDataTable() {
    // Vérifiez si la table n'est pas déjà initialisée
    if ($('#basic-datatable').length && !$.fn.DataTable.isDataTable('#basic-datatable')) {
        $('#basic-datatable').DataTable({
            searching: false, // Désactive la barre de recherche
            responsive: true,
            pageLength: 10,
            lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
            language: {
                paginate: {
                    previous: '<i class="mdi mdi-chevron-left"></i>',
                    next: '<i class="mdi mdi-chevron-right"></i>'
                },
                lengthMenu: '<?php echo get_phrase('show'); ?> _MENU_ <?php echo get_phrase('entries'); ?>',
                info: '<?php echo get_phrase('showing'); ?> _START_ <?php echo get_phrase('to'); ?> _END_ <?php echo get_phrase('of'); ?> _TOTAL_ <?php echo get_phrase('entries'); ?>',
                emptyTable: '<?php echo get_phrase("No recordings found"); ?>',
                zeroRecords: '<?php echo get_phrase("No recordings found"); ?>'
            },
            drawCallback: function() {
                $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
            }
        });
        // Marquez la table comme initialisée
        $('#basic-datatable').attr('data-datatable-initialized', 'true');
    }
}

function updateRecordingTable(recordings) {
    // Détruisez l'instance DataTables existante si elle existe
    if ($('#basic-datatable').attr('data-datatable-initialized') === 'true') {
        $('#basic-datatable').DataTable().destroy();
        $('#basic-datatable').removeAttr('data-datatable-initialized');
    }

    var tbody = $('#basic-datatable tbody');
    tbody.empty();
    if (recordings && recordings.length > 0) {
        $.each(recordings, function(index, recording) {
            var row = `
                <tr>
                    <td>${recording.name}</td>
                    <td>${moment(recording.created_at).format('DD/MM/YYYY HH:mm')}</td>
                    <td>${recording.formatted_duration}</td>
                    <td>
                        <div class="dropdown text-center">
                            <button type="button" class="btn btn-sm btn-icon btn-rounded btn-outline-secondary dropdown-btn1 dropdown-btn dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="mdi mdi-dots-vertical"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="${recording.recording_url}" target="_blank" class="dropdown-item text-primary">
                                    <?php echo get_phrase('View'); ?>
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>`;
            tbody.append(row);
        });
    }
    // Réinitialisez DataTables après avoir mis à jour le contenu
    initDataTable();
}

$(document).ready(function() {
    // Initialisez DataTables au chargement de la page
    initDataTable();

    // Initialisation de daterangepicker
    $('#date_range').daterangepicker({
        locale: {
            format: 'DD-MM-YYYY',
            separator: ' - ',
            applyLabel: '<?php echo get_phrase('apply'); ?>',
            cancelLabel: '<?php echo get_phrase('cancel'); ?>',
            fromLabel: '<?php echo get_phrase('from'); ?>',
            toLabel: '<?php echo get_phrase('to'); ?>',
            customRangeLabel: '<?php echo get_phrase('custom'); ?>',
            weekLabel: 'W',
            daysOfWeek: [
                '<?php echo get_phrase('su'); ?>',
                '<?php echo get_phrase('mo'); ?>',
                '<?php echo get_phrase('tu'); ?>',
                '<?php echo get_phrase('we'); ?>',
                '<?php echo get_phrase('th'); ?>',
                '<?php echo get_phrase('fr'); ?>',
                '<?php echo get_phrase('sa'); ?>'
            ],
            monthNames: [
                '<?php echo get_phrase('january'); ?>',
                '<?php echo get_phrase('february'); ?>',
                '<?php echo get_phrase('march'); ?>',
                '<?php echo get_phrase('april'); ?>',
                '<?php echo get_phrase('may'); ?>',
                '<?php echo get_phrase('june'); ?>',
                '<?php echo get_phrase('july'); ?>',
                '<?php echo get_phrase('august'); ?>',
                '<?php echo get_phrase('september'); ?>',
                '<?php echo get_phrase('october'); ?>',
                '<?php echo get_phrase('november'); ?>',
                '<?php echo get_phrase('december'); ?>'
            ],
            firstDay: 1
        },
        autoUpdateInput: false,
        ranges: {
            "<?php echo get_phrase('today'); ?>": [moment(), moment()],
            '<?php echo get_phrase('yesterday'); ?>': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            '<?php echo get_phrase('last_7_days'); ?>': [moment().subtract(6, 'days'), moment()],
            '<?php echo get_phrase('this_month'); ?>': [moment().startOf('month'), moment().endOf('month')],
            '<?php echo get_phrase('last_month'); ?>': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }).on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format('DD-MM-YYYY'));
    }).on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });

    function showNotification(type, message, duration = 3000) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: type,
            title: message,
            showConfirmButton: false,
            timer: duration,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });
    }

    // Apply filters
    $('#filterForm').on('submit', function(event) {
    event.preventDefault();
    var formData = $(this).serialize();
    console.log('Submitting filters:', formData);

    $.ajax({
        url: "<?php echo site_url('student/recording'); ?>",
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            console.log('Student recording response:', JSON.stringify(response, null, 2));
            if (response.status === 'success') {
                $('input[name="<?php echo $this->security->get_csrf_token_name(); ?>"]').val(response.csrf_token);
                updateRecordingTable(response.recordings);
            } else {
                console.error('Invalid response status:', response);
                showNotification('error', "<?php echo get_phrase("Failed to load recordings"); ?>");
            }
        },
        error: function(xhr, status, error) {
            console.error('Filter error:', xhr.responseText, status, error);
            showNotification('error', "<?php echo get_phrase("Failed to load recordings"); ?>");
        }
    });
});

    // Clear filters
    $('#clearFilters').on('click', function(event) {
        event.preventDefault();
        $('#meeting_name').val('');
        $('#date_range').val('');
        $('#date_range').daterangepicker('clear');
        var formData = {
            meeting_name: '',
            date_range: '',
            "<?php echo $this->security->get_csrf_token_name(); ?>": $('input[name="<?php echo $this->security->get_csrf_token_name(); ?>"]').val()
        };

        $.ajax({
            url: "<?php echo site_url('student/recording'); ?>",
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('input[name="<?php echo $this->security->get_csrf_token_name(); ?>"]').val(response.csrf_token);
                     updateRecordingTable(response.recordings);
                }
            },
            error: function(xhr, status, error) {
                console.error('Clear filter error:', xhr, status, error);
            }
        });
    });
});
</script>