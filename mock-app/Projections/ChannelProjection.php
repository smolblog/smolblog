<?php

namespace Smolblog\Mock\Projections;

use PDO;
use Smolblog\Core\Connector\Entities\Channel;
use Smolblog\Core\Connector\Events\ChannelSaved;
use Smolblog\Core\Connector\Queries\ChannelsForConnection;

class ChannelProjection {
	public function __construct(private PDO $db) {}

	public function onChannelSaved(ChannelSaved $event) {
		$prepared = $this->db->prepare('INSERT INTO channels
			(channel_id, connection_id, channel_key, display_name, details)
			VALUES (:id, :connectionId, :channelKey, :displayName, :details)');
		$prepared->execute([
			'id' => Channel::buildId(connectionId: $event->connectionId, channelKey: $event->channelKey)->toByteString(),
			'connectionId' => $event->connectionId->toByteString(),
			'channelKey' => $event->channelKey,
			'displayName' => $event->displayName,
			'details' => json_encode($event->details),
		]);
	}

	public function onChannelsForConnection(ChannelsForConnection $query) {
		$prepared = $this->db->prepare('SELECT
			channel_key as channelKey,
			display_name as displayName,
			details
		FROM channels WHERE connection_id = ?');
		$prepared->execute([$query->connectionId->toByteString()]);
		$results = $prepared->fetchAll(mode: PDO::FETCH_ASSOC);
		// $results['details'] = json_decode($results['details'], associative: true);
		//array map results to new Channel objects
		$query->setResults(array_map(fn($row) => new Channel(
			connectionId: $query->connectionId,
			channelKey: $row['channelKey'],
			displayName: $row['displayName'],
			details: json_decode($row['details'], associative: true),
		), $results));
	}
}
