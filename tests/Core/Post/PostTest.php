<?php

namespace Smolblog\Core\Post;

use DateTime;
use PHPUnit\Framework\TestCase;
use Smolblog\Core\Post\Blocks\ParagraphBlock;
use Smolblog\Framework\Identifier;

final class PostTest extends TestCase {
	public function testItCanBeCreatedWithMinimalData() {
		$this->assertInstanceOf(
			Post::class,
			new Post(
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
				id: Identifier::createRandom(),
				user_id: 5,
				timestamp: new DateTime(),
				slug: 'test',
				title: 'Test Post',
				content: [
					new ParagraphBlock(content: 'Hello!')
				],
				status: PostStatus::Published,
			),
		);
	}
}
