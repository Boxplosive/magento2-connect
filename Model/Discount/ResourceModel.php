<?php declare(strict_types=1);

namespace Boxplosive\Connect\Model\Discount;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Boxplosive customer resource class
 */
class ResourceModel extends AbstractDb
{

    /**
     * Table name
     */
    public const ENTITY_TABLE = 'boxplosive_discount';

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
     * @param $quoteId
     * @return bool
     */
    public function isQuoteIdExists($quoteId): bool
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getTable(self::ENTITY_TABLE), self::PRIMARY)
            ->where('quote_id = :quote_id');
        $bind = [':quote_id' => $quoteId];

        return (bool)$connection->fetchOne($select, $bind);
    }
}
