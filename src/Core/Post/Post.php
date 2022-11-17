<?php

namespace Smolblog\Core\Post;

use DateTime;
use Smolblog\Framework\Entity;

/**
 * Represents a blog post.
 */
class Post extends Entity {
	/**
	 * Create the Post object
	 *
	 * @param integer    $user_id   ID of the Post's author.
	 * @param DateTime   $timestamp Time and Date for post: time last saved if draft, time published if not.
	 * @param string     $slug      URL-friendly identifier for the post.
	 * @param integer    $id        Unique ID of the post in the blog.
	 * @param string     $title     Title of the post.
	 * @param Block[]    $content   Post's content as an ordered array of blocks.
	 * @param PostStatus $status    Status of the post.
	 */
	public function __construct(
		public readonly int $user_id,
		public readonly DateTime $timestamp,
		public readonly string $slug,
		?int $id = null,
		public readonly ?string $title = null,
		public readonly array $content = [],
		public readonly PostStatus $status = PostStatus::Draft,
	) {
		parent::__construct(id: $id ?? 0);
	}
}
