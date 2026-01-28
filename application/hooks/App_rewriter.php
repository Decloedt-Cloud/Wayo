<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class App_rewriter {
    
    public function handle_outgoing_output() {
        $CI =& get_instance();
        
        if (empty($output)) {
             $output = $CI->output->get_output();
        }

        // Roles to replace with 'app'
        // Put superadmin before admin to avoid partial matching issues
        $roles = array('superadmin', 'admin', 'teacher', 'student');
        
        // Get base URL
        $base_url = $CI->config->item('base_url');
        $base_url = rtrim($base_url, '/');
        
        // Handle role/student -> app/member, role/teacher -> app/mentor, role/exam -> app/certifications, role/event_calendar -> app/announcements
        // and role/school_settings -> app/community_settings
        $manager_roles = array('superadmin', 'admin', 'teacher', 'student', 'parent');
        $segment_map = array(
            'student' => 'member',
            'teacher' => 'mentor',
            'exam'    => 'certifications',
            'event_calendar' => 'announcements',
            'school_settings' => 'community_settings',
            'school' => 'community_list'
        );

        foreach ($manager_roles as $role) {
            foreach ($segment_map as $original_segment => $mapped_segment) {
                $target_segment = $role . '/' . $original_segment;
                $replacement_segment = 'app/' . $mapped_segment;
    
                // Replace "base_url/role/original/" with "base_url/app/mapped/"
                $search = $base_url . '/' . $target_segment . '/';
                $replace = $base_url . '/' . $replacement_segment . '/';
                $output = str_replace($search, $replace, $output);
                
                // Replace "base_url/index.php/role/original/" with "base_url/index.php/app/mapped/"
                $search_index = $base_url . '/index.php/' . $target_segment . '/';
                $replace_index = $base_url . '/index.php/' . $replacement_segment . '/';
                $output = str_replace($search_index, $replace_index, $output);
                
                // Handle exact match ending with quote
                $search_end = $base_url . '/' . $target_segment . '"';
                $replace_end = $base_url . '/' . $replacement_segment . '"';
                $output = str_replace($search_end, $replace_end, $output);
    
                $search_end_sq = $base_url . '/' . $target_segment . "'";
                $replace_end_sq = $base_url . '/' . $replacement_segment . '\'';
                $output = str_replace($search_end_sq, $replace_end_sq, $output);
            }
        }

        foreach ($roles as $role) {
            // Replace "base_url/role/" with "base_url/app/"
            $search = $base_url . '/' . $role . '/';
            $replace = $base_url . '/app/';
            $output = str_replace($search, $replace, $output);
            
            // Replace "base_url/index.php/role/" with "base_url/index.php/app/"
            $search_index = $base_url . '/index.php/' . $role . '/';
            $replace_index = $base_url . '/index.php/app/';
            $output = str_replace($search_index, $replace_index, $output);
            
            // Handle cases where the URL ends with the role (e.g. href=".../admin")
            $search_end = $base_url . '/' . $role . '"';
            $replace_end = $base_url . '/app"';
            $output = str_replace($search_end, $replace_end, $output);

            $search_end_sq = $base_url . '/' . $role . "'";
            $replace_end_sq = $base_url . '/app\'';
            $output = str_replace($search_end_sq, $replace_end_sq, $output);
        }
        
        // Handle addons replacements
        $addons_map = array(
            'addons/courses' => 'app/courses',
            'addons/lessons' => 'app/lessons'
        );

        foreach ($addons_map as $addon_search => $addon_replace) {
            // Replace "base_url/addons/xxx/" with "base_url/app/xxx/"
            $search = $base_url . '/' . $addon_search . '/';
            $replace = $base_url . '/' . $addon_replace . '/';
            $output = str_replace($search, $replace, $output);
            
            // Replace "base_url/index.php/addons/xxx/" with "base_url/index.php/app/xxx/"
            $search_index = $base_url . '/index.php/' . $addon_search . '/';
            $replace_index = $base_url . '/index.php/' . $addon_replace . '/';
            $output = str_replace($search_index, $replace_index, $output);
            
            // Handle exact match ending with quote
            $search_end = $base_url . '/' . $addon_search . '"';
            $replace_end = $base_url . '/' . $addon_replace . '"';
            $output = str_replace($search_end, $replace_end, $output);
    
            $search_end_sq = $base_url . '/' . $addon_search . "'";
            $replace_end_sq = $base_url . '/' . $addon_replace . '\'';
            $output = str_replace($search_end_sq, $replace_end_sq, $output);
        }

        // Handle home/xxx -> xxx replacements
        $home_segments = array(
            'communities', 'about', 
            'privacy_policy', 'community_details'
        );

        foreach ($home_segments as $segment) {
            $search_segment = 'home/' . $segment;
            $replace_segment = $segment;

            // Replace "base_url/home/segment/" with "base_url/segment/"
            $search = $base_url . '/' . $search_segment . '/';
            $replace = $base_url . '/' . $replace_segment . '/';
            $output = str_replace($search, $replace, $output);
            
            // Replace "base_url/index.php/home/segment/" with "base_url/index.php/segment/"
            $search_index = $base_url . '/index.php/' . $search_segment . '/';
            $replace_index = $base_url . '/index.php/' . $replace_segment . '/';
            $output = str_replace($search_index, $replace_index, $output);
            
            // Handle exact match ending with quote
            $search_end = $base_url . '/' . $search_segment . '"';
            $replace_end = $base_url . '/' . $replace_segment . '"';
            $output = str_replace($search_end, $replace_end, $output);

            $search_end_sq = $base_url . '/' . $search_segment . "'";
            $replace_end_sq = $base_url . '/' . $replace_segment . '\'';
            $output = str_replace($search_end_sq, $replace_end_sq, $output);
        }

        // Handle admission/online_admission -> join/community
        // Handle admission/online_admission_student -> join/member
        // Handle home/tutorial -> getting_started
        // Handle home/faq -> help-center
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

        foreach ($special_map as $search_segment => $replace_segment) {
            // Replace "base_url/search_segment/" with "base_url/replace_segment/"
            $search = $base_url . '/' . $search_segment . '/';
            $replace = $base_url . '/' . $replace_segment . '/';
            $output = str_replace($search, $replace, $output);
            
            // Replace "base_url/index.php/search_segment/" with "base_url/index.php/replace_segment/"
            $search_index = $base_url . '/index.php/' . $search_segment . '/';
            $replace_index = $base_url . '/index.php/' . $replace_segment . '/';
            $output = str_replace($search_index, $replace_index, $output);
            
            // Handle exact match ending with quote
            $search_end = $base_url . '/' . $search_segment . '"';
            $replace_end = $base_url . '/' . $replace_segment . '"';
            $output = str_replace($search_end, $replace_end, $output);

            $search_end_sq = $base_url . '/' . $search_segment . "'";
            $replace_end_sq = $base_url . '/' . $replace_segment . '\'';
            $output = str_replace($search_end_sq, $replace_end_sq, $output);
        }

        $CI->output->set_output($output);
        $CI->output->_display();
    }
}
