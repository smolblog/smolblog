<?php

namespace Smolblog\IndieWeb;

use Smolblog\Core\Content\Content;
use Smolblog\Core\Content\Extensions\Syndication\Syndication;
use Smolblog\Core\Content\Extensions\Tags\Tags;
use Smolblog\Core\Content\Types\Note\Note;
use Smolblog\Core\Content\Types\Reblog\Reblog;
use Smolblog\Core\User\User;

class MicroformatsConverter {
	public function entryPropertiesFromContent(Content $content, ?User $author): array {
		$props = [
			'name' => [$content->type->getTitle()],
			'content' => [['html' => $content->type->getBodyContent()]],
			'published' => [$content->publishTimestamp ?? null],
			'author' => [['value' => $author->displayName ?? null]],
			'category' => array_map(
				fn($ent) => $ent->text,
				$content->extensions[Tags::class]?->tags ?? []
			),
			'url' => [[$content->permalink ?? null]],
			'uid' => [[$content->id->toString()]],
			'syndication' => array_map(
				fn($ent) => $ent->url,
				$content->extensions[Syndication::class]?->links ?? []
			),
			'repost-of' => [''],
		];

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
