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

    public function __construct(TokenSetup $tokenSetup)
    {
        $this->_tokenSetup = $tokenSetup;
    }

    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $this->_tokenSetup->ensureTokenIsFetched();

        $setup->endSetup();
    }
}
