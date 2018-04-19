<?php
declare(strict_types=1);

namespace Siteimprove\Magento\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class AddFieldToAttributeObserver implements ObserverInterface
{
    /**
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    protected $_yesNo;

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
        /** @var \Magento\Framework\Data\Form $form */
        $form = $observer->getForm();
        $fieldset = $form->getElement('advanced_fieldset');
        $yesnoSource = $this->_yesNo->toOptionArray();
        $fieldset->addField(
            'is_monitored_by_siteimprove',
            'select',
            [
                'name' => 'is_monitored_by_siteimprove',
                'label' => __('Is Monitored by Siteimprove'),
                'title' => __('Is Monitored by Siteimprove'),
                'values' => $yesnoSource,
            ],
            'is_filterable'
        );
    }
}
