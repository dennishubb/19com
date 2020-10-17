<?php
	include __DIR__.'/../common/Medoo.php';
	include __DIR__.'/../common/function.php';
    include __DIR__.'/../config/config.php';
	include __DIR__.'/../common/lib_image_imagick.php';


	$sub_folder = date('mY');
	$thumbnail_folder = '/upload/user_thumbnail/' . $sub_folder;
	
	if (!is_dir($root_folder.$thumbnail_folder)) {
		mkdir($root_folder.$thumbnail_folder, 0777, true);
	}

    $user_list = $database->select('user_thumbnail_queue', ['id', 'user_id', 'upload_id'], ['status' => 1, 'ORDER' => ['created_at' => 'ASC'], 'LIMIT' => 10]);

    foreach ($user_list as $key => $value) {
    	$database->update('user_thumbnail_queue', ['status' => 2], ['id' => $value['id']]);
    }

    foreach ($user_list as $key => $value) {
    	$thumbnail = '';
    	if ($value['upload_id'] == 0) {
    		$thumbnail = '/assets/images/default_user_image.png';
    	}
    	else {
    		$file_path = '/' . $database->get('upload', 'url', ['id' => $value['upload_id']]);
    		if (is_file($root_folder.$file_path)) {

    			$img_url = $root_folder.$file_path;

    			$pathinfo = pathinfo($img_url);

				$file_name = '/' . md5(microtime(true)).'.'.$pathinfo['extension'];
				$save_to = $root_folder . $thumbnail_folder . $file_name;
				$imagick = new lib_image_imagick();
				$imagick->open($img_url);
				$imagick->resize_to(94, 94, 'center');
				$imagick->save_to($save_to);

				$thumbnail = $thumbnail_folder . $file_name;
    		}
    		else {
    			$thumbnail = '/assets/images/default_user_image.png';
    		}
    	}

    	$data['thumbnail'] = $thumbnail;

    	$database->update('user_thumbnail_queue', ['status' => 3], ['id' => $value['id']]);
    	$database->update('user', $data, ['id' => $value['user_id']]);
    }
?>