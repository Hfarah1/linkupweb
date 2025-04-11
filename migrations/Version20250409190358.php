<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250409190358 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', available_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', delivered_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE candidature DROP FOREIGN KEY candidature_ibfk_1
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE candidature DROP FOREIGN KEY candidature_ibfk_2
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_e33bd3b8b842c572 ON candidature
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_E33BD3B84103C75F ON candidature (id_offre)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_e33bd3b8fe6e88d7 ON candidature
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_E33BD3B850EAE44 ON candidature (id_utilisateur)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE candidature ADD CONSTRAINT candidature_ibfk_1 FOREIGN KEY (id_offre) REFERENCES offre (id_offre)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE candidature ADD CONSTRAINT candidature_ibfk_2 FOREIGN KEY (id_utilisateur) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE offre DROP FOREIGN KEY offre_ibfk_1
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE offre CHANGE organisation_logo organisation_logo LONGBLOB NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_af86866ffe6e88d7 ON offre
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_AF86866F50EAE44 ON offre (id_utilisateur)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE offre ADD CONSTRAINT offre_ibfk_1 FOREIGN KEY (id_utilisateur) REFERENCES user (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE messenger_messages
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE candidature DROP FOREIGN KEY FK_E33BD3B84103C75F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE candidature DROP FOREIGN KEY FK_E33BD3B850EAE44
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_e33bd3b84103c75f ON candidature
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_E33BD3B8B842C572 ON candidature (id_offre)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_e33bd3b850eae44 ON candidature
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_E33BD3B8FE6E88D7 ON candidature (id_utilisateur)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE candidature ADD CONSTRAINT FK_E33BD3B84103C75F FOREIGN KEY (id_offre) REFERENCES offre (id_offre)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE candidature ADD CONSTRAINT FK_E33BD3B850EAE44 FOREIGN KEY (id_utilisateur) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE offre DROP FOREIGN KEY FK_AF86866F50EAE44
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE offre CHANGE organisation_logo organisation_logo LONGBLOB DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_af86866f50eae44 ON offre
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_AF86866FFE6E88D7 ON offre (id_utilisateur)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE offre ADD CONSTRAINT FK_AF86866F50EAE44 FOREIGN KEY (id_utilisateur) REFERENCES user (id)
        SQL);
    }
}
