<?php
declare(strict_types=1);

namespace Siteimprove\Magento\Api;

interface TokenInterface {

    /**
     * @return string Siteimprove Token or empty string
     */
    public function getToken(): string;
}
