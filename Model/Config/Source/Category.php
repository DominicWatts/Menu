<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Xigen\Menu\Model\Config\Source;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;
use Xigen\Menu\Helper\Data;

class Category implements OptionSourceInterface
{
    /**
     * @var CategoryCollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * @var CategoryCollection
     */
    protected $_categoryCollection;

    /**
     * Page constructor.
     * @param CategoryCollectionFactory $categoryCollectionFactory
     */
    public function __construct(
        CategoryCollectionFactory $categoryCollectionFactory
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    /**
     * Options getter
     * @return array
     */
    public function toOptionArray($addEmptyField = true)
    {
        $collection = $this->_getCategoryCollection();
        $categorys = [];

        // phpcs:disable
        if ($addEmptyField) {
            $categorys[] = [
                'value' => '',
                'label' => __(Data::PLEASE_SELECT_TEXT)
            ];
        }
        // phpcs:enable

        foreach ($collection as $category) {
            $name = $category->getId() . ' ' . $category->getName();
            $urlKey = $category->getUrlKey();

            if (isset($name) && isset($urlKey)) {
                $suffix = '';

                if (!$category->getIsActive()) {
                    $suffix = __('(Inactive)');
                }

                $categorys[] = [
                    'value' => $category->getId(),
                    'label' => __($name) . ' ' . $suffix
                ];
            }
        }

        return $categorys;
    }

    /**
     * Get options in "key-value" format
     * @return array
     */
    public function toArray($addEmptyField = true)
    {
        $collection = $this->_getCategoryCollection();
        $categorys = [];

        // phpcs:disable
        if ($addEmptyField) {
            $categorys[] = __(Data::PLEASE_SELECT_TEXT);
        }
        // phpcs:enable

        foreach ($collection as $category) {
            $name = $category->getId() . ' ' . $category->getName();
            $urlKey = $category->getUrlKey();

            if (isset($name) && isset($urlKey)) {
                $suffix = '';

                if (!$category->getIsActive()) {
                    $suffix = __('(Inactive)');
                }

                $categorys[$category->getId()] = __($name) . ' ' . $suffix;
            }
        }

        return $categorys;
    }

    /**
     * @return mixed
     */
    protected function _getCategoryCollection()
    {
        if (!$this->_categoryCollection) {
            $collection = $this->categoryCollectionFactory
                ->create()
                ->addAttributeToSelect('*');
            $this->_categoryCollection = $collection;
        }
        return $this->_categoryCollection;
    }
}
