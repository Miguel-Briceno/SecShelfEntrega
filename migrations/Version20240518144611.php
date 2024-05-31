<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240518144611 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE basket ADD id_user_basket_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE basket ADD CONSTRAINT FK_2246507B2667CBB0 FOREIGN KEY (id_user_basket_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_2246507B2667CBB0 ON basket (id_user_basket_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE basket DROP FOREIGN KEY FK_2246507B2667CBB0');
        $this->addSql('DROP INDEX IDX_2246507B2667CBB0 ON basket');
        $this->addSql('ALTER TABLE basket DROP id_user_basket_id');
    }
}
