<?php

namespace Smolblog\Test\Kits;

trait NoteTestKit {
	private $simpleTextMd = 'I tend to frequent a local coffee shop for waffles. Today\'s soundtrack as I walked in was "I\'m Still Standing," "Funky Town" as I waited, and "Take On Me" as I left.';
	private $simpleTextTruncated = 'I tend to frequent a local coffee shop for waffles. Today\'s soundtrack as I walked in was "I\'m Still...';
	private $simpleTextFormatted = '<p>I tend to frequent a local coffee shop for waffles. Today\'s soundtrack as I walked in was "I\'m Still Standing," "Funky Town" as I waited, and "Take On Me" as I left.</p>';

	private $fancyTextMd = <<<EOF
🎵 Okay, what're the songs that, if you let them, will *absolutely* make you cry? For me, it's

- "Walk On" / U2
- "Dare You To Move" / Switchfoot
- "Happy Is a Yuppie Word" / Switchfoot
- "Christmas Don't Be Late" / Rosie Thomas

…among others.
EOF;
	private $fancyTextTruncated = '🎵 Okay, what\'re the songs that, if you let them, will absolutely make you cry? For me, it\'s  - "Walk...';
	private $fancyTextFormatted = <<<EOF
<p>🎵 Okay, what're the songs that, if you let them, will <em>absolutely</em> make you cry? For me, it's</p>

<ul>
	<li>"Walk On" / U2</li>
	<li>"Dare You To Move" / Switchfoot</li>
	<li>"Happy Is a Yuppie Word" / Switchfoot</li>
	<li>"Christmas Don't Be Late" / Rosie Thomas</li>
</ul>

<p>…among others.</p>
EOF;
}
