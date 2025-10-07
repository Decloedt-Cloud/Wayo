ALTER TABLE settings_school
ADD COLUMN Tax_residence VARCHAR(255)  AFTER language;

ALTER TABLE settings_school
ADD COLUMN file VARCHAR(255)  AFTER Tax_residence;

ALTER TABLE settings_school
ADD COLUMN type VARCHAR(255)  AFTER Tax_residence;

ALTER TABLE settings_school
ADD COLUMN vat INT(11) DEFAULT 0  AFTER Tax_residence;

ALTER TABLE settings_school
ADD COLUMN vat_rat INT(11) DEFAULT 0  AFTER vat;

ALTER TABLE settings_school
ADD COLUMN num_vat VARCHAR(50)  AFTER vat_rat;

ALTER TABLE schools
ADD COLUMN Rue VARCHAR(255) NULL AFTER address,
ADD COLUMN Numero VARCHAR(50) NULL AFTER Rue,
ADD COLUMN Ville VARCHAR(100) NULL AFTER Numero,
ADD COLUMN Codepostal VARCHAR(20) NULL AFTER Ville;
