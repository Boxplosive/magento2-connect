<?php
/**
 * Copyright Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Boxplosive\Connect\Model\Total;

use Boxplosive\Connect\Api\Discount\RepositoryInterface as BoxplosiveDiscountRepository;
use Magento\Framework\Exception\InputException;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Invoice\Total\AbstractTotal;

/**
 * Class BoxplosiveInvoice
 */
class BoxplosiveInvoice extends AbstractTotal
{

    /**
     * @var BoxplosiveDiscountRepository
     */
    private $boxplosiveDiscountRepository;

    /**
     * BoxplosiveInvoice constructor.
     * @param BoxplosiveDiscountRepository $boxplosiveDiscountRepository
     * @param array $data
     */
    public function __construct(
        BoxplosiveDiscountRepository $boxplosiveDiscountRepository,
        array $data = []
    ) {
        $this->boxplosiveDiscountRepository = $boxplosiveDiscountRepository;
        parent::__construct($data);
    }

    /**
     * @param Invoice $invoice
     * @return $this
     * @throws InputException
     */
    public function collect(Invoice $invoice): self
    {
        $quoteId = $invoice->getOrder()->getQuoteId();
        $boxplosiveDiscount = $this->boxplosiveDiscountRepository->getByQuoteId((int)$quoteId);

        if ($discountData = $boxplosiveDiscount->getDiscount()) {
            foreach ($discountData as $key => $value) {
                $invoice->setData('boxplosive_discount_' . $key, $value['value']);
                $invoice->setGrandTotal($invoice->getGrandTotal() + $value['value']);
                $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $value['value']);
            }
        }

        return $this;
    }
}
