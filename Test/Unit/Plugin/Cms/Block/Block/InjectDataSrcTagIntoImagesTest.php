<?php

namespace MageSuite\CmsLazyload\Test\Unit\Plugin\Cms\Block\Block;

class InjectDataSrcTagIntoImagesTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \MageSuite\CmsLazyload\Plugin\Cms\Block\Block\InjectDataSrcTagIntoImages
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

    public function setUp()
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

        $this->injecter = new \MageSuite\CmsLazyload\Plugin\Cms\Block\Block\InjectDataSrcTagIntoImages($this->configuration, $this->loggerDummy);
    }

    /**
     * @dataProvider blockContents
     */
    public function testItReplacesSrcArgumentsWithDataSrcOnes($originalHtml, $expectedHtml)
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
                '<img src="image.jpg" data-srcset="image.jpg" srcset="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" class=" lazyload">'
            ],
            [
                '<IMG src="image.jpg">',
                '<img src="image.jpg" data-srcset="image.jpg" srcset="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" class=" lazyload">'
            ],
            [
                '<IMG src="image.jpg"/>',
                '<img src="image.jpg" data-srcset="image.jpg" srcset="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" class=" lazyload">'
            ],
            [
                '<img alt="Preis sieger" src="https://www.example.com/image.jpg">',
                '<img alt="Preis sieger" src="https://www.example.com/image.jpg" data-srcset="https://www.example.com/image.jpg" srcset="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" class=" lazyload">'
            ],
            [
                '<img class="test" alt="Preis sieger" src="/image.jpg">',
                '<img class="test lazyload" alt="Preis sieger" src="/image.jpg" data-srcset="/image.jpg" srcset="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=">'
            ],
            [
                '<img alt="Preis sieger" src="image.jpg"/>',
                '<img alt="Preis sieger" src="image.jpg" data-srcset="image.jpg" srcset="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" class=" lazyload">'
            ],
            [
                '<img alt="Preis sieger" src="image.jpg"/>',
                '<img alt="Preis sieger" src="image.jpg" data-srcset="image.jpg" srcset="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" class=" lazyload">'
            ],
            [
                '<img class="" src="image.jpg">',
                '<img class=" lazyload" src="image.jpg" data-srcset="image.jpg" srcset="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=">'
            ],
            [
                '<img data-srcset="image.jpg" class="lazyload">',
                '<img data-srcset="image.jpg" class="lazyload">'
            ],
            [
                '<img class="" src="image.jpg"> <img class="" src="second.jpg">',
                '<img class=" lazyload" src="image.jpg" data-srcset="image.jpg" srcset="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="> <img class=" lazyload" src="second.jpg" data-srcset="second.jpg" srcset="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=">'
            ],
            [
                '<img data-src="image.jpg" class="lazyload">',
                '<img data-src="image.jpg" class="lazyload">'
            ],
        ];
    }
}