<?php

namespace App\Services;

use CodeIgniter\Database\BaseConnection;
use Config\Database;

class LanguageService
{
    private $db;
    private $session;

    public function __construct()
    {
        $this->db = Database::connect();
        $this->session = service('session');
    }

    public function getPhrase(string $phrase): string
    {
        $activeLanguage = $this->getActiveLanguage();
        
        $result = db()->table('language')
            ->where('name', $activeLanguage)
            ->where('phrase', $phrase)
            ->get()
            ->getRow();

        if (!$result) {
            $translated = str_replace('_', ' ', $phrase);
            $this->createPhraseInAllLanguages($phrase, $translated);
            return ucfirst($translated);
        }

        return ucfirst($result->translated);
    }

    public function getSitePhrase(string $phrase): string
    {
        return $this->getPhrase($phrase);
    }

    public function getAllLanguages(): array
    {
        return db()->table('language')
            ->distinct()
            ->select('name')
            ->get()
            ->getResultArray();
    }

    public function saveDefaultJsonFile(string $languageCode): string
    {
        $newLanguage = strtolower(htmlspecialchars($languageCode));
        
        $exists = db()->table('language')
            ->where('name', $newLanguage)
            ->countAllResults();

        if ($exists > 0) {
            return 'exist';
        }

        $activeLanguage = $this->getActiveLanguage();
        
        $phrases = db()->table('language')
            ->where('name', $activeLanguage)
            ->get()
            ->getResultArray();

        if (empty($phrases)) {
            db()->table('language')->insert([
                'name' => 'english',
                'phrase' => 'english',
                'translated' => 'english'
            ]);
            $phrases = db()->table('language')
                ->where('name', 'english')
                ->get()
                ->getResultArray();
        }

        foreach ($phrases as $phrase) {
            db()->table('language')->insert([
                'name' => $newLanguage,
                'phrase' => $phrase['phrase'],
                'translated' => str_replace('_', ' ', $phrase['phrase'])
            ]);
        }

        return 'done';
    }

    public function saveJsonFile(string $languageCode, string $updatingKey, string $updatingValue): void
    {
        $language = strtolower($languageCode);
        $phrase = strtolower($updatingKey);
        
        db()->table('language')
            ->where('name', $language)
            ->where('phrase', $phrase)
            ->update(['translated' => htmlspecialchars($updatingValue)]);
    }

    private function getActiveLanguage(): string
    {
        $sessionLang = session()->get('language');
        if ($sessionLang) {
            return $sessionLang;
        }

        $result = db()->table('settings')
            ->where('id', 1)
            ->get()
            ->getRow();

        return $result->language ?? 'english';
    }

    private function createPhraseInAllLanguages(string $phrase, string $translated): void
    {
        $languages = $this->getAllLanguages();
        
        foreach ($languages as $language) {
            db()->table('language')->insert([
                'name' => $language['name'],
                'phrase' => $phrase,
                'translated' => $translated
            ]);
        }
    }
}
