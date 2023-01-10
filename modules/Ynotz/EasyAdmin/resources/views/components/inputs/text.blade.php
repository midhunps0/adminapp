@props([
    'element',
    '_old' => [],
    'xerrors' => [],
    'label_position' => 'top',
])
@php
    $type = $element['input_type'];
    $name = $element['key'];
    $authorised = $element['authorised'];
    $label = $element['label'];
    $width = $element['width'] ?? 'full';
    $placeholder = $element["placeholder"] ?? null;
    $wrapper_styles = $element["wrapper_styles"] ?? null;
    $input_styles = $element["input_styles"] ?? null;
    $properties = $element['properties'] ?? [];
    $fire_input_event = $element['fire_input_event'] ?? false;
    $update_on_events = $element['update_on_events'] ?? null;
    $toggle_on_events = $element['toggle_on_events'] ?? null;
    $show = $element['show'] ?? true;

    $wclass = 'w-full';
    switch($width) {
        case '1/2':
            $wclass = 'w-1/2';
            break;
        case '1/3':
            $wclass = 'w-1/3';
            break;
        case '1/4':
            $wclass = 'w-1/4';
            break;
    }
@endphp
@if ($authorised)

<div x-data="{
        textval: '',
        errors: '',
        required: false,
        listeners: {},
        toggleListeners: {},
        {{-- toggleOnEvents: false, --}}
        showelement: true,
        updateOnEvent(source, value) {
            if (Object.keys(this.listeners).includes(source)) {
                console.log('source event caught by {{$name}}');
                console.log(this.listeners[source].serviceclass);
                if (this.listeners[source].serviceclass == null) {
                    this.textval = '';
                    console.log('textval reset!');
                } else {
                    let url = '{{route('easyadmin.fetch', ['service' => '__service__', 'method' => '__method__'])}}';
                    url = url.replace('__service__', this.listeners[source].serviceclass);
                    url = url.replace('__method__', this.listeners[source].method);
                    axios.get(
                        url,
                        {
                            params: {'value': value}
                        }
                    ).then((r) => {
                        this.textval = r.data.results;
                    }).catch((e) => {
                        console.log(e);
                    });
                }
            }
        },
        toggleOnEvent(source, value) {
            if (Object.keys(this.toggleListeners).includes(source)) {
                this.toggleListeners[source].forEach((item) => {
                    switch(item.condition) {
                        case '==':
                            if (item.value == value) {
                                this.showelement = item.show;
                            }
                            break;
                        case '!=':
                            if (item.value != value) {
                                this.showelement = item.show;
                            }
                            break;
                        case '>':
                            if (item.value > value) {
                                this.showelement = item.show;
                            }
                            break;
                        case '<':
                            if (item.value < value) {
                                this.showelement = item.show;
                            }
                            break;
                        case '>=':
                            if (item.value >= value) {
                                this.showelement = item.show;
                            }
                            break;
                        case '<=':
                            if (item.value <= value) {
                                this.showelement = item.show;
                            }
                            break;
                    }
                });
            }
        }
    }"
    x-init="
        @if (!$show)
            showelement =  false;
        @endif
        @if ($xerrors->has($name))
            ers = {{json_encode($xerrors->get($name))}};
            errors = ers.reduce((r, e) => {
                return r + ' ' + e;
            }, '').trim();
        @endif
        @if (isset($properties['required']) && $properties['required'])
            required = true;
        @endif
        @if (isset($update_on_events))
            @foreach ($update_on_events as $source => $api)
                listeners.{{$source}} = {
                    serviceclass: @if (isset($api[0])) '{{$api[0]}}' @else null @endif,
                    method: @if (isset($api[1])) '{{$api[1]}}' @else null @endif,
                };
            @endforeach
            console.log('{{$name}} listeners: ');
            console.log(listeners);
        @endif
        @if (isset($toggle_on_events))
        @foreach ($toggle_on_events as $source => $conditions)
            toggleListeners.{{$source}} = [];
            @foreach ($conditions as $condition)
                toggleListeners.{{$source}}.push({
                    condition: '{{$condition[0]}}',
                    value: '{{$condition[1]}}',
                    show: {{$condition[2] ? 'true' : 'false'}},
                });
            @endforeach
        @endforeach
        @endif
        @if (isset($_old[$name]))
            textval = '{{$_old[$name]}}';
        @endif

    "
    @class([
        'relative',
        'form-control',
        '{{$wclass}}',
        'my-4' => $label_position != 'side',
        'my-6 flex flex-row' => $label_position == 'side'
    ])
    @if (isset($update_on_events) || isset($toggle_on_events))
    @eaforminputevent.window="console.log('event captured'); console.log($event.detail.source+', '+$event.detail.value); updateOnEvent($event.detail.source, $event.detail.value); toggleOnEvent($event.detail.source, $event.detail.value);"
    @endif
    @formerrors.window="if (Object.keys($event.detail.errors).includes('{{$name}}')) {
        errors = $event.detail.errors['{{$name}}'];
    }"
    x-show="showelement"
    >
    @if ($label_position != 'float')
    <label for="{{$name}}" @class([
            'label',
            'justify-start',
            'w-36' => $label_position == 'side'
        ])>
        <span class="label-text">{{$label}}</span>@if (isset($properties['required']) && $properties['required'])
        &nbsp;<span class="text-warning">*</span>@endif
    </label>
    @endif
    <div @class([
            'flex-grow' => $label_position == 'side',
            'w-full' => $label_position != 'side',
        ]) >
        <input
        id="{{$name}}" x-model="textval" type="{{$type}}" name="{{$name}}" placeholder="{{$placeholder ?? ' '}}"
            class="peer input w-full input-bordered"
            :class="errors.length == 0 || 'border-error  border-opacity-50'"
            value="{{ $_old[$name] ?? '' }}"
            @foreach ($properties as $prop => $val)
                @if (!is_bool($val))
                    {{$prop}}="{{$val}}"
                @elseif ($val)
                    {{$prop}}
                @endif
            @endforeach
            @if ($fire_input_event)
                @change="$dispatch('eaforminputevent', {source: '{{$name}}', value: textval});"
            @endif
            />

        @if ($label_position == 'float')
        <label for="{{$name}}" class="absolute text-warning peer-placeholder-shown:text-base-content duration-300 transform -translate-y-4 scale-90 top-2 left-2 z-10 origin-[0] bg-base-100 px-2 peer-focus:px-2 peer-focus:text-warning peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-90 peer-focus:-translate-y-4 transition-all">
            {{-- {{$label}} --}}
            <span>{{$label}}</span>@if (isset($properties['required']) && $properties['required'])
            &nbsp;<span class="text-warning">*</span>
            @endif
        </label>
        @endif

        <x:easyadmin::partials.errortext />
    </div>
</div>

@endif
