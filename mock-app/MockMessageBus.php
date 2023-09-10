<?php

namespace Smolblog\Mock;

use Smolblog\Framework\Infrastructure\DefaultMessageBus;
use Smolblog\Framework\Messages\Message;
use Smolblog\Framework\Messages\Query;

class MockMessageBus extends DefaultMessageBus {
	private int $indent = 0;
	private array $queue = [];

	private function startDispatch() {
		$this->indent += 1;
	}

	private function endDispatch() {
		$this->indent -= 1;

		if ($this->indent <= 0) {
			$this->indent = 0;
			if (empty($this->queue)) { return; }

			$next = array_shift($this->queue);
			$this->dispatch($next);
		}
	}

	public function dispatch(object $message): mixed {
		$this->startDispatch();
		$val = parent::dispatch($message);
		$this->endDispatch();
		return $val;
	}

	public function fetch(Query $query): mixed {
		$this->startDispatch();
		$val = parent::fetch($query);
		$this->endDispatch();
		return $val;
	}

	public function dispatchAsync(Message $message): void {
		$this->queue[] = $message;
	}
}
