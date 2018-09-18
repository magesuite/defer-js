<?php

namespace MageSuite\DeferJs\Test\Unit\Services;

class ScriptDeferTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \MageSuite\DeferJs\Services\ScriptDefer
     */
    protected $scriptDefer;

    public function setUp()
    {
        $this->scriptDefer = new \MageSuite\DeferJs\Services\ScriptDefer(10000);
    }

    public function testItDoesNotAlterHTMLWhenThereAreNoScriptTags()
    {
        $beforeDeferHTML = file_get_contents(__DIR__ . '/assets/before_without_script.html', 'r');

        $html = $this->scriptDefer->execute($beforeDeferHTML);

        $this->assertEquals($beforeDeferHTML, $html);
    }

    public function testItDefersJavascript()
    {
        $beforeDeferHTML = file_get_contents(__DIR__ . '/assets/before.html', 'r');
        $afterDeferHTML = file_get_contents(__DIR__ . '/assets/after.html', 'r');

        $html = $this->scriptDefer->execute($beforeDeferHTML);

        $this->assertEquals($afterDeferHTML, $html);
    }

    public function testItDefersLongJavascriptStrings()
    {
        $scriptDefer = new \MageSuite\DeferJs\Services\ScriptDefer();
        $beforeDeferHTML = file_get_contents(__DIR__ . '/assets/before_long.html', 'r');
        $afterDeferHTML = file_get_contents(__DIR__ . '/assets/after_long.html', 'r');

        $html = $scriptDefer->execute($beforeDeferHTML);

        $this->assertEquals($afterDeferHTML, $html);
    }

    public function testItDefersJavascriptWithWhitelistedStrings()
    {
        $beforeDeferHTML = file_get_contents(__DIR__ . '/assets/before.html', 'r');
        $afterDeferHTML = file_get_contents(__DIR__ . '/assets/after_with_whitelist.html', 'r');

        $html = $this->scriptDefer->execute($beforeDeferHTML, ['data-template="uploader"']);

        $this->assertEquals($afterDeferHTML, $html);
    }
}