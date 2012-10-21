<?php

/*
 * This file is part of the easybook application.
 *
 * (c) Javier Eguiluz <javier.eguiluz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ShakeTheNations\Parsers;

use ShakeTheNations\DependencyInjection\Application;

/**
 * Interface implemented by content parser classes.
 */
interface ParserInterface
{
    /**
     * Converts the original content into the appropriate content
     *
     * @param string $content The original content to be parsed
     * @param string $format  The format of the output parsed content (e.g. 'json')
     *
     * @return string The parsed content
     */
    public function transform($content);
}
