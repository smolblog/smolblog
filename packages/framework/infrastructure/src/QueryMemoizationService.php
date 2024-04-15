<?php

namespace Smolblog\Framework\Infrastructure;

use Smolblog\Foundation\Service\Messaging\MemoizableQuery;
use Smolblog\Foundation\Service\Messaging\CheckMemoListener;
use Smolblog\Foundation\Service\Messaging\SaveMemoListener;
use Smolblog\Foundation\Service\Messaging\Listener;
use Smolblog\Foundation\Value\Traits\Memoizable;

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
	 * @param Memoizable $query Incoming query to check.
	 * @return void
	 */
	#[CheckMemoListener()]
	public function checkMemo(Memoizable $query): void {
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
	 * @param Memoizable $query Outgoing query to save.
	 * @return void
	 */
	#[SaveMemoListener()]
	public function setMemo(Memoizable $query): void {
		$key = $query->getMemoKey();

		$this->memos[$key] = $query->results();
	}
}
