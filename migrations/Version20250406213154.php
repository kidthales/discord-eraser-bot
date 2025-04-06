<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250406213154 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'Create/Drop `guild` & `user` tables';
    }

    /**
     * @param Schema $schema
     * @return void
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE "guild" (
              id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
              discord_id BIGINT NOT NULL,
              installed BOOLEAN NOT NULL,
              created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
              ,
              updated_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            )
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_IDENTIFIER_GUILD_DISCORD_ID ON "guild" (discord_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE "user" (
              id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
              discord_id BIGINT NOT NULL,
              roles CLOB NOT NULL --(DC2Type:json)
              ,
              created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
              ,
              updated_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            )
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_IDENTIFIER_USER_DISCORD_ID ON "user" (discord_id)
        SQL);
    }

    /**
     * @param Schema $schema
     * @return void
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE "guild"
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE "user"
        SQL);
    }
}
