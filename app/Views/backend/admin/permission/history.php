<!-- Champ cache pour le jeton CSRF -->
<input type="hidden" name="<?= csrf_token(); ?>" value="<?= csrf_hash(); ?>" />

<style>
    :root {
        --exp-primary: #6366f1;
        --exp-primary-light: #eef2ff;
        --exp-success: #059669;
        --exp-dark: #1e293b;
        --exp-gray: #64748b;
        --exp-light: #f8fafc;
        --exp-border: #e2e8f0;
        --exp-danger: #ef4444;
    }

    .exp-header {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        border-radius: 16px;
        padding: 1.5rem 2rem;
        margin-bottom: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        box-shadow: 0 4px 20px rgba(99, 102, 241, 0.3);
    }

    .exp-header-left {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .exp-header-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
    }

    .exp-header-text h4 {
        margin: 0;
        color: white;
        font-size: 1.35rem;
        font-weight: 700;
    }

    .exp-header-text p {
        margin: 0.25rem 0 0;
        color: rgba(255, 255, 255, 0.7);
        font-size: 0.85rem;
    }

    .exp-action-btn {
        padding: 0.65rem 1.1rem;
        border-radius: 10px;
        border: 1px solid rgba(255, 255, 255, 0.4);
        background: rgba(255, 255, 255, 0.1);
        color: #fff;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
        transition: all 0.2s;
    }

    .exp-action-btn:hover {
        color: #fff;
        background: rgba(255, 255, 255, 0.2);
        transform: translateY(-1px);
    }

    .exp-list-container {
        border: 1px solid var(--exp-border);
        border-radius: 12px;
        overflow: hidden;
        background: white;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    }

    .exp-table-header {
        padding: 1rem 1.5rem;
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        border-bottom: 1px solid var(--exp-border);
        color: white;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.85rem;
    }

    .exp-table-wrapper {
        padding: 0.75rem 1rem 1rem;
    }

    #permission_history_table thead th {
        color: var(--exp-gray);
        border-bottom: 1px solid var(--exp-border);
        font-size: 0.82rem;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        font-weight: 700;
    }

    #permission_history_table tbody td {
        vertical-align: middle;
        border-color: var(--exp-border);
        color: var(--exp-dark);
    }

    #permission_history_table tbody tr:hover {
        background: var(--exp-primary-light);
    }

    .exp-loading,
    .exp-empty {
        text-align: center;
        padding: 2rem !important;
        color: var(--exp-gray);
        font-weight: 500;
    }

    .exp-status {
        padding: 0.28rem 0.58rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
    }

    .exp-status-success {
        background: rgba(5, 150, 105, 0.12);
        color: var(--exp-success);
    }

    .exp-status-danger {
        background: rgba(239, 68, 68, 0.12);
        color: var(--exp-danger);
    }
</style>

<!-- Header -->
<div class="row">
    <div class="col-12">
        <div class="exp-header">
            <div class="exp-header-left">
                <div class="exp-header-icon">
                    <i class="mdi mdi-history"></i>
                </div>
                <div class="exp-header-text">
                    <h4><?php echo get_phrase('permission_history'); ?></h4>
                    <p><?php echo get_phrase('manage_teacher_access_rights'); ?></p>
                </div>
            </div>
            <a href="<?php echo route('permission'); ?>" class="exp-action-btn">
                <i class="mdi mdi-arrow-left"></i> <?php echo get_phrase('back_to_permissions'); ?>
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="exp-list-container">
            <div class="exp-table-header">
                <i class="mdi mdi-format-list-bulleted-square"></i>
                <?php echo get_phrase('permission_history'); ?>
            </div>
            <div class="exp-table-wrapper table-responsive">
                <table id="permission_history_table" class="table table-hover dt-responsive w-100 nowrap">
                    <thead>
                        <tr>
                            <th><?php echo get_phrase('date'); ?></th>
                            <th><?php echo get_phrase('permission_type'); ?></th>
                            <th><?php echo get_phrase('old_value'); ?></th>
                            <th><?php echo get_phrase('new_value'); ?></th>
                            <th><?php echo get_phrase('changed_by'); ?></th>
                        </tr>
                    </thead>
                    <tbody id="history_table_body">
                        <tr>
                            <td colspan="5" class="exp-loading">
                                <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                <?php echo get_phrase('loading'); ?>...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        loadPermissionHistory();
        var dtLanguageUrlMap = {
            french: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json',
            spanish: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json',
            german: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/de-DE.json',
            italian: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/it-IT.json',
            portuguese: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-PT.json',
            arabic: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/ar.json',
            turkish: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/tr.json'
        };
        var sessionLanguage = '<?php echo isset($_SESSION['language']) ? strtolower($_SESSION['language']) : 'english'; ?>';
        var dataTableLanguageConfig = { url: '' };
        if (dtLanguageUrlMap[sessionLanguage]) {
            dataTableLanguageConfig.url = dtLanguageUrlMap[sessionLanguage];
        }

        function escapeHtml(value) {
            return $('<div/>').text(value === null || value === undefined ? '' : value).html();
        }

        function getPermissionLabel(columnName) {
            if (columnName === 'marks') {
                return '<?php echo get_phrase('marks'); ?>';
            }
            if (columnName === 'attendance') {
                return '<?php echo get_phrase('attendance'); ?>';
            }
            if (columnName === 'all') {
                return '<?php echo get_phrase('all'); ?>';
            }
            return escapeHtml(columnName);
        }

        function getPermissionBadge(value) {
            if (parseInt(value, 10) === 1) {
                return '<span class="exp-status exp-status-success"><i class="mdi mdi-check-circle-outline"></i><?php echo get_phrase('granted'); ?></span>';
            }
            return '<span class="exp-status exp-status-danger"><i class="mdi mdi-close-circle-outline"></i><?php echo get_phrase('denied'); ?></span>';
        }

        function loadPermissionHistory() {
            var teacher_id = '<?php echo isset($teacher_id) ? $teacher_id : ''; ?>';
            var class_id = '<?php echo isset($class_id) ? $class_id : ''; ?>';
            var csrfName = $('input[name="<?= csrf_token(); ?>"]').attr('name');
            var csrfHash = $('input[name="<?= csrf_token(); ?>"]').val();

            $.ajax({
                url: '<?php echo route('admin/get_permission_history'); ?>',
                type: 'POST',
                data: {
                    teacher_id: teacher_id,
                    class_id: class_id,
                    [csrfName]: csrfHash
                },
                dataType: 'json',
                success: function (response) {
                    if (response.csrfName && response.csrfHash) {
                        $('input[name="' + response.csrfName + '"]').val(response.csrfHash);
                    }

                    if (response.status && response.data && response.data.length > 0) {
                        var html = '';
                        response.data.forEach(function (item) {
                            html += '<tr>';
                            html += '<td>' + escapeHtml(item.created_at) + '</td>';
                            html += '<td>' + getPermissionLabel(item.column_name) + '</td>';
                            html += '<td>' + getPermissionBadge(item.old_value) + '</td>';
                            html += '<td>' + getPermissionBadge(item.new_value) + '</td>';
                            html += '<td>' + escapeHtml(item.changed_by_name) + '</td>';
                            html += '</tr>';
                        });
                        $('#history_table_body').html(html);

                        if ($.fn.DataTable.isDataTable('#permission_history_table')) {
                            $('#permission_history_table').DataTable().destroy();
                        }

                        $('#permission_history_table').DataTable({
                            order: [[0, 'desc']],
                            responsive: true,
                            autoWidth: false,
                            language: dataTableLanguageConfig
                        });
                    } else {
                        $('#history_table_body').html('<tr><td colspan="5" class="exp-empty"><?php echo get_phrase('no_history_found'); ?></td></tr>');
                    }
                },
                error: function () {
                    $('#history_table_body').html('<tr><td colspan="5" class="exp-empty text-danger"><?php echo get_phrase('error_loading_history'); ?></td></tr>');
                }
            });
        }
    });
</script>