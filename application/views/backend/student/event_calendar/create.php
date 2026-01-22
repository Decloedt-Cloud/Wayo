<form method="POST" class="d-block ajaxForm" action="<?php echo route('event_calendar/create'); ?>">
  <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />
  
  <div class="exp-form-group">
    <label><?php echo get_phrase('event_title'); ?></label>
    <input type="text" class="form-control" name="title" required>
    <small class="text-muted"><?php echo get_phrase('provide_title_name'); ?></small>
  </div>
  
  <div class="exp-form-group">
    <label><?php echo get_phrase('start_date'); ?></label>
    <input type="text" class="form-control date" name="starting_date" data-provide="datepicker" data-date-format="yyyy-mm-dd" required>
  </div>
  
  <div class="exp-form-group">
    <label><?php echo get_phrase('end_date'); ?></label>
    <input type="text" class="form-control date" name="ending_date" data-provide="datepicker" data-date-format="yyyy-mm-dd" required>
  </div>

  <div class="text-right">
    <button class="modern-btn" type="submit"><?php echo get_phrase('save_event'); ?></button>
  </div>
</form>

<script>
$(document).ready(function() {
  $('.date').datepicker();
});
$(".ajaxForm").validate({});
$(".ajaxForm").submit(function(e) {
  var form = $(this);
  ajaxSubmit(e, form, refreshEventCalendar);
});
</script>
