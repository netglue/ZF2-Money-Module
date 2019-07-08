<?php
declare(strict_types=1);

namespace NetglueMoneyTest\Framework;

use NetglueMoney\Service\CurrencyList;
use NetglueMoney\Validator\CurrencyCode;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Zend\Form\Factory;
use Zend\Form\FormElementManager\FormElementManagerV3Polyfill;
use Zend\InputFilter\Factory as InputFilterFactory;
use Zend\InputFilter\InputFilterPluginManager;
use Zend\Mvc\Application;
use Zend\Validator\ValidatorPluginManager;

class TestCase extends PHPUnitTestCase
{

    /** @var Application */
    protected $application;

    protected function getZendApplication() : Application
    {
        if (! $this->application) {
            $this->application = $this->initZendApplication();
            $this->assertContainerHasDependencies();
        }
        return $this->application;
    }

    private function initZendApplication() : Application
    {
        $appConfig = [
            'modules' => [
                'Zend\I18n',
                'Zend\Form',
                'Zend\InputFilter',
                'Zend\Filter',
                'Zend\Router',
                'Zend\Hydrator',
                'Zend\Validator',
                'NetglueMoney',
            ],
            'module_listener_options' => [
                'module_paths' => [
                    __DIR__ . '/../../../vendor',
                    __DIR__ . '/../../../',
                ],
            ],
        ];
        return Application::init($appConfig);
    }

    private function assertContainerHasDependencies() : void
    {
        $app = $this->application;
        $container = $app->getServiceManager();
        $this->assertTrue($container->has(CurrencyList::class));
        $validators = $container->get(ValidatorPluginManager::class);
        $this->assertTrue($validators->has(CurrencyCode::class));
    }

    protected function getFormElementManager() : FormElementManagerV3Polyfill
    {
        $app = $this->getZendApplication();
        $container = $app->getServiceManager();
        return $container->get('FormElementManager');
    }

    protected function getInputFilterFactory() : InputFilterFactory
    {
        $container = $this->getZendApplication()->getServiceManager();
        return new InputFilterFactory(
            $container->get(InputFilterPluginManager::class)
        );
    }

    protected function getFormFactory() : Factory
    {
        $container = $this->getZendApplication()->getServiceManager();
        return new Factory(
            $container->get('FormElementManager'),
            $this->getInputFilterFactory()
        );
    }
}
