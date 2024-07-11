<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Boxplosive\Connect\Model\Config\System;

use Boxplosive\Connect\Api\Config\System\SettingsInterface;

/**
 * Settings provider class
 */
class SettingsRepository extends BaseRepository implements SettingsInterface
{

    /**
     * @inheritDoc
     */
    public function getFinishTransaction(?int $storeId = null): string
    {
        return (string)$this->getStoreValue(self::XML_PATH_FINISH_TRANSACTION, $storeId);
    }

    /**
     * @inheritDoc
     */
    public function useMultipleDiscount(int $storeId = null): bool
    {
        return $this->isSetFlag(self::XML_PATH_USE_MULTIPLE_DISCOUNT, $storeId);
    }
}
