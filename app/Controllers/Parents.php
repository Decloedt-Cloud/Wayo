<?php

namespace App\Controllers;


/*
*  @author   : Creativeitem
*  date      : November, 2019
*  Ekattor School Management System With Addons
*  http://codecanyon.net/user/Creativeitem
*  http://support.creativeitem.com
*/

class Parents extends BaseController
{

	public function __construct()
	{

		parent::__construct();

		/*LOADING ALL THE MODELS HERE*/
		$this->crud_model = model('Crud_model');
		$this->user_model = model('User_model');
		$this->settings_model = model('Settings_model');
		$this->payment_model = model('Payment_model');
		$this->email_model = model('Email_model');
		$this->addon_model = model('Addon_model');
		$this->frontend_model = model('Frontend_model');
		$this->driver_model = model('Driver_model');

		/*cache control*/
		$this->response->setHeader("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
		$this->response->setHeader("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		$this->response->setHeader("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
		$this->response->setHeader("Cache-Control: post-check=0, pre-check=0");
		$this->response->setHeader("Pragma: no-cache");

		/*SET DEFAULT TIMEZONE*/
		timezone();

		if (session()->get('parent_login') != 1) {
			return redirect()->to(site_url('login'));
		}
	}
	//dashboard
	public function index()
	{
		return redirect()->to(route('dashboard'));
	}

	public function dashboard()
	{

		$page_data['page_title'] = 'Dashboard';
		$page_data['folder_name'] = 'dashboard';
		return view('backend/index', $page_data);
	}

	public function class_wise_subject($class_id)
	{

		// PROVIDE A LIST OF SUBJECT ACCORDING TO CLASS ID
		$page_data['class_id'] = $class_id;
		return view('backend/parent/subject/dropdown', $page_data);
	}
	//END SUBJECT section


	//START SYLLABUS section
	public function syllabus($param1 = '', $param2 = '', $param3 = '')
	{

		if ($param1 == 'list') {
			$page_data['class_id'] = $param2;
			$page_data['section_id'] = $param3;
			return view('backend/parent/syllabus/list', $page_data);
		}

		if (empty($param1)) {
			$page_data['folder_name'] = 'syllabus';
			$page_data['page_title'] = 'syllabus';
			return view('backend/index', $page_data);
		}
	}
	//END SYLLABUS section


	//START TEACHER section
	public function teacher($param1 = '', $param2 = '', $param3 = '')
	{
		$page_data['working_page'] = 'filter';
		$page_data['folder_name'] = 'teacher';
		$page_data['page_title'] = 'techers';
		return view('backend/index', $page_data);
	}
	//END TEACHER section

	//START CLASS ROUTINE section
	public function routine($param1 = '', $param2 = '', $param3 = '', $param4 = '')
	{

		if ($param1 == 'filter') {
			$page_data['class_id'] = $param2;
			$page_data['section_id'] = $param3;
			return view('backend/parent/routine/list', $page_data);
		}

		if (empty($param1)) {
			$page_data['folder_name'] = 'routine';
			$page_data['page_title'] = 'routine';
			return view('backend/index', $page_data);
		}
	}
	//END CLASS ROUTINE section


	//START DAILY ATTENDANCE section
	public function attendance($param1 = '', $param2 = '', $param3 = '')
	{

		if ($param1 == 'filter') {
			$date = '01 ' . $this->request->getPost('month') . ' ' . $this->request->getPost('year');
			$page_data['attendance_date'] = strtotime($date);
			$page_data['class_id'] = htmlspecialchars($this->request->getPost('class_id'));
			$page_data['section_id'] = htmlspecialchars($this->request->getPost('section_id'));
			$page_data['month'] = htmlspecialchars($this->request->getPost('month'));
			$page_data['year'] = htmlspecialchars($this->request->getPost('year'));
			$page_data['student_id'] = htmlspecialchars($this->request->getPost('student_id'));
			return view('backend/parent/attendance/list', $page_data);
		}

		if (empty($param1)) {
			$page_data['folder_name'] = 'attendance';
			$page_data['page_title'] = 'attendance';
			return view('backend/index', $page_data);
		}
	}
	//END DAILY ATTENDANCE section


	//START EVENT CALENDAR section
	public function event_calendar($param1 = '', $param2 = '')
	{
		if ($param1 == 'all_events') {
			echo $this->crud_model->all_events();
		}

		if ($param1 == 'list') {
			return view('backend/parent/event_calendar/list');
		}

		if (empty($param1)) {
			$page_data['folder_name'] = 'event_calendar';
			$page_data['page_title'] = 'event_calendar';
			return view('backend/index', $page_data);
		}
	}
	//END EVENT CALENDAR section

	// This function is needed for Ajax calls only
	public function get_student_details_by_id($look_up_value = "", $student_id = "")
	{
		$student_details = $this->user_model->get_student_details_by_id('student', $student_id);
		echo $student_details[$look_up_value];
	}
	//END STUDENT ADN ADMISSION section


	//START EXAM section
	public function exam($param1 = '', $param2 = '')
	{
		if ($param1 == 'list') {
			return view('backend/parent/exam/list');
		}

		if (empty($param1)) {
			$page_data['folder_name'] = 'exam';
			$page_data['page_title'] = 'exam';
			return view('backend/index', $page_data);
		}
	}
	//END EXAM section

	//START MARKS section
	public function mark($param1 = '', $param2 = '')
	{

		if ($param1 == 'list') {
			$page_data['class_id'] = htmlspecialchars($this->request->getPost('class_id'));
			$page_data['section_id'] = htmlspecialchars($this->request->getPost('section_id'));
			$page_data['subject_id'] = htmlspecialchars($this->request->getPost('subject'));
			$page_data['exam_id'] = htmlspecialchars($this->request->getPost('exam'));
			$page_data['student_id'] = htmlspecialchars($this->request->getPost('student_id'));
			//$this->crud_model->mark_insert($page_data['class_id'], $page_data['section_id'], $page_data['subject_id'], $page_data['exam_id']);
			return view('backend/parent/mark/list', $page_data);
		}

		if (empty($param1)) {
			$page_data['folder_name'] = 'mark';
			$page_data['page_title'] = 'marks';
			return view('backend/index', $page_data);
		}
	}
	//END MARKS sesction

	// GRADE SECTION STARTS
	public function grade($param1 = "", $param2 = "")
	{
		$page_data['folder_name'] = 'grade';
		$page_data['page_title'] = 'grades';
		return view('backend/index', $page_data);
	}
	// GRADE SECTION ENDS

	// ACCOUNTING SECTION STARTS
	public function invoice($param1 = "", $param2 = "")
	{

		// Get the list of student. Here param2 defines classId
		if ($param1 == 'student') {
			$page_data['enrolments'] = $this->user_model->get_student_details_by_id('class', $param2);
			return view('backend/parent/student/dropdown', $page_data);
		}

		// showing the list of invoices
		if ($param1 == 'list') {
			$date = explode('-', $this->request->getGet('date'));
			$page_data['date_from'] = strtotime($date[0] . ' 00:00:00');
			$page_data['date_to']   = strtotime($date[1] . ' 23:59:59');
			return view('backend/parent/invoice/list', $page_data);
		}

		// showing the list of invoices
		if ($param1 == 'invoice') {
			$page_data['invoice_id'] = $param2;
			$page_data['folder_name'] = 'invoice';
			$page_data['page_name'] = 'invoice';
			$page_data['page_title']  = 'invoice';
			return view('backend/index', $page_data);
		}
		// showing the index file
		if (empty($param1)) {
			$page_data['folder_name'] = 'invoice';
			$page_data['page_title']  = 'invoice';
			return view('backend/index', $page_data);
		}
	}

	// PAYPAL CHECKOUT
	public function paypal_checkout()
	{
		$invoice_id = htmlspecialchars($this->request->getPost('invoice_id'));
		$invoice_details = $this->crud_model->get_invoice_by_id($invoice_id);

		$page_data['invoice_id']   = $invoice_id;
		$page_data['user_details']    = $this->user_model->get_student_details_by_id('student', $invoice_details['student_id']);
		$page_data['amount_to_pay']   = $invoice_details['total_amount'] - $invoice_details['paid_amount'];
		$page_data['folder_name'] = 'paypal';
		$page_data['page_title']  = 'paypal_checkout';
		return view('backend/payment_gateway/paypal_checkout', $page_data);
	}

	// STRIPE CHECKOUT
	public function stripe_checkout()
	{
		$invoice_id = htmlspecialchars($this->request->getPost('invoice_id'));
		$invoice_details = $this->crud_model->get_invoice_by_id($invoice_id);

		$page_data['invoice_id']   = $invoice_id;
		$page_data['user_details']    = $this->user_model->get_student_details_by_id('student', $invoice_details['student_id']);
		$page_data['amount_to_pay']   = $invoice_details['total_amount'] - $invoice_details['paid_amount'];
		$page_data['folder_name'] = 'paypal';
		$page_data['page_title']  = 'paypal_checkout';
		return view('backend/payment_gateway/stripe_checkout', $page_data);
	}

	public function payment_success($payment_method = "", $invoice_id = "", $amount_paid = "", $reference = "")
	{
		if ($payment_method == 'stripe') {
			$stripe = json_decode(get_payment_settings('stripe_settings'));
			$token_id = $this->request->getPost('stripeToken');
			$stripe_test_mode = $stripe[0]->stripe_mode;
			if ($stripe_test_mode == 'on') {
				$public_key = $stripe[0]->stripe_test_public_key;
				$secret_key = $stripe[0]->stripe_test_secret_key;
			} else {
				$public_key = $stripe[0]->stripe_live_public_key;
				$secret_key = $stripe[0]->stripe_live_secret_key;
			}
			$payment_status = $this->payment_model->stripe_payment($token_id, $invoice_id, $amount_paid, $secret_key);
		} elseif ($payment_method = 'paystack') {
			$this->loadModel('addons/paystack_model');
			$payment_status = $this->paystack_model->check_payment($reference);
		}

		$data['payment_method'] = $payment_method;
		$data['invoice_id'] = $invoice_id;
		$data['amount_paid'] = $amount_paid;

		if ($payment_status == true && $payment_method == 'stripe') {
			$this->crud_model->payment_success($data);
		} elseif ($payment_method == 'paystack') {
			$this->crud_model->payment_success($data);
		} elseif ($payment_method == 'paypal') {
			$this->crud_model->payment_success($data);
		}

		return redirect()->to(route('invoice'));
	}
	// ACCOUNTING SECTION ENDS

	// BACKOFFICE SECTION

	//BOOK LIST MANAGER
	public function book($param1 = "", $param2 = "")
	{
		// showing the list of book
		if ($param1 == 'list') {
			return view('backend/parent/book/list');
		}

		// showing the index file
		if (empty($param1)) {
			$page_data['folder_name'] = 'book';
			$page_data['page_title']  = 'books';
			return view('backend/index', $page_data);
		}
	}

	//MANAGE PROFILE STARTS
	public function profile($param1 = "", $param2 = "")
	{
		if ($param1 == 'update_profile') {
			$response = $this->user_model->update_profile();
			echo $response;
		}
		if ($param1 == 'update_password') {
			$response = $this->user_model->update_password();
			echo $response;
		}

		// showing the Smtp Settings file
		if (empty($param1)) {
			$page_data['folder_name'] = 'profile';
			$page_data['page_title']  = 'manage_profile';
			return view('backend/index', $page_data);
		}
	}
	//MANAGE PROFILE ENDS

	public function payment($invoice_id = "")
	{
		$page_data['page_title']  = 'payment_gateway';
		$page_data['invoice_details'] = $this->crud_model->get_invoice_by_id($invoice_id);
		return view('backend/payment_gateway/index', $page_data);
	}






	// PAYUMONEY CHECKOUT
	public function payumoney($invoice_id = "")
	{
		$page_data['page_title']  = 'payment_gateway';
		$page_data['invoice_details'] = $this->crud_model->get_invoice_by_id($invoice_id);
		return view('backend/payment_gateway/payumoney', $page_data);
	}

	public function trips($param1 = '')
	{
		$filter_child_id = $this->request->getPost('filter_child_id');
		$filter = $filter_child_id ?: '';
		$position = $ongoing_trip_id = '';
		if (!empty($filter_child_id)) {
			$student_trip_details = $this->user_model->get_student_trip_details($filter_child_id);

			if ($student_trip_details) {
				$position = $student_trip_details['last_location'] != '' ? json_decode($student_trip_details['last_location']) : '';
				$ongoing_trip_id = $student_trip_details['id'];
			}
		}

		$page_data['ongoing_trip_id'] = $ongoing_trip_id;
		$page_data['position'] = $position;
		$page_data['filter'] = $filter;
		$page_data['page_title'] = 'Trips';
		$page_data['folder_name'] = 'trips';
		return view('backend/index', $page_data);
	}

	function get_location()
	{
		$trip_id = $this->request->getPost('trip_id');
		$position = $this->user_model->get_trip_position($trip_id);
		echo $position['last_location'];
	}
}
