<?php

namespace MageSuite\DeferJs\Test\Unit\Service;

class ScriptDeferTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \MageSuite\DeferJs\Service\ScriptDefer
     */
    protected $scriptDefer;

    public function setUp(): void
    {
        $this->scriptDefer = new \MageSuite\DeferJs\Service\ScriptDefer();
    }

    public function testItDoesNotAlterHTMLWhenThereAreNoScriptTags()
    {
        $beforeDeferHTML = file_get_contents(__DIR__ . '/assets/before_without_script.html', 'r');
        $afterDeferHTML = file_get_contents(__DIR__ . '/assets/after_without_script.html', 'r');

        $html = $this->scriptDefer->execute($beforeDeferHTML);

        $this->assertEquals($afterDeferHTML, $html);
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
        $beforeDeferHTML = file_get_contents(__DIR__ . '/assets/before_long.html', 'r');
        $afterDeferHTML = file_get_contents(__DIR__ . '/assets/after_long.html', 'r');

        $html = $this->scriptDefer->execute($beforeDeferHTML);

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
