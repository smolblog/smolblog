<?php

namespace Smolblog\Mock;

use Smolblog\Core\Site\SiteUserLink;
use Smolblog\Core\Site\UserHasPermissionForSite;
use Smolblog\Framework\Messages\Listener;
use Smolblog\Framework\Objects\Identifier;

class SecurityService implements Listener {
	public const SITE1 = '5d1b0f16-ff8d-4650-af86-bdcbab459715';
	public const SITE1ADMIN = 'bb4b139b-7301-4f81-8b26-6489a407ce95';
	public const SITE1AUTHOR = '41441844-5a8d-42a1-a878-df678b5e9f85';

	public const SITE2 = '5ebc7836-441f-4c94-aabe-0c6562aff792';
	public const SITE2ADMIN = '8c1d7565-8d29-46c4-8dfd-69828d2cf68f';
	public const SITE2AUTHOR = '15880739-59bd-4bda-937f-c6363ff8f447';

	private array $links;

	public function __construct() {
		$links = [];
		$links[] = new SiteUserLink(
			siteId: Identifier::fromString(self::SITE1),
			userId: Identifier::fromString(self::SITE1ADMIN),
			isAuthor: true, isAdmin: true,
		);
		$links[] = new SiteUserLink(
			siteId: Identifier::fromString(self::SITE1),
			userId: Identifier::fromString(self::SITE1AUTHOR),
			isAuthor: true, isAdmin: false,
		);
		$links[] = new SiteUserLink(
			siteId: Identifier::fromString(self::SITE2),
			userId: Identifier::fromString(self::SITE2ADMIN),
			isAuthor: true, isAdmin: true,
		);
		$links[] = new SiteUserLink(
			siteId: Identifier::fromString(self::SITE2),
			userId: Identifier::fromString(self::SITE2AUTHOR),
			isAuthor: true, isAdmin: false,
		);

		foreach($links as $link) { $this->links[$link->id->toString()] = $link; }
	}

	private function getLink(Identifier|string $siteId, Identifier|string $userId): ?SiteUserLink {
		return $this->links[SiteUserLink::buildId(siteId: $siteId, userId: $userId)->toString()] ?? null;
	}

	public function onUserHasPermissionForSite(UserHasPermissionForSite $query) {
		$link = $this->getLink(siteId: $query->siteId, userId: $query->userId);

		$query->setResults(
			isset($link) &&
			(!$query->mustBeAdmin || $link->isAdmin) &&
			(!$query->mustBeAuthor || $link->isAuthor)
		);
	}
}
