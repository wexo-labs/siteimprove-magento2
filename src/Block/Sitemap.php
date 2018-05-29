<?php
declare(strict_types=1);

namespace Siteimprove\Magento\Block;

class Sitemap extends \Magento\Backend\Block\Template
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
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Store\Api\StoreRepositoryInterface $storeRepository,
        \Siteimprove\Magento\Model\SitemapGenerator $sitemapGenerator,
        array $data = [])
    {
        parent::__construct($context, $data);
        $this->_storeRepository = $storeRepository;
        $this->_sitemapGenerator = $sitemapGenerator;
    }

    /**
     * @return string[]
     */
    public function sitemapUrls(): array
    {
        $sitemapUrls = [];
        foreach ($this->_storeRepository->getList() as $store) {
            $storeId = (int)$store->getId();
            if ($this->_sitemapGenerator->isSitemapGenerated($storeId)) {
                $sitemapUrls[$storeId] = $this->_sitemapGenerator->getSitemapUrl($storeId);
            }
        }
        return $sitemapUrls;
    }
}
