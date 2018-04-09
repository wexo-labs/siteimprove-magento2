<?php
declare(strict_types=1);

namespace Siteimprove\Magento\Setup;

use Magento\Catalog\Setup\CategorySetupFactory,
    Magento\Framework\Setup\InstallDataInterface,
    Magento\Framework\Setup\ModuleContextInterface,
    Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{

    /**
     * @var TokenSetup
     */
    protected $_tokenSetup;

    /**
     * @var SitemapSetup
     */
    protected $_sitemapSetup;

    /**
     * @var CategorySetupFactory
     */
    protected $_categorySetupFactory;

    public function __construct(
        TokenSetup $tokenSetup,
        SitemapSetup $sitemapSetup,
        CategorySetupFactory $categorySetupFactory
    ) {
        $this->_tokenSetup = $tokenSetup;
        $this->_sitemapSetup = $sitemapSetup;
        $this->_categorySetupFactory = $categorySetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        /** @var \Magento\Catalog\Setup\CategorySetup $catalogSetup */
        $catalogSetup = $this->_categorySetupFactory->create(['setup' => $setup]);

        $this->_tokenSetup->ensureTokenIsFetched();
        $this->_sitemapSetup->ensureSitemapsIsGenerated();

        $setup->endSetup();
    }
}
