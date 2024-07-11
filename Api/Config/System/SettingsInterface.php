<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Boxplosive\Connect\Api\Config\System;

/**
 * Settings group config repository interface
 * @api
 */
interface SettingsInterface
{

    public const XML_PATH_FINISH_TRANSACTION = 'boxplosive_connect/settings/finish_transaction';
    public const XML_PATH_USE_MULTIPLE_DISCOUNT = 'boxplosive_connect/settings/use_multiple_discount';

    /**
     * Get Finish Transaction On
     *
     * @param int|null $storeId
     *
     * @return string
     */
    public function getFinishTransaction(?int $storeId = null): string;

    /**
     * Check if allowed to use multiple discount
     *
     * @param int|null $storeId
     *
     * @return bool
     */
    public function useMultipleDiscount(int $storeId = null): bool;
}
