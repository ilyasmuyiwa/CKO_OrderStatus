<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CheckoutCom\OrderUpdate\Model;

use CheckoutCom\OrderUpdate\Api\Data\StatusInterface;
use Magento\Framework\Model\AbstractModel;

class Status extends AbstractModel implements StatusInterface
{

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\CheckoutCom\OrderUpdate\Model\ResourceModel\Status::class);
    }

    /**
     * @inheritDoc
     */
    public function getStatusId()
    {
        return $this->getData(self::STATUS_ID);
    }

    /**
     * @inheritDoc
     */
    public function setStatusId($statusId)
    {
        return $this->setData(self::STATUS_ID, $statusId);
    }

    /**
     * @inheritDoc
     */
    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setOrderId($orderId)
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * @inheritDoc
     */
    public function getPaymentId()
    {
        return $this->getData(self::PAYMENT_ID);
    }

    /**
     * @inheritDoc
     */
    public function setPaymentId($paymentId)
    {
        return $this->setData(self::PAYMENT_ID, $paymentId);
    }

    /**
     * @inheritDoc
     */
    public function getOrderReference()
    {
        return $this->getData(self::ORDER_REFERENCE);
    }

    /**
     * @inheritDoc
     */
    public function setOrderReference($orderReference)
    {
        return $this->setData(self::ORDER_REFERENCE, $orderReference);
    }

    /**
     * @inheritDoc
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }
}

