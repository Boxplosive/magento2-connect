<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Boxplosive\Connect\Api\Discount;

use Boxplosive\Connect\Api\Discount\DataInterface as BoxplosiveDiscount;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\Data\CartInterface;

/**
 * Discount Repository Interface
 * @api
 */
interface RepositoryInterface
{

    /**
     * Input exception text
     */
    public const INPUT_EXCEPTION = 'An ID is needed. Set the ID and try again.';
    /**
     * "No such entity" exception text
     */
    public const NO_SUCH_ENTITY_EXCEPTION = 'The discount with id "%1" does not exist.';
    /**
     * "Could not delete" exception text
     */
    public const COULD_NOT_DELETE_EXCEPTION = 'Could not delete the discount: %1';
    /**
     * "Could not save" exception text
     */
    public const COULD_NOT_SAVE_EXCEPTION = 'Could not save the discount: %1';

    /**
     * Return new category object
     *
     * @return BoxplosiveDiscount
     */
    public function create(): DataInterface;

    /**
     * Register entity to delete
     *
     * @param BoxplosiveDiscount $discount
     *
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(BoxplosiveDiscount $discount): bool;

    /**
     * Register entity to save
     *
     * @param BoxplosiveDiscount $discount
     *
     * @return BoxplosiveDiscount
     * @throws LocalizedException
     */
    public function save(BoxplosiveDiscount $discount): BoxplosiveDiscount;

    /**
     * Loads a specified customer
     *
     * @param int $quoteId
     *
     * @return BoxplosiveDiscount
     * @throws InputException
     */
    public function getByQuoteId(int $quoteId): BoxplosiveDiscount;

    /**
     * Check if discount can be applied
     *
     * @param CartInterface $quote
     * @return bool
     */
    public function canApplyDiscount(CartInterface $quote): bool;
}
