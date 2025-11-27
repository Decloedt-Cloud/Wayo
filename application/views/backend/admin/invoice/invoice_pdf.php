<?php
  $invoice_details   = $this->crud_model->get_invoice_by_id($invoice_id);
  $student_details   = $this->user_model->get_student_details_by_id('student', $invoice_details['student_id']);
  $school            = $this->db->get_where('schools', array('id' => $invoice_details['school_id']))->row_array();

  // --------- TVA / VAT CALCULATION ---------
  $settings_school = $this->settings_model->get_settings_school_data($invoice_details['school_id']);

  $vat_applicable = isset($settings_school['vat']) && (int)$settings_school['vat'] === 1;
  $tax_residence  = isset($settings_school['Tax_residence']) ? $settings_school['Tax_residence'] : null;

  $vat_rate = 0; // en pourcentage
  if ($vat_applicable) {
      if ($tax_residence === 'MA') {
          $vat_rate = 20;
      } elseif ($tax_residence === 'UAE') {
          $vat_rate = 5;
      }
  }

  $sub_total   = (float)$invoice_details['total_amount'];
  $vat_amount  = $sub_total * ($vat_rate / 100);
  $grand_total = $sub_total + $vat_amount;
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Invoice <?php echo sprintf('%08d', $invoice_details['id']); ?></title>
  <style>
    body {
      font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
      font-size: 12px;
      color: #333;
    }
    .invoice-container {
      width: 100%;
      padding: 10px 20px;
    }
    .header {
      margin-bottom: 20px;
    }
    .logo {
      float: left;
    }
    .invoice-meta {
      float: right;
      text-align: right;
      font-size: 11px;
    }
    .clearfix::after {
      content: "";
      display: table;
      clear: both;
    }
    h2, h4 {
      margin: 2px 0;
    }
    .section-title {
      font-weight: bold;
      margin-top: 20px;
      margin-bottom: 5px;
      font-size: 13px;
    }
    .details-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
      font-size: 11px;
    }
    .details-table th,
    .details-table td {
      border: 1px solid #ddd;
      padding: 6px 8px;
    }
    .details-table th {
      background-color: #f2f2f2;
      text-align: left;
    }
    .totals {
      width: 40%;
      float: right;
      border: 1px solid #ddd;
      font-size: 11px;
    }
    .totals td {
      padding: 6px 8px;
      border-bottom: 1px solid #eee;
    }
    .totals tr:last-child td {
      border-bottom: none;
    }
    .totals-label {
      font-weight: bold;
      background-color: #f8f8f8;
    }
    .notes {
      margin-top: 30px;
      font-size: 10px;
    }
  </style>
</head>
<body>
  <div class="invoice-container">
    <div class="header clearfix">
      <div class="logo">
        <img src="<?php echo $this->settings_model->get_logo_dark(); ?>" alt="Logo" height="40">
        <h4><?php echo $school['name']; ?></h4>
        <div><?php echo $school['Rue'].' '.$school['Numero']; ?></div>
        <div><?php echo $school['Codepostal'].' '.$school['Ville']; ?></div>
      </div>
      <div class="invoice-meta">
        <h2><?php echo get_phrase('invoice'); ?></h2>
        <div><strong><?php echo get_phrase('invoice_no'); ?> :</strong> <?php echo sprintf('%08d', $invoice_details['id']); ?></div>
        <div><strong><?php echo get_phrase('date'); ?> :</strong> <?php echo date('d/m/Y'); ?></div>
        <div><strong><?php echo get_phrase('status'); ?> :</strong> <?php echo ucfirst($invoice_details['status']); ?></div>
      </div>
    </div>

    <div class="section-title"><?php echo get_phrase('billing_details').' '.get_phrase('membre'); ?></div>
    <p>
      <strong><?php echo $student_details['name']; ?></strong><br>
      <?php echo get_phrase('Rue').' : '.$student_details['Rue']; ?><br>
      <?php echo get_phrase('Numero').' : '.$student_details['Numero']; ?><br>
      <?php echo get_phrase('Codepostal').' : '.$student_details['Codepostal'].' '.$student_details['Ville']; ?><br>
      <?php echo get_phrase('Phone').' : '.$student_details['phone']; ?>
    </p>

    <div class="section-title"><?php echo get_phrase('invoice_details'); ?></div>
    <table class="details-table">
      <thead>
        <tr>
          <th><?php echo get_phrase('product_or_service'); ?></th>
          <th><?php echo get_phrase('price_excl_vat'); ?></th>
          <th><?php echo get_phrase('vat'); ?></th>
          <th><?php echo get_phrase('total_amount'); ?> TTC</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><?php echo $invoice_details['title']; ?></td>
          <td><?php echo currency($sub_total); ?></td>
          <td>
            <?php echo $vat_rate > 0 ? $vat_rate.'%' : '-'; ?>
          </td>
          <td><?php echo currency($grand_total); ?></td>
        </tr>
      </tbody>
    </table>

    <table class="totals">
      <tr>
        <td class="totals-label"><?php echo get_phrase('sub_total'); ?></td>
        <td style="text-align:right;"><?php echo currency($sub_total); ?></td>
      </tr>
      <tr>
        <td class="totals-label">
          <?php echo get_phrase('vat_total'); ?>
          <?php echo $vat_rate > 0 ? '(' . $vat_rate . '%)' : ''; ?>
        </td>
        <td style="text-align:right;"><?php echo currency($vat_amount); ?></td>
      </tr>
      <tr>
        <td class="totals-label"><?php echo get_phrase('grand_total'); ?></td>
        <td style="text-align:right;"><?php echo currency($grand_total); ?></td>
      </tr>
    </table>

    <div class="clearfix"></div>

    <div class="notes">
      <strong><?php echo get_phrase('notes'); ?> :</strong><br>
      - <?php echo get_phrase('payment_done_via_gateway'); ?><br>
      - <?php echo get_phrase('no_additional_payment_required'); ?><br>
      - <?php echo get_phrase('thank_you_for_your_trust'); ?>
    </div>
  </div>
</body>
</html>


