<?php

namespace Smolblog\CoreDataSql;

require_once __DIR__ . '/_base.php';

use Smolblog\Core\Channel\Entities\Channel;
use Smolblog\Core\Channel\Events\ChannelSaved;
use Smolblog\Core\Channel\Events\ContentPushedToChannel;
use Smolblog\Core\Connection\Entities\Connection;
use Smolblog\Core\Connection\Events\ConnectionEstablished;
use Smolblog\Core\Content\Events\ContentCreated;
use Smolblog\Core\Content\Fields\Markdown;
use Smolblog\Core\Content\Types\Note\Note;
use Smolblog\CoreDataSql\Test\DataTestBase;
use Smolblog\Foundation\Value\Messages\DomainEvent;

final class EventStreamTest extends DataTestBase {
	public function testEventPersistence() {
		$db = $this->app->container->get(DatabaseManager::class)->getConnection();
		$this->assertEquals(0, $db->fetchOne('SELECT COUNT(*) FROM event_stream'));

		$userId = $this->randomId();
		$siteId = $this->randomId();
		$contentId = $this->randomId();
		$connectionId = Connection::buildId('test', 'onetwo');

		$expected = [
			new ConnectionEstablished(
				provider: 'test',
				providerKey: 'onetwo',
				displayName: '@oneTwo',
				details: ['one' => 2],
				userId: $userId,
			),
			new ChannelSaved(
				new Channel(
					handler: 'test',
					handlerKey: 'onetwo',
					displayName: '@oneTwo',
					userId: $userId,
					connectionId: $connectionId,
				),
				userId: $userId,
			),
			new ContentCreated(
				body: new Note(new Markdown('The night is full of holes')),
				aggregateId: $siteId,
				userId: $userId,
				entityId: $contentId,
			),
			new class(
				userId: $userId,
				aggregateId: $siteId,
				processId: $this->randomId()
			) extends DomainEvent {}
		];

		foreach($expected as $event) {
			$this->app->dispatch($event);
		}

		$actual = array_map(
			fn($json) => \is_string($json) ? DomainEvent::fromJson($json) : DomainEvent::deserializeValue($json),
			$db->fetchFirstColumn('SELECT event_obj FROM event_stream'),
		);
		$this->assertEquals($expected, $actual);
	}
}
