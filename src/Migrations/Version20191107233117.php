<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191107233117 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE images (id INT AUTO_INCREMENT NOT NULL, products_id INT DEFAULT NULL, src LONGTEXT NOT NULL, INDEX IDX_E01FBE6A6C8A81A9 (products_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE products (id INT AUTO_INCREMENT NOT NULL, supplier_id INT NOT NULL, brand_id INT NOT NULL, type_id INT NOT NULL, size_id INT DEFAULT NULL, color_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, sku VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, tags VARCHAR(255) DEFAULT NULL, price VARCHAR(255) NOT NULL, compare VARCHAR(255) DEFAULT NULL, barcode VARCHAR(255) DEFAULT NULL, supplier_stock VARCHAR(255) NOT NULL, stock VARCHAR(255) NOT NULL, weight VARCHAR(255) DEFAULT NULL, length VARCHAR(255) DEFAULT NULL, season VARCHAR(255) DEFAULT NULL, women VARCHAR(255) DEFAULT NULL, men VARCHAR(255) DEFAULT NULL, girls VARCHAR(255) DEFAULT NULL, boys VARCHAR(255) DEFAULT NULL, unisex VARCHAR(255) DEFAULT NULL, to_shopify TINYINT(1) NOT NULL, INDEX IDX_B3BA5A5A2ADD6D8C (supplier_id), INDEX IDX_B3BA5A5A44F5D008 (brand_id), INDEX IDX_B3BA5A5AC54C8C93 (type_id), INDEX IDX_B3BA5A5A498DA827 (size_id), INDEX IDX_B3BA5A5A7ADA1FB5 (color_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE images ADD CONSTRAINT FK_E01FBE6A6C8A81A9 FOREIGN KEY (products_id) REFERENCES products (id)');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5A2ADD6D8C FOREIGN KEY (supplier_id) REFERENCES suppliers (id)');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5A44F5D008 FOREIGN KEY (brand_id) REFERENCES brands (id)');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5AC54C8C93 FOREIGN KEY (type_id) REFERENCES product_types (id)');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5A498DA827 FOREIGN KEY (size_id) REFERENCES product_sizes (id)');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5A7ADA1FB5 FOREIGN KEY (color_id) REFERENCES product_colors (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6A6C8A81A9');
        $this->addSql('DROP TABLE images');
        $this->addSql('DROP TABLE products');
    }
}
