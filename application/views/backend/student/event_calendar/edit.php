<?php $event_calendars = $this->db->get_where('event_calendars', array('id' => $param1))->result_array(); ?>
<?php foreach($event_calendars as $event_calendar){ ?>
    <form method="POST" class="d-block ajaxForm" action="<?php echo route('event_calendar/update/'.$param1); ?>">
        <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />
    
        <div class="exp-form-group">
            <label><?php echo get_phrase('event_title'); ?></label>
            <input type="text" class="form-control" value="<?php echo $event_calendar['title']; ?>" name="title" required>
            <small class="text-muted"><?php echo get_phrase('provide_title_name'); ?></small>
        </div>
        
        <div class="exp-form-group">
            <label><?php echo get_phrase('start_date'); ?></label>
            <input type="text" value="<?php echo date('Y-m-d', strtotime($event_calendar['starting_date'])); ?>" class="form-control date" name="starting_date" data-provide="datepicker" data-date-format="yyyy-mm-dd" required>
        </div>

        <div class="exp-form-group">
            <label><?php echo get_phrase('end_date'); ?></label>
            <input type="text" value="<?php echo date('Y-m-d', strtotime($event_calendar['ending_date'])); ?>" class="form-control date" name="ending_date" data-provide="datepicker" data-date-format="yyyy-mm-dd" required>
        </div>

        <div class="text-right">
            <button class="modern-btn" type="submit"><?php echo get_phrase('update_event'); ?></button>
            <a href="<?php echo route('event_calendar/delete/'.$param1); ?>" class="btn btn-danger btn-sm ml-2" onclick="return confirm('<?php echo get_phrase('are_you_sure_to_delete_this_event'); ?>?');" style="border-radius: 10px; padding: 0.6rem 1.5rem; font-weight: 600;"><?php echo get_phrase('delete'); ?></a>
        </div>
    </form>
<?php } ?>
<script>
$(document).ready(function() {
    $('.date').datepicker();
    $(".ajaxForm").validate({}); 
    $(".ajaxForm").submit(function(e) {
        var form = $(this);
        ajaxSubmit(e, form, refreshEventCalendar); // Use refreshEventCalendar from list.php
    });
});
</script>
</script>
