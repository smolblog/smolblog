<?php
/**
 * Smolblog-WP
 *
 * An interface between the core Smolblog library and WordPress.
 *
 * @package Smolblog\WP
 *
 * @wordpress-plugin
 * Plugin Name:       Smolblog WP
 * Plugin URI:        http://github.com/smolblog/smolblog-wp
 * Description:       WordPress + Smolblog
 * Version:           1.0.0
 * Author:            Smolblog
 * Author URI:        http://smolblog.org
 * License:           AGPL-3.0+
 * License URI:       https://www.gnu.org/licenses/agpl.html
 * Text Domain:       smolblog
 * Domain Path:       /languages
 */

namespace Smolblog\WP;

use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\WP\Helpers\DatabaseHelper;
use Smolblog\WP\Helpers\SiteHelper;
use Smolblog\WP\Helpers\UserHelper;

require_once __DIR__ . '/vendor/autoload.php';

// Load Action Scheduler.
$smolblog_action_scheduler = __DIR__ . '/vendor/woocommerce/action-scheduler/action-scheduler.php';
if ( is_readable( $smolblog_action_scheduler ) ) {
	require_once $smolblog_action_scheduler;
}

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

function get_current_user_uuid(): ?Identifier {
	if (get_current_user_id() > 0) {
		return UserHelper::IntToUuid(get_current_user_id());
	}

	return null;
}

function get_current_site_uuid(): Identifier {
	return SiteHelper::IntToUuid(get_current_blog_id());
}

$default_cpt_args = [
	'supports'              => array( 'editor', 'thumbnail', 'comments', 'custom-fields', 'page-attributes', 'post-formats' ),
	'taxonomies'            => array( 'post_tag' ),
	'public'                => true,
	'menu_position'         => 5,
	'has_archive'           => true,
];

add_action( 'init', fn() => register_post_type( 'sb-note', [
	'label'                 => __( 'Note', 'smolblog' ),
	'description'           => __( 'A short text post', 'smolblog' ),
	...$default_cpt_args,
] ), 0 );

add_action( 'init', fn() => register_post_type( 'sb-reblog', [
	'label'                 => __( 'Reblog', 'smolblog' ),
	'description'           => __( 'A webpage from off-site', 'smolblog' ),
	...$default_cpt_args,
] ), 0 );
add_action( 'init', fn() => register_post_type( 'sb-picture', [
	'label'                 => __( 'Picture', 'smolblog' ),
	'description'           => __( 'A visual medium', 'smolblog' ),
	...$default_cpt_args,
] ), 0 );

add_action( 'init', fn() => register_post_type( 'log', [
	'label'                 => __( 'Log', 'smolblog' ),
	'description'           => __( 'A debug log entry', 'smolblog' ),
	'supports'              => array( 'title', 'editor' ),
	'taxonomies'            => array( 'log_level' ),
	'public'                => false,
	'show_ui'               => true,
	'show_in_menu'          => true,
	'menu_position'         => 80,
	'show_in_admin_bar'     => false,
	'show_in_nav_menus'     => false,
] ), 0 );
add_action( 'init', fn() => register_taxonomy( 'log_level', [ 'log' ], [
	'label'             => __( 'Log Level', 'smolblog' ),
	'hierarchical'      => false,
	'public'            => false,
	'show_ui'           => true,
	'show_admin_column' => true,
	'show_in_nav_menus' => true,
	'show_tagcloud'     => false,
	'rewrite'           => false,
	'show_in_rest'      => false,
] ), 0 );

add_action( 'pre_get_posts', function($query) {
	if ( ! is_admin() && $query->is_main_query() ) {
		$query->set( 'post_type', array( 'post', 'page', 'status', 'reblog' ) );
	}
});

add_filter( 'the_title_rss', function($title) {
	global $wp_query;
	$type = $wp_query->post->post_type;
	if (in_array($type, [ 'note', 'reblog' ])) {
		return null;
	}
	return $title;
});

add_action( 'init',  function() {
	add_rewrite_rule(
		'^\.well-known\/webfinger',
		'index.php?rest_route=/smolblog/v2/webfinger',
		'top'
	);
} );

add_action( 'wp_head', function() {
	$siteId = get_current_site_uuid();
	echo '<link rel="micropub" href="' . get_rest_url( null, "/smolblog/v2/site/$siteId/micropub" ) . '">';
});

add_action( 'login_enqueue_scripts', function() {
	$logo_url = plugin_dir_url( __FILE__ ) . 'smolblog.wordmark.ondark.png';
	?>
		<style type="text/css">
			body.login {
				display: flex;
				background: #191b20;
				color: #e9e3db;
	
				& div#login h1 a {
					background-image: url(<?php echo $logo_url; ?>);
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
	<?php
	});
	
	add_filter( 'login_headerurl', fn() => 'https://smolblog.com/' );
	add_filter( 'login_headertext', fn() => 'Smolblog' );
