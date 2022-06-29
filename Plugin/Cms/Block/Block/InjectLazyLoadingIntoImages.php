<?php

namespace MageSuite\CmsLazyload\Plugin\Cms\Block\Block;

class InjectLazyLoadingIntoImages
{
    /**
     * @var \MageSuite\CmsLazyload\Helper\Configuration
     */
    protected $configuration;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    public function __construct(
        \MageSuite\CmsLazyload\Helper\Configuration $configuration,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->configuration = $configuration;
        $this->logger = $logger;
    }

    public function afterToHtml(\Magento\Cms\Block\Block $subject, $result)
    {
        if (!$this->configuration->isEnabled()) {
            return $result;
        }

        if (empty($result)) {
            return $result;
        }

        try {
            $dom = new \DomDocument();
            $dom->loadHTML(mb_convert_encoding('<html>' . $result . '</html>','HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        } catch (\Exception $e) {

            if ($this->configuration->isLoggerEnabled()) {
                $this->logger->warning(
                    sprintf('Issue when adjusting CMS block images to be lazy loaded. Provided HTML code is incorrect, base64 of HTML: %s', base64_encode($result))
                );
            }

            return $result;
        }

        $dom->formatOutput = false;

        $images = $dom->getElementsByTagName('img');

        /** @var \DOMElement $image */
        foreach ($images as $image) {
            $src = $image->getAttribute('src');

            if (empty($src)) {
                continue;
            }

            $loadingAttribute = $image->getAttribute('loading');

            if (!empty($loadingAttribute)) {
                continue;
            }


            $image->setAttribute('loading', 'lazy');
        }

        $newHtml = $dom->saveHTML();
        $newHtml = str_replace(['<html>', '</html>'], '', $newHtml);
        $newHtml = rtrim($newHtml, PHP_EOL);

        return $newHtml;
    }
}
