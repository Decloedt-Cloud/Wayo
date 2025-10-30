<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/responsive.css">
<style>
   .alert-modern .icon {
      background-color: #6c757d;
      color: #fff;
      width: 30px;
      height: 30px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      font-size: 1.1rem;
      flex-shrink: 0;
      cursor: pointer;
      margin-left: 10px;
   }
.alert-modern {
      padding: 0;
   }
 .alert-modern:hover .icon {
      background-color: #5a6268; /* Légère variation au survol */
   }

   /* Popover styling */
   .popover {
      --bs-popover-bg: #fff;
      --bs-popover-border-color: #6c757d;
      --bs-popover-header-bg: #f8f9fa;
      --bs-popover-header-color: #495057;
      --bs-popover-body-padding-x: 1rem;
      --bs-popover-body-padding-y: 0.75rem;
   }

   [dir="rtl"] .space-between-icon {
      margin-right: 10px !important;
   }

   /* Ensure popover shows on click for mobile */
   @media (max-width: 850.98px) {
      .alert-modern .icon {
         pointer-events: auto;
         margin-left: 10px;
      }
      .d-flex.align-items-center.justify-content-between {
            width: 15% !important;
        }
   }
   .d-flex.align-items-center.justify-content-between {
      width: 10%;
   }

   .page-title {
      margin: 0;
      display: flex;
      align-items: center;
   }

   .page-title, .fw-bold {
    white-space: nowrap;
}

 .dataTables_filter {
        display: none !important;
    }
    .daterangepicker {
        z-index: 1050;
    }
</style>

<div class="col-xl-12">
   <div class="header-card">
      <div class="card-body">
         <h4 class="page-title d-inline-block">
            <i class="fas fa-save fa-fw"></i> <?php echo get_phrase('Recordings'); ?>
         </h4>
      </div> <!-- end card body-->
   </div> <!-- end card -->
</div><!-- end col-->


<div class="col-xl-12">
    <div class="header-card">
        <div class="card-body">
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
</div>

<h4 class="fw-bold mb-3 d-flex align-items-center justify-content-between" style="flex-wrap: nowrap">
    <span>
        <i class="mdi mdi-history" style="font-size: 25px;"></i> <?php echo get_phrase("History") ?>
    </span>
    <div class="alert-modern d-flex align-items-center">
        <div class="icon flex-shrink-0"
             data-bs-toggle="popover"
             data-bs-trigger="hover focus"
             data-bs-content="<?php echo get_phrase("If you can't find your recording, we are currently preparing it, and it will be ready soon.") ?>"
             data-bs-placement="top">
            <i class="dripicons-information"></i>
        </div>
    </div>
</h4>
<div class="row">
<div class="col-12">
    <div class="mb-3">
        <div class="main-card">
            <div class="card-body">
        <div class="table-responsive">
             <table id="basic-datatable" class="table table-striped dt-responsive nowrap table-modern" width="100%">
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
                                            <a href="#" class="dropdown-item text-danger delete-recording" data-recording-id="<?php echo htmlspecialchars($recording['recording_id']); ?>">
                                                <?php echo get_phrase('Delete'); ?>
                                            </a>
                                        </div>
									</div>
								</td>
                            </tr>
                        <?php endforeach; ?>
                            <?php if (empty($recordings)): ?>
                                <tr>
                                <td colspan="5" class="text-center"><?php echo get_phrase("No recordings found"); ?></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<script src="<?php echo base_url(); ?>assets/backend/js/sweetalert.js"></script>
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
                info: '<?php echo get_phrase('showing'); ?> _START_ <?php echo get_phrase('to'); ?> _END_ <?php echo get_phrase('of'); ?> _TOTAL_ <?php echo get_phrase('entries'); ?>'
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
    if (recordings && recordings.length === 0) {
        tbody.append('<tr><td colspan="5" class="text-center"><?php echo get_phrase("No recordings found"); ?></td></tr>');
    } else {
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
                                <a type="button" class="dropdown-item text-danger delete-recording" data-recording-id="${recording.recording_id}">
                                    <?php echo get_phrase('Delete'); ?>
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
            url: "<?php echo site_url('superadmin/recording'); ?>",
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Update CSRF token
                    $('input[name="<?php echo $this->security->get_csrf_token_name(); ?>"]').val(response.csrf_token);

                    // Mettre à jour la table
                    updateRecordingTable(response.recordings);
                }
            },
            error: function(xhr, status, error) {
                console.error('Filter error:', xhr, status, error);
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
            url: "<?php echo site_url('superadmin/recording'); ?>",
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

    // Handle deletion
    $(document).on('click', 'a.delete-recording', function(event) {
        event.preventDefault();
        var recordingId = $(this).data('recording-id');

        Swal.fire({
        title: '<?php echo get_phrase("Are you sure?"); ?>',
        text: '<?php echo get_phrase("Are you sure you want to delete this recording?"); ?>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: '<?php echo get_phrase("Delete"); ?>',
        cancelButtonText: '<?php echo get_phrase("Cancel"); ?>'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "<?php echo site_url('superadmin/delete_recording'); ?>",
                type: 'POST',
                data: {
                    recording_id: recordingId,
                    skip_sync: 1,
                    "<?php echo $this->security->get_csrf_token_name(); ?>": $('input[name="<?php echo $this->security->get_csrf_token_name(); ?>"]').val()
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        showNotification('success', "<?php echo get_phrase("Recording deleted successfully"); ?>");
                        $('a.delete-recording[data-recording-id="' + recordingId + '"]').closest('tr').remove();
                        if (response.csrf_token) {
                            $('input[name="<?php echo $this->security->get_csrf_token_name(); ?>"]').val(response.csrf_token);
                        }
                    } else {
                        showNotification('error', '<?php echo get_phrase("Error"); ?>: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Delete error:', xhr, status, error);
                    showNotification('error', "<?php echo get_phrase("Failed to delete recording"); ?>");
                }
            });
        }
    });
});
});
</script>