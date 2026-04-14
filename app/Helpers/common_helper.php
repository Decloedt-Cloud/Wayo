<?php
use Config\Database;

if (!function_exists('db_get_where')) {
    function db_get_where($table, $where = []) {
        $db = Database::connect();
        $builder = $db->table($table);
        if (is_array($where) && !empty($where)) {
            $builder->where($where);
        }
        return $builder->get();
    }
}

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
    $session = \Config\Services::session();
    if ($session->get('user_type') == 'superadmin') {
      return get_settings('school_id');
    } else {
      if ($session->get('school_id') > 0) {
        return $session->get('school_id');
      }
      return 0;
    }
  }
}

//user id
if (!function_exists('user_id')) {
  function user_id()
  {
    $session = \Config\Services::session();
    $db = Database::connect();

    $user_id = $session->get('user_id');
    $user_details = $db->table('users')->where('id', $user_id)->get()->getRowArray();
    return $user_details ? $user_details['id'] : 0;
  }
}

if (!function_exists('get_settings')) {
  function get_settings($type = '')
  {
    $db = Database::connect();
    $result = db_get_where('settings', array('id' => 1))->getRowArray();
    return $result[$type] ?? null;
  }
}
if (!function_exists('get_user_language')) {
  function get_user_language() {
    $session = \Config\Services::session();
    $db = Database::connect();

    $session_lang = $session->get('language');
    if ($session_lang) {
      return $session_lang;
    }

    $user_id = $session->get('user_id');
    if ($user_id) {
      $user_data = db_get_where('users', ['id' => $user_id])->getRowArray();
      $language = isset($user_data['language']) && !empty($user_data['language']) 
                  ? $user_data['language'] 
                  : get_settings('language');
      
      $session->set('language', $language);
      return $language;
    }

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
    $session = \Config\Services::session();
    
    $lang_code = $session->get('lang_code');
    if ($lang_code) {
      return $lang_code;
    }
    
    $lang_name = strtolower(get_user_language());
    $supported = get_supported_lang_codes();
    
    foreach ($supported as $code => $name) {
      if ($name === $lang_name) {
        $session->set('lang_code', $code);
        return $code;
      }
    }
    
    $session->set('lang_code', 'en');
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
    $current_uri = \Config\Services::request()->getUri()->getPath();
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
    $controller = service('router')->fetchClass();
    
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
    $db = \Config\Database::connect();
    $result = $db->table('common_settings')->where('type', $type)->get()->getRow('description');
    return $result;
  }
}

if (!function_exists('get_payment_settings')) {
  function get_payment_settings($key = '',$school_id = '')
  {
    $db = \Config\Database::connect();
    $result = $db->table('payment_settings')->where('key', $key)->where('school_id', $school_id)->get()->getRow('value');
    return $result;
  }
}

if (!function_exists('timezone')) {
  function timezone($type = '')
  {
    $db = \Config\Database::connect();
    $result = db_get_where('settings', array('id' => 1))->getRowArray();
    date_default_timezone_set($result['timezone']);
  }
}

if (!function_exists('get_frontend_settings')) {
  function get_frontend_settings($type = '')
  {
    $db = \Config\Database::connect();
    $result = db_get_where('frontend_settings', array('id' => 1))->getRowArray();
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
    $model = model('SettingsModel');
    $result = $model->get_current_school_data();
    return $result[$type] ?? null;
  }
}


if (!function_exists('get_smtp')) {
  function get_smtp($type = '')
  {
    $db = \Config\Database::connect();
    $result = db_get_where('smtp_settings', array('id' => 1))->getRowArray();  
    return $result[$type] ?? null;
  }
}


if (!function_exists('get_sms')) {
  function get_sms($type = '')
  {
    $db = \Config\Database::connect();
    $result = db_get_where('sms_settings', array('id' => 1))->getRowArray();
    return $result[$type] ?? null;
  }
}

if (!function_exists('route')) {
  function route($path = '')
  {
    $path = ltrim((string) $path, '/');
    if ($path === '') return site_url('app/dashboard');

    $segments = array_values(array_filter(explode('/', $path), static fn($s) => $s !== ''));
    if (empty($segments)) return site_url('app/dashboard');

    $first = strtolower((string) $segments[0]);
    $second = strtolower((string) ($segments[1] ?? ''));

    $roleControllers = ['admin', 'teacher', 'student', 'superadmin', 'accountant', 'librarian', 'parent', 'parents'];
    $roleAliasMap = [
      'student' => 'member',
      'teacher' => 'mentor',
      'exam' => 'certifications',
      'event_calendar' => 'announcements',
      'school_settings' => 'community_settings',
      'school' => 'community_list',
    ];

    if ($first === 'app') {
      return site_url(implode('/', $segments));
    }

    if ($first === 'addons' && isset($segments[1])) {
      if (strtolower((string) $segments[1]) === 'courses') {
        $segments = array_merge(['app', 'courses'], array_slice($segments, 2));
        return site_url(implode('/', $segments));
      }
      if (strtolower((string) $segments[1]) === 'lessons') {
        $segments = array_merge(['app', 'lessons'], array_slice($segments, 2));
        return site_url(implode('/', $segments));
      }
    }

    if ($first === 'chat') {
      $segments = array_merge(['app', 'chat'], array_slice($segments, 1));
      return site_url(implode('/', $segments));
    }

    if (in_array($first, $roleControllers, true)) {
      if (isset($roleAliasMap[$second])) {
        $segments = array_merge(['app', $roleAliasMap[$second]], array_slice($segments, 2));
        return site_url(implode('/', $segments));
      }
      $segments = array_merge(['app'], array_slice($segments, 1));
      return site_url(implode('/', $segments));
    }

    return site_url('app/' . implode('/', $segments));
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
    $db = \Config\Database::connect();
    if ($lookup_value == 'parent') {
      $menu_detail = db_get_where('menus', array('id' => $menu_id))->getRowArray();
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
    $db = \Config\Database::connect();
    $session = \Config\Services::session();
    if(!empty($school_id)){
      $settings_data = db_get_where('settings_school', array('school_id' => $school_id ))->getRowArray(); 
    }else{
      $settings_data = db_get_where('settings_school', array('school_id' => $session->get('school_id') ))->getRowArray();
    }
    $currency_code = $settings_data['system_currency'];

    $symbol = $db->table('currencies')->where('code', $currency_code)->get()->getRow()->symbol;

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
    $db = \Config\Database::connect();
    $settings_data = db_get_where('settings_school', array('school_id ' => $school_id ))->getRowArray();
    $currency_code = $settings_data['system_currency'];

    $symbol = $db->table('currencies')->where('code', $currency_code)->get()->getRow()->symbol;

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
    $db = \Config\Database::connect();

    $settings_data = db_get_where('settings', array('id' => 1))->getRowArray();
    $currency_code = $settings_data['system_currency'];

    $symbol = $db->table('currencies')->where('code', $currency_code)->get()->getRow()->symbol;

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
    $db = \Config\Database::connect();
    $session = \Config\Services::session();

    $user_id = $session->get('user_id');
    $driver_details = $db->table('drivers')->where('user_id', $user_id)->get()->getRowArray();
    return $driver_details ? $driver_details['id'] : 0;
  }
}

//parent id
if (!function_exists('parent_id')) {
  function parent_id()
  {
    $db = \Config\Database::connect();
    $session = \Config\Services::session();

    $user_id = $session->get('user_id');
    $driver_details = $db->table('parents')->where('user_id', $user_id)->get()->getRowArray();
    return $driver_details ? $driver_details['id'] : 0;
  }
}

//ACTIVE SESSION
if (!function_exists('active_session')) {
    function active_session($param1 = '') {
        $db = \Config\Database::connect();
        
        $session_details = $db->table('sessions')->where('status', 1)->get()->getRowArray();
        
        if (empty($session_details)) {
            return 0;
        }

        if ($param1 == '') {
            return $session_details['id'];
        } else {
            return isset($session_details[$param1]) ? $session_details[$param1] : 0;
        }
    }
}

// TEACHER PERMISSION. PROVIDE MODULE NAME AND TEACHERS ID
if (!function_exists('has_permission')) {
  function has_permission($class_id = "", $module = "", $teacher_id = "")
  {
    $db = \Config\Database::connect();
    $session = \Config\Services::session();
    if (empty($teacher_id)) {
      $user_id = (int) ($session->get('user_id') ?? 0);
      $active_school_id = 0;
      if ((string) $class_id !== '') {
        $class_row = $db->table('classes')
          ->select('school_id')
          ->where('id', (int) $class_id)
          ->get()
          ->getRowArray();
        $active_school_id = (int) ($class_row['school_id'] ?? 0);
      }
      if ($active_school_id <= 0) {
        $active_school_id = (int) ($session->get('active_school_id') ?? 0);
      }
      if ($active_school_id <= 0) {
        $active_school_id = (int) (school_id() ?? 0);
      }
      if ($active_school_id <= 0) {
        $active_school_id = (int) ($session->get('school_id') ?? 0);
      }

      $teacher_details = [];
      if ($user_id > 0 && $active_school_id > 0) {
        $teacher_details = $db->table('teachers')
          ->where('user_id', $user_id)
          ->where('school_id', $active_school_id)
          ->get()
          ->getRowArray() ?? [];
      }
      if (empty($teacher_details) && $user_id > 0) {
        $teacher_details = $db->table('teachers')
          ->where('user_id', $user_id)
          ->get()
          ->getRowArray() ?? [];
      }
      $teacher_id = (int) ($teacher_details['id'] ?? 0);
      if ($teacher_id <= 0) {
        return 0;
      }
    }

    $permission_details = $db->table('teacher_permissions')
      ->where('class_id', $class_id)
      ->where('teacher_id', $teacher_id)
      ->get()
      ->getRowArray();

    if (!empty($permission_details)) {
      return (int) ($permission_details[$module] ?? 0);
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
    $db = \Config\Database::connect();
    if (empty($acquired_number)) {
      return "N/A";
    } else {
      $acquired_grade = $db->table('grades')->where('school_id', school_id())->get();
      if ($acquired_grade->numRows() > 0) {
        $acquired_grade = $acquired_grade->getResultArray();
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


// PAYMENT AND CURRENCY HELPER FUNCTIONS

if (!function_exists('get_system_base_currency')) {
  function get_system_base_currency()
  {
    $db = \Config\Database::connect();
    $result = $db->table('settings')->where('id', 1)->get()->getRowArray();
    return $result['currency'];
  }
}

if (!function_exists('get_currency_by_country')) {
  function get_currency_by_country($country_code, $default_currency = 'MAD')
  {
    $country_currency_map = [
      'MA' => 'MAD',
      'TN' => 'MAD',
      'DZ' => 'MAD',
      'EG' => 'MAD',
      'LY' => 'MAD',
      'FR' => 'EUR',
      'DE' => 'EUR',
      'IT' => 'EUR',
      'ES' => 'EUR',
      'NL' => 'EUR',
      'BE' => 'EUR',
      'US' => 'USD',
      'CA' => 'USD',
      'GB' => 'GBP',
    ];
    return isset($country_currency_map[$country_code]) ? $country_currency_map[$country_code] : $default_currency;
  }
}

if (!function_exists('get_stripe_currency')) {
  function get_stripe_currency($school_id = 1)
  {
    $db = \Config\Database::connect();
    try {
      $stripe_row = db_get_where('payment_settings', [
        'school_id' => $school_id,
        'key' => 'stripe_settings'
      ])->getRow();

      if ($stripe_row) {
        $stripe_settings = json_decode($stripe_row->value, true);
        if (is_array($stripe_settings) && isset($stripe_settings[0]['stripe_currency'])) {
          return strtoupper($stripe_settings[0]['stripe_currency']);
        }
      }
    } catch (Exception $e) {
      log_message('error', 'Erreur lors de la récupération de la devise Stripe: ' . $e->getMessage());
    }

    return 'USD';
  }
}

if (!function_exists('get_conversion_rate')) {
  function get_conversion_rate($from, $to)
  {
    $db = \Config\Database::connect();
    $rate = db_get_where('currency', ['from' => $from, 'to' => $to])->getRowArray();
    return $rate ? $rate['rate'] : 1;
  }
}

if (!function_exists('convertir_mad_vers_stripe')) {
  function convertir_mad_vers_stripe($montant_mad, $devise_stripe)
  {
    $taux = get_conversion_rate('MAD', strtoupper($devise_stripe));
    return round($montant_mad * $taux * 100);
  }
}

if (!function_exists('get_available_gateway_currencies')) {
  function get_available_gateway_currencies($school_id, $original_currency = 'EUR')
  {
    $currencies = ['EUR', 'USD', 'GBP'];
    if (strtoupper($original_currency) !== 'EUR' && in_array(strtoupper($original_currency), $currencies)) {
      return $currencies;
    }
    return $currencies;
  }
}

if (!function_exists('get_vat_rate')) {
  function get_vat_rate()
  {
    $db = \Config\Database::connect();
    $vat_rate = db_get_where('settings_school', [
      'school_id' => school_id(),
      'type' => 'vat'
    ])->getRow();

    if ($vat_rate && isset($vat_rate->description)) {
      return (float) $vat_rate->description;
    }

    return 5.0;
  }
}

if (!function_exists('update_url_language_prefix')) {
  function update_url_language_prefix($url, $new_lang_code) {
    $supported_codes = array('fr', 'en', 'ar', 'es', 'nl');
    $backend_prefixes = array('app', 'admin', 'teacher', 'student', 'superadmin', 'login', 'api', 'cron', 'wall', 'class_wall');
    
    $parsed = parse_url($url);
    $path = isset($parsed['path']) ? $parsed['path'] : '/';
    $base_path = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    $relative_path = $path;
    
    if (!empty($base_path) && strpos($path, $base_path) === 0) {
      $relative_path = substr($path, strlen($base_path));
    }
    
    $segments = explode('/', trim($relative_path, '/'));
    $first_segment = !empty($segments[0]) ? strtolower($segments[0]) : '';
    
    if (in_array($first_segment, $supported_codes)) {
      $segments[0] = $new_lang_code;
    } elseif (!in_array($first_segment, $backend_prefixes)) {
      array_unshift($segments, $new_lang_code);
    } else {
      return $url;
    }
    
    $new_path = $base_path . '/' . implode('/', $segments);
    $scheme = isset($parsed['scheme']) ? $parsed['scheme'] . '://' : '';
    $host = isset($parsed['host']) ? $parsed['host'] : '';
    $port = isset($parsed['port']) ? ':' . $parsed['port'] : '';
    $query = isset($parsed['query']) ? '?' . $parsed['query'] : '';
    $fragment = isset($parsed['fragment']) ? '#' . $parsed['fragment'] : '';
    
    return $scheme . $host . $port . $new_path . $query . $fragment;
  }
}

if (!function_exists('has_user_joined_school')) {
  function has_user_joined_school($user_id = null) {
    $db = \Config\Database::connect();
    $session = \Config\Services::session();
    
    if ($user_id === null) {
      $user_id = $session->get('user_id');
    }
    
    $student = $db->table('students')->where('user_id', $user_id)
      ->where('school_id IS NOT NULL', null, false)
      ->get()
      ->getRow();
    
    return !empty($student);
  }
}

if (!function_exists('clear_query_lock')) {
  function clear_query_lock() {
    $session = \Config\Services::session();
    $session->remove(['doc_query_in_progress', 'doc_query_started_at']);
  }
}

if (!function_exists('respond_with_json')) {
  function respond_with_json($data) {
    $security = \Config\Services::security();
    
    $data['csrf'] = array(
      'csrfName' => $security->get_csrf_token_name(),
      'csrfHash' => $security->get_csrf_hash()
    );
    
    return service('response')
      ->setContentType('application/json')
      ->setBody(json_encode($data));
  }
}

if (!function_exists('detect_user_country')) {
  function detect_user_country() {
    $request = \Config\Services::request();
    
    $user_ip = $request->getIPAddress();
    
    if ($user_ip === '127.0.0.1' || $user_ip === '::1') {
      $cf_country = $request->getServer('HTTP_CF_IPCOUNTRY');
      if (!empty($cf_country)) {
        return strtoupper($cf_country);
      }
      
      $forwarded_country = $request->getServer('HTTP_X_FORWARDED_COUNTRY');
      if (!empty($forwarded_country)) {
        return strtoupper($forwarded_country);
      }
      
      return 'MA';
    }
    
    try {
      $geo_url = "http://ip-api.com/json/{$user_ip}";
      $context = stream_context_create([
        'http' => [
          'timeout' => 2,
          'user_agent' => 'SchoolManagement/1.0'
        ]
      ]);
      
      $geo_data = @file_get_contents($geo_url, false, $context);
      if ($geo_data) {
        $geo = json_decode($geo_data, true);
        if ($geo && isset($geo['countryCode']) && $geo['status'] === 'success') {
          return strtoupper($geo['countryCode']);
        }
      }
    } catch (Exception $e) {
      log_message('debug', 'Erreur géolocalisation IP: ' . $e->getMessage());
    }
    
    return 'UNKNOWN';
  }
}

if (!function_exists('get_smtp_settings')) {
  function get_smtp_settings() {
    $db = \Config\Database::connect();
    
    $smtp_settings = $db->table('smtp_settings')->where('id', 1)->get()->getRowArray();
    
    if (!$smtp_settings) {
      return [];
    }
    return $smtp_settings;
  }
}

if (!function_exists('get_all_smtp_settings')) {
  function get_all_smtp_settings() {
    $db = \Config\Database::connect();
    
    $smtp_settings = $db->table('smtp_settings')->get()->getResultArray();
    
    if (empty($smtp_settings)) {
      return [];
    }
    return $smtp_settings;
  }
}

if (!function_exists('get_school_id_by_name')) {
  function get_school_id_by_name($school_name) {
    $db = \Config\Database::connect();
    
    $query = db_get_where('schools', array('name' => $school_name));
    
    if ($query->numRows() > 0) {
      $result = $query->getRow();
      return $result->id;
    }
    return null;
  }
}

if (!function_exists('get_class_id_by_name')) {
  function get_class_id_by_name($name) {
    $db = \Config\Database::connect();
    
    $query = $db->table('classes')->select('id')->where('name', $name)->get();
    
    if ($query->numRows() > 0) {
      return $query->getRow()->id;
    }
    return false;
  }
}

if (!function_exists('get_user_id_by_name')) {
  function get_user_id_by_name($name) {
    $db = \Config\Database::connect();
    
    $query = $db->table('users')->select('id')->where('name', $name)->get();
    
    if ($query->numRows() > 0) {
      return $query->getRow()->id;
    }
    return false;
  }
}

if (!function_exists('get_subject_id_by_name')) {
  function get_subject_id_by_name($name) {
    $db = \Config\Database::connect();
    
    $query = $db->table('subjects')->select('id')->where('name', $name)->get();
    
    if ($query->numRows() > 0) {
      return $query->getRow()->id;
    }
    return false;
  }
}

if (!function_exists('get_class_name')) {
  function get_class_name($class_id) {
    $db = \Config\Database::connect();
    
    $class = db_get_where('classes', array('id' => $class_id))->getRowArray();
    return $class ? $class['name'] : 'N/A';
  }
}

if (!function_exists('get_section_name')) {
  function get_section_name($section_id) {
    $db = \Config\Database::connect();
    
    $section = db_get_where('sections', array('id' => $section_id))->getRowArray();
    return $section ? $section['name'] : 'N/A';
  }
}
