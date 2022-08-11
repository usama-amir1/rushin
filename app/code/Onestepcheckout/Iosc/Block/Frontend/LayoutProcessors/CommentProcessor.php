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

class CommentProcessor implements \Magento\Checkout\Block\Checkout\LayoutProcessorInterface
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
        $configKey = 'comments';
        $layoutPath = $jsLayout['components']['checkout']
                        ['children']['iosc']
                        ['children'][$configKey] ?? false;

        if ($this->helper->isEnabled() && $layoutPath) {
            unset(
                $jsLayout['components']['checkout']
                    ['children']['iosc']
                    ['children'][$configKey]
            );

            $scopeStore = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $enabled = $this->scopeConfig->getValue('onestepcheckout_iosc/' . $configKey . '/enable', $scopeStore);
            $sidebarPath = $jsLayout['components']['checkout']
                            ['children']['sidebar']
                            ['children'][$configKey] ?? false;

            if ($enabled) {

                if ($sidebarPath) {
                    $componentConfig = array_merge($layoutPath, $sidebarPath);
                    $jsLayout['components']['checkout']
                        ['children']['sidebar']
                        ['children'][$configKey] = $componentConfig;
                }
            } else {
                if ($sidebarPath) {
                    unset(
                        $jsLayout['components']['checkout']
                            ['children']['sidebar']
                            ['children'][$configKey]
                    );
                }
            }
        }

        return $jsLayout;
    }
}
