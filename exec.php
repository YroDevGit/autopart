<?php

$output = [];
exec('tasklist /FI "IMAGENAME eq mysqld.exe" 2>NUL', $output);

$isRunning = false;
foreach ($output as $line) {
    if (stripos($line, 'mysqld.exe') !== false) {
        $isRunning = true;
        break;
    }
}

if ($isRunning) {
    echo "";
} else {
    pclose(popen('start "" /B cmd /c "C:\xampp\mysql_start.bat"', 'r'));

    sleep(2);

    $output = [];
    exec('tasklist /FI "IMAGENAME eq mysqld.exe" 2>NUL', $output);

    $started = false;
    foreach ($output as $line) {
        if (stripos($line, 'mysqld.exe') !== false) {
            $started = true;
            break;
        }
    }

    echo $started
        ? "\n✅ MySQL is now running."
        : "\n❌ MySQL may not have started.";
}