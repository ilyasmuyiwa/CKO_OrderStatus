<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CheckoutCom\OrderUpdate\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface StatusRepositoryInterface
{

    /**
     * Save Status
     * @param \CheckoutCom\OrderUpdate\Api\Data\StatusInterface $status
     * @return \CheckoutCom\OrderUpdate\Api\Data\StatusInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \CheckoutCom\OrderUpdate\Api\Data\StatusInterface $status
    );

    /**
     * Retrieve Status
     * @param string $statusId
     * @return \CheckoutCom\OrderUpdate\Api\Data\StatusInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($statusId);

    /**
     * Retrieve Status matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \CheckoutCom\OrderUpdate\Api\Data\StatusSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Status
     * @param \CheckoutCom\OrderUpdate\Api\Data\StatusInterface $status
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \CheckoutCom\OrderUpdate\Api\Data\StatusInterface $status
    );

    /**
     * Delete Status by ID
     * @param string $statusId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($statusId);
}

