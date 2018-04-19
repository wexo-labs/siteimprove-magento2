<?php
declare(strict_types=1);

namespace Siteimprove\Magento\Observer\Catalog\Category;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class PrepareSave implements ObserverInterface
{
    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Catalog\Model\Category $category */
        $category = $observer->getData('category');
        $category->setData('process_and_notify_siteimprove_change_made', true);
    }
}
