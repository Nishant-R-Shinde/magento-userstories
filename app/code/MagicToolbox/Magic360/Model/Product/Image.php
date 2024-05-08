<?php

namespace MagicToolbox\Magic360\Model\Product;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Magic360 image model
 *
 */
class Image extends \Magento\Catalog\Model\Product\Image
{
    /**
     * Catalog product media config
     *
     * @var \Magento\Catalog\Model\Product\Media\Config
     */
    protected $originalMediaConfig;

    /**
     * Magic360 media config
     *
     * @var \MagicToolbox\Magic360\Model\Product\Media\Config
     */
    protected $magicToolboxMediaConfig;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $rootDirectory;

    /**
     * @var \MagicToolbox\Magic360\Model\View\Asset\ImageFactory
     */
    protected $magicToolboxViewAssetImageFactory;

    /**
     * @var \Magento\Catalog\Model\View\Asset\PlaceholderFactory
     */
    protected $magicToolboxViewAssetPlaceholderFactory;

    /**
     * @var \MagicToolbox\Magic360\Model\View\Asset\Image
     */
    protected $magicToolboxImageAsset;

    /**
     * @var \Magento\Catalog\Model\Product\Image\ParamsBuilder
     */
    protected $magicToolboxParamsBuilder;

    /**
     * @var \Magento\Catalog\Model\Product\Image\SizeCache
     */
    protected $magicToolboxSizeCache;

    /**
     * @var \Magento\Framework\View\Asset\ContextInterface
     */
    protected static $magicToolboxImageAssetContext = null;

    /**
     * Whether to check the memory or not
     *
     * @var bool
     */
    protected $doCheckMemory = false;

    /**
     * Model construct for object initialization
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $configFactory = $objectManager->get(\MagicToolbox\Magic360\Model\Product\Media\ConfigFactory::class);

        $filesystem = $objectManager->get(\Magento\Framework\FilesystemFactory::class)->create();

        if (property_exists('\Magento\Catalog\Model\Product\Image', 'viewAssetImageFactory')) {
            //NOTE: for versions 2.0.x (x >= 17), 2.1.6, 2.2.x (x >= 0)
            /** @var \MagicToolbox\Magic360\Model\Product\Media\Config */
            $this->magicToolboxMediaConfig = $configFactory->create();
            $this->magicToolboxViewAssetImageFactory = $objectManager->get(
                \MagicToolbox\Magic360\Model\View\Asset\ImageFactory::class
            );
            $this->magicToolboxViewAssetPlaceholderFactory = $objectManager->get(
                \Magento\Catalog\Model\View\Asset\PlaceholderFactory::class
            );
            //NOTE: this class exists in versions: 2.1.6, 2.3.0
            if (class_exists('\Magento\Catalog\Model\Product\Image\ParamsBuilder')) {
                $this->magicToolboxParamsBuilder = $objectManager->get(
                    \Magento\Catalog\Model\Product\Image\ParamsBuilder::class
                );
            }
            //NOTE: this class exists in version 2.1.6
            if (class_exists('\Magento\Catalog\Model\Product\Image\SizeCache')) {
                $this->magicToolboxSizeCache = $objectManager->get(
                    \Magento\Catalog\Model\Product\Image\SizeCache::class
                );
            }
            if (static::$magicToolboxImageAssetContext === null) {
                /** @var \MagicToolbox\Magic360\Model\View\Asset\Image\Context */
                static::$magicToolboxImageAssetContext = $objectManager->create(
                    \MagicToolbox\Magic360\Model\View\Asset\Image\Context::class,
                    [
                        'mediaConfig' => $this->magicToolboxMediaConfig,
                        'filesystem' => $filesystem
                    ]
                );
            }
        } else {
            //NOTE: for versions 2.0.x (x < 17), 2.1.x (x != 6)
            $this->originalMediaConfig = $this->_catalogProductMediaConfig;
            /** @var \MagicToolbox\Magic360\Model\Product\Media\Config */
            $this->magicToolboxMediaConfig = $this->_catalogProductMediaConfig = $configFactory->create();
            $this->_mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
            $this->_mediaDirectory->create($this->magicToolboxMediaConfig->getBaseMediaPath());
        }
        //NOTE: we need this to copy 360 icon in any version
        $this->rootDirectory = $filesystem->getDirectoryWrite(DirectoryList::ROOT);

        $this->doCheckMemory = method_exists($this, '_checkMemory');
    }

    /**
     * Get relative watermark file path
     * or false if file not found
     *
     * @return string | bool
     */
    protected function _getWatermarkFilePath()
    {
        if (property_exists('\Magento\Catalog\Model\Product\Image', 'viewAssetImageFactory')) {
            //NOTE: for versions 2.0.x (x >= 17), 2.1.6, 2.2.x (x >= 0)
            return parent::_getWatermarkFilePath();
        }

        $filePath = false;

        if (!($file = $this->getWatermarkFile())) {
            return $filePath;
        }
        $baseDir = $this->originalMediaConfig->getBaseMediaPath();

        $candidates = [
            $baseDir . '/watermark/stores/' . $this->_storeManager->getStore()->getId() . $file,
            $baseDir . '/watermark/websites/' . $this->_storeManager->getWebsite()->getId() . $file,
            $baseDir . '/watermark/default/' . $file,
            $baseDir . '/watermark/' . $file,
        ];
        foreach ($candidates as $candidate) {
            if ($this->_mediaDirectory->isExist($candidate)) {
                $filePath = $this->_mediaDirectory->getAbsolutePath($candidate);
                break;
            }
        }
        if (!$filePath) {
            $filePath = $this->_viewFileSystem->getStaticFileName($file);
        }

        return $filePath;
    }

    /**
     * Get media directory
     *
     * @return \Magento\Framework\Filesystem\Directory\Write
     */
    public function getMediaDirectory()
    {
        return $this->_mediaDirectory ? $this->_mediaDirectory : null;
    }

    /**
     * Get root directory
     *
     * @return \Magento\Framework\Filesystem\Directory\Write
     */
    public function getRootDirectory()
    {
        return $this->rootDirectory ? $this->rootDirectory : null;
    }

    /**
     * Check if file exists
     *
     * @param string $filename
     * @return bool
     */
    public function fileExists($filename)
    {
        $baseDir = $this->magicToolboxMediaConfig->getBaseMediaPath();
        return $this->_fileExists($baseDir.$filename);
    }

    /**
     * Retrieve original image size
     * 0 - width, 1 - height
     *
     * @return int[]
     */
    public function getImageSizeArray()
    {
        if ($this->getBaseFile()) {
            if ($this->_isBaseFilePlaceholder) {
                $filename = $this->getBaseFile();
            } else {
                $filename = $this->_mediaDirectory->getAbsolutePath($this->getBaseFile());
            }
            list($imageWidth, $imageHeight) = getimagesize($filename);
        } else {
            return [null, null];
        }
        return [$imageWidth, $imageHeight];
    }

    /**
     * Set filenames for base file and new file
     *
     * @param string $file
     * @return $this
     * @throws \Exception
     */
    public function setBaseFile($file)
    {
        if (!property_exists('\Magento\Catalog\Model\Product\Image', 'viewAssetImageFactory')) {
            //NOTE: for versions 2.0.x (x < 17), 2.1.x (x != 6)
            return parent::setBaseFile($file);
        }

        //NOTE: for versions 2.0.x (x >= 17), 2.1.6, 2.2.x (x >= 0)
        $this->_isBaseFilePlaceholder = false;

        $this->magicToolboxImageAsset = $this->magicToolboxViewAssetImageFactory->create(
            [
                'mediaConfig' => $this->magicToolboxMediaConfig,
                'context' => static::$magicToolboxImageAssetContext,
                'miscParams' => $this->magicToolboxGetMiscParams(),
                'filePath' => $file,
            ]
        );

        if ($file == 'no_selection' || !$this->_fileExists($this->magicToolboxImageAsset->getSourceFile())
            || $this->doCheckMemory && !$this->_checkMemory($this->magicToolboxImageAsset->getSourceFile())
        ) {
            $this->_isBaseFilePlaceholder = true;
            $this->magicToolboxImageAsset = $this->magicToolboxViewAssetPlaceholderFactory->create(
                [
                    'type' => $this->getDestinationSubdir(),
                ]
            );
        }

        $this->_baseFile = $this->magicToolboxImageAsset->getSourceFile();

        return $this;
    }

    /**
     * Save image file
     *
     * @return $this
     */
    public function saveFile()
    {
        if (!property_exists('\Magento\Catalog\Model\Product\Image', 'viewAssetImageFactory')) {
            //NOTE: for versions 2.0.x (x < 17), 2.1.x (x != 6)
            return parent::saveFile();
        }

        //NOTE: for versions 2.0.x (x >= 17), 2.1.6, 2.2.x (x >= 0)
        if ($this->_isBaseFilePlaceholder) {
            return $this;
        }
        $filename = $this->getBaseFile() ? $this->magicToolboxImageAsset->getPath() : null;
        $this->getImageProcessor()->save($filename);
        $this->_coreFileStorageDatabase->saveFile($filename);
        if (isset($this->magicToolboxSizeCache)) {
            //NOTE: for version 2.1.6
            $this->magicToolboxSizeCache->save(
                $this->getWidth(),
                $this->getHeight(),
                $this->magicToolboxImageAsset->getPath()
            );
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        if (!property_exists('\Magento\Catalog\Model\Product\Image', 'viewAssetImageFactory')) {
            //NOTE: for versions 2.0.x (x < 17), 2.1.x (x != 6)
            return parent::getUrl();
        }
        return $this->magicToolboxImageAsset->getUrl();
    }

    /**
     * @return bool|void
     */
    public function isCached()
    {
        if (!property_exists('\Magento\Catalog\Model\Product\Image', 'viewAssetImageFactory')) {
            //NOTE: for versions 2.0.x (x < 17), 2.1.x (x != 6)
            return parent::isCached();
        }
        return file_exists($this->magicToolboxImageAsset->getPath());
    }

    /**
     * Return resized product image information
     *
     * @return array
     */
    public function getResizedImageInfo()
    {
        if (!property_exists('\Magento\Catalog\Model\Product\Image', 'viewAssetImageFactory')) {
            //NOTE: for versions 2.0.x (x < 17), 2.1.x (x != 6)
            return parent::getResizedImageInfo();
        }
        if ($this->isBaseFilePlaceholder()) {
            $image = $this->magicToolboxImageAsset->getPath();
        }

        return getimagesize($image);
    }

    /**
     * Retrieve misc params based on all image attributes
     *
     * @return array
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    private function magicToolboxGetMiscParams()
    {
        if (isset($this->magicToolboxParamsBuilder)) {
            //NOTE: for version 2.1.6, 2.3.0
            return $this->magicToolboxParamsBuilder->build(
                [
                    'type' => $this->getDestinationSubdir(),
                    'width' => $this->getWidth(),
                    'height' => $this->getHeight(),
                    'frame' => $this->_keepFrame,
                    'constrain' => $this->_constrainOnly,
                    'aspect_ratio' => $this->_keepAspectRatio,
                    'transparency' => $this->_keepTransparency,
                    'background' => $this->_backgroundColor,
                    'angle' => $this->_angle,
                    'quality' => $this->_quality
                ]
            );
        } else {
            //NOTE: for versions 2.2.x
            $miscParams = [
                'image_type' => $this->getDestinationSubdir(),
                'image_height' => $this->getHeight(),
                'image_width' => $this->getWidth(),
                'keep_aspect_ratio' => ($this->_keepAspectRatio ? '' : 'non') . 'proportional',
                'keep_frame' => ($this->_keepFrame ? '' : 'no') . 'frame',
                'keep_transparency' => ($this->_keepTransparency ? '' : 'no') . 'transparency',
                'constrain_only' => ($this->_constrainOnly ? 'do' : 'not') . 'constrainonly',
                'background' => $this->_rgbToString($this->_backgroundColor),
                'angle' => $this->_angle,
                'quality' => $this->_quality,
            ];
            if ($this->getWatermarkFile()) {
                $miscParams['watermark_file'] = $this->getWatermarkFile();
                $miscParams['watermark_image_opacity'] = $this->getWatermarkImageOpacity();
                $miscParams['watermark_position'] = $this->getWatermarkPosition();
                $miscParams['watermark_width'] = $this->getWatermarkWidth();
                $miscParams['watermark_height'] = $this->getWatermarkHeight();
            }
            return $miscParams;
        }
    }
}
