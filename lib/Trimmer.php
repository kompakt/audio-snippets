<?php

/*
 * This file is part of the kompakt/audio-snippets package.
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 *
 */

namespace Kompakt\AudioSnippets;

use Kompakt\AudioSnippets\Exception\InvalidArgumentException;
use Kompakt\AudioTools\Runner\SoxRunner;
use Kompakt\AudioTools\Factory\SoxiFactory;

class Trimmer
{
    protected $soxRunner = null;
    protected $soxiFactory = null;

    public function __construct(SoxRunner $soxRunner, SoxiFactory $soxiFactory)
    {
        $this->soxRunner = $soxRunner;
        $this->soxiFactory = $soxiFactory;
    }

    public function trim(string $inFile, string $outFile, $snippetLength = 60, $fadeType = 't', $fadeInLength = 3, $fadeOutLength = 3)
    {
        $info = new \SplFileInfo($inFile);

        if (!$info->isFile())
        {
            throw new InvalidArgumentException(sprintf('Audio file not found: %s', $inFile));
        }

        if (!$info->isReadable())
        {
            throw new InvalidArgumentException(sprintf('Audio file not readable: %s', $inFile));
        }

        $info = new \SplFileInfo(dirname($outFile));

        if (!$info->isDir())
        {
            throw new InvalidArgumentException(sprintf('Output file dir not found: %s', dirname($outFile)));
        }

        if (!$info->isWritable())
        {
            throw new InvalidArgumentException(sprintf('Output file dir not writable: %s', dirname($outFile)));
        }
        
        $soxi = $this->soxiFactory->getInstance($inFile);
        $duration = $soxi->getDuration();

        if ($duration < $snippetLength)
        {
            copy($inFile, $outFile);
            return;
        }

        $start = floor(($duration - $snippetLength) / 2);
        $cmd = sprintf("'%s' -t wav '%s'", $inFile, $outFile);
        $cmd = sprintf("%s trim %s %s", $cmd, $start, $snippetLength);
        $cmd = sprintf("%s fade %s %s %s %s", $cmd, $fadeType, $fadeInLength, $snippetLength, $fadeOutLength);
        $this->soxRunner->execute($cmd);
    }
}