<?php

namespace Smolblog\Core\Post;

/**
 * Indicates that this particular piece of media should be downloaded to the local store.
 *
 * The media stored in the local store could actually be stored on a different server and/or in a CDN, so simply having
 * an external or absolute URL isn't enough. This class denotes that a piece of media is stored on a third-party server
 * and should be copied and replaced with a first-party copy.
 *
 * Obviously, this is for use in importing and other situations where a user is getting *their own media*. Remember:
 * reblog, don't repost.
 */
class MediaToSideload extends Media {
}
