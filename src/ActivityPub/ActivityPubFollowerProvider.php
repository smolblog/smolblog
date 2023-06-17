<?php

namespace Smolblog\ActivityPub;

use Smolblog\Core\Content\Content;
use Smolblog\Core\Content\ContentVisibility;
use Smolblog\Core\Federation\FollowerProvider;
use Smolblog\Framework\Messages\MessageBus;

/**
 * Service that handles posting content to ActivityPub.
 */
class ActivityPubFollowerProvider implements FollowerProvider {
	public const SLUG = 'activitypub';

	/**
	 * Get the slug for this provider.
	 *
	 * @return string
	 */
	public static function getSlug(): string {
		return self::SLUG;
	}

	/**
	 * Construct the provider.
	 *
	 * @param MessageBus $bus MessageBus instance.
	 */
	public function __construct(
		private MessageBus $bus,
	) {
	}

	/**
	 * Post the given content to the given ActivityPub followers.
	 *
	 * @param Content $content   Content being created.
	 * @param array   $followers Followers interested in said content.
	 * @return void
	 */
	public function sendContentToFollowers(Content $content, array $followers): void {
		if ($content->visibility !== ContentVisibility::Published) {
			return;
		}
	}
}
