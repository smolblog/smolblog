<?php

namespace Smolblog\Core\Post;

use DateTime;
use PHPUnit\Framework\TestCase;

final class PostTest extends TestCase {
	public function testItCanBeCreatedWithMinimalData() {
		$this->assertInstanceOf(
			Post::class,
			new Post(
				id: 5,
				user_id: 5,
				timestamp: new DateTime(),
				slug: 'test',
			),
		);
	}

	public function testItCanBeCreatedWithAllParameters() {
		$this->assertInstanceOf(
			Post::class,
			new Post(
				id: 5,
				user_id: 5,
				timestamp: new DateTime(),
				slug: 'test',
				title: 'Test Post',
				content: '<p>Hello!</p>',
				status: PostStatus::Published,
			),
		);
	}
}
