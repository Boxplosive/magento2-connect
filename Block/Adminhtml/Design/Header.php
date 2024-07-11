<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Boxplosive\Connect\Block\Adminhtml\Design;

use Boxplosive\Connect\Api\Config\RepositoryInterface as ConfigRepository;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * System Configuration Module information Block
 */
class Header extends Field
{

    /**
     * @var string
     */
    protected $_template = 'Boxplosive_Connect::system/config/fieldset/header.phtml';

    /**
     * @var ConfigRepository
     */
    private $configRepository;

    /**
     * Header constructor.
     *
     * @param Context $context
     * @param ConfigRepository $config
     */
    public function __construct(
        Context $context,
        ConfigRepository $config
    ) {
        $this->configRepository = $config;
        parent::__construct($context);
    }

    /**
     * @param AbstractElement $element
     *
     * @return string
     */
    public function render(AbstractElement $element): string
    {
        $element->addClass('magmodules');
        return $this->toHtml();
    }

    /**
     * Support link for extension.
     *
     * @return string
     */
    public function getSupportLink(): string
    {
        return $this->configRepository->getSupportLink();
    }
}
