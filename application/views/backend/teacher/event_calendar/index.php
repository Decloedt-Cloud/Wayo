<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/responsive.css">
<!--title-->
<div class="col-xl-12">
   <div class="header-card">
      <div class="card-body">
         <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
               <h4 class="page-title d-inline-block">
                  <i class="mdi mdi-calendar-clock title_icon"></i> <?php echo get_phrase('event_calendar'); ?>
               </h4>
               <div class="alert-modern d-flex align-items-center space-between-icon" role="alert">
                  <div class="icon flex-shrink-0"
                       data-bs-toggle="popover"
                       data-bs-trigger="hover focus"
                       data-bs-content="<?php echo get_phrase("This tab allows you to publish an announcement that will appear on the platform’s dashboard. If you have any information, event, or news to share, feel free to post it here!") ?>"
                       data-bs-placement="top">
                     <i class="dripicons-information"></i>
                  </div>
               </div>
            </div>
            <button type="button" class="btn btn-outline-primary btn-rounded alignToTitle mt-1" onclick="rightModal('<?php echo site_url('modal/popup/event_calendar/create'); ?>', '<?php echo get_phrase('event_calendar'); ?>')"> 
               <i class="mdi mdi-plus"></i> <?php echo get_phrase('add_new_event'); ?>
            </button>
         </div>
      </div> <!-- end card body-->
   </div> <!-- end card -->
</div>

<div class="row">
   <div class="col-12">
      <div class="mb-3">
         <div class="main-card">
            <div class="card-body">
               <div class="col-12 event_calendar_content">
                  <?php include 'list.php'; ?>
               </div>
         </div>
       </div>
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
            initDataTable("basic-datatable");
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