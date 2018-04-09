<?php
declare(strict_types=1);

namespace Siteimprove\Magento\Setup;

use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var CategorySetupFactory
     */
    protected $_categorySetupFactory;

    public function __construct(CategorySetupFactory $categorySetupFactory) {
        $this->_categorySetupFactory = $categorySetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $installer->getConnection()->addColumn(
            $installer->getTable('catalog_eav_attribute'),
            'is_monitored_by_siteimprove',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'unsigned' => true,
                'nullable' => false,
                'default' => '1',
                'comment' => 'Is Monitored By Siteimprove',
            ]
        );

        $installer->endSetup();
    }
}
