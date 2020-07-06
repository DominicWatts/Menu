<?php

namespace Xigen\Menu\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Xigen\Menu\Helper\Data;
use Xigen\Menu\Model\ResourceModel\Item\CollectionFactory as ItemCollectionFactory;
use Magento\Framework\Registry;

class Item implements OptionSourceInterface
{
    private $itemCollectionFactory;
    protected $_itemCollection;
    protected $coreRegistry;

    public function __construct(
        ItemCollectionFactory $itemCollectionFactory,
        Registry $coreRegistry
    ) {
        $this->itemCollectionFactory = $itemCollectionFactory;
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * Options getter
     * @return array
     */
    public function toOptionArray($addRootField = true)
    {
        $collection = $this->_getItemCollection();
        $items = [];

        // phpcs:disable
        if ($addRootField) {
            $items[] = [
                'value' => Data::ROOT_ID,
                'label' => __(Data::ROOT_TEXT)
            ];
        }
        // phpcs:enable

        foreach ($collection as $item) {
            $name = $item->getItemId() . ' ' . $item->getTitle();
            $urlKey = $item->getIdentifier();

            if (isset($name) && isset($urlKey)) {
                $suffix = '';

                if (!$item->getIsActive()) {
                    $suffix = __('(Inactive)');
                }

                $items[] = [
                    'value' => $item->getItemId(),
                    'label' => __($name) . ' ' . $suffix
                ];
            }
        }

        return $items;
    }

    /**
     * Get options in "key-value" format
     * @return array
     */
    public function toArray($addRootField = true)
    {
        $collection = $this->_getItemCollection();
        $items = [];

        // phpcs:disable
        if ($addRootField) {
            $items[Data::ROOT_ID] = __(Data::ROOT_TEXT);
        }
        // phpcs:enable

        foreach ($collection as $item) {
            $name = $item->getItemId() . ' ' . $item->getTitle();
            $urlKey = $item->getIdentifier();

            if (isset($name) && isset($urlKey)) {
                $suffix = '';

                if (!$item->getIsActive()) {
                    $suffix = __('(Inactive)');
                }

                $items[$item->getItemId()] = __($name) . ' ' . $suffix;
            }
        }
    }

    /**
     * @return mixed
     */
    protected function _getItemCollection()
    {
        if (!$this->_itemCollection) {
            $collection = $this->itemCollectionFactory
                ->create()
                ->addStatusFilter(Data::ENABLED);
            
            $item = $this->coreRegistry->registry('xigen_menu_item');

            if ($item && $item->getItemId()) {
                $collection->excludeCurrentItem($item);
                $collection->addMenuFilter($item);
            }

            $this->_itemCollection = $collection;
        }
        return $this->_itemCollection;
    }
}
