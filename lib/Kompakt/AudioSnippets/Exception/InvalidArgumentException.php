<?php

/*
 * This file is part of the kompakt/audio-tools package.
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 *
 */

namespace Kompakt\AudioSnippets\Exception;

use Kompakt\AudioSnippets\Exception as AudioSnippetsException;

class InvalidArgumentException extends \InvalidArgumentException implements AudioSnippetsException
{}