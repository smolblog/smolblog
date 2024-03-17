<?php

namespace Smolblog\WP\Helpers;

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\ConnectionInterface;

class DatabaseHelper {
	public static function getLaravelConnection(): ConnectionInterface {
		global $wpdb;

		$capsule = new Manager();
		$capsule->addConnection( [
			'driver' => 'mysql',
			'host' => DB_HOST,
			'database' => DB_NAME,
			'username' => DB_USER,
			'password' => DB_PASSWORD,
			'charset' => DB_CHARSET,
			// 'collation' => $wpdb->get_charset_collate(),
			'prefix' => $wpdb->base_prefix . 'sb_',
		] );

		return $capsule->getConnection();
	}

	public const SCHEMA = [
		'activitypub_handles' => <<<EOF
			handle_uuid varchar(40) NOT NULL UNIQUE,
			handle varchar(40) NOT NULL,
			site_uuid varchar(40) NOT NULL,
		EOF,
		'channels' => <<<EOF
			channel_uuid varchar(40) NOT NULL UNIQUE,
			connection_uuid varchar(40) NOT NULL,
			channel_key varchar(50) NOT NULL,
			display_name varchar(100) NOT NULL,
			details text NOT NULL,
		EOF,
		'channel_site_links' => <<<EOF
			link_uuid varchar(40) NOT NULL UNIQUE,
			channel_uuid varchar(40) NOT NULL,
			site_uuid varchar(40) NOT NULL,
			can_push bool NOT NULL,
			can_pull bool NOT NULL,
		EOF,
		'connections' => <<<EOF
			connection_uuid varchar(40) NOT NULL UNIQUE,
			user_uuid varchar(40) NOT NULL,
			provider varchar(50) NOT NULL,
			provider_key varchar(50) NOT NULL,
			display_name varchar(50) NOT NULL,
			details text,
		EOF,
		'connector_events' => <<<EOF
			event_uuid varchar(40) NOT NULL UNIQUE,
			event_time varchar(30) NOT NULL,
			connection_uuid varchar(40) NOT NULL,
			user_uuid varchar(40) NOT NULL,
			event_type varchar(255) NOT NULL,
			payload text,
		EOF,
		'content_events' => <<<EOF
			event_uuid varchar(40) NOT NULL UNIQUE,
			event_time varchar(30) NOT NULL,
			content_uuid varchar(40) NOT NULL,
			site_uuid varchar(40) NOT NULL,
			user_uuid varchar(40) NOT NULL,
			event_type varchar(255) NOT NULL,
			payload text,
		EOF,
		'content_syndication' => <<<EOF
			row_uuid varchar(40) NOT NULL UNIQUE,
			content_uuid varchar(40) NOT NULL,
			channel_uuid varchar(40),
			url varchar(255),
		EOF,
		'followers' => <<<EOF
			follower_uuid varchar(40) NOT NULL UNIQUE,
			site_uuid varchar(40) NOT NULL,
			provider varchar(50) NOT NULL,
			provider_key varchar(50) NOT NULL,
			display_name varchar(100) NOT NULL,
			details text,
		EOF,
		'media' => <<<EOF
			content_uuid varchar(40) NOT NULL,
			user_uuid varchar(40) NOT NULL,
			site_uuid varchar(40) NOT NULL,
			title varchar(255) NOT NULL,
			accessibility_text varchar(255) NOT NULL,
			type varchar(40) NOT NULL,
			thumbnail_url varchar(255) NOT NULL,
			default_url varchar(255) NOT NULL,
			file text,
			uploaded_at varchar(30),
		EOF,
		'notes' => <<<EOF
			content_uuid varchar(40) NOT NULL UNIQUE,
			markdown text NOT NULL,
			html text,
		EOF,
		'pictures' => <<<EOF
			content_uuid varchar(40) NOT NULL UNIQUE,
			media text NOT NULL,
			caption text,
			media_html text,
			caption_html text,
		EOF,
		'reblogs' => <<<EOF
			content_uuid varchar(40) NOT NULL UNIQUE,
			url varchar(255) NOT NULL,
			comment text,
			comment_html text,
			url_info text,
		EOF,
		'site_events' => <<<EOF
			event_uuid varchar(40) NOT NULL UNIQUE,
			event_time varchar(30) NOT NULL,
			site_uuid varchar(40) NOT NULL,
			user_uuid varchar(40) NOT NULL,
			event_type varchar(255) NOT NULL,
			payload text,
		EOF,
		'standard_content' => <<<EOF
			content_uuid varchar(40) NOT NULL UNIQUE,
			type varchar(100) NOT NULL,
			title varchar(255),
			body text,
			site_uuid varchar(40) NOT NULL,
			author_uuid varchar(40) NOT NULL,
			permalink varchar(255),
			publish_timestamp varchar(50),
			visibility varchar(10) NOT NULL,
			extensions text NOT NULL,
		EOF,
	];

	public static function update_schema(): void {
		foreach ( self::SCHEMA as $table => $fields ) {
			self::table_delta( $table, $fields );
		}
	}

	public static function table_delta( string $table, string $fields ): void {
		global $wpdb;

		$table_name      = $wpdb->base_prefix . 'sb_' . $table;
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			$fields
			PRIMARY KEY  (id)
		) $charset_collate;";

		if ( md5( $sql ) === get_option( $table . '_schemaver', '' ) ) {
			return;
		}

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );

		update_option( $table . '_schemaver', md5( $sql ) );
	}
}
