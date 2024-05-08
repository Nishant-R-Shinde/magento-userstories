<?php

namespace MagicToolbox\Magic360\Model;

/**
 * Image handler library
 *
 */
class SirvImage extends \MagicToolbox\Magic360\Model\Image
{
    /**
     * Retrieve original image width
     *
     * @return int|null
     */
    public function getOriginalWidth()
    {
        if (isset($this->_fileName)) {
            return $this->_adapter->getOriginalWidth();
        }

        return null;
    }

    /**
     * Retrieve original image height
     *
     * @return int|null
     */
    public function getOriginalHeight()
    {
        if (isset($this->_fileName)) {
            return $this->_adapter->getOriginalHeight();
        }

        return null;
    }

    /**
     * Get get imaging options query
     *
     * @return string
     */
    public function getImagingOptionsQuery()
    {
        //NOTE: only for Sirv adapter
        return $this->_adapter->getImagingOptionsQuery();
    }

    /**
     * Get image size
     *
     * @return array
     */
    public function getImageSize()
    {
        $size = [
            $this->_adapter->getImagingOption('w'),
            $this->_adapter->getImagingOption('h')
        ];

        return $size;
    }
}
