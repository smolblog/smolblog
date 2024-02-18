<?php

namespace Smolblog\WP;

use wpdb;

class TableBacked {
	/**
	 * Get the full table name for this class.
	 *
	 * @return string
	 */
	public static function table_name(): string {
		global $wpdb;
		return $wpdb->prefix . 'sb_' . static::TABLE;
	}

	/**
	 * Check the schema version and update if needed.
	 *
	 * @return void
	 */
	public static function update_schema(): void {
		global $wpdb;

		$table_name      = static::table_name();
		$table_fields    = static::FIELDS;
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			$table_fields
			PRIMARY KEY  (id)
		) $charset_collate;";

		if ( md5( $sql ) === get_option( static::TABLE . '_schemaver', '' ) ) {
			return;
		}

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );

		update_option( static::TABLE . '_schemaver', md5( $sql ) );
	}

	public function __construct(protected wpdb $db) {
	}
}