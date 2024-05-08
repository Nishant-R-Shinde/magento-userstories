<?php

namespace MagicToolbox\Magic360\Helper;

/**
 * Generator class helps in generating other classes during compilation.
 *
 */
class Generator
{
    /**
     * Constructor
     *
     * @param \MagicToolbox\Magic360\Model\Product\ImageFactory $productImageFactory
     * @param \MagicToolbox\Magic360\Model\Product\Media\ConfigFactory $configFactory
     * @param \Magento\Framework\FilesystemFactory $filesystemFactory
     * @param \MagicToolbox\Magic360\Model\View\Asset\ImageFactory $assetImageFactory
     */
    public function __construct(
        \MagicToolbox\Magic360\Model\Product\ImageFactory $productImageFactory,
        \MagicToolbox\Magic360\Model\Product\Media\ConfigFactory $configFactory,
        \Magento\Framework\FilesystemFactory $filesystemFactory,
        \MagicToolbox\Magic360\Model\View\Asset\ImageFactory $assetImageFactory
    ) {
        //NOTE: during compilation the Magento compiler will create all the necessary files in the 'generated' folder
    }
}
