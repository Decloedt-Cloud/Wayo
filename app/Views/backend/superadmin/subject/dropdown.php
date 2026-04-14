<?php
$subjects = db()->table('subjects')->where('class_id', $class_id)->get()->getResultArray();
if (count($subjects) > 0):
  foreach ($subjects as $subject): ?>
    <option value="<?php echo $subject['id']; ?>"><?php echo $subject['name']; ?></option>
  <?php endforeach; ?>
<?php else: ?>
  <option value=""><?php echo get_phrase('no_subject_found'); ?></option>
<?php endif; ?>
