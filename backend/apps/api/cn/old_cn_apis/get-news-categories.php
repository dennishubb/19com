<?php
do {

    $categories = Collection::getNewsCategories($dbc);

    foreach ($categories as $category) {
        $item = array();
        $item['id'] = $category->getId();
        $item['rank'] = $category->getRank();
        $item['title'] = $category->getTitle();

        $response['categories'][] = $item;
    }

} while (0);
