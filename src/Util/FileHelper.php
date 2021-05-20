<?php

use Carbon\Carbon;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class FileHelper
{
    const SNAPSHOTS_PATH = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'snapshots';

    /**
     * Get all files in FILES_DIR
     *
     * @param $directory
     * @return array
     */
    public static function getFiles($directory)
    {
        $files = [];

        $fileSystem = new Filesystem();
        if (!$fileSystem->exists($directory)) {
            return $files;
        }

        $finder = new Finder();
        $finder->files()->in($directory)->sortByChangedTime();

        foreach ($finder as $file) {
            $files[] = [
                'name' => $file->getFilename(),
                'size' => FileHelper::humanFileSize($file->getSize()),
                'date' => Carbon::createFromTimestamp($file->getMTime())
            ];
        }

        return $files;
    }

    /**
     * Output human readable file size
     *
     * @param $bytes
     * @param int $decimals
     * @return string
     */
    public static function humanFileSize($bytes, $decimals = 2)
    {
        $sz = 'BKMGTP';
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
    }

    /**
     * Output human readable bandwidth size
     *
     * @param $bytes
     * @param int $decimals
     * @return string
     */
    public static function humanBandwidth($bytes, $decimals = 2)
    {
        $sz = 'BKMGTP';
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor] . '/s';
    }
}
