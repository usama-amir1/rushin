<?php

namespace Custom\RestrictShipping\Plugin\Model;

class ShippingMethodManagement
{
    /**
     * @param $shippingMethodManagement
     * @param $output
     * @return array|mixed
     */
    public function afterEstimateByAddress($shippingMethodManagement, $output)
    {
        return $this->filterOutput($output);
    }

    /**
     * @param $shippingMethodManagement
     * @param $output
     * @return array|mixed
     */
    public function afterEstimateByExtendedAddress($shippingMethodManagement, $output)
    {
        return $this->filterOutput($output);
    }

    /**
     * @param $shippingMethodManagement
     * @param $output
     * @return array|mixed
     */
    public function afterEstimateByAddressId($shippingMethodManagement, $output)
    {
        return $this->filterOutput($output);
    }

    /**
     * @param $output
     * @return array|mixed
     */
    private function filterOutput($output)
    {
        $free = [];
        foreach ($output as $shippingMethod) {
            if ($shippingMethod->getCarrierCode() == 'freeshipping' && $shippingMethod->getMethodCode() == 'freeshipping') {
                $free[] = $shippingMethod;
            }
        }

        if ($free) {
            return $free;
        }
        return $output;
    }
}
