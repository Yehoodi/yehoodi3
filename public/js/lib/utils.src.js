function mktime () {
    // http://kevin.vanzonneveld.net
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: baris ozdil
    // +      input by: gabriel paderni
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: FGFEmperor
    // +      input by: Yannoo
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +      input by: jakes
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   bugfixed by: Marc Palau
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // +      input by: 3D-GRAF
    // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
    // *     example 1: mktime(14, 10, 2, 2, 1, 2008);
    // *     returns 1: 1201871402
    // *     example 2: mktime(0, 0, 0, 0, 1, 2008);
    // *     returns 2: 1196463600
    // *     example 3: make = mktime();
    // *     example 3: td = new Date();
    // *     example 3: real = Math.floor(td.getTime()/1000);
    // *     example 3: diff = (real - make);
    // *     results 3: diff < 5
    // *     example 4: mktime(0, 0, 0, 13, 1, 1997)
    // *     returns 4: 883609200
    // *     example 5: mktime(0, 0, 0, 1, 1, 1998)
    // *     returns 5: 883609200
    // *     example 6: mktime(0, 0, 0, 1, 1, 98)
    // *     returns 6: 883609200

    var no=0, i = 0, ma=0, mb=0, d = new Date(), dn = new Date(), argv = arguments, argc = argv.length;

    var dateManip = {
        0: function (tt){ return d.setHours(tt); },
        1: function (tt){ return d.setMinutes(tt); },
        2: function (tt){ var set = d.setSeconds(tt); mb = d.getDate() - dn.getDate(); d.setDate(1); return set;},
        3: function (tt){ var set = d.setMonth(parseInt(tt, 10)-1); ma = d.getFullYear() - dn.getFullYear(); return set;},
        4: function (tt){ return d.setDate(tt+mb);},
        5: function (tt){
            if (tt >= 0 && tt <= 69) {
                tt += 2000;
            }
            else if (tt >= 70 && tt <= 100) {
                tt += 1900;
            }
            return d.setFullYear(tt+ma);
        }
        // 7th argument (for DST) is deprecated
    };

    for (i = 0; i < argc; i++){
        no = parseInt(argv[i]*1, 10);
        if (isNaN(no)) {
            return false;
        } else {
            // arg is number, let's manipulate date object
            if (!dateManip[i](no)){
                // failed
                return false;
            }
        }
    }
    for (i = argc; i < 6; i++) {
        switch (i) {
            case 0:
                no = dn.getHours();
                break;
            case 1:
                no = dn.getMinutes();
                break;
            case 2:
                no = dn.getSeconds();
                break;
            case 3:
                no = dn.getMonth()+1;
                break;
            case 4:
                no = dn.getDate();
                break;
            case 5:
                no = dn.getFullYear();
                break;
        }
        dateManip[i](no);
    }

    return Math.floor(d.getTime()/1000);
}


function date ( format, timestamp ) {
    // http://kevin.vanzonneveld.net
    // +   original by: Carlos R. L. Rodrigues (http://www.jsfromhell.com)
    // +      parts by: Peter-Paul Koch (http://www.quirksmode.org/js/beat.html)
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: MeEtc (http://yass.meetcweb.com)
    // +   improved by: Brad Touesnard
    // +   improved by: Tim Wiel
    // +   improved by: Bryan Elliott
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // +   improved by: David Randall
    // +      input by: Brett Zamir (http://brett-zamir.me)
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // +   improved by: Theriault
    // +  derived from: gettimeofday
    // +      input by: majak
    // +   bugfixed by: majak
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +      input by: Alex
    // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
    // +   improved by: Theriault
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // +   improved by: Theriault
    // %        note 1: Uses global: php_js to store the default timezone
    // *     example 1: date('H:m:s \\m \\i\\s \\m\\o\\n\\t\\h', 1062402400);
    // *     returns 1: '09:09:40 m is month'
    // *     example 2: date('F j, Y, g:i a', 1062462400);
    // *     returns 2: 'September 2, 2003, 2:26 am'
    // *     example 3: date('Y W o', 1062462400);
    // *     returns 3: '2003 36 2003'
    // *     example 4: x = date('Y m d', (new Date()).getTime()/1000); // 2009 01 09
    // *     example 4: (x+'').length == 10
    // *     returns 4: true
    // *     example 5: date('W', 1104534000);
    // *     returns 5: '53'
    // *     example 6: date('B t', 1104534000);
    // *     returns 6: '999 31'
    // *     example 7: date('W', 1293750000); // 2010-12-31
    // *     returns 7: '52'
    // *     example 8: date('W', 1293836400); // 2011-01-01
    // *     returns 8: '52'
    // *     example 9: date('W Y-m-d', 1293974054); // 2011-01-02
    // *     returns 9: '52 2011-01-02'

    var that = this,
        jsdate = (
        (typeof timestamp === 'undefined') ? new Date() : // Not provided
        (timestamp instanceof Date) ? new Date(timestamp) : // Javascript Date()
        new Date(timestamp * 1000) // UNIX timestamp (auto-convert to int)
    ), //, tal= [], // Keep this here (works, but for code commented-out below for file size reasons)
        formatChr = /\\?([a-z])/gi,
        formatChrCb = function (t, s) {
            return f[t] ? f[t]() : s;
        },
        _pad = function (n, c) {
            if ((n = n + "").length < c) {
                return new Array((++c) - n.length).join("0") + n;
            } else {
                return n;
            }
        },
        txt_words = ["Sun", "Mon", "Tues", "Wednes", "Thurs", "Fri", "Satur",
        "January", "February", "March", "April", "May", "June", "July",
        "August", "September", "October", "November", "December"],
        txt_ordin = {1: "st", 2: "nd", 3: "rd", 21: "st", 22: "nd", 23: "rd", 31: "st"},
        f = {
        // Day
            d: function () {
                return _pad(f.j(), 2);
            },
            D: function () {
                return f.l().slice(0, 3);
            },
            j: function () {
                return jsdate.getDate();
            },
            l: function () {
                return txt_words[f.w()] + 'day';
            },
            N: function () {
                return f.w() || 7;
            },
            S: function () {
                return txt_ordin[f.j()] || 'th';
            },
            w: function () {
                return jsdate.getDay();
            },
            z: function () {
                return (jsdate - new Date(f.Y(), 0, 1)) / 864e5 >> 0;
            },

        // Week
            W: function () {
                var c = new Date(f.Y(), f.n() - 1, f.j() - f.N() + 3);
                return 1 + Math.round((c - (new Date(c.getFullYear(), 0, 4))) / 864e5 / 7);
            },

        // Month
            F: function () {
                return txt_words[6 + f.n()];
            },
            m: function () {
                return _pad(f.n(), 2);
            },
            M: function () {
                return f.F().slice(0, 3);
            },
            n: function () {
                return jsdate.getMonth() + 1;
            },
            t: function () {
                return (new Date(f.Y(), f.n() + 1, 0)).getDate();
            },

        // Year
            L: function () {
                var y = f.Y();
                return (!(y & 3) && (y % 1e2 || !(y % 4e2))) ? 1 : 0;
            },
            o: function () {
                return f.Y() + (f.n() === 12 && f.W() < 9 ? -1 : (f.n() === 1 && f.W() > 9 ? 1 : 0));
            },
            Y: function () {
                return jsdate.getFullYear();
            },
            y: function () {
                return (jsdate.getFullYear() + "").slice(2);
            },

        // Time
            a: function () {
                return jsdate.getHours() > 11 ? "pm" : "am";
            },
            A: function () {
                return f.a().toUpperCase();
            },
            B: function () {
                return _pad(Math.floor(((jsdate.getUTCHours() * 36e2) + (jsdate.getUTCMinutes() * 60) +
                                        jsdate.getUTCSeconds() + 36e2) / 86.4) % 1e3, 3);
            },
            g: function () {
                return jsdate.getHours() % 12 || 12;
            },
            G: function () {
                return jsdate.getHours();
            },
            h: function () {
                return _pad(f.g(), 2);
            },
            H: function () {
                return _pad(f.G(), 2);
            },
            i: function () {
                return _pad(jsdate.getMinutes(), 2);
            },
            s: function () {
                return _pad(jsdate.getSeconds(), 2);
            },
            u: function () {
                return _pad(jsdate.getMilliseconds() * 1000, 6);
            },

        // Timezone
            e: function () {
// The following works, but requires inclusion of the very large timezone_abbreviations_list() function
/*                var abbr='', i=0;
                if (that.php_js && that.php_js.default_timezone) {
                    return that.php_js.default_timezone;
                }
                if (!tal.length) {
                    tal = that.timezone_abbreviations_list();
                }
                for (abbr in tal) {
                    for (i=0; i < tal[abbr].length; i++) {
                        if (tal[abbr][i].offset === -jsdate.getTimezoneOffset()*60) {
                            return tal[abbr][i].timezone_id;
                        }
                    }
                }
*/
                return 'UTC';
            },
            I: function () {
                // Compares Jan 1 minus Jan 1 UTC to Jul 1 minus Jul 1 UTC.
                // If they are not equal, then DST is observed.
                return 0 + (((new Date(f.Y(), 0)) - Date.UTC(f.Y(), 0)) !== ((new Date(f.Y(), 6)) - Date.UTC(f.Y(), 6)));
            },
            O: function () {
                var a = jsdate.getTimezoneOffset();
                return (a > 0 ? "-" : "+") + _pad(Math.abs(a / 60 * 100), 4);
            },
            P: function () {
                var O = f.O();
                return (O.substr(0, 3) + ":" + O.substr(3, 2));
            },
            T: function () {

// The following works, but requires inclusion of the very large timezone_abbreviations_list() function
/*                var abbr='', i=0;
                if (!tal.length) {
                    tal = that.timezone_abbreviations_list();
                }
                if (that.php_js && that.php_js.default_timezone) {
                    for (abbr in tal) {
                        for (i=0; i < tal[abbr].length; i++) {
                            if (tal[abbr][i].timezone_id === that.php_js.default_timezone) {
                                return abbr.toUpperCase();
                            }
                        }
                    }
                }
                for (abbr in tal) {
                    for (i=0; i < tal[abbr].length; i++) {
                        if (tal[abbr][i].offset === -jsdate.getTimezoneOffset()*60) {
                            return abbr.toUpperCase();
                        }
                    }
                }
*/
                return 'UTC';
            },
            Z: function () {
                return -jsdate.getTimezoneOffset() * 60;
            },

        // Full Date/Time
            c: function () {
                return 'Y-m-d\\Th:i:sP'.replace(formatChr, formatChrCb);
            },
            r: function () {
                return 'D, d M Y H:i:s O'.replace(formatChr, formatChrCb);
            },
            U: function () {
                return Math.round(jsdate.getTime() / 1000);
            }
        };
    return format.replace(formatChr, formatChrCb);
}

function utf8_encode ( argString ) {
    // http://kevin.vanzonneveld.net
    // +   original by: Webtoolkit.info (http://www.webtoolkit.info/)
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: sowberry
    // +    tweaked by: Jack
    // +   bugfixed by: Onno Marsman
    // +   improved by: Yves Sucaet
    // +   bugfixed by: Onno Marsman
    // +   bugfixed by: Ulrich
    // *     example 1: utf8_encode('Kevin van Zonneveld');
    // *     returns 1: 'Kevin van Zonneveld'

    var string = (argString+''); // .replace(/\r\n/g, "\n").replace(/\r/g, "\n");

    var utftext = "";
    var start, end;
    var stringl = 0;

    start = end = 0;
    stringl = string.length;
    for (var n = 0; n < stringl; n++) {
        var c1 = string.charCodeAt(n);
        var enc = null;

        if (c1 < 128) {
            end++;
        } else if (c1 > 127 && c1 < 2048) {
            enc = String.fromCharCode((c1 >> 6) | 192) + String.fromCharCode((c1 & 63) | 128);
        } else {
            enc = String.fromCharCode((c1 >> 12) | 224) + String.fromCharCode(((c1 >> 6) & 63) | 128) + String.fromCharCode((c1 & 63) | 128);
        }
        if (enc !== null) {
            if (end > start) {
                utftext += string.substring(start, end);
            }
            utftext += enc;
            start = end = n+1;
        }
    }

    if (end > start) {
        utftext += string.substring(start, string.length);
    }

    return utftext;
}

function serialize (mixed_value) {
    // http://kevin.vanzonneveld.net
    // +   original by: Arpad Ray (mailto:arpad@php.net)
    // +   improved by: Dino
    // +   bugfixed by: Andrej Pavlovic
    // +   bugfixed by: Garagoth
    // +      input by: DtTvB (http://dt.in.th/2008-09-16.string-length-in-bytes.html)
    // +   bugfixed by: Russell Walker (http://www.nbill.co.uk/)
    // +   bugfixed by: Jamie Beck (http://www.terabit.ca/)
    // +      input by: Martin (http://www.erlenwiese.de/)
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // -    depends on: utf8_encode
    // %          note: We feel the main purpose of this function should be to ease the transport of data between php & js
    // %          note: Aiming for PHP-compatibility, we have to translate objects to arrays
    // *     example 1: serialize(['Kevin', 'van', 'Zonneveld']);
    // *     returns 1: 'a:3:{i:0;s:5:"Kevin";i:1;s:3:"van";i:2;s:9:"Zonneveld";}'
    // *     example 2: serialize({firstName: 'Kevin', midName: 'van', surName: 'Zonneveld'});
    // *     returns 2: 'a:3:{s:9:"firstName";s:5:"Kevin";s:7:"midName";s:3:"van";s:7:"surName";s:9:"Zonneveld";}'

    var _getType = function (inp) {
        var type = typeof inp, match;
        var key;
        if (type == 'object' && !inp) {
            return 'null';
        }
        if (type == "object") {
            if (!inp.constructor) {
                return 'object';
            }
            var cons = inp.constructor.toString();
            match = cons.match(/(\w+)\(/);
            if (match) {
                cons = match[1].toLowerCase();
            }
            var types = ["boolean", "number", "string", "array"];
            for (key in types) {
                if (cons == types[key]) {
                    type = types[key];
                    break;
                }
            }
        }
        return type;
    };
    var type = _getType(mixed_value);
    var val, ktype = '';
    
    switch (type) {
        case "function": 
            val = ""; 
            break;
        case "boolean":
            val = "b:" + (mixed_value ? "1" : "0");
            break;
        case "number":
            val = (Math.round(mixed_value) == mixed_value ? "i" : "d") + ":" + mixed_value;
            break;
        case "string":
            mixed_value = this.utf8_encode(mixed_value);
            val = "s:" + encodeURIComponent(mixed_value).replace(/%../g, 'x').length + ":\"" + mixed_value + "\"";
            break;
        case "array":
        case "object":
            val = "a";
            /*
            if (type == "object") {
                var objname = mixed_value.constructor.toString().match(/(\w+)\(\)/);
                if (objname == undefined) {
                    return;
                }
                objname[1] = this.serialize(objname[1]);
                val = "O" + objname[1].substring(1, objname[1].length - 1);
            }
            */
            var count = 0;
            var vals = "";
            var okey;
            var key;
            for (key in mixed_value) {
                ktype = _getType(mixed_value[key]);
                if (ktype == "function") { 
                    continue; 
                }
                
                okey = (key.match(/^[0-9]+$/) ? parseInt(key, 10) : key);
                vals += this.serialize(okey) +
                        this.serialize(mixed_value[key]);
                count++;
            }
            val += ":" + count + ":{" + vals + "}";
            break;
        case "undefined": // Fall-through
        default: // if the JS object has a property which contains a null value, the string cannot be unserialized by PHP
            val = "N";
            break;
    }
    if (type != "object" && type != "array") {
        val += ";";
    }
    return val;
}