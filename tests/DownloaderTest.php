<?php

class DownloaderTest extends PHPUnit\Framework\TestCase
{

    use \Zver\Package\Test;

    protected static $files = [
        "https://www.lacisoft.com/blog/wp-content/uploads/2012/01/php2-642x350.png",
        "http://cdn.freebiesbug.com/wp-content/uploads/2017/03/cheque-11-580x591.jpg",
        "ftp://ftp.dlink.ru/pub/Software/ADSL_QIGs.rar",
        "ftp://ftp.dlink.ru/pub/Software/D-View%206.0%20Service%20Pack%203%20%28SP3%29%206.00.03B19.exe",
        "ftp://ftp.dlink.ru/pub/Software/D-View 6.0 Service Pack 3 (SP3) 6.00.03B19.exe",
        "ftp://ftp.dlink.ru/pub/Software/DLinkTftpServer.exe",
        "ftp://ftp.dlink.ru/pub/Software/autoupdate/pub/Router/DIR-300A_C1/Firmware/20130304_0812_DIR_300A_0.0.2_sdk-master.bin",
    ];

    protected static $badFiles = [
        "https://www.lacisoft.com/blog/wp-content/uploads/2012/01/php22321342-642x350.png",
        "http://cdn.freebiesbug.com/wp-content/uploads/2017/03/cheq22423efsdf3ue-11-580x591.jpg",
        "ftp://ftp.dlink.ru/pub/Software/ADSL_QI324sfsdfsdfGs.rar",
        "ftp://ftp.dlink.ru/pub/Software/D-View%206.0%20Service%20Pack%203%23dfsdfsdf20%28SP3%29%206.00.03B19.exe",
        "ftp://ftp.dlink.ru/pub/Software/D-View 6.0 Service Pack 3 (SP3) 623423rwsdfsdf.00.03B19.exe",
        "ftp://ftp.dlink.ru/pub/Software/DLinkTftpSe23sd2223dffdhrthrver.exe",
        "ftp://ftp.dlink.ru/pub/Software/autoupdate/pub/Router/DIR-300A_C1/Firmw2543435345324431asasasdare/20130304_0812_DIR_300A_0.0.2_sdk-master.bin",
    ];

    public static function setUpBeforeClass()
    {
        foreach (static::$files as $file) {
            if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . basename(urldecode($file)))) {
                unlink(__DIR__ . DIRECTORY_SEPARATOR . basename(urldecode($file)));
            }
        }
    }

    public static function tearDownAfterClass()
    {
        static::setUpBeforeClass();
    }

    public function testDownloads()
    {
        foreach (static::$files as $file) {
            $this->assertTrue(\Zver\Downloader::download($file, __DIR__ . DIRECTORY_SEPARATOR . basename($file)));
            $this->assertTrue(file_exists(__DIR__ . DIRECTORY_SEPARATOR . basename(urldecode($file))));
        }

        foreach (static::$badFiles as $file) {
            $this->assertFalse(\Zver\Downloader::download($file, __DIR__ . DIRECTORY_SEPARATOR . basename($file)));
        }

    }

}