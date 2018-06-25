<?php
declare(strict_types=1);

namespace NetglueMoney;

use NetglueMoney\Exception;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\FormElementProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\ValidatorProviderInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;

class Module implements
    ServiceProviderInterface,
    FormElementProviderInterface,
    ConfigProviderInterface,
    ValidatorProviderInterface,
    ViewHelperProviderInterface
{
    /** @var ConfigProvider */
    private $config;

    /**
     * @throws Exception\ExtensionNotLoadedException if ext/intl is not present
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        if (! extension_loaded('intl')) {
            throw new Exception\ExtensionNotLoadedException(sprintf(
                '%s component requires the intl PHP extension',
                __NAMESPACE__
            ));
        }
        $this->config = new ConfigProvider();
    }

    public function getServiceConfig() : array
    {
        return $this->config->getDependencyConfig();
    }

    public function getConfig() : array
    {
        return [
            'ng_money' => $this->config->getModuleConfig(),
            'view_manager' => [
                'template_map' => $this->config->getTemplateMap(),
            ],
        ];
    }

    public function getFormElementConfig() : array
    {
        return $this->config->getFormElementConfig();
    }

    public function getValidatorConfig() : array
    {
        return $this->config->getValidatorConfig();
    }

    public function getViewHelperConfig() : array
    {
        return $this->config->getViewHelperConfig();
    }
}
