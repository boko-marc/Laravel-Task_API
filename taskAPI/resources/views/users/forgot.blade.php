<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié</title>
</head>
<body>
    <p>  Bonjour {{ $name }} soyez les bienvenues sur notre plateforme.</p>
<div> Suite à votre demande de mot de passe oublié, vous recevez cet email afin de pouvoir créer un nouveau mot de passe. <br>
    Veuillez cliquer sur le lien ci dessous afin de créer un nouveau mot de passe.
</div>
<a href="http://localhost:8000/forgot/{{$token}}">Cliquez ici</a>
</body>
</html>
