<?php
declare(strict_types=1);

namespace Siteimprove\Magento\Observer\Cms\Page;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Observer for when the Cms controller prepares a Cms Page for being saved
 */
class PrepareSaveObserver implements ObserverInterface
{

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Cms\Model\Page $page */
        $page = $observer->getData('page');
        $page->setData('process_and_notify_siteimprove_change_made', true);
    }
}
