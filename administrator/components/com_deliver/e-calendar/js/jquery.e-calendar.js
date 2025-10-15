/**
 * @license e-Calendar v0.9.3
 * (c) 2014-2016 - Jhonis de Souza
 * License: GNU
 */

(function ($) {

    var eCalendar = function (options, object) {

        // Initializing global variables
        var adDay = new Date().getDate();
        var adMonth = new Date().getMonth();
        var adYear = new Date().getFullYear();
        var dDay = adDay;
        var dMonth = adMonth;
        var dYear = adYear;
        var instance = object;

        var settings = $.extend({}, $.fn.eCalendar.defaults, options);

        function lpad(value, length, pad) {
            if (typeof pad == 'undefined') {
                pad = '0';
            }
            var p;
            for (var i = 0; i < length; i++) {
                p += pad;
            }
            return (p + value).slice(-length);
        }

        var mouseOver = function () {
            $(this).addClass('c-nav-btn-over');
        };
        var mouseLeave = function () {
            $(this).removeClass('c-nav-btn-over');
        };

		var mouseClickEvent = function () {
            var d = $(this).attr('data-event-day');
            if(d){
                if(!$(this).hasClass('c-event-clicked')){
                    $('.c-event-clicked').removeClass('c-event-clicked');
                    $('.c-event-item-class').hide();
                    $(this).addClass('c-event-clicked');
                    $('div.c-event-item[data-event-day="' + d + '"]').addClass('c-event-clicked');
                    $('.c-event-item-'+d).show();
                }else{
                    $(this).removeClass('c-event-clicked');
                    $('div.c-event-item[data-event-day="' + d + '"]').removeClass('c-event-clicked');
                    $('.c-event-item-'+d).hide();
                }
            }else{
                var full_date = $(this).attr('full_date');
                if(multydate!=''){
                    multydate += ","+full_date;
                }else{
                    multydate += full_date;
                }
                var html_form="<form  method='post'>";
                html_form+='<div class="form-group" style="width: 96%">';
                html_form+="<label>Date: </label>";
                html_form+="    <input type='text' class='form-control' disabled value='"+multydate+"' name='date_out'>";
                html_form+="    <input type='hidden'  value='"+multydate+"' name='date'>";
                html_form+="<label>Name: </label>";
                html_form+="<input class='form-control item_name'  type='text' name='name' value='Sunday'>";
                html_form+="<label>Select type: </label>";
                html_form+="<select class='form-control' onChange='select_type(this)' name='type'>";
                html_form+="    <option vlaue='unavaliable'>Unavaliable</option>";
                html_form+="    <option value='free'>Free Delivery</option>";
                html_form+="    <option value='oot'>OOT Closed</option>";
                html_form+="    <option value='ootsurcharge'>OOT Closed, Rest Surcharge</option>";
                html_form+="    <option value='surcharge'>Surcharge</option>";
                html_form+="</select>";
                html_form+="<label class='price'  style='display: none;'>Price: </label>";
                html_form+="    <input class='form-control price' style='display: none;' type='number' value='0' name='price'>";
                html_form+="    <input type='hidden' value='com_deliver' name='option'>";
                html_form+="    <input type='hidden' value='create' name='task'>";
                html_form+="    <input class='btn btn-success' type='submit' style='margin-top:20px;width:100%' value='Create' name='submit'>";
                html_form+="</div>";
                html_form+="</form>";



                $('.c-event-clicked').removeClass('c-event-clicked');
                $('.c-event-item-class').hide();
                var day = $(this).text()
                var item = $('<div/>').addClass('c-event-item-class').addClass('c-event-item-'+day);
                var title = $('<div/>').addClass('title').html('new Item <span class="close_form" title="Close" onclick="close_event()">X</span>');
                var html = $('<div/>').addClass('description').html(html_form);
                item.attr('data-event-day', day);
                item.attr('event_full_date', multydate);
                item.append(title).append(html);
                selected_date_html = item;
                $('.c-event-list').append(item)

            }
        };

        var nextMonth = function () {
            if (dMonth < 11) {
                dMonth++;
            } else {
                dMonth = 0;
                dYear++;
            }
            print();
        };
        var previousMonth = function () {
            if (dMonth > 0) {
                dMonth--;
            } else {
                dMonth = 11;
                dYear--;
            }
            print();
        };

        function loadEvents() {
            if (typeof settings.url != 'undefined' && settings.url != '') {
                $.ajax({url: settings.url,
                    async: false,
                    success: function (result) {
                        settings.events = result;
                    }
                });
            }
        }

        function print() {
            loadEvents();
            var dWeekDayOfMonthStart = new Date(dYear, dMonth, 1).getDay() - settings.firstDayOfWeek;
            if (dWeekDayOfMonthStart < 0) {
                dWeekDayOfMonthStart = 6 - ((dWeekDayOfMonthStart + 1) * -1);
            }
            var dLastDayOfMonth = new Date(dYear, dMonth + 1, 0).getDate();
            var dLastDayOfPreviousMonth = new Date(dYear, dMonth + 1, 0).getDate() - dWeekDayOfMonthStart + 1;

            var cBody = $('<div/>').addClass('c-grid');
            var cEvents = $('<div/>').addClass('c-event-grid');
            var cEventsBody = $('<div/>').addClass('c-event-body');
            cEvents.append($('<div/>').addClass('c-event-title c-pad-top').html(settings.eventTitle));
            cEvents.append(cEventsBody);
            var cNext = $('<div/>').addClass('c-next c-grid-title c-pad-top');
            var cMonth = $('<div/>').addClass('c-month c-grid-title c-pad-top');
            var cPrevious = $('<div/>').addClass('c-previous c-grid-title c-pad-top');
            cPrevious.html(settings.textArrows.previous);
            cMonth.html(settings.months[dMonth] + ' ' + dYear);
            cNext.html(settings.textArrows.next);

            cPrevious.on('mouseover', mouseOver).on('mouseleave', mouseLeave).on('click', previousMonth);
            cNext.on('mouseover', mouseOver).on('mouseleave', mouseLeave).on('click', nextMonth);

            cBody.append(cPrevious);
            cBody.append(cMonth);
            cBody.append(cNext);
            var dayOfWeek = settings.firstDayOfWeek;
            for (var i = 0; i < 7; i++) {
                if (dayOfWeek > 6) {
                    dayOfWeek = 0;
                }
                var cWeekDay = $('<div/>').addClass('c-week-day c-pad-top');
                cWeekDay.html(settings.weekDays[dayOfWeek]);
                cBody.append(cWeekDay);
                dayOfWeek++;
            }
            var day = 1;
            var dayOfNextMonth = 1;
            for (var i = 0; i < 42; i++) {
                var cDay = $('<div/>');
                if (i < dWeekDayOfMonthStart) {
                    cDay.addClass('c-day-previous-month c-pad-top');
                    cDay.html(dLastDayOfPreviousMonth++);
                } else if (day <= dLastDayOfMonth) {

                    cDay.addClass('c-day c-pad-top')
                    if (day == dDay && adMonth == dMonth && adYear == dYear) {
                        cDay.addClass('c-today');
                    }
                    for (var j = 0; j < settings.events.length; j++) {
                        var d = settings.events[j].datetime;

						var classname = settings.events[j].classname;
						if(!classname){classname='c-event';}

                        var month = d.getMonth();
                        if (d.getDate() == day && month == dMonth && d.getFullYear() == dYear) {

							cDay.addClass(classname).attr('data-event-day', d.getDate());
                        }

                    }
                        var full_date = dYear + '-' + lpad(dMonth + 1, 2) + '-'+lpad(day, 2);
                        cDay.attr('full_date',full_date);

                        cDay.on('click',mouseClickEvent);

                    cDay.html(day++);
                } else {
                    cDay.addClass('c-day-next-month c-pad-top');
                    cDay.html(dayOfNextMonth++);
                }
                // cDay.attr('full_date', settings.events[i].datetime);
                cBody.append(cDay);
            }
            var eventList = $('<div/>').addClass('c-event-list');
            for (var i = 0; i < settings.events.length; i++) {

                var d = settings.events[i].datetime;
                var id=settings.events[i].id
                var month = d.getMonth();
                if (month == dMonth && d.getFullYear() == dYear) {
                    var date = lpad(d.getDate(), 2) + '/' + lpad(month + 1, 2);
                    var full_date = d.getFullYear() + '-' + lpad(month + 1, 2) + '-'+lpad(d.getDate(), 2);
                    var item = $('<div/>').addClass('c-event-item-class').addClass('c-event-item-'+d.getDate()).attr('event_full_date',full_date).hide();
                    var title = $('<div/>').addClass('title').html(full_date + '  ' + settings.events[i].title + '<br/>');
                    var delete_item = "<p class='delete_item' title='Delete Item' onclick='delete_item(this)' item='"+id+"'>X</p>";
                    var html = $('<div/>').addClass('description').html(settings.events[i].html);
                    item.attr('data-event-day', d.getDate());
                    item.append(delete_item).append(title).append(html);

                    // Add the url to the description if is set
                    if( settings.events[i].url !== undefined )
                    {
                        /**
                         * If the setting url_blank is set and is true, the target of the url
                         * will be "_blank"
                         */
                        type_url = settings.events[i].url_blank !== undefined && 
                                   settings.events[i].url_blank === true ? 
                                   '_blank':'';
                        html.wrap( '<a href="'+ settings.events[i].url +'" target="'+type_url+'" ></a>' );
                    }

                    eventList.append(item);
                }
            }
            eventList.append(selected_date_html)
            $(instance).addClass('calendar');
            cEventsBody.append(eventList);
            $(instance).html(cBody).append(cEvents);
        }

        return print();
    }

    $.fn.eCalendar = function (oInit) {
        return this.each(function () {
            return eCalendar(oInit, $(this));
        });
    };

    // plugin defaults
    $.fn.eCalendar.defaults = {
        weekDays: [ 'Sun','Mon','Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
        months: ['January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'],
        textArrows: {previous: '<', next: '>'},
        eventTitle: '',
        url: '',
        events: [
            {title: 'Evento de Abertura', description: 'Abertura das Olimpíadas Rio 2016', datetime: new Date(2016, new Date().getMonth(), 12, 17)},
            {title: 'Tênis de Mesa', description: 'BRA x ARG - Semifinal', datetime: new Date(2016, new Date().getMonth(), 23, 16)},
            {title: 'Ginástica Olímpica', description: 'Classificatórias de equipes', datetime: new Date(2016, new Date().getMonth(), 31, 16)}
        ],
        firstDayOfWeek: 0
    };

}(jQuery));