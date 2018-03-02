<?php
declare(strict_types=1);

namespace Siteimprove\Magento\Api\Data;


interface UrlInterface
{
    public function getUrl(): string;

    public function getStoreId(): int;
}
