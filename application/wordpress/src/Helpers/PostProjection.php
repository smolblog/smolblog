<?php

namespace Smolblog\WP\Helpers;

use DateTimeInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Smolblog\Core\ContentV1\ContentVisibility;
use Smolblog\Core\ContentV1\Events\{
	ContentCreated,
	PermalinkAssigned,
	PublicContentAdded,
	PublicContentChanged,
    PublicContentEvent,
    PublicContentRemoved
};
use Smolblog\Core\ContentV1\Extensions\Tags\Tags;
use Smolblog\Core\ContentV1\Types\Note\Note;
use Smolblog\Core\ContentV1\Types\Picture\Picture;
use Smolblog\Core\ContentV1\Types\Reblog\Reblog;
use Smolblog\Foundation\Service\Messaging\Attributes\ExecutionLayerListener;
use Smolblog\Foundation\Service\Messaging\MessageBus;
use Smolblog\Foundation\Service\Messaging\Projection;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\WP\Helpers\SiteHelper;

class PostProjection implements Projection {
	public function __construct(private MessageBus $bus, private LoggerInterface $log) {
	}

	#[ExecutionLayerListener(earlier: 3)]
	public function onPublicContentAdded(PublicContentAdded $event) {
		$content = $event->getContent();

		$wp_site_id = SiteHelper::UuidToInt( $content->siteId );
		switch_to_blog( $wp_site_id );

		$wp_author_id = SiteHelper::UuidToInt( $content->authorId );
		$wp_post_id = wp_insert_post( [
			'post_author' => $wp_author_id,
			'post_title' => $content->type->getTitle(),
			'post_content' => $content->type->getBodyContent(),
			'post_status' => $this->visibilityToStatus( ContentVisibility::Published ),
			'post_type' => $this->typeToPostType( get_class($content->type) ),
			'tags_input' => isset($content->extensions[Tags::class]) ?
				array_map(fn($tag) => $tag->text, $content->extensions[Tags::class]->tags) :
				[],
			'meta_input' => [ 'smolblog_uuid' => $event->contentId->toString() ],
		], true );

		if (is_wp_error( $wp_post_id )) {
			throw new Exception($wp_post_id);
		}

		$permalink_parts = parse_url( get_permalink( $wp_post_id ) );
		$permalink = $permalink_parts['path'] .
			(isset($permalink_parts['query']) ? '?' . $permalink_parts['query'] : '') .
			(isset($permalink_parts['fragment']) ? '#' . $permalink_parts['fragment'] : '');

		restore_current_blog();

		$this->bus->dispatch(new PermalinkAssigned(
			contentId: $event->contentId,
			userId: $event->userId,
			siteId: $event->siteId,
			permalink: $permalink,
		));
		$event->setContentProperty(permalink: $permalink);
	}

	#[ExecutionLayerListener]
	public function onPublicContentChanged(PublicContentChanged $event) {
		$content = $event->getContent();

		$wp_site_id = SiteHelper::UuidToInt( $content->siteId );
		switch_to_blog( $wp_site_id );

		$args = [
			'ID' => self::UuidToInt($content->id),
			'post_content' => $content->type->getBodyContent(),
			'post_title' => $content->type->getTitle(),
			'post_date' => $content->publishTimestamp->format( DateTimeInterface::ATOM ),
			'tags_input' => isset($content->extensions[Tags::class]) ?
				array_map(fn($tag) => $tag->text, $content->extensions[Tags::class]->tags) :
				[],
		];

		$results = wp_update_post( $args );
		if (is_wp_error( $results )) {
			throw new Exception($results);
		}

		restore_current_blog();
	}

	#[ExecutionLayerListener]
	public function onPublicContentRemoved(PublicContentRemoved $event) {
		$content = $event->getContent();

		$wp_site_id = SiteHelper::UuidToInt( $content->siteId );
		switch_to_blog( $wp_site_id );

		$args = [
			'ID' => self::UuidToInt($content->id),
			'post_status' => $this->visibilityToStatus( ContentVisibility::Draft ),
		];

		$results = wp_update_post( $args );
		if (is_wp_error( $results )) {
			throw new Exception($results);
		}

		restore_current_blog();
	}

	/**
	 * Convert an Identifier to a WordPress Post ID. *Must be called within the blog the post belongs to!*
	 *
	 * @param Identifier $uuid ID for the post.
	 * @return int|null
	 */
	public static function UuidToInt(Identifier $uuid) {
		global $wpdb;

		$results = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT post_id FROM $wpdb->postmeta WHERE meta_key = 'smolblog_uuid' AND meta_value = %s",
				$uuid->toString()
			)
		);

		if (!isset($results)) {
			throw new Exception("Could not find post $uuid.");
		}

		return $results;
	}

	private function visibilityToStatus(ContentVisibility $vis) {
		switch ($vis) {
			case ContentVisibility::Protected:
				return 'private';
			case ContentVisibility::Published:
				return 'publish';
			default:
				return 'draft';
		}
	}

	private function typeToPostType(string $contentType): string {
		switch ($contentType) {
			case Note::class:
				return 'sb-note';
			case Reblog::class:
				return 'sb-reblog';
			case Picture::class:
				return 'sb-picture';
			default:
				return 'post';
		}
	}
}
