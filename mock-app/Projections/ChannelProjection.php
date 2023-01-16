<?php

namespace Smolblog\Mock\Projections;

use PDO;
use Smolblog\Core\Connector\Entities\Connection;
use Smolblog\Core\Connector\Queries\ChannelsForConnection;

class ChannelProjection {
	public function __construct(private PDO $db) {}

	public function onChannelsForConnection(ChannelsForConnection $query) {
		$prepared = $this->db->prepare('SELECT
			channel_key as channelKey,
			display_name as displayName,
			details,
		FROM connections WHERE connection_id = ?');
		$prepared->execute([$query->connectionId->toByteString()]);
		$results = $prepared->fetchAll(mode: PDO::FETCH_ASSOC);
		// $results['details'] = json_decode($results['details'], associative: true);
		//array map results to new Channel objects
	}
}
