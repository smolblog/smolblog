<?php

namespace Smolblog\Core\Content\Events;

use PHPUnit\Framework\Attributes\CoversClass;
use Smolblog\Core\Content;
use Smolblog\Core\Content\Type\ContentType;
use Smolblog\Foundation\Value\Fields\DateTimeField;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Messages\DomainEvent;
use Smolblog\Test\Kits\ContentTestKit;
use Smolblog\Test\TestCase;

final readonly class ExampleBaseContentEvent extends BaseContentEvent {}

#[CoversClass(BaseContentEvent::class)]
final class BaseContentEventTest extends TestCase {
	use ContentTestKit;

	private Content $content;

	protected function setUp(): void {
		$this->content = $this->sampleContent();
	}

	public function testItWillCreateWithDefaultIdAndTimestamp() {
		$event = new ExampleBaseContentEvent(content: $this->content, userId: $this->randomId());

		$this->assertInstanceOf(DomainEvent::class, $event);
		$this->assertInstanceOf(Identifier::class, $event->id);
		$this->assertInstanceOf(DateTimeField::class, $event->timestamp);
	}

	public function testItWillSerializeAndDeserializeCorrectly() {
		$event = new ExampleBaseContentEvent(
			content: $this->content,
			userId: $this->randomId(),
			id: $this->randomId(),
			timestamp: new DateTimeField('2022-02-22 22:22:22.222'),
		);
		$array = [
			'type' => ExampleBaseContentEvent::class,
			'content' => $this->content->serializeValue(),
			'id' => strval($event->id),
			'timestamp' => strval($event->timestamp),
			'userId' => strval($event->userId),
			'aggregateId' => strval($this->content->siteId),
			'entityId' => strval($this->content->id),
		];

		$this->assertEquals($event, DomainEvent::deserializeValue($array));
		$this->assertEquals($array, $event->serializeValue());
	}
}
