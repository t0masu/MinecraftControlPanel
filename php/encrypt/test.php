<?php
	ini_set("display_errors",1);
	include("../includes/settings.php");
	$enc = base64_encode($ControlPanel->encryptData("dev")); //encrypted data
	$dec = $ControlPanel->decryptData(base64_decode("MbitscliylLyabgNG3pVNZH3uiV/JyzrM29kiVW6ZAyoGtlxHatyW/oU5JpSmd3Oqk5gIEM9gbebDpT+ajWj7Ta3TowcCueA2EehUEr0kb/3KwBAJ+ayvnxmyPBtflYuYgtzw9HQIXbVDLzYhC7tbZfBi/HwTCFfn3uwdhbhCJjaienmcwvQhj2KSP/VZ2le014FxmqZ4wCXzWqzhC5BVh7Rmi/oejI/dWFTynwiFDUsbTogx0KJBHbzh1hMMxQz1gEOcRoeTia0dYH4Wv+qHn0tM1h2OcoJKIfp94XTZ2A45tCXHTfXUz165AtBtQr1pKhDAfjDzUWl4b5qVBMRCQ=="));
	?>
		<pre>
			<?=$enc;?>
		</pre>
		<pre>
			<?=$dec;?>
		</pre>
	<?php
?>