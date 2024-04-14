<?php

namespace Smolblog\Core\ContentV1\Data;

use DateTimeImmutable;
use DateTimeInterface;
use Illuminate\Database\Schema\Blueprint;
use Smolblog\Core\ContentV1\Content;
use Smolblog\Core\ContentV1\ContentBuilder;
use Smolblog\Core\ContentV1\ContentExtension;
use Smolblog\Core\ContentV1\ContentVisibility;
use Smolblog\Core\ContentV1\Events\ContentBaseAttributeEdited;
use Smolblog\Core\ContentV1\Events\ContentBodyEdited;
use Smolblog\Core\ContentV1\Events\ContentCreated;
use Smolblog\Core\ContentV1\Events\ContentDeleted;
use Smolblog\Core\ContentV1\Events\ContentExtensionEdited;
use Smolblog\Core\ContentV1\Events\PermalinkAssigned;
use Smolblog\Core\ContentV1\Events\PublicContentAdded;
use Smolblog\Core\ContentV1\Events\PublicContentRemoved;
use Smolblog\Core\ContentV1\Extensions\Tags\Tag;
use Smolblog\Core\ContentV1\Extensions\Tags\Tags;
use Smolblog\Core\ContentV1\GenericContent;
use Smolblog\Core\ContentV1\Queries\ContentById;
use Smolblog\Core\ContentV1\Queries\ContentByPermalink;
use Smolblog\Core\ContentV1\Queries\ContentList;
use Smolblog\Core\ContentV1\Queries\ContentVisibleToUser;
use Smolblog\Core\ContentV1\Queries\GenericContentById;
use Smolblog\Core\ContentV1\Queries\UserCanEditContent;
use Smolblog\Core\Site\UserHasPermissionForSite;
use Smolblog\Foundation\Service\Messaging\MessageBus;
use Smolblog\Framework\Objects\ExtendableValueKit;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Framework\Objects\Value;
use Smolblog\Test\Kits\DatabaseTestKit;
use Smolblog\Test\TestCase;

final class StandardContentProjectionTest extends TestCase {
	use DatabaseTestKit;

	private StandardContentProjection $projection;
	private MessageBus $bus;

	protected function setUp(): void {
		$this->initDatabaseWithTable('standard_content', function(Blueprint $table) {
			$table->uuid('content_uuid')->primary();
			$table->string('type');
			$table->string('title')->nullable();
			$table->text('body')->nullable();
			$table->uuid('author_uuid');
			$table->uuid('site_uuid');
			$table->string('permalink')->nullable();
			$table->dateTimeTz('publish_timestamp')->nullable();
			$table->string('visibility');
			$table->text('extensions');
		});

		$this->bus = $this->createMock(MessageBus::class);
		$this->projection = new StandardContentProjection(db: $this->db, bus: $this->bus);
	}

	private function setUpSampleRow(): void {
		$this->db->table('standard_content')->insert([
			'content_uuid' => '3a694a6b-9540-45e6-8ec1-2a02a92d955d',
			'type' => 'spud',
			'title' => 'poTAYtos',
			'body' => '<p>Boil them, mash them</p>',
			'author_uuid' => '81721bdc-2c22-4c3a-90ca-d34194557767',
			'site_uuid' => '27ccd497-acac-4196-9b9a-70b95e49f463',
			'permalink' => null,
			'publish_timestamp' => null,
			'visibility' => ContentVisibility::Draft->value,
			'extensions' => '[]',
		]);
	}

	private function setUpManySampleRows(): void {
		$this->db->table('standard_content')->insert([
			'content_uuid' => '3a694a6b-9540-45e6-8ec1-2a02a92d955d',
			'type' => 'spud',
			'title' => 'poTAYtos',
			'body' => '<p>Boil them, mash them</p>',
			'author_uuid' => '81721bdc-2c22-4c3a-90ca-d34194557767',
			'site_uuid' => '27ccd497-acac-4196-9b9a-70b95e49f463',
			'permalink' => null,
			'publish_timestamp' => null,
			'visibility' => ContentVisibility::Draft->value,
			'extensions' => '[]',
		]);
		$this->db->table('standard_content')->insert([
			'content_uuid' => '9d5a93fa-a724-4cc5-b3f9-7e98b57ee393',
			'type' => 'spud',
			'title' => 'Taters',
			'body' => '<p>Stick \'em in a stew</p>',
			'author_uuid' => '81721bdc-2c22-4c3a-90ca-d34194557767',
			'site_uuid' => '27ccd497-acac-4196-9b9a-70b95e49f463',
			'permalink' => null,
			'publish_timestamp' => null,
			'visibility' => ContentVisibility::Draft->value,
			'extensions' => '[]',
		]);
		$this->db->table('standard_content')->insert([
			'content_uuid' => '699dda92-de68-4674-88db-abdc5c82edea',
			'type' => 'spud',
			'title' => 'Chips',
			'body' => '<p>Nice, crispy chips.</p>',
			'author_uuid' => '81721bdc-2c22-4c3a-90ca-d34194557767',
			'site_uuid' => '27ccd497-acac-4196-9b9a-70b95e49f463',
			'permalink' => '/spud/chips',
			'publish_timestamp' => '2022-02-22T22:22:22.000+00:00',
			'visibility' => ContentVisibility::Published->value,
			'extensions' => '{"'.Tags::class.'":{"tags":["one","two"]}}',
		]);
		$this->db->table('standard_content')->insert([
			'content_uuid' => '6e200361-549c-40e9-a88a-ff851870afbb',
			'type' => 'fish',
			'title' => 'He ruins it!',
			'body' => '<p>You stupid, fat hobbit!</p>',
			'author_uuid' => 'ba0c4043-4013-4cdc-b896-fdd1b9df80f8',
			'site_uuid' => '27ccd497-acac-4196-9b9a-70b95e49f463',
			'permalink' => null,
			'publish_timestamp' => null,
			'visibility' => ContentVisibility::Draft->value,
			'extensions' => '[]',
		]);
		$this->db->table('standard_content')->insert([
			'content_uuid' => 'f10a9ab8-32be-4f32-81cc-b47c2f814fcd',
			'type' => 'fish',
			'title' => 'Give it to us',
			'body' => '<p>RAW and wiggling</p>',
			'author_uuid' => 'ba0c4043-4013-4cdc-b896-fdd1b9df80f8',
			'site_uuid' => '27ccd497-acac-4196-9b9a-70b95e49f463',
			'permalink' => null,
			'publish_timestamp' => null,
			'visibility' => ContentVisibility::Draft->value,
			'extensions' => '[]',
		]);
		$this->db->table('standard_content')->insert([
			'content_uuid' => '28d096f3-dc85-4524-8c06-c66c2cdda533',
			'type' => 'fish',
			'title' => 'Pbpbpbpbpbpb',
			'body' => '<p>You keep nasty chips</p>',
			'author_uuid' => 'ba0c4043-4013-4cdc-b896-fdd1b9df80f8',
			'site_uuid' => '27ccd497-acac-4196-9b9a-70b95e49f463',
			'permalink' => '/fish/chips',
			'publish_timestamp' => '2022-02-22T22:22:22.000+00:00',
			'visibility' => ContentVisibility::Published->value,
			'extensions' => '{"'.Tags::class.'":{"tags":["one","two"]}}',
		]);
		$this->db->table('standard_content')->insert([
			'content_uuid' => '6c2a956a-4081-45df-acf2-0019e81e20c6',
			'type' => 'spud',
			'title' => 'Heinz 57',
			'body' => '<p>And french fride potatoes</p>',
			'author_uuid' => '81721bdc-2c22-4c3a-90ca-d34194557767',
			'site_uuid' => 'f627d542-9ef8-4124-abf5-d03b7e6ae4d3',
			'permalink' => null,
			'publish_timestamp' => null,
			'visibility' => ContentVisibility::Draft->value,
			'extensions' => '[]',
		]);
		$this->db->table('standard_content')->insert([
			'content_uuid' => 'a29ac7e4-de42-4ae1-a02e-82b6b0220279',
			'type' => 'spud',
			'title' => 'Waffle',
			'body' => '<p>Technically they came from Belgium</p>',
			'author_uuid' => '81721bdc-2c22-4c3a-90ca-d34194557767',
			'site_uuid' => 'f627d542-9ef8-4124-abf5-d03b7e6ae4d3',
			'permalink' => null,
			'publish_timestamp' => null,
			'visibility' => ContentVisibility::Draft->value,
			'extensions' => '[]',
		]);
		$this->db->table('standard_content')->insert([
			'content_uuid' => 'c4472739-c32d-4524-b5b1-f59b4435b4ca',
			'type' => 'spud',
			'title' => 'Put-back',
			'body' => '<p>Everything is better with CHEESE!</p>',
			'author_uuid' => '81721bdc-2c22-4c3a-90ca-d34194557767',
			'site_uuid' => 'f627d542-9ef8-4124-abf5-d03b7e6ae4d3',
			'permalink' => '/spud/put-back',
			'publish_timestamp' => '2022-02-22T22:22:22.000+00:00',
			'visibility' => ContentVisibility::Published->value,
			'extensions' => '{"'.Tags::class.'":{"tags":["one","two"]}}',
		]);
	}

	public function testItAddsNewContentToTheTable() {
		$event = new class(
			authorId: Identifier::fromString('81721bdc-2c22-4c3a-90ca-d34194557767'),
			contentId: Identifier::fromString('3a694a6b-9540-45e6-8ec1-2a02a92d955d'),
			userId: Identifier::fromString('4151844f-9031-477e-b6e9-0d4842a9697c'),
			siteId: Identifier::fromString('27ccd497-acac-4196-9b9a-70b95e49f463'),
		) extends ContentCreated {
			public function getContentType(): string { return 'spud'; }
			public function getNewBody(): string { return '<p>Boil them, mash them</p>'; }
			public function getNewTitle(): string { return 'poTAYtos'; }
			protected function getContentPayload(): array { return []; }
		};

		$this->projection->onContentCreated($event);

		$this->assertOnlyTableEntryEquals(
			table: $this->db->table('standard_content'),
			content_uuid: '3a694a6b-9540-45e6-8ec1-2a02a92d955d',
			type: 'spud',
			title: 'poTAYtos',
			body: '<p>Boil them, mash them</p>',
			author_uuid: '81721bdc-2c22-4c3a-90ca-d34194557767',
			site_uuid: '27ccd497-acac-4196-9b9a-70b95e49f463',
			permalink: null,
			publish_timestamp: null,
			visibility: ContentVisibility::Draft->value,
			extensions: '[]',
		);
	}

	public function testItUpdatesTitleAndBody() {
		$this->setUpSampleRow();

		$event = new class(
			contentId: Identifier::fromString('3a694a6b-9540-45e6-8ec1-2a02a92d955d'),
			userId: Identifier::fromString('4151844f-9031-477e-b6e9-0d4842a9697c'),
			siteId: Identifier::fromString('27ccd497-acac-4196-9b9a-70b95e49f463'),
		) extends ContentBodyEdited {
			public function getNewTitle(): ?string { return 'You keep nasty chips'; }
			public function getNewBody(): ?string { return '<h1>GIVE IT TO US RAW</h1>'; }
			public function getPayload(): array { return []; }
		};

		$this->projection->onContentBodyEdited($event);

		$this->assertOnlyTableEntryEquals(
			$this->db->table('standard_content'),
			content_uuid: '3a694a6b-9540-45e6-8ec1-2a02a92d955d',
			type: 'spud',
			title: 'You keep nasty chips',
			body: '<h1>GIVE IT TO US RAW</h1>',
			author_uuid: '81721bdc-2c22-4c3a-90ca-d34194557767',
			site_uuid: '27ccd497-acac-4196-9b9a-70b95e49f463',
			permalink: null,
			publish_timestamp: null,
			visibility: ContentVisibility::Draft->value,
			extensions: '[]',
		);
	}

	public function testItUpdatesBaseAttributes() {
		$this->setUpSampleRow();

		$event = new ContentBaseAttributeEdited(
			contentId: Identifier::fromString('3a694a6b-9540-45e6-8ec1-2a02a92d955d'),
			userId: Identifier::fromString('4151844f-9031-477e-b6e9-0d4842a9697c'),
			siteId: Identifier::fromString('27ccd497-acac-4196-9b9a-70b95e49f463'),
			publishTimestamp: new DateTimeImmutable('2022-02-22 22:22:22 +0:00'),
			authorId: Identifier::fromString('2ad53cbc-d639-4be2-a87c-27a99769bdbf'),
		);

		$this->projection->onContentBaseAttributeEdited($event);

		$this->assertOnlyTableEntryEquals(
			$this->db->table('standard_content'),
			content_uuid: '3a694a6b-9540-45e6-8ec1-2a02a92d955d',
			type: 'spud',
			title: 'poTAYtos',
			body: '<p>Boil them, mash them</p>',
			author_uuid: '2ad53cbc-d639-4be2-a87c-27a99769bdbf',
			site_uuid: '27ccd497-acac-4196-9b9a-70b95e49f463',
			permalink: null,
			publish_timestamp: '2022-02-22T22:22:22.000+00:00',
			visibility: ContentVisibility::Draft->value,
			extensions: '[]',
		);
	}

	public function testItUpdatesPermalinks() {
		$this->setUpSampleRow();

		$event = new PermalinkAssigned(
			contentId: Identifier::fromString('3a694a6b-9540-45e6-8ec1-2a02a92d955d'),
			userId: Identifier::fromString('4151844f-9031-477e-b6e9-0d4842a9697c'),
			siteId: Identifier::fromString('27ccd497-acac-4196-9b9a-70b95e49f463'),
			permalink: '/ask/what-is-taters',
		);

		$this->projection->onPermalinkAssigned($event);

		$this->assertOnlyTableEntryEquals(
			$this->db->table('standard_content'),
			content_uuid: '3a694a6b-9540-45e6-8ec1-2a02a92d955d',
			type: 'spud',
			title: 'poTAYtos',
			body: '<p>Boil them, mash them</p>',
			author_uuid: '81721bdc-2c22-4c3a-90ca-d34194557767',
			site_uuid: '27ccd497-acac-4196-9b9a-70b95e49f463',
			permalink: '/ask/what-is-taters',
			publish_timestamp: null,
			visibility: ContentVisibility::Draft->value,
			extensions: '[]',
		);
	}

	public function testItAddsANewExtension() {
		$this->setUpSampleRow();

		$ext = new class(one: 'two', three: 'four') extends Value implements ContentExtension {
			use ExtendableValueKit;
			public function __construct(mixed ...$props) { $this->extendedFields = $props; }
		};

		$event = new class(
			ext: $ext,
			contentId: Identifier::fromString('3a694a6b-9540-45e6-8ec1-2a02a92d955d'),
			userId: Identifier::fromString('4151844f-9031-477e-b6e9-0d4842a9697c'),
			siteId: Identifier::fromString('27ccd497-acac-4196-9b9a-70b95e49f463'),
		) extends ContentExtensionEdited {
			public function __construct(private $ext, mixed ...$props) { parent::__construct(...$props); }
			public function getNewExtension(): ContentExtension { return $this->ext; }
			public function getPayload(): array { return []; }
		};

		$this->projection->onContentExtensionEdited($event);

		$this->assertOnlyTableEntryEquals(
			$this->db->table('standard_content'),
			content_uuid: '3a694a6b-9540-45e6-8ec1-2a02a92d955d',
			type: 'spud',
			title: 'poTAYtos',
			body: '<p>Boil them, mash them</p>',
			author_uuid: '81721bdc-2c22-4c3a-90ca-d34194557767',
			site_uuid: '27ccd497-acac-4196-9b9a-70b95e49f463',
			permalink: null,
			publish_timestamp: null,
			visibility: ContentVisibility::Draft->value,
			extensions: json_encode([get_class($ext) => $ext->toArray()]),
		);
	}

	public function testItAddsASecondExtension() {
		$this->setUpSampleRow();
		$this->db->table('standard_content')->update(['extensions' => '{"std_class":{"one":"two"}}']);

		$ext = new class(one: 'two', three: 'four') extends Value implements ContentExtension {
			use ExtendableValueKit;
			public function __construct(mixed ...$props) { $this->extendedFields = $props; }
		};

		$event = new class(
			ext: $ext,
			contentId: Identifier::fromString('3a694a6b-9540-45e6-8ec1-2a02a92d955d'),
			userId: Identifier::fromString('4151844f-9031-477e-b6e9-0d4842a9697c'),
			siteId: Identifier::fromString('27ccd497-acac-4196-9b9a-70b95e49f463'),
		) extends ContentExtensionEdited {
			public function __construct(private $ext, mixed ...$props) { parent::__construct(...$props); }
			public function getNewExtension(): ContentExtension { return $this->ext; }
			public function getPayload(): array { return []; }
		};

		$this->projection->onContentExtensionEdited($event);

		$this->assertOnlyTableEntryEquals(
			$this->db->table('standard_content'),
			content_uuid: '3a694a6b-9540-45e6-8ec1-2a02a92d955d',
			type: 'spud',
			title: 'poTAYtos',
			body: '<p>Boil them, mash them</p>',
			author_uuid: '81721bdc-2c22-4c3a-90ca-d34194557767',
			site_uuid: '27ccd497-acac-4196-9b9a-70b95e49f463',
			permalink: null,
			publish_timestamp: null,
			visibility: ContentVisibility::Draft->value,
			extensions: json_encode(['std_class' => ['one' => 'two'], get_class($ext) => $ext->toArray()]),
		);
	}

	public function testItReplacesAnExistingExtension() {
		$this->setUpSampleRow();
		$ext = new class(one: 'two', three: 'four') extends Value implements ContentExtension {
			use ExtendableValueKit;
			public function __construct(mixed ...$props) { $this->extendedFields = $props; }
		};
		$this->db->table('standard_content')->update(['extensions' => json_encode([get_class($ext) => $ext->toArray()])]);

		$newExt = new (get_class($ext))(one: 'two', three: 'five!');

		$event = new class(
			ext: $newExt,
			contentId: Identifier::fromString('3a694a6b-9540-45e6-8ec1-2a02a92d955d'),
			userId: Identifier::fromString('4151844f-9031-477e-b6e9-0d4842a9697c'),
			siteId: Identifier::fromString('27ccd497-acac-4196-9b9a-70b95e49f463'),
		) extends ContentExtensionEdited {
			public function __construct(private $ext, mixed ...$props) { parent::__construct(...$props); }
			public function getNewExtension(): ContentExtension { return $this->ext; }
			public function getPayload(): array { return []; }
		};

		$this->projection->onContentExtensionEdited($event);

		$this->assertOnlyTableEntryEquals(
			$this->db->table('standard_content'),
			content_uuid: '3a694a6b-9540-45e6-8ec1-2a02a92d955d',
			type: 'spud',
			title: 'poTAYtos',
			body: '<p>Boil them, mash them</p>',
			author_uuid: '81721bdc-2c22-4c3a-90ca-d34194557767',
			site_uuid: '27ccd497-acac-4196-9b9a-70b95e49f463',
			permalink: null,
			publish_timestamp: null,
			visibility: ContentVisibility::Draft->value,
			extensions: json_encode([get_class($ext) => $newExt->toArray()]),
		);
	}

	public function testItDeletesContent() {
		$this->setUpSampleRow();

		$this->projection->onContentDeleted(new ContentDeleted(
			contentId: Identifier::fromString('3a694a6b-9540-45e6-8ec1-2a02a92d955d'),
			userId: Identifier::fromString('4151844f-9031-477e-b6e9-0d4842a9697c'),
			siteId: Identifier::fromString('27ccd497-acac-4196-9b9a-70b95e49f463'),
		));

		$this->assertTableEmpty($this->db->table('standard_content'));
	}

	public function testItAddsAPublishTimestampWhenPublishingContent() {
		$this->setUpSampleRow();

		$event = new PublicContentAdded(
			contentId: Identifier::fromString('3a694a6b-9540-45e6-8ec1-2a02a92d955d'),
			userId: Identifier::fromString('4151844f-9031-477e-b6e9-0d4842a9697c'),
			siteId: Identifier::fromString('27ccd497-acac-4196-9b9a-70b95e49f463'),
		);
		$this->projection->onPublicContentAdded($event);

		$this->assertOnlyTableEntryEquals(
			$this->db->table('standard_content'),
			content_uuid: '3a694a6b-9540-45e6-8ec1-2a02a92d955d',
			type: 'spud',
			title: 'poTAYtos',
			body: '<p>Boil them, mash them</p>',
			author_uuid: '81721bdc-2c22-4c3a-90ca-d34194557767',
			site_uuid: '27ccd497-acac-4196-9b9a-70b95e49f463',
			permalink: null,
			publish_timestamp: $event->timestamp->format(DateTimeInterface::RFC3339_EXTENDED),
			visibility: ContentVisibility::Published->value,
			extensions: '[]',
		);
	}

	public function testItDoesNotChangeAnExistingPublishTimestampWhenPublishingContent() {
		$this->setUpSampleRow();
		$this->db->table('standard_content')->update(['publish_timestamp' => '2022-02-02T22:22:22.000+00:00']);

		$this->projection->onPublicContentAdded(new PublicContentAdded(
			contentId: Identifier::fromString('3a694a6b-9540-45e6-8ec1-2a02a92d955d'),
			userId: Identifier::fromString('4151844f-9031-477e-b6e9-0d4842a9697c'),
			siteId: Identifier::fromString('27ccd497-acac-4196-9b9a-70b95e49f463'),
		));

		$this->assertOnlyTableEntryEquals(
			$this->db->table('standard_content'),
			content_uuid: '3a694a6b-9540-45e6-8ec1-2a02a92d955d',
			type: 'spud',
			title: 'poTAYtos',
			body: '<p>Boil them, mash them</p>',
			author_uuid: '81721bdc-2c22-4c3a-90ca-d34194557767',
			site_uuid: '27ccd497-acac-4196-9b9a-70b95e49f463',
			permalink: null,
			publish_timestamp: '2022-02-02T22:22:22.000+00:00',
			visibility: ContentVisibility::Published->value,
			extensions: '[]',
		);
	}

	public function testItUnpublishesContent() {
		$this->setUpSampleRow();
		$this->db->table('standard_content')->update([
			'publish_timestamp' => '2022-02-02T22:22:22.000+00:00',
			'visibility' => ContentVisibility::Published->value,
		]);

		$this->projection->onPublicContentRemoved(new PublicContentRemoved(
			contentId: Identifier::fromString('3a694a6b-9540-45e6-8ec1-2a02a92d955d'),
			userId: Identifier::fromString('4151844f-9031-477e-b6e9-0d4842a9697c'),
			siteId: Identifier::fromString('27ccd497-acac-4196-9b9a-70b95e49f463'),
		));

		$this->assertOnlyTableEntryEquals(
			$this->db->table('standard_content'),
			content_uuid: '3a694a6b-9540-45e6-8ec1-2a02a92d955d',
			type: 'spud',
			title: 'poTAYtos',
			body: '<p>Boil them, mash them</p>',
			author_uuid: '81721bdc-2c22-4c3a-90ca-d34194557767',
			site_uuid: '27ccd497-acac-4196-9b9a-70b95e49f463',
			permalink: null,
			publish_timestamp: '2022-02-02T22:22:22.000+00:00',
			visibility: ContentVisibility::Draft->value,
			extensions: '[]',
		);
	}

	public function testItAddsStandardAttributesOnDraftContentToAContentBuilder() {
		$this->setUpSampleRow();
		$ext = new class(one: 'two', three: 'four') extends Value implements ContentExtension {
			use ExtendableValueKit;
			public function __construct(mixed ...$props) { $this->extendedFields = $props; }
		};

		$message = $this->createMock(ContentBuilder::class);
		$message->method('getContentId')->willReturn(Identifier::fromString('3a694a6b-9540-45e6-8ec1-2a02a92d955d'));
		$message->expects($this->once())->method('setContentProperty')->with(
			id: $this->equalTo(Identifier::fromString('3a694a6b-9540-45e6-8ec1-2a02a92d955d')),
			siteId: $this->equalTo(Identifier::fromString('27ccd497-acac-4196-9b9a-70b95e49f463')),
			authorId: $this->equalTo(Identifier::fromString('81721bdc-2c22-4c3a-90ca-d34194557767')),
			permalink: null,
			publishTimestamp: null,
			visibility: $this->equalTo(ContentVisibility::Draft),
		);

		$this->projection->onContentBuilder($message);
	}

	public function testItAddsStandardAttributesOnPublishedContentToAContentBuilder() {
		$this->setUpSampleRow();
		$ext = new class(one: 'two', three: 'four') extends Value implements ContentExtension {
			use ExtendableValueKit;
			public function __construct(mixed ...$props) { $this->extendedFields = $props; }
		};
		$this->db->table('standard_content')->update([
			'permalink' => '/ask/whats-taters-precious',
			'publish_timestamp' => '2022-02-02T22:22:22.000+00:00',
			'visibility' => ContentVisibility::Published->value,
			'extensions' => json_encode([get_class($ext) => $ext->toArray()]),
		]);

		$message = $this->createMock(ContentBuilder::class);
		$message->method('getContentId')->willReturn(Identifier::fromString('3a694a6b-9540-45e6-8ec1-2a02a92d955d'));
		$message->expects($this->once())->method('setContentProperty')->with(
			id: $this->equalTo(Identifier::fromString('3a694a6b-9540-45e6-8ec1-2a02a92d955d')),
			siteId: $this->equalTo(Identifier::fromString('27ccd497-acac-4196-9b9a-70b95e49f463')),
			authorId: $this->equalTo(Identifier::fromString('81721bdc-2c22-4c3a-90ca-d34194557767')),
			permalink: $this->equalTo('/ask/whats-taters-precious'),
			publishTimestamp: $this->equalTo(new DateTimeImmutable('2022-02-02T22:22:22.000+00:00')),
			visibility: $this->equalTo(ContentVisibility::Published),
		);
		$message->expects($this->once())->method('addContentExtension')->with($this->equalTo($ext));

		$this->projection->onContentBuilder($message);
	}

	public function testItAddsTitleAndBodyToAGenericContentQuery() {
		$this->setUpSampleRow();

		$message = new class(
			contentId: Identifier::fromString('3a694a6b-9540-45e6-8ec1-2a02a92d955d'),
			userId: Identifier::fromString('4151844f-9031-477e-b6e9-0d4842a9697c'),
			siteId: Identifier::fromString('27ccd497-acac-4196-9b9a-70b95e49f463'),
		) extends GenericContentById {
			public function getContentType() { return $this->contentProps['type']; }
		};

		$this->projection->onGenericContentBuilder($message);
		$this->assertEquals(
			new GenericContent(title: 'poTAYtos', body: '<p>Boil them, mash them</p>'),
			$message->getContentType(),
		);
	}

	public function testPublicContentIsAlwaysVisible() {
		$this->setUpSampleRow();
		$this->db->table('standard_content')->update([
			'publish_timestamp' => '2022-02-02T22:22:22.000+00:00',
			'visibility' => ContentVisibility::Published->value,
		]);

		$siteAndContent = [
			'contentId' => Identifier::fromString('3a694a6b-9540-45e6-8ec1-2a02a92d955d'),
			'siteId' => Identifier::fromString('27ccd497-acac-4196-9b9a-70b95e49f463'),
		];
		$this->bus->expects($this->never())->method('fetch');

		$anonQuery = new ContentVisibleToUser(...$siteAndContent, userId: null);
		$this->projection->onContentVisibleToUser($anonQuery);
		$this->assertTrue($anonQuery->results());

		$otherQuery = new ContentVisibleToUser(...$siteAndContent, userId: $this->randomId());
		$this->projection->onContentVisibleToUser($otherQuery);
		$this->assertTrue($otherQuery->results());

		$ownerQuery = new ContentVisibleToUser(
			...$siteAndContent,
			userId: Identifier::fromString('81721bdc-2c22-4c3a-90ca-d34194557767')
		);
		$this->projection->onContentVisibleToUser($ownerQuery);
		$this->assertTrue($ownerQuery->results());
	}

	public function testAnAuthorCanAlwaysViewTheirContent() {
		$this->setUpSampleRow();

		$siteAndContent = [
			'contentId' => Identifier::fromString('3a694a6b-9540-45e6-8ec1-2a02a92d955d'),
			'siteId' => Identifier::fromString('27ccd497-acac-4196-9b9a-70b95e49f463'),
		];
		$this->bus->expects($this->never())->method('fetch');

		$ownerQuery = new ContentVisibleToUser(
			...$siteAndContent,
			userId: Identifier::fromString('81721bdc-2c22-4c3a-90ca-d34194557767')
		);
		$this->projection->onContentVisibleToUser($ownerQuery);
		$this->assertTrue($ownerQuery->results());
	}

	public function testDraftContentIsNeverVisibleToPublic() {
		$this->setUpSampleRow();

		$siteAndContent = [
			'contentId' => Identifier::fromString('3a694a6b-9540-45e6-8ec1-2a02a92d955d'),
			'siteId' => Identifier::fromString('27ccd497-acac-4196-9b9a-70b95e49f463'),
		];
		$this->bus->expects($this->never())->method('fetch');

		$anonQuery = new ContentVisibleToUser(...$siteAndContent, userId: null);
		$this->projection->onContentVisibleToUser($anonQuery);
		$this->assertFalse($anonQuery->results());
	}

	public function testItChecksForAdminIfContentIsDraftAndUserIsNotAuthor() {
		$this->setUpSampleRow();
		$userId = $this->randomId();
		$contentId = Identifier::fromString('3a694a6b-9540-45e6-8ec1-2a02a92d955d');
		$siteId = Identifier::fromString('27ccd497-acac-4196-9b9a-70b95e49f463');

		$this->bus->expects($this->once())->method('fetch')->with($this->equalTo(
			new UserHasPermissionForSite(siteId: $siteId, userId: $userId, mustBeAdmin: true)
		));

		$otherQuery = new ContentVisibleToUser(siteId: $siteId, userId: $userId, contentId: $contentId);
		$this->projection->onContentVisibleToUser($otherQuery);
	}

	public function testAuthorCanAlwaysEditTheirOwnContent() {
		$this->setUpSampleRow();

		$siteAndContent = [
			'contentId' => Identifier::fromString('3a694a6b-9540-45e6-8ec1-2a02a92d955d'),
			'siteId' => Identifier::fromString('27ccd497-acac-4196-9b9a-70b95e49f463'),
		];
		$this->bus->expects($this->never())->method('fetch');

		$ownerQuery = new UserCanEditContent(
			...$siteAndContent,
			userId: Identifier::fromString('81721bdc-2c22-4c3a-90ca-d34194557767')
		);
		$this->projection->onUserCanEditContent($ownerQuery);
		$this->assertTrue($ownerQuery->results());
	}

	public function testItChecksForAdminIfContentBeingEditedIsNotByUser() {
		$this->setUpSampleRow();
		$userId = $this->randomId();
		$contentId = Identifier::fromString('3a694a6b-9540-45e6-8ec1-2a02a92d955d');
		$siteId = Identifier::fromString('27ccd497-acac-4196-9b9a-70b95e49f463');

		$this->bus->expects($this->once())->method('fetch')->with($this->equalTo(
			new UserHasPermissionForSite(siteId: $siteId, userId: $userId, mustBeAdmin: true)
		));

		$otherQuery = new UserCanEditContent(siteId: $siteId, userId: $userId, contentId: $contentId);
		$this->projection->onUserCanEditContent($otherQuery);
	}

	public function testItWillFindContentByPermalink() {
		$this->setUpSampleRow();
		$this->db->table('standard_content')->update([
			'permalink' => '/ask/whats-taters-precious.html',
			'publish_timestamp' => '2022-02-02T22:22:22.000+00:00',
			'visibility' => ContentVisibility::Published->value,
		]);

		$query = new ContentByPermalink(
			siteId: Identifier::fromString('27ccd497-acac-4196-9b9a-70b95e49f463'),
			permalink: '/ask/whats-taters-precious.html',
		);
		$this->projection->onContentByPermalink($query);

		$this->assertEquals(Identifier::fromString('3a694a6b-9540-45e6-8ec1-2a02a92d955d'), $query->getContentId());
		$this->assertEquals('spud', $query->getContentType());
	}

	public function testItWillFindContentById() {
		$this->setUpSampleRow();
		$this->db->table('standard_content')->update([
			'permalink' => '/ask/whats-taters-precious.html',
			'publish_timestamp' => '2022-02-02T22:22:22.000+00:00',
			'visibility' => ContentVisibility::Published->value,
		]);

		$query = new ContentById(
			id: Identifier::fromString('3a694a6b-9540-45e6-8ec1-2a02a92d955d'),
			siteId: Identifier::fromString('27ccd497-acac-4196-9b9a-70b95e49f463'),
		);
		$this->projection->onContentById($query);

		$this->assertEquals(Identifier::fromString('3a694a6b-9540-45e6-8ec1-2a02a92d955d'), $query->getContentId());
		$this->assertEquals('spud', $query->getContentType());
	}

	public function testContentCanBeQueriedAnonymously() {
		$this->setUpManySampleRows();
		$query = new ContentList(siteId: Identifier::fromString('27ccd497-acac-4196-9b9a-70b95e49f463'));

		$this->projection->onContentList($query);

		$this->assertEquals([
			new Content(
				id: Identifier::fromString('699dda92-de68-4674-88db-abdc5c82edea'),
				type: new GenericContent('Chips', '<p>Nice, crispy chips.</p>', 'spud'),
				siteId: $query->siteId,
				authorId: Identifier::fromString('81721bdc-2c22-4c3a-90ca-d34194557767'),
				permalink: '/spud/chips',
				publishTimestamp: new DateTimeImmutable('2022-02-22T22:22:22.000+00:00'),
				visibility: ContentVisibility::Published,
			),
			new Content(
				id: Identifier::fromString('28d096f3-dc85-4524-8c06-c66c2cdda533'),
				type: new GenericContent('Pbpbpbpbpbpb', '<p>You keep nasty chips</p>', 'fish'),
				siteId: $query->siteId,
				authorId: Identifier::fromString('ba0c4043-4013-4cdc-b896-fdd1b9df80f8'),
				permalink: '/fish/chips',
				publishTimestamp: new DateTimeImmutable('2022-02-22T22:22:22.000+00:00'),
				visibility: ContentVisibility::Published,
			),
		], $query->results());
		$this->assertEquals($query->count, 2);
	}

	public function testAuthorsSeeAllTheirOwnContent() {
		$this->setUpManySampleRows();
		$query = new ContentList(
			siteId: Identifier::fromString('27ccd497-acac-4196-9b9a-70b95e49f463'),
			userId: Identifier::fromString('81721bdc-2c22-4c3a-90ca-d34194557767'),
		);

		$this->projection->onContentList($query);

		$this->assertEquals([
			new Content(
				id: Identifier::fromString('699dda92-de68-4674-88db-abdc5c82edea'),
				type: new GenericContent('Chips', '<p>Nice, crispy chips.</p>', 'spud'),
				siteId: $query->siteId,
				authorId: Identifier::fromString('81721bdc-2c22-4c3a-90ca-d34194557767'),
				permalink: '/spud/chips',
				publishTimestamp: new DateTimeImmutable('2022-02-22T22:22:22.000+00:00'),
				visibility: ContentVisibility::Published,
			),
			new Content(
				id: Identifier::fromString('28d096f3-dc85-4524-8c06-c66c2cdda533'),
				type: new GenericContent('Pbpbpbpbpbpb', '<p>You keep nasty chips</p>', 'fish'),
				siteId: $query->siteId,
				authorId: Identifier::fromString('ba0c4043-4013-4cdc-b896-fdd1b9df80f8'),
				permalink: '/fish/chips',
				publishTimestamp: new DateTimeImmutable('2022-02-22T22:22:22.000+00:00'),
				visibility: ContentVisibility::Published,
			),
			new Content(
				id: Identifier::fromString('3a694a6b-9540-45e6-8ec1-2a02a92d955d'),
				type: new GenericContent('poTAYtos', '<p>Boil them, mash them</p>', 'spud'),
				siteId: $query->siteId,
				authorId: Identifier::fromString('81721bdc-2c22-4c3a-90ca-d34194557767'),
				visibility: ContentVisibility::Draft,
			),
			new Content(
				id: Identifier::fromString('9d5a93fa-a724-4cc5-b3f9-7e98b57ee393'),
				type: new GenericContent('Taters', '<p>Stick \'em in a stew</p>', 'spud'),
				siteId: $query->siteId,
				authorId: Identifier::fromString('81721bdc-2c22-4c3a-90ca-d34194557767'),
				visibility: ContentVisibility::Draft,
			),
		], $query->results());
		$this->assertEquals($query->count, 4);
	}

	public function testAdminsSeeAllContent() {
		$this->setUpManySampleRows();
		$query = new ContentList(
			siteId: Identifier::fromString('27ccd497-acac-4196-9b9a-70b95e49f463'),
			userId: Identifier::fromString('81721bdc-2c22-4c3a-90ca-d34194557767'),
		);

		// Make the admin check true.
		$this->bus->method('fetch')->willReturn(true);

		$this->projection->onContentList($query);

		$this->assertEquals([
			new Content(
				id: Identifier::fromString('699dda92-de68-4674-88db-abdc5c82edea'),
				type: new GenericContent('Chips', '<p>Nice, crispy chips.</p>', 'spud'),
				siteId: $query->siteId,
				authorId: Identifier::fromString('81721bdc-2c22-4c3a-90ca-d34194557767'),
				permalink: '/spud/chips',
				publishTimestamp: new DateTimeImmutable('2022-02-22T22:22:22.000+00:00'),
				visibility: ContentVisibility::Published,
			),
			new Content(
				id: Identifier::fromString('28d096f3-dc85-4524-8c06-c66c2cdda533'),
				type: new GenericContent('Pbpbpbpbpbpb', '<p>You keep nasty chips</p>', 'fish'),
				siteId: $query->siteId,
				authorId: Identifier::fromString('ba0c4043-4013-4cdc-b896-fdd1b9df80f8'),
				permalink: '/fish/chips',
				publishTimestamp: new DateTimeImmutable('2022-02-22T22:22:22.000+00:00'),
				visibility: ContentVisibility::Published,
			),
			new Content(
				id: Identifier::fromString('3a694a6b-9540-45e6-8ec1-2a02a92d955d'),
				type: new GenericContent('poTAYtos', '<p>Boil them, mash them</p>', 'spud'),
				siteId: $query->siteId,
				authorId: Identifier::fromString('81721bdc-2c22-4c3a-90ca-d34194557767'),
				visibility: ContentVisibility::Draft,
			),
			new Content(
				id: Identifier::fromString('9d5a93fa-a724-4cc5-b3f9-7e98b57ee393'),
				type: new GenericContent('Taters', '<p>Stick \'em in a stew</p>', 'spud'),
				siteId: $query->siteId,
				authorId: Identifier::fromString('81721bdc-2c22-4c3a-90ca-d34194557767'),
				visibility: ContentVisibility::Draft,
			),
			new Content(
				id: Identifier::fromString('6e200361-549c-40e9-a88a-ff851870afbb'),
				type: new GenericContent('He ruins it!', '<p>You stupid, fat hobbit!</p>', 'fish'),
				siteId: $query->siteId,
				authorId: Identifier::fromString('ba0c4043-4013-4cdc-b896-fdd1b9df80f8'),
				visibility: ContentVisibility::Draft,
			),
			new Content(
				id: Identifier::fromString('f10a9ab8-32be-4f32-81cc-b47c2f814fcd'),
				type: new GenericContent('Give it to us', '<p>RAW and wiggling</p>', 'fish'),
				siteId: $query->siteId,
				authorId: Identifier::fromString('ba0c4043-4013-4cdc-b896-fdd1b9df80f8'),
				visibility: ContentVisibility::Draft,
			),
		], $query->results());
		$this->assertEquals($query->count, 6);
	}

	public function testTheContentListCanBePaged() {
		$this->setUpManySampleRows();
		$query = new ContentList(
			siteId: Identifier::fromString('27ccd497-acac-4196-9b9a-70b95e49f463'),
			userId: Identifier::fromString('81721bdc-2c22-4c3a-90ca-d34194557767'),
			page: 2,
			pageSize: 2
		);

		// Make the admin check true.
		$this->bus->method('fetch')->willReturn(true);

		$this->projection->onContentList($query);

		$this->assertEquals([
			new Content(
				id: Identifier::fromString('3a694a6b-9540-45e6-8ec1-2a02a92d955d'),
				type: new GenericContent('poTAYtos', '<p>Boil them, mash them</p>', 'spud'),
				siteId: $query->siteId,
				authorId: Identifier::fromString('81721bdc-2c22-4c3a-90ca-d34194557767'),
				visibility: ContentVisibility::Draft,
			),
			new Content(
				id: Identifier::fromString('9d5a93fa-a724-4cc5-b3f9-7e98b57ee393'),
				type: new GenericContent('Taters', '<p>Stick \'em in a stew</p>', 'spud'),
				siteId: $query->siteId,
				authorId: Identifier::fromString('81721bdc-2c22-4c3a-90ca-d34194557767'),
				visibility: ContentVisibility::Draft,
			),
		], $query->results());
		$this->assertEquals($query->count, 6);
	}

	public function testTheContentListCanBeFilteredByVisibility() {
		$this->setUpManySampleRows();
		$query = new ContentList(
			siteId: Identifier::fromString('27ccd497-acac-4196-9b9a-70b95e49f463'),
			userId: Identifier::fromString('81721bdc-2c22-4c3a-90ca-d34194557767'),
			visibility: [ContentVisibility::Draft]
		);

		// Make the admin check true.
		$this->bus->method('fetch')->willReturn(true);

		$this->projection->onContentList($query);

		$this->assertEquals([
			new Content(
				id: Identifier::fromString('3a694a6b-9540-45e6-8ec1-2a02a92d955d'),
				type: new GenericContent('poTAYtos', '<p>Boil them, mash them</p>', 'spud'),
				siteId: $query->siteId,
				authorId: Identifier::fromString('81721bdc-2c22-4c3a-90ca-d34194557767'),
				visibility: ContentVisibility::Draft,
			),
			new Content(
				id: Identifier::fromString('9d5a93fa-a724-4cc5-b3f9-7e98b57ee393'),
				type: new GenericContent('Taters', '<p>Stick \'em in a stew</p>', 'spud'),
				siteId: $query->siteId,
				authorId: Identifier::fromString('81721bdc-2c22-4c3a-90ca-d34194557767'),
				visibility: ContentVisibility::Draft,
			),
			new Content(
				id: Identifier::fromString('6e200361-549c-40e9-a88a-ff851870afbb'),
				type: new GenericContent('He ruins it!', '<p>You stupid, fat hobbit!</p>', 'fish'),
				siteId: $query->siteId,
				authorId: Identifier::fromString('ba0c4043-4013-4cdc-b896-fdd1b9df80f8'),
				visibility: ContentVisibility::Draft,
			),
			new Content(
				id: Identifier::fromString('f10a9ab8-32be-4f32-81cc-b47c2f814fcd'),
				type: new GenericContent('Give it to us', '<p>RAW and wiggling</p>', 'fish'),
				siteId: $query->siteId,
				authorId: Identifier::fromString('ba0c4043-4013-4cdc-b896-fdd1b9df80f8'),
				visibility: ContentVisibility::Draft,
			),
		], $query->results());
		$this->assertEquals($query->count, 4);
	}

	public function testTheContentListCanBeFilteredByType() {
		$this->setUpManySampleRows();
		$query = new ContentList(
			siteId: Identifier::fromString('27ccd497-acac-4196-9b9a-70b95e49f463'),
			userId: Identifier::fromString('81721bdc-2c22-4c3a-90ca-d34194557767'),
			types: ['fish'],
		);

		// Make the admin check true.
		$this->bus->method('fetch')->willReturn(true);

		$this->projection->onContentList($query);

		$this->assertEquals([
			new Content(
				id: Identifier::fromString('28d096f3-dc85-4524-8c06-c66c2cdda533'),
				type: new GenericContent('Pbpbpbpbpbpb', '<p>You keep nasty chips</p>', 'fish'),
				siteId: $query->siteId,
				authorId: Identifier::fromString('ba0c4043-4013-4cdc-b896-fdd1b9df80f8'),
				permalink: '/fish/chips',
				publishTimestamp: new DateTimeImmutable('2022-02-22T22:22:22.000+00:00'),
				visibility: ContentVisibility::Published,
			),
			new Content(
				id: Identifier::fromString('6e200361-549c-40e9-a88a-ff851870afbb'),
				type: new GenericContent('He ruins it!', '<p>You stupid, fat hobbit!</p>', 'fish'),
				siteId: $query->siteId,
				authorId: Identifier::fromString('ba0c4043-4013-4cdc-b896-fdd1b9df80f8'),
				visibility: ContentVisibility::Draft,
			),
			new Content(
				id: Identifier::fromString('f10a9ab8-32be-4f32-81cc-b47c2f814fcd'),
				type: new GenericContent('Give it to us', '<p>RAW and wiggling</p>', 'fish'),
				siteId: $query->siteId,
				authorId: Identifier::fromString('ba0c4043-4013-4cdc-b896-fdd1b9df80f8'),
				visibility: ContentVisibility::Draft,
			),
		], $query->results());
		$this->assertEquals($query->count, 3);
	}
}
