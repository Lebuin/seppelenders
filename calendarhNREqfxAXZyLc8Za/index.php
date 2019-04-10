<?php

function get_content($url) {
    $file = fopen($url, 'r');
    $content = '';
    while($file && !feof($file)) {
        $content .= fgets($file, 4096);
    }

    return $content;
}

function prefix($content, $prefix, $first) {
    $old_lines = explode("\r\n", $content);

    // Strip the first and last two lines (BEGIN:VCALENDAR and END:VCALENDAR)
    $old_lines = array_slice($old_lines, 1, count($old_lines) - 3);

    $new_lines = array();
    $events_started = false;

    foreach($old_lines as $line) {

        // Strip lines that are not events
        if(strpos($line, 'BEGIN:VEVENT') === 0) {
            $events_started = true;
        }
        if(!$events_started && !$first) {
            continue;
        }

        if(strpos($line, 'SUMMARY:') === 0) {
            $summary = substr($line, 8);
            $line = 'SUMMARY:' . $prefix . $summary;
        }

        $parts = str_split($line, 70);
        $num_parts = count($parts);
        $spaces = '';

        foreach($parts as $part) {
            array_push($new_lines, $spaces . $part);
            $spaces = ' ';
        }
    }

    return join("\r\n", $new_lines);
}



$name = $_GET['name'];
switch($name) {
    case 'seppe':
        $urls = array(
            '' => 'https://calendar.google.com/calendar/ical/seppe.lenders%40gmail.com/private-0fad6d2cbac026c1311c2a5f25967e76/basic.ics',
            '(JNM) ' => 'https://calendar.google.com/calendar/ical/allip39gmffdvs6i0hskfq7i7k%40group.calendar.google.com/private-b21c7801622f68df33ba4aaf018b4a64/basic.ics',
            '(werkdag) ' => 'https://calendar.google.com/calendar/ical/vectera.com_865883fn1rf955at75kjpetg4o%40group.calendar.google.com/private-7a8617424c831c6906001dfaca007a3b/basic.ics',
            '(Vectera) ' => 'https://calendar.google.com/calendar/ical/vectera.com_865883fn1rf955at75kjpetg4o%40group.calendar.google.com/private-7a8617424c831c6906001dfaca007a3b/basic.ics',
            '(Biotope) ' => 'https://calendar.google.com/calendar/ical/1ubt4d8hk5h7b6cgbpq6jjh3js%40group.calendar.google.com/private-e49eaf2dd869c6692964ac1a8aed4a30/basic.ics',
        );
        break;


    case 'lauren':
        $urls = array(
            '' => 'http://outlook.office365.com/owa/calendar/7102c6f2d43d498985b37db3f10072ce@bosplus.be/c5a091a2b80046f2903609b497f9cd3310711569703437950280/S-1-8-2009311037-1284090836-1312262278-3785212042/reachcalendar.ics',
            '(BOS+) ' => 'http://outlook.office365.com/owa/calendar/7102c6f2d43d498985b37db3f10072ce@bosplus.be/6bdf9bfc16e3416699182f1efab65f7618375762732277603347/S-1-8-2009311037-1284090836-1312262278-3785212042/reachcalendar.ics'
        );
        break;

    default:
        exit();
}


header('Content-type: text/calendar; charset=utf-8');
header('Content-Disposition: inline; filename=calendar.ics');

$contents = array("BEGIN:VCALENDAR");
$first = true;
foreach($urls as $prefix => $url) {
    $url_content = get_content($url);
    $prefixed_content = prefix($url_content, $prefix, $first);

    array_push($contents, $prefixed_content);
    $first = false;
}
array_push($contents, "END:VCALENDAR");

$content = join("\r\n", $contents);
echo $content;

?>
