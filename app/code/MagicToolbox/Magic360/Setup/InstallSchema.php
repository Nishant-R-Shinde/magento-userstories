<?php

namespace MagicToolbox\Magic360\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

/**
 * DB schema installs
 *
 * @codeCoverageIgnore
 */
class InstallSchema extends AbstractSchema implements InstallSchemaInterface
{
    /**
     * Installs DB schema
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $this->createConfigTable($setup);
        $this->upgradeGalleryTable($setup) || $this->createGalleryTable($setup);
        $this->createColumnsTable($setup);

        $setup->endSetup();
    }
}
