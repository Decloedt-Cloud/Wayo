<?php

// $lessons = db()->table('lesson')->where('course_id', $cours_id)->get()->getResultArray();
        // Récupérer tous les cours associés à la classe sélectionnée
        $courses = db()->table('course')->where('class_id', $class_id)->get()->getResultArray();
     
               // Si des cours sont trouvés, récupérer leurs leçons
               $lessons = [];
               if (!empty($courses)) {
                   foreach ($courses as $course) {
                       $course_lessons = db()->table('lesson')->where('course_id', $course['id'])->get()->getResultArray();
                       if (!empty($course_lessons)) {
                           // Ajouter toutes les leçons dans une liste
                           foreach ($course_lessons as $lesson) {
                               $lessons[] = $lesson; // Ajoute chaque leçon à un tableau
                           }
                       }
                   }
               }
        

if (!empty($lessons)): ?>
    <?php foreach ($lessons as $lesson): ?>
        <option value="<?php echo $lesson['id']; ?>"><?php echo $lesson['title']; ?></option>
    <?php endforeach; ?>
<?php else: ?>
    <option value=""><?php echo get_phrase('no_quiz_found'); ?></option>
<?php endif; ?>