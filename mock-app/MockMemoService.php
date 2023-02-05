<?php

namespace Smolblog\Mock;

use Smolblog\Framework\Infrastructure\QueryMemoizationService;

class MockMemoService extends QueryMemoizationService {
	/**
	 * Remove all memos from the service. For testing.
	 *
	 * No seriously, just for testing.
	 *
	 * @return void
	 */
	public function reset(): void {
		$this->memos = [];
	}
}
