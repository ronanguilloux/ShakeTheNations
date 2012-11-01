<?php

namespace ShakeTheNations\Parsers;

use ShakeTheNations\Parsers\ParserInterface;

class Parser implements ParserInterface
{

    /**
     * Transforms the original Markdown content into the desired output format.
     * @param  string $content      The original content to be parsed
     * @param  string $inputFormat  The expexted input format (it only supports 'xml' for now)
     * @param  string $outputFormat The desired output format (it only supports 'json' for now)
     * @return string The parsed content
     */
    public function transform($content, $inputFormat = 'xml', $outputFormat = 'json')
    {
        $supportedInputFormats = array('xml');
        $supportedOutputFormats = array('json');
        $parsedContent = null;

        if (!in_array($inputFormat, $supportedInputFormats)) {
            throw new \Exception(sprintf('No parser available for "%s" format',
                $inputFormat
            ));
        }

        if (!in_array($outputFormat, $supportedOutputFormats)) {
            throw new \Exception(sprintf('No parser available for "%s" format',
                $outputFormat
            ));
        }

        switch ($inputFormat) {
        case 'xml':
            switch ($outputFormat) {
            case 'json':
                $parsedContent = json_encode(simplexml_load_string($content));
                break;
            }
            break;
        }

        return $parsedContent;
    }
}
