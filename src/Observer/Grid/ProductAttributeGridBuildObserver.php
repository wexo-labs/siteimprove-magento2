<?php
declare(strict_types=1);

namespace Siteimprove\Magento\Observer\Grid;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class ProductAttributeGridBuildObserver implements ObserverInterface
{
    /**
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    protected $_yesNo;

    /**
     * @param \Magento\Config\Model\Config\Source\Yesno $yesNo
     */
    public function __construct(\Magento\Config\Model\Config\Source\Yesno $yesNo)
    {
        $this->_yesNo = $yesNo;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Catalog\Block\Adminhtml\Product\Attribute\Grid $grid */
        $grid = $observer->getGrid();

        $grid->addColumnAfter(
            'is_monitored_by_siteimprove',
            [
                'header' => __('Monitored by Siteimprove'),
                'sortable' => true,
                'index' => 'is_monitored_by_siteimprove',
                'type' => 'options',
                'options' => $this->_yesNo->toArray(),
                'align' => 'center',
            ],
            'is_filterable'
        );
    }
}
