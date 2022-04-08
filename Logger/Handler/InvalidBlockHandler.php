<?php

namespace MageSuite\CmsLazyload\Logger\Handler;

class InvalidBlockHandler extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * @var int
     */
    protected $loggerType = \Monolog\Logger::WARNING;

    /**
     * @var string
     */
    protected $fileName = '/var/log/invalid_blocks.log';
}
