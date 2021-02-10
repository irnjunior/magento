<?php


namespace Magenest\Popup\Model\Source\Popup;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;
use Magento\Framework\Data\OptionSourceInterface;

class ButtonPosition extends AbstractSource implements SourceInterface, OptionSourceInterface{
    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public static function getOptionArray()
    {
        return [
            \Magenest\Popup\Model\Popup::BUTTON_CENTER => __('Center'),
            \Magenest\Popup\Model\Popup::BUTTON_BOTTOM_LEFT => __('BOTTOM LEFT'),
            \Magenest\Popup\Model\Popup::BUTTON_BOTTOM_RIGHT => __('BOTTOM RIGHT'),
        ];
    }

    /**
     * Retrieve option array with empty value
     *
     * @return string[]
     */
    public function getAllOptions()
    {
        $result = [];

        foreach (self::getOptionArray() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }

        return $result;
    }
}