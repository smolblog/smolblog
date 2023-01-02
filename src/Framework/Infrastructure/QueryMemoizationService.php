<?php

namespace Smolblog\Framework\Infrastructure;

use Smolblog\Framework\Messages\MemoizableQuery;
use Smolblog\Framework\Messages\Attributes\CheckMemoLayerListener;
use Smolblog\Framework\Messages\Attributes\SaveMemoLayerListener;

/**
 * Simple class to memoize a query for the duration of a web request.
 *
 * This assumes that this is a standard PHP app that only lasts for the duration of a web request. If another
 * framework is being used that persists the application, then the reset() method should be called to remove the
 * existing memos.
 */
class QueryMemoizationService {
	/**
	 * Store the memoization results.
	 *
	 * @var array
	 */
	private array $memos = [];

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

		$query->results = $this->memos[$key];
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

		$this->memos[$key] = $query->results;
	}

	/**
	 * Delete the current memos.
	 *
	 * In the case where the memos need to be removed manually, such as when an external framework persists the
	 * application between web requests.
	 *
	 * @return void
	 */
	public function reset(): void {
		$this->memos = [];
	}
}
