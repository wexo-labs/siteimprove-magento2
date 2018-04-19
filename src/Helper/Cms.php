<?php
declare(strict_types=1);

namespace Siteimprove\Magento\Helper;

use Magento\Framework\DB\Select;
use Magento\Framework\UrlInterface;
use Magento\Cms\Api\Data\PageInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

class Cms
{
    const ADMIN_STORE_ID = \Magento\Store\Model\Store::DEFAULT_STORE_ID;

    /**
     * @var \Magento\Cms\Model\ResourceModel\Page
     */
    protected $_pageResource;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\UrlRewrite\Model\UrlFinderInterface
     */
    protected $_urlFinder;

    /**
     * @var \Siteimprove\Magento\Api\UrlManagerInterface
     */
    protected $_urlManager;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resourceConnection;

    /**
     * @var \Magento\Framework\EntityManager\TypeResolver
     */
    protected $_typeResolver;

    /**
     * @var \Magento\Framework\EntityManager\MetadataPool
     */
    protected $_metadataPool;

    /**
     * @var \Magento\Framework\Url\ScopeResolverInterface
     */
    protected $_urlScopeResolver;

    public function __construct(
        \Magento\Cms\Model\ResourceModel\Page $pageResource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\UrlRewrite\Model\UrlFinderInterface $urlFinder,
        \Siteimprove\Magento\Api\UrlManagerInterface $urlManager,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\EntityManager\TypeResolver $typeResolver,
        \Magento\Framework\EntityManager\MetadataPool $metadataPool,
        \Magento\Framework\Url\ScopeResolverInterface $urlScopeResolver
    ) {
        $this->_pageResource = $pageResource;
        $this->_storeManager = $storeManager;
        $this->_urlFinder = $urlFinder;
        $this->_urlManager = $urlManager;
        $this->_resourceConnection = $resourceConnection;
        $this->_typeResolver = $typeResolver;
        $this->_metadataPool = $metadataPool;
        $this->_urlScopeResolver = $urlScopeResolver;
    }

    /**
     * @param int $pageId
     * @param int[]|null $storeIds
     * @return string[]
     */
    public function getPageUrls(int $pageId, array $storeIds = null)
    {
        if ($storeIds === null) {
            $storeIds = $this->lookupFrontendStoreIds($pageId);
        } else {
            $storeIds = array_map('intval', $storeIds);
            $storeIds = array_filter($storeIds, function (int $storeId) {
                return $storeId !== $this::ADMIN_STORE_ID;
            });
        }

        if (!$storeIds) {
            return [];
        }

        $urlRewrites = $this->_urlFinder->findAllByData(
            [
                UrlRewrite::STORE_ID => $storeIds,
                UrlRewrite::ENTITY_ID => $pageId,
                UrlRewrite::ENTITY_TYPE => 'cms-page',
            ]
        );

        $urls = [];
        foreach ($urlRewrites as $urlRewrite) {
            $storeId = (int)$urlRewrite->getStoreId();
            if (isset($urls[$storeId])) {
                continue;
            }
            /** @var \Magento\Framework\Url\ScopeInterface $scope */
            $scope = $this->_urlScopeResolver->getScope($urlRewrite->getStoreId());
            $urls[$storeId] = $scope->getBaseUrl(UrlInterface::URL_TYPE_LINK) . $urlRewrite->getRequestPath();
        }

        return $urls;
    }

    /**
     * @param int $pageId
     * @return int[]
     */
    public function lookupFrontendStoreIds(int $pageId): array
    {
        $storeIds = $this->_pageResource->lookupStoreIds($pageId);

        if (in_array(self::ADMIN_STORE_ID, $storeIds)) {
            $pageIdentifier = $this->_pageResource->getCmsPageIdentifierById($pageId);
            $metadata = $this->_metadataPool->getMetadata(PageInterface::class);
            $connection = $this->_resourceConnection->getConnectionByName($metadata->getEntityConnectionName());

            $cPageId = $connection->quoteIdentifier($metadata->getIdentifierField());
            $cIdentifier = $connection->quoteIdentifier(PageInterface::IDENTIFIER);

            $select = $connection->select()
                ->from(['cp' => $metadata->getEntityTable()])
                ->join(
                    ['cps' => 'cms_page_store'],
                    "cp.{$cPageId} = cps.page_id",
                    []
                )
                ->where(
                    "cp.{$cIdentifier} = ?",
                    $pageIdentifier
                )
                ->where('cps.store_id NOT IN (?)', $storeIds)
                ->reset(Select::COLUMNS)
                ->columns('cps.store_id');

            $notStoreIds = $connection->fetchCol($select);
            $allStoreIds = array_map(function (StoreInterface $store) {
                return $store->getId();
            }, $this->_storeManager->getStores(false));
            $storeIds = array_filter(
                $allStoreIds,
                function ($storeId) use ($notStoreIds) {
                    return !in_array((int)$storeId, $notStoreIds, true);
                }
            );
        }

        return array_map('intval', $storeIds);
    }
}
