<?php
do {

    $seasons = Collection::getSeason($dbc, $params);

    $response['seasons'] = $seasons;

} while (0);