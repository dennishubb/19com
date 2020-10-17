<?php
    include_once($_SERVER['DOCUMENT_ROOT'] . "/config/config.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . "/config/shared_function.php");
?>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title>19资讯 - 活动专区</title>
<?php
    include("style_script.php");
?>
</head>

<body>
<?php
include("header.php");
?>
    <div class="main_container">

        <div class="body_container promo_bg">

            <div class="promo_container">
                <div class="swiper-container">
                    <div class="swiper-wrapper">
                        <?php 
                            $access_url = CURL_API_URL . '/service/promotion.php?action=promotion_list';
                            $data = get_curl($access_url);
                            $data = json_decode($data, true);
                            $html = '';
                            //echo "<pre>";
                            //print_r($data);
                            //echo "</pre>";
                            foreach ($data as $key => $value) {
                                $html .= '<div class="swiper-slide">';
                                $html .='<a href="' . $value['url'] . '"><img src="' . IMAGE_URL . $value['thumbnail_big'] . '" style="width: 375px;height:254px;"></a>';
                                
                                $html .= '<div class="promo_content">';
                                $html .= '<div class="title">'.$value['name'].'</div>';
                                $html .= '<div class="desc">'.$value['introduction'].'</div>';
                                //$html .= '<div class="btn"><a href="'. $value['url']. '">'."查看".'</a></div>';
                                $html .= '<div class="btn"><a href="promo_details.php?id='.$value['id'].'">'."查看".'</a></div>';
                                $html .= '</div>';
                                $html .= '</div>';
                            }
                            echo $html;
                        ?>
                    </div>
                            <div class="swiper-button-next"></div>
                            <div class="swiper-button-prev"></div>  
                </div>
            </div>

            <div class="long_term_promo">
                <div class=""><img src="<?php echo IMAGE_URL ?>/assets/images/1-380X200.png"></div>
                <div class=""><img src="<?php echo IMAGE_URL ?>/assets/images/2-380X200.png"></div>
                <div class=""><img src="<?php echo IMAGE_URL ?>/assets/images/3-380X200.png"></div>
            </div>

            <script>
            $(function(){
                var swiper = new Swiper('.promo_container .swiper-container', {
                    navigation: {
                        nextEl: '.promo_container .swiper-button-next',
                        prevEl: '.promo_container .swiper-button-prev',
                    },
                });
            })
            </script>

        </div>
    </div>

    <?php
        include("footer.php");
    ?>
</body>
</html>