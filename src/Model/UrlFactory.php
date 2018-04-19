<?php
declare(strict_types=1);

namespace Siteimprove\Magento\Model;


class UrlFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager = null;

    /**
     * Instance name to create
     *
     * @var string
     */
    protected $_instanceName = null;

    /**
     * Factory constructor
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param string $instanceName
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager, $instanceName = Url::class)
    {
        $this->_objectManager = $objectManager;
        $this->_instanceName = $instanceName;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     * @return \Siteimprove\Magento\Model\Url
     */
    public function create(string $url, int $storeId)
    {
        return $this->_objectManager->create($this->_instanceName, [
            'url'     => $url,
            'storeId' => $storeId,
        ]);
    }
}
