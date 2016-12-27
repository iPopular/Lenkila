debugger;
$(window).click(function(e) {
    if(e.target.className.indexOf('input-password') == -1 && e.target.className.indexOf('btn-password') == -1) {
        toggleAllPassword();
    }
});

$('.btn-password').click(function(){
    var divId = this.id.split('-');
    toggleAllPassword();
    toggleOnePassword(divId[2]);    
    //$('#'+this.id).hide();
    var el = document.getElementById('div-btn-password-' + divId[2]);
    el.style.display = 'none';
});

function toggleOnePassword (id) {
    var element = document.getElementById('div-password-' + id);

    if(element.style.display == 'none')
        element.style.display = 'block';
    else
        element.style.display = 'none';
}

function toggleAllPassword () {    
    var divPasswordElements = document.getElementsByClassName('div-password');
    var btnPasswordElements = document.getElementsByClassName('div-btn-password');
    [].forEach.call(divPasswordElements, function( el ) {
        el.style.display = 'none';
    });

    [].forEach.call(btnPasswordElements, function( el ) {
        el.style.display = 'block';
    });    
}

$('.btn-edit').click(function(){
    
    var divId = this.id.split('-');
    var change = toggleOneRow(divId[2]);
    
    var iconEdit = document.getElementById('icon-edit-' + divId[2]);
    var iconDelete = document.getElementById('icon-delete-' + divId[2]);
    var btnDelete = document.getElementById('btn-delete-' + divId[2]);
    if(this.className.indexOf('btn-warning') != -1) {
        if(!change){
            this.classList.remove('btn-warning');
            this.classList.add('btn-success');
            btnDelete.classList.remove('btn-danger');
            btnDelete.classList.add('btn-warning');
            iconEdit.classList.remove('fa-pencil');
            iconEdit.classList.add('fa-check');
            iconDelete.classList.remove('fa-trash-o');
            iconDelete.classList.add('fa-times');
        }
        this.type = 'button';
    }
    else {
        if(!change){
            this.classList.remove('btn-success');
            this.classList.add('btn-warning');
            btnDelete.classList.remove('btn-warning');
            btnDelete.classList.add('btn-danger');
            iconEdit.classList.remove('fa-check');
            iconEdit.classList.add('fa-pencil');
            iconDelete.classList.remove('fa-times');
            iconDelete.classList.add('fa-trash-o');
            
        }
        this.type = 'submit';
    }    
});

$('.btn-delete').click(function () {
    var divId = this.id.split('-');
    if(this.className.indexOf('btn-warning') != -1) {
        
        var inputRowElements = document.getElementsByClassName('input-row-' + divId[2]);
        for (var i = 0; i < inputRowElements.length; i++) {
            var oCurInput = inputRowElements[i];
            if (oCurInput.type == "text")
                oCurInput.value = oCurInput.defaultValue;
            if(oCurInput.type == "select-one"){
                var options = document.querySelectorAll('#'+oCurInput.id + ' option');
                for (var i = 0, l = options.length; i < l; i++) {
                    options[i].selected = options[i].defaultSelected;
                }
            }
        }
        toggleOneRow(divId[2]);

        var iconEdit = document.getElementById('icon-edit-' + divId[2]);
        var iconDelete = document.getElementById('icon-delete-' + divId[2]);
        var btnEdit = document.getElementById('btn-edit-' + divId[2]);
        if(this.className.indexOf('btn-warning') != -1) {
            this.classList.remove('btn-warning');
            this.classList.add('btn-danger');
            btnEdit.classList.remove('btn-success');
            btnEdit.classList.add('btn-warning');
            iconEdit.classList.remove('fa-check');
            iconEdit.classList.add('fa-pencil');
            iconDelete.classList.remove('fa-times');
            iconDelete.classList.add('fa-trash-o');    
            btnEdit.type = 'button';
        } 
    }
    else
    {        
        $('#str-ask-del').empty();
        $('#str-ask-del').append($('#username-'+divId[2]).val());
        $('#del-user').val($('#username-'+divId[2]).val());
        $('#modal-ConfirmDelete').modal('toggle');
    }
});


function toggleOneRow(id) {
    var divRowElements = document.getElementsByClassName('div-row-'+id);
    var inputRowElements = document.getElementsByClassName('input-row-'+id);

    var checkEmpty = false;
    [].forEach.call(inputRowElements, function( el ) { 
         if(el.value == "")
            checkEmpty = checkEmpty || true;
    });

    if(!checkEmpty) {

        [].forEach.call(divRowElements, function( el ) {
            if (el.style.display != 'none')
                el.style.display = 'none';
            else
                el.style.display = 'block';
        });     

        [].forEach.call(inputRowElements, function( el ) { 
            if (el.style.display != 'none')                 
                el.style.display = 'none';        
            else
                el.style.display = 'block';
        });

        if(!document.getElementById('btn-password-'+id).disabled)
            document.getElementById('btn-password-'+id).disabled = true;
        else
            document.getElementById('btn-password-'+id).disabled = false;
    }
    return checkEmpty;    
}

$('#datepicker-birthday').datepicker({
});
