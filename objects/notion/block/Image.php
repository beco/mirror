<?php

namespace b3co\notion\block;

use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Aws\S3\Exception\S3Exception;

class Image extends Block implements BlockInterface, Uploadable {

  public $caption;
  public $url;
  public $format;

  private $notion_url;
  private $s3;

  public function __construct($data, $parent, $upload = false) {
    parent::__construct($data, $parent, $upload);

    $type = '';
    if($data['object'] == 'page') {
      $type = 'cover';
    } else {
      $type = 'image';
    }

    $this->caption = $this->getCaption($data[$type]['caption']);
    $this->notion_url = $data[$type]['file']['url'];
    $this->url = $this->notion_url;

    if(VERBOSE) print "initializing S3 client\n";
    $this->s3 = new S3Client([
      'version' => 'latest',
      'region'  => 'us-east-2',
      'credentials' => [
        'key' => $this->parent_page->notion->config['aws_key'],
        'secret' => $this->parent_page->notion->config['aws_secret'],
      ]
    ]);

    if($upload) {
      $this->uploadToS3();
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
    return sprintf("%s/public/%s/%s.jpg",
      $this->parent_page->id,
      $this->type,
      $this->id
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

    if(VERBOSE) printf("- uploading image [%s]\n", $this->getS3Key());

    $file = file_get_contents($this->notion_url, "r");
    try {
      $result = $this->s3->putObject([
        'Bucket' => 'static.notion.b3co.com',
        'Key' => $this->getS3Key(),
        'contentType' => 'image/jpeg',
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

  public function toHtml($container = 'div') {
    $ret = sprintf("<figure><img src='%s'>", $this->url);
    if($this->caption != '') {
      $ret .= sprintf("<figcaption>👆 %s</figcaption>", $this->caption);
    }
    $ret .= "</figure>";
    return sprintf(Block::$html_containers[$container], $ret);
  }

  public function toString() {
    return sprintf("%s\n", $this->url);
  }

  public function toMarkDown() {
    $ret = sprintf("![inline](%s)", $this->url);
    if($this->caption != '') {
      $ret .= sprintf("\n> %s\n", $this->caption);
    }
    return $ret;
  }

  private function getCaption($data) {
    if(isset($data[0]['text']['content'])) {
      return $data[0]['text']['content'];
    }
    return '';
  }
}
