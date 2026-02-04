<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
*  @author   : Creativeitem
*  date      : November, 2019
*  Ekattor School Management System With Addons
*  http://codecanyon.net/user/Creativeitem
*  http://support.creativeitem.com
*/

class Modal extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->database();

		/*LOADING ALL THE MODELS HERE*/
		$this->load->model('Crud_model',     'crud_model');
		$this->load->model('User_model',     'user_model');
		$this->load->model('Settings_model', 'settings_model');
		$this->load->model('Payment_model',  'payment_model');
		$this->load->model('Email_model',    'email_model');
		$this->load->model('Addon_model',    'addon_model');
		$this->load->model('Frontend_model', 'frontend_model');

		if(addon_status('online_courses') != 0){
			$this->load->model('addons/Lms_model','lms_model');
			$this->load->model('addons/Video_model','video_model');
		}
		/*SET DEFAULT TIMEZONE*/
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
		
		// Special handling for billing_entity
		if ($folder_name == 'billing_entity') {
			$this->load->library('BillingEntityService', null, 'billingEntityService');
			$this->load->model('BillingEntity_model', 'billing_entity_model');
			
			if (($page_name == 'edit' || $page_name == 'credentials') && !empty($param1)) {
				// Load entity data
				$entity = $this->billing_entity_model->get_by_id($param1);
				if ($entity) {
					// Get mappings
					$mappings = $this->db->get_where('billing_entity_mappings', ['billing_entity_id' => $param1])->result_array();
					$entity['mappings'] = array_column($mappings, 'tax_residence_code');
				}
				$page_data['entity'] = $entity;
			}
			
			$this->load->view('backend/superadmin/billing_entities/' . $page_name . '.php', $page_data);
			return;
		}
		
		if($folder_name == 'academy'){
			$this->load->view( 'backend/'.$folder_name.'/'.$page_name.'.php' ,$page_data);
		}else{
			$this->load->view( 'backend/'.$this->session->userdata('user_type').'/'.$folder_name.'/'.$page_name.'.php' ,$page_data);
		}		
	}
}
