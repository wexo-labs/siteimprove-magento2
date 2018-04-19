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

    /**
     * @var \Siteimprove\Magento\Model\AfterCommitHasChanges
     */
    protected $_afterCommitHasChanges;

    /**
     * @var \Magento\Catalog\Model\Attribute\ScopeOverriddenValue
     */
    protected $_scopeOverriddenValue;

    public function __construct(
        \Siteimprove\Magento\Helper\Catalog $helper,
        \Siteimprove\Magento\Model\AfterCommitHasChanges $afterCommitHasChanges,
        \Magento\Catalog\Model\Attribute\ScopeOverriddenValue $scopeOverriddenValue
    ) {
        $this->_helper = $helper;
        $this->_afterCommitHasChanges = $afterCommitHasChanges;
        $this->_scopeOverriddenValue = $scopeOverriddenValue;
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
            $hasChanged = $this->_afterCommitHasChanges->checkProduct($product);
            if ($hasChanged) {
                $this->_helper->notifyAboutChanges((int)$product->getEntityId(), 'product', $hasChanged);
            }
        }
    }
}
