<?php

namespace NetglueMoney\I18n;

interface LocaleAwareInterface
{

    /**
     * Set Locale for this instance
     * @param string $locale
     * @return LocaleAwareInterface
     */
    public function setLocale($locale);

    /**
     * Return the set locale or the system wide default if not set
     * @return string
     */
    public function getLocale();

}
