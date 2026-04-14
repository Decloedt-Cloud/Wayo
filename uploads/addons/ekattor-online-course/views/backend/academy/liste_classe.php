<option value="<?php echo 'all'; ?>" ><?php echo get_phrase('all'); ?></option>
<?php
$classes = db()->table('classes')->where('school_id', $school_id)->get()->getResultArray();
if (count($classes) > 0):
  foreach ($classes as $classe): ?>
    <option value="<?php echo $classe['id']; ?>" ><?php echo $classe['name']; ?></option>
  <?php endforeach; ?>
<?php else: ?>
  <option value=""><?php echo get_phrase('no_classe_found'); ?></option>
<?php endif; ?>
