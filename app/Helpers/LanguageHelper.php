<?php

use App\Services\LanguageService;
use Config\Database;

if (!function_exists('get_phrase')) {
    function get_phrase(string $phrase = ''): string
    {
        $db = Database::connect();
        
        if ($db) {
            $active_language = get_settings('language');
            $query = get_where('language', ['name' => $active_language, 'phrase' => $phrase]);
            
            if ($query->num_rows() == 0) {
                $translated = str_replace('_', ' ', $phrase);
                foreach (get_all_language() as $language) {
                    $db->insert('language', [
                        'name' => $language['name'],
                        'phrase' => $phrase,
                        'translated' => $translated
                    ]);
                }
                return ucfirst($translated);
            }
            
            return ucfirst($query->getRow('translated'));
        }
        
        return ucfirst(str_replace('_', ' ', $phrase));
    }
}

if (!function_exists('site_phrase')) {
    function site_phrase(string $phrase = ''): string
    {
        return get_phrase($phrase);
    }
}

if (!function_exists('get_all_language')) {
    function get_all_language(): array
    {
        if (class_exists('\\CodeIgniter\\CodeIgniter')) {
            $service = service('languageService');
            return $service->getAllLanguages();
        }
        
        $db = Database::connect();
        return $db->table('language')->distinct()->select('name')->get()->getResultArray();
    }
}

if (!function_exists('saveDefaultJSONFile')) {
    function saveDefaultJSONFile(string $language_code)
    {
        if (class_exists('\\CodeIgniter\\CodeIgniter')) {
            $service = service('languageService');
            return $service->saveDefaultJsonFile($language_code);
        }
        
        $db = Database::connect();
        $new_language = strtolower(htmlspecialchars($language_code));
        $already_exist = get_where('language', ['name' => $new_language])->num_rows();
        
        if ($already_exist <= 0) {
            $db->where('name', get_settings('language'));
            $all_phrases = $db->get('language');

            if ($all_phrases->num_rows() <= 0) {
                $data = [
                    'name' => 'english',
                    'phrase' => 'english',
                    'translated' => 'english'
                ];
                $db->insert('language', $data);
            }

            foreach ($all_phrases->result_array() as $phrase) {
                $data = [
                    'name' => $new_language,
                    'phrase' => $phrase['phrase'],
                    'translated' => str_replace('_', ' ', $phrase['phrase'])
                ];
                $db->insert('language', $data);
            }
            return 'done';
        }
        
        return 'exist';
    }
}

if (!function_exists('saveJSONFile')) {
    function saveJSONFile(string $language_code, string $updating_key, string $updating_value): void
    {
        if (class_exists('\\CodeIgniter\\CodeIgniter')) {
            $service = service('languageService');
            $service->saveJsonFile($language_code, $updating_key, $updating_value);
            return;
        }
        
        $db = Database::connect();
        $language = strtolower($language_code);
        $phrase = strtolower($updating_key);
        $data['translated'] = htmlspecialchars($updating_value);

        $db->where('name', $language);
        $db->where('phrase', $phrase);
        $db->update('language', $data);
    }
}

if (!function_exists('create_language_table')) {
    function create_language_table(): void
    {
        if (class_exists('\\CodeIgniter\\CodeIgniter')) {
            return;
        }

        $dbforge = \Config\Database::forge();

        $language_table = [
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 50
            ],
            'phrase' => [
                'type' => 'TEXT'
            ],
            'translated' => [
                'type' => 'TEXT'
            ]
        ];

        $dbforge->add_field($language_table);
        $dbforge->add_key('id', true);
        $dbforge->create_table('language', true);
    }
}
