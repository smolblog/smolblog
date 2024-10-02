<?php

namespace Smolblog\Core\Content\Services;

use Smolblog\Core\Content\Commands\CreateContent;
use Smolblog\Core\Content\Commands\DeleteContent;
use Smolblog\Core\Content\Commands\UpdateContent;
use Smolblog\Core\Content\Entities\Content;
use Smolblog\Foundation\Service\Command\CommandHandler;
use Smolblog\Foundation\Service\Command\CommandHandlerService;

/**
 * Handle generic content commands.
 */
class ContentService implements CommandHandlerService {
	/**
	 * Construct the service
	 *
	 * @param ContentTypeRegistry      $types      Registry of content types.
	 * @param ContentExtensionRegistry $extensions Registry of content extensions.
	 */
	public function __construct(
		private ContentTypeRegistry $types,
		private ContentExtensionRegistry $extensions,
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
		$this->getTypeServiceForContent($command->content)->create($command);
		foreach ($this->getExtensionServicesForContent($command->content) as $extServ) {
			$extServ->create($command);
		}
	}

	/**
	 * Execute the UpdateContent Command.
	 *
	 * @param UpdateContent $command Command to execute.
	 * @return void
	 */
	#[CommandHandler]
	public function updateContent(UpdateContent $command): void {
		$this->getTypeServiceForContent($command->content)->update($command);
		foreach ($this->getExtensionServicesForContent($command->content) as $extServ) {
			$extServ->update($command);
		}
	}

	/**
	 * Execute the DeleteContent Command.
	 *
	 * @param DeleteContent $command Command to execute.
	 * @return void
	 */
	#[CommandHandler]
	public function deleteContent(DeleteContent $command): void {
		$this->getTypeServiceForContent($command->content)->delete($command);
		foreach ($this->getExtensionServicesForContent($command->content) as $extServ) {
			$extServ->delete($command);
		}
	}

	/**
	 * Get the Type Service for the given Content.
	 *
	 * @param Content $content Content being worked on.
	 * @return ContentTypeService
	 */
	private function getTypeServiceForContent(Content $content): ?ContentTypeService {
		return $this->types->getService(get_class($content->body)::KEY);
	}

	/**
	 * Get extension services for the given Content.
	 *
	 * @param Content $content Content being worked on.
	 * @return ContentExtensionService[]
	 */
	private function getExtensionServicesForContent(Content $content): array {
		return array_map(fn($srv) => $this->extensions->getService($srv), array_keys($content->extensions));
	}
}
