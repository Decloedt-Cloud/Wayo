<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Language Detector Hook
 * Detects language from URL prefix and sets it in session
 * Redirects frontend URLs without language prefix to the language-prefixed version
 */
class Language_detector
{
    /**
     * Supported language codes mapped to language names
     */
    private $supported_langs = array(
        'fr' => 'french',
        'en' => 'english',
        'ar' => 'arabic',
        'es' => 'spanish',
        'nl' => 'dutch'
    );

    /**
     * Default language code
     */
    private $default_lang_code = 'en';

    /**
     * Backend/excluded URL prefixes that should NOT have language prefix
     */
    private $backend_prefixes = array(
        'admin', 'teacher', 'student', 'superadmin', 'app', 'api', 
        'login', 'cron', 'export', 'addons', 'meeting', 'bigbluebutton',
        'assets', 'uploads', 'courses', 'admission', 'payment', 'test-meeting',
        'articles'
    );

    /**
     * AJAX/API methods in Home controller that should NOT be redirected
     * These are backend endpoints, not frontend pages
     */
    private $home_ajax_methods = array(
        'dropdown_guest', 'dropdown_guest_lang', 'set_guest_language',
        'check_student_status_ajax', 'switch_community_role', 'switch_community_role_front',
        'get_user_communities', 'get_user_roles', 'get_communities_by_role',
        'get_student_communities', 'switch_to_member_account', 'switch_to_member_account_front',
        'online_admission_school', 'check_community_name_exists', 'active_school_id_for_frontend',
        'communities_search', 'refresh_csrf'
    );

    /**
     * Frontend routes that MUST have language prefix
     */
    private $frontend_routes = array(
        'communities', 'community_details', 'tutorial', 'getting_started',
        'help-center', 'faq', 'contact', 'support', 'about', 'affiliation',
        'terms', 'terms_conditions', 'privacy_policy', 'trends', 'teachers',
        'events', 'gallery', 'gallery_view', 'noticeboard', 'notice_details',
        'join', 'webinaire'
    );

    /**
     * Old-style home/X routes that should redirect to homepage
     * (these pages are not available without language prefix)
     */
    private $blocked_home_routes = array(
        'teachers', 'events', 'gallery', 'gallery_view', 
        'noticeboard', 'notice_details', 'webinaire'
    );

    /**
     * Detect language from URL and set in session
     * Redirect frontend URLs without language prefix
     */
    public function detect()
    {
        $CI = &get_instance();
        $CI->load->library('session');
        $CI->load->database();
        
        // Get URL info
        $url_info = $this->parse_url();
        $lang_code = $url_info['lang_code'];
        $first_segment = $url_info['first_segment'];
        $relative_path = $url_info['relative_path'];
        
        // Check if this is a language-prefixed URL trying to access an AJAX method
        // e.g., /en/dropdown_guest_lang should redirect to /home/dropdown_guest_lang
        if ($lang_code) {
            $path_after_lang = $this->get_path_after_lang($relative_path);
            $second_segment = explode('/', $path_after_lang)[0];
            
            if (in_array($second_segment, $this->home_ajax_methods)) {
                // Redirect to correct AJAX URL without language prefix
                $this->redirect_to_ajax_url($second_segment, $path_after_lang);
                return;
            }
        }
        
        // Check if this is an old-style home/X route that should redirect to homepage
        // e.g., /home/teachers, /home/events, etc. -> redirect to /en (or user's language)
        if ($first_segment === 'home' && !$lang_code) {
            $path_parts = explode('/', $relative_path);
            if (isset($path_parts[1]) && in_array($path_parts[1], $this->blocked_home_routes)) {
                $this->redirect_to_home($CI);
                return;
            }
        }
        
        // Check if this is a frontend URL without language prefix - redirect if so
        if (!$lang_code && $this->is_frontend_url($first_segment, $relative_path)) {
            $this->redirect_to_language_url($CI, $relative_path);
            return;
        }
        
        if ($lang_code) {
            // Language prefix found in URL - set it in session
            $lang_name = $this->supported_langs[$lang_code];
            
            // Check if user is logged in
            $user_id = $CI->session->userdata('user_id');
            
            if ($user_id) {
                // Update user's language preference in database
                $CI->db->where('id', $user_id);
                $CI->db->update('users', array('language' => $lang_name));
            }
            
            // Store in session (for both guest and logged in)
            $CI->session->set_userdata('language', $lang_name);
            $CI->session->set_userdata('lang_code', $lang_code);
        } else {
            // No language prefix (backend URL) - determine from existing settings
            $user_id = $CI->session->userdata('user_id');
            
            // First check if language is already in session
            $session_lang = $CI->session->userdata('language');
            
            if ($session_lang) {
                $lang_name = $session_lang;
            } elseif ($user_id) {
                // Get user's language from database
                $user = $CI->db->get_where('users', array('id' => $user_id))->row_array();
                $lang_name = isset($user['language']) && !empty($user['language']) 
                           ? $user['language'] 
                           : $this->get_default_language($CI);
                $CI->session->set_userdata('language', $lang_name);
            } else {
                // Guest - use default
                $lang_name = $this->get_default_language($CI);
                $CI->session->set_userdata('language', $lang_name);
            }
            
            // Set language code based on language name
            $lang_code = $this->get_code_from_name($lang_name);
            $CI->session->set_userdata('lang_code', $lang_code);
        }
    }

    /**
     * Parse the current URL and extract language info
     */
    private function parse_url()
    {
        $result = array(
            'lang_code' => null,
            'first_segment' => '',
            'relative_path' => ''
        );
        
        if (!isset($_SERVER['REQUEST_URI'])) {
            return $result;
        }
        
        $request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Get the script path to determine base directory
        $script_name = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
        $base_dir = rtrim(dirname($script_name), '/');
        
        // Remove base directory from request URI to get relative path
        $relative_path = $request_uri;
        if (!empty($base_dir) && strpos($request_uri, $base_dir) === 0) {
            $relative_path = substr($request_uri, strlen($base_dir));
        }
        
        $relative_path = trim($relative_path, '/');
        $result['relative_path'] = $relative_path;
        
        // Parse the relative path
        $uri_parts = explode('/', $relative_path);
        
        if (!empty($uri_parts[0])) {
            $result['first_segment'] = $uri_parts[0];
            
            // Check if first segment is a language code
            if (array_key_exists($uri_parts[0], $this->supported_langs)) {
                $result['lang_code'] = $uri_parts[0];
            }
        }
        
        return $result;
    }

    /**
     * Get language code from URL (legacy method for compatibility)
     */
    private function get_url_lang_code()
    {
        $url_info = $this->parse_url();
        return $url_info['lang_code'];
    }

    /**
     * Check if the URL is a frontend URL that requires language prefix
     */
    private function is_frontend_url($first_segment, $relative_path)
    {
        // Empty path = homepage = frontend
        if (empty($first_segment) || empty($relative_path)) {
            return true;
        }
        
        // If first segment is a backend prefix, it's not frontend
        if (in_array($first_segment, $this->backend_prefixes)) {
            return false;
        }
        
        // If first segment is a Home AJAX method directly (shouldn't be redirected)
        if (in_array($first_segment, $this->home_ajax_methods)) {
            return false;
        }
        
        // Check if this is a Home controller AJAX method (not a frontend page)
        if ($first_segment === 'home') {
            $path_parts = explode('/', $relative_path);
            if (isset($path_parts[1]) && in_array($path_parts[1], $this->home_ajax_methods)) {
                return false; // It's an AJAX endpoint, not a frontend page
            }
            // Otherwise, it's a frontend page via Home controller
            return true;
        }
        
        // If first segment is a known frontend route, it needs prefix
        if (in_array($first_segment, $this->frontend_routes)) {
            return true;
        }
        
        // Default: not a frontend URL (could be a custom controller)
        return false;
    }

    /**
     * Redirect to the language-prefixed version of the URL
     */
    private function redirect_to_language_url($CI, $relative_path)
    {
        // Determine the user's preferred language
        $lang_code = $this->get_user_preferred_lang_code($CI);
        
        // Build the new URL with language prefix
        $base_url = rtrim(config_item('base_url'), '/');
        
        // Handle empty path (homepage)
        if (empty($relative_path)) {
            $new_url = $base_url . '/' . $lang_code;
        } else {
            // Remove 'home/' prefix if present (old-style URLs)
            if (strpos($relative_path, 'home/') === 0) {
                $relative_path = substr($relative_path, 5);
            } elseif ($relative_path === 'home') {
                $relative_path = '';
            }
            
            if (empty($relative_path)) {
                $new_url = $base_url . '/' . $lang_code;
            } else {
                $new_url = $base_url . '/' . $lang_code . '/' . $relative_path;
            }
        }
        
        // Preserve query string if present
        if (!empty($_SERVER['QUERY_STRING'])) {
            $new_url .= '?' . $_SERVER['QUERY_STRING'];
        }
        
        // Perform 301 redirect (permanent)
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: ' . $new_url);
        exit;
    }

    /**
     * Get user's preferred language code
     */
    private function get_user_preferred_lang_code($CI)
    {
        // Check session first
        $session_lang_code = $CI->session->userdata('lang_code');
        if ($session_lang_code && array_key_exists($session_lang_code, $this->supported_langs)) {
            return $session_lang_code;
        }
        
        // Check session language name
        $session_lang = $CI->session->userdata('language');
        if ($session_lang) {
            return $this->get_code_from_name($session_lang);
        }
        
        // Check logged-in user
        $user_id = $CI->session->userdata('user_id');
        if ($user_id) {
            $user = $CI->db->get_where('users', array('id' => $user_id))->row_array();
            if (isset($user['language']) && !empty($user['language'])) {
                return $this->get_code_from_name($user['language']);
            }
        }
        
        // Fall back to default
        return $this->default_lang_code;
    }

    /**
     * Get default language from settings
     */
    private function get_default_language($CI)
    {
        $result = $CI->db->get_where('settings', array('id' => 1))->row_array();
        return isset($result['language']) ? $result['language'] : 'english';
    }

    /**
     * Get language code from language name
     */
    private function get_code_from_name($lang_name)
    {
        $lang_name = strtolower($lang_name);
        foreach ($this->supported_langs as $code => $name) {
            if ($name === $lang_name) {
                return $code;
            }
        }
        return $this->default_lang_code;
    }

    /**
     * Get path after removing language prefix
     */
    private function get_path_after_lang($relative_path)
    {
        $parts = explode('/', $relative_path);
        if (!empty($parts[0]) && array_key_exists($parts[0], $this->supported_langs)) {
            array_shift($parts);
            return implode('/', $parts);
        }
        return $relative_path;
    }

    /**
     * Redirect to correct AJAX URL (without language prefix)
     */
    private function redirect_to_ajax_url($method, $path_after_lang)
    {
        $base_url = rtrim(config_item('base_url'), '/');
        $new_url = $base_url . '/home/' . $path_after_lang;
        
        // Preserve query string
        if (!empty($_SERVER['QUERY_STRING'])) {
            $new_url .= '?' . $_SERVER['QUERY_STRING'];
        }
        
        header('HTTP/1.1 302 Found');
        header('Location: ' . $new_url);
        exit;
    }

    /**
     * Redirect to homepage with appropriate language prefix
     * Used for blocked old-style routes like home/teachers, home/events, etc.
     */
    private function redirect_to_home($CI)
    {
        $lang_code = $this->get_user_preferred_lang_code($CI);
        $base_url = rtrim(config_item('base_url'), '/');
        $new_url = $base_url . '/' . $lang_code;
        
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: ' . $new_url);
        exit;
    }

    /**
     * Get supported languages
     */
    public function get_supported_languages()
    {
        return $this->supported_langs;
    }
}
