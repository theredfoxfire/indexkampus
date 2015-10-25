<?php

namespace IndexBundle\Twig;

use Symfony\Component\Intl\Intl;

class LocaleExtension extends \Twig_Extension
{
	private $locales;

	public function __construct($locales)
	{
		$this->locales = $locales;
	}

	public function getFunctions()
	{
		return array(
			new \Twig_SimpleFunction('locales', array($this, 'getLocales')),
		);
	}

	public function getLocales()
	{
		$localesCodes = explode('|', $this->locales);

		foreach ($localesCodes as $localeCode) {
			$locales[] = array('code' => $localeCode, 'name' => Intl::getLocaleBundle()->getLocaleName($localeCode, $localeCode));
		}

		return $locales;
	}

	public function getName()
	{
		return 'app.locale_extension';
	}
}