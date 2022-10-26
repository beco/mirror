📰 README
============

A PHP way to read Notion pages and (optionally) upload images and videos to an S3 bucket (and link accordingly).

This is my first package distributed through composer, bare with me.

# Operation

## Prepare Services

### Notion

1. you have to create a Notion integration [here](https://www.notion.so/my-integrations).
1. since we’re not writing _to_ Notion, at this time only `read` access is necessary
1. grab the `notion_token`, something like `secret_xxxxxxxxxx`
1. on every page you want to be read by this script, you need to share it with the integration as shown in the image, quite easy part but tricky to find

![inline](https://s3.us-east-2.amazonaws.com/static.notion.b3co.com/ac32ffd2-5c32-4b1e-8d93-2b41fa38752a/public/image/6d113cbb-2cfc-4e87-9dfd-05b761512391.jpg)
> my Notion integration is called **page mirror**, use your own, the one created on the first step ☝️

### S3

1. create an AWS S3 bucket
1. grab the `AWS_KEY`, `AWS_SECRET`, `AWS_REGION` and `BUCKET_NAME`
Note that by default, images will be uploading using the following structure:
`s3.AWS_REGION.amazonaws.com/BUCKET_NAME/PAGE_ID/public/image/IMAGE_ID.jpg`

## CLI Invocation

You have to create a `$config` object with the previously created services’ access variables as follows:

```php
$config = [
  'notion_token' => 'NOTION_TOKEN',
  'aws_key'      => 'AWS_KEY',
  'aws_secret'   => 'AWS_SECRET',
  'aws_region'   => 'AWS_REGION',
  'bucket_name'  => 'BUCKET_NAME',
];
```

You now can invoke the `Notion` object.

```php
$notion = new \b3co\notion\Notion($config);
$page   = $notion->getPage(PAGE_ID);
echo $page->toHtml();
```

## Yii Invocation

First, on the config file either `web.php` or `console.php` add

```php
$config = [
	...
	'components' => [
		...
		'notion' => [
			'class' => 'b3co\notion\YiiNotion',
		  'config' => [
		    'notion_token' => getenv('notion_token'),
		    'aws_key'      => getenv('aws_key'),
		    'aws_secret'   => getenv('aws_secret'),
		    'bucket_name'  => getenv('aws_bucket'),
		    'aws_region'   => getenv('aws_region'),
		  ]
		],
		...
  ]
	...
];
```

Second, use the `YiiNotion` object to get a `Notion` object and execute accordingly.

```php
class NotionController extends Controller {

  public function actionIndex($id) {
    define('VERBOSE', false);
    $notion = Yii::$app->get('notion')->getNotion();
    $page = $notion->getPage($id);
    echo $page->toHtml();

    return ExitCode::OK;
  }
}
```

# Templates (work in progress)

By default, three different export templates are set to each object:

1. HTML with `$page→toHtml()`
1. MarkDown with `$page→toMd()`
1. Plain text with `$page→toString()`

## Template families

Optionally you can use the template factory and create a template family under `$config->templates_dir/TEMPLATE_FAMILY/` so if you invoke `$page->toTemplate(TEMPLATE_FAMILY)`  
system will check for each block type template there, named as `BLOCK_TYPE.template`.
For instance, for a template family called `basic`, you’ll have:

![inline](https://s3.us-east-2.amazonaws.com/static.notion.b3co.com/ac32ffd2-5c32-4b1e-8d93-2b41fa38752a/public/image/94f0be88-c4a2-446d-b134-8757d148c524.jpg)

Where `image` and `page` are **block types**.

Each template can use any given format/language and use objects’ attributes to be printed like `[:ATTRIBUTE]`, for example an image with `url` and `caption`.

```html
<div><img src='[:url]'>
  <div>[:caption]</div>
</div>
```

A complete list of objects and attributes is [available here](/62dad5662cd94a518549af580200c17f).

**Important 🚨**: If a template file is not found, that block will not be processed.

### A note on `toHtml` method

HTML templating can be overwritten by creating a template family called html, if a block item template is found, will be used, if not, fail back will use the hardcoded HTML template.

# Notion Objects

## Supported Objects List

- [x] Paragraph blocks
- [x] Heading one blocks
- [x] Heading two blocks
- [x] Heading three blocks
- [x] Image blocks
- [x] To do blocks
- [x] Column List and Column Blocks
- [x] Quote blocks
- [x] Bulleted list item blocks
- [x] Code blocks
- [x] Toggle blocks
- [x] Divider blocks
- [x] Video blocks
- [x] Numbered list item blocks (tricky, work in progress)
- [x] Embed blocks
- [ ] Equation blocks
- [x] Table row blocks
- [x] Table blocks
- [ ] Callout blocks
- [x] Child page blocks
## Out of Scope
- [ ] Child database blocks
- [ ] File blocks
- [ ] PDF blocks
- [ ] Bookmark blocks
- [ ] Table of contents blocks
- [ ] Breadcrumb blocks
- [ ] Link Preview blocks
- [ ] Template blocks
- [ ] Link to page blocks
- [ ] Synced Block blocks

---

This very README file is created and [stored in Notion](/53588805075a4fd6beca350676d3fb48) and converted to MD using this script, last update 2022-08-24 → .
