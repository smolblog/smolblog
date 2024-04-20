<?php

namespace Smolblog\Core\ContentV1\Types\Reblog;

/**
 * Servie to handle getting information about an external URL.
 *
 * @deprecated Migrate to Smolblog\Core\Content
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
