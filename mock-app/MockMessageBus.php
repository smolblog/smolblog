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

			$next = array_shift($this->queue);
			$this->dispatch($next);
		}
	}

	public function dispatch(object $message): mixed {
		$this->startDispatch();
		parent::dispatch($message);
		$this->endDispatch();
	}

	public function fetch(Query $query): mixed {
		$this->startDispatch();
		parent::fetch($query);
		$this->endDispatch();
	}

	public function dispatchAsync(Message $message): void {
		$this->queue[] = $message;
	}
}
