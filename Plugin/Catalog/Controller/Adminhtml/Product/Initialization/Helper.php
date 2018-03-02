<?php
declare(strict_types=1);

namespace Siteimprove\Magento\Plugin\Catalog\Controller\Adminhtml\Product\Initialization;

class Helper
{
    /**
     * @param \Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper $subject
     * @param \Magento\Catalog\Model\Product $result
     */
    public function afterInitialize(
        \Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper $subject,
        \Magento\Catalog\Model\Product $result
    ) {
        $result->setData('process_and_notify_siteimprove_change_made', true);
        return $result;
    }
}
