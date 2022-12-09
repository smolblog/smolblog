<?php

namespace Smolblog\Core\Importer;

use Smolblog\Core\Connector\Entities\{ChannelReader, ConnectionReader};
use Smolblog\Core\Connector\Services\RefreshConnectionToken;
use Smolblog\Core\Post\PostWriter;
use Smolblog\Framework\{Executor, Service};

/**
 * Handle the PullFromChannel command and send it to the appropriate Importer.
 */
class ImportStarter implements Service {
	/**
	 * Construct the service
	 *
	 * @param ChannelReader          $channelRepo            For fetching the supplied channel.
	 * @param ConnectionReader       $connectionRepo         For fetching the Channel's Connection.
	 * @param RefreshConnectionToken $refreshConnectionToken Check for token validity.
	 * @param ImporterRegistrar      $importerRepo           For fetching the Connection's Importer.
	 * @param PostWriter             $postWriter             For saving posts.
	 * @param Executor               $commandBus             For firing off the next page command, if any.
	 */
	public function __construct(
		private ChannelReader $channelRepo,
		private ConnectionReader $connectionRepo,
		private RefreshConnectionToken $refreshConnectionToken,
		private ImporterRegistrar $importerRepo,
		private PostWriter $postWriter,
		private Executor $commandBus,
	) {
	}

	/**
	 * Handle the PullFromChannel command.
	 *
	 * @param PullFromChannel $command Command to execute.
	 * @return void
	 */
	public function run(PullFromChannel $command): void {
		$channel = $this->channelRepo->get(id: $command->channelId);
		$connection = $this->connectionRepo->get(id: $channel->connectionId);
		$importer = $this->importerRepo->get(key: $connection->provider);

		$readyConnection = $this->refreshConnectionToken->run(connection: $connection);

		$results = $importer->getPostsFromChannel(
			connection: $readyConnection,
			channel: $channel,
			options: $command->options
		);
		$this->postWriter->saveMany(posts: $results->posts);

		if (isset($results->nextPageCommand)) {
			$this->commandBus->exec(command: $results->nextPageCommand);
		}
	}
}