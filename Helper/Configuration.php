<?php

namespace MageSuite\CmsLazyload\Helper;

class Configuration
{
    const ENABLED_XML_PATH = 'web/image_urls_processing/lazy_load_images_in_cms_blocks';
    const LOGGER_ENABLED_XML_PATH = 'web/image_urls_processing/lazy_load_images_in_cms_blocks_logger_enabled';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function isEnabled()
    {
        return $this->scopeConfig->getValue(self::ENABLED_XML_PATH);
    }

    public function isLoggerEnabled()
    {
        return $this->scopeConfig->isSetFlag(self::LOGGER_ENABLED_XML_PATH);
    }
}
