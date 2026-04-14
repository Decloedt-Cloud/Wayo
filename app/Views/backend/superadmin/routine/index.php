<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/responsive.min.css">
<!--title-->
<div class="col-xl-12">
	<div class="header-card">
		<div class="card-body">
			<h4 class="page-title d-inline-block">
				<i class="mdi mdi-calendar-today title_icon"></i> <?php echo get_phrase('class_routine'); ?>
			</h4>
			<button type="button" class="btn btn-outline-primary btn-rounded alignToTitle float-end mt-1" onclick="rightModal('<?php echo site_url('modal/popup/routine/create'); ?>', '<?php echo get_phrase('create_routine'); ?>')"> <i class="mdi mdi-plus"></i> <?php echo get_phrase('add_class_routine'); ?></button>
		</div> <!-- end card body-->
	</div> <!-- end card -->
</div><!-- end col-->


<div class="row">
	<div class="col-12">

		<div class="mb-3">
			<div class="main-card">
				<div class="card-body">
					<div class="row mt-3">
						<div class="col-md-3 mb-1"></div>
						<div class="col-md-4 mb-1">
							<select name="class" id="class_id_routine" class="form-control" required>
								<option value=""><?php echo get_phrase('select_a_class'); ?></option>
								<?php
								$classes = db()->table('classes')->where('school_id', school_id())->get()->getResultArray();
								$school_id = school_id();
								foreach ($classes as $class) {
									$total_student = db()->table('enrols')
										->where('class_id', $class['id'])
										->where('school_id', $school_id)
										->get()
										->getResultArray();
								?>
									<option value="<?php echo $class['id']; ?>">
										<?php echo $class['name']; ?>
										<?php echo "(" . count($total_student) . ")"; ?>
									</option>
								<?php } ?>
							</select>
						</div>

						<div class="col-md-4 btncol">

							<button class="btn btn-block btn-secondary" onclick="filter_class_routine()"><?php echo get_phrase('filter'); ?></button>
						</div>
					</div>
					<div class="card-body class_routine_content">
						<?php include 'list.php'; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	function filter_class_routine() {
		var class_id = $('#class_id_routine').val();

		if (class_id != "") {
			getFilteredClassRoutine();
		} else {
			toastr.error('<?php echo get_phrase('please_select_a_class_and_section'); ?>');
		}
	}

	var getFilteredClassRoutine = function() {
		var class_id = $('#class_id_routine').val();

		if (class_id != "") {
			$.ajax({
				url: '<?php echo route('routine/filter/') ?>' + class_id,
				success: function(response) {
					$('.class_routine_content').html(response);
				}
			});
		}
	}
</script>