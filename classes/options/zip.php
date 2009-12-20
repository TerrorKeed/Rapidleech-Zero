<?php
function zip() {
	global $optxt, $list;
	if (count ( $_GET ["files"] ) < 1) {
		echo $optxt['select_one_file']."<br><br>";
	} else {
		for($i = 0; $i < count($_GET["files"]); $i++)
            {
            $file = $list[($_GET["files"][$i])];
            }
	print "What do you want to do?<br><br>";
	?>
<form name="ziplist" method="post">
<table cellspacing="5">
<tr>
<td align="center">
<select name="act" id="act" onChange="zip();">
<option SELECTED='SELECTED'><?php echo $optxt['select_action'];?></option>
<option value="zip_add"><?php echo $optxt['add_zip'];?></option>
</select>
</td>
<td>
</td>
<td id="add" align="center" style="DISPLAY: none;">
<table>
<tr><td><?php echo $optxt['arcv_name'];?>:&nbsp;<input type="text" name="archive" size="25" value=".zip"><br></td></tr>
<tr><td><input type="checkbox" name="no_compression">&nbsp;<?php echo $optxt['no_compress'];?><br></td></tr>
<tr><td><input type="checkbox" name="remove_path">&nbsp;<?php echo $optxt['no_subdir'];?><br></td></tr>
</table>
<table>
<tr><td><input type="submit" value="<?php echo $optxt['add_file'];?>"></td></tr>
</table>
</td>
</tr>
</table>
<?php
    echo "<br>Selected File".(count($_GET["files"]) > 1 ? "s" : "").": ";
    for($i = 0; $i < count($_GET["files"]); $i++)
		{
        $file = $list[($_GET["files"][$i])];
        print "<input type=\"hidden\" name=\"files[]\" value=\"{$_GET[files][$i]}\">\r\n";
        echo "<b>".basename($file["name"])."</b>";
        echo ($i == count($_GET["files"]) - 1) ? "." : ",&nbsp;";
        }
    ?>
</form>
<?php
	}
}

function zip_go() {
	global $optxt, $list;
	//$_GET ["archive"] = DOWNLOAD_DIR .((strlen ( trim ( urldecode ( $_GET ["archive"] ) ) ) > 4 && substr ( trim ( urldecode ( $_GET ["archive"] ) ), - 4 ) == ".zip") ? trim ( urldecode ( $_GET ["archive"] ) ) : "archive.zip");

/* mod Idx */
	$_GET ["archive"] = preg_replace("/[^a-zA-Z0-9]+/", "_", urldecode($_GET ["archive"]));
	$_GET ["archive"] = DOWNLOAD_DIR .(strlen ( $_GET ["archive"] ) > 4 ? (substr ( $_GET ["archive"], - 4 ) != ".zip" ? $_GET ["archive"] . ".zip" : $_GET ["archive"]) : "archive.zip");
	for($i = 0; $i < count ( $_GET ["files"] ); $i ++) {
		$files [] = $list [($_GET ["files"] [$i])];
	}
	foreach ( $files as $file ) {
		$CurrDir = ROOT_DIR;
		$inCurrDir = stristr ( dirname ( $file ["name"] ), $CurrDir ) ? TRUE : FALSE;
		
		if ($inCurrDir) {
			$add_files [] = substr ( $file ["name"], (strlen ( $CurrDir ) + 1) );
		}
		
		else{
		    $cl_Path = relink_path(DOWNLOAD_DIR, $CurrDir, PATH_SPLITTER);			
			$add_files [] = DOWNLOAD_DIR . basename($file ["name"]);
		}
		
	}
	require_once (CLASS_DIR . "pclzip.php");
	$archive = new PclZip ( $_GET ["archive"] );
	$no_compression = ($_GET ["no_compression"] == "on") ? PCLZIP_OPT_NO_COMPRESSION : 77777;
	$remove_path = ($_GET ["remove_path"] == "on") ? PCLZIP_OPT_REMOVE_ALL_PATH : 77777;
	if (file_exists ( $_GET ["archive"] )) {
		$v_list = $archive->add ( $add_files, $no_compression, $remove_path );
	} else {
		$v_list = $archive->create ( $add_files, $no_compression, $remove_path );
	}
	if ($v_list == 0) {
		echo $optxt['_error'].": " . $archive->errorInfo ( true ) . "<br><br>";
	} else {
		echo $optxt['_arcv']." <b>" . $_GET ["archive"] . "</b> ".$optxt['success_created']."<br><br>";
	}
}
?>