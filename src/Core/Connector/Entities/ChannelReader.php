<?php

namespace Smolblog\Core\Connector\Entities;

use Smolblog\Framework\Reader;

/**
 * Object to retrieve Channels from the repository.
 */
interface ChannelReader extends Reader {
	/**
	 * Get the indicated Channel from the repository. Should return null if not found.
	 *
	 * @param string|integer $id Unique identifier for the Channel.
	 * @return Channel Channel identified by $id; null if it does not exist.
	 */
	public function get(string|int $id): Channel;

	/**
	 * Get all channels for the given Connection.
	 *
	 * @param string $connectionId Connection to search by.
	 * @return Channel[] Array of Channels attached to this Connection.
	 */
	public function getChannelsForConnection(string $connectionId): array;

	/**
	 * Get all channels for all the given Connections.
	 *
	 * @param string[] $connectionIds Connections to search by.
	 * @return array[] Associative array of arrays of Channels keyed to their Connection.
	 */
	public function getChannelsForConnections(array $connectionIds): array;
}
