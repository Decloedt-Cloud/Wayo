<?php

namespace App\Models;

use CodeIgniter\Model;

class Payment_model extends Model {
    protected $table            = 'payments';
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

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Effectuer un paiement Stripe
     * 
     * @param string $token_id Token Stripe
     * @param string $invoice_id ID de la facture
     * @param string $amount_paid Montant à payer
     * @param string $stripe_secret_key Clé secrète Stripe
     * @param string|null $target_currency Devise à utiliser (si null, utilise la config BDD)
     * @return bool
     */
    public function stripe_payment($token_id = "", $invoice_id = "", $amount_paid = "", $stripe_secret_key = "", $target_currency = null) {
        // ========== SÉCURITÉ: Validation des entrées ==========
        if (empty($token_id) || empty($invoice_id) || empty($amount_paid) || empty($stripe_secret_key)) {
            log_message('error', 'Stripe: Paramètres manquants pour le paiement');
            return false;
        }
        
        $crudModel = model('App\Models\Crud_model');
        $invoice_details = $crudModel->get_invoice_by_id($invoice_id);
        
        if (empty($invoice_details)) {
            log_message('error', "Stripe: Facture #{$invoice_id} introuvable");
            return false;
        }
        
        // Utiliser la devise passée en paramètre OU lire depuis la config
        if (!empty($target_currency)) {
            $stripe_currency = strtoupper($target_currency);
            log_message('info', "Stripe: Devise forcée via paramètre: {$stripe_currency}");
        } else {
            $stripe_settings = json_decode(get_payment_settings('stripe_settings', $invoice_details['school_id']));
            $stripe_currency = $stripe_settings[0]->stripe_currency ?? 'USD';
            log_message('debug', "Stripe: Devise depuis config BDD: {$stripe_currency}");
        }
        
        $user_id = service('session')->get('user_id'); 
        $user = \db()->table('users')->where('id', $user_id)->get()->getRowArray();

        if (!is_array($user) || !isset($user['email'])) {
            log_message('error', 'Stripe: Détails utilisateur invalides');
            return false;
        }

        if (!class_exists(\Stripe\Stripe::class)) {
            $autoloadPath = ROOTPATH . 'vendor/autoload.php';
            if (file_exists($autoloadPath)) {
                require_once $autoloadPath;
            } else {
                log_message('error', 'Stripe: Composer autoload file not found');
                return false;
            }
        }
        
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
            log_message('error', 'Stripe CardException: ' . $e->getMessage());
            $msg = stripos($e->getMessage(), 'real card while testing') !== false
                ? 'Test mode is enabled. Please use a Stripe test card (e.g. 4242 4242 4242 4242).'
                : get_phrase('card_declined');
            service('session')->setFlashdata('error_message', $msg);
            return false;
        } catch (\Stripe\Error\Card $e) { // Legacy Stripe SDK (application/libraries/Stripe)
            log_message('error', 'Stripe Legacy Card Error: ' . $e->getMessage());
            $msg = stripos($e->getMessage(), 'real card while testing') !== false
                ? 'Test mode is enabled. Please use a Stripe test card (e.g. 4242 4242 4242 4242).'
                : get_phrase('card_declined');
            service('session')->setFlashdata('error_message', $msg);
            return false;
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            log_message('error', 'Stripe InvalidRequestException: ' . $e->getMessage());
            return false;
        } catch (\Stripe\Error\InvalidRequest $e) { // Legacy Stripe SDK
            log_message('error', 'Stripe Legacy InvalidRequest: ' . $e->getMessage());
            return false;
        } catch (\Stripe\Exception\AuthenticationException $e) {
            log_message('error', 'Stripe AuthenticationException: ' . $e->getMessage());
            return false;
        } catch (\Stripe\Error\Authentication $e) { // Legacy Stripe SDK
            log_message('error', 'Stripe Legacy Authentication: ' . $e->getMessage());
            return false;
        } catch (\Stripe\Exception\ApiConnectionException $e) {
            log_message('error', 'Stripe ApiConnectionException: ' . $e->getMessage());
            return false;
        } catch (\Stripe\Error\ApiConnection $e) { // Legacy Stripe SDK
            log_message('error', 'Stripe Legacy ApiConnection: ' . $e->getMessage());
            return false;
        } catch (\Stripe\Exception\ApiErrorException $e) {
            log_message('error', 'Stripe ApiErrorException: ' . $e->getMessage());
            return false;
        } catch (\Stripe\Error\Base $e) { // Legacy Stripe SDK generic API error
            log_message('error', 'Stripe Legacy ApiError: ' . $e->getMessage());
            return false;
        } catch (\Throwable $e) {
            log_message('error', 'Stripe Throwable: ' . $e->getMessage());
            return false;
        }
    }
}
