<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Boxplosive\Connect\Api\Customer;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Data Interface for Customers
 * @api
 */
interface DataInterface extends ExtensibleDataInterface
{
    public const GUEST_CUSTOMER_ID = 'mag-0';

    /**
     * Constants for keys of data array.
     */
    public const ID = 'entity_id';
    public const CUSTOMER_ID = 'customer_id';
    public const UPDATED_AT = 'updated_at';

    /**
     * @return int
     */
    public function getEntityId(): int;

    /**
     * @param $entityId
     * @return $this
     */
    public function setEntityId($entityId): self;

    /**
     * @return int
     */
    public function getCustomerId(): int;

    /**
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId(int $customerId): self;

    /**
     * @return string
     */
    public function getUpdatedAt(): string;

    /**
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt(string $updatedAt): self;
}
