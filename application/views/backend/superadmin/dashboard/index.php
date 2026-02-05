<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/main-responsive.min.css">

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
/* ========== MODERN DASHBOARD STYLES ========== */
.modern-dashboard {
  --primary: #6366f1;
  --primary-light: #818cf8;
  --secondary: #10b981;
  --accent: #f59e0b;
  --danger: #ef4444;
  --bg-main: #f8fafc;
  --bg-card: #ffffff;
  --text-dark: #1e293b;
  --text-muted: #64748b;
  --border-color: #e2e8f0;
  --shadow-sm: 0 1px 2px rgba(0,0,0,0.05);
  --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.07), 0 2px 4px -1px rgba(0,0,0,0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.08), 0 4px 6px -2px rgba(0,0,0,0.04);
  --shadow-xl: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
  
  font-family: 'DM Sans', sans-serif;
  background: var(--bg-main);
  min-height: 100vh;
  padding: 1.5rem;
  margin: -15px -15px 0 -15px;
}

/* Header */
.dash-header {
  margin-bottom: 2rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 1rem;
}

.dash-header h1 {
  font-family: 'Outfit', sans-serif;
  font-size: 1.875rem;
  font-weight: 700;
  color: var(--text-dark);
  margin: 0;
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.dash-header h1 .icon-box {
  width: 48px;
  height: 48px;
  background: linear-gradient(135deg, var(--primary), var(--primary-light));
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.25rem;
  box-shadow: 0 4px 14px rgba(99, 102, 241, 0.4);
}

.dash-header .date-badge {
  background: var(--bg-card);
  padding: 0.625rem 1rem;
  border-radius: 50px;
  font-size: 0.875rem;
  color: var(--text-muted);
  box-shadow: var(--shadow-sm);
  border: 1px solid var(--border-color);
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.dash-header .date-badge i {
  color: var(--primary);
}

/* Stats Grid */
.stats-row {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1.25rem;
  margin-bottom: 1.5rem;
}

@media (max-width: 768px) {
  .stats-row { grid-template-columns: 1fr; }
}

.stat-card {
  background: var(--bg-card);
  border-radius: 20px;
  padding: 1.5rem;
  position: relative;
  overflow: hidden;
  box-shadow: var(--shadow-md);
  border: 1px solid var(--border-color);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  cursor: pointer;
}

.stat-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-xl);
}

.stat-card::before {
  content: '';
  position: absolute;
  top: 0;
  right: 0;
  width: 120px;
  height: 120px;
  border-radius: 50%;
  opacity: 0.1;
  transform: translate(30%, -30%);
  transition: all 0.3s ease;
}

.stat-card:hover::before {
  transform: translate(20%, -20%) scale(1.2);
  opacity: 0.15;
}

.stat-card.schools::before { background: var(--primary); }
.stat-card.admins::before { background: var(--secondary); }

.stat-card .stat-icon {
  width: 52px;
  height: 52px;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.375rem;
  color: white;
  margin-bottom: 1.25rem;
  position: relative;
}

.stat-card.schools .stat-icon {
  background: linear-gradient(135deg, var(--primary), var(--primary-light));
  box-shadow: 0 4px 14px rgba(99, 102, 241, 0.35);
}

.stat-card.admins .stat-icon {
  background: linear-gradient(135deg, var(--secondary), #34d399);
  box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);
}

.stat-card .stat-label {
  font-size: 0.8125rem;
  font-weight: 500;
  color: var(--text-muted);
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 0.5rem;
}

.stat-card .stat-value {
  font-family: 'Outfit', sans-serif;
  font-size: 2.25rem;
  font-weight: 700;
  color: var(--text-dark);
  line-height: 1;
  margin-bottom: 0.5rem;
}

.stat-card .stat-desc {
  font-size: 0.8125rem;
  color: var(--text-muted);
}

.stat-card .stat-link {
  position: absolute;
  top: 1.25rem;
  right: 1.25rem;
  width: 32px;
  height: 32px;
  border-radius: 8px;
  background: var(--bg-main);
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--text-muted);
  text-decoration: none;
  transition: all 0.3s ease;
  opacity: 0;
}

.stat-card:hover .stat-link {
  opacity: 1;
}

.stat-card .stat-link:hover {
  background: var(--primary);
  color: white;
}

/* Content Grid */
.content-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.25rem;
  margin-bottom: 1.5rem;
}

@media (max-width: 992px) {
  .content-row { grid-template-columns: 1fr; }
}

/* Modern Cards */
.modern-card {
  background: var(--bg-card);
  border-radius: 20px;
  box-shadow: var(--shadow-md);
  border: 1px solid var(--border-color);
  overflow: hidden;
  transition: all 0.3s ease;
}

.modern-card:hover {
  box-shadow: var(--shadow-lg);
}

.modern-card-header {
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid var(--border-color);
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: linear-gradient(180deg, #fafbfc, transparent);
}

.modern-card-header h3 {
  font-family: 'Outfit', sans-serif;
  font-size: 1.0625rem;
  font-weight: 600;
  color: var(--text-dark);
  margin: 0;
  display: flex;
  align-items: center;
  gap: 0.625rem;
}

.modern-card-header h3 i {
  color: var(--primary);
  font-size: 1.125rem;
}

.modern-card-header .view-link {
  font-size: 0.8125rem;
  color: var(--primary);
  text-decoration: none;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 0.375rem;
  transition: all 0.2s ease;
}

.modern-card-header .view-link:hover {
  color: var(--primary-light);
  gap: 0.5rem;
}

.modern-card-body {
  padding: 1.5rem;
}

/* Modern Table */
.modern-table-wrap {
  max-height: 400px;
  overflow-y: auto;
}

.modern-table-wrap::-webkit-scrollbar {
  width: 4px;
}

.modern-table-wrap::-webkit-scrollbar-track {
  background: var(--bg-main);
}

.modern-table-wrap::-webkit-scrollbar-thumb {
  background: var(--border-color);
  border-radius: 2px;
}

.modern-table {
  width: 100%;
  border-collapse: collapse;
}

.modern-table thead th {
  background: var(--bg-main);
  padding: 0.875rem 1rem;
  font-size: 0.75rem;
  font-weight: 600;
  color: var(--text-muted);
  text-transform: uppercase;
  letter-spacing: 0.5px;
  text-align: left;
  position: sticky;
  top: 0;
  z-index: 10;
}

.modern-table tbody td {
  padding: 0.875rem 1rem;
  font-size: 0.875rem;
  color: var(--text-dark);
  border-bottom: 1px solid var(--border-color);
  vertical-align: middle;
}

.modern-table tbody tr:hover td {
  background: rgba(99, 102, 241, 0.04);
}

.modern-table tbody tr:last-child td {
  border-bottom: none;
}

.badge-status {
  display: inline-flex;
  align-items: center;
  gap: 0.375rem;
  padding: 0.375rem 0.75rem;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
}

.badge-status.paid {
  background: #dcfce7;
  color: #16a34a;
}

.badge-status.unpaid {
  background: #fee2e2;
  color: #dc2626;
}

.badge-status.partial {
  background: #fef3c7;
  color: #d97706;
}

.badge-status::before {
  content: '';
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: currentColor;
}

/* Animations */
.fade-up {
  animation: fadeUp 0.5s ease forwards;
  opacity: 0;
}

@keyframes fadeUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.delay-1 { animation-delay: 0.05s; }
.delay-2 { animation-delay: 0.1s; }
.delay-3 { animation-delay: 0.15s; }
.delay-4 { animation-delay: 0.2s; }
</style>

<div class="modern-dashboard">

  <!-- Header -->
  <div class="dash-header fade-up">
    <h1>
      <span class="icon-box"><i class="fas fa-home"></i></span>
      <?php echo get_phrase('dashboard'); ?>
    </h1>
    <div class="date-badge">
      <i class="far fa-calendar-alt"></i>
      <?php echo get_phrase(strtolower(date('l'))) . ', ' . date('j') . ' ' . get_phrase(strtolower(date('F'))) . ' ' . date('Y'); ?>
    </div>
  </div>

  <!-- Stats Row -->
  <div class="stats-row">
    
    <!-- Schools -->
    <div class="stat-card schools fade-up delay-1">
      <a href="<?php echo route('community_list'); ?>" class="stat-link">
        <i class="fas fa-arrow-right"></i>
      </a>
      <div class="stat-icon">
        <i class="fas fa-people-group"></i>
      </div>
      <div class="stat-label"><?php echo get_phrase('schools'); ?></div>
      <div class="stat-value" data-count="<?php echo $this->user_model->get_schools_count(); ?>">
        <?php echo $this->user_model->get_schools_count(); ?>
      </div>
      <div class="stat-desc"><?php echo get_phrase('total_number_of_communities'); ?></div>
    </div>

    <!-- Admins -->
    <div class="stat-card admins fade-up delay-2">
      <a href="<?php echo route('admin'); ?>" class="stat-link">
        <i class="fas fa-arrow-right"></i>
      </a>
      <div class="stat-icon">
        <i class="fas fa-user-shield"></i>
      </div>
      <div class="stat-label"><?php echo get_phrase('admins'); ?></div>
      <div class="stat-value" data-count="<?php echo $this->user_model->get_all_admins_count(); ?>">
        <?php echo $this->user_model->get_all_admins_count(); ?>
      </div>
      <div class="stat-desc"><?php echo get_phrase('total_number_of_admins'); ?></div>
    </div>

  </div>

  <!-- Content Row -->
  <div class="content-row">
    
    <!-- Invoices -->
    <div class="modern-card fade-up delay-3">
      <div class="modern-card-header">
        <h3><i class="fas fa-file-invoice-dollar"></i> <?php echo get_phrase('accounts_of'); ?> <?php echo get_phrase(strtolower(date('F'))); ?></h3>
        <a href="<?php echo route('invoice'); ?>" class="view-link">
          <?php echo get_phrase('view_all'); ?> <i class="fas fa-chevron-right"></i>
        </a>
      </div>
      <div class="modern-card-body" style="padding: 0;">
        <div class="modern-table-wrap">
          <?php
          $date_from = strtotime(date('Y-m-01')." 00:00:00");
          $date_to = strtotime(date('Y-m-t')." 23:59:59");
          $invoices = $this->crud_model->get_invoice_by_date_range($date_from, $date_to, 'all', 'all')->result_array();
          ?>
          <?php if (count($invoices) > 0): ?>
            <table class="modern-table">
              <thead>
                <tr>
                  <th><?php echo get_phrase('student'); ?></th>
                  <th><?php echo get_phrase('class'); ?></th>
                  <th><?php echo get_phrase('title'); ?></th>
                  <th><?php echo get_phrase('total'); ?></th>
                  <th><?php echo get_phrase('paid'); ?></th>
                  <th><?php echo get_phrase('status'); ?></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($invoices as $invoice): ?>
                  <tr>
                    <td>
                      <?php
                      $student_details = $this->user_model->get_student_details_by_id('student', $invoice['student_id']);
                      echo !empty($student_details) ? $student_details['name'] : 'N/A';
                      ?>
                    </td>
                    <td>
                       <?php
                        $class = $this->crud_model->get_classes($invoice['class_id'])->row_array();
                        echo isset($class['name']) ? $class['name'] : '';
                       ?>
                    </td>
                    <td><?php echo $invoice['title']; ?></td>
                    <td><strong><?php echo currency($invoice['total_amount']); ?></strong></td>
                    <td><?php echo currency($invoice['paid_amount']); ?></td>
                    <td>
                      <?php
                      $status = strtolower($invoice['status']);
                      $status_class = $status == 'paid' ? 'paid' : 'unpaid';
                      ?>
                      <span class="badge-status <?php echo $status_class; ?>">
                        <?php echo ucfirst($invoice['status']); ?>
                      </span>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php else: ?>
            <div class="p-4 text-center text-muted">
              <i class="far fa-folder-open fa-2x mb-3"></i>
              <p><?php echo get_phrase('no_data_found'); ?></p>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Expenses -->
    <div class="modern-card fade-up delay-4">
      <div class="modern-card-header">
        <h3><i class="fas fa-coins"></i> <?php echo get_phrase('expense_of'); ?> <?php echo get_phrase(strtolower(date('F'))); ?></h3>
        <a href="<?php echo route('expense'); ?>" class="view-link">
          <?php echo get_phrase('view_all'); ?> <i class="fas fa-chevron-right"></i>
        </a>
      </div>
      <div class="modern-card-body" style="padding: 0;">
        <div class="modern-table-wrap">
          <?php
          $expenses = $this->crud_model->get_expense($date_from, $date_to)->result_array();
          ?>
          <?php if (count($expenses) > 0): ?>
            <table class="modern-table">
              <thead>
                <tr>
                  <th><?php echo get_phrase('expense'); ?></th>
                  <th><?php echo get_phrase('amount'); ?></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($expenses as $expense): ?>
                  <tr>
                    <td>
                      <?php
                      $expense_category_details = $this->db->get_where('expense_categories', array('id' => $expense['expense_category_id']))->row_array();
                      echo isset($expense_category_details['name']) ? $expense_category_details['name'] : '';
                      ?>
                    </td>
                    <td><strong><?php echo currency($expense['amount']); ?></strong></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php else: ?>
            <div class="p-4 text-center text-muted">
              <i class="far fa-folder-open fa-2x mb-3"></i>
              <p><?php echo get_phrase('no_data_found'); ?></p>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

  </div>

</div>
