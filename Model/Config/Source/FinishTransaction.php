<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Boxplosive\Connect\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Mode Finish Transaction Source model
 */
class FinishTransaction implements OptionSourceInterface
{

    public const ORDER = 'order';
    public const INVOICE = 'invoice';

    /**
     * Returns mode option source array
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return  [
            ['value' => self::ORDER, 'label' => __('Order')],
            ['value' => self::INVOICE, 'label' => __('Invoice')]
        ];
    }
}
