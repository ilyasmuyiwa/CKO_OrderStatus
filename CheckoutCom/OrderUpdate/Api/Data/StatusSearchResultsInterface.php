<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CheckoutCom\OrderUpdate\Api\Data;

interface StatusSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Status list.
     * @return \CheckoutCom\OrderUpdate\Api\Data\StatusInterface[]
     */
    public function getItems();

    /**
     * Set order_id list.
     * @param \CheckoutCom\OrderUpdate\Api\Data\StatusInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

