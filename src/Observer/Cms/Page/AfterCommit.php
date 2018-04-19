<?php
declare(strict_types=1);

namespace Siteimprove\Magento\Observer\Cms\Page;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Check if Cms page have been saved by the controller and if yes notify Siteimprove
 */
class AfterCommit implements ObserverInterface
{

    const ADMIN_STORE_ID = \Magento\Store\Model\Store::DEFAULT_STORE_ID;

    /**
     * @var \Siteimprove\Magento\Helper\Cms
     */
    protected $_cmsHelper;

    /**
     * @var \Siteimprove\Magento\Api\UrlManagerInterface
     */
    protected $_urlManager;

    public function __construct(
        \Siteimprove\Magento\Helper\Cms $cmsHelper,
        \Siteimprove\Magento\Api\UrlManagerInterface $urlManager
    ) {
        $this->_cmsHelper = $cmsHelper;
        $this->_urlManager = $urlManager;
    }

    /**
     * @param Observer $observer
     * @return void
     * @throws \Exception
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Cms\Model\Page $page */
        $page = $observer->getData('object');
        if ($page->getData('process_and_notify_siteimprove_change_made')
            && (string)$page->getOrigData($page::CONTENT) !== (string)$page->getData($page::CONTENT)) {
            $urls = $this->_cmsHelper->getPageUrls((int)$page->getId(), $page->getStores());

            foreach ($urls as $storeId => $url) {
                $this->_urlManager->addUrl($url, $storeId);
            }
        }
    }
}
