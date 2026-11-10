<?php

declare(strict_types=1);

namespace Sylphian\LinkWarning\Pub\Controller;

use XF\Mvc\ParameterBag;
use XF\Mvc\Reply\AbstractReply;
use XF\Pub\Controller\AbstractController;

class LeaveSite extends AbstractController
{
	public function actionIndex(ParameterBag $params): AbstractReply
	{
		$url = trim($this->filter('url', 'str'));
		$urlParts = parse_url($url);

		if (
			!$urlParts
			|| empty($urlParts['host'])
			|| !in_array(strtolower($urlParts['scheme'] ?? ''), ['http', 'https'], true)
		)
		{
			return $this->error(\XF::phrase('sylphian_link_warning_invalid_link'));
		}

		$viewParams = [
			'url' => $url,
			'host' => $urlParts['host'],
			'isOverlay' => $this->filter('_xfWithData', 'bool'),
		];

		return $this->view(
			'Sylphian\LinkWarning:LeaveSite\Index',
			'sylphian_link_warning_leave',
			$viewParams
		);
	}
}
