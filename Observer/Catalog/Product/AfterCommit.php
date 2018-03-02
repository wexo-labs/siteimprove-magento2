<?php
declare(strict_types=1);

namespace Siteimprove\Magento\Observer\Catalog\Product;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Check if Cms page have been saved by the controller and if yes notify Siteimprove
 */
class AfterCommit implements ObserverInterface
{
    /**
     * @var \Siteimprove\Magento\Helper\Catalog
     */
    protected $_helper;

    public function __construct(
        \Siteimprove\Magento\Helper\Catalog $helper
    ) {
        $this->_helper = $helper;
    }

    /**
     * @param Observer $observer
     * @return void
     * @throws \Exception
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $observer->getData('product');
        if ($product->getData('process_and_notify_siteimprove_change_made')) {
            $this->_helper->notifyAboutChanges((int)$product->getEntityId(), 'product', $product->getStoreIds());
        }
    }
}
