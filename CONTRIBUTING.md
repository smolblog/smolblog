# Contribution Guidelines

## In General

This is not my full-time job. This isn't even a part-time job. And until it pays that, I can't devote enormous amounts
of time to it. If you're planning on contributing, you have my thanks! You can make _my_ job easier by making _your_
code easy to understand. Small changes are better if it makes the changes easier to reason about. If we can all operate
from the perspective of wanting this project to be the best it can be, then things should work out well.

## About Pull Requests

Pull request templates are coming soon, but the basic idea is

1. Run the fixer. Use your IDE or `composer lintfix`.
2. Run the linter. Use your IDE or `composer lint`.
3. Make sure tests for existing code pass. Use `composer test`.
	- Ideally, **existing tests should not change**.
	- If existing tests do need to change, include a note about why the changes are needed.
	- If a change breaks backwards compatability, include a note about why it should.
4. Write tests for new code.

## About Copyright

We are not currently accepting or requesting any additional terms or contributor license agreements.
All contributions are accepted and covered under the [AGPL3.0](LICENSE.md).

On behalf of the Smolblog project, we sincerely thank you for your contributions.

-- Evan
