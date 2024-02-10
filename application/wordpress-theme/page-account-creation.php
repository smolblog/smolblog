<?php
	$success = false;
	$errors = [];

	if ( ! empty( $_POST['wp-submit'] ) ) {
		if ( ! wp_verify_nonce( $_POST['_signup_form'], 'signup_form_' . $_POST['signup_form_id'] ) ) {
			$errors[] = 'Unable to submit this form; please try again.';
		}

		// Check the username.
		$sanitized_user_login = sanitize_user( $_POST['log'] );
		if ( '' === $sanitized_user_login ) {
			$errors[] = 'Please enter a username.';
		} elseif ( ! validate_username( $_POST['log'] ) ) {
			$errors[] = 'This username is invalid because it uses illegal characters. Please enter a valid username.';
			$sanitized_user_login = '';
		} elseif ( username_exists( $sanitized_user_login ) ) {
			$errors[] = 'This username is already registered. Please choose another one.';
		} else {
			$illegal_user_logins = (array) apply_filters( 'illegal_user_logins', array() );
			if ( in_array( strtolower( $sanitized_user_login ), array_map( 'strtolower', $illegal_user_logins ), true ) ) {
				$errors[] = 'Sorry, that username is not allowed.';
			}
		}

		// Check the email address.
		if ( '' ===  $_POST['email'] ) {
			$errors[] = 'Please type your email address.';
		} elseif ( ! is_email(  $_POST['email'] ) ) {
			$errors[] = 'The email address is not correct.';
		} elseif ( email_exists(  $_POST['email'] ) ) {
			$errors[] = 'This email address is already registered. <a href="/wp-login.php">Log in</a> with this address or choose another one.';
		}

		if ( $_POST['pwd'] !== $_POST['pwd-confirm'] ) {
			$errors[] = 'Passwords do not match.';
		}

		if ( empty( $_POST['key'])) {
			$errors[] = 'Smolblog is in private beta; a subscription key is required.';
		} else {
			$codes = [];
			if ( is_readable(__DIR__ . '../../../registration.json' ) ) {
				$registrationJson = file_get_contents(__DIR__ . '../../../registration.json');
				$codes = json_validate($registrationJson) ? json_decode($registrationJson, associative: true) : [];
			}

			if ( ! in_array( $_POST['key'], array_keys( $codes ) ) ) {
				$errors[] = 'Could not validate subscription key.';
			} elseif ( ! in_array( $user_email, $codes[ $_POST['key'] ] ) ) {
				$errors[] = 'Email is not valid for given subscription key.';
			}
		}
	}
?>
<!DOCTYPE html>
<html>

<head>
	<title>Register | Smolblog</title>
	<?php wp_head(); ?>

	<link rel='stylesheet' id='buttons-css' href='/wp-includes/css/buttons.min.css?ver=6.4.3' media='all'/>
	<link rel='stylesheet' id='forms-css' href='/wp-admin/css/forms.min.css?ver=6.4.3' media='all'/>
	<link rel='stylesheet' id='l10n-css' href='/wp-admin/css/l10n.min.css?ver=6.4.3' media='all'/>
	<link rel='stylesheet' id='login-css' href='/wp-admin/css/login.min.css?ver=6.4.3' media='all'/>
	<meta name='referrer' content='strict-origin-when-cross-origin'/>
	<meta name="viewport" content="width=device-width"/>
	<style type="text/css">
		body.login {
				display: flex;
				background: #191b20;
				color: #e9e3db;

				& div#login h1 a {
					background-image: url(<?php echo get_theme_file_uri( 'smolblog.wordmark.ondark.png' ); ?>);
					height:65px;
					width:320px;
					background-size: contain;
					background-repeat: no-repeat;
					padding-bottom: 30px;
				}

				& div#login form,
				& .message,
				& .success,
				& .notice {
					color: #3c434a;
				}

				& p#backtoblog a, & p#nav a {
					color: #9cd398;

					&:hover {
						color: #b7dfb3;
					}
				}
			}
	</style>
</head>

<body class="login wp-core-ui">
	<div id="login">
		<h1>
			<a href="/">Smolblog</a>
		</h1>

		<?php if ($success) : ?>
			<div class="success">
				<p>Welcome to Smolblog! <a href="https://dashboard.smolblog.com/">Head to the dashboard</a> to get started.</p>
			</div>
		<?php else : ?>
			<?php if (!empty($errors)) : ?>
				<div class="notice notice-error">
					<?php foreach ($errors as $err) : ?>
						<p><?= $err ?></p>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<form name="loginform" id="loginform" method="post">
				<p>
					<label for="registration_key">Subscription Key</label>
					<input type="text" name="key" value="<?= esc_attr( $_POST['key'] ?? '' ) ?>" id="registration_key" class="input" size="20" autocapitalize="off" autocomplete="username" required="required" />
				</p>
				<p>
					<label for="user_login">Username</label>
					<input type="text" name="log" value="<?= esc_attr( $_POST['log'] ?? '' ) ?>" id="user_login" class="input" size="20" autocapitalize="off" autocomplete="username" required="required" />
				</p>
				<p>
					<label for="user_email">Email Address</label>
					<input type="email" name="email" value="<?= esc_attr( $_POST['email'] ?? '' ) ?>" id="user_email" class="input" size="20" autocapitalize="off" autocomplete="email" required="required" />
				</p>

				<div class="user-pass-wrap">
					<label for="user_pass">Password</label>
					<div class="wp-pwd">
						<input type="password" name="pwd" id="user_pass" class="input password-input" value="" size="20" autocomplete="new-password" spellcheck="false" required="required" />
					</div>
				</div>
				<div class="user-pass-wrap">
					<label for="user_pass">Confirm Password</label>
					<div class="wp-pwd">
						<input type="password" name="pwd-confirm" id="user_pass_confirm" class="input password-input" value="" size="20" autocomplete="new-password" spellcheck="false" required="required" />
					</div>
				</div>
				<p class="submit">
					<?php signup_nonce_fields(); ?>
					<input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" value="Register" />
				</p>
			</form>

			<p id="nav">
				<a class="wp-login-register" href="/wp-login.php">Log in with an existing account</a>
			</p>
			<p id="backtoblog">
				<a href="/">&larr; Go to smolblog</a>
			</p>
		<?php endif; ?>
	</div>
</body>

</html>
