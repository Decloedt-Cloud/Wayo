<!-- En-tête et styles -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="row">
  <div class="col-xl-12">
    <div class="card">
      <div class="card-body py-2">
        <h4 class="page-title d-inline-block">
          <i class="mdi mdi-video" style="font-size: 25px;"></i> <?php echo get_phrase('Recordings'); ?>
        </h4>
      </div>
    </div>
  </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
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
</div>

<h4 class="fw-bold mb-3"><i class="mdi mdi-history" style="font-size: 25px;"></i> <?php echo get_phrase("History") ?> </h4>
<div class="alert alert-info text-center">
    <?php echo get_phrase("If you can't find your recording, we are currently preparing it, and it will be ready soon."); ?>
</div>
<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th><?php echo get_phrase('Name'); ?></th>
                        <th><?php echo get_phrase('Class'); ?></th>
                        <th><?php echo get_phrase('Creation Date'); ?></th>
                        <th><?php echo get_phrase('Duration'); ?></th>
                        <th><?php echo get_phrase('Action'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recordings as $recording): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($recording['name']); ?></td>
                            <td><?php echo htmlspecialchars($recording['class_name'] ?? 'N/A'); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($recording['created_at'])); ?></td>
                            <td><?php echo htmlspecialchars($recording['formatted_duration']); ?></td>
                            <td>
                                <a href="<?php echo htmlspecialchars($recording['recording_url']); ?>" target="_blank" class="btn btn-sm btn-primary">
                                    <i class="mdi mdi-play"></i> <?php echo get_phrase('View'); ?>
                                </a>
                                <button type="button" class="btn btn-sm btn-danger delete-recording" data-recording-id="<?php echo htmlspecialchars($recording['recording_id']); ?>">
                                    <i class="mdi mdi-trash-can"></i> <?php echo get_phrase('Delete'); ?>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($recordings)): ?>
                        <tr>
                            <td colspan="5" class="text-center"><?php echo get_phrase('No recordings found'); ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize daterangepicker
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

    // Show notification using SweetAlert2
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
            url: "<?php echo site_url('admin/recording'); ?>",
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Update CSRF token
                    $('input[name="<?php echo $this->security->get_csrf_token_name(); ?>"]').val(response.csrf_token);

                    // Update table
                    var tbody = $('table.table tbody');
                    tbody.empty();
                    if (response.recordings.length === 0) {
                        tbody.append('<tr><td colspan="5" class="text-center"><?php echo get_phrase("No recordings found"); ?></td></tr>');
                    } else {
                        $.each(response.recordings, function(index, recording) {
                            var row = `
                                <tr>
                                    <td>${recording.name}</td>
                                    <td>${recording.class_name || 'N/A'}</td>
                                    <td>${moment(recording.created_at).format('DD/MM/YYYY HH:mm')}</td>
                                    <td>${recording.formatted_duration}</td>
                                    <td>
                                        <a href="${recording.recording_url}" target="_blank" class="btn btn-sm btn-primary">
                                            <i class="mdi mdi-play"></i> <?php echo get_phrase('View'); ?>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger delete-recording" data-recording-id="${recording.recording_id}">
                                            <i class="mdi mdi-trash-can"></i> <?php echo get_phrase('Delete'); ?>
                                        </button>
                                    </td>
                                </tr>`;
                            tbody.append(row);
                        });
                    }
                    showNotification('success', '<?php echo get_phrase("Filters applied successfully"); ?>');
                } else {
                    showNotification('error', '<?php echo get_phrase("Failed to apply filters"); ?>: ' + (response.message || 'Unknown error'));
                }
            },
            error: function(xhr, status, error) {
                console.error('Filter error:', xhr, status, error);
                showNotification('error', '<?php echo get_phrase("Failed to apply filters"); ?>');
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
            url: "<?php echo site_url('admin/recording'); ?>",
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('input[name="<?php echo $this->security->get_csrf_token_name(); ?>"]').val(response.csrf_token);
                    var tbody = $('table.table tbody');
                    tbody.empty();
                    if (response.recordings.length === 0) {
                        tbody.append('<tr><td colspan="5" class="text-center"><?php echo get_phrase("No recordings found"); ?></td></tr>');
                    } else {
                        $.each(response.recordings, function(index, recording) {
                            var row = `
                                <tr>
                                    <td>${recording.name}</td>
                                    <td>${recording.class_name || 'N/A'}</td>
                                    <td>${moment(recording.created_at).format('DD/MM/YYYY HH:mm')}</td>
                                    <td>${recording.formatted_duration}</td>
                                    <td>
                                        <a href="${recording.recording_url}" target="_blank" class="btn btn-sm btn-primary">
                                            <i class="mdi mdi-play"></i> <?php echo get_phrase('View'); ?>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger delete-recording" data-recording-id="${recording.recording_id}">
                                            <i class="mdi mdi-trash-can"></i> <?php echo get_phrase('Delete'); ?>
                                        </button>
                                    </td>
                                </tr>`;
                            tbody.append(row);
                        });
                    }
                } else {
                    showNotification('error', '<?php echo get_phrase("Failed to clear filters"); ?>: ' + (response.message || 'Unknown error'));
                }
            },
            error: function(xhr, status, error) {
                console.error('Clear filter error:', xhr, status, error);
                showNotification('error', '<?php echo get_phrase("Failed to clear filters"); ?>');
            }
        });
    });

    // Handle deletion
    $(document).on('click', 'button.delete-recording', function(event) {
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
                    url: "<?php echo site_url('admin/delete_recording'); ?>",
                    type: 'POST',
                    data: {
                        recording_id: recordingId,
                        skip_sync: 1,
                        "<?php echo $this->security->get_csrf_token_name(); ?>": $('input[name="<?php echo $this->security->get_csrf_token_name(); ?>"]').val()
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            showNotification('success', '<?php echo get_phrase("Recording deleted successfully"); ?>');
                            $('button.delete-recording[data-recording-id="' + recordingId + '"]').closest('tr').remove();
                            if (response.csrf_token) {
                                $('input[name="<?php echo $this->security->get_csrf_token_name(); ?>"]').val(response.csrf_token);
                            }
                        } else {
                            showNotification('error', '<?php echo get_phrase("Error"); ?>: ' + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Delete error:', xhr, status, error);
                        showNotification('error', '<?php echo get_phrase("Failed to delete recording"); ?>');
                    }
                });
            }
        });
    });
});
</script>
<style>
    .daterangepicker {
        z-index: 1050;
    }
</style>