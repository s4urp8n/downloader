<?php

namespace Zver;

class Downloader
{

    public static function download($source, $destination, $maxSizeInBytes = 0)
    {

        $directory = dirname(urldecode($destination));

        $parsed = parse_url($source);

        if ($parsed !== false && isset($parsed['scheme']) && isset($parsed['host'])) {

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

            if (!$isDir) {

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
                curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
                curl_setopt($curl, CURLOPT_FAILONERROR, true);

                curl_setopt($curl, CURLOPT_PROGRESSFUNCTION, function (
                    $totalDownloadedBytes,
                    $downloadedBytes,
                    $totalUploadedBytes,
                    $uploadedBytes
                ) use ($maxSizeInBytes, &$downloaded) {

                    if ($maxSizeInBytes > 0 && $downloadedBytes > $maxSizeInBytes) {

                        $downloaded = false;

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
                catch (\Exception $e) {
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

        return false;

    }

}