/**
 * Countdown Plugin
 * Created by Zhukov Sergey
 * Email: zom688@gmail.com
 * Website: www.websiteprog.ru
 *
 * @param date      object with properties of target date ('second',minute','hour','day','month','year')
 *                  by default is using current day values. If you give only one numeric parameter,
 *                  timer will counting seconds by decrease this value.
 * @param element   object with properties of dom elements to display values('days', 'hours', 'minutes', 'second')
 *                  if you give only one string parameter it will specify id of target element
 *                  and create span elements inside
 * @param padzero   pad digits with zero - true or false
 *
 * samples:
 * countDown(30);           //countdown 30 seconds from now
 * countdown(30, "time");   //will create span elements inside element with "time" id and display timer there
 *
 * //29 august, current year, current hour, 40th minute. Puts seconds values inside element with 'myseconds' id
 * countdown({'day': 29, 'month': 8, 'minute': 40 }, { 'seconds':  document.getElementById('myseconds') }, true)
 *
 *
 */
(function () {

    function _extend(a, b) {
        for(var key in b)
            if( a.hasOwnProperty(key) ) a[key] = b[key];
        return a;
    }

    function _padZero(e) {
        return (e.toString().length == 1) ? '0'+ e : e;
    }

    function _createElements(elem, arr, html) {
        console.log(elem);
        console.log(arr);
        for( var i=0 ; i < arr.length; i++) {
            var obj = document.createElement('span');
            obj.id = arr[i]
            elem.appendChild(obj);
            html[arr[i]] = obj;
        }
    }

    countdown = function(date, element, padzero) {

        var now = new Date(), days, hours, minutes, seconds;
        var target = {
            'second': now.getSeconds(),
            'minute': now.getMinutes(),
            'hour':  now.getHours(),
            'day':  now.getDate(),
            'month': now.getMonth(),
            'year': now.getFullYear()
        }
        var html = {
            'seconds': document.getElementById('seconds'),
            'minutes': document.getElementById('minutes'),
            'hours': document.getElementById('hours'),
            'days': document.getElementById('days')
        }

        if( typeof element === 'string' && document.body.contains(document.getElementById(element)) ) {
            _createElements (document.getElementById(element), ['days', 'hours', 'minutes', 'seconds'], html);
        } else {
            html = _extend( html, element);
        }

        if (typeof date === 'number') {
            target.second = date + now.getSeconds() + 1;
        } else {
            date.month--;
            target = _extend( target, date );
            target.second++;
        }

        var endDate = new Date(target.year, target.month, target.day, target.hour, target.minute, target.second).getTime();
        var time = endDate - now.getTime();

        if (time < 0) {
            alert("Wrong date");
            return;
        }

        (function () {

            time = endDate - now.getTime();

            days = Math.floor(time / 864e5);
            hours = Math.floor(time / 36e5) % 24;
            minutes = Math.floor(time / 6e4) % 60;
            seconds = Math.floor(time / 1e3) % 60;

            html.days.innerHTML = padzero? _padZero(days) : days;
            html.hours.innerHTML = padzero? _padZero(hours) : hours ;
            html.minutes.innerHTML = padzero? _padZero(minutes) : minutes;
            html.seconds.innerHTML = padzero? _padZero(seconds): seconds;

            now.setSeconds(now.getSeconds() + 1);

            if (!seconds && !minutes && !days && !hours) {
                alert("The time is now");
            } else {
                setTimeout(arguments.callee, 1000);
            }
        })();
    }
})();

function addEvent(element, eventName, callback) {
    if (element.addEventListener) {
        element.addEventListener(eventName, callback, false);
    }
    else {
        element.attachEvent('on' + eventName, callback);
    }
}

function ajaxJSON(url, callback) {
    xml = new XMLHttpRequest();
    xml.open('GET', url, true);
    xml.onreadystatechange = function () {
        if (xml.readyState == 4) {
            if (xml.status == 200) {
                json = JSON.parse(xml.responseText);
                callback(json);
            }
        }
    };
    xml.send(null);
}


function popup_show(elem) {
    var w1 = document.body.offsetWidth;
    document.body.style.overflow = 'hidden';
    elem.style.display = 'block';
    var scrollwidth = w1 - document.body.offsetWidth;
    document.documentElement.style.marginRight = (0 - scrollwidth) + 'px';
    elem.className = elem.className + ' ready';
}

function popup_hide(elem) {
    elem.className = popup.className.replace(' ready', '');
    elem.className = popup.className + ' remove';

    setTimeout( function() {
        elem.className = elem.className.replace(' remove', '');
        document.body.style.overflow= 'auto';
        elem.style.display = 'none';
        document.documentElement.style.marginRight = 0;
    }, 500);
}

function setStateOptions(e) {
    var states = allstates[e.value], elem = document.getElementById('state');
    if (typeof states !== 'undefined' && states.length) {
        var select = document.createElement('select');
        select.id = 'state';
        select.name = 'state';
        for (i = 0; i < states.length; i++) {
            var state = states[i].split(':');
            var option = document.createElement('option');
            option.value = state[1];
            option.innerHTML = state[0];
            select.appendChild(option);
        }
        elem.parentNode.replaceChild(select, elem);
        document.getElementById('state-text').value = state[0];
        document.getElementById('state').onchange = function() {
            document.getElementById('state-text').value = e.options[e.selectedIndex].text;
        }
    } else if (elem.value || elem.tagName == 'select') {
        var input = document.createElement('input');
        input.type = 'text';
        input.name = 'state';
        input.id = 'state';
        elem.parentNode.replaceChild(input, elem);
        document.getElementById('state-text').value = '';
    }
    document.getElementById('country-text').value = e.options[e.selectedIndex].text;
}

var easeOutBounce = function (x, t, b, c, d) {
    if ((t/=d) < (1/2.75)) {
        return c*(7.5625*t*t) + b;
    } else if (t < (2/2.75)) {
        return c*(7.5625*(t-=(1.5/2.75))*t + .75) + b;
    } else if (t < (2.5/2.75)) {
        return c*(7.5625*(t-=(2.25/2.75))*t + .9375) + b;
    } else {
        return c*(7.5625*(t-=(2.625/2.75))*t + .984375) + b;
    }
}

function Animate(elem, propName, duration, start, end)  {
    var start_time = new Date().getTime();
    var interval = setInterval(function() {
        var current_time = new Date().getTime(),
            remaining = Math.max(0, start_time + duration - current_time),
            temp = remaining / duration || 0,
            percent = 1 - temp;

        if (start_time + duration < current_time) clearInterval(interval);

        var pos = easeOutBounce(null, duration * percent, 0, 1, duration),
            current = (end - start) * pos + start;

        elem.style[propName] = current + 'px';
    }, 1);
}

function sendContactForm(formid) {
    if (validate(formid) && opened)
    {
        var elems = document.getElementById(formid).querySelectorAll("select,input,textarea");
        var params = '';
        for (i=0; i < elems.length; i++)  {
            var elem = elems[i];
            if (i == 0) {
                params += '?'+elem.getAttribute('name')+'='+elem.value;
                elem.value = '';
            } else {
                params += '&'+elem.getAttribute('name')+'='+elem.value;
                elem.value = '';
            }
        }
        var loader = document.getElementById('preloader'), notice = document.getElementById('notice-msg');
        loader.style.display = 'block';
        ajaxJSON('sendcontact.php'+params, function(res) {
            if (res.status == 'ok') {
                loader.style.display = 'none';
                //Animate(document.getElementById('contactform'), 'right', 800, 0, -320);
                //opened = false;
                notice.innerHTML = 'Your message was sent!'
                notice.style.display = 'block';
            }
        });
    }
    return false;
}

function validate(formid)
{
    var elems = document.getElementById(formid).querySelectorAll("select,input,textarea"),
        errors = 0;

    for (i=0; i < elems.length; i++)  {
        if (!elems[i].value && elems[i].parentNode.getAttribute('type') == 'required')  {
            elems[i].setAttribute('valid', 'false');
            errors++;
        }  else {
            elems[i].setAttribute('valid', 'true');
        }
    }
    return errors > 0 ? false : true;
}