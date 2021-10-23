<?php
/**
 * @package     HitarthPattani\LinkGuestOrders
 * @author      Hitarth Pattani <hitarthpattani@gmail.com>
 * @copyright   Copyright © 2021. All rights reserved.
 */
declare(strict_types=1);

namespace HitarthPattani\LinkGuestOrders\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Message\ManagerInterface;
use HitarthPattani\LinkGuestOrders\Model\Helper\ConfigProvider;
use HitarthPattani\LinkGuestOrders\Model\LinkOrdersOnRegister;

class LinkGuestOrdersOnRegisterObserver implements ObserverInterface
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var LinkOrdersOnRegister
     */
    private $linkOrdersOnRegister;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @param ConfigProvider $configProvider
     * @param LinkOrdersOnRegister $linkOrdersOnRegister
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        ConfigProvider $configProvider,
        LinkOrdersOnRegister $linkOrdersOnRegister,
        ManagerInterface $messageManager
    ) {
        $this->configProvider = $configProvider;
        $this->linkOrdersOnRegister = $linkOrdersOnRegister;
        $this->messageManager = $messageManager;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if ($this->configProvider->isEnabled()
            && $this->configProvider->isLinkGuestOrdersOnCustomerRegistation()) {
            $customer = $observer->getEvent()->getCustomer();
            // phpcs:disable Generic.Files.LineLength.TooLong
            if ($this->linkOrdersOnRegister->execute((int) $customer->getId(), $customer->getEmail())) {
                $this->messageManager->addSuccessMessage(__('We found guest orders associated with email %1 and all guest order(s) successfully linked to your account.', $customer->getEmail()));
            }
            // phpcs:enable
        }
    }
}
