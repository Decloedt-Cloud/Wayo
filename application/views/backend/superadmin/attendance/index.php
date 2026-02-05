<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/responsive.min.css">
    <!--title-->
    <div class="col-xl-12">
        <div class="header-card">
            <div class="card-body d-flex justify-content-between align-items-center">
                <h4 class="page-title d-inline-block">
          <i class="fas fa-clipboard-user fa-fw"></i> <?php echo get_phrase('daily_attendance'); ?>
        </h4>
        <div class="action-buttons-container">
        <button type="button" class="btn-modern btn btn-outline-primary btn-rounded alignToTitle float-end mt-1" onclick="rightModal('<?php echo site_url('modal/popup/attendance/take_attendance'); ?>', '<?php echo get_phrase('take_attendance'); ?>')"> <i class="mdi mdi-plus"></i> <?php echo get_phrase('take_attendance'); ?></button>
      </div> <!-- end card body-->
    </div> <!-- end card -->
  </div><!-- end col-->
</div>

<div class="row">
  <div class="col-12">
    <div class="mb-3">
    <div class="main-card">
      <div class="card-body">
      <div class="row mt-3 d-print-none">
        <div class="col-md-1 mb-1"></div>
        <div class="col-md-2 mb-1">
          <select name="month" id="month" class="form-control"  required>
            <option value=""><?php echo get_phrase('select_a_month'); ?></option>
            <option value="Jan"<?php if(date('M') == 'Jan') echo 'selected'; ?>><?php echo get_phrase('january'); ?></option>
            <option value="Feb"<?php if(date('M') == 'Feb') echo 'selected'; ?>><?php echo get_phrase('february'); ?></option>
            <option value="Mar"<?php if(date('M') == 'Mar') echo 'selected'; ?>><?php echo get_phrase('march'); ?></option>
            <option value="Apr"<?php if(date('M') == 'Apr') echo 'selected'; ?>><?php echo get_phrase('april'); ?></option>
            <option value="May"<?php if(date('M') == 'May') echo 'selected'; ?>><?php echo get_phrase('may'); ?></option>
            <option value="Jun"<?php if(date('M') == 'Jun') echo 'selected'; ?>><?php echo get_phrase('june'); ?></option>
            <option value="Jul"<?php if(date('M') == 'Jul') echo 'selected'; ?>><?php echo get_phrase('july'); ?></option>
            <option value="Aug"<?php if(date('M') == 'Aug') echo 'selected'; ?>><?php echo get_phrase('august'); ?></option>
            <option value="Sep"<?php if(date('M') == 'Sep') echo 'selected'; ?>><?php echo get_phrase('september'); ?></option>
            <option value="Oct"<?php if(date('M') == 'Oct') echo 'selected'; ?>><?php echo get_phrase('october'); ?></option>
            <option value="Nov"<?php if(date('M') == 'Nov') echo 'selected'; ?>><?php echo get_phrase('november'); ?></option>
            <option value="Dec"<?php if(date('M') == 'Dec') echo 'selected'; ?>><?php echo get_phrase('december'); ?></option>
          </select>
        </div>
        <div class="col-md-2 mb-1">
          <select name="year" id="year" class="form-control"  required>
            <option value=""><?php echo get_phrase('select_a_year'); ?></option>
            <?php for($year = 2015; $year <= date('Y'); $year++){ ?>
              <option value="<?php echo $year; ?>"<?php if(date('Y') == $year) echo 'selected'; ?>><?php echo $year; ?></option>
            <?php } ?>

          </select>
        </div>
        <div class="col-md-2 mb-1">
          <select name="class" id="class_id_daily" class="form-control"   required>
            <option value=""><?php echo get_phrase('select_a_class'); ?></option>
            <?php
            $classes = $this->db->get_where('classes', array('school_id' => school_id()))->result_array();
            $school_id = school_id();
            foreach($classes as $class){
              $this->db->where('class_id', $class['id']);
              $this->db->where('school_id', $school_id);
              $total_student = $this->db->get('enrols');
              ?>
              <option value="<?php echo $class['id']; ?>">
                <?php echo $class['name']; ?>
                <?php echo "(".$total_student->num_rows().")"; ?>
              </option>
            <?php } ?>
          </select>
        </div>

        <div class="col-md-2 btncol">
      
          <button class="btn btn-block btn-secondary" onclick="filter_attendance()" ><?php echo get_phrase('filter'); ?></button>
        </div>
      </div>
      <div class="card-body attendance_content">
        <div class="empty_box text-center">
          <img class="mb-3" width="150px" src="<?php echo base_url('assets/backend/images/empty_box.png'); ?>" />
          <br>
          <span class=""><?php echo get_phrase('no_data_found'); ?></span>
        </div>
      </div>
    </div>
  </div>
</div>
  </div>
</div>

<script>
$('document').ready(function(){
  $('select.select2:not(.normal)').each(function () { $(this).select2({ dropdownParent: '#right-modal' }); }); //initSelect2(['#month', '#year', '#class_id', '#section_id']);
});


function filter_attendance(){
  var month = $('#month').val();
  var year = $('#year').val();
  var class_id = $('#class_id_daily').val();
 
  if(class_id != "" && month != "" && year != ""){
    getDailtyAttendance();
  }else{
    toastr.error('<?php echo get_phrase('please_select_in_all_fields !'); ?>');
  }
}

var getDailtyAttendance = function () {
  var month = $('#month').val();
  var year = $('#year').val();
  var class_id = $('#class_id_daily').val();
  
  // Récupérer le nom et la valeur du jeton CSRF depuis l'input caché
  var csrfName = $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').attr('name');
  var csrfHash = $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val();
  if(class_id != ""  && month != "" && year != ""){
    $.ajax({
      type: 'POST',
      url: '<?php echo route('attendance/filter') ?>',
      data: {month : month, year : year, class_id : class_id,  [csrfName]: csrfHash},
      dataType: 'json',
      success: function(response){
        $('.attendance_content').html(response.status);
        initDataTable('basic-datatable');

            // Mettre à jour le jeton CSRF avec le nouveau jeton renvoyé dans la réponse
            var newCsrfName = response.csrfName;
            var newCsrfHash = response.csrfHash;
            $('input[name="' + newCsrfName + '"]').val(newCsrfHash); // Mise à jour du token CSRF
      }
    });
  }
}
</script>
