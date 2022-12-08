<?php

namespace Smolblog\Core\Post;

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
}
