<?php

namespace App\Services;

use CodeIgniter\Database\BaseConnection;
use Config\Database;

class CommonService
{
    private $db;
    private $session;
    private $config;

    public function __construct()
    {
        $this->db = Database::connect();
        $this->session = service('session');
        $this->config = config('App');
    }

    public function getSchoolId(): int
    {
        $userType = session()->get('user_type');
        
        if ($userType === 'superadmin') {
            return (int) $this->getSetting('school_id');
        }
        
        $schoolId = session()->get('school_id');
        
        return ($schoolId > 0) ? (int) $schoolId : 0;
    }

    public function getUserId(): int
    {
        $userId = session()->get('user_id');
        
        if (!$userId) {
            return 0;
        }

        $user = db()->table('users')
            ->where('id', $userId)
            ->get()
            ->getRow();

        return $user ? (int) $user->id : 0;
    }

    public function getSetting(string $type)
    {
        $result = db()->table('settings')
            ->where('id', 1)
            ->get()
            ->getRow();

        return $result->$type ?? null;
    }

    public function getUserLanguage(): string
    {
        $sessionLang = session()->get('language');
        if ($sessionLang) {
            return $sessionLang;
        }

        $userId = session()->get('user_id');
        if ($userId) {
            $user = db()->table('users')
                ->where('id', $userId)
                ->get()
                ->getRow();

            $language = !empty($user->language) ? $user->language : $this->getSetting('language');
            session()->set('language', $language);
            return $language;
        }

        return $this->getSetting('language') ?? 'english';
    }

    public function getSupportedLangCodes(): array
    {
        return [
            'fr' => 'french',
            'en' => 'english',
            'ar' => 'arabic',
            'es' => 'spanish',
            'nl' => 'dutch'
        ];
    }
}
