<?php

namespace Smolblog\Tumblr;

use Smolblog\Foundation\DomainModel;

/**
 * Domain model for interacting with the Tumblr service.
 */
class Model extends DomainModel {
	public const SERVICES = [
		TumblrConnector::class => [
			'factory' => TumblrClientFactory::class,
		],
	];

	/**
	 * Get services and their dependencies for the container.
	 *
	 * Overriding the function because arrow functions cannot be added to a constant.
	 *
	 * @return array
	 */
	public static function getDependencyMap(): array {
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
