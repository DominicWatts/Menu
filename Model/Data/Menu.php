<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Xigen\Menu\Model\Data;

use Xigen\Menu\Api\Data\MenuInterface;

class Menu extends \Magento\Framework\Api\AbstractExtensibleObject implements MenuInterface
{

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
     * @return \Xigen\Menu\Api\Data\MenuInterface
     */
    public function setMenuId($menuId)
    {
        return $this->setData(self::MENU_ID, $menuId);
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
     * @return \Xigen\Menu\Api\Data\MenuInterface
     */
    public function setIdentifier($identifier)
    {
        return $this->setData(self::IDENTIFIER, $identifier);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Xigen\Menu\Api\Data\MenuExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Xigen\Menu\Api\Data\MenuExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Xigen\Menu\Api\Data\MenuExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
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
     * @return \Xigen\Menu\Api\Data\MenuInterface
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * Get css_class
     * @return string|null
     */
    public function getCssClass()
    {
        return $this->_get(self::CSS_CLASS);
    }

    /**
     * Set css_class
     * @param string $cssClass
     * @return \Xigen\Menu\Api\Data\MenuInterface
     */
    public function setCssClass($cssClass)
    {
        return $this->setData(self::CSS_CLASS, $cssClass);
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
     * @return \Xigen\Menu\Api\Data\MenuInterface
     */
    public function setIsActive($isActive)
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }
}

