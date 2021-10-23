<?php
/**
 * @package     HitarthPattani\LinkGuestOrders
 * @author      Hitarth Pattani <hitarthpattani@gmail.com>
 * @copyright   Copyright © 2021. All rights reserved.
 */
declare(strict_types=1);

namespace HitarthPattani\LinkGuestOrders\Model;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;

class LinkOrdersOnRegister
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param StoreManagerInterface $storeManager
     * @param ResourceConnection $resource
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        StoreManagerInterface $storeManager,
        ResourceConnection $resource
    ) {
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->storeManager = $storeManager;
        $this->resource = $resource;
    }

    /**
     * @param int $customerId
     * @param string $email
     * @return bool
     */
    public function execute(int $customerId, string $email)
    {
        // It is unique place in the class that process exception and only InputException. It is need because by
        // input data we found order and one more InputException could be throws deeper in stack trace
        // phpcs:disable Magento2.CodeAnalysis.EmptyBlock.DetectedCatch
        try {
            $orderIds = $this->getOrderIds($email);
            if ($orderIds) {
                $connection = $this->resource->getConnection();

                $connection->update(
                    $connection->getTableName('sales_order'),
                    [
                        "customer_id" => $customerId,
                        "customer_is_guest" => 0
                    ],
                    ['entity_id IN (?)' => $orderIds]
                );
                return true;
            }
        } catch (InputException $e) {
        }
        // phpcs:enable

        return false;
    }

    /**
     * @param string $email
     * @return array|bool
     */
    public function getOrderIds($email)
    {
        $records = $this->orderRepository->getList(
            $this->searchCriteriaBuilder
                ->addFilter('store_id', $this->storeManager->getStore()->getId())
                ->addFilter('customer_email', $email)
                ->addFilter('customer_is_guest', 1)
                ->create()
        );

        $items = $records->getItems();
        if (empty($items)) {
            return false;
        }

        $ids = [];

        foreach ($items as $item) {
            $ids[] = $item->getId();
        }

        return $ids;
    }
}
