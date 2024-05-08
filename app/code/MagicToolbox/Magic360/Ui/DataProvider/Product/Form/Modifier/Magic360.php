<?php

namespace MagicToolbox\Magic360\Ui\DataProvider\Product\Form\Modifier;

/**
 * Magic360 modifier for catalog product form
 *
 * @api
 * @since 100.1.0
 */
class Magic360 extends \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier
{
    const GROUP_MAGIC360 = 'magic360';
    const GROUP_CONTENT = 'image-management';
    const SORT_ORDER = 24;

    /**
     * @var \Magento\Catalog\Model\Locator\LocatorInterface
     * @since 100.1.0
     */
    protected $locator;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * @param \Magento\Catalog\Model\Locator\LocatorInterface $locator
     */
    public function __construct(
        \Magento\Catalog\Model\Locator\LocatorInterface $locator
    ) {
        $this->locator = $locator;
    }

    /**
     * {@inheritdoc}
     * @since 100.1.0
     */
    public function modifyMeta(array $meta)
    {
        if (!$this->locator->getProduct()->getId() || !$this->getModuleManager()->isOutputEnabled('MagicToolbox_Magic360')) {
            return $meta;
        }

        $version = $this->getProductMetadata()->getVersion();
        if (version_compare($version, '2.2.0', '<')) {
            return $meta;
        }

        $meta[static::GROUP_MAGIC360] = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => 'htmlContent',
                    ],
                    'wrapper' => [
                        'label' => __('Magic 360'),
                        'collapsible' => true,
                        'opened' => false,
                        'canShow' => true,
                        'componentType' => 'fieldset',
                        'sortOrder' => 24,
                    ],
                ],
                'block' => [
                    'name' => 'magic360',
                ],
            ],
        ];

        return $meta;
    }

    /**
     * {@inheritdoc}
     * @since 100.1.0
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * Retrieve module manager instance.
     *
     * @return \Magento\Framework\Module\Manager
     *
     * @deprecated 100.2.0
     */
    protected function getModuleManager()
    {
        if ($this->moduleManager === null) {
            $this->moduleManager = \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Framework\Module\Manager::class);
        }
        return $this->moduleManager;
    }

    /**
     * The getter function to get the ProductMetadata
     *
     * @return \Magento\Framework\App\ProductMetadataInterface
     * @deprecated
     */
    protected function getProductMetadata()
    {
        if ($this->productMetadata === null) {
            $this->productMetadata = \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Framework\App\ProductMetadataInterface::class);
        }
        return $this->productMetadata;
    }
}
