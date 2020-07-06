<?php

namespace Xigen\Menu\Ui\Component\Listing\Column;

use \Magento\Ui\Component\Listing\Columns\Column;
use \Magento\Framework\View\Element\UiComponent\ContextInterface;
use \Magento\Framework\View\Element\UiComponentFactory;
use \Xigen\Menu\Model\ItemFactory;


class ParentId extends Column
{
    /**
     * @var \Xigen\Menu\Model\ItemFactory
     */
    protected $itemFactory;

    /**
     * Undocumented function
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        ItemFactory $itemFactory,
        array $components = [],
        array $data = []
    ) {
        $this->itemFactory = $itemFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Undocumented function
     * @param array $dataSource
     * @return void
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $menu = $this->itemFactory
                    ->create()
                    ->load($item['parent_id']);
                if ($menu && $menu->getTitle()) {
                    $item['parent_id'] = (string) __("%1 %2",
                        $item['parent_id'],
                        $menu->getTitle()
                    );
                }
            }
        }
        return $dataSource;
    }
}
