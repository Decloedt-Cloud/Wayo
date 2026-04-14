<?php
/*
*  @author   : Creativeitem
*  date      : December, 2019
*  Ekattor School Management System With Addons
*  http://codecanyon.net/user/Creativeitem
*  http://support.creativeitem.com
*/


//SCHOOL ID
if (! function_exists('api_school_id')) {
  function api_school_id($user_id) {
    $db = \Config\Database::connect();
    $userdata = get_where('users', array('id' => $user_id))->getRowArray();
    if (strtolower($userdata['role']) == 'superadmin') {
      return get_settings('school_id');
    }else{
      return $userdata['school_id'];
    }
  }
}

//ACTIVE SESSION
if (! function_exists('api_active_session')) {
  function api_active_session($user_id = '', $type = '') {
    $db = \Config\Database::connect();
    $school_id = api_school_id($user_id);
    if($type == ''){
      $session_details = get_where('sessions', array('status' => 1))->getRowArray();
      return $session_details['id'];
    }else{
      $session_details = get_where('sessions', array('status' => 1))->getRowArray();
      return $session_details[$type];
    }
  }
}

// ------------------------------------------------------------------------
/* End of file api_helper.php */
