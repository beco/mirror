<?php

namespace b3co\notion\block;

require_once("Interfaces.php");

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
    $this->caption = $this->getCaption($data['image']['caption']);
    $this->notion_url = $data['image']['file']['url'];
    $this->url = $this->notion_url;
    if($upload) {
      if(VERBOSE) print "initializing S3 client\n";
      $this->s3 = new S3Client([
        'version' => 'latest',
        'region' => 'us-east-2'
      ]);
      $this->uploadToS3();
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
    return sprintf("%s/%s.jpg",
      //$this->parent_page->id,
      $this->type,
      $this->id
    );
  }

  public function uploadToS3():bool {
    if($this->isUploaded()) {
      $this->url = sprintf("https://s3.%s.amazonaws.com/%s/%s",
        'us-east-2',
        'static.notion.b3co.com',
        $this->getS3Key()
      );
      return true;
    }

    if(VERBOSE) echo "- uploading image\n";

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
    } catch(AwsException $ae) {

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
