<?php

namespace Smolblog\WP;

use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\Identifier;
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

		add_action( 'signup_extra_fields', function() {
			?>
			<label for="subscription_key">Subscription Key:</label>
			<input name="subscription_key" type="text" id="subscription_key" value="" autocapitalize="none" autocorrect="off" maxlength="60" required="required" aria-describedby="wp-signup-subkey-description">
			<p id="wp-signup-subkey-description">Provide the access key you received.</p>
			<?php
		}, 10, 0 );

		add_filter( 'registration_errors', function($errors, $sanitized_user_login, $user_email) {
			if (!isset($_POST['subscription_key'])) {
				$errors->add( 'no_sub_key', 'Smolblog is in private beta; a subscription key is required.' );
			}

			$codes = [];
			if (is_readable(__DIR__ . '../../../registration.json')) {
				$registrationJson = file_get_contents(__DIR__ . '../../../registration.json');
				$codes = json_validate($registrationJson) ? json_decode($registrationJson, associative: true) : [];
			}

			if (!in_array($_POST['subscription_key'], array_keys($codes))) {
				$errors->add( 'bad_sub_key', 'Could not validate subscription key.' );
			}
			if (!in_array($user_email, $codes[$_POST['subscription_key']])) {
				$errors->add( 'bad_email', 'Email is not valid for given subscription key.' );
			}
		}, 10, 3);
	}
}
