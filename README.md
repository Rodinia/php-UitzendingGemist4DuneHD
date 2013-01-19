Uitzending Gemist Index on Dune HD (PHP Script)
===============================================
Server side scripts to enabling watching Uitzending Gemist on Dune HD media player.

PHP script for indexing 'Uitzending Gemist' on Dune HD media player

## Use public Uitzending Gemist Dune HD service

This application is hosted on the following location(s):
- http://uitzendinggemist.comule.com/ (Sorry, this MIRROR IS DOWN)

Create an empty folder such as 'Uitzending Gemist' and create a text-file 'dune_folder.txt'. 
Put the following text in 'dune_folder.txt':
```
# Link to Uitzending Gemist running on comule.com
media_url = dune_http://uitzendinggemist.comule.com/dune/
```

See the Wiki (http://github.com/Rodinia/php-UitzendingGemist4DuneHD/wiki) how to create a link in favorites.

## How host your own uitzendinggemist PHP script with Dune HD player.
 
### Requirements:
* PHP >= 5.3 (+ PHP cURL library)
* Dune HD Player
* PHP Server (requires PHP Client URL Library component)

### How to install
* Install Uitzending Gemist service
 * Download latest version of php-UitzendingGemist4DuneHD (http://github.com/Rodinia/php-UitzendingGemist4DuneHD)
 * Extract distribution to your PHP enabled web server.
 * Edit `/lib/config.php` to fit yout need.
* Create short-cut for the DuneHD to access the service
 * Now create an empty folder on any media accessable by you Dsuch as 'Uitzending gemist' and create a text-file 'dune_folder.txt'. 
 * Put the following text in 'dune_folder.txt':
```
# Link to my 'Uitzending Gemist'  
media_url = dune_http://diskstation/uitzendinggemist/dune/
```

Make sure you adjest media_url to you needs.
It should start with 'dune_http://' and the URL should end with 'dune/'.

Source code available at: https://github.com/Rodinia/php-UitzendingGemist4DuneHD

Related, discussion thread at MPC Club: http://www.mpcclub.com/forum/showthread.php?t=29984

Have fun,

Rodinia



