<?php
declare(strict_types=1);

namespace Siteimprove\Magento\Model;

use \RangeException;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Store\Api\GroupRepositoryInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\Catalog\Api\Data\EavAttributeInterface;
use Magento\Catalog\Model\Attribute\ScopeOverriddenValue;
use Magento\Catalog\Model\AbstractModel as AbstractCatalogModel;

class AfterCommitHasChanges
{
    /**
     * @var StoreRepositoryInterface
     */
    protected $_storeRepository;

    /**
     * @var GroupRepositoryInterface
     */
    protected $_groupRepository;

    /**
     * @var WebsiteRepositoryInterface
     */
    protected $_websiteRepository;

    /**
     * @var ScopeOverriddenValue
     */
    protected $_scopeOverriddenValue;

    /**
     * @param StoreRepositoryInterface $storeRepository
     * @param GroupRepositoryInterface $groupRepository
     * @param WebsiteRepositoryInterface $websiteRepository
     * @param ScopeOverriddenValue $scopeOverriddenValue
     */
    public function __construct(
        StoreRepositoryInterface $storeRepository,
        GroupRepositoryInterface $groupRepository,
        WebsiteRepositoryInterface $websiteRepository,
        ScopeOverriddenValue $scopeOverriddenValue
    ) {
        $this->_storeRepository = $storeRepository;
        $this->_groupRepository = $groupRepository;
        $this->_websiteRepository = $websiteRepository;
        $this->_scopeOverriddenValue = $scopeOverriddenValue;
    }

    /**
     * @param Product $product
     * @return int Store Ids with changes
     */
    public function checkProduct(Product $product): array
    {
        return $hasChange = $this->checkForChanges($product);
    }

    /**
     * @param Category $category
     * @return int Store Ids with changes
     */
    public function checkCategory(Category $category): array
    {
        return $hasChange = $this->checkForChanges($category, true);
    }

    /**
     * @param Product|Category $entity
     * @return int[]
     */
    private function checkForChanges(AbstractCatalogModel $entity, bool $forceCheckAllAttr = false): array
    {
        $currentStoreId = (int)$entity->getStoreId();

        $currentScopeIsGlobal = $currentStoreId === 0;
        $entityStoreIds = array_map('intval', $entity->getStoreIds());
        $websitesInfo = $this->getWebsitesInfo($entityStoreIds);

        $hasChanged = [];
        /** @var EavAttributeInterface $attribute */
        foreach ($entity->getAttributes() as $attribute) {
            if ($entity->dataHasChangedFor($attribute->getAttributeCode())) {
                if (!$forceCheckAllAttr && !$attribute->getData('is_monitored_by_siteimprove')) {
                    continue;
                }

                $storesWithAttrChange = $this->getEffectedScope(
                    $entity,
                    $attribute,
                    $currentStoreId,
                    $currentScopeIsGlobal,
                    $entityStoreIds,
                    $websitesInfo
                );

                $hasChanged = $this->mergeUnique($hasChanged, $storesWithAttrChange);

                $storesWithNoChange = array_diff($entityStoreIds, $hasChanged);
                if (!$storesWithNoChange) {
                    // All of the entities store ids is confirmed to have changes so no need to test the rest
                    break;
                }
            }
        }

        return $hasChanged;
    }

    private function getEffectedScope(
        AbstractCatalogModel $entity,
        EavAttributeInterface $attribute,
        int $currentStoreId,
        bool $currentScopeIsGlobal,
        array $storesToCheck,
        array $websitesInfo
    ): array {

        if ($entity instanceof Product) {
            $entityType = ProductInterface::class;
        } else {
            $entityType = CategoryInterface::class;
        }

        $storesWithChanges = [];
        $attrCode = $attribute->getAttributeCode();
        if ($attribute->getScope() === EavAttributeInterface::SCOPE_GLOBAL_TEXT) {
            // We know all stores have changes at this point
            $storesWithChanges = $storesToCheck;
        } elseif ($attribute->getScope() === EavAttributeInterface::SCOPE_WEBSITE_TEXT) {
            if ($currentScopeIsGlobal) {
                foreach ($websitesInfo as $info) {
                    if (!$this->_scopeOverriddenValue->containsValue(
                        $entityType,
                        $entity,
                        $attrCode,
                        $info['default_store_id']
                    )) {
                        $storesWithChanges = array_merge($storesWithChanges, $info['store_ids']);
                    }
                }
            } else {
                $storesWithChanges = $this->extractWebsiteInfo($websitesInfo, $currentStoreId)['store_ids'];
            }
        } else { // Store scope
            if ($currentScopeIsGlobal) {
                foreach ($storesToCheck as $storeId) {
                    if (!$this->_scopeOverriddenValue->containsValue(
                        $entityType,
                        $entity,
                        $attrCode,
                        $storeId
                    )) {
                        $storesWithChanges[] = $storeId;
                    }
                }
            } else {
                $storesWithChanges[] = $currentStoreId;
            }
        }

        return $storesWithChanges;
    }

    /**
     * @param int[] $storeIds
     * @return array
     */
    private function getWebsitesInfo(array $storeIds): array
    {
        $map = [];
        foreach ($storeIds as $storeId) {
            $websiteId = $this->getWebsiteId($storeId);
            if (!isset($map[$websiteId])) {
                $map[$websiteId]= [];
                $map[$websiteId]['website_id'] = $websiteId;
                $map[$websiteId]['default_store_id'] = $this->getWebsitesDefaultStoreId($websiteId);
                $map[$websiteId]['store_ids'] = [];
            }
            $map[$websiteId]['store_ids'][] = $storeId;
        }
        return $map;
    }

    /**
     * @param array $websitesInfo
     * @param int $currentStoreId
     */
    private function extractWebsiteInfo(array $websitesInfo, int $currentStoreId)
    {
        foreach ($websitesInfo as $websiteInfo) {
            if (in_array($currentStoreId, $websiteInfo['store_ids'])) {
                return $websiteInfo;
            }
        }

        throw new RangeException;
    }

    /**
     * @param int $storeId
     * @return int
     */
    private function getWebsiteId(int $storeId)
    {
        return (int)$this->_storeRepository->getById($storeId)->getWebsiteId();
    }

    /**
     * @param int $websiteId
     * @return int
     */
    private function getWebsitesDefaultStoreId(int $websiteId)
    {
        $website = $this->_websiteRepository->getById($websiteId);
        $storeGroup = $this->_groupRepository->get($website->getDefaultGroupId());
        return (int)$storeGroup->getDefaultStoreId();
    }

    /**
     * @param array $a1
     * @param array $a2
     *
     * @return array
     */
    private function mergeUnique(array $a1, array $a2)
    {
        return array_unique(array_merge($a1, $a2));
    }
}
