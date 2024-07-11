<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Boxplosive\Connect\Block\Order;

use Boxplosive\Connect\Api\Discount\RepositoryInterface as BoxplosiveDiscountRepository;
use Magento\Framework\DataObject;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;

class Totals extends \Magento\Sales\Block\Order\Totals
{
    /**
     * @var BoxplosiveDiscountRepository
     */
    private $boxplosiveDiscountRepository;

    /**
     * Totals constructor.
     * @param Context $context
     * @param Registry $registry
     * @param BoxplosiveDiscountRepository $boxplosiveDiscountRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        BoxplosiveDiscountRepository $boxplosiveDiscountRepository,
        array $data = []
    ) {
        $this->boxplosiveDiscountRepository = $boxplosiveDiscountRepository;
        parent::__construct($context, $registry, $data);
    }

    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $order = $parent->getOrder();
        $quoteId = $order->getQuoteId();

        $boxplosiveDiscount = $this->boxplosiveDiscountRepository->getByQuoteId((int)$quoteId);

        if ($discountData = $boxplosiveDiscount->getDiscount()) {
            foreach ($discountData as $key => $value) {
                $parent->addTotal(
                    new DataObject(
                        [
                            'code' => 'boxplosive_discount_' . $key,
                            'strong' => false,
                            'value' => $value['value'],
                            'label' => __($value['title']),
                        ]
                    ),
                    'boxplosive_discount_' . $key
                );
            }
        }

        return $this;
    }
}
