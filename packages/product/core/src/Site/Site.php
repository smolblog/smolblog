<?php

namespace Smolblog\Core\Site;

use Smolblog\Framework\Objects\Entity;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Represents a site with its own URL, posts, etc.
 */
class Site extends Entity {
	/**
	 * Unique subdomain or subdirectory identifier for this site.
	 *
	 * @var string
	 */
	public readonly string $handle;

	/**
	 * Site title as shown in lists and other admin screens.
	 *
	 * @var string
	 */
	public readonly string $displayName;

	/**
	 * Base URL for the site.
	 *
	 * @var string
	 */
	public readonly string $baseUrl;

	/**
	 * Optional description for the site. Used primariliy in syndication.
	 *
	 * @var string|null
	 */
	public readonly ?string $description;

	/**
	 * Public key tied to the site. Used to verify messages from this site.
	 *
	 * @var string
	 */
	public readonly string $publicKey;

	/**
	 * Undocumented function
	 *
	 * @param Identifier $id          ID for this site.
	 * @param string     $handle      Unique subdomain or subdirectory identifier for this site.
	 * @param string     $displayName Site title as shown in lists and other admin screens.
	 * @param string     $baseUrl     Base URL for the site.
	 * @param string     $publicKey   Public key tied to the site.
	 * @param string     $description Optional description for the site.
	 */
	public function __construct(
		Identifier $id,
		string $handle,
		string $displayName,
		string $baseUrl,
		string $publicKey,
		?string $description = null,
	) {
		$this->handle = $handle;
		$this->displayName = $displayName;
		$this->baseUrl = $baseUrl;
		$this->description = $description;
		$this->publicKey = $publicKey;
		parent::__construct(id: $id);
	}
}
