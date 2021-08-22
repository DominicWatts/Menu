<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Xigen\Menu\Ui\Component\Listing\Column;

use Magento\Catalog\Model\CategoryFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class CategoryId extends Column
{
    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;

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
        CategoryFactory $categoryFactory,
        array $components = [],
        array $data = []
    ) {
        $this->categoryFactory = $categoryFactory;
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
                $category = $this->categoryFactory
                    ->create()
                    ->load($item['category_id']);

                $prefix = '';
                if ($category && $category->getName()) {
                    for ($count=0; $count < $category->getLevel(); $count++) { //phpcs:ignore
                        $prefix .= '-';
                    }

                    $item['category_id'] = (string) __(
                        "%1 %2 %3",
                        $prefix,
                        $item['category_id'],
                        $category->getName()
                    );
                }
            }
        }
        return $dataSource;
    }
}
