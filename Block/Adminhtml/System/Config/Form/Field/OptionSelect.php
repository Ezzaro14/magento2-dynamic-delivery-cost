<?php
declare(strict_types=1);

namespace Ezzar\DeliveryCost\Block\Adminhtml\System\Config\Form\Field;

use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Framework\View\Element\Html\Select;

class OptionSelect extends Select
{
    private const ATTR_CODE = 'delivery_charge';

    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        private readonly AttributeRepositoryInterface $attributeRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function setInputName($value)
    {
        return $this->setName($value);
    }

    public function _toHtml()
    {
        if (!$this->getOptions()) {
            $attribute = $this->attributeRepository->get('catalog_product', self::ATTR_CODE);

            foreach ($attribute->getOptions() as $opt) {
                if (!$opt->getValue()) {
                    continue;
                }
                $this->addOption($opt->getValue(), $opt->getLabel());
            }
        }
        $this->setClass('select');
        return parent::_toHtml();
    }
}
