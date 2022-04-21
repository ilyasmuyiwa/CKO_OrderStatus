<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CheckoutCom\OrderUpdate\Console\Command;

use CheckoutCom\OrderUpdate\Model\StatusFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Checkout\CheckoutApi;
use Magento\Framework\App\Config\ScopeConfigInterface;



class UpdateOrderStatus extends Command
{

    const NAME_ARGUMENT = "name";
    const NAME_OPTION = "option";

    const PRIVATE_KEY = 'settings/checkoutcom_configuration/secret_key';
    const ENVIRON = 'settings/checkoutcom_configuration/environment';

    protected $_orderCollectionFactory;
    private $state;
    private $paymentStatus;
    protected $apiHandler;
    protected $scopeConfig;
    protected $payment;
    /**
     * $checkoutApi field
     *
     * @var CheckoutApi $checkoutApi
     */
    protected $checkoutApi;

    protected $orderRepository;
    protected $searchCriteriaBuilder;
    protected $filterBuilder;
    protected $filterGroupBuilder;
    protected $_resource;
    private $timezone;
    private $dateTime;

    public function __construct(
        \Magento\Framework\App\State $state,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        ScopeConfigInterface $scopeConfig,
        StatusFactory $paymentStatus,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
    ) {
        $this->state = $state;
        $this->paymentStatus = $paymentStatus;
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->scopeConfig  = $scopeConfig;
        $this->orderRepository = $orderRepository;
        $this->_resource = $resource;
        $this->timezone = $timezone;
        $this->dateTime = $dateTime;
        parent::__construct();
    }



    /**
     * {@inheritdoc}
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $this->state->setAreaCode('global');
        $name = $input->getArgument(self::NAME_ARGUMENT);
        $option = $input->getOption(self::NAME_OPTION);
        $paymentMethod = 'checkoutcom';
        $collection = $this->orderCollectionDate();
        foreach ($collection as $order) {

            if (strpos($order->getPayment()->getMethod(), $paymentMethod) !== false) {

                $ckoStatus = $this->paymentStatus->create()->load($order->getId(), "order_id");
                if (empty($ckoStatus->getId())) {
                    return;
                }
                $paymentObject = $this->getPaymentDetailsActions($ckoStatus->getPaymentId(), $ckoStatus->getStoreId());
                $this->modifyOrderStatus($paymentObject, $order, $ckoStatus->getPaymentId(), $ckoStatus->getStoreId());
            }
        }

    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("checkoutcom_orderupdate:updateorderstatus");
        $this->setDescription("update order status");
        $this->setDefinition([
            new InputArgument(self::NAME_ARGUMENT, InputArgument::OPTIONAL, "Name"),
            new InputOption(self::NAME_OPTION, "-a", InputOption::VALUE_NONE, "Option functionality")
        ]);
        parent::configure();
    }

    protected function orderCollectionDate() {
        date_default_timezone_set('Asia/Dubai');
        $end = date('Y-m-d'); // 15 minutes before
        $ordersList  = $this->_orderCollectionFactory->create()->addFieldToSelect("*")
            ->addFieldToFilter(['status', 'state'],
                [
                    ['in' => ["pending", "pending_payment"]],
                    ['in' => ["pending", "new"]]
                ]
            )
            ->addFieldToFilter('created_at',
                ['gt' => $end]
            );

     //  echo  $ordersList->getSize();

       // echo $ordersList->getSelect()->__toString();
        return $ordersList->load();

    }

    public function initCKOSDK ($storeId) {
        $env = $this->getConfigValue(self::ENVIRON,$storeId);
        $secret = $this->getConfigValue(self::PRIVATE_KEY,$storeId);
        $this->checkoutApi = new CheckoutApi($secret, $env);
        return  $this->checkoutApi;
    }

    public function getPaymentDetailsActions($paymentId, $storeId) {
         $initCko = $this->initCKOSDK($storeId);
         $response =  $initCko->payments()->actions($paymentId);
         return $response;
     }

     public function getPaymentDetails($paymentId, $storeId)  {
         $initCko = $this->initCKOSDK($storeId);
         $response = $initCko->payments()->details($paymentId);
         if(isset($response->requested_on)) {
           return  $response->status;
         }
     }

    public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $field, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId
        );
    }

    public function modifyOrderStatus($paymentObject, $order, $paymentId, $storeId) {
        if($this->compareDate($paymentId, $storeId, $order)) {
            die("happy");
        }
        if(($paymentObject->id =="" || !isset($paymentObject->list))) {
            $order->addStatusHistoryComment("Payment is not successful");
            $order->setState("closed");
            $order->setStatus("closed");
            $order->save();
            return;
        }
        foreach (array_reverse($paymentObject->list) as $payment) {
            $this->addTransactionComment($payment->type, $payment->id, $order);
        }
    }

    public function addTransactionComment($type, $txn_id, $order)
    {
        if($type == "Authorization") {
            $order->setState("pending");
            $order->setStatus("pending");
            $comment = 'The order is Authorized. Transaction ID '. $txn_id;

        } elseif ($type == "Capture") {
            $order->setState("processing");
            $order->setStatus("processing");
            $comment = 'The order is Capture. Transaction ID '. $txn_id;

        } elseif ($type == "Void") {
            $order->setState("closed");
            $order->setStatus("closed");
            $comment = 'The order is Void. Transaction ID '. $txn_id;
        } elseif ($type == "Refund") {
            $order->setState("closed");
            $order->setStatus("closed");
            $comment = 'The order is Void. Transaction ID '. $txn_id;
        } else {
            return;
        }

        $order->addStatusHistoryComment($comment);
        $order->save();
    }

    public function compareDate( $paymentId, $storeId, $order) {
        $paymentStatus = $this->getPaymentDetails($paymentId, $storeId);
        if($paymentStatus=="Pending") {
            $currentDate = $this->dateTime->gmtDate();
            $created = $order->getCreatedAt();

            //Convert to store timezone
            $created = $this->timezone->date($created);
            $createdString = $created->format('Y-m-d H:i:s');
            echo $currentDate.'  '.$createdString;
            echo PHP_EOL;
            echo strtotime($currentDate).'  '.strtotime($createdString);
            echo PHP_EOL;
            echo $order->getId();

        }


        die("ssss");
        echo $paymentStatus;
        echo PHP_EOL;
      //  echo strtotime($paymentDate);
        echo PHP_EOL;
        echo strtotime($order->getCreatedAt());
        return $paymentStatus;
        die();

    }

}

