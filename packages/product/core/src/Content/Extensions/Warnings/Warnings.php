<?php

namespace Smolblog\Core\Content\Extensions\Warnings;

use Smolblog\Core\Content\Entities\ContentExtension;
use Smolblog\Foundation\Value\Attributes\ArrayType;

/**
 * Tag content with content warnings.
 *
 * Also known as "trigger warnings", these are simple tags that can allow readers to choose to engage with content
 * that may otherwise cause unwanted and/or involuntary psychological responses.
 */
readonly class Warnings extends ContentExtension {
	/**
	 * Construct the extension.
	 *
	 * @param ContentWarning[] $warnings Applicable content warnings.
	 */
	public function __construct(#[ArrayType(ContentWarning::class)] public array $warnings) {
	}
}
