<?php

namespace Smolblog\Test;

use Exception;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\TestDox;
use Smolblog\Core\Content;
use Smolblog\Core\Content\Commands\CreateContent;
use Smolblog\Core\Content\Commands\UpdateContent;
use Smolblog\Core\Content\Fields\Markdown;
use Smolblog\Core\Content\Note;
use Smolblog\Core\Content\Queries\ContentById;
use Smolblog\Foundation\Exceptions\InvalidEventPlacement;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Mock\App;
use Smolblog\Mock\SecurityService;

final class ContentV2Test extends TestCase {
	protected function tearDown(): void {
		App::resetMemo();
	}

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
		return $actual;
	}

	#[Depends('testNoteCreation')]
	public function testNoteModification(Content $current) {
		$userId = Identifier::fromString(SecurityService::SITE1ADMIN);
		$updatedContent = $current->with(
			body: new Note(new Markdown('I did it.')),
		);

		App::dispatch(new UpdateContent(userId: $userId, content: $updatedContent));

		$query = new ContentById($current->id);
		App::dispatch($query);
		$actual = $query->results();

		$this->assertEquals($updatedContent, $actual);
	}

	#[Depends('testNoteCreation')]
	#[TestDox('Note creation fails if ID is in use')]
	public function testNoteCreationFails(Content $current) {
		$this->expectException(InvalidEventPlacement::class);

		$userId = Identifier::fromString(SecurityService::SITE1ADMIN);
		$updatedContent = $current->with(
			body: new Note(new Markdown('I did it.')),
		);

		App::dispatch(new CreateContent(userId: $userId, content: $updatedContent));
	}

	#[TestDox('Note modification fails if ID is not present')]
	public function testNoteModificationFails() {
		$this->expectException(Exception::class);

		$userId = Identifier::fromString(SecurityService::SITE1ADMIN);
		$newContent = new Content(
			body: new Note(new Markdown('Hello.')),
			siteId: Identifier::fromString(SecurityService::SITE1),
			authorId: $userId,
		);

		App::dispatch(new UpdateContent(userId: $userId, content: $newContent));
	}
}
