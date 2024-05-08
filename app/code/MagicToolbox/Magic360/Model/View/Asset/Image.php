<?php

namespace MagicToolbox\Magic360\Model\View\Asset;

/**
 * Image file asset
 *
 */
class Image implements \Magento\Framework\View\Asset\LocalInterface
{
    /**
     * Image type (thumbnail, small_image, image, swatch_image, swatch_thumb)
     *
     * @var string
     */
    protected $sourceContentType;

    /**
     * File path
     *
     * @var string
     */
    protected $filePath;

    /**
     * Default image type
     *
     * @var string
     */
    protected $contentType = 'image';

    /**
     * Context interface
     *
     * @var \Magento\Framework\View\Asset\ContextInterface
     */
    protected $context;

    /**
     * Misc image params
     *
     * @var array
     */
    protected $miscParams;

    /**
     * Config interface
     *
     * @var \Magento\Catalog\Model\Product\Media\ConfigInterface
     */
    protected $mediaConfig;

    /**
     * Encryptor interface
     *
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    protected $encryptor;

    /**
     * Determine if the data has been initialized or not
     *
     * @var bool
     */
    protected static $isInitialized = false;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected static $storeManager = null;

    /**
     * Media URL format
     *
     * @var string
     */
    protected static $mediaUrlFormat = 'hash';

    /**
     * Constructor
     *
     * @param \Magento\Catalog\Model\Product\Media\ConfigInterface $mediaConfig
     * @param \Magento\Framework\View\Asset\ContextInterface $context
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     * @param string $filePath
     * @param array $miscParams
     * @return void
     */
    public function __construct(
        \Magento\Catalog\Model\Product\Media\ConfigInterface $mediaConfig,
        \Magento\Framework\View\Asset\ContextInterface $context,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        $filePath,
        array $miscParams = []
    ) {
        if (isset($miscParams['image_type'])) {
            $this->sourceContentType = $miscParams['image_type'];
            unset($miscParams['image_type']);
        } else {
            $this->sourceContentType = $this->contentType;
        }
        $this->mediaConfig = $mediaConfig;
        $this->context = $context;
        $this->filePath = $filePath;
        $this->miscParams = $miscParams;
        $this->encryptor = $encryptor;

        if (static::$isInitialized === false) {
            $this->initializeData();
        }
    }

    /**
     * Initialize the data
     *
     * @return void
     */
    protected function initializeData()
    {
        static::$isInitialized = true;
        //NOTE: this class exists since version 2.4.2
        if (class_exists('\Magento\Catalog\Model\Config\CatalogMediaConfig')) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            static::$storeManager = $objectManager->get(\Magento\Store\Model\StoreManagerInterface::class);
            $catalogMediaConfig =  $objectManager->get(\Magento\Catalog\Model\Config\CatalogMediaConfig::class);
            static::$mediaUrlFormat = $catalogMediaConfig->getMediaUrlFormat();
        }
    }

    /**
     * Get resource URL
     *
     * @return string
     */
    public function getUrl()
    {
        if (static::$mediaUrlFormat == 'hash') {
            return $this->context->getBaseUrl() . DIRECTORY_SEPARATOR . $this->getRelativePath();
        } else {
            return $this->context->getBaseUrl() . DIRECTORY_SEPARATOR .
                ltrim($this->getFilePath(), DIRECTORY_SEPARATOR) .
                '?' . http_build_query($this->getImageTransformationParameters());
        }
    }

    /**
     * Get list of transformations parameters
     *
     * @return string[]
     */
    public function getImageTransformationParameters()
    {
        return [
            'width' => $this->miscParams['image_width'],
            'height' => $this->miscParams['image_height'],
            'store' => static::$storeManager->getStore()->getCode(),
            'image-type' => $this->sourceContentType
        ];
    }

    /**
     * Get type of contents
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Get a "context" path to the asset file
     *
     * @return string
     */
    public function getPath()
    {
        return $this->context->getPath() . DIRECTORY_SEPARATOR . $this->getRelativePath();
    }

    /**
     * Get original source file
     *
     * @return string
     */
    public function getSourceFile()
    {
        return $this->mediaConfig->getBaseMediaPath() . DIRECTORY_SEPARATOR . ltrim($this->getFilePath(), DIRECTORY_SEPARATOR);
    }

    /**
     * Get source content type
     *
     * @return string
     */
    public function getSourceContentType()
    {
        return $this->sourceContentType;
    }

    /**
     * Get content of a local asset
     *
     * @return string
     */
    public function getContent()
    {
        return null;
    }

    /**
     * Get an invariant relative path to file
     *
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * Get context of the asset
     *
     * @return \Magento\Framework\View\Asset\ContextInterface
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Get the module context of file path
     *
     * @return string
     */
    public function getModule()
    {
        return 'cache';
    }

    /**
     * Retrieve part of path based on misc params
     *
     * @return string
     */
    protected function getMiscPath()
    {
        return $this->encryptor->hash(
            implode('_', $this->convertToReadableFormat($this->miscParams)),
            \Magento\Framework\Encryption\Encryptor::HASH_VERSION_MD5
        );
    }

    /**
     * Generate relative path
     *
     * @return string
     */
    protected function getRelativePath()
    {
        return preg_replace(
            '#\Q'. DIRECTORY_SEPARATOR . '\E+#',
            DIRECTORY_SEPARATOR,
            $this->getModule() . DIRECTORY_SEPARATOR . $this->getMiscPath() . DIRECTORY_SEPARATOR . $this->getFilePath()
        );
    }

    /**
     * Converting non-string values into a string representation
     *
     * @param array $params
     * @return array
     */
    protected function convertToReadableFormat($params)
    {
        if (!method_exists('\Magento\Catalog\Model\View\Asset\Image', 'convertToReadableFormat')) {
            return $params;
        }

        $params['image_height'] = 'h:' . ($params['image_height'] ?? 'empty');
        $params['image_width'] = 'w:' . ($params['image_width'] ?? 'empty');
        $params['quality'] = 'q:' . ($params['quality'] ?? 'empty');
        $params['angle'] = 'r:' . ($params['angle'] ?? 'empty');
        $params['keep_aspect_ratio'] = (!empty($params['keep_aspect_ratio']) ? '' : 'non') . 'proportional';
        $params['keep_frame'] = (!empty($params['keep_frame']) ? '' : 'no') . 'frame';
        $params['keep_transparency'] = (!empty($params['keep_transparency']) ? '' : 'no') . 'transparency';
        $params['constrain_only'] = (!empty($params['constrain_only']) ? 'do' : 'not') . 'constrainonly';
        if (!empty($params['background'])) {
            $params['background'] = 'rgb' . (is_array($params['background']) ? implode(',', $params['background']) : $params['background']);
        } else {
            $params['background'] = 'nobackground';
        }

        return $params;
    }
}
