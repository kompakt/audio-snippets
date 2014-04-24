<?php

/*
 * This file is part of the kompakt/audio-tools package.
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 *
 */

namespace Kompakt\Tests\AudioSnippets;

use Kompakt\AudioSnippets\Splicer;
use Kompakt\AudioTools\Runner\SoxRunner;
use Kompakt\AudioTools\Runner\SoxiRunner;
use Kompakt\AudioTools\SoxiFactory;

class SplicerTest extends \PHPUnit_Framework_TestCase
{
    public function test30SecondsWav()
    {
        // should be left as is
        $tmpDir = freshTmpSubDir(__METHOD__);
        $inFile = sprintf('%s/_files/SplicerTest/30-seconds.wav', __DIR__);
        $outFile = sprintf('%s/30-seconds.wav', $tmpDir);

        $splicer = new Splicer(new SoxRunner(TESTS_KOMPAKT_AUDIOSNIPPETS_SOX), new SoxiFactory(new SoxiRunner(TESTS_KOMPAKT_AUDIOSNIPPETS_SOXI)), $tmpDir);
        $splicer->splice($inFile, $outFile, 45, 1, $splicer->getVinylSplices());
        $this->assertFileExists($outFile);
    }

    public function test60SecondsWav()
    {
        // should be left as is
        $tmpDir = freshTmpSubDir(__METHOD__);
        $inFile = sprintf('%s/_files/SplicerTest/60-seconds.wav', __DIR__);
        $outFile = sprintf('%s/60-seconds.wav', $tmpDir);

        $splicer = new Splicer(new SoxRunner(TESTS_KOMPAKT_AUDIOSNIPPETS_SOX), new SoxiFactory(new SoxiRunner(TESTS_KOMPAKT_AUDIOSNIPPETS_SOXI)), $tmpDir);
        $splicer->splice($inFile, $outFile, 45, 1, $splicer->getVinylSplices());
        $this->assertFileExists($outFile);
    }

    public function test70SecondsWav()
    {
        // should receive one splice in the middle
        $tmpDir = freshTmpSubDir(__METHOD__);
        $inFile = sprintf('%s/_files/SplicerTest/70-seconds.wav', __DIR__);
        $outFile = sprintf('%s/70-seconds.wav', $tmpDir);

        $splicer = new Splicer(new SoxRunner(TESTS_KOMPAKT_AUDIOSNIPPETS_SOX), new SoxiFactory(new SoxiRunner(TESTS_KOMPAKT_AUDIOSNIPPETS_SOXI)), $tmpDir);
        $splicer->splice($inFile, $outFile, 45, 1, $splicer->getVinylSplices());
        $this->assertFileExists($outFile);
    }

    public function test120SecondsWav()
    {
        // should receive two splices
        $tmpDir = freshTmpSubDir(__METHOD__);
        $inFile = sprintf('%s/_files/SplicerTest/120-seconds.wav', __DIR__);
        $outFile = sprintf('%s/120-seconds.wav', $tmpDir);

        $splicer = new Splicer(new SoxRunner(TESTS_KOMPAKT_AUDIOSNIPPETS_SOX), new SoxiFactory(new SoxiRunner(TESTS_KOMPAKT_AUDIOSNIPPETS_SOXI)), $tmpDir);
        $splicer->splice($inFile, $outFile, 45, 1, $splicer->getVinylSplices());
        $this->assertFileExists($outFile);
    }

    public function test120SecondsAiff()
    {
        // should receive two splices (and be converted to wav)
        $tmpDir = freshTmpSubDir(__METHOD__);
        $inFile = sprintf('%s/_files/SplicerTest/120-seconds.aiff', __DIR__);
        $outFile = sprintf('%s/120-seconds.wav', $tmpDir);

        $splicer = new Splicer(new SoxRunner(TESTS_KOMPAKT_AUDIOSNIPPETS_SOX), new SoxiFactory(new SoxiRunner(TESTS_KOMPAKT_AUDIOSNIPPETS_SOXI)), $tmpDir);
        $splicer->splice($inFile, $outFile, 45, 1, $splicer->getVinylSplices());
        $this->assertFileExists($outFile);
    }
}