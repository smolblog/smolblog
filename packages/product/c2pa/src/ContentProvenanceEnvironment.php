<?php

namespace Smolblog\ContentProvenance;

/**
 * Environment information needed by the ContentProvenance module.
 */
interface ContentProvenanceEnvironment {
	/**
	 * Full path to the c2patool executable.
	 *
	 * @return string
	 */
	public function getPathToC2patool(): string;
}
