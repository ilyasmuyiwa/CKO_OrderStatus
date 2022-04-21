<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CheckoutCom\OrderUpdate\Model\ResourceModel\Status;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * @inheritDoc
     */
    protected $_idFieldName = 'status_id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            \CheckoutCom\OrderUpdate\Model\Status::class,
            \CheckoutCom\OrderUpdate\Model\ResourceModel\Status::class
        );
    }
}

