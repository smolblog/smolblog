<?php

namespace Smolblog\Core\ContentV1;

use DateTimeImmutable;
use Smolblog\Test\TestCase;
use Smolblog\Core\ContentV1\Commands\ChangeContentVisibility;
use Smolblog\Core\ContentV1\Commands\EditContentBaseAttributes;
use Smolblog\Core\ContentV1\Events\ContentBaseAttributeEdited;
use Smolblog\Core\ContentV1\Events\ContentVisibilityChanged;
use Smolblog\Core\ContentV1\Queries\AdaptableContentQuery;
use Smolblog\Core\ContentV1\Queries\BaseContentById;
use Smolblog\Foundation\Value\Traits\Message;
use Smolblog\Foundation\Service\Messaging\MessageBus;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Fields\RandomIdentifier;
use Smolblog\Test\Kits\EventComparisonTestKit;

final class ContentServiceTest extends TestCase {
	use EventComparisonTestKit;

	public function testItHandlesTheEditContentBaseAttributesCommand() {
		$command = new EditContentBaseAttributes(
			siteId: $this->randomId(),
			userId: $this->randomId(),
			contentId: $this->randomId(),
			publishTimestamp: new DateTimeImmutable(),
			authorId: $this->randomId(),
		);
		$expectedEvent = new ContentBaseAttributeEdited(
			contentId: $command->contentId,
			userId: $command->userId,
			siteId: $command->siteId,
			publishTimestamp: $command->publishTimestamp,
			authorId: $command->authorId,
		);

		$messageBus = $this->createMock(MessageBus::class);
		$messageBus->expects($this->once())->method('dispatch')->with($this->eventEquivalentTo($expectedEvent));

		$service = new ContentService(bus: $messageBus, registry: $this->createStub(ContentTypeRegistry::class));
		$service->onEditContentBaseAttributes($command);
	}

	public function testItHandlesAdaptableContentQueriesWithNoContentId() {
		$query = $this->createStub(AdaptableContentQuery::class);
		$service = new ContentService(
			bus: $this->createStub(MessageBus::class),
			registry: $this->createStub(ContentTypeRegistry::class),
		);

		$service->onAdaptableContentQuery($query);
		$this->assertNull($query->results());
	}

	public function testItHandlesAdaptableContentQueriesWithValidContent() {
		$content = $this->createStub(Content::class);

		$baseContentQuery = new class(
			siteId: Identifier::fromString('c4f086d8-ab63-4c99-b14d-c74b7a45e767'),
			userId: Identifier::fromString('8b837d64-6cb1-444c-81e1-afdbd3018e7d'),
			contentId: Identifier::fromString('b220c92c-af5f-48ba-8603-958af41e9160'),
		) extends BaseContentById {};

		$query = $this->createMock(AdaptableContentQuery::class);
		$query->method('getContentType')->willReturn('spud');
		$query->method('getUserId')->willReturn($baseContentQuery->userId);
		$query->method('getSiteId')->willReturn($baseContentQuery->siteId);
		$query->method('getContentId')->willReturn($baseContentQuery->contentId);
		$query->expects($this->once())->method('setResults')->with($this->equalTo($content));

		$registry = $this->createMock(ContentTypeRegistry::class);
		$registry->expects($this->once())->method('singleItemQueryFor')->
			with($this->equalTo('spud'))->willReturn(get_class($baseContentQuery));

		$bus = $this->createMock(MessageBus::class);
		$bus->expects($this->once())->method('fetch')->with($this->equalTo($baseContentQuery))->willReturn($content);

		$service = new ContentService(bus: $bus, registry: $registry);
		$service->onAdaptableContentQuery($query);
	}
}
