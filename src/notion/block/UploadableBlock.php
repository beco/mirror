<?php

namespace b3co\notion\block;

use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Aws\S3\Exception\S3Exception;

class UploadableBlock extends Block {

  public $s3;
  public $notion_url;
  public $url;
  public $extension;

  public static $media_types = [
    'jpg'  => 'image/jpeg',
    'mp4'  => 'video/mp4',
    'jpeg' => 'image/jpeg',
  ];

  public function __construct($data, $parent) {
    parent::__construct($data, $parent);

    //print_r($data);

    $type = $data['type'];

    $this->notion_url = $data[$type][$data[$type]['type']]['url'];

    $this->extension = '';
    if(preg_match('/^http.*\/.*?\.(\w+)\?.*$/', $this->notion_url, $m)) {
      $this->extension = $m[1];
      printf("type: %s\n", $this->extension);
    }

    $this->s3 = new S3Client([
      'version' => 'latest',
      'region'  => $this->parent_page->notion->config['aws_region'],
      'credentials' => [
        'key'    => $this->parent_page->notion->config['aws_key'],
        'secret' => $this->parent_page->notion->config['aws_secret'],
      ]
    ]);

  }

  protected function upload() {
    if($this->upload) {
      $this->uploadToS3();
      $this->url = $this->getS3Url();
    } else {
      // even if i am not uploading at the moment, check if already exists
      // up there
      if($this->isUploaded()) {
        $this->url = $this->getS3Url();
      }
    }
  }

  public function isUploaded():bool {
    try {
      $response = $this->s3->getObject([
        'Key' => $this->getS3Key(),
        'Bucket' => 'static.notion.b3co.com'
      ]);
      return true;
    } catch(S3Exception $e) {
      return false;
    } catch(Exception $e) {
      return false;
    }
    return false;
  }

  public function getS3Key():string {
    return sprintf("%s/public/%s/%s.%s",
      $this->parent_page->id,
      $this->type,
      $this->id,
      $this->extension
    );
  }

  public function getS3Url():string {
    return sprintf("https://s3.%s.amazonaws.com/%s/%s",
      'us-east-2',
      'static.notion.b3co.com',
      $this->getS3Key()
    );
  }

  public function uploadToS3():bool {
    if($this->isUploaded()) {
      $this->url = $this->getS3Url();
      return true;
    }

    if(VERBOSE) printf("- uploading %s [%s]\n", $this->type, $this->getS3Key());

    $file = file_get_contents($this->notion_url, "r");
    try {
      $result = $this->s3->putObject([
        'Bucket' => 'static.notion.b3co.com',
        'Key' => $this->getS3Key(),
        'ContentType' => self::$media_types[$this->extension],
        'Body' => $file
      ]);
      $this->url = $result->get('ObjectURL');
      return true;
    } catch(AwsException $e) {
      echo $e->getMessage();
      echo "\n";
    }
    return false;
  }

}
