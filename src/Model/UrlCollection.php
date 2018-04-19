<?php
declare(strict_types=1);

namespace Siteimprove\Magento\Model;

use Iterator,
    Siteimprove\Magento\Api\Data\UrlInterface;

class UrlCollection implements Iterator
{
    /**
     * @return UrlInterface[]
     */
    protected $_items = [];

    /**
     * @param UrlInterface $url
     */
    public function addUrl(UrlInterface $url)
    {
        $this->_items[] = $url;
    }

    /**
     * @param bool $clear
     * @return UrlInterface[]
     */
    public function getItems($clear = false): array
    {
        $items = $this->_items;
        if ($clear) {
            $this->_items = [];
        }
        return $items;
    }

    public function clear()
    {
        $this->_items = [];
    }

    /**
     * {@inheritdoc}
     */
    public function current(): UrlInterface
    {
        return current($this->_items);
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        next($this->_items);
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return key($this->_items);
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return (bool)current($this->_items);
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        reset($this->_items);
    }
}
