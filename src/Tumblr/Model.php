<?php

namespace Smolblog\Tumblr;

use Smolblog\Framework\Objects\DomainModel;

class Model extends DomainModel {
	public const SERVICES = [
		TumblrConnector::class => [
			'factory' => TumblrClientFactory::class,
		],
	];

	public static function getDependencyMap(): array
	{
		if (!defined('SMOLBLOG_TUMBLR_APPLICATION_KEY')) {
			return self::SERVICES;
		}

		return [
			...self::SERVICES,
			TumblrClientFactory::class => [
				'appKey' => fn() => SMOLBLOG_TUMBLR_APPLICATION_KEY,
				'appSecret' => fn() => SMOLBLOG_TUMBLR_APPLICATION_SECRET,
			],
		];
	}
}
