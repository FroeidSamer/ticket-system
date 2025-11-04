<?php
$text = $_POST['text'];

$command = 'wsay "' . $text . '" --voice 2"';

$output = shell_exec($command);


/*$text='عميل رقم 23';

$path='main.py';

$output_file='audios/test.mp3';

$command="python ".escapeshellarg($path)." ".escapeshellarg($text)." ".escapeshellarg($output_file);

shell_exec($command);

echo json_encode(['status' => 'success', 'audio_path' => $output_file]);*/

?>


