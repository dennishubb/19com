<?php
	include __DIR__.'/../common/Medoo.php';
	include __DIR__.'/../common/function.php';
    include __DIR__.'/../config/config.php';

	$sub_folder = date('mY');

	$output_folder = '/upload/output/' . $sub_folder;

	if (!is_dir($root_folder.$output_folder)) {
		mkdir($root_folder.$output_folder, 0777, true);
	}

	$exp = "/<a href=\"(.*?)(\.mp4)\"(.*?)>(.*?)<\/a>/i";

    $video = $database->select('article', ['id', 'content'], ['m3u8' => 2, 'ORDER' => ['active_at' => 'ASC'], 'LIMIT' => 5]);

    if ($video) {
        foreach ($video as $key => $value) {
            $video_id = $value['id'];
            $video_data = array();
            $video_data['m3u8'] = 3; // in progress
            $database->update('article', $video_data, ['id' => $video_id]);
        }
    	foreach ($video as $key => $value) {
            $video_id = $value['id'];
    		preg_match_all($exp, $value['content'], $matches_video);
    		foreach ($matches_video[4] as $video_url) {
    			$full_video_url = $root_folder . urldecode($video_url);
                if (is_file($full_video_url)) {
                    $file_name = md5_file($full_video_url);
                    $save_to = $root_folder . $output_folder . '/' . $file_name.".m3u8";
                    $command = 'ffmpeg -i "'.$full_video_url.'" -force_key_frames "expr:gte(t,n_forced*4)" -c:v libx264 -hls_time 4 -hls_list_size 0 -c:a aac -strict -2 -f hls '.$save_to.' 2>&1';
                    shell_exec($command);

                    if (!$database->get('video_chunk_index', 'id', ['id' => $file_name])) {
                        $chunk_data = array();
                        $poster_save_to = $root_folder . $output_folder . '/' . $file_name.".jpg";
                        $size = '640x360';
                        shell_exec("ffmpeg -i \"{$full_video_url}\" -deinterlace -an -ss 5 -f mjpeg -t 1 -r 1 -y -s {$size} {$poster_save_to} 2>&1");

                        $chunk_data['id'] = $file_name;
                        $chunk_data['path'] = $output_folder . '/' . $file_name.'.m3u8';
                        $chunk_data['poster'] = $output_folder . '/' . $file_name.'.jpg';
                        $database->insert('video_chunk_index', $chunk_data);
                    }
                }
    		}
            $video_data = array();
    		$video_data['media_type'] = 2;
    		$video_data['m3u8'] = 4;
    		$database->update('article', $video_data, ['id' => $video_id]);
    	}
    }
?>