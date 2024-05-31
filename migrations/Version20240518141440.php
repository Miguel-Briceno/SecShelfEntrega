<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240518141440 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE basket ADD id_shelf_basket_id INT DEFAULT NULL, ADD id_product_basket_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE basket ADD CONSTRAINT FK_2246507BA53AC723 FOREIGN KEY (id_shelf_basket_id) REFERENCES shelf (id)');
        $this->addSql('ALTER TABLE basket ADD CONSTRAINT FK_2246507B847625C8 FOREIGN KEY (id_product_basket_id) REFERENCES product (id)');
        $this->addSql('CREATE INDEX IDX_2246507BA53AC723 ON basket (id_shelf_basket_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2246507B847625C8 ON basket (id_product_basket_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE basket DROP FOREIGN KEY FK_2246507BA53AC723');
        $this->addSql('ALTER TABLE basket DROP FOREIGN KEY FK_2246507B847625C8');
        $this->addSql('DROP INDEX IDX_2246507BA53AC723 ON basket');
        $this->addSql('DROP INDEX UNIQ_2246507B847625C8 ON basket');
        $this->addSql('ALTER TABLE basket DROP id_shelf_basket_id, DROP id_product_basket_id');
    }
}
