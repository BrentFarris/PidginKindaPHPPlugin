PidginKindaPHPPlugin
====================

This Pidgin Perl plugin allows me to be able to write my user chat/im bot in PHP rather than in PERL

To use, you will first need the Pidgin chat client https://www.pidgin.im/

Next you will need "HTML-Strip" for PERL.  You can get it from: http://search.cpan.org/~kilinrax/HTML-Strip/ or find it in a compressed here.  Follow the install instructions in the HTML-Strip README file once extracted.

Next, copy the bot.pl file to your pidgin plugin directory (for linux it is ~/.purple/plugins/).

Next, you will need to run a local web server that supports PHP (will make this work with non-webserver PHP later).  Just drop the index.php file and brent.txt into a directory named "pidgin" in your web server directory.

Last, just log into pidgin, go to Tools->Plugins and then check on "Brents Plugin 0.1"
