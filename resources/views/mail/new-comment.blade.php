<x-mail::message>
# New comment

    Your post with id: {{$post->id}} has new comment from {{$user->name}}.

    Comment: {{$comment->content}}

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
