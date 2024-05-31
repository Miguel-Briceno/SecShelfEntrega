<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240520144141 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE basket ADD id_basket_product_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE basket ADD CONSTRAINT FK_2246507BA534B26C FOREIGN KEY (id_basket_product_id) REFERENCES product (id)');
        $this->addSql('CREATE INDEX IDX_2246507BA534B26C ON basket (id_basket_product_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE basket DROP FOREIGN KEY FK_2246507BA534B26C');
        $this->addSql('DROP INDEX IDX_2246507BA534B26C ON basket');
        $this->addSql('ALTER TABLE basket DROP id_basket_product_id');
    }
}
