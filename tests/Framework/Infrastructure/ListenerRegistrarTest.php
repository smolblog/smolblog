<?php

namespace Smolblog\Framework\Infrastructure;

use Generator;
use PHPUnit\Framework\TestCase;
use Smolblog\App\Container\Container;
use Smolblog\Framework\Messages\AuthorizableMessage;
use Smolblog\Framework\Messages\Event;
use Smolblog\Framework\Messages\Hook;
use Smolblog\Framework\Messages\MemoizableQuery;
use Smolblog\Framework\Messages\Query;
use Smolblog\Framework\Messages\StoppableMessageKit;
use Smolblog\Framework\Messages\Attributes\SecurityLayerListener;
use Smolblog\Framework\Messages\Attributes\CheckMemoLayerListener;
use Smolblog\Framework\Messages\Attributes\EventStoreLayerListener;
use Smolblog\Framework\Messages\Attributes\ExecutionLayerListener;
use Smolblog\Framework\Messages\Attributes\SaveMemoLayerListener;

function listenerTestTrace($add = '', $reset = false) {
	static $trace;
	if ($reset) {
		$snapshot = $trace;
		$trace = [];
		return $snapshot;
	}

	$trace ??= [];
	$trace[] = $add;
	return $trace;
}

final class ListenerTestMainService {
	#[SecurityLayerListener]
	public function security(AuthorizableMessage $message) {
		listenerTestTrace(add: __METHOD__);
	}
	#[CheckMemoLayerListener]
	public function checkMemo(MemoizableQuery $message) {
		listenerTestTrace(add: __METHOD__);
	}
	#[EventStoreLayerListener]
	public function eventStore(Event $event) {
		listenerTestTrace(add: __METHOD__);
	}
	public function onExecute(Hook $event) {
		listenerTestTrace(add: __METHOD__);
	}
	#[SaveMemoLayerListener]
	public function saveMemo(MemoizableQuery $query) {
		listenerTestTrace(add: __METHOD__);
	}
}

final class ListenerTestTimingService {
	#[SecurityLayerListener(earlier: 1)]
	public function beforeSecurity(AuthorizableMessage $message) {
		listenerTestTrace(add: __METHOD__);
	}
	#[SecurityLayerListener(later: 1)]
	public function afterSecurity(AuthorizableMessage $message) {
		listenerTestTrace(add: __METHOD__);
	}

	#[CheckMemoLayerListener(earlier: 1)]
	public function beforeCheckMemo(MemoizableQuery $message) {
		listenerTestTrace(add: __METHOD__);
	}
	#[CheckMemoLayerListener(later: 1)]
	public function afterCheckMemo(MemoizableQuery $message) {
		listenerTestTrace(add: __METHOD__);
	}

	#[EventStoreLayerListener(earlier: 1)]
	public function beforeEventStore(Event $message) {
		listenerTestTrace(add: __METHOD__);
	}
	#[EventStoreLayerListener(later: 1)]
	public function afterEventStore(Event $message) {
		listenerTestTrace(add: __METHOD__);
	}

	#[ExecutionLayerListener(earlier: 1)]
	public function beforeExecution(Hook $message) {
		listenerTestTrace(add: __METHOD__);
	}
	#[ExecutionLayerListener(later: 1)]
	public function afterExecution(Hook $message) {
		listenerTestTrace(add: __METHOD__);
	}

	#[SaveMemoLayerListener(earlier: 1)]
	public function beforeSaveMemo(MemoizableQuery $message) {
		listenerTestTrace(add: __METHOD__);
	}
	#[SaveMemoLayerListener(later: 1)]
	public function afterSaveMemo(MemoizableQuery $message) {
		listenerTestTrace(add: __METHOD__);
	}
}

final class ListenerRegistrarTest extends TestCase {
	private ListenerRegistrar $provider;
	private ServiceRegistrar $container;

	public function setUp(): void {
		$this->container = new ServiceRegistrar([
			ListenerTestMainService::class => [],
			ListenerTestTimingService::class => [],
		]);

		$this->provider = new ListenerRegistrar(container: $this->container);
	}

	public function testListenerCanBeACallable() {
		$this->provider->registerCallable(fn(Event $event) => listenerTestTrace(add: 'Callable 1'));
		$this->provider->registerCallable(fn(Query $event) => listenerTestTrace(add: 'Callable 2'));
		$event = $this->createStub(Query::class);

		foreach ($this->provider->getListenersForEvent($event) as $listener) { $listener($event); }

		$this->assertEquals(['Callable 2'], listenerTestTrace(reset: true));
	}

	public function testListenerCanBeAService() {
		$this->provider->registerService(ListenerTestMainService::class);
		$event = $this->createStub(Hook::class);

		foreach ($this->provider->getListenersForEvent($event) as $listener) { $listener($event); }

		$this->assertEquals([ListenerTestMainService::class . '::' . 'onExecute'], listenerTestTrace(reset: true));
	}

	public function testTimingLayerCanBeSetWithAttributes() {
		$this->provider->registerService(ListenerTestMainService::class);
		$this->provider->registerCallable(fn(Event $event) => listenerTestTrace(add: 'Callable'));
		$event = new class() extends Event implements AuthorizableMessage {
			use StoppableMessageKit;
			public function getAuthorizationQuery(): Query { return new class() extends Query {}; }
		};

		foreach ($this->provider->getListenersForEvent($event) as $listener) { $listener($event); }

		$this->assertEquals([
			ListenerTestMainService::class . '::' . 'security',
			ListenerTestMainService::class . '::' . 'eventStore',
			'Callable',
		], listenerTestTrace(reset: true));
	}

	public function testTimingAttributesCanBeAdjusted() {
		$this->provider->registerService(ListenerTestMainService::class);
		$this->provider->registerService(ListenerTestTimingService::class);
		$event = $this->createStub(MemoizableQuery::class);

		foreach ($this->provider->getListenersForEvent($event) as $listener) { $listener($event); }

		$this->assertEquals([
			ListenerTestTimingService::class . '::' . 'beforeCheckMemo',
			ListenerTestMainService::class . '::' . 'checkMemo',
			ListenerTestTimingService::class . '::' . 'afterCheckMemo',
			ListenerTestTimingService::class . '::' . 'beforeSaveMemo',
			ListenerTestMainService::class . '::' . 'saveMemo',
			ListenerTestTimingService::class . '::' . 'afterSaveMemo',
		], listenerTestTrace(reset: true));
	}
}
