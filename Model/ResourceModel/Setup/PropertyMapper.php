<?php
declare(strict_types=1);

namespace Siteimprove\Magento\Model\ResourceModel\Setup;

use Magento\Eav\Model\Entity\Setup\PropertyMapperAbstract;

class PropertyMapper extends PropertyMapperAbstract
{

    /**
     * {@inheritdoc}
     */
    public function map(array $input, $entityTypeId)
    {
        return [
            'is_monitored_by_siteimprove' => $this->_getValue($input, 'monitored_by_siteimprove', 0),
        ];
    }
}
