<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Boxplosive\Connect\Model\Customer;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Boxplosive customer resource class
 */
class ResourceModel extends AbstractDb
{

    /**
     * Table name
     */
    public const ENTITY_TABLE = 'boxplosive_customer';

    /**
     * Primary field
     */
    public const PRIMARY = 'entity_id';

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(self::ENTITY_TABLE, self::PRIMARY);
    }

    /**
     * Check is entity exists
     *
     * @param $entityId
     * @return bool
     */
    public function isExists($entityId): bool
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getTable(self::ENTITY_TABLE), self::PRIMARY)
            ->where('entity_id = :entity_id');
        $bind = [':entity_id' => $entityId];

        return (bool)$connection->fetchOne($select, $bind);
    }

    /**
     * Check is entity exists
     *
     * @param $customerId
     * @return bool
     */
    public function isCustomerExists($customerId): bool
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getTable(self::ENTITY_TABLE), self::PRIMARY)
            ->where('customer_id = :customer_id');
        $bind = [':customer_id' => $customerId];

        return (bool)$connection->fetchOne($select, $bind);
    }
}
