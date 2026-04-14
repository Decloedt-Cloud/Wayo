<?php

namespace App\Models\api;

use CodeIgniter\Model;

class Api_admin_model extends Model {
protected $DBGroup = 'default';

  public function __construct()
  {
    parent::__construct();
  }

public function menu($user_type) {
  $response = array();
  $items = array();
  $active_language = 'fr';

  $row = \db()->table('users')->where($user_type)->get()->getRow();
  if (!$row) {
      return ['status' => 404, 'items' => []];
  }

  $userRole = strtolower($row->role);

  $query_menus = \db()->table('menus')->get();
  foreach ($query_menus->getResult() as $row_menu) {
      if ($row_menu->parent == 0) { 
          if ($row_menu->id == $row_menu->parent) {
              $displayed_name = $row_menu->id . ' - ' . $row_menu->displayed_name;
          } else {
              $displayed_name = $row_menu->displayed_name;
          }

          $translated_name = get_phrase($displayed_name, $active_language);

          $submenu = $this->get_submenu($row_menu->id, $active_language);
          $containsSameName = false;
          foreach ($submenu as $subitem) {
              if ($subitem['displayed_name'] == $row_menu->displayed_name) {
                  $containsSameName = true;
                  break;
              }
          }

          if (!$containsSameName) {
              $access = $row_menu->{$userRole . '_access'};
              if ($access == 1) {
                  $item = array(
                      'id' => $row_menu->id,
                      'parent' => $row_menu->parent,
                      'displayed_name' => $translated_name,
                      'icon' => $row_menu->icon,
                      'submenu' => $submenu,
                      'access' => array(
                          'superadmin' => $row_menu->superadmin_access,
                          'admin' => $row_menu->admin_access, 
                          'teacher' => $row_menu->teacher_access,
                          'student' => $row_menu->student_access,
                          'parent' => $row_menu->parent_access,
                          'accountant' => $row_menu->accountant_access,
                          'librarian' => $row_menu->librarian_access,
                          'driver' => $row_menu->driver_access
                      )
                  );
                  $items[] = $item;
              }
          }
      }
  }

  foreach ($items as $key => $item) {
      if ($item['displayed_name'] == 'Online admission') {
          unset($items[$key]);
          array_unshift($items, $item);
          break;
      }
  }

  $ordered_items = array();
  $online_courses = array();
  $settings_item = null;
  foreach ($items as $item) {
      if ($item['displayed_name'] == 'Online courses') {
          $online_courses[] = $item;
      } elseif ($item['displayed_name'] == 'Settings') {
          $settings_item = $item;
      } else {
          $ordered_items[] = $item;
      }
  }
  $ordered_items = array_merge($ordered_items, $online_courses);
  if ($settings_item) {
      $ordered_items[] = $settings_item;
  }
  $response['status'] = 200;
  $response['items'] = $ordered_items;

  return $response;
}

  public function editProfile($user_id, $profile_data) {
    $response = array();
  
    if (empty($user_id)) {
      $response['status'] = 400;
      $response['message'] = 'User ID is missing or invalid.';
      return $response;
    }
  
  
    $data = array_filter($profile_data, function ($value) {
      return $value !== null;
    });
  
  
    $update = \db()->table('users')->where('id', $user_id)->update($data);
  
  
    if ($update) {
      $response['status'] = 200;
      $response['message'] = 'Profile updated successfully';
    } else {
      $response['status'] = 500;
      $response['message'] = 'Failed to update profile';
    }
  
    return $response;
  }
  public function updatePassword($user_id, $new_password) {
    $response = array();
  
    if (empty($user_id) || empty($new_password)) {
        $response['status'] = 400;
        $response['message'] = 'User ID or new password is missing or invalid.';
        return $response;
    }
  
  
    $encrypted_password = password_hash($new_password, PASSWORD_DEFAULT);
  
  
    $update_data = array('password' => $encrypted_password);
    $update = \db()->table('users')->where('id', $user_id)->update($update_data);
  
  
    if ($update) {
        $response['status'] = 200;
        $response['message'] = 'Password updated successfully';
    } else {
        $response['status'] = 500;
        $response['message'] = 'Failed to update password';
    }
  
    return $response;
  }
  



private function get_submenu($parent_id, $active_language) {
  $submenu = array();

  $query = \db()->table('menus')->where('parent', $parent_id)->get()->getResult();
  foreach ($query as $row) {
  
      if ($row->id != $parent_id) {
          $translated_name = get_phrase($row->displayed_name, $active_language);

          $subitem = array(
              'id' => $row->id,
              'parent' => $row->parent,
              'displayed_name' => $translated_name,
              'access' => array(
                  'superadmin' => $row->superadmin_access,
                  'admin' => $row->admin_access,
                  'teacher' => $row->teacher_access,
                  'student' => $row->student_access,
                  'parent' => $row->parent_access,
                  'accountant' => $row->accountant_access,
                  'librarian' => $row->librarian_access,
                  'driver' => $row->driver_access
              ),
              'icon' => $row->icon
          );

          $sub_submenu = $this->get_submenu($row->id, $active_language);
          if (!empty($sub_submenu)) {
              $subitem['submenu'] = $sub_submenu;
          }

          $submenu[] = $subitem;
      }
  }

  return $submenu;
}

  // Login mechanism
  public function login($email, $password) {
    $response = array();
    
    if (empty($email) || empty($password)) {
      $response['status'] = 400;
      $response['message'] = 'Email and password are required';
      $response['validity'] = false;
      return $response;
    }
    
    $query = \db()->table('users')->where('email', $email)->get();
    if ($query->numRows() > 0) {
      $row = $query->getRowArray();
      if (password_verify($password, $row['password'])) {
        $response['status'] = 200;
        $response['message'] = 'Loggedin Successfully';
        $response['user_id'] = $row['id'];
        $response['name'] = $row['name'];
        $response['email'] = $row['email'];
        $response['role'] = strtolower($row['role']);
        $response['school_id'] = $row['school_id'];
        $response['address'] = $row['address'];
        $response['phone'] = $row['phone'];
        $response['birthday'] = date('d-M-Y', $row['birthday']);
        $response['gender'] = strtolower($row['gender']);
        $response['blood_group'] = strtolower($row['blood_group']);
        $response['validity'] = true;
      } else {
        $response['status'] = 401;
        $response['message'] = 'Invalid credentials';
        $response['validity'] = false;
      }
    }else{
      $response['status'] = 404;
      $response['message'] = 'Not Found';
      $response['validity'] = false;
    }
    return $response;
  }

  //FORGOT PASSWORD RETREIVING
  public function forgot_password($email) {
    $response = array();
    
    if (empty($email)) {
      $response['status'] = 400;
      $response['message'] = 'Email is required';
      return $response;
    }
    
    $query = \db()->table('users')->where('email', $email)->get();
    if ($query->numRows() > 0) {
      $query = $query->getRowArray();
      $new_password = substr( md5( rand(100000000,20000000000) ) , 0,7);

      // updating the database
      $updater = array(
        'password' => password_hash($new_password, PASSWORD_DEFAULT)
      );
      \db()->table('users')->where('id', $query['id'])->update($updater);

      // sending mail to user
      $this->email_model->password_reset_email($new_password, $query['id']);

      $response['status'] = 200;
      $response['message'] = 'Password Reset Successfully';
    }else{
      $response['status'] = 404;
      $response['message'] = 'Not Found';
    }

    return $response;
  }

  // USERDATA GET
  public function get_userdata($user_id = "") {
    $response = array();
    $credential = array('id' => $user_id);
    $query = \db()->table('users')->where($credential)->get();
    if ($query->numRows() > 0) {
      $row = $query->getRowArray();
      $response['status'] = 200;
      $response['message'] = 'Password Reset Successfully';
      $response['user_id'] = $row['id'];
      $response['name'] = $row['name'];
      $response['email'] = $row['email'];
      $response['role'] = strtolower($row['role']);
      $response['school_id'] = $row['school_id'];
      $response['address'] = $row['address'];
      $response['phone'] = $row['phone'];
      $response['birthday'] = date('d-M-Y', $row['birthday']);
      $response['gender'] = strtolower($row['gender']);
      $response['blood_group'] = strtolower($row['blood_group']);
    }else{
      $response['status'] = 404;
      $response['message'] = get_phrase('user_not_found');
    }
    return $response;
  }

  // GET DASHBOARD DATA
  public function get_dashboard_data($user_id = "", $school_id = "", $active_session = "") {
    $response = array();
    $credential = array('id' => $user_id);
    $query = \db()->table('users')->where($credential)->get();
    if ($query->numRows() > 0) {
      $row = $query->getRowArray();
      $response['status'] = 200;
      $response['message'] = 'Data Fetched Successfully';
      
      if (empty($school_id)) {
        $school_id = api_school_id($row['id']);
      }
      if (empty($active_session)) {
        $active_session = api_active_session($row['id']);
      }
      
      $response['total_number_of_students'] = \db()->table('enrols')->where('school_id', $school_id)->countAllResults();
      $response['total_number_of_teachers'] = \db()->table('teachers')->where('school_id', $school_id)->countAllResults();
      $attendance_checker = array(
        'timestamp' => strtotime(date('Y-m-d')),
        'school_id' => $school_id,
        'status'    => 1
      );
      $todays_attendance = \db()->table('daily_attendances')->where($attendance_checker)->get();
      $response['total_number_of_student_attending_today'] = $todays_attendance->numRows();
      $response['total_number_of_unpaid_invoices'] = \db()->table('invoices')->where('school_id', $school_id)->countAllResults();
    }else{
      $response['status'] = 404;
      $response['message'] = get_phrase('user_not_found');
    }
    return $response;
  }

  // GET SUBJECTS AGAINST CLASS
  public function get_subjects($user_id = "", $class_id = "", $school_id = "", $active_session = "") {
    $response = array();
    if (empty($school_id)) {
      $school_id = api_school_id($user_id);
    }
    if (empty($active_session)) {
      $active_session = api_active_session($user_id);
    }
    
    if (empty($class_id)) {
      $response['status'] = 400;
      $response['message'] = 'Class ID is required';
      return $response;
    }
    
    $checker = array('class_id' => $class_id, 'school_id' => $school_id, 'session' => $active_session);
    $response['subjects'] = \db()->table('subjects')->where($checker)->get()->getResultArray();
    $response['status'] = 200;
    $response['message'] = 'Data Fetched Successfully';
    return $response;
  }

  //GET STUDENT LIST
  public function get_students($user_id = "", $class_id = "", $school_id = "", $active_session = "") {
    $response = array();
    if (empty($school_id)) {
      $school_id = api_school_id($user_id);
    }
    if (empty($active_session)) {
      $active_session = api_active_session($user_id);
    }
    
    if (empty($class_id)) {
      $response['status'] = 400;
      $response['message'] = 'Class ID is required';
      return $response;
    }
    
    $checker = array('class_id' => $class_id, 'school_id' => $school_id, 'session' => $active_session);
    $response['students'] = $this->student_data('class', $class_id, $school_id, $active_session);
    $response['status'] = 200;
    $response['message'] = 'Data Fetched Successfully';
    return $response;
  }

  // GET STUDENT'S ALL THE NECESSARY INFORMATION FROM ONE SINGLE FUNCTION
  public function student_data($lookUpType = "", $lookUpId = "", $school_id = "", $active_session = "") {
    $response = array();
    if ($lookUpType == 'class') {
      $checker = array(
        'class_id' => $lookUpId,
        'session' => $active_session,
        'school_id' => $school_id
      );
      $enrolments = \db()->table('enrols')->where($checker)->get()->getResultArray();
      foreach ($enrolments as $key => $enrolment) {
        $student_details = \db()->table('students')->where('id', $enrolment['student_id'])->get()->getRowArray();
        $response[$key]['student_id'] = $student_details['id'];
        $response[$key]['student_code'] = $student_details['code'];
        $response[$key]['user_id'] = $student_details['user_id'];
  
        $user_details = \db()->table('users')->where('id', $enrolment['user_id'])->get()->getRowArray();
        $response[$key]['name'] = $user_details['name'];
        $response[$key]['email'] = $user_details['email'];
        $response[$key]['role'] = $user_details['role'];
        $response[$key]['address'] = $user_details['address'];
        $response[$key]['phone'] = $user_details['phone'];
        $response[$key]['birthday'] = $user_details['birthday'];
        $response[$key]['gender'] = $user_details['gender'];
        $response[$key]['blood_group'] = $user_details['blood_group'];
        $class_details = $this->crud_model->get_class_details_by_id($enrolment['class_id'])->getRowArray();
        $section_details = $this->crud_model->get_section_details_by_id('section', $enrolment['section_id'])->getRowArray();
        $response[$key]['class_name'] = $class_details['name'];
        $response[$key]['section_name'] = $section_details['name'];
        $response[$key]['school_id']  = $enrolment['school_id'];
        $response[$key]['image_url']  = $this->user_model->get_user_image($student_details['user_id']);
      }
    }
    elseif ($lookUpType == 'student') {
      $checker = array(
        'student_id' => $lookUpId,
        'session' => $active_session,
        'school_id' => $school_id
      );
      $enrolment = \db()->table('enrols')->where($checker)->get()->getRowArray();
      $student_details = \db()->table('students')->where('id', $lookUpId)->get()->getRowArray();
      $response['student_id'] = $student_details['id'];
      $response['student_code'] = $student_details['code'];
      $response['user_id'] = $student_details['user_id'];
      
      $user_details = \db()->table('users')->where('id', $student_details['user_id'])->get()->getRowArray();
      $response['name'] = $user_details['name'];
      $response['email'] = $user_details['email'];
      $response['role'] = $user_details['role'];
      $response['address'] = $user_details['address'];
      $response['phone'] = $user_details['phone'];
      $response['birthday'] = $user_details['birthday'];
      $response['gender'] = $user_details['gender'];
      $response['blood_group'] = $user_details['blood_group'];
      $class_details = $this->crud_model->get_class_details_by_id($enrolment['class_id'])->getRowArray();
      $section_details = $this->crud_model->get_section_details_by_id('section', $enrolment['section_id'])->getRowArray();
      $response['class_name'] = $class_details['name'];
      $response['section_name'] = $section_details['name'];
      $response['school_id']  = $enrolment['school_id'];
      $response['image_url']  = $this->user_model->get_user_image($student_details['user_id']);
    }

    return $response;
  }

  // GET STUDENT WISE EXAM & MARKS
  public function get_student_wise_marks($user_id = "", $student_id = "", $school_id = "", $active_session = "") {
      $response = array();
      if (empty($school_id)) {
        $school_id = api_school_id($user_id);
      }
      if (empty($active_session)) {
        $active_session = api_active_session($user_id);
      }
      
      if (empty($student_id)) {
        $response['status'] = 400;
        $response['message'] = 'Student ID is required';
        return $response;
      }
      
      $enrolment_checker = array(
          'student_id' => $student_id,
          'session' => $active_session,
          'school_id' => $school_id
      );
      $enrolment = \db()->table('enrols')->where($enrolment_checker)->get()->getRowArray();
      $class_details = $this->crud_model->get_class_details_by_id($enrolment['class_id'])->getRowArray();
      $section_details = $this->crud_model->get_section_details_by_id('section', $enrolment['section_id'])->getRowArray();

      $exam_checker = array(
          'school_id' => $school_id,
          'session' => $active_session
      );
      $exams = \db()->table('exams')->where($exam_checker)->get()->getResultArray();
      foreach ($exams as $key => $exam) {
          $exam_array = array();
          $exam_array['name'] = $exam['name'];


          /* GET MARKS BY EXAM IDS */
          $mark_checker = array(
              'student_id' => $student_id,
              'session' => $active_session,
              'school_id' => $school_id,
              'class_id' => $enrolment['class_id'],
              'section_id' => $enrolment['section_id'],
              'exam_id' => $exam['id']
          );

          $marks = \db()->table('marks')->where($mark_checker)->get()->getResultArray();
          $mark_array = array();
          foreach ($marks as $mark_key => $mark) {
              $subject_details = $this->crud_model->get_subject_by_id($mark['subject_id']);
              $mark_obtained = 0;
              if ($mark['mark_obtained'] > 0) {
                  $mark_obtained = $mark['mark_obtained'];
              }
              $mark_array[$mark_key]['subject'] = $subject_details['name'];
              $mark_array[$mark_key]['mark_obtained'] = $mark_obtained;
              $mark_array[$mark_key]['comment'] = $mark['comment'];

              // FIND THE MARK WISE GRADE
              $grade_details = \db()->table('grades')
                ->where('mark_from <=', $mark_obtained)
                ->where('mark_upto >=', $mark_obtained)
                ->get()
                ->getRowArray();
              $mark_array[$mark_key]['grade'] = $grade_details['name'];

          }
          $exam_array['marks'] = $mark_array;
          $response['exam'][$key] = $exam_array;
      }

      //
      // foreach ($marks as $key => $mark) {
      //     $subject_details = $this->crud_model->get_subject_by_id($mark['subject_id']);
      //     $response[$key]['student_id'] => $mark['student_id'];
      //     $response[$key]['subject_id'] => $mark['subject_id'];
      //     $response[$key]['subject_name'] => $subject_details['name'];
      //     $response[$key]['mark_obtained'] => $mark['mark_obtained'];
      //     $response[$key]['mark_obtained'] => $mark['mark_obtained'];
      //     $response[$key]['class_name'] => $class_details['name'];
      //     $response[$key]['section_name'] => $section_details['name'];
      // }
      $response['status'] = 200;
      $response['message'] = 'Data Fetched Successfully';
      return $response;
  }

  public function get_user_by_email($email) {
    return \db()->table('users')->where('email', $email)->get()->getRowArray();
  }

  public function get_user_by_id($user_id) {
    return \db()->table('users')->where('id', $user_id)->get()->getRowArray();
  }

  public function email_exists($email) {
    return \db()->table('users')->where('email', $email)->countAllResults() > 0;
  }

  public function user_exists($user_id) {
    return \db()->table('users')->where('id', $user_id)->countAllResults() > 0;
  }

  public function get_school_by_id($school_id) {
    return \db()->table('schools')->where('id', $school_id)->get()->getRowArray();
  }

  public function get_class_by_id($class_id) {
    return \db()->table('classes')->where('id', $class_id)->get()->getRowArray();
  }

  public function get_classes_by_school($school_id) {
    return \db()->table('classes')->where('school_id', $school_id)->get()->getResultArray();
  }

  public function get_section_by_id($section_id) {
    return \db()->table('sections')->where('id', $section_id)->get()->getRowArray();
  }

  public function get_exam_by_id($exam_id) {
    return \db()->table('exams')->where('id', $exam_id)->get()->getRowArray();
  }

  public function get_subject_by_id($subject_id) {
    return \db()->table('subjects')->where('id', $subject_id)->get()->getRowArray();
  }

  public function get_student_by_id($student_id) {
    return \db()->table('students')->where('id', $student_id)->get()->getRowArray();
  }

  public function get_teacher_by_id($teacher_id) {
    return \db()->table('teachers')->where('id', $teacher_id)->get()->getRowArray();
  }

  public function insert_table($table, $data) {
    return \db()->table($table)->insert($data);
  }

  public function update_table($table, $where, $data) {
    return \db()->table($table)->where($where)->update($data);
  }

  public function delete_table($table, $where) {
    return \db()->table($table)->where($where)->delete();
  }

  public function get_by_query($sql, $params = []) {
    if (!empty($params)) {
      return \db()->query($sql, $params)->getResultArray();
    }
    return \db()->query($sql)->getResultArray();
  }

  public function get_row($table, $where) {
    return \db()->table($table)->where($where)->get()->getRowArray();
  }

  public function get_where_result($table, $where = [], $select = '*') {
    return \db()->table($table)->select($select)->where($where)->get()->getResultArray();
  }

  public function get_where($table, $where) {
    return \db()->table($table)->where($where)->get()->getRow();
  }

  public function get_result($table, $select = '*') {
    return \db()->table($table)->select($select)->get()->getResultArray();
  }

  public function get_ordered($table, $order_by, $order = 'ASC', $where = []) {
    return \db()->table($table)->orderBy($order_by, $order)->where($where)->get()->getResultArray();
  }

  public function count_all($table, $where = []) {
    return \db()->table($table)->where($where)->countAllResults();
  }

  public function get($table, $select = '*', $where = null) {
    if ($where !== null) {
      return \db()->table($table)->select($select)->where($where)->get()->getResultArray();
    }
    return \db()->table($table)->select($select)->get()->getResultArray();
  }
}
