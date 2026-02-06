<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/responsive.min.css">

<style>
/* ============================================================================
   SCHOOL INDEX - MODERN DESIGN (Adapted from Invoice Index)
   ============================================================================ */
:root {
    --idx-primary: #6366f1;
    --idx-primary-light: #eef2ff;
    --idx-success: #059669;
    --idx-dark: #1e293b;
    --idx-gray: #64748b;
    --idx-light: #f8fafc;
    --idx-border: #e2e8f0;
}

/* Header Card */
.idx-header {
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    border-radius: 16px;
    padding: 1.5rem 2rem;
    margin-bottom: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
}

.idx-header-left {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.idx-header-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    background: rgba(255,255,255,0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.idx-header-text h4 {
    margin: 0;
    color: white;
    font-size: 1.35rem;
    font-weight: 700;
}

.idx-header-text p {
    margin: 0.25rem 0 0;
    color: #94a3b8;
    font-size: 0.85rem;
}

.idx-header-actions {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.idx-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.625rem;
    padding: 0.875rem 1.75rem;
    border-radius: 16px;
    font-size: 0.95rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    box-shadow: 
        0 4px 12px rgba(0, 0, 0, 0.08),
        inset 0 1px 0 rgba(255, 255, 255, 0.15);
    text-transform: uppercase;
}

.idx-btn-primary {
    background: linear-gradient(135deg, var(--idx-primary), #8b5cf6);
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.25);
    backdrop-filter: blur(10px);
}

.idx-btn-primary::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.6s ease-in-out;
    z-index: 1;
}

.idx-btn-primary::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, transparent 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: 0;
}

.idx-btn-primary span,
.idx-btn-primary i {
    position: relative;
    z-index: 2;
}

.idx-btn-primary i {
    font-size: 1.1rem;
    transition: transform 0.3s ease;
}

.idx-btn-primary:hover {
    transform: translateY(-3px) scale(1.02);
    box-shadow: 
        0 12px 30px rgba(99, 102, 241, 0.6),
        0 4px 15px rgba(139, 92, 246, 0.4),
        inset 0 1px 0 rgba(255, 255, 255, 0.2);
    background: linear-gradient(135deg, #8b5cf6, var(--idx-primary));
}

.idx-btn-primary:hover::before {
    left: 100%;
}

.idx-btn-primary:hover::after {
    opacity: 1;
}

.idx-btn-primary:hover i {
    transform: scale(1.15);
}

.idx-btn-primary:active {
    transform: translateY(-1px) scale(0.99);
    box-shadow: 
        0 6px 20px rgba(99, 102, 241, 0.5),
        0 2px 8px rgba(139, 92, 246, 0.3);
}

.idx-btn-primary:focus {
    outline: none;
    box-shadow: 
        0 0 0 3px rgba(99, 102, 241, 0.3),
        0 12px 30px rgba(99, 102, 241, 0.6);
}
</style>

<!-- Modern Header -->
<div class="idx-header">
    <div class="idx-header-left">
        <div class="idx-header-icon">
            <i class="fa-solid fa-school"></i>
        </div>
        <div class="idx-header-text">
            <h4><?php echo get_phrase('school'); ?></h4>
            <p><?php echo get_phrase('manage_registered_schools'); ?></p>
        </div>
    </div>
    <div class="idx-header-actions">
        <button type="button" class="idx-btn idx-btn-primary" onclick="rightModal('<?php echo site_url('modal/popup/school/create'); ?>', '<?php echo get_phrase('create_school'); ?>')">
            <i class="fa-solid fa-plus"></i>
            <span><?php echo get_phrase('create_school'); ?></span>
        </button>
    </div>
</div>

<div class="row">
  <div class="col-12">
    <div class="mb-3">
<div class="main-card">
        <div class="card-body">
      <div class="card-body admin_content">
        <?php include 'list.php'; ?>
      </div>
    </div>
  </div>
</div>
</div>

<script>
var showAllSchools = function () {
  var url = '<?php echo route('school_crud/list'); ?>';

  $.ajax({
    type : 'GET',
    url: url,
    success : function(response) {
      $('.admin_content').html(response);
      initDataTable('basic-datatable');
    }
  });
}
</script>