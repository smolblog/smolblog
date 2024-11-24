<?php

namespace Smolblog\IndieWeb;

use DateTimeImmutable;
use Smolblog\Core\Content\Entities\Content;
use Smolblog\Core\Content\Fields\Markdown;
use Smolblog\Core\Content\Types\Article\Article;
use Smolblog\Foundation\Value\Fields\DateTimeField;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Fields\Url;
use Smolblog\Test\TestCase;

final class MicroformatsConverterTest extends TestCase {
	public function testBasicContentCanConvertToMicroformats() {
		$content = new Content(
			body: new Article(title: 'Test', text: new Markdown('Hello world!')),
			siteId: Identifier::fromString('3224ba48-9d3c-4ad4-9bb6-d0337c05a257'),
			userId: Identifier::fromString('627ac048-c307-4b8d-bb93-f32cee5f5cb2'),
			canonicalUrl: new Url('https://test.smol.blog/thing/one'),
			publishTimestamp: new DateTimeField('2022-02-02 22:22:22 +0:00'),
			id: Identifier::fromString('edef89d0-d0d6-46e9-9fb0-26cdf4ad956a'),
		);

		$expected = [
			'name' => ['Test'],
			'content' => [['html' => '<p>Hello world!</p>']],
			'published' => ['2022-02-02T22:22:22+00:00'],
			'url' => ['https://test.smol.blog/thing/one'],
			'uid' => ['edef89d0-d0d6-46e9-9fb0-26cdf4ad956a'],
		];
		$actual = (new MicroformatsConverter())->entryPropertiesFromContent($content);

		$this->assertEquals($expected, $actual);
	}
}
