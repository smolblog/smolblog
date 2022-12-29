<?php

namespace Smolblog\Framework\Exploration;

use Crell\Tukio\Dispatcher;
use PHPUnit\Framework\TestCase;
use Crell\Tukio\OrderedListenerProvider;
use JsonSerializable;
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
use Smolblog\Framework\Objects\SerializableKit;
use Smolblog\Framework\MessageBus\Attributes\SecurityLayerListener;
use Smolblog\Framework\MessageBus\Attributes\CheckMemoLayerListener;
use Smolblog\Framework\MessageBus\Attributes\EventStoreLayerListener;
use Smolblog\Framework\MessageBus\Attributes\SaveMemoLayerListener;

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

class PostsForBlog extends Query implements MemoizableQuery, JsonSerializable {
	use MemoizableQueryKit;
	use SerializableKit;

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
	public function __construct(private Dispatcher $messageBus) {}

	#[SecurityLayerListener]
	public function onAuthorizableMessage(AuthorizableMessage $event): void {
		currentTrace(self::class . '::' . get_class($event));

		$securityQuery = $event->getAuthorizationQuery();
		$this->messageBus->dispatch($securityQuery);

		if (!$securityQuery->results) {
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
	public function __construct(private Dispatcher $messageBus) {}

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
	public function __construct(private Dispatcher $messageBus) {}

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

final class MessageBusTest extends TestCase {
	private OrderedListenerProvider $provider;
	private Dispatcher $dispatcher;
	private Container $container;

	public function setUp(): void {
		$this->container = new Container();
		$this->container->addShared(SecurityService::class)->addArgument(Dispatcher::class);
		$this->container->addShared(MemoizeService::class);
		$this->container->addShared(PostService::class)->addArgument(Dispatcher::class);
		$this->container->addShared(EventStream::class);
		$this->container->addShared(PostProjection::class)->addArgument(Dispatcher::class);
		$this->container->addShared(StandardContentProjection::class);
		$this->container->addShared(Dispatcher::class, fn() => new Dispatcher(provider: $this->provider));

		$this->provider = new OrderedListenerProvider(container: $this->container);
		$this->provider->addSubscriber(SecurityService::class, SecurityService::class);
		$this->provider->addSubscriber(PostService::class, PostService::class);
		$this->provider->addSubscriber(EventStream::class, EventStream::class);
		$this->provider->addSubscriber(PostProjection::class, PostProjection::class);
		$this->provider->addSubscriber(StandardContentProjection::class, StandardContentProjection::class);
		$this->provider->addSubscriber(MemoizeService::class, MemoizeService::class);

		$this->dispatcher = $this->container->get(Dispatcher::class);
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
}
