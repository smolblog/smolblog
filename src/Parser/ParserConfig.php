<?php

namespace Smolblog\Core\Parser;

/**
 * Configuration for an Parser
 */
class ParserConfig {
	/**
	 * Construct the configuration
	 *
	 * @param string $slug Identifier for the Parser.
	 */
	public function __construct(
		public readonly string $slug
	) {
	}
}
