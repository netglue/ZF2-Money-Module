<?php
/**
 * Most of this was copied from
 * \Zend\I18n\View\Helper\CurrencyFormat but re-implemented because strict standards
 * prevent me from overriding __invoke()
 */

namespace NetglueMoney\View\Helper;


use Locale;
use NumberFormatter;
use NetglueMoney\I18n\LocaleAwareInterface;
use NetglueMoney\Exception;
use Zend\View\Helper\AbstractHelper;
use NetglueMoney\Money\Money;
use NetglueMoney\Money\Currency;


class MoneyFormat extends AbstractHelper implements LocaleAwareInterface
{

    /**
     * Locale String
     * @var string|NULL
     */
    protected $locale;

    /**
     * Formatter instances
     *
     * @var array
     */
    protected $formatters = array();

    /**
     * Currency pattern
     *
     * @var string
     */
    protected $currencyPattern;

    /**
     * If set to true, the currency will be returned with two decimals
     *
     * @var bool
     */
    protected $showDecimals = true;

    /**
     * @throws Exception\ExtensionNotLoadedException if ext/intl is not present
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        if (!extension_loaded('intl')) {
            throw new Exception\ExtensionNotLoadedException(sprintf(
                '%s component requires the intl PHP extension',
                __NAMESPACE__
            ));
        }
    }

    /**
     * Invoke - accepts money instance
     *
     * @param  Money  $money
     * @param  string $locale i.e. en_GB or null to leave us to try and work it out
     * @param  bool   $showDecimals Defaults to true
     * @param  string $pattern A pattern accepted by \NumberFormatter used to format the currency
     * @link http://www.icu-project.org/apiref/icu4c/classDecimalFormat.html#details
     * @return string
     */
    public function __invoke(
        Money $money,
        $locale       = null,
        $showDecimals = null,
        $pattern      = null
    ) {

        if (null === $locale) {
            $locale = $this->getLocale();
        }
        if (null === $showDecimals) {
            $showDecimals = $this->shouldShowDecimals();
        }
        if (null === $pattern) {
            $pattern = $this->getCurrencyPattern();
        }

        return $this->formatCurrency($money, $locale, $showDecimals, $pattern);
    }

    /**
     * Format a number
     *
     * @param  Money  $money
     * @param  string $locale
     * @param  bool   $showDecimals
     * @param  string $pattern
     * @return string
     */
    protected function formatCurrency(
        Money $money,
        $locale,
        $showDecimals,
        $pattern
    ) {
        $formatter = $this->getFormatter($locale);

        // Should we look up the default pattern and restore it after we're done formatting?
        if ($pattern !== null) {
            $formatter->setPattern($pattern);
        }

        $currency = $money->getCurrency();
        $amount = $money->getAmount() / $currency->getSubUnit();
        $code = $currency->getCurrencyCode();

        $formatter->setTextAttribute(NumberFormatter::CURRENCY_CODE, $code);

        if ($showDecimals) {
            $digits = $money->getCurrency()->getDefaultFractionDigits();
            $formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, $digits);
        } else {
            $formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, 0);
        }

        return $formatter->formatCurrency($amount, $code);
    }

    /**
     * Return the formatter for the given locale string
     * @param string $locale
     * @return NumberFormatter
     */
    public function getFormatter($locale = null)
    {
        if (null === $locale) {
            $locale = $this->getLocale();
        }
        $formatterId = md5(strtolower($locale));
        if (!isset($this->formatters[$formatterId])) {
            $this->formatters[$formatterId] = new NumberFormatter(
                $locale,
                NumberFormatter::CURRENCY
            );
        }
        return $this->formatters[$formatterId];
    }

    /**
     * Set Locale for this instance
     * @param  string $locale
     * @return self
     */
    public function setLocale($locale)
    {
        $this->locale = (string) $locale;

        return $this;
    }

    /**
     * Return the set locale or the system wide default if not set
     * @return string
     */
    public function getLocale()
    {
        if (null === $this->locale) {
            $this->locale = Locale::getDefault();
        }

        return $this->locale;
    }

    /**
     * Set the currency pattern
     *
     * @param  string $currencyPattern
     * @return CurrencyFormat
     */
    public function setCurrencyPattern($currencyPattern)
    {
        $this->currencyPattern = $currencyPattern;
        return $this;
    }

    /**
     * Get the currency pattern
     *
     * @return string
     */
    public function getCurrencyPattern()
    {
        return $this->currencyPattern;
    }

    /**
     * Set if the view helper should show decimals
     *
     * @param  bool $showDecimals
     * @return CurrencyFormat
     */
    public function setShouldShowDecimals($showDecimals)
    {
        $this->showDecimals = (bool) $showDecimals;
        return $this;
    }

    /**
     * Get if the view helper should show decimals
     *
     * @return bool
     */
    public function shouldShowDecimals()
    {
        return $this->showDecimals;
    }

}
