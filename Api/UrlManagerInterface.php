<?php
declare(strict_types=1);

namespace Siteimprove\Magento\Api;

interface UrlManagerInterface
{
    /**
     * @param string $url
     * @param int    $storeId
     * @return $this
     */
    public function addUrl(string $url, int $storeId);
}
