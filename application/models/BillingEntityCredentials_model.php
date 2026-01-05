<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * BillingEntityCredentials_model
 * Gestion des clés API PSP par entité de facturation
 */
class BillingEntityCredentials_model extends CI_Model
{
    const TABLE = 'billing_entity_credentials';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Récupérer les credentials d'une entité pour un provider
     */
    public function get_credentials($entity_id, $provider)
    {
        return $this->db->get_where(self::TABLE, [
            'billing_entity_id' => $entity_id,
            'provider' => strtolower($provider)
        ])->row_array();
    }

    /**
     * Récupérer tous les credentials d'une entité
     */
    public function get_all_for_entity($entity_id)
    {
        return $this->db->get_where(self::TABLE, [
            'billing_entity_id' => $entity_id
        ])->result_array();
    }

    /**
     * Récupérer les credentials actifs d'une entité
     */
    public function get_active_for_entity($entity_id)
    {
        return $this->db->get_where(self::TABLE, [
            'billing_entity_id' => $entity_id,
            'is_active' => 1
        ])->result_array();
    }

    /**
     * Récupérer les credentials Stripe pour une entité
     */
    public function get_stripe_credentials($entity_id)
    {
        $creds = $this->get_credentials($entity_id, 'stripe');
        
        if (!$creds || !$creds['is_active']) {
            return null;
        }

        $is_live = ($creds['mode'] === 'live');
        
        return [
            'mode' => $creds['mode'],
            'public_key' => $is_live ? $creds['live_public_key'] : $creds['test_public_key'],
            'secret_key' => $is_live ? $creds['live_secret_key'] : $creds['test_secret_key'],
            'currency' => $creds['currency'] ?? 'USD',
            'webhook_secret' => $creds['webhook_secret'],
            'account_name' => $creds['account_name']
        ];
    }

    /**
     * Récupérer les credentials PayPal pour une entité
     */
    public function get_paypal_credentials($entity_id)
    {
        $creds = $this->get_credentials($entity_id, 'paypal');
        
        if (!$creds || !$creds['is_active']) {
            return null;
        }

        $is_production = in_array($creds['mode'], ['live', 'production']);
        
        return [
            'mode' => $is_production ? 'production' : 'sandbox',
            'client_id' => $is_production ? $creds['production_client_id'] : $creds['sandbox_client_id'],
            'secret' => $is_production ? $creds['production_secret'] : $creds['sandbox_secret'],
            'currency' => $creds['currency'] ?? 'USD',
            'account_name' => $creds['account_name']
        ];
    }

    /**
     * Créer ou mettre à jour les credentials
     */
    public function save_credentials($entity_id, $provider, $data)
    {
        $provider = strtolower($provider);
        
        // Vérifier si existe déjà
        $existing = $this->get_credentials($entity_id, $provider);
        
        $save_data = [
            'billing_entity_id' => $entity_id,
            'provider' => $provider,
            'mode' => $data['mode'] ?? 'test',
            'is_active' => isset($data['is_active']) ? (int)$data['is_active'] : 1,
            'currency' => $data['currency'] ?? null,
            'account_name' => $data['account_name'] ?? null,
            'account_email' => $data['account_email'] ?? null,
            'notes' => $data['notes'] ?? null
        ];

        // Stripe fields
        if ($provider === 'stripe') {
            $save_data['test_public_key'] = $data['test_public_key'] ?? null;
            $save_data['test_secret_key'] = $data['test_secret_key'] ?? null;
            $save_data['live_public_key'] = $data['live_public_key'] ?? null;
            $save_data['live_secret_key'] = $data['live_secret_key'] ?? null;
            $save_data['webhook_secret'] = $data['webhook_secret'] ?? null;
        }

        // PayPal fields
        if ($provider === 'paypal') {
            $save_data['sandbox_client_id'] = $data['sandbox_client_id'] ?? null;
            $save_data['sandbox_secret'] = $data['sandbox_secret'] ?? null;
            $save_data['production_client_id'] = $data['production_client_id'] ?? null;
            $save_data['production_secret'] = $data['production_secret'] ?? null;
        }

        if ($existing) {
            $this->db->where('id', $existing['id']);
            return $this->db->update(self::TABLE, $save_data);
        } else {
            return $this->db->insert(self::TABLE, $save_data);
        }
    }

    /**
     * Supprimer les credentials
     */
    public function delete_credentials($entity_id, $provider)
    {
        return $this->db->delete(self::TABLE, [
            'billing_entity_id' => $entity_id,
            'provider' => strtolower($provider)
        ]);
    }

    /**
     * Activer/désactiver les credentials
     */
    public function toggle_active($entity_id, $provider, $is_active)
    {
        $this->db->where('billing_entity_id', $entity_id);
        $this->db->where('provider', strtolower($provider));
        return $this->db->update(self::TABLE, ['is_active' => (int)$is_active]);
    }

    /**
     * Récupérer les credentials par Tax Residence
     * Utilisé par le Payment Gateway
     */
    public function get_credentials_for_tax_residence($tax_residence, $provider)
    {
        $tax_residence = strtoupper(trim($tax_residence));
        
        // Normaliser AE -> UAE
        if ($tax_residence === 'AE') {
            $tax_residence = 'UAE';
        }

        // Trouver l'entité
        $this->load->model('BillingEntity_model', 'billing_entity_model');
        $entity = $this->billing_entity_model->get_by_tax_residence($tax_residence);

        if (!$entity) {
            // Fallback: entité par défaut
            $entity = $this->billing_entity_model->get_default();
        }

        if (!$entity) {
            return null;
        }

        if ($provider === 'stripe') {
            return $this->get_stripe_credentials($entity['id']);
        } elseif ($provider === 'paypal') {
            return $this->get_paypal_credentials($entity['id']);
        }

        return $this->get_credentials($entity['id'], $provider);
    }

    /**
     * Formater pour l'affichage (masquer les clés secrètes)
     */
    public function format_for_display($credentials)
    {
        if (!$credentials) return null;

        $masked = $credentials;
        
        // Masquer les clés secrètes
        $secret_fields = [
            'test_secret_key', 'live_secret_key', 
            'sandbox_secret', 'production_secret',
            'webhook_secret'
        ];

        foreach ($secret_fields as $field) {
            if (!empty($masked[$field])) {
                $masked[$field] = substr($masked[$field], 0, 8) . '••••••••' . substr($masked[$field], -4);
            }
        }

        return $masked;
    }

    /**
     * Obtenir les credentials de paiement pour une communauté
     * Utilise le tax_residence pour déterminer l'entité et ses credentials
     * Fallback vers payment_settings si pas de credentials d'entité
     * 
     * @param int $school_id ID de la communauté
     * @param string $provider stripe ou paypal
     * @return array Credentials formatés pour utilisation directe
     */
    public function get_payment_credentials_for_school($school_id, $provider)
    {
        // 1. Récupérer le country de la communauté (source unique de vérité)
        $school = $this->db->get_where('schools', ['id' => $school_id])->row_array();
        $country = !empty($school['country']) ? $school['country'] : null;
        
        // 2. Si country existe, chercher les credentials de l'entité
        if ($country) {
            $entity_creds = $this->get_credentials_for_tax_residence($country, $provider);
            if ($entity_creds && $this->_has_valid_keys($entity_creds, $provider)) {
                log_message('debug', "Using entity credentials for school {$school_id} (country: {$country}, provider: {$provider})");
                return $this->_format_as_legacy_settings($entity_creds, $provider);
            }
        }
        
        // 3. Fallback: utiliser payment_settings classiques
        log_message('debug', "Falling back to payment_settings for school {$school_id}, provider: {$provider}");
        return $this->_get_legacy_settings($school_id, $provider);
    }

    /**
     * Vérifier si les credentials contiennent des clés valides
     */
    private function _has_valid_keys($creds, $provider)
    {
        if ($provider === 'stripe') {
            return !empty($creds['public_key']) && !empty($creds['secret_key']);
        }
        if ($provider === 'paypal') {
            return !empty($creds['client_id']);
        }
        return false;
    }

    /**
     * Formater les credentials d'entité comme les anciens settings
     * Pour compatibilité avec le code existant
     */
    private function _format_as_legacy_settings($creds, $provider)
    {
        if ($provider === 'stripe') {
            $is_live = ($creds['mode'] === 'live');
            return [
                'active' => 'yes',
                'mode' => $is_live ? 'off' : 'on', // 'on' = test mode dans le vieux format
                'currency' => $creds['currency'] ?? 'USD',
                'public_key' => $creds['public_key'],
                'secret_key' => $creds['secret_key'],
                'webhook_secret' => $creds['webhook_secret'] ?? '',
                'source' => 'billing_entity',
                'account_name' => $creds['account_name'] ?? ''
            ];
        }
        
        if ($provider === 'paypal') {
            return [
                'active' => 'yes',
                'mode' => $creds['mode'] ?? 'sandbox',
                'currency' => $creds['currency'] ?? 'USD',
                'client_id' => $creds['client_id'],
                'secret' => $creds['secret'] ?? '',
                'source' => 'billing_entity',
                'account_name' => $creds['account_name'] ?? ''
            ];
        }
        
        return [];
    }

    /**
     * Récupérer les settings legacy depuis payment_settings
     */
    private function _get_legacy_settings($school_id, $provider)
    {
        $key = $provider . '_settings';
        $row = $this->db->get_where('payment_settings', [
            'school_id' => $school_id,
            'key' => $key
        ])->row();
        
        if (!$row || empty($row->value)) {
            return null;
        }
        
        $settings = json_decode($row->value);
        if (!$settings || !isset($settings[0])) {
            return null;
        }
        
        $s = $settings[0];
        
        if ($provider === 'stripe') {
            $is_test = ($s->stripe_mode ?? 'on') === 'on';
            return [
                'active' => $s->stripe_active ?? 'no',
                'mode' => $s->stripe_mode ?? 'on',
                'currency' => $s->stripe_currency ?? 'USD',
                'public_key' => $is_test ? ($s->stripe_test_public_key ?? '') : ($s->stripe_live_public_key ?? ''),
                'secret_key' => $is_test ? ($s->stripe_test_secret_key ?? '') : ($s->stripe_live_secret_key ?? ''),
                'source' => 'payment_settings'
            ];
        }
        
        if ($provider === 'paypal') {
            $is_production = ($s->paypal_mode ?? 'sandbox') === 'production';
            return [
                'active' => $s->paypal_active ?? 'no',
                'mode' => $s->paypal_mode ?? 'sandbox',
                'currency' => $s->paypal_currency ?? 'USD',
                'client_id' => $is_production ? ($s->paypal_client_id_production ?? '') : ($s->paypal_client_id_sandbox ?? ''),
                'source' => 'payment_settings'
            ];
        }
        
        return null;
    }

    /**
     * Obtenir toutes les infos de paiement pour le payment gateway view
     * Retourne un array prêt à être passé à la vue
     */
    public function get_gateway_page_data($school_id)
    {
        $stripe = $this->get_payment_credentials_for_school($school_id, 'stripe');
        $paypal = $this->get_payment_credentials_for_school($school_id, 'paypal');
        
        $data = [];
        
        // Stripe data
        if ($stripe) {
            $data['stripe_public_key'] = $stripe['public_key'] ?? '';
            $data['stripe_private_key'] = $stripe['secret_key'] ?? '';
            $data['stripe_currency'] = $stripe['currency'] ?? 'USD';
            $data['stripe_enabled'] = !empty($stripe['public_key']) && !empty($stripe['secret_key']);
            $data['stripe_source'] = $stripe['source'] ?? 'unknown';
        } else {
            $data['stripe_public_key'] = '';
            $data['stripe_private_key'] = '';
            $data['stripe_currency'] = 'USD';
            $data['stripe_enabled'] = false;
        }
        
        // PayPal data
        if ($paypal) {
            $is_production = ($paypal['mode'] ?? 'sandbox') === 'production';
            $data['paypal_mode'] = $paypal['mode'] ?? 'sandbox';
            $data['paypal_client_id_sandbox'] = $is_production ? '' : ($paypal['client_id'] ?? '');
            $data['paypal_client_id_production'] = $is_production ? ($paypal['client_id'] ?? '') : '';
            $data['paypal_currency'] = $paypal['currency'] ?? 'USD';
            $data['paypal_enabled'] = !empty($paypal['client_id']);
            $data['paypal_source'] = $paypal['source'] ?? 'unknown';
        } else {
            $data['paypal_mode'] = 'sandbox';
            $data['paypal_client_id_sandbox'] = '';
            $data['paypal_client_id_production'] = '';
            $data['paypal_currency'] = 'USD';
            $data['paypal_enabled'] = false;
        }
        
        return $data;
    }
}

