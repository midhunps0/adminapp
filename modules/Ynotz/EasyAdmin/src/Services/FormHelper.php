<?php
namespace Ynotz\EasyAdmin\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;

class FormHelper
{
    /**
     * Undocumented function
     *
     * @param string $inputType Type attribute of the html input element
     * @param string $key The 'name' attribute of the input element. This is the variable name that will be received among the request parameters.
     * @param string|null $label The label of the input element.
     * @param array|null $properties Associative array of attributes & values of the html select element. Eg: ['required' => 'true']
     * @param string $fireInputEvent Whether to fire an even on the input event of the html input. The value of the input will be included as the 'value' property of the event detail. The 'key' (name of the html element) will be included as the 'source' proerty of event detail.
     * @param array|null $updateOnEvents Eg: ['title' => [ServiceClass::class, 'method'], ...]. An associative array. Keys: Name of source elements to be listened for input events. Values: An indexed array with the name of the service class and the method which will provide the required values to be updated. The 'value' property of the listened event will be passed as the query parameter by the name 'value' (You can get it as $request->input('value') inside the method you define). The response of the method (should be a string), will be set as the value of this element.
     * @return array
     */
    public static function makeInput(
        string $inputType,
        string $key,
        string $label = null,
        array $properties = null,
        bool $fireInputEvent = false,
        array $updateOnEvents = null,
    ): array
    {
        $data =  [
            'item_type' => 'input',
            'input_type' => $inputType,
            'key' => $key,
            'label' => $label ?? $key,
            'fire_input_event' => $fireInputEvent,
            'update_on_events' => $updateOnEvents
        ];
        if (isset($properties)) {
            $data['properties'] = $properties;
        }
        return $data;
    }

    /**
     * Function makeSuggestlist
     *
     * @param string $key The 'name' attribute of the input element. This is the variable name that will be received among the request parameters.
     * @param string $label The label of the input element.
     * @param array $options_src An array with the service class and its method providing the options list [ServiceClass::class, 'method']. Eg: [App\Models\Product::class, 'suggestList']. The method may return a collection, an associative array [101 => 'Product One', ...], or an indexed array ['Product One', 'Product Two', ...] of strings.
     * @param string $options_type The return type of the $options_src method. Allowed values: 'collection', 'key_value' ,'value_only' for return types of Collection, Associative array and indexed array respectively. Default: 'collection'
     * @param string $options_id_key Required if the return type of $options_src method is collection. Default: 'id'.
     * @param string $options_text_key Required if the return type of $options_src method is collection. Default: 'name'.
     * @param string $none_selected This is the text displayed as placeholder or when no selection is made. Default: 'Select One'.
     * @param string|null $options_src_trigger The javascript event that triggers the dynamic re-loading of options. This is useful in cases such as dependent select lists.
     * @param array|null $properties Associative array of attributes & values of the html select element. Eg: ['required' => 'true']
     * @return array
     */
    public static function makeSuggestlist(
        string $key,
        string $label,
        array $options_src,
        string $options_type = 'collection',
        string $options_id_key = 'id',
        string $options_text_key = 'name',
        string $none_selected = 'Select One',
        array $properties = null,
    ): array
    {
        $data = [
            'item_type' => 'input',
            'input_type' => 'suggestlist',
            'key' => $key,
            'label' => $label,
            'options_type' => $options_type,
            'options_id_key' => $options_id_key,
            'options_text_key' => $options_text_key,
            'none_selected' => $none_selected,
            'options_src' => $options_src,
            'properties' => $properties,
        ];

        if (isset($properties)) {
            $data['properties'] = $properties;
        }

        return $data;
    }

    /**
     * Function makeSelect
     *
     * @param string $key The 'name' attribute of the input element. This is the variable name that will be received among the request parameters.
     * @param string $label The label of the input element.
     * @param array $options This may be a collection, an associative array [101 => 'Product One', ...], or an indexed array ['Product One', 'Product Two', ...] of strings.
     * @param string $options_type The return type of the $options_src method. Allowed values: 'collection', 'key_value' ,'value_only' for return types of Collection, Associative array and indexed array respectively. Default: 'collection'
     * @param string $options_id_key Required if the return type of $options is collection. Default: 'id'.
     * @param string $options_text_key Required if the return type of $options is collection. Default: 'name'.
     * @param array $options_src An array with the service class and its method providing the options list [ServiceClass::class, 'method']. Eg: [App\Models\Product::class, 'suggestList']. Used for dynamic loading of options. The method may return a collection, an associative array [101 => 'Product One', ...], or an indexed array ['Product One', 'Product Two', ...] of strings. It should be the same as the $options type.
     * @param string $none_selected This is the text displayed as placeholder or when no selection is made. Default: 'Select One'.
     * @param string|null $options_src_trigger The javascript event that triggers the dynamic re-loading of options. This is useful in cases such as dependent select lists.
     * @param array|null $properties Associative array of attributes & values of the html select element. Eg: ['required' => 'true']
     * @return array
     */
    public static function makeSelect(
        string $key,
        string $label,
        array $options,
        string $options_type = 'collection',
        string $options_id_key = 'id',
        string $options_text_key = 'name',
        array $options_src = null,
        string $none_selected = 'Select One',
        string $options_src_trigger = null,
        array $properties = null,
    ): array
    {
        $data = [
            'item_type' => 'input',
            'input_type' => 'select',
            'key' => $key,
            'label' => $label,
            'options' => $options,
            'options_type' => $options_type,
            'options_id_key' => $options_id_key,
            'options_text_key' => $options_text_key,
            'none_selected' => $none_selected,
            'options_src' => $options_src,
            'options_src_trigger' => $options_src_trigger,
            'properties' => $properties,
        ];

        if (isset($properties)) {
            $data['properties'] = $properties;
        }

        return $data;
    }
}
?>
