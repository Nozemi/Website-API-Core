<?php namespace NozCore\Endpoints;

use NozCore\Endpoint;
use NozCore\Message\Error;
use NozCore\Objects\File\File;

class FileCDN extends Endpoint {

    private $files = [];

    /**
     * @throws \ClanCats\Hydrahon\Query\Sql\Exception
     */
    public function get() {
        $file = new File();
        if($_REQUEST['endpoint'] == 'cdn-files' || $_REQUEST['endpoint'] == 'files') {
            $this->result = $file->getAll();
        } else if(($_REQUEST['endpoint'] == 'cdn-file' || $_REQUEST['endpoint'] == 'file') && isset($_REQUEST['id'])) {
            $file = $file->get($_REQUEST['id']);

            if(!$file instanceof File) {
                new Error('File was not found.');
            }

            $storedFile = $file->getProperty('location') . $file->getProperty('id') . $file->getProperty('extension');

            if(!file_exists($storedFile)) {
                new Error('File was not found.');
            }

            switch($file->getProperty('extension')) {
                case '.jpg':
                case '.jpeg':
                    $headerType = 'image/jpg';
                    break;
                default:
                    $headerType = false;
                    break;
            }

            if(!$headerType) {
                new Error('Extension not yet supported.');
            }

            header('Content-Disposition: inline');
            header('Content-Type: ' . $headerType);
            header('Content-Length: ' . filesize($storedFile));
            header('Content-Transfer-Encoding: binary');
            readfile($storedFile);
            exit;
        }
    }

    /**
     * @throws \ClanCats\Hydrahon\Query\Sql\Exception
     * @throws \ReflectionException
     */
    public function post() {
        $tmpFiles = [];
        if(isset($_FILES['file'])) {
            $tmpFiles = $_FILES['file'];
        } else if(isset($_FILES['files'])) {
            $tmpFiles = $_FILES['files'];
        }

        $amount = count($tmpFiles['name']);
        for($i = 0; $i < $amount; $i++) {
            $this->files[] = [
                'name' => $tmpFiles['name'][$i],
                'type' => $tmpFiles['type'][$i],
                'tmp_name' => $tmpFiles['tmp_name'][$i],
                'size' => $tmpFiles['size'][$i]
            ];
        }

        $max_upload = min(ini_get('post_max_size'), ini_get('upload_max_filesize'));
        $max_upload = str_replace('M', '', $max_upload);
        $max_upload = ($max_upload * 1024) * 1024;

        //$fileRoot = $GLOBALS['config']->fileRoot;
        $fileRoot = $GLOBALS['rootDir'] . '/files/';

        $failed = [];
        $success = [];
        foreach($this->files as $file) {
            if($file['size'] < $max_upload) {
                $fileObject = new File();
                $fileObject->generateId();

                $ext = '';

                switch($file['type']) {
                    case 'application/pdf':
                        $ext = '.pdf';
                        break;
                    case 'image/png':
                        $ext = '.png';
                        break;
                    case 'image/jpeg':
                    case 'image/jpg':
                        $ext = '.jpg';
                        break;
                    default:
                        new Error('This file-type is not yet supported. (' . $file['type'] . ')');
                }

                if(move_uploaded_file($file['tmp_name'], $fileRoot . $fileObject->getProperty('id') . $ext)) {
                    $fileObject->setProperty('name', $file['name']);
                    $fileObject->setProperty('size', $file['size']);
                    $fileObject->setProperty('location', $fileRoot);
                    $fileObject->setProperty('created', date('Y-m-d H:i:s'));
                    $fileObject->setProperty('modified', date('Y-m-d H:i:s'));
                    $fileObject->setProperty('extension', $ext);
                    $fileObject->save();

                    $success[] = $file;
                } else {
                    $file['reason'] = 'File was not uploaded due to an unknown reason.';
                    $failed[] = $file;
                }
            } else {
                $file['reason'] = 'File was too big. Max size: ' . $max_upload;
                $failed[] = $file;
            }
        }

        $this->result['successful'] = $success;

        if(!empty($failed)) {
            $this->result['failed'] = $failed;
        }
    }
}