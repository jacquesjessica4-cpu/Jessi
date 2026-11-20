-- sql/schema.sql
-- Projet final — Site Web pour une Bibliothèque en Ligne

CREATE DATABASE IF NOT EXISTS bibliotheque_en_ligne
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE bibliotheque_en_ligne;

-- ------------------------------------------------------------
-- Table livres — conforme exactement au modèle de l'énoncé
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS livres (
  id                 INT AUTO_INCREMENT PRIMARY KEY,
  titre              VARCHAR(100) NOT NULL,
  auteur             VARCHAR(100) NOT NULL,
  description        TEXT NULL,
  maison_edition     VARCHAR(100) NULL,
  nombre_exemplaire  INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table lecteurs — conforme au modèle de l'énoncé.
-- Un index UNIQUE sur email est ajouté : il permet de reconnaître un
-- lecteur qui revient sur le site (voir app/helpers.php,
-- fonction trouver_ou_creer_lecteur) sans lui demander de mot de passe.
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS lecteurs (
  id      INT AUTO_INCREMENT PRIMARY KEY,
  nom     VARCHAR(100) NOT NULL,
  prenom  VARCHAR(100) NOT NULL,
  email   VARCHAR(100) NOT NULL,
  CONSTRAINT uq_lecteurs_email UNIQUE (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table liste_lecture — conforme au modèle de l'énoncé.
-- Clé primaire composite (id_livre, id_lecteur) : un lecteur ne peut
-- avoir qu'une seule entrée active par livre dans sa liste.
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS liste_lecture (
  id_livre      INT NOT NULL,
  id_lecteur    INT NOT NULL,
  date_emprunt  DATE NOT NULL,
  date_retour   DATE NULL,
  PRIMARY KEY (id_livre, id_lecteur),
  CONSTRAINT fk_liste_lecture_livre
    FOREIGN KEY (id_livre) REFERENCES livres(id) ON DELETE CASCADE,
  CONSTRAINT fk_liste_lecture_lecteur
    FOREIGN KEY (id_lecteur) REFERENCES lecteurs(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
