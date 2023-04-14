<?php

namespace Smolblog\Framework\Messages;

/**
 * Denotes a projection-style listener service that reacts to Events and updates its own data accordingly.
 *
 * Most Listener services react to a message and perform a task. A Projection's task is to take an Event—a change in
 * state—and apply it to a set of data. In an event-sourced system, Projections are ephemeral and can be regenerated.
 *
 * Because Events are Messages, any service can react to them. This includes services with side effects; for example, a
 * service that reacts to published content by sending a webhook. Regenerating a projection should be a free action with
 * no loss of data or side effects. Therefore, when regenerating, we need to ensure that only Projection services are
 * called, not all Listeners.
 */
interface Projection extends Listener {
}
