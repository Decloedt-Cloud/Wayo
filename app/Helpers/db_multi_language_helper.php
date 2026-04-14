<?php
/**
* CodeIgniter
*
* An open source application development framework for PHP 5.1.6 or newer
*
* @package		CodeIgniter
* @author		ExpressionEngine Dev Team
* @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
* @license		http://codeigniter.com/user_guide/license.html
* @link		http://codeigniter.com
* @since		Version 1.0
* @filesource
*/


// This function helps us to get the translated phrase from the file. If it does not exist this function will save the phrase and by default it will have the same form as given
if ( ! function_exists('get_phrase'))
{
    function get_phrase($phrase = '') {
        $db = \Config\Database::connect();
        $active_language = get_settings('language');
        $query = get_where('language', array('name' => $active_language, 'phrase' => $phrase));
        if($query->numRows() == null){
            $translated = str_replace('_', ' ', $phrase);
            foreach(get_all_language() as $language){
                $db->insert('language', array('name' => $language['name'], 'phrase' => $phrase, 'translated' => $translated));
            }
            return ucfirst($translated);
        }
        return ucfirst($query->getRow('translated'));
    }
}

if ( ! function_exists('site_phrase'))
{
    function site_phrase($phrase = '') {
        $db = \Config\Database::connect();
        $active_language = get_settings('language');
        $query = get_where('language', array('name' => $active_language, 'phrase' => $phrase));
        if($query->numRows() == null){
            $translated = str_replace('_', ' ', $phrase);
            foreach(get_all_language() as $language){
                $db->insert('language', array('name' => $language['name'], 'phrase' => $phrase, 'translated' => $translated));
            }
            return ucfirst($translated);
        }
        return ucfirst($query->getRow('translated'));
    }
}

if ( ! function_exists('get_all_language'))
{
    function get_all_language() {
        $db = \Config\Database::connect();
        return $db->table('language')->distinct()->select('name')->get()->getResultArray();
    }
}

// This function helps us to create a new json file for new language
if ( ! function_exists('saveDefaultJSONFile'))
{
	function saveDefaultJSONFile($language_code){
		$db = \Config\Database::connect();
		$new_language = strtolower(htmlspecialchars($language_code));
		$already_exist = get_where('language', array('name' => $new_language))->numRows();
		if($already_exist <= 0){
			$db->where('name', get_settings('language'));
			$all_phrases = $db->get('language');

			if($all_phrases->numRows() <= 0){
				$data['name'] = 'english';
				$data['phrase'] = 'english';
				$data['translated'] = 'english';
				$db->insert('language', $data);
			}

			foreach($all_phrases->getResultArray() as $phrase){
				$data['name'] = $new_language;
				$data['phrase'] = $phrase['phrase'];
				$data['translated'] = str_replace('_', ' ', $phrase['phrase']);
				$db->insert('language', $data);
			}
			return 'done';
		}else{
			return 'exist';
		}
	}
}

if ( ! function_exists('saveJSONFile'))
{
	function saveJSONFile($language_code, $updating_key, $updating_value){
		$db = \Config\Database::connect();
		$language = strtolower($language_code);
		$phrase = strtolower($updating_key);
		$data['translated'] = htmlspecialchars($updating_value);

		$db->where('name', $language);
		$db->where('phrase', $phrase);
		$db->update('language', $data);
	}
}


if ( ! function_exists('create_language_table'))
{
	function create_language_table(){
		return;
	}
}