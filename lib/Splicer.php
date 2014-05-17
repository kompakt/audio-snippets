<?php

/*
 * This file is part of the kompakt/audio-snippets package.
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 *
 */

namespace Kompakt\AudioSnippets;

use Kompakt\AudioSnippets\Exception\InvalidArgumentException;
use Kompakt\AudioTools\Factory\SoxiFactory;
use Kompakt\AudioTools\Runner\SoxRunner;

class Splicer
{
    protected $soxRunner = null;
    protected $soxiFactory = null;
    protected $tmpDir = null;

    public function __construct(SoxRunner $soxRunner, SoxiFactory $soxiFactory, $tmpDir)
    {
        $info = new \SplFileInfo($tmpDir);

        if (!$info->isDir())
        {
            throw new InvalidArgumentException(sprintf('Temp dir not found: %s', $tmpDir));
        }

        if (!$info->isWritable())
        {
            throw new InvalidArgumentException(sprintf('Temp dir not writable: %s', $tmpDir));
        }

        if (!$info->isReadable())
        {
            throw new InvalidArgumentException(sprintf('Temp dir not readable: %s', $tmpDir));
        }
        
        $this->soxRunner = $soxRunner;
        $this->soxiFactory = $soxiFactory;
        $this->tmpDir = $tmpDir;
    }

    public function getVinylSplices()
    {
        $snippets = array();

        for ($x = 1; $x <= 9; $x++)
        {
            $snippets[] = sprintf("%s/Splicer/%s.wav", __DIR__, $x);
        }

        return $snippets;
    }

    public function splice($inFile, $outFile, $partLength = 45, $excess = 1, array $splices = array())
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

        if (!count($splices))
        {
            throw new InvalidArgumentException('At least one splice file must be provided');
        }
        
        foreach ($splices as $splice)
        {
            $info = new \SplFileInfo($splice);

            if (!$info->isFile())
            {
                throw new InvalidArgumentException(sprintf('Splice file not found: %s', $splice));
            }

            if (!$info->isReadable())
            {
                throw new InvalidArgumentException(sprintf('Splice file not readable: %s', $splice));
            }
            
            $soxi = $this->soxiFactory->getInstance($splice);

            if ($soxi->getDuration() != 6)
            {
                throw new InvalidArgumentException(sprintf('Splice file duration must be 6 seconds: ', $splice));
            }

            if ($soxi->getSampleRate() != 44100)
            {
                throw new InvalidArgumentException(sprintf('Splice file must have sampling-rate of 44100: ', $splice));
            }
        }

        $spliceLength = 6;
        $jobId = uniqid();
        $cleanInFile = sprintf("%s/%s-in.wav", $this->tmpDir, $jobId);
        $this->soxRunner->execute(sprintf("%s -r 44100 -t wav %s", $inFile, $cleanInFile));
        
        $soxi = $this->soxiFactory->getInstance($cleanInFile);
        $duration = $soxi->getDuration();
        $numParts = floor($duration / $partLength);
        $tailLength = $duration - $numParts * $partLength;
        $partStart = 0;
        $parts = array();

        if ($numParts == 0)
        {
            // too short - use as is
            rename($cleanInFile, $outFile);
            return;
        }
        else if ($numParts == 1)
        {
            if ($tailLength > $partLength / 2)
            {
                // make 2 parts with shorter partLength
                $partLength = floor($duration / 2);
                $numParts = floor($duration / $partLength);
                $tailLength = $duration - $numParts * $partLength;
            }

            // use directly with part > partLength
            rename($cleanInFile, $outFile);
            return;
        }

        $makePartName = function($dir, $jobId, $num)
        {
            return sprintf("%s/%s-part-%s.wav", $dir, $jobId, $num);
        };

        for($x = 1; $x < $numParts; $x++)
        {
            $parts[$x] = $makePartName($this->tmpDir, $jobId, $x);
            $this->trimAudio($cleanInFile, $parts[$x], $partStart, $partLength);
            $partStart = $partLength * $x;
        }

        if ($tailLength > $partLength / 2)
        {
            # trim last full part and tail as separate pieces
            $parts[$numParts] = $makePartName($this->tmpDir, $jobId, $numParts);
            $this->trimAudio($cleanInFile, $parts[$numParts], $partStart, $partLength);
            $partStart = $partLength * $numParts;
            $numLastPart = $numParts + 1;
            
            $parts[$numLastPart] = $makePartName($this->tmpDir, $jobId, $numLastPart);
            $this->trimAudio($cleanInFile, $parts[$numLastPart], $partStart, $tailLength);
            $numParts++;
        }
        else {
            # make one piece from last full part and tail
            $parts[$numParts] = $makePartName($this->tmpDir, $jobId, $numParts);
            $lastPartLength = $partLength + $tailLength;
            $this->trimAudio($cleanInFile, $parts[$numParts], $partStart, $lastPartLength);
        }

        $splicedParts = array();

        foreach ($parts as $key => $part)
        {
            if ($key < count($parts))
            {
                $splice = $splices[rand(0, count($splices) - 1)];
                $splicedParts[$key] = sprintf("%s/%s-spliced-%s.wav", $this->tmpDir, $jobId, $key);
                $this->spliceAudio($parts[$key], $splice, $splicedParts[$key], $partLength, $excess);
            }
        }

        #print_r($parts);
        #print_r($splicedParts);

        $tmpFileOne = sprintf("%s/%s-tmp-one.wav", $this->tmpDir, $jobId);
        $tmpFileTwo = sprintf("%s/%s-tmp-two.wav", $this->tmpDir, $jobId);

        if (count($splicedParts) > 1)
        {
            foreach ($splicedParts as $key => $part)
            {
                if ($key === 1)
                {
                    $nextKey = $key + 1;
                    $currentLength = ($partLength + $spliceLength) - 2 * $excess; 
                    $this->spliceAudio($splicedParts[$key], $splicedParts[$nextKey], $tmpFileOne, $currentLength, $excess);
                    copy($tmpFileOne, $tmpFileTwo);
                }
                else if ($key > 2 && $key <= count($splicedParts))
                {
                    $soxi = $this->soxiFactory->getInstance($tmpFileTwo);
                    $currentLength = $soxi->getDuration();
                    $this->spliceAudio($tmpFileTwo, $splicedParts[$key], $tmpFileOne, $currentLength, $excess);
                    copy($tmpFileOne, $tmpFileTwo);
                }
            }

            #print count($parts)."\n";
            $soxi = $this->soxiFactory->getInstance($tmpFileTwo);
            $currentLength = $soxi->getDuration();
            $lastPart = count($parts);
            $this->spliceAudio($tmpFileTwo, $parts[$lastPart], $tmpFileOne, $currentLength, $excess);
            unlink($tmpFileTwo);
        }
        else {
            $onlyPartWithNoise = $splicedParts[count($splicedParts)];
            $soxi = $this->soxiFactory->getInstance($onlyPartWithNoise);
            $currentLength = $soxi->getDuration();
            $lastPart = count($parts);
            $this->spliceAudio($onlyPartWithNoise, $parts[$lastPart], $tmpFileOne, $currentLength, $excess);
        }

        rename($tmpFileOne, $outFile);
        unlink($cleanInFile);

        foreach ($parts as $part)
        {
            unlink($part);
        }
        
        foreach ($splicedParts as $splicedPart)
        {
            unlink($splicedPart);
        }
    }

    protected function trimAudio($inFile, $part, $partStart, $partLength)
    {
        $this->soxRunner->execute(sprintf("%s %s trim %s %s", $inFile, $part, $partStart, $partLength));
    }

    protected function spliceAudio($inFile, $spliceFile, $outFile, $length, $excess)
    {
        $this->soxRunner->execute(sprintf("%s %s %s splice %s,%s", $inFile, $spliceFile, $outFile, $length, $excess));
    }
}