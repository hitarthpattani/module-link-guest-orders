<?php
/**
 * @package     HitarthPattani\LinkGuestOrders
 * @author      Hitarth Pattani <hitarthpattani@gmail.com>
 * @copyright   Copyright © 2021. All rights reserved.
 */
declare(strict_types=1);

namespace HitarthPattani\LinkGuestOrders\ViewModel;

use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Sales\Model\Order as OrderModel;

class Data implements ArgumentInterface
{
    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @var CurrentCustomer
     */
    private $currentCustomer;

    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @param UrlInterface $url
     * @param CurrentCustomer $currentCustomer
     * @param Registry $coreRegistry
     */
    public function __construct(
        UrlInterface $url,
        CurrentCustomer $currentCustomer,
        Registry $coreRegistry
    ) {
        $this->url = $url;
        $this->currentCustomer = $currentCustomer;
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * @return string
     */
    public function getCustomerEmail()
    {
        if (!$this->currentCustomer->getCustomerId()) {
            return '';
        }

        $customer = $this->currentCustomer->getCustomer();
        return $customer->getEmail();
    }

    /**
     * Returns action url for contact form
     *
     * @return string
     */
    public function getFormViewAction()
    {
        return $this->url->getUrl('linkguestorders/order/view', ['_secure' => true]);
    }

    /**
     * Returns action url for contact form
     *
     * @return string
     */
    public function getFormViewPostAction()
    {
        return $this->url->getUrl('linkguestorders/order/viewPost', ['_secure' => true]);
    }

    /**
     * @return OrderModel
     */
    private function getOrder()
    {
        return $this->coreRegistry->registry('current_order');
    }

    /**
     * @return string
     */
    public function getOrderIncrementId()
    {
        return $this->getOrder()->getIncrementId();
    }
}
