--dans table space de HumHub, ajouter les colonnes community_name et community_id 
ALTER TABLE space 
ADD COLUMN community_name VARCHAR(255) AFTER name,
ADD COLUMN community_id INT AFTER community_name;