<?php
declare(strict_types=1);

namespace Siteimprove\Magento\Setup;

use Magento\Catalog\Setup\CategorySetupFactory,
    Magento\Framework\Setup\InstallDataInterface,
    Magento\Framework\Setup\ModuleContextInterface,
    Magento\Framework\Setup\ModuleDataSetupInterface,
    Magento\Catalog\Api\Data\ProductAttributeInterface;

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
        $entityTypeId = ProductAttributeInterface::ENTITY_TYPE_ID;

        // General
        $catalogSetup->updateAttribute($entityTypeId, 'name',        'is_monitored_by_siteimprove', '1');
        $catalogSetup->updateAttribute($entityTypeId, 'status',      'is_monitored_by_siteimprove', '1');
        $catalogSetup->updateAttribute($entityTypeId, 'url_key',     'is_monitored_by_siteimprove', '1');
        $catalogSetup->updateAttribute($entityTypeId, 'visibility',  'is_monitored_by_siteimprove', '1');
        $catalogSetup->updateAttribute($entityTypeId, 'description', 'is_monitored_by_siteimprove', '1');

        // Meta information
        $catalogSetup->updateAttribute($entityTypeId, 'meta_title',       'is_monitored_by_siteimprove', '1');
        $catalogSetup->updateAttribute($entityTypeId, 'meta_keyword',     'is_monitored_by_siteimprove', '1');
        $catalogSetup->updateAttribute($entityTypeId, 'meta_description', 'is_monitored_by_siteimprove', '1');

        // Design
        $catalogSetup->updateAttribute($entityTypeId, 'page_layout',          'is_monitored_by_siteimprove', '1');
        $catalogSetup->updateAttribute($entityTypeId, 'custom_design',        'is_monitored_by_siteimprove', '1');
        $catalogSetup->updateAttribute($entityTypeId, 'custom_layout_update', 'is_monitored_by_siteimprove', '1');

        $this->_tokenSetup->ensureTokenIsFetched();
        $this->_sitemapSetup->ensureSitemapsIsGenerated();

        $setup->endSetup();
    }
}
