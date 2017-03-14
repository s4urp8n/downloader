<?php
namespace Zver;

use Zver\Common;

class Downloader
{

    public static function download($source, $destination)
    {
        $exception = null;

        try {

            $options = [
                "ssl" => [
                    "verify_peer"      => false,
                    "verify_peer_name" => false,
                ],
            ];

            $content = file_get_contents(urldecode($source), false, stream_context_create($options));

            if (!empty($content)) {

                $writeResult = file_put_contents(urldecode($destination), $content, LOCK_EX);

                if ($writeResult !== false) {
                    return true;
                }

            }
        }
        catch (\Exception $e) {

        }

        return false;
    }

}