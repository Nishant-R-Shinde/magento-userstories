<?php

namespace MagicToolbox\Magic360\Helper;

/**
 * Magic360 image helper
 */
class Image extends \Magento\Catalog\Helper\Image
{
    /**
     * Get current image model
     *
     * @return \MagicToolbox\Magic360\Model\Product\Image
     */
    protected function _getModel()
    {
        if (!$this->_model) {
            $factory = \Magento\Framework\App\ObjectManager::getInstance()->get(
                \MagicToolbox\Magic360\Model\Product\ImageFactory::class
            );
            $this->_model = $factory->create();
        }
        return $this->_model;
    }

    /**
     * Get current image model
     *
     * @return \MagicToolbox\Magic360\Model\Product\Image
     */
    public function getModel()
    {
        if (!$this->_model) {
            $this->_getModel();
        }
        return $this->_model;
    }

    /**
     * Check if file exists
     *
     * @param string $filename
     * @return bool
     */
    public function fileExists($filename)
    {
        return $this->_getModel()->fileExists($filename);
    }

    /**
     * Retrieve original image size
     * 0 - width, 1 - height
     *
     * @return int[]
     */
    public function getImageSizeArray()
    {
        return $this->_getModel()->getImageSizeArray();
    }
}
