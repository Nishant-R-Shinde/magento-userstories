<?php

namespace MagicToolbox\Magic360\Block\Adminhtml\Product\Edit\Magic360\Gallery;

use Magento\Backend\Block\Media\Uploader;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;

/**
 * Magic 360 gallery content
 *
 */
class Content extends \Magento\Backend\Block\Widget
{
    /**
     * @var string
     */
    protected $_template = 'MagicToolbox_Magic360::product/edit/magic360/gallery.phtml';

    /**
     * @var \MagicToolbox\Magic360\Model\Product\Media\Config
     */
    protected $_mediaConfig;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \MagicToolbox\Magic360\Model\Product\Media\Config $mediaConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \MagicToolbox\Magic360\Model\Product\Media\Config $mediaConfig,
        array $data = []
    ) {
        $this->_jsonEncoder = $jsonEncoder;
        $this->_mediaConfig = $mediaConfig;
        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        $this->addChild('uploader', 'Magento\Backend\Block\Media\Uploader', ['template' => 'MagicToolbox_Magic360::media/uploader.phtml']);

        $mageVersion = $this->getDataHelper()->getMagentoVersion();
        if (version_compare($mageVersion, '2.3.5', '<')) {
            $url = $this->_urlBuilder->addSessionParam()->getUrl('magic360/gallery/upload');
        } else {
            $url = $this->_urlBuilder->getUrl('magic360/gallery/upload');
        }

        $this->getUploader()->getConfig()->setUrl(
            $url
        )->setFileField(
            'image'
        )->setFilters(
            [
                'images' => [
                    'label' => __('Images (.gif, .jpg, .png)'),
                    'files' => ['*.gif', '*.jpg', '*.jpeg', '*.png'],
                ],
            ]
        );
        return parent::_prepareLayout();
    }

    /**
     * Retrieve uploader block
     *
     * @return Uploader
     */
    public function getUploader()
    {
        return $this->getChildBlock('uploader');
    }

    /**
     * Retrieve uploader block html
     *
     * @return string
     */
    public function getUploaderHtml()
    {
        return $this->getChildHtml('uploader');
    }

    /**
     * @return string
     */
    public function getJsObjectName()
    {
        return $this->getHtmlId() . 'JsObject';
    }

    /**
     * @return string
     */
    public function getImagesJson()
    {
        $imagesValue = $this->getElement()->getImages();
        if (is_array($imagesValue) && count($imagesValue)) {
            $directory = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA);
            foreach ($imagesValue as &$image) {
                $image['url'] = $this->_mediaConfig->getMediaUrl($image['file']);
                try {
                    $fileHandler = $directory->stat($this->_mediaConfig->getMediaPath($image['file']));
                    $image['size'] = $fileHandler['size'];
                } catch (FileSystemException $e) {
                    $image['url'] = $this->getImageHelper()->getDefaultPlaceholderUrl('small_image');
                    $image['size'] = 0;
                    $this->_logger->warning($e);
                }
            }
            return $this->_jsonEncoder->encode($imagesValue);
        }
        return '[]';
    }

    /**
     * @return \Magento\Catalog\Helper\Image
     * @deprecated
     */
    private function getImageHelper()
    {
        static $imageHelper = null;
        if ($imageHelper === null) {
            $imageHelper = \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Magento\Catalog\Helper\Image::class);
        }
        return $imageHelper;
    }

    /**
     * Get data helper
     *
     * @return \MagicToolbox\Magic360\Helper\Data
     */
    protected function getDataHelper()
    {
        static $helper = null;

        if ($helper === null) {
            $helper = \Magento\Framework\App\ObjectManager::getInstance()->get(
                \MagicToolbox\Magic360\Helper\Data::class
            );
        }

        return $helper;
    }
}
