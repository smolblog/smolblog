<?php

namespace Smolblog\IndieWeb;

use DateTimeImmutable;
use Smolblog\Core\Content\Content;
use Smolblog\Core\Content\ContentVisibility;
use Smolblog\Core\Content\GenericContent;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Test\TestCase;

final class MicroformatsConverterTest extends TestCase {
	public function testBasicContentCanConvertToMicroformats() {
		$content = new Content(
			type: new GenericContent(title: 'Test', body: '<p>Hello world!</p>'),
			siteId: Identifier::fromString('3224ba48-9d3c-4ad4-9bb6-d0337c05a257'),
			authorId: Identifier::fromString('627ac048-c307-4b8d-bb93-f32cee5f5cb2'),
			permalink: '/thing/one',
			publishTimestamp: new DateTimeImmutable('2022-02-02 22:22:22 +0:00'),
			visibility: ContentVisibility::Published,
			id: Identifier::fromString('edef89d0-d0d6-46e9-9fb0-26cdf4ad956a'),
		);

		$expected = [
			'name' => ['Test'],
			'content' => [['html' => '<p>Hello world!</p>']],
			'published' => ['+2022-02-02T22:22:22+00:00'],
			'url' => ['/thing/one'],
			'uid' => ['edef89d0-d0d6-46e9-9fb0-26cdf4ad956a'],
		];
		$actual = (new MicroformatsConverter())->entryPropertiesFromContent($content);

		$this->assertEquals($expected, $actual);
	}
}
