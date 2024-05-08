<?php

namespace MagicToolbox\Magic360\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Data upgrades
 *
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * Config table name
     */
    const MAGIC360_CONFIG_TABLE = 'magic360_config';

    /**
     * Upgrades data
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        //NOTE: 'data_version' from `setup_module` table
        $dataVersion = $context->getVersion();

        if (empty($dataVersion)) {
            //NOTE: skip upgrade when install
            return;
        }

        $setup->startSetup();

        if ($setup->tableExists(self::MAGIC360_CONFIG_TABLE)) {
            /** @var \Magento\Framework\DB\Adapter\Pdo\Mysql $connection */
            $connection = $setup->getConnection();

            $tableName = $setup->getTable(self::MAGIC360_CONFIG_TABLE);

            if (version_compare($dataVersion, '1.0.2') < 0) {
                //NOTE: make sure that the option is not present before inserting new one
                $connection->delete($tableName, ['name = ?' => 'display-spin']);
                $connection->insert($tableName, [
                    'platform' => 0,
                    'profile' => 'product',
                    'name' => 'display-spin',
                    'value' => 'inside fotorama gallery',
                    'status' => 2
                ]);
            }
        }

        $setup->endSetup();
    }
}
