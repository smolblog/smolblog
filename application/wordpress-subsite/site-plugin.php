<?php
/**
 * Smolblog Site Helper
 *
 * A lite plugin that provides functionality for every site without loading the full REST API.
 *
 * @package Smolblog\WP
 *
 * @wordpress-plugin
 * Plugin Name:       Smolblog Site Helper
 * Plugin URI:        http://github.com/smolblog/smolblog
 * Description:       Multisite helper for Smolblog
 * Version:           1.0.0
 * Author:            Smolblog
 * Author URI:        http://smolblog.org
 * License:           AGPL-3.0+
 * License URI:       https://www.gnu.org/licenses/agpl.html
 * Text Domain:       smolblog
 * Domain Path:       /languages
 */

namespace Smolblog\WP;

use Smolblog\ActivityPub\Api\Webfinger;
use Smolblog\Api\Exceptions\NotFound;
use WP_REST_Request;
use WP_REST_Response;

// Register Post Types

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
	'rewrite'               => [ 'slug' => 'note' ],
	...$default_cpt_args,
] ), 0 );
add_action( 'init', fn() => register_post_type( 'sb-reblog', [
	'label'                 => __( 'Reblog', 'smolblog' ),
	'description'           => __( 'A webpage from off-site', 'smolblog' ),
	'rewrite'               => [ 'slug' => 'reblog' ],
	...$default_cpt_args,
] ), 0 );
add_action( 'init', fn() => register_post_type( 'sb-picture', [
	'label'                 => __( 'Picture', 'smolblog' ),
	'description'           => __( 'A visual medium', 'smolblog' ),
	'rewrite'               => [ 'slug' => 'picture' ],
	...$default_cpt_args,
] ), 0 );

// Add post types to main query
add_action( 'pre_get_posts', function($query) {
	if ( ! is_admin() && $query->is_main_query() ) {
		$query->set( 'post_type', array( 'post', 'page', 'sb-note', 'sb-reblog', 'sb-picture' ) );
	}
});

// Remove the title display for post types
add_filter( 'the_title', function($title, $post_id) {
	// Method from Title Remover by WP Gurus, licened under GPL 2
	// https://wordpress.org/plugins/title-remover/
	if (
		! is_admin() &&
		in_the_loop() &&
		in_array( get_post_type( $post_id ), [ 'sb-note', 'sb-reblog', 'sb-picture' ] )
	) {
		return '';
	}

	return $title;
}, 10, 2);
add_filter( 'the_title_rss', function($title) {
	global $wp_query;
	$type = $wp_query->post->post_type;
	if (in_array($type, [ 'sb-note', 'sb-reblog', 'sb-picture' ])) {
		return null;
	}
	return $title;
});

// Put a notice on Smolblog CPTs
add_action( 'admin_notices', function() {
	if ( ! (
		function_exists( 'get_current_screen' ) &&
		in_array( get_current_screen()?->post_type ?? '', [ 'sb-note', 'sb-reblog', 'sb-picture' ] )
	) ) {
		return;
	}

	?>
	<div class="notice notice-warning">
	<p>
		<strong>Editing Smolblog content inside WordPress is not supported!</strong> Any changes made here might be
		overwritten by Smolblog at any time or could prevent Smolblog from operating correctly.
	</p>
	</div>
	<?php
} );

// Add Webfinger alias at expected location.
add_action( 'init',  function() {
	add_rewrite_rule(
		'^\.well-known\/webfinger',
		'index.php?rest_route=/smolblog/v2/webfinger',
		'top'
	);
} );

// Add a webfinger handler if this is not the main blog.
if ( get_current_blog_id() !== 1 ) {
	add_action( 'rest_api_init', function() {
		register_rest_route(
			'smolblog/v2',
			'/webfinger',
			array(
				'methods'             => [ 'GET' ],
				'callback'            => function(WP_REST_Request $request) {
					switch_to_blog( 1 );

					require_once __DIR__ . '/../../vendor/autoload.php';
					$app = new Smolblog();

					$endpoint = $app->container->get( Webfinger::class );
					$outgoing = new WP_REST_Response();

					try {
						$response = $endpoint->run( null, $request->get_params(), null );
						$outgoing->set_status( 200 );
						$outgoing->set_data( $response );
					} catch( NotFound $e ) {
						$outgoing->set_status( 404 );
						$outgoing->set_data( $e );
					}

					restore_current_blog();

					return $outgoing;
				},
				'permission_callback' => '__return_true',
			),
		);
	} );
}

// Add the Micropub endpoint for the site
add_action( 'wp_head', function() {
	$siteId = get_site_meta( get_current_blog_id(), 'smolblog_site_id', true );
	echo '<link rel="micropub" href="' . get_rest_url( null, "/smolblog/v2/site/$siteId/micropub" ) . '">';
});

// Customize the login panel
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

// Add a Smolblog Dashboard link to the admin bar.
add_action('admin_bar_menu', function($admin_bar) {
	$admin_bar->add_menu( array(
			'id'    => 'smolblog-dashboard',
			'title' => 'Smolblog Dashboard',
			'href'  => 'https://dashboard.smolblog.com/',
			'meta'  => array(
					'title' => 'Return to the Smolblog Dashboard',
			),
	));
}, 100);
