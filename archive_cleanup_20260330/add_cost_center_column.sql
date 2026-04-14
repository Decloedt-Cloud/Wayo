-- Script SQL pour ajouter la colonne cost_center à la table expense_categories
-- Date: 2025-01-06
-- Description: Ajout de la colonne cost_center pour stocker les centres de coût (chiffres uniquement)

USE school_management;

-- Ajouter la colonne cost_center à la table expense_categories
ALTER TABLE `expense_categories`
ADD COLUMN `cost_center` INT(11) DEFAULT NULL
COMMENT 'Centre de coût - chiffres uniquement'
AFTER `name`;

-- Index pour optimiser les recherches sur cost_center
ALTER TABLE `expense_categories`
ADD INDEX `idx_cost_center` (`cost_center`);

-- Vérifier que la colonne a été ajoutée
DESCRIBE expense_categories;
