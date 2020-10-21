<?php

class HumanSizeTest extends PHPUnit\Framework\TestCase
{
    public function getTestData()
    {
        return [
            [true, 0, true],
            ['true', 0, true],
            [0, 0, false],
            [12, 12, false],
            [-12, 12, false],
            ['12b', 12, false],
            ['12 b', 12, false],
            [' 1222 B ', 1222, false],
            [' -1222 B ', 1222, false],
            [' - 1222 B ', 1222, false],
            ['12kb', 12 * 1024, false],
            ['12 kb', 12 * 1024, false],
            ['12 k b', 12 * 1024, false],
            [' 12 k b', 12 * 1024, false],
            ['12mb', 12 * pow(1024, 2), false],
            ['12gb', 12 * pow(1024, 3), false],
            ['12tb', 12 * pow(1024, 4), false],
            ['12Tb', 12 * pow(1024, 4), false],
            ['12pb', 12 * pow(1024, 5), false],
            ['12eb', 12 * pow(1024, 6), false],
            ['12zb', 12 * pow(1024, 7), false],
            ['12zB', 12 * pow(1024, 7), false],
            ['12ZB', 12 * pow(1024, 7), false],
            ['12yb', 12 * pow(1024, 8), false],
            ['5mb', 5 * pow(1024, 2), false],
            ['5mB', 5 * pow(1024, 2), false],
            ['5MB', 5 * pow(1024, 2), false],
            ['5gb', 5 * pow(1024, 3), false],
            ['5tb', 5 * pow(1024, 4), false],
            ['5Tb', 5 * pow(1024, 4), false],
            ['5pb', 5 * pow(1024, 5), false],
            ['5eb', 5 * pow(1024, 6), false],
            ['5zb', 5 * pow(1024, 7), false],
            ['5zB', 5 * pow(1024, 7), false],
            ['5ZB', 5 * pow(1024, 7), false],
            ['5yb', 5 * pow(1024, 8), false],
            ['1234567yb', 1234567 * pow(1024, 8), false],
        ];
    }

    /**
     *
     * @dataProvider getTestData
     * @param $input
     * @param $output
     * @param $exception
     */
    public function testHumanSize($input, $output, $exception)
    {
        $excepted = false;
        try {
            $sizeInBytes = \Zver\Downloader::getFileLenghtInBytes($input);
        }
        catch (\Throwable $e) {
            $excepted = true;
        }
        $this->assertEquals($excepted, $exception);
        if (!$exception) {
            $this->assertEquals($sizeInBytes, $output);
        }
    }

}