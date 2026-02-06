<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class App_Access {

    public function block_direct_access() {
        $CI =& get_instance();
        
        // Get the requested URI from the server to bypass internal routing
        $request_uri = $_SERVER['REQUEST_URI'];
        
        // Get base URL path to handle subdirectories
        $base_url = $CI->config->item('base_url');
        $base_path = parse_url($base_url, PHP_URL_PATH);
        
        // Normalize base path (ensure it defaults to / if empty and ends with /)
        if (empty($base_path)) {
            $base_path = '/';
        }
        if (substr($base_path, -1) !== '/') {
            $base_path .= '/';
        }
        
        // Clean request URI by removing the base path
        if ($base_path !== '/' && strpos($request_uri, $base_path) === 0) {
            $clean_uri = substr($request_uri, strlen($base_path));
        } else {
            // Fallback for root or mismatch
            $clean_uri = $request_uri;
        }
        
        // Remove index.php from the beginning of the URI if present (case-insensitive)
        // This handles explicit access like /index.php/home/about
        $clean_uri = preg_replace('/^\/?index\.php\/?/i', '/', $clean_uri);
        
        // Ensure clean_uri starts with / for consistent matching
        if (substr($clean_uri, 0, 1) !== '/') {
            $clean_uri = '/' . $clean_uri;
        }

        // Special case: Redirect app/student -> app/member, app/teacher -> app/mentor, app/exam -> app/certifications, app/event_calendar -> app/announcements
        // and app/school_settings -> app/community_settings
        // This handles cases where the user might manually access /app/student or /app/teacher
        // or if a link wasn't properly rewritten
        $special_maps = array(
            '/app/student' => '/app/member',
            '/app/teacher' => '/app/mentor',
            '/app/exam'    => '/app/certifications',
            '/app/event_calendar' => '/app/announcements',
            '/app/school_settings' => '/app/community_settings',
            '/app/school' => '/app/community_list'
        );

        foreach ($special_maps as $target => $replacement) {
            if (strpos($clean_uri, $target . '/') === 0 || 
                $clean_uri === $target || 
                strpos($clean_uri, $target . '?') === 0) {
                
                $new_uri = substr_replace($clean_uri, $replacement, 0, strlen($target));
                $redirect_url = rtrim($base_url, '/') . $new_uri;
                header("Location: " . $redirect_url, true, 301);
                exit;
            }
        }

        // Special case: Redirect role/student -> app/member, role/teacher -> app/mentor, role/exam -> app/certifications, role/event_calendar -> app/announcements
        // and role/school_settings -> app/community_settings
        // Must be checked BEFORE the generic role blocking loop
        $manager_roles = array('superadmin', 'admin', 'teacher');
        $role_suffix_map = array(
            '/student' => '/app/member',
            '/teacher' => '/app/mentor',
            '/exam'    => '/app/certifications',
            '/event_calendar' => '/app/announcements',
            '/school_settings' => '/app/community_settings',
            '/school' => '/app/community_list'
        );

        foreach ($manager_roles as $role) {
            foreach ($role_suffix_map as $suffix => $replacement) {
                // Check patterns:
                // 1. /role/suffix/ (start of path)
                // 2. /role/suffix (exact match)
                // 3. /role/suffix? (start of query string)
                $target = '/' . $role . $suffix;
                
                if (strpos($clean_uri, $target . '/') === 0 || 
                    $clean_uri === $target || 
                    strpos($clean_uri, $target . '?') === 0) {
                    
                    // Replace "/role/suffix" with "/app/replacement"
                    $new_uri = substr_replace($clean_uri, $replacement, 0, strlen($target));
                    
                    // Construct the full redirect URL
                    $redirect_url = rtrim($base_url, '/') . $new_uri;
                    
                    // Perform 301 Redirect
                    header("Location: " . $redirect_url, true, 301);
                    exit;
                }
            }
        }

        // Segments that should be blocked/redirected
        $blocked_segments = array('admin', 'teacher', 'student', 'superadmin', 'addons');
        
        foreach ($blocked_segments as $segment) {
            // Check patterns:
            // 1. /segment/ (start of path)
            // 2. /segment (exact match)
            // 3. /segment? (start of query string)
            
            if (strpos($clean_uri, '/' . $segment . '/') === 0 || 
                $clean_uri === '/' . $segment || 
                strpos($clean_uri, '/' . $segment . '?') === 0) {
                
                // Construct new URI by replacing the segment with 'app'
                // Replaces only the first occurrence at the start
                $new_uri = preg_replace('/^\/' . preg_quote($segment, '/') . '/', '/app', $clean_uri, 1);
                
                // Construct the full redirect URL
                $redirect_url = rtrim($base_url, '/') . $new_uri;
                
                // Perform 301 Redirect
                header("Location: " . $redirect_url, true, 301);
                exit;
            }
        }



        // Redirect admission/online_admission -> join/community
        // Redirect admission/online_admission_student -> join/member
        // Redirect home/tutorial -> getting_started
        // Redirect tutorial -> getting_started
        // Redirect home/faq -> help-center
        $special_map = array(
            'admission/online_admission' => 'join/community',
            'admission/online_admission_student' => 'join/member',
            'home/tutorial' => 'getting_started',
            'tutorial' => 'getting_started',
            'home/faq' => 'help-center',
            'faq' => 'help-center',
            'home/terms_conditions' => 'terms',
            'terms_conditions' => 'terms',
            'home/contact' => 'support',
            'contact' => 'support'
        );

        foreach ($special_map as $target_segment => $replacement_segment) {
            $target = '/' . $target_segment;
            $replacement = '/' . $replacement_segment;
            
            if (strpos($clean_uri, $target . '/') === 0 || 
                $clean_uri === $target || 
                strpos($clean_uri, $target . '?') === 0) {
                
                // Skip redirect for POST requests to preserve form data
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    return; // Let the request continue without redirect
                }
                
                $new_uri = substr_replace($clean_uri, $replacement, 0, strlen($target));
                $redirect_url = rtrim($base_url, '/') . $new_uri;
                header("Location: " . $redirect_url, true, 301);
                exit;
            }
        }
        
        // Redirect home/segment -> segment
        $home_segments = array(
            'communities', 'about', 
            'privacy_policy', 'community_details'
        );

        foreach ($home_segments as $segment) {
            $target = '/home/' . $segment;
            $replacement = '/' . $segment;
            
            if (strpos($clean_uri, $target . '/') === 0 || 
                $clean_uri === $target || 
                strpos($clean_uri, $target . '?') === 0) {
                
                $new_uri = substr_replace($clean_uri, $replacement, 0, strlen($target));
                $redirect_url = rtrim($base_url, '/') . $new_uri;
                header("Location: " . $redirect_url, true, 301);
                exit;
            }
        }
    }
}
