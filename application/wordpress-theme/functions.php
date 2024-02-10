<?php

function smolblog_extra_fields($errors) {
	?>
	<!--
		<?php print_r($errors); ?>
	-->
	<label for="subscription_key">Subscription Key:</label>
	<!-- <p class="error">That email address has already been used. Please check your inbox for an activation email. It will become available in a couple of days if you do nothing.</p> -->
	<input name="subscription_key" type="text" id="subscription_key" value="" autocapitalize="none" autocorrect="off" maxlength="60" required="required" aria-describedby="wp-signup-subkey-description">
	<p id="wp-signup-subkey-description">Provide the access key you received.</p>
	<?php
}
add_action( 'signup_extra_fields', 'smolblog_extra_fields', 10, 1 );

function smolblog_registration_errors($errors, $sanitized_user_login, $user_email) {
	if (empty($_POST['subscription_key'])) {
		$errors->add( 'no_sub_key', 'Smolblog is in private beta; a subscription key is required.' );
		// return $errors;
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

	wp_die('<pre>'.print_r([$_POST, $errors], true).'</pre>');
	return $errors;
}
add_filter( 'registration_errors', 'smolblog_registration_errors', 5, 3);
