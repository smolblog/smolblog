<?php

namespace Smolblog\Core\Content\Services;

use Smolblog\Core\Content\Commands\CreateContent;
use Smolblog\Core\Content\Commands\DeleteContent;
use Smolblog\Core\Content\Commands\UpdateContent;
use Smolblog\Core\Content\Data\ContentRepo;
use Smolblog\Core\Content\Entities\Content;
use Smolblog\Core\Content\Entities\ContentExtension;
use Smolblog\Core\Content\Entities\ContentType;
use Smolblog\Core\Permissions\SitePermissionsService;
use Smolblog\Core\Site\Data\SiteRepo;
use Smolblog\Foundation\Exceptions\CommandNotAuthorized;
use Smolblog\Foundation\Exceptions\EntityNotFound;
use Smolblog\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Foundation\Service\Command\CommandHandler;
use Smolblog\Foundation\Service\Command\CommandHandlerService;
use Smolblog\Foundation\Value\Fields\DateIdentifier;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Handle generic content commands.
 */
class ContentService implements CommandHandlerService {
	/**
	 * Construct the service
	 *
	 * @param ContentTypeRegistry      $types      Registry of content types.
	 * @param ContentExtensionRegistry $extensions Registry of content extensions.
	 * @param ContentRepo              $repo       Content objects.
	 * @param SitePermissionsService   $perms      Check permissions.
	 */
	public function __construct(
		private ContentTypeRegistry $types,
		private ContentExtensionRegistry $extensions,
		private ContentRepo $repo,
		private SitePermissionsService $perms,
	) {
	}

	/**
	 * Execute the CreateContent Command.
	 *
	 * @param CreateContent $command Command to execute.
	 * @return void
	 */
	#[CommandHandler]
	public function createContent(CreateContent $command): void {
		// Check for existing ID.
		$contentId = $command->contentId;
		if (isset($contentId) && $this->repo->hasContentWithId($contentId)) {
			throw new InvalidValueProperties(
				message: "The given ID {$contentId} is already in use.",
				field: 'content.id',
			);
		}

		// Check permissions.
		if (!$this->perms->canCreateContent(userId: $command->userId, siteId: $command->siteId)) {
			throw new CommandNotAuthorized(originalCommand: $command);
		}

		// Generate a new ID.
		if (!isset($contentId)) {
			do {
				$contentId = new DateIdentifier();
			} while (!$this->repo->hasContentWithId($contentId));
		}

		// Save the new Content.
		foreach ($this->getServicesForContentExtensions($command->extensions) as $extServ) {
			$extServ->create($command, $contentId);
		}
		$this->getServiceForContentType($command->body)->create($command, $contentId);
	}

	/**
	 * Execute the UpdateContent Command.
	 *
	 * @param UpdateContent $command Command to execute.
	 * @return void
	 */
	#[CommandHandler]
	public function updateContent(UpdateContent $command): void {
		if (!$this->repo->hasContentWithId($command->contentId)) {
			throw new EntityNotFound(entityId: $command->contentId, entityName: Content::class);
		}

		if (!$this->userCanEditContent(userId: $command->userId, contentId: $command->contentId)) {
			throw new CommandNotAuthorized(originalCommand: $command);
		}

		foreach ($this->getServicesForContentExtensions($command->extensions) as $extServ) {
			$extServ->update($command);
		}
		$this->getServiceForContentType($command->body)->update($command);
	}

	/**
	 * Execute the DeleteContent Command.
	 *
	 * @param DeleteContent $command Command to execute.
	 * @return void
	 */
	#[CommandHandler]
	public function deleteContent(DeleteContent $command): void {
		$content = $this->repo->contentById(contentId: $command->contentId);
		if (!isset($content)) {
			throw new EntityNotFound(entityId: $command->contentId, entityName: Content::class);
		}

		if (!$this->userCanEditContent(userId: $command->userId, contentId: $command->contentId)) {
			throw new CommandNotAuthorized(originalCommand: $command);
		}

		foreach ($this->getServicesForContentExtensions($content->extensions) as $extServ) {
			$extServ->delete($command, $content);
		}
		$this->getServiceForContentType($content->body)->delete($command, $content);
	}

	/**
	 * Check if the given user can make changes to the given content.
	 *
	 * @param Identifier $userId    User to check.
	 * @param Identifier $contentId Content to check.
	 * @return boolean
	 */
	public function userCanEditContent(Identifier $userId, Identifier $contentId): bool {
		$content = $this->repo->contentById($contentId);
		if (!isset($content)) {
			return false;
		}
		if ($content?->userId == $userId) {
			return true;
		}

		return $this->perms->canEditAllContent(userId: $userId, siteId: $content->siteId);
	}

	/**
	 * Get the Type Service for the given ContentType.
	 *
	 * @param ContentType $body Content being worked on.
	 * @return ContentTypeService
	 */
	private function getServiceForContentType(ContentType $body): ?ContentTypeService {
		return $this->types->getService(get_class($body)::KEY);
	}

	/**
	 * Get extension services for the given ContentExtensions.
	 *
	 * @param ContentExtension[] $extensions Content being worked on.
	 * @return ContentExtensionService[]
	 */
	private function getServicesForContentExtensions(array $extensions): array {
		return array_map(fn($srv) => $this->extensions->getService($srv), array_keys($extensions));
	}
}
