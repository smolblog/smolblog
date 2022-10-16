<?php

namespace Smolblog\Core\Importer;

use Smolblog\Core\Connector\{ChannelReader, ConnectionReader};
use Smolblog\Core\Parser\ParserRegistrar;
use Smolblog\Core\Post\{PostReader, PostWriter};

/**
 * Handle the PullFromChannel command and send it to the appropriate Importer.
 */
class ImportConductor {
	/**
	 * Construct the service
	 *
	 * @param ChannelReader     $channelRepo    Used to get the given Channel.
	 * @param ConnectionReader  $connectionRepo Used to get the Channel's Connection.
	 * @param ImporterRegistrar $importerRepo   Used to get the Connection's Importer.
	 */
	public function __construct(
		private ChannelReader $channelRepo,
		private ConnectionReader $connectionRepo,
		private ImporterRegistrar $importerRepo,
		private ParserRegistrar $parserRepo,
		private PostReader $postReader,
		private PostWriter $postWriter,
	) {
	}

	/**
	 * Handle the PullFromChannel command.
	 *
	 * @param PullFromChannel $command Command to execute.
	 * @return void
	 */
	public function handlePullFromChannel(PullFromChannel $command): void {
		$channel = $this->channelRepo->get(id: $command->channelId);
		$connection = $this->connectionRepo->get(id: $channel->connectionId);
		$importer = $this->importerRepo->get(key: $connection->provider);

		$stagedPosts = $importer->getPostsForImport(connection: $connection, channel: $channel, options: $command->options);
		$checkedIds = $this->postReader->checkImportIds(array_map(fn($p) => $p->importId, $stagedPosts));

		if (empty($checkedIds)) {
			return;
		}

		$parser = $this->parserRepo->get(key: $connection->provider);

		$postsToParse = array_filter($stagedPosts, fn($p) => false !== array_search($p->importId, $checkedIds));
		$postsToImport = array_map(fn($p) => $parser->createPost(data: $p->postData, options: $command->options), $postsToParse);
		$this->postWriter->saveMany(posts: $postsToImport);

		// check pagination info and queue next job
	}
}
