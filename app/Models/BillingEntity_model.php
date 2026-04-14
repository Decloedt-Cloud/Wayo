<?php

namespace App\Models;

use CodeIgniter\Model;

class BillingEntity_model extends Model {
    protected $table            = 'billing_entities';
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

    const TABLE = 'billing_entities';
    const MAPPING_TABLE = 'billing_entity_mappings';

    /**
     * Cache des entités pour éviter les requêtes répétées
     */
    private $entities_cache = [];

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Récupérer toutes les entités de facturation actives
     * 
     * @param bool $active_only Filtrer uniquement les actives
     * @return array Liste des entités
     */
    public function get_all($active_only = true)
    {
        $builder = \db()->table(self::TABLE);
        if ($active_only) {
            $builder->where('is_active', 1);
        }
        return $builder->orderBy('display_order', 'ASC')->get()->getResultArray();
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
        
        $entity = \db()->table(self::TABLE)->where([
            'code' => $code,
            'is_active' => 1
        ])->get()->getRowArray();
        
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
        $entity = \db()->table(self::TABLE)->where('id', $id)->get()->getRowArray();
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
        $mapping = \db()->table(self::MAPPING_TABLE)->where([
            'tax_residence_code' => $tax_residence,
            'is_active' => 1
        ])->get()->getRowArray();
        
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
        $entity = \db()->table(self::TABLE)->where([
            'is_default' => 1,
            'is_active' => 1
        ])->get()->getRowArray();
        
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
        
        if (\db()->table(self::TABLE)->insert($insert_data)) {
            $entity_id = \db()->insertID();
            
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
    public function update_table($id, $data)
    {
        $update_data = $this->prepare_data($data);
        $update_data['updated_at'] = date('Y-m-d H:i:s');
        
        $result = \db()->table(self::TABLE)->where('id', $id)->update($update_data);
        
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
    public function delete_table($id)
    {
        // Supprimer les mappings associés
        \db()->table(self::MAPPING_TABLE)->where('billing_entity_id', $id)->delete();
        
        // Supprimer l'entité
        $result = \db()->table(self::TABLE)->where('id', $id)->delete();
        
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
        \db()->table(self::TABLE)->update(['is_default' => 0]);
        
        // Définir la nouvelle entité par défaut
        $result = \db()->table(self::TABLE)->where('id', $id)->update(['is_default' => 1]);
        
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
        $existing = \db()->table(self::MAPPING_TABLE)->where('tax_residence_code', $data['tax_residence_code'])->get()->getRow();
        
        if ($existing) {
            return \db()->table(self::MAPPING_TABLE)->where('id', $existing->id)->update($data);
        }
        
        return \db()->table(self::MAPPING_TABLE)->insert($data);
    }

    /**
     * Récupérer tous les mappings
     * 
     * @return array Liste des mappings
     */
    public function get_all_mappings()
    {
        return \db()->table(self::MAPPING_TABLE . ' m')
            ->select('m.*, e.name as entity_name, e.code as entity_code')
            ->join(self::TABLE . ' e', 'e.id = m.billing_entity_id', 'left')
            ->orderBy('m.priority', 'DESC')
            ->get()
            ->getResultArray();
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
        return \db()->tableExists(self::TABLE) && \db()->tableExists(self::MAPPING_TABLE);
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
            $mappings = \db()->table(self::MAPPING_TABLE)->where('billing_entity_id', $entity['id'])->get()->getResultArray();
            
            $entity['mappings'] = array_column($mappings, 'tax_residence_code');
        }
        
        return $entities;
    }
}

