@props(['row_data', 'col'])
@php
    $edit_key = $col['edit_key'] ?? 'id';
@endphp
<td class="sticky !left-36 z-20">
    <div class="flex flex-row justify-start space-x-4 items-center">
        <a href=""
            @click.prevent.stop="$dispatch('linkaction', {link: '{{$col['edit_route'], $row_data->$edit_key}}', route: '{{$col['edit_route']}}'});"
            class="btn btn-ghost btn-xs text-warning capitalize">
            <x-easyadmin::display.icon icon="icons.edit" height="h-4" width="w-4"/>
        </a>
        <button @click.prevent.stop="$dispatch('deleteitem', {itemId: {{$row_data->$edit_key}}});" class="btn btn-ghost btn-xs text-error capitalize"><x-easyadmin::display.icon icon="icons.delete" height="h-4" width="w-4"/></button>
    </div>
</td>
