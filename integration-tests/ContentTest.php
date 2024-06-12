<?php

namespace Smolblog\Test;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\Depends;
use Psr\Http\Message\UploadedFileInterface;
use Smolblog\Core\Connector\Entities\Channel;
use Smolblog\Core\Connector\Entities\Connection;
use Smolblog\Core\Content\Content;
use Smolblog\Core\Content\ContentVisibility;
use Smolblog\Core\Content\Extensions\Syndication\AddSyndicationLink;
use Smolblog\Core\Content\Extensions\Syndication\SetSyndicationChannels;
use Smolblog\Core\Content\Extensions\Syndication\Syndication;
use Smolblog\Core\Content\Extensions\Syndication\SyndicationLink;
use Smolblog\Core\Content\Extensions\Tags\SetTags;
use Smolblog\Core\Content\Extensions\Tags\Tag;
use Smolblog\Core\Content\Extensions\Tags\Tags;
use Smolblog\Core\Content\Media\HandleUploadedMedia;
use Smolblog\Core\Content\Media\Media;
use Smolblog\Core\Content\Media\MediaById;
use Smolblog\Core\Content\Media\MediaType;
use Smolblog\Core\Content\Queries\ContentById;
use Smolblog\Core\Content\Types\Note\CreateNote;
use Smolblog\Core\Content\Types\Note\EditNote;
use Smolblog\Core\Content\Types\Note\Note;
use Smolblog\Core\Content\Types\Note\NoteById;
use Smolblog\Core\Content\Types\Note\PublishNote;
use Smolblog\Core\Content\Types\Picture\CreatePicture;
use Smolblog\Core\Content\Types\Picture\Picture;
use Smolblog\Core\Content\Types\Picture\PictureById;
use Smolblog\Core\Content\Types\Picture\PublishPicture;
use Smolblog\Core\Content\Types\Reblog\CreateReblog;
use Smolblog\Core\Content\Types\Reblog\ExternalContentInfo;
use Smolblog\Core\Content\Types\Reblog\PublishReblog;
use Smolblog\Core\Content\Types\Reblog\Reblog;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Mock\App;
use Smolblog\Mock\SecurityService;

class ContentTest extends TestCase {
	private const NOTE = '5f98a21c-126d-40da-b62d-2a8cb03ed097';
	private const REBLOG = '74b9c097-5a1f-405e-9da1-289eccb93028';
	private const PICTURE = '1ee07396-7c81-426a-967a-b828e08d2cfb';
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
		App::dispatch(new SetSyndicationChannels(
			...$contentParams,
			channels: [
				Channel::buildId(
					connectionId: Connection::buildId(provider: 'smolblog', providerKey: 'woohoo543'),
					channelKey: 'snek.smol.blog',
				),
			]
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
				]),
				Syndication::class => new Syndication(
					links: [],
					channels: [
						Identifier::fromString(Channel::buildId(
							connectionId: Connection::buildId(provider: 'smolblog', providerKey: 'woohoo543'),
							channelKey: 'snek.smol.blog',
						)->toString()),
					]
				)
			]
		);

		$this->assertEquals($expected, $actual);
	}

	#[Depends('testNoteCreation')]
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
		App::dispatch(new AddSyndicationLink(
			...$contentParams,
			url: 'https://oddevan.bsky.social/post/1234567',
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
				]),
				Syndication::class => new Syndication(
					links: [new SyndicationLink(url: 'https://oddevan.bsky.social/post/1234567')],
					channels: [
						Identifier::fromString(Channel::buildId(
							connectionId: Connection::buildId(provider: 'smolblog', providerKey: 'woohoo543'),
							channelKey: 'snek.smol.blog',
						)->toString()),
					],
				),
			]
		);

		$this->assertEquals($expected, $actual);
	}

	public function testPictureCreation() {
		$photoUpload = $this->createStub(UploadedFileInterface::class);
		$photoUpload->method('getClientMediaType')->willReturn('image/png');
		$photoUpload->method('getClientFilename')->willReturn('IMG-543.png');
		$photoId = Identifier::fromString('e930a215-03e3-4139-9069-897490202f14');

		App::dispatch(new HandleUploadedMedia(
			file: $photoUpload,
			contentId: $photoId,
			userId: Identifier::fromString(SecurityService::SITE1AUTHOR),
			siteId: Identifier::fromString(SecurityService::SITE1),
			accessibilityText: 'Troy returning with pizza.',
		));

		$photoObject = App::fetch(new MediaById(
			contentId: $photoId,
			userId: Identifier::fromString(SecurityService::SITE1AUTHOR),
			siteId: Identifier::fromString(SecurityService::SITE1),
		));
		$photoFileId = $photoObject->file->id;


		$contentParams = [
			'contentId' => Identifier::fromString(self::PICTURE),
			'siteId' => Identifier::fromString(SecurityService::SITE1),
			'userId' => Identifier::fromString(SecurityService::SITE1AUTHOR),
		];

		App::dispatch(new CreatePicture(
			...$contentParams,
			mediaIds: [$photoId],
			caption: 'TFW you changed something and the tests broke.',
		));
		App::dispatch(new SetTags(
			...$contentParams,
			tags: ['tfw', 'relatable', 'community'],
		));
		App::dispatch(new PublishPicture(...$contentParams));


		$actual = App::fetch(new PictureById(
			contentId: Identifier::fromString(self::PICTURE),
			siteId: Identifier::fromString(SecurityService::SITE1),
		));
		$expected = new Content(
			id: Identifier::fromString(self::PICTURE),
			type: new Picture(
				media: [
					new Media(
						id: $photoId,
						userId: Identifier::fromString(SecurityService::SITE1AUTHOR),
						siteId: Identifier::fromString(SecurityService::SITE1),
						title: 'IMG-543.png',
						accessibilityText: 'Troy returning with pizza.',
						type: MediaType::Image,
						thumbnailUrl: "//cdn.smol.blog/$photoFileId/thumb",
						defaultUrl: "//cdn.smol.blog/$photoFileId/full",
						file: $photoObject->file,
					),
				],
				caption: 'TFW you changed something and the tests broke.',
				mediaHtml: ["<img src='//cdn.smol.blog/$photoFileId/full' alt='Troy returning with pizza.'>"],
				captionHtml: "<p>TFW you changed something and the tests broke.</p>\n",
			),
			siteId: Identifier::fromString(SecurityService::SITE1),
			authorId: Identifier::fromString(SecurityService::SITE1AUTHOR),
			permalink: '/post/' . $contentParams['contentId'],
			publishTimestamp: $actual->publishTimestamp ?? 'error', // Only concerned that this property exists.
			visibility: ContentVisibility::Published,
			extensions: [
				Tags::class => new Tags(tags: [
					new Tag('tfw'),
					new Tag('relatable'),
					new Tag('community'),
				]),
			]
		);

		$this->assertEquals($expected, $actual);
	}

	public function testReblogCreation() {
		$contentParams = [
			'contentId' => Identifier::fromString(self::REBLOG),
			'siteId' => Identifier::fromString(SecurityService::SITE1),
			'userId' => Identifier::fromString(SecurityService::SITE1AUTHOR),
		];

		App::dispatch(new CreateReblog(
			...$contentParams,
			url: 'https://eph.me/',
		));
		App::dispatch(new SetTags(
			...$contentParams,
			tags: ['Rick Astley', 'dead dove do not eat'],
		));
		App::dispatch(new PublishReblog(...$contentParams));

		// Don't use $contentParams here since it should be published and available to anonymous requests.
		$actual = App::fetch(new ContentById(
			id: Identifier::fromString(self::REBLOG),
			siteId: Identifier::fromString(SecurityService::SITE1),
		));
		$expected = new Content(
			id: Identifier::fromString(self::REBLOG),
			type: new Reblog(
				url: 'https://eph.me/',
				info: new ExternalContentInfo(
					title: 'A Webpage',
					embed: '<a href="https://eph.me/" target="_blank">A Webpage</a>',
				)
			),
			siteId: Identifier::fromString(SecurityService::SITE1),
			authorId: Identifier::fromString(SecurityService::SITE1AUTHOR),
			permalink: '/post/' . $contentParams['contentId'],
			publishTimestamp: $actual->publishTimestamp ?? 'error', // Only concerned that this property exists.
			visibility: ContentVisibility::Published,
			extensions: [
				Tags::class => new Tags(tags: [
					new Tag('Rick Astley'),
					new Tag('dead dove do not eat'),
				]),
			]
		);

		$this->assertEquals($expected, $actual);
	}
}
