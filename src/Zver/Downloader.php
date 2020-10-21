<?php

namespace Zver;

class Downloader
{

    public static function getFileLenghtInBytes($humanLenght)
    {
        if (is_int($humanLenght) || is_float($humanLenght) || is_numeric($humanLenght)) {
            return abs($humanLenght);
        }
        $humanLenght = preg_replace('#[\s\W]+#', '', strtolower($humanLenght));
        $units = ['b', 'kb', 'mb', 'gb', 'tb', 'pb', 'eb', 'zb', 'yb'];
        foreach ($units as $index => $unit) {
            $pattern = "#^(\d+)" . $unit . "$#";
            $matches = [];
            if (preg_match($pattern, $humanLenght, $matches) === 1) {
                return $matches[1] * pow(1024, $index);
            }
        }
        throw new \InvalidArgumentException('humanLength must be numeric or size string like: 20mb. Provided value is ' .
                                            $humanLenght);
    }

    public static function download($source, $destination, $maxSizeInBytes = 0)
    {
        $directory = dirname(urldecode($destination));
        $parsed = parse_url($source);

        $maxSizeInBytes = static::getFileLenghtInBytes($maxSizeInBytes);

        if (!$parsed || !isset($parsed['scheme']) || !isset($parsed['host'])) {
            return false;
        }

        @mkdir($directory, 0777, true);

        $decodedSource = urldecode($source);
        $decodedDestination = urldecode($destination);

        $isDir = false;
        try {
            $isDir = is_dir($decodedSource);
        }
        catch (\Exception $e) {
            $isDir = true;
        }

        if ($isDir) {
            return false;
        }

        $downloaded = true;

        $curl = curl_init(str_replace(" ", "%20", $decodedSource));
        $fp = fopen($decodedDestination, 'w+');
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        curl_setopt($curl, CURLOPT_FILE, $fp);
        curl_setopt($curl, CURLOPT_BUFFERSIZE, 1024);
        curl_setopt($curl, CURLOPT_NOPROGRESS, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_AUTOREFERER, true);
        curl_setopt($curl, CURLOPT_FAILONERROR, true);
        curl_setopt($curl, CURLOPT_PROGRESSFUNCTION, function ($curl, $totalDownloading, $downloaded) use ($maxSizeInBytes) {
            if ($maxSizeInBytes > 0
                &&
                ($totalDownloading > $maxSizeInBytes || $downloaded > $maxSizeInBytes)
            ) {
                return 1;
            }
            return 0;
        });

        try {
            curl_exec($curl);
        }
        catch (\Throwable $e) {
            $downloaded = false;
        }
        @curl_close($curl);
        @fclose($fp);

        if ($downloaded && filesize($decodedDestination) == 0) {
            $downloaded = false;
        }

        if (!$downloaded) {
            @unlink($decodedDestination);
        }

        return $downloaded;
    }

}