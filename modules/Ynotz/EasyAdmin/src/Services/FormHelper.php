<?php
namespace Ynotz\EasyAdmin\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;

class FormHelper
{
    /**
     * function makeInput
     *
     * @param string $inputType Type attribute of the html input element
     * @param string $key The column name in the model table or the name of the relation. It will be used as the 'name' attribute of the input element. This is the variable name that will be received among the request parameters.
     * @param string|null $label The label for the input element.
     * @param array|null $properties Associative array of attributes & values of the html select element. Eg: ['required' => 'true']
     * @param bool $fireInputEvent Whether to fire an even on the input event of the html input. The value of the input will be included as the 'value' property of the event detail. The 'key' (name of the html element) will be included as the 'source' proerty of event detail.
     * @param array|null $updateOnEvents Eg: ['sourcename' => [ServiceClass::class, 'method'], ...]. An associative array. Keys: Name of source elements to be listened for input events. Values: An indexed array with the name of the service class and the method which will provide the required values to be updated. If this array is empty, the value will be reset to empty string. The 'value' property of the listened event is passed to the defined service class method as an argument. The response of the method (should be a string), will be set as the value of this element.
     * @param array|null $toggleOnEvents Format: ['sourcename' => [['condition', 'value', show(true/false)]], ...] Eg: ['gender' => [['==', 'Male', true], ['==', 'Any', true]]]
     * @param bool $show Whether to show the element
     * @param bool $authorised Whether the user is authorised to access this element. If false, the element will not be rendered.
     * @return array
     */
    public static function makeInput(
        string $inputType,
        string $key,
        string $label = null,
        array $properties = null,
        bool $fireInputEvent = false,
        array $updateOnEvents = null,
        array $toggleOnEvents = null,
        bool $show = true,
        bool $authorised = true,
    ): array
    {
        $data =  [
            'item_type' => 'input',
            'input_type' => $inputType,
            'key' => $key,
            'label' => $label ?? $key,
            'fire_input_event' => $fireInputEvent,
            'update_on_events' => $updateOnEvents,
            'toggle_on_events' => $toggleOnEvents,
            'show' => $show,
            'authorised' => $authorised,
        ];
        if (isset($properties)) {
            $data['properties'] = $properties;
        }
        return $data;
    }

    /**
     * Function makeSuggestlist
     *
     * @param string $key The column name in the model table or the name of the relation. It will be used as the 'name' attribute of the input element. This is the variable name that will be received among the request parameters.
     * @param string $label The label for the input element.
     * @param array $options_src An array with the service class and its method providing the options list [ServiceClass::class, 'method']. Eg: [App\Models\Product::class, 'suggestList']. The method may return a collection, an associative array [101 => 'Product One', ...], or an indexed array ['Product One', 'Product Two', ...] of strings.
     * @param string $options_type The return type of the $options_src method. Allowed values: 'collection', 'key_value' ,'value_only' for return types of Collection, Associative array and indexed array respectively. Default: 'collection'
     * @param string $options_id_key Required if the return type of $options_src method is collection. Default: 'id'.
     * @param string $options_text_key Required if the return type of $options_src method is collection. Default: 'name'.
     * @param string $none_selected This is the text displayed as placeholder or when no selection is made. Default: 'Select One'.
     * @param string|null $options_src_trigger The javascript event that triggers the dynamic re-loading of options. This is useful in cases such as dependent select lists.
     * @param array|null $properties Associative array of attributes & values of the html select element. Eg: ['required' => 'true']
     * @param string $fireInputEvent Whether to fire an even on the input event of the html input. The value of the input will be included as the 'value' property of the event detail. The 'key' (name of the html element) will be included as the 'source' proerty of event detail.
     * @param array|null $resetOnEvents An indexed array of html input element names, for whose value change this field should reset. Eg: ['title', 'another_input', ..]
     * @param array|null $toggleOnEvents Format: ['sourcename' => [['condition', 'value', show(true/false)]], ...] Eg: ['gender' => [['==', 'Male', true], ['==', 'Any', true]]]
     * @param bool $show Whether to show the element
     * @param bool $authorised Whether the user is authorised to access this element. If false, the element will not be rendered.
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
        bool $fireInputEvent = false,
        array $resetOnEvents = null,
        array $toggleOnEvents = null,
        bool $show = true,
        bool $authorised = true,
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
            'fire_input_event' => $fireInputEvent,
            'reset_on_events' => $resetOnEvents,
            'toggle_on_events' => $toggleOnEvents,
            'show' => $show,
            'authorised' => $authorised,
        ];

        if (isset($properties)) {
            $data['properties'] = $properties;
        }

        return $data;
    }

    /**
     * Function makeSelect
     *
     * @param string $key The column name in the model table or the name of the relation. It will be used as the 'name' attribute of the input element. This is the variable name that will be received among the request parameters.
     * @param string $label The label for the input element.
     * @param Collection|array $options This may be a collection, an associative array [101 => 'Product One', ...], or an indexed array ['Product One', 'Product Two', ...] of strings.
     * @param string $options_type The return type of the $options_src method. Allowed values: 'collection', 'key_value' ,'value_only' for return types of Collection, Associative array and indexed array respectively. Default: 'collection'
     * @param string $options_id_key Required if the return type of $options is collection. Default: 'id'.
     * @param string $options_text_key Required if the return type of $options is collection. Default: 'name'.Used for dynamic loading of options. The method may return a collection, an associative array [101 => 'Product One', ...], or an indexed array ['Product One', 'Product Two', ...] of strings. It should be the same as the $options type.
     * @param string $none_selected This is the text displayed as placeholder or when no selection is made. Default: 'Select One'.
     * @param array|null $properties Associative array of attributes & values of the html select element. Eg: ['required' => 'true']
     * @param string $fireInputEvent Whether to fire an even on the input event of the html input. The value of the input will be included as the 'value' property of the event detail. The 'key' (name of the html element) will be included as the 'source' proerty of event detail.
     * @param array|null $resetOnEvents An indexed array of html input element names, for whose value change this field should reset. Eg: ['title', 'another_input', ..]
     * @param array $options_src An array with the service class and its method providing the options list [ServiceClass::class, 'method']. Eg: [App\Models\Product::class, 'suggestList'].
     * @param array|null $toggleOnEvents Format: ['sourcename' => [['condition', 'value', show(true/false)]], ...] Eg: ['gender' => [['==', 'Male', true], ['==', 'Any', true]]]
     * @param bool $show Whether to show the element
     * @param bool $authorised Whether the user is authorised to access this element. If false, the element will not be rendered.
     * @return array
     */
    public static function makeSelect(
        string $key,
        string $label,
        Collection|array $options,
        string $options_type = 'collection',
        string $options_id_key = 'id',
        string $options_text_key = 'name',
        string $none_selected = 'Select One',
        array $properties = null,
        bool $fireInputEvent = false,
        array $resetOnEvents = null,
        array $options_src = null,
        array $toggleOnEvents = null,
        bool $show = true,
        bool $authorised = true,
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
            'properties' => $properties,
            'fire_input_event' => $fireInputEvent,
            'reset_on_events' => $resetOnEvents,
            'options_src' => $options_src,
            'toggle_on_events' => $toggleOnEvents,
            'show' => $show,
            'authorised' => $authorised,
        ];

        if (isset($properties)) {
            $data['properties'] = $properties;
        }

        return $data;
    }

    /**
     * function makeFileUploader
     *
     * @param string $key The column name in the model table or the name of the relation. It will be used as the 'name' attribute of the input element. This is the variable name that will be received among the request parameters.
     * @param string|null $label The label for the input element.
     * @param array|null $properties Associative array of attributes & values of the html select element. Eg: ['required' => 'true']
     * @param bool $fireInputEvent Whether to fire an even on the input event of the html input. The value of the input will be included as the 'value' property of the event detail. The 'key' (name of the html element) will be included as the 'source' proerty of event detail.
     * @param array|null $updateOnEvents Eg: ['sourcename' => [ServiceClass::class, 'method'], ...]. An associative array. Keys: Name of source elements to be listened for input events. Values: An indexed array with the name of the service class and the method which will provide the required values to be updated. If this array is empty, the value will be reset to empty string. The 'value' property of the listened event is passed to the defined service class method as an argument. The response of the method (should be a string), will be set as the value of this element.
     * @param array|null $toggleOnEvents Format: ['sourcename' => [['condition', 'value', show(true/false)]], ...] Eg: ['gender' => [['==', 'Male', true], ['==', 'Any', true]]]
     * @param bool $show Whether to show the element
     * @param bool $authorised Whether the user is authorised to access this element. If false, the element will not be rendered.
     * @return array
     */
    public static function makeFileUploader(
        string $key,
        string $label = null,
        array $properties = null,
        bool $fireInputEvent = false,
        array $updateOnEvents = null,
        array $toggleOnEvents = null,
        bool $show = true,
        bool $authorised = true,
        ): array
    {
        $data =  [
            'item_type' => 'input',
            'input_type' => 'file_uploader',
            'key' => $key,
            'label' => $label ?? $key,
            'fire_input_event' => $fireInputEvent,
            'update_on_events' => $updateOnEvents,
            'toggle_on_events' => $toggleOnEvents,
            'show' => $show,
            'authorised' => $authorised,
        ];
        if (isset($properties)) {
            $data['properties'] = $properties;
        }
        return $data;
    }
}
?>
