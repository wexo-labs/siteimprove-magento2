<?php
declare(strict_types=1);

namespace Siteimprove\Magento\Model;

use Siteimprove\Magento\Api\Data\UrlInterface;

class Url implements UrlInterface
{
    protected $_url;

    protected $_storeId;

    public function __construct(string $url, int $storeId)
    {
        $this->_url = $url;
        $this->_storeId = $storeId;
    }

    public function getUrl(): string
    {
        return $this->_url;
    }

    public function getStoreId(): int
    {
        return $this->_storeId;
    }
}
