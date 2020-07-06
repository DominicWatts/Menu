<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Xigen\Menu\Api\Data;

interface MenuInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const TITLE = 'title';
    const IS_ACTIVE = 'is_active';
    const MENU_ID = 'menu_id';
    const CSS_CLASS = 'css_class';
    const IDENTIFIER = 'identifier';

    /**
     * Get menu_id
     * @return string|null
     */
    public function getMenuId();

    /**
     * Set menu_id
     * @param string $menuId
     * @return \Xigen\Menu\Api\Data\MenuInterface
     */
    public function setMenuId($menuId);

    /**
     * Get identifier
     * @return string|null
     */
    public function getIdentifier();

    /**
     * Set identifier
     * @param string $identifier
     * @return \Xigen\Menu\Api\Data\MenuInterface
     */
    public function setIdentifier($identifier);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Xigen\Menu\Api\Data\MenuExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Xigen\Menu\Api\Data\MenuExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Xigen\Menu\Api\Data\MenuExtensionInterface $extensionAttributes
    );

    /**
     * Get title
     * @return string|null
     */
    public function getTitle();

    /**
     * Set title
     * @param string $title
     * @return \Xigen\Menu\Api\Data\MenuInterface
     */
    public function setTitle($title);

    /**
     * Get css_class
     * @return string|null
     */
    public function getCssClass();

    /**
     * Set css_class
     * @param string $cssClass
     * @return \Xigen\Menu\Api\Data\MenuInterface
     */
    public function setCssClass($cssClass);

    /**
     * Get is_active
     * @return string|null
     */
    public function getIsActive();

    /**
     * Set is_active
     * @param string $isActive
     * @return \Xigen\Menu\Api\Data\MenuInterface
     */
    public function setIsActive($isActive);
}

