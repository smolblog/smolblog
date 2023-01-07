<?php

namespace Smolblog\Core\Importer;

use PHPUnit\Framework\TestCase;
use Smolblog\Core\Connector\Entities\Channel;
use Smolblog\Core\Connector\Entities\ChannelReader;
use Smolblog\Core\Connector\Entities\Connection;
use Smolblog\Core\Connector\Entities\ConnectionReader;
use Smolblog\Core\Connector\Services\RefreshConnectionToken;
use Smolblog\Core\Post\PostWriter;
use Smolblog\Framework\Executor;
use Smolblog\Framework\Objects\Identifier;

final class ImportStarterTest extends TestCase {
	public function testItHandlesThePullFromChannelCommand() {
		$connection = new Connection(Identifier::createRandom(), 'provider', 'key', 'name', []);
		$channel = new Channel($connection->id, 'key', 'name', []);
		$command = new PullFromChannel(channelId: $channel->id, options: []);

		$importer = $this->createMock(Importer::class);
		$importer->expects($this->once())->method('getPostsFromChannel')->willReturn(new ImportResults(
			posts: ['post'],
			nextPageCommand: $command,
		));

		$channelRepo = $this->createStub(ChannelReader::class);
		$channelRepo->method('get')->willReturn($channel);
		$connectionRepo = $this->createStub(ConnectionReader::class);
		$connectionRepo->method('get')->willReturn($connection);
		$importerRepo = $this->createStub(ImporterRegistrar::class);
		$importerRepo->method('get')->willReturn($importer);

		$refresher = $this->createStub(RefreshConnectionToken::class);
		$refresher->method('run')->willReturnArgument(0);

		$postWriter = $this->createMock(PostWriter::class);
		$postWriter->expects($this->once())->method('saveMany');

		$commandBus = $this->createMock(Executor::class);
		$commandBus->expects($this->once())->method('exec');

		(new ImportStarter(
			channelRepo: $channelRepo,
			connectionRepo: $connectionRepo,
			importerRepo: $importerRepo,
			postWriter: $postWriter,
			commandBus: $commandBus,
			refreshConnectionToken: $refresher,
		))->run(command: $command);
	}
}
