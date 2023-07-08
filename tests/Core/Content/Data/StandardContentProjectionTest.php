<?php

namespace Smolblog\Core\Content\Data;

use DateTimeImmutable;
use DateTimeInterface;
use Illuminate\Database\Schema\Blueprint;
use Smolblog\Core\Content\ContentBuilder;
use Smolblog\Core\Content\ContentExtension;
use Smolblog\Core\Content\ContentVisibility;
use Smolblog\Core\Content\Events\ContentBaseAttributeEdited;
use Smolblog\Core\Content\Events\ContentBodyEdited;
use Smolblog\Core\Content\Events\ContentCreated;
use Smolblog\Core\Content\Events\ContentDeleted;
use Smolblog\Core\Content\Events\ContentExtensionEdited;
use Smolblog\Core\Content\Events\PermalinkAssigned;
use Smolblog\Core\Content\Events\PublicContentAdded;
use Smolblog\Core\Content\Events\PublicContentRemoved;
use Smolblog\Core\Content\GenericContent;
use Smolblog\Core\Content\Queries\GenericContentById;
use Smolblog\Framework\Objects\ExtendableValueKit;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Framework\Objects\Value;
use Smolblog\Test\DatabaseTestKit;
use Smolblog\Test\TestCase;

final class StandardContentProjectionTest extends TestCase {
	use DatabaseTestKit;

	private StandardContentProjection $projection;

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

		$this->projection = new StandardContentProjection(db: $this->db);
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

	public function testItAddsStandardAttributesToAContentBuilder() {
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

		$this->projection->onGenericContentById($message);
		$this->assertEquals(
			new GenericContent(title: 'poTAYtos', body: '<p>Boil them, mash them</p>'),
			$message->getContentType(),
		);
	}
}
