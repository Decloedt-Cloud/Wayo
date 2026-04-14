<?php

namespace App\Controllers;


/*
*  @author   : Creativeitem
*  date      : November, 2019
*  Ekattor School Management System With Addons
*  http://codecanyon.net/user/Creativeitem
*  http://support.creativeitem.com
*/

class Librarian extends BaseController {
	public function __construct(){

		parent::__construct();

		/*LOADING ALL THE MODELS HERE*/
		$this->crud_model = model('Crud_model');
		$this->user_model = model('User_model');
		$this->settings_model = model('Settings_model');
		$this->payment_model = model('Payment_model');
		$this->email_model = model('Email_model');
		$this->addon_model = model('Addon_model');
		$this->frontend_model = model('Frontend_model');

		/*cache control*/
		$this->response->setHeader("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
		$this->response->setHeader("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		$this->response->setHeader("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
		$this->response->setHeader("Cache-Control: post-check=0, pre-check=0");
		$this->response->setHeader("Pragma: no-cache");

		/*SET DEFAULT TIMEZONE*/
		timezone();
		
		// CHECK WHETHER LIBRARIAN IS LOGGED IN
		if(session()->get('librarian_login') != 1){
			return redirect()->to(site_url('login'));
		}
	}

	// INDEX FUNCTION
	public function index(){
		return redirect()->to(site_url('app/dashboard'));
	}

	//DASHBOARD
	public function dashboard(){
		$page_data['page_title'] = 'Dashboard';
		$page_data['folder_name'] = 'dashboard';
		return view('backend/index', $page_data);
	}

	// BACKOFFICE MANAGEMENT STARTS
	//BOOK LIST MANAGER
	public function book($param1 = "", $param2 = "") {
		// adding book
		if ($param1 == 'create') {
			$response = $this->crud_model->create_book();
			echo $response;
		}

		// update book
		if ($param1 == 'update') {
			$response = $this->crud_model->update_book($param2);
			echo $response;
		}

		// deleting book
		if ($param1 == 'delete') {
			$response = $this->crud_model->delete_book($param2);
			echo $response;
		}
		// showing the list of book
		if ($param1 == 'list') {
			return view('backend/librarian/book/list');
		}

		// showing the index file
		if(empty($param1)){
			$page_data['folder_name'] = 'book';
			$page_data['page_title']  = 'books';
			return view('backend/index', $page_data);
		}
	}

	//BOOK ISSUE LIST MANAGER
	public function book_issue($param1 = "", $param2 = "") {
		// adding book
		if ($param1 == 'create') {
			$response = $this->crud_model->create_book_issue();
			echo $response;
		}

		// update book
		if ($param1 == 'update') {
			$response = $this->crud_model->update_book_issue($param2);
			echo $response;
		}

		// Returning a book
		if ($param1 == 'return') {
			$response = $this->crud_model->return_issued_book($param2);
			echo $response;
		}

		// deleting book
		if ($param1 == 'delete') {
			$response = $this->crud_model->delete_book_issue($param2);
			echo $response;
		}
		// showing the list of book
		if ($param1 == 'list') {
			$date = explode('-', $this->request->getGet('date'));
			$page_data['date_from'] = strtotime($date[0].' 00:00:00');
			$page_data['date_to']   = strtotime($date[1].' 23:59:59');
			return view('backend/librarian/book_issue/list', $page_data);
		}

		// showing the index file
		if(empty($param1)){
			$page_data['folder_name'] = 'book_issue';
			$page_data['page_title']  = 'book_issue';
			$page_data['date_from'] = strtotime(date('d-M-Y', strtotime(' -30 day')).' 00:00:00');
			$page_data['date_to']   = strtotime(date('d-M-Y').' 23:59:59');
			return view('backend/index', $page_data);
		}
	}
	// BACKOFFICE MANAGEMENT ENDS

	//STUDENT LIST STARTS
	public function student($param1 = "", $param2 = "") {
		// Get the list of student. Here param2 defines classId
		if ($param1 == 'dropdown') {
			$page_data['enrolments'] = $this->user_model->get_student_details_by_id('class', $param2);
			return view('backend/superadmin/student/dropdown', $page_data);
		}
	}
	//STUDENT LIST ENDS

	//MANAGE PROFILE STARTS
	public function profile($param1 = "", $param2 = "") {
		if ($param1 == 'update_profile') {
			$response = $this->user_model->update_profile();
			echo $response;
		}
		if ($param1 == 'update_password') {
			$response = $this->user_model->update_password();
			echo $response;
		}

		// showing the Smtp Settings file
		if(empty($param1)){
			$page_data['folder_name'] = 'profile';
			$page_data['page_title']  = 'manage_profile';
			return view('backend/index', $page_data);
		}
	//MANAGE PROFILE ENDS
}
}
