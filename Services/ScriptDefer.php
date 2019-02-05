<?php

namespace MageSuite\DeferJs\Services;

class ScriptDefer
{
    /**
     * 'pcre.backtrack_limit' and 'pcre.recursion_limit' value
     */
    private $backtrackLimit;

    private $defaultIgnoredSubstrings = ['defer-ignore'];

    public function __construct($backtrackLimit = 1000000)
    {
        $this->backtrackLimit = $backtrackLimit;
    }

    /**
     * @param string $html
     * @return string
     */
    public function execute($html, $ignoredScriptsSubstrings = [])
    {
        if(strpos($html,'</body>') === false) {
            return $html;
        }
        $scriptsPattern = '@<script(.*)<\/script>@msU';
        $pcreBacktrackLimit = ini_get('pcre.backtrack_limit');
        $pcreRecursionLimit = ini_get('pcre.recursion_limit');

        ini_set('pcre.backtrack_limit', $this->backtrackLimit);
        ini_set('pcre.recursion_limit', $this->backtrackLimit);

        if (preg_match_all($scriptsPattern, $html, $matches) !== false) {
            $deferredScripts = '';
            $ignoredScriptsSubstrings = array_merge($this->defaultIgnoredSubstrings, $ignoredScriptsSubstrings);
            $skipRegexp = join('|', $ignoredScriptsSubstrings);
            
            foreach($matches[0] as $key => $scriptTag) {
                if(!$skipRegexp or !preg_match("/$skipRegexp/ms", $scriptTag)) {
                    $html = str_replace($scriptTag, "<!--def$key-->", $html);
                    $deferredScripts.="<!--def$key-->".$scriptTag;
                }
            }

            $html = str_replace('</body>', $deferredScripts.'</body>', $html);
        }

        ini_set('pcre.backtrack_limit', $pcreBacktrackLimit);
        ini_set('pcre.recursion_limit', $pcreRecursionLimit);

        return $html;
    }

}