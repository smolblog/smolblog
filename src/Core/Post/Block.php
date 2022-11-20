<?php

namespace Smolblog\Core\Post;

use Smolblog\Framework\Entity;

/**
 * A unit of content for a Post. Just an empty Entity class because the only
 * common piece is an ID. Every Block's data could look different, and parsing
 * that data into another format is another class' job.
 */
abstract class Block extends Entity {
}
