<?php

namespace App\Controllers;


/*
*  @author   : Creativeitem
*  date      : November, 2019
*  Ekattor School Management System With Addons
*  http://codecanyon.net/user/Creativeitem
*  http://support.creativeitem.com
*/

class Accountant extends BaseController {
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

		/*LOAD EXTERNAL LIBRARIES*/
    // TODO: Replace with service: Config\Services::pdf();

		// CHECK WHETHER Accountant IS LOGGED IN
		if(session()->get('accountant_login') != 1){
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

	//	SECTION STARTED
	public function section($action = "", $id = "") {

		// PROVIDE A LIST OF SECTION ACCORDING TO CLASS ID
		if ($action == 'list') {
			$page_data['class_id'] = $id;
			return view('backend/accountant/section/list', $page_data);
		}
	}
	//	SECTION ENDED
	// ACCOUNTING SECTION STARTS
  public function invoice($param1 = "", $param2 = "") {
    // For creating new invoice
    if ($param1 == 'single') {
      $response = $this->crud_model->create_single_invoice();
      echo $response;
    }

    // For creating new mass invoice
    if ($param1 == 'mass') {
      $response = $this->crud_model->create_mass_invoice();
      echo $response;
    }

    // For editing invoice
    if ($param1 == 'update') {
      $response = $this->crud_model->update_invoice($param2);
      echo $response;
    }

    // For deleting invoice
    if ($param1 == 'delete') {
      $response = $this->crud_model->delete_invoice($param2);
      echo $response;
    }

    // Get the list of student. Here param2 defines classId
    if ($param1 == 'student') {
      $page_data['enrolments'] = $this->user_model->get_student_details_by_id('class', $param2);
      return view('backend/accountant/student/dropdown', $page_data);
    }

    // showing the list of invoices
    if ($param1 == 'invoice') {
      $page_data['invoice_id'] = $param2;
      $page_data['folder_name'] = 'invoice';
      $page_data['page_name'] = 'invoice';
      $page_data['page_title']  = 'invoice';
      return view('backend/index', $page_data);
    }

    // showing the list of invoices
    if ($param1 == 'list') {
      $date = explode('-', $this->request->getGet('date'));
      $page_data['date_from'] = strtotime($date[0].' 00:00:00');
      $page_data['date_to']   = strtotime($date[1].' 23:59:59');
      $page_data['selected_class'] = htmlspecialchars($this->request->getGet('selectedClass'));
      $page_data['selected_status'] = htmlspecialchars($this->request->getGet('selectedStatus'));
      return view('backend/accountant/invoice/list', $page_data);
    }
    // showing the index file
    if(empty($param1)){
      $page_data['folder_name'] = 'invoice';
      $page_data['page_title']  = 'invoice';
      $first_day_of_month = "1 ".date("M")." ".date("Y").' 00:00:00';
      $last_day_of_month = date("t")." ".date("M")." ".date("Y").' 23:59:59';
      $page_data['date_from']   = strtotime($first_day_of_month);
      $page_data['date_to']     = strtotime($last_day_of_month);
      $page_data['selected_class'] = 'all';
      $page_data['selected_status'] = 'all';
      return view('backend/index', $page_data);
    }
  }

  //EXPORT STUDENT FEES
  public function export($param1 = "", $date_from = "", $date_to = "", $selected_class = "", $selected_status = "") {
    //RETURN EXPORT URL
    if ($param1 == 'url') {
      $type = htmlspecialchars($this->request->getPost('type'));
      $dateRange = $this->request->getPost('dateRange');
      
      // Support both separators: ' — ' (em dash) and ' - ' (hyphen)
      if (strpos($dateRange, ' — ') !== false) {
        $date = explode(' — ', $dateRange);
      } elseif (strpos($dateRange, ' - ') !== false) {
        $date = explode(' - ', $dateRange);
      } else {
        $date = explode('-', $dateRange);
      }
      
      $date_from = isset($date[0]) ? strtotime(trim($date[0]).' 00:00:00') : strtotime('first day of this month');
      $date_to = isset($date[1]) ? strtotime(trim($date[1]).' 23:59:59') : strtotime('last day of this month');
      $selected_class = htmlspecialchars($this->request->getPost('selectedClass'));
      $selected_status = htmlspecialchars($this->request->getPost('selectedStatus'));
      
      $export_url = route('export/'.$type.'/'.$date_from.'/'.$date_to.'/'.$selected_class.'/'.$selected_status);
      $csrf = array(
        'csrfName' => csrf_token(),
        'csrfHash' => csrf_hash(),
      );
      echo json_encode(array('url' => $export_url, 'csrf' => $csrf));
    }
    // EXPORT AS PDF
    if($param1 == 'pdf' || $param1 == 'print') {
      $page_data['action']   = $param1;
      $page_data['date_from']   = $date_from;
      $page_data['date_to']     = $date_to;
      $page_data['selected_class'] = $selected_class;
      $page_data['selected_status'] = $selected_status;
      $html = view('backend/accountant/invoice/export',$page_data);

      $this->pdf->loadHtml($html);
      $this->pdf->set_paper("a4", "landscape" );
      $this->pdf->render();

      // FILE DOWNLOADING CODES
      if ($selected_status == 'all') {
        $paymentStatusForTitle = 'paid-and-unpaid';
      }else{
        $paymentStatusForTitle = $selected_status;
      }
      if ($selected_class == 'all') {
        $classNameForTitle = 'all_class';
      }else{
        $class_details = $this->crud_model->get_classes($selected_class);
        $classNameForTitle = $class_details['name'];
      }
      $fileName = 'Student_fees-'.date('d-M-Y', $date_from).'-to-'.date('d-M-Y', $date_to).'-'.$classNameForTitle.'-'.$paymentStatusForTitle.'.pdf';

      if ($param1 == 'pdf') {
        $this->pdf->stream($fileName, array("Attachment" => 1));
      }else{
        $this->pdf->stream($fileName, array("Attachment" => 0));
      }
    }
    // EXPORT AS CSV
    if($param1 == 'csv'){
      $date_from   = $date_from;
      $date_to     = $date_to;
      $selected_class = $selected_class;
      $selected_status = $selected_status;

      $invoices = $this->crud_model->get_invoice_by_date_range($date_from, $date_to, $selected_class, $selected_status);
      $csv_file = fopen("assets/csv_file/invoices.csv", "w");
      $header = array('Invoice-no', 'Student', 'Class', 'Invoice-Title', 'Total-Amount', 'Paid-Amount', 'Creation-Date', 'Payment-Date', 'Status');
      fputcsv($csv_file, $header);
      foreach ($invoices as $invoice) {
        $student_details = $this->user_model->get_student_details_by_id('student', $invoice['student_id']);
        $class_details = $this->crud_model->get_class_details_by_id($invoice['class_id']);
        if ($invoice['updated_at'] > 0){
          $payment_date = date('d-M-Y', $invoice['updated_at']);
        }else{
          $payment_date = get_phrase('not_found');
        }
        $lines = array(sprintf('%08d', $invoice['id']), $student_details['name'], $class_details['name'], $invoice['title'], currency($invoice['total_amount']), currency($invoice['paid_amount']), date('d-M-Y', $invoice['created_at']), $payment_date, ucfirst($invoice['status']));
        fputcsv($csv_file, $lines);
      }

      // FILE DOWNLOADING CODES
      if ($selected_status == 'all') {
        $paymentStatusForTitle = 'paid-and-unpaid';
      }else{
        $paymentStatusForTitle = $selected_status;
      }
      if ($selected_class == 'all') {
        $classNameForTitle = 'all_class';
      }else{
        $class_details = $this->crud_model->get_classes($selected_class);
        $classNameForTitle = $class_details['name'];
      }
      $fileName = 'Student_fees-'.date('d-M-Y', $date_from).'-to-'.date('d-M-Y', $date_to).'-'.$classNameForTitle.'-'.$paymentStatusForTitle.'.csv';
      $this->download_file('assets/csv_file/invoices.csv', $fileName);
    }
  }

  /*FUNCTION FOR DOWNLOADING A FILE*/
  function download_file($path, $name)
  {
    // make sure it's a file before doing anything!
    if(is_file($path))
    {
      // required for IE
      if(ini_get('zlib.output_compression')) { ini_set('zlib.output_compression', 'Off'); }

      // get the file mime type using the file extension
      helper('file');

      $mime = get_mime_by_extension($path);

      // Build the headers to push out the file properly.
      header('Pragma: public');     // required
      header('Expires: 0');         // no cache
      header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
      header('Last-Modified: '.gmdate ('D, d M Y H:i:s', filemtime ($path)).' GMT');
      header('Cache-Control: private',false);
      header('Content-Type: '.$mime);  // Add the mime type from Code igniter.
      header('Content-Disposition: attachment; filename="'.basename($name).'"');  // Add the file name
      header('Content-Transfer-Encoding: binary');
      header('Content-Length: '.filesize($path)); // provide file size
      header('Connection: close');
      readfile($path); // push it out
      exit();
    }
  }

	// Expense Category
	public function expense_category($param1 = "", $param2 = "") {
		if ($param1 == 'create') {
			$response = $this->crud_model->create_expense_category();
			echo $response;
		}

		if ($param1 == 'update') {
			$response = $this->crud_model->update_expense_category($param2);
			echo $response;
		}

		if ($param1 == 'delete') {
			$response = $this->crud_model->delete_expense_category($param2);
			echo $response;
		}

		if ($param1 == 'list') {
			return view('backend/accountant/expense_category/list');
		}
		// showing the index file
		if(empty($param1)){
			$page_data['folder_name'] = 'expense_category';
			$page_data['page_title']  = 'expense_category';
			return view('backend/index', $page_data);
		}
	}

	//Expense Manager
	public function expense($param1 = "", $param2 = "") {

		// adding expense
		if ($param1 == 'create') {
			$response = $this->crud_model->create_expense();
			echo $response;
		}

		// update expense
		if ($param1 == 'update') {
			$response = $this->crud_model->update_expense($param2);
			echo $response;
		}

		// deleting expense
		if ($param1 == 'delete') {
			$response = $this->crud_model->delete_expense($param2);
			echo $response;
		}
		// showing the list of expense
		if ($param1 == 'list') {
			$date = explode('-', $this->request->getGet('date'));
			$page_data['date_from'] = strtotime($date[0].' 00:00:00');
			$page_data['date_to']   = strtotime($date[1].' 23:59:59');
			$page_data['expense_category_id'] = htmlspecialchars($this->request->getGet('expense_category_id'));
			return view('backend/accountant/expense/list', $page_data);
		}

		// showing the index file
		if(empty($param1)){
			$page_data['folder_name'] = 'expense';
			$page_data['page_title']  = 'expense';
			$page_data['date_from']   = strtotime(date('d-M-Y', strtotime(' -30 day')).' 00:00:00');
			$page_data['date_to']     = strtotime(date('d-M-Y').' 23:59:59');
			return view('backend/index', $page_data);
		}
	}
	// ACCOUNTING SECTION ENDS

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
	}
	//MANAGE PROFILE ENDS
}
