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
     * @var \Magento\Catalog\Model\Attribute\ScopeOverriddenValue
     */
    protected $_scopeOverriddenValue;

    public function __construct(
        \Siteimprove\Magento\Helper\Catalog $helper,
        \Magento\Catalog\Model\Attribute\ScopeOverriddenValue $scopeOverriddenValue
    ) {
        $this->_helper = $helper;
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
            $hasChange = false;
            /** @var \Magento\Eav\Api\Data\AttributeInterface $attribute */
            foreach ($product->getAttributes() as $attribute) {
                if ($product->dataHasChangedFor($attribute->getAttributeCode())) {
                    $attrCode = $attribute->getAttributeCode();
                    if ($attrCode === 'updated_at' ||
                        $attrCode === 'quantity_and_stock_status') {
                        continue;
                    }
                    $hasChange = true;
                }
            }
            if ($hasChange) {
                $this->_helper->notifyAboutChanges((int)$product->getEntityId(), 'product', $product->getStoreIds());
            }
        }
    }
}
