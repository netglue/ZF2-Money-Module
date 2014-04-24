<?php

namespace NetglueMoney\Form\View\Helper;

use Zend\Form\View\Helper\AbstractHelper;
use Zend\Form\ElementInterface;

use NetglueMoney\Form\MoneyFieldset;
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
    public function render(ElementInterface $fieldset)
    {
        if(!$fieldset instanceof MoneyFieldset) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an instance of NetglueMoney\Form\MoneyFieldset',
                __METHOD__
            ));
        }

        $atrs = $fieldset->getAttributes();
        unset($atrs['name']);
        $view = $this->getView();

        $helper = $view->plugin('formText');
        $codeElement = $helper($fieldset->get('currency'));
        $amountElement = $helper($fieldset->get('amount'));

        $errorHelper = $view->plugin('formElementErrors');


        $markup = sprintf('<div %s>%s%s%s%s</div>',
            $this->createAttributesString($atrs),
            $codeElement,
            $amountElement,
            $errorHelper($fieldset->get('currency')),
            $errorHelper($fieldset->get('amount'))
        );

        return $markup;
    }

}
