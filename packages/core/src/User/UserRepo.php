<?php

namespace Smolblog\Core\User;

use Ramsey\Uuid\UuidInterface;

interface UserRepo {
	public function hasUserWithId(UuidInterface $id): bool;
	public function hasUserWithKey(string $key): bool;
	public function userById(UuidInterface $userId): ?User;
}
