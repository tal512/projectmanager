<html>
<body>

<span>Login</span>
<form method="post" action="user/login">
<input type="text" name="email" placeholder="email">
<input type="text" name="password" placeholder="password">
<input type="submit">
</form>

<span>Register</span>
<form method="post" action="user/register">
<input type="text" name="email" placeholder="email">
<input type="text" name="password" placeholder="password">
<input type="text" name="name" placeholder="name">
<input type="submit">
</form>

<span>Update</span>
<form method="post" action="user/update">
<input type="text" name="id" placeholder="id">
<input type="text" name="email" placeholder="email">
<input type="text" name="password" placeholder="password">
<input type="text" name="name" placeholder="name">
<input type="text" name="authKey" placeholder="authKey">
<input type="submit">
</form>

<span>Logout</span>
<form method="post" action="user/logout">
<input type="text" name="authKey" placeholder="authKey">
<input type="submit">
</form>

</body>
</html>