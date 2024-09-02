<?php

namespace Smolblog\Foundation\Value\Messages;

use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Traits\Message;
use Smolblog\Foundation\Value\Traits\MessageKit;
use Smolblog\Foundation\Value\Traits\MessageMetadata;
use Smolblog\Foundation\Value\Traits\ReadonlyMessageKit;

/**
 * An object that asks the domain model a thing.
 *
 * A query can be as simple as fetching an object from a repo or represent something slightly more complex. By
 * creating objects and sending them through a central orchestrator, we can more easily cache queries or send
 * complex queries to specialized handlers.
 *
 * All Queries that can be memoized should also implement Memoizable; use MemoizableKit to assist.
 *
 * @deprecated Use data interfaces instead of queries
 */
abstract readonly class Query implements Message {
	use ReadonlyMessageKit;

	/**
	 * Create the Query and initialize the metadata.
	 */
	public function __construct() {
		$this->meta = new MessageMetadata();
	}

	/**
	 * Set the results of the query. Override to add any extra validation.
	 *
	 * @param mixed $results Results of the query.
	 * @return void
	 */
	public function setResults(mixed $results): void {
		$this->setMetaValue('results', $results);
	}

	/**
	 * Get the query results. Override to add extra logic or specify a return type.
	 *
	 * @return mixed
	 */
	public function results(): mixed {
		return $this->getMetaValue('results');
	}
}
