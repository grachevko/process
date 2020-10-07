<?php

include \dirname(__DIR__) . "/vendor/autoload.php";

use Amp\Process\Process;
use function Amp\async;
use function Amp\await;
use function Amp\Promise\all;

function show_process_output(Process $process): void
{
    $process->start();

    $stream = $process->getStdout();

    while (null !== $chunk = $stream->read()) {
        echo $chunk;
    }

    $code = $process->join();
    $pid = $process->getPid();

    echo "Process {$pid} exited with {$code}\n";
}

$hosts = ['8.8.8.8', '8.8.4.4', 'google.com', 'stackoverflow.com', 'github.com'];

$promises = [];

foreach ($hosts as $host) {
    $command = \DIRECTORY_SEPARATOR === "\\"
        ? "ping -n 5 {$host}"
        : "ping -c 5 {$host}";
    $process = new Process($command);
    $promises[] = async(fn() => show_process_output($process));
}

await(all($promises));
