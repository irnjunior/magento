<?php

namespace Magenest\Popup\Controller\Popup;

class Subscriber extends \Magento\Newsletter\Controller\Subscriber
{

    /** @var \Magenest\Popup\Helper\Data $_helperData */
    protected $_helperData;

    /** @var \Magenest\Popup\Model\PopupFactory $_popupFactory */
    protected $_popupFactory;

    /** @var  \Magenest\Popup\Model\LogFactory $_popupLogFactory */
    protected $_popupLogFactory;
    /**
     * @var \Magento\Customer\Api\AccountManagementInterface $customerAccountManagement
     */
    protected $customerAccountManagement;

    /** @var \Magento\Framework\Validator\EmailAddress $emailValidator */
    private $emailValidator;

    /** @var \Psr\Log\LoggerInterface $_logger */
    protected $_logger;

    /** @var \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor */
    private $dataPersistor;
    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    protected $_jsonHelper;

    public function __construct(
        \Magenest\Popup\Helper\Data $helperData,
        \Magenest\Popup\Model\PopupFactory $popupFactory,
        \Magenest\Popup\Model\LogFactory $popupLogFactory,
        \Magento\Customer\Api\AccountManagementInterface $customerAccountManagement,
        \Magento\Framework\Validator\EmailAddress $emailValidator,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Url $customerUrl,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Json\Helper\Data $jsonHelper
    )
    {
        $this->_helperData = $helperData;
        $this->_popupFactory = $popupFactory;
        $this->_popupLogFactory = $popupLogFactory;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->emailValidator = $emailValidator ?: \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Framework\Validator\EmailAddress::class);
        $this->_logger = $logger;
        $this->dataPersistor = $dataPersistor;
        $this->_transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->scopeConfig = $scopeConfig;
        $this->_jsonHelper = $jsonHelper;
        parent::__construct($context, $subscriberFactory, $customerSession, $storeManager, $customerUrl);
    }

    /**
     * Validates that the email address isn't being used by a different account.
     *
     * @param string $email
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function validateEmailAvailable($email)
    {
        $out = null;
        $websiteId = $this->_storeManager->getStore()->getWebsiteId();
        if ($this->_customerSession->getCustomerDataObject()->getEmail() !== $email
            && !$this->customerAccountManagement->isEmailAvailable($email, $websiteId)
        ) {
            $out = [
                'status' => 0,
                'message' => __('This email address is already assigned to another user.')
            ];
        }
        return $out;
    }

    /**
     * Validates that if the current user is a guest, that they can subscribe to a newsletter.
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function validateGuestSubscription()
    {
        $out = null;
        if (\Magento\Framework\App\ObjectManager::getInstance()
                ->get('Magento\Backend\App\ConfigInterface')
                ->getValue(
                    \Magento\Newsletter\Model\Subscriber::XML_PATH_ALLOW_GUEST_SUBSCRIBE_FLAG
                ) != 1
            && !$this->_customerSession->isLoggedIn()
        ) {
            $out = [
                'status' => 0,
                'message' => __(
                    'Sorry, but the administrator denied subscription for guests. Please <a href="%1">register</a>.',
                    $this->_customerUrl->getRegisterUrl()
                )
            ];
        }
        return $out;
    }

    /**
     * Validates the format of the email address
     *
     * @param string $email
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function validateEmailFormat($email)
    {
        $out = null;
        if (!$this->emailValidator->isValid($email)) {
            $out = [
                'status' => 0,
                'message' => __('Please enter a valid email address.')
            ];
        }
        return $out;
    }

    /**
     * @param int $status
     * @return \Magento\Framework\Phrase
     */
    private function getSuccessMessage($status)
    {
        if ($status === \Magento\Newsletter\Model\Subscriber::STATUS_NOT_ACTIVE) {
            return __('The confirmation request has been sent.');
        }

        return __('Thank you for your subscription.');
    }

    public function execute()
    {
        $out = [];
        $params = json_decode($this->getRequest()->getContent(), true);
        if (is_array($params) && !empty($params)) {
            $dataPopup = $params['dataPopup'];
            $popup_type = $dataPopup[0];
            $popup_id = $popup_type['name'];
            if ($popup_type['value'] == \Magenest\Popup\Model\Popup::SUBCRIBE) {
                $out = $this->popupNewsletter($dataPopup);
            } elseif ($popup_type['value'] == \Magenest\Popup\Model\Popup::CONTACT_FORM) {
                $out = $this->popupContactform($dataPopup);
            }

            /** @var \Magenest\Popup\Model\Popup $popupModel */
            $popupModel = $this->_popupFactory->create()->load($popup_id);
            $email = '';
            $name = '';
            $comment = '';
            $phone = '';
            foreach ($dataPopup as $data) {
                if (isset($data['name'])) {
                    if ($data['name'] == 'email') {
                        $email = $data['value'];
                    }
                    if ($data['name'] == 'name') {
                        $name = $data['value'];
                    }
                    if ($data['name'] == 'msg') {
                        $comment = $data['value'];
                    }
                    if ($data['name'] == 'comment') {
                        $comment = $data['value'];
                    }
                    if ($data['name'] == 'phone') {
                        $phone = $data['value'];
                    }
                }
            }
            $popupLogModel = $this->_popupLogFactory->create();
            $popupLogModel->setPopupId($popup_id);
            $popupLogModel->setPopupName($popupModel->getPopupName());
            $popupLogModel->setContent('name: '. $name .'| email: '. $email.'| phone: '. $phone .'| comment: '. $comment. '|');
            $popupLogModel->save();
        }
        $response = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_RAW);
        $response->setHeader('Content-type', 'text/plain');
        $response->setContents(json_encode($out));
        return $response;
    }

    public function popupNewsletter($dataPopup)
    {
        foreach ($dataPopup as $data) {
            if ($data['name'] == 'email') {
                $email = $data['value'];
                break;
            } else {
                $email = '';
            }
        }
        try {
            if ($this->validateEmailFormat($email) != null) {
                return $this->validateEmailFormat($email);
            }
            if ($this->validateGuestSubscription() != null) {
                return $this->validateGuestSubscription();
            }
            if ($this->validateEmailAvailable($email) != null) {
                return $this->validateEmailAvailable($email);
            }

            $subscriber = $this->_subscriberFactory->create()->loadByEmail($email);
            if ($subscriber->getId()
                && (int)$subscriber->getSubscriberStatus() === \Magento\Newsletter\Model\Subscriber::STATUS_SUBSCRIBED
            ) {
                $out = [
                    'status' => 0,
                    'message' => __('This email address is already subscribed.')
                ];
            } else {
                if ($this->enableMailchimp($dataPopup)) {
                    $this->addEmailToMailChimp($dataPopup);
                }
                $status = (int)$this->_subscriberFactory->create()->subscribe($email);
                $out = [
                    'status' => 1,
                    'message' => $this->getSuccessMessage($status)
                ];
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $out = [
                'status' => 0,
                'message' => __('There was a problem with the subscription: %1', $e->getMessage())
            ];
            $this->_logger->critical($e->getMessage());
        } catch (\Exception $e) {
            $out = [
                'status' => 0,
                'message' => __('Something went wrong with the subscription.')
            ];
            $this->_logger->critical($e->getMessage());
        }
        return $out;
    }

    public function popupContactform($dataPopup)
    {
        try {
            if ($this->enableMailchimp($dataPopup)) {
                $this->addEmailToMailChimp($dataPopup);
            }
            $this->sendEmail($this->validatedParams($dataPopup));
            $out = [
                'status' => 1,
                'message' => __('Thanks for contacting us with your comments and questions. We\'ll respond to you very soon.')
            ];
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $out = [
                'status' => 0,
                'message' => $e->getMessage()
            ];
            $this->dataPersistor->set('contact_us', $this->getRequest()->getParams());
            $this->_logger->critical($e->getMessage());
        } catch (\Exception $e) {
            $this->_logger->critical($e);
            $out = [
                'status' => 0,
                'message' => __('An error occurred while processing your form. Please try again later.')
            ];
            $this->dataPersistor->set('contact_us', $this->getRequest()->getParams());
            $this->_logger->critical($e->getMessage());
        }
        return $out;
    }

    /**
     * @return array
     * @throws \Exception
     */
    private function validatedParams($dataPopup)
    {
        $result = [];
        foreach ($dataPopup as $data) {
            if ($data['name'] == 'name') {
                $result['name'] = $data['value'];
                if (trim($data['value']) === '') {
                    throw new \Magento\Framework\Exception\LocalizedException(__('Name is missing'));
                }
            }
            if ($data['name'] == 'comment') {
                $result['comment'] = $data['value'];
                if (trim($data['value']) === '') {
                    throw new \Magento\Framework\Exception\LocalizedException(__('Comment is missing'));
                }
            }
            if ($data['name'] == 'email') {
                $result['email'] = $data['value'];
                if (false === \strpos($data['value'], '@')) {
                    throw new \Magento\Framework\Exception\LocalizedException(__('Comment is missing'));
                }
            }
        }

        return $result;
    }

    /**
     * @param array $post Post data from contact form
     * @return void
     */
    private function sendEmail($post)
    {
        $this->sendMail(
            $post['email'],
            ['data' => new \Magento\Framework\DataObject($post)]
        );
    }

    protected function sendMail($replyTo, $variables)
    {
        $replyToName = !empty($variables['data']['name']) ? $variables['data']['name'] : null;
        $this->inlineTranslation->suspend();
        try {
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $transport = $this->_transportBuilder
                ->setTemplateIdentifier($this->scopeConfig->getValue('contact/email/email_template', $storeScope))
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => $this->_storeManager->getStore()->getId()
                    ]
                )
                ->setTemplateVars($variables)
                ->setFrom($this->scopeConfig->getValue('contact/email/sender_email_identity', $storeScope))
                ->addTo($this->scopeConfig->getValue('contact/email/recipient_email', $storeScope))
                ->setReplyTo($replyTo, $replyToName)
                ->getTransport();

            $transport->sendMessage();
        } finally {
            $this->inlineTranslation->resume();
        }
    }

    public function addEmailToMailChimp($dataPopup)
    {
        $popup = $this->_popupFactory->create()->load($dataPopup[0]['name']);

        $email = '';
        $name = '';
        foreach ($dataPopup as $data) {
            if ($data['name'] == 'email') {
                $email = $data['value'];
            }
            if ($data['name'] == 'name') {
                $name = $data['value'];
            }
        }
        $api_key = $popup->getApiKey();
        $audience_id = $popup->getAudienceId();

        strstr($api_key, "-");
        list($key, $us) = explode("-", $api_key, 2);
        $subscriber_id = hash('md5', (strtolower($email)));
        $url = 'https://' . $us . '.api.mailchimp.com/3.0/lists/' . $audience_id . '/members/' . $subscriber_id;
        $data = [
            'email_address' => $email,
            'status' => 'subscribed',
            'merge_fields' => [
                'FNAME' => $name,
                'LNAME' => ''
            ]
        ];

        $subscriber = $this->_jsonHelper->jsonEncode($data);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $api_key);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $subscriber);
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

    }

    public function enableMailchimp($dataPopup)
    {
        $enable_mailchimp = $this->_popupFactory->create()
            ->load($dataPopup[0]['name'])->getEnableMailchimp();
        if ($enable_mailchimp == 1) {
            return true;
        } else {
            return false;
        }
    }
}