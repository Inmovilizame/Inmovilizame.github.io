<?php

class SpeedTest
{
    const COMMAND_SKEL = '/usr/bin/speedtest-cli --simple --server %d';

    private $servers = array(
        3276 => "oviedo",
        890  => "gijon",
        3257 => 'badajoz'
    );

    public function __construct($servers = array())
    {
        if (0 !== count ($servers)) {
            $this->servers = $servers;
        }
    }

    public function runTest()
    {
        $serverNumbers = count($this->servers);
        $data = array(
            'time' => time(),
            'date' => date('Y:m:d H:i:s'),
            'average_down' => 0,
            'average_ping' => 0,
            'average_up' => 0
        );

        foreach ($this->servers as $serverId => $location) {
            $this->testServer($serverId, $location, $data);
        }

        $data['average_down'] = $data['average_down']/$serverNumbers;
        $data['average_ping'] = $data['average_ping']/$serverNumbers;
        $data['average_up']   = $data['average_up']/$serverNumbers;

        return $data;
    }

    private function testServer($serverId, $location, &$data)
    {
        $output = array();
        exec(sprintf(self::COMMAND_SKEL, $serverId), $output);

        foreach ($output as $line) {
            if (false === stripos($line, ':')) {
                continue;
            }

            $dataPart = explode(': ', $line);
            switch ($dataPart[0]) {
                case 'Ping':
                    $data[$location]['ping'] = (float) $dataPart[1];
                    $data['average_ping'] += (float) $dataPart[1];
                    break;
                case 'Download':
                    $data[$location]['down'] = (float) $dataPart[1];
                    $data['average_down'] += (float) $dataPart[1];
                    break;
                case 'Upload':
                    $data[$location]['up'] = (float) $dataPart[1];
                    $data['average_up'] += (float) $dataPart[1];
                    break;
                default:
                    break;
            }
        }

        return $data;
    }
}
