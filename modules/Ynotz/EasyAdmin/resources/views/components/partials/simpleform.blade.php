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
<form x-data="{
    postUrl: '{{ route($form['action_route']) }}',
    doSubmit() {
        let form = document.getElementById('{{$form['id']}}');
        let fd = new FormData(form);
        {{-- fd.append('x_fr', 'form_user_create'); --}}
        $dispatch('formsubmit', { url: this.postUrl, formData: fd, target: '{{$form['id']}}', fragment: 'create_form' });
    }
}" action="" id="{{$form['id']}}" @submit.prevent.stop="doSubmit();"
    @formresponse.window="
        console.log('formsesponse..');
    if ($event.detail.target == $el.id) {
        $el.innerHTML = $event.detail.content;
    }
"
class="min-w-fit {{$form_width}} border border-base-300 bg-base-200 shadow-sm rounded-md p-3"
>
@fragment('create_form')
    @foreach ($form['items'] as $item)
        @switch($item['item_type'])
            @case('input')
                @switch($item['input_type'])
                    @case('select')
                        <x-easyadmin::inputs.select
                            :options="$item['options']"
                            :options_type="$item['options_type']"
                            :options_id_key="$item['options_id_key'] ?? 'id'"
                            :options_text_key="$item['options_text_key'] ?? null"
                            :options_src="$item['options_src'] ?? null"
                            :options_src_trigger="$item['options_src_trigger'] ?? null"
                            :none_selected="$item['none_selected']"
                            :name="$item['key']"
                            :label="$item['label']"
                            :label_position="$form['label_position'] ?? 'top'"
                            :_old="$_old ?? []"
                            :xerrors="$errors ?? []"
                            :properties="$item['properties'] ?? []"
                            />
                        @break
                    @case('suggestlist')
                        <x-easyadmin::inputs.suggestlist
                            :options_type="$item['options_type']"
                            :options_id_key="$item['options_id_key']"
                            :options_text_key="$item['options_text_key']"
                            :options_src="$item['options_src']"
                            :options_src_trigger="$item['options_src_trigger']"
                            :none_selected="$item['none_selected']"
                            :name="$item['key']"
                            :label="$item['label']"
                            :label_position="$form['label_position'] ?? 'top'"
                            :_old="$_old ?? []"
                            :xerrors="$errors ?? []"
                            :properties="$item['properties'] ?? []"
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
                                "file",
                                "hidden",
                                "image",
                                "month",
                                "number",
                                "password",
                                "radio",
                                "range",
                                "reset",
                                "search",
                                "submit",
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
                            :type="$item['input_type']"
                            :name="$item['key']"
                            :label="$item['label']"
                            :label_position="$form['label_position'] ?? 'top'"
                            :_old="$_old ?? []"
                            :xerrors="$errors ?? []"
                            :properties="$item['properties'] ?? []"
                            :fire_input_event="$item['fire_input_event'] ?? false"
                            :update_on_events="$item['update_on_events'] ?? null"
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
</form>
