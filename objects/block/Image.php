<?php

namespace b3co\notion\block;

require_once("BlockInterface.php");

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

class Image extends Block implements BlockInterface {

  public $caption;
  public $url;
  public $format;

  private $notion_url;

  public function __construct($data, $upload = false) {
    parent::__construct($data);
    $this->caption = $this->getCaption($data['image']['caption']);
    $this->notion_url = $data['image']['file']['url'];
    $this->url = $this->notion_url;
    if($upload) {
      $this->uploadToS3();
    }
  }

  private function uploadToS3() {
    $s3 = new S3Client([
      'version' => 'latest',
      'region' => 'us-east-2'
    ]);

    $file = file_get_contents($this->notion_url, "r");

    try {
      $result = $s3->putObject([
        'Bucket' => 'static.notion.b3co.com',
        'Key' => sprintf("%s.jpg", $this->id),
        'contentType' => 'image/jpeg',
        'Body' => $file
      ]);
      $this->url = sprintf("https://s3.%s.amazonaws.com/%s/%s.jpg",
        'us-east-2',
        'static.notion.b3co.com',
        $this->id

      );
      $this->url = $result->get('ObjectURL');
      return true;
    } catch(AwsException $ae) {

    }
  }

  public function toHtml($container = 'div') {
    $ret  = sprintf("<img src='%s'>", $this->url);
    if($this->caption != '') {
      $ret .= sprintf("<p class='caption'>%s</p>", $this->caption);
    }
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
