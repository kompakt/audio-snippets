<?php

/*
 * This file is part of the kompakt/audio-snippets package.
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 *
 */

namespace Kompakt\AudioSnippets\Tests;

use Kompakt\AudioSnippets\Trimmer;
use Kompakt\AudioTools\Runner\SoxRunner;
use Kompakt\AudioTools\Runner\SoxiRunner;
use Kompakt\AudioTools\Factory\SoxiFactory;

class TrimmerTest extends \PHPUnit_Framework_TestCase
{
    public function test5Seconds()
    {
        // this should be left untouched
        $tmpDir = $this->getTmpDir(__METHOD__);

        $inFile = sprintf('%s/_files/TrimmerTest/05-seconds.wav', __DIR__);
        $outFile = sprintf('%s/05-seconds.wav', $tmpDir);
        
        $trimmer = $this->getTrimmer();
        $trimmer->trim($inFile, $outFile, 10);
        $this->assertFileExists($outFile);
    }

    public function test10Seconds()
    {
        // this should be faded in and out (and keep duration untouched)
        $tmpDir = $this->getTmpDir(__METHOD__);

        $inFile = sprintf('%s/_files/TrimmerTest/10-seconds.wav', __DIR__);
        $outFile = sprintf('%s/10-seconds.wav', $tmpDir);
        
        $trimmer = $this->getTrimmer();
        $trimmer->trim($inFile, $outFile, 10);
        $this->assertFileExists($outFile);
    }

    public function test30Seconds()
    {
        // this should be trimmed to 10 seconds plus fade in and out
        $tmpDir = $this->getTmpDir(__METHOD__);

        $inFile = sprintf('%s/_files/TrimmerTest/30-seconds.wav', __DIR__);
        $outFile = sprintf('%s/30-seconds.wav', $tmpDir);
        
        $trimmer = $this->getTrimmer();
        $trimmer->trim($inFile, $outFile, 10);
        $this->assertFileExists($outFile);
    }

    protected function getTmpDir($method)
    {
        $tmpDir = getTmpDir();
        return $tmpDir->makeSubDir($tmpDir->prepareSubDirPath($method));
    }

    protected function getTrimmer()
    {
        return new Trimmer(
            new SoxRunner(TESTS_KOMPAKT_AUDIOSNIPPETS_SOX),
            new SoxiFactory(new SoxiRunner(TESTS_KOMPAKT_AUDIOSNIPPETS_SOXI))
        );
    }
}