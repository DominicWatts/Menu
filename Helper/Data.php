<?php

declare(strict_types=1);

namespace Xigen\Menu\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{

    const PLEASE_SELECT_TEXT = "-- Please Select --";
    
    const ROOT_ID = 0;
    const ROOT_TEXT = "Root";

    const ENABLED = 1;
    const DISABLED = 0;
    const ENABLED_TEXT = "Enabled";
    const DISABLED_TEXT = "Disabled";

    const SAME_WINDOW = 0;
    const SAME_WINDOW_TEXT = "Same Window";
    const NEW_WINDOW = 1;
    const NEW_WINDOW_TEXT = "New Window";

    const CUSTOM_URL = 0;
    const CUSTOM_URL_TEXT = "Custom URL";
    const CMS_PAGE = 1;
    const CMS_PAGE_TEXT = "CMS Page";
    const CATEGORY = 2;
    const CATEGORY_TEXT = "Category";
}
