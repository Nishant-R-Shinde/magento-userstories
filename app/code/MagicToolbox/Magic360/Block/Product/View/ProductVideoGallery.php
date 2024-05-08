<?php

namespace MagicToolbox\Magic360\Block\Product\View;

class ProductVideoGallery extends \Magento\ProductVideo\Block\Product\View\Gallery
{
    /**
     * Magic360 spin position
     *
     * @var integer
     */
    protected $spinPosition = 0;

    /**
     * @var bool
     */
    protected $isProductVideoModuleDisabled;

    /**
     * @var \Magento\Framework\Json\DecoderInterface
     */
    protected $jsonDecoder;

    /**
     * Internal constructor, that is called from real constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $moduleManager = $objectManager->get(\Magento\Framework\Module\Manager::class);
        $this->isProductVideoModuleDisabled = !$moduleManager->isEnabled('Magento_ProductVideo');

        $mtHelper = $objectManager->get(\MagicToolbox\Magic360\Helper\Data::class);
        $toolObj = $mtHelper->getToolObj();
        $this->spinPosition = $toolObj->params->getValue('spin-position', 'product') - 1;

        $this->jsonDecoder = $objectManager->get(\Magento\Framework\Json\Decoder::class);
    }

    /**
     * Retrieve media gallery data in JSON format
     *
     * @return string
     */
    public function getMediaGalleryDataJson()
    {
        $data = $this->_coreRegistry->registry('magictoolbox');
        $standaloneMode = $data && isset($data['standalone-mode']) && $data['standalone-mode'];

        if (!$standaloneMode && $this->hasMagic360Media()) {
            $magic360ItemData = [
                'mediaType' => 'magic360',
                'videoUrl' => null,
                'isBase' => true,
            ];
            $data = [];
            $position = 0;
            foreach ($this->getProduct()->getMediaGalleryImages() as $itemData) {
                if ($position == $this->spinPosition) {
                    $data[$position] = $magic360ItemData;
                    $magic360ItemData = null;
                    $position++;
                }
                $data[$position] = [
                    'mediaType' => $itemData->getMediaType(),
                    'videoUrl' => $itemData->getVideoUrl(),
                    'isBase' => false,
                ];
                $position++;
            }
            if ($magic360ItemData) {
                $data[$position] = $magic360ItemData;
            }

            return $this->jsonEncoder->encode($data);
        }

        return parent::getMediaGalleryDataJson();
    }

    /**
     * Retrieve video settings data in JSON format
     *
     * @return string
     */
    public function getVideoSettingsJson()
    {
        $videoSettingData = parent::getVideoSettingsJson();
        $videoSettingData = $this->jsonDecoder->decode($videoSettingData);
        $mtConfig = [
            'enabled' => false,
        ];

        $block = $this->getProductMediaBlock();
        if ($block) {
            $mtConfig = [
                'enabled' => $block->toolObj->params->checkValue('enable-effect', 'Yes', 'product'),
            ];
        }

        $videoSettingData[] = [
            'mtConfig' => $mtConfig
        ];

        return $this->jsonEncoder->encode($videoSettingData);
    }

    /**
     * Retrieve product media block
     *
     * @return mixed
     */
    public function getProductMediaBlock()
    {
        $data = $this->_coreRegistry->registry('magictoolbox');
        if ($data && $data['blocks']['product.info.media.magic360']) {
            return $data['blocks']['product.info.media.magic360'];
        }
        return null;
    }

    /**
     * Checking for 360 media
     *
     * @return bool
     */
    public function hasMagic360Media()
    {
        static $result = null;
        if ($result === null) {
            $result = false;
            $block = $this->getProductMediaBlock();
            if ($block) {
                $images = $block->getGalleryImagesCollection();
                if ($images->count()) {
                    $result = true;
                }
            }
        }
        return $result;
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->isProductVideoModuleDisabled) {
            return '';
        }
        return parent::_toHtml();
    }

    /**
     * Processing block html after rendering
     *
     * @param   string $html
     * @return  string
     */
    protected function _afterToHtml($html)
    {
        if ($this->isProductVideoModuleDisabled) {
            return '';
        }
        return parent::_afterToHtml($html);
    }
}
