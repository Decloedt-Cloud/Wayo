<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class App_rewriter {
    
    /**
     * Extensions de fichiers à versionner pour le cache busting
     */
    protected $version_extensions = array('css', 'js');
    
    /**
     * Patterns à ignorer pour le versioning (CDN, externes)
     */
    protected $ignore_patterns = array(
        'cdn.jsdelivr.net',
        'cdnjs.cloudflare.com',
        'fonts.googleapis.com',
        'unpkg.com',
        'code.jquery.com',
        'maxcdn.bootstrapcdn.com',
        'stackpath.bootstrapcdn.com',
        'ajax.googleapis.com',
        '//fonts.',
    );
    
    public function handle_outgoing_output() {
        $CI =& get_instance();
        
        if (empty($output)) {
             $output = $CI->output->get_output();
        }
        
        // ============================================================
        // CACHE BUSTING: Ajouter le versioning aux fichiers CSS et JS
        // ============================================================
        $output = $this->_add_asset_versioning($output, $CI);

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
            'addons/lessons' => 'app/lessons',
            'chat' => 'app/chat'
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
    
    /**
     * ============================================================
     * CACHE BUSTING - Versioning automatique des assets CSS/JS
     * ============================================================
     * Ajoute un paramètre ?v=timestamp à tous les fichiers CSS et JS locaux
     * pour forcer le rechargement quand les fichiers sont modifiés.
     */
    
    /**
     * Ajoute le versioning à tous les fichiers CSS et JS
     * 
     * @param string $html
     * @param object $CI
     * @return string
     */
    protected function _add_asset_versioning($html, $CI) {
        // Ne traiter que le HTML
        $content_type = $CI->output->get_content_type();
        if (strpos($content_type, 'html') === false && strpos($content_type, 'text') === false) {
            return $html;
        }
        
        // Versionner les CSS
        $html = $this->_version_css($html, $CI);
        
        // Versionner les JS
        $html = $this->_version_js($html, $CI);
        
        return $html;
    }
    
    /**
     * Ajoute le versioning aux fichiers CSS
     */
    protected function _version_css($html, $CI) {
        $pattern = '/<link([^>]*?)href=["\']([^"\']+\.css)(\?[^"\']*)?["\']([^>]*?)>/i';
        
        return preg_replace_callback($pattern, function($matches) use ($CI) {
            $before_href = $matches[1];
            $url = $matches[2];
            $existing_query = isset($matches[3]) ? $matches[3] : '';
            $after_href = $matches[4];
            
            // Ignorer les URLs externes
            if ($this->_should_ignore_url($url, $CI)) {
                return $matches[0];
            }
            
            // Déjà versionné ?
            if (strpos($existing_query, 'v=') !== false) {
                return $matches[0];
            }
            
            $version = $this->_get_file_version($url, $CI);
            $separator = empty($existing_query) ? '?' : '&';
            $new_url = $url . $existing_query . $separator . 'v=' . $version;
            
            return '<link' . $before_href . 'href="' . $new_url . '"' . $after_href . '>';
        }, $html);
    }
    
    /**
     * Ajoute le versioning aux fichiers JS
     */
    protected function _version_js($html, $CI) {
        $pattern = '/<script([^>]*?)src=["\']([^"\']+\.js)(\?[^"\']*)?["\']([^>]*?)>/i';
        
        return preg_replace_callback($pattern, function($matches) use ($CI) {
            $before_src = $matches[1];
            $url = $matches[2];
            $existing_query = isset($matches[3]) ? $matches[3] : '';
            $after_src = $matches[4];
            
            // Ignorer les URLs externes
            if ($this->_should_ignore_url($url, $CI)) {
                return $matches[0];
            }
            
            // Déjà versionné ?
            if (strpos($existing_query, 'v=') !== false) {
                return $matches[0];
            }
            
            $version = $this->_get_file_version($url, $CI);
            $separator = empty($existing_query) ? '?' : '&';
            $new_url = $url . $existing_query . $separator . 'v=' . $version;
            
            return '<script' . $before_src . 'src="' . $new_url . '"' . $after_src . '>';
        }, $html);
    }
    
    /**
     * Vérifie si une URL doit être ignorée (CDN, externe)
     */
    protected function _should_ignore_url($url, $CI) {
        // URLs externes (http://, https://, //)
        if (preg_match('#^(https?:)?//#i', $url)) {
            $base_url = $CI->config->item('base_url');
            // Si c'est notre domaine, ne pas ignorer
            if (strpos($url, $base_url) === 0) {
                return false;
            }
            return true;
        }
        
        // Patterns à ignorer
        foreach ($this->ignore_patterns as $pattern) {
            if (stripos($url, $pattern) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Obtient la version (timestamp) d'un fichier
     */
    protected function _get_file_version($url, $CI) {
        $base_url = $CI->config->item('base_url');
        $path = $url;
        
        // Supprimer le base_url
        if (strpos($url, $base_url) === 0) {
            $path = substr($url, strlen($base_url));
        }
        
        $path = ltrim($path, '/');
        $file_path = FCPATH . $path;
        
        // Utiliser le timestamp de modification si le fichier existe
        if (file_exists($file_path)) {
            return filemtime($file_path);
        }
        
        // Sinon, date du jour
        return date('Ymd');
    }
}
