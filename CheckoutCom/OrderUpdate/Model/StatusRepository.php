<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CheckoutCom\OrderUpdate\Model;

use CheckoutCom\OrderUpdate\Api\Data\StatusInterface;
use CheckoutCom\OrderUpdate\Api\Data\StatusInterfaceFactory;
use CheckoutCom\OrderUpdate\Api\Data\StatusSearchResultsInterfaceFactory;
use CheckoutCom\OrderUpdate\Api\StatusRepositoryInterface;
use CheckoutCom\OrderUpdate\Model\ResourceModel\Status as ResourceStatus;
use CheckoutCom\OrderUpdate\Model\ResourceModel\Status\CollectionFactory as StatusCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class StatusRepository implements StatusRepositoryInterface
{

    /**
     * @var ResourceStatus
     */
    protected $resource;

    /**
     * @var Status
     */
    protected $searchResultsFactory;

    /**
     * @var StatusInterfaceFactory
     */
    protected $statusFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var StatusCollectionFactory
     */
    protected $statusCollectionFactory;


    /**
     * @param ResourceStatus $resource
     * @param StatusInterfaceFactory $statusFactory
     * @param StatusCollectionFactory $statusCollectionFactory
     * @param StatusSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceStatus $resource,
        StatusInterfaceFactory $statusFactory,
        StatusCollectionFactory $statusCollectionFactory,
        StatusSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->statusFactory = $statusFactory;
        $this->statusCollectionFactory = $statusCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(StatusInterface $status)
    {
        try {
            $this->resource->save($status);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the status: %1',
                $exception->getMessage()
            ));
        }
        return $status;
    }

    /**
     * @inheritDoc
     */
    public function get($statusId)
    {
        $status = $this->statusFactory->create();
        $this->resource->load($status, $statusId);
        if (!$status->getId()) {
            throw new NoSuchEntityException(__('Status with id "%1" does not exist.', $statusId));
        }
        return $status;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->statusCollectionFactory->create();
        
        $this->collectionProcessor->process($criteria, $collection);
        
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        
        $items = [];
        foreach ($collection as $model) {
            $items[] = $model;
        }
        
        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function delete(StatusInterface $status)
    {
        try {
            $statusModel = $this->statusFactory->create();
            $this->resource->load($statusModel, $status->getStatusId());
            $this->resource->delete($statusModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Status: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($statusId)
    {
        return $this->delete($this->get($statusId));
    }
}

