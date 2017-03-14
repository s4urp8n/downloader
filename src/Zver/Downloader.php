<?php
namespace Zver;

use Zver\Common;

class Downloader
{

    protected static function downloadHttp($source, $destination, $connectionTimeout, $parsed)
    {
        $options = [
            "ssl" => [
                "verify_peer"      => false,
                "verify_peer_name" => false,
            ],
        ];

        try {
            if ($sourceHandle = fopen($source, 'rb', null, stream_context_create($options))) {
                $downloaded = false;

                if (file_put_contents($destination, $sourceHandle, LOCK_EX) !== false) {
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

    protected static function downloadFtp($source, $destination, $connectionTimeout, $parsed)
    {

        try {

            $ftpPort = isset($parsed['port']) ? $parsed['port'] : 21;
            $ftpUser = isset($parsed['user']) ? $parsed['user'] : 'anonymous';
            $ftpPass = isset($parsed['pass']) ? $parsed['pass'] : '';

            $ftpConnection = ftp_connect($parsed['host'], $ftpPort, $connectionTimeout);

            if (is_resource($ftpConnection) && ftp_login($ftpConnection, $ftpUser, $ftpPass)) {

                ftp_pasv($ftpConnection, true);

                $downloaded = ftp_get($ftpConnection, $destination, urldecode($parsed['path']), FTP_BINARY);

                ftp_close($ftpConnection);

                return $downloaded;

            }
        }
        catch (\Exception $e) {

        }

        return false;
    }

    public static function download($source, $destination, $connectionTimeout = 300)
    {

        ini_set('default_socket_timeout', (float)$connectionTimeout);

        $directory = dirname(urldecode($destination));

        @mkdir($directory, 0777, true);

        $parsed = parse_url($source);

        if ($parsed !== false && isset($parsed['scheme']) && isset($parsed['host'])) {

            $decodedSource = urldecode($source);
            $decodedDestination = urldecode($destination);

            return in_array($parsed['scheme'], ['ftp', 'ftps'])
                ? static::downloadFtp($decodedSource, $decodedDestination, $connectionTimeout, $parsed)
                : static::downloadHttp($decodedSource, $decodedDestination, $connectionTimeout, $parsed);

        }

        return false;

    }

}