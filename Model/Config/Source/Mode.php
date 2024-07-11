<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Boxplosive\Connect\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Mode Options Source model
 */
class Mode implements OptionSourceInterface
{

    public const ACCEPTANCE = 'acceptance';
    public const PRODUCTION = 'production';

    /**
     * Returns mode option source array
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return  [
            ['value' => self::ACCEPTANCE, 'label' => __('Acceptance')],
            ['value' => self::PRODUCTION, 'label' => __('Production')]
        ];
    }
}
