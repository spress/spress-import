Import plugins for Spress
=========================
[![Build Status](https://travis-ci.org/spress/Spress-import.svg?branch=master)](https://travis-ci.org/spress/Spress-import)

This plugins let you import posts and pages from others platforms to a Spress site.

**Note**: this plugins is in a early stage. This means that its behaviour could change
frecuenly without notice until it reaches a stable version.

## Platforms supported
* Wordpress WXR files.

# Requirements
* php >= 5.5.
* [Spress](http://spress.yosymfony.com) >= 2.1.3.
* [Composer](https://getcomposer.org/).

## How to install?
1. Go to `your-spress-site/` folder.
2. Run `composer require spress/spress-import:@dev` (Composer tool is necessary).
3. When you run `spress` command, import commands will appears under `import` namespace.

## How to use?
See the concrete provider.

### WXR files from Wordpress
The sign of `import:wordpress` command is the following:

```bash
import:wordpress [--dry-run] [--post-layout POST-LAYOUT] [--page-layout PAGE-LAYOUT] [--] <file>
```
Example of use:
```bash
$ spress import:wordpress /path-to/my-wxr-file.xml
```
#### Options
* `--dry-run`: This option displays the items imported without actually modifying your site.
* `--post-layout`: Layout applied to posts. e.g: `--post-layout=post`.
* `--page-layout`: Layout applied to pages. e.g: `--page-layout=default`.
