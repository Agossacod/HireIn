-- Migration : ajouter les colonnes manquantes pour les profils étudiants
-- Exécuter depuis le dossier racine du projet.

ALTER TABLE student_profiles
    ADD COLUMN phone VARCHAR(20) NULL,
    ADD COLUMN profile_photo VARCHAR(255) NULL,
    ADD COLUMN cv VARCHAR(255) NULL;
