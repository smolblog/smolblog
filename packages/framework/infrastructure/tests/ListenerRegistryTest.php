<?php

namespace Smolblog\Framework\Infrastructure;

use Smolblog\Test\TestCase;
use Smolblog\Foundation\Value\Traits\AuthorizableMessage;
use Smolblog\Foundation\Value\Messages\Query;
use Smolblog\Foundation\Service\Messaging\Attributes\SecurityLayerListener;
use Smolblog\Foundation\Service\Messaging\Attributes\CheckMemoLayerListener;
use Smolblog\Foundation\Service\Messaging\Attributes\ContentBuildLayerListener;
use Smolblog\Foundation\Service\Messaging\Attributes\EventStoreLayerListener;
use Smolblog\Foundation\Service\Messaging\Attributes\ExecutionLayerListener;
use Smolblog\Foundation\Service\Messaging\Attributes\SaveMemoLayerListener;
use Smolblog\Foundation\Value\Messages\Command;
use Smolblog\Foundation\Service\Messaging\Listener;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Messages\DomainEvent;
use Smolblog\Foundation\Value\Traits\Memoizable;

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
	public function checkMemo(Memoizable $message) {
		listenerTestTrace(add: __METHOD__);
	}
	#[EventStoreLayerListener]
	public function eventStore(DomainEvent $event) {
		listenerTestTrace(add: __METHOD__);
	}
	#[ContentBuildLayerListener]
	public function contentBuild(Command $event) {
		listenerTestTrace(add: __METHOD__);
	}
	public function onExecute(Command $event) {
		listenerTestTrace(add: __METHOD__);
	}
	#[SaveMemoLayerListener]
	public function saveMemo(Memoizable $query) {
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
	public function beforeCheckMemo(Memoizable $message) {
		listenerTestTrace(add: __METHOD__);
	}
	#[CheckMemoLayerListener(later: 1)]
	public function afterCheckMemo(Memoizable $message) {
		listenerTestTrace(add: __METHOD__);
	}

	#[EventStoreLayerListener(earlier: 1)]
	public function beforeEventStore(DomainEvent $message) {
		listenerTestTrace(add: __METHOD__);
	}
	#[EventStoreLayerListener(later: 1)]
	public function afterEventStore(DomainEvent $message) {
		listenerTestTrace(add: __METHOD__);
	}

	#[ContentBuildLayerListener(earlier: 1)]
	public function beforeContentBuild(DomainEvent $message) {
		listenerTestTrace(add: __METHOD__);
	}
	#[ContentBuildLayerListener(later: 1)]
	public function afterContentBuild(DomainEvent $message) {
		listenerTestTrace(add: __METHOD__);
	}

	#[ExecutionLayerListener(earlier: 1)]
	public function beforeExecution(Command $message) {
		listenerTestTrace(add: __METHOD__);
	}
	#[ExecutionLayerListener(later: 1)]
	public function afterExecution(Command $message) {
		listenerTestTrace(add: __METHOD__);
	}

	#[SaveMemoLayerListener(earlier: 1)]
	public function beforeSaveMemo(Memoizable $message) {
		listenerTestTrace(add: __METHOD__);
	}
	#[SaveMemoLayerListener(later: 1)]
	public function afterSaveMemo(Memoizable $message) {
		listenerTestTrace(add: __METHOD__);
	}
}

final class ListenerRegistryTest extends TestCase {
	private ListenerRegistry $provider;
	private ServiceRegistry $container;

	public function setUp(): void {
		$this->container = new ServiceRegistry([
			ListenerTestMainService::class => [],
			ListenerTestTimingService::class => [],
		]);

		$this->provider = new ListenerRegistry(container: $this->container);
	}

	public function testItRegistersListeners() {
		$this->assertEquals(Listener::class, ListenerRegistry::getInterfaceToRegister());
	}

	public function testListenerCanBeACallable() {
		$this->provider->registerCallable(fn(DomainEvent $event) => listenerTestTrace(add: 'Callable 1'));
		$this->provider->registerCallable(fn(Query $event) => listenerTestTrace(add: 'Callable 2'));
		$event = new readonly class() extends Query {};

		foreach ($this->provider->getListenersForEvent($event) as $listener) { $listener($event); }

		$this->assertEquals(['Callable 2'], listenerTestTrace(reset: true));
	}

	public function testListenerCanBeAService() {
		$this->provider->registerService(ListenerTestMainService::class);
		$event = new readonly class() extends Command {};

		foreach ($this->provider->getListenersForEvent($event) as $listener) { $listener($event); }

		$this->assertEquals([
			ListenerTestMainService::class . '::' . 'contentBuild',
			ListenerTestMainService::class . '::' . 'onExecute'
		], listenerTestTrace(reset: true));
	}

	public function testTimingLayerCanBeSetWithAttributes() {
		$this->provider->registerCallable(fn(DomainEvent $event) => listenerTestTrace(add: 'Callable'));
		$this->provider->registerService(ListenerTestMainService::class);
		$event = new readonly class() extends DomainEvent implements AuthorizableMessage {
			public function __construct() {
				parent::__construct(userId: Identifier::fromString('33a893ad-2bab-453c-8e48-106859435aad'));
			}
			public function getAuthorizationQuery(): Query { return new readonly class() extends Query {}; }
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
		$event = $this->createStub(Memoizable::class);

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
