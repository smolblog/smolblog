<?php

namespace Smolblog\Core\Content\Queries;

use DateTimeImmutable;
use Smolblog\Core\Content\Content;
use Smolblog\Core\Content\ContentBuilder;
use Smolblog\Core\Content\ContentBuilderKit;
use Smolblog\Test\TestCase;
use Smolblog\Core\Content\ContentExtension;
use Smolblog\Core\Content\ContentType;
use Smolblog\Core\Content\ContentVisibility;
use Smolblog\Core\Content\GenericContent;
use Smolblog\Core\Content\InvalidContentException;
use Smolblog\Framework\Messages\Query;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Framework\Objects\SerializableKit;

final class ContentBuilderKitTestExtension implements ContentExtension {
	use SerializableKit;
	public function __construct(public readonly string $test) {}
}

final class ContentBuilderKitTest extends TestCase {
	private $builder;
	private ContentType $type;
	private ContentExtension $oneExtension;
	private ContentExtension $twoExtension;

	public function setUp(): void {
		$this->builder = new class() implements ContentBuilder {
			use ContentBuilderKit;
			public function getCurrentProps() { return $this->contentProps; }
			public function getContentId(): Identifier { return $this->contentProps['id'] ?? null; }
		};
	}

	public function testItThrowsAnErrorIfTheContentIsIncomplete() {
		$this->expectException(InvalidContentException::class);

		$this->builder->getContent();
	}

	public function testItCanBuildContentOverTime() {
		$this->builder->setContentType($this->createStub(ContentType::class));
		$this->builder->addContentExtension($this->createStub(ContentExtension::class));
		$this->builder->setContentProperty(
			id: $this->randomId(),
			siteId: $this->randomId(),
			authorId: $this->randomId(),
			permalink: '/thing/another',
			publishTimestamp: new DateTimeImmutable(),
			visibility: ContentVisibility::Published,
		);

		$content = $this->builder->getContent();
		$this->assertInstanceOf(Content::class, $content);
	}

	public function testPropertiesCanBeSetOverTime() {
		$props1 = [
			'id' => $this->randomId(),
			'siteId' => $this->randomId(),
			'authorId' => $this->randomId(),
			'permalink' => '/wrong/one',
		];
		$this->builder->setContentProperty(...$props1);

		$this->assertEquals($props1, $this->builder->getCurrentProps());

		$testDate = new DateTimeImmutable();
		$this->builder->setContentProperty(
			permalink: '/thing/another',
			publishTimestamp: $testDate,
			visibility: ContentVisibility::Published,
		);

		$this->assertEquals([
			'id' => $props1['id'],
			'siteId' => $props1['siteId'],
			'authorId' => $props1['authorId'],
			'permalink' => '/thing/another',
			'publishTimestamp' => $testDate,
			'visibility' => ContentVisibility::Published,
		], $this->builder->getCurrentProps());
	}

	public function testExtensionsAreAddedAndOverwrittenByName() {
		$ext1 = new class('one') implements ContentExtension {
			use SerializableKit;
			public function __construct(public readonly string $itStartsWith) {}
		};
		$ext2 = new class('two') implements ContentExtension {
			use SerializableKit;
			public function __construct(public readonly string $itTakes) {}
		};

		$this->builder->addContentExtension($ext1);
		$this->builder->addContentExtension($ext2);
		$this->builder->addContentExtension(new ContentBuilderKitTestExtension(test: 'Hello'));

		$this->assertEquals(3, count($this->builder->getCurrentProps()['extensions']));
		$this->assertEquals(
			'Hello',
			$this->builder->getCurrentProps()['extensions'][ContentBuilderKitTestExtension::class]->test
		);

		$this->builder->addContentExtension(new ContentBuilderKitTestExtension(test: 'World'));

		$this->assertEquals(3, count($this->builder->getCurrentProps()['extensions']));
		$this->assertEquals(
			'World',
			$this->builder->getCurrentProps()['extensions'][ContentBuilderKitTestExtension::class]->test
		);
	}
}
