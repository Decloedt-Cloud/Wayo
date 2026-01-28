<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/responsive.css">
<style>
:root { --exp-primary: #6366f1; --exp-primary-light: #eef2ff; --exp-success: #059669; --exp-dark: #1e293b; --exp-gray: #64748b; --exp-light: #f8fafc; --exp-border: #e2e8f0; --exp-warning: #f59e0b; }
.exp-header { background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); border-radius: 16px; padding: 1.5rem 2rem; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; box-shadow: 0 4px 20px rgba(99, 102, 241, 0.3); }
.exp-header-left { display: flex; align-items: center; gap: 1rem; }
.exp-header-icon { width: 50px; height: 50px; border-radius: 12px; background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: white; }
.exp-header-text h4 { margin: 0; color: white; font-size: 1.35rem; font-weight: 700; }
.exp-header-text p { margin: 0.25rem 0 0; color: rgba(255,255,255,0.7); font-size: 0.85rem; }
.exp-header-actions { display: flex; gap: 0.75rem; flex-wrap: wrap; }
.exp-btn { display: inline-flex; align-items: center; gap: 0.625rem; padding: 0.875rem 1.75rem; border-radius: 16px; font-size: 0.95rem; font-weight: 700; letter-spacing: 0.5px; text-decoration: none; border: none; cursor: pointer; transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1); position: relative; overflow: hidden; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08), inset 0 1px 0 rgba(255, 255, 255, 0.15); text-transform: uppercase; }
.exp-btn-primary { background: linear-gradient(135deg, var(--exp-primary), #8b5cf6); color: white; border: 1px solid rgba(255, 255, 255, 0.25); backdrop-filter: blur(10px); }
.exp-btn-primary::before { content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent); transition: left 0.6s ease-in-out; z-index: 1; }
.exp-btn-primary::after { content: ''; position: absolute; inset: 0; background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, transparent 100%); opacity: 0; transition: opacity 0.3s ease; z-index: 0; }
.exp-btn-primary span, .exp-btn-primary i { position: relative; z-index: 2; }
.exp-btn-primary i { font-size: 1.1rem; transition: transform 0.3s ease; }
.exp-btn-primary:hover { transform: translateY(-3px) scale(1.02); box-shadow: 0 12px 30px rgba(99, 102, 241, 0.6), 0 4px 15px rgba(139, 92, 246, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.2); background: linear-gradient(135deg, #8b5cf6, var(--exp-primary)); }
.exp-btn-primary:hover::before { left: 100%; }
.exp-btn-primary:hover::after { opacity: 1; }
.exp-btn-primary:hover i { transform: scale(1.15); }
.exp-btn-primary:active { transform: translateY(-1px) scale(0.99); box-shadow: 0 6px 20px rgba(99, 102, 241, 0.5), 0 2px 8px rgba(139, 92, 246, 0.3); }
.exp-btn-primary:focus { outline: none; box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.3), 0 12px 30px rgba(99, 102, 241, 0.6); }
.exp-content-card { background: white; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); border: 1px solid var(--exp-border); overflow: hidden; }
.exp-content-header { background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); padding: 1rem 1.5rem; border-bottom: 1px solid var(--exp-border); display: flex; justify-content: space-between; align-items: center; }
.exp-content-title { display: flex; align-items: center; gap: 0.5rem; font-weight: 600; color: var(--exp-dark); font-size: 0.95rem; }
.exp-content-title i { color: var(--exp-primary); }
.exp-content-body { padding: 0; }
.exp-loading { display: flex; align-items: center; justify-content: center; padding: 3rem; color: var(--exp-gray); }
.exp-loading i { font-size: 2rem; animation: exp-spin 1s linear infinite; margin-right: 0.75rem; }
@keyframes exp-spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
@media (max-width: 768px) { .exp-header { flex-direction: column; text-align: center; } .exp-header-left { flex-direction: column; } }
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
            if($('#basic-datatable').length > 0){
               initDataTable("basic-datatable");
            }
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
    <?php if ($this->config->item('enable_toasts') == FALSE): ?>
        return;
    <?php endif; ?>
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
