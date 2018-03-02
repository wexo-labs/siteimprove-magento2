<?php
declare(strict_types=1);

namespace Siteimprove\Magento\Model;

use Magento\Backend\Model\Session,
    Siteimprove\Magento\Api\Data\UrlInterface,
    Siteimprove\Magento\Api\UrlManagerInterface;

class UrlManager implements UrlManagerInterface
{
    /**
     * @var Session
     */
    protected $_session;

    /**
     * @var UrlFactory
     */
    protected $_urlFactory;

    /**
     * @var UrlCollectionFactory
     */
    protected $_urlCollectionFactory;

    public function __construct(
        Session $session,
        UrlFactory $urlFactory,
        UrlCollectionFactory $urlCollectionFactory
    ) {
        $this->_session = $session;
        $this->_urlFactory = $urlFactory;
        $this->_urlCollectionFactory = $urlCollectionFactory;
    }

    /**
     * @return UrlCollection
     */
    protected function _getUrls(): UrlCollection
    {
        if (!$this->_session->getData('siteimprove_url_collection')) {
            $this->_session->setData('siteimprove_url_collection', $this->_urlCollectionFactory->create());
        }

        return $this->_session->getData('siteimprove_url_collection');
    }

    /**
     * @param bool $clear
     * @return UrlInterface[]
     */
    public function getUrls($clear = false): array
    {
        return $this->_getUrls()->getItems($clear);
    }


    /**
     * {@inheritdoc}
     */
    public function addUrl(string $url, int $storeId)
    {
        $url = $this->_urlFactory->create($url, $storeId);

        $this->_getUrls()->addUrl($url);

        return $this;
    }
}
