<?php

/*
 * This file is part of the kompakt/audio-snippets package.
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 *
 */

// load testing configuration
require_once (file_exists(__DIR__ . '/config.php')) ? 'config.php' : 'config.php.dist';

// autoloader
require_once dirname(__DIR__) . '/vendor/autoload.php';

// set defaults
date_default_timezone_set('UTC');


// public util functions
function freshTmpSubDir($class)
{
    $tmpSubDir = resolveTmpSubDir($class);
    clearTmpSubDir($tmpSubDir);
    return createTmpSubDir($tmpSubDir);
}

// "private" functions (should not be used from within tests)
function resolveTmpSubDir($classOrMethod)
{
    return TESTS_KOMPAKT_AUDIOSNIPPETS_TEMP_DIR . '/' . preg_replace('/\\\/', '/', preg_replace('/::/', '/', $classOrMethod));
}

function clearTmpSubDir($pathname)
{
    $fileInfo = new \SplFileInfo($pathname);

    if (!$fileInfo->isDir() || !$fileInfo->isReadable() || !$fileInfo->isWritable())
    {
        return;
    }

    foreach (new \DirectoryIterator($pathname) as $fileInfo)
    {
        if ($fileInfo->isDot())
        {
            continue;
        }

        if ($fileInfo->isDir())
        {
            clearTmpSubDir($fileInfo->getPathname());
        }
        else {
            unlink($fileInfo->getPathname());
        }
    }

    rmdir($pathname);
}

function createTmpSubDir($pathname)
{
    $fileInfo = new \SplFileInfo($pathname);

    if (!$fileInfo->isDir())
    {
        mkdir($pathname, 0777, true);
    }

    return $pathname;
}