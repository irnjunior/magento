<?php
namespace Magenest\Popup\Controller\Adminhtml\Popup;

abstract class Popup extends \Magento\Widget\Controller\Adminhtml\Widget\Instance{
    /** @var  \Magenest\Popup\Model\PopupFactory */
    protected $_popupFactory;
    /** @var  \Magenest\Popup\Model\TemplateFactory $_popupTemplateFactory */
    protected $_popupTemplateFactory;
    /** @var  \Psr\Log\LoggerInterface $_logger */
    protected $_logger;
    /** @var  \Magento\Framework\Registry $_coreRegistry */
    protected $_coreRegistry;

    protected $_dateTime;
    /** @var \Magento\Framework\App\Cache\TypeListInterface $cache */
    protected $cache;

    /**
     * Popup constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magenest\Popup\Model\PopupFactory $popupFactory
     * @param \Magenest\Popup\Model\TemplateFactory $popupTemplateFactory
     * @param \Magento\Widget\Model\Widget\InstanceFactory $widgetFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Math\Random $mathRandom
     * @param \Magento\Framework\App\Cache\TypeListInterface $cache
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Magento\Framework\Translate\InlineInterface $translateInline
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magenest\Popup\Model\PopupFactory $popupFactory,
        \Magenest\Popup\Model\TemplateFactory $popupTemplateFactory,
        \Magento\Widget\Model\Widget\InstanceFactory $widgetFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Math\Random $mathRandom,
        \Magento\Framework\App\Cache\TypeListInterface $cache,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\Translate\InlineInterface $translateInline
    ){
        $this->_popupFactory = $popupFactory;
        $this->_popupTemplateFactory = $popupTemplateFactory;
        $this->_logger = $logger;
        $this->_coreRegistry = $coreRegistry;
        $this->_dateTime = $dateTime;
        $this->cache = $cache;
        parent::__construct($context, $coreRegistry, $widgetFactory, $logger, $mathRandom, $translateInline);
    }

    public function validDateFromTo($from, $to){
        if($from == '' || $to == ''){
            return false;
        }else{
            $timestampFrom = $this->_dateTime->timestamp($from);
            $timestampTo = $this->_dateTime->timestamp($to);
            if($timestampFrom > $timestampTo){
                return __('Start Date must not be later than End Date');
            }else{
                return false;
            }
        }
    }
}