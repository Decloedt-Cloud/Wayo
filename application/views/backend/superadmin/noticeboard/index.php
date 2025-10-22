<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/responsive.css">
<!--title-->
<div class="col-xl-12">
  <div class="header-card">
    <div class="card-body">
       <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
      <h4 class="page-title d-inline-block">
        <i class="mdi mdi-calendar-clock title_icon"></i> <?php echo get_phrase('noticeboard_calendar'); ?>
      </h4>
      <div class="alert-modern d-flex align-items-center space-between-icon" role="alert">
                  <div class="icon flex-shrink-0"
                       data-bs-toggle="popover"
                       data-bs-trigger="hover focus"
                       data-bs-content="<?php echo get_phrase("This tab allows you to publish an announcement that will appear on the platform’s showcase website. If you have any information, event, or news to share, feel free to post it here!") ?>"
                       data-bs-placement="top">
                     <i class="dripicons-information"></i>
                  </div>
               </div>
  </div>
      <div class="action-buttons-container">
        <button type="button" class="btn-modern btn btn-outline-primary btn-rounded alignToTitle float-end mt-1" onclick="rightModal('<?php echo site_url('modal/popup/noticeboard/create'); ?>', '<?php echo get_phrase('add_new_notice'); ?>')"> <i class="mdi mdi-plus"></i> <?php echo get_phrase('add_new_notice'); ?></button>
      </div>
      </div>
    </div> <!-- end card body-->
  </div> <!-- end card -->
</div><!-- end col-->


<div class="row">
  <div class="col-12">
    <div class="mb-3">
      <div class="main-card">
        <div class="card-body">
          <div class="col-12 noticeboard_content">
            <?php include 'list.php'; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  $(document).ready(function() {
    refreshNoticeCalendar();
  });

  var showAllNotices = function() {
    var url = '<?php echo route('noticeboard/list'); ?>';

    $.ajax({
      type: 'GET',
      url: url,
      success: function(response) {
        $('.noticeboard_content').html(response);
        refreshNoticeCalendar();
      }
    });
  }

  var refreshNoticeCalendar = function() {
    var url = '<?php echo route('noticeboard/all_notices'); ?>';
    $.ajax({
      type: 'GET',
      url: url,
      dataType: 'json',
      success: function(response) {

        var notice_calendar = [];
        for (let i = 0; i < response.length; i++) {

          var obj;
          obj = {
            "id": response[i].id,
            "title": response[i].notice_title,
            "start": response[i].date,
            "end": response[i].date
          };
          notice_calendar.push(obj);
        }

        $('#calendar').fullCalendar({
          disableDragging: true,
          events: notice_calendar,
          displayEventTime: false,
          eventClick: function(info) {
            rightModal(`<?php echo site_url('modal/popup/noticeboard/edit/'); ?>${info.id}`, "<?php echo get_phrase('edit_notice'); ?>");
          },
          dayClick: function(date) {
            rightModal('<?php echo site_url('modal/popup/noticeboard/create/'); ?>' + date.format(), '<?php echo get_phrase('add_new_notice'); ?>')
          }
        });
      }
    });
  }
</script>