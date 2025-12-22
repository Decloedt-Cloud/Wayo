<?php
/**
 * Invoice PDF Template
 * Modern, professional design compatible with mPDF
 * 
 * Required variables: $invoice_id
 * Loads: $invoice_details, $student_details, $school, $settings_school
 */

// =============================================================================
// DATA LOADING
// =============================================================================
$invoice_details = $this->crud_model->get_invoice_by_id($invoice_id);
$student_details = $this->user_model->get_student_details_by_id('student', $invoice_details['student_id']);
$school = $this->db->get_where('schools', array('id' => $invoice_details['school_id']))->row_array();
$settings_school = $this->settings_model->get_settings_school_data($invoice_details['school_id']);

// Logo
$school_logo = $this->settings_model->get_logo_school($invoice_details['school_id']);
$has_logo = !empty($school_logo) && strpos($school_logo, 'placeholder') === false;

// Class details (optional)
$class_details = !empty($invoice_details['class_id']) 
    ? $this->crud_model->get_class_details_by_id($invoice_details['class_id'])->row_array() 
    : null;

// =============================================================================
// VAT CALCULATION (aligned with invoice.php)
// =============================================================================
$vat_applicable = isset($settings_school['vat']) && (int)$settings_school['vat'] === 1;
$tax_residence = isset($settings_school['Tax_residence']) ? $settings_school['Tax_residence'] : null;

// FX Data (needed for correct calculation)
$conversion_applied = isset($invoice_details['conversion_applied']) && $invoice_details['conversion_applied'] == 1;
$payment_amount_converted = isset($invoice_details['payment_amount_converted']) ? (float)$invoice_details['payment_amount_converted'] : null;
$fx_rate = isset($invoice_details['fx_rate']) ? (float)$invoice_details['fx_rate'] : null;

// Calculate correct total: if FX conversion was applied, reconstruct original amount
// The total_amount might be incorrect if it was calculated from converted amount
$original_total = (float)$invoice_details['total_amount'];

if ($conversion_applied && $payment_amount_converted && $fx_rate && $fx_rate > 0) {
    // Reconstruct original amount from converted amount
    $reconstructed_total = $payment_amount_converted / $fx_rate;
    // If reconstructed total is much smaller, it's likely the correct one
    // (the DB might have the converted amount reconverted incorrectly)
    if ($reconstructed_total < $original_total * 0.5) {
        $original_total = $reconstructed_total;
    }
}

if (isset($invoice_details['vat_amount']) && isset($invoice_details['vat_rate']) && isset($invoice_details['sub_total'])) {
    $sub_total = (float)$invoice_details['sub_total'];
    $vat_amount = (float)$invoice_details['vat_amount'];
    $vat_rate = (float)$invoice_details['vat_rate'];
    // Recalculate grand_total to ensure consistency
    $grand_total = $sub_total + $vat_amount;
    
    // If calculated total differs significantly from stored total, use calculated
    if (abs($grand_total - $original_total) > 0.01) {
        $grand_total = $sub_total + $vat_amount;
    }
} else {
    $vat_rate = 0;
    if ($vat_applicable) {
        if ($tax_residence === 'MA') $vat_rate = 20;
        elseif ($tax_residence === 'UAE' || $tax_residence === 'AE') $vat_rate = 5;
    }
    $grand_total = $original_total;
    // Calculate from grand_total
    $sub_total = $vat_rate > 0 ? $grand_total / (1 + ($vat_rate / 100)) : $grand_total;
    $vat_amount = $grand_total - $sub_total;
}

// Use consistent variable names
$subtotal_ht = round($sub_total, 2);
$vat_amount = round($vat_amount, 2);
$total_ttc = round($grand_total, 2);

// =============================================================================
// FX CONVERSION DATA (additional variables)
// =============================================================================
$payment_currency = isset($invoice_details['payment_currency']) ? $invoice_details['payment_currency'] : null;
$fx_rate_date = isset($invoice_details['fx_rate_date']) ? $invoice_details['fx_rate_date'] : null;

// =============================================================================
// STATUS & DATES
// =============================================================================
$is_paid = strtolower($invoice_details['status']) === 'paid';
$invoice_date = date('d/m/Y', $invoice_details['created_at']);
$payment_date = ($invoice_details['updated_at'] > 0) ? date('d/m/Y', $invoice_details['updated_at']) : null;
$invoice_currency = htmlspecialchars($invoice_details['currency'], ENT_QUOTES, 'UTF-8');

// =============================================================================
// ESCAPE HELPER
// =============================================================================
function esc($str) {
    return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Facture <?php echo sprintf('%08d', $invoice_details['id']); ?></title>
    <style>
        /* =================================================================
           PAGE SETUP
           ================================================================= */
        @page {
            margin: 12mm 12mm 20mm 12mm;
            footer: html_myfooter;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 9px;
            color: #1a202c;
            line-height: 1.5;
            background: #fff;
        }
        
        /* =================================================================
           HEADER
           ================================================================= */
        .header {
            padding: 8px 0;
            border-bottom: 2px solid #2d3748;
            margin-bottom: 16px;
        }
        
        .header-table {
            width: 100%;
        }
        
        .header-left {
            vertical-align: middle;
        }
        
        .header-logo {
            height: 12px;
            vertical-align: middle;
            margin-right: 6px;
        }
        
        .header-company {
            font-size: 11px;
            font-weight: bold;
            color: #1a202c;
            vertical-align: middle;
        }
        
        .header-right {
            text-align: right;
            vertical-align: middle;
        }
        
        .invoice-title {
            font-size: 14px;
            font-weight: bold;
            color: #2d3748;
            letter-spacing: 1px;
        }
        
        .invoice-number {
            font-size: 9px;
            color: #718096;
            margin-top: 2px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 10px;
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 4px;
        }
        
        .status-paid {
            background: #c6f6d5;
            color: #22543d;
        }
        
        .status-unpaid {
            background: #fed7d7;
            color: #742a2a;
        }
        
        /* =================================================================
           BILLING SECTION
           ================================================================= */
        .billing-section {
            margin-bottom: 16px;
        }
        
        .billing-table {
            width: 100%;
        }
        
        .billing-cell {
            width: 48%;
            vertical-align: top;
        }
        
        .billing-box {
            background: #f7fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 10px 12px;
        }
        
        .billing-box.client {
            background: #ebf8ff;
            border-color: #bee3f8;
        }
        
        .billing-label {
            font-size: 6px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #a0aec0;
            margin-bottom: 6px;
            font-weight: bold;
        }
        
        .billing-name {
            font-size: 10px;
            font-weight: bold;
            color: #1a202c;
            margin-bottom: 4px;
        }
        
        .billing-details {
            font-size: 8px;
            color: #4a5568;
            line-height: 1.6;
        }
        
        /* =================================================================
           META INFO BAR
           ================================================================= */
        .meta-bar {
            background: #2d3748;
            border-radius: 4px;
            padding: 10px;
            margin-bottom: 16px;
        }
        
        .meta-table {
            width: 100%;
        }
        
        .meta-cell {
            text-align: center;
            color: #fff;
            padding: 4px 8px;
            border-right: 1px solid #4a5568;
        }
        
        .meta-cell:last-child {
            border-right: none;
        }
        
        .meta-label {
            font-size: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #a0aec0;
            margin-bottom: 3px;
        }
        
        .meta-value {
            font-size: 10px;
            font-weight: bold;
        }
        
        /* =================================================================
           ITEMS TABLE
           ================================================================= */
        .section-title {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #718096;
            margin-bottom: 8px;
            font-weight: bold;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        
        .items-table thead th {
            background: #edf2f7;
            font-size: 7px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 10px 8px;
            text-align: left;
            color: #4a5568;
            border-bottom: 2px solid #cbd5e0;
            font-weight: bold;
        }
        
        .items-table thead th.text-right {
            text-align: right;
        }
        
        .items-table tbody td {
            padding: 12px 8px;
            font-size: 9px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }
        
        .items-table tbody td.text-right {
            text-align: right;
        }
        
        .item-name {
            font-weight: 600;
            color: #1a202c;
        }
        
        .item-description {
            font-size: 7px;
            color: #718096;
            margin-top: 3px;
        }
        
        /* =================================================================
           TOTALS BOX
           ================================================================= */
        .totals-wrapper {
            width: 45%;
            float: right;
            margin-bottom: 16px;
        }
        
        .totals-table {
            width: 100%;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .totals-table tr {
            border-bottom: 1px solid #e2e8f0;
        }
        
        .totals-table tr:last-child {
            border-bottom: none;
        }
        
        .totals-table td {
            padding: 8px 10px;
            font-size: 9px;
        }
        
        .totals-table .label {
            color: #718096;
            background: #f7fafc;
        }
        
        .totals-table .value {
            text-align: right;
            font-weight: 600;
            color: #1a202c;
        }
        
        .totals-table .grand-total {
            background: #2d3748;
        }
        
        .totals-table .grand-total td {
            color: #fff;
            font-weight: bold;
            font-size: 11px;
            padding: 10px;
        }
        
        .clear {
            clear: both;
        }
        
        /* =================================================================
           INFO BOXES (FX & PAYMENT)
           ================================================================= */
        .info-blocks {
            margin-top: 16px;
        }
        
        .info-box {
            border-radius: 4px;
            padding: 10px 12px;
            margin-bottom: 10px;
        }
        
        .info-box.fx {
            background: #ebf4ff;
            border-left: 3px solid #4299e1;
        }
        
        .info-box.payment {
            background: #f0fff4;
            border-left: 3px solid #48bb78;
        }
        
        .info-box-title {
            font-size: 8px;
            font-weight: bold;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .info-box.fx .info-box-title {
            color: #2b6cb0;
        }
        
        .info-box.payment .info-box-title {
            color: #276749;
        }
        
        .info-box-content {
            font-size: 8px;
        }
        
        .info-box.fx .info-box-content {
            color: #2c5282;
        }
        
        .info-box.payment .info-box-content {
            color: #276749;
        }
        
        .info-row {
            margin-bottom: 3px;
        }
        
        .info-row-label {
            display: inline-block;
            width: 55%;
        }
        
        .info-row-value {
            display: inline-block;
            width: 44%;
            text-align: right;
            font-weight: 600;
        }
        
        /* =================================================================
           NOTES
           ================================================================= */
        .notes-section {
            background: #fffaf0;
            border-left: 3px solid #ed8936;
            padding: 10px 12px;
            margin-top: 16px;
            border-radius: 0 4px 4px 0;
        }
        
        .notes-title {
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
            color: #c05621;
            margin-bottom: 4px;
        }
        
        .notes-text {
            font-size: 7px;
            color: #744210;
            line-height: 1.6;
        }
        
        /* =================================================================
           FOOTER (mPDF htmlpagefooter)
           ================================================================= */
        .pdf-footer {
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
            text-align: center;
            font-size: 7px;
            color: #718096;
        }
        
        .pdf-footer-company {
            font-weight: bold;
            color: #2d3748;
        }
        
        .pdf-footer-meta {
            color: #a0aec0;
            margin-top: 2px;
        }
    </style>
</head>
<body>

    <!-- ================================================================
         HEADER
         ================================================================ -->
    <div class="header">
        <table class="header-table">
            <tr>
                <td class="header-left">
                    <?php if ($has_logo): ?>
                        <img src="<?php echo $school_logo; ?>" alt="" class="header-logo">
                    <?php endif; ?>
                    <span class="header-company"><?php echo esc($school['name']); ?></span>
                </td>
                <td class="header-right">
                    <div class="invoice-title">FACTURE</div>
                    <div class="invoice-number">#<?php echo sprintf('%08d', $invoice_details['id']); ?> — <?php echo $invoice_date; ?></div>
                    <span class="status-badge <?php echo $is_paid ? 'status-paid' : 'status-unpaid'; ?>">
                        <?php echo $is_paid ? '✓ Payée' : '○ En attente'; ?>
                    </span>
                </td>
            </tr>
        </table>
    </div>

    <!-- ================================================================
         BILLING SECTION
         ================================================================ -->
    <div class="billing-section">
        <table class="billing-table">
            <tr>
                <td class="billing-cell" style="padding-right: 8px;">
                    <div class="billing-box">
                        <div class="billing-label">Émetteur</div>
                        <div class="billing-name"><?php echo esc($school['name']); ?></div>
                        <div class="billing-details">
                            <?php if (!empty($school['Rue'])): ?>
                                <?php echo esc($school['Rue']); ?> <?php echo esc($school['Numero']); ?><br>
                            <?php endif; ?>
                            <?php echo esc($school['Codepostal']); ?> <?php echo esc($school['Ville']); ?>
                            <?php if (!empty($school['num_vat'])): ?>
                                <br>N° TVA: <?php echo esc($school['num_vat']); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </td>
                <td class="billing-cell" style="padding-left: 8px;">
                    <div class="billing-box client">
                        <div class="billing-label">Facturer à</div>
                        <div class="billing-name"><?php echo esc($student_details['name']); ?></div>
                        <div class="billing-details">
                            <?php if (!empty($student_details['Rue'])): ?>
                                <?php echo esc($student_details['Rue']); ?> <?php echo esc($student_details['Numero']); ?><br>
                            <?php endif; ?>
                            <?php if (!empty($student_details['Codepostal']) || !empty($student_details['Ville'])): ?>
                                <?php echo esc($student_details['Codepostal']); ?> <?php echo esc($student_details['Ville']); ?><br>
                            <?php endif; ?>
                            <?php if (!empty($student_details['email'])): ?>
                                <?php echo esc($student_details['email']); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- ================================================================
         META INFO BAR
         ================================================================ -->
    <div class="meta-bar">
        <table class="meta-table">
            <tr>
                <td class="meta-cell">
                    <div class="meta-label">N° Facture</div>
                    <div class="meta-value"><?php echo sprintf('%08d', $invoice_details['id']); ?></div>
                </td>
                <td class="meta-cell">
                    <div class="meta-label">Date d'émission</div>
                    <div class="meta-value"><?php echo $invoice_date; ?></div>
                </td>
                <td class="meta-cell">
                    <div class="meta-label">Méthode de paiement</div>
                    <div class="meta-value"><?php echo !empty($invoice_details['payment_method']) ? esc(ucfirst($invoice_details['payment_method'])) : '—'; ?></div>
                </td>
                <td class="meta-cell">
                    <div class="meta-label">Devise</div>
                    <div class="meta-value"><?php echo $invoice_currency; ?></div>
                </td>
                <td class="meta-cell">
                    <div class="meta-label">Total TTC</div>
                    <div class="meta-value"><?php echo number_format($total_ttc, 2, ',', ' '); ?> <?php echo $invoice_currency; ?></div>
                </td>
            </tr>
        </table>
    </div>

    <!-- ================================================================
         ITEMS TABLE
         ================================================================ -->
    <div class="section-title">Détails de la facture</div>
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 50%;">Description</th>
                <th style="width: 10%;" class="text-right">Qté</th>
                <th style="width: 20%;" class="text-right">Prix unitaire HT</th>
                <th style="width: 20%;" class="text-right">Total HT</th>
            </tr>
        </thead>
        <tbody>
            <!-- Item row (supports future multiple items) -->
            <tr>
                <td>
                    <div class="item-name"><?php echo esc($invoice_details['title']); ?></div>
                    <?php if ($class_details): ?>
                        <div class="item-description">Classe: <?php echo esc($class_details['name']); ?></div>
                    <?php endif; ?>
                    <?php if (!empty($invoice_details['description'])): ?>
                        <div class="item-description"><?php echo esc($invoice_details['description']); ?></div>
                    <?php endif; ?>
                </td>
                <td class="text-right">1</td>
                <td class="text-right"><?php echo number_format($subtotal_ht, 2, ',', ' '); ?> <?php echo $invoice_currency; ?></td>
                <td class="text-right"><?php echo number_format($subtotal_ht, 2, ',', ' '); ?> <?php echo $invoice_currency; ?></td>
            </tr>
        </tbody>
    </table>

    <!-- ================================================================
         TOTALS BOX
         ================================================================ -->
    <div class="totals-wrapper">
        <table class="totals-table">
            <tr>
                <td class="label">Sous-total HT</td>
                <td class="value"><?php echo number_format($subtotal_ht, 2, ',', ' '); ?> <?php echo $invoice_currency; ?></td>
            </tr>
            <tr>
                <td class="label">TVA <?php echo $vat_rate > 0 ? '(' . $vat_rate . '%)' : '(0%)'; ?></td>
                <td class="value"><?php echo number_format($vat_amount, 2, ',', ' '); ?> <?php echo $invoice_currency; ?></td>
            </tr>
            <tr class="grand-total">
                <td>Total TTC</td>
                <td style="text-align: right;"><?php echo number_format($total_ttc, 2, ',', ' '); ?> <?php echo $invoice_currency; ?></td>
            </tr>
        </table>
    </div>
    <div class="clear"></div>

    <!-- ================================================================
         INFO BOXES
         ================================================================ -->
    <div class="info-blocks">
        
        <?php if ($is_paid && $conversion_applied && $payment_currency && $payment_amount_converted): ?>
        <!-- FX Conversion Block -->
        <div class="info-box fx">
            <div class="info-box-title">↔ Conversion de devise</div>
            <div class="info-box-content">
                <div class="info-row">
                    <span class="info-row-label">Montant facturé:</span>
                    <span class="info-row-value"><?php echo number_format($total_ttc, 2, ',', ' '); ?> <?php echo $invoice_currency; ?></span>
                </div>
                <div class="info-row">
                    <span class="info-row-label">Montant débité:</span>
                    <span class="info-row-value"><?php echo number_format($payment_amount_converted, 2, ',', ' '); ?> <?php echo esc($payment_currency); ?></span>
                </div>
                <?php if ($fx_rate): ?>
                <div class="info-row">
                    <span class="info-row-label">Taux de change:</span>
                    <span class="info-row-value">1 <?php echo $invoice_currency; ?> = <?php echo number_format($fx_rate, 4); ?> <?php echo esc($payment_currency); ?></span>
                </div>
                <?php endif; ?>
                <?php if ($fx_rate_date): ?>
                <div class="info-row">
                    <span class="info-row-label">Date du taux:</span>
                    <span class="info-row-value"><?php echo date('d/m/Y', strtotime($fx_rate_date)); ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if ($is_paid): ?>
        <!-- Payment Confirmation Block -->
        <div class="info-box payment">
            <div class="info-box-title">✓ Paiement confirmé</div>
            <div class="info-box-content">
                <div class="info-row">
                    <span class="info-row-label">Méthode:</span>
                    <span class="info-row-value"><?php echo esc(ucfirst($invoice_details['payment_method'])); ?></span>
                </div>
                <?php if ($payment_date): ?>
                <div class="info-row">
                    <span class="info-row-label">Date de paiement:</span>
                    <span class="info-row-value"><?php echo $payment_date; ?></span>
                </div>
                <?php endif; ?>
                <div class="info-row">
                    <span class="info-row-label">Montant reçu:</span>
                    <span class="info-row-value">
                        <?php 
                        if ($conversion_applied && $payment_amount_converted) {
                            echo number_format($payment_amount_converted, 2, ',', ' ') . ' ' . esc($payment_currency);
                        } else {
                            echo number_format($total_ttc, 2, ',', ' ') . ' ' . $invoice_currency;
                        }
                        ?>
                    </span>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
    </div>

    <!-- ================================================================
         NOTES
         ================================================================ -->
    <div class="notes-section">
        <div class="notes-title">Informations</div>
        <div class="notes-text">
            • Paiement traité de manière sécurisée<br>
            • Conservez cette facture pour vos dossiers comptables<br>
            • Merci pour votre confiance
        </div>
    </div>

    <!-- ================================================================
         FOOTER (mPDF - appears on every page)
         ================================================================ -->
    <htmlpagefooter name="myfooter">
        <div class="pdf-footer">
            <span class="pdf-footer-company"><?php echo esc($school['name']); ?></span>
            <?php if (!empty($school['email'])): ?> • <?php echo esc($school['email']); ?><?php endif; ?>
            <?php if (!empty($school['phone'])): ?> • <?php echo esc($school['phone']); ?><?php endif; ?>
            <div class="pdf-footer-meta">
                Document généré le <?php echo date('d/m/Y à H:i'); ?> • Page {PAGENO} sur {nbpg}
            </div>
        </div>
    </htmlpagefooter>
    <sethtmlpagefooter name="myfooter" value="on" />

</body>
</html>
