<?php

namespace Smolblog\Markdown;

/**
 * Interface for classes that can provide embed HTML for a given URL.
 */
interface EmbedProvider {
	/**
	 * Provide the HTML code to embed the given URL in a web page.
	 *
	 * This should be a "safe" function in that, if the code cannot be retrieved for any reason (including exceptions,
	 * errors, blocked connections), it should return null.
	 *
	 * Likewise, if a consumer receives null from this function, it should have a backup strategy available.
	 *
	 * @param string $url URL to embed.
	 * @return string|null Embed code; null if code cannot be provided.
	 */
	public function getEmbedCodeFor(string $url): ?string;
}
