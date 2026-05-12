<h1>Rappel de vos tâches</h1>
<p>Bonjour,</p>
<p>Voici les tâches qui arrivent à échéance dans les prochaines 24 heures :</p>
<ul>
    @foreach($tasks as $task)
        <li><strong>{{ $task->title }}</strong> (Événement : {{ $task->event->title }}) - Échéance : {{ $task->due_date }}</li>
    @endforeach
</ul>
<p>Connectez-vous à l'application pour les gérer.</p>
<p>L'équipe FestiVault</p>
