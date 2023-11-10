<?php
namespace b3co\notion;

use GuzzleHttp\Client;
use b3co\notion\block\Page;

class Notion {

  public static $classes = [
    'image'       => '\b3co\notion\block\Image',
    'paragraph'   => '\b3co\notion\block\Paragraph',
    'heading_1'   => '\b3co\notion\block\H1',
    'heading_2'   => '\b3co\notion\block\H2',
    'heading_3'   => '\b3co\notion\block\H3',
    'to_do'       => '\b3co\notion\block\ToDo',
    'column_list' => '\b3co\notion\block\Columns',
    'column'      => '\b3co\notion\block\Column',
    'divider'     => '\b3co\notion\block\Divider',
    'quote'       => '\b3co\notion\block\Quote',
    'embed'       => '\b3co\notion\block\Embed',
    'toggle'      => '\b3co\notion\block\Toggle',
    'code'        => '\b3co\notion\block\Code',
    'table'       => '\b3co\notion\block\Table',
    'table_row'   => '\b3co\notion\block\TableRow',
    'bookmark'    => '\b3co\notion\block\Bookmark',
    'child_page'  => '\b3co\notion\block\ChildPage',
    'video'       => '\b3co\notion\block\Video',
    'file'        => '\b3co\notion\block\File',
    'callout'     => '\b3co\notion\block\Callout',
    'table_of_contents'  => '\b3co\notion\block\TableOfContents',
    'bulleted_list_item' => '\b3co\notion\block\BulletListItem',
    'numbered_list_item' => '\b3co\notion\block\NumberListItem',
  ];

  private static $endpoints = [
    'get_page' => [
      'url' => 'https://api.notion.com/v1/pages/:page_id',
      'params' => ['page_id'],
      'optional' => [],
    ],
    'get_children' => [
      'url' => 'https://api.notion.com/v1/blocks/:block_id/children',
      'params' => ['block_id'],
      // will be appended as the GET part of the request
      'optional' => ['start_cursor'],
    ],
  ];

  private $token = '';
  private $client;
  public $config;
  public $s3_ready;

  public function __construct($config = []) {
    $this->config = $config;
    $this->token  = $config['notion_token'] or die("no notion token");
    $this->client = new Client();
    $this->s3_ready = $this->isS3Ready();
  }

  private function isS3Ready() {
    return isset($this->config['aws_key']) && $this->config['aws_key'] != '' &&
      isset($this->config['aws_secret']) && $this->config['aws_secret'] != '' &&
      isset($this->config['aws_region']) && $this->config['aws_region'] != '' &&
      isset($this->config['bucket_name']) && $this->config['bucket_name'] != '';
  }

  public function getPage($id, $save = false) {
    return new Page($id, $this, $save);
  }

  public function getStats($id) {
    $p = $this->getPage($id);
    $r = [];
    $b = ['paragraph', 'image', 'heading_1', 'heading_2', 'heading_3', 'title'];
    foreach($p->children as $block) {
      if(in_array($block->type, $b)) {
        if($block->type == 'image') {
          $t = $block->simple_caption;
        } else {
          $t = $block->toString();
        }

        $r[$block->id] = [
          'words' => str_word_count($t),
          'chars' => strlen($t),
          'type' => $block->type,
          'text' => $t,
        ];
      }
    }
    $total = [];
    foreach($r as $item) {
      if($total[$item['type']] === null) {
        $total[$item['type']] = [
          'words' => 0,
          'chars' => 0,
          'count' => 0,
        ];
      }
      $total[$item['type']]['words'] += $item['words'];
      $total[$item['type']]['chars'] += $item['chars'];
      $total[$item['type']]['count']++;

    }
    return $total;
  }

  public function getNodesFrom($id) {
    $blocks   = [];
    $continue = false;
    $cursor   = null;

    $i = 0;

    do {
      $continue  = false;
      $optionals = [];

      if($cursor !== null) {
        $optionals['start_cursor'] = $cursor;
      }

      $content = $this->retrieve('get_children', [
        'block_id' => $id
      ], $optionals);

      $content = json_decode($content, true);
      $blocks  = array_merge($blocks, $content['results']);

      unset($content['results']);

      if($content['has_more'] == 1) {
        $cursor = $content['next_cursor'];
        $continue = true;
      }

    } while($continue);
    return $blocks;
  }

  public function retrieve($endpoint, $params = [], $optionals = []) {
    $url = $this->populateUrl($endpoint, $params, $optionals);
    try {
      $response = $this->client->request('GET', $url, [
        'headers' => [
          'Accept'         => 'application/json',
          'Authorization'  => sprintf('Bearer %s', $this->token),
          'Notion-Version' => '2022-02-22',
        ],
      ]);
    } catch (Excpetion $e) {
      echo "error";
    }
    return $response->getBody();
  }

  private function populateUrl($endpoint, $params = [], $optionals = []) {
    $url = Notion::$endpoints[$endpoint]['url'];
    $ps  = [];

    foreach(Notion::$endpoints[$endpoint]['params'] as $param) {
      if($params[$param] !== null) {
        $url = preg_replace(sprintf('/\:%s/', $param), $params[$param], $url, 1);
      }
    }

    if(Notion::$endpoints[$endpoint]['optional'] !== null) {
      foreach(Notion::$endpoints[$endpoint]['optional'] as $optional) {
        if($optionals[$optional] !== null) {
          $ps[] = sprintf("%s=%s", $optional, $optionals[$optional]);
        }
      }
    }

    return sprintf("%s?%s", $url, join("&", $ps));
  }

  public function getChildren($id, $page) {
    $blocks = $this->getNodesFrom($id);
    $children = [];

    $i = 0;

    foreach($blocks as $block) {
      $type = $block['type'];
      if(Notion::$classes[$type]) {
        $children[$i] = new Notion::$classes[$type]($block, $page);

        if(isset($children[$i-1])) {
          if($children[$i-1]->type != $type){
            $children[$i]->is_first  = true;
            $children[$i-1]->is_last = true;
          } else {
            $children[$i]->is_first  = false;
            $children[$i-1]->is_last = false;
          }
        }

        $i++;
        if(VERBOSE) fwrite(STDERR, sprintf("✅ initializing %s\n", $block['type']));
      } else {
        if(VERBOSE) fwrite(STDERR, sprintf("🔴 no class for %s\n", $block['type']));
      }
    }
    return $children;
  }

}
