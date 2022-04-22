<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votre tâche est proche de prendre fin</title>
</head>
<body>
    <p>  Bonjour {{ $task->user()->identifiant }} merci pour votre confiance en nous.</p>
<div>Vous avez crée la tâche {{ $task->title }} le {{ $task->start_date }} <br>
    qui consiste à {{ $task->description }} et qui devrait finir le {{ $task->date_of_end }},<br>
     malheureusement ou heureusement cette tâche prend fin dans la journéé.Nous tenons juste à <br>
     vous rappeler cela afin que vous puissiez finir votre tâche en beauté.! Cordialement </div>
</body>
</html>
