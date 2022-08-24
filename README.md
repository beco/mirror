Notion Mirror
=============

A php way to read Notion pages and upload resources to an s3 bucket.

First package to composer, bare with me on this one.

# Operation

## prepare services
1. Notion: create a Notion integration [here](https://www.notion.so/my-integrations)
  1. at this time only _read_ access is necesary
  1. grab the `notion_token`
1. AWS: create an s3 bucket, grab `aws_key`, `aws_secret`, `bucket_name`
1. create a `$config` array and pass it when creating a `b3co/notion/Notion` object, like this example:

```php
$config = [
  'notion_token' => 'NOTION_TOKEN',
  'aws_key'      => 'AWS_KEY',
  'aws_secret'   => 'AWS_SECRET',
  'bucket_name'  => 'BUCKET_NAME',
];
$notion = new \b3co\notion\Notion($config);
$page   = $notion->getPage(PAGE_ID);
echo $page->toHtml();
```

# Templates

When invoking a `$page->toTemplate(TEMPLATE)` this happens:
1. checks if `__DIR__/templates/TEMPLATE/$block` file_exists
2. if it does checks for any `[:KEY]` to replaces it with `$block->$key`

# Supported 📦 Objects

## Planned
- [x] Paragraph blocks
- [x] Heading one blocks
- [x] Heading two blocks
- [x] Heading three blocks
- [x] Image blocks
- [x] To do blocks
- [x] Column List and Column Blocks
- [x] Quote blocks
- [x] Bulleted list item blocks
- [ ] Numbered list item blocks
- [x] Code blocks
- [ ] Video blocks
- [ ] Toggle blocks
- [x] Divider blocks
- [x] Embed blocks
- [x] Child page blocks
- [x] Table blocks
- [x] Table row blocks

### Out of Scope
- [ ] Callout blocks
- [ ] Child database blocks
- [ ] File blocks
- [ ] PDF blocks
- [ ] Bookmark blocks
- [ ] Equation blocks
- [ ] Table of contents blocks
- [ ] Breadcrumb blocks
- [ ] Link Preview blocks
- [ ] Template blocks
- [ ] Link to page blocks
- [ ] Synced Block blocks
