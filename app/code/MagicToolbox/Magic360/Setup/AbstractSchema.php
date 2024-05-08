<?php

namespace MagicToolbox\Magic360\Setup;

use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;

/**
 * DB schema installs/upgrades
 *
 * @codeCoverageIgnore
 */
abstract class AbstractSchema
{
    /**
     * Config table name
     */
    const MAGIC360_CONFIG_TABLE = 'magic360_config';

    /**
     * Gallery table name
     */
    const MAGIC360_GALLERY_TABLE = 'magic360_gallery';

    /**
     * Columns table name
     */
    const MAGIC360_COLUMNS_TABLE = 'magic360_columns';

    /**
     * Create config table
     *
     * @param SchemaSetupInterface $setup
     * @param bool $skipIfExists
     * @return void
     */
    protected function createConfigTable(SchemaSetupInterface $setup, $skipIfExists = true)
    {
        /** @var \Magento\Framework\DB\Adapter\Pdo\Mysql $connection */
        $connection = $setup->getConnection();

        $tableName = $setup->getTable(self::MAGIC360_CONFIG_TABLE);

        if ($setup->tableExists(self::MAGIC360_CONFIG_TABLE)) {
            if ($skipIfExists) {
                return;
            }
            $connection->dropTable($tableName);
        }

        $table = $connection->newTable(
            $tableName
        )->addColumn(
            'id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'ID'
        )->addColumn(
            'platform',
            Table::TYPE_BOOLEAN,
            null,
            ['nullable' => false, 'default' => '0'],
            'Platform'
        )->addColumn(
            'profile',
            Table::TYPE_TEXT,
            64,
            ['nullable'  => false],
            'Profile'
        )->addColumn(
            'name',
            Table::TYPE_TEXT,
            64,
            ['nullable'  => false],
            'Name'
        )->addColumn(
            'value',
            Table::TYPE_TEXT,
            null,
            ['nullable'  => false],
            'Value'
        )->addColumn(
            'status',
            Table::TYPE_BOOLEAN,
            null,
            ['nullable' => false, 'default' => '0'],
            'Status'
        )->setComment(
            'Magic 360 configuration'
        );

        $connection->createTable($table);
    }

    /**
     * Create gallery table
     *
     * @param SchemaSetupInterface $setup
     * @param bool $skipIfExists
     * @return void
     */
    protected function createGalleryTable(SchemaSetupInterface $setup, $skipIfExists = true)
    {
        /** @var \Magento\Framework\DB\Adapter\Pdo\Mysql $connection */
        $connection = $setup->getConnection();

        $tableName = $setup->getTable(self::MAGIC360_GALLERY_TABLE);

        if ($setup->tableExists(self::MAGIC360_GALLERY_TABLE)) {
            if ($skipIfExists) {
                return;
            }
            $connection->dropTable($tableName);
        }

        $table = $connection->newTable(
            $tableName
        )->addColumn(
            'id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'ID'
        )->addColumn(
            'product_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Product ID'
        )->addColumn(
            'position',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true],
            'Position'
        )->addColumn(
            'file',
            Table::TYPE_TEXT,
            255,
            [],
            'File'
        )->addIndex(
            $setup->getIdxName(
                $tableName,
                ['product_id']
            ),
            ['product_id']
        )->setComment(
            'Magic 360 gallery'
        );

        $connection->createTable($table);
    }

    /**
     * Create columns table
     *
     * @param SchemaSetupInterface $setup
     * @param bool $skipIfExists
     * @return void
     */
    protected function createColumnsTable(SchemaSetupInterface $setup, $skipIfExists = true)
    {
        /** @var \Magento\Framework\DB\Adapter\Pdo\Mysql $connection */
        $connection = $setup->getConnection();

        $tableName = $setup->getTable(self::MAGIC360_COLUMNS_TABLE);

        if ($setup->tableExists(self::MAGIC360_COLUMNS_TABLE)) {
            if ($skipIfExists) {
                return;
            }
            $connection->dropTable($tableName);
        }

        $table = $connection->newTable(
            $tableName
        )->addColumn(
            'product_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0', 'primary' => true],
            'Product ID'
        )->addColumn(
            'columns',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true],
            'Columns'
        )->setComment(
            'Magic 360 columns'
        );

        $connection->createTable($table);
    }

    /**
     * Upgrade gallery table
     *
     * @param SchemaSetupInterface $setup
     * @return bool
     */
    protected function upgradeGalleryTable(SchemaSetupInterface $setup)
    {
        if (!$setup->tableExists(self::MAGIC360_GALLERY_TABLE)) {
            return false;
        }

        /** @var \Magento\Framework\DB\Adapter\Pdo\Mysql $connection */
        $connection = $setup->getConnection();

        $tableName = $setup->getTable(self::MAGIC360_GALLERY_TABLE);

        $indexList = $connection->getIndexList($tableName);
        $indexName = $setup->getIdxName(
            $tableName,
            ['product_id']
        );

        if (!isset($indexList[$indexName])) {
            $connection->addIndex(
                $tableName,
                $indexName,
                ['product_id']
            );
        }

        return true;
    }
}
