CandyCMS Version 2.1
========================================================================================================================

What is CandyCMS?
------------------------------------------------------------------------------------------------------------------------

CandyCMS is a modern PHP CMS with its main focus on usability, speed and security.

It provides...

- a blog that supports tags, comments, RSS and full social media integration
- content pages
- a gallery with multiple file upload (based on HTML5) and Media RSS
- a calendar with the option to download iCalendar events
- a download section
- easy and simple user management
- file management
- newsletter management (uses [Mailchimp API](http://mailchimp.com))
- a full log system
- and many more!


Additional reasons, why CandyCMS might be interesting for you
------------------------------------------------------------------------------------------------------------------------
- easy internationalization and localization via YAML
- WYSIWYG-Editor ([TinyMCE](http://tinymce.moxiecode.com/)) and support of [BB-Code](https://github.com/marcoraddatz/candyCMS/wiki/BBCode)
- uses the [Smarty template engine](http://smarty.org) and lots of HTML5
- supports [reCAPTCHA](http://recaptcha.org)
- completely object oriented and use of MVC
- easy to extend
- supports templates
- clean URLs due to mod_rewrite
- full Facebook integration
- supports CDNs
- easy to update or migrate
- SEO optimized (sitemap.xml and basic stuff)
- 2click social share privacy
- Tests for the whole core functions


Requirements
------------------------------------------------------------------------------------------------------------------------
- at least PHP 5.1 & PDO supported database (PHP 5.3 recommended)
- Imagemagick, GD2 and mod_rewrite
- an account at http://recaptcha.org to use captchas
- an account at http://mailchimp.com to use the newsletter management
- about 10MB webspace


Setup
------------------------------------------------------------------------------------------------------------------------
1. Configure your website settings at "app/config/Candy.inc.php, upload all files.
2. Execute the "/install/index.php" file.
3. Follow the instructions and make sure, you delete the install dir after installation.
4. Download and install Composer (http://getcomposer.org/): `curl -s http://getcomposer.org/installer | php`.
5. Update your packages afterwards: `php composer.phar update`.

To upgrade CandyCMS, upload the install folder, run "/install/index.php" and click on "migrate". Make sure you override
the existing "vendor/*", folders before. Please also take a look at the release notes.


Credits
------------------------------------------------------------------------------------------------------------------------
Icons were created by [famfamfam.com](http://famfamfam.com). Big thanks to Hauke Schade who gave great feedback and
built many impressive features.


License
------------------------------------------------------------------------------------------------------------------------
CandyCMS is licensed under MIT license. All of its components should be Open Source and free to use, too.
Note that [fancyBox](http://fancyapps.com/fancybox/) needs a license for commercial projects.