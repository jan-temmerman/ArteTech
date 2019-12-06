<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191206001855 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE pause_length (id INT AUTO_INCREMENT NOT NULL, time TIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE hourly_rate (id INT AUTO_INCREMENT NOT NULL, price INT NOT NULL, unit VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, status_id INT DEFAULT NULL, hourly_rate_id INT DEFAULT NULL, transport_rate_id INT DEFAULT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, INDEX IDX_8D93D6496BF700BD (status_id), INDEX IDX_8D93D6496733CA9E (hourly_rate_id), INDEX IDX_8D93D649284E5F6E (transport_rate_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE task (id INT AUTO_INCREMENT NOT NULL, employee_id INT NOT NULL, pause_length_id INT NOT NULL, period_id INT NOT NULL, date DATE NOT NULL, hours_worked DOUBLE PRECISION NOT NULL, materials_used VARCHAR(255) NOT NULL, INDEX IDX_527EDB258C03F15C (employee_id), INDEX IDX_527EDB25DA2A9DCC (pause_length_id), INDEX IDX_527EDB25EC8B7ADE (period_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE company (id INT AUTO_INCREMENT NOT NULL, admin_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_4FBF094F642B8210 (admin_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transport_rate (id INT AUTO_INCREMENT NOT NULL, price DOUBLE PRECISION NOT NULL, unit VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_status (id INT AUTO_INCREMENT NOT NULL, status VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE period (id INT AUTO_INCREMENT NOT NULL, hourly_rate_id INT NOT NULL, transport_rate_id INT NOT NULL, company_id INT NOT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, name VARCHAR(255) DEFAULT NULL, INDEX IDX_C5B81ECE6733CA9E (hourly_rate_id), INDEX IDX_C5B81ECE284E5F6E (transport_rate_id), INDEX IDX_C5B81ECE979B1AD6 (company_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6496BF700BD FOREIGN KEY (status_id) REFERENCES user_status (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6496733CA9E FOREIGN KEY (hourly_rate_id) REFERENCES hourly_rate (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649284E5F6E FOREIGN KEY (transport_rate_id) REFERENCES transport_rate (id)');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB258C03F15C FOREIGN KEY (employee_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25DA2A9DCC FOREIGN KEY (pause_length_id) REFERENCES pause_length (id)');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25EC8B7ADE FOREIGN KEY (period_id) REFERENCES period (id)');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094F642B8210 FOREIGN KEY (admin_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE period ADD CONSTRAINT FK_C5B81ECE6733CA9E FOREIGN KEY (hourly_rate_id) REFERENCES hourly_rate (id)');
        $this->addSql('ALTER TABLE period ADD CONSTRAINT FK_C5B81ECE284E5F6E FOREIGN KEY (transport_rate_id) REFERENCES transport_rate (id)');
        $this->addSql('ALTER TABLE period ADD CONSTRAINT FK_C5B81ECE979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB25DA2A9DCC');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6496733CA9E');
        $this->addSql('ALTER TABLE period DROP FOREIGN KEY FK_C5B81ECE6733CA9E');
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB258C03F15C');
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094F642B8210');
        $this->addSql('ALTER TABLE period DROP FOREIGN KEY FK_C5B81ECE979B1AD6');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649284E5F6E');
        $this->addSql('ALTER TABLE period DROP FOREIGN KEY FK_C5B81ECE284E5F6E');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6496BF700BD');
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB25EC8B7ADE');
        $this->addSql('DROP TABLE pause_length');
        $this->addSql('DROP TABLE hourly_rate');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE task');
        $this->addSql('DROP TABLE company');
        $this->addSql('DROP TABLE transport_rate');
        $this->addSql('DROP TABLE user_status');
        $this->addSql('DROP TABLE period');
    }
}
