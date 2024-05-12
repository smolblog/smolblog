<?php

namespace Smolblog\Core\Content;

use PHPUnit\Framework\Attributes\CoversClass;
use Smolblog\Core\Content\Events\ContentCreated;
use Smolblog\Core\Content\Events\ContentUpdated;
use Smolblog\Foundation\Exceptions\InvalidEventPlacement;
use Smolblog\Test\Kits\ContentTestKit;
use Smolblog\Test\Kits\ServiceTestKit;
use Smolblog\Test\TestCase;

#[CoversClass(ContentValidationService::class)]
final class ContentValidationServiceTest extends TestCase {
	use ServiceTestKit;
	use ContentTestKit;

	private ContentValidationService $service;

	protected function setUp(): void {
		$this->service = $this->setUpService(ContentValidationService::class);
	}

	public function testItStopsACreateCommandWithAnExistingId() {
		$this->expectException(InvalidEventPlacement::class);
		$content = $this->sampleContent();
		$this->deps->repo->expects($this->once())->method('contentExists')->with($content->id)->willReturn(true);

		$event = new ContentCreated(userId: $this->randomId(), content: $content);
		$this->service->validateCreate(event: $event);

		$this->assertTrue($event->isPropagationStopped());
	}

	public function testItAllowsACreateCommandWithoutAnExistingId() {
		$content = $this->sampleContent();
		$this->deps->repo->expects($this->once())->method('contentExists')->with($content->id)->willReturn(false);

		$event = new ContentCreated(userId: $this->randomId(), content: $content);
		$this->service->validateCreate(event: $event);

		$this->assertFalse($event->isPropagationStopped());
	}

	public function testItStopsAnEditCommandWithoutAnExistingId() {
		$this->expectException(InvalidEventPlacement::class);
		$content = $this->sampleContent();
		$this->deps->repo->expects($this->once())->method('contentExists')->with($content->id)->willReturn(false);

		$event = new ContentUpdated(userId: $this->randomId(), content: $content);
		$this->service->validateUpdate(event: $event);

		$this->assertTrue($event->isPropagationStopped());
	}

	public function testItAllowsAnEditCommandWithAnExistingId() {
		$content = $this->sampleContent();
		$this->deps->repo->expects($this->once())->method('contentExists')->with($content->id)->willReturn(true);

		$event = new ContentUpdated(userId: $this->randomId(), content: $content);
		$this->service->validateUpdate(event: $event);

		$this->assertFalse($event->isPropagationStopped());
	}
}
