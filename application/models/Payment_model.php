<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
*  @author   : Creativeitem
*  date      : November, 2019
*  Ekattor School Management System With Addons
*  http://codecanyon.net/user/Creativeitem
*  http://support.creativeitem.com
*/

class Payment_model extends CI_Model {

    function __construct()
    {
        parent::__construct();
        /*cache control*/
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
    }

    public function stripe_payment($token_id = "", $invoice_id = "", $amount_paid = "", $stripe_secret_key = "") {
        // ========== SÉCURITÉ: Validation des entrées ==========
        if (empty($token_id) || empty($invoice_id) || empty($amount_paid) || empty($stripe_secret_key)) {
            log_message('error', 'Stripe: Paramètres manquants pour le paiement');
            return false;
        }
        
        $invoice_details = $this->crud_model->get_invoice_by_id($invoice_id);
        
        if (empty($invoice_details)) {
            log_message('error', "Stripe: Facture #{$invoice_id} introuvable");
            return false;
        }
        
        $stripe_settings = json_decode(get_payment_settings('stripe_settings', $invoice_details['school_id']));
        $stripe_currency = $stripe_settings[0]->stripe_currency ?? 'USD';
        
        $user_id = $this->session->userdata('user_id'); 
        $user = $this->db->get_where('users', ['id' => $user_id])->row_array();

        if (!is_array($user) || !isset($user['email'])) {
            log_message('error', 'Stripe: Détails utilisateur invalides');
            return false;
        }

        require_once(APPPATH.'libraries/Stripe/init.php');
        
        try {
            \Stripe\Stripe::setApiKey($stripe_secret_key);
            
            // Créer le client Stripe
            $customer = \Stripe\Customer::create([
                'email' => $user['email'],
                'source' => $token_id
            ]);
            
            // Créer la charge (montant en centimes)
            $charge = \Stripe\Charge::create([
                'customer' => $customer->id, 
                'amount' => intval($amount_paid * 100), 
                'currency' => $stripe_currency, 
                'receipt_email' => $user['email'],
                'description' => 'Paiement facture #' . $invoice_id,
                'metadata' => [
                    'invoice_id' => $invoice_id
                ]
            ]);
            
            if ($charge->status === 'succeeded') {
                log_message('info', "Stripe: Paiement réussi pour facture #{$invoice_id}, charge_id: {$charge->id}");
                return true;
            } else {
                log_message('error', "Stripe: Statut de charge inattendu: {$charge->status}");
                return false;
            }
            
        } catch (\Stripe\Exception\CardException $e) {
            // Erreur de carte (carte refusée, etc.)
            log_message('error', 'Stripe CardException: ' . $e->getMessage());
            $this->session->set_flashdata('error_message', get_phrase('card_declined'));
            return false;
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            // Requête invalide
            log_message('error', 'Stripe InvalidRequestException: ' . $e->getMessage());
            return false;
        } catch (\Stripe\Exception\AuthenticationException $e) {
            // Erreur d'authentification API
            log_message('error', 'Stripe AuthenticationException: ' . $e->getMessage());
            return false;
        } catch (\Stripe\Exception\ApiConnectionException $e) {
            // Erreur de connexion réseau
            log_message('error', 'Stripe ApiConnectionException: ' . $e->getMessage());
            return false;
        } catch (\Stripe\Exception\ApiErrorException $e) {
            // Erreur API générique
            log_message('error', 'Stripe ApiErrorException: ' . $e->getMessage());
            return false;
        } catch (Exception $e) {
            // Autres erreurs
            log_message('error', 'Stripe Exception: ' . $e->getMessage());
            return false;
        }
    }
}
