<?php
    $student = $this->db->get_where('students', array('id' => $param1))->row_array();
?>
<div class="container py-4">
    <div class="row g-4 align-items-start">
        <!-- Card Profil Étudiant -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <img class="rounded-circle border border-3 border-primary mb-3" width="100" height="100" src="<?php echo $this->user_model->get_user_image($student['user_id']); ?>" alt="Profile Image">
                    <h5 class="card-title mb-1"><?php echo $this->user_model->get_user_details($student['user_id'], 'name'); ?></h5>
                    <p class="text-muted mb-1"><?php echo get_phrase('student_code'); ?>: <strong><?php echo $student['code']; ?></strong></p>
                    <p class="text-muted mb-0"><?php echo get_phrase('class'); ?>: 
                        <strong>
                        <?php
                            $class_id = $this->db->get_where('enrols', array('student_id' => $param1))->row('class_id');
                            echo $this->db->get_where('classes', array('id' => $class_id))->row('name');
                        ?>
                        </strong>
                    </p>
                </div>
            </div>
        </div>

        <!-- Onglets Profil / Notes -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <ul class="nav nav-tabs nav-tabs-bordered mb-3" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab"><?php echo get_phrase('profile'); ?></button>
                        </li>
                      
                    </ul>

                    <div class="tab-content" id="myTabContent">
                        <!-- Profil -->
                        <div class="tab-pane fade show active" id="profile" role="tabpanel">
                            <table class="table table-borderless mb-0">
                                <tbody>
                                    <tr>
                                        <td class="fw-bold"><?php echo get_phrase('name'); ?>:</td>
                                        <td><?php echo $this->user_model->get_user_details($student['user_id'], 'name'); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold"><?php echo get_phrase('student_code'); ?>:</td>
                                        <td><?php echo $student['code']; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold"><?php echo get_phrase('class'); ?>:</td>
                                        <td>
                                            <?php
                                                echo $this->db->get_where('classes', array('id' => $class_id))->row('name');
                                            ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                     
                    </div> <!-- /tab-content -->
                </div> <!-- /card-body -->
            </div> <!-- /card -->
        </div>
    </div> <!-- /row -->
</div>

<!-- CSS personnalisé -->
<style>

    .card {
        border-radius: 12px;
    }
    .table th, .table td {
        vertical-align: middle;
    }
</style>
