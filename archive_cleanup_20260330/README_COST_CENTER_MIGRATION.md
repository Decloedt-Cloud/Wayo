# Migration - Ajout de la colonne Cost Center

## Description
Ce script ajoute une nouvelle colonne `cost_center` à la table `expense_categories` pour stocker les centres de coût (chiffres uniquement).

## Fichiers inclus
- `add_cost_center_column.sql` - Script SQL pour phpMyAdmin ou ligne de commande
- `add_cost_center_migration.php` - Script PHP pour exécution via navigateur

## Structure de la table après migration
```
expense_categories:
- id (int) - Clé primaire
- name (varchar) - Nom de la catégorie
- cost_center (int) - NOUVEAU: Centre de coût (chiffres uniquement)
- school_id (int) - ID de l'école
- session (varchar) - Session
```

## Méthodes d'exécution

### Option 1: Via phpMyAdmin ou MySQL Workbench
1. Ouvrir phpMyAdmin
2. Sélectionner la base `school_management`
3. Aller dans l'onglet "SQL"
4. Copier/coller le contenu du fichier `add_cost_center_column.sql`
5. Exécuter la requête

### Option 2: Via ligne de commande MySQL
```bash
mysql -u root -p school_management < add_cost_center_column.sql
```

### Option 3: Via le script PHP (recommandé)
1. Placer le fichier `add_cost_center_migration.php` dans le répertoire racine du projet
2. Ouvrir dans un navigateur: `http://localhost/SchoolManagement/add_cost_center_migration.php`
3. Le script s'exécutera automatiquement et affichera le résultat

## Vérification
Après l'exécution, vérifier que la colonne `cost_center` apparaît dans la table `expense_categories`.

## Sécurité
- Le script vérifie si la colonne existe déjà avant de l'ajouter
- Les données existantes ne seront pas affectées
- Un index est créé pour optimiser les performances des recherches

## Rollback (si nécessaire)
```sql
ALTER TABLE expense_categories DROP COLUMN cost_center;
ALTER TABLE expense_categories DROP INDEX idx_cost_center;
```




