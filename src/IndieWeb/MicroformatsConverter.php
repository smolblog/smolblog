<?php

namespace Smolblog\IndieWeb;

use DateTimeInterface;
use Smolblog\Core\Content\Content;
use Smolblog\Core\Content\Extensions\Syndication\Syndication;
use Smolblog\Core\Content\Extensions\Tags\Tags;
use Smolblog\Core\Content\Types\Note\Note;
use Smolblog\Core\Content\Types\Reblog\Reblog;
use Smolblog\Core\User\User;

class MicroformatsConverter {
	public function entryPropertiesFromContent(Content $content, ?User $author = null): array {
		$props = [
			'name' => [$content->type->getTitle()],
			'content' => [['html' => $content->type->getBodyContent()]],
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
		}

		if (isset($author)) {
			$props['author'] = [['value' => $author?->displayName ?? null]];
		}

		switch (get_class($content->type)) {
			case Note::class:
				unset($props['name']);
				$props['content'][0]['value'] = $content->type->text;
				break;

			case Reblog::class:
				$props['content'][0] = ['value' => $content->type->comment ?? ''];
				$props['repost-of'] = [$content->type->url];
				break;
		}

		return $props;
	}
}
