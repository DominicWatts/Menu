<?php

namespace Xigen\Menu\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Xigen\Menu\Model\MenuFactory;

class MenuId extends Column
{
    /**
     * @var \Xigen\Menu\Model\MenuFactory
     */
    protected $menuFactory;

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
        MenuFactory $menuFactory,
        array $components = [],
        array $data = []
    ) {
        $this->menuFactory = $menuFactory;
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
                $menu = $this->menuFactory
                    ->create()
                    ->load($item['menu_id']);
                if ($menu && $menu->getTitle()) {
                    $item['menu_id'] = (string) __(
                        "%1 %2",
                        $item['menu_id'],
                        $menu->getTitle()
                    );
                }
            }
        }
        return $dataSource;
    }
}
