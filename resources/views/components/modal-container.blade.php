<!-- Update Idea -->
@can('update', $idea)
    <livewire:edit-idea :idea="$idea"/>
@endcan

<!-- Delete Idea -->
@can('delete',$idea)
    <livewire:delete-idea :idea="$idea"/>
@endcan

<!-- Mark Spam -->
@auth
    <livewire:mark-idea-spam :idea="$idea"/>
@endauth

<!-- Mark Not Spam -->
@admin
<livewire:mark-idea-not-spam :idea="$idea"/>
@endadmin

<!-- Edit Comment -->
@auth
    <livewire:edit-comment/>
@endauth

<!-- Delete Comment -->
@auth
    <livewire:delete-comment/>
@endauth

<!-- Mark Comment As Spam -->
@auth
    <livewire:mark-comment-as-spam/>
@endauth

<!-- Mark Comment As Not Spam -->
@admin
<livewire:mark-comment-as-not-spam />
@endadmin
