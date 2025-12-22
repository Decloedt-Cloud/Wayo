<?php

use Mpdf\Mpdf;

defined('BASEPATH') or exit('No direct script access allowed');

/*
 *  @author   : Creativeitem
 *  date      : November, 2019
 *  Ekattor School Management System With Addons
 *  http://codecanyon.net/user/Creativeitem
 *  http://support.creativeitem.com
 */

class Admin extends CI_Controller
{
	
	public function __construct()
	{

		parent::__construct();

		$this->load->database();
		
		$this->load->library('Humhub_sso');
		$this->load->library('session');
		$this->config->load('config'); 
    	
		require_once APPPATH . '../vendor/autoload.php';

		/*LOADING ALL THE MODELS HERE model  */
		$this->load->model('Crud_model', 'crud_model');
		$this->load->model('User_model', 'user_model');
		$this->load->model('Settings_model', 'settings_model');
		$this->load->model('Payment_model', 'payment_model');
		$this->load->model('addons/Lms_model','lms_model');
		$this->load->model('Email_model', 'email_model');
		$this->load->model('Addon_model', 'addon_model');
		$this->load->model('Frontend_model', 'frontend_model');
		$this->load->model('Driver_model', 'driver_model');
		$this->load->model('Room_model','room_model');

		/*cache control*/
		$this->output->set_header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
		$this->output->set_header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", false);
		$this->output->set_header("Pragma: no-cache");

		/*SET DEFAULT TIMEZONE*/
		timezone();

		/*LOAD EXTERNAL LIBRARIES*/
		$this->load->library('pdf');

		if ($this->session->userdata('admin_login') != 1) {
			redirect(site_url('login'), 'refresh');
		}

		if ($this->session->userdata('user_type') == 'admin') {
			$school_id = $this->session->userdata('school_id');
			$school    = $this->db->get_where('schools', ['id' => $school_id])->row_array();

			$current_method = $this->router->method;

			// Protection de base : communauté non approuvée
			if (!$school || (int)$school['status'] !== 1) {
				// Autoriser uniquement : dashboard, logout, et changement de langue
				$allowed_methods = ['dashboard', 'logout', 'language'];

				if (!in_array($current_method, $allowed_methods)) {
					redirect(site_url('admin/dashboard'));
				}
			}

			// ---- Gestion de la période d'essai de 14 jours pour l'admin de la communauté ----
			// On considère qu'une communauté est en essai si is_trial = 1 et is_paid = 0
			// et que la date actuelle est supérieure à trial_end.
			$trial_expired = false;
			$subscription_expired = false;
			if ($school) {
				$now         = time();
				$is_trial    = isset($school['is_trial']) ? (int)$school['is_trial'] : 0;
				$is_paid     = isset($school['is_paid']) ? (int)$school['is_paid'] : 0;
				$trial_end   = isset($school['trial_end']) ? (int)$school['trial_end'] : 0;
				$subscription_end = isset($school['subscription_end']) ? (int)$school['subscription_end'] : 0;

				// DEBUG: Log pour vérifier les valeurs
				log_message('debug', "Admin construct - school_id: $school_id, is_paid: $is_paid, subscription_end: $subscription_end, now: $now");
				if ($subscription_end > 0) {
					log_message('debug', "Admin construct - subscription_end date: " . date('Y-m-d H:i:s', $subscription_end) . ", now date: " . date('Y-m-d H:i:s', $now));
				}
				
				// DEBUG TEMPORAIRE: Afficher les valeurs pour debug
				if ($current_method == 'dashboard' && isset($_GET['debug_subscription'])) {
					echo "<pre>DEBUG SUBSCRIPTION:\n";
					echo "school_id: $school_id\n";
					echo "is_trial: $is_trial\n";
					echo "is_paid: $is_paid\n";
					echo "subscription_end: $subscription_end (" . ($subscription_end > 0 ? date('Y-m-d H:i:s', $subscription_end) : 'NULL/0') . ")\n";
					echo "now: $now (" . date('Y-m-d H:i:s', $now) . ")\n";
					echo "now > subscription_end: " . ($now > $subscription_end ? 'YES' : 'NO') . "\n";
					echo "subscription_expired will be: " . ($is_trial === 0 && $subscription_end > 0 && $now > $subscription_end ? 'YES' : 'NO') . "\n";
					echo "</pre>";
				}

				// Vérifier si l'essai de 14 jours est expiré
				if ($is_trial === 1 && $is_paid === 0 && $trial_end > 0 && $now > $trial_end) {
					$trial_expired = true;
					log_message('debug', "Admin construct - Trial expired detected");
				}

			// Vérifier si l'abonnement mensuel est expiré
			// Si subscription_end existe et est passé, l'abonnement est expiré (peu importe is_paid)
			// On vérifie seulement si l'école n'est pas en période d'essai (is_trial = 0)
			// Car si is_trial = 1, on utilise trial_end pour la vérification
			if ($is_trial === 0 && $subscription_end > 0 && $now > $subscription_end) {
				$subscription_expired = true;
				log_message('debug', "Admin construct - Subscription expired detected: subscription_end (" . date('Y-m-d H:i:s', $subscription_end) . ") < now (" . date('Y-m-d H:i:s', $now) . ")");
			} elseif ($is_trial === 0 && $subscription_end > 0 && $now <= $subscription_end) {
				// Log pour confirmer que l'abonnement est encore valide
				log_message('debug', "Admin construct - Subscription still valid: subscription_end (" . date('Y-m-d H:i:s', $subscription_end) . ") >= now (" . date('Y-m-d H:i:s', $now) . ")");
			}
			}

			// Créer automatiquement une facture si l'essai ou l'abonnement est expiré
			if (($trial_expired || $subscription_expired) && $school_id) {
				// Si l'abonnement est expiré, remettre is_paid à 0 pour refléter l'état bloqué
				if ($subscription_expired && $is_paid === 1) {
					log_message('debug', "Admin construct - Updating is_paid to 0 for expired subscription");
					$this->db->where('id', $school_id);
					$this->db->update('schools', [
						'is_paid'     => 0,
						'is_trial' => 1, 
						'updated_at'  => time(),
					]);
					// Mettre à jour la variable locale aussi
					$is_paid = 0;
				}

				// Déterminer la date de référence pour créer une nouvelle facture
				// Pour l'essai expiré : utiliser trial_end
				// Pour l'abonnement expiré : utiliser subscription_end
				$reference_date = 0;
				if ($subscription_expired && $subscription_end > 0) {
					$reference_date = $subscription_end;
				} elseif ($trial_expired && $trial_end > 0) {
					$reference_date = $trial_end;
				}

				// Vérifier s'il existe déjà une facture impayée créée APRÈS l'expiration
				$this->db->order_by('id', 'DESC');
				$this->db->where('school_id', $school_id);
				$this->db->where('payment_type', 'subscription_admin');
				$this->db->where('status', 'unpaid');
				if ($reference_date > 0) {
					// Ne prendre que les factures créées après l'expiration
					$this->db->where('created_at >=', $reference_date);
				}
				$existing_invoice = $this->db->get('invoices')->row_array();

				if ($existing_invoice) {
					// Une facture impayée existe déjà créée après l'expiration, on ne crée pas de doublon
					log_message('debug', "Admin construct - Invoice already exists (ID: " . $existing_invoice['id'] . ") created after expiration");
					// Si la facture n'a pas de payment_type, l'aligner pour éviter les doublons futurs
					if (empty($existing_invoice['payment_type'])) {
						$this->db->where('id', $existing_invoice['id']);
						$this->db->update('invoices', ['payment_type' => 'subscription_admin']);
					}
				} else {
					// Créer une nouvelle facture car aucune facture impayée n'existe après l'expiration
					log_message('debug', "Admin construct - Creating new subscription_admin invoice for school_id: $school_id");
					$school_name = isset($school['name']) ? $school['name'] : 'Community';
					$invoice_data = [
						'title' => $school_name . ' - Monthly Subscription',
						'total_amount' => 790, // Montant par défaut, peut être ajusté
						'payment_type' => 'subscription_admin',
						'status' => 'unpaid',
						'school_id' => $school_id,
						'session' => active_session(),
						'created_at' => time(), // Utiliser time() au lieu de strtotime pour avoir le timestamp exact
						'student_id' => null // Pas de student_id pour les abonnements admin
					];
					$this->db->insert('invoices', $invoice_data);
					$new_invoice_id = $this->db->insert_id();
					log_message('debug', "Admin construct - New invoice created with ID: $new_invoice_id");
				}
			}

			// Partage l'info avec les vues
			$this->trial_expired = $trial_expired || $subscription_expired;
			$this->school_data   = $school;

			// Si l'essai ou l'abonnement est expiré, on bloque l'accès
			if ($trial_expired || $subscription_expired) {
               
				// Laisser accès uniquement au dashboard, au logout et à la page de paiement (si définie)
				$allowed_methods_trial = ['dashboard', 'logout', 'language', 'subscription', 'payment', 'payment_success'];

				if (!in_array($current_method, $allowed_methods_trial)) {
                    die('test');
					// Bloquer l'accès : rediriger vers le dashboard où un pop-up de paiement sera affiché
					log_message('debug', "Admin construct - Access blocked for method '$current_method' (trial_expired: " . ($trial_expired ? 'true' : 'false') . ", subscription_expired: " . ($subscription_expired ? 'true' : 'false') . ")");
					redirect(site_url('admin/dashboard'));
				} else {
					log_message('debug', "Admin construct - Access allowed for method '$current_method' (trial/subscription expired but method is in allowed list)");
				}
			}
		}
	}
	//dashboard
	public function index()
	{
		redirect(route('dashboard'), 'refresh');
		
	}

	   //START TEACHER Create_Join bigbleubutton 
	   public function Create_Join($param1 = '', $param2 = '', $param3 = '')
	   {
	 
   
	 
		 if (empty($param1)) {
		   $page_data['folder_name'] = 'bigbleubutton';
		   $page_data['page_title'] = 'Démarrer Réunion';
		   $this->load->view('backend/index', $page_data);
		 }
	   }

	   //START TEACHER Create_Join bigbleubutton 
	   public function Liveclasse($param1 = '', $param2 = '', $param3 = '')
	   {
	 
		 if ($param1 == 'create') {
		   $response = $this->room_model->create_room();
		   // echo $response;
		   // Préparer la réponse avec un nouveau jeton CSRF
		   $csrf = array(
			 'csrfName' => $this->security->get_csrf_token_name(),
			 'csrfHash' => $this->security->get_csrf_hash(),
		 );
	 
		 // Renvoyer la réponse avec un nouveau jeton CSRF
		 echo json_encode(array('status' => $response, 'csrf' => $csrf));
		 }
   
		 if ($param1 == 'update') {
		   $response = $this->room_model->update_room($param2);
		   // echo $response;
		   // Préparer la réponse avec un nouveau jeton CSRF
		   $csrf = array(
			 'csrfName' => $this->security->get_csrf_token_name(),
			 'csrfHash' => $this->security->get_csrf_hash(),
		 );
	 
		 // Renvoyer la réponse avec un nouveau jeton CSRF
		 echo json_encode(array('status' => $response, 'csrf' => $csrf));
		 }
		  if ($param1 == 'list') {
			 $this->load->view('backend/admin/bigbleubutton/list');
		   }
	 
		 if (empty($param1)) {
		   $page_data['folder_name'] = 'bigbleubutton';
		   $page_data['page_title'] = 'Démarrer Réunion';
		   $this->load->view('backend/index', $page_data);
		 }
	   }
	   //END TEACHER Create_Join bigbleubutton 
	 
   
	   public function get_appointments() {
		 $appointments = $this->room_model->get_all_appointments();
		 echo json_encode($appointments);
		 }
		 
		 public function add_appointment() {
			// Récupération et sécurisation des données
			$schoolID = school_id();
			$title = $this->input->post('title', true);
			$start_date = $this->input->post('start', true);
			$description = $this->input->post('description', true);
			$classe_id = $this->input->post('classe_id', true);
			$room_id = $this->input->post('room_id', true);
			$sections = $this->input->post('sections'); // Tableau de sections sélectionnées
			
		  
		   
			// Vérification : Les champs obligatoires ne doivent pas être vides
			if (empty($title) || empty($start_date)) {
				echo json_encode(["status" => "error", "message" => "Titre et date sont obligatoires"]);
				return;
			}
		
			// Création du tableau de données à insérer
			$data = array(
				'title' => $title,
				'start_date' => $start_date,
				'description' => $description,
				'classe_id' => $classe_id,
				'sections_id' => $sections, // Stockage sous forme "1,2,3"
				'room_id' => $room_id,
				'school_id' => $schoolID
			);
		
			// Insertion dans la base de données avec gestion d'erreur
			try {
				$this->db->insert('appointments', $data);
				echo json_encode(["status" => "success", "message" => "Rendez-vous ajouté avec succès"]);
			} catch (Exception $e) {
				echo json_encode(["status" => "error", "message" => "Erreur lors de l'ajout du rendez-vous : " . $e->getMessage()]);
			}
		  }
	   
	   public function delete_appointment() {
		   $id = $this->input->post('id');
   
		   $appointments ['Etat'] = 0;
   
		   $this->db->where('id', $id);
			$this->db->update('appointments', $appointments);
	   
	   
		   // $this->db->where('id', $id);
		   // $this->db->delete('appointments');
		   echo json_encode(["status" => "deleted"]);
	   }
	   //END TEACHER Create_Join bigbleubutton 


		   //START TEACHER Create_Join bigbleubutton 
		public function recording($param1 = '', $param2 = '', $param3 = '') {
    // Check authentication
    if ($this->session->userdata('admin_login') != 1) {
        redirect(site_url('login'), 'refresh');
    }

    // Récupérer l'ID de l'utilisateur connecté
    $user_id = $this->session->userdata('user_id'); // Assurez-vous que 'user_id' est défini dans la session lors de la connexion

    // Récupérer le school_id depuis la table users
    $user = $this->db->get_where('users', ['id' => $user_id])->row_array();
    if (!$user || empty($user['school_id'])) {
        // Gérer le cas où l'utilisateur n'a pas de school_id ou n'existe pas
        log_message('error', 'recording - No school_id found for user_id: ' . $user_id);
        show_error('No school associated with this user.', 403);
        return;
    }
    $school_id = $user['school_id'];

    // Synchronize recordings
    $this->load->config('bigbluebutton');
    $bbb_url = $this->config->item('bbb_url');
    $bbb_secret = $this->config->item('bbb_secret');

    $last_sync = $this->session->userdata('last_recording_sync');
    $current_time = time();
    $sync_interval = 10; // Synchronize every 10 seconds (for testing, adjust as needed)

    if (!$last_sync || ($current_time - $last_sync) > $sync_interval) {
        // Filtrer les réunions par school_id
        $this->db->where('school_id', $school_id);
        $meetings = $this->db->get('sessions_meetings')->result_array();

        foreach ($meetings as $meeting) {
            $params = "meetingID=" . urlencode($meeting['meeting_id']);
            $checksum = sha1("getRecordings" . $params . $bbb_secret);
            $recordings_url = $bbb_url . "getRecordings?" . $params . "&checksum=" . $checksum;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $recordings_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            $recordings_response = curl_exec($ch);
            $curl_error = curl_error($ch);
            curl_close($ch);

            if ($curl_error) {
                log_message('error', 'recording - cURL error for meeting_id: ' . $meeting['meeting_id'] . ': ' . $curl_error);
                continue;
            }

            $recordings_xml = simplexml_load_string($recordings_response);
            if ($recordings_xml && (string)$recordings_xml->returncode === "SUCCESS") {
                foreach ($recordings_xml->recordings->recording as $recording) {
                    if ((string)$recording->state !== 'published') {
                        continue;
                    }
                    $recording_id = (string)$recording->recordID;
                    $recording_start_time = (string)$recording->startTime;
                    $recording_end_time = (string)$recording->endTime;
                    $original_recording_url = (string)$recording->playback->format->url;
                    // Replace the domain in the recording URL
                    $recording_url = str_replace('https://31.97.52.98', 'https://visio.wayo.site', $original_recording_url);
                    $duration_seconds = (int)(($recording_end_time - $recording_start_time) / 1000);
    
                    // Format duration
                    $hours = floor($duration_seconds / 3600);
                    $minutes = floor(($duration_seconds % 3600) / 60);
                    $seconds = $duration_seconds % 60;
                    
                    if ($hours >= 1) {
                        $formatted_duration = sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
                    } else {
                        $formatted_duration = sprintf("%02d:%02d", $minutes, $seconds);
                    }

                    $start_time = date('Y-m-d H:i:s', $recording_start_time / 1000);
                    $end_time = date('Y-m-d H:i:s', $recording_end_time / 1000);

                    $existing = $this->db->get_where('recordings', ['recording_id' => $recording_id])->row_array();
                    if (!$existing) {
                        $recording_data = [
                            'meeting_id' => $meeting['meeting_id'],
                            'appointment_id' => $meeting['appointment_id'],
                            'recording_id' => $recording_id,
                            'name' => $meeting['name'],
                            'class_id' => $meeting['class_id'],
                            'school_id' => $meeting['school_id'],
                            'start_time' => $start_time,
                            'end_time' => $end_time,
                            'duration' => $duration_seconds,
                            'formatted_duration' => $formatted_duration,
                            'recording_url' => $recording_url,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ];
                        $this->db->insert('recordings', $recording_data);
                    } else {
                        $this->db->where('recording_id', $recording_id);
                        $this->db->update('recordings', [
                            'recording_url' => $recording_url,
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                    }
                }
            }
        }

        $this->session->set_userdata('last_recording_sync', $current_time);
    }

    // Initialize filters
    $filters = [
        'meeting_name' => $this->input->post('meeting_name', true) ?? '',
        'date_range' => $this->input->post('date_range', true) ?? ''
    ];

    // Build query
    $this->db->select('r.*, c.name as class_name');
    $this->db->from('recordings r');
    $this->db->join('classes c', 'r.class_id = c.id', 'left');
    $this->db->where('r.school_id', $school_id);

    // Apply filters
    if (!empty($filters['meeting_name'])) {
        $this->db->like('r.name', $filters['meeting_name'], 'both');
    }
    if (!empty($filters['date_range'])) {
        $dates = explode(' - ', $filters['date_range']);
        if (count($dates) == 1) {
            $date = DateTime::createFromFormat('d-m-Y', trim($dates[0]));
            if ($date) {
                $this->db->where('DATE(r.start_time)', $date->format('Y-m-d'));
            }
        } elseif (count($dates) == 2) {
            $date_from = DateTime::createFromFormat('d-m-Y', trim($dates[0]));
            $date_to = DateTime::createFromFormat('d-m-Y', trim($dates[1]));
            if ($date_from && $date_to) {
                $this->db->where('r.start_time >=', $date_from->format('Y-m-d 00:00:00'));
                $this->db->where('r.start_time <=', $date_to->format('Y-m-d 23:59:59'));
            }
        }
    }

    $this->db->order_by('r.created_at', 'DESC');
    $recordings = $this->db->get()->result_array();

    // Handle AJAX request
    if ($this->input->is_ajax_request()) {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'recordings' => $recordings,
            'filters' => $filters,
            'csrf_token' => $this->security->get_csrf_hash()
        ]);
        return;
    }

    // Load view for non-AJAX request
    $page_data['recordings'] = $recordings;
    $page_data['filters'] = $filters;
    $page_data['page_name'] = 'recording/recording';
    $page_data['page_title'] = 'recording';

    $this->load->view('backend/index', $page_data);
}
		   
			public function delete_recording()
{
    // Vérifier l'authentification
    if ($this->session->userdata('admin_login') != 1) {
        $response = [
            'status' => 'error',
            'message' => get_phrase('unauthorized_access'),
            'csrf_token' => $this->security->get_csrf_hash()
        ];
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
        return;
    }

    // Récupérer l'ID de l'enregistrement depuis la requête POST
    $recording_id = trim($this->input->post('recording_id', true));

    if (empty($recording_id)) {
        $response = [
            'status' => 'error',
            'message' => get_phrase('invalid_recording_id'),
            'csrf_token' => $this->security->get_csrf_hash()
        ];
        log_message('error', 'delete_recording - Invalid recording_id received');
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
        return;
    }

    // Vérifier si l'enregistrement existe dans la table recordings
    $this->db->where('recording_id', $recording_id);
    $existing = $this->db->get('recordings')->row_array();

    if (!$existing) {
        $response = [
            'status' => 'error',
            'message' => get_phrase('recording_not_found'),
            'csrf_token' => $this->security->get_csrf_hash()
        ];
        log_message('error', 'delete_recording - Recording not found: recording_id=' . $recording_id);
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
        return;
    }

    // Charger la configuration BigBlueButton
    $this->load->config('bigbluebutton');
    $bbb_url = $this->config->item('bbb_url');
    $bbb_secret = $this->config->item('bbb_secret');

    // Supprimer l'enregistrement de BigBlueButton
    $params = "recordID=" . urlencode($recording_id);
    $checksum = sha1("deleteRecordings" . $params . $bbb_secret);
    $delete_url = $bbb_url . "deleteRecordings?" . $params . "&checksum=" . $checksum;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $delete_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $delete_response = curl_exec($ch);
    $curl_error = curl_error($ch);
    curl_close($ch);

    if ($curl_error) {
        log_message('error', 'delete_recording - cURL error while deleting from BBB: recording_id=' . $recording_id . ', Error: ' . $curl_error);
        $response = [
            'status' => 'error',
            'message' => get_phrase('failed_to_delete_recording_from_bbb'),
            'csrf_token' => $this->security->get_csrf_hash()
        ];
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
        return;
    }

    // Vérifier la réponse de l'API BBB
    $delete_xml = simplexml_load_string($delete_response);
    if (!$delete_xml || (string)$delete_xml->returncode !== "SUCCESS") {
        log_message('error', 'delete_recording - BBB API delete failed: recording_id=' . $recording_id . ', Response: ' . $delete_response);
        $response = [
            'status' => 'error',
            'message' => get_phrase('failed_to_delete_recording_from_bbb'),
            'csrf_token' => $this->security->get_csrf_hash()
        ];
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
        return;
    }

    // Supprimer l'enregistrement de la table recordings
    $this->db->where('recording_id', $recording_id);
    $this->db->delete('recordings');
    $db_error = $this->db->error();


    if ($db_error['code'] == 0 && $this->db->affected_rows() > 0) {
        $response = [
            'status' => 'success',
            'message' => get_phrase('recording_deleted_successfully'),
            'csrf_token' => $this->security->get_csrf_hash()
        ];
    } else {
        log_message('error', 'delete_recording - Failed to delete recording: recording_id=' . $recording_id . ', Last Query: ' . $this->db->last_query() . ', DB Error: ' . json_encode($db_error));
        $response = [
            'status' => 'error',
            'message' => get_phrase('failed_to_delete_recording') . ' (DB Error: ' . $db_error['message'] . ')',
            'csrf_token' => $this->security->get_csrf_hash()
        ];
    }

    $this->output->set_content_type('application/json')->set_output(json_encode($response));
}
	 
		 
		 
		   public function delete_appointment_and_recording($appointment_id)
		   {
		 
		 
			   if (empty($appointment_id)) {
				   $this->session->set_flashdata('error_message', "ID de l'appointment invalide.");
				   redirect(site_url('admin/Recording'), 'refresh');
				   return;
			   }
	 
			   // Appel à la méthode de suppression dans le modèle
			   $success = $this->room_model->delete_bbb_recording_by_appointment($appointment_id);
			   die($success);
			   // Vérification du succès de la suppression
			   if ($success) {
				   $this->session->set_flashdata('success_message', 'Appointment et enregistrements supprimés avec succès.');
			   } else {
				   $this->session->set_flashdata('error_message', "Erreur lors de la suppression de l'enregistrement ou de l'appointment.");
			   }
	 
			   // Redirection vers la liste des enregistrements
			   redirect(site_url('admin/Recording'), 'refresh');
		   }
	 
		   public function delete_room()
		   {
			   $data = json_decode(file_get_contents("php://input"), true);
			   $roomID = $data['selectedRoomID'];
			   // $this->db->delete("rooms", ["id" => $roomID]);
			   $this->room_model->update_room_by_id($roomID);
			   echo json_encode(["status" => "success", "message" => "Room supprimée avec succès !"]);
		   }
	public function dashboard()
	{

		// $this->msg91_model->clickatell();
		
			// 1) Vérifier que c'est bien un admin
			if (! $this->session->userdata('admin_login')) {
				show_error('Accès réservé aux administrateurs.');
			}
			
			// 2) Récupérer les infos du user (ici : depuis la table Wayo)
			$userId = $this->session->userdata('user_id');
			$wUser  = $this->db->get_where('users', ['id' => $userId])->row();
			if (empty($wUser) || ! filter_var($wUser->email, FILTER_VALIDATE_EMAIL)) {
				log_message('error', 'Invalid Wayo user data: ' . print_r($wUser, true));
				show_error('Impossible de retrouver votre compte Wayo pour SSO.');
			}
			log_message('debug', 'Wayo user data: ' . print_r($wUser, true));

			// 3) Stocker temporairement cet objet pour la librairie SSO
			$this->session->set_userdata('user', $wUser);

			// 4) Générer l’URL SSO
			$iframeUrl = $this->humhub_sso->provisionAndGetIframeUrl();
			log_message('debug', 'Generated iframe URL: ' . $iframeUrl);

			if (! $iframeUrl) {
				show_error('Impossible de générer l’URL SSO HumHub.');
			}
			
			// 5) Passer à la vue
			$page_data = [
				'folder_name' => 'dashboard',
				'page_title'  => 'Dashboard',
				//'page_name'   => 'central',
				'iframe_url'  => $iframeUrl,
			];
			$this->load->view('backend/index', $page_data);
	}

	

	public function get_csrf_token()
	{
		$csrf = array(
			'csrf_name' => $this->security->get_csrf_token_name(),
			'csrf_hash' => $this->security->get_csrf_hash(),
		);
		echo json_encode($csrf);
	}

    public function subscription_admin($param1 = "", $school_id = "")
    {
  
     
      if ($param1 == 'assigned') {
          
       // Stocker les données de l'inscription dans la session pour un accès ultérieur

          $data['session'] = active_session();
      
            $this->session->set_userdata('enrolment_data', $data);
  
  
          $num_rows_invoices = $this->db->get_where('invoices', array('payment_type' => 'subscription_admin','student_id' => $data['student_id']))->num_rows();
          // print_r($num_rows_invoices);die;
          if($num_rows_invoices == 0){
              $name = $this->db->get_where('schools', array('id' => $data['school_id']))->row('name');
              $data_invoice['title'] = $name." - Subscription " ;
              $data_invoice['total_amount'] = "790";
            //   $data_invoice['currency'] = $data['currency']; // ADD THIS LINE
              $data_invoice['payment_type'] = "subscription_admin";
              $data_invoice['status'] = "unpaid";
              $data_invoice['school_id'] = $school_id;
              $data_invoice['session'] = $data['session'];
              $data_invoice['created_at'] = strtotime(date('d-M-Y'));
              $this->db->insert('invoices', $data_invoice);
              $invoice_id = $this->db->insert_id();
          }else{
            //   $invoice_id = $this->db->get_where('invoices', array('class_id' => $data['class_id'],'student_id' => $data['student_id']))->row('id');
          }
  
  
        redirect(site_url('admin/payment/' . $invoice_id), 'refresh');
  
      //   $this->session->set_flashdata('flash_message', get_phrase('admission_request_has_been_updated'));
      //   redirect(site_url('addons/courses'), 'refresh');
      }
    }
  
	public function payment($param1 = "",$invoice_id = ""){
  
        $page_data['page_title'] = 'payment_gateway';
        $page_data['type'] = $param1;
        
        // L'invoice_id peut être dans $param1 ou $invoice_id selon la route
        if (empty($invoice_id) && !empty($param1) && is_numeric($param1)) {
            $invoice_id = $param1;
        }
        
        // Vérifier que l'invoice_id est valide
        if (empty($invoice_id) || !is_numeric($invoice_id)) {
            show_error('Invalid invoice ID');
            return;
        }
        
        // Get invoice details by ID
        $page_data['invoice_details'] = $this->crud_model->get_invoice_by_id($invoice_id);
        
        // Vérifier que la facture existe
        if (empty($page_data['invoice_details'])) {
            show_error('Invoice not found');
            return;
        }
        // Pass invoice ID to view
        $page_data['invoice_id'] = $invoice_id;

        // ========== CHECK INVOICE STATUS ==========
        // If invoice is paid, redirect or show a different view
        if ($page_data['invoice_details']['status'] == 'paid') {
            // Option 1: Redirect to a different page (e.g., invoice view page)
            redirect('/Student/invoice');
        }
        
        // Load user details based on invoice
        // For subscription_admin invoices, use the logged-in admin user
        $student_id = $page_data['invoice_details']['student_id'];
        if (empty($student_id) && isset($page_data['invoice_details']['payment_type']) && $page_data['invoice_details']['payment_type'] === 'subscription_admin') {
            // Use the logged-in admin user for subscription_admin invoices
            $student_id = $this->session->userdata('user_id');
        }
        $page_data['user_details'] = $this->db->get_where('users', ['id' => $student_id])->row_array();

        // Get school ID from invoice or session
        $school_id = $page_data['invoice_details']['school_id'];
        if (empty($school_id)) {
            $school_id = $this->session->userdata('payment_school_id');
        }
        if (empty($school_id)) {
            $school_id = $this->session->userdata('school_id');
        }
        $page_data['school'] = $this->db->get_where('schools', ['id' => $school_id])->row(); // Pass to view

        
        // Fetch invoice from database (alternative method)
        $invoice = $this->db->get_where('invoices', ['id' => $this->uri->segment(4)])->row();
        
        // Set the total amount to pay and currency
        $page_data['amount_to_pay'] = $page_data['invoice_details']['total_amount'];
        $page_data['currency'] = $page_data['invoice_details']['currency'];

        

            // Load community name (from school table)
            $school_id = $page_data['invoice_details']['school_id'];
            $community = $this->db->get_where('schools', ['id' => $school_id])->row();
            $page_data['community_name'] = $community ? $community->name : "";
       
        // ========== PAYMENT GATEWAY SETTINGS ==========

        
        // Get payment settings from database
        $school_id = $page_data['invoice_details']['school_id'];
        
        // Query Stripe settings

        $stripe_row = $this->db->get_where('payment_settings', [
            'school_id' => 1,
            'key' => 'stripe_settings'
        ])->row();

        $paypal_row = $this->db->get_where('payment_settings', [
            'school_id' => 1,
            'key' => 'paypal_settings'
        ])->row();


        // Handle JSON decoding - check if already decoded or needs decoding
        if ($stripe_row && is_string($stripe_row->value)) {
            $stripe = json_decode(trim($stripe_row->value));
        } elseif ($stripe_row && is_object($stripe_row->value)) {
            $stripe = $stripe_row->value;
        } else {
            $stripe = []; // Default empty array
        }

        if ($paypal_row && is_string($paypal_row->value)) {
                $paypal = json_decode(trim($paypal_row->value));
        } elseif ($paypal_row && is_object($paypal_row->value)) {
                $paypal = $paypal_row->value;
        } else {
                $paypal = []; // Default empty array
        }

        // Convert to array if needed for consistent access
        $stripe = is_object($stripe) ? [$stripe] : (array)$stripe;
        $paypal = is_object($paypal) ? [$paypal] : (array)$paypal;


        
        // ========== STRIPE SETTINGS ==========
        $stripe_test_mode = $stripe[0]->stripe_mode ?? 'on';
        
        if ($stripe_test_mode == 'on') {
            $page_data['stripe_public_key'] = $stripe[0]->stripe_test_public_key ?? '';
            $page_data['stripe_private_key'] = $stripe[0]->stripe_test_secret_key ?? '';
        } else {
            $page_data['stripe_public_key'] = $stripe[0]->stripe_live_public_key ?? '';
            $page_data['stripe_private_key'] = $stripe[0]->stripe_live_secret_key ?? '';
        }
        
        // Chaque mode de paiement a sa propre devise
        $page_data['stripe_currency'] = $stripe[0]->stripe_currency ?? 'USD';
        $page_data['stripe_enabled'] = !empty($page_data['stripe_private_key']) && !empty($page_data['stripe_public_key']);
        
        // ========== PAYPAL SETTINGS ==========
        $page_data['paypal_mode'] = isset($paypal[0]->paypal_mode) ? $paypal[0]->paypal_mode : 'sandbox';
        $page_data['paypal_client_id_sandbox'] = isset($paypal[0]->paypal_client_id_sandbox) ? $paypal[0]->paypal_client_id_sandbox : '';
        $page_data['paypal_client_id_production'] = isset($paypal[0]->paypal_client_id_production) ? $paypal[0]->paypal_client_id_production : '';
        // PayPal a sa propre devise configurée
        $page_data['paypal_currency'] = isset($paypal[0]->paypal_currency) ? $paypal[0]->paypal_currency : 'USD';
        
        // Determine PayPal enabled status
        if ($page_data['paypal_mode'] == 'sandbox') {
            $page_data['paypal_enabled'] = !empty($page_data['paypal_client_id_sandbox']);
        } else {
            $page_data['paypal_enabled'] = !empty($page_data['paypal_client_id_production']);
        }

        // Load payment gateway view
        $this->load->view('backend/payment_gateway/index', $page_data);
}

	public function payment_success($payment_method = "", $invoice_id = "", $amount_paid = "", $reference = "") {
		// Vérifier l'authentification
		if ($this->session->userdata('admin_login') != 1) {
			redirect(site_url('login'), 'refresh');
			return;
		}

		// Récupérer les détails de la facture
		$invoice_details = $this->crud_model->get_invoice_by_id($invoice_id);
		
		if (empty($invoice_details)) {
			log_message('error', "Tentative de paiement pour une facture inexistante: #{$invoice_id}");
			$this->session->set_flashdata('error_message', get_phrase('invalid_invoice'));
			redirect(site_url('admin/dashboard'), 'refresh');
			return;
		}

		// ========== SÉCURITÉ: Vérifier que la facture n'est pas déjà payée ==========
		if ($invoice_details['status'] === 'paid') {
			log_message('warning', "Tentative de double paiement pour facture #{$invoice_id}");
			$this->session->set_flashdata('error_message', get_phrase('invoice_already_paid'));
			redirect(site_url('admin/dashboard'), 'refresh');
			return;
		}

		// ========== SÉCURITÉ: Utiliser le montant de la BDD, pas celui du client ==========
		$secure_amount = (float) $invoice_details['total_amount'];
		$client_amount = (float) $amount_paid;
		
		// Tolérance de 0.01 pour les erreurs d'arrondi
		if (!empty($amount_paid) && abs($secure_amount - $client_amount) > 0.01) {
			log_message('error', "ALERTE SÉCURITÉ: Manipulation de montant détectée! Facture #{$invoice_id} - Montant BDD: {$secure_amount}, Montant client: {$client_amount}");
			$this->session->set_flashdata('error_message', get_phrase('payment_amount_mismatch'));
			redirect(site_url('admin/payment/' . $invoice_id), 'refresh');
			return;
		}
		
		// Utiliser le montant sécurisé de la BDD
		$amount_paid = $secure_amount;
		$currency = $invoice_details['currency'] ?? 'USD';

		$payment_status = false;
		
		// Traiter le paiement selon la méthode
		if ($payment_method == 'stripe') {
			$stripe_settings = get_payment_settings('stripe_settings', $invoice_details['school_id']);
			$stripe = json_decode($stripe_settings);
			$currency = $stripe[0]->stripe_currency ?? $currency;
			
			// Convertir en tableau si nécessaire
			if (is_object($stripe)) {
				$stripe = [$stripe];
			} elseif (!is_array($stripe)) {
				$stripe = [];
			}
			
			$token_id = $this->input->post('stripeToken');
			
			// Vérifier que le token Stripe est présent
			if (empty($token_id)) {
				log_message('error', "Token Stripe manquant pour facture #{$invoice_id}");
				$this->session->set_flashdata('error_message', get_phrase('payment_error'));
				redirect(site_url('admin/payment/' . $invoice_id), 'refresh');
				return;
			}
			
			$stripe_test_mode = isset($stripe[0]) && isset($stripe[0]->stripe_mode) ? $stripe[0]->stripe_mode : 'on';
			
			if ($stripe_test_mode == 'on') {
				$secret_key = isset($stripe[0]) ? ($stripe[0]->stripe_test_secret_key ?? '') : '';
			} else {
				$secret_key = isset($stripe[0]) ? ($stripe[0]->stripe_live_secret_key ?? '') : '';
			}
			
			if (!empty($secret_key)) {
				$payment_status = $this->payment_model->stripe_payment($token_id, $invoice_id, $amount_paid, $secret_key);
			} else {
				log_message('error', 'Stripe payment failed: missing secret key');
			}
			
		} elseif ($payment_method == 'paypal') {
			// ========== SÉCURITÉ: Valider le paiement PayPal côté serveur ==========
			$paymentID = $this->input->post('paymentID');
			$payerID = $this->input->post('payerID');
			
			if (empty($paymentID) || empty($payerID)) {
				log_message('error', "PayPal: paymentID ou payerID manquant pour facture #{$invoice_id}");
				$this->session->set_flashdata('error_message', get_phrase('payment_error'));
				redirect(site_url('admin/payment/' . $invoice_id), 'refresh');
				return;
			}
			
			// Valider le paiement via l'API PayPal
			$payment_status = $this->validate_paypal_payment($paymentID, $payerID, $amount_paid, $invoice_details['school_id']);
			
			if (!$payment_status) {
				log_message('error', "PayPal: Validation échouée pour facture #{$invoice_id}, paymentID: {$paymentID}");
				$this->session->set_flashdata('error_message', get_phrase('paypal_validation_failed'));
				redirect(site_url('admin/payment/' . $invoice_id), 'refresh');
				return;
			}
			
			// Récupérer la devise PayPal depuis les paramètres
			$paypal_settings = json_decode(get_payment_settings('paypal_settings', $invoice_details['school_id']));
			$currency = $paypal_settings[0]->paypal_currency ?? $currency;
			
		} elseif ($payment_method == 'paystack') {
			$this->load->model('addons/paystack_model');
			$payment_status = $this->paystack_model->check_payment($reference);
		}

		// Si le paiement est réussi
		if ($payment_status === true) {
			// Préparer les données pour mettre à jour la facture
			$data = [
				'payment_method' => $payment_method,
				'invoice_id' => $invoice_id,
				'amount_paid' => $amount_paid
			];

			// Mettre à jour la facture
			$this->db->where('id', $invoice_id);
			$invoice_current = $this->db->get('invoices')->row_array();
			$due_amount = $invoice_current['total_amount'] - ($invoice_current['paid_amount'] ?? 0);
			
			if ($due_amount <= $amount_paid) {
				$updater = [
					'status' => 'paid',
					'payment_method' => $payment_method,
					'paid_amount' => $amount_paid + ($invoice_current['paid_amount'] ?? 0),
                    'currency' => $currency,
					'updated_at' => strtotime(date('d-M-Y'))
				];
				$this->db->where('id', $invoice_id);
				$this->db->update('invoices', $updater);

				// Si c'est une facture subscription_admin, mettre à jour l'école
				if (isset($invoice_details['payment_type']) && $invoice_details['payment_type'] === 'subscription_admin') {
					$school_id = $invoice_details['school_id'];
					
					// Calculer la date de fin d'abonnement (1 mois à partir de maintenant)
					$subscription_end = strtotime('+1 month');
					
					log_message('debug', "Admin payment_success - Updating school subscription: school_id=$school_id, subscription_end=" . date('Y-m-d H:i:s', $subscription_end));
					
					// Mettre à jour l'école
					$school_updater = [
						'is_paid' => 1,
						'is_trial' => 0, // L'essai est terminé, maintenant c'est un abonnement payant
						'subscription_end' => $subscription_end,
						'updated_at' => time()
					];
					$this->db->where('id', $school_id);
					$this->db->update('schools', $school_updater);

					log_message('debug', "Admin payment_success - School updated successfully: is_paid=1, subscription_end=" . date('Y-m-d H:i:s', $subscription_end));

					// Message de succès
					$this->session->set_flashdata('flash_message', get_phrase('subscription_activated_successfully'));
					
					// Rediriger vers le dashboard
					redirect(site_url('admin/dashboard'), 'refresh');
					return;
				}
			}
		}

		// Si le paiement a échoué ou si ce n'est pas une facture subscription_admin
		$this->session->set_flashdata('error_message', get_phrase('payment_failed') ?: 'Le paiement a échoué');
		redirect(site_url('admin/payment/' . $invoice_id), 'refresh');
	}

	/**
	 * ========== SÉCURITÉ: Validation PayPal côté serveur ==========
	 * Vérifie auprès de l'API PayPal que le paiement est bien complété
	 * et que le montant correspond à celui attendu
	 */
	private function validate_paypal_payment($paymentID, $payerID, $expected_amount, $school_id) {
		try {
			// Récupérer les paramètres PayPal
			$paypal_settings = json_decode(get_payment_settings('paypal_settings', $school_id));
			
			if (empty($paypal_settings) || !isset($paypal_settings[0])) {
				log_message('error', 'PayPal: Paramètres PayPal non configurés');
				return false;
			}
			
			$paypal = $paypal_settings[0];
			$mode = $paypal->paypal_mode ?? 'sandbox';
			
			// Sélectionner le client_id et secret selon le mode
			if ($mode === 'sandbox') {
				$client_id = $paypal->paypal_sandbox_client_id ?? '';
				$client_secret = $paypal->paypal_sandbox_secret_key ?? '';
				$api_base = 'https://api.sandbox.paypal.com';
			} else {
				$client_id = $paypal->paypal_production_client_id ?? '';
				$client_secret = $paypal->paypal_production_secret_key ?? '';
				$api_base = 'https://api.paypal.com';
			}
			
			if (empty($client_id) || empty($client_secret)) {
				log_message('error', 'PayPal: Client ID ou Secret manquant');
				return false;
			}
			
			// Étape 1: Obtenir un access token
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $api_base . '/v1/oauth2/token');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');
			curl_setopt($ch, CURLOPT_USERPWD, $client_id . ':' . $client_secret);
			curl_setopt($ch, CURLOPT_HTTPHEADER, [
				'Accept: application/json',
				'Accept-Language: en_US'
			]);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
			
			$response = curl_exec($ch);
			$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			
			if ($http_code !== 200) {
				log_message('error', 'PayPal: Échec obtention access token. HTTP Code: ' . $http_code);
				return false;
			}
			
			$token_data = json_decode($response, true);
			if (empty($token_data['access_token'])) {
				log_message('error', 'PayPal: Access token vide');
				return false;
			}
			
			$access_token = $token_data['access_token'];
			
			// Étape 2: Vérifier le paiement
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $api_base . '/v1/payments/payment/' . $paymentID);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, [
				'Content-Type: application/json',
				'Authorization: Bearer ' . $access_token
			]);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
			
			$response = curl_exec($ch);
			$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			
			if ($http_code !== 200) {
				log_message('error', 'PayPal: Échec vérification paiement. HTTP Code: ' . $http_code);
				return false;
			}
			
			$payment_data = json_decode($response, true);
			
			// Vérifier le statut du paiement
			if (empty($payment_data['state']) || $payment_data['state'] !== 'approved') {
				log_message('error', 'PayPal: Paiement non approuvé. État: ' . ($payment_data['state'] ?? 'inconnu'));
				return false;
			}
			
			// Vérifier le montant
			if (!empty($payment_data['transactions'][0]['amount']['total'])) {
				$paid_amount = (float) $payment_data['transactions'][0]['amount']['total'];
				$expected = (float) $expected_amount;
				
				// Tolérance de 0.01 pour les erreurs d'arrondi
				if (abs($paid_amount - $expected) > 0.01) {
					log_message('error', "PayPal: ALERTE SÉCURITÉ - Montant incorrect! Attendu: {$expected}, Reçu: {$paid_amount}");
					return false;
				}
			} else {
				log_message('error', 'PayPal: Impossible de vérifier le montant du paiement');
				return false;
			}
			
			// Vérifier le payerID
			if (!empty($payment_data['payer']['payer_info']['payer_id'])) {
				if ($payment_data['payer']['payer_info']['payer_id'] !== $payerID) {
					log_message('error', 'PayPal: PayerID ne correspond pas');
					return false;
				}
			}
			
			log_message('info', "PayPal: Paiement #{$paymentID} validé avec succès pour {$paid_amount}");
			return true;
			
		} catch (Exception $e) {
			log_message('error', 'PayPal: Exception lors de la validation - ' . $e->getMessage());
			return false;
		}
	}

	//START CLASS secion
	public function manage_class($param1 = '', $param2 = '', $param3 = '')
    {
        if ($param1 == 'create') {
            $modelResponse = $this->crud_model->class_create();
            $csrf = array(
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash()
            );
            $response = array(
                'status' => $modelResponse['status'],
                'notification' => $modelResponse['notification'],
                'csrf' => $csrf
            );
            echo json_encode($response);
        }

        if ($param1 == 'delete') {
            $response = $this->crud_model->class_delete($param2);
            $csrf = array(
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
            );
            echo json_encode(array('status' => $response['status'], 'notification' => $response['notification'], 'csrf' => $csrf));
        }

        if ($param1 == 'update') {
            $response = $this->crud_model->class_update($param2);
            $csrf = array(
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
            );
            echo json_encode(array('status' => $response['status'], 'notification' => $response['notification'], 'csrf' => $csrf));
        }



        if ($param1 == 'list') {
            $this->load->view('backend/admin/class/list');
        }

        if (empty($param1)) {
            $page_data['folder_name'] = 'class';
            $page_data['page_title'] = 'class';
            $this->load->view('backend/index', $page_data);
        }
    }
	//END CLASS section



	public function filter_recordings()
	{
		$this->load->config('bigbluebutton');
		$bbbUrl = rtrim($this->config->item('bbb_url'), '/') . '/';
		$bbbSecret = $this->config->item('bbb_secret');
	
		$meeting_name = trim($this->input->post('meeting_name', true));
		$date_range = $this->input->post('date_range', true);
		$schoolID = school_id();
	
		// Préparation de la requête principale
		$this->db->select('
			appointments.id,
			appointments.title,
			appointments.start_date AS start,
			appointments.description,
			appointments.sections_id AS section,
			appointments.classe_id,
			classes.name AS class_name,
			appointments.room_id,
			rooms.name AS room_name,
			appointments.meeting_id
		');
		$this->db->from('appointments');
		$this->db->join('rooms', 'rooms.id = appointments.room_id', 'left');
		$this->db->join('classes', 'classes.id = appointments.classe_id', 'left');
		$this->db->where('appointments.Etat', 1);
		$this->db->where('appointments.school_id', $schoolID);
	
		if (!empty($meeting_name)) {
			$this->db->like('appointments.title', $meeting_name);
		}
	
		if (!empty($date_range)) {
			$dates = explode(' to ', $date_range);
			if (count($dates) === 2) {
				$this->db->where('appointments.start_date >=', $dates[0] . ' 00:00:00');
				$this->db->where('appointments.start_date <=', $dates[1] . ' 23:59:59');
			}
		}
	
		$appointments = $this->db->get()->result_array();
	
		foreach ($appointments as &$appointment_rec) {
			$appointment['recordings'] = [];
			$meetingId = $appointment_rec['meeting_id'] ?? null;
	
			if ($meetingId) {
				$params = ['meetingID' => $meetingId];
				$query = http_build_query($params);
				$checksum = sha1('getRecordings' . $query . $bbbSecret);
				$url = $bbbUrl . 'getRecordings?' . $query . '&checksum=' . $checksum;
	
				$ch = curl_init($url);
				curl_setopt_array($ch, [
					CURLOPT_RETURNTRANSFER => 1,
					CURLOPT_SSL_VERIFYPEER => false,
					CURLOPT_SSL_VERIFYHOST => false,
				]);
				$response = curl_exec($ch);
				curl_close($ch);
	
				$xml = @simplexml_load_string($response);
				if ($xml && $xml->returncode == 'SUCCESS') {
					foreach ($xml->recordings->recording as $rec) {
						$appointment_rec['recordings'][] = [
							'meetingID' => (string)$rec->meetingID,
							'playback_url' => (string)$rec->playback->format->url,
							'duration' => (string)$rec->playback->format->length,
							'video_download_url' => (string)$rec->playback->format->url,
							'endTime' => (string)$rec->endTime,
						];
					}
				}
			}
		}
 
		// 🔽 Affichage HTML
		foreach ($appointments as $appointment) {
			// Traitement des sections
			$section_label = '—';
			if (!empty($appointment['section'])) {
				$section_ids = explode(',', $appointment['section']);
				$section_names = [];
				foreach ($section_ids as $id) {
					$name = $this->db->get_where('sections', ['id' => $id])->row('name');
					if ($name) $section_names[] = $name;
				}
				$section_label = implode(', ', $section_names);
			}
	
			// Affichage ligne
			echo '<tr>';
			echo '<td>' . htmlspecialchars($appointment['title']) . '</td>';
			echo '<td>' . htmlspecialchars($appointment['room_name']) . '</td>';
			echo '<td>' . htmlspecialchars($appointment['class_name']) . '</td>';
			echo '<td>' . htmlspecialchars($section_label) . '</td>';
			echo '<td>' . date('d-m-Y H:i', strtotime($appointment['start'])) . '</td>';
			echo '<td>' . (!empty($appointment_rec['recordings']) ? $appointment_rec['recordings'][0]['duration'] : '—') . '</td>';
	
			echo '<td>';
			if (!empty($appointment_rec['recordings'])) {
				$rec = $appointment_rec['recordings'][0];
				$endTime = !empty($rec['endTime']) ? (int)$rec['endTime'] / 1000 : null;
				$isExpired = $endTime ? (time() > ($endTime + 7 * 24 * 3600)) : false;
	
				if ($isExpired) {
					echo '<span class="badge bg-warning text-dark">Expired</span>';
				} elseif (!empty($rec['playback_url'])) {
					echo '<a href="' . htmlspecialchars($rec['playback_url']) . '" target="_blank" class="btn btn-sm btn-success">VIDEO</a>';
				} else {
					echo '<span class="badge bg-danger">NOT RECORDED</span>';
				}
			} else {
				echo '<span class="badge bg-danger">NOT RECORDED</span>';
			}
			echo '</td>';
	
			echo '<td>';
			if (!empty($appointment_rec['recordings'])) {
				$rec = $appointment_rec['recordings'][0];
				echo '<a href="' . htmlspecialchars($rec['video_download_url']) . '" class="btn btn-sm btn-success">Download</a> ';
			}
	
			echo '<a href="' . site_url('superadmin/delete_appointment_and_recording/' . $appointment['id']) . '" class="btn btn-sm btn-danger" onclick="return confirm(\'❗ Cette action supprimera le rendez-vous et l’enregistrement associé. Continuer ?\')">🗑️ Supprimer</a>';
			echo '</td></tr>';
		}
	}
	

	
	public function export_recordings_csv() {
		$this->load->config('bigbluebutton');
		$bbbUrl = $this->config->item('bbb_url');
		$bbbSecret = $this->config->item('bbb_secret');
		$meeting_name = $this->input->get('meeting_name', true);
		$date_range = $this->input->get('date_range', true);
		$schoolID = school_id();
		// $this->db->select('*')->from('appointments');

		$this->db->select('
			appointments.id,
			appointments.title,
			appointments.start_date AS start,
			appointments.description,
			appointments.sections_id AS section,
			appointments.classe_id,
			classes.name AS class_name,
			appointments.room_id,
			rooms.name AS room_name,
			appointments.meeting_id
		');
		$this->db->from('appointments');
		$this->db->join('rooms', 'rooms.id = appointments.room_id', 'left');
		$this->db->join('classes', 'classes.id = appointments.classe_id', 'left');
		$this->db->where('appointments.Etat', 1);
		$this->db->where('appointments.school_id', $schoolID);
		// Filtrer par nom de réunion
		if (!empty($meeting_name)) {
		  $this->db->like('appointments.title', $meeting_name);
		}
	
		if (!empty($date_range)) {
			$dates = explode(' to ', $date_range);
			if (count($dates) === 2) {
				$this->db->where('appointments.start_date >=', $dates[0] . ' 00:00:00');
				$this->db->where('appointments.start_date <=', $dates[1] . ' 23:59:59');
			}
		}

		$appointments = $this->db->get()->result_array();
		

		// Préparer le fichier CSV
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment;filename="recordings.csv"');



		$output = fopen('php://output', 'w');
		fputcsv($output, ['Name', 'Room', 'Class', 'Section', 'Creation Date', 'Duration', 'Recording']);

		foreach ($appointments as &$appointment_reco) {
		  $meetingId = $appointment_reco['meeting_id'] ?? null;
		  $appointment_reco['recordings'] = [];
	 
		  if ($meetingId) {
			  // Générer l’URL avec meetingID spécifique
			  // $query = http_build_query(['meetingID' => $meetingId]);
			  // $checksum = sha1('getRecordings' . $query . $bbbSecret);
			  // $url = $bbbUrl . 'api/getRecordings?' . $query . '&checksum=' . $checksum;
			  $params = ['meetingID' => $meetingId];
			  $query = http_build_query($params);
			  $checksum = sha1('getRecordings' . $query . $bbbSecret);
			  $url = $bbbUrl . 'getRecordings?' . $query . '&checksum=' . $checksum;
  
			  // Appel de l’API
			  $ch = curl_init();
			  curl_setopt($ch, CURLOPT_URL, $url);
			  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			  $response = curl_exec($ch);
			  curl_close($ch);
  
			  $xml = @simplexml_load_string($response);
	 
			  if ($xml && $xml->returncode == 'SUCCESS') {
				  foreach ($xml->recordings->recording as $rec) {
					  $appointment_reco['recordings'][] = [
						  'meetingID' => (string) $rec->meetingID,
						  'playback_url' => (string) $rec->playback->format->url,
						  'duration' => (string) $rec->playback->format->length,
						  'video_download_url' => (string) $rec->playback->format->url,
						  'endTime' => (string) $rec->endTime
					  ];
				  }
			  }
		  }
	  }

		foreach ($appointments as $appointment) {
			$recording_url = !empty($appointment_reco['recordings']) && isset($appointment_reco['recordings'][0]['playback_url']) 
				? $appointment_reco['recordings'][0]['playback_url'] 
				: 'NOT RECORDED';

				   // Traitement des sections
			$section_label = '—';
			if (!empty($appointment['section'])) {
				$section_ids = explode(',', $appointment['section']);
				$section_names = [];
				foreach ($section_ids as $id) {
					$name = $this->db->get_where('sections', ['id' => $id])->row('name');
					if ($name) $section_names[] = $name;
				}
				$section_label = implode(', ', $section_names);
			}
		 

			fputcsv($output, [
				$appointment['title'],
				$appointment['room_name'],
				$appointment['class_name'],
				$section_label,
				date('d-m-Y H:i', strtotime($appointment['start'])),
				!empty($appointment_reco['recordings']) && isset($appointment_reco['recordings'][0]['duration']) 
					? $appointment_reco['recordings'][0]['duration'] 
					: '—',
				$recording_url
			]);
		}

		fclose($output);
		exit;
	}

	  // PAYMENT SETTINGS MANAGER
	  public function payment_settings($param1 = "", $param2 = "")
	  {
		if ($param1 == 'system') {
		  $response = $this->settings_model->update_system_currency_settings();
		//   echo $response;
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
				'csrfName' => $this->security->get_csrf_token_name(),
				'csrfHash' => $this->security->get_csrf_hash(),
				);
			
			// Renvoyer la réponse avec un nouveau jeton CSRF
			echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}
		if ($param1 == 'vat') {
			
		  $response = $this->settings_model->update_system_vat();
		
		
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
				'csrfName' => $this->security->get_csrf_token_name(),
				'csrfHash' => $this->security->get_csrf_hash(),
				);
			
			// Renvoyer la réponse avec un nouveau jeton CSRF
			echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}
		if ($param1 == 'price') {
			
		  $response = $this->settings_model->update_system_price();
		
		
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
				'csrfName' => $this->security->get_csrf_token_name(),
				'csrfHash' => $this->security->get_csrf_hash(),
				);
			
			// Renvoyer la réponse avec un nouveau jeton CSRF
			echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}
		if ($param1 == 'paypal') {
		  $response = $this->settings_model->update_paypal_settings();
		//   echo $response;
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
				'csrfName' => $this->security->get_csrf_token_name(),
				'csrfHash' => $this->security->get_csrf_hash(),
				);
			
			// Renvoyer la réponse avec un nouveau jeton CSRF
			echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}
		if ($param1 == 'stripe') {
		  $response = $this->settings_model->update_stripe_settings();
		//   echo $response;
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
				'csrfName' => $this->security->get_csrf_token_name(),
				'csrfHash' => $this->security->get_csrf_hash(),
				);
			
			// Renvoyer la réponse avec un nouveau jeton CSRF
			echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}
	
		// showing the Payment Settings file
		if (empty($param1)) {
		  $page_data['folder_name'] = 'settings';
		  $page_data['page_title'] = 'payment_settings';
		  $page_data['settings_type'] = 'payment_settings';
		  $this->load->view('backend/index', $page_data);
		}
	  }

	  // LANGUAGE SETTINGS
	public function language($param1 = "", $param2 = "")
	{
		// adding language
		// if ($param1 == 'create') {
		// 	$response = $this->settings_model->create_language();
		// 	// echo $response;
		// 	// Préparer la réponse avec un nouveau jeton CSRF
		// 	$csrf = array(
		// 		'csrfName' => $this->security->get_csrf_token_name(),
		// 		'csrfHash' => $this->security->get_csrf_hash(),
		// 			);
					
		// 	// Renvoyer la réponse avec un nouveau jeton CSRF
		// 	echo json_encode(array('status' => $response, 'csrf' => $csrf));
		// }

		// // update language
		// if ($param1 == 'update') {
		// 	$response = $this->settings_model->update_language($param2);
		// 	// echo $response;
		// 	// Préparer la réponse avec un nouveau jeton CSRF
		// 	$csrf = array(
		// 		'csrfName' => $this->security->get_csrf_token_name(),
		// 		'csrfHash' => $this->security->get_csrf_hash(),
		// 			);
				
		// 	// Renvoyer la réponse avec un nouveau jeton CSRF
		// 	echo json_encode(array('status' => $response, 'csrf' => $csrf));
		// }

		// // deleting language
		// if ($param1 == 'delete') {
		// 	$response = $this->settings_model->delete_language($param2);
		// 	// echo $response;
		// 	// Préparer la réponse avec un nouveau jeton CSRF
		// 	$csrf = array(
		// 		'csrfName' => $this->security->get_csrf_token_name(),
		// 		'csrfHash' => $this->security->get_csrf_hash(),
		// 			);
				
		// 	// Renvoyer la réponse avec un nouveau jeton CSRF
		// 	echo json_encode(array('status' => $response, 'csrf' => $csrf));
		// 	}

		// // showing the list of language
		// if ($param1 == 'list') {
		// 	$this->load->view('backend/admin/language/list');
		// }

		 if ($param1 == 'active') {
			$user_id = $this->session->userdata('user_id');
			$this->settings_model->update_system_language($user_id, $param2);

			// Redirige vers la page précédente si elle existe, sinon vers le dashboard
			$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
			if (!empty($referer)) {
				redirect($referer, 'refresh');
			} else {
				redirect(site_url('home'), 'refresh');
			}
		}
  

		// // showing the list of language
		// if ($param1 == 'update_phrase') {
		// 	$current_editing_language = htmlspecialchars($this->input->post('currentEditingLanguage'));
		// 	$updatedValue = htmlspecialchars($this->input->post('updatedValue'));
		// 	$key = htmlspecialchars($this->input->post('key'));
		// 	saveJSONFile($current_editing_language, $key, $updatedValue);
		// 	$response =  $current_editing_language . ' ' . $key . ' ' . $updatedValue;
		// 	// Préparer la réponse avec un nouveau jeton CSRF
		// 	$csrf = array(
		// 		'csrfName' => $this->security->get_csrf_token_name(),
		// 		'csrfHash' => $this->security->get_csrf_hash(),
		// 			);
					
		// 	// Renvoyer la réponse avec un nouveau jeton CSRF
		// 	echo json_encode(array('response' => $response, 'csrf' => $csrf));
		// }

		// GET THE DROPDOWN OF LANGUAGES
		if ($param1 == 'dropdown') {
		    $this->load->view('backend/admin/language/dropdown');
		}
		// showing the index file
		if (empty($param1)) {
			$page_data['folder_name'] = 'language';
			$page_data['page_title'] = 'languages';
			$this->load->view('backend/index', $page_data);
		}
	}

	//START CLASS_ROOM section
	public function class_room($param1 = '', $param2 = '')
	{

		if ($param1 == 'create') {
			$modelResponse = $this->crud_model->class_room_create();
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
			  'name' => $this->security->get_csrf_token_name(),
			  'hash' => $this->security->get_csrf_hash()
		  );
		  
		  // Fusionner la réponse du modèle avec le CSRF
		  $response = array(
			  'status' => $modelResponse['status'],
			  'notification' => $modelResponse['notification'],
			  'csrf' => $csrf
		  );
		  
		  echo json_encode($response);
		}

		if ($param1 == 'update') {
			$response = $this->crud_model->class_room_update($param2);
			// echo $response;
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
				'csrfName' => $this->security->get_csrf_token_name(),
				'csrfHash' => $this->security->get_csrf_hash(),
				);
			
			// Renvoyer la réponse avec un nouveau jeton CSRF
			echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}

		if ($param1 == 'delete') {
			$response = $this->crud_model->class_room_delete($param2);
			// echo $response;
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
				'csrfName' => $this->security->get_csrf_token_name(),
				'csrfHash' => $this->security->get_csrf_hash(),
				);
			
			// Renvoyer la réponse avec un nouveau jeton CSRF
			echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}

		// PROVIDE A LIST OF SECTION ACCORDING TO CLASS ID
		if ($param1 == 'list') {
			$this->load->view('backend/admin/class_room/list');
		}

		if (empty($param1)) {
			$page_data['folder_name'] = 'class_room';
			$page_data['page_title'] = 'class_room';
			$this->load->view('backend/index', $page_data);
		}
	}
	//END CLASS_ROOM section





	//START SYLLABUS section
	public function syllabus($param1 = '', $param2 = '', $param3 = '')
	{

		if ($param1 == 'create') {
			$modelResponse = $this->crud_model->syllabus_create();
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
			  'name' => $this->security->get_csrf_token_name(),
			  'hash' => $this->security->get_csrf_hash()
		  );
		  
		  // Fusionner la réponse du modèle avec le CSRF
		  $response = array(
			  'status' => $modelResponse['status'],
			  'notification' => $modelResponse['notification'],
			  'csrf' => $csrf
		  );
		  
		  echo json_encode($response);
		}

		if ($param1 == 'delete') {
			$response = $this->crud_model->syllabus_delete($param2);
			// echo $response;
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
				'csrfName' => $this->security->get_csrf_token_name(),
				'csrfHash' => $this->security->get_csrf_hash(),
				);
			
			// Renvoyer la réponse avec un nouveau jeton CSRF
			echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}

		if ($param1 == 'list') {
			$page_data['class_id'] = $param2;
			
			$this->load->view('backend/admin/syllabus/list', $page_data);
		}

		if (empty($param1)) {
			$page_data['folder_name'] = 'syllabus';
			$page_data['page_title'] = 'syllabus';
			$this->load->view('backend/index', $page_data);
		}
	}
	//END SYLLABUS section

	//START TEACHER section
	public function teacher($param1 = '', $param2 = '', $param3 = '')
	{


		if ($param1 == 'create') {
			$modelResponse = $this->user_model->create_teacher();
      // Préparer la réponse avec un nouveau jeton CSRF
      $csrf = array(
        'name' => $this->security->get_csrf_token_name(),
        'hash' => $this->security->get_csrf_hash()
    );
    
    // Fusionner la réponse du modèle avec le CSRF
    $response = array(
        'status' => $modelResponse['status'],
        'notification' => $modelResponse['notification'],
        'csrf' => $csrf
    );
    
      echo json_encode($response);
		}

		if ($param1 == 'update') {
			$response = $this->user_model->update_teacher($param2);
			// echo $response;
				// Préparer la réponse avec un nouveau jeton CSRF
				$csrf = array(
					'csrfName' => $this->security->get_csrf_token_name(),
					'csrfHash' => $this->security->get_csrf_hash(),
					);
				
			// Renvoyer la réponse avec un nouveau jeton CSRF
			echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}

		if ($param1 == 'delete') {
			$teacher_id = $this->db->get_where('teachers', array('user_id' => $param2))->row('id');
			$response = $this->user_model->delete_teacher($param2, $teacher_id);
			// echo $response;
			     // Préparer la réponse avec un nouveau jeton CSRF
				 $csrf = array(
					'csrfName' => $this->security->get_csrf_token_name(),
					 'csrfHash' => $this->security->get_csrf_hash(),
					 );
				 
		   // Renvoyer la réponse avec un nouveau jeton CSRF
		   echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}

		if ($param1 == 'list') {
			$this->load->view('backend/admin/teacher/list');
		}

		if (empty($param1)) {
			$page_data['folder_name'] = 'teacher';
			$page_data['page_title'] = 'techers';
			$this->load->view('backend/index', $page_data);
		}
	}
	//END TEACHER section



	//START TEACHER PERMISSION section
	public function permission($param1 = '', $param2 = '', $param3 = '')
	{

		if ($param1 == 'filter') {
			$page_data['class_id'] = $param2;
			
			$this->load->view('backend/admin/permission/list', $page_data);
		}

		if ($param1 == 'modify_permission') {
			$page_data['class_id'] = htmlspecialchars($this->input->post('class_id'));
			
			$this->user_model->teacher_permission();
			// $this->load->view('backend/admin/permission/list', $page_data);

			// Charger la vue mise à jour
			$response_html = $this->load->view('backend/admin/permission/list', $page_data, TRUE);
			// Préparer le nouveau jeton CSRF
			$csrf = array(
			'csrfName' => $this->security->get_csrf_token_name(),
			'csrfHash' => $this->security->get_csrf_hash(),
				);

			// Renvoyer la réponse JSON avec le HTML mis à jour et le nouveau jeton CSRF
			echo json_encode(array('status' => $response_html, 'csrfName' => $csrf['csrfName'], 'csrfHash' => $csrf['csrfHash']));

		}

		if (empty($param1)) {
			$page_data['folder_name'] = 'permission';
			$page_data['page_title'] = 'teacher_permissions';
			$this->load->view('backend/index', $page_data);
		}
	}
	//END TEACHER PERMISSION section


	


	//START ACCOUNTANT section
	public function accountant($param1 = '', $param2 = '')
	{

		if ($param1 == 'create') {
			$modelResponse = $this->user_model->accountant_create();
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
				'name' => $this->security->get_csrf_token_name(),
				'hash' => $this->security->get_csrf_hash()
			);
			
			// Fusionner la réponse du modèle avec le CSRF
			$response = array(
				'status' => $modelResponse['status'],
				'notification' => $modelResponse['notification'],
				'csrf' => $csrf
			);
			
			echo json_encode($response);
		}

		if ($param1 == 'update') {
			$response = $this->user_model->accountant_update($param2);
			// echo $response;
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
				'csrfName' => $this->security->get_csrf_token_name(),
				'csrfHash' => $this->security->get_csrf_hash(),
				);
			
			// Renvoyer la réponse avec un nouveau jeton CSRF
			echo json_encode(array('status' => $response, 'csrf' => $csrf));

		}

		if ($param1 == 'delete') {
			$response = $this->user_model->accountant_delete($param2);
			// echo $response;
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
				'csrfName' => $this->security->get_csrf_token_name(),
				'csrfHash' => $this->security->get_csrf_hash(),
				);
			
			// Renvoyer la réponse avec un nouveau jeton CSRF
			echo json_encode(array('status' => $response, 'csrf' => $csrf));

		}

		// show data from database
		if ($param1 == 'list') {
			$this->load->view('backend/admin/accountant/list');
		}

		if (empty($param1)) {
			$page_data['folder_name'] = 'accountant';
			$page_data['page_title'] = 'accountant';
			$this->load->view('backend/index', $page_data);
		}
	}
	//END ACCOUNTANT section


	//START LIBRARIAN section
	public function librarian($param1 = '', $param2 = '')
	{

		if ($param1 == 'create') {
			$modelResponse = $this->user_model->librarian_create();
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
			  'name' => $this->security->get_csrf_token_name(),
			  'hash' => $this->security->get_csrf_hash()
		  );
		  
		  // Fusionner la réponse du modèle avec le CSRF
		  $response = array(
			  'status' => $modelResponse['status'],
			  'notification' => $modelResponse['notification'],
			  'csrf' => $csrf
		  );
		  
			echo json_encode($response);
		}

		if ($param1 == 'update') {
			$response = $this->user_model->librarian_update($param2);
			// echo $response;
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
				'csrfName' => $this->security->get_csrf_token_name(),
				'csrfHash' => $this->security->get_csrf_hash(),
				);
			
			// Renvoyer la réponse avec un nouveau jeton CSRF
			echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}

		if ($param1 == 'delete') {
			$response = $this->user_model->librarian_delete($param2);
			// echo $response;
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
				'csrfName' => $this->security->get_csrf_token_name(),
				'csrfHash' => $this->security->get_csrf_hash(),
				);
			
			// Renvoyer la réponse avec un nouveau jeton CSRF
			echo json_encode(array('status' => $response, 'csrf' => $csrf));

		}

		// show data from database
		if ($param1 == 'list') {
			$this->load->view('backend/admin/librarian/list');
		}

		if (empty($param1)) {
			$page_data['folder_name'] = 'librarian';
			$page_data['page_title'] = 'librarian';
			$this->load->view('backend/index', $page_data);
		}
	}
	//END LIBRARIAN section

	//START CLASS ROUTINE section
	public function routine($param1 = '', $param2 = '', $param3 = '', $param4 = '')
	{

		if ($param1 == 'create') {
			$modelResponse = $this->crud_model->routine_create();
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
			  'name' => $this->security->get_csrf_token_name(),
			  'hash' => $this->security->get_csrf_hash()
		  );
		  
		  // Fusionner la réponse du modèle avec le CSRF
		  $response = array(
			  'status' => $modelResponse['status'],
			  'notification' => $modelResponse['notification'],
			  'csrf' => $csrf
		  );
		  
		  echo json_encode($response);
		}

		if ($param1 == 'update') {
			$response = $this->crud_model->routine_update($param2);
			// echo $response;
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
				'csrfName' => $this->security->get_csrf_token_name(),
				'csrfHash' => $this->security->get_csrf_hash(),
				);
			
			// Renvoyer la réponse avec un nouveau jeton CSRF
			echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}

		if ($param1 == 'delete') {
			$response = $this->crud_model->routine_delete($param2);
			// echo $response;
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
				'csrfName' => $this->security->get_csrf_token_name(),
				'csrfHash' => $this->security->get_csrf_hash(),
				);
			
			// Renvoyer la réponse avec un nouveau jeton CSRF
			echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}

		if ($param1 == 'filter') {
			$page_data['class_id'] = $param2;
	
			$this->load->view('backend/admin/routine/list', $page_data);
		}

		if (empty($param1)) {
			$page_data['folder_name'] = 'routine';
			$page_data['page_title'] = 'routine';
			$this->load->view('backend/index', $page_data);
		}
	}
	//END CLASS ROUTINE section


	//START DAILY ATTENDANCE section
	public function attendance($param1 = '', $param2 = '', $param3 = '')
	{

		if ($param1 == 'take_attendance') {
			$modelResponse = $this->crud_model->take_attendance();
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
			  'name' => $this->security->get_csrf_token_name(),
			  'hash' => $this->security->get_csrf_hash()
		  );
		  
		  // Fusionner la réponse du modèle avec le CSRF
		  $response = array(
			  'status' => $modelResponse['status'],
			  'notification' => $modelResponse['notification'],
			  'csrf' => $csrf
		  );
		  
		  echo json_encode($response);

		}

		if ($param1 == 'filter') {
			$date = '01 ' . $this->input->post('month') . ' ' . $this->input->post('year');
			$page_data['attendance_date'] = strtotime($date);
			$page_data['class_id'] = $this->input->post('class_id');
		
			$page_data['month'] = $this->input->post('month');
			$page_data['year'] = $this->input->post('year');
			// $this->load->view('backend/admin/attendance/list', $page_data);
			// Charger la vue mise à jour
			$response_html = $this->load->view('backend/admin/attendance/list', $page_data, TRUE);
			// Préparer le nouveau jeton CSRF
			$csrf = array(
					'csrfName' => $this->security->get_csrf_token_name(),
					'csrfHash' => $this->security->get_csrf_hash(),
				 );
			
			// Renvoyer la réponse JSON avec le HTML mis à jour et le nouveau jeton CSRF
			echo json_encode(array('status' => $response_html, 'csrf' => $csrf));

		}

		if ($param1 == 'student') {
			$page_data['attendance_date'] = strtotime($this->input->post('date'));
			$page_data['class_id'] = htmlspecialchars($this->input->post('class_id'));
		
			// $this->load->view('backend/admin/attendance/student', $page_data);

			        // Charger la vue mise à jour
					$response_html = $this->load->view('backend/admin/attendance/student', $page_data, TRUE);
					// Préparer le nouveau jeton CSRF
					$csrf = array(
					 'csrfName' => $this->security->get_csrf_token_name(),
					 'csrfHash' => $this->security->get_csrf_hash(),
				 );
			
				 // Renvoyer la réponse JSON avec le HTML mis à jour et le nouveau jeton CSRF
				 echo json_encode(array('status' => $response_html, 'csrf' => $csrf));

		}

		if (empty($param1)) {
			$page_data['folder_name'] = 'attendance';
			$page_data['page_title'] = 'attendance';
			$this->load->view('backend/index', $page_data);
		}
	}
	//END DAILY ATTENDANCE section


	//START EVENT CALENDAR section
	public function event_calendar($param1 = '', $param2 = '')
	{

		if ($param1 == 'create') {
			$modelResponse = $this->crud_model->event_calendar_create();
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
			  'name' => $this->security->get_csrf_token_name(),
			  'hash' => $this->security->get_csrf_hash()
		  );
		  
		  // Fusionner la réponse du modèle avec le CSRF
		  $response = array(
			  'status' => $modelResponse['status'],
			  'notification' => $modelResponse['notification'],
			  'csrf' => $csrf
		  );
		  
		  echo json_encode($response);
		}

		if ($param1 == 'update') {
			$response = $this->crud_model->event_calendar_update($param2);
			// echo $response;
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
				'csrfName' => $this->security->get_csrf_token_name(),
				'csrfHash' => $this->security->get_csrf_hash(),
				);
			
			// Renvoyer la réponse avec un nouveau jeton CSRF
			echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}

		if ($param1 == 'delete') {
			$response = $this->crud_model->event_calendar_delete($param2);
			// echo $response;
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
				'csrfName' => $this->security->get_csrf_token_name(),
				'csrfHash' => $this->security->get_csrf_hash(),
				);
			
			// Renvoyer la réponse avec un nouveau jeton CSRF
			echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}

		if ($param1 == 'all_events') {
			echo $this->crud_model->all_events();
		}

		if ($param1 == 'list') {
			$this->load->view('backend/admin/event_calendar/list');
		}

		if (empty($param1)) {
			$page_data['folder_name'] = 'event_calendar';
			$page_data['page_title'] = 'event_calendar';
			$this->load->view('backend/index', $page_data);
		}
	}
	//END EVENT CALENDAR section



	//START STUDENT ADN ADMISSION section
	public function student($param1 = '', $param2 = '', $param3 = '', $param4 = '', $param5 = '')
  {
    $this->session->unset_session();
    $page_data['class_id'] = '';
  

    if ($param1 == 'create') {
      //form view
      if ($param2 == 'bulk') {
        $page_data['aria_expand'] = 'bulk';
        $page_data['working_page'] = 'create';
        $page_data['folder_name'] = 'student';
        $page_data['page_title'] = 'add_student';
        $this->load->view('backend/index', $page_data);
      } elseif ($param2 == 'excel') {
        $page_data['aria_expand'] = 'excel';
        $page_data['working_page'] = 'create';
        $page_data['folder_name'] = 'student';
        $page_data['page_title'] = 'add_student';
        $this->load->view('backend/index', $page_data);
      } else {
        $page_data['aria_expand'] = 'single';
        $page_data['working_page'] = 'create';
        $page_data['folder_name'] = 'student';
        $page_data['page_title'] = 'add_student';
        $this->load->view('backend/index', $page_data);
      }
    }

    //create to database
    if ($param1 == 'create_single_student') {

      if ($param2 == "submit") {
        header('Content-Type: application/json'); // Force le retour JSON
        $response_from_model = $this->user_model->single_student_create();
        $status = ($response_from_model === true); // Check if the model returned true for success
        //$this->session->set_flashdata('flash_message', get_phrase('student_added_successfully'));
        // Ajout du token CSRF à la réponse
        $response = [
          'status' => $status,
          'message' => $status ? get_phrase('student_added_successfully') : $this->session->flashdata('error'),
          'redirect' => site_url('admin/student'),
          'csrf' => [
              'name' => $this->security->get_csrf_token_name(),
              'hash' => $this->security->get_csrf_hash()
          ]
      ];

      echo json_encode($response);
      exit;
    } else {
        // Load the view with filtered data
        $page_data['class_id'] = html_escape($this->input->post('class_id'));
        
        $page_data['working_page'] = 'filter';
        $page_data['folder_name'] = 'student';
        $page_data['page_title'] = 'student_list';

        $this->load->view('backend/index', $page_data);
    }
  }else {
    // Nouveau else ajouté pour la condition parente
    $this->session->set_flashdata('flash_message', get_phrase('welcome_back'));
  }

    if ($param1 == 'create_bulk_student') {
      $response = $this->user_model->bulk_student_create();
      // echo $response;
      // Préparer la réponse avec un nouveau jeton CSRF
      $csrf = array(
            'csrfName' => $this->security->get_csrf_token_name(),
              'csrfHash' => $this->security->get_csrf_hash(),
            );
            
      // Renvoyer la réponse avec un nouveau jeton CSRF
      echo json_encode(array('status' => $response, 'csrf' => $csrf));
    }

    if ($param1 == 'create_excel') {
      $response = $this->user_model->excel_create();
      // die($response) ;
      // Préparer la réponse avec un nouveau jeton CSRF
      $csrf = array(
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
            );
                
      // Renvoyer la réponse avec un nouveau jeton CSRF
      echo json_encode(array('status' => $response, 'csrf' => $csrf));
    }

   // form view
   if ($param1 == 'edit') {
    $page_data['student_id'] = $param2;
    $page_data['working_page'] = 'edit';
    $page_data['folder_name'] = 'student';
    $page_data['page_title'] = 'update_student_information';
    $this->load->view('backend/index', $page_data);
  }


    if ($param1 == 'status') {
      $this->db->where('id', $param3);
      $this->db->update('users', array('status' => $param4));
      $response = array(
        'status' => true,
        'notification' => get_phrase('status_has_been_updated')
      );
      
        // Préparer la réponse avec un nouveau jeton CSRF
        $csrf = array(
                  'csrfName' => $this->security->get_csrf_token_name(),
                  'csrfHash' => $this->security->get_csrf_hash(),
                );
              
      // Renvoyer la réponse avec un nouveau jeton CSRF
      echo json_encode(array('status' => json_encode($response), 'csrf' => $csrf));
    }

    //updated to database
    if ($param1 == 'updated') {
      $response = $this->user_model->student_update($param2, $param3);
      // echo $response;
         // Préparer la réponse avec un nouveau jeton CSRF
         $csrf = array(
          'csrfName' => $this->security->get_csrf_token_name(),
          'csrfHash' => $this->security->get_csrf_hash(),
      );
  
      // Renvoyer la réponse avec un nouveau jeton CSRF
       echo json_encode(array('status' => $response, 'csrf' => $csrf));
    }
    //updated to database
    if ($param1 == 'id_card') {
      $page_data['student_id'] = $param2;
      $page_data['folder_name'] = 'student';
      $page_data['page_title'] = 'identity_card';
      $page_data['page_name'] = 'id_card';
      $this->load->view('backend/index', $page_data);
    }

    if ($param1 == 'delete') {
      $response = $this->user_model->delete_student($param2, $param3);
      // echo $response;
       // Préparer la réponse avec un nouveau jeton CSRF
      $csrf = array(
        'csrfName' => $this->security->get_csrf_token_name(),
        'csrfHash' => $this->security->get_csrf_hash(),
    );

    // Renvoyer la réponse avec un nouveau jeton CSRF
     echo json_encode(array('status' => $response, 'csrf' => $csrf));
    }

    if ($param1 == 'filter') {
            $page_data['class_id'] = ($param2 == '' || $param2 == 'all') ? 'all' : $param2;
           
            $html_content = $this->load->view('backend/admin/student/list', $page_data, TRUE);
            $csrf = array(
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
            );
            echo json_encode(array('html' => $html_content, 'csrf' => $csrf));
        }

        if (empty($param1)) {
            $page_data['class_id'] = 'all';
           
            $page_data['working_page'] = 'filter';
            $page_data['folder_name'] = 'student';
            $page_data['page_title'] = 'student_list';
            $this->load->view('backend/index', $page_data);
        }
  }
	//END STUDENT ADN ADMISSION section


	//START EXAM section
	public function exam($param1 = '', $param2 = '')
{
    if ($param1 == 'create') {
        $response = $this->crud_model->exam_create();
        $response_data = json_decode($response, true);
        
        // Récupérer la classe sélectionnée (si disponible dans les données POST)
        $class_id = $this->input->post('class_id') ?: '';
        
        // Ajouter un nouveau jeton CSRF
        $csrf = array(
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
        );
        
        // Construire la réponse
        $output = array(
            'status' => $response_data['status'] ?? ($response ? true : false),
            'message' => $response_data['notification'] ?? ($response ? 'Exam created successfully' : 'Failed to create exam'),
            'class_id' => $class_id, // Inclure l'ID de la classe pour le frontend
            'csrf' => $csrf
        );
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($output));
    }


    if ($param1 == 'update') {
        $response = $this->crud_model->exam_update($param2);
        // Vérifier si la réponse est déjà encodée en JSON et la décoder
        if (is_string($response) && (json_decode($response) !== null)) {
            $response = json_decode($response, true); // Convertir en tableau
        }
        
        $exam = $this->db->get_where('exams', array('id' => $param2))->row_array();
        $class_id = $exam['class_id'] ?? ''; // Récupérer l'ID de la classe de l'examen
        
        if ($exam) {
            $exam['formatted_date'] = date('D, d-M-Y H:i', $exam['starting_date']);
            $class = $this->db->get_where('classes', array('id' => $exam['class_id']))->row_array();
           
            $exam['class_name'] = $class ? $class['name'] : 'No Class';
           
            $output = array(
                'status' => isset($response['status']) ? $response['status'] : $response,
                'exam' => $exam,
                'class_id' => $class_id, // Inclure l'ID de la classe
                'message' => $response['notification'] ?? ($response ? 'Exam updated successfully' : 'Failed to update exam'),
                'csrf' => array(
                    'csrfName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                )
            );
        } else {
            $output = array(
                'status' => false,
                'message' => 'Exam not found',
                'csrf' => array(
                    'csrfName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                )
            );
        }
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($output));
    }

    if ($param1 == 'delete') {
        $response = $this->crud_model->exam_delete($param2);
        $this->output
            ->set_content_type('application/json')
            ->set_output($response);
    }

    if ($param1 == 'list') {
        $this->load->view('backend/admin/exam/list');
    }

    if (empty($param1)) {
        $page_data['folder_name'] = 'exam';
        $page_data['page_title'] = 'Certifications';
        $this->load->view('backend/index', $page_data);
    }
}
	//END EXAM section

		//HUMHUB DASHBOARD
		public function wall()
		{
			// 1) Vérifier que c'est bien un admin
			if (! $this->session->userdata('admin_login')) {
				show_error('Accès réservé aux administrateurs.');
			}
			
			// 2) Récupérer les infos du user (ici : depuis la table Wayo)
			$userId = $this->session->userdata('user_id');
			$wUser  = $this->db->get_where('users', ['id' => $userId])->row();
			if (empty($wUser) || ! filter_var($wUser->email, FILTER_VALIDATE_EMAIL)) {
				log_message('error', 'Invalid Wayo user data: ' . print_r($wUser, true));
				show_error('Impossible de retrouver votre compte Wayo pour SSO.');
			}
			log_message('debug', 'Wayo user data: ' . print_r($wUser, true));

			// 3) Stocker temporairement cet objet pour la librairie SSO
			$this->session->set_userdata('user', $wUser);

			// 4) Générer l’URL SSO
			$iframeUrl = $this->humhub_sso->provisionAndGetIframeUrl();
			log_message('debug', 'Generated iframe URL: ' . $iframeUrl);

			if (! $iframeUrl) {
				show_error('Impossible de générer l’URL SSO HumHub.');
			}
			
			// 5) Passer à la vue
			$page_data = [
				'folder_name' => 'humhub',
				'page_title'  => 'wall',
				'page_name'   => 'wall',
				'iframe_url'  => $iframeUrl,
			];
			$this->load->view('backend/index', $page_data);
		}
			//HUMHUB PEOPLE
		public function people()
		{
			// 1) Vérifier que c'est bien un admin
			if (! $this->session->userdata('admin_login')) {
				show_error('Accès réservé aux administrateurs.');
			}
			
			// 2) Récupérer les infos du user (ici : depuis la table Wayo)
			$userId = $this->session->userdata('user_id');
			$wUser  = $this->db->get_where('users', ['id' => $userId])->row();

			if (empty($wUser) || ! filter_var($wUser->email, FILTER_VALIDATE_EMAIL)) {
				log_message('error', 'Invalid Wayo user data: ' . print_r($wUser, true));
				show_error('Impossible de retrouver votre compte Wayo pour SSO.');
			}
			log_message('debug', 'Wayo user data: ' . print_r($wUser, true));

			// 3) Stocker temporairement cet objet pour la librairie SSO
			$this->session->set_userdata('user', $wUser);

			// 4) Générer l’URL SSO
			$iframeUrl = $this->humhub_sso->provisionAndGetIframeUrl();
			log_message('debug', 'Generated iframe URL: ' . $iframeUrl);
		
			if (! $iframeUrl) {
				show_error('Impossible de générer l’URL SSO HumHub.');
			}

			// 5) Passer à la vue
			$page_data = [
				'folder_name' => 'humhub',
				'page_title'  => 'People',
				'page_name'   => 'people',
				'iframe_url'  => $iframeUrl,
			];
			$this->load->view('backend/index', $page_data);
		}

		//HUMHUB SPEACES
		public function spaces() {
			// 1) Vérifier que c'est bien un admin
			if (! $this->session->userdata('admin_login')) {
				show_error('Accès réservé aux administrateurs.');
			}
			
			// 2) Récupérer les infos du user (ici : depuis la table Wayo)
			$userId = $this->session->userdata('user_id');
			$wUser  = $this->db->get_where('users', ['id' => $userId])->row();
			if (empty($wUser) || ! filter_var($wUser->email, FILTER_VALIDATE_EMAIL)) {
				log_message('error', 'Invalid Wayo user data: ' . print_r($wUser, true));
				show_error('Impossible de retrouver votre compte Wayo pour SSO.');
			}
			log_message('debug', 'Wayo user data: ' . print_r($wUser, true));

			// 3) Stocker temporairement cet objet pour la librairie SSO
			$this->session->set_userdata('user', $wUser);

			// 4) Générer l’URL SSO
			$iframeUrl = $this->humhub_sso->provisionAndGetIframeUrl();
			log_message('debug', 'Generated iframe URL: ' . $iframeUrl);

			if (! $iframeUrl) {
				show_error('Impossible de générer l’URL SSO HumHub.');
			}

			// 5) Passer à la vue
			$page_data = [
				'folder_name' => 'humhub',
				'page_title'  => 'Spaces',
				'page_name'   => 'spaces',
				'iframe_url'  => $iframeUrl,
			];
			$this->load->view('backend/index', $page_data);
		
		}
		//HUMHUB MESSAGING	
		public function chat() {
			// 1) Vérifier que c'est bien un admin
			if (! $this->session->userdata('admin_login')) {
				show_error('Accès réservé aux admins.');
			}
			
			// 2) Récupérer les infos du user (ici : depuis la table Wayo)
			$userId = $this->session->userdata('user_id');


			$wUser  = $this->db->get_where('users', ['id' => $userId])->row();
			if (empty($wUser) || ! filter_var($wUser->email, FILTER_VALIDATE_EMAIL)) {
				log_message('error', 'Invalid Wayo user data: ' . print_r($wUser, true));
				show_error('Impossible de retrouver votre compte Wayo pour SSO.');
			}
			log_message('debug', 'Wayo user data: ' . print_r($wUser, true));

			// 3) Stocker temporairement cet objet pour la librairie SSO
			$this->session->set_userdata('user', $wUser);

			// 4) Générer l’URL SSO
			$iframeUrl = $this->humhub_sso->provisionAndGetIframeUrl();
			log_message('debug', 'Generated iframe URL: ' . $iframeUrl);

			if (! $iframeUrl) {
				show_error('Impossible de générer l’URL SSO HumHub.');
			}

			$iframeUrl .= '&redirect=' . urlencode('/mail/mail/index') . '&t=' . time();
			
 			// Ajoute ça pour le badge :
  		   $unread_messages = $this->user_model->get_unread_messages_count($userId);
			// 5) Passer à la vue
			$page_data = [
				'folder_name' => 'humhub',
				'page_title'  => 'Messages',
				'page_name'   => 'message',
				'iframe_url'  => $iframeUrl,
				'unread_messages'=>$unread_messages,
			];
			$this->load->view('backend/index', $page_data);
		
		}


  // SMTP SETTINGS MANAGER
  public function smtp_settings($param1 = "", $param2 = "")
  {
    if ($param1 == 'update') {
      $response = $this->settings_model->update_smtp_settings();
    //   echo $response;
	      // Préparer la réponse avec un nouveau jeton CSRF
		  $csrf = array(
			'csrfName' => $this->security->get_csrf_token_name(),
			'csrfHash' => $this->security->get_csrf_hash(),
				  );
				
		  // Renvoyer la réponse avec un nouveau jeton CSRF
		  echo json_encode(array('status' => $response, 'csrf' => $csrf));
    }

    // showing the Smtp Settings file
    if (empty($param1)) {
      $page_data['folder_name'] = 'settings';
      $page_data['page_title'] = 'smtp_settings';
      $page_data['settings_type'] = 'smtp_settings';
      $this->load->view('backend/index', $page_data);
    }
  }

	//START MARKS section
	public function mark($param1 = '', $param2 = '')
	{

		if ($param1 == 'list') {
			$page_data['class_id'] = htmlspecialchars($this->input->post('class_id'));
			
			// $page_data['subject_id'] = htmlspecialchars($this->input->post('subject'));
			$page_data['exam_id'] = htmlspecialchars($this->input->post('exam'));
			$this->crud_model->mark_insert($page_data['class_id'], $page_data['exam_id']);
			// $this->load->view('backend/admin/mark/list', $page_data);

			// Charger la vue mise à jour
			$response_html = $this->load->view('backend/admin/mark/list', $page_data, TRUE);
			// Préparer le nouveau jeton CSRF
			$csrf = array(
					'csrfName' => $this->security->get_csrf_token_name(),
					'csrfHash' => $this->security->get_csrf_hash(),
				 );
			
			// Renvoyer la réponse JSON avec le HTML mis à jour et le nouveau jeton CSRF
			echo json_encode(array('status' => $response_html, 'csrf' => $csrf));
		}

		if ($param1 == 'mark_update') {
			$this->crud_model->mark_update();
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
				'csrfName' => $this->security->get_csrf_token_name(),
				'csrfHash' => $this->security->get_csrf_hash(),
				);
			
			// Renvoyer la réponse avec un nouveau jeton CSRF
			echo json_encode(array('csrf' => $csrf));
		}

		if (empty($param1)) {
			$page_data['folder_name'] = 'mark';
			$page_data['page_title'] = 'marks';
			$this->load->view('backend/index', $page_data);
		}
	}

	//START quiz section
	public function quiz_result($param1 = '', $param2 = '')
	{
  
	  if ($param1 == 'list') {
		$page_data['class_id'] = htmlspecialchars($this->input->post('class_id'));
		$page_data['cours_id'] = htmlspecialchars($this->input->post('cours_id'));
		$page_data['quiz_id'] = htmlspecialchars($this->input->post('quiz_id'));
		// $this->load->view('backend/admin/quiz/list', $page_data);

			// Charger la vue mise à jour
			$response_html = $this->load->view('backend/admin/quiz/list', $page_data, TRUE);
			// Préparer le nouveau jeton CSRF
			$csrf = array(
					'csrfName' => $this->security->get_csrf_token_name(),
					'csrfHash' => $this->security->get_csrf_hash(),
				 );
			
			// Renvoyer la réponse JSON avec le HTML mis à jour et le nouveau jeton CSRF
			echo json_encode(array('status' => $response_html, 'csrf' => $csrf));
	  }
  
   
  
	  if (empty($param1)) {
		$page_data['folder_name'] = 'quiz';
		$page_data['page_title'] = 'quiz';
		$this->load->view('backend/index', $page_data);
	  }
	}
 
	public function quiz($action = "", $id = "")
	{
  
	  // PROVIDE A LIST OF SECTION ACCORDING TO CLASS ID
	  if ($action == 'list') {
		$page_data['class_id'] = $id;
		$this->load->view('backend/admin/quiz/list_quiz', $page_data);
	  }
	}
	// GET THE GRADE ACCORDING TO MARK
	public function get_grade($acquired_mark)
	{
		echo get_grade($acquired_mark);
	}
	//END MARKS sesction

	// GRADE SECTION STARTS
	public function grade($param1 = "", $param2 = "")
	{

		// store data on database
		if ($param1 == 'create') {
			$modelResponse = $this->crud_model->grade_create();
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
			  'name' => $this->security->get_csrf_token_name(),
			  'hash' => $this->security->get_csrf_hash()
		  );
		  
		  // Fusionner la réponse du modèle avec le CSRF
		  $response = array(
			  'status' => $modelResponse['status'],
			  'notification' => $modelResponse['notification'],
			  'csrf' => $csrf
		  );
		  
		  echo json_encode($response);
		}

		// update data on database
		if ($param1 == 'update') {
			$response = $this->crud_model->grade_update($param2);
			// echo $response;
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
				'csrfName' => $this->security->get_csrf_token_name(),
				'csrfHash' => $this->security->get_csrf_hash(),
				);
			
			// Renvoyer la réponse avec un nouveau jeton CSRF
			echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}

		// delelte data from database
		if ($param1 == 'delete') {
			$response = $this->crud_model->grade_delete($param2);
			// echo $response;
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
				'csrfName' => $this->security->get_csrf_token_name(),
				'csrfHash' => $this->security->get_csrf_hash(),
				);
			
			// Renvoyer la réponse avec un nouveau jeton CSRF
			echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}

		// show data from database
		if ($param1 == 'list') {
			$this->load->view('backend/admin/grade/list');
		}

		// showing the index file
		if (empty($param1)) {
			$page_data['folder_name'] = 'grade';
			$page_data['page_title'] = 'grades';
			$this->load->view('backend/index', $page_data);
		}
	}
	// GRADE SECTION ENDS

	// STUDENT PROMOTION SECTION STARTS
	function promotion($param1 = "", $promotion_data = "")
	{

		// Promote students. Here promotion_data contains all the data of a student to promote
		if ($param1 == 'promote') {
			$response = $this->crud_model->promote_student($promotion_data);
			// echo $response;
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
				'csrfName' => $this->security->get_csrf_token_name(),
				'csrfHash' => $this->security->get_csrf_hash(),
				);
			
			// Renvoyer la réponse avec un nouveau jeton CSRF
			echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}
		//showing the list of student to promote
		if ($param1 == 'list') {
			$page_data['session_from'] = htmlspecialchars($this->input->post('session_from'));
			$page_data['session_to'] = htmlspecialchars($this->input->post('session_to'));
			$page_data['class_id_from'] = htmlspecialchars($this->input->post('class_id_from'));
			$page_data['class_id_to'] = htmlspecialchars($this->input->post('class_id_to'));
			$page_data['class_from_details'] = $this->crud_model->get_classes($this->input->post('class_id_from'))->row_array();
			$page_data['class_to_details'] = $this->crud_model->get_classes($this->input->post('class_id_to'))->row_array();
			$page_data['session_from_details'] = $this->crud_model->get_session($this->input->post('session_from'))->row_array();
			$page_data['session_to_details'] = $this->crud_model->get_session($this->input->post('session_to'))->row_array();
			$page_data['enrolments'] = $this->crud_model->get_student_list()->result_array();
			// $this->load->view('backend/admin/promotion/list', $page_data);
			// Charger la vue mise à jour
			$response_html = $this->load->view('backend/admin/promotion/list', $page_data, TRUE);
			// Préparer le nouveau jeton CSRF
			$csrf = array(
					'csrfName' => $this->security->get_csrf_token_name(),
					'csrfHash' => $this->security->get_csrf_hash(),
				 );
			
			// Renvoyer la réponse JSON avec le HTML mis à jour et le nouveau jeton CSRF
			echo json_encode(array('status' => $response_html, 'csrf' => $csrf));
		}
		// showing the index file
		if (empty($param1)) {
			$page_data['folder_name'] = 'promotion';
			$page_data['page_title'] = 'student_promotion';
			$this->load->view('backend/index', $page_data);
		}
	}
	// STUDENT PROMOTION SECTION ENDS

	// ACCOUNTING SECTION STARTS
	public function invoice($param1 = "", $param2 = "")
	{
		// For creating new invoice
		if ($param1 == 'single') {
			$modelResponse = $this->crud_model->create_single_invoice();
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
			  'name' => $this->security->get_csrf_token_name(),
			  'hash' => $this->security->get_csrf_hash()
		  );
		  
		  // Fusionner la réponse du modèle avec le CSRF
		  $response = array(
			  'status' => $modelResponse['status'],
			  'notification' => $modelResponse['notification'],
			  'csrf' => $csrf
		  );
		  
		  echo json_encode($response);
		}

		// For creating new mass invoice
		if ($param1 == 'mass') {
			$modelResponse = $this->crud_model->create_mass_invoice();
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
			  'name' => $this->security->get_csrf_token_name(),
			  'hash' => $this->security->get_csrf_hash()
		  );
		  
		  // Fusionner la réponse du modèle avec le CSRF
		  $response = array(
			  'status' => $modelResponse['status'],
			  'notification' => $modelResponse['notification'],
			  'csrf' => $csrf
		  );
		  
		  echo json_encode($response);
		}

		// For editing invoice
		if ($param1 == 'update') {
			$response = $this->crud_model->update_invoice($param2);
			// echo $response;
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
				'csrfName' => $this->security->get_csrf_token_name(),
				'csrfHash' => $this->security->get_csrf_hash(),
				);
			
			// Renvoyer la réponse avec un nouveau jeton CSRF
			echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}

		// For deleting invoice
		if ($param1 == 'delete') {
			$response = $this->crud_model->delete_invoice($param2);
			// echo $response;
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
				'csrfName' => $this->security->get_csrf_token_name(),
				'csrfHash' => $this->security->get_csrf_hash(),
				);
			
			// Renvoyer la réponse avec un nouveau jeton CSRF
			echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}

		// Get the list of student. Here param2 defines classId
		if ($param1 == 'student') {
			$page_data['enrolments'] = $this->user_model->get_student_details_by_id('class', $param2);
			$this->load->view('backend/admin/student/dropdown', $page_data);
		}

		// showing the list of invoices
		if ($param1 == 'invoice') {
			$page_data['invoice_id'] = $param2;
			$page_data['folder_name'] = 'invoice';
			$page_data['page_name'] = 'invoice';
			$page_data['page_title'] = 'invoice';
			$this->load->view('backend/index', $page_data);
		}

		// showing the list of invoices
		if ($param1 == 'list') {
			$date = explode('-', $this->input->get('date'));
			$page_data['date_from'] = strtotime($date[0] . ' 00:00:00');
			$page_data['date_to'] = strtotime($date[1] . ' 23:59:59');
			$page_data['selected_class'] = htmlspecialchars($this->input->get('selectedClass'));
			$page_data['selected_status'] = htmlspecialchars($this->input->get('selectedStatus'));
			$this->load->view('backend/admin/invoice/list', $page_data);
		}
		// showing the index file
		if (empty($param1)) {
			$page_data['folder_name'] = 'invoice';
			$page_data['page_title'] = 'invoice';
			$first_day_of_month = "1 " . date("M") . " " . date("Y") . ' 00:00:00';
			$last_day_of_month = date("t") . " " . date("M") . " " . date("Y") . ' 23:59:59';
			$page_data['date_from'] = strtotime($first_day_of_month);
			$page_data['date_to'] = strtotime($last_day_of_month);
			$page_data['selected_class'] = 'all';
			$page_data['selected_status'] = 'all';
			$this->load->view('backend/index', $page_data);
		}
	}

  //EXPORT STUDENT FEES
  public function export($param1 = "", $date_from = "", $date_to = "", $selected_class = "", $selected_status = "")
  {
    //RETURN EXPORT URL
    if ($param1 == 'url') {
      $type = htmlspecialchars($this->input->post('type'));
      $dateRange = $this->input->post('dateRange');
      
      // Support both separators: ' — ' (em dash) and ' - ' (hyphen)
      if (strpos($dateRange, ' — ') !== false) {
        $date = explode(' — ', $dateRange);
      } elseif (strpos($dateRange, ' - ') !== false) {
        $date = explode(' - ', $dateRange);
      } else {
        $date = explode('-', $dateRange);
      }
      
      $date_from = isset($date[0]) ? strtotime(trim($date[0]) . ' 00:00:00') : strtotime('first day of this month');
      $date_to = isset($date[1]) ? strtotime(trim($date[1]) . ' 23:59:59') : strtotime('last day of this month');
      $selected_class = htmlspecialchars($this->input->post('selectedClass'));
      $selected_status = htmlspecialchars($this->input->post('selectedStatus'));
      // echo route('export/' . $type . '/' . $date_from . '/' . $date_to . '/' . $selected_class . '/' . $selected_status);
       // Générer l'URL de l'exportation
       $export_url = route('export/' . $type . '/' . $date_from . '/' . $date_to . '/' . $selected_class . '/' . $selected_status);
        
        // Générer un nouveau jeton CSRF
          $csrf = array(
               'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
            );
    
            // Renvoyer la réponse avec l'URL et le nouveau jeton CSRF
          echo json_encode(array('url' => $export_url, 'csrf' => $csrf));
    }
    // EXPORT AS PDF
    if ($param1 == 'pdf' || $param1 == 'print') {
      // Préparer les données à exporter
      $page_data['action'] = $param1;
      $page_data['date_from'] = $date_from;
      $page_data['date_to'] = $date_to;
      $page_data['selected_class'] = $selected_class;
      $page_data['selected_status'] = $selected_status;

      // Charger la vue comme HTML
      ob_start();
      $this->load->view('backend/admin/invoice/export', $page_data);
	  
      $html = ob_get_clean();

      try {
          // Créer une instance de mPDF
          $mpdf = new Mpdf();

          // Charger le contenu HTML dans mPDF
          $mpdf->WriteHTML($html);

          // Définir le nom du fichier
          $fileName = 'Student_fees-' . date('d-M-Y', $date_from) . '-to-' . date('d-M-Y', $date_to) . '.pdf';

          // Stream pour télécharger ou afficher le PDF
          if ($param1 == 'pdf') {
              $mpdf->Output($fileName, \Mpdf\Output\Destination::DOWNLOAD); // Télécharger le PDF
          } else {
              $mpdf->Output($fileName, \Mpdf\Output\Destination::INLINE); // Afficher le PDF dans le navigateur
          }

      } catch (\Mpdf\MpdfException $e) {
          // Gérer les exceptions de mPDF
          echo $e->getMessage();
      }
    }
    // EXPORT AS CSV
    if ($param1 == 'csv') {
      $date_from = $date_from;
      $date_to = $date_to;
      $selected_class = $selected_class;
      $selected_status = $selected_status;

      $invoices = $this->crud_model->get_invoice_by_date_range($date_from, $date_to, $selected_class, $selected_status)->result_array();
      $csv_file = fopen("assets/csv_file/invoices.csv", "w");
      $header = array('Invoice-no', 'Student', 'Class', 'Invoice-Title', 'Total-Amount', 'Paid-Amount', 'Creation-Date', 'Payment-Date', 'Status');
      fputcsv($csv_file, $header);
      foreach ($invoices as $invoice) {
        $student_details = $this->user_model->get_student_details_by_id('student', $invoice['student_id']);
        $class_details = $this->crud_model->get_class_details_by_id($invoice['class_id'])->row_array();
        if ($invoice['updated_at'] > 0) {
          $payment_date = date('d-M-Y', $invoice['updated_at']);
        } else {
          $payment_date = get_phrase('not_found');
        }
        $lines = array(sprintf('%08d', $invoice['id']), $student_details['name'], $class_details['name'], $invoice['title'], currency($invoice['total_amount']), currency($invoice['paid_amount']), date('d-M-Y', $invoice['created_at']), $payment_date, ucfirst($invoice['status']));
        fputcsv($csv_file, $lines);
      }

      // FILE DOWNLOADING CODES
      if ($selected_status == 'all') {
        $paymentStatusForTitle = 'paid-and-unpaid';
      } else {
        $paymentStatusForTitle = $selected_status;
      }
      if ($selected_class == 'all') {
        $classNameForTitle = 'all_class';
      } else {
        $class_details = $this->crud_model->get_classes($selected_class)->row_array();
        $classNameForTitle = $class_details['name'];
      }
      $fileName = 'Student_fees-' . date('d-M-Y', $date_from) . '-to-' . date('d-M-Y', $date_to) . '-' . $classNameForTitle . '-' . $paymentStatusForTitle . '.csv';
      $this->download_file('assets/csv_file/invoices.csv', $fileName);
    }
  }

    /**
     * Download a single invoice as PDF
     */
    public function invoice_pdf($invoice_id = "")
    {
      if ($this->session->userdata('admin_login') != 1) {
        redirect(site_url('login'), 'refresh');
      }

      if (empty($invoice_id)) {
        show_error('Invalid invoice id');
      }

      $page_data['invoice_id'] = $invoice_id;

      // Rendre la facture en HTML
      ob_start();
      $this->load->view('backend/admin/invoice/invoice_pdf', $page_data);
      $html = ob_get_clean();

      try {
        $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4']);
        $mpdf->WriteHTML($html);
        $fileName = 'Invoice-' . sprintf('%08d', $invoice_id) . '.pdf';
        $mpdf->Output($fileName, \Mpdf\Output\Destination::DOWNLOAD);
      } catch (\Mpdf\MpdfException $e) {
        echo $e->getMessage();
      }
    }

	/*FUNCTION FOR DOWNLOADING A FILE*/
	function download_file($path, $name)
	{
		// make sure it's a file before doing anything!
		if (is_file($path)) {
			// required for IE
			if (ini_get('zlib.output_compression')) {
				ini_set('zlib.output_compression', 'Off');
			}

			// get the file mime type using the file extension
			$this->load->helper('file');

			$mime = get_mime_by_extension($path);

			// Build the headers to push out the file properly.
			header('Pragma: public');     // required
			header('Expires: 0');         // no cache
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($path)) . ' GMT');
			header('Cache-Control: private', false);
			header('Content-Type: ' . $mime);  // Add the mime type from Code igniter.
			header('Content-Disposition: attachment; filename="' . basename($name) . '"');  // Add the file name
			header('Content-Transfer-Encoding: binary');
			header('Content-Length: ' . filesize($path)); // provide file size
			header('Connection: close');
			readfile($path); // push it out
			exit();
		}
	}

	// Expense Category
	public function expense_category($param1 = "", $param2 = "")
	{
		if ($param1 == 'create') {
			$modelResponse = $this->crud_model->create_expense_category();
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
			  'name' => $this->security->get_csrf_token_name(),
			  'hash' => $this->security->get_csrf_hash()
		  );
		  
		  // Fusionner la réponse du modèle avec le CSRF
		  $response = array(
			  'status' => $modelResponse['status'],
			  'notification' => $modelResponse['notification'],
			  'csrf' => $csrf
		  );
		  
			echo json_encode($response);
		}

		if ($param1 == 'update') {
			$response = $this->crud_model->update_expense_category($param2);
			// echo $response;
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
				'csrfName' => $this->security->get_csrf_token_name(),
				'csrfHash' => $this->security->get_csrf_hash(),
				);
			
			// Renvoyer la réponse avec un nouveau jeton CSRF
			echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}

		if ($param1 == 'delete') {
			$response = $this->crud_model->delete_expense_category($param2);
			// echo $response;
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
				'csrfName' => $this->security->get_csrf_token_name(),
				'csrfHash' => $this->security->get_csrf_hash(),
				);
			
			// Renvoyer la réponse avec un nouveau jeton CSRF
			echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}

		if ($param1 == 'list') {
			$this->load->view('backend/admin/expense_category/list');
		}
		// showing the index file
		if (empty($param1)) {
			$page_data['folder_name'] = 'expense_category';
			$page_data['page_title'] = 'expense_category';
			$this->load->view('backend/index', $page_data);
		}
	}

	//Expense Manager
	public function expense($param1 = "", $param2 = "")
	{

		// adding expense
		if ($param1 == 'create') {
			$modelResponse = $this->crud_model->create_expense();
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
				'name' => $this->security->get_csrf_token_name(),
				'hash' => $this->security->get_csrf_hash()
			);
			
			// Fusionner la réponse du modèle avec le CSRF
			$response = array(
				'status' => $modelResponse['status'],
				'notification' => $modelResponse['notification'],
				'csrf' => $csrf
			);
			
			echo json_encode($response);
		}

		// update expense
		if ($param1 == 'update') {
			$response = $this->crud_model->update_expense($param2);
			// echo $response;
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
				'csrfName' => $this->security->get_csrf_token_name(),
				'csrfHash' => $this->security->get_csrf_hash(),
				);
			
			// Renvoyer la réponse avec un nouveau jeton CSRF
			echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}

		// deleting expense
		if ($param1 == 'delete') {
			$response = $this->crud_model->delete_expense($param2);
			// echo $response;
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
				'csrfName' => $this->security->get_csrf_token_name(),
				'csrfHash' => $this->security->get_csrf_hash(),
				);
			
			// Renvoyer la réponse avec un nouveau jeton CSRF
			echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}
		// showing the list of expense
		if ($param1 == 'list') {
			$date = explode('-', $this->input->get('date'));
			$page_data['date_from'] = strtotime($date[0] . ' 00:00:00');
			$page_data['date_to'] = strtotime($date[1] . ' 23:59:59');
			$page_data['expense_category_id'] = htmlspecialchars($this->input->get('expense_category_id'));
			$this->load->view('backend/admin/expense/list', $page_data);
		}

		// showing the index file
		if (empty($param1)) {
			$page_data['folder_name'] = 'expense';
			$page_data['page_title'] = 'expense';
			$page_data['date_from'] = strtotime(date('d-M-Y', strtotime(' -30 day')) . ' 00:00:00');
			$page_data['date_to'] = strtotime(date('d-M-Y') . ' 23:59:59');
			$this->load->view('backend/index', $page_data);
		}
	}
	// ACCOUNTING SECTION ENDS

	// BACKOFFICE SECTION
	//BOOK LIST MANAGER
	public function book($param1 = "", $param2 = "")
	{
		// adding book
		if ($param1 == 'create') {
			$modelResponse = $this->crud_model->create_book();
          // Préparer la réponse avec un nouveau jeton CSRF
          $csrf = array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        );
        
        // Fusionner la réponse du modèle avec le CSRF
        $response = array(
            'status' => $modelResponse['status'],
            'notification' => $modelResponse['notification'],
            'csrf' => $csrf
        );
        
        echo json_encode($response);
		}

		// update book
		if ($param1 == 'update') {
			$response = $this->crud_model->update_book($param2);
			// echo $response;
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
				'csrfName' => $this->security->get_csrf_token_name(),
				'csrfHash' => $this->security->get_csrf_hash(),
				);
			
			// Renvoyer la réponse avec un nouveau jeton CSRF
			echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}

		// deleting book
		if ($param1 == 'delete') {
			$response = $this->crud_model->delete_book($param2);
			// echo $response;
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
				'csrfName' => $this->security->get_csrf_token_name(),
				'csrfHash' => $this->security->get_csrf_hash(),
				);
			
			// Renvoyer la réponse avec un nouveau jeton CSRF
			echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}
		// showing the list of book
		if ($param1 == 'list') {
			$this->load->view('backend/admin/book/list');
		}

		// showing the index file
		if (empty($param1)) {
			$page_data['folder_name'] = 'book';
			$page_data['page_title'] = 'books';
			$this->load->view('backend/index', $page_data);
		}
	}

	//BOOK ISSUE LIST MANAGER
	public function book_issue($param1 = "", $param2 = "")
	{
		// adding book
		if ($param1 == 'create') {
			$modelResponse = $this->crud_model->create_book_issue();
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
			  'name' => $this->security->get_csrf_token_name(),
			  'hash' => $this->security->get_csrf_hash()
		  );
		  
		  // Fusionner la réponse du modèle avec le CSRF
		  $response = array(
			  'status' => $modelResponse['status'],
			  'notification' => $modelResponse['notification'],
			  'csrf' => $csrf
		  );
		  
		  echo json_encode($response);
		}

		// update book
		if ($param1 == 'update') {
			$response = $this->crud_model->update_book_issue($param2);
			// echo $response;
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
				'csrfName' => $this->security->get_csrf_token_name(),
				'csrfHash' => $this->security->get_csrf_hash(),
				);
			
			// Renvoyer la réponse avec un nouveau jeton CSRF
			echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}

		// Returning a book
		if ($param1 == 'return') {
			$response = $this->crud_model->return_issued_book($param2);
			// echo $response;
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
				'csrfName' => $this->security->get_csrf_token_name(),
				'csrfHash' => $this->security->get_csrf_hash(),
				);
			
			// Renvoyer la réponse avec un nouveau jeton CSRF
			echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}

		// deleting book
		if ($param1 == 'delete') {
			$response = $this->crud_model->delete_book_issue($param2);
			// echo $response;
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
				'csrfName' => $this->security->get_csrf_token_name(),
				'csrfHash' => $this->security->get_csrf_hash(),
				);
			
			// Renvoyer la réponse avec un nouveau jeton CSRF
			echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}
		// showing the list of book
		if ($param1 == 'list') {
			$date = explode('-', $this->input->get('date'));
			$page_data['date_from'] = strtotime($date[0] . ' 00:00:00');
			$page_data['date_to'] = strtotime($date[1] . ' 23:59:59');
			$this->load->view('backend/admin/book_issue/list', $page_data);
		}

		// showing the index file
		if (empty($param1)) {
			$page_data['folder_name'] = 'book_issue';
			$page_data['page_title'] = 'book_issue';
			$page_data['date_from'] = strtotime(date('d-M-Y', strtotime(' -30 day')) . ' 00:00:00');
			$page_data['date_to'] = strtotime(date('d-M-Y') . ' 23:59:59');
			$this->load->view('backend/index', $page_data);
		}
	}

	// NOTICEBOARD MANAGER
	public function noticeboard($param1 = "", $param2 = "", $param3 = "")
	{
		// adding notice
		if ($param1 == 'create') {
			$modelResponse = $this->crud_model->create_notice();
          // Préparer la réponse avec un nouveau jeton CSRF
          $csrf = array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        );
        
        // Fusionner la réponse du modèle avec le CSRF
        $response = array(
            'status' => $modelResponse['status'],
            'notification' => $modelResponse['notification'],
            'csrf' => $csrf
        );
        
        echo json_encode($response);
		}

		// update notice
		if ($param1 == 'update') {
			$modelResponse = $this->crud_model->update_notice($param2);
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
			  'name' => $this->security->get_csrf_token_name(),
			  'hash' => $this->security->get_csrf_hash()
		  );
		  
		  // Fusionner la réponse du modèle avec le CSRF
		  $response = array(
			  'status' => $modelResponse['status'],
			  'notification' => $modelResponse['notification'],
			  'csrf' => $csrf
		  );
		  
			echo json_encode($response);
		}

		// deleting notice
		if ($param1 == 'delete') {
			$response = $this->crud_model->delete_notice($param2);
			// echo $response;
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
				'csrfName' => $this->security->get_csrf_token_name(),
				'csrfHash' => $this->security->get_csrf_hash(),
				);
			
			// Renvoyer la réponse avec un nouveau jeton CSRF
			echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}
		// showing the list of notice
		if ($param1 == 'list') {
			$this->load->view('backend/admin/noticeboard/list');
		}

		// showing the all the notices
		if ($param1 == 'all_notices') {
			$response = $this->crud_model->get_all_the_notices();
			echo $response;
		}

		// showing the index file
		if (empty($param1)) {
			$page_data['folder_name'] = 'noticeboard';
			$page_data['page_title'] = 'noticeboard';
			$this->load->view('backend/index', $page_data);
		}
	}

	// SETTINGS MANAGER
	public function school_settings($param1 = "", $param2 = "")
	{
		if ($param1 == 'update') {
			$response = $this->settings_model->update_current_school_settings();
			// echo $response;
			
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
				'csrfName' => $this->security->get_csrf_token_name(),
				'csrfHash' => $this->security->get_csrf_hash(),
				);
			
			// Renvoyer la réponse avec un nouveau jeton CSRF
			echo json_encode(array('status' => $response, 'csrf' => $csrf));
		
		}

		if ($param1 == 'delete_tax_document') {
			$response = json_decode($this->settings_model->delete_tax_document(), true);
			
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
				'name' => $this->security->get_csrf_token_name(),
				'hash' => $this->security->get_csrf_hash(),
			);
			
			// Renvoyer la réponse avec un nouveau jeton CSRF
			echo json_encode(array(
				'status' => $response['status'],
				'notification' => $response['notification'],
				'csrf' => $csrf
			));
		}

		// showing the System Settings file
		if (empty($param1)) {
			$page_data['folder_name'] = 'settings';
			$page_data['page_title'] = 'school_settings';
			$page_data['settings_type'] = 'school_settings';
			$this->load->view('backend/index', $page_data);
		}
	}
	// SETTINGS MANAGER

	//MANAGE PROFILE STARTS
	public function profile($param1 = "", $param2 = "")
	{
		if ($param1 == 'update_profile') {
			$response = $this->user_model->update_profile();
			die($response);
			echo $response;
		}
		if ($param1 == 'update_password') {
			$response = $this->user_model->update_password();
			echo $response;
		}

		// showing the Smtp Settings file
		if (empty($param1)) {
			$page_data['folder_name'] = 'profile';
			$page_data['page_title'] = 'manage_profile';
			$this->load->view('backend/index', $page_data);
		}
	}
	//MANAGE PROFILE ENDS

	// ABOUT APPLICATION STARTS

    public function online_admission($param1 = "", $user_id = "")
{
    if ($param1 == 'assigned') {
        $data['student_id'] = $this->input->post('student_id');

        $user_id = $this->db->get_where('students', array('id' => $data['student_id']))->row('user_id');
        $this->email_model->approved_online_admission($data['student_id'], $user_id);

        $this->db->where('user_id', $user_id);
        $this->db->update('students', array('status' => 1));

        $this->session->set_flashdata('flash_message', get_phrase('admission_request_has_been_updated'));
        redirect(site_url('admin/online_admission'), 'refresh');
    }

    if ($param1 == 'delete') {
        $this->db->where('user_id', $user_id);
        $this->db->delete('students');

        $this->session->set_flashdata('flash_message', get_phrase('admission_data_deleted_successfully'));
        redirect(site_url('admin/online_admission'), 'refresh');
    }

    // JOIN POUR RÉCUPÉRER USERS + STUDENTS
    $this->db->select('students.id as student_id, students.user_id, students.school_id, users.id, users.name, users.email');
    $this->db->from('students');
    $this->db->join('users', 'users.id = students.user_id');
    $this->db->where('students.status', 0);
    $this->db->where('students.school_id', $this->session->userdata('school_id'));

    $page_data['applications'] = $this->db->get();

    $page_data['folder_name'] = 'online_admission';
    $page_data['page_title'] = 'online_admission';

    $this->load->view('backend/index', $page_data);
}
	// ABOUT APPLICATION ENDS

	//   transport feature starts

	//                                       	1. driver action
	/*------------------------------------------------------------------------------------------------------------*/
	function driver($param1 = '', $param2 = '', $param3 = '')
	{
		if ($param1 == 'create') {
			$response = $this->driver_model->create_driver();
			echo $response;
		}

		if ($param1 == 'update') {
			$response = $this->driver_model->update_driver($param2);
			echo $response;
		}

		if ($param1 == 'delete') {
			$driver_id = $this->db->get_where('drivers', array('user_id' => $param2))->row('id');
			$response = $this->driver_model->delete_driver($param2, $driver_id);
			echo $response;
		}

		if ($param1 == 'list') {
			$this->load->view('backend/admin/driver/list');
		}

		if (empty($param1)) {
			$page_data['folder_name'] = 'driver';
			$page_data['page_title'] = 'drivers';
			$this->load->view('backend/index', $page_data);
		}
	}

	//                                       	2. vehicle action
	/*------------------------------------------------------------------------------------------------------------*/
	function vehicle($param1 = '', $param2 = '', $param3 = '')
	{
		if ($param1 == 'create') {
			$response = $this->driver_model->create_vehicle();
			echo $response;
		}

		if ($param1 == 'update') {
			$response = $this->driver_model->update_vehicle($param2);
			echo $response;
		}

		if ($param1 == 'delete') {
			$response = $this->driver_model->delete_vehicle($param2);
			echo $response;
		}

		if ($param1 == 'list') {
			$this->load->view('backend/admin/vehicle/list');
		}

		if (empty($param1)) {
			$page_data['folder_name'] = 'vehicle';
			$page_data['page_title'] = 'vehicles';
			$this->load->view('backend/index', $page_data);
		}
	}

	//                                       	3. assign students
	/*------------------------------------------------------------------------------------------------------------*/
	function assign_student($param1 = '', $param2 = '', $param3 = '', $param4 = '')
	{
		if ($param1 == 'add') {
			$response = $this->driver_model->add_to_vehicle();
			echo $response;
		}

		if ($param1 == 'delete') {
			$response = $this->driver_model->remove_from_vehicle($param2);
			echo $response;
		}

		if ($param1 == 'list') {
			$this->load->view('backend/admin/assign_student/list');
		}

		if ($param1 == 'filter') {
			$page_data['parent_category'] = $param2;
			$page_data['child_category'] = $param3;
			$this->load->view('backend/admin/assign_student/list', $page_data);
		}

		if (empty($param1)) {
			$page_data['folder_name'] = 'assign_student';
			$page_data['page_title'] = 'assign_student';
			$this->load->view('backend/index', $page_data);
		}
	}

	function class_wise_student($class_id)
	{
		$this->db->select('enrols.*, users.name');
		$this->db->from('enrols');
		$this->db->join('students', 'enrols.student_id = students.id');
		$this->db->join('users', 'students.user_id = users.id');
		$this->db->where('enrols.class_id', $class_id);
		$this->db->where('enrols.school_id', school_id());
		$total_students = $this->db->get()->result_array();

		$first_option = count($total_students) > 0 ? 'select_a_student' : 'no_student_available';
		echo '<select><option>' . get_phrase($first_option) . '</option>';

		foreach ($total_students as $student) {
			echo '<option value="' . $student['student_id'] . '">' . $student['name'] . '</option>';
		}
		echo '</select>';
	}

	function getAdditionalCategory($category)
	{
		$name = 'name';
		if ($category == 'class') {
			$this->db->where('school_id', school_id());
			$query = $this->db->get('classes');
		} elseif ($category == 'vehicle') {
			$name = 'vh_num';
			$this->db->where('school_id', school_id());
			$query = $this->db->get('vehicles');
		} elseif ($category == 'driver') {
			$this->db->select('drivers.*, users.name');
			$this->db->from('drivers');
			$this->db->join('users', 'drivers.user_id = users.id');
			$this->db->where('drivers.school_id', school_id());
			$query = $this->db->get();
		}

		$result = $query->result_array();

		$first_option = count($result) > 0 ? 'select_a_' . $category : 'no_' . $category . '_available';
		echo '<select><option>' . get_phrase($first_option) . '</option>';
		foreach ($result as $item) {
			echo '<option value="' . $item['id'] . '">' . $item[$name] . '</option>';
		}
		echo '</select>';
	}
	public function exam_results($exam_id = "", $student_id = "") {
		try {
			// Vérifier si l'utilisateur est connecté en tant qu'admin
			if (!$this->session->userdata('admin_login') || $this->session->userdata('user_type') != 'admin') {
				redirect(site_url('login'), 'refresh');
			}
	
			// Récupérer la session active
			$session_id = active_session();
	
			// Récupérer l'ID de l'école de l'admin
			$school_id = $this->session->userdata('school_id');
			if (!$school_id) {
				redirect(site_url('admin/exam'), 'refresh');
			}
	
			// Récupérer les détails de l'examen
			$this->db->select('exams.*, classes.name as class_name schools.name as school_name');
			$this->db->from('exams');
			$this->db->join('classes', 'exams.class_id = classes.id', 'left');
			
			$this->db->join('schools', 'exams.school_id = schools.id', 'left');
			$this->db->where('exams.id', $exam_id);
			$this->db->where('exams.school_id', $school_id);
	
			$exam = $this->db->get()->row_array();
	
			// Vérifier si l'examen existe
			if (!$exam) {
				redirect(site_url('admin/exam'), 'refresh');
			}
	
			// Vérifier l'existence de l'étudiant et son association avec l'école
			$student_data = $this->db->get_where('students', ['id' => $student_id, 'school_id' => $school_id])->row_array();
			if (!$student_data) {
				redirect(site_url('admin/exam'), 'refresh');
			}
	
			// Récupérer les réponses soumises par l'étudiant
			$this->db->select('er.*, eq.title as question_title');
			$this->db->from('exam_responses er');
			$this->db->join('exam_questions eq', 'er.exam_question_id = eq.id', 'left');
			$this->db->where('er.exam_id', $exam_id);
			$this->db->where('er.user_id', $student_data['user_id']);
			$submitted_answers = $this->db->get()->result_array();
	
			// Préparer les données pour la vue
			$page_data['exam_details'] = $exam;
			$page_data['submitted_answers'] = $submitted_answers;
			$page_data['page_title'] = $exam['name'] . ' - ' . get_phrase('results');
	
			// Charger la vue partielle pour le modal
			$this->load->view('backend/student/mark/exam_results_modal', $page_data);
		} catch (Exception $e) {
			redirect(site_url('admin/exam'), 'refresh');
		}
	}

	public function filter_exams()
{
    if ($this->session->userdata('admin_login') != 1) {
        echo json_encode(['error' => 'Unauthorized']);
        return;
    }

    $school_id = school_id();
    $session = active_session();

    $class_id = $this->input->post('class_id');

    $date_range = $this->input->post('date_range');
    $date_from = '';
    $date_to = '';

    if (!empty($date_range)) {
        $dates = explode(' - ', $date_range);
        $date_from = strtotime(trim($dates[0]) . ' 00:00:00');
        $date_to = strtotime(trim($dates[1]) . ' 23:59:59');
    }

    $this->db->select('exams.*, classes.name as class_name');
    $this->db->from('exams');
    $this->db->join('classes', 'exams.class_id = classes.id', 'left');
    $this->db->where('exams.school_id', $school_id);
    $this->db->where('exams.session', $session);
    if (!empty($class_id)) {
        $this->db->where('exams.class_id', $class_id);
    }
 
    if (!empty($date_range)) {
        $this->db->where('exams.starting_date >=', $date_from);
        $this->db->where('exams.starting_date <=', $date_to);
    }
    $exams = $this->db->get()->result_array();

    $exam_data = [];
    foreach ($exams as $exam) {
        $exam_data[] = [
            'id' => $exam['id'],
            'name' => $exam['name'] ?: 'Unnamed Exam',
            'formatted_date' => $exam['starting_date'] ? date('D, d-M-Y H:i', $exam['starting_date']) : 'No Date',
            'class_name' => $exam['class_name'] ?: 'No Class'
           
        ];
    }

    $exam_calendar = [];
    foreach ($exams as $exam) {
        if ($exam['starting_date']) {
            $exam_calendar[] = [
                'title' => $exam['name'] ?: 'Unnamed Exam',
                'start' => date('Y-m-d H:i:s', $exam['starting_date'])
            ];
        }
    }

    echo json_encode([
        'exams' => $exam_data,
        'calendar' => $exam_calendar,
        'debug' => [
            'class_id' => $class_id,
            'date_range' => $date_range,
            'exam_count' => count($exams)
        ]
    ]);
}

public function calendar($param1 = '', $param2 = '', $param3 = '', $param4 = '') {
    if ($this->session->userdata('admin_login') != 1) {
        $csrf = [
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
        ];
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized', 'csrf' => $csrf]);
        return;
    }

    if ($param1 == 'create') {
        $this->create_event();
    }

    if ($param1 == 'update') {
        $this->update_event();
    }

    if ($param1 == 'delete') {
        $this->delete_event();
    }

    if ($param1 == 'get_events') {
        $this->get_events();
    }

    if ($param1 == 'get_user_school') {
        $this->get_user_school();
    }

    if ($param1 == 'get_school_data') {
        $this->get_school_data();
    }

    if ($param1 == 'start_meeting') {
        $this->start_meeting();
    }

    if ($param1 == 'filter') {
        $page_data['class_id'] = $param2;
        $page_data['start_date'] = $param3;
        $page_data['end_date'] = $param4;
        $this->load->view('backend/admin/calendar/list', $page_data);
    }

    if (empty($param1)) {
        $page_data['folder_name'] = 'calendar';
        $page_data['page_title'] = 'calendar';
        $this->load->view('backend/index', $page_data);
    }
}

public function get_user_school() {
    if ($this->session->userdata('admin_login') != 1) {
        echo json_encode(['status' => 'error', 'message' => 'Session expired, please login again']);
        return;
    }

    $user_id = $this->session->userdata('user_id');
    $user = $this->db->get_where('users', ['id' => $user_id])->row_array();

    if (!$user || empty($user['school_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'No school associated with this user']);
        return;
    }

    $school = $this->db->get_where('schools', ['id' => $user['school_id'], 'Etat' => 1, 'status' => 1])->row_array();
    if (!$school) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid school']);
        return;
    }

    $csrf = array(
        'csrfName' => $this->security->get_csrf_token_name(),
        'csrfHash' => $this->security->get_csrf_hash(),
    );

    echo json_encode([
        'status' => 'success',
        'data' => [
            'id' => $school['id'],
            'name' => $school['name']
        ],
        'csrf' => $csrf
    ]);
}

public function create_event() {
    if ($this->session->userdata('admin_login') != 1) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Unauthorized',
            'csrf' => [
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash()
            ]
        ]);
        return;
    }

    $data = [
        'title' => htmlspecialchars($this->input->post('title', true)),
        'starting_date' => $this->input->post('starting_date', true),
        'starting_time' => $this->input->post('starting_time', true),
		'ending_date' => $this->input->post('ending_date', true) ?: null,
        'ending_time' => $this->input->post('ending_time', true),
        'school_id' => filter_var($this->input->post('school_id', true), FILTER_VALIDATE_INT),
        'session' => $this->input->post('session', true),
        'description' => htmlspecialchars($this->input->post('description', true)),
        'recurrence_type' => $this->input->post('recurrence_type', true),
        'recurrence_end_date' => $this->input->post('recurrence_end_date', true),
        'custom_recurrence' => $this->input->post('custom_recurrence', true),
        'visio' => !empty($this->input->post('visio', true)) && $this->input->post('visio', true) == 1 ? 1 : 0,
		'created_by' => $this->session->userdata('user_id')
    ];

    // Validation des champs obligatoires
    if (empty($data['title']) || empty($data['starting_date']) || empty($data['starting_time']) || empty($data['ending_time']) || empty($data['school_id'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Tous les champs obligatoires doivent être remplis',
            'csrf' => [
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash()
            ]
        ]);
        return;
    }

    // Validation du format de la date (strictement YYYY-MM-DD)
    $start_date_obj = DateTime::createFromFormat('Y-m-d', $data['starting_date']);
    if ($start_date_obj === false || $start_date_obj->format('Y-m-d') !== $data['starting_date']) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Format de date de début invalide (attendu : YYYY-MM-DD)',
            'csrf' => [
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash()
            ]
        ]);
        return;
    }
    $data['starting_date'] = $start_date_obj->format('Y-m-d');

    // Vérifier que la date de début n'est pas dans le passé
    $now = new DateTime('now', new DateTimeZone('UTC'));
    $event_start = DateTime::createFromFormat('Y-m-d H:i:s', $data['starting_date'] . ' ' . $data['starting_time'], new DateTimeZone('UTC'));
    if ($event_start === false) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Format de date et heure de début invalide',
            'csrf' => [
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash()
            ]
        ]);
        return;
    }

	if ($data['ending_date']) {
    $end_date_obj = DateTime::createFromFormat('Y-m-d', $data['ending_date']);
    if ($end_date_obj === false || $end_date_obj->format('Y-m-d') !== $data['ending_date']) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Format de date de fin invalide (attendu : YYYY-MM-DD)',
            'csrf' => [
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash()
            ]
        ]);
        return;
    }
    $data['ending_date'] = $end_date_obj->format('Y-m-d');

    // Validation: ending_date must be on or after starting_date
    if ($end_date_obj < $start_date_obj) {
        echo json_encode([
            'status' => 'error',
            'message' => 'La date de fin doit être postérieure ou égale à la date de début',
            'csrf' => [
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash()
            ]
        ]);
        return;
    }
}
	
    if ($event_start < $now) {
        echo json_encode([
            'status' => 'error',
            'message' => 'La date et l\'heure de début ne peuvent pas être dans le passé',
            'csrf' => [
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash()
            ]
        ]);
        return;
    }

    // Validation des heures (strictement HH:mm:ss)
    if (!preg_match('/^([01][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/', $data['starting_time'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Format d\'heure de début invalide (attendu : HH:mm:ss)',
            'csrf' => [
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash()
            ]
        ]);
        return;
    }
    if (!preg_match('/^([01][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/', $data['ending_time'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Format d\'heure de fin invalide (attendu : HH:mm:ss)',
            'csrf' => [
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash()
            ]
        ]);
        return;
    }

    // Validation de school_id
    if (!$this->db->get_where('schools', ['id' => $data['school_id'], 'Etat' => 1, 'status' => 1])->row()) {
        echo json_encode([
            'status' => 'error',
            'message' => 'École invalide',
            'csrf' => [
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash()
            ]
        ]);
        return;
    }

    // Validation de class_id
    $participants = json_decode($this->input->post('participants', true), true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($participants) || empty($participants)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'You must assign the event to someone.',
            'csrf' => [
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash()
            ]
        ]);
        return;
    }

	foreach ($participants as $participant) {
        if (!isset($participant['type']) || !isset($participant['id'])) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Chaque participant doit avoir un type et un ID',
                'csrf' => [
                    'csrfName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash()
                ]
            ]);
            return;
        }
        if ($participant['type'] === 'class') {
            if (!$this->db->get_where('classes', ['id' => $participant['id'], 'school_id' => $data['school_id']])->row()) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Classe invalide pour cette école : ' . $participant['id'],
                    'csrf' => [
                        'csrfName' => $this->security->get_csrf_token_name(),
                        'csrfHash' => $this->security->get_csrf_hash()
                    ]
                ]);
                return;
            }
        } elseif ($participant['type'] === 'individual') {
            if (!$this->db->get_where('users', ['id' => $participant['id']])->row()) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Utilisateur invalide : ' . $participant['id'],
                    'csrf' => [
                        'csrfName' => $this->security->get_csrf_token_name(),
                        'csrfHash' => $this->security->get_csrf_hash()
                    ]
                ]);
                return;
            }
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Type de participant invalide : ' . $participant['type'],
                'csrf' => [
                    'csrfName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash()
                ]
            ]);
            return;
        }
    }

    // Validation de session (exemple)
    if (!empty($data['session']) && !preg_match('/^\d{4}-\d{4}$/', $data['session'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Format de session invalide (ex. 2024-2025)',
            'csrf' => [
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash()
            ]
        ]);
        return;
    }

    // Validation de la récurrence
    if (!in_array($data['recurrence_type'], ['does_not_repeat', 'every_weekday', 'daily', 'weekly', 'monthly', 'yearly'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Type de récurrence invalide',
            'csrf' => [
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash()
            ]
        ]);
        return;
    }

    if ($data['recurrence_type'] !== 'does_not_repeat') {
        if (!empty($data['recurrence_end_date'])) {
            $recurrence_end_date_obj = DateTime::createFromFormat('Y-m-d', $data['recurrence_end_date']);
            if ($recurrence_end_date_obj === false || $recurrence_end_date_obj->format('Y-m-d') !== $data['recurrence_end_date']) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Format de date de fin de récurrence invalide (attendu : YYYY-MM-DD)',
                    'csrf' => [
                        'csrfName' => $this->security->get_csrf_token_name(),
                        'csrfHash' => $this->security->get_csrf_hash()
                    ]
                ]);
                return;
            }
            if ($recurrence_end_date_obj < $start_date_obj) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'La date de fin de récurrence doit être postérieure à la date de début',
                    'csrf' => [
                        'csrfName' => $this->security->get_csrf_token_name(),
                        'csrfHash' => $this->security->get_csrf_hash()
                    ]
                ]);
                return;
            }
            $data['recurrence_end_date'] = $recurrence_end_date_obj->format('Y-m-d');
        } else {
            // Set default recurrence end date to 1 year from starting_date
            $default_end_date = clone $start_date_obj;
            $default_end_date->modify('+1 year');
            $data['recurrence_end_date'] = $default_end_date->format('Y-m-d');
        }
    } else {
        $data['recurrence_end_date'] = null;
        $data['custom_recurrence'] = null;
    }

    // Validation de custom_recurrence pour weekly
    if ($data['recurrence_type'] === 'weekly' && !empty($data['custom_recurrence'])) {
        $decoded = json_decode($data['custom_recurrence'], true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded) || empty($decoded)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'La récurrence personnalisée doit être un tableau JSON valide pour la récurrence hebdomadaire',
                'csrf' => [
                    'csrfName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash()
                ]
            ]);
            return;
        }
        $validDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        if (array_diff($decoded, $validDays)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Les jours dans la récurrence personnalisée doivent être des jours valides',
                'csrf' => [
                    'csrfName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash()
                ]
            ]);
            return;
        }
    } elseif ($data['recurrence_type'] !== 'weekly') {
        $data['custom_recurrence'] = null;
    }

    try {
        $this->db->insert('event_calendars', $data);
        $event_id = $this->db->insert_id();
		foreach ($participants as $participant) {
            $this->db->insert('participants', [
                'event_id' => $event_id,
                'guest' => $participant['id'],
                'type' => $participant['type'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
        echo json_encode([
            'status' => 'success',
            'message' => get_phrase('Event created successfully'),
            'csrf' => [
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash()
            ],
            'event_id' => $event_id,
            'event_data' => $data,
			'participants' => $participants
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => get_phrase('Error creating event: ') . $e->getMessage(),
            'csrf' => [
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash()
            ]
        ]);
    }
}

    public function update_event() {
        if ($this->session->userdata('admin_login') != 1) {
            $csrf = [
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
            ];
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized', 'csrf' => $csrf]);
            return;
        }

        $event_id = filter_var($this->input->post('id', true), FILTER_VALIDATE_INT);
        if (!$event_id) {
            $csrf = [
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
            ];
            echo json_encode(['status' => 'error', 'message' => 'Event ID is required', 'csrf' => $csrf]);
            return;
        }

        // Vérifier si l'événement existe
        if (!$this->db->get_where('event_calendars', ['id' => $event_id])->row()) {
            $csrf = [
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
            ];
            echo json_encode(['status' => 'error', 'message' => get_phrase('Event not found'), 'csrf' => $csrf]);
            return;
        }

        $data = [
            'title' => htmlspecialchars($this->input->post('title', true)),
            'starting_date' => $this->input->post('starting_date', true),
            'starting_time' => $this->input->post('starting_time', true),
			'ending_date' => $this->input->post('ending_date', true) ?: null,
            'ending_time' => $this->input->post('ending_time', true),
            'school_id' => filter_var($this->input->post('school_id', true), FILTER_VALIDATE_INT),
            'session' => $this->input->post('session', true),
            'description' => htmlspecialchars($this->input->post('description', true)),
            'recurrence_type' => $this->input->post('recurrence_type', true),
            'recurrence_end_date' => $this->input->post('recurrence_end_date', true),
            'custom_recurrence' => $this->input->post('custom_recurrence', true),
            'visio' => !empty($this->input->post('visio', true)) && $this->input->post('visio', true) == 1 ? 1 : 0
        ];

        $csrf = [
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
        ];

        // Validation des champs obligatoires
        if (empty($data['title']) || empty($data['starting_date']) || empty($data['starting_time']) || empty($data['ending_time']) || empty($data['school_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Tous les champs obligatoires doivent être remplis', 'csrf' => $csrf]);
            return;
        }

        // Validation du format de la date (strictement YYYY-MM-DD)
        $start_date_obj = DateTime::createFromFormat('Y-m-d', $data['starting_date']);
        if ($start_date_obj === false || $start_date_obj->format('Y-m-d') !== $data['starting_date']) {
            echo json_encode(['status' => 'error', 'message' => 'Format de date de début invalide (attendu : YYYY-MM-DD)', 'csrf' => $csrf]);
            return;
        }
        $data['starting_date'] = $start_date_obj->format('Y-m-d');

        $now = new DateTime('now', new DateTimeZone('UTC'));
        $event_start = DateTime::createFromFormat('Y-m-d H:i:s', $data['starting_date'] . ' ' . $data['starting_time'], new DateTimeZone('UTC'));
        if ($event_start === false) {
            echo json_encode(['status' => 'error', 'message' => 'Format de date et heure de début invalide', 'csrf' => $csrf]);
            return;
        }
        if ($event_start < $now) {
            echo json_encode(['status' => 'error', 'message' => 'La date et l\'heure de début ne peuvent pas être dans le passé', 'csrf' => $csrf]);
            return;
        }
		if ($data['ending_date']) {
    $end_date_obj = DateTime::createFromFormat('Y-m-d', $data['ending_date']);
    if ($end_date_obj === false || $end_date_obj->format('Y-m-d') !== $data['ending_date']) {
        echo json_encode(['status' => 'error', 'message' => 'Format de date de fin invalide (attendu : YYYY-MM-DD)', 'csrf' => $csrf]);
        return;
    }
    $data['ending_date'] = $end_date_obj->format('Y-m-d');

    // Validation: ending_date must be on or after starting_date
    if ($end_date_obj < $start_date_obj) {
        echo json_encode(['status' => 'error', 'message' => 'La date de fin doit être postérieure ou égale à la date de début', 'csrf' => $csrf]);
        return;
    }
}
        // Validation des heures (strictement HH:mm:ss)
        if (!preg_match('/^([01][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/', $data['starting_time'])) {
            echo json_encode(['status' => 'error', 'message' => 'Format d\'heure de début invalide (attendu : HH:mm:ss)', 'csrf' => $csrf]);
            return;
        }
        if (!preg_match('/^([01][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/', $data['ending_time'])) {
            echo json_encode(['status' => 'error', 'message' => 'Format d\'heure de fin invalide (attendu : HH:mm:ss)', 'csrf' => $csrf]);
            return;
        }


        // Validation de school_id
        if (!$this->db->get_where('schools', ['id' => $data['school_id'], 'Etat' => 1, 'status' => 1])->row()) {
            echo json_encode(['status' => 'error', 'message' => 'École invalide', 'csrf' => $csrf]);
            return;
        }

        // Validation de class_id
        $participants = json_decode($this->input->post('participants', true), true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($participants) || empty($participants)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'You must assign the event to someone.',
                'csrf' => $csrf
            ]);
            return;
        }

		foreach ($participants as $participant) {
            if (!isset($participant['type']) || !isset($participant['id'])) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Chaque participant doit avoir un type et un ID',
                    'csrf' => $csrf
                ]);
                return;
            }
            if ($participant['type'] === 'class') {
                if (!$this->db->get_where('classes', ['id' => $participant['id'], 'school_id' => $data['school_id']])->row()) {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Classe invalide pour cette école : ' . $participant['id'],
                        'csrf' => $csrf
                    ]);
                    return;
                }
            } elseif ($participant['type'] === 'individual') {
                if (!$this->db->get_where('users', ['id' => $participant['id']])->row()) {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Utilisateur invalide : ' . $participant['id'],
                        'csrf' => $csrf
                    ]);
                    return;
                }
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Type de participant invalide : ' . $participant['type'],
                    'csrf' => $csrf
                ]);
                return;
            }
        }

        // Validation de la récurrence
        if (!in_array($data['recurrence_type'], ['does_not_repeat', 'every_weekday', 'daily', 'weekly', 'monthly', 'yearly'])) {
            echo json_encode(['status' => 'error', 'message' => 'Type de récurrence invalide', 'csrf' => $csrf]);
            return;
        }

        if ($data['recurrence_type'] !== 'does_not_repeat') {
            if (!empty($data['recurrence_end_date'])) {
                $recurrence_end_date_obj = DateTime::createFromFormat('Y-m-d', $data['recurrence_end_date']);
                if ($recurrence_end_date_obj === false || $recurrence_end_date_obj->format('Y-m-d') !== $data['recurrence_end_date']) {
                    echo json_encode(['status' => 'error', 'message' => 'Format de date de fin de récurrence invalide (attendu : YYYY-MM-DD)', 'csrf' => $csrf]);
                    return;
                }
                if ($recurrence_end_date_obj < $start_date_obj) {
                    echo json_encode(['status' => 'error', 'message' => 'La date de fin de récurrence doit être postérieure à la date de début', 'csrf' => $csrf]);
                    return;
                }
                $data['recurrence_end_date'] = $recurrence_end_date_obj->format('Y-m-d');
            } else {
                // Set default recurrence end date to 1 year from starting_date
                $default_end_date = clone $start_date_obj;
                $default_end_date->modify('+1 year');
                $data['recurrence_end_date'] = $default_end_date->format('Y-m-d');
            }
        } else {
            $data['recurrence_end_date'] = null;
            $data['custom_recurrence'] = null;
        }

        // Validation de custom_recurrence pour weekly
        if ($data['recurrence_type'] === 'weekly' && !empty($data['custom_recurrence'])) {
            $decoded = json_decode($data['custom_recurrence'], true);
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded) || empty($decoded)) {
                echo json_encode(['status' => 'error', 'message' => 'La récurrence personnalisée doit être un tableau JSON valide pour la récurrence hebdomadaire', 'csrf' => $csrf]);
                return;
            }
            $validDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            if (array_diff($decoded, $validDays)) {
                echo json_encode(['status' => 'error', 'message' => 'Les jours dans la récurrence personnalisée doivent être des jours valides', 'csrf' => $csrf]);
                return;
            }
        } elseif ($data['recurrence_type'] !== 'weekly') {
            $data['custom_recurrence'] = null;
        }

        try {
            $this->db->where('id', $event_id);
            $this->db->update('event_calendars', $data);
            // Mettre à jour les rendez-vous associés
            $this->db->where('event_id', $event_id);
            $this->db->update('appointments', [
                'title' => $data['title'],
                'start_date' => $data['starting_date'],
                'school_id' => $data['school_id'],
                'visio' => $data['visio']
            ]);
			 $this->db->where('event_id', $event_id);
            $this->db->delete('participants');
            foreach ($participants as $participant) {
                $this->db->insert('participants', [
                    'event_id' => $event_id,
                    'guest' => $participant['id'],
                    'type' => $participant['type'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
            echo json_encode([
                'status' => 'success',
                'message' => get_phrase('Event successfully updated'),
                'csrf' => $csrf,
                'event_data' => $data,
				'participants' => $participants
            ]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => get_phrase('Error updating event : ') . $e->getMessage(), 'csrf' => $csrf]);
        }
    }

    public function delete_event() {
        if ($this->session->userdata('admin_login') != 1) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            return;
        }

        $event_id = filter_var($this->input->post('id', true), FILTER_VALIDATE_INT);
        if (!$event_id) {
            echo json_encode(['status' => 'error', 'message' => 'Event ID is required']);
            return;
        }

        // Vérifier si l'événement existe
        if (!$this->db->get_where('event_calendars', ['id' => $event_id])->row()) {
            echo json_encode(['status' => 'error', 'message' => get_phrase('Event not found')]);
            return;
        }

        try {
            // Terminer les réunions BBB associées
            $appointments = $this->db->get_where('appointments', ['event_id' => $event_id])->result_array();
            foreach ($appointments as $appointment) {
                $existing_meeting = $this->db->get_where('sessions_meetings', ['appointment_id' => $appointment['id']])->row_array();
                if ($existing_meeting && $existing_meeting['meeting_id']) {
                    $this->load->config('bigbluebutton');
                    $bbb_url = $this->config->item('bbb_url');
                    $bbb_secret = $this->config->item('bbb_secret');
                    $params = "meetingID=" . urlencode($existing_meeting['meeting_id']);
                    $checksum = sha1("end" . $params . $bbb_secret);
                    $api_url = $bbb_url . "end?" . $params . "&checksum=" . $checksum;
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $api_url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                    curl_exec($ch);
                    curl_close($ch);
                }
            }

            $this->db->where('event_id', $event_id);
            $this->db->delete('appointments');

			$this->db->where('event_id', $event_id);
            $this->db->delete('participants');

            $this->db->where('id', $event_id);
            $this->db->delete('event_calendars');

            $csrf = [
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
            ];
            echo json_encode([
                'status' => 'success',
                'message' => get_phrase('Event successfully deleted'),
                'csrf' => $csrf
            ]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => get_phrase('Error deleting event: ') . $e->getMessage()]);
        }
    }

    public function get_events() {
    // Vérifier l'authentification de l'utilisateur
    if ($this->session->userdata('admin_login') != 1) {
        $csrf = [
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
        ];
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized', 'csrf' => $csrf]);
        return;
    }

    // Récupérer l'ID de l'école
    $user_id = $this->session->userdata('user_id');
    $user_details = $this->user_model->get_user_details($user_id);
    $school_id = $user_details['school_id'] ?? null;

    if (!$school_id) {
        $csrf = [
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
        ];
        echo json_encode(['status' => 'error', 'message' => 'No school associated with this user', 'csrf' => $csrf]);
        return;
    }

    $start_date = $this->input->get('start_date', true);
    $end_date = $this->input->get('end_date', true);
    $event_id = $this->input->get('id', true);
    $class_id = $this->input->get('class_id', true);

    // Validation des dates
    if ($start_date && $end_date) {
        $start_date_obj = DateTime::createFromFormat('Y-m-d', $start_date);
        $end_date_obj = DateTime::createFromFormat('Y-m-d', $end_date);
        if ($start_date_obj === false || $end_date_obj === false || $start_date_obj->format('Y-m-d') !== $start_date || $end_date_obj->format('Y-m-d') !== $end_date) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid date format (expected: YYYY-MM-DD)',
                'csrf' => [
                    'csrfName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash()
                ]
            ]);
            return;
        }
        $start_date = $start_date_obj->format('Y-m-d');
        $end_date = $end_date_obj->format('Y-m-d');
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'start_date and end_date are required',
            'csrf' => [
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash()
            ]
        ]);
        return;
    }

    // Récupérer les événements
    $this->db->select('event_calendars.*, schools.name as school_name, users.name as created_by_name');
    $this->db->from('event_calendars');
    $this->db->join('schools', 'event_calendars.school_id = schools.id', 'left');
    $this->db->join('users', 'event_calendars.created_by = users.id', 'left');
    $this->db->where('event_calendars.school_id', $school_id);

    if ($event_id) {
        $this->db->where('event_calendars.id', $event_id);
    } else {
        $this->db->where('(event_calendars.starting_date <= "' . $end_date . '" AND (event_calendars.ending_date >= "' . $start_date . '" OR event_calendars.ending_date IS NULL))');
    }

    if ($class_id) {
        $this->db->join('participants', 'event_calendars.id = participants.event_id', 'left');
        $this->db->where('participants.guest', $class_id);
        $this->db->where('participants.type', 'class');
    }

    $events = $this->db->get()->result_array();

    // Charger la configuration BigBlueButton
    $this->load->config('bigbluebutton');
    $bbb_url = $this->config->item('bbb_url');
    $bbb_secret = $this->config->item('bbb_secret');

    // Post-traitement des événements
    $processed_events = [];
	$now = new DateTime('now', new DateTimeZone('UTC'));
    $threshold = (clone $now)->modify('-24 hours');
    foreach ($events as $event) {
        $event['school_name'] = $event['school_name'] ?? '';
        $event['created_by_name'] = $event['created_by_name'] ?? 'Unknown';
        $event['participants'] = [];
        $participants = $this->db->get_where('participants', ['event_id' => $event['id']])->result_array();
        foreach ($participants as $participant) {
            $participant_data = [
                'id' => $participant['guest'],
                'type' => $participant['type']
            ];
            if ($participant['type'] === 'class') {
                $class = $this->db->get_where('classes', ['id' => $participant['guest']])->row();
                $participant_data['name'] = $class ? $class->name : 'Unknown';
            } elseif ($participant['type'] === 'individual') {
                $user = $this->db->get_where('users', ['id' => $participant['guest']])->row();
                $participant_data['name'] = $user ? $user->name : 'Unknown';
            }
            $event['participants'][] = $participant_data;
        }

        // Normaliser les dates
        $start_date_obj = DateTime::createFromFormat('Y-m-d', $event['starting_date']);
        if ($start_date_obj) {
            $event['starting_date'] = $start_date_obj->format('Y-m-d');
        }
        if ($event['ending_date']) {
            $end_date_obj = DateTime::createFromFormat('Y-m-d', $event['ending_date']);
            if ($end_date_obj) {
                $event['ending_date'] = $end_date_obj->format('Y-m-d');
            }
        }
        if ($event['recurrence_end_date']) {
            $recurrence_end_date_obj = DateTime::createFromFormat('Y-m-d H:i:s', $event['recurrence_end_date']);
            if ($recurrence_end_date_obj === false) {
                $recurrence_end_date_obj = DateTime::createFromFormat('Y-m-d', $event['recurrence_end_date']);
            }
            $event['recurrence_end_date'] = $recurrence_end_date_obj ? $recurrence_end_date_obj->format('Y-m-d') : null;
        }

        // Check if the event has expired
        $event_start = DateTime::createFromFormat('Y-m-d H:i:s', $event['starting_date'] . ' ' . $event['starting_time'], new DateTimeZone('UTC'));
        $event['is_expired'] = $event['recurrence_type'] === 'does_not_repeat' && $event_start < $threshold;

        // Initialize occurrences array
        $event['occurrences'] = [];
		if ($event['recurrence_type'] !== 'does_not_repeat' && $event['recurrence_end_date']) {
            $recurrence_end_date = new DateTime($event['recurrence_end_date']);
            $current_date = new DateTime(max($event['starting_date'], $start_date));
            $end_period = new DateTime($end_date);

            $interval = null;
            if ($event['recurrence_type'] === 'daily') {
                $interval = new DateInterval('P1D');
            } elseif ($event['recurrence_type'] === 'weekly') {
                $interval = new DateInterval('P1D');
            } elseif ($event['recurrence_type'] === 'every_weekday') {
                $interval = new DateInterval('P1D');
            } elseif ($event['recurrence_type'] === 'monthly') {
                $interval = new DateInterval('P1M');
            } elseif ($event['recurrence_type'] === 'yearly') {
                $interval = new DateInterval('P1Y');
            }

            $custom_days = ($event['recurrence_type'] === 'weekly' && $event['custom_recurrence']) ? json_decode($event['custom_recurrence'], true) : [];

            while ($current_date <= $recurrence_end_date && $current_date <= $end_period) {
                $is_valid_date = true;
                if ($event['recurrence_type'] === 'every_weekday') {
                    $day_of_week = $current_date->format('l');
                    $is_valid_date = !in_array($day_of_week, ['Saturday', 'Sunday']);
                } elseif ($event['recurrence_type'] === 'weekly' && !empty($custom_days)) {
                    $day_of_week = $current_date->format('l');
                    $is_valid_date = in_array($day_of_week, $custom_days);
                } elseif ($event['recurrence_type'] === 'weekly') {
                    $is_valid_date = $current_date->format('l') === $start_date_obj->format('l');
                }

                if ($is_valid_date && $current_date->format('Y-m-d') >= $start_date) {
                    $occurrence_date = $current_date->format('Y-m-d');
                    $occurrence_start = DateTime::createFromFormat('Y-m-d H:i:s', $occurrence_date . ' ' . $event['starting_time'], new DateTimeZone('UTC'));
                    if ($occurrence_start === false) {
                        $current_date->add($interval);
                        continue;
                    }
                    $is_expired = $occurrence_start < $threshold;

                    $event['occurrences'][$occurrence_date] = [
                        'meeting_id' => null,
                        'is_running' => false,
                        'participant_count' => 0,
                        'is_expired' => $is_expired
                    ];
                }

                $current_date->add($interval);
            }
        }
        if ($event['visio'] == 1) {
            $this->db->select('start_date, meeting_id');
            $this->db->from('appointments');
            $this->db->where('event_id', $event['id']);
            $this->db->where('Etat', 1);
            $this->db->where('DATE(start_date) >=', $start_date);
            $this->db->where('DATE(start_date) <=', $end_date);
            $appointments = $this->db->get()->result_array();

            foreach ($appointments as $appointment) {
                $occurrence_date = (new DateTime($appointment['start_date']))->format('Y-m-d');
                $meeting_id = $appointment['meeting_id'] ?? null;

                // Check meeting status via BBB API
                $is_running = false;
                $participant_count = 0;
                if ($meeting_id) {
                    $params = "meetingID=" . urlencode($meeting_id);
                    $checksum = sha1("isMeetingRunning" . $params . $bbb_secret);
                    $is_running_url = $bbb_url . "isMeetingRunning?" . $params . "&checksum=" . $checksum;

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $is_running_url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                    $is_running_response = curl_exec($ch);
                    $curl_error = curl_error($ch);
                    curl_close($ch);

                    if (!$curl_error) {
                        $is_running_xml = simplexml_load_string($is_running_response);
                        if ($is_running_xml && (string)$is_running_xml->returncode === "SUCCESS") {
                            $is_running = (string)$is_running_xml->running === "true";
                            if ($is_running) {
                                $params = "meetingID=" . urlencode($meeting_id);
                                $checksum = sha1("getMeetingInfo" . $params . $bbb_secret);
                                $api_url = $bbb_url . "getMeetingInfo?" . $params . "&checksum=" . $checksum;
                                $ch = curl_init();
                                curl_setopt($ch, CURLOPT_URL, $api_url);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                                $response = curl_exec($ch);
                                curl_close($ch);
                                $xml = simplexml_load_string($response);
                                if ($xml && (string)$xml->returncode === "SUCCESS") {
                                    $participant_count = (int)$xml->participantCount;
                                }
                            }
                        }
                    }
                }

                $event['occurrences'][$occurrence_date] = [
                    'meeting_id' => $meeting_id,
                    'is_running' => $is_running,
                    'participant_count' => $participant_count,
					'is_expired' => $is_expired
                ];
            }
        }

        // Add to processed events
        if ($event_id) {
            $processed_events[] = $event;
        } else {
            $event_start = new DateTime($event['starting_date']);
            $event_end = $event['ending_date'] ? new DateTime($event['ending_date']) : $event_start;
            $range_start = new DateTime($start_date);
            $range_end = new DateTime($end_date);

            if ($event_start <= $range_end && $event_end >= $range_start) {
                $processed_events[] = $event;
            }
        }
    }

    echo json_encode([
        'status' => 'success',
        'data' => $processed_events,
        'csrf' => [
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash()
        ]
    ]);
}

public function start_meeting(){
    // Vérifier l'authentification
    if ($this->session->userdata('admin_login') != 1) {
        $csrf = [
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
        ];
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized', 'csrf' => $csrf]);
        return;
    }

    // Récupérer et valider les paramètres
    $event_id = filter_var($this->input->post('event_id', true), FILTER_VALIDATE_INT);
    $occurrence_date = $this->input->post('occurrence_date', true);
    if (!$event_id || !$occurrence_date) {
        $csrf = [
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
        ];
        echo json_encode(['status' => 'error', 'message' => 'Event ID and occurrence date are required', 'csrf' => $csrf]);
        return;
    }

    // Valider le format de la date
    $occurrence_date_obj = DateTime::createFromFormat('Y-m-d', $occurrence_date);
    if ($occurrence_date_obj === false || $occurrence_date_obj->format('Y-m-d') !== $occurrence_date) {
        $csrf = [
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
        ];
        echo json_encode(['status' => 'error', 'message' => 'Invalid occurrence date format (expected: YYYY-MM-DD)', 'csrf' => $csrf]);
        return;
    }
    $occurrence_date = $occurrence_date_obj->format('Y-m-d');

    // Charger l'événement
    $event = $this->db->get_where('event_calendars', ['id' => $event_id])->row_array();
    if (!$event) {
        $csrf = [
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
        ];
        echo json_encode(['status' => 'error', 'message' => 'Event not found', 'csrf' => $csrf]);
        return;
    }

    // Vérifier si l'événement est en visio
    if ($event['visio'] != 1) {
        $csrf = [
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
        ];
        echo json_encode(['status' => 'error', 'message' => 'Video conferencing is not enabled for this event', 'csrf' => $csrf]);
        return;
    }

    // Désactiver les autres appointments pour cet événement
    $this->db->where('event_id', $event_id);
    $this->db->where('DATE(start_date) !=', $occurrence_date);
    $this->db->where('Etat', 1);
    $this->db->update('appointments', ['Etat' => 0]);

    // Vérifier si un appointment actif existe pour cette occurrence
    $this->db->where('event_id', $event_id);
    $this->db->where('DATE(start_date)', $occurrence_date);
    $this->db->where('Etat', 1);
    $appointment = $this->db->get('appointments')->row_array();
    $appointment_id = $appointment ? $appointment['id'] : null;

    // Charger la configuration BigBlueButton
    $this->load->config('bigbluebutton');
    $bbb_url = $this->config->item('bbb_url');
    $bbb_secret = $this->config->item('bbb_secret');

    // Si un appointment existe et le meeting est en cours, le rejoindre
    if ($appointment && $appointment['meeting_id']) {
        $meeting = $this->db->get_where('sessions_meetings', ['meeting_id' => $appointment['meeting_id'], 'appointment_id' => $appointment_id])->row_array();
        if ($meeting) {
            $params = "meetingID=" . urlencode($appointment['meeting_id']);
            $checksum = sha1("isMeetingRunning" . $params . $bbb_secret);
            $is_running_url = $bbb_url . "isMeetingRunning?" . $params . "&checksum=" . $checksum;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $is_running_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            $is_running_response = curl_exec($ch);
            $curl_error = curl_error($ch);
            curl_close($ch);

            if ($curl_error) {
                $csrf = [
                    'csrfName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                ];
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Failed to check meeting status due to server error',
                    'csrf' => $csrf
                ]);
                return;
            }

            $is_running_xml = simplexml_load_string($is_running_response);
            if ($is_running_xml && (string)$is_running_xml->returncode === "SUCCESS" && (string)$is_running_xml->running === "true") {
                $params = "meetingID=" . urlencode($appointment['meeting_id']);
                $checksum = sha1("getMeetingInfo" . $params . $bbb_secret);
                $api_url = $bbb_url . "getMeetingInfo?" . $params . "&checksum=" . $checksum;

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $api_url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                $response = curl_exec($ch);
                curl_close($ch);

                $xml = simplexml_load_string($response);
                $participant_count = ($xml && (string)$xml->returncode === "SUCCESS") ? (int)$xml->participantCount : 0;

                $user_details = $this->user_model->get_user_details($this->session->userdata('user_id'));
                $full_name = urlencode($user_details['name'] ?? 'Admin-' . rand(1000, 9999));
                $join_params = "fullName=$full_name&meetingID=" . urlencode($appointment['meeting_id']) . "&password=" . $meeting['moderator_pw'] . "&redirect=true";
                $join_checksum = sha1("join" . $join_params . $bbb_secret);
                $join_url = $bbb_url . "join?" . $join_params . "&checksum=" . $join_checksum;

				// Retrieve associated participants
                $this->db->select('guest, type');
                $this->db->from('appointment_participants');
                $this->db->where('appointment_id', $appointment_id);
                $participants = $this->db->get()->result_array();

                $csrf = [
                    'csrfName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                ];
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Joining existing meeting',
                    'join_url' => $join_url,
                    'meeting_id' => $appointment['meeting_id'],
                    'appointment_id' => $appointment_id,
                    'participant_count' => $participant_count,
                    'is_running' => true,
					'participants' => $participants,
                    'csrf' => $csrf
                ]);
                return;
            } else {
                // Marquer l'ancien appointment comme inactif
                $this->db->where('id', $appointment_id);
                $this->db->update('appointments', ['Etat' => 0]);
            }
        }
    }

    // Retrieve all participants (classes and users) for the event
    $this->db->select('guest, type');
    $this->db->from('participants');
    $this->db->where('event_id', $event_id);
    $participants = $this->db->get()->result_array();
    $appointment_data = [
        'event_id' => $event_id,
        'title' => $event['title'],
        'start_date' => $occurrence_date . ' ' . $event['starting_time'],
        'school_id' => $event['school_id'],
        'visio' => $event['visio'],
        'Etat' => 1,
        'meeting_id' => null
    ];
    $this->db->insert('appointments', $appointment_data);
    $appointment_id = $this->db->insert_id();

	foreach ($participants as $participant) {
        $this->db->insert('appointment_participants', [
            'appointment_id' => $appointment_id,
            'guest' => $participant['guest'],
            'type' => $participant['type']
        ]);
    }

    // Create a new BigBlueButton meeting
    $meeting_name = $event['title'] ? $event['title'] : "Meeting for Event $event_id";
    $new_meeting_id = "meeting-$appointment_id-" . time();
    $attendee_password = "ap_" . $appointment_id;
    $moderator_password = "mp_" . $appointment_id;
    $start_time = $occurrence_date . ' ' . $event['starting_time'];
    $end_time = date('Y-m-d H:i:s', strtotime($start_time . ' +2 hours'));

    $params = "name=" . urlencode($meeting_name) .
              "&meetingID=" . urlencode($new_meeting_id) .
              "&attendeePW=$attendee_password" .
              "&moderatorPW=$moderator_password" .
              "&record=true" .
              "&autoStartRecording=false" .
              "&allowStartStopRecording=true" .
              "&welcome=" . urlencode(get_phrase("Welcome to the meeting") . ': ' . $event['title']) .
              "&endWhenNoModerator=false" .
              "&duration=120";

    $checksum = sha1("create" . $params . $bbb_secret);
    $create_url = $bbb_url . "create?" . $params . "&checksum=" . $checksum;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $create_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $response = curl_exec($ch);
    $curl_error = curl_error($ch);
    curl_close($ch);

    if ($curl_error) {
        $csrf = [
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
        ];
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to create meeting due to server error',
            'csrf' => $csrf
        ]);
        return;
    }

    if (empty($response)) {
        $csrf = [
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
        ];
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to create meeting: No response from BBB server',
            'csrf' => $csrf
        ]);
        return;
    }

    $xml = simplexml_load_string($response);
    if ($xml === false || !isset($xml->returncode)) {
        $csrf = [
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
        ];
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to create meeting: Invalid response from BBB server',
            'csrf' => $csrf
        ]);
        return;
    }

    if ((string)$xml->returncode === "SUCCESS") {
        // Enregistrer les détails du meeting
        $meeting_data = [
            'meeting_id' => $new_meeting_id,
            'appointment_id' => $appointment_id,
            'name' => $meeting_name,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'attendee_pw' => $attendee_password,
            'moderator_pw' => $moderator_password,
            'school_id' => $event['school_id'],
            'user_id' => $this->session->userdata('user_id'),
            'class_id' => null,
            'created_at' => date('Y-m-d H:i:s'),
            'description' => null
        ];
        $this->db->insert('sessions_meetings', $meeting_data);

        // Mettre à jour l'appointment avec le nouveau meeting_id
        $this->db->where('id', $appointment_id);
        $this->db->update('appointments', ['meeting_id' => $new_meeting_id]);

        // Vérifier si le meeting est en cours
        $is_running_params = "meetingID=" . urlencode($new_meeting_id);
        $is_running_checksum = sha1("isMeetingRunning" . $is_running_params . $bbb_secret);
        $is_running_url = $bbb_url . "isMeetingRunning?" . $is_running_params . "&checksum=" . $is_running_checksum;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $is_running_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $is_running_response = curl_exec($ch);
        $curl_error = curl_error($ch);
        curl_close($ch);

        $is_running = false;
        $participant_count = 0;
        if (!$curl_error) {
            $is_running_xml = simplexml_load_string($is_running_response);
            if ($is_running_xml && (string)$is_running_xml->returncode === "SUCCESS") {
                $is_running = (string)$is_running_xml->running === "true";
                if ($is_running) {
                    $params = "meetingID=" . urlencode($new_meeting_id);
                    $checksum = sha1("getMeetingInfo" . $params . $bbb_secret);
                    $api_url = $bbb_url . "getMeetingInfo?" . $params . "&checksum=" . $checksum;
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $api_url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                    $response = curl_exec($ch);
                    curl_close($ch);
                    $xml = simplexml_load_string($response);
                    if ($xml && (string)$xml->returncode === "SUCCESS") {
                        $participant_count = (int)$xml->participantCount;
                    }
                }
            }
        }

        // Generate Join URL
        $user_details = $this->user_model->get_user_details($this->session->userdata('user_id'));
        $full_name = urlencode($user_details['name'] ?? 'Admin-' . rand(1000, 9999));
        $join_params = "fullName=$full_name&meetingID=" . urlencode($new_meeting_id) . "&password=$moderator_password&redirect=true";
        $join_checksum = sha1("join" . $join_params . $bbb_secret);
        $join_url = $bbb_url . "join?" . $join_params . "&checksum=" . $join_checksum;

		// Retrieve associated participants
        $this->db->select('guest, type');
        $this->db->from('appointment_participants');
        $this->db->where('appointment_id', $appointment_id);
        $participants = $this->db->get()->result_array();

        $csrf = [
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
        ];
        echo json_encode([
            'status' => 'success',
            'message' => 'New meeting created successfully',
            'join_url' => $join_url,
            'meeting_id' => $new_meeting_id,
            'appointment_id' => $appointment_id,
            'participant_count' => $participant_count,
            'is_running' => $is_running,
			'participants' => $participants,
            'csrf' => $csrf
        ]);
    } else {
        $csrf = [
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
        ];
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to create meeting: ' . (string)$xml->message,
            'csrf' => $csrf
        ]);
    }
}

public function get_classes_by_school()
{
    if ($this->session->userdata('admin_login') != 1) {
        echo json_encode(['status' => 'error', 'message' => 'Session expired, please login again']);
        return;
    }

    $school_id = $this->input->post('school_id');
    if (empty($school_id)) {
        echo json_encode(['classes' => [], 'status' => 'error', 'message' => 'No school ID provided']);
        return;
    }

    $classes = $this->db->get_where('classes', array('school_id' => $school_id))->result_array();
    if (empty($classes)) {
        echo json_encode(['classes' => [], 'status' => 'success', 'message' => 'No classes found for this school']);
    } else {
        $csrf = array(
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
        );
        echo json_encode(array('classes' => $classes, 'csrf' => $csrf, 'status' => 'success'));
    }
}

public function get_school_data() {
    if ($this->session->userdata('admin_login') != 1) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Unauthorized',
            'csrf' => [
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash()
            ]
        ]);
        return;
    }

    $school_id = filter_var($this->input->post('school_id', true), FILTER_VALIDATE_INT);
    if (!$school_id || !$this->db->get_where('schools', ['id' => $school_id, 'Etat' => 1, 'status' => 1])->row()) {
        echo json_encode([
            'status' => 'error',
            'message' => 'École invalide',
            'csrf' => [
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash()
            ]
        ]);
        return;
    }

    try {
        // Récupérer les classes
        $this->db->select('id, name');
        $this->db->where('school_id', $school_id);
        $classes = $this->db->get('classes')->result_array();

        $current_user_id = $this->session->userdata('user_id');

        // Récupérer les participants envoyés dans la requête
        $participants = json_decode($this->input->post('participants', true), true);
        $class_ids = [];

        if (is_array($participants) && !empty($participants)) {
            foreach ($participants as $participant) {
                if (isset($participant['type']) && $participant['type'] === 'class' && isset($participant['id'])) {
                    $class_ids[] = $participant['id'];
                }
            }
        }

        $users = [];

        // 1. Récupérer les étudiants
        $this->db->select('DISTINCT(u.id), u.name, "student" as type, u.role, u.status as user_status, s.status as student_status');
        $this->db->from('users u');
        $this->db->join('students s', 's.user_id = u.id', 'inner');
        $this->db->join('enrols e', 'e.student_id = s.id', 'inner');
        $this->db->where('e.school_id', $school_id);
        $this->db->where('u.status', 1);
        $this->db->where('s.status', 1);
        $this->db->where('u.id !=', $current_user_id);
        if (!empty($class_ids)) {
            $this->db->where_not_in('e.class_id', $class_ids);
        }
        $this->db->order_by('u.name', 'ASC');
        $students = $this->db->get()->result_array();
        $users = array_merge($users, $students);

        // 2. Récupérer les enseignants
        $this->db->select('DISTINCT(u.id), u.name, "teacher" as type, u.role');
        $this->db->from('users u');
        $this->db->join('teachers t', 't.user_id = u.id', 'inner');
        $this->db->where('t.school_id', $school_id);
        $this->db->where('u.status', 1);
        $this->db->where('u.id !=', $current_user_id);
        $this->db->order_by('u.name', 'ASC');
        $teachers = $this->db->get()->result_array();
        $users = array_merge($users, $teachers);

        // 3. Récupérer les admins et superadmins
        $this->db->select('DISTINCT(u.id), u.name, u.role as type, u.role');
        $this->db->from('users u');
        $this->db->where('u.school_id', $school_id);
        $this->db->where('u.status', 1);
        $this->db->where_in('u.role', ['admin', 'superadmin']);
        $this->db->order_by('u.name', 'ASC');
        $admins = $this->db->get()->result_array();
        $users = array_merge($users, $admins);

        // Éliminer les doublons basés sur l'ID
        $unique_users = [];
        $seen_ids = [];
        foreach ($users as $user) {
            if (!in_array($user['id'], $seen_ids)) {
                $seen_ids[] = $user['id'];
                $unique_users[] = $user;
            }
        }

        // Trier les utilisateurs par nom
        usort($unique_users, function($a, $b) {
            return strcmp($a['name'], $b['name']);
        });

        echo json_encode([
            'status' => 'success',
            'classes' => $classes,
            'users' => $unique_users,
            'csrf' => [
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash()
            ]
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Erreur lors de la récupération des données',
            'csrf' => [
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash()
            ]
        ]);
    }
}

    public function check_teacher_email()
    {
        $email = $this->input->post('email');
        $school_id = school_id();

        $this->db->where('email', $email);
        $user = $this->db->get('users')->row_array();

        if (!$user) {
            echo json_encode(['status' => 'new']);
            return;
        }

        $this->db->where('user_id', $user['id']);
        $this->db->where('school_id', $school_id);
        $this->db->where('role', 'teacher');
        $existing_teacher = $this->db->get('user_schools')->row();

        if ($existing_teacher) {
            echo json_encode([
                'status' => 'exists_in_school',
                'message' => get_phrase("this_email_already_exists_as_teacher_in_this_community")
            ]);
            return;
        }

        echo json_encode([
            'status' => 'exists',
            'user' => [
                'id' => $user['id'],
                'name' => html_entity_decode($user['name']),
                'email' => $user['email']
            ]
        ]);
    }
	
}
