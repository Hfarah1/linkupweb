<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250414134951 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574AC9486A13');
        $this->addSql('DROP INDEX id_categorie ON events');
        $this->addSql('CREATE INDEX IDX_5387574AC9486A13 ON events (id_categorie)');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574AC9486A13 FOREIGN KEY (id_categorie) REFERENCES categories (id_categorie)');
        $this->addSql('DROP INDEX id_rencontre ON feedbacks');
        $this->addSql('DROP INDEX reply_to ON feedbacks');
        $this->addSql('ALTER TABLE feedbacks CHANGE commentaire commentaire LONGTEXT NOT NULL, CHANGE date_commentaire date_commentaire DATETIME NOT NULL, CHANGE post post LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE lieu MODIFY idLieu INT NOT NULL');
        $this->addSql('DROP INDEX `primary` ON lieu');
        $this->addSql('ALTER TABLE lieu ADD nom_lieu VARCHAR(255) NOT NULL, ADD ville_lieu VARCHAR(255) NOT NULL, ADD type_lieu VARCHAR(255) NOT NULL, ADD prix_location NUMERIC(10, 0) NOT NULL, DROP nomLieu, DROP villeLieu, DROP typeLieu, DROP prixLocation, CHANGE adresse adresse VARCHAR(255) NOT NULL, CHANGE idLieu id_lieu INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE lieu ADD PRIMARY KEY (id_lieu)');
        $this->addSql('DROP INDEX fk_event ON materiels');
        $this->addSql('ALTER TABLE materiels ADD nom_mat VARCHAR(255) NOT NULL, ADD description_mat LONGTEXT NOT NULL, ADD categorie_mat VARCHAR(255) NOT NULL, ADD marque_mat VARCHAR(255) NOT NULL, ADD reference_mat VARCHAR(255) NOT NULL, ADD quantite_stock INT NOT NULL, ADD quantite_reservee INT NOT NULL, DROP nomMat, DROP descriptionMat, DROP categorieMat, DROP marqueMat, DROP referenceMat, DROP quantiteStock, DROP quantiteReservee, CHANGE idEv id_ev INT DEFAULT NULL, CHANGE imagePath image_path VARCHAR(255) DEFAULT NULL');
        $this->addSql('DROP INDEX IDX_75EA56E0FB7336F0 ON messenger_messages');
        $this->addSql('DROP INDEX IDX_75EA56E0E3BD61CE ON messenger_messages');
        $this->addSql('DROP INDEX IDX_75EA56E016BA31DB ON messenger_messages');
        $this->addSql('ALTER TABLE messenger_messages CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE queue_name queue_name VARCHAR(255) NOT NULL, CHANGE created_at created_at DATETIME NOT NULL, CHANGE available_at available_at DATETIME NOT NULL, CHANGE delivered_at delivered_at DATETIME DEFAULT NULL');
        $this->addSql('DROP INDEX id_rencontre ON new_table_namematch_entity');
        $this->addSql('ALTER TABLE new_table_namematch_entity CHANGE date_match date_match DATETIME NOT NULL, CHANGE statut statut VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE offre DROP FOREIGN KEY offre_ibfk_1');
        $this->addSql('ALTER TABLE offre CHANGE salaire salaire NUMERIC(10, 0) DEFAULT NULL');
        $this->addSql('DROP INDEX idx_af86866ffe6e88d7 ON offre');
        $this->addSql('CREATE INDEX IDX_AF86866F50EAE44 ON offre (id_utilisateur)');
        $this->addSql('ALTER TABLE offre ADD CONSTRAINT offre_ibfk_1 FOREIGN KEY (id_utilisateur) REFERENCES user (id)');
        $this->addSql('DROP INDEX idx_produit_type ON produit');
        $this->addSql('ALTER TABLE produit CHANGE description description LONGTEXT NOT NULL, CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL');
        $this->addSql('DROP INDEX idx_coordinates ON rencontres');
        $this->addSql('ALTER TABLE rencontres CHANGE description description LONGTEXT DEFAULT NULL, CHANGE categorie_rencontre categorie_rencontre VARCHAR(255) NOT NULL, CHANGE image image LONGBLOB DEFAULT NULL, CHANGE budget budget NUMERIC(10, 0) DEFAULT NULL, CHANGE bio bio LONGTEXT DEFAULT NULL, CHANGE statut statut VARCHAR(255) DEFAULT NULL, CHANGE genre_recherche genre_recherche VARCHAR(255) DEFAULT NULL, CHANGE niveau_relation niveau_relation VARCHAR(255) DEFAULT NULL, CHANGE created_at created_at DATETIME NOT NULL, CHANGE latitude latitude NUMERIC(10, 0) DEFAULT NULL, CHANGE longitude longitude NUMERIC(10, 0) DEFAULT NULL');
        $this->addSql('ALTER TABLE role CHANGE name name VARCHAR(255) NOT NULL');
        $this->addSql('DROP INDEX unique_email ON utilisateur');
        $this->addSql('DROP INDEX fk_user_role ON utilisateur');
        $this->addSql('ALTER TABLE utilisateur CHANGE nom nom VARCHAR(255) NOT NULL, CHANGE prenom prenom VARCHAR(255) NOT NULL, CHANGE email email VARCHAR(255) NOT NULL, CHANGE role role VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE verification_codes CHANGE verification_code verification_code VARCHAR(255) NOT NULL, CHANGE created_at created_at DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574AC9486A13');
        $this->addSql('DROP INDEX idx_5387574ac9486a13 ON events');
        $this->addSql('CREATE INDEX id_categorie ON events (id_categorie)');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574AC9486A13 FOREIGN KEY (id_categorie) REFERENCES categories (id_categorie)');
        $this->addSql('ALTER TABLE feedbacks CHANGE commentaire commentaire TEXT NOT NULL, CHANGE date_commentaire date_commentaire DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE post post TEXT DEFAULT NULL');
        $this->addSql('CREATE INDEX id_rencontre ON feedbacks (id_rencontre)');
        $this->addSql('CREATE INDEX reply_to ON feedbacks (reply_to)');
        $this->addSql('ALTER TABLE lieu MODIFY id_lieu INT NOT NULL');
        $this->addSql('DROP INDEX `PRIMARY` ON lieu');
        $this->addSql('ALTER TABLE lieu ADD nomLieu VARCHAR(400) NOT NULL, ADD villeLieu VARCHAR(400) NOT NULL, ADD typeLieu VARCHAR(400) NOT NULL, ADD prixLocation DOUBLE PRECISION NOT NULL, DROP nom_lieu, DROP ville_lieu, DROP type_lieu, DROP prix_location, CHANGE adresse adresse VARCHAR(400) NOT NULL, CHANGE id_lieu idLieu INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE lieu ADD PRIMARY KEY (idLieu)');
        $this->addSql('ALTER TABLE materiels ADD nomMat VARCHAR(200) NOT NULL, ADD descriptionMat TEXT NOT NULL, ADD categorieMat VARCHAR(200) NOT NULL, ADD marqueMat VARCHAR(200) NOT NULL, ADD referenceMat VARCHAR(200) NOT NULL, ADD quantiteStock INT NOT NULL, ADD quantiteReservee INT NOT NULL, DROP nom_mat, DROP description_mat, DROP categorie_mat, DROP marque_mat, DROP reference_mat, DROP quantite_stock, DROP quantite_reservee, CHANGE id_ev idEv INT DEFAULT NULL, CHANGE image_path imagePath VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE INDEX fk_event ON materiels (idEv)');
        $this->addSql('ALTER TABLE messenger_messages CHANGE id id BIGINT AUTO_INCREMENT NOT NULL, CHANGE queue_name queue_name VARCHAR(190) NOT NULL, CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE available_at available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE delivered_at delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('ALTER TABLE new_table_namematch_entity CHANGE date_match date_match DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE statut statut VARCHAR(50) NOT NULL');
        $this->addSql('CREATE INDEX id_rencontre ON new_table_namematch_entity (id_rencontre)');
        $this->addSql('ALTER TABLE offre DROP FOREIGN KEY FK_AF86866F50EAE44');
        $this->addSql('ALTER TABLE offre CHANGE salaire salaire NUMERIC(10, 2) DEFAULT NULL');
        $this->addSql('DROP INDEX idx_af86866f50eae44 ON offre');
        $this->addSql('CREATE INDEX IDX_AF86866FFE6E88D7 ON offre (id_utilisateur)');
        $this->addSql('ALTER TABLE offre ADD CONSTRAINT FK_AF86866F50EAE44 FOREIGN KEY (id_utilisateur) REFERENCES user (id)');
        $this->addSql('ALTER TABLE produit CHANGE description description TEXT NOT NULL, CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE updated_at updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('CREATE INDEX idx_produit_type ON produit (type_produit)');
        $this->addSql('ALTER TABLE rencontres CHANGE description description TEXT DEFAULT NULL, CHANGE categorie_rencontre categorie_rencontre VARCHAR(50) NOT NULL, CHANGE image image MEDIUMBLOB DEFAULT NULL, CHANGE budget budget DOUBLE PRECISION DEFAULT NULL, CHANGE bio bio TEXT DEFAULT NULL, CHANGE statut statut VARCHAR(50) DEFAULT \'active\', CHANGE genre_recherche genre_recherche VARCHAR(50) DEFAULT NULL, CHANGE niveau_relation niveau_relation VARCHAR(50) DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE latitude latitude DOUBLE PRECISION DEFAULT NULL, CHANGE longitude longitude DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('CREATE INDEX idx_coordinates ON rencontres (latitude, longitude)');
        $this->addSql('ALTER TABLE role CHANGE name name VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE utilisateur CHANGE nom nom VARCHAR(30) NOT NULL, CHANGE prenom prenom VARCHAR(40) NOT NULL, CHANGE email email VARCHAR(50) NOT NULL, CHANGE role role VARCHAR(20) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX unique_email ON utilisateur (email)');
        $this->addSql('CREATE INDEX fk_user_role ON utilisateur (role)');
        $this->addSql('ALTER TABLE verification_codes CHANGE verification_code verification_code VARCHAR(6) NOT NULL, CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
    }
}
