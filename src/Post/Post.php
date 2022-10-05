<?php

namespace Smolblog\Core\Post;

use DateTime;
use Smolblog\Core\Entity\Entity;

/**
 * Represents a blog post.
 */
class Post extends Entity {
	/**
	 * Create the Post object
	 *
	 * @param integer    $id        Unique ID of the post in the blog.
	 * @param integer    $user_id   ID of the Post's author.
	 * @param DateTime   $timestamp Time and Date for post: time last saved if draft, time published if not.
	 * @param string     $slug      URL-friendly identifier for the post.
	 * @param string     $title     Title of the post.
	 * @param string     $content   HTML content of the post.
	 * @param PostStatus $status    Status of the post.
	 */
	public function __construct(
		int $id,
		public readonly int $user_id,
		public readonly DateTime $timestamp,
		public readonly string $slug,
		public readonly ?string $title = null,
		public readonly string $content = '',
		public readonly PostStatus $status = PostStatus::Draft,
	) {
		parent::__construct(id: $id);
	}
}
