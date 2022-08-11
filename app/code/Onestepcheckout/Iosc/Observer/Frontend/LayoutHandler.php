<?php
/**
 * OneStepCheckout
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to One Step Checkout AS software license.
 *
 * License is available through the world-wide-web at this URL:
 * https://www.onestepcheckout.com/LICENSE.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to mail@onestepcheckout.com so we can send you a copy immediately.
 *
 * @category   onestepcheckout
 * @package    onestepcheckout_iosc
 * @copyright  Copyright (c) 2017 OneStepCheckout  (https://www.onestepcheckout.com/)
 * @license    https://www.onestepcheckout.com/LICENSE.txt
 */
namespace Onestepcheckout\Iosc\Observer\Frontend;

class LayoutHandler implements \Magento\Framework\Event\ObserverInterface
{

    public $scopeConfig;
    public $helper;

    /**
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Onestepcheckout\Iosc\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Onestepcheckout\Iosc\Helper\Data $helper
    ) {

        $this->scopeConfig = $scopeConfig;
        $this->helper = $helper;
    }

    /**
     * Add handles to the page for checkout page .
     *
     *
     * @param Observer $observer
     * @event layout_load_before
     *
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {

        if (!$this->helper->isEnabled()) {
            return;
        }

        $action = $observer->getFullActionName();

        if ($action === 'checkout_index_index') {
            $observer->getLayout()
                ->getUpdate()
                ->addHandle('checkout_onestepcheckout_iosc');
        }
    }
}
