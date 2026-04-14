<?php
/**
 * Expense Export PDF Template
 * 
 * This template is used to generate PDF exports of expenses
 */

$school_details = db()->table('settings_school')->where('school_id', school_id())->get()->getResultArray();
$school_name = isset($school_details['school_name']) ? $school_details['school_name'] : 'School Management System';
$total_amount = array_sum(array_column($expenses, 'amount'));
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo get_phrase('expenses_report'); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #6366f1;
        }
        .header h1 {
            font-size: 24px;
            color: #6366f1;
            margin-bottom: 5px;
        }
        .header h2 {
            font-size: 16px;
            color: #666;
            font-weight: normal;
        }
        .meta-info {
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .meta-info p {
            margin: 5px 0;
        }
        .meta-info strong {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-weight: 600;
        }
        th:first-child {
            border-radius: 8px 0 0 0;
        }
        th:last-child {
            border-radius: 0 8px 0 0;
            text-align: right;
        }
        td {
            padding: 10px 8px;
            border-bottom: 1px solid #e9ecef;
        }
        td:last-child {
            text-align: right;
            font-weight: 600;
        }
        tr:nth-child(even) {
            background: #f8f9fa;
        }
        tr:hover {
            background: #e9ecef;
        }
        .summary {
            margin-top: 20px;
            padding: 20px;
            background: linear-gradient(135deg, #10b981, #34d399);
            border-radius: 8px;
            color: white;
            text-align: right;
        }
        .summary h3 {
            font-size: 14px;
            font-weight: normal;
            margin-bottom: 5px;
        }
        .summary .total {
            font-size: 24px;
            font-weight: bold;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            color: #999;
            font-size: 10px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }
        .expense-id {
            color: #6366f1;
            font-family: monospace;
        }
        .category-badge {
            display: inline-block;
            padding: 3px 8px;
            background: #e9ecef;
            border-radius: 4px;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1><?php echo $school_name; ?></h1>
        <h2><?php echo get_phrase('expenses_report'); ?></h2>
    </div>

    <div class="meta-info">
        <p><strong><?php echo get_phrase('period'); ?>:</strong> <?php echo date('d M Y', $date_from); ?> - <?php echo date('d M Y', $date_to); ?></p>
        <p><strong><?php echo get_phrase('total_expenses'); ?>:</strong> <?php echo count($expenses); ?></p>
        <p><strong><?php echo get_phrase('generated_on'); ?>:</strong> <?php echo date('d M Y H:i'); ?></p>
    </div>

    <?php if (count($expenses) > 0): ?>
    <table>
        <thead>
            <tr>
                <th><?php echo get_phrase('id'); ?></th>
                <th><?php echo get_phrase('date'); ?></th>
                <th><?php echo get_phrase('category'); ?></th>
                <th><?php echo get_phrase('amount'); ?> (<?php echo $school_currency; ?>)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($expenses as $expense): 
                $category = db()->table('expense_categories')->where('id', $expense['expense_category_id'])->get()->getRowArray();
                $category_name = isset($category['name']) ? $category['name'] : get_phrase('unknown');
            ?>
            <tr>
                <td><span class="expense-id">EXP-<?php echo str_pad($expense['id'], 4, '0', STR_PAD_LEFT); ?></span></td>
                <td><?php echo date('d M Y', $expense['date']); ?></td>
                <td><span class="category-badge"><?php echo $category_name; ?></span></td>
                <td><?php echo number_format($expense['amount'], 2); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="summary">
        <h3><?php echo get_phrase('total_amount'); ?></h3>
        <div class="total"><?php echo number_format($total_amount, 2); ?> <?php echo $school_currency; ?></div>
    </div>
    <?php else: ?>
    <p style="text-align: center; color: #666; padding: 40px;">
        <?php echo get_phrase('no_expenses_found'); ?>
    </p>
    <?php endif; ?>

    <div class="footer">
        <p><?php echo get_phrase('generated_by'); ?> <?php echo $school_name; ?> - <?php echo date('Y'); ?></p>
    </div>
</body>
</html>
