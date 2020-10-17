<?php

namespace CKSource\CKFinder\Plugin\CustomUpload;

include_once($_SERVER['DOCUMENT_ROOT'] . "/config/config.php");

use CKSource\CKFinder\CKFinder;
use CKSource\CKFinder\Event\AfterCommandEvent;
use CKSource\CKFinder\Event\CKFinderEvent;
use CKSource\CKFinder\Event\DeleteFileEvent;
use CKSource\CKFinder\Event\DeleteFolderEvent;
use CKSource\CKFinder\Event\FileUploadEvent;
use CKSource\CKFinder\Event\MoveFileEvent;
use CKSource\CKFinder\Event\RenameFileEvent;
use CKSource\CKFinder\Event\RenameFolderEvent;
use CKSource\CKFinder\Plugin\PluginInterface;
use CURLFile;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CustomUpload implements PluginInterface, EventSubscriberInterface
{
    protected $app;

    public function setContainer(CKFinder $app)
    {
        $this->app = $app;
    }

    public function getDefaultConfig()
    {
        return [];
    }

    public function fileUpload(FileUploadEvent $event)
    {
        $file = $event->getFile();

        $success = false;

        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . "/upload/media/_temp")) {
            mkdir($_SERVER['DOCUMENT_ROOT'] . "/upload/media/_temp", '0755', true);
        }

        // Temporary Store at "_temp" path under "upload/media"
        $fileName = $_FILES['upload']['name'];
        $dest = $_SERVER['DOCUMENT_ROOT'] . "/upload/media/_temp/" . $fileName;
        file_put_contents($dest, $file->getContents());

        $request = [
            "file" => self::make_curl_file($dest),
            "folder" => substr($_GET['currentFolder'], 1),
        ];
        $meta_response = self::curl_media_meta($request);
        $meta_obj = json_decode($meta_response, true);
        unlink($dest);

        if ($meta_response) {

            $url = str_replace('//', '/', $meta_obj['url']);

            // Delete Existing
            $request = [
                "filter" => [
                    [
                        "field" => "url",
                        "value" => $url,
                        "operator" => "=",
                    ]
                ]
            ];
            $delete_response = self::curl_backend_delete($request);
            $delete_obj = json_decode($delete_response, true);

            // New Upload
            $request = [
                "url" => $url,
                "name" => $meta_obj['name'],
                "md5" => $meta_obj['md5'],
                "type" => $meta_obj['type'],
                "size" => $meta_obj['filesize'],
                "resolution" => $meta_obj['resolution'],
                "extension" => $meta_obj['extension'],
            ];
            $upload_response = self::curl_backend_upload($request);
            $upload_obj = json_decode($upload_response, true);

            if ($upload_obj['code'] == 401) {
                $success = false;
            } else {
                $success = true;
            }
        }

        if ($success) {

            $request = [
                "tempfile" => $meta_obj['extra']
            ];
            $save_response = self::curl_media_save($request);
            $save_obj = json_decode($save_response, true);

            if ($save_response && $save_obj['code']) {

                $response = array(
                    "resourceType" => $_GET['type'],
                    "currentFolder" => array(
                        "path" => "upload/media/" . $_GET['currentFolder']
                    ),
                    "fileName" => $meta_obj['name'],
                    "uploaded" => "1"
                );

                echo json_encode($response);

            }

        } else {
//            unlink($_SERVER['DOCUMENT_ROOT'] . '/' . $meta_obj['url']);

            $error = array(
                "error" => array(
                    "number" => 116,
                    "message" => $upload_obj['message'],
                )
            );

            echo json_encode($error);
        }
        die;

    }

    public function fileDelete(DeleteFileEvent $event)
    {
        $file = $event->getFile();

        $request = [
            "filter" => [
                [
                    "field" => "url",
                    "value" => "upload/media" . $_GET['currentFolder'] . $file->getFilename(),
                    "operator" => "=",
                ]
            ],
        ];

        $delete_response = self::curl_backend_delete($request);
        $delete_obj = json_decode($delete_response, true);

        if ($delete_obj['code'] == 401) {
            $error = array(
                "error" => array(
                    "number" => 116,
                    "message" => $delete_obj['message'],
                )
            );

            echo json_encode($error);
            die;
        }
    }

    public function fileRename(RenameFileEvent $event)
    {
        $file = $event->getFile();

        $request = [
            "filter" => [
                [
                    "field" => "url",
                    "value" => "upload/media" . $_GET['currentFolder'] . $file->getFilename(),
                    "operator" => "=",
                ]
            ],
            "data" => [
                "url" => "upload/media" . $_GET['currentFolder'] . $_GET['newFileName'],
            ],
            "action" => "replace"
        ];

        $put_response = self::curl_backend_put($request);
        $put_obj = json_decode($put_response, true);

        if ($put_obj['code'] == 401) {
            $error = array(
                "error" => array(
                    "number" => 116,
                    "message" => $put_obj['message'],
                )
            );

            echo json_encode($error);
            die;
        }
    }

    public function folderRename(RenameFolderEvent $event)
    {
        $request = [
            "filter" => [
                [
                    "field" => "url",
                    "value" => "%upload/media" . $_GET['currentFolder'] . '%',
                    "operator" => "LIKE",
                ]
            ],
            "data" => [
                "url" => "upload/media/" . $_GET['newFolderName'] . '/',
            ],
            "action" => "replace"
        ];

        $put_response = self::curl_backend_put($request);
        $put_obj = json_decode($put_response, true);

        if ($put_obj['code'] == 401) {
            $error = array(
                "error" => array(
                    "number" => 116,
                    "message" => $put_obj['message'],
                )
            );

            echo json_encode($error);
            die;
        }
    }

    public function afterFileMove(AfterCommandEvent $event)
    {
        $content = json_decode($event->getResponse()->getContent(), true);

        if ($content['moved']) {
            $json_obj = json_decode($_POST['jsonData'], true);

            foreach ($json_obj['files'] as $file) {

                $newFileName = $file['name'];

                switch ($file['options']) {
                    case "overwrite":
                        $request = [
                            "filter" => [
                                [
                                    "field" => "url",
                                    "value" => "upload/media" . $_GET['currentFolder'] . $file['name'],
                                    "operator" => "=",
                                ]
                            ]
                        ];
                        $delete_response = self::curl_backend_delete($request);
                        $delete_obj = json_decode($delete_response, true);
                        $delete_obj['params'] = json_encode($request);
                        break;
                    case "autorename":
                        $i = 1;

                        $extension = pathinfo($_SERVER['DOCUMENT_ROOT'] . "upload/media" . $file['folder'] . $file['name'], PATHINFO_EXTENSION);

                        while (true) {
                            $fileName = str_replace("." . $extension, "", $file['name']);
                            $uploadedFileName = "upload/media" . $_GET['currentFolder'] . $fileName . '(' . $i . ').' . $extension;

                            $file_exist = file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . $uploadedFileName);
                            if (!$file_exist) {
                                $uploadedFileName = "upload/media" . $_GET['currentFolder'] . $fileName . '(' . ($i - 1) . ').' . $extension;
                                break;
                            }

                            $i++;
                        }


                        $newFileName = str_replace("upload/media" . $_GET['currentFolder'], "", $uploadedFileName);
                        break;
                }

                $request = [
                    "filter" => [
                        [
                            "field" => "url",
                            "value" => "upload/media" . $file['folder'] . $file['name'],
                            "operator" => "LIKE",
                        ],
                        [
                            "field" => "name",
                            "value" => $file['name'],
                            "operator" => "=",
                        ]
                    ],
                    "data" => [
                        "url" => "upload/media" . $_GET['currentFolder'] . $newFileName,
                        "name" => $newFileName,
                    ],
                    "action" => "replace"
                ];

                $put_response = self::curl_backend_put($request);
                $put_obj = json_decode($put_response, true);
                $put_obj['params'] = json_encode($request);

                var_dump($put_response);

                if ($put_obj['code'] == 0 || $put_obj['code'] == 401) {
                    $error = array(
                        "error" => array(
                            "number" => 1,
                            "message" => $put_obj['message'],
                            "extra" => $put_response,
                        )
                    );

                    echo json_encode($error);
                    die;
                } else {
                    $file['name'] = $newFileName;
                }

            }
        }
    }

    public function fileMove(MoveFileEvent $event)
    {
    }

    public function folderDelete(DeleteFolderEvent $event)
    {
        $request = [
            "filter" => [
                [
                    "field" => "url",
                    "value" => "%upload/media" . $_GET['currentFolder'] . '%',
                    "operator" => "LIKE",
                ]
            ],
        ];

        $delete_response = self::curl_backend_delete($request);
        $delete_obj = json_decode($delete_response, true);

        if ($delete_obj['code'] == 401) {
            $error = array(
                "error" => array(
                    "number" => 116,
                    "message" => $delete_obj['message'],
                )
            );

            echo json_encode($error);
            die;
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            CKFinderEvent::FILE_UPLOAD => 'fileUpload',
            CKFinderEvent::DELETE_FILE => 'fileDelete',
            CKFinderEvent::RENAME_FILE => 'fileRename',
            CKFinderEvent::RENAME_FOLDER => 'folderRename',
//            CKFinderEvent::MOVE_FILE => 'fileMove',
            CKFinderEvent::AFTER_COMMAND_MOVE_FILES => 'afterFileMove',
            CKFinderEvent::DELETE_FOLDER => 'folderDelete',
        ];
    }

    function make_curl_file($file)
    {
        $mime = mime_content_type($file);
        $info = pathinfo($file);
        $name = $info['basename'];
        $output = new CURLFile($file, $mime, $name);
        return $output;
    }

    public static function curl_media_meta($request_body)
    {
        $url = CURL_FRONTEND_URL . "/assets/php/media-meta.php";
        $headers = array();
        $headers[] = "content-type: multipart/form-data";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request_body);
        $response = curl_exec($ch);

        curl_close($ch);

        return $response;
    }

    public static function curl_media_save($request_body)
    {
        $url = CURL_FRONTEND_URL . "/assets/php/media-save.php";
        $headers = array();
        $headers[] = "content-type: multipart/form-data";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request_body);
        $response = curl_exec($ch);

        curl_close($ch);

        return $response;
    }

    public static function curl_backend_upload($request_body)
    {
        $url = CURL_BACKEND_URL . '/api/cn/upload';
        $headers = array();
        $headers[] = "Authorization: " . $_GET['token'];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "CKFinder Upload");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request_body));
        $response = curl_exec($ch);

        curl_close($ch);

        return $response;
    }

    public static function curl_backend_put($request_body)
    {
        $url = CURL_BACKEND_URL . '/api/cn/upload';
        $headers = array();
        $headers[] = "Authorization: " . $_GET['token'];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "CKFinder Upload");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request_body));
        $response = curl_exec($ch);

        curl_close($ch);

        return $response;
    }

    public static function curl_backend_delete($request_body)
    {
        $url = CURL_BACKEND_URL . '/api/cn/upload';
        $headers = array();
        $headers[] = "Authorization: " . $_GET['token'];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "CKFinder Upload");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request_body));
        $response = curl_exec($ch);

        curl_close($ch);

        return $response;
    }

}