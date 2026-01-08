<?php

namespace BroCode\ImageWebpOptimizer\Model\Converter;

use BroCode\ImageOptimizer\Model\Converter\AbstractImageConverter;
use BroCode\ImageWebpOptimizer\Api\Constants;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Psr\Log\LoggerInterface;

class WebpImageConverter extends AbstractImageConverter
{
    const CONVERTER_ID = 'webp';

    protected $imageQuality = null;

    private ScopeConfigInterface $scopeConfig;


    public function __construct(
        LoggerInterface $logger,
        ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($logger);
        $this->scopeConfig = $scopeConfig;
    }

    protected function getConversionImageExtension()
    {
        return '.' . self::CONVERTER_ID;
    }

    /**
     * @inheritDoc
     */
    public function getConverterId()
    {
        return self::CONVERTER_ID;
    }

    function doConvert($imagePath, $newFile)
    {
        if (!$this->supportsGd()) {
            $this->logger->warning('BroCode - ImageOptimizer: GD is not supported. Please install GD library to convert images to webp.');
            return false;
        }

        $imageData = file_get_contents($imagePath);
        try {
            $image = imagecreatefromstring($imageData);
            imagepalettetotruecolor($image);

            $converted = imagewebp($image, $newFile, $this->getImageQuality());
            if (!$converted) {
                $this->logger->info('BroCode - ImageOptimizer: Could not convert image to webp: ' . $imagePath);
            }
        } catch (\Exception $ex) {
            $this->logger->info('BroCode - ImageOptimizer: Could not transform/load image ' . $imagePath . ': ' . $ex->getMessage());
            return false;
        }

        return $converted;
    }

    protected function supportsGd()
    {
        return function_exists('imagewebp');
    }

    protected function getImageQuality()
    {
        if ($this->imageQuality === null) {
            $this->imageQuality = $this->scopeConfig->getValue(Constants::CONFIG_WEBP_QUALITY, 'store');
        }
        return $this->imageQuality;
    }

    protected function isEnabled()
    {
        return $this->scopeConfig->getValue(Constants::CONFIG_WEBP_ENABLED, 'store') == true;
    }
}
