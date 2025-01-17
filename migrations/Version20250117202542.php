<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250117202542 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE catalogue ADD pricing_min NUMERIC(10, 2) DEFAULT NULL, ADD pricing_max NUMERIC(10, 2) DEFAULT NULL');
        $this->addSql('ALTER TABLE item ADD create_date DATETIME DEFAULT NULL, ADD update_date DATETIME DEFAULT NULL');
        $this->addSql('UPDATE item SET create_date = CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE item CHANGE create_date create_date DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE catalogue DROP pricing_min, DROP pricing_max');
        $this->addSql('ALTER TABLE item DROP create_date, DROP update_date');
    }
}
