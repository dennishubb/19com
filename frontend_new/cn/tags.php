<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/config/config.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/config/shared_function.php");

$tag = trim(urldecode($_GET['tags']));
if (!$tag) {
	exit(0);
}
$page = intval($_GET['page']);
if (!$page) {
	$page = 1;
}
?>
<!DOCTYPE html>
<html lang="zh-hans">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport">
    <meta content="black" name="apple-mobile-web-app-status-bar-style">
    <meta content="telephone=no" name="format-detection">
    <meta content="email=no" name="format-detection">
    <?php include_once('layout/resource.php'); ?>
    <title>19资讯 - <?php echo $tag; ?></title>
</head>
<body>
<?php include 'layout/header.php'; ?>

<div class="main_area">

    <div id="article_tags">
    	<div class="index_p5 layout1200">
		    <div class="title_area"><span id="title_area"><?php echo $tag; ?></span></div>
		    <div class="category_area sub_tags">
		        <div class="category_item_area">
		            <?php
		                $access_url = CURL_API_URL . '/service/news.php?action=get_tag_news&tag='.$tag.'&page='.$page;
		                $data = get_curl($access_url);
		                $data = json_decode($data, true);
		                $html = '';

		                foreach ($data['data'] as $key => $value) {
		                    $html .= '<a class="category_item" href="/cn/article.php?id='.$value['id'].'">';
			                $html .= '<img src="'.$value['thumbnail_small2'].'" style="width: 200px;height: 115px;">';
			                $html .= '<div>';
			                $html .= '<div class="text">';
			                $html .= $value['title'];
			                $html .= '</div>';
			                $html .= '<div class="sub_text">';
			                $html .= $value['active_at'].'&nbsp;'.$value['category'];
			                $html .= '</div>';
			                $html .= '</div>';
			            	$html .= '</a>';
		                }

		                echo $html;
		            ?>
		        </div>
		    </div>
		</div>

		<div class="pagination_area layout1200">
			<?php
				$total_page = $data['total_page'];
			?>
		    <nav aria-label="Page navigation example">
		        <ul class="pagination">
		            <li class="page-item">
		                <a class="page-link page-pagination" href="/cn/tags.php?tags=<?php echo $tag; ?>&page=<?php echo ($page > 1 ? $page - 1 : 1); ?>" aria-label="Previous">
		                    <span aria-hidden="true">«</span>
		                </a>
		            </li>
		            <?php for ($i=1; $i <= $total_page; $i++) { ?>
					<li class="page-item page-pagination <?php echo ($page == $i ? 'active' : ''); ?>"><a class="page-link" href="/cn/tags.php?tags=<?php echo $tag; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
		            <?php } ?>
		            <li class="page-item">
		                <a class="page-link page-pagination" href="/cn/tags.php?tags=<?php echo $tag; ?>&page=<?php echo ($page < $total_page ? $page + 1 : $total_page); ?>" aria-label="Next">
		                    <span aria-hidden="true">»</span>
		                </a>
		            </li>
		        </ul>
		    </nav>
		</div>
	</div>

    <div class="index_p6 layout1200">
        <div class="more_news_list_area">
            <div class="title_area "><span>更多新闻</span></div>
        	<?php 
                $access_url = CURL_API_URL . '/service/news.php?action=get_latest_news&limit=30';
                $data = get_curl($access_url);
                $data = json_decode($data, true);
                $html = '';

                $total = count($data);

                $num = 0;

                foreach ($data as $key => $value) {
                    if ($num % 10 == 0) {
                        $html .= '<div class="list">';
                    }

                	$html .= '<a class="list_item" href="/cn/article.php?id='.$value['id'].'"><div>'.$value['title'].'</div><div>'.date('Y-m-d', strtotime($value['active_at'])).'</div></a>';

                    if ($num % 10 == 9 || $num == ($total - 1)) {
                        $html .= '</div>';
                    }
                    $num++;
                }
                echo $html;
        	?>
        </div>
    </div>
</div>
<?php include 'layout/footer.php'; ?>

<script type="text/javascript">

</script>

</body>
</html>
