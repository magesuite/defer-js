<?php

namespace MageSuite\DeferJs\Service;

class ScriptDefer
{
    protected $defaultIgnoredSubstrings = ['defer-ignore'];

    /**
     * Moves script tags from given HTML to the end of the body.
     * Every script is additionally checked against ignored list configured in admin panel.
     * 
     * @param string $html HTML with script tags all over the place.
     * @return string HTML with non-ignored script tags moved to the end of body.
     */
    public function execute($html, $ignoredScriptsSubstrings = [])
    {
        $bodyClose = '</body';

        if (strpos($html, $bodyClose) === false) {
            return $html;
        }

        $scripts = '';

        $scriptOpen = '<script';
        $scriptClose = '</script>';

        $ignoredScriptsSubstrings = array_merge($this->defaultIgnoredSubstrings, $ignoredScriptsSubstrings);
        $ignoredScriptsRegexp = join('|', $ignoredScriptsSubstrings);

        $scriptOpenPos = strpos($html, $scriptOpen);
        while ($scriptOpenPos !== false) {
            $scriptClosePos = strpos($html, $scriptClose, $scriptOpenPos);
            $script = substr($html, $scriptOpenPos, $scriptClosePos - $scriptOpenPos + strlen($scriptClose));

            if ($ignoredScriptsRegexp && preg_match("/$ignoredScriptsRegexp/ms", $script)) {
                // Script ignored, search for the next one after it.
                $scriptOpenPos = strpos($html, $scriptOpen, $scriptClosePos);
                continue;
            }

            $scripts .= "\n" . $script;
            $html = str_replace($script, '', $html);
            // Script cut out, continue search from its position.
            $scriptOpenPos = strpos($html, $scriptOpen, $scriptOpenPos);
        }

        $html = str_replace($bodyClose, $scripts . "\n" . $bodyClose, $html);

        return $html;
    }

}