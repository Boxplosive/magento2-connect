<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Boxplosive\Connect\Block\Adminhtml\System\Config\Button;

use Exception;
use Magento\Backend\Block\Widget\Button;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Boxplosive\Connect\Api\Log\RepositoryInterface as LogRepository;

/**
 * Log check button class
 */
class Log extends Field
{

    /**
     * @var string
     */
    protected $_template = 'Boxplosive_Connect::system/config/button/log.phtml';

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
     * @param string $type
     * @return string
     */
    public function getDownloadUrl(string $type): string
    {
        return $this->getUrl('boxplosive/log/stream', ['type' => $type]);
    }

    /**
     * @param string $type
     * @return string
     */
    public function getButtonHtml(string $type): string
    {
        try {
            return $this->getLayout()
                ->createBlock(Button::class)
                ->setData([
                    'id' => 'mm-ui-button_' . $type,
                    'label' => __('Show last %1 %2 log records', LogRepository::STREAM_DEFAULT_LIMIT, $type)
                ])->toHtml();
        } catch (Exception $e) {
            return '';
        }
    }
}
