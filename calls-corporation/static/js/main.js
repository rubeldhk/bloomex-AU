function myInterval() {
    jQuery('div.order > table > tbody').html('').closest('.info').hide();
    jQuery('#company')[0].reset();
    window.callAttemptInterval = setInterval(callAttempt, 5000);
}

function callAttempt() {
    jQuery('div.row.any.loader').show();
    jQuery('div.order > table > tbody').html('').closest('.info').hide();

    clearInterval(window.callAttemptInterval);

    jQuery.ajax({
        type: 'POST',
        url: './index.php',
        data: {
            task: 'callAttempt'
        },
        dataType: 'json',
        success: function (json) {
            if (json.result == true) {
                var html = '<tr><td>Order ID</td><td><a target="_blank" href="/administrator/index2.php?page=order.order_list&show=&option=com_virtuemart&order_id_filter=' + json.obj.order_id + '">' + json.obj.order_id + '</a></td></tr>';
                html += '<tr><td>Last purchase</td><td>' + json.obj.cdate + '</td></tr>';
                html += '<tr><td>Occasion</td><td>' + json.obj.customer_occasion + '</td></tr>';
                html += '<tr><td>Card Message</td><td>' + json.obj.customer_note + '</td></tr>';
                html += '<tr><td>Current status of order</td><td>' + json.obj.order_status_name + '</td></tr>';
                html += '<tr><td>Dollar value of last 3 purchases</td><td>$' + json.obj.last_3_total + '</td></tr>';
                html += '<tr><td>First name</td><td>' + json.obj.first_name + '</td></tr>';
                html += '<tr><td>Last name</td><td>' + json.obj.last_name + '</td></tr>';
                html += '<tr><td>Company</td><td>' + json.obj.company_name + '</td></tr>';
                html += '<tr><td>City</td><td>' + json.obj.city + '</td></tr>';
                html += '<tr><td>Email</td><td>' + json.obj.email + '</td></tr>';
                html += '<tr><td>Phone</td><td>' + json.obj.phone + '</td></tr>';

                jQuery('#company_domain').val(json.obj.email.split('@')[1]);
                jQuery('#company_name').val(json.obj.company_name);
                jQuery('#id').val(json.obj.id);
                jQuery('div.order > table > tbody').html(html).closest('.info').show();
            } else {
                if (json.error) {
                    alert(json.error);
                }
                myInterval();
            }
            jQuery('div.row.any.loader').hide();
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR.responseText);
            jQuery('div.row.any.loader').hide();
            myInterval();
        },
        timeout: 30000
    });
}

function setPause() {
    jQuery('div.row.any.loader').hide();
    clearInterval(window.callAttemptInterval);

    jQuery('a.pause').hide();
    jQuery('a.play').css('display', 'inline-block');
}

function setPlay() {
    jQuery('div.row.any.loader').show();

    jQuery('a.play').hide();
    jQuery('a.pause').css('display', 'inline-block');
    myInterval();
}

function setCount() {
    jQuery.ajax({
        type: 'POST',
        url: './index.php',
        data: {
            task: 'setCount'
        },
        dataType: 'json',
        context: this,
        beforeSend: function (jqXHR) {
        },
        success: function (json) {
            if (json.result == true) {
                jQuery('#all_count').text(json.all);
                jQuery('div.count').css('display', 'flex');
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR.responseText);
        },
        timeout: 60000
    });
}

jQuery(document).ready(function () {

    if (needAttempt == true) {
        myInterval();
        window.setCountInterval = setInterval(setCount, 10000);
    }

    jQuery('#btn_update').click(function (e) {
        e.preventDefault();

        if (jQuery('#company_name').val() == '' || jQuery('#company_domain').val() == '') {
            alert('Fill company information.');

            return true;
        }

        jQuery('div.row.any.loader').show();
        jQuery('div.row.any.info').hide();

        jQuery.ajax({
            type: 'POST',
            url: './index.php',
            data: {
                task: 'saveCompany',
                company_name: jQuery('#company_name').val(),
                company_domain: jQuery('#company_domain').val(),
                id: jQuery('#id').val()
            },
            dataType: 'json',
            success: function (json) {
                if (json.result == true) {
                    alert('Success.');
                    myInterval();
                } else {
                    alert(json.error);
                    myInterval();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert(jqXHR.responseText);
                myInterval();
            },
            timeout: 60000
        });
    });

    jQuery('#btn_no').click(function (e) {
        e.preventDefault();

        if (jQuery('#company_name').val() == '' || jQuery('#company_domain').val() == '') {
            alert('Fill company information.');

            return true;
        }

        jQuery('div.row.any.loader').show();
        jQuery('div.row.any.info').hide();

        jQuery.ajax({
            type: 'POST',
            url: './index.php',
            data: {
                task: 'stakeHolder',
                company_name: jQuery('#company_name').val(),
                company_domain: jQuery('#company_domain').val(),
                id: jQuery('#id').val()
            },
            dataType: 'json',
            success: function (json) {
                if (json.result == true) {
                    alert('Success.');
                    myInterval();
                } else {
                    alert(json.error);
                    myInterval();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert(jqXHR.responseText);
                myInterval();
            },
            timeout: 60000
        });
    });

    jQuery('#btn_no_corp').click(function (e) {
        e.preventDefault();

        if (jQuery('#company_domain').val() == '') {
            alert('Fill company domain.');

            return true;
        }

        jQuery('div.row.any.loader').show();
        jQuery('div.row.any.info').hide();

        jQuery.ajax({
            type: 'POST',
            url: './index.php',
            data: {
                task: 'noCorp',
                company_domain: jQuery('#company_domain').val(),
                id: jQuery('#id').val()
            },
            dataType: 'json',
            success: function (json) {
                if (json.result == true) {
                    alert('Success.');
                    myInterval();
                } else {
                    alert(json.error);
                    myInterval();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert(jqXHR.responseText);
                myInterval();
            },
            timeout: 60000
        });
    });

    jQuery('#btn_email').click(function (e) {
        e.preventDefault();

        setPause();

        jQuery('div.row.any.loader').show();

        jQuery.ajax({
            type: 'POST',
            url: './index.php',
            data: {
                task: 'sendEmail'
            },
            dataType: 'json',
            success: function (json) {
                if (json.result == true) {
                    alert('Success.');
                    setPlay();
                } else {
                    console.log(json.error);
                    setPlay();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert(jqXHR.responseText);
                setPlay();
            },
            timeout: 60000
        });
    });

    jQuery('#btn_requeue').click(function (e) {
        e.preventDefault();

        jQuery('div.row.any.loader').show();
        jQuery('div.row.any.info').hide();

        jQuery.ajax({
            type: 'POST',
            url: './index.php',
            data: {
                task: 'reQueue',
                id: jQuery('#id').val()
            },
            dataType: 'json',
            success: function (json) {
                if (json.result == true) {
                    alert('Success.');
                    myInterval();
                } else {
                    alert(json.error);
                    myInterval();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert(jqXHR.responseText);
                myInterval();
            },
            timeout: 60000
        });
    });

});



