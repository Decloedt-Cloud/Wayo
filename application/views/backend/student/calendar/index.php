<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/custom/event_calendar.css">

<style>
  /* ========== MODERN DASHBOARD STYLES ========== */
  :root {
    /* MONOCHROMATIC THEME (INDIGO) */
    --primary: #6366f1;
    --primary-light: #818cf8;
    --primary-lighter: #e0e7ff;
    --primary-dark: #4338ca;
    --secondary: #10b981;
    /* Green for success */
    --bg-main: #f8fafc;
    --bg-card: #ffffff;
    --text-dark: #1e293b;
    --text-muted: #64748b;
    --border-color: #e2e8f0;
    --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.07), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.04);
    --font-body: 'DM Sans', sans-serif;
    --font-header: 'Outfit', sans-serif;
  }

  body {
    font-family: var(--font-body);
    background-color: var(--bg-main);
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
    font-family: var(--font-header);
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

  /* Modern Cards */
  .modern-card {
    background: var(--bg-card);
    border-radius: 20px;
    box-shadow: var(--shadow-md);
    border: 1px solid var(--border-color);
    overflow: hidden;
    transition: all 0.3s ease;
    margin-bottom: 2rem;
  }

  .modern-card:hover {
    box-shadow: var(--shadow-lg);
  }

  .modern-card-body {
    padding: 1.5rem;
  }
</style>

<!-- Modern Header -->
<div class="dash-header">
  <h1>
    <div class="icon-box">
      <i class="fas fa-calendar-alt"></i>
    </div>
    <?php echo get_phrase('calendar'); ?>
  </h1>
  <div class="date-badge">
    <i class="far fa-calendar-alt"></i>
    <span><?php echo get_phrase(strtolower(date('l'))) . ', ' . date('j') . ' ' . get_phrase(strtolower(date('F'))) . ' ' . date('Y'); ?></span>
  </div>
</div>

<!-- Main Content -->
<div class="row">
  <div class="col-12">
    <div class="modern-card">
      <div class="modern-card-body">
        <?php include 'list.php'; ?>
      </div>
    </div>
  </div>
</div>