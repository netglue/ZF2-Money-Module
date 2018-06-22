<?php
declare(strict_types=1);

namespace NetglueMoney\View\Helper;

use Zend\Form\View\Helper\FormInput;
use Locale;
use NumberFormatter;
use Zend\Form\Exception;
use Zend\Form\ElementInterface;

class FormMoney extends FormInput
{

    /**
     * Locale String
     * @var string|NULL
     */
    private $locale;

    /**
     * Formatter instances
     *
     * @var array
     */
    private $formatters = [];

    /**
     * Sets the locale option
     *
     * @param  string|null    $locale
     */
    public function setLocale($locale = null) : void
    {
        $this->locale = $locale;
    }

    /**
     * Returns the locale option
     *
     * @return string
     */
    public function getLocale() : string
    {
        if (null === $this->locale) {
            return Locale::getDefault();
        }

        return $this->locale;
    }

    /**
     * Render a form <input> element from the provided $element
     *
     * @param  ElementInterface $element
     * @throws Exception\DomainException
     * @return string
     */
    public function render(ElementInterface $element) : string
    {
        $name = $element->getName();
        if ($name === null || $name === '') {
            throw new Exception\DomainException(sprintf(
                '%s requires that the element has an assigned name; none discovered',
                __METHOD__
            ));
        }

        $attributes          = $element->getAttributes();
        $attributes['name']  = $name;
        $attributes['type']  = $this->getType($element);
        $attributes['value'] = $this->formatValue($element->getValue());
        return sprintf(
            '<input %s%s',
            $this->createAttributesString($attributes),
            $this->getInlineClosingBracket()
        );
    }

    /**
     * Format the input to localised decimal format according to current locale
     * @param int|float $value
     * @return string
     */
    public function formatValue($value) : string
    {
        $formatter = $this->getFormatter();
        return $formatter->format($value);
    }

    /**
     * Return the formatter for the given locale string
     * @param string $locale
     * @return NumberFormatter
     */
    public function getFormatter($locale = null) : NumberFormatter
    {
        if (null === $locale) {
            $locale = $this->getLocale();
        }
        $formatterId = md5(strtolower($locale));
        if (! isset($this->formatters[$formatterId])) {
            $this->formatters[$formatterId] = new NumberFormatter(
                $locale,
                NumberFormatter::DECIMAL
            );
        }
        return $this->formatters[$formatterId];
    }
}
