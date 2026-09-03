<?php
declare(strict_types=1);

namespace Ezzar\DeliveryCost\Block\Adminhtml\System\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;

class Rates extends AbstractFieldArray
{
    private ?OptionSelect $optionRenderer = null;

    protected function _prepareToRender()
    {
        $this->addColumn('option_id', [
            'label' => __('Delivery Class'),
            'renderer' => $this->getOptionRenderer(),
        ]);

        $this->addColumn('price', [
            'label' => __('Price'),
            'class' => 'validate-number validate-zero-or-greater',
        ]);

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Delivery Charge');
    }

    private function getOptionRenderer(): OptionSelect
    {
        if ($this->optionRenderer === null) {
            $this->optionRenderer = $this->getLayout()->createBlock(
                OptionSelect::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->optionRenderer;
    }

    protected function _prepareArrayRow(DataObject $row): void
    {
        $optionId = $row->getData('option_id');
        $options = [];

        if ($optionId !== null) {
            $options['option_' . $this->getOptionRenderer()->calcOptionHash((string)$optionId)]
                = 'selected="selected"';
        }

        $row->setData('option_extra_attrs', $options);
    }
}
