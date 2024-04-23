<?php

namespace Smolblog\Core\ContentV1\Data;

use DateTimeImmutable;
use Illuminate\Database\Schema\Blueprint;
use Smolblog\Core\ContentV1\Events\ContentEvent;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Test\Kits\DatabaseTestKit;
use Smolblog\Test\TestCase;

final class ContentEventStreamTest extends TestCase {
	use DatabaseTestKit;

	private ContentEventStream $stream;

	protected function setUp(): void {
		$this->initDatabaseWithTable('content_events', function(Blueprint $table) {
			$table->uuid('event_uuid')->primary();
			$table->dateTimeTz('event_time');
			$table->uuid('content_uuid');
			$table->uuid('user_uuid');
			$table->uuid('site_uuid');
			$table->string('event_type');
			$table->text('payload');
		});

		$this->stream = new ContentEventStream(db: $this->db);
	}

	public function testItPersistsAContentEvent() {
		$event = new class() extends ContentEvent {
			public function __construct() {
				parent::__construct(
					contentId: Identifier::fromString('3bf85790-60f7-41f5-a75f-a3b806be6a58'),
					userId: Identifier::fromString('dfd72da8-827e-472c-9a58-9d1ce3ed4482'),
					siteId: Identifier::fromString('d4a4887f-4140-4d7a-9a2b-a3cde6c5d4be'),
					id: Identifier::fromString('8289a96d-e8c7-4c6a-8d6e-143436c59ec2'),
					timestamp: new DateTimeImmutable('2022-02-22 02:02:02+00:00'),
				);
			}

			public function getPayload(): array {
				return ['one' => 'two', 'three' => 'four'];
			}
		};

		$this->stream->onContentEvent($event);

		$this->assertOnlyTableEntryEquals(
			table: $this->db->table('content_events'),
			event_uuid: '8289a96d-e8c7-4c6a-8d6e-143436c59ec2',
			event_time: '2022-02-22T02:02:02.000+00:00',
			content_uuid: '3bf85790-60f7-41f5-a75f-a3b806be6a58',
			user_uuid: 'dfd72da8-827e-472c-9a58-9d1ce3ed4482',
			site_uuid: 'd4a4887f-4140-4d7a-9a2b-a3cde6c5d4be',
			event_type: get_class($event),
			payload: '{"one":"two","three":"four"}',
		);
	}
}
