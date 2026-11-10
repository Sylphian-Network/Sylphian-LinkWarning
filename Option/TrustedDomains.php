<?php

declare(strict_types=1);

namespace Sylphian\LinkWarning\Option;

use XF\Entity\Option;
use XF\Option\AbstractOption;

class TrustedDomains extends AbstractOption
{
	public static function verifyOption(&$value, Option $option): bool
	{
		$domains = [];

		foreach (preg_split('/[\s,]+/', (string) $value, -1, PREG_SPLIT_NO_EMPTY) AS $entry)
		{
			$domain = static::normalizeDomain($entry);
			if ($domain === null)
			{
				$option->error(\XF::phrase('sylphian_link_warning_invalid_trusted_domain_x', ['domain' => $entry]), $option->option_id);
				return false;
			}

			$domains[$domain] = $domain;
		}

		$value = implode("\n", $domains);

		return true;
	}

	protected static function normalizeDomain(string $entry): ?string
	{
		$domain = strtolower($entry);
		$domain = preg_replace('#^[a-z][a-z0-9+.-]*://#', '', $domain);
		$domain = preg_replace('#^\*\.#', '', $domain);
		$domain = preg_replace('#[/:?\#].*$#s', '', $domain);

		if (function_exists('idn_to_ascii') && preg_match('/[^\x00-\x7F]/', $domain))
		{
			$domain = idn_to_ascii($domain, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46) ?: $domain;
		}

		if (!preg_match('/^(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/', $domain))
		{
			return null;
		}

		return $domain;
	}
}
