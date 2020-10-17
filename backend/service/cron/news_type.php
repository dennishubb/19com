<?php
	include __DIR__.'/../common/Medoo.php';
  include __DIR__.'/../common/function.php';
  include __DIR__.'/../config/config.php';
	include __DIR__.'/../common/lib_image_imagick.php';

    // $datas_new = $database->select('article', ['id', 'content', 'active_at'], ['media_type' => 0, 'ORDER' => ['active_at' => 'DESC'],'LIMIT' => 5]);
  $datas = array();
  $id = 0;
  if (isset($argv[1])) {
    $id = intval($argv[1]);
  }

  if ($id > 0) {
    $datas = $database->select('article', ['id', 'content', 'active_at', 'tags'], ['id' => $id]);
  }
  else {
    $current_time = date('Y-m-d H:i');
    $last_min = date('Y-m-d H:i', strtotime($current_time) - 60);

    $datas = $database->select('article', ['id', 'content', 'active_at', 'tags'], ['updated_at[>=]' => $last_min, 'updated_at[<]' => $current_time, 'ORDER' => ['active_at' => 'DESC']]);
  }

	$exp = "/<a href=\"(.*?)(\.mp4)\"(.*?)>(.*?)<\/a>/i";

	$exp2 = "/<img src=\"(.*?)(\.gif|\.jpg|\.png|\.jpeg)\"(.*?)>/i";

	$exp3 = "/\"(.*?)\"/";

	$url_preg = "/^http(s)?:\\/\\/.+/";

	$table = 'article';

	$m3u8 = 1;

	$image_size_arr = array(
		"small" => array(120, 80),
		"small2" => array(200, 115),
    	"medium" => array(200, 133),
    	"medium4" => array(290, 211),
		"big" => array(680, 383),
		'small_h5' => array(106, 71),
		'medium_h5' => array(187, 123),
		'big_h5' => array(400, 225),
	);

	$video_size_arr = array(
		"small" => array(120, 80),
		"small2" => array(200, 115),
		"medium" => array(200, 133),
		"medium2" => array(384, 216),
		"medium3" => array(572, 322),
    	"medium4" => array(290, 211),
		"big" => array(680, 383),
		'small_h5' => array(106, 71),
		'medium_h5' => array(187, 123),
		'big_h5' => array(400, 225),
	);

	$sub_folder = date('mY');
	$thumbnail_folder = '/upload/thumbnail/' . $sub_folder;

	if (!is_dir($root_folder.$thumbnail_folder)) {
		mkdir($root_folder.$thumbnail_folder, 0777, true);
	}

	foreach ($datas as $key => $value) {
		$database->delete('article_tags', ['article_id' => $value['id']]);
		preg_match_all($exp3, $value['tags'], $matches_tags);
		foreach ($matches_tags[1] as $tag_value) {
			$database->insert('article_tags', ['tag' => $tag_value, 'article_id' => $value['id']]);
		}

		$data['media_type'] = 0;
		$data['thumbnail_small'] = '';
		$data['thumbnail_small2'] = '';
		$data['thumbnail_medium'] = '';
		$data['thumbnail_medium2'] = '';
	    $data['thumbnail_medium3'] = '';
	    $data['thumbnail_medium4'] = '';
		$data['thumbnail_big'] = '';
		$data['thumbnail_small_h5'] = '';
		$data['thumbnail_medium_h5'] = '';
		$data['thumbnail_big_h5'] = '';

		preg_match_all($exp, $value['content'], $matches_video);
		if (count($matches_video[0]) > 0) {
			// $data['media_type'] = 2;
      		$m3u8 = 2;
			$video_url = $root_folder . urldecode($matches_video[4][0]);

			$interval = 5;  
			$file_name_small = '/' . md5(microtime(true)).'small_video.jpg';
			$save_to = $root_folder . $thumbnail_folder . $file_name_small;
			$size = $video_size_arr['small'][0].'x'.$video_size_arr['small'][1];
			shell_exec("ffmpeg -i \"$video_url\" -deinterlace -an -ss $interval -f mjpeg -t 1 -r 1 -y -s $size $save_to 2>&1");

			$file_name_small2 = '/' . md5(microtime(true)).'small2_video.jpg';
			$save_to = $root_folder . $thumbnail_folder . $file_name_small2;
			$size = $video_size_arr['small2'][0].'x'.$video_size_arr['small2'][1];
			shell_exec("ffmpeg -i \"$video_url\" -deinterlace -an -ss $interval -f mjpeg -t 1 -r 1 -y -s $size $save_to 2>&1");

			$file_name_medium = '/' . md5(microtime(true)).'medium_video.jpg';
			$save_to = $root_folder . $thumbnail_folder . $file_name_medium;
			$size = $video_size_arr['medium'][0].'x'.$video_size_arr['medium'][1];
			shell_exec("ffmpeg -i \"$video_url\" -deinterlace -an -ss $interval -f mjpeg -t 1 -r 1 -y -s $size $save_to 2>&1");

			$file_name_medium2 = '/' . md5(microtime(true)).'medium2_video.jpg';
			$save_to = $root_folder . $thumbnail_folder . $file_name_medium2;
			$size = $video_size_arr['medium2'][0].'x'.$video_size_arr['medium2'][1];
			shell_exec("ffmpeg -i \"$video_url\" -deinterlace -an -ss $interval -f mjpeg -t 1 -r 1 -y -s $size $save_to 2>&1");

			$file_name_medium3 = '/' . md5(microtime(true)).'medium3_video.jpg';
			$save_to = $root_folder . $thumbnail_folder . $file_name_medium3;
			$size = $video_size_arr['medium3'][0].'x'.$video_size_arr['medium3'][1];
			shell_exec("ffmpeg -i \"$video_url\" -deinterlace -an -ss $interval -f mjpeg -t 1 -r 1 -y -s $size $save_to 2>&1");

			$file_name_medium4 = '/' . md5(microtime(true)).'medium4_video.jpg';
			$save_to = $root_folder . $thumbnail_folder . $file_name_medium4;
			$size = $video_size_arr['medium4'][0].'x'.$video_size_arr['medium4'][1];
			shell_exec("ffmpeg -i \"$video_url\" -deinterlace -an -ss $interval -f mjpeg -t 1 -r 1 -y -s $size $save_to 2>&1");

			$file_name_big = '/' . md5(microtime(true)).'big_video.jpg';
			$save_to = $root_folder . $thumbnail_folder . $file_name_big;
			$size = $video_size_arr['big'][0].'x'.$video_size_arr['big'][1];
			shell_exec("ffmpeg -i \"$video_url\" -deinterlace -an -ss $interval -f mjpeg -t 1 -r 1 -y -s $size $save_to 2>&1");

			$file_name_small_h5 = '/' . md5(microtime(true)).'small_h5_video.jpg';
			$save_to = $root_folder . $thumbnail_folder . $file_name_small_h5;
			$size = $video_size_arr['small_h5'][0].'x'.$video_size_arr['small_h5'][1];
			shell_exec("ffmpeg -i \"$video_url\" -deinterlace -an -ss $interval -f mjpeg -t 1 -r 1 -y -s $size $save_to 2>&1");

			$file_name_medium_h5 = '/' . md5(microtime(true)).'medium_h5_video.jpg';
			$save_to = $root_folder . $thumbnail_folder . $file_name_medium_h5;
			$size = $video_size_arr['medium_h5'][0].'x'.$video_size_arr['medium_h5'][1];
			shell_exec("ffmpeg -i \"$video_url\" -deinterlace -an -ss $interval -f mjpeg -t 1 -r 1 -y -s $size $save_to 2>&1");

			$file_name_big_h5 = '/' . md5(microtime(true)).'big_h5_video.jpg';
			$save_to = $root_folder . $thumbnail_folder . $file_name_big_h5;
			$size = $video_size_arr['big_h5'][0].'x'.$video_size_arr['big_h5'][1];
			shell_exec("ffmpeg -i \"$video_url\" -deinterlace -an -ss $interval -f mjpeg -t 1 -r 1 -y -s $size $save_to 2>&1");

			$data['thumbnail_small'] = $thumbnail_folder . $file_name_small;
			$data['thumbnail_small2'] = $thumbnail_folder . $file_name_small2;
			$data['thumbnail_medium'] = $thumbnail_folder . $file_name_medium;
			$data['thumbnail_medium2'] = $thumbnail_folder . $file_name_medium2;
			$data['thumbnail_medium3'] = $thumbnail_folder . $file_name_medium3;
			$data['thumbnail_medium4'] = $thumbnail_folder . $file_name_medium4;
			$data['thumbnail_big'] = $thumbnail_folder . $file_name_big;
			$data['thumbnail_small_h5'] = $thumbnail_folder . $file_name_small_h5;
			$data['thumbnail_medium_h5'] = $thumbnail_folder . $file_name_medium_h5;
			$data['thumbnail_big_h5'] = $thumbnail_folder . $file_name_big_h5;
		}
		else {
			preg_match_all($exp2, $value['content'], $matches_img);
			if (count($matches_img[0]) > 0) {
				$data['media_type'] = 1;
				
				if (preg_match($url_preg, $matches_img[1][0].$matches_img[2][0])) {
					$img_url = $matches_img[1][0].$matches_img[2][0];
				}
				else {
					$img_url = $root_folder . $matches_img[1][0].$matches_img[2][0];
				}
				

				$file_name_small = '/' . md5(microtime(true)).'small'.$matches_img[2][0];
				$save_to = $root_folder . $thumbnail_folder . $file_name_small;
				$imagick = new lib_image_imagick();
				$imagick->open($img_url);
				$imagick->resize_to($image_size_arr['small'][0], $image_size_arr['small'][1], 'center');
				$imagick->save_to($save_to);


				$file_name_small2 = '/' . md5(microtime(true)).'small2'.$matches_img[2][0];
				$save_to = $root_folder . $thumbnail_folder . $file_name_small2;
				$imagick = new lib_image_imagick();
				$imagick->open($img_url);
				$imagick->resize_to($image_size_arr['small2'][0], $image_size_arr['small2'][1], 'center');
				$imagick->save_to($save_to);

		        $file_name_medium = '/' . md5(microtime(true)).'medium'.$matches_img[2][0];
		        $save_to = $root_folder . $thumbnail_folder . $file_name_medium;
		        $imagick = new lib_image_imagick();
		        $imagick->open($img_url);
		        $imagick->resize_to($image_size_arr['medium'][0], $image_size_arr['medium'][1], 'center');
		        $imagick->save_to($save_to);

		        $file_name_medium4 = '/' . md5(microtime(true)).'medium4'.$matches_img[2][0];
		        $save_to = $root_folder . $thumbnail_folder . $file_name_medium4;
		        $imagick = new lib_image_imagick();
		        $imagick->open($img_url);
		        $imagick->resize_to($image_size_arr['medium4'][0], $image_size_arr['medium4'][1], 'center');
		        $imagick->save_to($save_to);

				$file_name_big = '/' . md5(microtime(true)).'big'.$matches_img[2][0];
				$save_to = $root_folder . $thumbnail_folder . $file_name_big;
				$imagick = new lib_image_imagick();
				$imagick->open($img_url);
				$imagick->resize_to($image_size_arr['big'][0], $image_size_arr['big'][1], 'center');
				$imagick->save_to($save_to);

				$file_name_small_h5 = '/' . md5(microtime(true)).'small_h5'.$matches_img[2][0];
				$save_to = $root_folder . $thumbnail_folder . $file_name_small_h5;
				$imagick = new lib_image_imagick();
				$imagick->open($img_url);
				$imagick->resize_to($image_size_arr['small_h5'][0], $image_size_arr['small_h5'][1], 'center');
				$imagick->save_to($save_to);

				$file_name_medium_h5 = '/' . md5(microtime(true)).'medium_h5'.$matches_img[2][0];
				$save_to = $root_folder . $thumbnail_folder . $file_name_medium_h5;
				$imagick = new lib_image_imagick();
				$imagick->open($img_url);
				$imagick->resize_to($image_size_arr['medium_h5'][0], $image_size_arr['medium_h5'][1], 'center');
				$imagick->save_to($save_to);

				$file_name_big_h5 = '/' . md5(microtime(true)).'big_h5'.$matches_img[2][0];
				$save_to = $root_folder . $thumbnail_folder . $file_name_big_h5;
				$imagick = new lib_image_imagick();
				$imagick->open($img_url);
				$imagick->resize_to($image_size_arr['big_h5'][0], $image_size_arr['big_h5'][1], 'center');
				$imagick->save_to($save_to);

				$data['thumbnail_small'] = $thumbnail_folder . $file_name_small;
				$data['thumbnail_small2'] = $thumbnail_folder . $file_name_small2;
				$data['thumbnail_medium'] = $thumbnail_folder . $file_name_medium;
				$data['thumbnail_medium2'] = '';
		        $data['thumbnail_medium3'] = '';
		        $data['thumbnail_medium4'] = $thumbnail_folder . $file_name_medium4;
				$data['thumbnail_big'] = $thumbnail_folder . $file_name_big;
				$data['thumbnail_small_h5'] = $thumbnail_folder . $file_name_small_h5;
				$data['thumbnail_medium_h5'] = $thumbnail_folder . $file_name_medium_h5;
				$data['thumbnail_big_h5'] = $thumbnail_folder . $file_name_big_h5;
			}
			else {
				$data['media_type'] = 3;
			}
		}
		$where['id'] = $value['id'];
	    $data['active_timestamp'] = strtotime($value['active_at']);
	    $data['m3u8'] = $m3u8;
		$database->update($table, $data, $where);
	}

?>