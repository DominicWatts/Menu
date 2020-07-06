<?php

namespace Xigen\Menu\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Xigen\Menu\Helper\Data;

class OpenType implements OptionSourceInterface
{
    /**
     * Options getter
     * @return array
     */
    public function toOptionArray()
    {
        // phpcs:disable
        return [
            ['value' => Data::SAME_WINDOW, 'label' => __(Data::SAME_WINDOW_TEXT)],
            ['value' => Data::NEW_WINDOW, 'label' => __(Data::NEW_WINDOW_TEXT)]
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
            Data::SAME_WINDOW => __(Data::SAME_WINDOW_TEXT),
            Data::NEW_WINDOW => __(Data::NEW_WINDOW_TEXT)
        ];
        // phpcs:enable
    }
}
