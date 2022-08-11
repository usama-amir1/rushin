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
namespace Onestepcheckout\Iosc\Block\Frontend\LayoutProcessors;

class TermsProcessor implements \Magento\Checkout\Block\Checkout\LayoutProcessorInterface
{
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
     * {@inheritdoc}
     */
    public function process($jsLayout)
    {

        if ($this->helper->isEnabled()) {
            $scopeStore = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $enabled = $this->scopeConfig->getValue(
                'checkout/options/enable_agreements',
                $scopeStore
            );
            if ($enabled) {

                $layoutPath = $jsLayout['components']['checkout']
                    ['children']['steps']
                    ['children']['billing-step']
                    ['children']['payment']
                    ['children']['payments-list']
                    ['children']['before-place-order']
                    ['children']['agreements'] ?? false;

                if ($layoutPath) {
                    unset(
                        $jsLayout['components']['checkout']
                            ['children']['steps']
                            ['children']['billing-step']
                            ['children']['payment']
                            ['children']['payments-list']
                            ['children']['before-place-order']
                            ['children']['agreements']
                    );

                    $sidebarPath = $jsLayout['components']['checkout']
                                    ['children']['sidebar']
                                    ['children']['agreements'] ?? false ;
                    if ($sidebarPath) {
                        $layoutPath = array_merge($layoutPath, $sidebarPath);
                    }

                    $jsLayout['components']['checkout']
                        ['children']['sidebar']
                        ['children']['agreements'] = $layoutPath;
                }
            }
        }

        return $jsLayout;
    }
}
