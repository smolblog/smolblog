<?php

namespace Smolblog\Core\Content;

use Smolblog\Test\TestCase;

final class MediaTest extends TestCase {
	public function testItCanBeInstantiated() {
		$this->assertInstanceOf(
			Media::class,
			new Media(
				url: '//cdn.smol.blog/astley.jpg',
				descriptiveText: 'A blonde British singer in a trenchcoat.',
				attributes: [],
			)
			);
	}
}
