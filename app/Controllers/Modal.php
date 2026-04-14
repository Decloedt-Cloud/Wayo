<?php

namespace App\Controllers;


/*
*  @author   : Creativeitem
*  date      : November, 2019
*  Ekattor School Management System With Addons
*  http://codecanyon.net/user/Creativeitem
*  http://support.creativeitem.com
*/

class Modal extends BaseController
{
	protected $models = [
		'Crud_model' => 'crud_model',
		'User_model' => 'user_model',
		'Settings_model' => 'settings_model',
		'Payment_model' => 'payment_model',
		'Email_model' => 'email_model',
		'Addon_model' => 'addon_model',
		'Frontend_model' => 'frontend_model',
		'addons/Lms_model' => 'lms_model',
	];

	public function __construct()
	{
		timezone();
	}

	function popup($folder_name = '', $page_name = '' , $param1 = '' , $param2 = '', $param3 = '' , $param4 = '' , $param5 = '', $param6 = '', $param7 = '', $param8 = '')
	{
		$page_data['param1']		=	$param1;
		$page_data['param2']		=	$param2;
		$page_data['param3']		=	$param3;
		$page_data['param4']		=	$param4;
		$page_data['param5']		=	$param5;
		$page_data['param6']		=	$param6; // VAT applicable (0/1)
		$page_data['param7']		=	$param7; // VAT rate
		$page_data['param8']		=	$param8; // Sub total (HT)

		// Prepare exam modal data in controller to keep views presentation-only.
		if ($folder_name == 'exam' && in_array($page_name, ['create', 'edit'], true)) {
			$school_id = (int) (session()->get('active_school_id') ?? 0);
			if ($school_id <= 0) {
				$school_id = (int) school_id();
			}
			if ($school_id <= 0) {
				$school_id = (int) (session()->get('school_id') ?? 0);
			}
			if ($school_id <= 0) {
				$currentUserId = (int) (session()->get('user_id') ?? 0);
				if ($currentUserId > 0) {
					$userRow = $this->db->table('users')->select('school_id')->where('id', $currentUserId)->get()->getRowArray();
					$school_id = (int) ($userRow['school_id'] ?? 0);
				}
			}
			if ($school_id <= 0) {
				$school_id = (int) get_settings('school_id');
			}
			$user_type = (string) (session()->get('user_type') ?? '');
			if ($user_type === '') {
				$user_type = (int) session()->get('teacher_login') === 1 ? 'teacher' : ((int) session()->get('admin_login') === 1 ? 'admin' : '');
			}
			$classes_builder = $this->db->table('classes');
			if ($school_id > 0) {
				$classes_builder->where('school_id', $school_id);
			} else {
				$classes_builder->where('id', 0);
			}

			if ($user_type === 'teacher') {
				$user_id = (int) session()->get('user_id');
				$permitted_class_ids = [];
				$teacherRows = [];
				if ($user_id > 0 && $school_id > 0) {
					$teacherRows = $this->db->table('teachers')
						->select('id')
						->where('user_id', $user_id)
						->where('school_id', $school_id)
						->get()
						->getResultArray();
				}
				if (empty($teacherRows) && $user_id > 0) {
					$teacherRows = $this->db->table('teachers')
						->select('id')
						->where('user_id', $user_id)
						->get()
						->getResultArray();
				}
				$teacher_ids = array_values(array_unique(array_map('intval', array_column($teacherRows, 'id'))));

				if (!empty($teacher_ids)) {
					$permitted_classes = $this->db->table('teacher_permissions')
						->select('class_id')
						->whereIn('teacher_id', $teacher_ids)
						->where('attendance', 1)
						->get()
						->getResultArray();
					$permitted_class_ids = array_values(array_unique(array_map('intval', array_column($permitted_classes, 'class_id'))));
				}

				if (!empty($permitted_class_ids)) {
					$classes_builder->whereIn('id', $permitted_class_ids);
				} else {
					$classes_builder->where('id', 0);
				}
			}

			$page_data['modal_exam_classes'] = $classes_builder->get()->getResultArray();

			if ($page_name === 'edit' && !empty($param1)) {
				$page_data['modal_exam'] = $this->db->table('exams')->where('id', $param1)->get()->getRowArray();
			}
		}

		if ($folder_name == 'student' && in_array($page_name, ['profile', 'update'], true)) {
			$student_id = (int) $param1;
			$page_data['student_id'] = $student_id;
			$page_data['modal_student'] = [];
			$page_data['modal_student_user'] = [];
			$page_data['modal_student_enrols'] = [];
			$page_data['modal_student_class_names'] = [];
			$page_data['modal_student_not_found'] = true;
			$page_data['modal_student_classes'] = [];
			$page_data['modal_student_selected_class_ids'] = [];
			$page_data['modal_student_enroll'] = [];

			if ($student_id > 0) {
				$student = $this->db->table('students')->where('id', $student_id)->get()->getRowArray();
				if (!empty($student)) {
					$page_data['modal_student'] = $student;
					$page_data['modal_student_not_found'] = false;
					$page_data['modal_student_user'] = $this->db->table('users')->where('id', (int) $student['user_id'])->get()->getRowArray() ?? [];
					$enrols = $this->db->table('enrols')->where('student_id', $student_id)->get()->getResultArray();
					$page_data['modal_student_enrols'] = $enrols;
					$page_data['modal_student_enroll'] = $enrols[0] ?? [];
					$page_data['modal_student_selected_class_ids'] = array_values(array_unique(array_map('intval', array_column($enrols, 'class_id'))));

					if (!empty($page_data['modal_student_selected_class_ids'])) {
						$class_rows = $this->db->table('classes')
							->select('id, name')
							->whereIn('id', $page_data['modal_student_selected_class_ids'])
							->get()
							->getResultArray();
						foreach ($class_rows as $class_row) {
							$page_data['modal_student_class_names'][(int) $class_row['id']] = (string) $class_row['name'];
						}
					}

					$school_id = (int) ($student['school_id'] ?? 0);
					if ($school_id <= 0) {
						$school_id = (int) school_id();
					}
					if ($school_id > 0) {
						$page_data['modal_student_classes'] = $this->db->table('classes')->where('school_id', $school_id)->get()->getResultArray();
					}
				}
			}
		}

		if ($folder_name == 'attendance' && $page_name == 'take_attendance') {
			$user_type = (string) (session()->get('user_type') ?? '');
			$attendance_school_id = (int) (session()->get('active_school_id') ?? 0);
			if ($attendance_school_id <= 0) {
				$page_data['attendance_school_id'] = 0;
				$page_data['modal_attendance_classes'] = [];
				echo view('backend/'.$this->active_user.'/modal/'.$folder_name.'/'.$page_name, $page_data);
				return;
			}

			$modal_attendance_classes = [];
			if ($user_type === 'teacher') {
				$user_id = (int) (session()->get('user_id') ?? 0);
				$teacherRow = $this->db->table('teachers')
					->select('id')
					->where('user_id', $user_id)
					->where('school_id', $attendance_school_id)
					->get()
					->getRowArray();

				$teacher_id = (int) ($teacherRow['id'] ?? 0);
				$permitted_class_ids = [];
				if ($teacher_id > 0) {
					$permissionRows = $this->db->table('teacher_permissions')
						->select('class_id')
						->where('teacher_id', $teacher_id)
						->where('attendance', 1)
						->get()
						->getResultArray();
					$permitted_class_ids = array_values(array_unique(array_map('intval', array_column($permissionRows, 'class_id'))));
				}

				if (!empty($permitted_class_ids) && $attendance_school_id > 0) {
					$modal_attendance_classes = $this->db->table('classes')
						->select('id, name')
						->where('school_id', $attendance_school_id)
						->whereIn('id', $permitted_class_ids)
						->orderBy('name', 'ASC')
						->get()
						->getResultArray();
				}
			} elseif ($attendance_school_id > 0) {
				$modal_attendance_classes = $this->db->table('classes')
					->select('id, name')
					->where('school_id', $attendance_school_id)
					->orderBy('name', 'ASC')
					->get()
					->getResultArray();
			}

			$page_data['attendance_school_id'] = $attendance_school_id;
			$page_data['modal_attendance_classes'] = $modal_attendance_classes;
		}
		
		// Special handling for billing_entity
		if ($folder_name == 'billing_entity') {
			// TODO: Replace with $this->billingEntityService = Config\Services::BillingEntityService();
			$this->loadModel('BillingEntity_model', 'billing_entity_model');
			
			if (($page_name == 'edit' || $page_name == 'credentials') && !empty($param1)) {
				// Load entity data
				$entity = $this->billing_entity_model->get_by_id($param1);
				if ($entity) {
					// Get mappings
					$mappings = $this->db->table('billing_entity_mappings')
						->where('billing_entity_id', $param1)
						->get()
						->getResultArray();
					$entity['mappings'] = array_column($mappings, 'tax_residence_code');
				}
				$page_data['entity'] = $entity;
			}
			
			return view('backend/superadmin/billing_entities/' . $page_name . '.php', $page_data);
			return;
		}

		if ($folder_name == 'academy' && $page_name == 'exam_questions') {
			$exam_result = $this->lms_model->get_exams('exam', $param1);
			$page_data['exam_details'] = !empty($exam_result) && is_object($exam_result[0]) ? (array) $exam_result[0] : null;
			$page_data['questions'] = $this->lms_model->get_exam_questions($param1)->getResultArray();
		}
		
		if($folder_name == 'academy'){
			return view( 'backend/'.$folder_name.'/'.$page_name.'.php' ,$page_data);
		}else{
			// Resolve active backend context from login flags first.
			// This avoids stale session('user_type') pointing to the wrong view folder.
			if ((int) session()->get('superadmin_login') === 1) {
				$userType = 'superadmin';
			} elseif ((int) session()->get('admin_login') === 1) {
				$userType = 'admin';
			} elseif ((int) session()->get('teacher_login') === 1) {
				$userType = 'teacher';
			} elseif ((int) session()->get('student_login') === 1) {
				$userType = 'student';
			} else {
				$userType = (string) (session()->get('user_type') ?? 'admin');
				if ($userType === '') {
					$userType = 'admin';
				}
			}
			return view('backend/' . $userType . '/' . $folder_name . '/' . $page_name . '.php', $page_data);
		}		
	}
}
