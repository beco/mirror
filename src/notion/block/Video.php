<?php

namespace b3co\notion\block;

use b3co\notion\block\interfaces\BlockInterface;

class Video extends UploadableBlock implements BlockInterface {

  public $caption;
  public $video_type;
  public $url;
  public $yt_code;
  public $yt_origin = false;

  public function __construct($data, $parent) {
    parent::__construct($data, $parent);
    $this->video_type = $data['video']['type'];
    $this->caption    = new RichText($data['video']['caption']);
    $this->url        = $data['video'][$this->video_type]['url'];
    $this->yt_origin  = $this->isYoutube();

    if($this->isFile()) {
      printf("is file, uploading \n");
      $this->upload();
    } else {
        printf("no go %s\n", $this->data['video']['type']);
    }
  }

  public function toString() {
    return sprintf("Video: (%s)\n", $this->url);
  }

  public function toMarkDown() {
    return sprintf("Video [here](%s)\n", $this->url);
  }

  public function toHtml($container = 'div') {
    if($this->video_type == 'file') {
      return sprintf('<iframe
          width="640"
          height="390"
          src="%s"
          ></iframe>',
        $this->getS3Url());
    } elseif($this->video_type == 'external' && $this->yt_origin) {
      return sprintf('<iframe id="player"
          width="640"
          height="390"
          title="YouTube video player"
          frameborder="0"
          src="https://www.youtube.com/embed/%s"
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
          allowfullscreen></iframe>',
        $this->yt_code);
    }
    return 'no code';
  }

  public function isYoutube():bool {
    if(preg_match('/^https?\:\/\/.*youtube\.com\/watch?.*v=([a-zA-Z0-9\-_]{6,15})/', $this->url, $m)) {
      $this->yt_code = $m[1];
      return true;
    }
    return false;
  }

  public function isFile():bool {
    print_r($this->raw);
    return $this->raw['video']['type'] == 'file';
  }
}
