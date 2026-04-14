<?php

namespace App\Models\addons;

use CodeIgniter\Model;

class Lms_model extends Model {
    protected $table            = 'lms_model';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [];
    protected $useTimestamps    = false;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField    = 'deleted_at';
    protected $dateFormat       = 'datetime';

    protected $DBGroup = 'default';

    protected $request;
    protected $session;

    // constructor
    function __construct()
    {
        parent::__construct();
        $this->request = \Config\Services::request();
        $this->session = \Config\Services::session();
    }

    private function uploadedFile(string $field)
    {
        return $this->request->getFile($field);
    }

    private function isUploadOk(string $field): bool
    {
        $file = $this->uploadedFile($field);
        return $file && $file->getError() === UPLOAD_ERR_OK;
    }

    function index() {}

    public function get_course_by_id($course_id)
    {
        return \db()->table('course')
            ->where('id', $course_id)
            ->where('school_id', school_id())
            ->get()
            ->getRowArray();
    }

    
   public function filter_course_for_backend($class_id = "", $user_id = "", $status = "", $school_id = "")
    {
        $superadmin_login = session()->get('superadmin_login');
        $admin_login = session()->get('admin_login');
        $teacher_login = session()->get('teacher_login');
        $student_login = session()->get('student_login');

        if ($superadmin_login == 1 || $admin_login == 1):
            return $this->filter_course_for_admin($class_id, $user_id, $status, $school_id);
        endif;

        if ($teacher_login == 1):
            return $this->filter_course_for_teacher($class_id, $user_id, $status, $school_id);
        endif;

        if ($student_login == 1):
            return $this->filter_course_for_student($class_id, $user_id, $status, $school_id);
        endif;
    }
    
    public function filter_course_for_admin($class_id = "all", $user_id = "all", $status = "all", $school_id = "all")
    {
        $builder = \db()->table('course');
        $builder->select('course.*, GROUP_CONCAT(DISTINCT t.name SEPARATOR ", ") AS teacher_names');
        $builder->where('course.school_id', school_id());

        // Join with course_classes to filter by class
        if ($class_id != "all" && !empty($class_id)) {
            $builder->join('course_classes', 'course_classes.course_id = course.id', 'inner');
            $builder->where('course_classes.class_id', $class_id);
        }

        // Join with course_teachers + users to get teacher names
        $builder->join('course_teachers', 'course_teachers.course_id = course.id', 'left');
        $builder->join('users t', 't.id = course_teachers.user_id', 'left');

        // Filter by teacher (if selected)
        if ($user_id != "all" && !empty($user_id)) {
            $builder->where('course_teachers.user_id', $user_id);
        }

        // Filter by status
        if ($status != "all" && !empty($status)) {
            $builder->where('course.status', $status);
        }

        // Avoid duplicate courses + group teacher names
        $builder->groupBy('course.id');
        $builder->orderBy('course.id', 'DESC');

        return $builder->get()->getResultArray();
    }

    
   public function filter_course_for_teacher($class_id = "all", $user_id = "all", $status = "all", $school_id = "all")
    {
        $teacher_user_id = session()->get('user_id');
        $current_school_id = school_id();

        // 1. Get teacher ID for the current school to check permissions
        $teacher = \db()->table('teachers')
            ->where('school_id', school_id())
            ->where('user_id', $teacher_user_id)
            ->get()
            ->getRowArray();
        $real_teacher_id = isset($teacher['id']) ? $teacher['id'] : 0;

        // 2. Get allowed class IDs from permissions (marks = 1, interpreted as manage)
        $allowed_class_ids = [];
        if ($real_teacher_id) {
            $perms = \db()->table('teacher_permissions')
                ->select('class_id')
                ->where('teacher_id', $real_teacher_id)
                ->where('marks', 1)
                ->get()
                ->getResultArray();
            $allowed_class_ids = array_column($perms, 'class_id');
        }

        // STRICT CHECK: If no class permissions, return empty immediately.
        // The user requirement is strict: courses are visible ONLY if linked to an allowed class.
        if (empty($allowed_class_ids)) {
            return [];
        }

        $builder = \db()->table('course');
        $builder->select('course.*, GROUP_CONCAT(DISTINCT t.name SEPARATOR ", ") AS teacher_names');
        $builder->where('course.school_id', $current_school_id);

        // Join course_classes to filter by allowed classes
        // Use INNER JOIN to ensure we only get courses that ARE in these classes
        $builder->join('course_classes cc', 'cc.course_id = course.id', 'inner');
        
        // Join course_teachers and users for display info
        $builder->join('course_teachers ct', 'ct.course_id = course.id', 'left');
        $builder->join('users t', 't.id = ct.user_id', 'left');

        // Apply Permission Filter: Course MUST be in one of the allowed classes
        $builder->whereIn('cc.class_id', $allowed_class_ids);

        // Filter by class if selected in dropdown
        if ($class_id != "all" && !empty($class_id)) {
            $builder->where('cc.class_id', $class_id);
        }

        // Filter by status
        if ($status != "all" && !empty($status)) {
            $builder->where('course.status', $status);
        }

        $builder->groupBy('course.id');
        $builder->orderBy('course.id', 'DESC');

        return $builder->get()->getResultArray();
    }

    public function filter_course_for_student($class_id, $user_id, $status, $school_id)
    {
        // $class_id = $this->get_class_id_by_user(session()->get('user_id'));
        // // \db()->where('school_id', school_id());
        $builder = \db()->table('course');
        $builder->select('*, course.id as id, course.thumbnail as thumbnail');
        $builder->join('students', 'course.school_id = students.school_id', 'left');
        $builder->join('schools', 'schools.id = course.school_id', 'left');
        $builder->where('students.user_id', session()->get('user_id'));
        $builder->where('course.status', 'active');
        $builder->where('students.status', 1);

        if ($user_id != "all") {
            $builder->where('course.user_id', $user_id);
        }
        if ($school_id != "all") {
            $builder->where('course.school_id', $school_id);
        }
        if ($class_id != "all") {
            $builder->where('course.class_id', $class_id);
        }

        return $builder->get()->getResultArray();
    }

    public function get_subject_by_class_id($class_id = "")
    {
        return \db()->table('subjects')
            ->where('school_id', school_id())
            ->get()
            ->getResultArray();
    }

    public function get_status_wise_courses($status = "")
    {
        if ($status != "") {
            $courses = \db()->table('course')->where('school_id', school_id())->get()->getResult();
        } else {
            if (session()->get('teacher_login') == 1) {
                $teacher_id  = session()->get('user_id');
                $courses['inactive'] = \db()->table('course')->where('school_id', school_id())->get()->getResult();
                $courses['active'] = \db()->table('course')->where('school_id', school_id())->get()->getResult();
            } else {
                $courses['inactive'] = \db()->table('course')->where('school_id', school_id())->get()->getResult();
                $courses['active'] = \db()->table('course')->where('school_id', school_id())->get()->getResult();
            }
        }
        return $courses;
    }

    public function get_section($type_by, $id)
    {
        $builder = \db()->table('course_section')->orderBy('orders', 'ASC');
        if ($type_by == 'course') {
            $builder->where('course_id', $id);
            return $builder->get()->getResult();
        } elseif ($type_by == 'section') {
            $builder->where('id', $id);
            return $builder->get()->getResult();
        }
    }

    // Pagination des sections
    public function get_sections_paginated($course_id, $limit = 10, $offset = 0)
    {
        return \db()->table('course_section')
            ->where('course_id', $course_id)
            ->orderBy('orders', 'ASC')
            ->limit($limit, $offset)
            ->get()
            ->getResultArray();
    }

    public function count_sections($course_id)
    {
        return \db()->table('course_section')->where('course_id', $course_id)->countAllResults();
    }

    public function course_activity($course_id)
    {
        $course = \db()->table('course')
            ->where('id', $course_id)
            ->where('school_id', school_id())
            ->get()
            ->getRowArray();
        if (!$course) {
            return false;
        }

        $nextStatus = (($course['status'] ?? '') === 'active') ? 'inactive' : 'active';
        return \db()->table('course')
            ->where('id', $course_id)
            ->update(['status' => $nextStatus]);
    }

    public function delete_course($course_id)
    {
        $course = \db()->table('course')
            ->where('id', $course_id)
            ->where('school_id', school_id())
            ->get()
            ->getRowArray();

        if (file_exists('uploads/course_thumbnail/' . $course['thumbnail']))
            unlink('uploads/course_thumbnail/' . $course['thumbnail']);

        \db()->table('course')->where('id', $course_id)->delete();
        \db()->table('course_section')->where('course_id', $course_id)->delete();
        \db()->table('lesson')->where('course_id', $course_id)->delete();

        $response = array(
            'status' => true,
            'notification' => get_phrase('course_data_deleted_successfully')
        );
        return json_encode($response);
    }

    public function get_lessons($type = "", $id = "")
    {
        $builder = \db()->table('lesson')->orderBy('order', 'ASC');
        if ($type == "course") {
            $builder->where('course_id', $id);
            return $builder->get()->getResult();
        } elseif ($type == "section") {
            $builder->where('section_id', $id);
            return $builder->get()->getResult();
        } elseif ($type == "lesson") {
            $builder->where('id', $id);
            return $builder->get()->getResult();
        } else {
            return $builder->get();
        }
    }

    public function get_subject_by_class($class_id = '')
    {
        $subjects = \db()->table('subjects')->where('school_id', school_id())->get()->getResult()->getResultArray();
        $option = '<option value="">' . get_phrase('select_a_subject') . '</option>';
        $count = 0;
        foreach ($subjects as $subject):
            $count++;
            $option .= '<option value="' . $subject['id'] . '">' . $subject['name'] . '</option>';
        endforeach;

        if ($count > 0) {
            return $option;
        } else {
            return '<option value="">' . get_phrase('data_not_found') . '</option>';;
        }
    }
    // === CLASSES ===
    
// Cette fonction récupère toutes les classes associées à un cours donné
    public function get_classes_by_course($course_id)
    {
        return \db()->table('classes')
            ->select('classes.*')// On sélectionne toutes les colonnes de la table 'classes'
            ->join('course_classes', 'course_classes.class_id = classes.id')// On fait un JOIN avec la table pivot 'course_classes' qui relie les cours aux classes
            ->where('course_classes.course_id', $course_id)// On filtre uniquement les classes qui appartiennent au cours $course_id
            ->get()
            ->getResultArray();
    }

    public function get_courses_by_class($class_id)
    {
        return \db()->table('course')
            ->select('course.*, course.id as id, course.thumbnail as thumbnail')
            ->join('course_classes', 'course_classes.course_id = course.id')
            ->where('course_classes.class_id', $class_id)
            ->where('course.status', 'active')
            ->get()
            ->getResultArray();
    }

    // Cette fonction met à jour les classes liées à un cours

        public function update_course_classes($course_id, $class_ids)
        {
            // Nettoyage
            \db()->table('course_classes')->where('course_id', $course_id)->delete();

            // Normalisation du format
            if (!is_array($class_ids)) {
                $decoded = json_decode($class_ids, true);
                $class_ids = is_array($decoded) ? $decoded : explode(',', $class_ids);
            }

            // Insertion
            foreach ($class_ids as $class_id) {
                if (!empty($class_id)) {
                    \db()->table('course_classes')->insert([
                        'course_id' => $course_id,
                        'class_id'  => (int)$class_id
                    ]);
                }
            }

            log_message('debug', 'Classes liées au cours ' . $course_id . ': ' . json_encode($class_ids));
        }


    // === TEACHERS ===
    // Cette fonction récupère tous les enseignants (mentors) associés à un cours
    public function get_teachers_by_course($course_id)
    {
        return \db()->table('users')
            ->select('users.*')
            ->join('course_teachers', 'course_teachers.user_id = users.id')// On fait un JOIN avec la table pivot 'course_teachers' qui relie les cours aux enseignants
            ->where('course_teachers.course_id', $course_id)// On filtre uniquement les enseignants qui appartiennent au cours $course_id
            ->get()
            ->getResultArray();
    }
    
    // Cette fonction met à jour les enseignants liés à un cours
    public function update_course_teachers($course_id, $teacher_ids)
    {
        \db()->table('course_teachers')->where('course_id', $course_id)->delete();
    // Si $teacher_ids est un tableau (ex: [5, 6, 7])
        if (is_array($teacher_ids)) {
            foreach ($teacher_ids as $user_id) {
                // Pour chaque teacher_id, on crée une nouvelle entrée dans la table pivot
                \db()->table('course_teachers')->insert([
                    'course_id' => $course_id,
                    'user_id'   => $user_id
                ]);
            }
        }
    }

    public function course_add()
    {
        $data['title'] = $this->request->getPost('title');
        // $data['class_id'] = $this->request->getPost('class_id');
        // $data['user_id'] = $this->request->getPost('user_id');
        // $data['subject_id'] = $this->request->getPost('subject_id');
        $data['description'] = $this->request->getPost('description');
        $data['outcomes'] = $this->request->getPost('outcomes');
        
        // Traitement des prérequis (tags)
        $prerequisites_json = $this->request->getPost('prerequisites');
        if (!empty($prerequisites_json)) {
            // Nettoyer et valider le JSON
            $prerequisites_array = json_decode($prerequisites_json, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($prerequisites_array)) {
                // Nettoyer chaque tag (supprimer les espaces, éviter les doublons)
                $cleaned_tags = array_map('trim', $prerequisites_array);
                $cleaned_tags = array_filter($cleaned_tags); // Supprimer les tags vides
                $cleaned_tags = array_unique($cleaned_tags); // Supprimer les doublons
                
                if (!empty($cleaned_tags)) {
                    $data['prerequisites'] = json_encode($cleaned_tags, JSON_UNESCAPED_UNICODE);
                } else {
                    $data['prerequisites'] = null;
                }
            } else {
                $data['prerequisites'] = null;
            }
        } else {
            $data['prerequisites'] = null;
        }
        
        // Traitement du champ d'activité
        $field_of_activity = $this->request->getPost('field_of_activity');
        if (!empty($field_of_activity)) {
            $data['field_of_activity'] = html_escape($field_of_activity);
        } else {
            $data['field_of_activity'] = null;
        }

        // Traitement du style du cours
        $course_style = $this->request->getPost('course_style');
        if (!empty($course_style)) {
            $data['course_style'] = html_escape($course_style);
        } else {
            $data['course_style'] = null;
        }
        
        $data['course_overview_provider'] = $this->request->getPost('course_overview_provider');
        $data['course_overview_url'] = $this->request->getPost('course_overview_url');
        $data['thumbnail'] = rand() . '.jpg';
    // récupérer la valeur envoyée par le switch
    $status = $this->request->getPost('status'); // 'active' ou 'inactive'
    $data['status'] = !empty($status) ? $status : 'inactive'; // sécurité par défaut
        $data['date_added'] = strtotime(date('d M Y'));
        $data['school_id'] = school_id();

        \db()->table('course')->insert($data);
    $course_id = \db()->insertID(); // récupère l'id du cours créé
        $courseThumbnail = $this->uploadedFile('course_thumbnail');
        if ($courseThumbnail && $courseThumbnail->getError() === UPLOAD_ERR_OK) {
            $courseThumbnail->move('uploads/course_thumbnail', $data['thumbnail'], true);
        }
        // Récupérer les classes et mentors sélectionnés
    $class_ids = $this->request->getPost('class_id');   // tableau d'ids
    $teacher_ids = $this->request->getPost('user_id');  // tableau d'ids

    // Ensure teacher_ids is an array
    if (!empty($teacher_ids) && !is_array($teacher_ids)) {
        $teacher_ids = array($teacher_ids);
    }

    // Insérer dans table pivot course_classes
    if (!empty($class_ids)) {
        foreach ($class_ids as $class_id) {
            \db()->table('course_classes')->insert([
                'course_id' => $course_id,
                'class_id' => $class_id
            ]);
        }
    }

    // Insérer dans table pivot course_teachers
    if (!empty($teacher_ids)) {
        foreach ($teacher_ids as $teacher_id) {
            \db()->table('course_teachers')->insert([
                'course_id' => $course_id,
                'user_id' => $teacher_id
            ]);
        }
    }

    return $course_id;
    }

    public function course_edit($course_id)
    {
        $data['title'] = $this->request->getPost('title');
        // $data['class_id'] = $this->request->getPost('class_id');
        // $data['user_id'] = $this->request->getPost('user_id');
        // $data['subject_id'] = $this->request->getPost('subject_id');
        $data['description'] = $this->request->getPost('description');
        $data['outcomes'] = $this->request->getPost('outcomes');
        
        // Traitement des prérequis (tags)
        $prerequisites_json = $this->request->getPost('prerequisites');
        if (!empty($prerequisites_json)) {
            // Nettoyer et valider le JSON
            $prerequisites_array = json_decode($prerequisites_json, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($prerequisites_array)) {
                // Nettoyer chaque tag (supprimer les espaces, éviter les doublons)
                $cleaned_tags = array_map('trim', $prerequisites_array);
                $cleaned_tags = array_filter($cleaned_tags); // Supprimer les tags vides
                $cleaned_tags = array_unique($cleaned_tags); // Supprimer les doublons
                
                if (!empty($cleaned_tags)) {
                    $data['prerequisites'] = json_encode($cleaned_tags, JSON_UNESCAPED_UNICODE);
                } else {
                    $data['prerequisites'] = null;
                }
            } else {
                $data['prerequisites'] = null;
            }
        } else {
            $data['prerequisites'] = null;
        }
        
        // Traitement du champ d'activité
        $field_of_activity = $this->request->getPost('field_of_activity');
        if (!empty($field_of_activity)) {
            $data['field_of_activity'] = html_escape($field_of_activity);
        } else {
            $data['field_of_activity'] = null;
        }

        // Traitement du style du cours
        $course_style = $this->request->getPost('course_style');
        if (!empty($course_style)) {
            $data['course_style'] = html_escape($course_style);
        } else {
            $data['course_style'] = null;
        }
        
        $data['course_overview_provider'] = $this->request->getPost('course_overview_provider');
        $data['course_overview_url'] = $this->request->getPost('course_overview_url');
        $data['last_modified'] = strtotime(date('d M Y'));

        if ($this->isUploadOk('course_thumbnail')) {
            unlink('uploads/course_thumbnail/' . $this->request->getPost('current_thumbnail'));
            $data['thumbnail'] = rand() . '.jpg';
            $this->uploadedFile('course_thumbnail')->move('uploads/course_thumbnail', $data['thumbnail'], true);
        }

        \db()->table('course')->where('id', $course_id)->update($data);
        // Mettre à jour les relations multi-classes et multi-teachers
        $class_ids = $this->request->getPost('class_id');
        $teacher_ids = $this->request->getPost('user_id');

        // Ensure teacher_ids is an array
        if (!empty($teacher_ids) && !is_array($teacher_ids)) {
            $teacher_ids = array($teacher_ids);
        }

        $this->update_course_classes($course_id, $class_ids);
        $this->update_course_teachers($course_id, $teacher_ids);
    }

    public function add_course_section($course_id)
    {
        // Get max order for this course
        $max_order_row = \db()->table('course_section')
            ->selectMax('orders')
            ->where('course_id', $course_id)
            ->get()
            ->getRow();
        $max_order = $max_order_row->orders ?? null;
        
        $data['title'] = html_escape($this->request->getPost('title'));
        $data['course_id'] = $course_id;
        $data['orders'] = ($max_order !== null) ? $max_order + 1 : 1;
        \db()->table('course_section')->insert($data);
        $section_id = \db()->insertID();

        $course_details = $this->get_course_by_id($course_id);
        $previous_sections = json_decode($course_details['section'] ?? '[]');
        if (!is_array($previous_sections)) {
            $previous_sections = [];
        }

        if (isset($previous_sections) > 0) {
            array_push($previous_sections, $section_id);
            $updater['section'] = json_encode($previous_sections);
            \db()->table('course')->where('id', $course_id)->update($updater);
        } else {
            $previous_sections = array();
            array_push($previous_sections, $section_id);
            $updater['section'] = json_encode($previous_sections);
            \db()->table('course')->where('id', $course_id)->update($updater);
        }
    }

    public function edit_course_section($section_id)
    {
        $data['title'] = $this->request->getPost('title');
        \db()->table('course_section')->where('id', $section_id)->update($data);
    }

    public function add_lesson()
    {
        $data['course_id'] = html_escape($this->request->getPost('course_id'));
        $data['title'] = html_escape($this->request->getPost('title'));
        $data['section_id'] = html_escape($this->request->getPost('section_id'));

        $lesson_type_array = explode('-', $this->request->getPost('lesson_type'));
        $lesson_type = $lesson_type_array[0];

        $data['attachment_type'] = $lesson_type_array[1];
        $data['lesson_type'] = $lesson_type;

        if ($lesson_type == 'video') {
            // This portion is for web application's video lesson
            $lesson_provider = $this->request->getPost('lesson_provider');
            if ($lesson_provider == 'youtube' || $lesson_provider == 'vimeo') {
                if ($this->request->getPost('video_url') == "" || $this->request->getPost('duration') == "") {
                    session()->setFlashdata('error_message', get_phrase('invalid_lesson_url_and_duration'));
                    redirect(site_url('addons/courses/course_edit/' . $data['course_id']));
                }
                $data['video_url'] = html_escape($this->request->getPost('video_url'));

                $duration_formatter = explode(':', $this->request->getPost('duration'));
                $hour = sprintf('%02d', $duration_formatter[0]);
                $min = sprintf('%02d', $duration_formatter[1]);
                $sec = sprintf('%02d', $duration_formatter[2]);
                $data['duration'] = $hour . ':' . $min . ':' . $sec;

                $video_details = $this->video_model->getVideoDetails($data['video_url']);
                $data['video_type'] = $video_details['provider'];
            } elseif ($lesson_provider == 'html5') {
                if ($this->request->getPost('html5_video_url') == "" || $this->request->getPost('html5_duration') == "") {
                    session()->setFlashdata('error_message', get_phrase('invalid_lesson_url_and_duration'));
                    redirect(site_url('addons/courses/course_edit/' . $data['course_id']));
                }
                $data['video_url'] = html_escape($this->request->getPost('html5_video_url'));
                $duration_formatter = explode(':', $this->request->getPost('html5_duration'));
                $hour = sprintf('%02d', $duration_formatter[0]);
                $min = sprintf('%02d', $duration_formatter[1]);
                $sec = sprintf('%02d', $duration_formatter[2]);
                $data['duration'] = $hour . ':' . $min . ':' . $sec;
                $data['video_type'] = 'html5';
            } elseif ($lesson_provider == 'mydevice') {
                $data['video_type'] = 'mydevice';

                if (!file_exists('uploads/videos')) {
                    mkdir('uploads/videos', 0777, true);
                }


                // Configuration de l'upload
                $upload_path = './uploads/videos/';
                $allowed_types = array('mp4', 'avi', 'mov');
                $max_size = 102400; // 100MB

                if ($this->isUploadOk('userfileMe')) {
                    $userVideoFile = $this->uploadedFile('userfileMe');
                    // Vérifiez le type de fichier
                    $file_type = strtolower(pathinfo($userVideoFile->getClientName(), PATHINFO_EXTENSION));
                    if (in_array($file_type, $allowed_types)) {
                        // Vérifiez la taille du fichier
                        if ($userVideoFile->getSize() <= $max_size * 1024) {
                            // Déplacez le fichier vers le dossier de téléchargement
                            $data['video_uplaod'] = rand() . '.mp4';
                            $file_name = $data['video_uplaod'];
                            $destination = $upload_path . $file_name;

                            if ($userVideoFile->move('uploads/videos', $file_name, true)) {
                                // Upload réussi
                                $datavideo['upload_data'] = array(
                                    'file_name' => $file_name,
                                    'file_type' => $file_type,
                                    'file_path' => $upload_path,
                                    'full_path' => $destination,
                                    'file_size' => $userVideoFile->getSize(),
                                );
                            } else {
                                // Erreur de déplacement du fichier
                                session()->setFlashdata('error_message', get_phrase('There was a problem moving the file.'));
                                redirect(site_url('addons/courses/course_edit/' . $data['course_id']));
                            }
                        } else {
                            // Fichier trop grand
                            session()->setFlashdata('error_message', get_phrase('The file size exceeds the limit.'));
                            redirect(site_url('addons/courses/course_edit/' . $data['course_id']));
                        }
                    } else {
                        // Type de fichier non autorisé
                        session()->setFlashdata('error_message', get_phrase('The file type is not allowed.'));
                        redirect(site_url('addons/courses/course_edit/' . $data['course_id']));
                    }
                } else {
                    // Erreur d'upload
                    session()->setFlashdata('error_message', get_phrase('No file uploaded or there was an upload error.'));
                    redirect(site_url('addons/courses/course_edit/' . $data['course_id']));
                }
            } else {
                session()->setFlashdata('error_message', get_phrase('invalid_lesson_provider'));
                redirect(site_url('addons/courses/course_edit/' . $data['course_id']));
            }
        } else {
            if (!$this->isUploadOk('attachment')) {
                session()->setFlashdata('error_message', get_phrase('invalid_attachment'));
                redirect(site_url('addons/courses/course_edit/' . $data['course_id']));
            } else {
                $data['duration']   = 0;
                $attachmentFile     = $this->uploadedFile('attachment');
                $fileName           = $attachmentFile->getClientName();
                $tmp                = explode('.', $fileName);
                $fileExtension      = end($tmp);
                $uploadable_file    =  md5(uniqid(rand(), true)) . '.' . $fileExtension;
                $data['attachment'] = $uploadable_file;

                if (!file_exists('uploads/lesson_files')) {
                    mkdir('uploads/lesson_files', 0777, true);
                }
                $attachmentFile->move('uploads/lesson_files', $uploadable_file, true);
            }
        }

        $data['date_added'] = strtotime(date('D, d-M-Y'));
        $data['summary'] = $this->request->getPost('summary');

        \db()->table('lesson')->insert($data);
        $inserted_id = \db()->insertID();

        if ($this->isUploadOk('thumbnail')) {
            if (!file_exists('uploads/thumbnails/lesson_thumbnails')) {
                mkdir('uploads/thumbnails/lesson_thumbnails', 0777, true);
            }
            $this->uploadedFile('thumbnail')->move('uploads/thumbnails/lesson_thumbnails', $inserted_id . '.jpg', true);
        }
    }
    private function delete_old_files($path)
    {
        $files = glob($path . '*'); // Obtenir tous les fichiers dans le répertoire
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file); // Supprimer chaque fichier
            }
        }
    }

    public function edit_lesson($lesson_id)
    {
        $previous_data = \db()->table('lesson')
            ->where('id', $lesson_id)
            ->where('school_id', school_id())
            ->get()
            ->getRowArray();

        $data['course_id'] = html_escape($this->request->getPost('course_id'));
        $data['title'] = html_escape($this->request->getPost('title'));
        $data['section_id'] = html_escape($this->request->getPost('section_id'));

        $lesson_type_array = explode('-', $this->request->getPost('lesson_type'));
        $lesson_type = $lesson_type_array[0];

        $data['attachment_type'] = $lesson_type_array[1];
        $data['lesson_type'] = $lesson_type;
        if ($lesson_type == 'video') {
            $lesson_provider = $this->request->getPost('lesson_provider');
            if ($lesson_provider == 'youtube' || $lesson_provider == 'vimeo') {
                if ($this->request->getPost('video_url') == "" || $this->request->getPost('duration') == "") {
                    session()->setFlashdata('error_message', get_phrase('invalid_lesson_url_and_duration'));
                    redirect(site_url(strtolower(session()->get('role')) . '/course_form/course_edit/' . $data['course_id']));
                }
                $data['video_url'] = html_escape($this->request->getPost('video_url'));

                $duration_formatter = explode(':', $this->request->getPost('duration'));
                $hour = sprintf('%02d', $duration_formatter[0]);
                $min = sprintf('%02d', $duration_formatter[1]);
                $sec = sprintf('%02d', $duration_formatter[2]);
                $data['duration'] = $hour . ':' . $min . ':' . $sec;

                $video_details = $this->video_model->getVideoDetails($data['video_url']);
                $data['video_type'] = $video_details['provider'];
            } elseif ($lesson_provider == 'html5') {
                if ($this->request->getPost('html5_video_url') == "" || $this->request->getPost('html5_duration') == "") {
                    session()->setFlashdata('error_message', get_phrase('invalid_lesson_url_and_duration'));
                    redirect(site_url(strtolower(session()->get('role')) . '/course_form/course_edit/' . $data['course_id']));
                }
                $data['video_url'] = html_escape($this->request->getPost('html5_video_url'));

                $duration_formatter = explode(':', $this->request->getPost('html5_duration'));
                $hour = sprintf('%02d', $duration_formatter[0]);
                $min = sprintf('%02d', $duration_formatter[1]);
                $sec = sprintf('%02d', $duration_formatter[2]);
                $data['duration'] = $hour . ':' . $min . ':' . $sec;
                $data['video_type'] = 'html5';

                if ($this->isUploadOk('thumbnail')) {
                    if (!file_exists('uploads/thumbnails/lesson_thumbnails')) {
                        mkdir('uploads/thumbnails/lesson_thumbnails', 0777, true);
                    }
                    $this->uploadedFile('thumbnail')->move('uploads/thumbnails/lesson_thumbnails', $lesson_id . '.jpg', true);
                }
            } elseif ($lesson_provider == 'mydevice') {



                // $data['video_type'] = 'mydevice';
                //  $data['video_uplaod'] = rand().'.mp4';
                // Configuration de l'upload
                $upload_path = './uploads/videos/';
                $allowed_types = array('mp4', 'avi', 'mov');
                $max_size = 102400; // 100MB

                if ($this->isUploadOk('userfileMe')) {
                    $userVideoFile = $this->uploadedFile('userfileMe');
                    // Vérifiez le type de fichier
                    $file_type = strtolower(pathinfo($userVideoFile->getClientName(), PATHINFO_EXTENSION));
                    if (in_array($file_type, $allowed_types)) {
                        // Vérifiez la taille du fichier
                        if ($userVideoFile->getSize() <= $max_size * 1024) {
                            // Déplacez le fichier vers le dossier de téléchargement
                            $data['video_uplaod'] = rand() . '.mp4';
                            $file_name = $data['video_uplaod'];
                            $destination = $upload_path . $file_name;

                            if ($userVideoFile->move('uploads/videos', $file_name, true)) {
                                // Upload réussi
                                $datavideo['upload_data'] = array(
                                    'file_name' => $file_name,
                                    'file_type' => $file_type,
                                    'file_path' => $upload_path,
                                    'full_path' => $destination,
                                    'file_size' => $userVideoFile->getSize(),
                                );
                                $this->delete_old_files('uploads/videos/' . $previous_data['video_uplaod']);
                            } else {
                                // Erreur de déplacement du fichier
                                session()->setFlashdata('error_message', get_phrase('There was a problem moving the file.'));
                                redirect(site_url('addons/courses/course_edit/' . $data['course_id']));
                            }
                        } else {
                            // Fichier trop grand
                            session()->setFlashdata('error_message', get_phrase('The file size exceeds the limit.'));
                            redirect(site_url('addons/courses/course_edit/' . $data['course_id']));
                        }
                    } else {
                        // Type de fichier non autorisé
                        session()->setFlashdata('error_message', get_phrase('The file type is not allowed.'));
                        redirect(site_url('addons/courses/course_edit/' . $data['course_id']));
                    }
                } else {
                    // Erreur d'upload
                    session()->setFlashdata('error_message', get_phrase('No file uploaded or there was an upload error.'));
                    redirect(site_url('addons/courses/course_edit/' . $data['course_id']));
                }
            } else {
                session()->setFlashdata('error_message', get_phrase('invalid_lesson_provider'));
                redirect(site_url(strtolower(session()->get('role')) . '/course_form/course_edit/' . $data['course_id']));
            }
            $data['attachment'] = "";
        } else {
            if ($this->isUploadOk('attachment')) {
                // unlinking previous attachments
                if ($previous_data['attachment'] != "") {
                    unlink('uploads/lesson_files/' . $previous_data['attachment']);
                }

                $attachmentFile     = $this->uploadedFile('attachment');
                $fileName           = $attachmentFile->getClientName();
                $tmp                = explode('.', $fileName);
                $fileExtension      = end($tmp);
                $uploadable_file    =  md5(uniqid(rand(), true)) . '.' . $fileExtension;
                $data['attachment'] = $uploadable_file;
                $data['video_type'] = "";
                $data['duration'] = "";
                $data['video_url'] = "";
                if (!file_exists('uploads/lesson_files')) {
                    mkdir('uploads/lesson_files', 0777, true);
                }
                $attachmentFile->move('uploads/lesson_files', $uploadable_file, true);
            }
        }

        $data['last_modified'] = strtotime(date('D, d-M-Y'));
        $data['summary'] = $this->request->getPost('summary');

        \db()->table('lesson')->where('id', $lesson_id)->update($data);
    }

    public function delete_lesson($lesson_id)
    {
        \db()->table('lesson')->where('id', $lesson_id)->delete();
        $response = array(
            'status' => true,
            'notification' => get_phrase('lesson_deleted_successfully')
        );
        return json_encode($response);
    }

    // Adding quiz functionalities
    public function add_quiz($course_id = "")
    {
        $data['course_id'] = $course_id;
        $data['title'] = html_escape($this->request->getPost('title'));
        $data['section_id'] = html_escape($this->request->getPost('section_id'));

        $data['lesson_type'] = 'quiz';
        $data['duration'] = 0;
        $data['date_added'] = strtotime(date('D, d-M-Y'));
        $data['summary'] = html_escape($this->request->getPost('summary'));
        \db()->table('lesson')->insert($data);
    }

    // updating quiz functionalities
    public function edit_quiz($lesson_id = "")
    {
        $data['title'] = html_escape($this->request->getPost('title'));
        $data['section_id'] = html_escape($this->request->getPost('section_id'));
        $data['last_modified'] = strtotime(date('D, d-M-Y'));
        $data['summary'] = html_escape($this->request->getPost('summary'));
        \db()->table('lesson')->where('id', $lesson_id)->update($data);
    }

    public function delete_course_section($course_id, $section_id)
    {
        \db()->table('course_section')
            ->where('id', $section_id)
            ->delete();

        \db()->table('lesson')
            ->where('course_id', $course_id)
            ->where('section_id', $section_id)
            ->delete();

        $course_details = $this->get_course_by_id($course_id);
        $previous_sections = json_decode($course_details['section'] ?? '[]');
        if (!is_array($previous_sections)) {
            $previous_sections = [];
        }

        if (count($previous_sections) > 0) {
            $new_section = array();
            for ($i = 0; $i < count($previous_sections); $i++) {
                if ($previous_sections[$i] != $section_id) {
                    array_push($new_section, $previous_sections[$i]);
                }
            }
            $updater['section'] = json_encode($new_section);
            \db()->table('course')
                ->where('id', $course_id)
                ->update($updater);
        }
        $response = array(
            'status' => true,
            'notification' => get_phrase('course_section_deleted_successfully')
        );
        return json_encode($response);
    }

    public function sort_section($section_json, $start_order = 1)
    {
        $sections = json_decode($section_json);
        
        if (!is_array($sections) || empty($sections)) {
            return false;
        }
        
        foreach ($sections as $key => $value) {
            $section_id = (int) $value; // Ensure integer
            $new_order = $start_order + $key; // Use start_order instead of key + 1
            
            \db()->table('course_section')->where('id', $section_id)->update(['orders' => $new_order]);
        }
        
        return true;
    }
    
    // Move a section to a specific position
    public function move_section_to_position($section_id, $target_position, $course_id)
    {
        // Get current section info
        $section = \db()->table('course_section')->where('id', $section_id)->get()->getRow();
        if (!$section) {
            return false;
        }
        
        $current_order = (int) $section->orders;
        $target_order = (int) $target_position;
        
        if ($current_order === $target_order) {
            return true; // Already in position
        }
        
        \db()->transBegin();
        
        try {
            if ($target_order < $current_order) {
                // Moving up: increment orders of sections between target and current
                \db()->table('course_section')
                    ->set('orders', 'orders + 1', false)
                    ->where('course_id', $course_id)
                    ->where('orders >=', $target_order)
                    ->where('orders <', $current_order)
                    ->update();
            } else {
                // Moving down: decrement orders of sections between current and target
                \db()->table('course_section')
                    ->set('orders', 'orders - 1', false)
                    ->where('course_id', $course_id)
                    ->where('orders >', $current_order)
                    ->where('orders <=', $target_order)
                    ->update();
            }
            
            // Set the section to target position
            \db()->table('course_section')->where('id', $section_id)->update(['orders' => $target_order]);
            
            if (\db()->transStatus() === false) {
                \db()->transRollback();
                return false;
            }
            
            \db()->transCommit();
            return true;
            
        } catch (\Throwable $e) {
            \db()->transRollback();
            return false;
        }
    }

    public function sort_lesson($lesson_json)
    {
        $lessons = json_decode($lesson_json);
        foreach ($lessons as $key => $value) {
            $updater = array(
                'order' => $key + 1
            );
            \db()->table('lesson')->where('id', $value)->update($updater);
        }
    }

    public function get_quiz_questions($quiz_id)
    {
        return \db()->table('question')
            ->where('quiz_id', $quiz_id)
            ->orderBy('order', 'ASC')
            ->get();
    }

    public function sort_question($question_json)
    {
        $questions = json_decode($question_json);
        foreach ($questions as $key => $value) {
            $updater = array(
                'order' => $key + 1
            );
            \db()->table('question')->where('id', $value)->update($updater);
        }
    }
    // Add Quiz Questions
    public function add_quiz_questions($quiz_id)
    {
        $question_type = $this->request->getPost('question_type');
        if ($question_type == 'mcq') {
            $response = $this->add_multiple_choice_question($quiz_id);
            return $response;
        }
    }
    // multiple_choice_question crud functions
    public function add_multiple_choice_question($quiz_id)
    {
        if (sizeof($this->request->getPost('options')) != $this->request->getPost('number_of_options')) {
            return false;
        }
        foreach ($this->request->getPost('options') as $option) {
            if ($option == "") {
                return false;
            }
        }
        if (sizeof($this->request->getPost('correct_answers')) == 0) {
            $correct_answers = [""];
        } else {
            $correct_answers = $this->request->getPost('correct_answers');
        }
        $data['quiz_id']            = $quiz_id;
        $data['title']              = html_escape($this->request->getPost('title'));
        $data['number_of_options']  = html_escape($this->request->getPost('number_of_options'));
        $data['type']               = 'multiple_choice';
        $data['options']            = json_encode($this->request->getPost('options'));
        $data['correct_answers']    = json_encode($correct_answers);
        \db()->table('question')->insert($data);
        return true;
    }
    public function update_quiz_questions($question_id)
    {
        $question_type = $this->request->getPost('question_type');
        if ($question_type == 'mcq') {
            $response = $this->update_multiple_choice_question($question_id);
            return $response;
        }
    }
    // update multiple choice question
    public function update_multiple_choice_question($question_id)
    {
        if (sizeof($this->request->getPost('options')) != $this->request->getPost('number_of_options')) {
            return false;
        }
        foreach ($this->request->getPost('options') as $option) {
            if ($option == "") {
                return false;
            }
        }

        if (sizeof($this->request->getPost('correct_answers')) == 0) {
            $correct_answers = [""];
        } else {
            $correct_answers = $this->request->getPost('correct_answers');
        }

        $data['title']              = html_escape($this->request->getPost('title'));
        $data['number_of_options']  = html_escape($this->request->getPost('number_of_options'));
        $data['type']               = 'multiple_choice';
        $data['options']            = json_encode($this->request->getPost('options'));
        $data['correct_answers']    = json_encode($correct_answers);
        \db()->table('question')->where('id', $question_id)->update($data);
        return true;
    }
    public function delete_quiz_question($question_id)
    {
        \db()->table('question')->where('id', $question_id)->delete();
        $response = array(
            'status' => true,
            'notification' => get_phrase('quiz_questions_deleted_successfully')
        );
        return json_encode($response);
    }
    public function get_quiz_question_by_id($question_id)
    {
        return \db()->table('question')
            ->where('id', $question_id)
            ->orderBy('order', 'ASC')
            ->get();
    }

    // code of mark this lesson as completed
    function save_course_progress()
    {
        $lesson_id = $this->request->getPost('lesson_id');
        $progress = $this->request->getPost('progress');
        $user_id   = session()->get('user_id');
        $user_details = \db()->table('users')
            ->select('watch_history')
            ->where('id', $user_id)
            ->get()
            ->getRowArray();
        $watch_history = $user_details['watch_history'] ?? '';
        $watch_history_array = array();
        if ($watch_history == '') {
            array_push($watch_history_array, array('lesson_id' => $lesson_id, 'progress' => $progress));
        } else {
            $founder = false;
            $watch_history_array = json_decode($watch_history, true);
            if (!is_array($watch_history_array)) {
                $watch_history_array = [];
            }
            for ($i = 0; $i < count($watch_history_array); $i++) {
                $watch_history_for_each_lesson = $watch_history_array[$i];
                if ($watch_history_for_each_lesson['lesson_id'] == $lesson_id) {
                    $watch_history_for_each_lesson['progress'] = $progress;
                    $watch_history_array[$i]['progress'] = $progress;
                    $founder = true;
                }
            }
            if (!$founder) {
                array_push($watch_history_array, array('lesson_id' => $lesson_id, 'progress' => $progress));
            }
        }
        $data['watch_history'] = json_encode($watch_history_array);
        \db()->table('users')
            ->where('id', $user_id)
            ->update($data);

        return $progress;
    }

    public function get_class_id_by_user($user_id = "")
    {
        if ($user_id == "") {
            $user_id = session()->get('user_id');
        }
        $student = \db()->table('students')
            ->select('id')
            ->where('user_id', $user_id)
            ->where('school_id', school_id())
            ->get()
            ->getRowArray();
        $student_id = (int) ($student['id'] ?? 0);
        if ($student_id <= 0) {
            return null;
        }

        $enrol = \db()->table('enrols')
            ->select('class_id')
            ->where('student_id', $student_id)
            ->where('school_id', school_id())
            ->where('session', active_session())
            ->get()
            ->getRowArray();
        return $enrol['class_id'] ?? null;
    }

    public function get_exams($type = "", $id = "")
    {
        $builder = \db()->table('exams')->orderBy('id', 'ASC'); // Tri par "id" au lieu de "order"
        if ($type == "exam") {
            $query = $builder->where('school_id', school_id())->get()->getResult();
            if ($query === FALSE) {
                return FALSE;
            }
            return $query;
        } else {
            $query = $builder->get();
            if ($query === FALSE) {
                return FALSE;
            }
            return $query;
        }
    }

    public function get_exam_questions($exam_id)
    {
        return \db()->table('exam_questions')
            ->where('exam_id', $exam_id)
            ->orderBy('order', 'ASC')
            ->get();
    }

    public function get_exam_question_by_id($question_id)
    {
        return \db()->table('exam_questions')
            ->where('id', $question_id)
            ->orderBy('order', 'ASC')
            ->get();
    }

    public function add_exam_questions($exam_id)
    {
        $question_type = $this->request->getPost('question_type');
        if ($question_type == 'mcq') {
            $response = $this->add_multiple_choice_exam_question($exam_id);
            return $response;
        }
    }

    public function add_multiple_choice_exam_question($exam_id)
    {
        if (sizeof($this->request->getPost('options')) != $this->request->getPost('number_of_options')) {
            return false;
        }
        foreach ($this->request->getPost('options') as $option) {
            if ($option == "") {
                return false;
            }
        }
        if (sizeof($this->request->getPost('correct_answers')) == 0) {
            $correct_answers = [""];
        } else {
            $correct_answers = $this->request->getPost('correct_answers');
        }
        $data['exam_id']            = $exam_id;
        $data['title']              = html_escape($this->request->getPost('title'));
        $data['number_of_options']  = html_escape($this->request->getPost('number_of_options'));
        $data['type']               = 'multiple_choice';
        $data['options']            = json_encode($this->request->getPost('options'));
        $data['correct_answers']    = json_encode($correct_answers);
        \db()->table('exam_questions')->insert($data);
        return true;
    }

    public function update_exam_questions($question_id)
    {
        $question_type = $this->request->getPost('question_type');
        if ($question_type == 'mcq') {
            $response = $this->update_multiple_choice_exam_question($question_id);
            return $response;
        }
    }

    public function update_multiple_choice_exam_question($question_id)
    {
        if (sizeof($this->request->getPost('options')) != $this->request->getPost('number_of_options')) {
            return false;
        }
        foreach ($this->request->getPost('options') as $option) {
            if ($option == "") {
                return false;
            }
        }

        if (sizeof($this->request->getPost('correct_answers')) == 0) {
            $correct_answers = [""];
        } else {
            $correct_answers = $this->request->getPost('correct_answers');
        }

        $data['title']              = html_escape($this->request->getPost('title'));
        $data['number_of_options']  = html_escape($this->request->getPost('number_of_options'));
        $data['type']               = 'multiple_choice';
        $data['options']            = json_encode($this->request->getPost('options'));
        $data['correct_answers']    = json_encode($correct_answers);
        \db()->table('exam_questions')->where('id', $question_id)->update($data);
        return true;
    }

    public function delete_exam_question($question_id)
    {
        \db()->table('exam_questions')->where('id', $question_id)->delete();
        return \db()->affectedRows() > 0;
    }

    // Dans models/Lms_model.php
    public function sort_exam_question($question_json)
    {
        $questions = json_decode($question_json);
        foreach ($questions as $key => $value) {
            $updater = array(
                'order' => $key + 1
            );
            \db()->table('exam_questions')->where('id', $value)->update($updater);
        }
    }

    // Get teachers by class selection
    public function get_teachers_by_class_selection($class_ids = []) {
        if (empty($class_ids)) {
            return [];
        }
        
        // Ensure class_ids is an array
        if (!is_array($class_ids)) {
            $class_ids = explode(',', $class_ids);
        }
        
        // 1. Get teachers with permissions
        $teachers = \db()->table('users')
            ->select('users.id, users.name')
            ->join('teachers', 'teachers.user_id = users.id')
            ->join('teacher_permissions', 'teacher_permissions.teacher_id = teachers.id')
            ->whereIn('teacher_permissions.class_id', $class_ids)
            ->where('teacher_permissions.attendance', 1)
            ->where('users.school_id', school_id())
            ->groupBy('users.id')
            ->get()
            ->getResultArray();

        // 2. Get admins
        $admins = \db()->table('users')
            ->select('users.id, users.name')
            ->where('users.school_id', school_id())
            ->groupStart()
            ->where('role', 'admin')
            ->groupEnd()
            ->get()
            ->getResultArray();

        // 3. Merge and deduplicate
        $all_users = array_merge($teachers, $admins);
        $unique_users = [];
        foreach ($all_users as $user) {
            $unique_users[$user['id']] = $user;
        }

        return array_values($unique_users);
    }

    public function get_student_by_user_and_school($user_id, $school_id) {
        return \db()->table('students')
            ->where('user_id', $user_id)
            ->where('school_id', $school_id)
            ->get()
            ->getRowArray();
    }

    public function get_all_categories() {
        return \db()->table('categories')->get()->getResultArray();
    }

    public function get_school_by_id($school_id) {
        return \db()->table('schools')->where('id', $school_id)->get()->getRowArray();
    }

    public function get_lesson_by_id($lesson_id) {
        return \db()->table('lesson')->where('id', $lesson_id)->get()->getRowArray();
    }

    public function get_quiz_by_id($quiz_id) {
        return \db()->table('lesson')->where('id', $quiz_id)->get()->getRowArray();
    }

    public function get_questions_by_quiz($quiz_id, $ordered = false) {
        $builder = \db()->table('question')->where('quiz_id', $quiz_id);
        if ($ordered) {
            $builder->orderBy('order', 'ASC');
        }
        return $builder->get()->getResultArray();
    }

    public function get_lesson_by_course($course_id) {
        return \db()->table('lesson')
            ->where('course_id', $course_id)
            ->orderBy('order', 'ASC')
            ->get()
            ->getRowArray();
    }

    public function get_first_non_quiz_lesson($course_id) {
        return \db()->table('lesson')
            ->where('course_id', $course_id)
            ->where("LOWER(lesson_type) !=", 'quiz')
            ->orderBy('order', 'ASC')
            ->get()
            ->getRowArray();
    }

    public function get_course_section_by_id($section_id) {
        return \db()->table('course_section')
            ->where('id', $section_id)
            ->get()
            ->getRowArray();
    }

    public function insert_lesson($data) {
        \db()->table('lesson')->insert($data);
        return \db()->insertID();
    }

    public function update_lesson($lesson_id, $data) {
        return \db()->table('lesson')
            ->where('id', $lesson_id)
            ->update($data);
    }

    public function delete_questions_by_quiz($quiz_id) {
        return \db()->table('question')
            ->where('quiz_id', $quiz_id)
            ->delete();
    }

    public function insert_question($data) {
        \db()->table('question')->insert($data);
        return \db()->insertID();
    }

    public function delete_exam_questions_by_exam($exam_id) {
        return \db()->table('exam_questions')
            ->where('exam_id', $exam_id)
            ->delete();
    }

    public function insert_exam_question($data) {
        $inserted = \db()->table('exam_questions')->insert($data);
        if ($inserted === false) {
            return false;
        }

        // Some migrated schemas may not expose a usable insertId for this table.
        // Prefer explicit insert success based on affected rows.
        return \db()->affectedRows() > 0;
    }

    public function count_exam_questions($exam_id) {
        return \db()->table('exam_questions')
            ->where('exam_id', $exam_id)
            ->countAllResults();
    }

    public function insert_course_section($data) {
        \db()->table('course_section')->insert($data);
        return \db()->insertID();
    }

    public function get_max_section_order($course_id) {
        $result = \db()->table('course_section')
            ->selectMax('orders')
            ->where('course_id', $course_id)
            ->get()
            ->getRow();
        return isset($result->orders) ? (int) $result->orders : 0;
    }

    public function count_all_questions($table = 'question') {
        return \db()->table($table)->countAll();
    }

    public function update_course($course_id, $data) {
        return \db()->table('course')
            ->where('id', $course_id)
            ->update($data);
    }

    public function get_max_lesson_order($section_id) {
        $result = \db()->table('lesson')
            ->selectMax('order', 'max_order')
            ->where('section_id', $section_id)
            ->get()
            ->getRow();
        return $result->max_order ?? 0;
    }

    public function get_sections_by_ids($course_id, $section_ids) {
        return \db()->table('course_section')
            ->select('id, title, orders')
            ->where('course_id', $course_id)
            ->whereIn('id', $section_ids)
            ->orderBy('orders', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function get_section_by_id($section_id) {
        return \db()->table('course_section')
            ->select('orders')
            ->where('id', $section_id)
            ->get()
            ->getRow();
    }

    public function get_sections_before_order($course_id, $target_order) {
        return \db()->table('course_section')
            ->select('id, title, orders')
            ->where('course_id', $course_id)
            ->where('orders <', $target_order)
            ->orderBy('orders', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function get_text_lessons_by_section($section_id) {
        return \db()->table('lesson')
            ->select('id, title, summary')
            ->where('section_id', $section_id)
            ->where('lesson_type', 'text')
            ->orderBy('order', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function get_lesson_title_summary($lesson_id) {
        return \db()->table('lesson')
            ->select('title, summary')
            ->where('id', $lesson_id)
            ->get()
            ->getRow();
    }

    public function get_row($table, $where) {
        return \db()->table($table)->where($where)->get()->getRowArray();
    }

    public function custom_insert($table, $data) {
        \db()->table($table)->insert($data);
        return \db()->insertID();
    }

    public function custom_delete($table, $where) {
        return \db()->table($table)->where($where)->delete();
    }

    public function get_where_result($table, $where, $select = '*') {
        return \db()->table($table)->select($select)->where($where)->get()->getResultArray();
    }
}