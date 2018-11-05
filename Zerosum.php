<?php

# Store this below the /www/ folder!
$dbHost = "localhost";
$dbBase = "";
$dbUser = "";
$dbPass = "";
$dbLink = mysql_connect($dbHost, $dbUser, $dbPass) or die();
mysql_select_db($dbBase) or die();

function zerosum($dir) {

	// Settings
	$update_files = false; // update files after check, program does this already.
	$index_files = false; // indexing files
	$mailbatch = true; // send an alert mail
	$showbatch = false; // show batch onscreen
	$domain = 'example.com';
	$mailto = 'YOU@example.com';
	$subject = 'Log';
	$hashses = array();
	$directory = dir($dir);
	while ($file = $directory->read())
		{
		if ($file != '.' && $file != '..')
			{
			$modified = date("F d Y H:i:s", filemtime($dir . '/' . $file));
			$permission = substr(sprintf('%o', fileperms($dir . '/' . $file)) , -4);
			$hashsum = filesize($dir . '/' . $file);
			array_push($hashses, htmlspecialchars($dir . '/' . $file, ENT_QUOTES, 'UTF-8') . '|' . $modified . '|' . $hashsum . '|' . $permission);
			}
		}

	$directory->close();
	if ($showbatch)
		{
		echo '<p>Batchdate: ' . date("F d Y H:i:s") . '</p>';
		$count = 0;
		foreach($hashses as $hash)
			{
			$tmp = explode('|', $hash);
			$sql = mysql_query("select * from zerosum where filename = '" . $tmp[0] . "'");
			if (mysql_num_rows($sql) > 0)
				{
				while ($row = mysql_fetch_array($sql))
					{
					if ($row['filehash'] != sha1($tmp[2]))
						{
						echo $row['filename'] . ' was altered on: ' . $tmp[1] . ' current permission: ' . $tmp[3] . ' size: ' . $hashsum . '<br />';
						$count++;
						}

					if ($row['permhash'] != sha1($tmp[3]))
						{
						echo $row['filename'] . 's permission was altered to: ' . $tmp[3] . '<br />';
						$count++;
						}
					}
				}
			  else
				{
				echo $tmp[0] . ' was deleted, or does not exist! <br />';
				}

			if ($count > 0)
				{

				// index all files again.

				$update_files = true;
				}
			}
		}

	if ($mailbatch)
		{
		if ($mailto)
			{
			$message1 = 'Batchdate: ' . date("F d Y H:i:s") . ', ';
			$count1 = 0;
			foreach($hashses as $hash)
				{
				$tmp1 = explode('|', $hash);
				$sql1 = mysql_query("select * from zerosum where filename = '" . $tmp1[0] . "'");
				if (mysql_num_rows($sql1) > 0)
					{
					while ($row1 = mysql_fetch_array($sql1))
						{
						if ($row1['filehash'] != sha1($tmp1[2]))
							{
							$message1.= $row1['filename'] . ' was altered on: ' . $tmp1[1] . ' current permission: ' . $tmp1[3] . ' size: ' . $hashsum . ', ';
							$count1++;
							}

						if ($row1['permhash'] != sha1($tmp1[3]))
							{
							$message1.= $row1['filename'] . 's permission was altered to: ' . $tmp1[3] . ', ';
							$count1++;
							}
						}
					}
				  else
					{
					echo $tmp1[0] . ' was deleted, or does not exist! <br />';
					}
				}

			if ($count1 > 0)
				{

				// mail batch
				mail($mailto, $subject, $message1, "from:zerosum@" . $domain);

				// index all files again.
				$update_files = true;
				}
			}
		  else
			{
			echo 'Cannot email batch, e-mail is empty!';
			exit;
			}
		}

	if ($update_files)
		{
		foreach($hashses as $hash)
			{
			$tmp = explode('|', $hash);
			$sql = mysql_query("update zerosum set filehash = '" . sha1($tmp[2]) . "', permhash = '" . sha1($tmp[3]) . "' where filename = '" . $tmp[0] . "'") or die();
			}
		}

	if ($index_files)
		{

		// You must run this first, to Index all the files in the DIR!
		$empty = mysql_query("TRUNCATE TABLE `zerosum`") or die();
		foreach($hashses as $hash)
			{
			$tmp = explode('|', $hash);
			$sql = mysql_query("insert into zerosum set filename = '" . $tmp[0] . "', filehash = '" . sha1($tmp[2]) . "', permhash = '" . sha1($tmp[3]) . "'") or die();
			}
		}

	echo 'OK';
	}

# Example call: zerosum('folder');

?>