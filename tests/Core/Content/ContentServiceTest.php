<?php

namespace Smolblog\Core\Content;

use DateTimeImmutable;
use Smolblog\Test\TestCase;
use Smolblog\Core\Content\Commands\ChangeContentVisibility;
use Smolblog\Core\Content\Commands\EditContentBaseAttributes;
use Smolblog\Core\Content\Events\ContentBaseAttributeEdited;
use Smolblog\Core\Content\Events\ContentVisibilityChanged;
use Smolblog\Core\Content\Queries\BaseContentById;
use Smolblog\Framework\Messages\Message;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Test\EventComparisonTestKit;

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

		$service = new ContentService(bus: $messageBus);
		$service->onEditContentBaseAttributes($command);
	}

	// public function testItHandlesBaseContentByIdQueries() {
	// 	$contentMock = $this->createStub(Content::class);

	// 	$queryMock = $this->createMock(BaseContentById::class);
	// 	$queryMock->method('getContent')->willReturn($contentMock);

	// 	$service = new ContentService(bus: $this->createStub(MessageBus::class));
	// 	$service->onBaseContentById($queryMock);

	// 	$this->assertEquals($contentMock, $queryMock->results());
	// }
}
