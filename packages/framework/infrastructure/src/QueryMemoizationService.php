<?php

namespace Smolblog\Framework\Infrastructure;

use Smolblog\Framework\Messages\MemoizableQuery;
use Smolblog\Framework\Messages\Attributes\CheckMemoLayerListener;
use Smolblog\Framework\Messages\Attributes\SaveMemoLayerListener;
use Smolblog\Framework\Messages\Listener;

/**
 * Simple class to memoize a query for the duration of a web request.
 *
 * This assumes that this is a standard PHP app that only lasts for the duration of a web request. If another
 * framework is being used that persists the application, then this service will need to be replaced.
 */
class QueryMemoizationService implements Listener {
	/**
	 * Store the memoization results.
	 *
	 * @var array
	 */
	protected array $memos = [];

	/**
	 * Check the incoming query for an existing memo and provide the results if so.
	 *
	 * @param MemoizableQuery $query Incoming query to check.
	 * @return void
	 */
	#[CheckMemoLayerListener()]
	public function checkMemo(MemoizableQuery $query): void {
		$key = $query->getMemoKey();
		if (!array_key_exists($key, $this->memos)) {
			return;
		}

		$query->setResults($this->memos[$key]);
		$query->stopMessage();
	}

	/**
	 * Save the results of the query to be used later.
	 *
	 * @param MemoizableQuery $query Outgoing query to save.
	 * @return void
	 */
	#[SaveMemoLayerListener()]
	public function setMemo(MemoizableQuery $query): void {
		$key = $query->getMemoKey();

		$this->memos[$key] = $query->results();
	}
}
