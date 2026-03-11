<?php

namespace Smolblog\Core\Test;

use Ramsey\Uuid\UuidInterface;
use Smolblog\Core\Site\Entities\SitePermissionLevel;
use Smolblog\Core\User\User;

/**
 * A series of integration tests that ensure that a data module is correctly storing information needed by Smolblog.
 */
trait DataModuleTest {
	abstract protected function setUpGlobalAdminUser(): User;
	abstract protected function setUpSiteUser(UuidInterface $siteId, SitePermissionLevel $level): User;
}
