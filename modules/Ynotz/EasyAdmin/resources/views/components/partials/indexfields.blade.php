@props(['result', 'columns'])

{{-- @foreach ($results as $result) --}}
    {{-- <tr> --}}
        @foreach ($columns as $col)
            <x-dynamic-component :component="'easyadmin::display.'.$col['component']"
                :row_data="$result"
                :col="$col"/>
        @endforeach
    {{-- </tr> --}}
{{-- @endforeach --}}
