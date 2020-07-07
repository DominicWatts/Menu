<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Xigen\Menu\Api\Data;

interface ItemInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const ITEM_ID = 'item_id';
    const IS_ACTIVE = 'is_active';
    const PARENT_ID = 'parent_id';
    const TITLE = 'title';
    const POSITION = 'position';
    const CATEGORY_ID = 'category_id';
    const MENU_ID = 'menu_id';
    const URL = 'url';
    const CMS_PAGE_IDENTIFIER = 'cms_page_identifier';
    const URL_TYPE = 'url_type';
    const OPEN_TYPE = 'open_type';
    const IDENTIFIER = 'identifier';

    /**
     * Get item_id
     * @return string|null
     */
    public function getItemId();

    /**
     * Set item_id
     * @param string $itemId
     * @return \Xigen\Menu\Api\Data\ItemInterface
     */
    public function setItemId($itemId);

    /**
     * Get menu_id
     * @return string|null
     */
    public function getMenuId();

    /**
     * Set menu_id
     * @param string $menuId
     * @return \Xigen\Menu\Api\Data\ItemInterface
     */
    public function setMenuId($menuId);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Xigen\Menu\Api\Data\ItemExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Xigen\Menu\Api\Data\ItemExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Xigen\Menu\Api\Data\ItemExtensionInterface $extensionAttributes
    );

    /**
     * Get parent_id
     * @return string|null
     */
    public function getParentId();

    /**
     * Set parent_id
     * @param string $parentId
     * @return \Xigen\Menu\Api\Data\ItemInterface
     */
    public function setParentId($parentId);

    /**
     * Get title
     * @return string|null
     */
    public function getTitle();

    /**
     * Set title
     * @param string $title
     * @return \Xigen\Menu\Api\Data\ItemInterface
     */
    public function setTitle($title);

    /**
     * Get identifier
     * @return string|null
     */
    public function getIdentifier();

    /**
     * Set identifier
     * @param string $identifier
     * @return \Xigen\Menu\Api\Data\ItemInterface
     */
    public function setIdentifier($identifier);

    /**
     * Get url
     * @return string|null
     */
    public function getUrl();

    /**
     * Set url
     * @param string $url
     * @return \Xigen\Menu\Api\Data\ItemInterface
     */
    public function setUrl($url);

    /**
     * Get url
     * @return string|null
     */
    public function getFinalUrl();

    /**
     * Set url
     * @param string $url
     * @return \Xigen\Menu\Api\Data\ItemInterface
     */
    public function setFinalUrl($url);

    /**
     * Get open_type
     * @return string|null
     */
    public function getOpenType();

    /**
     * Set open_type
     * @param string $openType
     * @return \Xigen\Menu\Api\Data\ItemInterface
     */
    public function setOpenType($openType);

    /**
     * Get url_type
     * @return string|null
     */
    public function getUrlType();

    /**
     * Set url_type
     * @param string $urlType
     * @return \Xigen\Menu\Api\Data\ItemInterface
     */
    public function setUrlType($urlType);

    /**
     * Get cms_page_identifier
     * @return string|null
     */
    public function getCmsPageIdentifier();

    /**
     * Set cms_page_identifier
     * @param string $cmsPageIdentifier
     * @return \Xigen\Menu\Api\Data\ItemInterface
     */
    public function setCmsPageIdentifier($cmsPageIdentifier);

    /**
     * Get category_id
     * @return string|null
     */
    public function getCategoryId();

    /**
     * Set category_id
     * @param string $categoryId
     * @return \Xigen\Menu\Api\Data\ItemInterface
     */
    public function setCategoryId($categoryId);

    /**
     * Get position
     * @return string|null
     */
    public function getPosition();

    /**
     * Set position
     * @param string $position
     * @return \Xigen\Menu\Api\Data\ItemInterface
     */
    public function setPosition($position);

    /**
     * Get is_active
     * @return string|null
     */
    public function getIsActive();

    /**
     * Set is_active
     * @param string $isActive
     * @return \Xigen\Menu\Api\Data\ItemInterface
     */
    public function setIsActive($isActive);
}
