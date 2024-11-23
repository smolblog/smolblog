<?php

namespace Smolblog\CoreDataSql;

require_once __DIR__ . '/_base.php';

use Smolblog\Core\Channel\Entities\BasicChannel;
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
				new BasicChannel(
					handler: 'test',
					handlerKey: 'onetwo',
					displayName: '@oneTwo',
					userId: $userId,
					connectionId: $connectionId,
					details: ['eleven' => 22],
				),
				userId: $userId,
			),
			new ContentCreated(
				body: new Note(new Markdown('The night is full of holes')),
				aggregateId: $siteId,
				userId: $userId,
				entityId: $contentId,
			),
			new readonly class(
				userId: $userId,
				aggregateId: $siteId,
				processId: $this->randomId()
			) extends DomainEvent {}
		];

		foreach($expected as $event) {
			$this->app->dispatch($event);
		}

		$this->assertEquals(
			array_map(fn($evt) => json_encode($evt), $expected),
			$db->fetchFirstColumn('SELECT event_obj FROM event_stream')
		);
	}
}
