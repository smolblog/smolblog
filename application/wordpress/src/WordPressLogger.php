<?php

namespace Smolblog\WP;

use DateTimeInterface;
use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;
use Stringable;

class WordPressLogger extends AbstractLogger {
	private int $post_id = 0;
	private string $log_title;

	public function log($level, string|Stringable $message, array $context = []): void
	{
		// if (!WP_DEBUG && $level === LogLevel::DEBUG) {
		// 	return;
		// }

		switch_to_blog( 1 );

		$current = $this->getCurrentContent();
		$this->log_title ??= date(DateTimeInterface::COOKIE);
		
		$result = wp_insert_post([
			'ID' => $this->post_id,
			'post_title' => $this->log_title,
			'post_content' => $current . "\n\n##$message\n\n" .
				$level === LogLevel::DEBUG ? wp_json_encode( $context, JSON_PRETTY_PRINT, 5 ) : print_r( $context, true ),
			'post_type' => 'log',
			'tax_input' => [ 'log_level' => 'aggregate' ],
			'post_status' => 'publish',
		], true);

		if (is_wp_error( $result )) {
			$blog_id = get_current_blog_id();
			throw new \Exception( $result->get_error_message() . " ($this->post_id, site $blog_id)" );
		}

		$this->post_id = $result;

		restore_current_blog();
	}

	/**
	 * Get or create 
	 *
	 * @return string
	 */
	private function getCurrentContent(): string {
		if ($this->post_id > 0) {
			return get_the_content( null, false, $this->post_id );
		}

		return 'Started log ' . date(DateTimeInterface::COOKIE);
	}
}