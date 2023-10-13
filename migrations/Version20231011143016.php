<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231011143016 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE magasin (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, opening_time TIME NOT NULL, closing_time TIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_line (id INT AUTO_INCREMENT NOT NULL, o_id_id INT NOT NULL, p_id_id INT NOT NULL, quantity INT DEFAULT NULL, INDEX IDX_9CE58EE1A42C301A (o_id_id), INDEX IDX_9CE58EE135BADC3D (p_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE orders (id INT AUTO_INCREMENT NOT NULL, uid_id INT NOT NULL, m_id_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', status TINYINT(1) NOT NULL, pick_up TIME NOT NULL, INDEX IDX_E52FFDEE534B549B (uid_id), INDEX IDX_E52FFDEE33B32133 (m_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE order_line ADD CONSTRAINT FK_9CE58EE1A42C301A FOREIGN KEY (o_id_id) REFERENCES orders (id)');
        $this->addSql('ALTER TABLE order_line ADD CONSTRAINT FK_9CE58EE135BADC3D FOREIGN KEY (p_id_id) REFERENCES products (id)');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE534B549B FOREIGN KEY (uid_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE33B32133 FOREIGN KEY (m_id_id) REFERENCES magasin (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_line DROP FOREIGN KEY FK_9CE58EE1A42C301A');
        $this->addSql('ALTER TABLE order_line DROP FOREIGN KEY FK_9CE58EE135BADC3D');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEE534B549B');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEE33B32133');
        $this->addSql('DROP TABLE magasin');
        $this->addSql('DROP TABLE order_line');
        $this->addSql('DROP TABLE orders');
    }
}
