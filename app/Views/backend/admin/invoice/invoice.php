<?php
/**
 * Admin Invoice View - Professional Edition
 * Modern single invoice display with all details
 */

$invoice_details = $this->crud_model->get_invoice_by_id($invoice_id);
$student_details = $this->user_model->get_student_details_by_id('student', $invoice_details['student_id']);
$school = db()->table('schools')->where('id', $invoice_details['school_id'])->get()->getRowArray();
$settings_school = $this->settings_model->get_settings_school_data($invoice_details['school_id']);

// Class details
$class_details = !empty($invoice_details['class_id']) 
    ? $this->crud_model->get_class_details_by_id($invoice_details['class_id']) 
    : null;

// VAT Calculation - Support both old 'vat' and new 'vat_enabled' column names
$vat_applicable = (isset($settings_school['vat_enabled']) && (int)$settings_school['vat_enabled'] === 1) 
                || (isset($settings_school['vat']) && (int)$settings_school['vat'] === 1);

// Utiliser country depuis schools table (source unique de vérité)
$tax_residence = null;
if (!empty($school['country'])) {
    $tax_residence = strtoupper($school['country']);
}

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

// FX Data
$conversion_applied = isset($invoice_details['conversion_applied']) && $invoice_details['conversion_applied'] == 1;
$payment_currency = isset($invoice_details['payment_currency']) ? $invoice_details['payment_currency'] : null;
$payment_amount_converted = isset($invoice_details['payment_amount_converted']) ? (float)$invoice_details['payment_amount_converted'] : null;
$fx_rate = isset($invoice_details['fx_rate']) ? (float)$invoice_details['fx_rate'] : null;
$fx_rate_date = isset($invoice_details['fx_rate_date']) ? $invoice_details['fx_rate_date'] : null;

// Status & Dates
$is_paid = strtolower($invoice_details['status']) === 'paid';
$invoice_date = date('d/m/Y', $invoice_details['created_at']);
$payment_date = ($invoice_details['updated_at'] > 0) ? date('d/m/Y', $invoice_details['updated_at']) : null;
$due_amount = $grand_total - (float)$invoice_details['paid_amount'];

// Logo
$school_logo = $this->settings_model->get_logo_school($invoice_details['school_id']);
?>

<style>
/* Invoice View Pro Styles */
:root {
    --inv-primary: #6366f1;
    --inv-success: #059669;
    --inv-danger: #dc2626;
    --inv-warning: #d97706;
    --inv-dark: #1e293b;
    --inv-gray: #64748b;
    --inv-light: #f8fafc;
    --inv-border: #e2e8f0;
}

.inv-container {
    max-width: 900px;
    margin: 0 auto;
}

.inv-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    overflow: hidden;
    border: 1px solid var(--inv-border);
}

/* Header */
.inv-header {
    background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
    padding: 1.5rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.inv-header-left {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.inv-logo {
    height: 40px;
    max-width: 120px;
    object-fit: contain;
    filter: brightness(0) invert(1);
}

.inv-company {
    color: white;
}

.inv-company-name {
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
}

.inv-company-address {
    font-size: 0.8rem;
    color: #94a3b8;
}

.inv-header-right {
    text-align: right;
}

.inv-title {
    font-size: 1.75rem;
    font-weight: 800;
    color: white;
    letter-spacing: 2px;
    margin-bottom: 0.25rem;
}

.inv-number {
    font-size: 1rem;
    color: #94a3b8;
    font-family: 'SF Mono', monospace;
}

/* Status Banner */
.inv-status-banner {
    padding: 0.75rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.inv-status-banner.paid {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
}

.inv-status-banner.unpaid {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
}

.inv-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 700;
    font-size: 0.9rem;
}

.inv-status-banner.paid .inv-status-badge {
    color: #065f46;
}

.inv-status-banner.unpaid .inv-status-badge {
    color: #991b1b;
}

.inv-status-badge i {
    font-size: 1.25rem;
}

.inv-status-date {
    font-size: 0.8rem;
    color: var(--inv-gray);
}

/* Body */
.inv-body {
    padding: 2rem;
}

/* Info Grid */
.inv-info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
    margin-bottom: 2rem;
}

@media (max-width: 768px) {
    .inv-info-grid { grid-template-columns: 1fr; }
}

.inv-info-box {
    background: var(--inv-light);
    border-radius: 12px;
    padding: 1.25rem;
    border: 1px solid var(--inv-border);
}

.inv-info-box.client {
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    border-color: #bfdbfe;
}

.inv-info-label {
    font-size: 0.65rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: var(--inv-gray);
    margin-bottom: 0.75rem;
    font-weight: 600;
}

.inv-info-name {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--inv-dark);
    margin-bottom: 0.5rem;
}

.inv-info-details {
    font-size: 0.85rem;
    color: var(--inv-gray);
    line-height: 1.6;
}

.inv-info-details a {
    color: var(--inv-primary);
    text-decoration: none;
}

/* Meta Cards */
.inv-meta-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
    margin-bottom: 2rem;
}

@media (max-width: 768px) {
    .inv-meta-row { grid-template-columns: repeat(2, 1fr); }
}

.inv-meta-card {
    background: white;
    border: 1px solid var(--inv-border);
    border-radius: 10px;
    padding: 1rem;
    text-align: center;
}

.inv-meta-card.highlight {
    background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
    border: none;
}

.inv-meta-card.highlight .inv-meta-label,
.inv-meta-card.highlight .inv-meta-value {
    color: white;
}

.inv-meta-label {
    font-size: 0.65rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--inv-gray);
    margin-bottom: 0.5rem;
}

.inv-meta-value {
    font-size: 1rem;
    font-weight: 700;
    color: var(--inv-dark);
}

/* Items Table */
.inv-table-wrap {
    background: white;
    border-radius: 12px;
    border: 1px solid var(--inv-border);
    overflow: hidden;
    margin-bottom: 1.5rem;
}

.inv-table {
    width: 100%;
    border-collapse: collapse;
}

.inv-table thead {
    background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
}

.inv-table thead th {
    padding: 1rem;
    text-align: left;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--inv-gray);
    font-weight: 600;
    border-bottom: 2px solid var(--inv-border);
}

.inv-table thead th:last-child {
    text-align: right;
}

.inv-table tbody td {
    padding: 1.25rem 1rem;
    vertical-align: top;
    border-bottom: 1px solid var(--inv-border);
}

.inv-table tbody td:last-child {
    text-align: right;
}

.inv-table tbody tr:last-child td {
    border-bottom: none;
}

.inv-item-name {
    font-weight: 600;
    color: var(--inv-dark);
    margin-bottom: 0.25rem;
}

.inv-item-desc {
    font-size: 0.8rem;
    color: var(--inv-gray);
}

.inv-item-class {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    background: #e0e7ff;
    color: #4338ca;
    padding: 0.2rem 0.5rem;
    border-radius: 4px;
    font-size: 0.7rem;
    margin-top: 0.5rem;
}

/* Totals */
.inv-totals-section {
    display: flex;
    justify-content: flex-end;
    margin-bottom: 2rem;
}

.inv-totals-box {
    width: 320px;
    background: var(--inv-light);
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid var(--inv-border);
}

.inv-totals-row {
    display: flex;
    justify-content: space-between;
    padding: 0.85rem 1.25rem;
    border-bottom: 1px solid var(--inv-border);
}

.inv-totals-row:last-child {
    border-bottom: none;
}

.inv-totals-row.grand {
    background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
}

.inv-totals-label {
    font-size: 0.85rem;
    color: var(--inv-gray);
}

.inv-totals-row.grand .inv-totals-label {
    color: #94a3b8;
    font-weight: 600;
}

.inv-totals-value {
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--inv-dark);
}

.inv-totals-row.grand .inv-totals-value {
    color: white;
    font-size: 1.1rem;
}

.inv-totals-row.due {
    background: #fef2f2;
}

.inv-totals-row.due .inv-totals-label {
    color: #991b1b;
}

.inv-totals-row.due .inv-totals-value {
    color: #dc2626;
    font-size: 1.1rem;
}

/* FX & Payment Info */
.inv-info-cards {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
    margin-bottom: 2rem;
}

@media (max-width: 768px) {
    .inv-info-cards { grid-template-columns: 1fr; }
}

.inv-info-card {
    border-radius: 12px;
    padding: 1.25rem;
}

.inv-info-card.fx {
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    border: 1px solid #bfdbfe;
}

.inv-info-card.payment {
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    border: 1px solid #bbf7d0;
}

.inv-info-card-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.85rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.inv-info-card.fx .inv-info-card-title {
    color: #1d4ed8;
}

.inv-info-card.payment .inv-info-card-title {
    color: #15803d;
}

.inv-info-card-row {
    display: flex;
    justify-content: space-between;
    padding: 0.4rem 0;
    font-size: 0.85rem;
}

.inv-info-card.fx .inv-info-card-row {
    color: #1e40af;
}

.inv-info-card.payment .inv-info-card-row {
    color: #166534;
}

.inv-info-card-row span:last-child {
    font-weight: 600;
}

/* Actions */
.inv-actions {
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
    padding-top: 1.5rem;
    border-top: 1px solid var(--inv-border);
    flex-wrap: wrap;
}

.inv-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.25rem;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
}

.inv-btn-primary {
    background: var(--inv-primary);
    color: white;
}

.inv-btn-primary:hover {
    background: #4338ca;
    color: white;
}

.inv-btn-success {
    background: var(--inv-success);
    color: white;
}

.inv-btn-success:hover {
    background: #047857;
    color: white;
}

.inv-btn-outline {
    background: white;
    color: var(--inv-gray);
    border: 1px solid var(--inv-border);
}

.inv-btn-outline:hover {
    background: var(--inv-light);
    color: var(--inv-dark);
}

/* Print Styles */
@media print {
    body * { visibility: hidden; }
    .inv-container, .inv-container * { visibility: visible; }
    .inv-container {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        max-width: 100%;
    }
    .inv-actions { display: none !important; }
    .inv-card { box-shadow: none; border: 1px solid #ddd; }
}
</style>

<!-- Page Title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">
                <i class="mdi mdi-file-document-outline title_icon"></i> 
                <?php echo get_phrase('invoice'); ?> #<?php echo sprintf('%08d', $invoice_details['id']); ?>
        	</h4>
        </div>
    </div>
</div>

<div class="inv-container">
    <div class="inv-card">
        
        <!-- Header -->
        <div class="inv-header">
            <div class="inv-header-left">
                <?php if (!empty($school_logo)): ?>
                    <img src="<?php echo $school_logo; ?>" alt="" class="inv-logo">
                <?php endif; ?>
                <div class="inv-company">
                    <div class="inv-company-name"><?php echo htmlspecialchars($school['name']); ?></div>
                    <div class="inv-company-address">
                        <?php if (!empty($school['Rue'])): ?>
                            <?php echo htmlspecialchars($school['Rue']); ?> <?php echo htmlspecialchars($school['Numero']); ?>,
                        <?php endif; ?>
                        <?php echo htmlspecialchars($school['Codepostal']); ?> <?php echo htmlspecialchars($school['Ville']); ?>
                    </div>
                </div>
            </div>
            <div class="inv-header-right">
                <div class="inv-title"><?php echo get_phrase('invoice'); ?></div>
                <div class="inv-number">#<?php echo sprintf('%08d', $invoice_details['id']); ?></div>
          </div>
        </div>

        <!-- Status Banner -->
        <div class="inv-status-banner <?php echo $is_paid ? 'paid' : 'unpaid'; ?>">
            <span class="inv-status-badge">
                <i class="mdi <?php echo $is_paid ? 'mdi-check-circle' : 'mdi-clock-alert'; ?>"></i>
                <?php echo $is_paid ? get_phrase('paid') : get_phrase('unpaid'); ?>
            </span>
            <span class="inv-status-date">
                <?php if ($is_paid && $payment_date): ?>
                    <?php echo get_phrase('paid_on'); ?> <?php echo $payment_date; ?>
                <?php else: ?>
                    <?php echo get_phrase('created_on'); ?> <?php echo $invoice_date; ?>
                <?php endif; ?>
            </span>
        </div>
        
        <!-- Body -->
        <div class="inv-body">
            
            <!-- Billing Info -->
            <div class="inv-info-grid">
                <div class="inv-info-box">
                    <div class="inv-info-label"><?php echo get_phrase('from'); ?></div>
                    <div class="inv-info-name"><?php echo htmlspecialchars($school['name']); ?></div>
                    <div class="inv-info-details">
                        <?php if (!empty($school['Rue'])): ?>
                            <?php echo htmlspecialchars($school['Rue']); ?> <?php echo htmlspecialchars($school['Numero']); ?><br>
                        <?php endif; ?>
                        <?php echo htmlspecialchars($school['Codepostal']); ?> <?php echo htmlspecialchars($school['Ville']); ?>
                        <?php if (!empty($school['num_vat'])): ?>
                            <br><?php echo get_phrase('vat'); ?>: <?php echo htmlspecialchars($school['num_vat']); ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="inv-info-box client">
                    <div class="inv-info-label"><?php echo get_phrase('bill_to'); ?></div>
                    <div class="inv-info-name"><?php echo htmlspecialchars($student_details['name']); ?></div>
                    <div class="inv-info-details">
                        <?php if (!empty($student_details['Rue'])): ?>
                            <?php echo htmlspecialchars($student_details['Rue']); ?> <?php echo htmlspecialchars($student_details['Numero']); ?><br>
                        <?php endif; ?>
                        <?php if (!empty($student_details['Codepostal']) || !empty($student_details['Ville'])): ?>
                            <?php echo htmlspecialchars($student_details['Codepostal']); ?> <?php echo htmlspecialchars($student_details['Ville']); ?><br>
                        <?php endif; ?>
                        <?php if (!empty($student_details['email'])): ?>
                            <a href="mailto:<?php echo $student_details['email']; ?>"><?php echo htmlspecialchars($student_details['email']); ?></a><br>
                        <?php endif; ?>
                        <?php if (!empty($student_details['phone'])): ?>
                            <?php echo htmlspecialchars($student_details['phone']); ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Meta Row -->
            <div class="inv-meta-row">
                <div class="inv-meta-card">
                    <div class="inv-meta-label"><?php echo get_phrase('invoice_no'); ?></div>
                    <div class="inv-meta-value"><?php echo sprintf('%08d', $invoice_details['id']); ?></div>
                </div>
                <div class="inv-meta-card">
                    <div class="inv-meta-label"><?php echo get_phrase('date'); ?></div>
                    <div class="inv-meta-value"><?php echo $invoice_date; ?></div>
                </div>
                <div class="inv-meta-card">
                    <div class="inv-meta-label"><?php echo get_phrase('method'); ?></div>
                    <div class="inv-meta-value">
                        <?php 
                        if (!empty($invoice_details['payment_method'])) {
                            echo ucfirst($invoice_details['payment_method']);
                        } elseif ($is_paid && (float)$invoice_details['paid_amount'] > 0) {
                            // Déduire la méthode pour les anciennes factures payées
                            if (!empty($invoice_details['stripe_payment_intent_id']) || $conversion_applied) {
                                echo 'Stripe';
                            } else {
                                echo 'Stripe'; // Default
                            }
                        } else {
                            echo '—';
                        }
                        ?>
                    </div>
                </div>
                <div class="inv-meta-card highlight">
                    <div class="inv-meta-label"><?php echo get_phrase('total'); ?></div>
                    <div class="inv-meta-value"><?php echo number_format($grand_total, 2, ',', ' '); ?> <?php echo $invoice_details['currency']; ?></div>
        </div>
        </div>
            
            <!-- Items Table -->
            <div class="inv-table-wrap">
                <table class="inv-table">
                <thead>
                  <tr>
                            <th style="width: 50%;"><?php echo get_phrase('description'); ?></th>
                            <th style="width: 15%;"><?php echo get_phrase('qty'); ?></th>
                            <th style="width: 17%;"><?php echo get_phrase('price_ht'); ?></th>
                            <th style="width: 18%;"><?php echo get_phrase('total'); ?></th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>
                                <div class="inv-item-name"><?php echo htmlspecialchars($invoice_details['title']); ?></div>
                                <div class="inv-item-desc"><?php echo get_phrase('created_at'); ?>: <?php echo date('d/m/Y H:i', $invoice_details['created_at']); ?></div>
                                <?php if ($class_details): ?>
                                    <span class="inv-item-class">
                                        <i class="mdi mdi-school"></i>
                                        <?php echo htmlspecialchars($class_details['name']); ?>
                                    </span>
                                <?php endif; ?>
                    </td>
                            <td>1</td>
                            <td><?php echo number_format($sub_total, 2, ',', ' '); ?> <?php echo $invoice_details['currency']; ?></td>
                            <td><?php echo number_format($sub_total, 2, ',', ' '); ?> <?php echo $invoice_details['currency']; ?></td>
                  </tr>
                </tbody>
              </table>
            </div>
            
            <!-- Totals -->
            <div class="inv-totals-section">
                <div class="inv-totals-box">
                    <div class="inv-totals-row">
                        <span class="inv-totals-label"><?php echo get_phrase('sub_total'); ?> <?php echo get_phrase('excl_tax'); ?></span>
                        <span class="inv-totals-value"><?php echo number_format($sub_total, 2, ',', ' '); ?> <?php echo $invoice_details['currency']; ?></span>
                    </div>
                    <div class="inv-totals-row">
                        <span class="inv-totals-label"><?php echo get_phrase('vat'); ?> <?php echo $vat_rate > 0 ? '('.$vat_rate.'%)' : '(0%)'; ?></span>
                        <span class="inv-totals-value"><?php echo number_format($vat_amount, 2, ',', ' '); ?> <?php echo $invoice_details['currency']; ?></span>
                    </div>
                    <div class="inv-totals-row grand">
                        <span class="inv-totals-label"><?php echo get_phrase('total'); ?> <?php echo get_phrase('incl_tax'); ?></span>
                        <span class="inv-totals-value"><?php echo number_format($grand_total, 2, ',', ' '); ?> <?php echo $invoice_details['currency']; ?></span>
                    </div>
                    <?php if (!$is_paid && $due_amount > 0): ?>
                    <div class="inv-totals-row due">
                        <span class="inv-totals-label"><?php echo get_phrase('due_amount'); ?></span>
                        <span class="inv-totals-value"><?php echo number_format($due_amount, 2, ',', ' '); ?> <?php echo $invoice_details['currency']; ?></span>
                    </div>
                    <?php endif; ?>
                </div>
        </div>
            
            <!-- FX & Payment Info -->
            <?php if ($is_paid || ($conversion_applied && $payment_currency)): ?>
            <div class="inv-info-cards">
                <?php if ($conversion_applied && $payment_currency && $payment_amount_converted): ?>
                <div class="inv-info-card fx">
                    <div class="inv-info-card-title">
                        <i class="mdi mdi-swap-horizontal-circle"></i>
                        <?php echo get_phrase('currency_conversion'); ?>
                    </div>
                    <div class="inv-info-card-row">
                        <span><?php echo get_phrase('invoiced_amount'); ?>:</span>
                        <span><?php echo number_format($grand_total, 2, ',', ' '); ?> <?php echo $invoice_details['currency']; ?></span>
                    </div>
                    <div class="inv-info-card-row">
                        <span><?php echo get_phrase('charged_amount'); ?>:</span>
                        <span><?php echo number_format($payment_amount_converted, 2, ',', ' '); ?> <?php echo $payment_currency; ?></span>
                    </div>
                    <?php if ($fx_rate): ?>
                    <div class="inv-info-card-row">
                        <span><?php echo get_phrase('exchange_rate'); ?>:</span>
                        <span>1 <?php echo $invoice_details['currency']; ?> = <?php echo number_format($fx_rate, 4); ?> <?php echo $payment_currency; ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($fx_rate_date): ?>
                    <div class="inv-info-card-row">
                        <span><?php echo get_phrase('rate_date'); ?>:</span>
                        <span><?php echo date('d/m/Y', strtotime($fx_rate_date)); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <?php if ($is_paid): ?>
                <div class="inv-info-card payment">
                    <div class="inv-info-card-title">
                        <i class="mdi mdi-check-decagram"></i>
                        <?php echo get_phrase('payment_confirmed'); ?>
                    </div>
                    <div class="inv-info-card-row">
                        <span><?php echo get_phrase('method'); ?>:</span>
                        <span>
                            <?php 
                            if (!empty($invoice_details['payment_method'])) {
                                echo ucfirst($invoice_details['payment_method']);
                            } elseif (!empty($invoice_details['stripe_payment_intent_id']) || $conversion_applied) {
                                echo 'Stripe';
                            } else {
                                echo 'Stripe'; // Default for paid invoices
                            }
                            ?>
                        </span>
                    </div>
                    <?php if ($payment_date): ?>
                    <div class="inv-info-card-row">
                        <span><?php echo get_phrase('payment_date'); ?>:</span>
                        <span><?php echo $payment_date; ?></span>
              </div>
                    <?php endif; ?>
                    <div class="inv-info-card-row">
                        <span><?php echo get_phrase('amount_received'); ?>:</span>
                        <span>
                            <?php 
                            if ($conversion_applied && $payment_amount_converted) {
                                echo number_format($payment_amount_converted, 2, ',', ' ') . ' ' . $payment_currency;
                            } else {
                                echo number_format($invoice_details['paid_amount'], 2, ',', ' ') . ' ' . $invoice_details['currency'];
                            }
                            ?>
                  </span>
                    </div>
              </div>
                <?php endif; ?>
          </div>
            <?php endif; ?>
            
            <!-- Actions -->
            <div class="inv-actions d-print-none">
                <a href="<?php echo site_url('admin/invoice'); ?>" class="inv-btn inv-btn-outline">
                    <i class="mdi mdi-arrow-left"></i> <?php echo get_phrase('back'); ?>
                </a>
                <a href="javascript:void(0);" onclick="rightModal('<?php echo site_url('modal/popup/invoice/edit/'.$invoice_details['id']); ?>', '<?php echo get_phrase('update_invoice'); ?>');" class="inv-btn inv-btn-outline">
                    <i class="mdi mdi-pencil"></i> <?php echo get_phrase('edit'); ?>
                </a>
                <a href="javascript:window.print()" class="inv-btn inv-btn-primary">
                    <i class="mdi mdi-printer"></i> <?php echo get_phrase('print'); ?>
                </a>
                <a href="<?php echo site_url('admin/invoice_pdf/'.$invoice_details['id']); ?>" class="inv-btn inv-btn-success">
                    <i class="mdi mdi-file-pdf-box"></i> <?php echo get_phrase('download'); ?> PDF
                </a>
            </div>
            
            </div>
          </div>
  </div>
