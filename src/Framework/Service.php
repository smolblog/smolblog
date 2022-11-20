<?php

namespace Smolblog\Framework;

/**
 * An object that does a thing. Should implement the `run` method by convention.
 *
 * A service does something, usually with some data. This can be handling a Command or just running some bit of code
 * that does some menial task.
 *
 * Using Service objects instead of functions allows Services to use dependency injection. Dependencies can be declared
 * and provided in the object's constructor then used in the run method.
 *
 * The `run` method is not defined here; this will allow implementing classes to properly type hint any parameters.
 */
interface Service {
}
