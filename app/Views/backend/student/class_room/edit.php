<?php $class_rooms = db()->table('class_rooms')->where('id', $param1)->get()->getResultArray(); ?>
<?php foreach($class_rooms as $class_room){ ?>
<form method="POST" class="d-block ajaxForm" action="<?php echo route('class_room/update/'.$param1); ?>">
    <!-- Champ caché pour le jeton CSRF -->
    <input type="hidden" name="<?=csrf_token();?>" value="<?=csrf_hash();?>" />
    
    <div class="form-row">
        <div class="form-group mb-1">
            <label for="name"><?php echo get_phrase('class_room_name'); ?></label>
            <input type="text" class="form-control" value="<?php echo $class_room['name']; ?>" id="name" name = "name" required>
            <small id="name_help" class="form-text text-muted"><?php echo get_phrase('provide_class_room_name'); ?></small>
        </div>

        <div class="form-group  col-md-12">
            <button class="btn btn-block btn-primary" type="submit"><?php echo get_phrase('update_class_room'); ?></button>
        </div>
    </div>
</form>
<?php } ?>

<script>
$(".ajaxForm").validate({}); // Jquery form validation initialization
$(".ajaxForm").submit(function(e) {
    var form = $(this);
    ajaxSubmit(e, form, showAllClassRooms);
});
</script>
