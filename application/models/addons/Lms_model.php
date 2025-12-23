<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Lms_model extends CI_Model
{

    // constructor
    function __construct()
    {
        parent::__construct();
        $this->load->library('upload');
    }

    function index() {}

    public function get_course_by_id($course_id)
    {
        return $this->db->get_where('course', array('id' => $course_id))->row_array();
    }

    
   public function filter_course_for_backend($class_id = "", $user_id = "", $status = "", $school_id = "")
    {
        $superadmin_login = $this->session->userdata('superadmin_login');
        $admin_login = $this->session->userdata('admin_login');
        $teacher_login = $this->session->userdata('teacher_login');
        $student_login = $this->session->userdata('student_login');

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
        $this->db->select('
            course.*,
            GROUP_CONCAT(DISTINCT t.name SEPARATOR ", ") AS teacher_names
        ');
        $this->db->from('course');
        $this->db->where('course.school_id', school_id());

        // Join with course_classes to filter by class
        if ($class_id != "all" && !empty($class_id)) {
            $this->db->join('course_classes', 'course_classes.course_id = course.id', 'inner');
            $this->db->where('course_classes.class_id', $class_id);
        }

        // Join with course_teachers + users to get teacher names
        $this->db->join('course_teachers', 'course_teachers.course_id = course.id', 'left');
        $this->db->join('users t', 't.id = course_teachers.user_id', 'left');

        // Filter by teacher (if selected)
        if ($user_id != "all" && !empty($user_id)) {
            $this->db->where('course_teachers.user_id', $user_id);
        }

        // Filter by status
        if ($status != "all" && !empty($status)) {
            $this->db->where('course.status', $status);
        }

        // Avoid duplicate courses + group teacher names
        $this->db->group_by('course.id');
        $this->db->order_by('course.id', 'DESC');

        return $this->db->get()->result_array();
    }

    
    /* public function filter_course_for_admin($class_id, $user_id, $status)
    {
        $this->db->where('school_id', school_id());

        if ($class_id != "all") {
            $this->db->where('class_id', $class_id);
        }

        if ($user_id != "all") {
            $this->db->where('user_id', $user_id);
        }

        if ($status != "all") {
            $this->db->where('status', $status);
        }
        return $this->db->get('course')->result_array();
    }  */
   

    /* public function filter_course_for_teacher($class_id, $user_id, $status)
    {
        $this->db->where('school_id', school_id());
        $this->db->where('user_id', $this->session->userdata('user_id'));

        if ($class_id != "all") {
            $this->db->where('class_id', $class_id);
            
        } 
       

        if ($status != "all") {
            $this->db->where('status', $status);
        }
        return $this->db->get('course')->result_array();
    } */
   public function filter_course_for_teacher($class_id = "all", $user_id = "all", $status = "all", $school_id = "all")
    {
        $teacher_id = $this->session->userdata('user_id');

        $this->db->select('
            course.*,
            GROUP_CONCAT(DISTINCT t.name SEPARATOR ", ") AS teacher_names
        ');
        $this->db->from('course');
        $this->db->where('course.school_id', school_id());

        // Teacher can only see HIS/HER own courses (via course_teachers table)
        $this->db->join('course_teachers ct', 'ct.course_id = course.id', 'inner');
        $this->db->where('ct.user_id', $teacher_id);

        // Join users table to get teacher names (for display)
        $this->db->join('users t', 't.id = ct.user_id', 'left');

        // Filter by class using course_classes junction table
        if ($class_id != "all" && !empty($class_id)) {
            $this->db->join('course_classes cc', 'cc.course_id = course.id', 'inner');
            $this->db->where('cc.class_id', $class_id);
        }

        // Filter by status
        if ($status != "all" && !empty($status)) {
            $this->db->where('course.status', $status);
        }

        $this->db->group_by('course.id');
        $this->db->order_by('course.id', 'DESC');

        return $this->db->get()->result_array();
    }

    public function filter_course_for_student($class_id, $user_id, $status, $school_id)
    {
        // $class_id = $this->get_class_id_by_user($this->session->userdata('user_id'));
        // // $this->db->where('school_id', school_id());


        $schools =  $this->db->select('*,course.id as id,course.thumbnail as thumbnail');
        $this->db->from('course');
        $this->db->join('students', 'course.school_id = students.school_id', 'left');
        $this->db->join('schools', 'schools.id = course.school_id', 'left');
        $this->db->where('students.user_id', $this->session->userdata('user_id'));
        $this->db->where('course.status', 'active');
        $this->db->where('students.status', 1);


        if ($user_id != "all") {
            $this->db->where('course.user_id', $user_id);
        }
        if ($school_id != "all") {
            $this->db->where('course.school_id', $school_id);
        }
        if ($class_id != "all") {
            $this->db->where('course.class_id', $class_id);
        }

        // return $this->db->get('course')->result_array();
        return $this->db->get()->result_array();
    }

    public function get_subject_by_class_id($class_id = "")
    {
        return $this->db->get_where('subjects', array('class_id' => $class_id))->result_array();
    }

    public function get_status_wise_courses($status = "")
    {
        if ($status != "") {
            $courses = $this->db->get_where('course', array('status' => $status, 'school_id' => school_id()));
        } else {
            if ($this->session->userdata('teacher_login') == 1) {
                $teacher_id  = $this->session->userdata('user_id');
                $courses['inactive'] = $this->db->get_where('course', array('status' => 'inactive', 'user_id' => $teacher_id));
                $courses['active'] = $this->db->get_where('course', array('status' => 'active', 'user_id' => $teacher_id));
            } else {
                $courses['inactive'] = $this->db->get_where('course', array('status' => 'inactive', 'school_id' => school_id()));
                $courses['active'] = $this->db->get_where('course', array('status' => 'active', 'school_id' => school_id()));
            }
        }
        return $courses;
    }

    public function get_section($type_by, $id)
    {
        $this->db->order_by("orders", "asc");
        if ($type_by == 'course') {
            return $this->db->get_where('course_section', array('course_id' => $id));
        } elseif ($type_by == 'section') {
            return $this->db->get_where('course_section', array('id' => $id));
        }
    }

    // Pagination des sections
    public function get_sections_paginated($course_id, $limit = 10, $offset = 0)
    {
        $this->db->order_by("orders", "asc");
        $this->db->limit($limit, $offset);
        return $this->db->get_where('course_section', array('course_id' => $course_id))->result_array();
    }

    public function count_sections($course_id)
    {
        return $this->db->where('course_id', $course_id)->count_all_results('course_section');
    }

    public function course_activity($course_id)
    {
        $course = $this->db->get_where('course', array('id' => $course_id))->row_array();
        if ($course['status'] == 'active') {
            $data['status'] = 'inactive';
            $this->db->where('id', $course_id);
            $this->db->update('course', $data);
        } else {
            $data['status'] = 'active';
            $this->db->where('id', $course_id);
            $this->db->update('course', $data);
        }
    }

    public function delete_course($course_id)
    {
        $course = $this->db->get_where('course', array('id' => $course_id))->row_array();

        if (file_exists('uploads/course_thumbnail/' . $course['thumbnail']))
            unlink('uploads/course_thumbnail/' . $course['thumbnail']);

        $this->db->where('id', $course_id);
        $this->db->delete('course');

        $this->db->where('course_id', $course_id);
        $this->db->delete('course_section');

        $this->db->where('course_id', $course_id);
        $this->db->delete('lesson');

        $response = array(
            'status' => true,
            'notification' => get_phrase('course_data_deleted_successfully')
        );
        return json_encode($response);
    }

    public function get_lessons($type = "", $id = "")
    {
        $this->db->order_by("order", "asc");
        if ($type == "course") {
            return $this->db->get_where('lesson', array('course_id' => $id));
        } elseif ($type == "section") {
            return $this->db->get_where('lesson', array('section_id' => $id));
        } elseif ($type == "lesson") {
            return $this->db->get_where('lesson', array('id' => $id));
        } else {
            return $this->db->get('lesson');
        }
    }

    public function get_subject_by_class($class_id = '')
    {
        $subjects = $this->db->get_where('subjects', array('class_id' => $class_id))->result_array();
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
        return $this->db
            ->select('classes.*')// On sélectionne toutes les colonnes de la table 'classes'
            ->from('classes')
            ->join('course_classes', 'course_classes.class_id = classes.id')// On fait un JOIN avec la table pivot 'course_classes' qui relie les cours aux classes
            ->where('course_classes.course_id', $course_id)// On filtre uniquement les classes qui appartiennent au cours $course_id
            ->get()
            ->result_array();
    }

    public function get_courses_by_class($class_id)
    {
        return $this->db
            ->select('course.*, course.id as id, course.thumbnail as thumbnail')
            ->from('course')
            ->join('course_classes', 'course_classes.course_id = course.id')
            ->where('course_classes.class_id', $class_id)
            ->where('course.status', 'active')
            ->get()
            ->result_array();
    }

    // Cette fonction met à jour les classes liées à un cours

        public function update_course_classes($course_id, $class_ids)
        {
            // Nettoyage
            $this->db->where('course_id', $course_id)->delete('course_classes');

            // Normalisation du format
            if (!is_array($class_ids)) {
                $decoded = json_decode($class_ids, true);
                $class_ids = is_array($decoded) ? $decoded : explode(',', $class_ids);
            }

            // Insertion
            foreach ($class_ids as $class_id) {
                if (!empty($class_id)) {
                    $this->db->insert('course_classes', [
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
        return $this->db
            ->select('users.*')
            ->from('users')
            ->join('course_teachers', 'course_teachers.user_id = users.id')// On fait un JOIN avec la table pivot 'course_teachers' qui relie les cours aux enseignants
            ->where('course_teachers.course_id', $course_id)// On filtre uniquement les enseignants qui appartiennent au cours $course_id
            ->get()
            ->result_array();
    }
    
    // Cette fonction met à jour les enseignants liés à un cours
    public function update_course_teachers($course_id, $teacher_ids)
    {
        $this->db->where('course_id', $course_id)->delete('course_teachers');
    // Si $teacher_ids est un tableau (ex: [5, 6, 7])
        if (is_array($teacher_ids)) {
            foreach ($teacher_ids as $user_id) {
                // Pour chaque teacher_id, on crée une nouvelle entrée dans la table pivot
                $this->db->insert('course_teachers', [
                    'course_id' => $course_id,
                    'user_id'   => $user_id
                ]);
            }
        }
    }

    public function course_add()
    {
        $data['title'] = $this->input->post('title');
        // $data['class_id'] = $this->input->post('class_id');
        // $data['user_id'] = $this->input->post('user_id');
        // $data['subject_id'] = $this->input->post('subject_id');
        $data['description'] = $this->input->post('description');
        $data['outcomes'] = $this->input->post('outcomes');
        $data['course_overview_provider'] = $this->input->post('course_overview_provider');
        $data['course_overview_url'] = $this->input->post('course_overview_url');
        $data['thumbnail'] = rand() . '.jpg';
    // récupérer la valeur envoyée par le switch
    $status = $this->input->post('status'); // 'active' ou 'inactive'
    $data['status'] = !empty($status) ? $status : 'inactive'; // sécurité par défaut
        $data['date_added'] = strtotime(date('d M Y'));
        $data['school_id'] = school_id();

        $this->db->insert('course', $data);
    $course_id = $this->db->insert_id(); // récupère l'id du cours créé
        move_uploaded_file($_FILES['course_thumbnail']['tmp_name'], 'uploads/course_thumbnail/' . $data['thumbnail']);
        // Récupérer les classes et mentors sélectionnés
    $class_ids = $this->input->post('class_id');   // tableau d'ids
    $teacher_ids = $this->input->post('user_id');  // tableau d'ids

    // Insérer dans table pivot course_classes
    if (!empty($class_ids)) {
        foreach ($class_ids as $class_id) {
            $this->db->insert('course_classes', [
                'course_id' => $course_id,
                'class_id' => $class_id
            ]);
        }
    }

    // Insérer dans table pivot course_teachers
    if (!empty($teacher_ids)) {
        foreach ($teacher_ids as $teacher_id) {
            $this->db->insert('course_teachers', [
                'course_id' => $course_id,
                'user_id' => $teacher_id
            ]);
        }
    }

    }

    public function course_edit($course_id)
    {
        $data['title'] = $this->input->post('title');
        // $data['class_id'] = $this->input->post('class_id');
        // $data['user_id'] = $this->input->post('user_id');
        // $data['subject_id'] = $this->input->post('subject_id');
        $data['description'] = $this->input->post('description');
        $data['outcomes'] = $this->input->post('outcomes');
        $data['course_overview_provider'] = $this->input->post('course_overview_provider');
        $data['course_overview_url'] = $this->input->post('course_overview_url');
        $data['last_modified'] = strtotime(date('d M Y'));

        if ($_FILES['course_thumbnail']['tmp_name']) {
            unlink('uploads/course_thumbnail/' . $this->input->post('current_thumbnail'));
            $data['thumbnail'] = rand() . '.jpg';
            move_uploaded_file($_FILES['course_thumbnail']['tmp_name'], 'uploads/course_thumbnail/' . $data['thumbnail']);
        }

        $this->db->where('id', $course_id);
        $this->db->update('course', $data);
        // Mettre à jour les relations multi-classes et multi-teachers
        $class_ids = $this->input->post('class_id');
        $teacher_ids = $this->input->post('user_id');

        $this->update_course_classes($course_id, $class_ids);
        $this->update_course_teachers($course_id, $teacher_ids);
    }

    public function add_course_section($course_id)
    {
        // Get max order for this course
        $this->db->select_max('orders');
        $this->db->where('course_id', $course_id);
        $max_order = $this->db->get('course_section')->row()->orders;
        
        $data['title'] = html_escape($this->input->post('title'));
        $data['course_id'] = $course_id;
        $data['orders'] = ($max_order !== null) ? $max_order + 1 : 0;
        $this->db->insert('course_section', $data);
        $section_id = $this->db->insert_id();

        $course_details = $this->get_course_by_id($course_id);
        $previous_sections = json_decode($course_details['section']);

        if (isset($previous_sections) > 0) {
            array_push($previous_sections, $section_id);
            $updater['section'] = json_encode($previous_sections);
            $this->db->where('id', $course_id);
            $this->db->update('course', $updater);
        } else {
            $previous_sections = array();
            array_push($previous_sections, $section_id);
            $updater['section'] = json_encode($previous_sections);
            $this->db->where('id', $course_id);
            $this->db->update('course', $updater);
        }
    }

    public function edit_course_section($section_id)
    {
        $data['title'] = $this->input->post('title');
        $this->db->where('id', $section_id);
        $this->db->update('course_section', $data);
    }

    public function add_lesson()
    {
        $data['course_id'] = html_escape($this->input->post('course_id'));
        $data['title'] = html_escape($this->input->post('title'));
        $data['section_id'] = html_escape($this->input->post('section_id'));

        $lesson_type_array = explode('-', $this->input->post('lesson_type'));
        $lesson_type = $lesson_type_array[0];

        $data['attachment_type'] = $lesson_type_array[1];
        $data['lesson_type'] = $lesson_type;

        if ($lesson_type == 'video') {
            // This portion is for web application's video lesson
            $lesson_provider = $this->input->post('lesson_provider');
            if ($lesson_provider == 'youtube' || $lesson_provider == 'vimeo') {
                if ($this->input->post('video_url') == "" || $this->input->post('duration') == "") {
                    $this->session->set_flashdata('error_message', get_phrase('invalid_lesson_url_and_duration'));
                    redirect(site_url('addons/courses/course_edit/' . $data['course_id']), 'refresh');
                }
                $data['video_url'] = html_escape($this->input->post('video_url'));

                $duration_formatter = explode(':', $this->input->post('duration'));
                $hour = sprintf('%02d', $duration_formatter[0]);
                $min = sprintf('%02d', $duration_formatter[1]);
                $sec = sprintf('%02d', $duration_formatter[2]);
                $data['duration'] = $hour . ':' . $min . ':' . $sec;

                $video_details = $this->video_model->getVideoDetails($data['video_url']);
                $data['video_type'] = $video_details['provider'];
            } elseif ($lesson_provider == 'html5') {
                if ($this->input->post('html5_video_url') == "" || $this->input->post('html5_duration') == "") {
                    $this->session->set_flashdata('error_message', get_phrase('invalid_lesson_url_and_duration'));
                    redirect(site_url('addons/courses/course_edit/' . $data['course_id']), 'refresh');
                }
                $data['video_url'] = html_escape($this->input->post('html5_video_url'));
                $duration_formatter = explode(':', $this->input->post('html5_duration'));
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


                // move_uploaded_file($_FILES['userfileMe']['tmp_name'], 'uploads/videos/'.$data['video_uplaod']);
                // Configuration de l'upload
                $upload_path = './uploads/videos/';
                $allowed_types = array('mp4', 'avi', 'mov');
                $max_size = 102400; // 100MB

                if (isset($_FILES['userfileMe']) && $_FILES['userfileMe']['error'] == 0) {
                    // Vérifiez le type de fichier
                    $file_type = pathinfo($_FILES['userfileMe']['name'], PATHINFO_EXTENSION);
                    if (in_array($file_type, $allowed_types)) {
                        // Vérifiez la taille du fichier
                        if ($_FILES['userfileMe']['size'] <= $max_size * 1024) {
                            // Déplacez le fichier vers le dossier de téléchargement
                            $data['video_uplaod'] = rand() . '.mp4';
                            $file_name = $data['video_uplaod'];
                            $tmp_name = $_FILES['userfileMe']['tmp_name'];
                            $destination = $upload_path . $file_name;

                            if (move_uploaded_file($tmp_name, $destination)) {
                                // Upload réussi
                                $datavideo['upload_data'] = array(
                                    'file_name' => $file_name,
                                    'file_type' => $file_type,
                                    'file_path' => $upload_path,
                                    'full_path' => $destination,
                                    'file_size' => $_FILES['userfileMe']['size'],
                                );
                            } else {
                                // Erreur de déplacement du fichier
                                $this->session->set_flashdata('error_message', get_phrase('There was a problem moving the file.'));
                                redirect(site_url('addons/courses/course_edit/' . $data['course_id']), 'refresh');
                            }
                        } else {
                            // Fichier trop grand
                            $this->session->set_flashdata('error_message', get_phrase('The file size exceeds the limit.'));
                            redirect(site_url('addons/courses/course_edit/' . $data['course_id']), 'refresh');
                        }
                    } else {
                        // Type de fichier non autorisé
                        $this->session->set_flashdata('error_message', get_phrase('The file type is not allowed.'));
                        redirect(site_url('addons/courses/course_edit/' . $data['course_id']), 'refresh');
                    }
                } else {
                    // Erreur d'upload
                    $this->session->set_flashdata('error_message', get_phrase('No file uploaded or there was an upload error.'));
                    redirect(site_url('addons/courses/course_edit/' . $data['course_id']), 'refresh');
                }
            } else {
                $this->session->set_flashdata('error_message', get_phrase('invalid_lesson_provider'));
                redirect(site_url('addons/courses/course_edit/' . $data['course_id']), 'refresh');
            }
        } else {
            if ($_FILES['attachment']['name'] == "") {
                $this->session->set_flashdata('error_message', get_phrase('invalid_attachment'));
                redirect(site_url('addons/courses/course_edit/' . $data['course_id']), 'refresh');
            } else {
                $data['duration']   = 0;
                $fileName           = $_FILES['attachment']['name'];
                $tmp                = explode('.', $fileName);
                $fileExtension      = end($tmp);
                $uploadable_file    =  md5(uniqid(rand(), true)) . '.' . $fileExtension;
                $data['attachment'] = $uploadable_file;

                if (!file_exists('uploads/lesson_files')) {
                    mkdir('uploads/lesson_files', 0777, true);
                }
                move_uploaded_file($_FILES['attachment']['tmp_name'], 'uploads/lesson_files/' . $uploadable_file);
            }
        }

        $data['date_added'] = strtotime(date('D, d-M-Y'));
        $data['summary'] = $this->input->post('summary');

        $this->db->insert('lesson', $data);
        $inserted_id = $this->db->insert_id();

        if ($_FILES['thumbnail']['name'] != "") {
            if (!file_exists('uploads/thumbnails/lesson_thumbnails')) {
                mkdir('uploads/thumbnails/lesson_thumbnails', 0777, true);
            }
            move_uploaded_file($_FILES['thumbnail']['tmp_name'], 'uploads/thumbnails/lesson_thumbnails/' . $inserted_id . '.jpg');
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
        $previous_data = $this->db->get_where('lesson', array('id' => $lesson_id))->row_array();

        $data['course_id'] = html_escape($this->input->post('course_id'));
        $data['title'] = html_escape($this->input->post('title'));
        $data['section_id'] = html_escape($this->input->post('section_id'));

        $lesson_type_array = explode('-', $this->input->post('lesson_type'));
        $lesson_type = $lesson_type_array[0];

        $data['attachment_type'] = $lesson_type_array[1];
        $data['lesson_type'] = $lesson_type;
        if ($lesson_type == 'video') {
            $lesson_provider = $this->input->post('lesson_provider');
            if ($lesson_provider == 'youtube' || $lesson_provider == 'vimeo') {
                if ($this->input->post('video_url') == "" || $this->input->post('duration') == "") {
                    $this->session->set_flashdata('error_message', get_phrase('invalid_lesson_url_and_duration'));
                    redirect(site_url(strtolower($this->session->userdata('role')) . '/course_form/course_edit/' . $data['course_id']), 'refresh');
                }
                $data['video_url'] = html_escape($this->input->post('video_url'));

                $duration_formatter = explode(':', $this->input->post('duration'));
                $hour = sprintf('%02d', $duration_formatter[0]);
                $min = sprintf('%02d', $duration_formatter[1]);
                $sec = sprintf('%02d', $duration_formatter[2]);
                $data['duration'] = $hour . ':' . $min . ':' . $sec;

                $video_details = $this->video_model->getVideoDetails($data['video_url']);
                $data['video_type'] = $video_details['provider'];
            } elseif ($lesson_provider == 'html5') {
                if ($this->input->post('html5_video_url') == "" || $this->input->post('html5_duration') == "") {
                    $this->session->set_flashdata('error_message', get_phrase('invalid_lesson_url_and_duration'));
                    redirect(site_url(strtolower($this->session->userdata('role')) . '/course_form/course_edit/' . $data['course_id']), 'refresh');
                }
                $data['video_url'] = html_escape($this->input->post('html5_video_url'));

                $duration_formatter = explode(':', $this->input->post('html5_duration'));
                $hour = sprintf('%02d', $duration_formatter[0]);
                $min = sprintf('%02d', $duration_formatter[1]);
                $sec = sprintf('%02d', $duration_formatter[2]);
                $data['duration'] = $hour . ':' . $min . ':' . $sec;
                $data['video_type'] = 'html5';

                if ($_FILES['thumbnail']['name'] != "") {
                    if (!file_exists('uploads/thumbnails/lesson_thumbnails')) {
                        mkdir('uploads/thumbnails/lesson_thumbnails', 0777, true);
                    }
                    move_uploaded_file($_FILES['thumbnail']['tmp_name'], 'uploads/thumbnails/lesson_thumbnails/' . $lesson_id . '.jpg');
                }
            } elseif ($lesson_provider == 'mydevice') {



                // $data['video_type'] = 'mydevice';
                //  $data['video_uplaod'] = rand().'.mp4';
                // move_uploaded_file($_FILES['userfileMe']['tmp_name'], 'uploads/videos/'.$data['video_uplaod']);
                // Configuration de l'upload
                $upload_path = './uploads/videos/';
                $allowed_types = array('mp4', 'avi', 'mov');
                $max_size = 102400; // 100MB

                if (isset($_FILES['userfileMe']) && $_FILES['userfileMe']['error'] == 0) {
                    // Vérifiez le type de fichier
                    $file_type = pathinfo($_FILES['userfileMe']['name'], PATHINFO_EXTENSION);
                    if (in_array($file_type, $allowed_types)) {
                        // Vérifiez la taille du fichier
                        if ($_FILES['userfileMe']['size'] <= $max_size * 1024) {
                            // Déplacez le fichier vers le dossier de téléchargement
                            $data['video_uplaod'] = rand() . '.mp4';
                            $file_name = $data['video_uplaod'];
                            $tmp_name = $_FILES['userfileMe']['tmp_name'];
                            $destination = $upload_path . $file_name;

                            if (move_uploaded_file($tmp_name, $destination)) {
                                // Upload réussi
                                $datavideo['upload_data'] = array(
                                    'file_name' => $file_name,
                                    'file_type' => $file_type,
                                    'file_path' => $upload_path,
                                    'full_path' => $destination,
                                    'file_size' => $_FILES['userfileMe']['size'],
                                );
                                $this->delete_old_files('uploads/videos/' . $previous_data['video_uplaod']);
                            } else {
                                // Erreur de déplacement du fichier
                                $this->session->set_flashdata('error_message', get_phrase('There was a problem moving the file.'));
                                redirect(site_url('addons/courses/course_edit/' . $data['course_id']), 'refresh');
                            }
                        } else {
                            // Fichier trop grand
                            $this->session->set_flashdata('error_message', get_phrase('The file size exceeds the limit.'));
                            redirect(site_url('addons/courses/course_edit/' . $data['course_id']), 'refresh');
                        }
                    } else {
                        // Type de fichier non autorisé
                        $this->session->set_flashdata('error_message', get_phrase('The file type is not allowed.'));
                        redirect(site_url('addons/courses/course_edit/' . $data['course_id']), 'refresh');
                    }
                } else {
                    // Erreur d'upload
                    $this->session->set_flashdata('error_message', get_phrase('No file uploaded or there was an upload error.'));
                    redirect(site_url('addons/courses/course_edit/' . $data['course_id']), 'refresh');
                }
            } else {
                $this->session->set_flashdata('error_message', get_phrase('invalid_lesson_provider'));
                redirect(site_url(strtolower($this->session->userdata('role')) . '/course_form/course_edit/' . $data['course_id']), 'refresh');
            }
            $data['attachment'] = "";
        } else {
            if ($_FILES['attachment']['name'] != "") {
                // unlinking previous attachments
                if ($previous_data['attachment'] != "") {
                    unlink('uploads/lesson_files/' . $previous_data['attachment']);
                }

                $fileName           = $_FILES['attachment']['name'];
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
                move_uploaded_file($_FILES['attachment']['tmp_name'], 'uploads/lesson_files/' . $uploadable_file);
            }
        }

        $data['last_modified'] = strtotime(date('D, d-M-Y'));
        $data['summary'] = $this->input->post('summary');

        $this->db->where('id', $lesson_id);
        $this->db->update('lesson', $data);
    }

    public function delete_lesson($lesson_id)
    {
        $this->db->where('id', $lesson_id);
        $this->db->delete('lesson');
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
        $data['title'] = html_escape($this->input->post('title'));
        $data['section_id'] = html_escape($this->input->post('section_id'));

        $data['lesson_type'] = 'quiz';
        $data['duration'] = 0;
        $data['date_added'] = strtotime(date('D, d-M-Y'));
        $data['summary'] = html_escape($this->input->post('summary'));
        $this->db->insert('lesson', $data);
    }

    // updating quiz functionalities
    public function edit_quiz($lesson_id = "")
    {
        $data['title'] = html_escape($this->input->post('title'));
        $data['section_id'] = html_escape($this->input->post('section_id'));
        $data['last_modified'] = strtotime(date('D, d-M-Y'));
        $data['summary'] = html_escape($this->input->post('summary'));
        $this->db->where('id', $lesson_id);
        $this->db->update('lesson', $data);
    }

    public function delete_course_section($course_id, $section_id)
    {
        $this->db->where('id', $section_id);
        $this->db->delete('course_section');

        $this->db->where('course_id', $course_id);
        $this->db->where('section_id', $section_id);
        $this->db->delete('lesson');

        $course_details = $this->get_course_by_id($course_id);
        $previous_sections = json_decode($course_details['section']);

        if (sizeof($previous_sections) > 0) {
            $new_section = array();
            for ($i = 0; $i < sizeof($previous_sections); $i++) {
                if ($previous_sections[$i] != $section_id) {
                    array_push($new_section, $previous_sections[$i]);
                }
            }
            $updater['section'] = json_encode($new_section);
            $this->db->where('id', $course_id);
            $this->db->update('course', $updater);
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
            
            $this->db->where('id', $section_id);
            $this->db->update('course_section', array('orders' => $new_order));
        }
        
        return true;
    }
    
    // Move a section to a specific position
    public function move_section_to_position($section_id, $target_position, $course_id)
    {
        // Get current section info
        $section = $this->db->where('id', $section_id)->get('course_section')->row();
        if (!$section) {
            return false;
        }
        
        $current_order = (int) $section->orders;
        $target_order = (int) $target_position;
        
        if ($current_order === $target_order) {
            return true; // Already in position
        }
        
        $this->db->trans_begin();
        
        try {
            if ($target_order < $current_order) {
                // Moving up: increment orders of sections between target and current
                $this->db->set('orders', 'orders + 1', false);
                $this->db->where('course_id', $course_id);
                $this->db->where('orders >=', $target_order);
                $this->db->where('orders <', $current_order);
                $this->db->update('course_section');
            } else {
                // Moving down: decrement orders of sections between current and target
                $this->db->set('orders', 'orders - 1', false);
                $this->db->where('course_id', $course_id);
                $this->db->where('orders >', $current_order);
                $this->db->where('orders <=', $target_order);
                $this->db->update('course_section');
            }
            
            // Set the section to target position
            $this->db->where('id', $section_id);
            $this->db->update('course_section', array('orders' => $target_order));
            
            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                return false;
            }
            
            $this->db->trans_commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->trans_rollback();
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
            $this->db->where('id', $value);
            $this->db->update('lesson', $updater);
        }
    }

    public function get_quiz_questions($quiz_id)
    {
        $this->db->order_by("order", "asc");
        $this->db->where('quiz_id', $quiz_id);
        return $this->db->get('question');
    }

    public function sort_question($question_json)
    {
        $questions = json_decode($question_json);
        foreach ($questions as $key => $value) {
            $updater = array(
                'order' => $key + 1
            );
            $this->db->where('id', $value);
            $this->db->update('question', $updater);
        }
    }
    // Add Quiz Questions
    public function add_quiz_questions($quiz_id)
    {
        $question_type = $this->input->post('question_type');
        if ($question_type == 'mcq') {
            $response = $this->add_multiple_choice_question($quiz_id);
            return $response;
        }
    }
    // multiple_choice_question crud functions
    public function add_multiple_choice_question($quiz_id)
    {
        if (sizeof($this->input->post('options')) != $this->input->post('number_of_options')) {
            return false;
        }
        foreach ($this->input->post('options') as $option) {
            if ($option == "") {
                return false;
            }
        }
        if (sizeof($this->input->post('correct_answers')) == 0) {
            $correct_answers = [""];
        } else {
            $correct_answers = $this->input->post('correct_answers');
        }
        $data['quiz_id']            = $quiz_id;
        $data['title']              = html_escape($this->input->post('title'));
        $data['number_of_options']  = html_escape($this->input->post('number_of_options'));
        $data['type']               = 'multiple_choice';
        $data['options']            = json_encode($this->input->post('options'));
        $data['correct_answers']    = json_encode($correct_answers);
        $this->db->insert('question', $data);
        return true;
    }
    public function update_quiz_questions($question_id)
    {
        $question_type = $this->input->post('question_type');
        if ($question_type == 'mcq') {
            $response = $this->update_multiple_choice_question($question_id);
            return $response;
        }
    }
    // update multiple choice question
    public function update_multiple_choice_question($question_id)
    {
        if (sizeof($this->input->post('options')) != $this->input->post('number_of_options')) {
            return false;
        }
        foreach ($this->input->post('options') as $option) {
            if ($option == "") {
                return false;
            }
        }

        if (sizeof($this->input->post('correct_answers')) == 0) {
            $correct_answers = [""];
        } else {
            $correct_answers = $this->input->post('correct_answers');
        }

        $data['title']              = html_escape($this->input->post('title'));
        $data['number_of_options']  = html_escape($this->input->post('number_of_options'));
        $data['type']               = 'multiple_choice';
        $data['options']            = json_encode($this->input->post('options'));
        $data['correct_answers']    = json_encode($correct_answers);
        $this->db->where('id', $question_id);
        $this->db->update('question', $data);
        return true;
    }
    public function delete_quiz_question($question_id)
    {
        $this->db->where('id', $question_id);
        $this->db->delete('question');
        $response = array(
            'status' => true,
            'notification' => get_phrase('quiz_questions_deleted_successfully')
        );
        return json_encode($response);
    }
    public function get_quiz_question_by_id($question_id)
    {
        $this->db->order_by("order", "asc");
        $this->db->where('id', $question_id);
        return $this->db->get('question');
    }

    // code of mark this lesson as completed
    function save_course_progress()
    {
        $lesson_id = $this->input->post('lesson_id');
        $progress = $this->input->post('progress');
        $user_id   = $this->session->userdata('user_id');
        $user_details  = $this->user_model->get_all_users($user_id)->row_array();
        $watch_history = $user_details['watch_history'];
        $watch_history_array = array();
        if ($watch_history == '') {
            array_push($watch_history_array, array('lesson_id' => $lesson_id, 'progress' => $progress));
        } else {
            $founder = false;
            $watch_history_array = json_decode($watch_history, true);
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
        $this->db->where('id', $user_id);
        $this->db->update('users', $data);

        return $progress;
    }

    public function get_class_id_by_user($user_id = "")
    {
        if ($user_id == "") {
            $user_id = $this->session->userdata('user_id');
        }
        $student_id = $this->db->get_where('students', array('user_id' => $user_id))->row('id');
        return $class_id = $this->db->get_where('enrols', array('student_id' => $student_id,  'session' => active_session()))->row('class_id');
    }

    public function get_exams($type = "", $id = "")
    {
        $this->db->order_by("id", "asc"); // Tri par "id" au lieu de "order"
        if ($type == "exam") {
            $query = $this->db->get_where('exams', array('id' => $id));
            if ($query === FALSE) {
                return FALSE;
            }
            return $query;
        } else {
            $query = $this->db->get('exams');
            if ($query === FALSE) {
                return FALSE;
            }
            return $query;
        }
    }

    public function get_exam_questions($exam_id)
    {
        $this->db->order_by("order", "asc");
        $this->db->where('exam_id', $exam_id);
        return $this->db->get('exam_questions');
    }

    public function get_exam_question_by_id($question_id)
    {
        $this->db->order_by("order", "asc");
        $this->db->where('id', $question_id);
        return $this->db->get('exam_questions');
    }

    public function add_exam_questions($exam_id)
    {
        $question_type = $this->input->post('question_type');
        if ($question_type == 'mcq') {
            $response = $this->add_multiple_choice_exam_question($exam_id);
            return $response;
        }
    }

    public function add_multiple_choice_exam_question($exam_id)
    {
        if (sizeof($this->input->post('options')) != $this->input->post('number_of_options')) {
            return false;
        }
        foreach ($this->input->post('options') as $option) {
            if ($option == "") {
                return false;
            }
        }
        if (sizeof($this->input->post('correct_answers')) == 0) {
            $correct_answers = [""];
        } else {
            $correct_answers = $this->input->post('correct_answers');
        }
        $data['exam_id']            = $exam_id;
        $data['title']              = html_escape($this->input->post('title'));
        $data['number_of_options']  = html_escape($this->input->post('number_of_options'));
        $data['type']               = 'multiple_choice';
        $data['options']            = json_encode($this->input->post('options'));
        $data['correct_answers']    = json_encode($correct_answers);
        $this->db->insert('exam_questions', $data);
        return true;
    }

    public function update_exam_questions($question_id)
    {
        $question_type = $this->input->post('question_type');
        if ($question_type == 'mcq') {
            $response = $this->update_multiple_choice_exam_question($question_id);
            return $response;
        }
    }

    public function update_multiple_choice_exam_question($question_id)
    {
        if (sizeof($this->input->post('options')) != $this->input->post('number_of_options')) {
            return false;
        }
        foreach ($this->input->post('options') as $option) {
            if ($option == "") {
                return false;
            }
        }

        if (sizeof($this->input->post('correct_answers')) == 0) {
            $correct_answers = [""];
        } else {
            $correct_answers = $this->input->post('correct_answers');
        }

        $data['title']              = html_escape($this->input->post('title'));
        $data['number_of_options']  = html_escape($this->input->post('number_of_options'));
        $data['type']               = 'multiple_choice';
        $data['options']            = json_encode($this->input->post('options'));
        $data['correct_answers']    = json_encode($correct_answers);
        $this->db->where('id', $question_id);
        $this->db->update('exam_questions', $data);
        return true;
    }

    public function delete_exam_question($question_id)
    {
        $this->db->where('id', $question_id);
        $this->db->delete('exam_questions');
        return $this->db->affected_rows() > 0;
    }

    // Dans models/Lms_model.php
    public function sort_exam_question($question_json)
    {
        $questions = json_decode($question_json);
        foreach ($questions as $key => $value) {
            $updater = array(
                'order' => $key + 1
            );
            $this->db->where('id', $value);
            $this->db->update('exam_questions', $updater);
        }
    }
}