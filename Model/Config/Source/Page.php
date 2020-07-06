<?php

namespace Xigen\Menu\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Xigen\Menu\Helper\Data;
use Magento\Cms\Model\ResourceModel\Page\CollectionFactory as PageCollectionFactory;

class Page implements OptionSourceInterface
{
    private $pageCollectionFactory;
    protected $_pageCollection;

    public function __construct(
        PageCollectionFactory $pageCollectionFactory
    ) {
        $this->pageCollectionFactory = $pageCollectionFactory;
    }

    /**
     * Options getter
     * @return array
     */
    public function toOptionArray($addEmptyField = true)
    {
        $collection = $this->_getPageCollection();
        $pages = [];

        // phpcs:disable
        if ($addEmptyField) {
            $pages[] = [
                'value' => '',
                'label' => __(Data::PLEASE_SELECT_TEXT)
            ];
        }
        // phpcs:enable

        foreach ($collection as $page) {
            $name = $page->getPageId() . ' ' . $page->getTitle();
            $urlKey = $page->getIdentifier();

            if (isset($name) && isset($urlKey)) {
                $suffix = '';

                if (!$page->getIsActive()) {
                    $suffix = __('(Inactive)');
                }

                $pages[] = [
                    'value' => $page->getPageId(),
                    'label' => __($name) . ' ' . $suffix
                ];
            }
        }

        return $pages;
    }

    /**
     * Get options in "key-value" format
     * @return array
     */
    public function toArray($addEmptyField = true)
    {
        $collection = $this->_getPageCollection();
        $pages = [];

        // phpcs:disable
        if ($addEmptyField) {
            $pages[] = __(Data::PLEASE_SELECT_TEXT);
        }
        // phpcs:enable

        foreach ($collection as $page) {
            $name = $page->getPageId() . ' ' . $page->getTitle();
            $urlKey = $page->getIdentifier();

            if (isset($name) && isset($urlKey)) {
                $suffix = '';

                if (!$page->getIsActive()) {
                    $suffix = __('(Inactive)');
                }

                $pages[$page->getPageId()] = __($name) . ' ' . $suffix;
            }
        }
    }

    /**
     * @return mixed
     */
    protected function _getPageCollection()
    {
        if (!$this->_pageCollection) {
            $collection = $this->pageCollectionFactory
                ->create();
            $this->_pageCollection = $collection;
        }
        return $this->_pageCollection;
    }
}
