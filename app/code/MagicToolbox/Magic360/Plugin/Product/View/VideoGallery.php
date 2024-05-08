<?php

namespace MagicToolbox\Magic360\Plugin\Product\View;

/**
 * Plugin for \Magento\ProductVideo\Block\Product\View\Gallery
 */
class VideoGallery
{
    /**
     * Helper
     *
     * @var \MagicToolbox\Magic360\Helper\Data
     */
    protected $magicToolboxHelper = null;

    /**
     * Disable flag
     * @var bool
     */
    protected $isDisabled = true;

    /**
     * Display Magic360 spin as a separate block or inside fotorama gallery
     *
     * @var bool
     */
    protected $standaloneMode = false;

    /**
     * Magic360 spin position
     *
     * @var integer
     */
    protected $spinPosition = 0;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @var \Magento\Framework\Json\DecoderInterface
     */
    protected $jsonDecoder;

    /**
     * @param \MagicToolbox\Magic360\Helper\Data $magicToolboxHelper
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\Json\DecoderInterface $jsonDecoder
     */
    public function __construct(
        \MagicToolbox\Magic360\Helper\Data $magicToolboxHelper,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Json\DecoderInterface $jsonDecoder
    ) {
        $this->magicToolboxHelper = $magicToolboxHelper;
        $toolObj = $magicToolboxHelper->getToolObj();
        $this->isDisabled = !$toolObj->params->checkValue('enable-effect', 'Yes', 'product');
        $this->standaloneMode = $toolObj->params->checkValue('display-spin', 'separately', 'product');
        $this->spinPosition = $toolObj->params->getValue('spin-position', 'product') - 1;
        $this->jsonEncoder = $jsonEncoder;
        $this->jsonDecoder = $jsonDecoder;
    }

    /**
     * Retrieve media gallery data for product options in JSON format
     *
     * @param \Magento\Catalog\Block\Product\View\Gallery $subject
     * @param string $result
     * @return string
     * @since 100.1.0
     */
    public function afterGetOptionsMediaGalleryDataJson(\Magento\Catalog\Block\Product\View\Gallery $subject, $result)
    {
        if ($this->isDisabled || $this->standaloneMode) {
            return $result;
        }

        $result = $this->jsonDecoder->decode($result);
        foreach ($result as $productId => &$mediaData) {
            if ($this->magicToolboxHelper->hasMagic360Media($productId)) {
                $magic360ItemData = [
                    'mediaType' => 'magic360',
                    'videoUrl' => null,
                    'isBase' => true,
                ];
                $data = [];
                $position = 0;
                foreach ($mediaData as $mediaItemData) {
                    if ($position == $this->spinPosition) {
                        $data[$position] = $magic360ItemData;
                        $magic360ItemData = null;
                        $position++;
                    }
                    $mediaItemData['isBase'] = false;
                    $data[$position] = $mediaItemData;
                    $position++;
                }
                if ($magic360ItemData) {
                    $data[$position] = $magic360ItemData;
                }
                $mediaData = $data;
            }
        }

        return $this->jsonEncoder->encode($result);
    }
}
