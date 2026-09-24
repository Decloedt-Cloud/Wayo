<?php if($working_page == 'filter'): ?>
    <?php $classes = isset($student_list_classes) && is_array($student_list_classes) ? $student_list_classes : []; ?>
    <!--title-->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">
                	<i class="fas fa-user-group fa-fw"></i> <?php echo get_phrase('student'); ?>
                	<a href="<?php echo route('student/create'); ?>" class="btn btn-icon btn-success btn-rounded mb-1 mt-3 alignToTitle float-end"> <i class="mdi mdi-plus"></i> <?php echo get_phrase('add_new_student'); ?></a>
            	</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card ">
                <div class="row mt-3">
                    <div class="col-md-1 mb-1"></div>
                    <div class="col-md-4 mb-1">
                        <select name="class" id="class_id" class="form-control"  required >
                            <option value=""><?php echo get_phrase('select_a_class'); ?></option>
                                <?php foreach($classes as $class){ ?>
                                    <option value="<?php echo $class['id']; ?>"><?php echo $class['name']; ?></option>
                                <?php } ?>
                        </select>
                    </div>
  
                    <div class="col-md-2">
                        <button class="btn btn-block btn-secondary" onclick="filter_student()" ><?php echo get_phrase('filter'); ?></button>
                    </div>
                </div>
                <div class="card-body student_content">
                    <div class="empty_box text-center">
                        <img class="mb-3" width="150px" src="<?php echo base_url('assets/backend/images/empty_box.png'); ?>" />
                        <br>
                        <span class=""><?php echo get_phrase('no_data_found'); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php elseif($working_page == 'create'): ?>
    <?php include 'create.php'; ?>
<?php elseif($working_page == 'edit'): ?>
    <?php include 'update.php'; ?>
<?php endif; ?>

<script>
    $('document').ready(function(){

    });

    
    function filter_student(){
        var class_id = $('#class_id').val();
       
        if(class_id != "" ){
            $.ajax({
                url: '<?php echo route('student/filter/') ?>/'+class_id,
                success: function(response){
                    $('.student_content').html(response);
                }
            });
        }else{
            toastr.error('<?php echo get_phrase('please_select_a_class'); ?>');
        }
    }
</script>
