<?php
declare(strict_types=1);

namespace Siteimprove\Magento\Plugin\Catalog\Model\Category;

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
                if ($storeId && !isset($this->_loadedData[$categoryId][$storeId])) {
                    $this->_loadedData[$categoryId][$storeId] = [
                        $this->_catalogHelper->getUrl($categoryId, 'category', $storeId),
                    ];
                }
                $item['_siteimprove'] = $this->_loadedData[$categoryId][$storeId];
                $item['_siteimprove_token'] = $this->_token->getToken();
            }
        }

        return $result;
    }
}
