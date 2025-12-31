<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * BillingEntity Model
 * 
 * Gère les entités de facturation WAYO (Decloedt SARL, Bouhouti, etc.)
 * Permet une configuration dynamique depuis le Superadmin
 * 
 * @author Senior PHP Engineer
 * @version 1.0.0
 */
class BillingEntity_model extends CI_Model {

    const TABLE = 'billing_entities';
    const MAPPING_TABLE = 'billing_entity_mappings';

    /**
     * Cache des entités pour éviter les requêtes répétées
     */
    private $entities_cache = [];

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Récupérer toutes les entités de facturation actives
     * 
     * @param bool $active_only Filtrer uniquement les actives
     * @return array Liste des entités
     */
    public function get_all($active_only = true)
    {
        if ($active_only) {
            $this->db->where('is_active', 1);
        }
        $this->db->order_by('display_order', 'ASC');
        return $this->db->get(self::TABLE)->result_array();
    }

    /**
     * Récupérer une entité par son code
     * 
     * @param string $code Code de l'entité (MA, UAE, etc.)
     * @return array|null Entité ou null
     */
    public function get_by_code($code)
    {
        $code = strtoupper(trim($code));
        
        // Vérifier le cache
        if (isset($this->entities_cache[$code])) {
            return $this->entities_cache[$code];
        }
        
        $entity = $this->db->get_where(self::TABLE, [
            'code' => $code,
            'is_active' => 1
        ])->row_array();
        
        if ($entity) {
            // Parser le champ JSON applies_to
            $entity['applies_to'] = json_decode($entity['applies_to'] ?? '[]', true);
            $this->entities_cache[$code] = $entity;
        }
        
        return $entity;
    }

    /**
     * Récupérer une entité par son ID
     * 
     * @param int $id ID de l'entité
     * @return array|null
     */
    public function get_by_id($id)
    {
        $entity = $this->db->get_where(self::TABLE, ['id' => $id])->row_array();
        if ($entity) {
            $entity['applies_to'] = json_decode($entity['applies_to'] ?? '[]', true);
        }
        return $entity;
    }

    /**
     * Récupérer l'entité de facturation correspondant à un code pays
     * 
     * @param string $tax_residence Code pays ISO-2 depuis schools.country (MA, AE, etc.)
     * @return array|null Entité de facturation ou null
     */
    public function get_by_tax_residence($tax_residence)
    {
        $tax_residence = strtoupper(trim($tax_residence));
        
        // Vérifier le cache
        $cache_key = 'tax_' . $tax_residence;
        if (isset($this->entities_cache[$cache_key])) {
            return $this->entities_cache[$cache_key];
        }
        
        // Chercher dans la table de mapping
        $mapping = $this->db->get_where(self::MAPPING_TABLE, [
            'tax_residence_code' => $tax_residence,
            'is_active' => 1
        ])->row_array();
        
        if ($mapping) {
            $entity = $this->get_by_id($mapping['billing_entity_id']);
            if ($entity) {
                $this->entities_cache[$cache_key] = $entity;
                return $entity;
            }
        }
        
        // Fallback : chercher directement par code
        $entity = $this->get_by_code($tax_residence);
        if ($entity) {
            $this->entities_cache[$cache_key] = $entity;
            return $entity;
        }
        
        // Fallback UAE pour AE
        if ($tax_residence === 'AE') {
            return $this->get_by_code('UAE');
        }
        
        // Retourner l'entité par défaut
        return $this->get_default();
    }

    /**
     * Récupérer l'entité par défaut
     * 
     * @return array|null
     */
    public function get_default()
    {
        $entity = $this->db->get_where(self::TABLE, [
            'is_default' => 1,
            'is_active' => 1
        ])->row_array();
        
        if ($entity) {
            $entity['applies_to'] = json_decode($entity['applies_to'] ?? '[]', true);
        }
        
        return $entity;
    }

    /**
     * Créer une nouvelle entité de facturation
     * 
     * @param array $data Données de l'entité
     * @return int|false ID de l'entité créée ou false
     */
    public function create($data)
    {
        // Préparer les données
        $insert_data = $this->prepare_data($data);
        $insert_data['created_at'] = date('Y-m-d H:i:s');
        
        if ($this->db->insert(self::TABLE, $insert_data)) {
            $entity_id = $this->db->insert_id();
            
            // Créer le mapping si nécessaire
            if (!empty($data['code'])) {
                $this->create_mapping($data['code'], $entity_id);
            }
            
            // Vider le cache
            $this->entities_cache = [];
            
            return $entity_id;
        }
        
        return false;
    }

    /**
     * Mettre à jour une entité de facturation
     * 
     * @param int $id ID de l'entité
     * @param array $data Nouvelles données
     * @return bool Succès
     */
    public function update($id, $data)
    {
        $update_data = $this->prepare_data($data);
        $update_data['updated_at'] = date('Y-m-d H:i:s');
        
        $this->db->where('id', $id);
        $result = $this->db->update(self::TABLE, $update_data);
        
        // Vider le cache
        $this->entities_cache = [];
        
        return $result;
    }

    /**
     * Supprimer une entité de facturation
     * 
     * @param int $id ID de l'entité
     * @return bool Succès
     */
    public function delete($id)
    {
        // Supprimer les mappings associés
        $this->db->delete(self::MAPPING_TABLE, ['billing_entity_id' => $id]);
        
        // Supprimer l'entité
        $result = $this->db->delete(self::TABLE, ['id' => $id]);
        
        // Vider le cache
        $this->entities_cache = [];
        
        return $result;
    }

    /**
     * Définir une entité comme défaut
     * 
     * @param int $id ID de l'entité
     * @return bool Succès
     */
    public function set_default($id)
    {
        // Retirer le défaut des autres
        $this->db->update(self::TABLE, ['is_default' => 0]);
        
        // Définir la nouvelle entité par défaut
        $this->db->where('id', $id);
        $result = $this->db->update(self::TABLE, ['is_default' => 1]);
        
        // Vider le cache
        $this->entities_cache = [];
        
        return $result;
    }

    /**
     * Créer un mapping country code -> Entité de facturation
     * 
     * @param string $tax_residence_code Code pays ISO-2 (MA, AE, etc.)
     * @param int $billing_entity_id ID de l'entité
     * @param int $priority Priorité
     * @return bool Succès
     */
    public function create_mapping($tax_residence_code, $billing_entity_id, $priority = 100)
    {
        $data = [
            'tax_residence_code' => strtoupper($tax_residence_code),
            'billing_entity_id' => $billing_entity_id,
            'priority' => $priority,
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        // Upsert
        $existing = $this->db->get_where(self::MAPPING_TABLE, [
            'tax_residence_code' => $data['tax_residence_code']
        ])->row();
        
        if ($existing) {
            $this->db->where('id', $existing->id);
            return $this->db->update(self::MAPPING_TABLE, $data);
        }
        
        return $this->db->insert(self::MAPPING_TABLE, $data);
    }

    /**
     * Récupérer tous les mappings
     * 
     * @return array Liste des mappings
     */
    public function get_all_mappings()
    {
        $this->db->select('m.*, e.name as entity_name, e.code as entity_code');
        $this->db->from(self::MAPPING_TABLE . ' m');
        $this->db->join(self::TABLE . ' e', 'e.id = m.billing_entity_id', 'left');
        $this->db->order_by('m.priority', 'DESC');
        return $this->db->get()->result_array();
    }

    /**
     * Préparer les données pour insertion/mise à jour
     * 
     * @param array $data Données brutes
     * @return array Données préparées
     */
    private function prepare_data($data)
    {
        $prepared = [];
        
        $allowed_fields = [
            'code', 'name', 'legal_name', 'country_code', 'country_name', 'country_flag',
            'vat_rate', 'currency_code', 'currency_symbol', 'bank_name', 'bank_country',
            'psp_name', 'psp_type', 'address', 'vat_number', 'registration_number',
            'email', 'phone', 'is_default', 'is_active', 'applies_to', 'display_order',
            'color_primary', 'color_secondary', 'created_by'
        ];
        
        foreach ($allowed_fields as $field) {
            if (isset($data[$field])) {
                if ($field === 'applies_to' && is_array($data[$field])) {
                    $prepared[$field] = json_encode($data[$field]);
                } elseif ($field === 'code') {
                    $prepared[$field] = strtoupper(trim($data[$field]));
                } elseif ($field === 'vat_rate') {
                    $prepared[$field] = (float) $data[$field];
                } else {
                    $prepared[$field] = $data[$field];
                }
            }
        }
        
        return $prepared;
    }

    /**
     * Formater une entité pour l'affichage dans l'interface de paiement
     * 
     * @param array $entity Entité brute
     * @return array Entité formatée pour l'UI
     */
    public function format_for_ui($entity)
    {
        if (empty($entity)) {
            return null;
        }
        
        return [
            'id' => $entity['id'],
            'code' => $entity['code'],
            'name' => $entity['name'],
            'legal_name' => $entity['legal_name'],
            'country_code' => $entity['country_code'],
            'country_name' => $entity['country_name'],
            'country_flag' => $entity['country_flag'],
            'vat_rate' => (float) $entity['vat_rate'],
            'vat_rate_display' => $entity['vat_rate'] . '%',
            'currency_code' => $entity['currency_code'],
            'currency_symbol' => $entity['currency_symbol'],
            'bank_name' => $entity['bank_name'],
            'bank_country' => $entity['bank_country'],
            'psp_name' => $entity['psp_name'],
            'psp_type' => $entity['psp_type'],
            'psp_display' => $entity['psp_name'] . ' (' . strtoupper($entity['psp_type']) . ')',
            'address' => $entity['address'],
            'color_primary' => $entity['color_primary'] ?? '#1a237e',
            'color_secondary' => $entity['color_secondary'] ?? '#3949ab',
            'is_default' => (bool) $entity['is_default'],
            'is_active' => (bool) $entity['is_active']
        ];
    }

    /**
     * Vérifier si les tables existent
     * 
     * @return bool True si les tables existent
     */
    public function tables_exist()
    {
        return $this->db->table_exists(self::TABLE) && $this->db->table_exists(self::MAPPING_TABLE);
    }

    /**
     * Obtenir les entités avec leurs mappings pour l'administration
     * 
     * @return array Liste complète pour l'admin
     */
    public function get_for_admin()
    {
        $entities = $this->get_all(false); // Toutes, même inactives
        
        foreach ($entities as &$entity) {
            $entity['applies_to'] = json_decode($entity['applies_to'] ?? '[]', true);
            
            // Récupérer les mappings associés
            $mappings = $this->db->get_where(self::MAPPING_TABLE, [
                'billing_entity_id' => $entity['id']
            ])->result_array();
            
            $entity['mappings'] = array_column($mappings, 'tax_residence_code');
        }
        
        return $entities;
    }
}

