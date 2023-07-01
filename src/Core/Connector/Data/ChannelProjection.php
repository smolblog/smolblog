<?php

namespace Smolblog\Core\Connector\Data;

use Illuminate\Database\ConnectionInterface;
use Smolblog\Core\Connector\Entities\Channel;
use Smolblog\Core\Connector\Events\ChannelDeleted;
use Smolblog\Core\Connector\Events\ChannelSaved;
use Smolblog\Core\Connector\Queries\ChannelById;
use Smolblog\Core\Connector\Queries\ChannelsForConnection;
use Smolblog\Framework\Messages\Projection;
use Smolblog\Framework\Objects\Identifier;

/**
 * Store Channel state.
 *
 * Table structure:
 * channel_uuid varchar(40) NOT NULL UNIQUE,
 * connection_uuid varchar(40) NOT NULL,
 * channel_key varchar(50) NOT NULL,
 * display_name varchar(100) NOT NULL,
 * details text NOT NULL,
 */
class ChannelProjection implements Projection {
	public const TABLE = 'channels';

	/**
	 * Construct the service.
	 *
	 * @param ConnectionInterface $db Working database connection.
	 */
	public function __construct(
		private ConnectionInterface $db
	) {
	}

	/**
	 * Save a channel to the database. Replaces an existing channel with the same ID.
	 *
	 * @param ChannelSaved $event Event to persist.
	 * @return void
	 */
	public function onChannelSaved(ChannelSaved $event) {
		$channelId = Channel::buildId(
			connectionId: $event->connectionId,
			channelKey: $event->channelKey,
		);

		$this->db->table(self::TABLE)->upsert(
			[
				'channel_uuid' => $channelId->toString(),
				'connection_uuid' => $event->connectionId->toString(),
				'channel_key' => $event->channelKey,
				'display_name' => $event->displayName,
				'details' => json_encode($event->details),
			],
			'channel_uuid',
			['display_name', 'details']
		);
	}

	/**
	 * Remove a channel.
	 *
	 * @param ChannelDeleted $event Event to persist.
	 * @return void
	 */
	public function onChannelDeleted(ChannelDeleted $event) {
		$this->db->table(self::TABLE)->where('channel_uuid', Channel::buildId(
			connectionId: $event->connectionId,
			channelKey: $event->channelKey,
		))->delete();
	}

	/**
	 * Retrieve a single Channel.
	 *
	 * @param ChannelById $query Query to execute.
	 * @return void
	 */
	public function onChannelById(ChannelById $query) {
		$result = $this->db->table(self::TABLE)->where('channel_uuid', $query->channelId->toString())->first();

		$query->setResults($this->channelFromRow($result));
	}

	/**
	 * Get an array of all channels for a particular Connection.
	 *
	 * @param ChannelsForConnection $query Query to execute.
	 * @return void
	 */
	public function onChannelsForConnection(ChannelsForConnection $query) {
		$results = $this->db->table(self::TABLE)->where('connection_uuid', $query->connectionId->toString())->get();

		$query->setResults($results->map(fn($con) => $this->channelFromRow($con))->all());
	}

	/**
	 * Create a Channel object from database data
	 *
	 * @param object $data Object from the database.
	 * @return Channel
	 */
	public function channelFromRow(object $data): Channel {
		return new Channel(
			connectionId: Identifier::fromString($data->connection_uuid),
			channelKey: $data->channel_key,
			displayName: $data->display_name,
			details: json_decode($data->details, true),
		);
	}
}
