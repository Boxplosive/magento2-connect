<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Boxplosive\Connect\Api\WebApi\Internal;

/**
 * Internal Api Repository Interface
 * @api
 */
interface RepositoryInterface
{
    /**
     * @api
     * Retrieves the current state of the customer account
     *
     * @param string $quoteId
     *
     * @return mixed
     */
    public function getSubtotal(string $quoteId): array;

    /**
     * @api
     * Retrieves the current state of guest account
     *
     * @param string $quoteId
     *
     * @return mixed
     */
    public function getGuestSubtotal(string $quoteId): array;

    /**
     * @api
     * Retrieves the current state of the customer account
     *
     * @param string $quoteId
     * @param string $value
     *
     * @return bool
     */
    public function subscribe(string $quoteId, string $value): bool;

    /**
     * @api
     * Apply discount to quote
     *
     * @param string $quoteId
     *
     * @return array
     */
    public function applyDiscount(string $quoteId): array;

    /**
     * @api
     * Get current customer balance
     *
     * @param int $customerId
     * @return array
     */
    public function getBalance(int $customerId): array;
}
