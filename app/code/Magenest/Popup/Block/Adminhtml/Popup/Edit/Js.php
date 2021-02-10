<?php
namespace Magenest\Popup\Block\Adminhtml\Popup\Edit;

use Magento\Store\Model\StoreManagerInterface;

class Js extends \Magento\Framework\View\Element\Template
{
    protected $_storeManager;
    protected $_template = "Magenest_Popup::popup/js.phtml";

    public function __construct(
        StoreManagerInterface $storeManager,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    )
    {
        $this->_storeManager= $storeManager;
        parent::__construct($context, $data);
    }

    public function getUrlPreview()
    {
        $url = $this->_storeManager->getStore()->getBaseUrl().'magenest_popup/template/preview/id/';
        return $url;
    }
}