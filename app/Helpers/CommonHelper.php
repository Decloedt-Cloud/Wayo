<?php

use App\Services\CommonService;

if (!function_exists('school_id')) {
    function school_id(): int
    {
        $session = \Config\Services::session();
        if ($session->get('user_type') == 'superadmin') {
            if (function_exists('get_settings')) {
                return get_settings('school_id');
            }
            return 0;
        }
        
        $sessionSchoolId = $session->get('school_id');
        return $sessionSchoolId > 0 ? $sessionSchoolId : 0;
    }
}

if (!function_exists('user_id')) {
    function user_id(): int
    {
        $session = \Config\Services::session();
        $user_id = $session->get('user_id');
        $db = \Config\Database::connect();
        $user_details = $db->where('id', $user_id)->get('users')->getRowArray();
        return $user_details ? $user_details['id'] : 0;
    }
}

if (!function_exists('get_settings')) {
    function get_settings($type = '')
    {
        $db = \Config\Database::connect();
        $result = get_where('settings', ['id' => 1])->getRowArray();
        return $result[$type] ?? null;
    }
}

if (!function_exists('get_user_language')) {
    function get_user_language(): string
    {
        $session = \Config\Services::session();
        $session_lang = $session->get('language');
        if ($session_lang) {
            return $session_lang;
        }

        $user_id = $session->get('user_id');
        if ($user_id) {
            $db = \Config\Database::connect();
            $user_data = get_where('users', ['id' => $user_id])->getRowArray();
            $language = !empty($user_data['language']) ? $user_data['language'] : get_settings('language');
            $session->set('language', $language);
            return $language;
        }

        return get_settings('language');
    }
}

if (!function_exists('get_supported_lang_codes')) {
    function get_supported_lang_codes(): array
    {
        if (class_exists('\\CodeIgniter\\CodeIgniter')) {
            $service = service('commonService');
            return $service->getSupportedLangCodes();
        }
        
        return [
            'fr' => 'french',
            'en' => 'english',
            'ar' => 'arabic',
            'es' => 'spanish',
            'nl' => 'dutch'
        ];
    }
}
