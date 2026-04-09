<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>lgoin page</h1>
    <form action="{{ 'login' }}" method="post">
        @csrf
        <input name="email" type="email" placeholder="email">
        <input name="password" type="password" placeholder="password">
        <button type="submit"> login</button>
    </form>
</body>
</html>