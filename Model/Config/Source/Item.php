<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Xigen\Menu\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Registry;
use Xigen\Menu\Helper\Data;
use Xigen\Menu\Model\ResourceModel\Item\CollectionFactory as ItemCollectionFactory;

class Item implements OptionSourceInterface
{
    /**
     * @var ItemCollectionFactory
     */
    private $itemCollectionFactory;

    /**
     * @var ItemCollection
     */
    protected $_itemCollection;

    /**
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * Item constructor.
     * @param ItemCollectionFactory $itemCollectionFactory
     * @param Registry $coreRegistry
     */
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

        return $items;
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
