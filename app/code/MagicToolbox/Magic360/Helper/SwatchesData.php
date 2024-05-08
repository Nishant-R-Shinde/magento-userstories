<?php

namespace MagicToolbox\Magic360\Helper;

/**
 * Class Helper Data
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SwatchesData extends \Magento\Swatches\Helper\Data
{
    /**
     * @param \Magento\Catalog\Model\Product $configurableProduct
     * @param array $requiredAttributes
     * @return bool|\Magento\Catalog\Api\Data\ProductInterface
     */
    public function loadFirstVariationIgnoringImages(\Magento\Catalog\Model\Product $configurableProduct, array $requiredAttributes)
    {
        if ($this->isProductHasSwatch($configurableProduct)) {
            $usedProducts = $configurableProduct->getTypeInstance()->getUsedProducts($configurableProduct);

            foreach ($usedProducts as $simpleProduct) {
                if (!array_diff_assoc($requiredAttributes, $simpleProduct->getData())) {
                    return $simpleProduct;
                }
            }
        }

        return false;
    }
}
