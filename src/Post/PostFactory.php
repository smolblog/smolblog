<?php

namespace Smolblog\Core\Post;

use Smolblog\Core\Model\ModelHelper;

/**
 * Object for making ConnectionCredential models.
 */
class PostFactory {
	/**
	 * Create the factory
	 *
	 * @param ModelHelper $helper Helper to use when creating Posts.
	 */
	public function __construct(private ModelHelper $helper) {
	}

	/**
	 * Get a Post model for the given ID. Returns null if post is not found.
	 *
	 * @param integer $postId ID for the post.
	 * @return ?Post Post model if found; null if not.
	 */
	public function findById(int $postId): ?Post {
		$post = new Post(withHelper: $this->helper);
		if ($post->loadWithId(['id' => $postId])) {
			return $post;
		}
		return null;
	}

	/**
	 * Create a new empty Post model.
	 *
	 * @return Post New post model.
	 */
	public function newPost(): Post {
		return new Post(withHelper: $this->helper);
	}
}
