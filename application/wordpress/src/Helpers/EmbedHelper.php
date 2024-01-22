<?php

namespace Smolblog\WP\Helpers;

use DOMDocument;
use WP_oEmbed;
use Smolblog\Core\Content\Types\Reblog\ExternalContentInfo;
use Smolblog\Core\Content\Types\Reblog\ExternalContentService;

class EmbedHelper implements ExternalContentService {
	private WP_oEmbed $internal;

	public function __construct()
	{
		$this->internal = new WP_oEmbed();
	}

	public function getExternalContentInfo(string $url): ExternalContentInfo
	{
		$data = $this->internal->get_data($url);

		if (!$data) {
			$title = $this->getExternalTitle($url);
			return new ExternalContentInfo(
				title: $title,
				embed: "<p><a href=\"$url\" target=\"_blank\">$title</a></p>",
			);
		}
		
		return new ExternalContentInfo(
			title: $data->title ?? ($data->author_name ? "$data->author_name on " : '') . $data->provider_name,
			embed: $this->internal->data2html($data, $url),
		);
	}

	private function getExternalTitle(string $url): string {
		$title = 'A Webpage';
		$external_doc = file_get_contents($url);
		$dom = new DOMDocument();

		if($dom->loadHTMLFile($external_doc)) {
				$list = $dom->getElementsByTagName("title");
				if ($list->length > 0) {
						$title = $list->item(0)->textContent;
				}
		}

		return $title;
	}
}