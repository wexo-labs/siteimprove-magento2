<?php
declare(strict_types=1);

namespace Siteimprove\Magento\Ui\DataProvider\Product\Form\Modifier;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;

class SiteimproveInput implements ModifierInterface
{
    const DATA_SOURCE_DEFAULT = AbstractModifier::DATA_SOURCE_DEFAULT;

    /**
     * @var \Siteimprove\Magento\Api\TokenInterface
     */
    protected $_token;

    /**
     * @var \Magento\Catalog\Model\Locator\LocatorInterface
     */
    protected $_locator;

    /**
     * @var \Siteimprove\Magento\Helper\Catalog
     */
    protected $_catalogHelper;

    public function __construct(
        \Siteimprove\Magento\Api\TokenInterface $token,
        \Magento\Catalog\Model\Locator\LocatorInterface $locator,
        \Siteimprove\Magento\Helper\Catalog $catalogHelper
    )
    {
        $this->_token = $token;
        $this->_locator = $locator;
        $this->_catalogHelper = $catalogHelper;
    }

    /**
     * @param array $data
     * @return array
     * @since 100.1.0
     */
    public function modifyData(array $data): array
    {
        $storeId = (int)$this->_locator->getStore()->getId();
        $productId = (int)$this->_locator->getProduct()->getId();

        if (!$productId) {
            return $data;
        }

        if (!$storeId) {
            /** @var \Magento\Catalog\Model\Product $product */
            $product = $this->_locator->getProduct();
            foreach ($product->getStoreIds() as $productStoreId) {
                if ($productStoreId) { // Not admin id
                    $storeId = (int)$productStoreId;
                    break;
                }
            }

            if (!$storeId) {
                return $data;
            }
        }

        $data[$productId][self::DATA_SOURCE_DEFAULT]['_siteimprove_token'] =
            $this->_token->getToken();
        $data[$productId][self::DATA_SOURCE_DEFAULT]['_siteimprove_url'] =
            $this->_catalogHelper->getUrl($productId, 'product', $storeId);

        return $data;
    }

    /**
     * @param array $meta
     * @return array
     * @since 100.1.0
     */
    public function modifyMeta(array $meta): array
    {
        return $meta;
    }
}
