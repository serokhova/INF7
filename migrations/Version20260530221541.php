<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260530221541 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE chore_assignment (id INT AUTO_INCREMENT NOT NULL, day VARCHAR(10) NOT NULL, week_number INT NOT NULL, year INT NOT NULL, status VARCHAR(20) NOT NULL, household_id INT NOT NULL, task_id INT NOT NULL, assigned_to_id INT NOT NULL, INDEX IDX_F99C1762E79FF843 (household_id), INDEX IDX_F99C17628DB60186 (task_id), INDEX IDX_F99C1762F4BD7827 (assigned_to_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE expense (id INT AUTO_INCREMENT NOT NULL, category VARCHAR(30) NOT NULL, label VARCHAR(150) NOT NULL, amount NUMERIC(8, 2) NOT NULL, period VARCHAR(7) NOT NULL, created_at DATETIME NOT NULL, household_id INT NOT NULL, INDEX IDX_2D3A8DA6E79FF843 (household_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE household (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, monthly_charges NUMERIC(8, 2) DEFAULT 0 NOT NULL, owner_id INT NOT NULL, INDEX IDX_54C32FC07E3C61F9 (owner_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL, household_id INT NOT NULL, author_id INT NOT NULL, INDEX IDX_B6BD307FE79FF843 (household_id), INDEX IDX_B6BD307FF675F31B (author_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE password_reset_token (id INT AUTO_INCREMENT NOT NULL, token VARCHAR(64) NOT NULL, expires_at DATETIME NOT NULL, created_at DATETIME NOT NULL, used TINYINT NOT NULL, user_id INT NOT NULL, UNIQUE INDEX UNIQ_6B7BA4B65F37A13B (token), INDEX IDX_6B7BA4B6A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE payment (id INT AUTO_INCREMENT NOT NULL, rent_amount NUMERIC(8, 2) NOT NULL, charges_amount NUMERIC(8, 2) NOT NULL, period VARCHAR(7) NOT NULL, status VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL, paid_at DATETIME DEFAULT NULL, tenant_id INT NOT NULL, household_id INT NOT NULL, INDEX IDX_6D28840D9033212A (tenant_id), INDEX IDX_6D28840DE79FF843 (household_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE task (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(100) NOT NULL, description VARCHAR(255) DEFAULT NULL, points_value INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(100) NOT NULL, last_name VARCHAR(100) NOT NULL, tantieme NUMERIC(5, 2) DEFAULT NULL, monthly_rent NUMERIC(8, 2) DEFAULT NULL, household_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), INDEX IDX_8D93D649E79FF843 (household_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE chore_assignment ADD CONSTRAINT FK_F99C1762E79FF843 FOREIGN KEY (household_id) REFERENCES household (id)');
        $this->addSql('ALTER TABLE chore_assignment ADD CONSTRAINT FK_F99C17628DB60186 FOREIGN KEY (task_id) REFERENCES task (id)');
        $this->addSql('ALTER TABLE chore_assignment ADD CONSTRAINT FK_F99C1762F4BD7827 FOREIGN KEY (assigned_to_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE expense ADD CONSTRAINT FK_2D3A8DA6E79FF843 FOREIGN KEY (household_id) REFERENCES household (id)');
        $this->addSql('ALTER TABLE household ADD CONSTRAINT FK_54C32FC07E3C61F9 FOREIGN KEY (owner_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FE79FF843 FOREIGN KEY (household_id) REFERENCES household (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FF675F31B FOREIGN KEY (author_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE password_reset_token ADD CONSTRAINT FK_6B7BA4B6A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D9033212A FOREIGN KEY (tenant_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840DE79FF843 FOREIGN KEY (household_id) REFERENCES household (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D649E79FF843 FOREIGN KEY (household_id) REFERENCES household (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chore_assignment DROP FOREIGN KEY FK_F99C1762E79FF843');
        $this->addSql('ALTER TABLE chore_assignment DROP FOREIGN KEY FK_F99C17628DB60186');
        $this->addSql('ALTER TABLE chore_assignment DROP FOREIGN KEY FK_F99C1762F4BD7827');
        $this->addSql('ALTER TABLE expense DROP FOREIGN KEY FK_2D3A8DA6E79FF843');
        $this->addSql('ALTER TABLE household DROP FOREIGN KEY FK_54C32FC07E3C61F9');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FE79FF843');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FF675F31B');
        $this->addSql('ALTER TABLE password_reset_token DROP FOREIGN KEY FK_6B7BA4B6A76ED395');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840D9033212A');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840DE79FF843');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649E79FF843');
        $this->addSql('DROP TABLE chore_assignment');
        $this->addSql('DROP TABLE expense');
        $this->addSql('DROP TABLE household');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE password_reset_token');
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP TABLE task');
        $this->addSql('DROP TABLE `user`');
    }
}
