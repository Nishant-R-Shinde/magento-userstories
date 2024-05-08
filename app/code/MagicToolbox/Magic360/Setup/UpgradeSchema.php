<?php

namespace MagicToolbox\Magic360\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

/**
 * DB schema upgrades
 *
 * @codeCoverageIgnore
 */
class UpgradeSchema extends AbstractSchema implements UpgradeSchemaInterface
{
    /**
     * Upgrades DB schema
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        //NOTE: 'schema_version' from `setup_module` table
        $schemaVersion = $context->getVersion();
        if (empty($schemaVersion)) {
            //NOTE: skip upgrade when install
            return;
        }

        $setup->startSetup();

        $this->createConfigTable($setup);
        $this->upgradeGalleryTable($setup) || $this->createGalleryTable($setup);
        $this->createColumnsTable($setup);

        $setup->endSetup();
    }
}
