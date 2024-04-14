<?php

namespace Smolblog\WP\Helpers;

use Exception;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Log\LoggerInterface;
use Smolblog\Core\ContentV1\Media\{MediaHandler, MediaFile, InvalidMediaException};
use Smolblog\Foundation\Value\Fields\DateIdentifier;
use Smolblog\Foundation\Value\Fields\Identifier;

class MediaLibraryHelper implements MediaHandler {
	public static function getHandle(): string {
		return 'wordpress';
	}

	public function __construct(private LoggerInterface $log) {
	}

	public function handleUploadedFile(
		UploadedFileInterface $file,
		?Identifier $userId = null,
		?Identifier $siteId = null
	): MediaFile {
		// These files need to be included as dependencies when on the front end.
		require_once( ABSPATH . 'wp-admin/includes/image.php' );
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		require_once( ABSPATH . 'wp-admin/includes/media.php' );

		$file_key = $this->getFilesKeyForGivenPsrFile($file);
		$this->log->debug("WordPress MediaLibraryHelper handling upload", [
			'File Key' => $file_key,
			'File Object' => $file,
			'Files Array' => $_FILES,
		]);

		$blog_id = isset($siteId) ? SiteHelper::UuidToInt($siteId) : 1;
		switch_to_blog( $blog_id );

		$wp_id = media_handle_upload( $file_key, 0 );

		if (is_wp_error($wp_id)) {
			throw new InvalidMediaException('Error saving to library: ' . $wp_id->get_error_message(), upload: $file);
		}

		$file = new MediaFile(
			id: new DateIdentifier(),
			handler: 'wordpress',
			mimeType: get_post( $wp_id )->post_mime_type,
			details: [ 'wp_id' => $wp_id, 'wp_site' => $blog_id ],
		);

		restore_current_blog();
		return $file;
	}

	public function sideloadFile(
		string $url,
		?Identifier $userId = null,
		?Identifier $siteId = null
	): MediaFile {
		require_once( ABSPATH . 'wp-admin/includes/image.php' );
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		require_once( ABSPATH . 'wp-admin/includes/media.php' );

		$this->log->debug("WordPress MediaLibraryHelper handling sideload", [
			'URL' => $url,
		]);

		$tmp = download_url( $url );
		if ( is_wp_error( $tmp ) ) {
			throw new InvalidMediaException('Error downloading file: ' . $tmp->get_error_message(), url: $url);
		}
		$file_array = array();

		// Set variables for storage
		// fix file filename for query strings.
		preg_match( '/[^\?]+\.(jpg|jpe|jpeg|gif|png|mp4|m4v|mp3)/i', $url, $matches );

		$file_array['name']     = basename( $matches[0] );
		$file_array['tmp_name'] = $tmp;

		$blog_id = isset($siteId) ? SiteHelper::UuidToInt($siteId) : 1;
		switch_to_blog( $blog_id );

		// do the validation and storage stuff.
		$wp_id = media_handle_sideload( $file_array, $post_id, $desc );

		// If error storing permanently, unlink.
		if ( is_wp_error( $wp_id ) ) {
			unlink( $file_array['tmp_name'] );
			throw new InvalidMediaException('Error saving downloaded file: ' . $tmp->get_error_message(), url: $url);
		}

		$file = new MediaFile(
			id: new DateIdentifier(),
			handler: 'wordpress',
			mimeType: get_post( $wp_id )->post_mime_type,
			details: [ 'wp_id' => $wp_id, 'wp_site' => $blog_id ],
		);

		restore_current_blog();
		return $file;
	}

	public function getThumbnailUrlFor(MediaFile $file): string {
		switch_to_blog( $file->details['wp_site'] ?? 1 );

		$info = wp_get_attachment_image_src( $file->details['wp_id'], 'thumbnail', true );

		restore_current_blog();
		return $info[0];
	}

	public function getUrlFor(MediaFile $file, ?int $maxWidth = null, ?int $maxHeight = null, mixed ...$props): string {
		switch_to_blog( $file->details['wp_site'] ?? 1 );

		$size = isset($maxWidth) || isset($maxHeight) ? [$maxWidth ?? 9999, $maxHeight ?? 9999] : 'full';
		$info = wp_get_attachment_image_src( $file->details['wp_id'], $size, true );

		restore_current_blog();
		return $info[0];
	}

	public function getHtmlFor(MediaFile $file, ?int $maxWidth = null, ?int $maxHeight = null, mixed ...$props): string {
		return '';
	}

	private function getFilesKeyForGivenPsrFile(UploadedFileInterface $given): string|int|false {
		foreach ($_FILES as $key => $file) {
			if ($file['name'] === $given->getClientFilename() && $file['size'] === $given->getSize()) {
				return $key;
			}
			return false;
		}
	}
}
