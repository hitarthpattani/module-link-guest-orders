<?php
/**
 * @package     HitarthPattani\LinkGuestOrders
 * @author      Hitarth Pattani <hitarthpattani@gmail.com>
 * @copyright   Copyright © 2021. All rights reserved.
 */
declare(strict_types=1);

namespace HitarthPattani\LinkGuestOrders\Model;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Registry;
use Magento\Customer\Api\CustomerRepositoryInterface;

class LinkOrder
{
    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @var GetOrder
     */
    private $getOrder;

    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @param Registry $coreRegistry
     * @param ManagerInterface $messageManager
     * @param GetOrder $getOrder
     * @param ResourceConnection $resource
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        Registry $coreRegistry,
        ManagerInterface $messageManager,
        GetOrder $getOrder,
        ResourceConnection $resource,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->messageManager = $messageManager;
        $this->getOrder = $getOrder;
        $this->resource = $resource;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @param RequestInterface $request
     * @return bool
     */
    public function execute(RequestInterface $request)
    {
        $post = $request->getPostValue();

        // It is unique place in the class that process exception and only InputException. It is need because by
        // input data we found order and one more InputException could be throws deeper in stack trace
        try {
            $order = (!empty($post)
                && isset($post['guest_order_number'], $post['guest_order_email'])
                && !$this->hasPostDataEmptyFields($post))
                ? $this->getOrder->execute(
                    $post['guest_order_number'],
                    $post['guest_order_email']
                ): false;
            if ($order) {
                $customerData = $this->customerRepository->get($post['guest_order_email']);
                if ($customerData->getId()) {
                    $connection = $this->resource->getConnection();

                    $connection->update(
                        $connection->getTableName('sales_order'),
                        [
                            "customer_id" => $customerData->getId(),
                            "customer_is_guest" => 0
                        ],
                        ['entity_id = ?' => $order->getId()]
                    );
                }
                return true;
            }
        } catch (InputException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return false;
    }

    /**
     * Check post data for empty fields
     *
     * @param array $postData
     * @return bool
     */
    private function hasPostDataEmptyFields(array $postData)
    {
        return empty($postData['guest_order_number']) || empty($postData['guest_order_email']);
    }
}
