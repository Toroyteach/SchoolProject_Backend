<?php

global $baseUrl;

$baseUrl = "http://localhost/schoolProject/testTwo/admin/";
$prodUrl = "https://schoolproject.toroyteach.com/";

function pr($arr)
{
	echo '<pre>';
	print_r($arr);
}

function prx($arr)
{
	echo '<pre>';
	print_r($arr);
	die();
}

function get_safe_value($con, $str)
{
	if ($str != '') {
		$str = trim($str);
		return mysqli_real_escape_string($con, $str);
	}
}
function isAdmin()
{
	global $baseUrl;

	if (!isset($_SESSION['ADMIN_LOGIN'])) {
?>
		<script>
			window.location.href = '<?php echo $baseUrl; ?>login.php';
		</script>
	<?php
	}
	if ($_SESSION['ADMIN_ROLE'] == 1) {
	?>
		<script>
			window.location.href = '<?php echo $baseUrl; ?>logout.php';
		</script>
<?php
	}
}
?>