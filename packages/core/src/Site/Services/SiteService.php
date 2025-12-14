<?php

namespace Smolblog\Core\Site\Services;

use Psr\EventDispatcher\EventDispatcherInterface;
use Smolblog\Core\Permissions\GlobalPermissionsService;
use Smolblog\Core\Permissions\SitePermissionsService;
use Smolblog\Core\Site\Commands\CreateSite;
use Smolblog\Core\Site\Commands\SetUserSitePermissions;
use Smolblog\Core\Site\Commands\UpdateSiteDetails;
use Smolblog\Core\Site\Data\SiteRepo;
use Smolblog\Core\Site\Entities\Site;
use Smolblog\Core\Site\Events\SiteCreated;
use Smolblog\Core\Site\Events\SiteDetailsUpdated;
use Smolblog\Core\Site\Events\UserSitePermissionsSet;
use Smolblog\Foundation\Exceptions\CommandNotAuthorized;
use Smolblog\Foundation\Exceptions\EntityNotFound;
use Smolblog\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Foundation\Service\Command\CommandHandler;
use Smolblog\Foundation\Service\Command\CommandHandlerService;
use Smolblog\Foundation\Service\KeypairGenerator;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Fields\RandomIdentifier;

/**
 * Handle Site-related commands.
 */
class SiteService implements CommandHandlerService {
	/**
	 * Create the service.
	 *
	 * @param GlobalPermissionsService $globalPerms Check global permissions.
	 * @param SitePermissionsService   $sitePerms   Check site-level permissions.
	 * @param SiteRepo                 $repo        Retrieve sites.
	 * @param KeypairGenerator         $keygen      Generate encryption keys.
	 * @param EventDispatcherInterface $eventBus    Dispatch events.
	 */
	public function __construct(
		private GlobalPermissionsService $globalPerms,
		private SitePermissionsService $sitePerms,
		private SiteRepo $repo,
		private KeypairGenerator $keygen,
		private EventDispatcherInterface $eventBus,
	) {
	}

	/**
	 * Create a new Site
	 *
	 * @throws InvalidValueProperties If the ID or key is already in use.
	 * @throws CommandNotAuthorized If the user cannot create sites.
	 *
	 * @param CreateSite $command Command to execute.
	 * @return Identifier ID of created site.
	 */
	#[CommandHandler]
	public function onCreateSite(CreateSite $command): Identifier {
		$siteId = $command->siteId;
		if (isset($siteId) && $this->repo->hasSiteWithId($siteId)) {
			throw new InvalidValueProperties(
				message: "The given ID {$siteId} is already in use.",
				field: 'siteId',
			);
		}

		if ($this->repo->hasSiteWithKey($command->key)) {
			throw new InvalidValueProperties(
				message: "The given key {$command->key} is already in use.",
				field: 'key',
			);
		}

		if (!$this->globalPerms->canCreateSite($command->userId)) {
			throw new CommandNotAuthorized(originalCommand: $command);
		}

		if (!isset($siteId)) {
			do {
				$siteId = new RandomIdentifier();
			} while (!$this->repo->hasSiteWithId($siteId));
		}

		$event = new SiteCreated(
			userId: $command->userId,
			aggregateId: $siteId,
			key: $command->key,
			displayName: $command->displayName,
			keypair: $this->keygen->generate(),
			siteUserId: $command->siteUser ?? $command->userId,
			description: $command->description,
		);

		$this->eventBus->dispatch($event);

		$command->setReturnValue($siteId);
		return $siteId;
	}

	/**
	 * Set user permissions for a site.
	 *
	 * @throws EntityNotFound If the site does not exist.
	 * @throws CommandNotAuthorized If the user cannot manage permissions.
	 *
	 * @param SetUserSitePermissions $command Command to execute.
	 * @return void
	 */
	#[CommandHandler]
	public function onSetUserSitePermissions(SetUserSitePermissions $command): void {
		if (!$this->repo->hasSiteWithId($command->siteId)) {
			throw new EntityNotFound(
				entityId: $command->siteId,
				entityName: Site::class,
			);
		}

		if (!$this->sitePerms->canManagePermissions(userId: $command->userId, siteId: $command->siteId)) {
			throw new CommandNotAuthorized(originalCommand: $command);
		}

		$this->eventBus->dispatch(new UserSitePermissionsSet(
			userId: $command->userId,
			aggregateId: $command->siteId,
			entityId: $command->linkedUserId,
			level: $command->level,
		));
	}

	/**
	 * Update details for a site.
	 *
	 * @throws EntityNotFound If the site does not exist.
	 * @throws CommandNotAuthorized If the user cannot manage settings.
	 *
	 * @param UpdateSiteDetails $command Command to execute.
	 * @return void
	 */
	#[CommandHandler]
	public function onUpdateSiteDetails(UpdateSiteDetails $command): void {
		if (!$this->repo->hasSiteWithId($command->siteId)) {
			throw new EntityNotFound(
				entityId: $command->siteId,
				entityName: Site::class,
			);
		}

		if (!$this->sitePerms->canManageSettings(userId: $command->userId, siteId: $command->siteId)) {
			throw new CommandNotAuthorized(originalCommand: $command);
		}

		$this->eventBus->dispatch(new SiteDetailsUpdated(
			userId: $command->userId,
			aggregateId: $command->siteId,
			displayName: $command->displayName,
			description: $command->description,
			pictureId: $command->pictureId,
		));
	}
}
