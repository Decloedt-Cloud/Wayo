<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/responsive.css">
<style>
/* ============================================================================
   EVENT CALENDAR - MODERN DESIGN
   ============================================================================ */
:root {
    --exp-primary: #6366f1;
    --exp-primary-light: #eef2ff;
    --exp-success: #059669;
    --exp-dark: #1e293b;
    --exp-gray: #64748b;
    --exp-light: #f8fafc;
    --exp-border: #e2e8f0;
    --exp-warning: #f59e0b;
}

/* Header Styling */
.exp-header {
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    border-radius: 16px;
    padding: 1.5rem 2rem;
    margin-bottom: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    box-shadow: 0 4px 20px rgba(99, 102, 241, 0.3);
}

.exp-header-left {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.exp-header-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(10px);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.exp-header-text h4 {
    margin: 0;
    color: white;
    font-size: 1.35rem;
    font-weight: 700;
}

.exp-header-text p {
    margin: 0.25rem 0 0;
    color: rgba(255,255,255,0.7);
    font-size: 0.85rem;
}

.exp-header-actions {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

/* Modern Button */
.exp-btn {
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
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08), inset 0 1px 0 rgba(255, 255, 255, 0.15);
    text-transform: uppercase;
}

.exp-btn-primary {
    background: linear-gradient(135deg, var(--exp-primary), #8b5cf6);
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.25);
    backdrop-filter: blur(10px);
}

.exp-btn-primary::before {
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

.exp-btn-primary::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, transparent 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: 0;
}

.exp-btn-primary span, 
.exp-btn-primary i {
    position: relative;
    z-index: 2;
}

.exp-btn-primary i {
    font-size: 1.1rem;
    transition: transform 0.3s ease;
}

.exp-btn-primary:hover {
    transform: translateY(-3px) scale(1.02);
    box-shadow: 0 12px 30px rgba(99, 102, 241, 0.6), 0 4px 15px rgba(139, 92, 246, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.2);
    background: linear-gradient(135deg, #8b5cf6, var(--exp-primary));
}

.exp-btn-primary:hover::before {
    left: 100%;
}

.exp-btn-primary:hover::after {
    opacity: 1;
}

.exp-btn-primary:hover i {
    transform: scale(1.15);
}

.exp-btn-primary:active {
    transform: translateY(-1px) scale(0.99);
    box-shadow: 0 6px 20px rgba(99, 102, 241, 0.5), 0 2px 8px rgba(139, 92, 246, 0.3);
}

.exp-btn-primary:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.3), 0 12px 30px rgba(99, 102, 241, 0.6);
}

/* Content Card */
.exp-content-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    border: 1px solid var(--exp-border);
    overflow: hidden;
}

.exp-content-header {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    padding: 1rem 1.5rem;
    border-bottom: 1px solid var(--exp-border);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.exp-content-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    color: var(--exp-dark);
    font-size: 0.95rem;
}

.exp-content-title i {
    color: var(--exp-primary);
}

.exp-content-body {
    padding: 0;
}

/* Responsive */
@media (max-width: 768px) {
    .exp-header {
        flex-direction: column;
        text-align: center;
    }
    
    .exp-header-left {
        flex-direction: column;
    }
}

/* Shared styles for list.php content */
.exp-dashboard { padding: 1.5rem; border-bottom: 1px solid var(--exp-border); background: #fff; }
.exp-stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; }
.exp-stat-card { background: white; border-radius: 16px; padding: 1.5rem; position: relative; overflow: hidden; border: 1px solid var(--exp-border); box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02); transition: transform 0.3s ease, box-shadow 0.3s ease; display: flex; align-items: center; gap: 1rem; }
.exp-stat-card:hover { transform: translateY(-5px); box-shadow: 0 12px 24px rgba(0, 0, 0, 0.06); }
.exp-stat-icon-wrap { width: 48px; height: 48px; border-radius: 12px; background: var(--exp-primary-light); color: var(--exp-primary); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; position: relative; z-index: 2; }
.exp-stat-data { display: flex; flex-direction: column; z-index: 2; }
.exp-stat-value { font-size: 1.5rem; font-weight: 700; color: var(--exp-dark); line-height: 1.2; }
.exp-stat-title { font-size: 0.85rem; color: var(--exp-gray); font-weight: 500; }
.exp-stat-trend { position: absolute; right: 1.5rem; top: 1.5rem; display: flex; align-items: center; gap: 0.25rem; font-size: 0.75rem; color: var(--exp-success); font-weight: 600; background: rgba(5, 150, 105, 0.1); padding: 0.25rem 0.5rem; border-radius: 20px; }

/* Toolbar */
.exp-toolbar { padding: 1rem 1.5rem; background: #fff; border-bottom: 1px solid var(--exp-border); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; }
.exp-search-box { position: relative; display: flex; align-items: center; max-width: 300px; width: 100%; }
.exp-search-field { width: 100%; padding: 0.6rem 1rem 0.6rem 2.5rem; border: 1px solid var(--exp-border); border-radius: 10px; font-size: 0.9rem; transition: all 0.2s; background: var(--exp-light); }
.exp-search-field:focus { outline: none; border-color: var(--exp-primary); background: white; box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1); }
.exp-search-icon-wrapper { position: absolute; left: 0.8rem; color: var(--exp-gray); pointer-events: none; }
.exp-search-shortcut { position: absolute; right: 0.8rem; display: flex; gap: 2px; opacity: 0.5; }
.exp-search-shortcut kbd { background: #fff; border: 1px solid var(--exp-border); border-radius: 4px; padding: 0 4px; font-size: 0.7rem; font-family: inherit; }

.exp-list-header { display: grid; grid-template-columns: 2fr 1fr 1fr 100px; padding: 1rem 1.5rem; background: var(--exp-light); border-bottom: 1px solid var(--exp-border); font-size: 0.85rem; font-weight: 600; color: var(--exp-gray); text-transform: uppercase; letter-spacing: 0.5px; }
.exp-list-item { display: grid; grid-template-columns: 2fr 1fr 1fr 100px; padding: 1rem 1.5rem; border-bottom: 1px solid var(--exp-border); align-items: center; transition: background 0.2s; position: relative; }
.exp-list-item:hover { background: #f8fafc; }
.exp-list-item:last-child { border-bottom: none; }
.exp-item-name { font-size: 0.95rem; font-weight: 600; color: var(--exp-dark); margin: 0; display: flex; align-items: center; gap: 0.5rem; }

.exp-date-display { display: flex; align-items: center; gap: 0.75rem; }
.exp-date-icon { width: 36px; height: 36px; border-radius: 8px; background: var(--exp-light); color: var(--exp-primary); display: flex; align-items: center; justify-content: center; font-size: 1.1rem; }
.exp-date-text { display: flex; flex-direction: column; }
.exp-date-main { font-size: 0.9rem; font-weight: 600; color: var(--exp-dark); }
.exp-date-day { font-size: 0.75rem; color: var(--exp-gray); }

.exp-action-buttons { display: flex; justify-content: flex-end; gap: 0.5rem; }
.exp-action-btn { width: 32px; height: 32px; border-radius: 8px; border: 1px solid transparent; background: transparent; color: var(--exp-gray); display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; }
.exp-action-btn:hover { background: var(--exp-light); color: var(--exp-dark); border-color: var(--exp-border); }
.exp-btn-edit:hover { color: var(--exp-primary); background: var(--exp-primary-light); border-color: transparent; }
.exp-btn-delete:hover { color: #ef4444; background: #fef2f2; border-color: transparent; }

/* Empty State */
.exp-empty-state { padding: 4rem 2rem; text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; }
.exp-empty-icon-container { position: relative; width: 100px; height: 100px; margin-bottom: 2rem; }
.exp-empty-icon { position: relative; width: 100%; height: 100%; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 3rem; color: var(--exp-primary); box-shadow: 0 10px 30px rgba(99, 102, 241, 0.2); z-index: 10; }
.exp-empty-circle { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); border-radius: 50%; border: 1px solid var(--exp-primary); opacity: 0.2; }
.exp-circle-1 { width: 140%; height: 140%; animation: pulse 3s infinite; }
.exp-circle-2 { width: 180%; height: 180%; animation: pulse 3s infinite 0.5s; }
.exp-circle-3 { width: 220%; height: 220%; animation: pulse 3s infinite 1s; }
@keyframes pulse { 0% { transform: translate(-50%, -50%) scale(0.8); opacity: 0.5; } 100% { transform: translate(-50%, -50%) scale(1.2); opacity: 0; } }

/* Pagination */
.exp-pagination-bar { padding: 1rem 1.5rem; background: #fff; border-top: 1px solid var(--exp-border); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; }
.exp-pagination-info { display: flex; align-items: center; gap: 1.5rem; }
.exp-info-text { font-size: 0.85rem; color: var(--exp-gray); }
.exp-info-text strong { color: var(--exp-dark); }
.exp-progress-bar { width: 100px; height: 4px; background: var(--exp-light); border-radius: 2px; overflow: hidden; }
.exp-progress-fill { height: 100%; background: var(--exp-primary); border-radius: 2px; transition: width 0.3s ease; }
.exp-pagination-nav { display: flex; align-items: center; gap: 0.5rem; background: var(--exp-light); padding: 0.25rem; border-radius: 8px; }
.exp-nav-btn { width: 32px; height: 32px; border-radius: 6px; border: none; background: transparent; color: var(--exp-gray); display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; }
.exp-nav-btn:hover:not(:disabled) { background: white; color: var(--exp-primary); box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
.exp-nav-btn:disabled { opacity: 0.5; cursor: not-allowed; }
.exp-page-indicators { display: flex; align-items: center; gap: 2px; }
.exp-page-dot { width: 8px; height: 8px; border-radius: 50%; background: #cbd5e1; cursor: pointer; transition: all 0.2s; }
.exp-page-dot.active { background: var(--exp-primary); transform: scale(1.2); }

@media (max-width: 768px) {
    .exp-list-header { display: none; }
    .exp-list-item { grid-template-columns: 1fr; gap: 1rem; padding: 1.5rem; }
    .exp-col-date { display: flex; justify-content: space-between; width: 100%; border-top: 1px solid var(--exp-light); padding-top: 0.75rem; }
    .exp-col-actions { justify-content: flex-start; border-top: 1px solid var(--exp-light); padding-top: 1rem; width: 100%; }
}
</style>

<div class="exp-header">
    <div class="exp-header-left">
        <div class="exp-header-icon">
            <i class="fas fa-bell"></i>
        </div>
        <div class="exp-header-text">
            <h4><?php echo get_phrase('event_calendar'); ?></h4>
            <p><?php echo get_phrase('manage_all_events'); ?></p>
        </div>
    </div>
    <div class="exp-header-actions">
        <button type="button" class="exp-btn exp-btn-primary" onclick="rightModal('<?php echo site_url('modal/popup/event_calendar/create'); ?>', '<?php echo get_phrase('event_calendar'); ?>')">
            <i class="mdi mdi-plus"></i> <?php echo get_phrase('add_new_event'); ?>
        </button>
    </div>
</div>

<div class="exp-content-card">
    <div class="exp-content-header">
        <span class="exp-content-title">
            <i class="mdi mdi-format-list-bulleted"></i>
            <?php echo get_phrase('event_list'); ?>
        </span>
    </div>
    <div class="exp-content-body">
        <div class="event_calendar_content">
            <?php include 'list.php'; ?>
        </div>
    </div>
</div>

<script>
   $(document).ready(function() {
      refreshEventCalendar();
   });

   var showAllEvents = function() {
      var url = '<?php echo route('event_calendar/list'); ?>';
      $.ajax({
         type: 'GET',
         url: url,
         success: function(response) {
            $('.event_calendar_content').html(response);
            // initDataTable not needed for new design
            refreshEventCalendar();
         }
      });
   }

   var refreshEventCalendar = function() {
      var url = '<?php echo route('event_calendar/all_events'); ?>';
      $.ajax({
         type: 'GET',
         url: url,
         dataType: 'json',
         success: function(response) {
            var event_calendar = [];
            for (let i = 0; i < response.length; i++) {
               var obj;
               obj = {
                  "title": response[i].title,
                  "start": response[i].starting_date,
                  "end": response[i].ending_date
               };
               event_calendar.push(obj);
            }

            $('#calendar').fullCalendar({
               disableDragging: true,
               events: event_calendar,
               displayEventTime: false
            });
         }
      });
   }

   function showNotification(type, message) {
    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: 'toast-top-right',
        timeOut: 5000,
        showMethod: 'fadeIn',
        hideMethod: 'fadeOut',
    };
    if (type === 'success') {
        toastr.success(message);
    } else if (type === 'error') {
        toastr.error(message);
    } else if (type === 'warning') {
        toastr.warning(message);
    }
}
</script>