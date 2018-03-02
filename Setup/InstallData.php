<?php
declare(strict_types=1);

namespace Siteimprove\Magento\Setup;

use Magento\Framework\Setup\InstallDataInterface,
    Magento\Framework\Setup\ModuleContextInterface,
    Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
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
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $this->_tokenSetup->ensureTokenIsFetched();

        $setup->endSetup();
    }
}
