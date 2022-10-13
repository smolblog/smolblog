<?php

namespace Smolblog\Core\Connector;

use Smolblog\Core\Entity\Reader;

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
}
