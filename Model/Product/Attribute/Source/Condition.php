<?php
/**
 * Copyright © Encora Digital LLC All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Encora\ProductClone\Model\Product\Attribute\Source;

class Condition extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{

    /**
     * getAllOptions
     *
     * @return array
     */
    public function getAllOptions()
    {
        $this->_options = [
        ['value' => 'new', 'label' => __('new')],
        ['value' => 'used', 'label' => __('used')],
        ['value' => 'refurbished', 'label' => __('refurbished')]
        ];
        return $this->_options;
    }

    /**
     * @return array
     */
    public function getFlatColumns()
    {
        $attributeCode = $this->getAttribute()->getAttributeCode();
        return [
            $attributeCode => [
            'unsigned' => false,
            'default' => null,
            'extra' => null,
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length' => 255,
            'nullable' => true,
            'comment' => $attributeCode . ' column',
            ],
        ];
    }

    /**
     * @return array
     */
    public function getFlatIndexes()
    {
        $indexes = [];
        
        $index = 'IDX_' . strtoupper($this->getAttribute()->getAttributeCode());
        $indexes[$index] = ['type' => 'index', 'fields' => [$this->getAttribute()->getAttributeCode()]];
        
        return $indexes;
    }

    /**
     * @param int $store
     * @return \Magento\Framework\DB\Select|null
     */
    public function getFlatUpdateSelect($store)
    {
        return $this->eavAttrEntity->create()->getFlatUpdateSelect($this->getAttribute(), $store);
    }
}

