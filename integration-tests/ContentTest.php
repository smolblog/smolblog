<?php

namespace Smolblog\Test;

use DateTimeImmutable;
use Smolblog\Core\Content\Content;
use Smolblog\Core\Content\ContentVisibility;
use Smolblog\Core\Content\Extensions\Syndication\SetSyndicationChannels;
use Smolblog\Core\Content\Extensions\Tags\SetTags;
use Smolblog\Core\Content\Extensions\Tags\Tag;
use Smolblog\Core\Content\Extensions\Tags\Tags;
use Smolblog\Core\Content\Types\Note\CreateNote;
use Smolblog\Core\Content\Types\Note\EditNote;
use Smolblog\Core\Content\Types\Note\Note;
use Smolblog\Core\Content\Types\Note\NoteById;
use Smolblog\Core\Content\Types\Note\PublishNote;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Mock\App;
use Smolblog\Mock\SecurityService;

class ContentTest extends TestCase {
	private const NOTE = '5f98a21c-126d-40da-b62d-2a8cb03ed097';
	private const REBLOG = '74b9c097-5a1f-405e-9da1-289eccb93028';
	private const PHOTO = '1ee07396-7c81-426a-967a-b828e08d2cfb';
	private const ARTICLE = '38d0d034-79bb-4a96-815b-d5457be08a04';

	public function testNoteCreation() {
		$contentParams = [
			'contentId' => Identifier::fromString(self::NOTE),
			'siteId' => Identifier::fromString(SecurityService::SITE1),
			'userId' => Identifier::fromString(SecurityService::SITE1AUTHOR),
		];

		App::dispatch(new CreateNote(
			...$contentParams,
			text: "Don't you know who I am?\n\nI'm The Juggernaut, _bleep_!",
			publish: false,
		));
		App::dispatch(new SetTags(
			...$contentParams,
			tags: ['x-men', 'meme', 'the juggernaut'],
		));
		App::dispatch(new PublishNote(...$contentParams));

		// Don't use $contentParams here since it should be published and available to anonymous requests.
		$actual = App::fetch(new NoteById(
			contentId: Identifier::fromString(self::NOTE),
			siteId: Identifier::fromString(SecurityService::SITE1),
		));
		$expected = new Content(
			id: Identifier::fromString(self::NOTE),
			type: new Note(
				text: "Don't you know who I am?\n\nI'm The Juggernaut, _bleep_!",
				rendered: $actual->type->getBodyContent(), // Not concerned with this property.
			),
			siteId: Identifier::fromString(SecurityService::SITE1),
			authorId: Identifier::fromString(SecurityService::SITE1AUTHOR),
			permalink: '/post/' . $contentParams['contentId'],
			publishTimestamp: $actual->publishTimestamp ?? 'error', // Only concerned that this property exists.
			visibility: ContentVisibility::Published,
			extensions: [
				Tags::class => new Tags(tags: [
					new Tag('x-men'),
					new Tag('meme'),
					new Tag('the juggernaut'),
				])
			]
		);

		$this->assertEquals($expected, $actual);
	}

	/** @depends testNoteCreation */
	public function testNoteModification() {
		// Use the Admin user to test those permissions.
		$contentParams = [
			'contentId' => Identifier::fromString(self::NOTE),
			'siteId' => Identifier::fromString(SecurityService::SITE1),
			'userId' => Identifier::fromString(SecurityService::SITE1ADMIN),
		];

		App::dispatch(new SetTags(
			...$contentParams,
			tags: ['x-men', 'meme', 'they saw their shot and they took it'],
		));
		App::dispatch(new EditNote(
			...$contentParams,
			text: "> Don't you know who I am?\n\n&mdash The Juggernaut",
		));

		// Don't use $contentParams here since it should be published and available to anonymous requests.
		$actual = App::fetch(new NoteById(
			contentId: Identifier::fromString(self::NOTE),
			siteId: Identifier::fromString(SecurityService::SITE1),
		));
		$expected = new Content(
			id: Identifier::fromString(self::NOTE),
			type: new Note(
				text: "> Don't you know who I am?\n\n&mdash The Juggernaut",
				rendered: $actual->type->getBodyContent(), // Not concerned with this property.
			),
			siteId: Identifier::fromString(SecurityService::SITE1),
			authorId: Identifier::fromString(SecurityService::SITE1AUTHOR),
			permalink: '/post/' . $contentParams['contentId'],
			publishTimestamp: $actual->publishTimestamp ?? 'error', // Only concerned that this property exists.
			visibility: ContentVisibility::Published,
			extensions: [
				Tags::class => new Tags(tags: [
					new Tag('x-men'),
					new Tag('meme'),
					new Tag('they saw their shot and they took it'),
				])
			]
		);

		$this->assertEquals($expected, $actual);
	}
}
