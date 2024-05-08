<?php

namespace MagicToolbox\Magic360\Helper;

/**
 * Sirv image helper
 *
 */
class SirvImage extends \MagicToolbox\Magic360\Helper\Image
{
    /**
     * Determine if the data has been initialized or not
     *
     * @var bool
     */
    protected $isInitialized = false;

    /**
     * Is Sirv enabled flag
     *
     * @var bool
     */
    protected $isSirvEnabled = false;

    /**
     * Initialize the data
     *
     * @return void
     */
    protected function initializeData()
    {
        $this->isInitialized = true;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $dataHelper = $objectManager->get(\Sirv\Magento2\Helper\Data::class);
        $this->isSirvEnabled = $dataHelper->isSirvEnabled();
    }
}
