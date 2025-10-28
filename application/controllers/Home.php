<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
 *  @author   : Creativeitem
 *  date      : November, 2019
 *  Ekattor School Management System With Addons
 *  http://codecanyon.net/user/Creativeitem
 *  http://support.creativeitem.com
 */

class Home extends CI_Controller
{
	protected $theme;
	protected $active_school_id;

	public function __construct()
	{
		parent::__construct();

		$this->load->database();
		$this->load->library('session');

		/*LOADING ALL THE MODELS HERE*/
		$this->load->model('Crud_model', 'crud_model');
		$this->load->model('User_model', 'user_model');
		$this->load->model('Settings_model', 'settings_model');
		$this->load->model('Payment_model', 'payment_model');
		$this->load->model('Email_model', 'email_model');
		$this->load->model('Addon_model', 'addon_model');
		$this->load->model('Frontend_model', 'frontend_model');

		if (addon_status('alumni')) {
			$this->load->model('addons/Alumni_model', 'alumni_model');
		}
		/*cache control*/
		$this->output->set_header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
		$this->output->set_header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", false);
		$this->output->set_header("Pragma: no-cache");

		/*SET DEFAULT TIMEZONE*/
		timezone();

		$this->theme = get_frontend_settings('theme');
		$this->active_school_id = $this->frontend_model->get_active_school_id();

		if (!$this->session->userdata('active_school_id')) {
			$this->active_school_id_for_frontend();
		}
	}

	// INDEX FUNCTION
	// default function
	public function index()
	{
		$page_data['page_name'] = 'home';
		$page_data['page_title'] = get_phrase('home');
		$this->load->view('frontend/' . $this->theme . '/index', $page_data);
	}

	//ABOUT PAGE
	function about()
	{
		$page_data['page_name'] = 'about';
		$page_data['page_title'] = get_phrase('about_us');
		$this->load->view('frontend/' . $this->theme . '/index', $page_data);
	}

	//WEBINARE PAGE
	function webinaire()
	{
		$page_data['page_name'] = 'webinaire';
		$page_data['page_title'] = get_phrase('webinaire');
		$this->load->view('frontend/' . $this->theme . '/index', $page_data);
	}

	// TUTORIAL PAGE
	function tutorial()
	{
		$page_data['page_name'] = 'tutorial';
		$page_data['page_title'] = get_phrase('tutorial');
		$this->load->view('frontend/' . $this->theme . '/index', $page_data);
	}

		// TUTORIAL PAGE
	function faq()
	{
		$page_data['page_name'] = 'faq';
		$page_data['page_title'] = get_phrase('faq');
		$this->load->view('frontend/' . $this->theme . '/index', $page_data);
	}

	// TEACHERS PAGE
	function teachers()
	{
		$count_teachers = $this->db->get_where('users', array('role' => 'teacher', 'school_id' => $this->active_school_id))->num_rows();
		$config = array();
		$config = manager($count_teachers, 9);
		$config['base_url'] = site_url('home/teachers/');
		$this->pagination->initialize($config);

		$page_data['per_page'] = $config['per_page'];
		$page_data['page_name'] = 'teacher';
		$page_data['page_title'] = get_phrase('teachers');
		$this->load->view('frontend/' . $this->theme . '/index', $page_data);
	}

	// EVENTS GETTING
	function events()
	{
		$count_events = $this->db->get_where('frontend_events', array('status' => 1, 'school_id' => $this->active_school_id))->num_rows();
		$config = array();
		$config = manager($count_events, 8);
		$config['base_url'] = site_url('home/events/');
		$this->pagination->initialize($config);

		$page_data['per_page'] = $config['per_page'];
		$page_data['page_name'] = 'event';
		$page_data['page_title'] = get_phrase('event_list');
		$this->load->view('frontend/' . $this->theme . '/index', $page_data);
	}

	// SCHOOL WISE GALLERY
	function gallery()
	{
		$count_gallery = $this->db->get_where('frontend_gallery', array('show_on_website' => 1, 'school_id' => $this->active_school_id))->num_rows();
		$config = array();
		$config = manager($count_gallery, 6);
		$config['base_url'] = site_url('home/gallery/');
		$this->pagination->initialize($config);

		$page_data['per_page'] = $config['per_page'];
		$page_data['page_name'] = 'gallery';
		$page_data['page_title'] = get_phrase('gallery');
		$this->load->view('frontend/' . $this->theme . '/index', $page_data);
	}

	// GALLERY DETAILS
	function gallery_view($gallery_id = '')
	{
		$count_images = $this->db->get_where(
			'frontend_gallery_image',
			array(
				'frontend_gallery_id' => $gallery_id
			)
		)->num_rows();
		$config = array();
		$config = manager($count_images, 9);
		$config['base_url'] = site_url('home/gallery_view/' . $gallery_id . '/');
		$this->pagination->initialize($config);

		$page_data['per_page'] = $config['per_page'];
		$page_data['gallery_id'] = $gallery_id;
		$page_data['page_name'] = 'gallery_view';
		$page_data['page_title'] = get_phrase('gallery');
		$this->load->view('frontend/' . $this->theme . '/index', $page_data);
	}

	//GET THE CONTACT PAGE
	function contact($param1 = '')
	{

		if ($param1 == 'send') {
			if (!$this->crud_model->check_recaptcha() && get_common_settings('recaptcha_status') == true) {
				redirect(site_url('home/contact'), 'refresh');
			}
			$this->frontend_model->send_contact_message();

		}else {

		 $this->session->unset_userdata('toast_message');
		}
		$page_data['page_name'] = 'contact';
		$page_data['page_title'] = get_phrase('contact_us');

		$this->load->view('frontend/' . $this->theme . '/index', $page_data);
	}

	//GET THE PRIVACY POLICY PAGE
	function privacy_policy()
	{
		$page_data['page_name'] = 'privacy_policy';
		$page_data['page_title'] = get_phrase('privacy_policy');
		$this->load->view('frontend/' . $this->theme . '/index', $page_data);
	}

	//GET THE TERMS AND CONDITION PAGE
	function terms_conditions()
	{
		$page_data['page_name'] = 'terms_conditions';
		$page_data['page_title'] = get_phrase('terms_and_conditions');
		$this->load->view('frontend/' . $this->theme . '/index', $page_data);
	}

	//GET THE ALLUMNI EVENT PAGE IF THE ADDON IS ENABLED
	function alumni_event()
	{
		if (addon_status('alumni')) {
			$page_data['page_name'] = 'alumni_event';
			$page_data['page_title'] = get_phrase('alumni_event');
			$this->load->view('frontend/' . $this->theme . '/index', $page_data);
		} else {
			redirect(site_url(), 'refresh');
		}
	}

	//GET THE ALLUMNI GALLERY PAGE IF THE ADDON IS ENABLED
	function alumni_gallery()
	{
		if (addon_status('alumni')) {
			$page_data['page_name'] = 'alumni_gallery';
			$page_data['page_title'] = get_phrase('alumni_gallery');
			$this->load->view('frontend/' . $this->theme . '/index', $page_data);
		} else {
			redirect(site_url(), 'refresh');
		}
	}

	//GET THE ALLUMNI GALLERY DETAILS
	function alumni_gallery_view($gallery_id = '')
	{
		if (addon_status('alumni')) {
			$count_images = $this->db->get_where(
				'alumni_gallery_photos',
				array(
					'gallery_id' => $gallery_id
				)
			)->num_rows();
			$config = array();
			$config = manager($count_images, 9);
			$config['base_url'] = site_url('home/alumni_gallery_view/' . $gallery_id . '/');
			$this->pagination->initialize($config);

			$page_data['per_page'] = $config['per_page'];
			$page_data['gallery_id'] = $gallery_id;
			$page_data['page_name'] = 'alumni_gallery_view';
			$page_data['page_title'] = get_phrase('alumni_gallery');
			$this->load->view('frontend/' . $this->theme . '/index', $page_data);
		} else {
			redirect(site_url(), 'refresh');
		}
	}

	// NOTICEBOARD
	function noticeboard()
	{
		$count_notice = $this->db->get_where('noticeboard', array('show_on_website' => 1, 'school_id' => $this->active_school_id, 'session' => active_session()))->num_rows();
		$config = array();
		$config = manager($count_notice, 9);
		$config['base_url'] = site_url('home/noticeboard/');
		$this->pagination->initialize($config);

		$page_data['per_page'] = $config['per_page'];
		$page_data['page_name'] = 'noticeboard';
		$page_data['page_title'] = get_phrase('noticeboard');
		$this->load->view('frontend/' . $this->theme . '/index', $page_data);
	}

	function notice_details($notice_id = '')
	{
		$page_data['notice_id'] = $notice_id;
		$page_data['page_name'] = 'notice_details';
		$page_data['page_title'] = get_phrase('notice_details');
		$this->load->view('frontend/' . $this->theme . '/index', $page_data);
	}


	//communities Overview Page
	function communities($param1 = null, $param2 = null)
{
    $config = array();
    $config['base_url'] = site_url('home/communities/');
    $config['per_page'] = 8;
    $config['use_page_numbers'] = true;

    // Vérifier si une catégorie est spécifiée
    $is_category = false;
    $category = null;
    if ($param1 != null) {
        $cat_formated = str_replace("_", " ", $param1);
        $category = $cat_formated;
        $is_category = $this->frontend_model->contains("categories", "name", $category);
    }

    // Déterminer la page actuelle et l'offset
    if ($is_category) {
        // Si c'est une catégorie, la page est dans le segment 4 (home/communities/category/page)
        $page = $this->uri->segment(4) ? (int)$this->uri->segment(4) : 1;
        $config['base_url'] = site_url('home/communities/' . str_replace(" ", "_", $category));
    } else {
        // Si ce n'est pas une catégorie, la page est dans le segment 3 (home/communities/page)
        $page = ($param1 != null && is_numeric($param1)) ? (int)$param1 : ($this->uri->segment(3) ? (int)$this->uri->segment(3) : 1);
    }

    $offset = ($page - 1) * $config['per_page'];

    // Si une catégorie est spécifiée et qu'il y a des écoles dans cette catégorie
    if ($is_category && $this->db->get_where('schools', array('category' => $category, 'status' => 1, 'Etat' => 1))->num_rows() > 0) {
        $config['uri_segment'] = 4;
        try {
            $page_data['schools'] = $this->user_model->get_schools_per_category($category, $config['per_page'], $offset);
            $config['total_rows'] = $this->user_model->get_schools_per_category_count($category);
            $page_data['statement'] = 1;
        } catch (Exception $e) {
            log_message('error', 'Erreur dans get_schools_per_category : ' . $e->getMessage());
            show_error('Une erreur est survenue lors du chargement des écoles pour la catégorie ' . $category, 500);
        }
    }
    // Si une catégorie est spécifiée mais qu'il n'y a pas d'écoles
    elseif ($is_category) {
        $config['uri_segment'] = 4;
        $page_data['schools'] = array();
        $config['total_rows'] = 0;
        $page_data['no_courses_found'] = get_phrase('0_communities_found_in_category') . ' ' . $category;
        $page_data['statement'] = 2;
    }
    // Si aucune catégorie n'est spécifiée (cas "All")
    else {
        $config['uri_segment'] = 3;
        $page_data['schools'] = $this->user_model->get_schools($config['per_page'], $offset);
        $config['total_rows'] = $this->user_model->get_schools_count();
        $page_data['statement'] = 4;
    }

	// Configuration pagination Bootstrap
	$config['num_links'] = 2; // nb de liens autour de la page active

	$config['full_tag_open']   = '<nav aria-label="Communities pagination"><ul class="pagination justify-content-center pagination-custom">';
	$config['full_tag_close']  = '</ul></nav>';

	$config['first_link']      = '&laquo;';
	$config['first_tag_open']  = '<li class="page-item">';
	$config['first_tag_close'] = '</li>';

	$config['last_link']       = '&raquo;';
	$config['last_tag_open']   = '<li class="page-item">';
	$config['last_tag_close']  = '</li>';

	$config['next_link']       = '&rsaquo;';
	$config['next_tag_open']   = '<li class="page-item">';
	$config['next_tag_close']  = '</li>';

	$config['prev_link']       = '&lsaquo;';
	$config['prev_tag_open']   = '<li class="page-item">';
	$config['prev_tag_close']  = '</li>';

	$config['cur_tag_open']    = '<li class="page-item active"><a class="page-link" href="#">';
	$config['cur_tag_close']   = '</a></li>';

	$config['num_tag_open']    = '<li class="page-item">';
	$config['num_tag_close']   = '</li>';

	$config['attributes']      = ['class' => 'page-link'];

    // Initialiser la pagination
    $this->pagination->initialize($config);

    // Créer les liens de pagination
    $page_data['links'] = $this->pagination->create_links();
	
	

    // Définir les données de la page
    $page_data['selected_category'] = $category;
    $page_data['categories'] = $this->frontend_model->get_categories();
    $page_data['page_name'] = 'communities';
    $page_data['page_title'] = get_phrase('communities');
	// --- 🔥 Ajout : Support AJAX ---
		if ($this->input->is_ajax_request()) {
			// En cas d’appel AJAX : on renvoie seulement le HTML partiel
			$this->load->view('frontend/' . $this->theme . '/partials/communities_grid', $page_data);
			return;
		}


    $this->load->view('frontend/' . $this->theme . '/index', $page_data);
}

	function communities_search()
	{

		$input = htmlspecialchars($this->input->get('search'));

		$config = array();
		$config['base_url'] = site_url('home/communities_search/');
		$config['suffix'] = '?search=' . urlencode($input);
		$config['per_page'] = 8;
		$config['use_page_numbers'] = true;
		$config['uri_segment'] = 3;


		if ($input == null) {

			$page = ($this->uri->segment($config['uri_segment'])) ? $this->uri->segment($config['uri_segment']) : 1;
			$offset = ($page - 1) * $config['per_page'];
			$page_data['schools'] = $this->user_model->get_schools($config['per_page'], $offset);
			$config['total_rows'] = $this->db->count_all('schools');
			$page_data['statement'] = 1;

		} else {

			$page = ($this->uri->segment($config['uri_segment'])) ? $this->uri->segment($config['uri_segment']) : 1;
			$offset = ($page - 1) * $config['per_page'];
			$page_data['input_search'] = $input;


			$page_data['schools'] = $this->user_model->get_schools_search($input, $config['per_page'], $offset);
			$config['total_rows'] = $this->user_model->get_schools_search_count($input);
			$page_data['statement'] = 2;

		}

		if ($page_data['schools']->num_rows() == 0) {
			$page_data['no_courses_found'] = get_phrase('0_communities_found_for_search') . ' ' . '"' . $input . '"';
		}

		//pagination bootstrap settings
		{
			$config['first_url'] = $config['base_url'] . $config['suffix'];


			$config['num_links'] = 1;

			// Icônes Font Awesome
			$config['first_link'] = '<i class="fas fa-angle-double-left fa-xs"></i>';
			$config['last_link']  = '<i class="fas fa-angle-double-right fa-xs"></i>';
			$config['next_link']  = '<i class="fas fa-angle-right fa-xs"></i>';
			$config['prev_link']  = '<i class="fas fa-angle-left fa-xs"></i>';

			// === Structure Bootstrap ===
			$config['full_tag_open']  = '<ul class="pagination justify-content-center pagination-custom">'; 
			$config['full_tag_close'] = '</ul>';

			$config['first_tag_open'] = '<li class="page-item">';
			$config['first_tag_close'] = '</li>';
			
			$config['last_tag_open'] = '<li class="page-item">';
			$config['last_tag_close'] = '</li>';
			

			$config['next_tag_open'] = '<li class="page-item">';
			$config['next_tag_close'] = '</li>';

			$config['prev_tag_open'] = '<li class="page-item">';
			$config['prev_tag_close'] = '</li>';

			$config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';
			$config['cur_tag_close'] = '</a></li>';

			$config['num_tag_open'] = '<li class="page-item">';
			$config['num_tag_close'] = '</li>';

			$config['attributes'] = array('class' => 'page-link');

		}

		//initialize pagination
		$this->pagination->initialize($config);

		//create pagination links
		$page_data['links'] = $this->pagination->create_links();

		$page_data['categories'] = $this->frontend_model->get_categories();
		$page_data['page_name'] = 'communities';
		$page_data['page_title'] = get_phrase('communities');
		$this->load->view('frontend/' . $this->theme . '/index', $page_data);


	}



function community_details($school_id = '')
{
    $school_id = urldecode($school_id);
    $page_data['school'] = $this->user_model->get_school_details($school_id);
    $page_data['school_id'] = $page_data['school']['id'];
  	$page_data['settings_data'] = $this->db->get_where('settings_school', array('school_id ' =>$page_data['school_id'] ))->row_array();
    // passe la valeur deux façons : dans school et comme variable indépendante
    $page_data['course_students_count'] = $this->user_model->get_community_students_count($page_data['school']['id']);
    $page_data['school']['course_students_count'] = $page_data['course_students_count'];

    $page_data['classes_count'] = $this->crud_model->get_school_classes_count($page_data['school']['id']);
    $page_data['school']['classes_count'] = $page_data['classes_count'];

    $page_data['teachers_count'] = $this->user_model->get_school_teachers_count($page_data['school']['id']);
    $page_data['school']['teachers_count'] = $page_data['teachers_count'];

    // Récupération des classes
    $classes = $this->crud_model->get_school_classes($school_id);

    // Récupérer le créateur du school
    $school_creator = $this->user_model->get_creator_by_school($page_data['school_id']);
    $creator_name = !empty($school_creator['name']) ? $school_creator['name'] : 'À définir';

    // Ajouter le nom du créateur comme mentor à chaque classe
    foreach ($classes as &$class) {
        $class['mentor'] = $creator_name;
    }
    unset($class);

    $page_data['classes'] = $classes;

    $page_data['page_name']  = 'community_details';
    $page_data['page_title'] = get_phrase('community_details');
    $this->load->view('frontend/' . $this->theme . '/index', $page_data);
}

	// function join_school($param1 ,$school_id)
	// {
	// 	$this->user_model->join_school($school_id);
	
	// }


// public function join_school($param1, $school_id)
// {
//     if ($param1 == 'assigned') {

//         // 🔹 1. Récupération des données envoyées par le formulaire
//         $data['student_id'] = $this->session->userdata('user_id'); 
//         $data['school_id']  = htmlspecialchars($this->input->post('school_id'));
//         $data['price']      = htmlspecialchars($this->input->post('price'));
//         $data['currency']   = htmlspecialchars($this->input->post('currency'));
//         $data['session']    = active_session();

//         // 🔹 2. Vérifier si l'école existe
//         $school_name = $this->db->get_where('schools', ['id' => $data['school_id']])->row('name');
//         if (!$school_name) {
//             show_error('École non trouvée.');
//             return;
//         }

//         // 🔹 3. Vérifier s'il existe déjà une facture pour cette école et cet étudiant
//         $existing_invoice = $this->db->get_where('invoices', [
//             'school_id'  => $data['school_id'],
//             'student_id' => $data['student_id']
//         ])->row();

//         if (!$existing_invoice) {
//             // 🔹 4. Créer la facture (invoice)
//             $invoice_data = [
//                 'title'        => 'Adhésion - ' . $school_name,
//                 'total_amount' => $data['price'],
//                 'student_id'   => $data['student_id'],
//                 'school_id'    => $data['school_id'],
//                 'status'       => 'unpaid',
//                 'currency'     => $data['currency'],
//                 'session'      => $data['session'],
//                 'created_at'   => strtotime(date('Y-m-d H:i:s')),
//                 'payment_type' => 'school_join' // 🔹 ajout pour identifier le type de paiement
//             ];
//             $this->db->insert('invoices', $invoice_data);
//             $invoice_id = $this->db->insert_id();
//         } else {
//             $invoice_id = $existing_invoice->id;
//         }

//         // 🔹 5. Vérifier s’il existe déjà un paiement
//         $existing_payment = $this->db->get_where('payments', [
//             'school_id'  => $data['school_id'],
//             'student_id' => $data['student_id']
//         ])->row();

//         if (!$existing_payment) {
//             // 🔹 6. Créer l'entrée de paiement dans la table "payments"
//             $payment_data = [
//                 'student_id'     => $data['student_id'],
//                 'school_id'      => $data['school_id'],
//                 'amount'         => $data['price'],
//                 'currency'       => $data['currency'],
//                 'payment_type'   => 'community_join',
//                 'payment_status' => 'pending',
//                 'invoice_id'     => $invoice_id,
//                 'created_at'     => date('Y-m-d H:i:s')
//             ];
//             $this->db->insert('payments', $payment_data);
//         }
// 		// die($data['price']);
//         // 🔹 7. Redirection vers la page de paiement ou la facture
       
// 		redirect(site_url('Student/invoice/' . $invoice_id), 'refresh');
//     }
// }





	// ACTIVE SCHOOL ID FOR FRONTEND
	function active_school_id_for_frontend($active_school_id = "")
	{
		if (addon_status('multi-school') && $active_school_id > 0) {
			$this->session->set_userdata('active_school_id', $active_school_id);
		} else {
			$active_school_id = get_settings('school_id');
			$this->session->set_userdata('active_school_id', $active_school_id);
		}
	}



	public function check_student_status_ajax($school_id)
	{
		$user_id = $this->session->userdata('user_id');
		$user_type = $this->session->userdata('user_type');

		if ($user_id && $user_type == "student") {
			$status = $this->user_model->check_student_status($school_id);
			echo json_encode(array('status' => $status));
		}
		else if ($user_id && $user_type !== "student") {

			echo json_encode(array('status' => 2));
		} else {
			echo json_encode(array('status' => null));
		}
	}

	public function dropdown_guest() {
    $languages = $this->settings_model->get_all_languages(); // <-- Utilise settings_model ici
    $current_language = function_exists('get_user_language') ? get_user_language() : 'english';
    foreach ($languages as $language) {
        echo '<a class="dropdown-item'.($current_language == $language ? ' active' : '').'" href="#" onclick="setGuestLanguage(\''.$language.'\')">'.ucfirst($language).'</a>';
    }
	}

// Change la langue pour les guests (stockée en session)
	public function set_guest_language() {
    $lang = $this->input->post('language', TRUE); // Filtrage XSS
    if ($lang) {
        $this->session->set_userdata('language', $lang);
        $response = array(
            'status' => 'success',
            'message' => get_phrase('language_updated_successfully'),
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash()
        );
    } else {
        $response = array(
            'status' => 'error',
            'message' => get_phrase('language_not_provided'),
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash()
        );
    }

    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($response));
}
}
