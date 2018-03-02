<?php
declare(strict_types=1);

namespace Siteimprove\Magento\Block;

use Siteimprove\Magento\Model\UrlManager;
use Siteimprove\Magento\Api\TokenInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Api\StoreConfigManagerInterface;

class Overlay extends \Magento\Backend\Block\Template
{

    /**
     * @var StoreConfigManagerInterface
     */
    protected $_storeConfigManager;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var UrlManager
     */
    protected $_urlManager;

    /**
     * @var TokenInterface
     */
    protected $_token;

    public function __construct(
        StoreConfigManagerInterface $storeConfigManager,
        StoreManagerInterface $storeManager,
        UrlManager $urlManager,
        TokenInterface $token,
        Context $context,
        array $data = []
    ) {
        $this->_storeConfigManager = $storeConfigManager;
        $this->_storeManager = $storeManager;
        $this->_urlManager = $urlManager;
        $this->_token = $token;
        parent::__construct($context, $data);
    }

    public function getToken()
    {
        return $this->_token->getToken();
    }

    /**
     * @return string
     */
    public function getDefaultDomain(): string
    {
        if ($this->_storeManager->isSingleStoreMode()) {
            $defaultStore = $this->_storeManager->getDefaultStoreView();
            /** @var \Magento\Store\Api\Data\StoreConfigInterface $storeConfig */
            $storeConfigs = $this->_storeConfigManager->getStoreConfigs([$defaultStore->getCode()]);
            $storeConfig = reset($storeConfigs);
            return $storeConfig->getBaseUrl();
        }

        return '';
    }

    /**
     * @return \Siteimprove\Magento\Api\Data\UrlInterface[]
     */
    public function getUrls(): array
    {
        return $this->_urlManager->getUrls(true);
    }
}
