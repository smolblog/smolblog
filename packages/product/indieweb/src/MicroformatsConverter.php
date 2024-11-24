<?php

namespace Smolblog\IndieWeb;

use DateTimeInterface;
use Smolblog\Core\Content\Entities\Content;
use Smolblog\Core\Content\Extensions\Syndication\Syndication;
use Smolblog\Core\Content\Extensions\Tags\Tags;
use Smolblog\Core\Content\Types\Note\Note;
use Smolblog\Core\Content\Types\Picture\Picture;
use Smolblog\Core\Content\Types\Reblog\Reblog;
use Smolblog\Core\User\User;

/**
 * Convert Smolblog objects to their Microformats counterparts.
 */
class MicroformatsConverter {
	/**
	 * Convert a Content object to a h-entry.
	 *
	 * @param Content   $content Given content.
	 * @param User|null $author  User object corresponding to the author.
	 * @return array
	 */
	public function entryPropertiesFromContent(Content $content, ?User $author = null): array {
		$props = [
			'name' => [$content->title()],
			'content' => [['html' => $content->body->getBodyContent()]],
			'published' => [$content->publishTimestamp?->format(DateTimeInterface::ATOM) ?? null],
			'url' => [$content->permalink ?? null],
			'uid' => [$content->id->toString()],
		];

		if (!empty($content->extensions[Tags::class])) {
			$props['category'] = array_map(
				fn($ent) => $ent->text,
				$content->extensions[Tags::class]?->tags ?? []
			);
		}

		if (!empty($content->extensions[Syndication::class])) {
			$props['syndication'] = array_map(
				fn($ent) => $ent->url,
				$content->extensions[Syndication::class]?->links ?? []
			);
			$props['mp-syndicate-to'] = array_map(
				fn($id) => $id->toString(),
				$content->extensions[Syndication::class]?->channels ?? []
			);
		}

		if (isset($author)) {
			$props['author'] = [['value' => $author?->displayName ?? null]];
		}

		switch (get_class($content->type)) {
			case Note::class:
				unset($props['name']);
				$props['content'][0] = [
					$content->type->text,
				];
				break;

			case Reblog::class:
				$props['content'][0] = [
					$content->type->comment ?? '',
				];
				$props['repost-of'] = [$content->type->url];
				break;

			case Picture::class:
				$props['content'][0] = [
					$content->type->caption ?? '',
				];
				$props['photo'] = array_map(
					fn($media) => ['value' => $media->defaultUrl, 'alt' => $media->accessibilityText],
					$content->type->media,
				);
				break;
		}//end switch

		return $props;
	}
}
