@props([
    'form',
    'errors',
    '_old'
])
@php
    $wclasses = [
        '1/2' => 'w-1/2',
        '1/3' => 'w-1/3',
        '1/4' => 'w-1/4',
        '2/3' => 'w-2/3',
        '2/5' => 'w-2/5',
        '3/4' => 'w-3/4',
        '3/5' => 'w-3/5',
        'full' => 'w-full'
    ];
    $form_width = isset($form['width']) && isset($wclasses[$form['width']]) ? $wclasses[$form['width']] : 'w-1/2';
@endphp
<div class="flex flex-col space-y-2 w-full items-center">
<div class="w-full text-right px-4">
    <a href="#" class="btn btn-sm py-2 normal-case" @click.prevent.stop="window.history.back();">Go Back</a>
</div>
<form x-data="{
    postUrl: '',
    successRedirectUrl: null,
    successRedirectRoute: null,
    doSubmit() {
        let form = document.getElementById('{{$form['id']}}');
        let fd = new FormData(form);
        {{-- fd.append('x_fr', 'form_user_create'); --}}
        $dispatch('formsubmit', { url: this.postUrl, formData: fd, target: '{{$form['id']}}', fragment: 'create_form' });
    }
}" action="" id="{{$form['id']}}"
@submit.prevent.stop="doSubmit();"
    @formresponse.window="console.log($event.detail);
    if ($event.detail.target == $el.id) {
        if ($event.detail.content.success) {
            $dispatch('showmodal', {message: $event.detail.content.message, mode: 'success', redirectUrl: successRedirectUrl, redirectRoute: successRedirectRoute});
        } else if (typeof $event.detail.content.errors != undefined) {
            $dispatch('formerrors', {errors: $event.detail.content.errors});
        } else{
            $dispatch('showmodal', {message: $event.detail.content.error, mode: 'error', redirectUrl: null, redirectRoute: null});
        }
    }
"
class="w-2/3 {{$form_width}} border border-base-300 bg-base-200 shadow-sm rounded-md p-3"
x-init="
    postUrl = '{{ route($form['action_route']) }}';
    @if (isset($form['success_redirect_route']))
        successRedirectUrl = '{{route($form['success_redirect_route'])}}';
        successRedirectRoute = '{{$form['success_redirect_route']}}';
    @endif
"
>
    @fragment('create_form')
        @foreach ($form['items'] as $item)
            @switch($item['item_type'])
                @case('input')
                    @switch($item['input_type'])
                        @case('select')
                            <x-easyadmin::inputs.select
                                :element="$item"
                                :label_position="$form['label_position'] ?? 'top'"
                                :_old="$_old ?? []"
                                :xerrors="$errors ?? []"
                                />
                            @break
                        @case('suggestlist')
                            <x-easyadmin::inputs.suggestlist
                                :element="$item"
                                :label_position="$form['label_position'] ?? 'top'"
                                :_old="$_old ?? []"
                                :xerrors="$errors ?? []"
                                />
                            @break
                        @case('file_uploader')
                            <x-easyadmin::inputs.fileuploader
                                :element="$item"
                                :label_position="$form['label_position'] ?? 'top'"
                                :_old="$_old ?? []"
                                :xerrors="$errors ?? []"
                                />
                            @break
                        @default
                            @php
                                $html_inputs = [
                                    "button",
                                    "checkbox",
                                    "color",
                                    "date",
                                    "datetime-local",
                                    "email",
                                    // "file",
                                    "hidden",
                                    // "image",
                                    "month",
                                    "number",
                                    "password",
                                    // "radio",
                                    // "range",
                                    // "reset",
                                    "search",
                                    // "submit",
                                    "tel",
                                    "text",
                                    "time",
                                    "url",
                                    "week",
                                ];
                            @endphp
                            @if (in_array($item['input_type'], $html_inputs))
                                <x-dynamic-component
                                :component="'easyadmin::inputs.text'"
                                :element="$item"
                                :label_position="$form['label_position'] ?? 'top'"
                                :_old="$_old ?? []"
                                :xerrors="$errors ?? []"
                                />
                            @endif
                            @break
                    @endswitch
                @default
                    @break
            @endswitch
        @endforeach
        <div class="form-control w-full mt-8 mb-4">
            <button class="btn btn-sm py-2" type="submit">Submit</button>
        </div>
    @endfragment
    <div class="form-control w-full mb-4">
        <a href="#" class="btn btn-link py-2 normal-case" @click.prevent.stop="window.history.back();">Go Back</a>
    </div>
</form>
</div>
