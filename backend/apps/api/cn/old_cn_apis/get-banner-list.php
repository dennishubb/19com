<?php
do {

    $banners = Collection::getBanners($dbc, $params);

    foreach ($banners as $banner) {
        $item = array();
        $item['id'] = $banner->getId();
        $item['rank'] = $banner->getRank();
        $item['date'] = $banner->getShowDate();
        $item['title'] = $banner->getTitle();
        $item['tags'] = $banner->getTagsObject();
        $item['link'] = $banner->getLink();
        $item['image'] = ["normal" => $banner->getImage(), "mobile" => $banner->getSmImage()];

        $response['banners'][] = $item;
    }

} while (0);


