<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Boxplosive\Connect\Api\WebApi\PointOfSale;

use Magento\Quote\Api\Data\CartInterface;

/**
 * Point of Sale Repository Interface
 * @api
 */
interface RepositoryInterface
{

    public const REQUEST_URI_GET_SUBTOTAL = 'transaction-platform-api/tenant/%s/GetSubtotal';
    public const REQUEST_URI_CUSTOMER_INFO = 'transaction-vendor-api/tenant/%s/GetCustomerInfo';
    public const REQUEST_URI_CANCEL = 'transaction-platform-api/tenant/%s/Cancel';
    public const REQUEST_URI_FINISH = 'transaction-platform-api/tenant/%s/Finish';

    /**
     * Retrieves the current state of the customer account, including active coupons and discounts.
     *
     * @param CartInterface $quote
     * @return array
     */
    public function getSubtotal(CartInterface $quote): array;

    /**
     * Start or continue an interaction with the customer at the POS.
     * Given a loyalty-card number, return the customer's coupons and loyalty points.
     *
     * @param CartInterface $quote
     * @return array
     */
    public function getCustomerInfo(CartInterface $quote): array;

    /**
     * Cancels an interaction with the customer at the POS.
     *
     * @param CartInterface $quote
     * @param array $transaction
     * @return bool
     */
    public function cancel(CartInterface $quote, array $transaction): bool;

    /**
     * Finish.
     *
     * @param CartInterface $quote
     * @param array $transaction
     * @return bool
     */
    public function finish(CartInterface $quote, array $transaction): bool;
}
