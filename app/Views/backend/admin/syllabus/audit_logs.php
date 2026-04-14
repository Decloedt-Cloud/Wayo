<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo get_phrase('syllabus_audit_logs'); ?></title>
    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #4338ca;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
            --dark: #1e293b;
            --gray: #64748b;
            --light: #f1f5f9;
            --white: #ffffff;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: var(--light);
            margin: 0;
            padding: 20px;
        }

        .audit-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .audit-header {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: var(--white);
            padding: 2rem;
            border-radius: 16px;
            margin-bottom: 1.5rem;
            box-shadow: 0 10px 40px rgba(79, 70, 229, 0.3);
        }

        .audit-header h1 {
            margin: 0 0 0.5rem 0;
            font-size: 1.75rem;
            font-weight: 700;
        }

        .audit-header p {
            margin: 0;
            opacity: 0.9;
            font-size: 0.95rem;
        }

        .audit-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background: var(--white);
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        }

        .stat-label {
            font-size: 0.875rem;
            color: var(--gray);
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark);
        }

        .stat-card.create .stat-value {
            color: var(--success);
        }

        .stat-card.delete .stat-value {
            color: var(--danger);
        }

        .stat-card.view .stat-value {
            color: var(--info);
        }

        .stat-card.download .stat-value {
            color: var(--warning);
        }

        .audit-table {
            background: var(--white);
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .audit-table-header {
            background: var(--dark);
            color: var(--white);
            padding: 1rem 1.5rem;
            font-weight: 600;
        }

        .audit-table-wrapper {
            overflow-x: auto;
        }

        .audit-table table {
            width: 100%;
            border-collapse: collapse;
        }

        .audit-table th,
        .audit-table td {
            padding: 1rem 1.5rem;
            text-align: left;
            border-bottom: 1px solid var(--light);
        }

        .audit-table th {
            background: var(--light);
            font-weight: 600;
            color: var(--dark);
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .audit-table tr:hover {
            background: rgba(79, 70, 229, 0.03);
        }

        .action-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .action-badge.create {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
        }

        .action-badge.delete {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
        }

        .action-badge.view {
            background: rgba(59, 130, 246, 0.1);
            color: var(--info);
        }

        .action-badge.download {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning);
        }

        .user-type-badge {
            display: inline-block;
            padding: 0.125rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 500;
            background: var(--light);
            color: var(--gray);
        }

        .timestamp {
            color: var(--gray);
            font-size: 0.875rem;
        }

        .details-toggle {
            color: var(--primary);
            cursor: pointer;
            font-size: 0.875rem;
            font-weight: 500;
            transition: color 0.2s;
        }

        .details-toggle:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        .details-content {
            display: none;
            margin-top: 0.5rem;
            padding: 1rem;
            background: var(--light);
            border-radius: 8px;
            font-size: 0.875rem;
        }

        .details-content.active {
            display: block;
        }

        .detail-item {
            margin-bottom: 0.5rem;
        }

        .detail-label {
            font-weight: 600;
            color: var(--dark);
        }

        .detail-value {
            color: var(--gray);
        }

        .no-logs {
            text-align: center;
            padding: 3rem;
            color: var(--gray);
        }

        .no-logs i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: var(--white);
            color: var(--dark);
            border: 2px solid var(--light);
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            margin-bottom: 1rem;
        }

        .back-button:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        @media (max-width: 768px) {
            .audit-stats {
                grid-template-columns: 1fr;
            }

            .audit-table th,
            .audit-table td {
                padding: 0.75rem;
            }
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css" rel="stylesheet">
</head>
<body>
    <div class="audit-container">
        <a href="<?php echo site_url('admin/syllabus'); ?>" class="back-button">
            <i class="mdi mdi-arrow-left"></i>
            <?php echo get_phrase('back_to_syllabus'); ?>
        </a>

        <div class="audit-header">
            <h1><?php echo get_phrase('syllabus_audit_logs'); ?></h1>
            <p><?php echo get_phrase('track_all_syllabus_related_activities'); ?></p>
        </div>

        <?php if (isset($stats) && !empty($stats)): ?>
            <div class="audit-stats">
                <div class="stat-card create">
                    <div class="stat-label"><?php echo get_phrase('total_created'); ?></div>
                    <div class="stat-value"><?php echo $stats['create'] ?? 0; ?></div>
                </div>
                <div class="stat-card delete">
                    <div class="stat-label"><?php echo get_phrase('total_deleted'); ?></div>
                    <div class="stat-value"><?php echo $stats['delete'] ?? 0; ?></div>
                </div>
                <div class="stat-card view">
                    <div class="stat-label"><?php echo get_phrase('total_viewed'); ?></div>
                    <div class="stat-value"><?php echo $stats['view'] ?? 0; ?></div>
                </div>
                <div class="stat-card download">
                    <div class="stat-label"><?php echo get_phrase('total_downloaded'); ?></div>
                    <div class="stat-value"><?php echo $stats['download'] ?? 0; ?></div>
                </div>
            </div>
        <?php endif; ?>

        <div class="audit-table">
            <div class="audit-table-header">
                <?php echo get_phrase('recent_activity'); ?>
            </div>
            <div class="audit-table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th><?php echo get_phrase('action'); ?></th>
                            <th><?php echo get_phrase('user'); ?></th>
                            <th><?php echo get_phrase('timestamp'); ?></th>
                            <th><?php echo get_phrase('ip_address'); ?></th>
                            <th><?php echo get_phrase('details'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($logs) && !empty($logs)): ?>
                            <?php foreach ($logs as $log): ?>
                                <tr>
                                    <td>
                                        <span class="action-badge <?php echo $log['action_type']; ?>">
                                            <?php echo get_phrase($log['action_type']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div>
                                            <strong><?php echo htmlspecialchars($log['user_id'] ?? 'N/A'); ?></strong>
                                        </div>
                                        <span class="user-type-badge">
                                            <?php echo htmlspecialchars($log['user_type'] ?? 'N/A'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="timestamp">
                                            <?php echo date('Y-m-d H:i:s', $log['created_at']); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <code><?php echo htmlspecialchars($log['ip_address'] ?? 'N/A'); ?></code>
                                    </td>
                                    <td>
                                        <span class="details-toggle" onclick="toggleDetails(<?php echo $log['id']; ?>)">
                                            <?php echo get_phrase('view_details'); ?>
                                        </span>
                                        <div class="details-content" id="details-<?php echo $log['id']; ?>">
                                            <?php
                                            $details = json_decode($log['action_details'], true);
                                            if ($details && is_array($details)):
                                                foreach ($details as $key => $value):
                                            ?>
                                                <div class="detail-item">
                                                    <span class="detail-label"><?php echo ucfirst(str_replace('_', ' ', $key)); ?>: </span>
                                                    <span class="detail-value"><?php echo htmlspecialchars($value); ?></span>
                                                </div>
                                            <?php
                                                endforeach;
                                            else:
                                            ?>
                                                <div class="detail-item">
                                                    <span class="detail-value"><?php echo get_phrase('no_details_available'); ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5">
                                    <div class="no-logs">
                                        <i class="mdi mdi-history"></i>
                                        <p><?php echo get_phrase('no_audit_logs_found'); ?></p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function toggleDetails(logId) {
            var details = document.getElementById('details-' + logId);
            details.classList.toggle('active');
        }
    </script>
</body>
</html>