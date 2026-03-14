<?php

namespace Smolblog\Core\User;

use Cavatappi\Foundation\Command\CommandHandler;
use Cavatappi\Foundation\Command\CommandHandlerService;
use Cavatappi\Foundation\Exceptions\CommandNotAuthorized;
use Cavatappi\Foundation\Exceptions\EntityNotFound;
use Cavatappi\Foundation\Exceptions\InvalidValueProperties;
use Cavatappi\Foundation\Factories\UuidFactory;
use Cavatappi\Foundation\Service;
use Dom\Entity;
use Psr\EventDispatcher\EventDispatcherInterface;
use Ramsey\Uuid\UuidInterface;
use Smolblog\Core\Permissions\GlobalPermissionsService;

class UserService implements Service, CommandHandlerService {
	public function __construct(
		private UserRepo $repo,
		private GlobalPermissionsService $perms,
		private EventDispatcherInterface $eventBus,
	) {}

	#[CommandHandler]
	public function onRegisterUser(RegisterUser $command): UuidInterface {
		$newUserId = $command->newUserId;
		if (isset($newUserId) && $this->repo->hasUserWithId($newUserId)) {
			throw new InvalidValueProperties(
				message: "The given ID {$newUserId} is already in use.",
				field: 'newUserId',
			);
		}

		if ($this->repo->hasUserWithKey($command->key)) {
			throw new InvalidValueProperties(
				message: "The given key {$command->key} is already in use.",
				field: 'key',
			);
		}

		if (!$this->perms->canRegisterUser($command->userId)) {
			throw new CommandNotAuthorized(originalCommand: $command);
		}

		if (!isset($newUserId)) {
			do {
				$newUserId = UuidFactory::random();
			} while (!$this->repo->hasUserWithId($newUserId));
		}

		$event = new UserRegistered(
			userId: $command->userId,
			entityId: $newUserId,
			key: $command->key,
			displayName: $command->displayName,
		);

		$this->eventBus->dispatch($event);

		return $newUserId;
	}

	#[CommandHandler]
	public function onGrantUserSudo(GrantUserSudo $command): void {
		if (!$this->perms->canGrantUserSudo($command->userId)) {
			throw new CommandNotAuthorized(originalCommand: $command);
		}

		if (!$this->repo->hasUserWithId($command->userIdToEscalate)) {
			throw new EntityNotFound(
				entityId: $command->userIdToEscalate,
				entityName: User::class,
			);
		}

		$event = new UserGrantedSudo(
			userId: $command->userId,
			entityId: $command->userIdToEscalate,
		);
		$this->eventBus->dispatch($event);
	}
}
