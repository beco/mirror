<?php
namespace b3co\notion;

use GuzzleHttp\Client;

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
  ];

  private static $endpoints = [
    'get_page' => [
      'url' => 'https://api.notion.com/v1/pages/:page_id',
      'params' => ['page_id']
    ],
    'get_children' => [
      'url' => 'https://api.notion.com/v1/blocks/:block_id/children',
      'params' => ['block_id']
    ],
  ];

  private $token = '';
  private $client;

  public function __construct() {
    $this->token = getenv('notion_token');
    $this->client = new Client();
  }

  public function getPage($id) {
    return $this->retrieve('get_page', ['page_id' => $id]);
  }

  public function getNodesFrom($id) {
    $content = $this->retrieve('get_children', ['block_id' => $id]);
    return $content;
  }

  private function retrieve($endpoint, $params = []) {
    $url = $this->populateUrl($endpoint, $params);
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

  private function populateUrl($endpoint, $params = []) {
    $url = Notion::$endpoints[$endpoint]['url'];
    foreach(Notion::$endpoints[$endpoint]['params'] as $param) {
      if($params[$param] != null) {
        $url = preg_replace(sprintf('/\:%s/', $param), $params[$param], $url, 1);
      }
    }
    return $url;
  }

  public function getChildren($id, $upload = false) {
    $blocks = json_decode($this->getNodesFrom($id), true);
    $children = [];
    foreach($blocks['results'] as $block) {
      if(Notion::$classes[$block['type']]) {
        $children[] = new Notion::$classes[$block['type']]($block, $page, $upload);
      } else {
        if(VERBOSE) printf("no class for %s\n", $block['type']);
      }
    }
    return $children;
  }

}
