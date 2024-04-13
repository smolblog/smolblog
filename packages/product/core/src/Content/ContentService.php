<?php

namespace Smolblog\Core\Content;

use Smolblog\Core\Content;
use Smolblog\Core\Content\Commands\CreateContent;
use Smolblog\Core\Content\Commands\DeleteContent;
use Smolblog\Core\Content\Commands\UpdateContent;
use Smolblog\Core\Content\Extension\ContentExtensionService;
use Smolblog\Core\Content\Extension\ContentExtensionRegistry;
use Smolblog\Core\Content\Type\ContentTypeRegistry;
use Smolblog\Core\Content\Type\ContentTypeService;
use Smolblog\Foundation\Service\Messaging\ExecutionListener;
use Smolblog\Foundation\Service\Messaging\Listener;

/**
 * Handle generic content commands.
 */
class ContentService implements Listener {
	/**
	 * Construct the service
	 *
	 * @param ContentTypeRegistry $types Registry of content types.
	 * @param ContentExtensionRegistry $extensions Registry of content extensions.
	 */
	public function __construct(
		private ContentTypeRegistry $types,
		private ContentExtensionRegistry $extensions,
	) {
	}

	#[ExecutionListener(later: 5)]
	public function createContent(CreateContent $command): void {
		$this->getTypeServiceForContent($command->content)->create($command);
		foreach($this->getExtensionServicesForContent($command->content) as $extServ) {
			$extServ->create($command);
		}
	}

	#[ExecutionListener(later: 5)]
	public function updateContent(UpdateContent $command): void {
		$this->getTypeServiceForContent($command->content)->update($command);
		foreach($this->getExtensionServicesForContent($command->content) as $extServ) {
			$extServ->update($command);
		}
	}

	#[ExecutionListener(later: 5)]
	public function deleteContent(DeleteContent $command): void {
		$this->getTypeServiceForContent($command->content)->delete($command);
		foreach($this->getExtensionServicesForContent($command->content) as $extServ) {
			$extServ->delete($command);
		}
	}

	/**
	 * Get the Type Service for the given Content.
	 *
	 * @param Content $content Content being worked on.
	 * @return ContentTypeService
	 */
	private function getTypeServiceForContent(Content $content): ContentTypeService {
		return $this->types->get($content->type());
	}

	/**
	 * Get extension services for the given Content.
	 *
	 * @param Content $content Content being worked on.
	 * @return ContentExtensionService[]
	 */
	private function getExtensionServicesForContent(Content $content): array {
		return array_map(fn($srv) => $this->extensions->get($srv), array_keys($content->extensions));
	}
}
