<?php
/**
 * @package     HitarthPattani\LinkGuestOrders
 * @author      Hitarth Pattani <hitarthpattani@gmail.com>
 * @copyright   Copyright © 2021. All rights reserved.
 */
declare(strict_types=1);

namespace HitarthPattani\LinkGuestOrders\Controller\Order;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use HitarthPattani\LinkGuestOrders\Model\LinkOrder;
use Psr\Log\LoggerInterface;

class ViewPost extends Action
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var LinkOrder
     */
    private $linkOrder;

    /**
     * @param Context $context
     * @param LoggerInterface $logger
     * @param LinkOrder $linkOrder
     */
    public function __construct(
        Context $context,
        LoggerInterface $logger,
        LinkOrder $linkOrder
    ) {
        parent::__construct($context);
        $this->logger = $logger;
        $this->linkOrder = $linkOrder;
    }

    /**
     * @return ResponseInterface|ResultInterface|void
     */
    public function execute()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }
        try {
            if ($this->linkOrder->execute($this->getRequest())) {
                $this->messageManager->addSuccessMessage(
                    __('Order successfully linked to your account.')
                );
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->critical($e);
            $this->messageManager->addErrorMessage(
                __('An error occurred while processing your request. Please try again later.')
            );
        }
        return $this->resultRedirectFactory->create()->setPath('linkguestorders/order/find');
    }
}
