<?php

namespace Smolblog\Core\Connector;

use Smolblog\Core\Entity\Writer;

/**
 * Object for saving Channels to the repository.
 */
interface ChannelWriter extends Writer {
	/**
	 * Save the given Channel to the repository.
	 *
	 * @param Channel $channel Channel object to save.
	 * @return void
	 */
	public function save(Channel $channel): void;

	/**
	 * Delete the indicated Channel from the repository.
	 *
	 * @param string $id ID of Channel to delete.
	 * @return void
	 */
	public function delete(string $id): void;
}
