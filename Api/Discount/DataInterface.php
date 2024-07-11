<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Boxplosive\Connect\Api\Discount;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Data Interface for Discount
 * @api
 */
interface DataInterface extends ExtensibleDataInterface
{

    /**
     * Constants for keys of data array.
     */
    public const ID = 'entity_id';
    public const QUOTE_ID = 'quote_id';
    public const DISCOUNT = 'discount';

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
    public function getQuoteId(): int;

    /**
     * @param int $quoteId
     * @return $this
     */
    public function setQuoteId(int $quoteId): self;

    /**
     * @return array
     */
    public function getDiscount(): array;

    /**
     * @param array $discount
     * @return $this
     */
    public function setDiscount(array $discount): self;
}
