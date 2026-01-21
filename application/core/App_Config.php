<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class App_Config extends CI_Config {

    /**
     * Site URL
     *
     * Returns base_url . index_page [. uri_string]
     * Overridden to mask role segments with 'app'
     *
     * @param   string|string[] $uri      URI string or an array of segments
     * @param   string          $protocol Protocol
     * @return  string
     */
    public function site_url($uri = '', $protocol = NULL)
    {
        // Get the standard URL from parent
        $url = parent::site_url($uri, $protocol);

        // Roles to mask
        $roles = array('superadmin', 'admin', 'teacher', 'student');
        
        $base_url = $this->slash_item('base_url');
        $index_page = $this->item('index_page');

        // Prepare prefixes to check (with and without index.php)
        $check_prefixes = array($base_url);
        if (!empty($index_page)) {
            $check_prefixes[] = $base_url . $index_page . '/';
        }

        // Mask role/student -> app/member, role/teacher -> app/mentor, role/exam -> app/certifications, role/event_calendar -> app/announcements
        // and role/school_settings -> app/community_settings
        $manager_roles = array('superadmin', 'admin', 'teacher', 'student', 'parent');
        $segment_map = array(
            'student' => 'member',
            'teacher' => 'mentor',
            'exam'    => 'certifications',
            'event_calendar' => 'announcements',
            'school_settings' => 'community_settings'
        );

        foreach ($manager_roles as $role) {
            foreach ($segment_map as $original_segment => $mapped_segment) {
                foreach ($check_prefixes as $prefix) {
                    $target = $prefix . $role . '/' . $original_segment;
                    
                    // Check for .../role/original/
                    if (strpos($url, $target . '/') === 0) {
                        return substr_replace($url, $prefix . 'app/' . $mapped_segment . '/', 0, strlen($target) + 1);
                    }
                    // Check for .../role/original (exact)
                    if ($url === $target) {
                        return $prefix . 'app/' . $mapped_segment;
                    }
                    // Check for .../role/original?
                    if (strpos($url, $target . '?') === 0) {
                        return substr_replace($url, $prefix . 'app/' . $mapped_segment . '?', 0, strlen($target) + 1);
                    }
                }
            }
        }

        foreach ($roles as $role) {
            foreach ($check_prefixes as $prefix) {
                // Pattern 1: prefix + role + slash (e.g. .../admin/...)
                $search_slash = $prefix . $role . '/';
                if (strpos($url, $search_slash) === 0) {
                    // Replace start of string only
                    return substr_replace($url, $prefix . 'app/', 0, strlen($search_slash));
                }

                // Pattern 2: prefix + role (exact match, e.g. .../admin)
                $search_exact = $prefix . $role;
                if ($url === $search_exact) {
                    return $prefix . 'app';
                }
                
                // Pattern 3: prefix + role + ? (query string, e.g. .../admin?foo=bar)
                $search_query = $prefix . $role . '?';
                if (strpos($url, $search_query) === 0) {
                     return substr_replace($url, $prefix . 'app?', 0, strlen($search_query));
                }
            }
        }

        // Mask addons paths
        $addons_map = array(
            'addons/courses' => 'app/courses',
            'addons/lessons' => 'app/lessons'
        );

        foreach ($addons_map as $addon_path => $app_path) {
            foreach ($check_prefixes as $prefix) {
                $search_slash = $prefix . $addon_path . '/';
                if (strpos($url, $search_slash) === 0) {
                    return substr_replace($url, $prefix . $app_path . '/', 0, strlen($search_slash));
                }
                
                $search_exact = $prefix . $addon_path;
                if ($url === $search_exact) {
                    return $prefix . $app_path;
                }
                
                $search_query = $prefix . $addon_path . '?';
                if (strpos($url, $search_query) === 0) {
                    return substr_replace($url, $prefix . $app_path . '?', 0, strlen($search_query));
                }
            }
        }

        return $url;
    }
}
