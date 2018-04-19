<?php
declare(strict_types=1);

namespace Siteimprove\Magento\Model;

class Sitemap extends \Magento\Sitemap\Model\Sitemap
{
    public function save()
    {
        // Do nothing
        return $this;
    }
}
