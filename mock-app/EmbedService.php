<?php

namespace Smolblog\Mock;

use Smolblog\Core\ContentV1\Types\Reblog\ExternalContentInfo;
use Smolblog\Core\ContentV1\Types\Reblog\ExternalContentService;

class EmbedService implements ExternalContentService {
	public function getExternalContentInfo(string $url): ExternalContentInfo {
		return match($url) {
			'https://youtu.be/rTga41r3a4s' => new ExternalContentInfo(
				title: 'Rick Astley - Never Gonna Give You Up (Pianoforte) (Performance) - YouTube',
				embed: '<iframe width="560" height="315" src="https://www.youtube.com/embed/rTga41r3a4s?si=02jX1Tsd3WWwrXUf" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>',
			),
			default => new ExternalContentInfo(
				title: 'A Webpage',
				embed: '<a href="'.$url.'" target="_blank">A Webpage</a>',
			),
		};
	}
}
