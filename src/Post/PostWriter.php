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
}
