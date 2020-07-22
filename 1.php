<?php

// set up signal handler
function sig_handler(int $signo) {
	switch ($signo) {
		case SIGALRM:
			echo "SIGALRM received\n";
			break;
		case SIGVTALRM:
			echo "SIGVTALRM received\n";
			break;
		case SIGPROF:
			echo "SIGPROF received\n";
			break;
		default:
			echo "{$signo} received\n";
			exit;
	}
}

if (!pcntl_signal(SIGALRM, "sig_handler")) {
	echo "pcntl_signal(SIGALRM) failed!\n";
	exit;
}
if (!pcntl_signal(SIGVTALRM, "sig_handler")) {
	echo "pcntl_signal(SIGVTALRM) failed!\n";
	exit;
}
if (!pcntl_signal(SIGPROF, "sig_handler")) {
	echo "pcntl_signal(SIGPROF) failed!\n";
	exit;
}


// set up ITIMER_VIRTUAL to expire every 0.1 sec
if (pcntl_setitimer(ITIMER_VIRTUAL, 0.1, 0.1) == -1) {
	echo "pcntl_setitimer() failed!\n";
	exit;
}

// set up ITIMER_PROF to expire every 1 sec
// note how the optional parameters are being used here to retrieve old_value
if (pcntl_setitimer(ITIMER_PROF, 1, 1, $it_interval, $it_value) == -1) {
	echo "pcntl_setitimer() failed!\n";
	exit;
} else {
	echo "pcntl_setitimer(ITIMER_PROF), old_it_interval = {$it_interval}, old_it_value = {$it_value}\n";
}

// validate setup
if (pcntl_getitimer(ITIMER_VIRTUAL, $it_interval, $it_value) == -1) {
	echo "pcntl_getitimer() failed!\n";
	exit;
} else {
	echo "pcntl_getitimer(ITIMER_VIRTUAL), it_interval = {$it_interval}, it_value = {$it_value}\n";
}
if (pcntl_getitimer(ITIMER_PROF, $it_interval, $it_value) == -1) {
	echo "pcntl_getitimer() failed!\n";
	exit;
} else {
	echo "pcntl_getitimer(ITIMER_PROF), it_interval = {$it_interval}, it_value = {$it_value}\n";
}
echo "\n";

// loop and handle signals
$start = microtime(true);
while (true) {
	echo "Elapsed: ", microtime(true) - $start, PHP_EOL;

	for($i=0; $i<10000000; $i++);

	if (!pcntl_signal_dispatch()) {
		echo "pcntl_signal_dispatch() failed!\n";
		exit;
	}
	echo "\n";
}

?>
