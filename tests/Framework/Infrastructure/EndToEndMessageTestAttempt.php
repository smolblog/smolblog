<?php

namespace Smolblog\Framework\Infrastructure\EndToEndTest;

use PHPUnit\Framework\TestCase;
use Smolblog\App\Container\Container;
use Smolblog\Framework\Messages\AuthorizableMessage;
use Smolblog\Framework\Messages\Command;
use Smolblog\Framework\Messages\Event;
use Smolblog\Framework\Messages\Hook;
use Smolblog\Framework\Messages\MemoizableQuery;
use Smolblog\Framework\Messages\Query;
use Smolblog\Framework\Messages\StoppableMessageKit;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Framework\Messages\MemoizableQueryKit;
use Smolblog\Framework\Messages\Attributes\SecurityLayerListener;
use Smolblog\Framework\Messages\Attributes\CheckMemoLayerListener;
use Smolblog\Framework\Messages\Attributes\EventStoreLayerListener;
use Smolblog\Framework\Messages\Attributes\ExecutionLayerListener;
use Smolblog\Framework\Messages\Attributes\SaveMemoLayerListener;
use Smolblog\Framework\Infrastructure\DefaultMessageBus;
use Smolblog\Framework\Infrastructure\ListenerRegistrar;
use Smolblog\Framework\Infrastructure\ServiceRegistrar;

function currentTrace(string $add = null) {
	static $trace;
	if (!isset($add)) {
		$snapshot = $trace;
		$trace = [];
		return $snapshot;
	}

	$trace ??= [];
	$trace[] = $add;
	return $trace;
}

class IsUserAuthorized extends Query {
	function __construct(
		public readonly bool $user,
		public readonly bool $blog,
	) {
	}
}

class PostsForBlog extends Query implements MemoizableQuery {
	use MemoizableQueryKit;

	function __construct(public readonly string $blogId) {}
}

class PostPost extends Command implements AuthorizableMessage {
	use StoppableMessageKit;
	function __construct(public readonly string $id, public readonly bool $allow) {}
	function getAuthorizationQuery(): Query { return new IsUserAuthorized(user: true, blog: $this->allow); }
}
class DeletePost extends Command implements AuthorizableMessage {
	use StoppableMessageKit;
	function __construct(public readonly string $id, public readonly bool $allow) {}
	function getAuthorizationQuery(): Query { return new IsUserAuthorized(user: true, blog: $this->allow); }
}

class PostPosted extends Event {
	function __construct(public readonly string $postId) { parent::__construct(); }
}
class PostDeleted extends Event {
	function __construct(public readonly string $postId) { parent::__construct(); }
}

class StandardContentPosted extends Hook {
	function __construct(public readonly Event $original) {}
}
class StandardContentDeleted extends Hook {
	function __construct(public readonly Event $original) {}
}

class SecurityService {
	public function __construct(private DefaultMessageBus $messageBus) {}

	#[SecurityLayerListener]
	public function onAuthorizableMessage(AuthorizableMessage $event): void {
		currentTrace(self::class . '::' . get_class($event));

		$securityQuery = $event->getAuthorizationQuery();
		if (!$this->messageBus->fetch($securityQuery)) {
			$event->stopMessage();
		}
	}

	public function onIsUserAuthorized(IsUserAuthorized $event): void {
		currentTrace(self::class . '::' . get_class($event));

		$event->results = ($event->user && $event->blog);
	}
}

class MemoizeService {
	private array $memos = [];

	#[CheckMemoLayerListener]
	public function checkMemo(MemoizableQuery $query) {
		currentTrace(self::class . '::' . get_class($query));
		$key = $query->getMemoKey();
		if (!array_key_exists($key, $this->memos)) { return; }

		$query->results = $this->memos[$key];
		$query->stopMessage();
	}

	#[SaveMemoLayerListener]
	public function setMemo(MemoizableQuery $query) {
		currentTrace(self::class . '::' . get_class($query));
		$key = $query->getMemoKey();

		$this->memos[$key] = $query->results;
	}
}

class PostService {
	public function __construct(private DefaultMessageBus $messageBus) {}

	public function onPostPost(PostPost $command): void {
		currentTrace(self::class . '::' . get_class($command));

		$this->messageBus->dispatch(new PostPosted(postId: $command->id));
	}

	public function onDeletePost(DeletePost $command): void {
		currentTrace(self::class . '::' . get_class($command));

		$this->messageBus->dispatch(new PostDeleted(postId: $command->id));
	}
}

class EventStream {
	#[EventStoreLayerListener]
	public function onEvent(Event $event): void {
		currentTrace(self::class . '::' . get_class($event));
	}
}

class PostProjection {
	public function __construct(private DefaultMessageBus $messageBus) {}

	public function onPostPosted(PostPosted $event): void {
		currentTrace(self::class . '::' . get_class($event));

		$this->messageBus->dispatch(new StandardContentPosted(original: $event));
	}

	public function onPostDeleted(PostDeleted $event): void {
		currentTrace(self::class . '::' . get_class($event));

		$this->messageBus->dispatch(new StandardContentDeleted(original: $event));
	}

	public function onPostsForBlog(PostsForBlog $query): void {
		currentTrace(self::class . '::' . get_class($query));
		$query->results = [
			$query->blogId,
			strval(Identifier::createRandom()),
		];
	}
}

class StandardContentProjection {
	public function onStandardContentPosted(StandardContentPosted $event): void {
		currentTrace(self::class . '::' . get_class($event));
	}

	public function onStandardContentDeleted(StandardContentDeleted $event): void {
		currentTrace(self::class . '::' . get_class($event));
	}
}

class TimingService {
	// This will add a lot of noise if we don't turn it off!
	public bool $active = false;

	#[SecurityLayerListener(earlier: 1)]
	public function beforeSecurity(AuthorizableMessage $message): void {
		if ($this->active) currentTrace('beforeSecurity' . '::' . get_class($message));
	}
	#[SecurityLayerListener(later: 1)]
	public function afterSecurity(AuthorizableMessage $message): void {
		if ($this->active) currentTrace('afterSecurity' . '::' . get_class($message));
	}

	#[CheckMemoLayerListener(earlier: 1)]
	public function beforeCheckMemo(MemoizableQuery $message) {
		if ($this->active) currentTrace('beforeCheckMemo' . '::' . get_class($message));
	}
	#[CheckMemoLayerListener(later: 1)]
	public function afterCheckMemo(MemoizableQuery $message) {
		if ($this->active) currentTrace('afterCheckMemo' . '::' . get_class($message));
	}

	#[EventStoreLayerListener(earlier: 1)]
	public function beforeEventStore(Event $message) {
		if ($this->active) currentTrace('beforeEventStore' . '::' . get_class($message));
	}
	#[EventStoreLayerListener(later: 1)]
	public function afterEventStore(Event $message) {
		if ($this->active) currentTrace('afterEventStore' . '::' . get_class($message));
	}

	#[ExecutionLayerListener(earlier: 1)]
	public function beforeExecution(Hook $message) {
		if ($this->active) currentTrace('beforeExecution' . '::' . get_class($message));
	}
	#[ExecutionLayerListener(later: 1)]
	public function afterExecution(Hook $message) {
		if ($this->active) currentTrace('afterExecution' . '::' . get_class($message));
	}

	#[SaveMemoLayerListener(earlier: 1)]
	public function beforeSaveMemo(MemoizableQuery $message) {
		if ($this->active) currentTrace('beforeSaveMemo' . '::' . get_class($message));
	}
	#[SaveMemoLayerListener(later: 1)]
	public function afterSaveMemo(MemoizableQuery $message) {
		if ($this->active) currentTrace('afterSaveMemo' . '::' . get_class($message));
	}
}

final class EndToEndMessageTest extends TestCase {
	private ListenerRegistrar $provider;
	private DefaultMessageBus $dispatcher;
	private ServiceRegistrar $container;

	private function loadRegistrar(ListenerRegistrar $registrar) {
		$registrar->registerService(SecurityService::class);
		$registrar->registerService(PostService::class);
		$registrar->registerService(EventStream::class);
		$registrar->registerService(PostProjection::class);
		$registrar->registerService(StandardContentProjection::class);
		$registrar->registerService(MemoizeService::class);
		$registrar->registerService(TimingService::class);
	}

	public function setUp(): void {
		$this->container = new ServiceRegistrar(configuration: [
			SecurityService::class => ['messageBus' => DefaultMessageBus::class],
			MemoizeService::class => [],
			PostService::class => ['messageBus' => DefaultMessageBus::class],
			EventStream::class => [],
			PostProjection::class => ['messageBus' => DefaultMessageBus::class],
			StandardContentProjection::class => [],
			TimingService::class => [],
			DefaultMessageBus::class => fn() => new DefaultMessageBus(provider: $this->provider),
		]);

		$this->provider = new ListenerRegistrar(container: $this->container);
		$this->loadRegistrar($this->provider);

		$this->dispatcher = $this->container->get(DefaultMessageBus::class);
	}

	public function testAMessageWillBeStoppedIfNotAuthorized() {
		$badMessage = new PostPost(id: '5e52fb73-ba30-42c6-84a0-c6f4438c4ae4', allow: false);
		$this->dispatcher->dispatch($badMessage);

		$expectedTrace = [
			SecurityService::class . '::' . PostPost::class,
			SecurityService::class . '::' . IsUserAuthorized::class,
		];

		$this->assertTrue($badMessage->isPropagationStopped());
		$this->assertEquals($expectedTrace, currentTrace());
	}

	public function testAnEventWillPersistFirst() {
		$event = new PostPosted(postId: 'c643e215-d59b-4dac-ad81-a3cfe614b4a4');
		$this->dispatcher->dispatch($event);

		$expectedTrace = [
			EventStream::class . '::' . PostPosted::class,
			PostProjection::class . '::' . PostPosted::class,
			StandardContentProjection::class . '::' . StandardContentPosted::class,
		];

		$this->assertEquals($expectedTrace, currentTrace());
	}

	public function testASecondaryEventWillNotPersist() {
		$event = new StandardContentPosted(original: new PostPosted(postId: 'c643e215-d59b-4dac-ad81-a3cfe614b4a4'));
		$this->dispatcher->dispatch($event);

		$expectedTrace = [
			StandardContentProjection::class . '::' . StandardContentPosted::class,
		];
		$this->assertEquals($expectedTrace, currentTrace());
	}

	public function testMessagesCanDispatchOtherEvents() {
		$command = new PostPost(id: '5e52fb73-ba30-42c6-84a0-c6f4438c4ae4', allow: true);
		$this->dispatcher->dispatch($command);

		$expectedTrace = [
			SecurityService::class . '::' . PostPost::class,
			SecurityService::class . '::' . IsUserAuthorized::class,
			PostService::class . '::' . PostPost::class,
			EventStream::class . '::' . PostPosted::class,
			PostProjection::class . '::' . PostPosted::class,
			StandardContentProjection::class . '::' . StandardContentPosted::class,
		];

		$this->assertEquals($expectedTrace, currentTrace());
	}

	public function testMemoizableQueriesCanBeMemoized() {
		$query = new PostsForBlog(blogId: '9107f050-6715-47ef-a8f3-2fff24bbd573');
		$queryKey = $query->getMemoKey();
		$this->dispatcher->dispatch($query);

		$expectedTrace = [
			MemoizeService::class . '::' . PostsForBlog::class,
			PostProjection::class . '::' . PostsForBlog::class,
			MemoizeService::class . '::' . PostsForBlog::class,
		];

		$this->assertEquals($expectedTrace, currentTrace());

		$anotherQuery = new PostsForBlog(blogId: '9107f050-6715-47ef-a8f3-2fff24bbd573');
		$this->assertEquals($queryKey, $anotherQuery->getMemoKey());
		$this->dispatcher->dispatch($anotherQuery);

		$anotherExpectedTrace = [
			MemoizeService::class . '::' . PostsForBlog::class,
		];

		$this->assertEquals($anotherExpectedTrace, currentTrace());
		// The results contain a random value. If the memoization was correct, the results should
		// be the same...
		$this->assertEquals($query->results, $anotherQuery->results);
	}

	public function testTimingAttributesCanBeSetCorrectly() {
		$this->container->get(TimingService::class)->active = true;
		$command = new PostPost(id: '5e52fb73-ba30-42c6-84a0-c6f4438c4ae4', allow: true);
		$this->dispatcher->dispatch($command);
		$this->container->get(TimingService::class)->active = false;

		$expectedTrace = [
			'beforeSecurity' . '::' . PostPost::class,
			SecurityService::class . '::' . PostPost::class,
			SecurityService::class . '::' . IsUserAuthorized::class,
			'afterSecurity' . '::' . PostPost::class,
			PostService::class . '::' . PostPost::class,
			'beforeEventStore' . '::' . PostPosted::class,
			EventStream::class . '::' . PostPosted::class,
			'afterEventStore' . '::' . PostPosted::class,
			PostProjection::class . '::' . PostPosted::class,
			'beforeExecution' . '::' . StandardContentPosted::class,
			StandardContentProjection::class . '::' . StandardContentPosted::class,
			'afterExecution' . '::' . StandardContentPosted::class,
		];

		$this->assertEquals($expectedTrace, currentTrace());
	}
}
