<?php
/**
 * Copyright Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Boxplosive\Connect\Model\Total;

use Boxplosive\Connect\Api\Customer\RepositoryInterface as BoxplosiveCustomerRepository;
use Boxplosive\Connect\Api\Discount\RepositoryInterface as BoxplosiveDiscountRepository;
use Boxplosive\Connect\Api\WebApi\PointOfSale\RepositoryInterface as PointOfSaleRepository;
use Magento\Framework\Phrase;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;

/**
 * Boxplosive Total
 */
class Boxplosive extends AbstractTotal
{

    private $value = null;

    /**
     * @var PointOfSaleRepository
     */
    private $pointOfSaleRepository;
    /**
     * @var BoxplosiveCustomerRepository
     */
    private $boxplosiveCustomerRepository;
    /**
     * @var BoxplosiveDiscountRepository
     */
    private $boxplosiveDiscountRepository;

    /**
     * Boxplosive constructor.
     * @param PointOfSaleRepository $pointOfSaleRepository
     * @param BoxplosiveCustomerRepository $boxplosiveCustomerRepository
     * @param BoxplosiveDiscountRepository $boxplosiveDiscountRepository
     */
    public function __construct(
        PointOfSaleRepository $pointOfSaleRepository,
        BoxplosiveCustomerRepository $boxplosiveCustomerRepository,
        BoxplosiveDiscountRepository $boxplosiveDiscountRepository
    ) {
        $this->pointOfSaleRepository = $pointOfSaleRepository;
        $this->boxplosiveCustomerRepository = $boxplosiveCustomerRepository;
        $this->boxplosiveDiscountRepository = $boxplosiveDiscountRepository;
        $this->setCode('boxplosive_discount');
    }

    public function collect(
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    ): self {
        parent::collect($quote, $shippingAssignment, $total);

        if ($quote->getCustomerId()) {
            $amounts = $this->getValue($quote);
            if ($amounts) {
                foreach ($amounts as $amount) {
                    $total->addTotalAmount($amount['code'], $amount['value']);
                    $total->setBaseTotalAmount($amount['code'], $amount['value']);
                }
            }
        }

        return $this;
    }

    /**
     * @param Quote $quote
     * @return int|mixed
     */
    private function getValue(Quote $quote)
    {
        if ($this->value === null) {
            $this->value = [];
            if ($quote->getCustomerId()) {
                try {
                    $this->boxplosiveCustomerRepository->getByCustomerId((int)$quote->getCustomerId());
                    $boxplosiveSubtotal = $this->pointOfSaleRepository->getSubtotal($quote);
                    if ($boxplosiveSubtotal['success']) {
                        $transaction = [
                            'transaction_id' => $boxplosiveSubtotal['transaction_id'],
                            'created_at' => $boxplosiveSubtotal['created_at']
                        ];
                        $this->pointOfSaleRepository->cancel($quote, $transaction);
                    }

                    if (isset($boxplosiveSubtotal['discount'])) {
                        $i = 0;
                        foreach ($boxplosiveSubtotal['discount'] as $discount) {
                            $this->value[$i] = [
                                'code' => 'boxplosive_discount_' . $i,
                                'title' => __($discount['title']),
                                'value' => $discount['value']
                            ];
                            $i++;
                        }

                        $boxplosiveDiscount = $this->boxplosiveDiscountRepository->getByQuoteId((int)$quote->getId());
                        $boxplosiveDiscount->setQuoteId((int)$quote->getId())
                            ->setDiscount($boxplosiveSubtotal['discount']);
                        $this->boxplosiveDiscountRepository->save($boxplosiveDiscount);
                    }
                } catch (\Exception $e) {
                    return $this->value;
                }
            }
        }
        return $this->value;
    }

    /**
     * @param Quote $quote
     * @param Total $total
     * @return array
     */
    public function fetch(Quote $quote, Total $total)
    {
        return $this->getValue($quote);
    }

    /**
     * Get Subtotal label
     *
     * @return Phrase
     */
    public function getLabel(): Phrase
    {
        return __('Boxplosive Discount');
    }

    /**
     * @param Total $total
     */
    protected function clearValues(Total $total)
    {
        $total->setTotalAmount('subtotal', 0);
        $total->setBaseTotalAmount('subtotal', 0);
        $total->setTotalAmount('tax', 0);
        $total->setBaseTotalAmount('tax', 0);
        $total->setTotalAmount('discount_tax_compensation', 0);
        $total->setBaseTotalAmount('discount_tax_compensation', 0);
        $total->setTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setBaseTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setSubtotalInclTax(0);
        $total->setBaseSubtotalInclTax(0);
    }
}
