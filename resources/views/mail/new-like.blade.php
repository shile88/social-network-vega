<x-mail::message>
# New like

    Your post with id: {{$post->id}} has new like from {{$user->name}}.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
