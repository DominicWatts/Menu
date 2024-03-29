# Menu Builder

[![Coding Standards](https://github.com/DominicWatts/Menu/actions/workflows/standards.yml/badge.svg)](https://github.com/DominicWatts/Menu/actions/workflows/standards.yml)

Manage menu in backend

## Release Notes

1.1.0 switch to declarative schema

## Install Instructions

`composer require dominicwatts/menu`

`php bin/magento setup:upgrade`

`php bin/magento setup:di:compile`

`php bin/magento setup:static-content:deploy`

## Useage Instructions

    Admin > Marketing > Menu

## Templating

    layout/default.xml

```xml
<?xml version="1.0"?>
<page xmlns:xsi="http://www.w3.org/2001/XMLSchemainstance" xsi:noNamespaceSchemaLocation="urn:magento:framework:View/Layout/etc/page_configuration.xsd">
    <body>
        <!-- <referenceBlock name="catalog.topnav" remove="true"/> -->
        <referenceBlock name="store.menu">
            <block class="Xigen\Menu\Block\Menu" name="catalog.topnav" template="Xigen_Menu::html/{MENU_TEMPLATE_GOES_HERE}.phtml">
                <arguments>
                    <argument name="identifier" xsi:type="string">{MENU_ID_GOES_HERE}</argument>
                </arguments>
            </block>
        </referenceBlock>
    </body>
</page>
```

    html/default.phtml

This template uses magento built in block logic. Most of the markup can be found within Xigen\Menu\Block\Menu

    html/bespoke.phtml

This template demonstrates how to built a custom menu with custom markup structure
    