<?php
/**
 * Copyright Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Boxplosive\Connect\Model\Total;

use Boxplosive\Connect\Api\Discount\RepositoryInterface as BoxplosiveDiscountRepository;
use Magento\Framework\Exception\InputException;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal;

/**
 * Class BoxplosiveCreditmemo
 */
class BoxplosiveCreditmemo extends AbstractTotal
{

    /**
     * @var BoxplosiveDiscountRepository
     */
    private $boxplosiveDiscountRepository;

    /**
     * BoxplosiveCreditmemo constructor.
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
     * @param Creditmemo $creditmemo
     * @return $this
     * @throws InputException
     */
    public function collect(Creditmemo $creditmemo): self
    {
        $quoteId = $creditmemo->getOrder()->getQuoteId();
        $boxplosiveDiscount = $this->boxplosiveDiscountRepository->getByQuoteId((int)$quoteId);

        if ($discountData = $boxplosiveDiscount->getDiscount()) {
            foreach ($discountData as $key => $value) {
                $creditmemo->setData('boxplosive_discount_' . $key, $value['value']);
                $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $value['value']);
                $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $value['value']);
            }
        }

        return $this;
    }
}
