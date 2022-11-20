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

final class ImportStarterTest extends TestCase {
	public function testItHandlesThePullFromChannelCommand() {
		$command = new PullFromChannel(channelId: '12|34|56', options: []);

		$importer = $this->createMock(Importer::class);
		$importer->expects($this->once())->method('getPostsFromChannel')->willReturn(new ImportResults(
			posts: ['post'],
			nextPageCommand: $command,
		));

		$channelRepo = $this->createStub(ChannelReader::class);
		$channelRepo->method('get')->willReturn(new Channel('id', 'key', 'name', []));
		$connectionRepo = $this->createStub(ConnectionReader::class);
		$connectionRepo->method('get')->willReturn(new Connection(5, 'provider', 'key', 'name', []));
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
