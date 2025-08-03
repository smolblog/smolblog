<!DOCTYPE html>
<html>
	<head>
		<?php wp_head(); ?>
		<style type="text/css">
			body {
				display: flex;
				flex-direction: column;
				justify-content: center;
				align-items: center;
				background-color: #191b20;
				color: #e9e3db;
				height: 100vh;
			}

				h1 {
					text-align: center;
					padding-bottom: 30px;
				}

				h2, hr, #footer {
					display: none;
				}

				div.wp-signup-container {
					background-color: #e9e3db;
					padding: 10px;
					color: black;
				}

				.mu_register {
					& form#setupform {
						margin-top: 0;
					}

					& label[for="user_name"] {
						margin-top: 0;
					}

					& input[type="submit"] {
						background: #2271b1;
						border-color: #2271b1;
						color: #fff;
						text-decoration: none;
						text-shadow: none;
						font-size: 13px;
						border-width: 1px;
						border-style: solid;
						border-radius: 3px;
						min-height: 32px;
						line-height: 2.30769231;
						padding: 0 12px;
					}
				}

				div#signup-content form,
				.message,
				.success,
				.notice {
					color: #3c434a;
				}

				p.submit {
					margin-bottom: 0;
				}

				p#backtoblog a, p#nav a {
					color: #9cd398;

					&:hover {
						color: #b7dfb3;
					}
				}

				#subscription_key {
					width: 100%;
					font-size: 24px;
					margin: 5px 0;
					box-sizing: border-box;
				}

		</style>
	</head>
	<body>
		<div>
		<h1>
			<a href="/">
				<img src="<?php echo get_theme_file_uri( 'smolblog.wordmark.ondark.png' ); ?>" alt="Smolblog" width="320" height="65">
			</a>
		</h1>
