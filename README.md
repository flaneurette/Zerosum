# Zerosum
Automated file integrity bot.

This script will generate hashes from all your files on your server, and then compares them against those stored in the database. If they are different, then it means the file has changed and that can mean someone accessed your files (might be you too :)

This PHP script was written in 2007, so it needs some more love and revision. Be gentle.

Create table:
-------------
Run the attached MySQL file for the table structure.

Create a crontab to run the scripts:
------------------------------------

lynx -dump "http://www.example.com/zerosum.php" >/dev/null 2>&1

or

/usr/bin/php -q /home/public_html/zerosum.php

