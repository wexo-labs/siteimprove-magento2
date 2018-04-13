<?php
declare(strict_types=1);

namespace Siteimprove\Magento\Observer\Catalog\Category;

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

    public function __construct(
        \Siteimprove\Magento\Helper\Catalog $helper,
        \Siteimprove\Magento\Model\AfterCommitHasChanges $afterCommitHasChanges
    ) {
        $this->_helper = $helper;
        $this->_afterCommitHasChanges = $afterCommitHasChanges;
    }

    /**
     * @param Observer $observer
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Catalog\Model\Category $category */
        $category = $observer->getData('category');
        if ($category->getData('process_and_notify_siteimprove_change_made')) {
            $hasChanged = $this->_afterCommitHasChanges->checkCategory($category);
            if ($hasChanged) {
                $this->_helper->notifyAboutChanges((int)$category->getEntityId(), 'category', $hasChanged);
            }
        }
    }
}
