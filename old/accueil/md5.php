<?php include("../includes/header.php"); ?>

<h1>MD5</h1>

Entrer votre cl&eacute; &agrave; transformer avec MD5 :
<form name="md5" action="" method="post">
<input name="md5" type="password" value="" />
<input type="submit" value="Valider" />
</form>

<?php 

if (isset($_POST['md5'])){
	echo md5($_POST['md5']);
}

?>

<?php include("../includes/footer.php"); ?>