<?php

namespace MageSuite\CmsLazyload\Test\Unit\Plugin\Cms\Block\Block;

class InjectLazyLoadingIntoImagesTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \MageSuite\CmsLazyload\Plugin\Cms\Block\Block\InjectLazyLoadingIntoImages
     */
    protected $injecter;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $configuration;

    /**
     * @var \Magento\Cms\Block\Block|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $blockDummy;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $loggerDummy;

    public function setUp(): void
    {
        $this->objectManager = \Magento\TestFramework\ObjectManager::getInstance();

        $this->configuration = $this->getMockBuilder(\MageSuite\CmsLazyload\Helper\Configuration::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->blockDummy = $this->getMockBuilder(\Magento\Cms\Block\Block::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->loggerDummy = $this->getMockBuilder(\Psr\Log\LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->injecter = new \MageSuite\CmsLazyload\Plugin\Cms\Block\Block\InjectLazyLoadingIntoImages($this->configuration, $this->loggerDummy);
    }

    /**
     * @dataProvider blockContents 
     */
    public function testItAddsLazyLoadingAttribute($originalHtml, $expectedHtml)
    {
        $this->configuration->method('isEnabled')
            ->willReturn(true);

        $resultHtml = $this->injecter->afterToHtml($this->blockDummy, $originalHtml);

        $this->assertEquals($expectedHtml, $resultHtml);
    }

    public function testItDoesNothingWhenDisabled()
    {
        $this->configuration->method('isEnabled')
            ->willReturn(false);

        $resultHtml = $this->injecter->afterToHtml($this->blockDummy, '<img src="image.jpg" />');

        $this->assertEquals('<img src="image.jpg" />', $resultHtml);
    }

    public static function blockContents()
    {
        return [
            [
                '<img src="image.jpg">',
                '<img src="image.jpg" loading="lazy">'
            ],
            [
                '<IMG src="image.jpg">',
                '<img src="image.jpg" loading="lazy">'
            ],
            [
                '<IMG src="image.jpg"/>',
                '<img src="image.jpg" loading="lazy">'
            ],
            [
                '<img alt="Preis sieger" src="https://www.example.com/image.jpg">',
                '<img alt="Preis sieger" src="https://www.example.com/image.jpg" loading="lazy">'
            ],
            [
                '<img class="test" alt="Preis sieger" src="/image.jpg">',
                '<img class="test" alt="Preis sieger" src="/image.jpg" loading="lazy">'
            ],
            [
                '<img alt="Preis sieger" src="image.jpg"/>',
                '<img alt="Preis sieger" src="image.jpg" loading="lazy">'
            ],
            [
                '<img class="" src="image.jpg">',
                '<img class="" src="image.jpg" loading="lazy">'
            ],
            [
                '<img class="" src="image.jpg" loading="eager">',
                '<img class="" src="image.jpg" loading="eager">'
            ],
            [
                '<img class="" src="image.jpg" loading="lazy">',
                '<img class="" src="image.jpg" loading="lazy">'
            ],
            [
                '<img data-srcset="image.jpg" class="lazyload">',
                '<img data-srcset="image.jpg" class="lazyload">'
            ],
            [
                '<img class="" src="image.jpg"> <img class="" src="second.jpg">',
                '<img class="" src="image.jpg" loading="lazy"> <img class="" src="second.jpg" loading="lazy">'
            ],
            [
                '<p>Umlaut test:Sofortüberweisung</p>',
                '<p>Umlaut test:Sofort&uuml;berweisung</p>'
            ]
        ];
    }
}
