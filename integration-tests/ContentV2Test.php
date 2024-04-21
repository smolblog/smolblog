<?php

namespace Smolblog\Test;

use Smolblog\Core\Content;
use Smolblog\Core\Content\Commands\CreateContent;
use Smolblog\Core\Content\Fields\Markdown;
use Smolblog\Core\Content\Note;
use Smolblog\Core\Content\Queries\ContentById;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Mock\App;
use Smolblog\Mock\SecurityService;

final class ContentV2Test extends TestCase {
	private const NOTE = '4d8b7721-612b-44ae-9938-caccc43dce21';
	private const REBLOG = 'f06a5e7f-76d0-4e61-b339-a08732259846';
	private const PICTURE = '4286ff3f-f84e-4f12-95a3-486bbeb13c78';
	private const ARTICLE = '0a33ad2a-bde9-42bb-8eb8-ac6ccb05f64a';

	public function testNoteCreation() {
		$contentId = Identifier::fromString(self::NOTE);
		$userId = Identifier::fromString(SecurityService::SITE1AUTHOR);
		$newContent = new Content(
			id: $contentId,
			body: new Note(text: new Markdown("No, I don't need a new keyboard.\n\nBut I _want_ one...")),
			siteId: Identifier::fromString(SecurityService::SITE1),
			authorId: $userId,
			published: true,
		);

		App::dispatch(new CreateContent(userId: $userId, content: $newContent));

		$query = new ContentById($contentId);
		App::dispatch($query);
		$actual = $query->results();

		$this->assertEquals($newContent, $actual);
	}
}
