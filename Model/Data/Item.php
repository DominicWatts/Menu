<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Xigen\Menu\Model\Data;

use Xigen\Menu\Api\Data\ItemInterface;

class Item extends \Magento\Framework\Api\AbstractExtensibleObject implements ItemInterface
{
    /**
     * Get item_id
     * @return string|null
     */
    public function getItemId()
    {
        return $this->_get(self::ITEM_ID);
    }

    /**
     * Set item_id
     * @param string $itemId
     * @return \Xigen\Menu\Api\Data\ItemInterface
     */
    public function setItemId($itemId)
    {
        return $this->setData(self::ITEM_ID, $itemId);
    }

    /**
     * Get menu_id
     * @return string|null
     */
    public function getMenuId()
    {
        return $this->_get(self::MENU_ID);
    }

    /**
     * Set menu_id
     * @param string $menuId
     * @return \Xigen\Menu\Api\Data\ItemInterface
     */
    public function setMenuId($menuId)
    {
        return $this->setData(self::MENU_ID, $menuId);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Xigen\Menu\Api\Data\ItemExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Xigen\Menu\Api\Data\ItemExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Xigen\Menu\Api\Data\ItemExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get parent_id
     * @return string|null
     */
    public function getParentId()
    {
        return $this->_get(self::PARENT_ID);
    }

    /**
     * Set parent_id
     * @param string $parentId
     * @return \Xigen\Menu\Api\Data\ItemInterface
     */
    public function setParentId($parentId)
    {
        return $this->setData(self::PARENT_ID, $parentId);
    }

    /**
     * Get title
     * @return string|null
     */
    public function getTitle()
    {
        return $this->_get(self::TITLE);
    }

    /**
     * Set title
     * @param string $title
     * @return \Xigen\Menu\Api\Data\ItemInterface
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * Get identifier
     * @return string|null
     */
    public function getIdentifier()
    {
        return $this->_get(self::IDENTIFIER);
    }

    /**
     * Set identifier
     * @param string $identifier
     * @return \Xigen\Menu\Api\Data\ItemInterface
     */
    public function setIdentifier($identifier)
    {
        return $this->setData(self::IDENTIFIER, $identifier);
    }

    /**
     * Get url
     * @return string|null
     */
    public function getUrl()
    {
        return $this->_get(self::URL);
    }

    /**
     * Set url
     * @param string $url
     * @return \Xigen\Menu\Api\Data\ItemInterface
     */
    public function setUrl($url)
    {
        return $this->setData(self::URL, $url);
    }

    /**
     * Get open_type
     * @return string|null
     */
    public function getOpenType()
    {
        return $this->_get(self::OPEN_TYPE);
    }

    /**
     * Set open_type
     * @param string $openType
     * @return \Xigen\Menu\Api\Data\ItemInterface
     */
    public function setOpenType($openType)
    {
        return $this->setData(self::OPEN_TYPE, $openType);
    }

    /**
     * Get url_type
     * @return string|null
     */
    public function getUrlType()
    {
        return $this->_get(self::URL_TYPE);
    }

    /**
     * Set url_type
     * @param string $urlType
     * @return \Xigen\Menu\Api\Data\ItemInterface
     */
    public function setUrlType($urlType)
    {
        return $this->setData(self::URL_TYPE, $urlType);
    }

    /**
     * Get cms_page_identifier
     * @return string|null
     */
    public function getCmsPageIdentifier()
    {
        return $this->_get(self::CMS_PAGE_IDENTIFIER);
    }

    /**
     * Set cms_page_identifier
     * @param string $cmsPageIdentifier
     * @return \Xigen\Menu\Api\Data\ItemInterface
     */
    public function setCmsPageIdentifier($cmsPageIdentifier)
    {
        return $this->setData(self::CMS_PAGE_IDENTIFIER, $cmsPageIdentifier);
    }

    /**
     * Get category_id
     * @return string|null
     */
    public function getCategoryId()
    {
        return $this->_get(self::CATEGORY_ID);
    }

    /**
     * Set category_id
     * @param string $categoryId
     * @return \Xigen\Menu\Api\Data\ItemInterface
     */
    public function setCategoryId($categoryId)
    {
        return $this->setData(self::CATEGORY_ID, $categoryId);
    }

    /**
     * Get position
     * @return string|null
     */
    public function getPosition()
    {
        return $this->_get(self::POSITION);
    }

    /**
     * Set position
     * @param string $position
     * @return \Xigen\Menu\Api\Data\ItemInterface
     */
    public function setPosition($position)
    {
        return $this->setData(self::POSITION, $position);
    }

    /**
     * Get is_active
     * @return string|null
     */
    public function getIsActive()
    {
        return $this->_get(self::IS_ACTIVE);
    }

    /**
     * Set is_active
     * @param string $isActive
     * @return \Xigen\Menu\Api\Data\ItemInterface
     */
    public function setIsActive($isActive)
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }

    /**
     * Get side_image
     * @return string|null
     */
    public function getSideImage()
    {
        return $this->_get(self::SIDE_IMAGE);
    }

    /**
     * Set side_image
     * @param string $sideImage
     * @return \Xigen\Menu\Api\Data\ItemInterface
     */
    public function setSideImage($sideImage)
    {
        return $this->setData(self::SIDE_IMAGE, $sideImage);
    }

    /**
     * Get side_url
     * @return string|null
     */
    public function getSideUrl()
    {
        return $this->_get(self::SIDE_URL);
    }

    /**
     * Set side_url
     * @param string $sideUrl
     * @return \Xigen\Menu\Api\Data\ItemInterface
     */
    public function setSideUrl($sideUrl)
    {
        return $this->setData(self::SIDE_URL, $sideUrl);
    }
}
