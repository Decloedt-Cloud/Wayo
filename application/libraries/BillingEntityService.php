<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * BillingEntity Service
 * 
 * Service pour gérer dynamiquement les entités de facturation WAYO
 * Utilisé dans l'interface de paiement et l'administration
 * 
 * @author Senior PHP Engineer
 * @version 1.0.0
 */
class BillingEntityService {

    protected $CI;
    protected $model_loaded = false;

    /**
     * Entités de fallback si la base de données n'est pas configurée
     */
    const FALLBACK_ENTITIES = [
        'MA' => [
            'id' => 1,
            'code' => 'MA',
            'name' => 'Decloedt SARL',
            'legal_name' => 'Decloedt SARL - Société à Responsabilité Limitée',
            'country_code' => 'MA',
            'country_name' => 'Morocco',
            'country_flag' => '🇲🇦',
            'vat_rate' => 20.00,
            'currency_code' => 'MAD',
            'currency_symbol' => 'DH',
            'bank_name' => 'Banque Populaire',
            'bank_country' => 'Morocco',
            'psp_name' => 'Stripe', // CashPlus temporairement désactivé
            'psp_type' => 'international',
            'color_primary' => '#c62828',
            'color_secondary' => '#e53935',
            'is_default' => true,
            'is_active' => true
        ],
        'UAE' => [
            'id' => 2,
            'code' => 'UAE',
            'name' => 'Bouhouti',
            'legal_name' => 'Bouhouti LLC',
            'country_code' => 'AE',
            'country_name' => 'United Arab Emirates',
            'country_flag' => '🇦🇪',
            'vat_rate' => 5.00,
            'currency_code' => 'AED',
            'currency_symbol' => 'AED',
            'bank_name' => 'Emirates NBD',
            'bank_country' => 'United Arab Emirates',
            'psp_name' => 'Stripe',
            'psp_type' => 'international',
            'color_primary' => '#00695c',
            'color_secondary' => '#00897b',
            'is_default' => false,
            'is_active' => true
        ]
    ];

    public function __construct()
    {
        $this->CI =& get_instance();
    }

    /**
     * Charger le modèle si nécessaire
     */
    private function load_model()
    {
        if (!$this->model_loaded) {
            try {
                $this->CI->load->model('BillingEntity_model', 'billing_entity_model');
                $this->model_loaded = true;
            } catch (Exception $e) {
                log_message('error', 'BillingEntityService: Failed to load model - ' . $e->getMessage());
                $this->model_loaded = false;
            }
        }
    }

    /**
     * Vérifier si le système de facturation dynamique est disponible
     * 
     * @return bool
     */
    public function is_available()
    {
        $this->load_model();
        
        if (!$this->model_loaded) {
            return false;
        }
        
        try {
            return $this->CI->billing_entity_model->tables_exist();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Récupérer l'entité de facturation pour un Tax_residence donné
     * 
     * @param string $tax_residence Code Tax_residence (MA, UAE, AE, etc.)
     * @return array Entité de facturation
     */
    public function get_entity_for_tax_residence($tax_residence)
    {
        $tax_residence = strtoupper(trim($tax_residence));
        
        // Normaliser AE -> UAE
        if ($tax_residence === 'AE') {
            $tax_residence = 'UAE';
        }
        
        // Essayer la base de données
        if ($this->is_available()) {
            try {
                $entity = $this->CI->billing_entity_model->get_by_tax_residence($tax_residence);
                if ($entity) {
                    return $this->CI->billing_entity_model->format_for_ui($entity);
                }
            } catch (Exception $e) {
                log_message('error', 'BillingEntityService: Error getting entity - ' . $e->getMessage());
            }
        }
        
        // Fallback statique
        return $this->get_fallback_entity($tax_residence);
    }

    /**
     * Récupérer toutes les entités pour l'affichage
     * 
     * @return array Liste des entités formatées
     */
    public function get_all_entities()
    {
        if ($this->is_available()) {
            try {
                $entities = $this->CI->billing_entity_model->get_all();
                $formatted = [];
                foreach ($entities as $entity) {
                    $formatted[] = $this->CI->billing_entity_model->format_for_ui($entity);
                }
                return $formatted;
            } catch (Exception $e) {
                log_message('error', 'BillingEntityService: Error getting all entities - ' . $e->getMessage());
            }
        }
        
        // Fallback
        return array_values(self::FALLBACK_ENTITIES);
    }

    /**
     * Récupérer toutes les entités actives
     * 
     * @return array Liste des entités actives
     */
    public function get_all_active()
    {
        if ($this->is_available()) {
            try {
                $this->CI->db->where('is_active', 1);
                $this->CI->db->order_by('display_order', 'ASC');
                $entities = $this->CI->db->get('billing_entities')->result_array();
                
                // Décoder le JSON applies_to
                foreach ($entities as &$entity) {
                    if (isset($entity['applies_to']) && is_string($entity['applies_to'])) {
                        $entity['applies_to'] = json_decode($entity['applies_to'], true);
                    }
                }
                
                return $entities;
            } catch (Exception $e) {
                log_message('error', 'BillingEntityService: Error getting active entities - ' . $e->getMessage());
            }
        }
        
        // Fallback - retourner les entités statiques "actives"
        return array_values(self::FALLBACK_ENTITIES);
    }

    /**
     * Récupérer les données pour l'affichage dans payment_gateway
     * 
     * @param string $tax_residence Code Tax_residence actuel
     * @return array Données complètes pour l'UI
     */
    public function get_payment_gateway_data($tax_residence)
    {
        $tax_residence = strtoupper(trim($tax_residence));
        
        // Normaliser AE -> UAE pour comparaison
        $normalized_residence = ($tax_residence === 'AE') ? 'UAE' : $tax_residence;
        
        // Récupérer toutes les entités
        $all_entities = $this->get_all_entities();
        
        // Récupérer l'entité active
        $active_entity = $this->get_entity_for_tax_residence($tax_residence);
        
        // Préparer les données pour chaque entité (active/inactive)
        $entities_display = [];
        foreach ($all_entities as $entity) {
            $is_active = ($entity['code'] === $normalized_residence) || 
                         ($entity['code'] === 'UAE' && $tax_residence === 'AE');
            
            $entities_display[] = [
                'entity' => $entity,
                'is_active' => $is_active,
                'css_class' => $is_active ? 'active' : 'inactive'
            ];
        }
        
        return [
            'active_entity' => $active_entity,
            'all_entities' => $entities_display,
            'tax_residence' => $tax_residence,
            'normalized_residence' => $normalized_residence,
            'has_vat' => ($active_entity && $active_entity['vat_rate'] > 0),
            'vat_rate' => $active_entity ? $active_entity['vat_rate'] : 0,
            'currency' => $active_entity ? $active_entity['currency_code'] : 'MAD',
            'legal_entity_name' => $active_entity ? $active_entity['name'] : 'Unknown',
            'legal_entity_full' => $active_entity ? $active_entity['legal_name'] : 'Unknown',
            'psp_info' => $active_entity ? $active_entity['psp_display'] : 'N/A'
        ];
    }

    /**
     * Récupérer une entité de fallback statique
     * 
     * @param string $code Code de l'entité
     * @return array Entité fallback
     */
    private function get_fallback_entity($code)
    {
        $code = strtoupper($code);
        
        // Normaliser AE -> UAE
        if ($code === 'AE') {
            $code = 'UAE';
        }
        
        if (isset(self::FALLBACK_ENTITIES[$code])) {
            return self::FALLBACK_ENTITIES[$code];
        }
        
        // Retourner MA par défaut
        return self::FALLBACK_ENTITIES['MA'];
    }

    /**
     * Créer une nouvelle entité de facturation (pour Superadmin)
     * 
     * @param array $data Données de l'entité
     * @return array Résultat de l'opération
     */
    public function create_entity($data)
    {
        if (!$this->is_available()) {
            return [
                'success' => false,
                'message' => 'Billing entities table not available'
            ];
        }
        
        try {
            $entity_id = $this->CI->billing_entity_model->create($data);
            
            if ($entity_id) {
                return [
                    'success' => true,
                    'message' => 'Entity created successfully',
                    'entity_id' => $entity_id
                ];
            }
            
            return [
                'success' => false,
                'message' => 'Failed to create entity'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Mettre à jour une entité de facturation (pour Superadmin)
     * 
     * @param int $id ID de l'entité
     * @param array $data Nouvelles données
     * @return array Résultat de l'opération
     */
    public function update_entity($id, $data)
    {
        if (!$this->is_available()) {
            return [
                'success' => false,
                'message' => 'Billing entities table not available'
            ];
        }
        
        try {
            $result = $this->CI->billing_entity_model->update($id, $data);
            
            return [
                'success' => $result,
                'message' => $result ? 'Entity updated successfully' : 'Failed to update entity'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Supprimer une entité de facturation (pour Superadmin)
     * 
     * @param int $id ID de l'entité
     * @return array Résultat de l'opération
     */
    public function delete_entity($id)
    {
        if (!$this->is_available()) {
            return [
                'success' => false,
                'message' => 'Billing entities table not available'
            ];
        }
        
        try {
            $result = $this->CI->billing_entity_model->delete($id);
            
            return [
                'success' => $result,
                'message' => $result ? 'Entity deleted successfully' : 'Failed to delete entity'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Récupérer les entités pour l'administration (Superadmin)
     * 
     * @return array Liste des entités avec mappings
     */
    public function get_for_admin()
    {
        if (!$this->is_available()) {
            // Retourner les fallback avec format admin
            $entities = [];
            foreach (self::FALLBACK_ENTITIES as $code => $entity) {
                $entity['mappings'] = [$code];
                $entity['is_fallback'] = true;
                $entities[] = $entity;
            }
            return $entities;
        }
        
        try {
            return $this->CI->billing_entity_model->get_for_admin();
        } catch (Exception $e) {
            log_message('error', 'BillingEntityService: Error getting admin data - ' . $e->getMessage());
            return [];
        }
    }
}

