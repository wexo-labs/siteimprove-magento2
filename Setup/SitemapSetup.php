<?php
declare(strict_types=1);

namespace Siteimprove\Magento\Setup;


class SitemapSetup
{
    /**
     * @var \Magento\Store\Api\StoreRepositoryInterface
     */
    protected $_storeRepository;

    /**
     * @var \Siteimprove\Magento\Model\SitemapGenerator
     */
    protected $_sitemapGenerator;

    public function __construct(
        \Magento\Store\Api\StoreRepositoryInterface $storeRepository,
        \Siteimprove\Magento\Model\SitemapGenerator $sitemapGenerator
    ) {
        $this->_storeRepository = $storeRepository;
        $this->_sitemapGenerator = $sitemapGenerator;
    }

    /**
     * Ensure Magento have a Siteimprove Token
     */
    public function ensureSitemapsIsGenerated()
    {
        $pathSecret = 'asdf1234';
        /** @var \Magento\Store\Api\Data\StoreInterface $store */
        foreach ($this->_storeRepository->getList() as $store) {
            $storeId = (int)$store->getId();
            if ($this->_sitemapGenerator->isSitemapGenerated($pathSecret, $storeId)) {
                continue;
            }

            $this->_sitemapGenerator->getSitemapModel($pathSecret, $storeId)->generateXml();
        }
    }
}
