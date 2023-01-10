const formatted = (e) => {
    return e.toFixed(2);
};
const test = (x) => {
    return 'From utils: ' + x;
};
const createFilepondInput = (inputElement) => {
    return FilePond.create(inputElement);
};

export {formatted, test, createFilepondInput};
