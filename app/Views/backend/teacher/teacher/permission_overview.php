<div class="row" style="min-width: 300px;">
    <div class="col-md-12">
        <h5 class="text-center"><?php echo db()->table('users')->where('id', $param2)->get()->getRow()->name ?? ''; ?></h5>
        <?php $teacher_permissions = db()->table('teacher_permissions')->where('teacher_id', $param1)->get()->getResultArray();
            $count = 0;
            foreach($teacher_permissions as $teacher_permission){
                $count++;
        ?>
                <table class="table table-hover table-centered table-bordered mb-0" style="margin-bottom: 50px !important; background-color: #FAFAFA;">
                    <tbody>
                        <tr>
                            <td><?php echo get_phrase('class'); ?></td>
                            <td>
                                <?php echo db()->table('classes')->where('id', $teacher_permission['class_id'])->get()->getRow()->name ?? ''; ?>
                            </td>
                        </tr>
       
                        
                        <tr>
                            <td><?php echo get_phrase('marks'); ?></td>
                            <td>
                                <i class="mdi mdi-circle text-<?php if($teacher_permission['marks'] == 1){echo 'success';}else{echo 'danger';} ?>"></i>
                            </td>
                        </tr>
                        <!-- <tr>
                            <td><?php echo get_phrase('assignment'); ?></td>
                            <td>
                                <i class="mdi mdi-circle text-<?php if($teacher_permission['assignment'] == 1){echo 'success';}else{echo 'danger';} ?>"></i>
                            </td>
                        </tr> -->
                        <tr>
                            <td><?php echo get_phrase('attendance'); ?></td>
                            <td>
                                <i class="mdi mdi-circle text-<?php if($teacher_permission['attendance'] == 1){echo 'success';}else{echo 'danger';} ?>"></i>
                            </td>
                        </tr>
                        <!-- <tr>
                            <td><?php echo get_phrase('online_exam'); ?></td>
                            <td>
                                <i class="mdi mdi-circle text-<?php if($teacher_permission['online_exam'] == 1){echo 'success';}else{echo 'danger';} ?>"></i>
                            </td>
                        </tr> -->
                    </tbody>
                </table>
        <?php } ?>
        <?php if($count == 0){ ?>
            <p class = "text-center"><?php echo get_phrase('no_permission_assigned_yet'); ?></p>
        <?php } ?>
        <a href="<?php echo route('permission'); ?>" class="btn btn-info btn-block"><?php echo get_phrase('update_permissions'); ?></a>
    </div>
</div>
