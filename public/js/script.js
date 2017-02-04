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
        var sPath = window.location.pathname;
        var sPage = sPath.substring(sPath.lastIndexOf('/') + 1);
        $('#str-ask-del').empty();
        if(sPage == "account_management" || sPage == "owner_management")
        {            
            $('#str-ask-del').append($('#username-'+divId[2]).val());
            $('#del-user').val($('#username-'+divId[2]).val());            
        }
        else if(sPage == "stadium_management")
        {
            $('#str-ask-del').append($('#name-'+divId[2]).val());
            $('#del-stadium').val($('#hddStadiumId-'+divId[2]).val());
        }
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

var myLineChart = null
    dataAmountDays = null,
    dataCntDays = null,
    labelsDays = null,
    dataAmountWeek = null,
    dataCntWeek = null,
    labelsWeek = null,
    dataAmountTimes = null,
    dataCntTimes = null,
    labelsTimes = null;

$( document ).ready(function() {

    var sPath = window.location.pathname;
    var sPage = sPath.substring(sPath.lastIndexOf('/') + 1);

    // Material Select Initialization
    $('.mdb-select').material_select();

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

        $('.input-daterange').change(function() {
            getLinechart();
        });

        $('#lineChartData').change(function() {
            // if(this.value == 'amount')
            //     drawLineChart(labelsDays, dataAmountDays);
            // else
            //     drawLineChart(labelsDays, dataCntDays);
            var activeTab = $('.nav-tabs .active').text();
            if(activeTab.indexOf('วันที่') >= 0) {
                if(this.value == 'amount')
                    drawLineChart(labelsDays, dataAmountDays);
                else
                    drawLineChart(labelsDays, dataCntDays);
            }
            else if(activeTab.indexOf('สัปดาห์') >= 0)
                if(this.value == 'amount')
                    drawLineChart(labelsWeek, dataAmountWeek);
                else
                    drawLineChart(labelsWeek, dataCntWeek);
            else if(activeTab.indexOf('ช่วงเวลา') >= 0)
                if(this.value == 'amount')
                    drawLineChart(labelsTimes, dataAmountTimes);
                else
                    drawLineChart(labelsTimes, dataCntTimes);
        });

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            var target = $(e.target).attr("href") // activated tab
            if(target == '#lineChartDate') {
                if($('#lineChartData').val() == 'amount')
                    drawLineChart(labelsDays, dataAmountDays);
                else
                    drawLineChart(labelsDays, dataCntDays);
            }
            else if(target == '#lineChartWeek')
                if($('#lineChartData').val() == 'amount')
                    drawLineChart(labelsWeek, dataAmountWeek);
                else
                    drawLineChart(labelsWeek, dataCntWeek);
            else if(target == '#lineChartTime')
                if($('#lineChartData').val() == 'amount')
                    drawLineChart(labelsTimes, dataAmountTimes);
                else
                    drawLineChart(labelsTimes, dataCntTimes);
        });

        $('.input-daterange input').val(monthNames[monthIndex] + '-' + year);
        //getLinechart();
    }
    else if(sPage == "login") {
        $('.form-login label').addClass('active');
    }    
    if(sPage == "dashboard") {
        $('.input-daterange').datepicker({
            format: "yyyy-mm-dd",
            maxViewMode: 2,
            autoclose: true
        });

        // store the currently selected tab in the hash value
        $("ul.nav-tabs > li > a").on("shown.bs.tab", function(e) {
            var id = $(e.target).attr("href").substr(1);
            window.location.hash = id;
        });

        // on load of the page: switch to the currently selected tab
        var hash = window.location.hash;
        $('#myTab a[href="' + hash + '"]').tab('show');

        $('#discount_type').change(function(){
            changeDiscountTypeIcon();
        });

        $('#discount_type_edit').change(function(){
            changeDiscountTypeIcon();
        });

        changeDiscountTypeIcon();
    }
    else if(sPage != "reservation"){
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
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
    }

    $('.table').DataTable({
        
        language: {
            search: "_INPUT_",
            searchPlaceholder: "ค้นหา..."
        },
        bLengthChange: false,
        responsive: true,
        "bPaginate": true,
        "autoWidth": false,
        "dom": "<'row'<'col-sm-3'f><'col-sm-6'><'col-sm-3'l>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12'i>>"+
                "<'row'<'col-sm-12'p>>",
        colResize: {
           exclude: [2,3,4,5,6,7]
        },
        // scrollY:        "300px",
        // scrollX:        'true',
        // scrollCollapse: 'true',
    });

});

function changeDiscountTypeIcon() {
    if($('#discount_type').val() == 'THB') {
        $('.discount_type > i').removeClass('fa fa-percent prefix');
        $('.discount_type > i').addClass('fa fa-money prefix');
    }
    else {
        $('.discount_type > i').removeClass('fa fa-money prefix');
        $('.discount_type > i').addClass('fa fa-percent prefix');
    }
    if($('#discount_type_edit').val() == 'THB') {
        $('.discount_type > i').removeClass('fa fa-percent prefix');
        $('.discount_type > i').addClass('fa fa-money prefix');
    }
    else {
        $('.discount_type > i').removeClass('fa fa-money prefix');
        $('.discount_type > i').addClass('fa fa-percent prefix');
    }
}


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
            console.log(info[7]);
            
            dataAmountDays = info[1];
            dataCntDays = info[2];
            labelsDays = info[0];
            dataAmountWeek = info[6][0];
            dataCntWeek = info[6][1];
            labelsWeek = info[6][2];
            dataAmountTimes = info[7][0];
            dataCntTimes = info[7][1];
            labelsTimes = info[7][2];

            var activeTab = $('.nav-tabs .active').text();
            if(activeTab.indexOf('วันที่') >= 0) {
                if($('#lineChartData').val() == 'amount')
                    drawLineChart(labelsDays, dataAmountDays);
                else
                    drawLineChart(labelsDays, dataCntDays);
            }
            else if(activeTab.indexOf('สัปดาห์') >= 0)
                if($('#lineChartData').val() == 'amount')
                    drawLineChart(labelsWeek, dataAmountWeek);
                else
                    drawLineChart(labelsWeek, dataCntWeek);
            else if(activeTab.indexOf('ช่วงเวลา') >= 0)
                if($('#lineChartData').val() == 'amount')
                    drawLineChart(labelsTimes, dataAmountTimes);
                else
                    drawLineChart(labelsTimes, dataCntTimes);

            document.getElementById('income').innerText = info[3] + ' บาท';
            document.getElementById('count_reserve').innerText = info[4] + ' ครั้ง';
            if(info[5][0] != '')
            {
                document.getElementById('btn-customer').style.display = "block";
                document.getElementById('best_customer').innerText = 'คุณ ' + info[5][0];
                getCutomerDataAnalysis(info[5][1], info[5][2], info[5][3], info[5][4]);
            }
            else
            {
                document.getElementById('btn-customer').style.display = "none";
            }
            
        },error:function(){ 
            console.log('error');
        }
    });
}

function drawLineChart(labels, data) {
    var data = {
        labels: labels,
        datasets: [
            {
                label: "My Second dataset",
                fillColor: "rgba(151,187,205,0.2)",
                strokeColor: "rgba(151,187,205,1)",
                pointColor: "rgba(151,187,205,1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(151,187,205,1)",
                data: data
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
}

function getCutomerDataAnalysis(info, visited, oftenTime, note){
    try {
        $('.edit label').addClass('active');
        $('#nickname').val(info['nickname']);    
        $('#mobile_number').val(info['mobile_number']);
        $('#hdd_mobile_number').val(info['mobile_number']);
        $('#firstname').val(info['firstname']);
        $('#lastname').val(info['lastname']);
        $('#workplace').val(info['workplace']);
        $('#note').val(note);
        $('#visited').val(visited);        
        $('#time-often').val(oftenTime[info['id']][0]);
        

        var sex = info['sex'];
        if( sex === 'male')
            $('#male').prop('checked', true);
        else
            $('#female').prop('checked', true);


        var queryDate = info['birthday'];
        var dateParts = queryDate.split('-');
        var parsedDate = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]); 
        
        $('#birthday').datepicker('setDate', parsedDate);
    }
    catch(err) {
        //console.log(err.message);
    }
}

$('.btn-edit-customer').click(function (){
    var Id = this.id.split('-');
    $('.edit label').addClass('active');
    $('#nickname-edit').val($('#nickname-' + Id[3]).val());    
    $('#mobile_number-edit').val($('#mobile_number-' + Id[3]).val());
    $('#hdd_mobile_number').val($('#mobile_number-' + Id[3]).val());
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

$('.btn-edit-field_price').click(function (){
    var Id = this.id.split('-');
    $('#modal-edit-field_price .edit label').addClass('active');
    $('#modal-edit-field_price #field_label_edit').removeClass('active');
    $('#modal-edit-field_price #hdd_field_price').val(Id[3]);
    $('#modal-edit-field_price #field_price').val(Id[3]);
    $('#modal-edit-field_price #field_edit').val($('#field-' + Id[3]).val());
    $('#modal-edit-field_price #field_edit').material_select();
    $('#modal-edit-field_price #start_time_edit').val($('#start_time-' + Id[3]).val());
    $('#modal-edit-field_price #end_time_edit').val($('#end_time-' + Id[3]).val());
    $('#modal-edit-field_price #start_date_edit').val($('#start_date-' + Id[3]).val());
    $('#modal-edit-field_price #end_date_edit').val($('#end_date-' + Id[3]).val());
    $('#modal-edit-field_price #field_price_edit').val($('#price-' + Id[3]).val());
    $('#modal-edit-field_price #bgColor_edit').val($('#bgColor-' + Id[3]).val());
});

$('.btn-delete-field_price').click(function (){
    var Id = this.id.split('-');
    $('#del-field_price').val(Id[2]);

    $('#modal-delete-field_price').modal('toggle');
});

$('.btn-edit-promotion').click(function (){
    var Id = this.id.split('-');
    $('#modal-edit-promotion .edit label').addClass('active');    
    $('#modal-edit-promotion #promotion_name_edit').val($('#pro-promotion_name-' + Id[3]).val());
    $('#modal-edit-promotion #hddpromotion').val(Id[3]);
    $('#modal-edit-promotion #discount_type_edit').val($('#pro-discount_type-' + Id[3]).val());
    $('#modal-edit-promotion #discount_type_edit').material_select();
    $('#modal-edit-promotion #start_time_edit').val($('#pro-start_time-' + Id[3]).val());
    $('#modal-edit-promotion #end_time_edit').val($('#pro-end_time-' + Id[3]).val());   
    $('#modal-edit-promotion #start_date_edit').val($('#pro-start_date-' + Id[3]).val());
    $('#modal-edit-promotion #end_date_edit').val($('#pro-end_date-' + Id[3]).val());
    $('#modal-edit-promotion #discount_edit').val($('#pro-discount-' + Id[3]).val());
    // if($('#pro-fixed_range-' + Id[3]).val() == '1')
    //     $('#modal-edit-promotion #fixed_range_edit').prop('checked', true); // Checks it
    // else
    //     $('#modal-edit-promotion #fixed_range_edit').prop('checked', false); // Unchecks it
        
});

$('.btn-delete-promotion').click(function (){
    var Id = this.id.split('-');
    $('#del-promotion').val(Id[2]);

    $('#modal-delete-promotion').modal('toggle');
});

function checkCustomer() {
    var mobile_number = $("#mobile_number").val();
    $.ajax({
        url: 'getCustomer',
        type: 'POST',
        beforeSend: function (xhr) {
            var token = $('meta[name="csrf_token"]').attr('content');

            if (token) {
                return xhr.setRequestHeader('X-CSRF-TOKEN', token);
            }
        },
        data: { _mobile_number: mobile_number },        
        success: function(info) {
            $('#nickname').val(info);
        },
        error: function() {
            console.log('error');
        }
    });

}

$('.btn-edit-paid').click(function (){

    var Id = this.id.split('-');
    $('#modal-paid-reserve .reserve label').addClass('active');    

    var reserve_id = '', 
        field_price = 0, 
        water_price = 0, 
        supplement_price = 0,
        discount_price = 0;
    $('#table_reserve_today tr').filter(':has(:checkbox:checked)').each(function() {
        // this = tr
        
        var tr = this.id;
        reserve_id = reserve_id + '-' + tr;
        field_price += parseInt($('#field_price-' + tr).val());
        water_price += parseInt($('#supplement_price-' + tr).val());
        supplement_price += parseInt($('#water_price-' + tr ).val());
        discount_price += parseInt($('#discount_price-' + tr ).val());

    });
    $('#checkbox-' + Id[3]).trigger('click');
    $('#modal-paid-reserve #hddReserveId').val(reserve_id);
    $('#modal-paid-reserve #field_price').val(field_price);
    $('#modal-paid-reserve #supplement_price').val(water_price);
    $('#modal-paid-reserve #discount_price').val(discount_price);

    sumPrice();

        
});

$('#table_reserve_today tr').click(function(event) {
    if (event.target.type !== 'checkbox') {
        $(':checkbox', this).trigger('click');
        // if($('#checkbox-' + this.id).is(":checked"))
        //     $('#btn-edit-paid-' + this.id).prop('disabled', false);
        // else
        //     $('#btn-edit-paid-' + this.id).prop('disabled', true);
    }
});

function sumPrice() {
    var field_price = $('#field_price').val(),
    water_price = $('#water_price').val(),
    supplement_price = $('#supplement_price').val();
    var total = int_try_parse(field_price, 0) + int_try_parse(water_price, 0) + int_try_parse(supplement_price, 0);

    $('#total_price').val(total);
}

var int_try_parse = function TryParseInt(str,defaultValue) {
    var retValue = defaultValue;
    if(str !== null) {
        if(str.length > 0) {
            if (!isNaN(str)) {
                retValue = parseInt(str);
            }
        }
    }
    return retValue;
}

function checkMax() {
    var startTime = $("#modal-edit-reserve #startTime").val();
    var endTime = $("#modal-edit-reserve #endTime").val();

    if (startTime >= endTime) {
      $("#modal-edit-reserve #startTime").val(endTime);
    }
  }