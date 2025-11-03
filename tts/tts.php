<?php
$text = $_POST['text'];

$command = 'wsay "' . $text . '" --voice 2"';

$output = shell_exec($command);
