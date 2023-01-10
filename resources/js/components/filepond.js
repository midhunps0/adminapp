import * as FilePond from 'filepond';

export default () => ({
    required: false,
    multiple: false,
    oldFiles: [],
    createFilepondInput (inputElement) {
        return FilePond.create(inputElement);
    },
});
