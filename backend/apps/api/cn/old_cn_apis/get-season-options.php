<?php
do {

    $options = Collection::getSeasonOptions($dbc, $params);

    $response['season_options'] = $options;

} while (0);