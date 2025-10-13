ALTER TABLE classes
ADD COLUMN date_debut DATE NULL,
ADD COLUMN date_fin DATE NULL,
ADD COLUMN statut ENUM('active', 'inactive') NOT NULL DEFAULT 'inactive',
ADD COLUMN photo VARCHAR(255) NULL,
ADD COLUMN nombre_max_membre INT UNSIGNED NULL;