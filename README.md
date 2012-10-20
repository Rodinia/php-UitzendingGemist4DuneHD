php-UitzendingGemist4DuneHD
===========================

PHP script for indexing 'Uitzending Gemist' on Dune HD media player

*********************************************************************
How to use uitzendinggemist PHP script with Dune HD player.
-----------------------------------------------------------
 
Requirements:
- Dune HD Player
- PHP Server

I have tested it with a Synology NAS server and a Dune HD Smart B1 media player.
The Synology NAS server has PHP enabled if you set up a web service.


Copy the content of the 'uitzendinggemist' to a web folder.
Test the script with a webbrowser, by going to the corresponding URL in my case ('http://diskstation/uitzendinggemist/')

Create a folder on some media accessable by your Dune HD player.

Create an empty folder such as 'Uitzending gemist' and create a text-file 'dune_folder.txt'. 
Put the following text in 'dune_folder.txt':
------------------------------------------------------------
# Redirected to 'Uitzending Gemist' PHP index
media_url = dune_http://diskstation/uitzendinggemist/dune/  
------------------------------------------------------------
Make sure you adjest media_url to you needs.
It should start with 'dune_http://' and end with 'dune/'

Update 'ugconfig.xml' to your needs.

Source code available at: https://github.com/Rodinia/php-UitzendingGemist4DuneHD

Have fun,

Rodinia



