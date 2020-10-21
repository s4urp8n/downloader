<?php

class DownloaderTest extends PHPUnit\Framework\TestCase
{

    use \Zver\Package\Helper;

    /**
     * PLEASE KEEP THIS ARRAY ORDERED FROM SMALL TO BIG
     * SMALLER FIRST - BIGGER LAST
     *
     * @var array
     */
    protected static $files = [
        "https://speedtest.selectel.ru/10MB",
        "https://speedtest.selectel.ru/100MB",
    ];

    protected static $badFiles = [
        "ftp://de231dqwdqdmo:passwo12asdsadrd@test.rebex.net:21/readme.txt",
        "",
        "http://",
        "_______239fdjw8e9fjwefewfwef",
        "d23dsdf3f23f23f",
        "https://www.lacisoft.com/blog/wp-content/uploads/2012/01/php22321342-642x350.png",
        "http://cdn.freebiesbug.com/wp-content/uploads/2017/03/cheq22423efsdf3ue-11-580x591.jpg",
        "ftp://ftp.dlink.ru/pub/Software/ADSL_QI324sfsdfsdfGs.rar",
        "ftp://ftp.dlink.ru/pub/Software/D-View%206.0%20Service%20Pack%203%23dfsdfsdf20%28SP3%29%206.00.03B19.exe",
        "ftp://ftp.dlink.ru/pub/Software/D-View 6.0 Service Pack 3 (SP3) 623423rwsdfsdf.00.03B19.exe",
        "ftp://ftp.dlink.ru/pub/Software/DLinkTftpSe23sd2223dffdhrthrver.exe",
        "ftp://ftp.dlink.ru/pub/Software/autoupdate/pub/Router/DIR-300A_C1/Firmw2543435345324431asasasdare/20130304_0812_DIR_300A_0.0.2_sdk-master.bin",
        "ftp://ftp.dlink.ru/pub/Software/",
    ];

    public static function setUpBeforeClass(): void
    {
        foreach (static::$files as $file) {
            if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . basename(urldecode($file)))) {
                unlink(__DIR__ . DIRECTORY_SEPARATOR . basename(urldecode($file)));
            }
        }
    }

    public static function tearDownAfterClass(): void
    {
        static::setUpBeforeClass();
    }

    public function testMaxSize()
    {
        foreach (static::$files as $file) {
            $this->assertFalse(
                \Zver\Downloader::download($file, __DIR__ . DIRECTORY_SEPARATOR . basename($file), '5mb')
            );
            $this->assertFalse(
                file_exists(__DIR__ . DIRECTORY_SEPARATOR . basename(urldecode($file)))
            );
        }
    }

    public function testDownloads()
    {
        foreach (static::$files as $file) {

            $this->assertTrue(
                \Zver\Downloader::download($file, __DIR__ . DIRECTORY_SEPARATOR . basename($file)),
                'Can\'t download ' . $file
            );

            $this->assertTrue(
                file_exists(__DIR__ . DIRECTORY_SEPARATOR . basename(urldecode($file))),
                'File ' . $file . ' is not exists'
            );
        }

        foreach (static::$badFiles as $file) {
            $this->assertFalse(
                \Zver\Downloader::download($file, __DIR__ . DIRECTORY_SEPARATOR . basename($file)),
                'File [' . $file . '] downloaded but THIS IS ERROR!'
            );
        }

    }

    public function testRandomName()
    {
        $file = static::$files[0];

        $downloadName = '32dd3fwefsdfdsfdgsdxcnw3r8wef.exe';

        $this->assertTrue(\Zver\Downloader::download($file, __DIR__ . DIRECTORY_SEPARATOR . $downloadName));
        $this->assertTrue(file_exists(__DIR__ . DIRECTORY_SEPARATOR . $downloadName));

        @unlink(__DIR__ . DIRECTORY_SEPARATOR . $downloadName);
    }

}