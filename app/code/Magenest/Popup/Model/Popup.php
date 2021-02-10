<?php
namespace Magenest\Popup\Model;

class Popup extends \Magento\Framework\Model\AbstractModel{
    // popup type
    const YESNO_BUTTON = 1;
    const CONTACT_FORM = 2;
    const SHARE_SOCIAL = 3;
    const SUBCRIBE = 4;
    const STATIC_POPUP = 5;
    const HOT_DEAL = 6;

    //popup status
    const ENABLE = 1;
    const DISABLE = 0;

    //popup trigger
    const X_SECONDS_ON_PAGE = 1;
    const SCROLL_PAGE_BY_Y_PERCENT = 2;
    const VIEW_X_PAGE = 3;
    const EXIT_INTENT = 4;

    //popup animation
    const NONE = 0;
    const ZOOM = 1;
    const ZOOMOUT = 2;
    const MOVE_FROM_LEFT = 3;
    const MOVE_FROM_RIGHT = 4;
    const MOVE_FROM_TOP = 5;
    const MOVE_FROM_BOTTOM = 6;

    //popup position in page
    const CENTER = 0;
    const TOP_LEFT = 1;
    const TOP_RIGHT = 2;
    const BOTTOM_LEFT = 3;
    const BOTTOM_RIGHT = 4;
    const MIDDLE_LEFT = 5;
    const MIDDLE_RIGHT = 6;
    const TOP_CENTER = 7;
    const BOTTOM_CENTER = 8;

    //popup Position
    const ALLPAGE = 0;
    const HOMEPAGE = 'cms_index_index';
    const CMSPAGE = 'cms_page_view';
    const CATEGORY = 'catalog_category_view';
    const PRODUCT = 'catalog_product_view';

    // floating button positon
    const BUTTON_CENTER = 0;
    const BUTTON_BOTTOM_LEFT = 1;
    const BUTTON_BOTTOM_RIGHT = 2;

    // floating button display popup
    const BEFORE_CLICK_BUTTON = 0;
    const AFTER_CLICK_BUTTON = 1;

    public function _construct()
    {
        $this->_init('Magenest\Popup\Model\ResourceModel\Popup');
    }
}