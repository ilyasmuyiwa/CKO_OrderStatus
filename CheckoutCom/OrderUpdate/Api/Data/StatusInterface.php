<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CheckoutCom\OrderUpdate\Api\Data;

interface StatusInterface
{

    const ORDER_ID = 'order_id';
    const STORE_ID = 'store_id';
    const STATUS_ID = 'status_id';
    const ORDER_REFERENCE = 'order_reference';
    const PAYMENT_ID = 'payment_id';

    /**
     * Get status_id
     * @return string|null
     */
    public function getStatusId();

    /**
     * Set status_id
     * @param string $statusId
     * @return \CheckoutCom\OrderUpdate\Status\Api\Data\StatusInterface
     */
    public function setStatusId($statusId);

    /**
     * Get order_id
     * @return string|null
     */
    public function getOrderId();

    /**
     * Set order_id
     * @param string $orderId
     * @return \CheckoutCom\OrderUpdate\Status\Api\Data\StatusInterface
     */
    public function setOrderId($orderId);

    /**
     * Get payment_id
     * @return string|null
     */
    public function getPaymentId();

    /**
     * Set payment_id
     * @param string $paymentId
     * @return \CheckoutCom\OrderUpdate\Status\Api\Data\StatusInterface
     */
    public function setPaymentId($paymentId);

    /**
     * Get order_reference
     * @return string|null
     */
    public function getOrderReference();

    /**
     * Set order_reference
     * @param string $orderReference
     * @return \CheckoutCom\OrderUpdate\Status\Api\Data\StatusInterface
     */
    public function setOrderReference($orderReference);

    /**
     * Get store_id
     * @return string|null
     */
    public function getStoreId();

    /**
     * Set store_id
     * @param string $storeId
     * @return \CheckoutCom\OrderUpdate\Status\Api\Data\StatusInterface
     */
    public function setStoreId($storeId);
}

