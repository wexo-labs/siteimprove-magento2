<?php
declare(strict_types=1);

namespace Siteimprove\Magento\Plugin\Catalog\Model\Category;

use Magento\Framework\Exception\NoSuchEntityException;

class DataProvider
{
    /**
     * @var string[][]
     */
    protected $_loadedData = [];

    /**
     * @var \Siteimprove\Magento\Helper\Catalog
     */
    protected $_catalogHelper;

    /**
     * @var \Siteimprove\Magento\Api\TokenInterface
     */
    protected $_token;

    public function __construct(
        \Siteimprove\Magento\Helper\Catalog $catalogHelper,
        \Siteimprove\Magento\Api\TokenInterface $token
    ) {
        $this->_catalogHelper = $catalogHelper;
        $this->_token = $token;
    }

    /**
     * @param \Magento\Cms\Model\Page\DataProvider $subject
     * @param array $result
     * @return array
     */
    public function afterGetData(\Magento\Catalog\Model\Category\DataProvider $subject, $result)
    {
        if (is_array($result)) {
            foreach ($result as $id => & $item) {
                $categoryId = (int)$id;
                if (!isset($this->_loadedData[$categoryId])) {
                    $this->_loadedData[$categoryId] = [];
                }

                $storeId = (int)$item['store_id'];

                if ($storeId) {
                    if (!isset($this->_loadedData[$categoryId][$storeId])) {
                        $this->_loadedData[$categoryId][$storeId] =
                            $this->_catalogHelper->getUrl($categoryId, 'category', $storeId);
                    }
                } else {
                    $storeIds = [];
                    try {
                        $storeIds = array_map('intval', $subject->getCurrentCategory()->getStoreIds());
                    } catch (NoSuchEntityException $e) {}

                    foreach ($storeIds as $storeId) {
                        if (isset($this->_loadedData[$categoryId][$storeId])) {
                            break;
                        }
                        if ($categoryUrl = $this->_catalogHelper->getUrl($categoryId, 'category', $storeId)) {
                            $this->_loadedData[$categoryId][$storeId] = $categoryUrl;
                            break;
                        }
                    }
                }

                $item['_siteimprove'] = $this->_loadedData[$categoryId];
                $item['_siteimprove_token'] = $this->_token->getToken();
            }
        }

        return $result;
    }
}
