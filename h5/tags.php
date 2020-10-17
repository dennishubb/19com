<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title>19.com</title>
<?php
	include_once($_SERVER['DOCUMENT_ROOT'] . "/config/config.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . "/config/shared_function.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . '/common/function.php');
    include("style_script.php");
?>
</head>

<body>
	<?php include 'header.php'; ?>
    <div class="main_container">
        <div class="body_container">
           
            <!-- Start Landing Latest News -->
            <session>
                 <?php 
					$tag = trim(urldecode($_GET['tag']));
					if (!$tag) {
						exit(0);
					}
					$page = 1;
					if (isset($_GET['page'])) {
						$page = intval($_GET['page']);
					}
					
				?>
				<div class="session_block" style="border-top: 7px solid #f5f5f5;">
                    <div class="session_block_title"><?php echo $tag; ?> </div>
                    <div class="break_line"></div>

                    <div class="index_latestnews_thumb_list" id='tag_list'>
                        
                    </div>
                </div>
            </session>
            <!-- End Landing Latest News -->
            
        </div>
    </div>

    <?php
        include("footer.php");
    ?>

   
</body>
</html>

<script>
var page=<?php echo $page;?>;
getTag(0);

function getTag(autoScroll){
	var euid = Cookies.get("euid");
	var tag="<?php echo $tag ?>"; 
	
	
	var data={"action":"get_tag_news","euid":euid};
	data.tag=tag;
	data.page=page;
	
	$.ajax({
		url: getBackendHost() + '/service/news.php',
		type: 'get',
		data: data,
		dataType: "json",
		crossDomain: true,
		xhrFields: {
			withCredentials: true
		},
		//async: false,
		success: function (response, status, xhr) {
			
			if (xhr.status == 200) {
					//console.log(page,response)
					var html = template.render($("#tag_tpl").html(), {"data": response.data});	
					
					if (autoScroll==0)//if not from auto scroll
						$("#tag_list").html(html);
					else //if from auto scroll
						$("#tag_list").append(html);
					
					if($.trim( $('#tag_list').html()).length==0 && page==1 && autoScroll==0)
							$("#tag_list").html('无记录');
					
				}
				else if (xhr.status == -201) {
					Cookies.remove('euid');
				}
				else {
					alert(response.message);
				}
		  },
		  error: function(res) {
			
		  }
		})

	
}

var winH = $(window).height();
	var scrollHandler = function () {
			var pageH = $(document.body).height();
			var scrollT = $(window).scrollTop();
			var aa = (pageH - winH - scrollT) / winH;
			
			if (aa < 0.02) {
				page++;
				getTag(1);
			}
		}
		$(window).scroll(scrollHandler);
</script>

<script type="text/html" id="tag_tpl">
	{{each data value index }}
		<a href="article.php?id={{value.id}}">
            <img src="{{value.thumbnail_small_h5}}">
                <div>
                    <div class="title">{{value.title}}</div>
                    <div class="datetime">{{value.active_at}} {{value.category}}</div>
                </div>
        </a>
		
	{{ /each }}
</script>