<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  BlueFormBuilder
 * @package   BlueFormBuilder_Core
 * @copyright Copyright (C) 2019 Magezon (https://www.magezon.com)
 */

namespace BlueFormBuilder\Core\Model\Element;

class MultipleSelect extends \BlueFormBuilder\Core\Model\Element
{
	public function prepareValue($value)
	{
		if ($value && is_array($value)) {
			$result = [];
			$post = $this->getPost();
			$othersSelected = in_array('bfb_others', $value);
			$options = $this->getConfig('options');
			if ($value && is_array($value)) {
				foreach ($value as $_value) {
					if ($_value == 'bfb_others') continue;
					foreach ($options as $_option) {
						if ((isset($_option['value']) && $_option['value'])) {
							$_optionValue = $_option['value'];
							if ($_optionValue == $_value) {
								$result[] = $_option['label'];
								break;
							}
							continue;
						}
						if ((isset($_option['label']) && $_option['label'])) {
							$_optionValue = $_option['label'];
							if ($_optionValue == $_value) {
								$result[] = $_option['label'];
								break;
							}
						}
					}
				}
			}
			$value = implode(', ', $result);
			if ($othersSelected) {
				$value .= '<br/>';
				$othersKey = $this->getElemName() . '_others';
				if (isset($post[$othersKey])) {
					$value .= $this->getConfig('others_label') . ': ' . $post[$othersKey];
				}
			}
			$this->setValue($value);
	        $this->setHtmlValue($value);
	        $this->setEmailHtmlValue($value);
	    }
	}

	public function getInsightsData()
	{
		$simpleValues = $this->getSubmission()->getSimpleValues();
		$values = isset($simpleValues[$this->getElemName()]) ? explode(', ', $simpleValues[$this->getElemName()]) : [];
		return $values;
	}

	public function getInsightsLabels()
	{
		$simpleValues = $this->getSubmission()->getSimpleValues();
		$values = isset($simpleValues[$this->getElemName()]) ? explode(', ', $simpleValues[$this->getElemName()]) : [];
		foreach ($this->getConfig('options') as $_option) {
			$key = (isset($_option['value']) && $_option['value']) ? $_option['value'] : $_option['label'];
			if (!in_array($key, $values)) {
				$values[] = $key;
			}
		}
		return $values;
	}
}