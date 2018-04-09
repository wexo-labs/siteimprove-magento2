<?php
declare(strict_types=1);

namespace Siteimprove\Magento\Setup;

use Magento\Framework\Setup\SchemaSetupInterface,
    Magento\Framework\Setup\InstallSchemaInterface,
    Magento\Framework\Setup\ModuleContextInterface;

class Recurring implements InstallSchemaInterface
{
    /**
     * @var TokenSetup
     */
    protected $_tokenSetup;

    /**
     * @var SitemapSetup
     */
    protected $_sitemapSetup;

    public function __construct(TokenSetup $tokenSetup, SitemapSetup $sitemapSetup)
    {
        $this->_tokenSetup = $tokenSetup;
        $this->_sitemapSetup = $sitemapSetup;
    }

    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $this->_tokenSetup->ensureTokenIsFetched();
        $this->_sitemapSetup->ensureSitemapsIsGenerated();

        $setup->endSetup();
    }
}
