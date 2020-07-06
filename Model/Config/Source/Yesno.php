<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Xigen\Menu\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Xigen\Menu\Helper\Data;

class Yesno implements OptionSourceInterface
{
    /**
     * Options getter
     * @return array
     */
    public function toOptionArray()
    {
        // phpcs:disable
        return [
            ['value' => Data::ENABLED, 'label' => __(Data::ENABLED_TEXT)],
            ['value' => Data::DISABLED, 'label' => __(Data::DISABLED_TEXT)]
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
            Data::DISABLED => __(Data::ENABLED_TEXT),
            Data::ENABLED => __(Data::DISABLED_TEXT)
        ];
        // phpcs:enable
    }
}
