<?php

namespace Zver;

class Downloader
{

    public static function isWGETInstalled()
    {
        $output = Common::executeInSystem('wget --version');

        $pattern = '#wget\s+\d+\.\d+#i';

        return (preg_match($pattern, $output) === 1);
    }

    public static function download($source, $destination)
    {

        $directory = dirname(urldecode($destination));

        $parsed = parse_url($source);

        if ($parsed !== false && isset($parsed['scheme']) && isset($parsed['host'])) {

            @mkdir($directory, 0777, true);

            $decodedSource = urldecode($source);
            $decodedDestination = urldecode($destination);

            if (static::isWGETInstalled()) {

                $isDir = false;

                try {
                    $isDir = is_dir($decodedSource);
                }
                catch (\Exception $e) {
                    $isDir = true;
                }

                if (!$isDir) {

                    $exitCode = $output = '';

                    $command = sprintf('wget -O "%s" "%s"', $decodedDestination, $decodedSource);

                    @exec($command, $output, $exitCode);

                    $downloaded = ($exitCode == 0);

                    if (!$downloaded) {
                        @unlink($decodedDestination);
                    }

                    return $downloaded;
                }

            }

        }

        return false;

    }

}