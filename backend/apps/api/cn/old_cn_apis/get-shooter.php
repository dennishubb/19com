<?php
do {

    $shooters = Collection::getShooters($dbc, $params);

    $response = $shooters;

} while (0);
