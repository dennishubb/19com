<?php
do {

    $categories = Collection::getSportsCategories($dbc);

    foreach ($categories as $category) {
        $item = array();
        $item['id'] = $category->getId();
        $item['rank'] = $category->getRank();
        $item['title'] = $category->getTitle();
        $item['en_title'] = $category->getEnTitle();

        $response['categories'][] = $item;
    }

} while (0);
