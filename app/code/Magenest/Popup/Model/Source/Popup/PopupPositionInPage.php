<?php
namespace Magenest\Popup\Model\Source\Popup;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;
use Magento\Framework\Data\OptionSourceInterface;

class PopupPositionInPage extends AbstractSource implements SourceInterface, OptionSourceInterface{
    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public static function getOptionArray()
    {
        return [
            \Magenest\Popup\Model\Popup::CENTER => __('Center'),
            \Magenest\Popup\Model\Popup::TOP_LEFT => __('Top Left'),
            \Magenest\Popup\Model\Popup::TOP_RIGHT => __('Top Right'),
            \Magenest\Popup\Model\Popup::TOP_CENTER => __('Top Center'),
            \Magenest\Popup\Model\Popup::BOTTOM_LEFT => __('Bottom Left'),
            \Magenest\Popup\Model\Popup::BOTTOM_RIGHT => __('Bottom Right'),
            \Magenest\Popup\Model\Popup::BOTTOM_CENTER => __('Bottom Center'),
            \Magenest\Popup\Model\Popup::MIDDLE_LEFT => __('Middle Left'),
            \Magenest\Popup\Model\Popup::MIDDLE_RIGHT => __('Middle Right'),
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