<?php

namespace MagicToolbox\Magic360\Controller\Ajax;

/**
 * Ajax media controller
 *
 */
class Media extends \Magento\Swatches\Controller\Ajax\Media
{

    /**
     * Get product media
     *
     * @return string
     */
    public function execute()
    {
        $result = [];

        if ($productId = (int)$this->getRequest()->getParam('product_id')) {
            $currentProduct = $this->productModelFactory->create()->load($productId);
            $isConfigurable = ($currentProduct->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE);
            $attributes = (array)$this->getRequest()->getParam('attributes');

            if ($isConfigurable && method_exists($this, 'getProductVariationWithMedia')) {
                $product = null;
                if (!empty($attributes)) {
                    $product = $this->getProductVariationWithMedia360($currentProduct, $attributes);
                }

                if ($product && $product->getImage() && $product->getImage() != 'no_selection') {
                    $currentProduct = $product;
                    $product = null;
                }

                if ($product && $this->hasMagic360Media($product->getId())) {
                    $currentProduct = $product;
                }
            }

            $result['variantProductId'] = $currentProduct->getId();
        }

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON);
        $resultJson->setData($result);
        return $resultJson;
    }

    /**
     * Get Swatches module composer version
     *
     * @return string
     */
    protected function getSwatchesModuleVersion()
    {
        static $version = null;
        if ($version === null) {
            $componentRegistrar = $this->_objectManager->get(\Magento\Framework\Component\ComponentRegistrarInterface::class);
            $path = $componentRegistrar->getPath(
                \Magento\Framework\Component\ComponentRegistrar::MODULE,
                'Magento_Swatches'
            );
            $readFactory = $this->_objectManager->get(\Magento\Framework\Filesystem\Directory\ReadFactory::class);
            $directoryRead = $readFactory->create($path);
            $composerJsonData = $directoryRead->readFile('composer.json');
            $data = json_decode($composerJsonData);

            $version = empty($data->version) ? '' : (string)$data->version;
        }
        return $version;
    }

    /**
     * Get product variation
     *
     * @param  \Magento\Catalog\Model\Product $currentConfigurable
     * @param  array $attributes
     * @return \Magento\Catalog\Model\Product|bool|null
     */
    protected function getProductVariationWithMedia360(\Magento\Catalog\Model\Product $currentConfigurable, array $attributes)
    {
        $product = null;
        $layeredAttributes = [];
        $configurableAttributes = $this->swatchHelper->getAttributesFromConfigurable($currentConfigurable);
        if ($configurableAttributes) {
            $layeredAttributes = $this->getLayeredAttributesIfExists($configurableAttributes);
        }
        $resultAttributes = array_merge($layeredAttributes, $attributes);

        $product = $this->swatchHelper->loadVariationByFallback($currentConfigurable, $resultAttributes);
        $doSearchAgain = !($product && $product->getImage() && $product->getImage() != 'no_selection');

        if ($product && $doSearchAgain && $this->hasMagic360Media($product->getId())) {
            return $product;
        }

        $version = $this->getSwatchesModuleVersion();
        if (version_compare($version, '100.1.0', '>=')) {
            //NOTE: for Magento versions 2.1.0 - 2.1.12 (Swatches module versions 100.1.0 - 100.1.10)
            if ($doSearchAgain) {
                $product = $this->swatchHelper->loadFirstVariationWithSwatchImage($currentConfigurable, $resultAttributes);
            }
            if (!$product) {
                $product = $this->swatchHelper->loadFirstVariationWithImage($currentConfigurable, $resultAttributes);
            }
        } elseif (version_compare($version, '100.0.4', '>=')) {
            //NOTE: for Magento versions 2.0.3 - 2.0.18 (Swatches module versions 100.0.4 - 100.0.12)
            if ($doSearchAgain) {
                $product = $this->swatchHelper->loadFirstVariationSwatchImage($currentConfigurable, $resultAttributes);
            }
            if (!$product) {
                $product = $this->swatchHelper->loadFirstVariationImage($currentConfigurable, $resultAttributes);
            }
        } else {
            //NOTE: for Magento versions 2.0.0 - 2.0.2  (Swatches module versions 100.0.2 - 100.0.3)
            if ($doSearchAgain) {
                $product = $this->swatchHelper->loadFirstVariationWithImage($currentConfigurable, $resultAttributes);
            }
        }

        if (!$product) {
            $helperSwatchesData = $this->_objectManager->get(
                \MagicToolbox\Magic360\Helper\SwatchesData::class
            );
            $product = $helperSwatchesData->loadFirstVariationIgnoringImages($currentConfigurable, $resultAttributes);
        }

        return $product;
    }

    /**
     * Check if product has 360 images
     *
     * @param  int $id Product id
     * @return bool
     */
    protected function hasMagic360Media($id)
    {
        static $media = [];
        if (!isset($media[$id])) {
            $modelGalleryFactory = $this->_objectManager->get(
                \MagicToolbox\Magic360\Model\GalleryFactory::class
            );
            $galleryModel = $modelGalleryFactory->create();
            $collection = $galleryModel->getCollection();
            $collection->addFieldToFilter('product_id', $id);
            $media[$id] = (bool)$collection->count();
        }
        return $media[$id];
    }
}
