@props([
    'element',
    '_old' => [],
    'xerrors' => [],
    'label_position' => 'top',
])
@php
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
    if ($label_position == 'side') {
        $wclass .= ' my-6 flex flex-row';
    } else {
        $wclass .= ' my-4';
    }
    $elid = Illuminate\Support\Str::ulid();
@endphp
@if ($authorised)
    <div x-data="
        {
            url: '{{route('easyadmin.file_upload')}}',
            deleteUrl: '{{route('easyadmin.file_delete')}}',
            inputElement: null,
            files: [],
            invalidatedFiles: [],
            theme: 'rounded',
            allowFromGallery: false,
            multiple: false,
            required: false,
            mimeTypes: [],
            maxSizeMB: 5,
            showConfirm: false,
            deleteItemKey: null,
            errors: [],
            initFilepicker() {
                if (!this.allowFromGallery) {
                    this.inputElement.click();
                } else {
                    alert('implement file chooser modal');
                }
            },
            validateType(file) {
                if(this.mimeTypes.length > 0 && this.mimeTypes.indexOf(file.type) == -1) {
                    return false;
                }
                return true;
            },
            validateSize(file) {
                if(file.size > this.maxSizeMB * 1024 * 1024) {
                    return false;
                }
                return true;
            },
            doUpload(files) {
                if (!this.multiple) { this.files = []; }
                for(i = 0; i < this.inputElement.files.length; i++) {
                    file = this.inputElement.files[i];
                    // validate size
                    // validate type
                    newFile = {
                        file: file,
                        name: file.name,
                        uploaded_pc: 0,
                        id: (new Date()).getTime() + Math.floor(Math.random() * 100),
                        path: '',
                        show: true,
                        fromServer: false,
                        sizeValidation: this.validateSize(file),
                        typeValidation:this.validateType(file)
                    };
                    this.files.push(newFile);
                    if ( newFile.sizeValidation && newFile.typeValidation) {
                        this.upoladFile(newFile);
                    }
                    {{-- if ( sizeValidation && typeValidation) {
                        this.files.push(newFile);
                        this.upoladFile(newFile);
                    } else {
                        newFile.sizeValidation = sizeValidation;
                        newFile.typeValidation = typeValidation;
                        this.invalidatedFiles.push(newFile);
                    } --}}
                }
                {{-- if (this.inputElement != null) { this.inputElement.value = ''; } --}}
            },
            upoladFile(file) {
                let formData = new FormData();
                formData.append('file', file.file);
                axios.post(
                    this.url,
                    formData,
                    {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                        },
                        onUploadProgress: (event) => {
                            let pc = Math.floor((event.loaded * 100) / event.total);
                            this.files.forEach((f) => {
                                if (f.id == file.id) { f.uploaded_pc = pc; }
                            });
                        },
                    }
                ).then((r) => {
                    file.uploaded_pc = 100;
                    this.files.forEach((f) => {
                        if (f.id == file.id) { f.path = r.data.path; }
                    });
                }).catch((e) => {
                    console.log(e);
                });
            },
            doRemove(id) {
                let theFile = this.files.filter((f) => {
                    return f.id == id;
                })[0];
                let formData = new FormData();
                formData.append('_method', 'delete');
                formData.append('file', theFile.path);
                axios.post(
                    this.deleteUrl,
                    formData,
                        {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                        }
                    }
                ).then((r) => {
                    if (r.data.success) {
                        this.files.forEach((f) => {
                            if (f.id == id) {
                                f.show = false;
                                setTimeout(() => {
                                    this.files = this.files.filter((f) => {
                                        return f.id != id;
                                    });
                                }, 100);
                            }
                        });
                        this.deleteItemKey = null;
                    }
                }).catch((e) => {
                    console.log(e);
                });
            },
            removeSelectedFile(id, isValidated = true) {
                if (!isValidated) {
                    this.invalidatedFiles = this.invalidatedFiles.filter((f) => {
                        return f.id!== id;
                    });
                } else {
                    let file = this.files.filter((f) => {
                        return f.id == id;
                    })[0];
                    if (!file.fromServer) {
                        this.doRemove(id);
                    } else {
                        {{-- this.deleteItemKey = id;
                        this.showConfirm = true; --}}
                        this.files = this.files.filter((f) => {
                            return f.id!== id;
                        });
                    }
                }
            },
            confirmDelete() {
                this.doRemove(this.deleteItemKey);
            },
            cancelDelete(id) {}
        }
        "
        x-init="
            @if (isset($element['url']))
                url = '{{ $element['url'] }}';
            @endif
            @if (isset($element['delete_url']))
                deleteUrl = '{{ $element['delete_url'] }}';
            @endif
            inputElement = document.getElementById('{{$elid}}');
            @if (isset($properties['mime_types']) && count($properties['mime_types']) > 0)
                mimeTypes = [
                @foreach ($properties['mime_types'] as $type)
                    '{{$type}}',
                @endforeach
                ];
            @endif
            @if (isset($properties['max_size']))
                mxSizeMB = $properties['max_size'];
            @endif
            @if (isset($properties['multiple']) && $properties['multiple'])
                multiple = true;
            @endif
            @if (isset($properties['required']) && $properties['required'])
                required = true;
            @endif
            @if (isset($_old[$name]))
                @if(isset($properties['multiple']) && $properties['multiple'])
                @foreach ($_old[$name] as $f)
                    this.files.push({
                        file: null,
                        name: ('{{$f}}'.split('/')).pop(),
                        uploaded_pc: 100,
                        id: (new Date()).getTime() + Math.floor(Math.random() * 100),
                        path: '{{$f}}',
                        show: true,
                        fromServer: true
                    });
                @endforeach
                @else
                    this.files.push({
                        file: null,
                        name: ('{{$_old[$name]}}'.split('/')).pop(),
                        uploaded_pc: 100,
                        id: (new Date()).getTime() + Math.floor(Math.random() * 100),
                        path: '{{$_old[$name]}}',
                        show: true,
                        fromServer: true
                    });
                @endif
            @endif
            @if ($xerrors->has($name))
                @if(isset($properties['multiple']) && $properties['multiple'])
                    console.log(JSON.parse('{{json_encode($xerrors->get($name))}}'));
                @else
                    ers = {{json_encode($xerrors->get($name))}};
                    errors.push(ers.reduce((r, e) => {
                        return r + ' ' + e;
                    }, '').trim());
                @endif
            @endif
        "
        @class([
            'relative',
            'form-control',
            '{{$wclass}}',
            'my-4' => $label_position != 'side',
            'my-6 flex flex-row' => $label_position == 'side'
        ])
        >
        @if ($label_position != 'float')
            <label @class([
                    'label',
                    'justify-start',
                    'w-36' => $label_position == 'side'
                ])>
                <span class="label-text">{{$label}}</span>@if (isset($properties['required']) && $properties['required'])
                &nbsp;<span class="text-warning">*</span>@endif
            </label>
        @endif
        <template x-for="file in files">
            <input type="hidden" @if (isset($properties['multiple']) && $properties['multiple']) name="{{$name}}[]" @else name="{{$name}}" @endif x-model="file.path">
        </template>
        <div class="border border-base-content border-opacity-20 rounded-lg bg-base-100 flex flex-row flex-wrap justify-start relative p-2 space-x-2 flex-grow @if ($label_position == 'side') 'flex-grow' @else 'w-full' @endif">
            @if ($label_position == 'float')
                <label class="relative">
                    <span class="label-text">{{$label}}</span>@if (isset($properties['required']) && $properties['required'])
                    &nbsp;<span class="text-warning">*</span>@endif
                </label>
            @endif
            <div class="relative">
                <button type="button" tabindex="0" x-show="files.length == 0 || multiple == true" @click="initFilepicker()" class="h-12 w-32 border border-base-content border-opacity-30 border-dotted flex flex-row justify-center items-center" :class="theme == 'rounded' ? 'rounded-full' : 'rounded-md'">
                    <span class="opacity-30"><x-easyadmin::display.icon icon="easyadmin::icons.plus"/></span>
                </button>
                <input type="file" id="{{$elid}}" class="h-1 absolute -z-10 left-0" @if (isset($properties['required']) && $properties['required']) required @endif @if (isset($properties['multiple']) && $properties['multiple']) multiple @endif
                    @change="doUpload()"
                    >
            </div>
            <template x-for="file in files">
                <div x-show="file.show == true" x-transition.dutation.100ms class="px-2 py-2 space-x-4 flex flex-row border border-base-content border-opacity-20 items-center bg-base-200" :class="theme == 'rounded' ? 'rounded-full' : 'rounded-md'">
                    <div class="radial-progress text-xs" :class="file.uploaded_pc == 100 ? 'text-success' : 'text-base-content opacity-30'" style="--size:0.8rem; --thickness: 2px;" :style="'--value:'+file.uploaded_pc+'; --size: 2rem; --thickness: 2px;'">
                        <span x-show="!file.fromServer && file.uploaded_pc == 100" x-transition><x-easyadmin::display.icon icon="easyadmin::icons.tick" /></span>
                        <span x-show="file.fromServer && file.uploaded_pc == 100" x-transition><x-easyadmin::display.icon icon="easyadmin::icons.cloud_up" /></span>
                    </div>
                    <span class="flex flex-row items-center justify-start" x-text="file.name"></span>
                    <button type="button" tabindex="0" x-show="file.uploaded_pc == 100" x-transition class="border border-warning border-opacity-50 p-0 h-6 w-6 flex flex-row items-center justify-center text-error opacity-70" :class="theme == 'rounded' ? 'rounded-full' : 'rounded-sm'" @click.prevent.stop="removeSelectedFile(file.id);">
                        <x-easyadmin::display.icon icon="easyadmin::icons.close" height="h-4" width="w-4"/>
                    </button>
                </div>
            </template>
            <template x-for="file in invalidatedFiles">
                <div x-show="file.show == true" x-transition.dutation.500ms class="px-2 py-2 space-x-4 flex flex-row border border-error border-opacity-50 items-center bg-error bg-opacity-30" :class="theme == 'rounded' ? 'rounded-full' : 'rounded-md'">
                    <span class="flex flex-row items-center justify-start" x-text="file.name"></span>
                    <button type="button" tabindex="0" x-transition class="border border-warning border-opacity-50 p-0 h-6 w-6 flex flex-row items-center justify-center text-error opacity-70" :class="theme == 'rounded' ? 'rounded-full' : 'rounded-sm'" @click.prevent.stop="removeSelectedFile(file.id, false);">
                        <x-easyadmin::display.icon icon="easyadmin::icons.close" height="h-4" width="w-4"/>
                    </button>
                </div>
            </template>
        </div>
        {{-- <x-easyadmin::display.confirm message="The file will be deleted from the server. Do you want to coninue?" showKey="showConfirm" :okFunction="'confirmDelete'" :cancelFunction="'cancelDelete'"/> --}}
    </div>
@endif
