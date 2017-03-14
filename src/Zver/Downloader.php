<?php
namespace Zver;

use Zver\Common;

class Downloader
{

    public static function download($source, $destination)
    {
        $exception = null;

        $options = [
            "ssl" => [
                "verify_peer"      => false,
                "verify_peer_name" => false,
            ],
        ];

        $decodedSource = urldecode($source);
        $decodedDestination = urldecode($destination);

        try {

            if ($sourceHandle = fopen($decodedSource, 'rb', null, stream_context_create($options))) {

                $downloaded = false;

                @mkdir(dirname($decodedDestination), 0777, true);

                if (file_put_contents($decodedDestination, $sourceHandle, LOCK_EX) !== false) {
                    $downloaded = true;
                }

                fclose($sourceHandle);

                return $downloaded;
            }
        }
        catch (\Exception $e) {

        }

        return false;
    }

}