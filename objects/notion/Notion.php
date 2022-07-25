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

  public function __construct($config = []) {
    $this->config = $config;
    $this->token  = $config['notion_token'] or die("no notion token");
    $this->client = new Client();
  }

  public function getPage($id, $save = false) {
    return new Page($id, $save, $this);
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

  public function getChildren($id, $page, $upload = false) {
    $blocks = $this->getNodesFrom($id);
    $children = [];
    foreach($blocks as $block) {
      if(Notion::$classes[$block['type']]) {
        $children[] = new Notion::$classes[$block['type']]($block, $page, $upload);
      } else {
        if(VERBOSE) printf("🔴 no class for %s\n", $block['type']);
      }
    }
    return $children;
  }

}
