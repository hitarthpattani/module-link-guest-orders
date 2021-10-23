<?php
/**
 * @package     HitarthPattani\LinkGuestOrders
 * @author      Hitarth Pattani <hitarthpattani@gmail.com>
 * @copyright   Copyright © 2021. All rights reserved.
 */
declare(strict_types=1);

namespace HitarthPattani\LinkGuestOrders\Model;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Registry;

class LoadOrder
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
     * @param Registry $coreRegistry
     * @param ManagerInterface $messageManager
     * @param GetOrder $getOrder
     */
    public function __construct(
        Registry $coreRegistry,
        ManagerInterface $messageManager,
        GetOrder $getOrder
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->messageManager = $messageManager;
        $this->getOrder = $getOrder;
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
                $this->coreRegistry->register('current_order', $order);
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
