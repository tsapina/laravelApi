<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>api Test</title>
</head>
<body>

<form method="post" action="api/users"> 
  First name:<br>
  <input type="text" name="firstname"><br>
  Last name:<br>
  <input type="text" name="lastname"> <br>
  Email:<br>
  <input type="text" name="email"> <br>
  Password:<br>
  <input type="password" name="password"> <br>
  Adress:<br>
  <input type="text" name="adress"> <br>
  RoleID: <br>
  <input type="number" name="role"> <br>

  <input type="hidden" name="_token" value="{{ csrf_token() }}">
  <input type="submit" name="submit">  
dsadsad
</form>


    
</body>
</html>