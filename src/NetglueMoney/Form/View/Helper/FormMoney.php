<?php

namespace NetglueMoney\Form\View\Helper;

use Zend\Form\View\Helper\AbstractHelper;
use Zend\Form\ElementInterface;

use NetglueMoney\Form\Element\MoneyElement;
use NetglueMoney\Exception;



class FormMoney extends AbstractHelper
{

    /**
     * Invoke helper as function
     *
     * Proxies to {@link render()}.
     *
     * @param  ElementInterface|null $element
     * @return string|FormElement
     */
    public function __invoke(ElementInterface $element = null)
    {
        if (!$element) {
            return $this;
        }

        return $this->render($element);
    }

    /**
     * Render
     * @return string
     */
    public function render(ElementInterface $element)
    {
        if(!$element instanceof MoneyElement) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an instance of NetglueMoney\Form\Element\MoneyElement',
                __METHOD__
            ));
        }

        $atrs = $element->getAttributes();
        unset($atrs['name']);
        $view = $this->getView();

        $helper = $view->plugin('formText');
        $codeElement = $helper($element->getCurrencyElement());
        $amountElement = $helper($element->getAmountElement());

        $markup = sprintf('<div %s>%s%s</div>',
            $this->createAttributesString($atrs),
            $codeElement,
            $amountElement
        );

        return $markup;
        var_dump($element->getAttributes());

        return 'MONEY!';
    }

}
