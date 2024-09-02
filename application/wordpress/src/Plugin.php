<?php

namespace Smolblog\WP;

use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\WP\Helpers\DatabaseHelper;
use Smolblog\WP\Helpers\SiteHelper;
use Smolblog\WP\Helpers\UserHelper;

class Plugin {
	public static function BootstrapMain(): void {
		DatabaseHelper::update_schema();

		$app = new Smolblog();

		// Ensure the async hook is in place
		add_action(
			'smolblog_async_dispatch',
			fn($class, $message) => $app->container->get(MessageBus::class)->dispatch($class::fromArray($message)),
			10,
			2
		);

		add_action( 'rest_api_init', fn() => $app->container->get(EndpointRegistrar::class)->init() );
	}
}
