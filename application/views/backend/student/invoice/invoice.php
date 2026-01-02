<?php
/**
 * Student Invoice View - Professional Edition
 * Modern single invoice display for students
 */

$invoice_details = $this->crud_model->get_invoice_by_id($invoice_id);
$student_details = $this->user_model->get_student_details_by_id('student', $invoice_details['student_id']);
$school = $this->db->get_where('schools', array('id' => $invoice_details['school_id']))->row_array();
  $settings_school = $this->settings_model->get_settings_school_data($invoice_details['school_id']);

// Class details
$class_details = !empty($invoice_details['class_id']) 
    ? $this->crud_model->get_class_details_by_id($invoice_details['class_id'])->row_array() 
    : null;

// VAT Calculation
  $vat_applicable = isset($settings_school['vat']) && (int)$settings_school['vat'] === 1;
// Utiliser country depuis schools table (source unique de vérité)
$tax_residence = isset($school['country']) ? strtoupper($school['country']) : null;

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
/* Student Invoice View Pro Styles */
:root {
    --sinv-primary: #4f46e5;
    --sinv-success: #059669;
    --sinv-danger: #dc2626;
    --sinv-warning: #d97706;
    --sinv-dark: #1e293b;
    --sinv-gray: #64748b;
    --sinv-light: #f8fafc;
    --sinv-border: #e2e8f0;
}

.sinv-container {
    max-width: 900px;
    margin: 0 auto;
}

.sinv-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    overflow: hidden;
    border: 1px solid var(--sinv-border);
}

/* Header */
.sinv-header {
    background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
    padding: 1.5rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.sinv-header-left {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.sinv-logo {
    height: 40px;
    max-width: 120px;
    object-fit: contain;
    filter: brightness(0) invert(1);
}

.sinv-company {
    color: white;
}

.sinv-company-name {
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
}

.sinv-company-address {
    font-size: 0.8rem;
    color: #94a3b8;
}

.sinv-header-right {
    text-align: right;
}

.sinv-title {
    font-size: 1.75rem;
    font-weight: 800;
    color: white;
    letter-spacing: 2px;
    margin-bottom: 0.25rem;
}

.sinv-number {
    font-size: 1rem;
    color: #94a3b8;
    font-family: 'SF Mono', monospace;
}

/* Status Banner */
.sinv-status-banner {
    padding: 0.75rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.sinv-status-banner.paid {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
}

.sinv-status-banner.unpaid {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
}

.sinv-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 700;
    font-size: 0.9rem;
}

.sinv-status-banner.paid .sinv-status-badge {
    color: #065f46;
}

.sinv-status-banner.unpaid .sinv-status-badge {
    color: #991b1b;
}

.sinv-status-badge i {
    font-size: 1.25rem;
}

.sinv-status-date {
    font-size: 0.8rem;
    color: var(--sinv-gray);
}

/* Body */
.sinv-body {
    padding: 2rem;
}

/* Info Grid */
.sinv-info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
    margin-bottom: 2rem;
}

@media (max-width: 768px) {
    .sinv-info-grid { grid-template-columns: 1fr; }
}

.sinv-info-box {
    background: var(--sinv-light);
    border-radius: 12px;
    padding: 1.25rem;
    border: 1px solid var(--sinv-border);
}

.sinv-info-box.client {
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    border-color: #bfdbfe;
}

.sinv-info-label {
    font-size: 0.65rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: var(--sinv-gray);
    margin-bottom: 0.75rem;
    font-weight: 600;
}

.sinv-info-name {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--sinv-dark);
    margin-bottom: 0.5rem;
}

.sinv-info-details {
    font-size: 0.85rem;
    color: var(--sinv-gray);
    line-height: 1.6;
}

.sinv-info-details a {
    color: var(--sinv-primary);
    text-decoration: none;
}

/* Meta Cards */
.sinv-meta-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
    margin-bottom: 2rem;
}

@media (max-width: 768px) {
    .sinv-meta-row { grid-template-columns: repeat(2, 1fr); }
}

.sinv-meta-card {
    background: white;
    border: 1px solid var(--sinv-border);
    border-radius: 10px;
    padding: 1rem;
    text-align: center;
}

.sinv-meta-card.highlight {
    background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
    border: none;
}

.sinv-meta-card.highlight .sinv-meta-label,
.sinv-meta-card.highlight .sinv-meta-value {
    color: white;
}

.sinv-meta-label {
    font-size: 0.65rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--sinv-gray);
    margin-bottom: 0.5rem;
}

.sinv-meta-value {
    font-size: 1rem;
    font-weight: 700;
    color: var(--sinv-dark);
}

/* Items Table */
.sinv-table-wrap {
    background: white;
    border-radius: 12px;
    border: 1px solid var(--sinv-border);
    overflow: hidden;
    margin-bottom: 1.5rem;
}

.sinv-table {
    width: 100%;
    border-collapse: collapse;
}

.sinv-table thead {
    background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
}

.sinv-table thead th {
    padding: 1rem;
    text-align: left;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--sinv-gray);
    font-weight: 600;
    border-bottom: 2px solid var(--sinv-border);
}

.sinv-table thead th:last-child {
    text-align: right;
}

.sinv-table tbody td {
    padding: 1.25rem 1rem;
    vertical-align: top;
    border-bottom: 1px solid var(--sinv-border);
}

.sinv-table tbody td:last-child {
    text-align: right;
}

.sinv-table tbody tr:last-child td {
    border-bottom: none;
}

.sinv-item-name {
    font-weight: 600;
    color: var(--sinv-dark);
    margin-bottom: 0.25rem;
}

.sinv-item-desc {
    font-size: 0.8rem;
    color: var(--sinv-gray);
}

.sinv-item-class {
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
.sinv-totals-section {
    display: flex;
    justify-content: flex-end;
    margin-bottom: 2rem;
}

.sinv-totals-box {
    width: 320px;
    background: var(--sinv-light);
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid var(--sinv-border);
}

.sinv-totals-row {
    display: flex;
    justify-content: space-between;
    padding: 0.85rem 1.25rem;
    border-bottom: 1px solid var(--sinv-border);
}

.sinv-totals-row:last-child {
    border-bottom: none;
}

.sinv-totals-row.grand {
    background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
}

.sinv-totals-label {
    font-size: 0.85rem;
    color: var(--sinv-gray);
}

.sinv-totals-row.grand .sinv-totals-label {
    color: #94a3b8;
    font-weight: 600;
}

.sinv-totals-value {
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--sinv-dark);
}

.sinv-totals-row.grand .sinv-totals-value {
    color: white;
    font-size: 1.1rem;
}

.sinv-totals-row.due {
    background: #fef2f2;
}

.sinv-totals-row.due .sinv-totals-label {
    color: #991b1b;
}

.sinv-totals-row.due .sinv-totals-value {
    color: #dc2626;
    font-size: 1.1rem;
}

/* FX & Payment Info */
.sinv-info-cards {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
    margin-bottom: 2rem;
}

@media (max-width: 768px) {
    .sinv-info-cards { grid-template-columns: 1fr; }
}

.sinv-info-card {
    border-radius: 12px;
    padding: 1.25rem;
}

.sinv-info-card.fx {
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    border: 1px solid #bfdbfe;
}

.sinv-info-card.payment {
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    border: 1px solid #bbf7d0;
}

.sinv-info-card-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.85rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.sinv-info-card.fx .sinv-info-card-title {
    color: #1d4ed8;
}

.sinv-info-card.payment .sinv-info-card-title {
    color: #15803d;
}

.sinv-info-card-row {
    display: flex;
    justify-content: space-between;
    padding: 0.4rem 0;
    font-size: 0.85rem;
}

.sinv-info-card.fx .sinv-info-card-row {
    color: #1e40af;
}

.sinv-info-card.payment .sinv-info-card-row {
    color: #166534;
}

.sinv-info-card-row span:last-child {
    font-weight: 600;
}

/* Pay Now Button */
.sinv-pay-section {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    border: 1px solid #fbbf24;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    text-align: center;
}

.sinv-pay-title {
    font-size: 1rem;
    font-weight: 700;
    color: #92400e;
    margin-bottom: 0.5rem;
}

.sinv-pay-amount {
    font-size: 1.75rem;
    font-weight: 800;
    color: #78350f;
    margin-bottom: 1rem;
}

.sinv-pay-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 2rem;
    background: linear-gradient(135deg, #059669 0%, #10b981 100%);
    color: white;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.2s;
    border: none;
    cursor: pointer;
}

.sinv-pay-btn:hover {
    background: linear-gradient(135deg, #047857 0%, #059669 100%);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(5, 150, 105, 0.4);
}

/* Actions */
.sinv-actions {
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
    padding-top: 1.5rem;
    border-top: 1px solid var(--sinv-border);
    flex-wrap: wrap;
}

.sinv-btn {
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

.sinv-btn-primary {
    background: var(--sinv-primary);
    color: white;
}

.sinv-btn-primary:hover {
    background: #4338ca;
    color: white;
}

.sinv-btn-success {
    background: var(--sinv-success);
    color: white;
}

.sinv-btn-success:hover {
    background: #047857;
    color: white;
}

.sinv-btn-outline {
    background: white;
    color: var(--sinv-gray);
    border: 1px solid var(--sinv-border);
}

.sinv-btn-outline:hover {
    background: var(--sinv-light);
    color: var(--sinv-dark);
}

/* Print Styles */
@media print {
    body * { visibility: hidden; }
    .sinv-container, .sinv-container * { visibility: visible; }
    .sinv-container {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        max-width: 100%;
    }
    .sinv-actions, .sinv-pay-section { display: none !important; }
    .sinv-card { box-shadow: none; border: 1px solid #ddd; }
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

<div class="sinv-container">
    <div class="sinv-card">
        
        <!-- Header -->
        <div class="sinv-header">
            <div class="sinv-header-left">
                <?php if (!empty($school_logo)): ?>
                    <img src="<?php echo $school_logo; ?>" alt="" class="sinv-logo">
                <?php endif; ?>
                <div class="sinv-company">
                    <div class="sinv-company-name"><?php echo htmlspecialchars($school['name']); ?></div>
                    <div class="sinv-company-address">
                        <?php if (!empty($school['Rue'])): ?>
                            <?php echo htmlspecialchars($school['Rue']); ?> <?php echo htmlspecialchars($school['Numero']); ?>,
                        <?php endif; ?>
                        <?php echo htmlspecialchars($school['Codepostal']); ?> <?php echo htmlspecialchars($school['Ville']); ?>
                    </div>
                </div>
            </div>
            <div class="sinv-header-right">
                <div class="sinv-title">FACTURE</div>
                <div class="sinv-number">#<?php echo sprintf('%08d', $invoice_details['id']); ?></div>
          </div>
        </div>

        <!-- Status Banner -->
        <div class="sinv-status-banner <?php echo $is_paid ? 'paid' : 'unpaid'; ?>">
            <span class="sinv-status-badge">
                <i class="mdi <?php echo $is_paid ? 'mdi-check-circle' : 'mdi-clock-alert'; ?>"></i>
                <?php echo $is_paid ? get_phrase('paid') : get_phrase('unpaid'); ?>
            </span>
            <span class="sinv-status-date">
                <?php if ($is_paid && $payment_date): ?>
                    <?php echo get_phrase('paid_on'); ?> <?php echo $payment_date; ?>
                <?php else: ?>
                    <?php echo get_phrase('created_on'); ?> <?php echo $invoice_date; ?>
                <?php endif; ?>
            </span>
            </div>

        <!-- Body -->
        <div class="sinv-body">
            
            <!-- Billing Info -->
            <div class="sinv-info-grid">
                <div class="sinv-info-box">
                    <div class="sinv-info-label"><?php echo get_phrase('from'); ?></div>
                    <div class="sinv-info-name"><?php echo htmlspecialchars($school['name']); ?></div>
                    <div class="sinv-info-details">
                        <?php if (!empty($school['Rue'])): ?>
                            <?php echo htmlspecialchars($school['Rue']); ?> <?php echo htmlspecialchars($school['Numero']); ?><br>
                        <?php endif; ?>
                        <?php echo htmlspecialchars($school['Codepostal']); ?> <?php echo htmlspecialchars($school['Ville']); ?>
                        <?php if (!empty($school['num_vat'])): ?>
                            <br>TVA: <?php echo htmlspecialchars($school['num_vat']); ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="sinv-info-box client">
                    <div class="sinv-info-label"><?php echo get_phrase('bill_to'); ?></div>
                    <div class="sinv-info-name"><?php echo htmlspecialchars($student_details['name']); ?></div>
                    <div class="sinv-info-details">
                        <?php if (!empty($student_details['Rue'])): ?>
                            <?php echo htmlspecialchars($student_details['Rue']); ?> <?php echo htmlspecialchars($student_details['Numero']); ?><br>
                        <?php endif; ?>
                        <?php if (!empty($student_details['Codepostal']) || !empty($student_details['Ville'])): ?>
                            <?php echo htmlspecialchars($student_details['Codepostal']); ?> <?php echo htmlspecialchars($student_details['Ville']); ?><br>
                        <?php endif; ?>
                        <?php if (!empty($student_details['email'])): ?>
                            <?php echo htmlspecialchars($student_details['email']); ?><br>
                        <?php endif; ?>
                        <?php if (!empty($student_details['phone'])): ?>
                            <?php echo htmlspecialchars($student_details['phone']); ?>
                <?php endif; ?>
            </div>
                </div>
        </div>
            
            <!-- Meta Row -->
            <div class="sinv-meta-row">
                <div class="sinv-meta-card">
                    <div class="sinv-meta-label"><?php echo get_phrase('invoice_no'); ?></div>
                    <div class="sinv-meta-value"><?php echo sprintf('%08d', $invoice_details['id']); ?></div>
                </div>
                <div class="sinv-meta-card">
                    <div class="sinv-meta-label"><?php echo get_phrase('date'); ?></div>
                    <div class="sinv-meta-value"><?php echo $invoice_date; ?></div>
                </div>
                <div class="sinv-meta-card">
                    <div class="sinv-meta-label"><?php echo get_phrase('method'); ?></div>
                    <div class="sinv-meta-value"><?php echo !empty($invoice_details['payment_method']) ? ucfirst($invoice_details['payment_method']) : '—'; ?></div>
                </div>
                <div class="sinv-meta-card highlight">
                    <div class="sinv-meta-label"><?php echo get_phrase('total'); ?></div>
                    <div class="sinv-meta-value"><?php echo number_format($grand_total, 2, ',', ' '); ?> <?php echo $invoice_details['currency']; ?></div>
        </div>
        </div>
            
            <!-- Items Table -->
            <div class="sinv-table-wrap">
                <table class="sinv-table">
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
                                <div class="sinv-item-name"><?php echo htmlspecialchars($invoice_details['title']); ?></div>
                                <div class="sinv-item-desc"><?php echo get_phrase('created_at'); ?>: <?php echo date('d/m/Y H:i', $invoice_details['created_at']); ?></div>
                                <?php if ($class_details): ?>
                                    <span class="sinv-item-class">
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
            <div class="sinv-totals-section">
                <div class="sinv-totals-box">
                    <div class="sinv-totals-row">
                        <span class="sinv-totals-label"><?php echo get_phrase('sub_total'); ?> HT</span>
                        <span class="sinv-totals-value"><?php echo number_format($sub_total, 2, ',', ' '); ?> <?php echo $invoice_details['currency']; ?></span>
                    </div>
                    <div class="sinv-totals-row">
                        <span class="sinv-totals-label"><?php echo get_phrase('vat'); ?> <?php echo $vat_rate > 0 ? '('.$vat_rate.'%)' : '(0%)'; ?></span>
                        <span class="sinv-totals-value"><?php echo number_format($vat_amount, 2, ',', ' '); ?> <?php echo $invoice_details['currency']; ?></span>
                    </div>
                    <div class="sinv-totals-row grand">
                        <span class="sinv-totals-label"><?php echo get_phrase('total'); ?> TTC</span>
                        <span class="sinv-totals-value"><?php echo number_format($grand_total, 2, ',', ' '); ?> <?php echo $invoice_details['currency']; ?></span>
                    </div>
                    <?php if (!$is_paid && $due_amount > 0): ?>
                    <div class="sinv-totals-row due">
                        <span class="sinv-totals-label"><?php echo get_phrase('due_amount'); ?></span>
                        <span class="sinv-totals-value"><?php echo number_format($due_amount, 2, ',', ' '); ?> <?php echo $invoice_details['currency']; ?></span>
              </div>
                    <?php endif; ?>
              </div>
          </div>
            
            <!-- Pay Now Section (only if unpaid) -->
            <?php if (!$is_paid && $due_amount > 0): ?>
            <div class="sinv-pay-section d-print-none">
                <div class="sinv-pay-title"><?php echo get_phrase('amount_due'); ?></div>
                <div class="sinv-pay-amount"><?php echo number_format($due_amount, 2, ',', ' '); ?> <?php echo $invoice_details['currency']; ?></div>
                <a href="<?php echo site_url('student/payment_gateway/invoice/'.$invoice_details['id']); ?>" class="sinv-pay-btn">
                    <i class="mdi mdi-credit-card"></i>
                    <?php echo get_phrase('pay_now'); ?>
              </a>
            </div>
            <?php endif; ?>
            
            <!-- FX & Payment Info -->
            <?php if ($is_paid || ($conversion_applied && $payment_currency)): ?>
            <div class="sinv-info-cards">
                <?php if ($conversion_applied && $payment_currency && $payment_amount_converted): ?>
                <div class="sinv-info-card fx">
                    <div class="sinv-info-card-title">
                        <i class="mdi mdi-swap-horizontal-circle"></i>
                        <?php echo get_phrase('currency_conversion'); ?>
                    </div>
                    <div class="sinv-info-card-row">
                        <span><?php echo get_phrase('invoiced_amount'); ?>:</span>
                        <span><?php echo number_format($grand_total, 2, ',', ' '); ?> <?php echo $invoice_details['currency']; ?></span>
                    </div>
                    <div class="sinv-info-card-row">
                        <span><?php echo get_phrase('charged_amount'); ?>:</span>
                        <span><?php echo number_format($payment_amount_converted, 2, ',', ' '); ?> <?php echo $payment_currency; ?></span>
                    </div>
                    <?php if ($fx_rate): ?>
                    <div class="sinv-info-card-row">
                        <span><?php echo get_phrase('exchange_rate'); ?>:</span>
                        <span>1 <?php echo $invoice_details['currency']; ?> = <?php echo number_format($fx_rate, 4); ?> <?php echo $payment_currency; ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($fx_rate_date): ?>
                    <div class="sinv-info-card-row">
                        <span><?php echo get_phrase('rate_date'); ?>:</span>
                        <span><?php echo date('d/m/Y', strtotime($fx_rate_date)); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <?php if ($is_paid): ?>
                <div class="sinv-info-card payment">
                    <div class="sinv-info-card-title">
                        <i class="mdi mdi-check-decagram"></i>
                        <?php echo get_phrase('payment_confirmed'); ?>
                    </div>
                    <div class="sinv-info-card-row">
                        <span><?php echo get_phrase('method'); ?>:</span>
                        <span><?php echo ucfirst($invoice_details['payment_method']); ?></span>
                    </div>
                    <?php if ($payment_date): ?>
                    <div class="sinv-info-card-row">
                        <span><?php echo get_phrase('payment_date'); ?>:</span>
                        <span><?php echo $payment_date; ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="sinv-info-card-row">
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
            <div class="sinv-actions d-print-none">
                <a href="<?php echo site_url('student/invoice'); ?>" class="sinv-btn sinv-btn-outline">
                    <i class="mdi mdi-arrow-left"></i> <?php echo get_phrase('back'); ?>
                </a>
                <a href="javascript:window.print()" class="sinv-btn sinv-btn-primary">
                    <i class="mdi mdi-printer"></i> <?php echo get_phrase('print'); ?>
                </a>
                <a href="<?php echo site_url('student/invoice_pdf/'.$invoice_details['id']); ?>" class="sinv-btn sinv-btn-success">
                    <i class="mdi mdi-file-pdf-box"></i> <?php echo get_phrase('download'); ?> PDF
                </a>
  </div>

        </div>
    </div>
</div>
