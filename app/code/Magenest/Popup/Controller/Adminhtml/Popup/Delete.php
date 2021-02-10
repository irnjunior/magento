<?php
namespace Magenest\Popup\Controller\Adminhtml\Popup;

class Delete extends \Magenest\Popup\Controller\Adminhtml\Popup\Popup
{
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;

    /**
     * Delete constructor.
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
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
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
        \Magento\Framework\Translate\InlineInterface $translateInline,
        \Magento\Framework\App\ResourceConnection $resourceConnection
    ){
        $this->resourceConnection = $resourceConnection;
        parent::__construct($context, $coreRegistry, $popupFactory, $popupTemplateFactory, $widgetFactory, $logger, $mathRandom, $cache, $dateTime, $translateInline);
    }

    public function execute()
    {
        $params = $this->_request->getParams();
        $connection = $this->resourceConnection->getConnection();
        $popupLayoutTable = $this->resourceConnection->getTableName('magenest_popup_layout');
        $popupId = isset($params['id']) ? $params['id'] : '';
        $select = $connection->select()->from($popupLayoutTable, 'layout_update_id')->where('popup_id = ?', $popupId);
        $removeLayoutUpdateIds = $connection->fetchCol($select);

        try{
            /** @var \Magenest\Popup\Model\Popup $popupModel */
            $popupModel = $this->_popupFactory->create();
            if($popupId){
                $popupModel->load($popupId);
                $popupModel->delete();
                if(!empty($removeLayoutUpdateIds)){
                    $inCond = $connection->prepareSqlCondition('popup_id', $popupId);
                    $connection->delete($popupLayoutTable, $inCond);
                    if (isset($removeLayoutUpdateIds) && !empty($removeLayoutUpdateIds)) {
                        $inCond = $connection->prepareSqlCondition('layout_update_id', ['in' => $removeLayoutUpdateIds]);
                        $connection->delete( $this->resourceConnection->getTableName('layout_update'), $inCond);
                    }
                }
            }
            /* Invalidate Full Page Cache */
            $this->cache->invalidate('full_page');
            $this->messageManager->addSuccess(__('The Popup has been deleted.'));
        }catch (\Exception $exception){
            $this->messageManager->addError($exception->getMessage());
            $this->_logger->critical($exception->getMessage());
        }
        $this->_redirect('*/*/index');
    }
}
