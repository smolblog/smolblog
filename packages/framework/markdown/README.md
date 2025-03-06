# Smolblog-flavored Markdown

A superset of Markdown with some Smolblog flair.

## Why?

Picking a Markdown dialect to support is a contract with your users. And while I would love to support every flavor of
Markdown possible, it is more valuable, especially in these early days, to start small and expand from there.

Thus, instead of picking [Github Flavored Markdown][gfm] or even the proposed [CommonMark][cm] standard, we are instead
supporting the [John Gruber/Aaron Swartz original][og] flavor with a limited number of add-ons and original
extensions.

[gfm]: https://github.github.com/gfm
[cm]: https://commonmark.org
[og]: https://daringfireball.net/projects/markdown

And also because it's cool.

## Original Extensions

### oEmbed

Being able to embed content from around the internet is core to Smolblog's philosophy, so being able to add embedded
content to a post is certainly important. The embed extension tells the parser to attempt to embed the given URL
using [oEmbed].

[oEmbed]: https://oembed.com

While this is similar to images, both in syntax and execution, it is not deterministic. The same image Markdown code
will always generate the same HTML code. This is _not_ true for oEmbed as the final HTML code is decided by a remote
server in most cases. An image does not have to exist when the Markdown is converted to HTML, but the remote webpage
_does_ need to exist in order to get the code to embed it. A well-crafted library could mitigate this concern.
Regardless, if the parser does not get an oEmbed code, it will default back to a standard link.

_Thanks to [Chris][v_] and [Logan][llbbl] for [chiming in on Micro.blog][convo] about this._

[v_]: https://vv.micro.blog/
[llbbl]: https://llbbl.blog
[convo]: https://micro.blog/oddevan/16389878

The markdown:

	:[An innocuous YouTube video](https://youtu.be/rTga41r3a4s)

The markup:

	<iframe width="200" height="113" src="https://www.youtube.com/embed/rTga41r3a4s?feature=oembed" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen title="Rick Astley - Never Gonna Give You Up (Pianoforte) (Performance)"></iframe>

The markup (without oEmbed)

	<p><a href="https://youtu.be/rTga41r3a4s" target="_blank">An innocuous YouTube video</a></p>

## Other add-ons

These are attributes and extensions found in other dialects of Markdown that we are choosing to support.

### Fenced code blocks

These are most commonly seen in developer documentation, but as tutorials and examples can be key parts of many
developers' blogs, this felt like a good add-on to consider. Its syntax is unobtrusive, and it adds an explicit tag
for the programming language being used, if any.

Note that the original code block of a single tab or 4 spaces is still supported (and in fact used here).

The markdown:

	```php
	$extArray = json_decode($results['extensions'] ?? '{}', associative: true);
	$extParsed = [];
	foreach ($extArray as $class => $data) {
		$extParsed[$class] = $class::fromArray($data);
	}
	```

The markup:

	<pre><code class="language-php">$extArray = json_decode($results['extensions'] ?? '{}', associative: true);
	$extParsed = [];
	foreach ($extArray as $class => $data) {
		$extParsed[$class] = $class::fromArray($data);
	}
	</code></pre>

(Actually bringing in syntax highlighting is a problem for future Smolblog.)

## Using this library

~~Install it using [composer]: `composer require smolblog/smolblog-markdown`~~ _not on packagist yet ^_^;;_

[composer]: https://getcomposer.org/

### Create a parser without oEmbed

```php
use Smolblog\Markdown\SmolblogMarkdown;

//...

$md = new SmolblogMarkdown();
```

This will create the parser **with no oEmbed support.** Embed directives will show as normal links as seen above.

### Custom oEmbed provider

If you have a different oEmbed library you want to use, you can pass in anything that implements the `EmbedProvider`
interface:

```php
interface EmbedProvider {
	public function getEmbedCodeFor(string $url): ?string;
}
```

### Parsing Markdown

Once you have the parser instantiated, it's as easy as:

```php
$md->parse($markdown_text);
```

This library is built on [cebe/markdown][cbmd], and more detailed usage can be found in its documentation.

[cbmd]: https://github.com/cebe/markdown

## Acknowledgements

- Based on [cebe/markdown][cbmd]
- Syntax feedback provided by [Chris][v_] and [Logan][llbbl]
- Built by Evan Hildreth for the Smolblog project
