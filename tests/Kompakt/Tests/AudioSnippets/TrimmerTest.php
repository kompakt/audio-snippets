<?php

/*
 * This file is part of the kompakt/audio-snippets package.
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 *
 */

namespace Kompakt\Tests\AudioSnippets;

use Kompakt\AudioSnippets\Trimmer;
use Kompakt\AudioTools\Runner\SoxRunner;
use Kompakt\AudioTools\Runner\SoxiRunner;
use Kompakt\AudioTools\SoxiFactory;

class TrimmerTest extends \PHPUnit_Framework_TestCase
{
    public function test5Seconds()
    {
        // this should be left untouched
        $tmpDir = freshTmpSubDir(__METHOD__);
        $inFile = sprintf('%s/_files/TrimmerTest/05-seconds.wav', __DIR__);
        $outFile = sprintf('%s/05-seconds.wav', $tmpDir);
        $soxiFactory = new SoxiFactory(new SoxiRunner(TESTS_KOMPAKT_AUDIOSNIPPETS_SOXI));

        $trimmer = new Trimmer(new SoxRunner(TESTS_KOMPAKT_AUDIOSNIPPETS_SOX), $soxiFactory);
        $trimmer->trim($inFile, $outFile, 10);
        $this->assertFileExists($outFile);

        $soxi = $soxiFactory->getInstance($outFile);
        $this->assertEquals(5, $soxi->getDuration(), "", TESTS_KOMPAKT_AUDIOSNIPPETS_DELTA);
    }

    public function test10Seconds()
    {
        // this should be faded in and out (and keep duration untouched)
        $tmpDir = freshTmpSubDir(__METHOD__);
        $inFile = sprintf('%s/_files/TrimmerTest/10-seconds.wav', __DIR__);
        $outFile = sprintf('%s/10-seconds.wav', $tmpDir);
        $soxiFactory = new SoxiFactory(new SoxiRunner(TESTS_KOMPAKT_AUDIOSNIPPETS_SOXI));

        $trimmer = new Trimmer(new SoxRunner(TESTS_KOMPAKT_AUDIOSNIPPETS_SOX), $soxiFactory);
        $trimmer->trim($inFile, $outFile, 10);
        $this->assertFileExists($outFile);

        $soxi = $soxiFactory->getInstance($outFile);
        $this->assertEquals(10, $soxi->getDuration(), "", TESTS_KOMPAKT_AUDIOSNIPPETS_DELTA);
    }

    public function test30Seconds()
    {
        // this should be trimmed to 10 seconds plus fade in and out
        $tmpDir = freshTmpSubDir(__METHOD__);
        $inFile = sprintf('%s/_files/TrimmerTest/30-seconds.wav', __DIR__);
        $outFile = sprintf('%s/30-seconds.wav', $tmpDir);
        $soxiFactory = new SoxiFactory(new SoxiRunner(TESTS_KOMPAKT_AUDIOSNIPPETS_SOXI));

        $trimmer = new Trimmer(new SoxRunner(TESTS_KOMPAKT_AUDIOSNIPPETS_SOX), $soxiFactory);
        $trimmer->trim($inFile, $outFile, 10);
        $this->assertFileExists($outFile);

        $soxi = $soxiFactory->getInstance($outFile);
        $this->assertEquals(10, $soxi->getDuration(), "", TESTS_KOMPAKT_AUDIOSNIPPETS_DELTA);
    }
}