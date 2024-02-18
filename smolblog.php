<?php
/**
 * Smolblog
 *
 * An interface between the core Smolblog library and WordPress.
 *
 * @package Smolblog\WP
 *
 * @wordpress-plugin
 * Plugin Name:       Smolblog WP
 * Plugin URI:        http://github.com/smolblog/smolblog
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

Plugin::BootstrapMain();
