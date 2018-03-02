<?php
declare(strict_types=1);

namespace Siteimprove\Magento\Model;

use Siteimprove\Magento\Api\TokenInterface;

class Token implements TokenInterface
{

    /**
     * @var Flag
     */
    protected $_flag;

    public function __construct(
        Flag $flag
    ) {
        $this->_flag = $flag;
    }

    /**
     * {@inheritdoc}
     */
    public function getToken(): string
    {
        if (!$this->_flag->getFlagData()) {
            $this->_flag->loadSelf();
        }

        return $this->_flag->getFlagData() ?? '';
    }
}
