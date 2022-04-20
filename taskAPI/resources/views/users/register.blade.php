<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register compte validation</title>
</head>
<body>
    <p>  Bonjour {{ $register->identifiant }} soyez les bienvenues sur notre plateforme.</p>
<div> Suite à votre inscription, vous recevez cet email afin de pouvoir valider votre compte. <br>
    Veuillez cliquer sur le lien ci dessous afin de valider votre compte
</div>
<a href="http://localhost:8000/validation/{{$register->api_key}}">Cliquez ici</a>
</body>
</html>
