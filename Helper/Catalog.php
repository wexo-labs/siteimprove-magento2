<?php
declare(strict_types=1);

namespace Siteimprove\Magento\Helper;

use Magento\Framework\UrlInterface;
use Magento\Framework\Exception\InputException;

class Catalog
{

    const ADMIN_STORE_ID = \Magento\Store\Model\Store::DEFAULT_STORE_ID;

    /**
     * @var \Siteimprove\Magento\Api\UrlManagerInterface
     */
    protected $_urlManager;

    /**
     * @var \Magento\Framework\Url\ScopeResolverInterface
     */
    protected $_urlScopeResolver;

    /**
     * @var \Magento\CatalogUrlRewrite\Model\Map\UrlRewriteFinder
     */
    protected $_urlRewriteFinder;

    public function __construct(
        \Siteimprove\Magento\Api\UrlManagerInterface $urlManager,
        \Magento\Framework\Url\ScopeResolverInterface $urlScopeResolver,
        \Magento\CatalogUrlRewrite\Model\Map\UrlRewriteFinder $urlRewriteFinder
    ) {
        $this->_urlManager = $urlManager;
        $this->_urlScopeResolver = $urlScopeResolver;
        $this->_urlRewriteFinder = $urlRewriteFinder;
    }

    /**
     * @param $type
     * @return string
     * @throws InputException
     */
    public function urlEntityTYpe($type)
    {
        switch ($type) {
            case 'product':
                return $this->_urlRewriteFinder::ENTITY_TYPE_PRODUCT;
            case 'category':
                return $this->_urlRewriteFinder::ENTITY_TYPE_CATEGORY;
            default:
                throw new InputException(__('Invalid url entity type'));
        }
    }

    /**
     * @param int    $entityId
     * @param string $type
     * @param int[]  $storeIds
     * @throws InputException
     */
    public function notifyAboutChanges(int $entityId, string $type, array $storeIds)
    {
        foreach ($storeIds as $storeId) {
            $storeId = (int)$storeId;
            $url = $this->getUrl($entityId, $type, $storeId);

            if (!$url) {
                continue;
            }

            $this->_urlManager->addUrl($url, $storeId);
        }
    }

    /**
     * @param int $entityId
     * @param string $type
     * @param int $storeId
     * @return string|null
     * @throws InputException
     */
    public function getUrl(int $entityId, string $type, int $storeId)
    {
        $urlEntityType = $this->urlEntityTYpe($type);
        $urlRewrites = $this->_urlRewriteFinder->findAllByData(
            $entityId,
            $storeId,
            $urlEntityType
        );

        /** @var null|\Magento\UrlRewrite\Service\V1\Data\UrlRewrite $urlRewrite */
        $urlRewrite = array_shift($urlRewrites);
        if (!$urlRewrite) {
            return null;
        }

        /** @var \Magento\Framework\Url\ScopeInterface $scope */
        $scope = $this->_urlScopeResolver->getScope($urlRewrite->getStoreId());
        return $scope->getBaseUrl(UrlInterface::URL_TYPE_LINK) . $urlRewrite->getRequestPath();
    }
}
