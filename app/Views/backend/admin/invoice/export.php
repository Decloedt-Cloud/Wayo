<?php
  if ($action == 'pdf') {
    $action = get_phrase('export_pdf');
  }else{
    $action = get_phrase($action);
  }
  if ($selected_class == 'all') {
    $classNameForTitle = get_phrase('all_class');
  }else{
    $class_details = $this->crud_model->get_classes($selected_class);
    $classNameForTitle = $class_details['name'];
  }
  if ($selected_status == 'all') {
    $selectedStatusForTitle = get_phrase('all');
  }else{
    $selectedStatusForTitle = ucfirst($selected_status);
  }
  
  // Récupérer les factures et calculer les totaux
  $invoices = $this->crud_model->get_invoice_by_date_range($date_from, $date_to, $selected_class, $selected_status);
  $total_invoices = count($invoices);
  $total_amount = array_sum(array_column($invoices, 'total_amount'));
  $total_paid = array_sum(array_column($invoices, 'paid_amount'));
  $total_unpaid = $total_amount - $total_paid;
  
  // Récupérer les informations de l'école
  $school_details = $this->settings_model->get_current_school_data();
  // Récupérer les paramètres système
  $system_settings = get_settings();
  
  // Date d'export
  $export_date = date('d-M-Y H:i:s');
?>
<!DOCTYPE html>
<html>
<head>
  <title><?php echo $action.' '.get_phrase('student_fee_report'); ?></title>
  <link rel="shortcut icon" href="<?php echo $this->settings_model->get_favicon(); ?>">
  <style>
    /* Styles globaux */
    body {
      font-family: Arial, sans-serif;
      font-size: 11pt;
      color: #333;
      line-height: 1.6;
    }
    
    /* Conteneur principal */
    .container {
      max-width: 1000px;
      margin: 0 auto;
      padding: 20px;
    }
    
    /* En-tête de la page */
    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 30px;
      padding-bottom: 15px;
      border-bottom: 2px solid #2c3e50;
    }
    
    .school-info {
      flex: 1;
    }
    
    .school-logo {
      width: 100px;
      height: auto;
    }
    
    .school-name {
      font-size: 18pt;
      font-weight: bold;
      color: #2c3e50;
      margin: 0;
    }
    
    .school-address {
      font-size: 10pt;
      color: #666;
      margin: 5px 0;
    }
    
    /* Titre du rapport */
    .report-title {
      text-align: center;
      margin-bottom: 20px;
    }
    
    .report-title h2 {
      font-size: 16pt;
      color: #2c3e50;
      margin: 0;
      font-weight: bold;
    }
    
    /* Filtres */
    .filters {
      background-color: #f8f9fa;
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 20px;
      border: 1px solid #e9ecef;
    }
    
    .filters p {
      margin: 5px 0;
      font-size: 10pt;
    }
    
    /* Tableau */
    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
      font-size: 10pt;
    }
    
    th {
      background-color: #3498db;
      color: white;
      padding: 12px 8px;
      text-align: left;
      font-weight: bold;
      border: 1px solid #2980b9;
    }
    
    td {
      padding: 10px 8px;
      border: 1px solid #ddd;
    }
    
    /* Style zébré pour les lignes */
    tbody tr:nth-child(even) {
      background-color: #f8f9fa;
    }
    
    /* Couleurs de statut */
    .status-paid {
      color: #27ae60;
      font-weight: bold;
    }
    
    .status-unpaid {
      color: #e74c3c;
      font-weight: bold;
    }
    
    /* Alignement des montants */
    .amount {
      text-align: right;
      font-weight: bold;
    }
    
    /* Section résumé */
    .summary {
      background-color: #f8f9fa;
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 20px;
      border: 1px solid #e9ecef;
    }
    
    .summary h3 {
      font-size: 12pt;
      color: #2c3e50;
      margin-top: 0;
      margin-bottom: 10px;
    }
    
    .summary-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 10px;
    }
    
    .summary-item {
      display: flex;
      justify-content: space-between;
      font-size: 10pt;
    }
    
    .summary-label {
      font-weight: bold;
    }
    
    .summary-value {
      font-weight: bold;
      color: #2c3e50;
    }
    
    /* Pied de page */
    .footer {
      text-align: center;
      padding-top: 15px;
      border-top: 1px solid #ddd;
      font-size: 9pt;
      color: #666;
      margin-top: 30px;
    }
  </style>
</head>
<body>
  <div class="container">
    <!-- En-tête -->
    <div class="header">
      <div class="school-info">
        <h1 class="school-name"><?php echo isset($school_details['name']) ? $school_details['name'] : get_phrase('school_management_system'); ?></h1>
        <p class="school-address">
          <?php echo isset($school_details['Rue']) ? $school_details['Rue'] : ''; ?>
          <?php echo isset($school_details['Numero']) ? ' ' . $school_details['Numero'] : ''; ?><br>
          <?php echo isset($school_details['Codepostal']) ? $school_details['Codepostal'] : ''; ?> <?php echo isset($school_details['Ville']) ? $school_details['Ville'] : ''; ?><br>
          <?php echo isset($school_details['phone']) ? $school_details['phone'] : ''; ?>
        </p>
      </div>
      <?php 
        // Vérifier si le logo de l'école existe
        $school_logo_path = 'uploads/schools/' . $school_details['id'] . '.jpg';
        if (file_exists(FCPATH . $school_logo_path)): 
      ?>
        <img src="<?php echo base_url($school_logo_path); ?>" alt="School Logo" class="school-logo">
      <?php endif; ?>
    </div>
    
    <!-- Titre du rapport -->
    <div class="report-title">
      <h2><?php echo get_phrase('student_fee_report'); ?></h2>
    </div>
    
    <!-- Filtres d'export -->
    <div class="filters">
      <p><strong><?php echo get_phrase('date_range'); ?>:</strong> <?php echo date('d-M-Y', $date_from) . ' ' . get_phrase('to') . ' ' . date('d-M-Y', $date_to); ?></p>
      <p><strong><?php echo get_phrase('class'); ?>:</strong> <?php echo $classNameForTitle; ?></p>
      <p><strong><?php echo get_phrase('status'); ?>:</strong> <?php echo $selectedStatusForTitle; ?></p>
      <p><strong><?php echo get_phrase('export_date'); ?>:</strong> <?php echo $export_date; ?></p>
    </div>
    
    <!-- Résumé des données -->
    <div class="summary">
      <h3><?php echo get_phrase('summary'); ?></h3>
      <div class="summary-grid">
        <div class="summary-item">
          <span class="summary-label"><?php echo get_phrase('total_invoices'); ?>:</span>
          <span class="summary-value"><?php echo $total_invoices; ?></span>
        </div>
        <div class="summary-item">
          <span class="summary-label"><?php echo get_phrase('total_amount'); ?>:</span>
          <span class="summary-value amount"><?php echo currency($total_amount); ?></span>
        </div>
        <div class="summary-item">
          <span class="summary-label"><?php echo get_phrase('total_paid'); ?>:</span>
          <span class="summary-value amount status-paid"><?php echo currency($total_paid); ?></span>
        </div>
        <div class="summary-item">
          <span class="summary-label"><?php echo get_phrase('total_unpaid'); ?>:</span>
          <span class="summary-value amount status-unpaid"><?php echo currency($total_unpaid); ?></span>
        </div>
      </div>
    </div>
    
    <!-- Tableau des factures -->
    <table>
      <thead>
        <tr>
          <th><?php echo get_phrase('invoice_no'); ?></th>
          <th><?php echo get_phrase('student'); ?></th>
          <th><?php echo get_phrase('class'); ?></th>
          <th><?php echo get_phrase('invoice_title'); ?></th>
          <th><?php echo get_phrase('total_amount'); ?></th>
          <th><?php echo get_phrase('paid_amount'); ?></th>
          <th><?php echo get_phrase('creation_date'); ?></th>
          <th><?php echo get_phrase('payment_date'); ?></th>
          <th><?php echo get_phrase('status'); ?></th>
        </tr>
      </thead>
      <tbody>
        <?php if ($total_invoices > 0): ?>
          <?php foreach ($invoices as $invoice):
            $student_details = $this->user_model->get_student_details_by_id('student', $invoice['student_id']);
            $class_details = $this->crud_model->get_class_details_by_id($invoice['class_id']);
            $status_class = $invoice['status'] == 'paid' ? 'status-paid' : 'status-unpaid';
          ?>
            <tr>
              <td> <?php echo sprintf('%08d', $invoice['id']); ?> </td>
              <td> <?php echo $student_details['name']; ?> </td>
              <td> <?php echo $class_details['name']; ?> </td>
              <td> <?php echo $invoice['title']; ?> </td>
              <td class="amount"> <?php echo currency($invoice['total_amount']); ?> </td>
              <td class="amount"> <?php echo currency($invoice['paid_amount']); ?> </td>
              <td> <?php echo date('d-M-Y', $invoice['created_at']); ?> </td>
              <td>
                <?php if ($invoice['updated_at'] > 0): ?>
                  <?php echo date('d-M-Y', $invoice['updated_at']); ?>
                <?php else: ?>
                  <?php echo get_phrase('not_available'); ?>
                <?php endif; ?>
              </td>
              <td class="<?php echo $status_class; ?>"> <?php echo ucfirst($invoice['status']); ?> </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="9" style="text-align: center; padding: 20px;"><?php echo get_phrase('no_invoices_found'); ?></td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
    
    <!-- Pied de page -->
    <div class="footer">
      <p><?php echo get_phrase('generated_by'); ?> <?php echo isset($school_details['name']) ? $school_details['name'] : get_phrase('school_management_system'); ?></p>
      <p><?php echo get_phrase('page'); ?> {PAGENO} <?php echo get_phrase('of'); ?> {nb}</p>
    </div>
  </div>
</body>
</html>
