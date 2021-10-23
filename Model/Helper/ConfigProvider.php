<?php
/**
 * @package     HitarthPattani\LinkGuestOrders
 * @author      Hitarth Pattani <hitarthpattani@gmail.com>
 * @copyright   Copyright © 2021. All rights reserved.
 */
declare(strict_types=1);

namespace HitarthPattani\LinkGuestOrders\Model\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class ConfigProvider
{
    /**
     * @var string
     */
    // phpcs:disable Generic.Files.LineLength.TooLong
    const XML_PATH_LINK_GUEST_ORDERS_ENABLED = 'customer/link_guest_orders/enabled';
    const XML_PATH_LINK_GUEST_ORDERS_ON_REGISTATION = 'customer/link_guest_orders/link_guest_orders_on_customer_registation';
    // phpcs:enable

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get configuration
     *
     * @param string $path
     * @return mixed
     */
    public function execute($path)
    {
        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return (bool) $this->execute(self::XML_PATH_LINK_GUEST_ORDERS_ENABLED);
    }

    /**
     * @return bool
     */
    public function isLinkGuestOrdersOnCustomerRegistation(): ?bool
    {
        return (bool) $this->execute(self::XML_PATH_LINK_GUEST_ORDERS_ON_REGISTATION);
    }
}
