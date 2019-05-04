<?php
    // Get params
    $station = $_GET['station'];
    $region = $_GET['region'];

    $wakaStationInfo = json_decode(file_get_contents("https://waka.app/a/{$region}/station/{$station}"), true);
    $wakaStationTimetable = json_decode(file_get_contents("https://waka.app/a/{$region}/station/{$station}/times"), true);

    $frameArray = array(
        array(
            'text' => $wakaStationInfo['stop_name'],
            'icon' => 'i2451'
        )
    );

    $currentTime = $wakaStationTimetable['currentTime'];

    foreach ($wakaStationTimetable['trips'] as $trip) {
        $time = ($trip['departure_time_seconds'] % 86400) - $currentTime;
        if ($time > 0 && $time <= 3600) {
            // if this trip is in the future...
            $frame = array(
                'text' => "{$trip['route_short_name']}: {$trip['trip_headsign']}",
                'icon' => ($trip['direction_id'] == 1 ? 'a6588' : 'a6590') // going out or in
            );

            // Goal screen
            $goalFrame = array(
                'goalData' => array(
                    'start' => 0,
                    'current' => $time/60,
                    'end' => 60,
                    'unit' => 'min'
                )
            );

            array_push($frameArray, $frame, $goalFrame);
        }
    }

    $returnData = array(
        'frames' => $frameArray
    );

    header('Content-Type: application/json');
    echo(json_encode($returnData));
?>