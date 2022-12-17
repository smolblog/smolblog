<?php

namespace Smolblog\Core\Post;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;
use Smolblog\Core\Post\Blocks\ImageBlock;
use Smolblog\Core\Post\Blocks\ParagraphBlock;
use Smolblog\Core\Post\Blocks\QuoteBlock;
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

	public function testItCanBeJsonSerializedWithBlocks() {
		$post = new Post(
			id: Identifier::fromString('3467abac-b52b-486a-b4b3-ae033d2d05ed'),
			user_id: 5,
			timestamp: new DateTimeImmutable('2022-02-02T02:22:22.000+00:00'),
			slug: 'test',
			title: 'Test Post',
			content: [
				new ParagraphBlock(
					id: Identifier::fromString('2fcc2802-3952-4d6d-8f9e-f694cdc27d13'),
					content: 'The fish was delish, and it made quite a dish.'
				),
				new ImageBlock(
					id: Identifier::fromString('f7752834-1246-48cc-812e-4e8f188323cb'),
					media: new Media(
						id: Identifier::fromString('dab72efb-235a-4294-be06-ecacbecfa17f'),
						url: '/art/151.jpg',
						descriptiveText: 'An image of the pokémon mew',
						attributes: []
					)
				),
				new QuoteBlock(
					id: Identifier::fromString('2fcc2802-3952-4d6d-8f9e-f694cdc27d13'),
					content: 'What is this? Did the quadratic formula explode?',
					citation: 'Strong Bad, sbemail 118'
				),
			]
		);

		$json = json_encode($post);
		$this->assertJsonStringEqualsJsonFile(__DIR__ . '/post.json', $json);
	}

	public function testItCanBeDeserializedFromJson() {
		$post = Post::jsonDeserialize(file_get_contents(__DIR__ . '/post.json'));

		$this->assertInstanceOf(Post::class, $post);
		$this->assertEquals('3467abac-b52b-486a-b4b3-ae033d2d05ed', strval($post->id));
		$this->assertEquals('Wednesday, 02-Feb-2022 02:22:22 GMT+0000', $post->timestamp->format(DateTimeInterface::COOKIE));
		$this->assertIsArray($post->content);
		$this->assertInstanceOf(ParagraphBlock::class, $post->content[0]);
		$this->assertEquals('2fcc2802-3952-4d6d-8f9e-f694cdc27d13', strval($post->content[0]->id));
		$this->assertInstanceOf(ImageBlock::class, $post->content[1]);
		$this->assertEquals('f7752834-1246-48cc-812e-4e8f188323cb', strval($post->content[1]->id));
		$this->assertEquals('dab72efb-235a-4294-be06-ecacbecfa17f', strval($post->content[1]->media->id));
		$this->assertInstanceOf(QuoteBlock::class, $post->content[2]);
		$this->assertEquals('2fcc2802-3952-4d6d-8f9e-f694cdc27d13', strval($post->content[2]->id));
	}

	public function testItCanBeModifiedByCopying() {
		$base = new Post(
			id: Identifier::createRandom(),
			user_id: 5,
			timestamp: new DateTime(),
			slug: 'test',
			title: 'Test Post',
			content: [
				new ParagraphBlock(content: 'Hello!')
			],
			status: PostStatus::Published,
		);
		$modified = $base->newWith(title: 'Modified post', content: [ new ParagraphBlock(content: 'Goodbye...') ]);

		$this->assertEquals(strval($base), strval($modified));
		$this->assertNotEquals($base->title, $modified->title);
		$this->assertInstanceOf(ParagraphBlock::class, $modified->content[0]);
		$this->assertEquals('Hello!', $base->content[0]->content);
		$this->assertEquals('Goodbye...', $modified->content[0]->content);
	}
}
