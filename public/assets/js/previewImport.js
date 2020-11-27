function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#cover').attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
    }
}

$("input[type=file]").change(function() {
    readURL(this);
});

document.getElementById('loadFile').onclick = function() {
    document.getElementsByClassName('custom-file-input')[0].click();
};