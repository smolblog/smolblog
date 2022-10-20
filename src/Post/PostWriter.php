<?php

namespace Smolblog\Core\Post;

use Smolblog\Core\Entity\Writer;

/**
 * Object for saving Posts to the repository.
 */
interface PostWriter extends Writer {
	/**
	 * Save the given Post to the repository.
	 *
	 * @param Post $post Post object to save.
	 * @return void
	 */
	public function save(Post $post): void;

	/**
	 * Save the given Posts to the repository.
	 *
	 * @param Post[] $posts Post objects to save.
	 * @return void
	 */
	public function saveMany(array $posts): void;
}
