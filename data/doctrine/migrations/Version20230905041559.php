<?php

declare(strict_types=1);

namespace Frontend\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230905041559 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE contact_message (uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary_ordered_time)\', email VARCHAR(150) NOT NULL, name VARCHAR(150) NOT NULL, subject LONGTEXT NOT NULL, message LONGTEXT NOT NULL, platform LONGTEXT NOT NULL, created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE department (uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary_ordered_time)\', name VARCHAR(191) NOT NULL, created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary_ordered_time)\', identity VARCHAR(191) NOT NULL, password VARCHAR(191) NOT NULL, status ENUM(\'pending\', \'active\'), isDeleted TINYINT(1) NOT NULL, hash VARCHAR(64) NOT NULL, created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_8D93D6496A95E9C4 (identity), UNIQUE INDEX UNIQ_8D93D649D1B862B8 (hash), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_roles (userUuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary_ordered_time)\', roleUuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary_ordered_time)\', INDEX IDX_54FCD59FD73087E9 (userUuid), INDEX IDX_54FCD59F88446210 (roleUuid), PRIMARY KEY(userUuid, roleUuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_avatar (uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary_ordered_time)\', name VARCHAR(191) NOT NULL, created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', userUuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary_ordered_time)\', UNIQUE INDEX UNIQ_73256912D73087E9 (userUuid), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_detail (uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary_ordered_time)\', firstName VARCHAR(191) DEFAULT NULL, lastName VARCHAR(191) DEFAULT NULL, created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', userUuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary_ordered_time)\', UNIQUE INDEX UNIQ_4B5464AED73087E9 (userUuid), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_remember_me (uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary_ordered_time)\', rememberMeToken VARCHAR(100) NOT NULL, userAgent LONGTEXT NOT NULL, expireDate DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', userUuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary_ordered_time)\', UNIQUE INDEX UNIQ_D3E96EBD1BBB86A0 (rememberMeToken), INDEX IDX_D3E96EBDD73087E9 (userUuid), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_reset_password (uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary_ordered_time)\', expires DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', hash VARCHAR(64) NOT NULL, status VARCHAR(20) NOT NULL, created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', userUuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary_ordered_time)\', UNIQUE INDEX UNIQ_D21DE3BCD1B862B8 (hash), INDEX IDX_D21DE3BCD73087E9 (userUuid), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_role (uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary_ordered_time)\', name VARCHAR(30) NOT NULL, created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_2DE8C6A35E237E06 (name), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_roles ADD CONSTRAINT FK_54FCD59FD73087E9 FOREIGN KEY (userUuid) REFERENCES user (uuid)');
        $this->addSql('ALTER TABLE user_roles ADD CONSTRAINT FK_54FCD59F88446210 FOREIGN KEY (roleUuid) REFERENCES user_role (uuid)');
        $this->addSql('ALTER TABLE user_avatar ADD CONSTRAINT FK_73256912D73087E9 FOREIGN KEY (userUuid) REFERENCES user (uuid)');
        $this->addSql('ALTER TABLE user_detail ADD CONSTRAINT FK_4B5464AED73087E9 FOREIGN KEY (userUuid) REFERENCES user (uuid)');
        $this->addSql('ALTER TABLE user_remember_me ADD CONSTRAINT FK_D3E96EBDD73087E9 FOREIGN KEY (userUuid) REFERENCES user (uuid)');
        $this->addSql('ALTER TABLE user_reset_password ADD CONSTRAINT FK_D21DE3BCD73087E9 FOREIGN KEY (userUuid) REFERENCES user (uuid)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_roles DROP FOREIGN KEY FK_54FCD59FD73087E9');
        $this->addSql('ALTER TABLE user_roles DROP FOREIGN KEY FK_54FCD59F88446210');
        $this->addSql('ALTER TABLE user_avatar DROP FOREIGN KEY FK_73256912D73087E9');
        $this->addSql('ALTER TABLE user_detail DROP FOREIGN KEY FK_4B5464AED73087E9');
        $this->addSql('ALTER TABLE user_remember_me DROP FOREIGN KEY FK_D3E96EBDD73087E9');
        $this->addSql('ALTER TABLE user_reset_password DROP FOREIGN KEY FK_D21DE3BCD73087E9');
        $this->addSql('DROP TABLE contact_message');
        $this->addSql('DROP TABLE department');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_roles');
        $this->addSql('DROP TABLE user_avatar');
        $this->addSql('DROP TABLE user_detail');
        $this->addSql('DROP TABLE user_remember_me');
        $this->addSql('DROP TABLE user_reset_password');
        $this->addSql('DROP TABLE user_role');
    }
}
