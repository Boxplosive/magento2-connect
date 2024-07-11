<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Boxplosive\Connect\Api\WebApi\Distribution;

use Magento\Customer\Api\Data\CustomerInterface;

/**
 * Distribution Repository Interface
 * @api
 */
interface RepositoryInterface
{

    public const REQUEST_LOYALTY_BALANCES = 'distribution/tenant/%s/customer/%s/LoyaltyBalances';
    public const REQUEST_LOYALTY_PROGRAMS = 'distribution/tenant/%s/customer/%s/LoyaltyPrograms';
    public const REQUEST_SAVING_GOALS = 'distribution/tenant/%s/customer/%s/SavingGoals';
    public const REQUEST_ACTIVE_COUPONS = 'distribution/tenant/%s/customer/%s/ActiveCouponsAndReservations';
    public const REQUEST_COUPONS_BY_CODE = 'distribution/tenant/%s/customer/%s/couponsbycode';
    public const REQUEST_CLAIM_COUPON = 'distribution/tenant/%s/customer/%s/claimcoupon';

    /**
     * Returns loyalty balance(s) for a specific customer.
     *
     * @param CustomerInterface $customer
     * @return array
     */
    public function getLoyaltyBalances(CustomerInterface $customer): array;

    /**
     * Returns loyalty program(s) for a specific customer.
     *
     * @param CustomerInterface $customer
     * @return array
     */
    public function getLoyaltyPrograms(CustomerInterface $customer): array;

    /**
     * Get the list of saving goals for a logged in customer.
     *
     * @param CustomerInterface $customer
     * @return array
     */
    public function getSavingGoals(CustomerInterface $customer): array;

    /**
     * Method returns all active coupons and coupon-reservations for a user.
     *
     * @param CustomerInterface $customer
     * @return array
     */
    public function getActiveCouponsAndReservations(CustomerInterface $customer): array;

    /**
     * Method returns the coupon definition based on couponcode(s).
     *
     * @param CustomerInterface $customer
     * @param array $couponCodes
     * @return array
     */
    public function getCouponsByCode(CustomerInterface $customer, array $couponCodes): array;

    /**
     * Method used to claim a coupon for a logged-in user.
     *
     * @param CustomerInterface $customer
     * @param string $couponCode
     * @return array
     */
    public function claimCoupon(CustomerInterface $customer, string $couponCode): array;
}
