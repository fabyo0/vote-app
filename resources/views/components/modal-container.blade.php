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
