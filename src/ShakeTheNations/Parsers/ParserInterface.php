<?php

namespace ShakeTheNations\Parsers;

/**
 * Interface implemented by content parser classes.
 */
interface ParserInterface
{
    /**
     * Transforms the original Markdown content into the desired output format.
     * @param  string $content      The original content to be parsed
     * @param  string $inputFormat  The expexted input format (it only supports 'xml' for now)
     * @param  string $outputFormat The desired output format (it only supports 'json' for now)
     * @return string The parsed content
     */
    public function transform($content, $inputFormat = 'xml', $outputFormat = 'json');
}
