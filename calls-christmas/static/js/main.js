function myInterval() {
    jQuery('.pause').css('visibility', 'visible');
    jQuery('div.order > table > tbody').html('').closest('.info').hide();
    jQuery('#company')[0].reset();

    window.callAttemptInterval = setInterval(callAttempt, 5000);
}

function openModal(type, text) {

    var modal_id = 'resultModalNO';

    if (type == 1) {
        modal_id = 'resultModal';
    }

    jQuery('#' + modal_id + ' .modal-body').html(text);
    jQuery('#' + modal_id + '').modal('show');

    setCount();
}

function setHistory(histories) {
    if (histories.length > 0) {
        var history_html = `
        <table class="table">
            <thead>
                <tr>
                    <th>
                        Comment
                    </th>
                    <th>
                        Datetime
                    </th>
                </tr>
            </thead>
            <tbody>
        `;

        jQuery.each(histories, function (i, value) {
            history_html += `
                <tr>
                    <td>
                        ` + value.comment + `
                    </td>
                    <td>
                        ` + value.datetime_add + `
                    </td>
                </tr>
            `;
        });

        history_html += `
            </tbody>
        </table>
        `;

        jQuery('.history').html(history_html);
    } else {
        jQuery('.history').html('');
    }
}
function getNextPrevCorpUser(action = 'next') {
    clearInterval(window.callAttemptInterval);

    var id = jQuery('#id').val();
    console.log(action, id)

    jQuery.ajax({
        type: 'POST',
        url: './index.php',
        data: {
            task: 'getNextPrevCorpUser',
            id: id,
            action: action
        },
        dataType: 'json',
        beforeSend: function (jqXHR) {
            jQuery('.loader').show();
            jQuery('div.order > table > tbody').html('').closest('.info').hide();
            jQuery('.pause').css('visibility', 'hidden');
        },
        success: function (json) {
            if (json.result == true) {
                var html = '<tr><td>Order ID</td><td><a target="_blank" href="../administrator/index2.php?page=order.order_list&show=&option=com_virtuemart&order_id_filter=' + json.obj.order_id + '">' + json.obj.order_id + '</a></td></tr>';
                html += '<tr><td>Last purchase</td><td>' + json.obj.cdate + '</td></tr>';
                html += '<tr><td>Occasion</td><td>' + json.obj.customer_occasion + '</td></tr>';
                html += '<tr><td>Card Message</td><td>' + json.obj.customer_note + '</td></tr>';
                html += '<tr><td>Current status of order</td><td>' + json.obj.order_status_name + '</td></tr>';
                // html += '<tr><td>Dollar value of last 3 purchases</td><td>$' + json.obj.last_3_total + '</td></tr>';
                html += '<tr><td>Recipient name</td><td>' + json.obj.recipient_name + '</td></tr>';
                html += '<tr><td>First name</td><td>' + json.obj.first_name + '</td></tr>';
                html += '<tr><td>Last name</td><td>' + json.obj.last_name + '</td></tr>';
                html += '<tr><td>Company</td><td>' + json.obj.company + '</td></tr>';
                html += '<tr><td>City</td><td>' + json.obj.city + '</td></tr>';
                html += '<tr><td>Email</td><td>' + json.obj.user_email + '</td></tr>';
                html += '<tr><td>Country</td><td>' + json.obj.country + '</td></tr>';
                html += '<tr><td>Phone</td><td id="phone"><input type="text" class="form-control" id="company_phone" name="company_phone" value="'+json.obj.phone+'"></td></tr>';
                jQuery('#id').val(json.obj.id);
                jQuery('div.order > table > tbody').html(html).closest('.info').css('display', 'flex');

                const orderStatusElement = document.getElementById("order_status");
                if (json.obj.status > 0) {
                    orderStatusElement.innerHTML = '<img style="width: 34px;margin-right: 5px;" src="static/images/success.png">';
                } else {
                    orderStatusElement.innerHTML = '<img style="width: 34px;margin-right: 5px;" src="static/images/failure.png">';
                }
                orderStatusElement.style.display = 'inline';
            } else {
                myInterval();
            }
            jQuery('.loader').hide();
            setCount();
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR.responseText + textStatus + errorThrown);
            jQuery('.loader').hide();
            myInterval();
        },
        timeout: 30000
    });
}
function callAttempt() {
    clearInterval(window.callAttemptInterval);

    jQuery.ajax({
        type: 'POST',
        url: './index.php',
        data: {
            task: 'callAttempt'
        },
        dataType: 'json',
        beforeSend: function (jqXHR) {
            jQuery('.loader').show();
            jQuery('div.order > table > tbody').html('').closest('.info').hide();
            jQuery('.pause').css('visibility', 'hidden');
        },
        success: function (json) {
            if (json.result == true) {
                var html = '<tr><td>Order ID</td><td><a target="_blank" href="../administrator/index2.php?page=order.order_list&show=&option=com_virtuemart&order_id_filter=' + json.obj.order_id + '">' + json.obj.order_id + '</a></td></tr>';
                html += '<tr><td>Last purchase</td><td>' + json.obj.cdate + '</td></tr>';
                html += '<tr><td>Occasion</td><td>' + json.obj.customer_occasion + '</td></tr>';
                html += '<tr><td>Card Message</td><td>' + json.obj.customer_note + '</td></tr>';
                html += '<tr><td>Current status of order</td><td>' + json.obj.order_status_name + '</td></tr>';
                // html += '<tr><td>Dollar value of last 3 purchases</td><td>$' + json.obj.last_3_total + '</td></tr>';
                html += '<tr><td>Recipient name</td><td>' + json.obj.recipient_name + '</td></tr>';
                html += '<tr><td>First name</td><td>' + json.obj.first_name + '</td></tr>';
                html += '<tr><td>Last name</td><td>' + json.obj.last_name + '</td></tr>';
                html += '<tr><td>Company</td><td>' + json.obj.company + '</td></tr>';
                html += '<tr><td>City</td><td>' + json.obj.city + '</td></tr>';
                html += '<tr><td>Email</td><td>' + json.obj.user_email + '</td></tr>';
                html += '<tr><td>Country</td><td>' + json.obj.country + '</td></tr>';
                html += '<tr><td>Phone</td><td id="phone"><input type="text" class="form-control" id="company_phone" name="company_phone" value="'+json.obj.phone+'"></td></tr>';
                setHistory(json.histories);
                jQuery('#id').val(json.obj.id);
                jQuery('div.order > table > tbody').html(html).closest('.info').css('display', 'flex');
            } else {
                myInterval();
            }
            jQuery('.loader').hide();
            setCount();
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR.responseText + textStatus + errorThrown);
            jQuery('.loader').hide();
            myInterval();
        },
        timeout: 30000
    });
}

function setPause() {
    jQuery('.loader').hide();
    clearInterval(window.callAttemptInterval);
    openModal(2, 'Have a nice pause =)');

    jQuery('a.pause').hide();
    jQuery('a.play').show();
}

function setPlay() {
    jQuery('.loader').show();

    jQuery('a.play').hide();
    jQuery('a.pause').show();
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
                jQuery('#ext_count').text(json.ext);
                jQuery('#all_ext_count').text(json.all_ext);
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR.responseText);
        },
        timeout: 10000
    });
}

jQuery(document).ready(function () {
    jQuery('#btn_start_call').click(function (e) {
        e.preventDefault();

        jQuery.ajax({
            type: 'POST',
            url: './index.php',
            data: {
                task: 'startCall',
                phone: jQuery('#company_phone').val(),
                id: jQuery('#id').val()
            },
            dataType: 'json',
            success: function (json) {
                if (json.result == true) {
                    alert('The call was sent successfully, please wait.');
                } else {
                    alert('Please try again later!');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Server error. Please try again later!');
            }
        });
    });

    jQuery('#requeue_datetime').datetimepicker({
        uiLibrary: 'bootstrap4',
        footer: true,
        modal: true,
        format: 'yyyy-mm-dd HH:MM',
        change: function (e) {
            jQuery.ajax({
                type: 'POST',
                url: './index.php',
                data: {
                    task: 'reQueue',
                    datetime: jQuery('#requeue_datetime').val(),
                    comment: jQuery('#comment').val(),
                    id: jQuery('#id').val()
                },
                dataType: 'json',
                beforeSend: function (jqXHR) {
                    jQuery('#btn_hot')
                        .prop('disabled', true)
                        .find('.spinner-border').removeClass('d-none');
                },
                success: function (json) {
                    if (json.result == true) {
                        openModal(1, 'Success.');
                    } else {
                        openModal(1, json.error);
                    }

                    jQuery('#btn_hot')
                        .prop('disabled', false)
                        .find('.spinner-border').addClass('d-none');
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    openModal(1, jqXHR.responseText);

                    jQuery('#btn_hot')
                        .prop('disabled', false)
                        .find('.spinner-border').addClass('d-none');
                },
                timeout: 10000
            });
        }
    });

    if (needAttempt == true) {
        myInterval();
        setCount();
    }

    jQuery('#btn_hot').click(function (e) {
        e.preventDefault();

        jQuery('span.input-group-append').trigger('click');
    });

    jQuery('#btn_resend').click(function (e) {
        e.preventDefault();

        jQuery.ajax({
            type: 'POST',
            url: './index.php',
            data: {
                task: 'reSendEmail',
                id: jQuery('#id').val()
            },
            dataType: 'json',
            context: this,
            beforeSend: function (jqXHR) {
                jQuery(this)
                    .prop('disabled', true)
                    .find('.spinner-border').removeClass('d-none');
            },
            success: function (json) {
                if (json.result == true) {
                    jQuery('#btn_history').trigger('click');
                    openModal(2, 'Success.');
                } else {
                    openModal(2, json.error);
                }

                jQuery(this)
                    .prop('disabled', false)
                    .find('.spinner-border').addClass('d-none');
            },
            error: function (jqXHR, textStatus, errorThrown) {
                openModal(2, jqXHR.responseText);

                jQuery(this)
                    .prop('disabled', false)
                    .find('.spinner-border').addClass('d-none');
            },
            timeout: 60000
        });
    });

    jQuery('#btn_not').click(function (e) {
        e.preventDefault();

        jQuery.ajax({
            type: 'POST',
            url: './index.php',
            data: {
                task: 'setNot',
                comment: jQuery('#comment').val(),
                id: jQuery('#id').val()
            },
            dataType: 'json',
            context: this,
            beforeSend: function (jqXHR) {
                jQuery(this)
                    .prop('disabled', true)
                    .find('.spinner-border').removeClass('d-none');
            },
            success: function (json) {
                if (json.result == true) {
                    openModal(1, 'Success.');
                } else {
                    openModal(1, json.error);
                }

                jQuery(this)
                    .prop('disabled', false)
                    .find('.spinner-border').addClass('d-none');
            },
            error: function (jqXHR, textStatus, errorThrown) {
                openModal(1, jqXHR.responseText);

                jQuery(this)
                    .prop('disabled', false)
                    .find('.spinner-border').addClass('d-none');
            },
            timeout: 20000
        });
    });

    jQuery('#btn_buyer').click(function (e) {
        e.preventDefault();

        jQuery.ajax({
            type: 'POST',
            url: './index.php',
            data: {
                task: 'setBuyer',
                comment: jQuery('#comment').val(),
                id: jQuery('#id').val()
            },
            dataType: 'json',
            context: this,
            beforeSend: function (jqXHR) {
                jQuery(this)
                    .prop('disabled', true)
                    .find('.spinner-border').removeClass('d-none');
            },
            success: function (json) {
                if (json.result == true) {
                    openModal(1, 'Success.');
                } else {
                    openModal(1, json.error);
                }

                jQuery(this)
                    .prop('disabled', false)
                    .find('.spinner-border').addClass('d-none');
            },
            error: function (jqXHR, textStatus, errorThrown) {
                openModal(1, jqXHR.responseText);

                jQuery(this)
                    .prop('disabled', false)
                    .find('.spinner-border').addClass('d-none');
            },
            timeout: 20000
        });
    });

    jQuery('#btn_history').click(function (e) {
        e.preventDefault();

        jQuery.ajax({
            type: 'POST',
            url: './index.php',
            data: {
                task: 'getHistories',
                id: jQuery('#id').val()
            },
            dataType: 'json',
            context: this,
            beforeSend: function (jqXHR) {
                jQuery(this)
                    .prop('disabled', true)
                    .find('.spinner-border').removeClass('d-none');
            },
            success: function (json) {
                if (json.result == true) {
                    setHistory(json.histories);
                }

                jQuery(this)
                    .prop('disabled', false)
                    .find('.spinner-border').addClass('d-none');
            },
            error: function (jqXHR, textStatus, errorThrown) {
                openModal(2, jqXHR.responseText);

                jQuery(this)
                    .prop('disabled', false)
                    .find('.spinner-border').addClass('d-none');
            },
            timeout: 20000
        });
    });

    jQuery('#btn_voicemail').click(function (e) {
        e.preventDefault();

        jQuery.ajax({
            type: 'POST',
            url: './index.php',
            data: {
                task: 'setVoicemail',
                comment: jQuery('#comment').val(),
                id: jQuery('#id').val()
            },
            dataType: 'json',
            context: this,
            beforeSend: function (jqXHR) {
                jQuery(this)
                    .prop('disabled', true)
                    .find('.spinner-border').removeClass('d-none');
            },
            success: function (json) {
                if (json.result == true) {
                    openModal(1, 'Success. Please turn input <b>' + voicemail_number + '</b> in your x-lite now.');
                } else {
                    openModal(1, json.error);
                }

                jQuery(this)
                    .prop('disabled', false)
                    .find('.spinner-border').addClass('d-none');
            },
            error: function (jqXHR, textStatus, errorThrown) {
                openModal(1, jqXHR.responseText);

                jQuery(this)
                    .prop('disabled', false)
                    .find('.spinner-border').addClass('d-none');
            },
            timeout: 20000
        });
    });

    jQuery('#resultModal').on('hide.bs.modal', function (e) {
        jQuery('.loader').show();
        myInterval();

        jQuery(this).removeClass('need');
    });

});
function playMusicFirst() {
    const audio = document.getElementById('background-music');
    audio.volume = 0.08;

    if (audio.paused) {
        audio.play().catch((error) => {
            console.log('Audio playback failed:', error);
        });
    } else {
        audio.pause();
    }
}
function playMusicSecond() {
    const audio = document.getElementById('background-music-let-it-snow');
    audio.volume = 0.08;

    if (audio.paused) {
        audio.play().catch((error) => {
            console.log('Audio playback failed:', error);
        });
    } else {
        audio.pause();
    }
}
