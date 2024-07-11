<?php
/**
 * Copyright Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Boxplosive\Connect\Model\Total;

use Boxplosive\Connect\Api\Discount\RepositoryInterface as BoxplosiveDiscountRepository;
use Magento\Framework\Exception\InputException;
use Magento\Sales\Model\Order\Pdf\Total\DefaultTotal;
use Magento\Tax\Helper\Data;
use Magento\Tax\Model\Calculation;
use Magento\Tax\Model\ResourceModel\Sales\Order\Tax\CollectionFactory;

class BoxplosivePdf extends DefaultTotal
{
    /**
     * @var BoxplosiveDiscountRepository
     */
    private $boxplosiveDiscountRepository;

    /**
     * @param Data $taxHelper
     * @param Calculation $taxCalculation
     * @param CollectionFactory $ordersFactory
     * @param BoxplosiveDiscountRepository $boxplosiveDiscountRepository
     * @param array $data
     */
    public function __construct(
        Data $taxHelper,
        Calculation $taxCalculation,
        CollectionFactory $ordersFactory,
        BoxplosiveDiscountRepository $boxplosiveDiscountRepository,
        array $data = []
    ) {
        $this->boxplosiveDiscountRepository = $boxplosiveDiscountRepository;
        parent::__construct($taxHelper, $taxCalculation, $ordersFactory, $data);
    }

    /**
     * Check if tax amount should be included to grandtotal block
     * array(
     *  $index => array(
     *      'amount'   => $amount,
     *      'label'    => $label,
     *      'font_size'=> $font_size
     *  )
     * )
     * @return array
     * @throws InputException
     */
    public function getTotalsForDisplay(): array
    {
        $quoteId = $this->getOrder()->getQuoteId();

        $boxplosiveDiscount = $this->boxplosiveDiscountRepository->getByQuoteId((int)$quoteId);
        $totals = [];

        if ($discountData = $boxplosiveDiscount->getDiscount()) {
            $fontSize = $this->getData('font_size') ? $this->getData('font_size') : 7;
            foreach ($discountData as $key => $value) {
                $totals[] = [
                    'amount' => $value['value'],
                    'label' => $value['title'],
                    'font_size' => $fontSize
                ];
            }
        }

        return array_merge($totals, parent::getTotalsForDisplay());
    }
}
