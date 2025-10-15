
var check_first = 'true';
var date_start = '';

function check_new_calls()
{
    extension = getCookie('extension');
    id_info = $('#id_info').val();
    type = $('#type').val();
    number_id = $('#number_id').val();

    $.ajax({
        type: "POST",
        url: 'check_new_calls.php',
        data: {
            ext_prj: extension,
            id_info: id_info,
            type: type,
            number_id: number_id
        },
        success: function (data)
        {
            if (data == 0)
            {
                if (window.check_new_interval != '') {
                    clearTimeout(window.check_new_interval);
                }

                next_call = confirm('Line is free, start the next call NOW?');

                if (next_call) {
                    $('.pop_up_button').click();
                }
                else {
                    var count_seconds = 61;

                    function countdown_interval() {
                        count_seconds--;

                        if (count_seconds < 0) {
                            $('#countdown_seconds').parent().hide();
                            $('.time_button').click();
                        }
                        else {
                            if ($('#countdown_seconds').parent().is(':visible')) {
                                $('#countdown_seconds').text(count_seconds).parent().show();
                                setTimeout(countdown_interval,1000);
                            }
                            else {
                                $('#countdown_seconds').text('');
                                return false;
                            }
                        }
                    }

                    $('#countdown_seconds').text('').parent().show();
                    setTimeout(countdown_interval, 1000);
                }
            }
            else {
                window.check_new_interval = setTimeout(check_new_calls, 20000);
            }
        }
    });

}

function downloadCallsMade(type) {

    $('#downloadCallsMade').val('Sending...');
    $.ajax({
        type: 'post',
        url: 'calls_made.php',
        dataType: 'json',
        data: {
            ext: extension,
            type: type
        },
        success: function (data) {
            if (data.status == 'success') {
                $('.downloadCallsMade').val('Download');
                var blob = new Blob([data.csvData], { type: 'text/csv' });
                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = 'data.csv';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                $('#downloadCallsMade').val('TODAY\'S REPORT');
            } else {
                alert(data.message);
            }
        }
    });
}



function track_login()
{
    extension = getCookie('extension');
    $.ajax({
        type: "POST",
        url: 'track_login.php',
        data: {
            ext_prj: extension,
            check_first: check_first,
            date_start: date_start
        },
        success: function (data)
        {
            date_start = data;
        }
    });
    setTimeout(track_login, 10000);
    check_first = 'false';
}
function reSetInterval(id_info, type, no_more, answer_type)
{
    $('#countdown_seconds').parent().hide();

    if (id_info > 0)
    {
        $('#countdown_seconds').parent().hide();

        if (window.check_new_interval != '') {
            clearTimeout(window.check_new_interval);
        }
        if(answer_type=='VOICEMAIL'){
            alert('Type  ##122 to send the voicemail')
        }

        var note = $('#note').val();
        var number = $('#number').val();
        var order = $('#order').val();
        var number_id = $('#number_id').val();
        extension = getCookie('extension');
        $('.end_button_div').html('<img src="./images/loader.gif" alt="loading..." />');
        $.ajax({
            type: "POST",
            url: 'save_call.php',
            data: {id_info: id_info, type: type, no_more: no_more, note: note, answer_type: answer_type, ext: extension, number: number, order: order, number_id: number_id},
            success: function (data)
            {
                getInfo();
            }
        });
    } else {

        $('.end_button_div').html('<img src="./images/loader.gif" alt="loading..." />');
        getInfo();
    }


    window.intervalID = setInterval(getInfo, 5000);
}
var i = 0;
var infoRequestInProgress = false;
function getInfo()
{
    extension = getCookie('extension');
    call_type = getCookie('call_type');
    access_abandonment = parseInt(getCookie('access_abandonment'));
    access_occassion = parseInt(getCookie('access_occassion'));
    call_back = parseInt(getCookie('call_back'));
    if (!infoRequestInProgress) {

        infoRequestInProgress = true;

        if (access_abandonment && !access_occassion && !call_back && call_type == 'abandonment') {

            i = 0
        } else if (!access_abandonment && access_occassion && !call_back && call_type == 'ocassion') {

            i = 1
        } else if (!access_abandonment && !access_occassion && call_back) {

            i = 2
        } else if (access_abandonment && access_occassion && !call_back) {
            if (i == 2 && call_type == 'abandonment') {
                i = 0
            }
        } else if (!access_abandonment && access_occassion && call_back) {
            if (i == 0 && call_type == 'ocassion') {
                i = 1
            }
        } else if (access_abandonment && !access_occassion && call_back) {
            if (i == 1) {
                i = 2
            }
        } else if (access_abandonment && access_occassion && call_back && call_type == 'abandonment') {
            i = 0;
        } else if (access_abandonment && access_occassion && call_back && call_type == 'ocassion') {
            i = 1;
        } else if (!access_abandonment && !access_occassion && !call_back) {
            i = 'not access';

        }

        if (i == 0) {

            $('.no_loader_div').show();
            $.ajax({
                type: 'post',
                url: 'get_info_abandonment.php',
                data: {ext_prj: extension},
                success: function (info_json) {

                    get_our_info(info_json);
                    infoRequestInProgress = false;
                },
                error: function () {
                    infoRequestInProgress = false;
                }
            });

            i++;
        } else if (i == 1) {

            $('.no_loader_div').show();
            $.ajax({
                type: 'post',
                url: 'get_info_ocassion.php',
                data: {ext_prj: extension},
                success: function (info_json) {

                    get_our_info(info_json);
                    infoRequestInProgress = false;
                },
                error: function () {
                    infoRequestInProgress = false;
                }
            });
            i++;
        } else if (i == 2) {

            $('.no_loader_div').show();
            $.ajax({
                type: 'post',
                url: 'get_call_back.php',
                data: {ext_prj: extension},
                success: function (info_json) {

                    get_our_info(info_json);
                    infoRequestInProgress = false;
                },
                error: function () {
                    infoRequestInProgress = false;
                }

            });


            i = 0;
        }
    }
}

function get_our_info(info_json) {
    $('.no_loader_div').hide();

    var info_a = $.parseJSON(info_json);

    //console.log(info_a);

    if (parseInt(info_a.id_info) > 0)
    {
        //console.log('stop');

        clearInterval(window.intervalID);

        $.ajax({
            type: "POST",
            url: 'get_our_info.php',
            data: {type: info_a.type, id_info: info_a.id_info, number: info_a.number, number_id: info_a.number_id},
            success: function (html_json)
            {
                var html_a = $.parseJSON(html_json);

                $('#left_div').html(html_a.left);
                $('#right_div').html(html_a.right);
                $('.left_div').show();
                $('.right_div').show();
                if (info_a.count > 0) {
                    $('.no_calling_div').html(info_a.type.toUpperCase() + ' CALLING PROCESS (' + info_a.count + ' calls left.)');
                } else {
                    $('.no_calling_div').html(info_a.type.toUpperCase() + ' CALLING PROCESS');
                }

                check_new_calls();
            }
        });
    } else
    {
        $('.left_div').hide();
        $('.right_div').hide();
        $('.no_calling_div').html('NO CALLING');
    }

}

function getCookie(name)
{
    var matches = document.cookie.match(new RegExp(
        "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
    ));
    return matches ? decodeURIComponent(matches[1]) : undefined;
}
function send_email(){
    $('.send_email_btn').val('Sending...')
    $.ajax({
        type: 'post',
        url: 'send_email.php',
        dataType: 'json',
        data: {email: $('#user_email').val(),cart:$('#cart_products').val(),first_name:$('#first_name').val()},
        success: function (data)
        {
            if (data.result)
            {
                $('.send_email_btn').val('Email Sent')
            } else
            {
                $('.send_email_btn').val('Email Not Sent')

            }
        }
    });
}

$(document).ready(function () {
    session_hash = Math.round(new Date().getTime() / 1000)
    if (performance.navigation.type == 1) {
        reSetInterval(0);
        track_login();
    } else {
        $.ajax({
            type: 'post',
            url: 'get_one_tab.php',
            dataType: 'json',
            data: {session_hash: session_hash},
            success: function (data)
            {
                if (data.result)
                {
                    reSetInterval(0);
                    track_login();
                } else
                {
                    $('body').html('');
                    alert('Only one tab should be open.');

                }
            }
        });

    }

});
