<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Boxplosive\Connect\Block\Adminhtml\System\Config\Button;

use Boxplosive\Connect\Model\Config\Repository as ConfigRepository;
use Exception;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Button;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Version check button class
 */
class VersionCheck extends Field
{

    /**
     * @var string
     */
    protected $_template = 'Boxplosive_Connect::system/config/button/version.phtml';
    /**
     * @var ConfigRepository
     */
    private $configRepository;

    /**
     * VersionCheck constructor.
     * @param Context $context
     * @param ConfigRepository $configRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        ConfigRepository $configRepository,
        array $data = []
    ) {
        $this->configRepository = $configRepository;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return (string)$this->configRepository->getExtensionVersion();
    }

    /**
     * @inheritDoc
     */
    public function render(AbstractElement $element): string
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * @inheritDoc
     */
    public function _getElementHtml(AbstractElement $element): string
    {
        return $this->_toHtml();
    }

    /**
     * @return string
     */
    public function getVersionCheckUrl(): string
    {
        return $this->getUrl('boxplosive/versionCheck/index');
    }

    /**
     * @return string
     */
    public function getChangeLogUrl(): string
    {
        return $this->getUrl('boxplosive/versionCheck/changelog');
    }

    /**
     * @return string
     */
    public function getButtonHtml(): string
    {
        try {
            return $this->getLayout()
                ->createBlock(Button::class)
                ->setData([
                    'id' => 'mm-ui-button_version',
                    'label' => __('Check for latest versions')
                ])->toHtml();
        } catch (Exception $e) {
            return '';
        }
    }
}
