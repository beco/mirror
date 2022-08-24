Notion Mirror
=============

A php way to read Notion pages and upload resources to an s3 bucket.

# Operation

## 1. prepare notion
1. create an integration [here](https://www.notion.so/my-integrations)
2. at this time only _read_ access is necesary
3. grab the `notion_token` and add it as an environment variable `export notion_token=CCCCCCCC`

## 2. check for local variables needed

These `env` vars are mandatory:
- `notion_token`
- `env`

### Optional
- `aws_key` - that.
- `aws_secret` - that.
- `s3_bucket` - also, that.

# Templates

When invoking a `$page->toTemplate($template)` this happens:
1. checks if `__DIR__/templates/$template/$block` file_exists
2. if it does checks for any `[:KEY]` to replaces it with `$block->$key`

## Supported 📦 Objects

### Planned
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
- [ ] Table blocks
- [ ] Table row blocks
