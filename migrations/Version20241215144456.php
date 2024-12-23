<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241215144456 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE car ADD category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE car ADD CONSTRAINT FK_773DE69D12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('CREATE INDEX IDX_773DE69D12469DE2 ON car (category_id)');
        $this->addSql('ALTER TABLE rental ADD car_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE rental ADD CONSTRAINT FK_1619C27DC3C6F69F FOREIGN KEY (car_id) REFERENCES car (id)');
        $this->addSql('CREATE INDEX IDX_1619C27DC3C6F69F ON rental (car_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE car DROP FOREIGN KEY FK_773DE69D12469DE2');
        $this->addSql('DROP INDEX IDX_773DE69D12469DE2 ON car');
        $this->addSql('ALTER TABLE car DROP category_id');
        $this->addSql('ALTER TABLE rental DROP FOREIGN KEY FK_1619C27DC3C6F69F');
        $this->addSql('DROP INDEX IDX_1619C27DC3C6F69F ON rental');
        $this->addSql('ALTER TABLE rental DROP car_id');
    }
}
