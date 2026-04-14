<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentMethod_model extends Model {
    protected $table            = 'payment_methods';
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

    const LINK_TABLE = 'billing_entity_payment_methods';

    // Définition statique des moyens de paiement connus
    // (pas besoin de table, ces infos sont constantes)
    const KNOWN_METHODS = [
        'stripe' => [
            'code' => 'stripe',
            'name' => 'Stripe',
            'display_name' => 'Carte bancaire (Stripe)',
            'type' => 'card',
            'icon' => 'fab fa-cc-stripe',
            'color' => '#635bff',
            'settings_key' => 'stripe_settings',
            'provider_type' => 'international'
        ],
        'paypal' => [
            'code' => 'paypal',
            'name' => 'PayPal',
            'display_name' => 'PayPal',
            'type' => 'wallet',
            'icon' => 'fab fa-paypal',
            'color' => '#003087',
            'settings_key' => 'paypal_settings',
            'provider_type' => 'international'
        ],
        // TEMPORAIREMENT DÉSACTIVÉ - CashPlus
        // 'cashplus' => [
        //     'code' => 'cashplus',
        //     'name' => 'CashPlus',
        //     'display_name' => 'CashPlus (Maroc)',
        //     'type' => 'cash',
        //     'icon' => 'fas fa-money-bill-wave',
        //     'color' => '#e63946',
        //     'settings_key' => 'cashplus_settings',
        //     'provider_type' => 'local'
        // ],
        'cmi' => [
            'code' => 'cmi',
            'name' => 'CMI',
            'display_name' => 'CMI (Maroc)',
            'type' => 'card',
            'icon' => 'fas fa-credit-card',
            'color' => '#1a5f7a',
            'settings_key' => 'cmi_settings',
            'provider_type' => 'regional'
        ],
        'bank_transfer' => [
            'code' => 'bank_transfer',
            'name' => 'Virement',
            'display_name' => 'Virement bancaire',
            'type' => 'bank',
            'icon' => 'fas fa-university',
            'color' => '#2d3436',
            'settings_key' => null, // Pas de config nécessaire
            'provider_type' => 'international'
        ]
    ];

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Récupérer tous les moyens de paiement connus
     */
    public function get_all_known()
    {
        return self::KNOWN_METHODS;
    }

    /**
     * Récupérer un moyen par son code
     */
    public function get_by_code($code)
    {
        $code = strtolower(trim($code));
        return self::KNOWN_METHODS[$code] ?? null;
    }

    // =====================================================
    // GESTION DES LIAISONS ENTITÉ ↔ MÉTHODES
    // =====================================================

    /**
     * Récupérer les méthodes autorisées pour une entité
     */
    public function get_for_entity($entity_id, $active_only = true)
    {
        $builder = \db()->table(self::LINK_TABLE)->where('billing_entity_id', $entity_id);
        if ($active_only) {
            $builder->where('is_active', 1);
        }
        $links = $builder->orderBy('priority', 'ASC')->get()->getResultArray();

        $methods = [];
        foreach ($links as $link) {
            $method = $this->get_by_code($link['method_code']);
            if ($method) {
                $method['is_default'] = (bool)$link['is_default'];
                $method['priority'] = $link['priority'];
                $methods[] = $method;
            }
        }

        return $methods;
    }

    /**
     * Récupérer les méthodes par Tax Residence
     */
    public function get_for_tax_residence($tax_residence)
    {
        $tax_residence = strtoupper(trim($tax_residence));

        // Normaliser AE -> UAE
        if ($tax_residence === 'AE') {
            $tax_residence = 'UAE';
        }

        // Chercher l'entité correspondante
        $billingEntityModel = new \App\Models\BillingEntity_model();
        $entity = $billingEntityModel->get_by_tax_residence($tax_residence);

        if ($entity) {
            return $this->get_for_entity($entity['id']);
        }

        // Fallback: retourner Stripe et PayPal (internationaux)
        return [
            self::KNOWN_METHODS['stripe'],
            self::KNOWN_METHODS['paypal']
        ];
    }

    /**
     * Lier une méthode à une entité
     */
    public function link_to_entity($entity_id, $method_code, $options = [])
    {
        // Vérifier que le code est valide
        if (!isset(self::KNOWN_METHODS[$method_code])) {
            return false;
        }

        // Vérifier si le lien existe déjà
        $existing = \db()->table(self::LINK_TABLE)->where([
            'billing_entity_id' => $entity_id,
            'method_code' => $method_code
        ])->get()->getRow();

        if ($existing) {
            // Mettre à jour
            $update_data = [
                'is_active' => isset($options['is_active']) ? (int)$options['is_active'] : 1,
                'is_default' => isset($options['is_default']) ? (int)$options['is_default'] : 0,
                'priority' => $options['priority'] ?? 0
            ];
            return \db()->table(self::LINK_TABLE)->where('id', $existing->id)->update($update_data);
        }

        // Créer nouveau lien
        return \db()->table(self::LINK_TABLE)->insert([
            'billing_entity_id' => $entity_id,
            'method_code' => $method_code,
            'is_default' => isset($options['is_default']) ? (int)$options['is_default'] : 0,
            'is_active' => isset($options['is_active']) ? (int)$options['is_active'] : 1,
            'priority' => $options['priority'] ?? 0
        ]);
    }

    /**
     * Délier une méthode d'une entité
     */
    public function unlink_from_entity($entity_id, $method_code)
    {
        return \db()->table(self::LINK_TABLE)
            ->where('billing_entity_id', $entity_id)
            ->where('method_code', $method_code)
            ->delete();
    }

    /**
     * Mettre à jour les liaisons d'une entité (bulk)
     */
    public function update_entity_methods($entity_id, $method_codes)
    {
        // Supprimer les liaisons actuelles
        \db()->table(self::LINK_TABLE)->where('billing_entity_id', $entity_id)->delete();

        // Créer les nouvelles liaisons
        if (!empty($method_codes)) {
            $priority = 0;
            foreach ($method_codes as $code) {
                $this->link_to_entity($entity_id, $code, [
                    'priority' => $priority,
                    'is_default' => ($priority === 0) ? 1 : 0
                ]);
                $priority++;
            }
        }

        return true;
    }

    // =====================================================
    // INTÉGRATION AVEC payment_settings EXISTANT
    // =====================================================

    /**
     * Récupérer les méthodes disponibles pour une école
     * Filtre selon: entité + configurations payment_settings
     */
    public function get_available_for_school($tax_residence, $school_id)
    {
        // 1. Méthodes autorisées pour l'entité
        $entity_methods = $this->get_for_tax_residence($tax_residence);
        
        // 2. Configurations de l'école
        $school_settings = \db()->table('payment_settings')
            ->where('school_id', $school_id)
            ->get()
            ->getResultArray();
        $configured = [];
        
        foreach ($school_settings as $setting) {
            $key = $setting['key'];
            $value = json_decode($setting['value'], true);
            
            if ($key === 'stripe_settings' && !empty($value)) {
                if (isset($value['stripe_active']) && $value['stripe_active'] === 'yes') {
                    $configured['stripe'] = $value;
                }
            }
            
            if ($key === 'paypal_settings' && !empty($value)) {
                if (isset($value['paypal_active']) && $value['paypal_active'] === 'yes') {
                    $configured['paypal'] = $value;
                }
            }
            
            // Ajouter d'autres PSP ici si nécessaire
        }
        
        // 3. Filtrer: autorisé ET configuré
        $available = [];
        foreach ($entity_methods as $method) {
            $code = $method['code'];
            
            // Méthodes qui nécessitent config
            if (in_array($code, ['stripe', 'paypal'])) {
                if (isset($configured[$code])) {
                    $method['school_config'] = $configured[$code];
                    $method['is_configured'] = true;
                    $available[] = $method;
                }
            } else {
                // Méthodes sans config (virement, etc.)
                $method['is_configured'] = true;
                $available[] = $method;
            }
        }
        
        return $available;
    }

    /**
     * Vérifier si une méthode est disponible pour une école
     */
    public function is_method_available($method_code, $tax_residence, $school_id)
    {
        $available = $this->get_available_for_school($tax_residence, $school_id);
        
        foreach ($available as $method) {
            if ($method['code'] === $method_code) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Stats pour Superadmin
     */
    public function get_stats()
    {
        return [
            'total' => count(self::KNOWN_METHODS),
            'active' => count(self::KNOWN_METHODS),
            'by_provider' => [
                'international' => 3,
                'local' => 0, // CashPlus désactivé temporairement
                'regional' => 1
            ]
        ];
    }
}
