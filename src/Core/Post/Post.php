<?php

namespace Smolblog\Core\Post;

use DateTimeImmutable;
use DateTimeInterface;
use Smolblog\Framework\Entity;
use Smolblog\Framework\Identifier;

/**
 * Represents a blog post.
 */
class Post extends Entity {
	/**
	 * Create the Post object
	 *
	 * @param integer           $user_id   ID of the Post's author.
	 * @param DateTimeInterface $timestamp Time and Date for post: time last saved if draft, time published if not.
	 * @param string            $slug      URL-friendly identifier for the post.
	 * @param Identifier        $id        Unique ID of the post in the blog. Creates date-based ID if not provided.
	 * @param string            $title     Title of the post.
	 * @param Block[]           $content   Post's content as an ordered array of blocks.
	 * @param PostStatus        $status    Status of the post.
	 */
	public function __construct(
		public readonly int $user_id,
		public readonly DateTimeInterface $timestamp,
		public readonly string $slug,
		Identifier $id = null,
		public readonly ?string $title = null,
		public readonly array $content = [],
		public readonly PostStatus $status = PostStatus::Draft,
	) {
		parent::__construct(id: $id ?? Identifier::createFromDate());
	}

	/**
	 * Make sure blocks are recursively serialized.
	 *
	 * @return array
	 */
	public function toArray(): array {
		$arr = parent::toArray();
		$arr['timestamp'] = $this->timestamp->format(DateTimeInterface::RFC3339_EXTENDED);
		$arr['content'] = array_map(fn($block) => $block->toArray(), $this->content);

		return $arr;
	}

	/**
	 * Create a Post from an associative array.
	 *
	 * Used in tandem with JSON parsing.
	 *
	 * @param array $data Associative array.
	 * @return static
	 */
	public static function fromArray(array $data): static {
		$dataWithObjects = [
			...$data,
			'timestamp' => new DateTimeImmutable($data['timestamp']),
			'content' => array_map(fn($blockArray) => Block::fromTypedArray($blockArray), $data['content']),
			'status' => PostStatus::from($data['status']),
		];
		return parent::fromArray($dataWithObjects);
	}
}
