<?php

namespace Smolblog\Core\Content\Types\Reblog;

/**
 * Servie to handle getting information about an external URL.
 */
interface ExternalContentService {
	/**
	 * Fetch the external content's info.
	 *
	 * @param string $url URL to fetch and embed.
	 * @return ExternalContentInfo
	 */
	public function getExternalContentInfo(string $url): ExternalContentInfo;
}
