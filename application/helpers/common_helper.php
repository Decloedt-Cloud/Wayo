<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
*  @author   : Creativeitem
*  date      : November, 2019
*  Ekattor School Management System With Addons
*  http://codecanyon.net/user/Creativeitem
*  http://support.creativeitem.com
*/


//SCHOOL ID
if (!function_exists('school_id')) {
  function school_id()
  {
    $CI = &get_instance();
    if ($CI->session->userdata('user_type') == 'superadmin') {
      return get_settings('school_id');
    } else {
      if ($CI->session->userdata('school_id') > 0) {
        return $CI->session->userdata('school_id');
      }
      // else {
      //   return get_settings('school_id');
      // }
      return 0;
    }
  }
}

//user id
if (!function_exists('user_id')) {
  function user_id()
  {
    $CI = &get_instance();
    $CI->load->database();

    $user_id = $CI->session->userdata('user_id');
    $user_details = $CI->db->where('id', $user_id)->get('users')->row_array();
    return $user_details ? $user_details['id'] : 0;
  }
}

if (!function_exists('get_settings')) {
  function get_settings($type = '')
  {
    $CI  = &get_instance();
    $CI->load->database();
    $result = $CI->db->get_where('settings', array('id' => 1))->row_array();
    return $result[$type];
  }
}
// Dans common_helper.php
if (!function_exists('get_user_language')) {
  function get_user_language() {
    $CI =& get_instance();
    $CI->load->database();

    // First check if language is set in session (works for both logged-in and guests)
    $session_lang = $CI->session->userdata('language');
    if ($session_lang) {
      return $session_lang;
    }

    $user_id = $CI->session->userdata('user_id');
    if ($user_id) {
      $user_data = $CI->db->get_where('users', ['id' => $user_id])->row_array();
      $language = isset($user_data['language']) && !empty($user_data['language']) 
                  ? $user_data['language'] 
                  : get_settings('language');
      
      // Cache in session for subsequent calls
      $CI->session->set_userdata('language', $language);
      return $language;
    }

    // Default language
    return get_settings('language');
  }
}

// =====================================================
// LANGUAGE PREFIX URL HELPERS
// =====================================================

/**
 * Get supported languages with their codes
 */
if (!function_exists('get_supported_lang_codes')) {
  function get_supported_lang_codes() {
    return array(
      'fr' => 'french',
      'en' => 'english',
      'ar' => 'arabic',
      'es' => 'spanish',
      'nl' => 'dutch'
    );
  }
}

/**
 * Get current language code (fr, en, ar, etc.)
 */
if (!function_exists('get_current_lang_code')) {
  function get_current_lang_code() {
    $CI =& get_instance();
    
    // First check if stored in session
    $lang_code = $CI->session->userdata('lang_code');
    if ($lang_code) {
      return $lang_code;
    }
    
    // Get from user language and compute the code
    $lang_name = strtolower(get_user_language());
    $supported = get_supported_lang_codes();
    
    foreach ($supported as $code => $name) {
      if ($name === $lang_name) {
        // Cache in session for subsequent calls
        $CI->session->set_userdata('lang_code', $code);
        return $code;
      }
    }
    
    // Default to English
    $CI->session->set_userdata('lang_code', 'en');
    return 'en';
  }
}

/**
 * Generate a language-prefixed URL for frontend pages
 * 
 * @param string $path The path without language prefix (e.g., 'home/communities')
 * @param string|null $lang_code Optional language code, uses current if null
 * @return string Full URL with language prefix
 */
if (!function_exists('lang_url')) {
  function lang_url($path = '', $lang_code = null) {
    if ($lang_code === null) {
      $lang_code = get_current_lang_code();
    }
    
    // Clean the path
    $path = ltrim($path, '/');
    
    // List of frontend paths that should have language prefix
    $frontend_paths = array(
      'home', 'communities', 'tutorial', 'getting_started', 'help-center', 'faq',
      'contact', 'support', 'about', 'affiliation', 'terms', 'terms_conditions',
      'privacy_policy', 'community_details', 'trends', 'teachers', 'events',
      'gallery', 'gallery_view', 'noticeboard', 'notice_details', 'join',
      'webinaire'
    );
    
    // Check if this path should have a language prefix
    $should_prefix = false;
    $path_parts = explode('/', $path);
    $first_segment = isset($path_parts[0]) ? $path_parts[0] : '';
    
    // Remove 'home/' prefix if present for checking
    if ($first_segment === 'home' && isset($path_parts[1])) {
      $check_segment = $path_parts[1];
      // Remove 'home' from path for certain routes
      if (in_array($check_segment, $frontend_paths) || $check_segment === '') {
        $should_prefix = true;
        // Rewrite path to remove 'home/' prefix for clean URLs
        if ($check_segment !== '' && $check_segment !== 'index') {
          array_shift($path_parts); // Remove 'home'
          $path = implode('/', $path_parts);
        } else {
          $path = ''; // Just the language prefix for home page
        }
      }
    } else if (in_array($first_segment, $frontend_paths)) {
      $should_prefix = true;
    }
    
    if ($should_prefix) {
      return site_url($lang_code . '/' . $path);
    }
    
    // For non-frontend paths (backend, api, etc.), return without prefix
    return site_url($path);
  }
}

/**
 * Generate a language-prefixed URL using route-style short names
 * 
 * @param string $route_name Short route name (e.g., 'communities', 'about')
 * @param string|null $param Optional parameter
 * @param string|null $lang_code Optional language code
 * @return string Full URL with language prefix
 */
if (!function_exists('lang_route')) {
  function lang_route($route_name, $param = null, $lang_code = null) {
    if ($lang_code === null) {
      $lang_code = get_current_lang_code();
    }
    
    // Handle 'home' specially - just the language code
    if ($route_name === 'home' || $route_name === '' || $route_name === 'index') {
      return site_url($lang_code);
    }
    
    $path = $route_name;
    if ($param !== null) {
      $path .= '/' . $param;
    }
    
    return site_url($lang_code . '/' . $path);
  }
}

/**
 * Get URL for switching to a different language
 * Preserves the current path but changes the language prefix
 * 
 * @param string $lang_code Target language code
 * @return string URL with new language prefix
 */
if (!function_exists('switch_lang_url')) {
  function switch_lang_url($lang_code) {
    $CI =& get_instance();
    $current_uri = $CI->uri->uri_string();
    $supported = get_supported_lang_codes();
    
    // Check if current URI has a language prefix
    $uri_parts = explode('/', $current_uri);
    if (!empty($uri_parts[0]) && array_key_exists($uri_parts[0], $supported)) {
      // Replace existing language prefix
      $uri_parts[0] = $lang_code;
      return site_url(implode('/', $uri_parts));
    }
    
    // No existing prefix, add one
    return site_url($lang_code . '/' . $current_uri);
  }
}

/**
 * Check if we are on a frontend page (to decide if we should use lang_url)
 */
if (!function_exists('is_frontend_page')) {
  function is_frontend_page() {
    $CI =& get_instance();
    $controller = $CI->router->fetch_class();
    
    $frontend_controllers = array('home', 'articles', 'admission');
    return in_array(strtolower($controller), $frontend_controllers);
  }
}

/**
 * Smart URL generator - uses lang_url for frontend, site_url for backend
 */
if (!function_exists('smart_url')) {
  function smart_url($path = '', $lang_code = null) {
    // Check if this looks like a frontend path
    $frontend_indicators = array(
      'home/', 'communities', 'tutorial', 'getting_started', 'help-center',
      'faq', 'contact', 'support', 'about', 'affiliation', 'terms',
      'privacy_policy', 'community_details', 'trends', 'teachers',
      'events', 'gallery', 'noticeboard', 'join/', 'webinaire'
    );
    
    $is_frontend = false;
    foreach ($frontend_indicators as $indicator) {
      if (strpos($path, $indicator) === 0 || $path === '' || $path === 'home') {
        $is_frontend = true;
        break;
      }
    }
    
    if ($is_frontend) {
      return lang_url($path, $lang_code);
    }
    
    return site_url($path);
  }
}

if (!function_exists('get_common_settings')) {
  function get_common_settings($type = '')
  {
    $CI = &get_instance();
    $CI->load->database();

    $CI->db->where('type', $type);
    $result = $CI->db->get('common_settings')->row('description');
    return $result;
  }
}

if (!function_exists('get_payment_settings')) {
  function get_payment_settings($key = '',$school_id = '')
  {
    $CI = &get_instance();
    $CI->load->database();
    // die("dfdd");
    // if()
    $CI->db->where('key', $key);
    $CI->db->where('school_id', $school_id);

    $result = $CI->db->get('payment_settings')->row('value');
    return $result;
  }
}

if (!function_exists('timezone')) {
  function timezone($type = '')
  {
    $CI  = &get_instance();
    $CI->load->database();
    $result = $CI->db->get_where('settings', array('id' => 1))->row_array();
    date_default_timezone_set($result['timezone']);
  }
}

if (!function_exists('get_frontend_settings')) {
  function get_frontend_settings($type = '')
  {
    $CI  = &get_instance();
    $CI->load->database();
    $result = $CI->db->get_where('frontend_settings', array('id' => 1))->row_array();
    if ($type == 'facebook' || $type == 'twitter' || $type == 'linkedin' || $type == 'google' || $type == 'youtube' || $type == 'instagram') {
      $social = $result['social_links'];
      $links = json_decode($social);
      return $links[0]->$type;
    }
    return $result[$type];
  }
}

if (!function_exists('get_current_school_data')) {
  function get_current_school_data($type = '')
  {
    $CI  = &get_instance();
    $CI->load->database();
    $result = $CI->settings_model->get_current_school_data();
    return $result[$type];
  }
}


if (!function_exists('get_smtp')) {
  function get_smtp($type = '')
  {
    $CI  = &get_instance();
    $CI->load->database();
     $result = $CI->db->get_where('smtp_settings', array('id' => 1))->row_array();  
     return $result[$type];
  }
}


if (!function_exists('get_sms')) {
  function get_sms($type = '')
  {
    $CI  = &get_instance();
    $CI->load->database();
    $result = $CI->db->get_where('sms_settings', array('id' => 1))->row_array();
    return $result[$type];
  }
}

if (!function_exists('route')) {
  function route($path = '')
  {
    $CI  = &get_instance();
    $controller = "";
    if ($CI->session->userdata('user_type') == 'parent') {
      $controller = 'parents';
    } else {
      $controller = $CI->session->userdata('user_type');
    }
    return site_url($controller . '/' . $path);
  }
}

if (!function_exists('trimmer')) {
  function trimmer($text)
  {
    $text = preg_replace('~[^\\pL\d]+~u', '-', $text);
    $text = trim($text, '-');
    $text = strtolower($text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    if (empty($text))
      return 'n-a';
    return $text;
  }
}

if (!function_exists('get_menu')) {
  function get_menu($menu_id, $parameter)
  {
    $CI  = &get_instance();
    if ($lookup_value == 'parent') {
      $menu_detail = $CI->db->get_where('menus', array('id' => $menu_id))->row_array();
      return $menu_detail['parent'];
    }
  }
}

if (!function_exists('slugify')) {
  function slugify($text)
  {
    $text = preg_replace('~[^\\pL\d]+~u', '-', $text);
    $text = trim($text, '-');
    $text = strtolower($text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    if (empty($text))
      return 'n-a';
    return $text;
  }
}

// Currency helpers
if (!function_exists('currency')) {
  function currency($price = "", $school_id = "")
  {
    $CI  = &get_instance();
    $CI->load->database();
    if(!empty($school_id)){
      $settings_data = $CI->db->get_where('settings_school', array('school_id' => $school_id ))->row_array(); 
    }else{
      $settings_data = $CI->db->get_where('settings_school', array('school_id' => $CI->session->userdata('school_id') ))->row_array();
    }
    // return $CI->session->userdata('school_id') ;
    // $settings_data = $CI->db->get_where('settings_school', array('school_id' => $CI->session->userdata('school_id') ))->row_array();
    $currency_code = $settings_data['system_currency'];

    $CI->db->where('code', $currency_code);
    $symbol = $CI->db->get('currencies')->row()->symbol;

    // Force AED symbol to 'AED' instead of Arabic
    if ($currency_code == 'AED' || $symbol == 'د.م' || $symbol == 'د.م.') {
      $symbol = 'AED';
    }

    $position = $settings_data['currency_position'];

    // Force right-space position for AED
    if ($currency_code == 'AED' || $symbol == 'AED') {
      $position = 'right-space';
    }

    if ($position == 'right') {
      return $price . $symbol;
    } elseif ($position == 'right-space') {
      return $price . ' ' . $symbol;
    } elseif ($position == 'left') {
      return $symbol . $price;
    } elseif ($position == 'left-space') {
      return $symbol . ' ' . $price;
    }
  }
}

// Currency helpers
if (!function_exists('currency_payment')) {
  function currency_payment($price = "",$school_id= "" )
  {
    $CI  = &get_instance();
    $CI->load->database();
    $settings_data = $CI->db->get_where('settings_school', array('school_id ' => $school_id ))->row_array();
    $currency_code = $settings_data['system_currency'];

    $CI->db->where('code', $currency_code);
    $symbol = $CI->db->get('currencies')->row()->symbol;

    // Force AED symbol to 'AED' instead of Arabic
    if ($currency_code == 'AED' || $symbol == 'د.م' || $symbol == 'د.م.') {
      $symbol = 'AED';
    }

    $position = $settings_data['currency_position'];

    // Force right-space position for AED
    if ($currency_code == 'AED' || $symbol == 'AED') {
      $position = 'right-space';
    }

    if ($position == 'right') {
      return $price . $symbol;
    } elseif ($position == 'right-space') {
      return $price . ' ' . $symbol;
    } elseif ($position == 'left') {
      return $symbol . $price;
    } elseif ($position == 'left-space') {
      return $symbol . ' ' . $price;
    }
  }
}

if (!function_exists('currency_code_and_symbol')) {
  function currency_code_and_symbol($type = "")
  {
    $CI  = &get_instance();
    $CI->load->database();

    $settings_data = $CI->db->get_where('settings', array('id' => 1))->row_array();
    $currency_code = $settings_data['system_currency'];

    $CI->db->where('code', $currency_code);
    $symbol = $CI->db->get('currencies')->row()->symbol;

    // Force AED symbol to 'AED' instead of Arabic
    if ($currency_code == 'AED' || $symbol == 'د.م' || $symbol == 'د.م.') {
      $symbol = 'AED';
    }

    if ($type == "") {
      return $symbol;
    } else {
      return $currency_code;
    }
  }
}




//driver id
if (!function_exists('driver_id')) {
  function driver_id()
  {
    $CI = &get_instance();
    $CI->load->database();

    $user_id = $CI->session->userdata('user_id');
    $driver_details = $CI->db->where('user_id', $user_id)->get('drivers')->row_array();
    return $driver_details ? $driver_details['id'] : 0;
  }
}

//parent id
if (!function_exists('parent_id')) {
  function parent_id()
  {
    $CI = &get_instance();
    $CI->load->database();

    $user_id = $CI->session->userdata('user_id');
    $driver_details = $CI->db->where('user_id', $user_id)->get('parents')->row_array();
    return $driver_details ? $driver_details['id'] : 0;
  }
}

//ACTIVE SESSION
if (!function_exists('active_session')) {
    function active_session($param1 = '') {
        $CI =& get_instance();
        $CI->load->database();
        
        // Préciser la table pour la colonne 'status'
        $CI->db->select('*');
        $CI->db->from('sessions');
        $CI->db->where('sessions.status', 1);
        $session_details = $CI->db->get()->row_array();
        
        if ($param1 == '') {
            return $session_details['id'];
        } else {
            return $session_details[$param1];
        }
    }
}

// TEACHER PERMISSION. PROVIDE MODULE NAME AND TEACHERS ID
if (!function_exists('has_permission')) {
  function has_permission($class_id = "", $module = "", $teacher_id = "")
  {
    $CI = &get_instance();
    $CI->load->database();
    if (empty($teacher_id)) {
      $user_id = $CI->session->userdata('user_id');
      $teacher_details = $CI->db->get_where('teachers', array('user_id' => $user_id))->row_array();
      $teacher_id = $teacher_details['id'];
    }
    $school_id = school_id();
    $permission_details = $CI->db->get_where('teacher_permissions', array('class_id' => $class_id, 'teacher_id' => $teacher_id));
    if ($permission_details->num_rows() > 0) {
      $permission_details = $permission_details->row_array();
      return $permission_details[$module];
    } else {
      return 0;
    }
  }
}

// TEACHER PERMISSION. PROVIDE MODULE NAME AND TEACHERS ID
if (!function_exists('null_checker')) {
  function null_checker($value = "")
  {
    if (trim($value, "") == "") {
      return '(' . get_phrase('not_found') . ')';
    } else {
      return $value;
    }
  }
}

// RANDOM NUMBER GENERATOR FOR STUDENT CODE
if (!function_exists('student_code')) {
  function student_code($length_of_string = 8)
  {
    // String of all numeric character
    $str_result = '0123456789';
    // Shufle the $str_result and returns substring of specified length
    $unique_id = substr(str_shuffle($str_result), 0, $length_of_string);
    $splited_unique_id = str_split($unique_id, 4);
    $running_year = date('Y');
    $student_code = $running_year . '-' . $splited_unique_id[0] . '-' . $splited_unique_id[1];
    return $student_code;
  }
}

// RANDOM NUMBER GENERATOR FOR ELSEWHERE
if (!function_exists('random')) {
  function random($length_of_string)
  {
    // String of all alphanumeric character
    $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

    // Shufle the $str_result and returns substring
    // of specified length
    return substr(str_shuffle($str_result), 0, $length_of_string);
  }
}

// ELLIPSIS A TEXT
if (!function_exists('ellipsis')) {
  // Checks if a video is youtube, vimeo or any other
  function ellipsis($long_string, $max_character = 30)
  {
    $short_string = strlen($long_string) > $max_character ? substr($long_string, 0, $max_character) . "..." : $long_string;
    return $short_string;
  }
}


// GET GRADE
if (!function_exists('get_grade')) {
  function get_grade($acquired_number = "", $type = "")
  {
    $CI = &get_instance();
    $CI->load->database();
    if (empty($acquired_number)) {
      return "N/A";
    } else {
      $CI->db->where('school_id', school_id());
      $acquired_grade = $CI->db->get('grades');
      if ($acquired_grade->num_rows() > 0) {
        $acquired_grade = $acquired_grade->result_array();
        $founder = false;
        foreach ($acquired_grade as $grade) {
          if ($acquired_number >= $grade['mark_from'] && $acquired_number <= $grade['mark_upto']) {
            $founder = true;
            if (!empty($type)) {
              return $grade[$type];
            } else {
              return $grade['name'] . '(' . $grade['grade_point'] . ')';
            }
          }
        }
        if (!$founder) {
          return "N/A";
        }
      } else {
        return "N/A";
      }
    }
  }
}


// ------------------------------------------------------------------------
/* End of file common_helper.php */
