<?php

namespace App\Models;

use CodeIgniter\Model;

class User_model extends Model {
    protected $table            = 'users';
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
    protected $school_id;
    protected $active_session;
    protected $email_model;
    
    public function __construct()
    {
        parent::__construct();
        $this->request = \Config\Services::request();
        $this->email_model = model('Email_model');
        
        if (function_exists('school_id')) {
            $this->school_id = school_id();
        }
        if (function_exists('active_session')) {
            $this->active_session = active_session();
        }
    }

	// GET SUPERADMIN DETAILS
	public function get_superadmin()
	{
		return \db()->table('users')->where('role', 'superadmin')->get()->getRowArray();
	}
	// GET USER DETAILS
	public function get_user_details($user_id = '', $column_name = '')
	{
		if (empty($user_id)) {
			$user_id = session()->get('user_id');
		}
		
		if (empty($user_id)) {
			return null;
		}
		
		if ($column_name != '') {
			$result = \db()->table('users')->where('id', $user_id)->get()->getRow($column_name);
			return $result;
		} else {
			return \db()->table('users')->where('id', $user_id)->get()->getRowArray();
		}
	}

	// ADMIN CRUD SECTION STARTS
	public function create_admin()
	{
		$data['school_id'] = html_escape($this->request->getPost('school_id'));
		$data['name'] = html_entity_decode(html_escape($this->request->getPost('name')));
		$data['email'] = html_escape($this->request->getPost('email'));
		$data['password'] = password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT);
		$data['phone'] = html_escape($this->request->getPost('phone'));
		$data['gender'] = html_escape($this->request->getPost('gender'));
		$data['address'] = html_escape($this->request->getPost('address'));
		$data['role'] = 'admin';
		$data['watch_history'] = '[]';

		// check email duplication
		$duplication_status = $this->check_duplication('on_create', $data['email']);
		if ($duplication_status) {
			\db()->table('users')->insert($data);
			$response = array(
				'status' => true,
				'notification' => get_phrase('admin_added_successfully')
			);
		} else {
			$response = array(
				'status' => false,
				'notification' => get_phrase('sorry_this_email_has_been_taken')
			);
		}

		return json_encode($response);
	}

	public function update_admin($param1 = '')
	{
		$data['name'] = html_entity_decode(html_escape($this->request->getPost('name')));
		$data['email'] = html_escape($this->request->getPost('email'));
		$data['phone'] = html_escape($this->request->getPost('phone'));
		$data['gender'] = html_escape($this->request->getPost('gender'));
		$data['address'] = html_escape($this->request->getPost('address'));
		$data['school_id'] = html_escape($this->request->getPost('school_id'));
		// check email duplication
		$duplication_status = $this->check_duplication('on_update', $data['email'], $param1);
		if ($duplication_status) {
			\db()->table('users')->where('id', $param1)->update($data);

			$notification = get_phrase('admin_has_been_updated_successfully');
			$response = array(
				'status' => true,
				'notification' => $notification,
				'csrf' => array(
					'name' => $this->security->get_csrf_token_name(),
					'hash' => $this->security->get_csrf_hash()
				)
			);
		} else {
			$response = array(
				'status' => false,
				'notification' => get_phrase('sorry_this_email_has_been_taken'),
				'csrf' => array(
					'name' => $this->security->get_csrf_token_name(),
					'hash' => $this->security->get_csrf_hash()
				)
			);
		}

		return json_encode($response);
	}

	public function delete_admin($param1 = '')
	{
		\db()->table('users')->where('id', $param1)->delete();

		$response = array(
			'status' => true,
			'notification' => get_phrase('admin_has_been_deleted_successfully')
		);
		return json_encode($response);
	}
	// ADMIN CRUD SECTION ENDS
	

	// SCHOOL CRUD SECTION STARTS
	public function create_school()
	{
		// $data['school_id'] = html_escape($this->request->getPost('school_id'));
		$data['name'] = html_entity_decode(html_escape($this->request->getPost('name')));
		$data['phone'] = html_escape($this->request->getPost('phone'));
		// $data['email'] = html_escape($this->request->getPost('email'));
		$data['description'] = html_escape($this->request->getPost('description'));
		$data['address'] = html_escape($this->request->getPost('address'));
		$data['access'] = html_escape($this->request->getPost('access'));
		$data['category'] = html_escape($this->request->getPost('category'));
		$data['status'] = 1;
		$data = array_merge($data, community_subscription_seed());
		// $data['role'] = 'admin';
		// $data['watch_history'] = '[]';

		// check email duplication
		$duplication_status = $this->check_duplication('on_create', $data['name']);
		if ($duplication_status) {
			\db()->table('schools')->insert($data);
			$school_id = \db()->insertID();
			$schoolImage = $this->request->getFile('school_image');
			if ($schoolImage && $schoolImage->isValid() && !$schoolImage->hasMoved()) {
				$uploadDir = FCPATH . 'uploads/schools';
				if (!is_dir($uploadDir)) {
					@mkdir($uploadDir, 0755, true);
				}
				$schoolImage->move($uploadDir, $school_id . '.jpg', true);
			}
			// Data to be inserted
			$data = array(
				array(

					'key' => 'stripe_settings',
					'value' => '[{\"stripe_active\":\"yes\",\"stripe_mode\":\"on\",\"stripe_test_secret_key\":\"1234\",\"stripe_test_public_key\":\"1234\",\"stripe_live_secret_key\":\"1234\",\"stripe_live_public_key\":\"1234\",\"stripe_currency\":\"USD\"}]',
					'school_id' => $school_id
				),
				array(

					'key' => 'paypal_settings',
					'value' => '[{\"paypal_active\":\"yes\",\"paypal_mode\":\"sandbox\",\"paypal_client_id_sandbox\":\"1234\",\"paypal_client_id_production\":\"1234\",\"paypal_currency\":\"USD\"}]',
					'school_id' => $school_id
				)
			);

			// Insert data into the `payment_settings` table
			\db()->table('payment_settings')->insertBatch($data);

			// Data to be inserted
			$data = array(

				'school_id' => $school_id,
				'system_currency' => 'USD',
				'currency_position' => 'left',
				'language' => 'english',

			);

			// Insert data into the `settings` table
			\db()->table('settings_school')->insert($data);

			$response = array(
				'status' => true,
				'notification' => get_phrase('school_added_successfully')
			);
		} else {
			$response = array(
				'status' => false,
				'notification' => get_phrase('sorry_this_name_has_been_taken')
			);
		}

		return json_encode($response);
	}

	public function update_school($param1 = '')
	{
		$data['name'] = html_entity_decode(html_escape($this->request->getPost('name')));
		$data['phone'] = html_escape($this->request->getPost('phone'));
		// $data['email'] = html_escape($this->request->getPost('email'));
		$data['description'] = html_escape($this->request->getPost('description'));
		$data['address'] = html_escape($this->request->getPost('address'));
		$data['access'] = html_escape($this->request->getPost('access'));
		$data['category'] = htmlspecialchars_decode($this->request->getPost('category'));
		$tracked = ['name', 'phone', 'description', 'address', 'access', 'category'];
		$before = \db()->table('schools')->select(implode(',', $tracked))->where('id', $param1)->get()->getRowArray() ?? [];
		// check email duplication
		// $duplication_status = $this->check_duplication('on_update', $data['email'], $param1);
		// if($duplication_status){
		\db()->table('schools')->where('id', $param1)->update($data);
		$logoUpdated = false;
		$schoolImage = $this->request->getFile('school_image');
		if ($schoolImage && $schoolImage->isValid() && !$schoolImage->hasMoved()) {
			$uploadDir = FCPATH . 'uploads/schools';
			if (!is_dir($uploadDir)) {
				@mkdir($uploadDir, 0755, true);
			}
			$schoolImage->move($uploadDir, $param1 . '.jpg', true);
			$logoUpdated = true;
		}

		(new \App\Models\Audit_log_model())->log_fiche_change(
			$param1,
			array_intersect_key($before, array_flip($tracked)),
			array_intersect_key($data, array_flip($tracked)),
			['logo_updated' => $logoUpdated]
		);
		
		$response = array(
			'status' => true,
			'notification' => get_phrase('school_has_been_updated_successfully')
		);

		// }else{
		//  $response = array(
		//      'status' => false,
		//      'notification' => get_phrase('sorry_this_email_has_been_taken')
		//  );
		// }

		return json_encode($response);
	}
	public function delete_school($param1 = '')
	{
		// \db()->where('id', $param1);
		$data['Etat'] = 0;
		\db()->table('schools')->where('id', $param1)->update($data);
		// \db()->delete('schools');
	
		// Désactiver le compte admin principal lié à cette école
		$admin = \db()->table('users')
			->where('school_id', $param1)
			->where('role', 'admin')
			->get()
			->getRow();
		if (!empty($admin)) {
			\db()->table('users')->where('id', $admin->id)->update(['status' => 0]); // soft delete admin
			log_message('debug', "Admin désactivé pour l'école ID {$param1}");
		}

		$response = array(
			'status' => true,
			'notification' => get_phrase('school_has_been_deleted_successfully')
		);
		return json_encode($response);
	}
	// school CRUD SECTION ENDS

	//START TEACHER section
	public function create_teacher()
	{
		$school_id = (int) school_id();
		$existing_user_id = (int) ($this->request->getPost('existing_user_id') ?? 0);

		if ($existing_user_id) {
			// Vérifie qu’il n’est PAS déjà teacher dans cette école
			$already = \db()->table('user_schools')
				->where('user_id', $existing_user_id)
				->where('school_id', $school_id)
				->where('role', 'teacher')
				->countAllResults();

			if ($already > 0) {
				return [
					'status' => false,
					'notification' => get_phrase("this_email_already_exists_as_teacher_in_this_school")
				];
			}

			$teacher_id = $existing_user_id;
		} else {
			// === Création d'un nouvel utilisateur ===
			$data['school_id'] = html_escape($this->request->getPost('school_id'));
			$data['name'] = html_entity_decode(html_escape($this->request->getPost('name')));
			$data['email'] = html_escape($this->request->getPost('email'));
			$data['password'] = password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT);
			$data['phone'] = html_escape($this->request->getPost('phone'));
			$data['gender'] = html_escape($this->request->getPost('gender'));
			$data['address'] = html_escape($this->request->getPost('address'));
			$data['role'] = 'teacher';
			$data['watch_history'] = '[]';
			$data['status'] = 1;

			$duplication_status = $this->check_duplication('on_create', $data['email']);
			if (!$duplication_status) {
				return [
					'status' => false,
					'notification' => get_phrase("sorry_this_email_has_been_taken")
				];
			}

			\db()->table('users')->insert($data);
			$teacher_id = \db()->insertID();

			$imageFile = $this->request->getFile('image_file');
			if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
				$uploadDir = FCPATH . 'uploads/users';
				if (!is_dir($uploadDir)) {
					@mkdir($uploadDir, 0755, true);
				}
				$imageFile->move($uploadDir, $teacher_id . '.jpg', true);
			}

		}

		// =============================================
		// BLOC COMMUN : exécuté dans les DEUX cas
		// =============================================
		$teacher_table_data = [
			'user_id' => $teacher_id,
			'about' => html_escape($this->request->getPost('about')),
			'social_links' => json_encode([
				'facebook' => $this->request->getPost('facebook_link'),
				'twitter' => $this->request->getPost('twitter_link'),
				'linkedin' => $this->request->getPost('linkedin_link')
			]),
			'designation' => html_escape($this->request->getPost('designation')),
			'school_id' => $school_id,
			'show_on_website' => $this->request->getPost('show_on_website') ? 1 : 0
		];
		$existingTeacherRow = \db()->table('teachers')
			->where('user_id', $teacher_id)
			->where('school_id', $school_id)
			->get()
			->getRowArray();
		if ($existingTeacherRow) {
			\db()->table('teachers')
				->where('id', $existingTeacherRow['id'])
				->update($teacher_table_data);
		} else {
			\db()->table('teachers')->insert($teacher_table_data);
		}

		// Ajout dans user_schools
		$existingUserSchool = \db()->table('user_schools')
			->where('user_id', $teacher_id)
			->where('school_id', $school_id)
			->get()
			->getRowArray();
		if ($existingUserSchool) {
			\db()->table('user_schools')
				->where('id', $existingUserSchool['id'])
				->update(['role' => 'teacher']);
		} else {
			\db()->table('user_schools')->insert([
				'user_id' => $teacher_id,
				'school_id' => $school_id,
				'role' => 'teacher'
			]);
		}
		\db()->table('users')->where('id', $teacher_id)->update(['school_id' => $school_id]);
		return [
			'status' => true,
			'notification' => get_phrase("teacher_added_successfully")
		];
	}

	public function update_teacher($param1 = '')
	{
		$data['name'] = html_entity_decode((string) html_escape($this->request->getPost('name')));
		$data['email'] = html_escape($this->request->getPost('email'));
		$data['phone'] = html_escape($this->request->getPost('phone'));
		$data['gender'] = html_escape($this->request->getPost('gender'));
		$data['address'] = html_escape($this->request->getPost('address'));
		$postedSchoolId = (int) ($this->request->getPost('school_id') ?? 0);
		$targetSchoolId = $postedSchoolId > 0 ? $postedSchoolId : (int) ($this->school_id ?? 0);

		// check email duplication
		$duplication_status = $this->check_duplication('on_update', $data['email'], $param1);
		if ($duplication_status) {
			$userBuilder = \db()->table('users')->where('id', $param1);
			if ($targetSchoolId > 0) {
				$userBuilder->where('school_id', $targetSchoolId);
			}
			$userBuilder->update($data);


			$teacher_table_data['designation'] = html_escape($this->request->getPost('designation'));
			$teacher_table_data['about'] = html_escape($this->request->getPost('about'));
			$social_links = array(
				'facebook' => $this->request->getPost('facebook_link'),
				'twitter' => $this->request->getPost('twitter_link'),
				'linkedin' => $this->request->getPost('linkedin_link')
			);
			$teacher_table_data['social_links'] = json_encode($social_links);
			//$teacher_table_data['show_on_website'] = $this->request->getPost('show_on_website');
			$teacherBuilder = \db()->table('teachers')->where('user_id', $param1);
			if ($targetSchoolId > 0) {
				$teacherBuilder->where('school_id', $targetSchoolId);
			}
			$teacherBuilder->update($teacher_table_data);

			$imageFile = $this->request->getFile('image_file');
			if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
				$imageFile->move(FCPATH . 'uploads/users', $param1 . '.jpg', true);
			}
			// Par défaut, succès de Wayo
			$notification = get_phrase('teacher_has_been_updated_successfully');
			$response = array(
				'status' => true,
				'notification' => $notification
			);
		} else {
			$response = array(
				'status' => false,
				'notification' => get_phrase('sorry_this_email_has_been_taken')
			);
		}

		return $response;
	}



	public function delete_teacher($param1 = '', $param2 = '')
	{
		// Suppression dans la base locale (CI4 Query Builder)
		\db()->table('users')->where('id', $param1)->delete();
		\db()->table('teachers')->where('user_id', $param1)->delete();
		if (!empty($param2)) {
			\db()->table('teacher_permissions')->where('teacher_id', $param2)->delete();
		}

		$response = array(
			'status' => true,
			'notification' => get_phrase('teacher_has_been_deleted_successfully')
		);
		return $response;
	}


	public function get_teachers()
	{
		$checker = array(
			'school_id' => $this->school_id,
			'role' => 'teacher'
		);
		$result = \db()->table('users')->where($checker)->get()->getResultArray();
		return $result;
	}

	public function get_teacher_by_id($teacher_id = "")
	{
		$checker = array(
			'school_id' => $this->school_id,
			'id' => $teacher_id
		);
		$result = \db()->table('teachers')->where($checker)->get()->getRowArray();
		return \db()->table('users')->where('school_id', $this->school_id)->get()->getResult();
	}
	//END TEACHER section


	//START TEACHER PERMISSION section
	public function teacher_permission()
	{
		$class_id = (int)html_escape($this->request->getPost('class_id'));
		$teacher_id = (int)html_escape($this->request->getPost('teacher_id'));
		$column_name = html_escape($this->request->getPost('column_name'));
		$value = (int) html_escape($this->request->getPost('value'));
		$value = $value === 1 ? 1 : 0;

		if ($class_id <= 0 || $teacher_id <= 0) {
			return json_encode([
				'status' => false,
				'notification' => get_phrase('invalid_request')
			]);
		}

		$user_id = session()->get('user_id');
		$rateLimitKey = 'teacher_permission_rate_limit_' . $user_id;
		$rateLimitCount = session()->get($rateLimitKey, 0);
		$rateLimitTime = session()->get($rateLimitKey . '_time', time());

		if (time() - $rateLimitTime > 60) {
			session()->set($rateLimitKey, 0);
			$rateLimitCount = 0;
		}

		if ($rateLimitCount >= 100) {
			log_message('warning', 'Rate limit exceeded for teacher_permission by user_id: ' . $user_id);
			return json_encode([
				'status' => false,
				'notification' => get_phrase('too_many_requests')
			]);
		}

		session()->set($rateLimitKey, $rateLimitCount + 1);
		session()->set($rateLimitKey . '_time', time());

		$teacher = \db()->table('teachers')
			->where('id', $teacher_id)
			->where('school_id', $this->school_id)
			->get()
			->getRow();

		if (!$teacher) {
			log_message('warning', 'Unauthorized permission modification attempt by user_id: ' . 
				$user_id . ' for teacher_id: ' . $teacher_id . ', class_id: ' . $class_id . 
				', school_id: ' . $this->school_id);
			return json_encode([
				'status' => false,
				'notification' => get_phrase('unauthorized')
			]);
		}

		$class = \db()->table('classes')
			->where('id', $class_id)
			->where('school_id', $this->school_id)
			->get()
			->getRow();

		if (!$class) {
			log_message('warning', 'Unauthorized permission modification attempt by user_id: ' . 
				$user_id . ' for teacher_id: ' . $teacher_id . ', class_id: ' . $class_id . 
				', school_id: ' . $this->school_id);
			return json_encode([
				'status' => false,
				'notification' => get_phrase('unauthorized')
			]);
		}

		$builder = \db()->table('teacher_permissions');
		$current = $builder
			->where('class_id', $class_id)
			->where('teacher_id', $teacher_id)
			->get()
			->getRowArray();

		$data = [];
		if ($column_name === 'all') {
			$data['marks'] = $value;
			$data['attendance'] = $value;
		} elseif (in_array($column_name, ['marks', 'attendance'], true)) {
			$data[$column_name] = $value;
		}

		if (empty($data)) {
			return json_encode([
				'status' => false,
				'notification' => get_phrase('invalid_request')
			]);
		}

		$school_id = $this->school_id;
		$changed_by = $user_id;

		if ($current) {
			\db()->table('teacher_permissions')
				->where('class_id', $class_id)
				->where('teacher_id', $teacher_id)
				->update($data);
			
			log_message('info', 'Permission modified by user_id: ' . $changed_by . 
				' for teacher_id: ' . $teacher_id . ', class_id: ' . $class_id . 
				', changes: ' . json_encode($data) . ', school_id: ' . $school_id);
			
			$this->log_permission_changes($teacher_id, $class_id, $column_name, $current, $data, $changed_by, $school_id);
		} else {
			$insert = array_merge(
				[
					'class_id' => $class_id,
					'teacher_id' => $teacher_id,
					'marks' => 0,
					'attendance' => 0,
				],
				$data
			);
			\db()->table('teacher_permissions')->insert($insert);
			
			log_message('info', 'Permission created by user_id: ' . $changed_by . 
				' for teacher_id: ' . $teacher_id . ', class_id: ' . $class_id . 
				', initial values: ' . json_encode($data) . ', school_id: ' . $school_id);
			
			$this->log_permission_changes($teacher_id, $class_id, $column_name, ['marks' => 0, 'attendance' => 0], $data, $changed_by, $school_id);
		}

		return json_encode([
			'status' => true,
			'notification' => get_phrase('teacher_permission_updated')
		]);
	}

	private function log_permission_changes($teacher_id, $class_id, $column_name, $old_data, $new_data, $changed_by, $school_id)
	{
		if ($column_name === 'all') {
			$columns_to_log = ['marks', 'attendance'];
		} else {
			$columns_to_log = [$column_name];
		}

		foreach ($columns_to_log as $col) {
			$old_value = isset($old_data[$col]) ? (int) $old_data[$col] : 0;
			$new_value = isset($new_data[$col]) ? (int) $new_data[$col] : 0;

			if ($old_value !== $new_value) {
				\db()->table('permission_history')->insert([
					'teacher_id' => $teacher_id,
					'class_id' => $class_id,
					'column_name' => $col,
					'old_value' => $old_value,
					'new_value' => $new_value,
					'changed_by' => $changed_by,
					'school_id' => $school_id
				]);
			}
		}
	}
	//END TEACHER PERMISSION section

	//START ACCOUNTANT section
	public function accountant_create()
	{
		$data['name'] = html_escape($this->request->getPost('name'));
		$data['email'] = html_escape($this->request->getPost('email'));
		$data['password'] = password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT);
		$data['phone'] = html_escape($this->request->getPost('phone'));
		$data['gender'] = html_escape($this->request->getPost('gender'));
		$data['address'] = html_escape($this->request->getPost('address'));
		$data['school_id'] = $this->school_id;
		$data['role'] = 'accountant';
		$data['watch_history'] = '[]';

		$duplication_status = $this->check_duplication('on_create', $data['email']);
		if ($duplication_status) {
			\db()->table('users')->insert($data);

			return array(
				'status' => true,
				'notification' => get_phrase('accountant_added_successfully')
			);
		} else {
			return array(
				'status' => false,
				'notification' => get_phrase('sorry_this_email_has_been_taken')
			);
		}

		//return json_encode($response);
	}

	public function accountant_update($param1 = '')
	{
		$data['name'] = html_escape($this->request->getPost('name'));
		$data['email'] = html_escape($this->request->getPost('email'));
		$data['phone'] = html_escape($this->request->getPost('phone'));
		$data['gender'] = html_escape($this->request->getPost('gender'));
		$data['address'] = html_escape($this->request->getPost('address'));

		$duplication_status = $this->check_duplication('on_update', $data['email'], $param1);
		if ($duplication_status) {
			\db()->table('users')->where('id', $param1)->update($data);

			$response = array(
				'status' => true,
				'notification' => get_phrase('accountant_has_been_updated_successfully')
			);
		} else {
			$response = array(
				'status' => false,
				'notification' => get_phrase('sorry_this_email_has_been_taken')
			);
		}

		return json_encode($response);
	}

	public function accountant_delete($param1 = '')
	{
		\db()->table('users')->where('id', $param1)->delete();

		$response = array(
			'status' => true,
			'notification' => get_phrase('accountant_has_been_deleted_successfully')
		);

		return json_encode($response);
	}

	public function get_accountants()
	{
		$checker = array(
			'school_id' => $this->school_id,
			'role' => 'accountant'
		);
		return \db()->table('users')->where($checker)->get()->getResult();
	}

	public function get_accountant_by_id($accountant_id = "")
	{
		$checker = array(
			'school_id' => $this->school_id,
			'id' => $accountant_id
		);
		return \db()->table('users')->where($checker)->get()->getResult();
	}
	//END ACCOUNTANT section

	//START LIBRARIAN section
	public function librarian_create()
	{
		$data['name'] = html_escape($this->request->getPost('name'));
		$data['email'] = html_escape($this->request->getPost('email'));
		$data['password'] = password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT);
		$data['phone'] = html_escape($this->request->getPost('phone'));
		$data['gender'] = html_escape($this->request->getPost('gender'));
		$data['address'] = html_escape($this->request->getPost('address'));
		$data['school_id'] = $this->school_id;
		$data['role'] = 'librarian';
		$data['watch_history'] = '[]';

		// check email duplication
		$duplication_status = $this->check_duplication('on_create', $data['email']);
		if ($duplication_status) {
			\db()->table('users')->insert($data);

			return array(
				'status' => true,
				'notification' => get_phrase('librarian_added_successfully')
			);
		} else {
			return array(
				'status' => false,
				'notification' => get_phrase('sorry_this_email_has_been_taken')
			);
		}

		//return json_encode($response);
	}

	public function librarian_update($param1 = '')
	{
		$data['name'] = html_escape($this->request->getPost('name'));
		$data['email'] = html_escape($this->request->getPost('email'));
		$data['phone'] = html_escape($this->request->getPost('phone'));
		$data['gender'] = html_escape($this->request->getPost('gender'));
		$data['address'] = html_escape($this->request->getPost('address'));

		// check email duplication
		$duplication_status = $this->check_duplication('on_update', $data['email'], $param1);
		if ($duplication_status) {
			\db()->table('users')->where('id', $param1)->update($data);

			$response = array(
				'status' => true,
				'notification' => get_phrase('librarian_updated_successfully')
			);
		} else {
			$response = array(
				'status' => false,
				'notification' => get_phrase('sorry_this_email_has_been_taken')
			);
		}

		return json_encode($response);
	}

	public function librarian_delete($param1 = '')
	{
		\db()->table('users')->where('id', $param1)->delete();

		$response = array(
			'status' => true,
			'notification' => get_phrase('librarian_deleted_successfully')
		);
		return json_encode($response);
	}


	public function get_librarians()
	{
		$checker = array(
			'school_id' => $this->school_id,
			'role' => 'librarian'
		);
		return \db()->table('users')->where($checker)->get()->getResult();
	}

	public function get_librarian_by_id($librarian_id = "")
	{
		$checker = array(
			'school_id' => $this->school_id,
			'id' => $librarian_id
		);
		return \db()->table('users')->where($checker)->get()->getResult();
	}
	//END LIBRARIAN section


	//START STUDENT AND ADMISSION section
	public function single_student_create()
	{

		/*Une transaction est utilisée pour s'assurer que toutes les opérations de base de données sont exécutées avec succès. 
		Si une erreur survient, tout est annulé.*/
		\db()->transBegin(); // Début de transaction

		try {
			// // Vérification des champs obligatoires (email et image de l'étudiant)
			// if (empty($this->request->getPost('email']) || empty($FILES['student_image']['tmp_name'])) {
			// 	session()->setFlashdata('error', get_phrase('required_fields_missing'));
			// 	throw new Exception(get_phrase('required_fields_missing'));
			// }
			// Préparation des données utilisateur
			$user_data = [
				'name' => html_entity_decode(html_escape($this->request->getPost('name'))),
				'email' => html_escape($this->request->getPost('email')),
				'birthday' => date('Y-m-d', strtotime(html_escape($this->request->getPost('birthday')))),
				'gender' => html_escape($this->request->getPost('gender')),
				'Rue' => html_escape($this->request->getPost('Street')),
				'Numero' => html_escape($this->request->getPost('number')),
				'Ville' => html_escape($this->request->getPost('city')),
				'Codepostal' => html_escape($this->request->getPost('postal_code')),
				'num_vat' => html_escape($this->request->getPost('VAT_number')),
				'phone' => html_escape($this->request->getPost('phone')),
				'role' => 'student',
				'school_id' => $this->school_id,
				'watch_history' => '[]',
				'status' => 1,
			];

			// Vérifier que l'email n'existe pas déjà pour éviter les doublons
			if (!$this->check_duplication('on_create', $user_data['email'])) {
				session()->setFlashdata('error', get_phrase('sorry_this_email_has_been_taken'));
				throw new Exception(get_phrase('sorry_this_email_has_been_taken'));
			}

			// Insertion utilisateur
			if (!\db()->table('users')->insert($user_data)) {
				session()->setFlashdata('error', get_phrase('user_creation_failed'));
				throw new Exception(get_phrase('user_creation_failed'));
			}
			$user_id = \db()->insertID();
		

			// Insertion étudiant
			$student_data = [
				'code' => student_code(),
				'user_id' => $user_id,
				'session' => $this->active_session,
				'school_id' => $this->school_id,
				'status' => 1
			];

			// Insérer le profil étudiant
			if (!\db()->table('students')->insert($student_data)) {
				session()->setFlashdata('error', get_phrase('student_profile_creation_failed'));
				throw new Exception(get_phrase('student_profile_creation_failed'));
			}
			$student_id = \db()->insertID();// Récupérer l'ID de l'étudiant créé

			// Inscription à la classe
			$class_id   = html_escape($this->request->getPost('class_id'));
			$enroll_data = [
				'student_id' => $student_id,
				'class_id'   => $class_id,
				'session'    => $this->active_session,
				'school_id'  => $this->school_id

			];
			// Insérer l'inscription de l'étudiant
			if (!\db()->table('enrols')->insert($enroll_data)) {
				session()->setFlashdata('error', get_phrase('enrollment_failed'));
				throw new Exception(get_phrase('enrollment_failed'));
			}

			

			$studentImage = $this->request->getFile('student_image');
			if ($studentImage && $studentImage->isValid() && !$studentImage->hasMoved()) {
				$uploadDir = FCPATH . 'uploads/users';
				if (!is_dir($uploadDir)) {
					@mkdir($uploadDir, 0755, true);
				}

				$targetPath = $uploadDir . DIRECTORY_SEPARATOR . $user_id . '.jpg';
				if (!$studentImage->move($uploadDir, $user_id . '.jpg', true)) {
					log_message('error', 'student image upload failed to ' . $targetPath);
					throw new Exception(get_phrase('image_upload_failed'));
				}
			}


			// Envoi d'email
			$reset_link = base_url("login/new_password_student?user_id=" . $user_id);
			if (!$this->email_model->password_send_add_student($reset_link, $user_id)) {
				log_message('error', 'Email sending failed for user: ' . $user_id);
			}

			//Tout s'est bien passé, valider la transaction
			\db()->transCommit();
			//session()->setFlashdata('flash_message', get_phrase('student_added_successfully'));
			return true;

		} catch (Exception $e) {
			//En cas d'erreur, annuler toutes les modifications (rollback)
			\db()->transRollback();
			return $e->getMessage(); // Return the error message, not just false
		}
	}
	
	public function bulk_student_create()
	{
		$duplication_counter = 0;
		$class_id = html_escape($this->request->getPost('class_id'));


		$students_name = html_escape($this->request->getPost('name'));
		$students_email = html_escape($this->request->getPost('email'));
		//$students_password = html_escape($this->request->getPost('password'));
		$students_gender = html_escape($this->request->getPost('gender'));
		$students_parent = html_escape($this->request->getPost('parent_id'));
		// Préparation des données utilisateur
	
		foreach ($students_name as $key => $value):
			// check email duplication
			$duplication_status = $this->check_duplication('on_create', $students_email[$key]);
			if ($duplication_status) {
				$user_data['name'] = html_entity_decode($students_name[$key]);
				$user_data['email'] = $students_email[$key];
				// $user_data['password'] = sha1($students_password[$key]);
				$user_data['gender'] = $students_gender[$key];
				$user_data['role'] = 'student';
				$user_data['school_id'] = $this->school_id;
				$user_data['watch_history'] = '[]';
				$user_data['status'] = 1;
			
				\db()->table('users')->insert($user_data);
				$user_id = \db()->insertID();
			

				$student_data['code'] = student_code();
				$student_data['user_id'] = $user_id;

				$student_data['session'] = $this->active_session;
				$student_data['school_id'] = $this->school_id;
				$student_data['status'] = 1;
				\db()->table('students')->insert($student_data);
				$student_id = \db()->insertID();

				$enroll_data['student_id'] = $student_id;
				$enroll_data['class_id'] = $class_id;

				$enroll_data['session'] = $this->active_session;
				$enroll_data['school_id'] = $this->school_id;
				\db()->table('enrols')->insert($enroll_data);

				// Envoi d'email de réinitialisation du mot de passe
				$reset_link = base_url("login/new_password_student?user_id=" . $user_id);
				if (!$this->email_model->password_send_add_student($reset_link, $user_id)) {
					log_message('error', 'Email sending failed for user: ' . $user_id);
				}
			} else {
				$duplication_counter++;
			}
		endforeach;

		if ($duplication_counter > 0) {
			$response = array(
				'status' => true,
				'notification' => get_phrase('some_of_the_emails_have_been_taken'),
				'type' => 'error'
			);
		} else {
			$response = array(
				'status' => true,
				'notification' => get_phrase('students_added_successfully'),
				'type' => 'success'
			);
		}

		header('Content-Type: application/json');
		echo json_encode($response);
		exit();
	}
	public function excel_create()
	{

		$class_id = html_escape($this->request->getPost('class_id'));

		$school_id = $this->school_id;
		$session_id = $this->active_session;
		$role = 'student';
		$files = $this->request->getFiles();
		$file_name = $files['csv_file']['name'];
		// move_uploaded_file($files['csv_file']['tmp_name'], 'uploads/csv_file/student.generate.csv');	
		$upload_path = 'uploads/csv_file/student.generate.csv';
		// Vérifier si le dossier de destination existe
		if (!is_dir('uploads/csv_file/')) {
			mkdir('uploads/csv_file/', 0755, true); // Créer le dossier avec les permissions nécessaires
		}

		if (!move_uploaded_file($files['csv_file']['tmp_name'], $upload_path)) {
			error_log("Erreur : Impossible de déplacer le fichier uploadé.");
			return json_encode(array('status' => false, 'notification' => 'Erreur lors du déplacement du fichier.'));
		}

		// Vérifier si le fichier a bien été déplacé
		if (!file_exists($upload_path)) {
			error_log("Erreur : Fichier CSV non trouvé à l'emplacement : $upload_path");
			return json_encode(array('status' => false, 'notification' => 'Fichier CSV introuvable.'));
		}

		if (($handle = fopen('uploads/csv_file/student.generate.csv', 'r')) !== FALSE) { // Check the resource is valid
			$count = 0;
			$duplication_counter = 0;
			while (($line = fgets($handle)) !== FALSE) { // Lire chaque ligne en tant que chaîne de caractères
				$all_data = explode(',', $line); // Diviser la ligne en utilisant la virgule comme séparateur

				if ($count > 0) {
					$user_data['name'] = html_entity_decode(str_replace('"', '', trim($all_data[0])));
					$user_data['email'] = html_escape($all_data[1]);
					$user_data['phone'] = trim(html_escape($all_data[2]));
					$user_data['gender'] = str_replace('"', '', trim($all_data[3]));
					$user_data['role'] = $role;
					$user_data['school_id'] = $school_id;
					$user_data['watch_history'] = '[]';
					$user_data['status'] = 1;

					// check email duplication
					$duplication_status = $this->check_duplication('on_create', $user_data['email']);
					if ($duplication_status) {
						\db()->table('users')->insert($user_data);
						$user_id = \db()->insertID();

						$student_data['code'] = student_code();
						$student_data['user_id'] = $user_id;

						$student_data['session'] = $session_id;
						$student_data['school_id'] = $school_id;
						$student_data['status'] = 1;
						\db()->table('students')->insert($student_data);
						$student_id = \db()->insertID();

						$enroll_data['student_id'] = $student_id;
						$enroll_data['class_id'] = $class_id;

						$enroll_data['session'] = $session_id;
						$enroll_data['school_id'] = $school_id;
						\db()->table('enrols')->insert($enroll_data);

						// Envoi d'email de réinitialisation du mot de passe
						$reset_link = base_url("login/new_password_student?user_id=" . $user_id);
						if (!$this->email_model->password_send_add_student($reset_link, $user_id)) {
							log_message('error', 'Email sending failed for user: ' . $user_id);
						}
					} else {
						$duplication_counter++;
					}
				}
				$count++;
			}
			fclose($handle);
		}

		if ($duplication_counter > 0) {
			$response = array(
				'status' => true,
				'notification' => get_phrase('some_of_the_emails_have_been_taken'),
				'type' => 'error'
			);
		} else {
			$response = array(
				'status' => true,
				'notification' => get_phrase('students_added_successfully'),
				'type' => 'success'
			);
		}

		header('Content-Type: application/json');
		echo json_encode($response);
		exit();
	}

	public function student_update($student_id = '', $user_id = '')
	{
		$student_id = (int) $student_id;
		$user_id = (int) $user_id;
		$current_user_id = (int) (session()->get('user_id') ?? 0);
		$current_user_type = strtolower((string) (session()->get('user_type') ?? session()->get('role') ?? ''));
		$is_teacher_context = (session()->get('teacher_login') == 1) || $current_user_type === 'teacher';
		$active_school_id = (int) ($this->school_id ?? (function_exists('school_id') ? school_id() : 0));
		if ($active_school_id <= 0) {
			$active_school_id = (int) (session()->get('active_school_id') ?? session()->get('school_id') ?? 0);
		}

		$student_row = \db()->table('students')
			->select('id, user_id, school_id')
			->where('id', $student_id)
			->get()
			->getRowArray();

		if (empty($student_row) || (int) ($student_row['user_id'] ?? 0) !== $user_id) {
			$response = array(
				'status' => false,
				'notification' => get_phrase('action_not_allowed'),
				'csrf' => array(
					'name' => csrf_token(),
					'hash' => csrf_hash()
				)
			);
			echo json_encode($response);
			exit();
		}

		$target_school_id = (int) ($student_row['school_id'] ?? 0);
		if ($target_school_id <= 0) {
			$target_school_id = $active_school_id;
		}

		$class_ids = $this->request->getPost('class_id');
		if (!is_array($class_ids)) {
			$class_ids = array($class_ids);
		}
		$class_ids = array_values(array_unique(array_map('intval', $class_ids)));
		$class_ids = array_values(array_filter($class_ids, static fn ($id) => $id > 0));

		if ($is_teacher_context) {
			if ($current_user_id <= 0 || $active_school_id <= 0 || $target_school_id !== $active_school_id) {
				$response = array(
					'status' => false,
					'notification' => get_phrase('action_not_allowed'),
					'csrf' => array(
						'name' => csrf_token(),
						'hash' => csrf_hash()
					)
				);
				echo json_encode($response);
				exit();
			}

			$teacher_row = \db()->table('teachers')
				->select('id')
				->where('user_id', $current_user_id)
				->where('school_id', $active_school_id)
				->get()
				->getRowArray();
			$teacher_id = (int) ($teacher_row['id'] ?? 0);
			if ($teacher_id <= 0) {
				$response = array(
					'status' => false,
					'notification' => get_phrase('action_not_allowed'),
					'csrf' => array(
						'name' => csrf_token(),
						'hash' => csrf_hash()
					)
				);
				echo json_encode($response);
				exit();
			}

			$permission_rows = \db()->table('teacher_permissions')
				->select('class_id')
				->where('teacher_id', $teacher_id)
				->groupStart()
				->where('attendance', 1)
				->orWhere('marks', 1)
				->groupEnd()
				->get()
				->getResultArray();
			$permitted_class_ids = array_values(array_unique(array_map('intval', array_column($permission_rows, 'class_id'))));

			if (empty($permitted_class_ids) || empty($class_ids)) {
				$response = array(
					'status' => false,
					'notification' => get_phrase('action_not_allowed'),
					'csrf' => array(
						'name' => csrf_token(),
						'hash' => csrf_hash()
					)
				);
				echo json_encode($response);
				exit();
			}

			$current_access_count = \db()->table('enrols')
				->where('student_id', $student_id)
				->where('school_id', $active_school_id)
				->whereIn('class_id', $permitted_class_ids)
				->countAllResults();
			if ($current_access_count <= 0) {
				$response = array(
					'status' => false,
					'notification' => get_phrase('action_not_allowed'),
					'csrf' => array(
						'name' => csrf_token(),
						'hash' => csrf_hash()
					)
				);
				echo json_encode($response);
				exit();
			}

			$allowed_posted_class_ids = array_values(array_intersect($class_ids, $permitted_class_ids));
			if (count($allowed_posted_class_ids) !== count($class_ids)) {
				$response = array(
					'status' => false,
					'notification' => get_phrase('action_not_allowed'),
					'csrf' => array(
						'name' => csrf_token(),
						'hash' => csrf_hash()
					)
				);
				echo json_encode($response);
				exit();
			}

			$existing_classes = \db()->table('classes')
				->select('id')
				->where('school_id', $active_school_id)
				->whereIn('id', $class_ids)
				->get()
				->getResultArray();
			$existing_class_ids = array_values(array_unique(array_map('intval', array_column($existing_classes, 'id'))));
			if (count($existing_class_ids) !== count($class_ids)) {
				$response = array(
					'status' => false,
					'notification' => get_phrase('action_not_allowed'),
					'csrf' => array(
						'name' => csrf_token(),
						'hash' => csrf_hash()
					)
				);
				echo json_encode($response);
				exit();
			}
		}

		$user_data['name'] = html_entity_decode((string) ($this->request->getPost('name') ?? ''));
		$user_data['email'] = html_escape($this->request->getPost('email'));

		//Avec strtotime(...) : Le format stocké sera un timestamp Unix, c'est-à-dire un entier représentant le nombre de secondes depuis le 1er janvier 1970 (ex. 1617513600).
		//$user_data['birthday'] = strtotime(html_escape($this->request->getPost('birthday')));
		//$user_data['birthday']=date('Y-m-d', strtotime(html_escape($this->request->getPost('birthday'))));


		//birthday format
		// Récupérer la date envoyée par l'utilisateur via le formulaire, et échapper les caractères spéciaux pour éviter les attaques XSS.
		$posted_birthday = html_escape($this->request->getPost('birthday'));

		// Convertir la chaîne de texte (date) en format 'Y-m-d' (année-mois-jour) pour une insertion dans la base de données
		// - strtotime() convertit la date en timestamp Unix
		// - date('Y-m-d', ...) formate ce timestamp en une date standardisée pour la base de données.

		$user_data['birthday'] = date('Y-m-d', strtotime($posted_birthday));//Avec date('Y-m-d', strtotime(...)) : Le format stocké sera Y-m-d (ex. 2025-04-04), un format de date standard.

		$user_data['gender'] = html_escape($this->request->getPost('gender'));
		$user_data['Rue'] = html_escape($this->request->getPost('Street'));
		$user_data['Numero'] = html_escape($this->request->getPost('number'));
		$user_data['Ville'] = html_escape($this->request->getPost('city'));
		$user_data['Codepostal'] = html_escape($this->request->getPost('postal_code'));
		$user_data['phone'] = html_escape($this->request->getPost('phone'));
		$user_data['num_vat'] = html_escape($this->request->getPost('VAT_number'));

		// Check Duplication
		$duplication_status = $this->check_duplication('on_update', $user_data['email'], $user_id);

		if ($duplication_status) {
			// Start transaction
			\db()->transBegin();

			try {
				// Delete old class enrollments
				\db()->table('enrols')
					->where('student_id', $student_id)
					->where('school_id', $target_school_id)
					->delete();

				if (!empty($class_ids)) {
					foreach ($class_ids as $class_id) {


						// Verify both class_id and section_id exist before inserting
						if (!empty($class_id)) {
							$data = array(
								'student_id' => $student_id,
								'class_id' => html_escape($class_id),

								'session' => $this->active_session,
								'school_id' => $target_school_id,
							);
							\db()->table('enrols')->insert($data);
						}
					}
				}

				// Update user data
				\db()->table('users')->where('id', $user_id)->update($user_data);

				// Upload image if provided
				// if (isset($FILES['student_image']) && $FILES['student_image']['size'] > 0) {
				// 	move_uploaded_file($FILES['student_image']['tmp_name'], 'uploads/users/' . $user_id . '.jpg');
				// }

				$studentImage = $this->request->getFile('student_image');
				if ($studentImage && $studentImage->isValid() && !$studentImage->hasMoved()) {
					$maxSizeBytes = 2 * 1024 * 1024; // 2 MB
					$allowedMime = ['image/jpeg', 'image/png'];
					$sizeBytes = (int) $studentImage->getSize();
					$mimeType = (string) $studentImage->getMimeType();
					$tempPath = $studentImage->getTempName();

					if ($sizeBytes <= 0 || $sizeBytes > $maxSizeBytes) {
						throw new \RuntimeException(get_phrase('image_upload_failed'));
					}
					if (!in_array($mimeType, $allowedMime, true)) {
						throw new \RuntimeException(get_phrase('image_upload_failed'));
					}
					if ($tempPath === '' || @getimagesize($tempPath) === false) {
						throw new \RuntimeException(get_phrase('image_upload_failed'));
					}

					$uploadDir = FCPATH . 'uploads/users';
					if (!is_dir($uploadDir)) {
						@mkdir($uploadDir, 0755, true);
					}
					if (!$studentImage->move($uploadDir, $user_id . '.jpg', true)) {
						throw new \RuntimeException(get_phrase('image_upload_failed'));
					}
				}

				// Complete transaction
				\db()->transCommit();

				if (\db()->transStatus() === false) {
					// Transaction failed
					$error_message = \db()->error();
					log_message('error', 'Student update failed: ' . json_encode($error_message));

					$response = array(
						'status' => false,
						'notification' => get_phrase('database_error_occurred'),
						'csrf' => array(
							'name' => csrf_token(),
							'hash' => csrf_hash()
						)
					);
				} else {
					$notification = get_phrase('student_updated_successfully');

					$response = [
						'status' => true,
						'notification' => $notification,
						'csrf' => [
							'name' => csrf_token(),
							'hash' => csrf_hash()
						]
					];
				}
			} catch (\Throwable $e) {
				// Rollback transaction on exception
				\db()->transRollback();

				log_message('error', 'Exception in student_update: ' . $e->getMessage());

				$response = array(
					'status' => false,
					'notification' => get_phrase('error_updating_student') . ': ' . $e->getMessage(),
					'csrf' => array(
						'name' => csrf_token(),
						'hash' => csrf_hash()
					)
				);
			}
		} else {
			$response = array(
				'status' => false,
				'notification' => get_phrase('sorry_this_email_has_been_taken'),
				'csrf' => array(
					'name' => csrf_token(),
					'hash' => csrf_hash()
				)
			);
		}

		echo json_encode($response);
		exit();
	}
	public function delete_student($student_id, $user_id, $school_id = null)
	{
		$student_id = (int) $student_id;
		$user_id = (int) $user_id;
		$school_id = $school_id !== null ? (int) $school_id : (int) ($this->school_id ?? 0);

		if ($student_id <= 0 || $user_id <= 0) {
			return false;
		}

		$studentBuilder = \db()->table('students')
			->select('id, user_id, school_id')
			->where('id', $student_id)
			->where('user_id', $user_id);
		if ($school_id > 0) {
			$studentBuilder->where('school_id', $school_id);
		}
		$studentRow = $studentBuilder->get()->getRowArray();
		if (empty($studentRow)) {
			return false;
		}

		$enrolBuilder = \db()->table('enrols')->where('student_id', $student_id);
		if ($school_id > 0) {
			$enrolBuilder->where('school_id', $school_id);
		}
		$enrolBuilder->delete();


		// $path = 'uploads/users/' . $user_id . '.jpg';
		// if (file_exists($path)) {
		// 	unlink($path);
		// }

		$response = array(
			'status' => true,
			'notification' => get_phrase('student_deleted_successfully')
		);

		return true;
	}

	public function student_enrolment()
	{
		return \db()->table('enrols')->where('school_id', $this->school_id)->get()->getResult();
	}


	// This function will help to fetch student data by section, class or student id
	public function get_student_details_by_id($type = "", $id = "")
	{
		$enrol_data = array();
		if ($type == "class") {
			$checker = array(
				'class_id' => $id,
				'session' => $this->active_session,
				'school_id' => $this->school_id
			);
			$enrol_data = \db()->table('enrols')->where($checker)->get()->getResultArray();
			foreach ($enrol_data as $key => $enrol) {
				$student_details = \db()->table('students')->where('school_id', $this->school_id)->get()->getRowArray();
				$enrol_data[$key]['code'] = $student_details['code'];
				$enrol_data[$key]['user_id'] = $student_details['user_id'];

				$user_details = \db()->table('users')->where('school_id', $this->school_id)->get()->getRowArray();
				$enrol_data[$key]['name'] = $user_details['name'];
				$enrol_data[$key]['email'] = $user_details['email'];
				$enrol_data[$key]['role'] = $user_details['role'];
				$enrol_data[$key]['address'] = $user_details['address'];
				$enrol_data[$key]['phone'] = $user_details['phone'];
				$enrol_data[$key]['birthday'] = $user_details['birthday'];
				$enrol_data[$key]['gender'] = $user_details['gender'];

				$crudModel = $this->crud_model ?? model('Crud_model');
				if (is_object($crudModel) && method_exists($crudModel, 'get_class_details_by_id')) {
					$classResult = $crudModel->get_class_details_by_id($enrol['class_id']);
					$class_details = is_array($classResult)
						? $classResult
						: (method_exists($classResult, 'getRowArray') ? $classResult->getRowArray() : []);
				} else {
					$class_details = \db()->table('classes')->where('id', $enrol['class_id'])->get()->getRowArray();
				}

				$enrol_data[$key]['class_name'] = $class_details['name'];
			}
		} elseif ($type == "student") {
			$checker = array(
				'student_id' => $id,
				'session' => $this->active_session,

			);
			$enrol_data = \db()->table('enrols')->where($checker)->get()->getRowArray();
			// Try to find by user_id first
			$student_details = \db()->table('students')->where('school_id', $this->school_id)->get()->getRowArray();
			
			// If not found, try by id (student_id)
			if (empty($student_details)) {
				$student_details = \db()->table('students')->where('school_id', $this->school_id)->get()->getRowArray();
			}

			if ($student_details) {
				$enrol_data['code'] = $student_details['code'];
				$enrol_data['user_id'] = $student_details['user_id'];
			} else {
				// Handle case where student details are not found
				log_message('error', "User_model: Student details not found for user_id: {$id}");
				return null;
			}

			$user_details = \db()->table('users')->where('school_id', $this->school_id)->get()->getRowArray();

			if ($user_details) {
				$enrol_data['name'] = $user_details['name'];
				$enrol_data['email'] = $user_details['email'];
				$enrol_data['role'] = $user_details['role'];
				$enrol_data['address'] = $user_details['address'];
				$enrol_data['Rue'] = $user_details['Rue'];
				$enrol_data['Numero'] = $user_details['Numero'];
				$enrol_data['Ville'] = $user_details['Ville'];
				$enrol_data['Codepostal'] = $user_details['Codepostal'];
				$enrol_data['num_vat'] = $user_details['num_vat'];
				$enrol_data['phone'] = $user_details['phone'];
				$enrol_data['birthday'] = $user_details['birthday'];
				$enrol_data['gender'] = $user_details['gender'];
			} else {
				// Handle case where user details are not found
				log_message('error', "User_model: User details not found for student user_id: {$student_details['user_id']}");
				return null;
			}

			$class_id = isset($enrol_data['class_id']) ? $enrol_data['class_id'] : null;
			if ($class_id) {
				$crudModel = $this->crud_model ?? model('Crud_model');
				if (is_object($crudModel) && method_exists($crudModel, 'get_class_details_by_id')) {
					$classResult = $crudModel->get_class_details_by_id($class_id);
					$class_details = is_array($classResult)
						? $classResult
						: (method_exists($classResult, 'getRowArray') ? $classResult->getRowArray() : null);
				} else {
					$class_details = \db()->table('classes')->where('id', $class_id)->get()->getRowArray();
				}
			} else {
				$class_details = null;
			}

			$enrol_data['class_name'] = isset($class_details['name']) ? $class_details['name'] : '';
		}
		return $enrol_data;
	}
	//END STUDENT AND ADMISSION section


	//STUDENT OF EACH SESSION
	public function get_session_wise_student()
	{
		$checker = array(
			'session' => $this->active_session,
			'school_id' => $this->school_id
		);
		$result = \db()->table('enrols')->where($checker)->get()->getResultArray();
		return $result;
	}

	// Get User Image Starts
	public function get_user_image($user_id)
	{
		$image_path = 'uploads/users/' . $user_id . '.jpg';
		if (file_exists($image_path))
			return base_url() . $image_path . '?v=' . filemtime($image_path);
		else
			return base_url() . 'uploads/users/placeholder.jpg';
	}
	// Get User Image Ends

	// Check user duplication
	public function check_duplication($action = "", $email = "", $user_id = "")
	{
		$email = strtolower(trim($email));
		if (empty($email)) {
			return true;
		}
		
		$db = \Config\Database::connect();
		
		if ($action == 'on_create') {
			$duplicate_email_check = $db->table('users')
				->where('email', $email)
				->get();
			
			return $duplicate_email_check->getNumRows() === 0;
		} elseif ($action == 'on_update') {
			$duplicate_email_check = $db->table('users')
				->where('email', $email)
				->where('id !=', $user_id)
				->get();
			
			return $duplicate_email_check->getNumRows() === 0;
		}
		
		return true;
	}



	// Get School Image Starts
	public function get_school_image($school_id)
	{
		if (file_exists('uploads/schools/' . $school_id . '.jpg'))
			return base_url() . 'uploads/schools/' . $school_id . '.jpg';
		else
			return base_url() . 'uploads/schools/placeholder.jpg';
	}
	// Get School Image Ends

	// Get School cover Starts
	public function get_school_cover($school_id)
	{
		$cover = FCPATH . 'uploads/communityCover/' . $school_id . '.jpg';
		if (is_file($cover))
			return base_url() . 'uploads/communityCover/' . $school_id . '.jpg';
		else
			return base_url() . 'uploads/communityCover/placeholder.jpg';
	}
	// Get School Image Ends

	public function check_duplication_school($action = "", $name = "")
	{
		$name = strtolower(trim($name));
		if (empty($name)) {
			return true;
		}
		
		$db = \Config\Database::connect();
		
		try {
			if ($action == 'on_create') {
				$duplicate_name_check = $db->table('schools')
					->where('LOWER(name)', $name)
					->get();
				
				return $duplicate_name_check->getNumRows() === 0;
			}
		} catch (\Exception $e) {
			return true;
		}
		
		return true;
	}

	public function get_school_count()
	{
		return \db()->table('schools')->countAllResults();
	}

	public function get_schools($limit, $start)
	{
		$result = \db()->table('schools')->where(['status' => 1, 'Etat' => 1])->limit($limit, $start)->get();
		return $result;
	}

	public function get_schools_per_category($category, $limit, $start)
	{
		$result = \db()->table('schools')->where(['status' => 1, 'Etat' => 1, 'category' => $category])->limit($limit, $start)->get();
		return $result;
	}

	public function get_schools_per_category_count($category)
	{
		return \db()->table('schools')->where('id', $this->school_id)->countAllResults();
	}

	public function get_schools_search($input, $limit, $start)
	{
		return \db()->table('schools')
			->where('status', 1)
			->groupStart()
			->like('name', $input)
			->orLike('description', $input)
			->orLike('category', $input)
			->groupEnd()
			->limit($limit, $start)
			->get();
	}

	public function get_schools_search_count($input)
	{
		return \db()->table('schools')
			->where('status', 1)
			->groupStart()
			->like('name', $input)
			->orLike('description', $input)
			->orLike('category', $input)
			->groupEnd()
			->countAllResults();
	}



	public function get_school_details($school_id = '')
	{
		return \db()->table('schools')->where('id', $this->school_id)->get()->getRowArray();
	}



	//GET LOGGED IN USER DATA
	public function get_profile_data()
	{
		return \db()->table('users')->where('school_id', $this->school_id)->get()->getRowArray();
	}
	public function approved_school()
	{
		$response = array();
		$school_id = html_escape($this->request->getPost('school_id'));
		$admin_user = \db()->table('users')->where('school_id', $this->school_id)->get()->getRowArray();

		// Update Admin User Status
		if (!empty($admin_user['id'])) {
			\db()->table('users')
				->where('id', $admin_user['id'])
				->update(['status' => 1]);
		}
		// return $school_id;
		$data['status'] = 1;
		\db()->table('schools')
			->where('id', $school_id)
			->update($data);

		$response = array(
			'status' => true,
			'notification' => get_phrase('School_updated_successfully')
		);

		return json_encode($response);
	}
	// 	public function update_profile()
// 	{

	// 		$response = array();
// 		$user_id = session()->get('user_id');
// 		$data['name'] = htmlspecialchars($this->request->getPost('name'));
// 		$email= htmlspecialchars($this->request->getPost('email'));
// 		$data['phone'] = htmlspecialchars($this->request->getPost('phone'));
// 		$data['address'] = htmlspecialchars($this->request->getPost('address'));
// 		// print_r($email);
// 		// Check Duplication
// 		$duplication_status = $this->check_duplication('on_update', $email, $user_id);
// 		// print_r($duplication_status);
// 		if ($duplication_status) {
// 			// print_r("OK");
// 			\db()->where('id', $user_id);
// 			\db()->update('users', $data);

	// 			if (isset($FILES['profile_image']) && is_uploaded_file($FILES['profile_image']['tmp_name'])) 
// 			{
// 				$sourceLocal  = 'uploads/users/' . $user_id . '.jpg';
// 				move_uploaded_file($FILES['profile_image']['tmp_name'],$sourceLocal);
// 			}

	// 			// Par défaut, succès de Wayo
// 			$notification = get_phrase('updated_successfully');
// 			$user =\db()->table('users')->where('school_id', $this->school_id)->get()->getResult()->getRow();
// 			// if(!empty($user->humhub_id))
// 			// {
// 			// 	$nameParts = explode(' ', $data['name'], 2);
// 			// 	$firstname = $nameParts[0];
// 			// 	$lastname = isset($nameParts[1]) ? $nameParts[1] : '';

	// 			// 	$username = $this->sanitizeUsername($data['name']);

	// 			// 	$humhubData = [
// 			// 		'account' => [
// 			// 			'email'    => $email,
// 			// 			'username' => $username
// 			// 		],
// 			// 		'profile' => [
// 			// 			'firstname' => $firstname,
// 			// 			'lastname'  => $lastname
// 			// 		]
// 			// 	];
// 			// 	$humhubResponse = $this->humhub_sso->updateUser($user->humhub_id, $humhubData);
// 			// 	log_message('debug', 'Réponse HumHub updateUser depuis update_profile: ' . json_encode($humhubResponse));
// 			// 	if (isset($FILES['profile_image']) && is_uploaded_file($FILES['profile_image']['tmp_name'])) 
// 			// 	{
// 			// 				// $sourceLocal  = 'uploads/users/' . $user_id . '.jpg';
// 			// 				// move_uploaded_file($FILES['profile_image']['tmp_name'],$sourceLocal);
// 			// 				// 2. Copier vers HumHub
// 			// 						$sourceImage = FCPATH . $sourceLocal;
// 			// 						$humhubUploadsPath = 'C:/xampp/htdocs/humhub/humhub-1.17.2/uploads/profile_image/';
// 			// 						$guid = $humhubResponse['guid']; 
// 			// 						$destImageOrg = $humhubUploadsPath . $guid . '_org.jpg';
// 			// 						$destImage = $humhubUploadsPath . $guid . '.jpg';

	// 			// 					if (copy($sourceImage, $destImageOrg) && copy($sourceImage, $destImage)) {
// 			// 						log_message('debug', ' Image copiée vers HumHub avec succès.');
// 			// 					} else {
// 			// 						log_message('error', ' Erreur lors de la copie de l\'image vers HumHub.');
// 			// 					}
// 			// 					if (!$humhubResponse || isset($humhubResponse['code'])) {
// 			// 						$reason = isset($humhubResponse['message']) ? $humhubResponse['message'] : 'Erreur inconnue';
// 			// 						log_message('error', 'Échec HumHub dans update_profile pour user_id=' . $user_id . ' : ' . $reason);
// 			// 					}
// 			//     }
// 			// 	$response = array(
// 			// 		'status' => true,
// 			// 		'notification' => $notification
// 			// 	);
// 			// } else {
// 			// 	$response = array(
// 			// 		'status' => false,
// 			// 		'notification' => get_phrase('sorry_this_email_has_been_taken')
// 			// 	);
// 			// }
// 			$response = array(
// 				'status' => true,
// 				'notification' => $notification
// 			);
// 			$csrf = array(
// 			'csrfName' => $this->security->get_csrf_token_name(),
// 			'csrfHash' => $this->security->get_csrf_hash(),
// 		  );

	// 		// Renvoyer la réponse avec un nouveau jeton CSRF
// 		return json_encode(array('status' => json_encode($response), 'csrf' => $csrf));

	// 		// return json_encode($response);si j'ai fait ca il va causé une error alert n'affiche pas
// 	   }
//    }


	public function update_profile()
	{
		$response = array();

		$user_id = session()->get('user_id');
		$data['name'] = htmlspecialchars($this->request->getPost('name'));
		$email = htmlspecialchars($this->request->getPost('email'));
		$data['phone'] = htmlspecialchars($this->request->getPost('phone'));
		// $data['address'] = htmlspecialchars($this->request->getPost('address'));
		$data['Rue'] = html_escape($this->request->getPost('Street'));
		$data['Numero'] = html_escape($this->request->getPost('number'));
		$data['Ville'] = html_escape($this->request->getPost('city'));
		$data['Codepostal'] = html_escape($this->request->getPost('postal_code'));
		$data['num_vat'] = html_escape($this->request->getPost('VAT_number'));
		
		// Check Duplication
		$duplication_status = $this->check_duplication('on_update', $email, $user_id);

		if ($duplication_status) {
			\db()->table('users')->where('id', $user_id)->update($data);

			$files = $this->request->getFiles();
			// === CONTRÔLES UPLOAD (taille & extension) ===========================
			if (isset($files['profile_image']) && is_uploaded_file($files['profile_image']['tmp_name'])) {

				$MAX_SIZE_BYTES = 2 * 1024 * 1024; // 2 Mo

				$ALLOWED_EXT = array('jpg', 'jpeg', 'png');
				$ALLOWED_MIME = array('image/jpeg', 'image/png');


				$file = $files['profile_image'];

				// Erreur d'upload native PHP
				if ($file['error'] !== UPLOAD_ERR_OK) {
					$response = array('status' => false, 'notification' => 'Erreur de téléversement (code ' . $file['error'] . ').');
					$csrf = array(
						'csrfName' => $this->security->get_csrf_token_name(),
						'csrfHash' => $this->security->get_csrf_hash(),
					);
					return json_encode(array('status' => json_encode($response), 'csrf' => $csrf));
				}

				// Taille max
				if ($file['size'] > $MAX_SIZE_BYTES) {
					$response = array('status' => false, 'notification' => 'La photo est trop volumineuse (max 2 Mo).');
					$csrf = array(
						'csrfName' => $this->security->get_csrf_token_name(),
						'csrfHash' => $this->security->get_csrf_hash(),
					);
					return json_encode(array('status' => json_encode($response), 'csrf' => $csrf));
				}

				// Extension autorisée
				$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
				if (!in_array($ext, $ALLOWED_EXT, true)) {
					$response = array('status' => false, 'notification' => 'Extension non autorisée. Formats acceptés : JPG/JPEG/PNG.');
					$csrf = array(
						'csrfName' => $this->security->get_csrf_token_name(),
						'csrfHash' => $this->security->get_csrf_hash(),
					);
					return json_encode(array('status' => json_encode($response), 'csrf' => $csrf));
				}

				// MIME (sécurité)
				$finfo = finfo_open(FILEINFO_MIME_TYPE);
				$mime = finfo_file($finfo, $file['tmp_name']);

				finfo_close($finfo);
				if (!in_array($mime, $ALLOWED_MIME, true)) {
					$response = array('status' => false, 'notification' => 'Fichier invalide (type MIME incorrect).');
					$csrf = array(
						'csrfName' => $this->security->get_csrf_token_name(),
						'csrfHash' => $this->security->get_csrf_hash(),
					);
					return json_encode(array('status' => json_encode($response), 'csrf' => $csrf));
				}

				// Vérifie que c’est bien une image
				if (@getimagesize($file['tmp_name']) === false) {
					$response = array('status' => false, 'notification' => 'Le fichier n’est pas une image valide.');
					$csrf = array(
						'csrfName' => $this->security->get_csrf_token_name(),
						'csrfHash' => $this->security->get_csrf_hash(),
					);
					return json_encode(array('status' => json_encode($response), 'csrf' => $csrf));
				}

				// Destination (on garde .jpg pour compatibilité front)
				$destPath = 'uploads/users/' . $user_id . '.jpg';

				if ($mime === 'image/png' || $ext === 'png') {
					// Conversion PNG -> JPG (fond blanc pour gérer la transparence)
					$src = @imagecreatefrompng($file['tmp_name']);
					if ($src === false) {
						$response = array('status' => false, 'notification' => 'Impossible de lire l’image PNG.');
						$csrf = array(
							'csrfName' => $this->security->get_csrf_token_name(),
							'csrfHash' => $this->security->get_csrf_hash(),
						);
						return json_encode(array('status' => json_encode($response), 'csrf' => $csrf));
					}
					$w = imagesx($src);
					$h = imagesy($src);
					$bg = imagecreatetruecolor($w, $h);
					$white = imagecolorallocate($bg, 255, 255, 255);
					imagefill($bg, 0, 0, $white);
					imagealphablending($bg, true);
					imagecopy($bg, $src, 0, 0, 0, 0, $w, $h);
					$ok = imagejpeg($bg, $destPath, 90);
					imagedestroy($src);
					imagedestroy($bg);

					if (!$ok) {
						$response = array('status' => false, 'notification' => 'Échec de la conversion PNG en JPG.');
						$csrf = array(
							'csrfName' => $this->security->get_csrf_token_name(),
							'csrfHash' => $this->security->get_csrf_hash(),
						);
						return json_encode(array('status' => json_encode($response), 'csrf' => $csrf));
					}
				} else {
					// JPEG → déplacement direct
					if (!move_uploaded_file($file['tmp_name'], $destPath)) {
						$response = array('status' => false, 'notification' => 'Échec de l’enregistrement de la photo.');
						$csrf = array(
							'csrfName' => $this->security->get_csrf_token_name(),
							'csrfHash' => $this->security->get_csrf_hash(),
						);
						return json_encode(array('status' => json_encode($response), 'csrf' => $csrf));
					}
				}
			}
			// =====================================================================

			// Par défaut, succès
			$notification = get_phrase('updated_successfully');
			$user = \db()->table('users')->where('school_id', $this->school_id)->get()->getRow();

			// SYNC CHAT SERVICE
			$this->_sync_user_to_chat_service($user_id);

			// UPDATE SESSION (Nom & User Object)
			session()->set('user_name', $user->name);
			session()->set('user', $user);

			$response = array(
				'status' => true,
				'notification' => $notification
			);
			$csrf = array(
				'csrfName' => $this->security->get_csrf_token_name(),
				'csrfHash' => $this->security->get_csrf_hash(),
			);

			// Renvoyer la réponse avec un nouveau jeton CSRF
			return json_encode(array('status' => json_encode($response), 'csrf' => $csrf));
			// return json_encode($response); // resté comme noté, ça cassait ton alert
		}
	}

	public function update_teacher_status($user_id = '', $status = '')
	{
		$user_id = (int) $user_id;
		$status = ((int) $status) === 1 ? 1 : 0;
		if ($user_id > 0) {
			\db()->table('users')->where('id', $user_id)->update(['status' => $status]);
			return array(
				'status' => true,
				'notification' => get_phrase('status_updated_successfully')
			);
		}
		return array(
			'status' => false,
			'notification' => get_phrase('failed_to_update_status')
		);
	}


	public function update_password($current_password, $new_password, $confirm_password)
	{
		$user_id = session()->get('user_id');
		
		if (!empty($current_password) && !empty($new_password) && !empty($confirm_password)) {
			$user_details = $this->get_user_details($user_id);

			$stored_hash = (string) ($user_details['password'] ?? '');
			$is_legacy_sha1 = (bool) preg_match('/^[a-f0-9]{40}$/i', $stored_hash);
			$is_valid_current_password = $is_legacy_sha1
				? hash_equals(strtolower($stored_hash), sha1($current_password))
				: password_verify($current_password, $stored_hash);

			if (!$is_valid_current_password) {
				$response = array(
					'status' => false,
					'field' => 'current_password',
					'notification' => get_phrase('current_password_is_incorrect')
				);
			} elseif (strlen($new_password) < 8) {
				$response = array(
					'status' => false,
					'field' => 'new_password',
					'notification' => get_phrase('password_must_be_at_least_8_characters')
				);
			} elseif ($new_password != $confirm_password) {
				$response = array(
					'status' => false,
					'field' => 'confirm_password',
					'notification' => get_phrase('passwords_do_not_match')
				);
			} else {
				$data['password'] = password_hash((string) $new_password, PASSWORD_DEFAULT);
				\db()->table('users')->where('id', $user_id)->update($data);

				$response = array(
					'status' => true,
					'notification' => get_phrase('password_updated_successfully')
				);
			}
		} else {
			$response = array(
				'status' => false,
				'notification' => get_phrase('password_can_not_be_empty')
			);
		}
		return json_encode($response);
	}

	//GET LOGGED IN USERS CLASS ID AND SECTION ID (FOR STUDENT LOGGED IN VIEW)
	public function get_logged_in_student_details($school_id = null)
	{
		$user_id = session()->get('user_id');
		$target_school_id = $school_id ?? $this->school_id;
		$student_data = \db()->table('students')
			->where('user_id', $user_id)
			->where('school_id', $target_school_id)
			->get()
			->getRowArray();

		// Fallback: some older records may not have school_id populated consistently.
		if (empty($student_data)) {
			$student_data = \db()->table('students')
				->where('user_id', $user_id)
				->get()
				->getRowArray();
		}

		if (empty($student_data) || !isset($student_data['user_id'])) {
			return [];
		}

		$student_details = $this->get_student_details_by_id('student', $student_data['user_id']);
		return $student_details;
	}

	// GET STUDENT LIST BY PARENT
	public function get_student_list_of_logged_in_parent()
	{
		$parent_id = session()->get('user_id');
		$parent_data = \db()->table('parents')->where('school_id', $this->school_id)->get()->getRowArray();
		$checker = array(
			'parent_id' => $parent_data['id'],
			'session' => $this->active_session,
			'school_id' => $this->school_id
		);
		$students = \db()->table('students')->where($checker)->get()->getResultArray();
		foreach ($students as $key => $student) {
			$checker = array(
				'student_id' => $student['id'],
				'session' => $this->active_session,
				'school_id' => $this->school_id
			);
			$enrol_data = \db()->table('enrols')->where($checker)->get()->getRowArray();

			$user_details = \db()->table('users')->where('school_id', $this->school_id)->get()->getRowArray();
			$students[$key]['student_id'] = $student['id'];
			$students[$key]['name'] = $user_details['name'];
			$students[$key]['email'] = $user_details['email'];
			$students[$key]['role'] = $user_details['role'];
			$students[$key]['address'] = $user_details['address'];
			$students[$key]['phone'] = $user_details['phone'];
			$students[$key]['birthday'] = $user_details['birthday'];
			$students[$key]['gender'] = $user_details['gender'];
			$students[$key]['class_id'] = $enrol_data['class_id'];


			$crudModel = $this->crud_model ?? model('Crud_model');
			if (is_object($crudModel) && method_exists($crudModel, 'get_class_details_by_id')) {
				$classResult = $crudModel->get_class_details_by_id($enrol_data['class_id']);
				$class_details = is_array($classResult)
					? $classResult
					: (method_exists($classResult, 'getRowArray') ? $classResult->getRowArray() : []);
			} else {
				$class_details = \db()->table('classes')->where('id', $enrol_data['class_id'])->get()->getRowArray();
			}

			$students[$key]['class_name'] = $class_details['name'];
		}
		return $students;
	}

	// In Array for associative array
	function is_in_array($associative_array = array(), $look_up_key = "", $look_up_value = "")
	{
		foreach ($associative_array as $associative) {
			$keys = array_keys($associative);
			for ($i = 0; $i < count($keys); $i++) {
				if ($keys[$i] == $look_up_key) {
					if ($associative[$look_up_key] == $look_up_value) {
						return true;
					}
				}
			}
		}
		return false;
	}

	function get_all_teachers($user_id = "")
	{
		$builder = \db()->table('users');

		if ($user_id > 0) {
			$builder->where('id', $user_id);
		}

		$builder->where('school_id', $this->school_id);
		$builder->where("(role='superadmin' OR role='admin' OR role='teacher')");
		return $builder->get();
	}
	function get_all_users($user_id = "")
	{
		$builder = \db()->table('users');

		if ($user_id > 0) {
			$builder->where('id', $user_id);
		}

		$builder->where('school_id', $this->school_id);
		return $builder->get();
	}


	public function get_school_id($school_name)
	{
		return \db()->table('schools')->where('id', $this->school_id)->get()->getRowArray()['id'];
	}

	public function googleAPI()
	{ {
			$api = '';
			return $api;
		}
	}


	public function get_school_students_count($school_id)
	{
		return \db()->table('users')
			->where([
				'status' => 1,
				'school_id' => $school_id,
				'role' => 'student'
			])
			->get()->numRows();
	}
	public function get_community_students_count($school_id)
	{
		return \db()->table('students')
			->where('school_id', $school_id)
			->where('status', 1)
			->countAllResults();
	}

	public function get_school_teachers_count($school_id)
	{
		return \db()->table('teachers')
			->where('school_id', $school_id)
			->countAllResults();
	}

	public function get_all_schools_with_counts()
	{
		return \db()->table('schools')->countAllResults();
	}

	public function get_all_classes_with_counts()
	{
		return \db()->table('classes')->countAllResults();
	}

	public function get_creator_by_school($school_id)
{
    return \db()->table('users')
                    ->select('name')
                    ->where('school_id', $school_id)
                    ->get()
                    ->getRowArray();
}




	public function get_school_admin($school_id)
	{
		return \db()->table('users')
			->where([
				'school_id' => $school_id,
				'role' => 'admin'
			])
			->get()->getRowArray();
	}

	public function get_school_admin_image($school_id)
	{
		$admin = get_school_admin($school_id);

		if (file_exists('uploads/users/' . $admin["id"] . '.jpg'))
			return base_url() . 'uploads/users/' . $admin["id"] . '.jpg';
		else
			return base_url() . 'uploads/schools/placeholder.jpg';
	}


	public function join_school($school_id, $data_invoice = array())
	{
	
		if (session()->get('user_id') == null || session()->get('user_id') == "") {
			session()->setFlashdata('error', get_phrase('please_login_before_continuing'));
			$referer = $this->request->getServer('HTTP_REFERER');
			if (!empty($referer)) {
				return redirect()->to($referer);
			}
		} else
			$user_id = session()->get('user_id');

		if ($school_id == null || $school_id == "") {

			session()->setFlashdata('error', get_phrase('no_school_found'));
			$referer = $this->request->getServer('HTTP_REFERER');
			if (!empty($referer)) {
				return redirect()->to($referer);
			}
		} else {

			// Check if already joined - Prioritize approved status
			// First, try to find an approved student record
			$existing_student = \db()->table('students')
				->where('user_id', $user_id)
				->where('school_id', $school_id)
				->where('status', 1)
				->get()
				->getRowArray();

			// If not found, look for any record (pending)
			if (empty($existing_student)) {
				$existing_student = \db()->table('students')
					->where('user_id', $user_id)
					->where('school_id', $school_id)
					->get()
					->getRowArray();
			}
			
			log_message('error', 'join_school debug: user_id='.$user_id.' school_id='.$school_id.' existing_student='.json_encode($existing_student));

			if (!empty($existing_student)) {
				
				// Cas Spécial : Mise à jour du statut pour accès gratuit/public si l'utilisateur était déjà en attente
				// Ou si c'est une première inscription gratuite
				if (!empty($data_invoice) && isset($data_invoice['payment_method']) && $data_invoice['payment_method'] == 'free_access') {
					
					// Vérifier si l'école est publique pour forcer le statut 1
					$query_school = \db()->table('schools')->where('id', $school_id)->get();
					if ($query_school->getNumRows() > 0) {
						$row_school = $query_school->getRow();
						if ($row_school->access == 0) {
							\db()->table('students')->where('id', $existing_student['id'])->update(['status' => 1]);
							$existing_student['status'] = 1; 
							log_message('info', 'Student auto-approved for free public community (existing record updated).');
						}
					}
				}

				// Only handle redirects if this is NOT a payment confirmation call
				if (empty($data_invoice)) {
					if ($existing_student['status'] == 1) {
						// Already approved? Switch session!
						session()->set('active_school_id', $school_id);
						session()->set('school_id', $school_id);
						session()->set('role', 'student');
						session()->set('user_type', 'student');

						// Ensure user_schools is up to date just in case
						$user_school_check = \db()->table('user_schools')
							->where('user_id', $user_id)
							->where('school_id', $school_id)
							->get()
							->getRow();
						if ($user_school_check) {
							\db()->table('user_schools')->where('id', $user_school_check->id)->update(['role' => 'student']);
						} else {
							// Insert missing user_schools entry if it doesn't exist
							\db()->table('user_schools')->insert([
								'user_id' => $user_id,
								'school_id' => $school_id,
								'role' => 'student'
							]);
						}

						session()->setFlashdata('success', get_phrase('welcome_back_to_the_community'));
						return redirect()->to(site_url('app/dashboard'));
					} else {
						// Still pending - Switch session anyway to allow restricted access (grayed out menu)
						session()->set('active_school_id', $school_id);
						session()->set('school_id', $school_id);
						session()->set('role', 'student');
						session()->set('user_type', 'student');

						// Ensure user_schools is up to date just in case (for pending users too)
						$user_school_check = \db()->table('user_schools')
							->where('user_id', $user_id)
							->where('school_id', $school_id)
							->get()
							->getRow();
						if ($user_school_check) {
							\db()->table('user_schools')->where('id', $user_school_check->id)->update(['role' => 'student']);
						} else {
							\db()->table('user_schools')->insert([
								'user_id' => $user_id,
								'school_id' => $school_id,
								'role' => 'student'
							]);
						}

						// Redirect to invoice page where the message will be shown
						return redirect()->to(site_url('student/invoice'));
					}
				} else {
					// Pre-fill data for payment processing context (Existing Student)
					$row = \db()->table('schools')->where('id', $school_id)->get()->getRow();
					$data['code'] = $existing_student['code'];
					$data['status'] = $existing_student['status']; // Important: Transmettre le statut à jour
				}
			} else {

				$student_row = \db()->table('students')->where('school_id', $school_id)->get()->getRowArray();
				$student_code = !empty($student_row) ? $student_row['code'] : student_code();

				$data['school_id'] = $school_id;
				$data['user_id'] = $user_id;
				$data['code'] = $student_code;
				$data['session'] = $this->active_session;
				$query = \db()->table('schools')->where('id', $school_id)->get();

				if ($query->getNumRows() > 0) {
					$row = $query->getRow();

					// 0 = Public, 1 = Private (Convention inversée ou spécifique à vérifier)
					// Dans community_details.php : if ($school["access"] > 0) { Private } else { Public }
					// Donc access == 0 => Public => Status = 1 (Approved)
					if ($row->access == 0) {
						$data['status'] = 1; // Public -> Approved directly
					} else {
						$data['status'] = 0; // Private -> Pending
					}
				}

		\db()->table('students')->insert($data);
			
		// Ensure user_schools contains this user/community mapping
		$user_school_row = \db()->table('user_schools')
			->where('user_id', $user_id)
			->where('school_id', $school_id)
			->get()
			->getRowArray();
		if (!empty($user_school_row)) {
			\db()->table('user_schools')->where('id', $user_school_row['id'])->update([
				'role' => 'student'
			]);
		} else {
			\db()->table('user_schools')->insert([
				'user_id' => $user_id,
				'school_id' => $school_id,
				'role' => 'student'
			]);
		}
		
		// Mettre à jour le school_id de l'utilisateur s'il est null
		// avec le school_id de user_schools
		$user_schools_row = \db()->table('user_schools')
			->where('user_id', $user_id)
			->where('school_id', $school_id)
			->get()
			->getRow();
		
		if ($user_schools_row) {
			\db()->table('users')->where('id', $user_id)->update(['school_id' => $school_id]);
		}
		
		// -------------------------------------------------------------------------
		// PROCESS INVOICE AND EMAILS BEFORE REDIRECTION
		// -------------------------------------------------------------------------
		$user_row = \db()->table('users')->where('id', $user_id)->get()->getRowArray();
		$user_email = $user_row['email'] ?? '';
		$user_name = $user_row['name'] ?? '';
		$admin_row = \db()->table('users')
			->where('school_id', $school_id)
			->whereIn('role', array('admin', 'superadmin'))
			->get()
			->getRowArray();
		$user_email_admin = $admin_row['email'] ?? '';

		if (!empty($data_invoice) && !empty($data_invoice['invoice_id'])) {
			$invoice_details = \db()->table('invoices')->where('id', $data_invoice['invoice_id'])->get()->getRowArray();

			if (!empty($invoice_details)) {
				$paid_amount = isset($data_invoice['amount_paid']) ? (float)$data_invoice['amount_paid'] : 0;
				$invoice_amount_paid = isset($invoice_details['amount_paid']) ? (float)$invoice_details['amount_paid'] : 0;
				$invoice_total = (float)$invoice_details['total_amount'];
				$due_amount = $invoice_total - $invoice_amount_paid;
				
				// Si une conversion de devise a été appliquée, utiliser le montant original
				$amount_to_compare = $paid_amount;
				if (isset($data_invoice['conversion_applied']) && $data_invoice['conversion_applied'] && isset($data_invoice['original_amount'])) {
					$amount_to_compare = (float)$data_invoice['original_amount'];
				}
				
				// Tolérance de 0.01 pour les erreurs d'arrondi
				$is_full_payment = abs($due_amount - $amount_to_compare) < 0.01;
				
				log_message('debug', "Community payment check: due={$due_amount}, compare={$amount_to_compare}, is_full=" . ($is_full_payment ? 'true' : 'false'));
				
				if ($is_full_payment) {
					$updater = array(
						'status' => 'paid',
						'payment_method' => isset($data_invoice['payment_method']) ? $data_invoice['payment_method'] : $invoice_details['payment_method'],
						'paid_amount' => $invoice_total, // Marquer comme totalement payé
						'currency' => isset($data_invoice['original_currency']) ? $data_invoice['original_currency'] : (isset($data_invoice['currency']) ? $data_invoice['currency'] : $invoice_details['currency']),
						'payment_type' => isset($data_invoice['payment_type']) ? $data_invoice['payment_type'] : $invoice_details['payment_type'],
						'vat_amount' => isset($data_invoice['vat_amount']) ? $data_invoice['vat_amount'] : $invoice_details['vat_amount'],
						'vat_rate' => isset($data_invoice['vat_rate']) ? $data_invoice['vat_rate'] : $invoice_details['vat_rate'],
						'sub_total' => isset($data_invoice['sub_total']) ? $data_invoice['sub_total'] : $invoice_details['sub_total'],
						'total_amount' => $invoice_total,
						'updated_at'  => strtotime(date('d-M-Y'))
					);
					
					// Ajouter les infos de conversion FX si présentes
					if (isset($data_invoice['conversion_applied']) && $data_invoice['conversion_applied']) {
						$updater['payment_currency'] = isset($data_invoice['currency']) ? $data_invoice['currency'] : null;
						$updater['payment_amount_converted'] = $paid_amount;
						$updater['fx_rate'] = isset($data_invoice['fx_rate']) ? $data_invoice['fx_rate'] : null;
						$updater['fx_rate_date'] = isset($data_invoice['fx_rate_date']) ? $data_invoice['fx_rate_date'] : null;
						$updater['conversion_applied'] = 1;
					}
					
					\db()->table('invoices')->where('id', $data_invoice['invoice_id'])->update($updater);
					
					// Approve the student in the school
					\db()->table('students')
						->where('user_id', $user_id)
						->where('school_id', $school_id)
						->update(array('status' => 1));

					// Update local data status for the redirection logic at the end of function
					$data['status'] = 1;

					log_message('info', "Community invoice #{$data_invoice['invoice_id']} marked as paid");
				}
			}
		}

		$this->email_model->join_student_email($user_name, $user_email, $data['code'], $row->name, $school_id);
		$this->email_model->join_student_email_for_admin($user_name, $user_email_admin, $data['code'], $row->name, $school_id);
		// -------------------------------------------------------------------------
		
		// Check if the student status is pending (0) or approved (1)
		// If pending, do NOT show popup, just switch session to allow restricted access
		if (isset($data['status']) && $data['status'] == 0) {
			// Update session to switch to the new school (pending state)
			session()->set('active_school_id', $school_id);
			session()->set('school_id', $school_id);
			session()->set('role', 'student');
			session()->set('user_type', 'student');
			
			session()->setFlashdata('info', get_phrase('request_sent_waiting_for_approval'));
			return redirect()->to(site_url('student/invoice'));
		} else {
			// Update session to immediately switch to the new school ONLY if approved
			session()->set('active_school_id', $school_id);
			session()->set('school_id', $school_id);
			session()->set('role', 'student');
			session()->set('user_type', 'student');

			session()->setFlashdata('success', get_phrase('welcome_to_the_community'));
			return redirect()->to(site_url('app/dashboard'));
		}

				// if (isset($_SERVER['HTTP_REFERER'])) {
				// 	redirect($_SERVER['HTTP_REFERER'], 'refresh');
				// }
			}
		}
	}



	public function check_student_status($school_id)
	{
		$user_id = (int) (session()->get('user_id') ?? 0);
		$school_id = (int) $school_id;
		if ($user_id <= 0 || $school_id <= 0) {
			return -1;
		}

		$student_data = \db()->table('students')
			->where('school_id', $school_id)
			->where('user_id', $user_id)
			->get()
			->getRowArray();

		if (!$student_data) {
			return -1;
		}

		$status = (int) ($student_data['status'] ?? -1);
		if ($status === 1) {
			return 1;
		}
		if ($status === 0) {
			return 0;
		}
		return -1;
	}


	public function register_user()
	{
		$emailPattern = '/^[^\s@]+@[^\s@]+\.[^\s@]+$/';


		if ($this->request->getPost('register_email') == '' || !preg_match($emailPattern, $this->request->getPost('register_email')) || $this->request->getPost('register_password') == '' || $this->request->getPost('register_first_name') == '' || $this->request->getPost('register_last_name') == '' || $this->request->getPost('register_date_of_birth') == '' || $this->request->getPost('register_repeat_password') == '') {

			session()->setFlashdata('error', get_phrase('validation_error'));
			$referer = $this->request->getServer('HTTP_REFERER');
			if (!empty($referer)) {
				redirect($referer);
			}

		} else if (\db()->table('users')->where('school_id', $this->school_id)->countAllResults() > 0) {

			session()->setFlashdata('error', get_phrase('email_already_exists'));
			$referer = $this->request->getServer('HTTP_REFERER');
			if (!empty($referer)) {
				redirect($referer);
			}

		} else {
			$passwordValidation = $this->validatePasswordComplexity($this->request->getPost('register_password'));
			if (!$passwordValidation['valid']) {
				session()->setFlashdata('error', implode('<br>', $passwordValidation['errors']));
				$referer = $this->request->getServer('HTTP_REFERER');
				if (!empty($referer)) {
					redirect($referer);
				}
			}

			$password = $this->request->getPost('register_password');
			$repeatPassword = $this->request->getPost('register_repeat_password');
			if ($password !== $repeatPassword) {
				session()->setFlashdata('error', get_phrase('passwords_do_not_match'));
				$referer = $this->request->getServer('HTTP_REFERER');
				if (!empty($referer)) {
					redirect($referer);
				}
			}

			$data['name'] = htmlspecialchars($this->request->getPost('register_first_name') . ' ' . $this->request->getPost('register_last_name'));
			$data['email'] = htmlspecialchars($this->request->getPost('register_email'));
			$data['birthday'] = htmlspecialchars($this->request->getPost('register_date_of_birth'));
			$data['gender'] = htmlspecialchars($this->request->getPost('register_gender'));
			$data['password'] = password_hash($this->request->getPost('register_password'), PASSWORD_DEFAULT);
			$data['role'] = 'student';
			$data['status'] = 1;
			$data['school_id'] = null;
			$data['watch_history'] = '[]';

			\db()->table('users')->insert($data);
			$user_id = \db()->insertID();


			$files = $this->request->getFiles();
			if (isset($files['student_image_upload']) && $files['student_image_upload']['error'] == UPLOAD_ERR_OK) {
				$upload_path = 'uploads/users/' . $user_id . '.jpg';
				move_uploaded_file($files['student_image_upload']['tmp_name'], $upload_path);
			}
			$this->email_model->Add_online_admission($data['email'], $user_id, $data['name']);
			session()->set('user_login_type', true);
			session()->set('student_login', true);
			session()->set('user_id', $user_id);
			session()->set('school_id', null);
			session()->set('user_name', $data['name']);
			session()->set('user_type', 'student');
			session()->setFlashdata('success', get_phrase('registration_successful'));
		}

		$referer = $this->request->getServer('HTTP_REFERER');
		if (!empty($referer)) {
			redirect($referer);
		}
	}





	public function register_user_form()
	{
		$emailPattern = '/^[^\s@]+@[^\s@]+\.[^\s@]+$/';
		$plainPassword = $this->request->getPost('password-student');
		
		// Valider les champs requis
		if (
			$this->request->getPost('student_email') == '' ||
			!preg_match($emailPattern, $this->request->getPost('student_email')) ||
			$plainPassword == '' ||
			$this->request->getPost('first_name') == '' ||
			$this->request->getPost('last_name') == '' ||
			$this->request->getPost('date_of_birth') == '' ||
			$this->request->getPost('repeat-password-student') == ''
		) {
			$audit_log_model = new \App\Models\Audit_log_model();
			$audit_log_model->log_failed_registration_student([
				'name' => $this->request->getPost('first_name') . ' ' . $this->request->getPost('last_name'),
				'email' => $this->request->getPost('student_email'),
				'birthday' => $this->request->getPost('date_of_birth')
			], 'validation_error');
			
			return json_encode([
				'status' => false,
				'message' => get_phrase('validation_error'),
				'csrf' => [
					'csrfName' => csrf_token(),
					'csrfHash' => csrf_hash()
				]
			]);
		}

		$email = strtolower(trim((string) $this->request->getPost('student_email')));
		if (!$this->check_duplication('on_create', $email)) {
			$audit_log_model = new \App\Models\Audit_log_model();
			$audit_log_model->log_failed_registration_student([
				'name' => $this->request->getPost('first_name') . ' ' . $this->request->getPost('last_name'),
				'email' => $this->request->getPost('student_email'),
				'birthday' => $this->request->getPost('date_of_birth')
			], 'email_already_exists');
			
			return json_encode([
				'status' => false,
				'message' => get_phrase('email_already_exists'),
				'csrf' => [
					'csrfName' => csrf_token(),
					'csrfHash' => csrf_hash()
				]
			]);
		}

		$data = [
			'name' => html_entity_decode(htmlspecialchars($this->request->getPost('first_name') . ' ' . $this->request->getPost('last_name'))),
			'email' => $email,
			'birthday' => (string) $this->request->getPost('date_of_birth'),
			'password' => password_hash($plainPassword, PASSWORD_DEFAULT),
			'role' => 'student',
			'status' => 1,
			'watch_history' => '[]'
		];
		$columns = \Config\Database::connect()->getFieldNames('users');
		$data = array_intersect_key($data, array_flip($columns));

		\db()->table('users')->insert($data);
		$user_id = \db()->insertID();
		try {
			$this->email_model->Add_online_admission($data['email'], $user_id, $data['name']);
		} catch (\Throwable $e) {
			log_message('error', 'Member registration email failed: ' . $e->getMessage());
		}

		try {
			$audit_log_model = new \App\Models\Audit_log_model();
			$audit_log_model->log_online_admission_student($user_id, [
				'name' => $data['name'],
				'email' => $data['email'],
				'birthday' => $data['birthday'],
				'language' => get_user_language()
			]);
		} catch (\Throwable $e) {
			log_message('error', 'Member registration audit failed: ' . $e->getMessage());
		}

		// Auto-login
		session()->set([
			'user_login_type' => true,
			'student_login' => true,
			'user_id' => $user_id,
			'user_name' => $data['name'],
			'user_type' => 'student',
			'is_logged_in' => true
		]);

		// Réponse JSON pour succès
		return json_encode([
			'status' => true,
			'message' => get_phrase('registration_successful'),
			'csrf' => [
				'csrfName' => csrf_token(),
				'csrfHash' => csrf_hash()
			]
		]);
	}

	public function get_schools_count()
	{
		return \db()->table('schools')
			->where('status', 1)
			->where('Etat', 1)
			->countAllResults();
	}


      public function count_schools_by_category($category)
	{
		return \db()->table('schools')
			->where('category', $category)
			->where('status', 1)
			->where('Etat', 1)
			->countAllResults();
	}
private function _sync_user_to_chat_service($user_id)
	{
		// Récupérer les données fraîches de l'utilisateur
		$user = \db()->table('users')->where('school_id', $this->school_id)->get()->getRow();
		if (!$user) return;

		// Construire l'URL de l'avatar
		$avatar_path = 'uploads/users/' . $user->id . '.jpg';
		$avatar_url = null;
		if (file_exists($avatar_path)) {
			$avatar_url = base_url($avatar_path);
		}

		$payload = [
			'user_id' => $user->id,
			'email' => $user->email,
			'name' => $user->name,
			'gender' => $user->gender, // Peut être null
			'avatar' => $avatar_url
		];

		// URL du Chat Service (hardcodé comme ailleurs dans le projet)
		$url = 'https://wayochat.wayo.ac/api/auth/sync-user';

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
		curl_setopt($ch, CURLOPT_TIMEOUT, 2); // Timeout court pour ne pas bloquer l'UI
		
		$response = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		
		if (curl_errno($ch)) {
			log_message('error', 'Chat Sync Error: ' . curl_error($ch));
		} else {
			if ($http_code >= 400) {
				log_message('error', 'Chat Sync Failed (' . $http_code . '): ' . $response);
			} else {
				log_message('info', 'Chat Sync Success for user ' . $user_id);
			}
		}
		
		curl_close($ch);
	}

  public function get_all_admins_count()
	{
		return \db()->table('users')
			->where('role', 'admin')
			->where('status', 1)
			->countAllResults();

	}

	public function get_user_by_credentials($credential) {
		return \db()->table('users')->where($credential)->get();
	}

	public function get_user_by_email($email) {
		return \db()->table('users')->where('school_id', $this->school_id)->get()->getRowArray();
	}

	public function get_user_by_id($user_id) {
		return \db()->table('users')->where('school_id', $this->school_id)->get()->getRowArray();
	}

	public function get_user_by_reset_token($token) {
		return \db()->table('users')->where('school_id', $this->school_id)->get()->getRowArray();
	}

	public function get_user_by_email_password($email, $password) {
		return \db()->table('users')->where('school_id', $this->school_id)->get()->getRowArray();
	}

	public function update_user($user_id, $data) {
		return \db()->table('users')
			->where('id', $user_id)
			->update($data);
	}

	public function get_school_by_name($school_name) {
		return \db()->table('schools')->where('id', $this->school_id)->get()->getRowArray();
	}

	public function get_student_trip_details($filter_child_id) {
		return \db()->table('trips')
			->select('trips.*, assign_students.student_id')
			->join('assign_students', 'trips.driver_id = assign_students.driver_id')
			->where('trips.status', 1)
			->where('assign_students.student_id', $filter_child_id)
			->get()
			->getRowArray();
	}

	public function get_trip_position($trip_id) {
		return \db()->table('trips')->where(['id' => $trip_id, 'status' => 1])->get()->getRowArray();
	}

	public function get_row($table, $where) {
		return \db()->table($table)->where($where)->get()->getRowArray();
	}

	public function get_where_result($table, $where, $select = '*') {
		return \db()->table($table)->select($select)->where($where)->get()->getResultArray();
	}

	public function insert_data($table, $data) {
		\db()->table($table)->insert($data);
		return \db()->insertID();
	}

	public function update_data($table, $where, $data) {
		return \db()->table($table)->where($where)->update($data);
	}

	public function delete_data($table, $where) {
		return \db()->table($table)->where($where)->delete();
	}

	public function count_all($table, $where = []) {
		$builder = \db()->table($table);
		if (!empty($where)) {
			$builder->where($where);
		}
		return $builder->countAllResults();
	}

	private function validatePasswordComplexity(string $password): array
	{
		$errors = [];
		$minLength = 8;
		$hasUpperCase = preg_match('/[A-Z]/', $password);
		$hasLowerCase = preg_match('/[a-z]/', $password);
		$hasDigit = preg_match('/[0-9]/', $password);
		$hasSpecialChar = preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password);

		if (strlen($password) < $minLength) {
			$errors[] = get_phrase('password_must_be_at_least') . ' ' . $minLength . ' ' . get_phrase('characters');
		}

		if (!$hasUpperCase) {
			$errors[] = get_phrase('password_must_contain_at_least_one_uppercase_letter');
		}

		if (!$hasLowerCase) {
			$errors[] = get_phrase('password_must_contain_at_least_one_lowercase_letter');
		}

		if (!$hasDigit) {
			$errors[] = get_phrase('password_must_contain_at_least_one_digit');
		}

		if (!$hasSpecialChar) {
			$errors[] = get_phrase('password_must_contain_at_least_one_special_character');
		}

		return [
			'valid' => empty($errors),
			'errors' => $errors
		];
	}

}
