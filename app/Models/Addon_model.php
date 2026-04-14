<?php

namespace App\Models;

use CodeIgniter\Model;

class Addon_model extends Model {
    protected $table            = 'addons';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [];
    protected $useTimestamps    = false;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField    = 'deleted_at';
    protected $dateFormat       = 'datetime';

    protected $DBGroup = 'default';
    protected $request;

	public function __construct()
	{
		parent::__construct();
        $this->request = \Config\Services::request();
	}

    private function uploadedFile(string $field)
    {
        return $this->request->getFile($field);
    }

	public function install_addon() {

		// CHECK IF THE ADDON FOLDER INSIDE CONTROLLERS EXISTS
		if (!is_dir('app/Controllers/addons')){
			mkdir("app/Controllers/addons", 0777, true);
		}

		// CHECK IF THE ADDON FOLDER INSIDE MODELS EXISTS
		if (!is_dir('app/Models/addons')){
			mkdir("app/Models/addons", 0777, true);
		}

		$addonZip = $this->uploadedFile('addon_zip');
		$zipped_file_name = ($addonZip && $addonZip->getError() === UPLOAD_ERR_OK) ? $addonZip->getClientName() : '';

		if (!empty($zipped_file_name)) {
			// Create update directory.
			$dir = 'uploads/addons';
			if (!is_dir($dir))
				mkdir($dir, 0777, true);

			$path = "uploads/addons/".$zipped_file_name;
			$addonZip->move('uploads/addons', $zipped_file_name, true);
			//Unzip uploaded update file and remove zip file.
			$zip = new ZipArchive;
			$res = $zip->open($path);
			if ($res === TRUE) {
				$zip->extractTo('uploads/addons');
				$zip->close();
				unlink($path);
			}

			$unzipped_file_name = substr($zipped_file_name, 0, -4);
			$config_str = file_get_contents('uploads/addons/' . $unzipped_file_name . '/config.json');
			$config = json_decode($config_str, true);

			// CREATE DIRECTORIES
			if (!empty($config['directories'])) {
				foreach ($config['directories'] as $directory) {
					if (!is_dir($directory['name'])){
						mkdir($directory['name'], 0777, true);
					}
				}
			}

			// CREATE OR REPLACE NEW FILES
			if (!empty($config['files'])) {
				foreach ($config['files'] as $file){
					copy($file['root_directory'], $file['update_directory']);
				}
			}

			// EXECUTE THE SQL FILE
			if (!empty($config['sql_file'])) {
				require './uploads/addons/'.$unzipped_file_name.'/sql/'.$config['sql_file'];
			}

			// INSERT OR UPDATE AN ENTRY ON DATABASE

			$data['name'] = $config['name'];
			$data['unique_identifier'] = $config['unique_identifier'];
			$data['version'] = $config['version'];
			$data['status'] = 1;

			//CHECK IF THE ADDON IS ALREADY INSTALLED OR NOT
			$addon_details = \db()->table('addons')->where('unique_identifier', $data['unique_identifier'])->get();

			if ($addon_details->numRows() > 0) {
				$data['updated_at'] = strtotime(date('d-m-y'));
				\db()->table('addons')->where('unique_identifier', $data['unique_identifier'])->update($data);

			}else{
				$data['created_at'] = strtotime(date('d-m-y'));
				\db()->table('addons')->insert($data);
			}


			return array(
				'status' => true,
				'notification' => get_phrase('addon_installed_successfully')
			);
		}else{
			return array(
				'status' => false,
				'notification' => get_phrase('no_addon_found')
			);
		}

		//return json_encode($response);
	}

	//DEACTIVATE AN ADDON
	public function deactivate_addon($addon_id = "") {
		$checker = array(
			'id' => $addon_id
		);
		$addon_details = \db()->table('addons')->where($checker)->get()->getResult()->getRowArray();
		if ($addon_details['status']) {
			$addon_updater = array(
				'status' => 0
			);
			\db()->table('addons')->where($checker)->update($addon_updater);

			$this->hide_addon_from_menu($addon_details['unique_identifier']);

			$response = array(
				'status' => true,
				'notification' => get_phrase('addon_is_deactivated_successfully')
			);
		}else{
			$response = array(
				'status' => true,
				'notification' => get_phrase('this_addon_is_already_deactivated')
			);
		}
		return json_encode($response);
	}

	// ACTIVATE AN ADDON
	public function activate_addon($addon_id = "") {
		$checker = array(
			'id' => $addon_id
		);
		$addon_details = \db()->table('addons')->where($checker)->get()->getResult()->getRowArray();
		if (!$addon_details['status']) {
			$addon_updater = array(
				'status' => 1
			);
			\db()->table('addons')->where($checker)->update($addon_updater);

			$this->show_addon_on_menu($addon_details['unique_identifier']);

			$response = array(
				'status' => true,
				'notification' => get_phrase('addon_is_activated_successfully')
			);
		}else{
			$response = array(
				'status' => true,
				'notification' => get_phrase('this_addon_is_already_active')
			);
		}
		return json_encode($response);
	}

	public function hide_addon_from_menu($unique_identifier = "") {
		$menu_updater = array(
			'status' => 0
		);
		$checker = array(
			'unique_identifier' => $unique_identifier
		);
		\db()->table('menus')->where($checker)->update($menu_updater);
	}

	public function show_addon_on_menu($unique_identifier = "") {
		$menu_updater = array(
			'status' => 1
		);
		$checker = array(
			'unique_identifier' => $unique_identifier
		);
		\db()->table('menus')->where($checker)->update($menu_updater);
	}


	// REMOVE ADDON
	public function remove_addon($addon_id = "") {
		$checker = array(
			'id' => $addon_id
		);
		$addon_details = \db()->table('addons')->where($checker)->get();
		if ($addon_details->numRows() > 0) {
			$addon_details = $addon_details->getRowArray();

			$this->hide_addon_from_menu($addon_details['unique_identifier']);
			\db()->table('addons')->where($checker)->delete();

			$response = array(
				'status' => true,
				'notification' => get_phrase('addon_deleted_successfully')
			);
		}else{
			$response = array(
				'status' => true,
				'notification' => get_phrase('addon_is_already_deleted')
			);
		}
		return json_encode($response);
	}


	public function remove_files_and_folders($dir) {
		if (is_dir($dir)) {
			$objects = scandir($dir);
			foreach ($objects as $object) {
				if ($object != "." && $object != "..") {
					if (filetype($dir."/".$object) == "dir")
					$this->remove_files_and_folders($dir."/".$object);
					else unlink   ($dir."/".$object);
				}
			}
			reset($objects);
			rmdir($dir);
		}
	}
}
