<?php
declare(strict_types=1);

namespace Siteimprove\Magento\Plugin\Cms\Model\Page;

use Magento\Cms\Api\Data\PageInterface;

class DataProvider
{

    /**
     * @var string[][]
     */
    protected $_loadedData = [];

    /**
     * @var \Siteimprove\Magento\Helper\Cms
     */
    protected $_cmsHelper;

    /**
     * @var \Siteimprove\Magento\Api\TokenInterface
     */
    protected $_token;

    public function __construct(
        \Siteimprove\Magento\Helper\Cms $cmsHelper,
        \Siteimprove\Magento\Api\TokenInterface $token
    ) {
        $this->_cmsHelper = $cmsHelper;
        $this->_token = $token;
    }

    /**
     * @param \Magento\Cms\Model\Page\DataProvider $subject
     * @param array $result
     * @return array
     */
    public function afterGetData(\Magento\Cms\Model\Page\DataProvider $subject, $result)
    {
        if (is_array($result)) {
            foreach ($result as & $item) {
                $pageId = (int)$item[PageInterface::PAGE_ID];
                if (!isset($this->_loadedData[$pageId])) {
                    $this->_loadedData[$pageId] = $this->_cmsHelper->getPageUrls((int)$pageId);
                }
                $item['_siteimprove'] = $this->_loadedData[$pageId];
                $item['_siteimprove_token'] = $this->_token->getToken();
            }
        }

        return $result;
    }
}
