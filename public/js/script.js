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

        if($('#btn-password-' + id).is(':disabled'))
            $('#btn-password-' + id).prop('disabled', false);
        else
            $('#btn-password-' + id).prop('disabled', true);
    }
    return checkEmpty;    
}

$('.datepicker').datepicker({
    format: 'yyyy/mm/dd',
    startView: 3,
    autoclose: true,
    maxViewMode: 2,
    maxDate: '0'
});

$('.input-daterange').datepicker({
    format: "MM-yyyy",
    minViewMode: 1,
    maxViewMode: 2,
    autoclose: true,
    defaultViewDate: new Date(2017, 1, 20)
});

$( document ).ready(function() {

    var sPath = window.location.pathname;
    var sPage = sPath.substring(sPath.lastIndexOf('/') + 1);
    if(sPage == "analysis") {    
        var monthNames = [
            "January", "February", "March",
            "April", "May", "June", "July",
            "August", "September", "October",
            "November", "December"
        ];

        var date = new Date();
        var monthIndex = date.getMonth();
        var year = date.getFullYear();

        $('.input-daterange input').val(monthNames[monthIndex] + '-' + year);
        getLinechart();
    }
    else if(sPage == "login") {
        $('.form-login label').addClass('active');
    }
});


$('.input-daterange').change(function() {
    getLinechart();
});

var myLineChart = null;

function getLinechart(){
    var start = $("input[name=mount]").val();
    $.ajax({
        url: 'analysis-getStat',
        type: 'POST',
        beforeSend: function (xhr) {
            var token = $('meta[name="csrf_token"]').attr('content');

            if (token) {
                return xhr.setRequestHeader('X-CSRF-TOKEN', token);
            }
        },
        data: { _date: start },        
        success: function(info){
            tmpLabels = info[0];
            tmpData = info[1];

            var data = {
                labels: info[0],
                datasets: [
                    {
                        label: "My Second dataset",
                        fillColor: "rgba(151,187,205,0.2)",
                        strokeColor: "rgba(151,187,205,1)",
                        pointColor: "rgba(151,187,205,1)",
                        pointStrokeColor: "#fff",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(151,187,205,1)",
                        data: info[1]
                    }
                ]
            };
            
            var option = {
                responsive: true,
            };
            
            if(myLineChart!=null){
                myLineChart.destroy();
            }
            // Get the context of the canvas element we want to select
            var ctx = document.getElementById("lineChartEx").getContext('2d');
            myLineChart = new Chart(ctx).Line(data, option); //'Line' defines type of the chart.
            
            document.getElementById('income').innerText = info[2] + ' บาท';
            document.getElementById('count_reserve').innerText = info[3] + ' ครั้ง';
            document.getElementById('best_customer').innerText = '    คุณ ' + info[4];
        },error:function(){ 
            console.log('error');
        }
    });
}

$('.btn-edit-customer').click(function (){
    var Id = this.id.split('-');
    $('.edit label').addClass('active');
    $('#nickname-edit').val($('#nickname-' + Id[3]).val());    
    $('#mobile_number-edit').val($('#mobile_number-' + Id[3]).val());
    $('#firstname-edit').val($('#firstname-' + Id[3]).val());
    $('#lastname-edit').val($('#lastname-' + Id[3]).val());
    $('#workplace-edit').val($('#workplace-' + Id[3]).val());
    $('#note-edit').val($('#note-' + Id[3]).val());
    $('#visited-edit').val($('#visited-count-' + Id[3]).val());
    $('#time-often-edit').val($('#visited-time-' + Id[3]).val());

    var sex = $('#sex-' + Id[3]).val();
    if( sex === 'male')
        $('#male-edit').prop('checked', true);
    else
        $('#female-edit').prop('checked', true);


    var queryDate = $('#birthday-' + Id[3]).val();
    var dateParts = queryDate.split('-');
    var parsedDate = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]); 
    
    $('#birthday-edit').datepicker('setDate', parsedDate);
});

$('.btn-delete-customer').click(function (){
    var Id = this.id.split('-');
    $('#del-customer').val($('#mobile_number-' + Id[2]).val());

    $('#str-ask-del').empty();
    $('#str-ask-del').append($('#nickname-' + Id[2]).val());
    $('#modal-delete-customer').modal('toggle');
});
