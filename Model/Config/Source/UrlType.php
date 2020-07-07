<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Xigen\Menu\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Xigen\Menu\Helper\Data;

class UrlType implements OptionSourceInterface
{
    /**
     * Options getter
     * @return array
     */
    public function toOptionArray()
    {
        // phpcs:disable
        return [
            ['value' => Data::CUSTOM_URL, 'label' => __(Data::CUSTOM_URL_TEXT)],
            ['value' => Data::CMS_PAGE, 'label' => __(Data::CMS_PAGE_TEXT)],
            ['value' => Data::CATEGORY, 'label' => __(Data::CATEGORY_TEXT)]
        ];
        // phpcs:enable
    }

    /**
     * Get options in "key-value" format
     * @return array
     */
    public function toArray()
    {
        // phpcs:disable
        return [
            Data::CUSTOM_URL => __(Data::CUSTOM_URL_TEXT),
            Data::CMS_PAGE => __(Data::CMS_PAGE_TEXT),
            Data::CATEGORY => __(Data::CATEGORY_TEXT)
        ];
        // phpcs:enable
    }
}
