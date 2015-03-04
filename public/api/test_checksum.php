<html>
<body>

<form method="post">
<input type="text" name="data" placeholder="data" value="<?= (isset($_POST['data'])) ? $_POST['data'] : ''; ?>">
<input type="text" name="privateKey" placeholder="privateKey" value="<?= (isset($_POST['privateKey'])) ? $_POST['privateKey'] : ''; ?>">
<input type="submit">
</form>

<?= (isset($_POST['data'])) ? hash_hmac('sha256', $_POST['data'], $_POST['privateKey']) : ''; ?>

</body>
</html>