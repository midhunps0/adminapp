@props([
    'name',
    'label',
    // 'options',
    'none_selected',
    'options_type' => 'key_value',
    'options_id_key' => 'id',
    'options_text_key' => 'name',
    'options_src' => null,
    'options_src_trigger' => null,
    'width' => 'full',
    'placeholder' => null,
    'wrapper_styles' => null,
    'input_styles' => null,
    '_old' => [],
    'xerrors' => [],
    'label_position' => 'top',
    'properties' => [],
])
<div x-data="{
//        values: [],
        oldVals: [],
        select_options: [],
        search: '',
        errors: '',
        multiple: false,
        search: '',
        options: [

        ],
        selected: [],
        selectedVals: [],
        show: false,
        selId: '',
        fetchOptions(val) {
            if (val.length > 2) {
                axios.get(
                    '{{route('easyadmin.fetch', ['service' => $options_src[0], 'method' => $options_src[1]])}}',
                    {
                        params: {'search': val}
                    }
                ).then((r) => {
                    this.select_options = [];
                    {{-- r.data.results.forEach((item) => {
                        this.select_options.push({key: item.{{$options_id_key}}, text: item.{{$options_text_key}}});
                    }); --}}
                    let ops = r.data.results;
                    @if ($options_type == 'key_value')
                        Object.keys(ops).forEach((key) => {
                            this.select_options.push({key: key, text: ops[key]});
                        });
                    @elseif ($options_type == 'value_only')
                        ops.forEach((op) => {
                            this.select_options.push({key: op, text: op });
                        });
                    @elseif ($options_type == 'collection')
                        ops.forEach((op) => {
                            this.select_options.push({key: op.{{$options_id_key}}, text: op.{{$options_text_key}} });
                        });
                    @endif
                    this.initOptions(this.select_options)
                }).catch((e) => {

                });
            }
        },
        open() { this.show = true; this.focusOnList(); },
        close() { this.show = false; this.search = ''; },
        isOpen() { return this.show === true },
        select(val, event) {
            let theoption = this.options.filter((op) => {
                return op.value == val;
            })[0];
            if (!theoption.selected) {
                this.options.forEach((op) => {
                    if (op.value == val) {
                        op.selected = true;
                    }
                });
                if (!this.multiple) {
                    this.selected = [];
                    this.selectedVals = [];
                }
                this.selected.push(theoption);
                this.selectedVals.push(val);
            } else {
                this.options.forEach((op) => {
                    if (op.value == val) {
                        op.selected = false;
                    }
                });
                this.selected = this.selected.filter((item) => {
                    return item.value != val;
                });
                this.selectedVals = this.selectedVals.filter((item) => {
                    return item != val;
                });
            }
            this.focusOnList();
            this.search = '';
        },
        remove(index, val) {
            let theoption = this.selected.filter((op) => {
                return op.value == val;
            })[0];
            this.options = this.options.filter((op) => {
                return op.value != val;
            });
            this.selected = this.selected.filter((op) => {
                return op.value != val;
            });
            this.selectedVals = this.selectedVals.filter((v) => {
                return v != val;
            });
        },
        initOptions(options) {
            let intvalues = this.selectedVals.map((v) => {
                return parseInt(v);
            });
            this.options = [];
            for (let i = 0; i < options.length; i++) {
                this.options.push({
                    value: options[i].key,
                    text: options[i].text,
                    selected: intvalues.includes(parseInt(options[i].key))
                });
            }
        },
        focusOnList() {
            $nextTick(() => {
                let item = document.getElementById('slinput');
                setTimeout(() => {
                    if (item != null && item != undefined) {
                        item.focus();
                    }
                }, 100);

            });
        },
    }"
    @class([
        'relative',
        'form-control',
        '{{ $wclass }}',
        'my-4' => $label_position != 'side',
        'my-6 flex flex-row' => $label_position == 'side',
    ])
    x-init="
        @if(isset($_old[$name]))
            selectedVals = [{{implode(',', $_old[$name])}}];
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
        selId = 'select-{{$name}}';
        initOptions(select_options);
        $nextTick(() => {
            if (selectedVals.length > 0) {
                let intvalues = selectedVals.map((v) => {
                    return parseInt(v);
                });
                for(i=0; i < options.length; i++) {
                    if (intvalues.includes(parseInt(options[i].value))) {
                        selected.push(i);
                        selectedVals.push(options[i].value);
                    }
                };
            }
        });

        @if (isset($properties['multiple']) && $properties['multiple'])
            multiple = true;
        @endif
    "
    @sldosearch.window="fetchOptions($event.detail.searchstr);"
    >
    @if ($label_position != 'float')
        <label @class(['label', 'justify-start', 'w-36' => $label_position == 'side'])>
            <span class="label-text">{{ $label }}</span>@if (isset($properties['required']) && $properties['required'])
            &nbsp;<span class="text-warning">*</span>@endif
        </label>
    @endif
    <div @class([
            'flex-grow' => $label_position == 'side',
            'w-full' => $label_position != 'side',
        ])>
        <select x-model="selectedVals" id="select-{{$name}}" @if(isset($properties['multiple']) && $properties['multiple']) name="{{ $name }}[]" @else name="{{$name}}" @endif class="h-0 w-11/12 absolute -z-0 rounded-md left-1 top-4 overflow-hidden"
            @foreach ($properties as $prop => $val)
                @if (!is_bool($val))
                    {{ $prop }}="{{ $val }}"
                @elseif ($val)
                    {{ $prop }}
                @endif
            @endforeach @if(isset($properties['multiple']) && $properties['multiple'])  multiple @endif>
            <template x-for="op in select_options">
                <option :value="op.key" x-text="op.text" :selected="selectedVals.includes(op.key)"></option>
            </template>
        </select>

        <div class="w-full flex flex-col items-center">
            <div class="inline-block relative w-full">
                <div class="flex flex-col items-center relative">
                    <div x-on:click="open" class="w-full">
                        <div class="p-1 input flex border bg-base-100 rounded-lg"
                        :class="errors.length > 0 ? 'border-error border-opacity-50' : 'border-base-content border-opacity-20'">
                            <div class="flex flex-auto flex-wrap">
                                <template x-for="(option,index) in selected" :key="option.value">
                                    <div
                                        class="flex justify-center items-center m-1 font-medium py-0 px-1 bg-base-100 rounded border border-base-300">
                                        <div class="text-xs font-normal leading-none max-w-full flex-initial" x-model="option.value" x-text="option.text"></div>&nbsp;
                                        <div class="flex flex-auto flex-row-reverse">
                                            <button x-on:click.stop="remove(index,option.value)">
                                                <svg class="fill-current h-4 w-4 text-base-content opacity-50" role="button" viewBox="0 0 20 20">
                                                    <path
                                                        d="M14.348,14.849c-0.469,0.469-1.229,0.469-1.697,0L10,11.819l-2.651,3.029c-0.469,0.469-1.229,0.469-1.697,0
                                                     c-0.469-0.469-0.469-1.229,0-1.697l2.758-3.15L5.651,6.849c-0.469-0.469-0.469-1.228,0-1.697s1.228-0.469,1.697,0L10,8.183
                                                     l2.651-3.031c0.469-0.469,1.228-0.469,1.697,0s0.469,1.229,0,1.697l-2.758,3.152l2.758,3.15
                                                     C14.817,13.62,14.817,14.38,14.348,14.849z" />
                                                </svg>

                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            <div
                                class="text-gray-300 w-8 py-1 pl-2 pr-1 border-l flex items-center border-base-200 svelte-1l8159u">

                                <button type="button" x-show="isOpen() === true" x-on:click="open();"
                                    class="cursor-pointer w-6 h-6 text-gray-600">
                                    <x-easyadmin::display.icon icon="easyadmin::icons.zoom_out"/>
                                </button>
                                <button type="button" x-show="isOpen() === false" @click="close(); focusOnList();"
                                    class="cursor-pointer w-6 h-6 text-gray-600">
                                    <x-easyadmin::display.icon icon="easyadmin::icons.zoom_in"/>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="w-full px-4">
                        <div x-show.transition.origin.top="isOpen()"
                            class="absolute shadow top-100 bg-base-100 z-40 w-full left-0 rounded max-h-select"
                            x-on:click.away="close">
                            <div @keyup.prevent="if($event.key=='Escape'){close();}" class="flex flex-col w-full overflow-y-auto h-48 border border-base-200" id="multiselect-items">
                                <input id="slinput" x-model="search" @keyup="$dispatch('sldosearch', {searchstr: search});" type="text" placeholder="Search" class="input input-md border border-base-content border-opacity-20 bg-base-100">
                                <template x-for="(option,index) in options" :key="option.value"
                                    class="overflow-auto">
                                    <button @click.prevent.stop="false;" x-show="!option.selected" class="cursor-pointer w-full border-base-200 rounded-t border-b focus:outline-none focus:bg-base-200 hover:bg-base-200 js-btn"
                                        @click="select(option.value,$event)">
                                        <div
                                            class="flex w-full items-center p-2 pl-2 border-transparent border-l-2 relative">
                                            <div class="w-full items-center flex justify-between">
                                                <div class="mx-2 leading-6" x-text="option.text"></div>
                                                <div x-show="option.selected">
                                                    <svg class="svg-icon" viewBox="0 0 20 20">
                                                        <path fill="none"
                                                            d="M7.197,16.963H7.195c-0.204,0-0.399-0.083-0.544-0.227l-6.039-6.082c-0.3-0.302-0.297-0.788,0.003-1.087
                                      C0.919,9.266,1.404,9.269,1.702,9.57l5.495,5.536L18.221,4.083c0.301-0.301,0.787-0.301,1.087,0c0.301,0.3,0.301,0.787,0,1.087
                                      L7.741,16.738C7.596,16.882,7.401,16.963,7.197,16.963z">
                                                        </path>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($label_position == 'float')
            <label
                class="absolute duration-300 bg-base-100 px-2 transition-all left-2"
                :class="selectedVals.length > 0 ? 'text-warning transform -translate-y-4 scale-90 top-2 z-10 origin-[0]' : 'transform translate-y-2 top-2'"
                >
                <span>{{ $label }}</span>@if (isset($properties['required']) && $properties['required'])
                &nbsp;<span class="text-warning">*</span>@endif
            </label>
        @endif
        <x:easyadmin::partials.errortext />
    </div>
</div>
