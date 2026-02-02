<?php // phpcs:disable Squiz.Commenting.FunctionComment.TypeHintMissing

namespace Smolblog\Markdown\Elements;

use cebe\markdown\block\FencedCodeTrait;

/**
 * A extra layer on top of fenced code blocks that allows language processing.
 *
 * By default, a tagged fenced block will include the language. For example:
 *
 *     ```php
 *     $this->mf->process($content);
 *     ```
 *
 * Will be rendered as:
 *
 *     <pre><code class="language-php>$this->mf->process($context);</code></pre>
 *
 * This allows client-side libraries like Highlight.js to identify and style the code block.
 *
 * This trait allows for extra processing during the Markdown->HTML conversion. It provides
 * an array property $codeRenderers where the keys are programming languages and the values are
 * callable functions that take the text of the code block and return HTML code. This allows
 * for server-side code highlighting, or turning the code block into something else entirely!
 */
trait CustomFencedCodeTrait {
	use FencedCodeTrait {
		renderCode as baseRenderCode;
	}

	/**
	 * Store custom code block renderers.
	 *
	 * @var array<string, callable>
	 */
	protected array $codeRenderers = [];

	/**
	 * Render a code block.
	 *
	 * If the block has a language set and a renderer exists in $this->codeRenderers, that result will
	 * be returned. Otherwise it is passed through to the base CodeTrait::renderCode method.
	 *
	 * @param array $block Block to render.
	 * @return string
	 */
	protected function renderCode($block) {
		if (isset($block['language']) && array_key_exists($block['language'], $this->codeRenderers)) {
			return call_user_func($this->codeRenderers[$block['language']], $block);
		}
		return $this->baseRenderCode($block);
	}
}
// phpcs:enable Squiz.Commenting.FunctionComment.TypeHintMissing
