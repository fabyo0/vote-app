@component('mail::message')

# A comment was posted on your idea

{{ $comment->user->name }} comment on your idea

**{{ $comment->idea->title }}**

Comment {{ \Illuminate\Support\Str::limit($comment->body,50)  }}

@component('mail::button', ['url' => route('idea.show',$comment->idea)])
Go to Idea
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
