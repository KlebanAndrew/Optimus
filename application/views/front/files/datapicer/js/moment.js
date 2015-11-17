
(function (undefined) {

    /************************************
        Constants
    ************************************/

    var moment,
        VERSION = "2.5.1",
        global = this,
        round = Math.round,
        i,

        YEAR = 0,
        MONTH = 1,
        DATE = 2,
        HOUR = 3,
        MINUTE = 4,
        SECOND = 5,
        MILLISECOND = 6,

        // internal storage for language config files
        languages = {},

        // moment internal properties
        momentProperties = {
            _isAMomentObject: null,
            _i : null,
            _f : null,
            _l : null,
            _strict : null,
            _isUTC : null,
            _offset : null,  // optional. Combine with _isUTC
            _pf : null,
            _lang : null  // optional
        },

        // check for nodeJS
        hasModule = (typeof module !== 'undefined' && module.exports && typeof require !== 'undefined'),

        // ASP.NET json date format regex
        aspNetJsonRegex = /^\/?Date\((\-?\d+)/i,
        aspNetTimeSpanJsonRegex = /(\-)?(?:(\d*)\.)?(\d+)\:(\d+)(?:\:(\d+)\.?(\d{3})?)?/,

        // from http://docs.closure-library.googlecode.com/git/closure_goog_date_date.js.source.html
        // somewhat more in line with 4.4.3.2 2004 spec, but allows decimal anywhere
        isoDurationRegex = /^(-)?P(?:(?:([0-9,.]*)Y)?(?:([0-9,.]*)M)?(?:([0-9,.]*)D)?(?:T(?:([0-9,.]*)H)?(?:([0-9,.]*)M)?(?:([0-9,.]*)S)?)?|([0-9,.]*)W)$/,

        // format tokens
        formattingTokens = /(\[[^\[]*\])|(\\)?(Mo|MM?M?M?|Do|DDDo|DD?D?D?|ddd?d?|do?|w[o|w]?|W[o|W]?|YYYYYY|YYYYY|YYYY|YY|gg(ggg?)?|GG(GGG?)?|e|E|a|A|hh?|HH?|mm?|ss?|S{1,4}|X|zz?|ZZ?|.)/g,
        localFormattingTokens = /(\[[^\[]*\])|(\\)?(LT|LL?L?L?|l{1,4})/g,

        // parsing token regexes
        parseTokenOneOrTwoDigits = /\d\d?/, // 0 - 99
        parseTokenOneToThreeDigits = /\d{1,3}/, // 0 - 999
        parseTokenOneToFourDigits = /\d{1,4}/, // 0 - 9999
        parseTokenOneToSixDigits = /[+\-]?\d{1,6}/, // -999,999 - 999,999
        parseTokenDigits = /\d+/, // nonzero number of digits
        parseTokenWord = /[0-9]*['a-z\u00A0-\u05FF\u0700-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+|[\u0600-\u06FF\/]+(\s*?[\u0600-\u06FF]+){1,2}/i, // any word (or two) characters or numbers including two/three word month in arabic.
        parseTokenTimezone = /Z|[\+\-]\d\d:?\d\d/gi, // +00:00 -00:00 +0000 -0000 or Z
        parseTokenT = /T/i, // T (ISO separator)
        parseTokenTimestampMs = /[\+\-]?\d+(\.\d{1,3})?/, // 123456789 123456789.123

        //strict parsing regexes
        parseTokenOneDigit = /\d/, // 0 - 9
        parseTokenTwoDigits = /\d\d/, // 00 - 99
        parseTokenThreeDigits = /\d{3}/, // 000 - 999
        parseTokenFourDigits = /\d{4}/, // 0000 - 9999
        parseTokenSixDigits = /[+-]?\d{6}/, // -999,999 - 999,999
        parseTokenSignedNumber = /[+-]?\d+/, // -inf - inf

        // iso 8601 regex
        // 0000-00-00 0000-W00 or 0000-W00-0 + T + 00 or 00:00 or 00:00:00 or 00:00:00.000 + +00:00 or +0000 or +00)
        isoRegex = /^\s*(?:[+-]\d{6}|\d{4})-(?:(\d\d-\d\d)|(W\d\d$)|(W\d\d-\d)|(\d\d\d))((T| )(\d\d(:\d\d(:\d\d(\.\d+)?)?)?)?([\+\-]\d\d(?::?\d\d)?|\s*Z)?)?$/,

        isoFormat = 'YYYY-MM-DDTHH:mm:ssZ',

        isoDates = [
            ['YYYYYY-MM-DD', /[+-]\d{6}-\d{2}-\d{2}/],
            ['YYYY-MM-DD', /\d{4}-\d{2}-\d{2}/],
            ['GGGG-[W]WW-E', /\d{4}-W\d{2}-\d/],
            ['GGGG-[W]WW', /\d{4}-W\d{2}/],
            ['YYYY-DDD', /\d{4}-\d{3}/]
        ],

        // iso time formats and regexes
        isoTimes = [
            ['HH:mm:ss.SSSS', /(T| )\d\d:\d\d:\d\d\.\d{1,3}/],
            ['HH:mm:ss', /(T| )\d\d:\d\d:\d\d/],
            ['HH:mm', /(T| )\d\d:\d\d/],
            ['HH', /(T| )\d\d/]
        ],

        // timezone chunker "+10:00" > ["10", "00"] or "-1530" > ["-15", "30"]
        parseTimezoneChunker = /([\+\-]|\d\d)/gi,

        // getter and setter names
        proxyGettersAndSetters = 'Date|Hours|Minutes|Seconds|Milliseconds'.split('|'),
        unitMillisecondFactors = {
            'Milliseconds' : 1,
            'Seconds' : 1e3,
            'Minutes' : 6e4,
            'Hours' : 36e5,
            'Days' : 864e5,
            'Months' : 2592e6,
            'Years' : 31536e6
        },

        unitAliases = {
            ms : 'millisecond',
            s : 'second',
            m : 'minute',
            h : 'hour',
            d : 'day',
            D : 'date',
            w : 'week',
            W : 'isoWeek',
            M : 'month',
            y : 'year',
            DDD : 'dayOfYear',
            e : 'weekday',
            E : 'isoWeekday',
            gg: 'weekYear',
            GG: 'isoWeekYear'
        },

        camelFunctions = {
            dayofyear : 'dayOfYear',
            isoweekday : 'isoWeekday',
            isoweek : 'isoWeek',
            weekyear : 'weekYear',
            isoweekyear : 'isoWeekYear'
        },

        // format function strings
        formatFunctions = {},

        // tokens to ordinalize and pad
        ordinalizeTokens = 'DDD w W M D d'.split(' '),
        paddedTokens = 'M D H h m s w W'.split(' '),

        formatTokenFunctions = {
            M    : function () {
                return this.month() + 1;
            },
            MMM  : function (format) {
                return this.lang().monthsShort(this, format);
            },
            MMMM : function (format) {
                return this.lang().months(this, format);
            },
            D    : function () {
                return this.date();
            },
            DDD  : function () {
                return this.dayOfYear();
            },
            d    : function () {
                return this.day();
            },
            dd   : function (format) {
                return this.lang().weekdaysMin(this, format);
            },
            ddd  : function (format) {
                return this.lang().weekdaysShort(this, format);
            },
            dddd : function (format) {
                return this.lang().weekdays(this, format);
            },
            w    : function () {
                return this.week();
            },
            W    : function () {
                return this.isoWeek();
            },
            YY   : function () {
                return leftZeroFill(this.year() % 100, 2);
            },
            YYYY : function () {
                return leftZeroFill(this.year(), 4);
            },
            YYYYY : function () {
                return leftZeroFill(this.year(), 5);
            },
            YYYYYY : function () {
                var y = this.year(), sign = y >= 0 ? '+' : '-';
                return sign + leftZeroFill(Math.abs(y), 6);
            },
            gg   : function () {
                return leftZeroFill(this.weekYear() % 100, 2);
            },
            gggg : function () {
                return leftZeroFill(this.weekYear(), 4);
            },
            ggggg : function () {
                return leftZeroFill(this.weekYear(), 5);
            },
            GG   : function () {
                return leftZeroFill(this.isoWeekYear() % 100, 2);
            },
            GGGG : function () {
                return leftZeroFill(this.isoWeekYear(), 4);
            },
            GGGGG : function () {
                return leftZeroFill(this.isoWeekYear(), 5);
            },
            e : function () {
                return this.weekday();
            },
            E : function () {
                return this.isoWeekday();
            },
            a    : function () {
                return this.lang().meridiem(this.hours(), this.minutes(), true);
            },
            A    : function () {
                return this.lang().meridiem(this.hours(), this.minutes(), false);
            },
            H    : function () {
                return this.hours();
            },
            h    : function () {
                return this.hours() % 12 || 12;
            },
            m    : function () {
                return this.minutes();
            },
            s    : function () {
                return this.seconds();
            },
            S    : function () {
                return toInt(this.milliseconds() / 100);
            },
            SS   : function () {
                return leftZeroFill(toInt(this.milliseconds() / 10), 2);
            },
            SSS  : function () {
                return leftZeroFill(this.milliseconds(), 3);
            },
            SSSS : function () {
                return leftZeroFill(this.milliseconds(), 3);
            },
            Z    : function () {
                var a = -this.zone(),
                    b = "+";
                if (a < 0) {
                    a = -a;
                    b = "-";
                }
                return b + leftZeroFill(toInt(a / 60), 2) + ":" + leftZeroFill(toInt(a) % 60, 2);
            },
            ZZ   : function () {
                var a = -this.zone(),
                    b = "+";
                if (a < 0) {
                    a = -a;
                    b = "-";
                }
                return b + leftZeroFill(toInt(a / 60), 2) + leftZeroFill(toInt(a) % 60, 2);
            },
            z : function () {
                return this.zoneAbbr();
            },
            zz : function () {
                return this.zoneName();
            },
            X    : function () {
                return this.unix();
            },
            Q : function () {
                return this.quarter();
            }
        },

        lists = ['months', 'monthsShort', 'weekdays', 'weekdaysShort', 'weekdaysMin'];

    function defaultParsingFlags() {
        // We need to deep clone this object, and es5 standard is not very
        // helpful.
        return {
            empty : false,
            unusedTokens : [],
            unusedInput : [],
            overflow : -2,
            charsLeftOver : 0,
            nullInput : false,
            invalidMonth : null,
            invalidFormat : false,
            userInvalidated : false,
            iso: false
        };
    }

    function padToken(func, count) {
        return function (a) {
            return leftZeroFill(func.call(this, a), count);
        };
    }
    function ordinalizeToken(func, period) {
        return function (a) {
            return this.lang().ordinal(func.call(this, a), period);
        };
    }

    while (ordinalizeTokens.length) {
        i = ordinalizeTokens.pop();
        formatTokenFunctions[i + 'o'] = ordinalizeToken(formatTokenFunctions[i], i);
    }
    while (paddedTokens.length) {
        i = paddedTokens.pop();
        formatTokenFunctions[i + i] = padToken(formatTokenFunctions[i], 2);
    }
    formatTokenFunctions.DDDD = padToken(formatTokenFunctions.DDD, 3);


    /************************************
        Constructors
    ************************************/

    function Language() {

    }

    // Moment prototype object
    function Moment(config) {
        checkOverflow(config);
        extend(this, config);
    }

    // Duration Constructor
    function Duration(duration) {
        var normalizedInput = normalizeObjectUnits(duration),
            years = normalizedInput.year || 0,
            months = normalizedInput.month || 0,
            weeks = normalizedInput.week || 0,
            days = normalizedInput.day || 0,
            hours = normalizedInput.hour || 0,
            minutes = normalizedInput.minute || 0,
            seconds = normalizedInput.second || 0,
            milliseconds = normalizedInput.millisecond || 0;

        // representation for dateAddRemove
        this._milliseconds = +milliseconds +
            seconds * 1e3 + // 1000
            minutes * 6e4 + // 1000 * 60
            hours * 36e5; // 1000 * 60 * 60
        // Because of dateAddRemove treats 24 hours as different from a
        // day when working around DST, we need to store them separately
        this._days = +days +
            weeks * 7;
        // It is impossible translate months into days without knowing
        // which months you are are talking about, so we have to store
        // it separately.
        this._months = +months +
            years * 12;

        this._data = {};

        this._bubble();
    }

    /************************************
        Helpers
    ************************************/


    function extend(a, b) {
        for (var i in b) {
            if (b.hasOwnProperty(i)) {
                a[i] = b[i];
            }
        }

        if (b.hasOwnProperty("toString")) {
            a.toString = b.toString;
        }

        if (b.hasOwnProperty("valueOf")) {
            a.valueOf = b.valueOf;
        }

        return a;
    }

    function cloneMoment(m) {
        var result = {}, i;
        for (i in m) {
            if (m.hasOwnProperty(i) && momentProperties.hasOwnProperty(i)) {
                result[i] = m[i];
            }
        }

        return result;
    }

    function absRound(number) {
        if (number < 0) {
            return Math.ceil(number);
        } else {
            return Math.floor(number);
        }
    }

    // left zero fill a number
    // see http://jsperf.com/left-zero-filling for performance comparison
    function leftZeroFill(number, targetLength, forceSign) {
        var output = '' + Math.abs(number),
            sign = number >= 0;

        while (output.length < targetLength) {
            output = '0' + output;
        }
        return (sign ? (forceSign ? '+' : '') : '-') + output;
    }

    // helper function for _.addTime and _.subtractTime
    function addOrSubtractDurationFromMoment(mom, duration, isAdding, ignoreUpdateOffset) {
        var milliseconds = duration._milliseconds,
            days = duration._days,
            months = duration._months,
            minutes,
            hours;

        if (milliseconds) {
            mom._d.setTime(+mom._d + milliseconds * isAdding);
        }
        // store the minutes and hours so we can restore them
        if (days || months) {
            minutes = mom.minute();
            hours = mom.hour();
        }
        if (days) {
            mom.date(mom.date() + days * isAdding);
        }
        if (months) {
            mom.month(mom.month() + months * isAdding);
        }
        if (milliseconds && !ignoreUpdateOffset) {
            moment.updateOffset(mom);
        }
        // restore the minutes and hours after possibly changing dst
        if (days || months) {
            mom.minute(minutes);
            mom.hour(hours);
        }
    }

    // check if is an array
    function isArray(input) {
        return Object.prototype.toString.call(input) === '[object Array]';
    }

    function isDate(input) {
        return  Object.prototype.toString.call(input) === '[object Date]' ||
                input instanceof Date;
    }

    // compare two arrays, return the number of differences
    function compareArrays(array1, array2, dontConvert) {
        var len = Math.min(array1.length, array2.length),
            lengthDiff = Math.abs(array1.length - array2.length),
            diffs = 0,
            i;
        for (i = 0; i < len; i++) {
            if ((dontConvert && array1[i] !== array2[i]) ||
                (!dontConvert && toInt(array1[i]) !== toInt(array2[i]))) {
                diffs++;
            }
        }
        return diffs + lengthDiff;
    }

    function normalizeUnits(units) {
        if (units) {
            var lowered = units.toLowerCase().replace(/(.)s$/, '$1');
            units = unitAliases[units] || camelFunctions[lowered] || lowered;
        }
        return units;
    }

    function normalizeObjectUnits(inputObject) {
        var normalizedInput = {},
            normalizedProp,
            prop;

        for (prop in inputObject) {
            if (inputObject.hasOwnProperty(prop)) {
                normalizedProp = normalizeUnits(prop);
                if (normalizedProp) {
                    normalizedInput[normalizedProp] = inputObject[prop];
                }
            }
        }

        return normalizedInput;
    }

    function makeList(field) {
        var count, setter;

        if (field.indexOf('week') === 0) {
            count = 7;
            setter = 'day';
        }
        else if (field.indexOf('month') === 0) {
            count = 12;
            setter = 'month';
        }
        else {
            return;
        }

        moment[field] = function (format, index) {
            var i, getter,
                method = moment.fn._lang[field],
                results = [];

            if (typeof format === 'number') {
                index = format;
                format = undefined;
            }

            getter = function (i) {
                var m = moment().utc().set(setter, i);
                return method.call(moment.fn._lang, m, format || '');
            };

            if (index != null) {
                return getter(index);
            }
            else {
                for (i = 0; i < count; i++) {
                    results.push(getter(i));
                }
                return results;
            }
        };
    }

    function toInt(argumentForCoercion) {
        var coercedNumber = +argumentForCoercion,
            value = 0;

        if (coercedNumber !== 0 && isFinite(coercedNumber)) {
            if (coercedNumber >= 0) {
                value = Math.floor(coercedNumber);
            } else {
                value = Math.ceil(coercedNumber);
            }
        }

        return value;
    }

    function daysInMonth(year, month) {
        return new Date(Date.UTC(year, month + 1, 0)).getUTCDate();
    }

    function daysInYear(year) {
        return isLeapYear(year) ? 366 : 365;
    }

    function isLeapYear(year) {
        return (year % 4 === 0 && year % 100 !== 0) || year % 400 === 0;
    }

    function checkOverflow(m) {
        var overflow;
        if (m._a && m._pf.overflow === -2) {
            overflow =
                m._a[MONTH] < 0 || m._a[MONTH] > 11 ? MONTH :
                m._a[DATE] < 1 || m._a[DATE] > daysInMonth(m._a[YEAR], m._a[MONTH]) ? DATE :
                m._a[HOUR] < 0 || m._a[HOUR] > 23 ? HOUR :
                m._a[MINUTE] < 0 || m._a[MINUTE] > 59 ? MINUTE :
                m._a[SECOND] < 0 || m._a[SECOND] > 59 ? SECOND :
                m._a[MILLISECOND] < 0 || m._a[MILLISECOND] > 999 ? MILLISECOND :
                -1;

            if (m._pf._overflowDayOfYear && (overflow < YEAR || overflow > DATE)) {
                overflow = DATE;
            }

            m._pf.overflow = overflow;
        }
    }

    function isValid(m) {
        if (m._isValid == null) {
            m._isValid = !isNaN(m._d.getTime()) &&
                m._pf.overflow < 0 &&
                !m._pf.empty &&
                !m._pf.invalidMonth &&
                !m._pf.nullInput &&
                !m._pf.invalidFormat &&
                !m._pf.userInvalidated;

            if (m._strict) {
                m._isValid = m._isValid &&
                    m._pf.charsLeftOver === 0 &&
                    m._pf.unusedTokens.length === 0;
            }
        }
        return m._isValid;
    }

    function normalizeLanguage(key) {
        return key ? key.toLowerCase().replace('_', '-') : key;
    }

    // Return a moment from input, that is local/utc/zone equivalent to model.
    function makeAs(input, model) {
        return model._isUTC ? moment(input).zone(model._offset || 0) :
            moment(input).local();
    }

    /************************************
        Languages
    ************************************/


    extend(Language.prototype, {

        set : function (config) {
            var prop, i;
            for (i in config) {
                prop = config[i];
                if (typeof prop === 'function') {
                    this[i] = prop;
                } else {
                    this['_' + i] = prop;
                }
            }
        },

        _months : "January_February_March_April_May_June_July_August_September_October_November_December".split("_"),
        months : function (m) {
            return this._months[m.month()];
        },

        _monthsShort : "Jan_Feb_Mar_Apr_May_Jun_Jul_Aug_Sep_Oct_Nov_Dec".split("_"),
        monthsShort : function (m) {
            return this._monthsShort[m.month()];
        },

        monthsParse : function (monthName) {
            var i, mom, regex;

            if (!this._monthsParse) {
                this._monthsParse = [];
            }

            for (i = 0; i < 12; i++) {
                // make the regex if we don't have it already
                if (!this._monthsParse[i]) {
                    mom = moment.utc([2000, i]);
                    regex = '^' + this.months(mom, '') + '|^' + this.monthsShort(mom, '');
                    this._monthsParse[i] = new RegExp(regex.replace('.', ''), 'i');
                }
                // test the regex
                if (this._monthsParse[i].test(monthName)) {
                    return i;
                }
            }
        },

        _weekdays : "Sunday_Monday_Tuesday_Wednesday_Thursday_Friday_Saturday".split("_"),
        weekdays : function (m) {
            return this._weekdays[m.day()];
        },

        _weekdaysShort : "Sun_Mon_Tue_Wed_Thu_Fri_Sat".split("_"),
        weekdaysShort : function (m) {
            return this._weekdaysShort[m.day()];
        },

        _weekdaysMin : "Su_Mo_Tu_We_Th_Fr_Sa".split("_"),
        weekdaysMin : function (m) {
            return this._weekdaysMin[m.day()];
        },

        weekdaysParse : function (weekdayName) {
            var i, mom, regex;

            if (!this._weekdaysParse) {
                this._weekdaysParse = [];
            }

            for (i = 0; i < 7; i++) {
                // make the regex if we don't have it already
                if (!this._weekdaysParse[i]) {
                    mom = moment([2000, 1]).day(i);
                    regex = '^' + this.weekdays(mom, '') + '|^' + this.weekdaysShort(mom, '') + '|^' + this.weekdaysMin(mom, '');
                    this._weekdaysParse[i] = new RegExp(regex.replace('.', ''), 'i');
                }
                // test the regex
                if (this._weekdaysParse[i].test(weekdayName)) {
                    return i;
                }
            }
        },

        _longDateFormat : {
            LT : "h:mm A",
            L : "MM/DD/YYYY",
            LL : "MMMM D YYYY",
            LLL : "MMMM D YYYY LT",
            LLLL : "dddd, MMMM D YYYY LT"
        },
        longDateFormat : function (key) {
            var output = this._longDateFormat[key];
            if (!output && this._longDateFormat[key.toUpperCase()]) {
                output = this._longDateFormat[key.toUpperCase()].replace(/MMMM|MM|DD|dddd/g, function (val) {
                    return val.slice(1);
                });
                this._longDateFormat[key] = output;
            }
            return output;
        },

        isPM : function (input) {
            // IE8 Quirks Mode & IE7 Standards Mode do not allow accessing strings like arrays
            // Using charAt should be more compatible.
            return ((input + '').toLowerCase().charAt(0) === 'p');
        },

        _meridiemParse : /[ap]\.?m?\.?/i,
        meridiem : function (hours, minutes, isLower) {
            if (hours > 11) {
                return isLower ? 'pm' : 'PM';
            } else {
                return isLower ? 'am' : 'AM';
            }
        },

        _calendar : {
            sameDay : '[Today at] LT',
            nextDay : '[Tomorrow at] LT',
            nextWeek : 'dddd [at] LT',
            lastDay : '[Yesterday at] LT',
            lastWeek : '[Last] dddd [at] LT',
            sameElse : 'L'
        },
        calendar : function (key, mom) {
            var output = this._calendar[key];
            return typeof output === 'function' ? output.apply(mom) : output;
        },

        _relativeTime : {
            future : "in %s",
            past : "%s ago",
            s : "a few seconds",
            m : "a minute",
            mm : "%d minutes",
            h : "an hour",
            hh : "%d hours",
            d : "a day",
            dd : "%d days",
            M : "a month",
            MM : "%d months",
            y : "a year",
            yy : "%d years"
        },
        relativeTime : function (number, withoutSuffix, string, isFuture) {
            var output = this._relativeTime[string];
            return (typeof output === 'function') ?
                output(number, withoutSuffix, string, isFuture) :
                output.replace(/%d/i, number);
        },
        pastFuture : function (diff, output) {
            var format = this._relativeTime[diff > 0 ? 'future' : 'past'];
            return typeof format === 'function' ? format(output) : format.replace(/%s/i, output);
        },

        ordinal : function (number) {
            return this._ordinal.replace("%d", number);
        },
        _ordinal : "%d",

        preparse : function (string) {
            return string;
        },

        postformat : function (string) {
            return string;
        },

        week : function (mom) {
            return weekOfYear(mom, this._week.dow, this._week.doy).week;
        },

        _week : {
            dow : 0, // Sunday is the first day of the week.
            doy : 6  // The week that contains Jan 1st is the first week of the year.
        },

        _invalidDate: 'Invalid date',
        invalidDate: function () {
            return this._invalidDate;
        }
    });

    // Loads a language definition into the `languages` cache.  The function
    // takes a key and optionally values.  If not in the browser and no values
    // are provided, it will load the language file module.  As a convenience,
    // this function also returns the language values.
    function loadLang(key, values) {
        values.abbr = key;
        if (!languages[key]) {
            languages[key] = new Language();
        }
        languages[key].set(values);
        return languages[key];
    }

    // Remove a language from the `languages` cache. Mostly useful in tests.
    function unloadLang(key) {
        delete languages[key];
    }

    // Determines which language definition to use and returns it.
    //
    // With no parameters, it will return the global language.  If you
    // pass in a language key, such as 'en', it will return the
    // definition for 'en', so long as 'en' has already been loaded using
    // moment.lang.
    function getLangDefinition(key) {
        var i = 0, j, lang, next, split,
            get = function (k) {
                if (!languages[k] && hasModule) {
                    try {
                        require('./lang/' + k);
                    } catch (e) { }
                }
                return languages[k];
            };

        if (!key) {
            return moment.fn._lang;
        }

        if (!isArray(key)) {
            //short-circuit everything else
            lang = get(key);
            if (lang) {
                return lang;
            }
            key = [key];
        }

        //pick the language from the array
        //try ['en-au', 'en-gb'] as 'en-au', 'en-gb', 'en', as in move through the list trying each
        //substring from most specific to least, but move to the next array item if it's a more specific variant than the current root
        while (i < key.length) {
            split = normalizeLanguage(key[i]).split('-');
            j = split.length;
            next = normalizeLanguage(key[i + 1]);
            next = next ? next.split('-') : null;
            while (j > 0) {
                lang = get(split.slice(0, j).join('-'));
                if (lang) {
                    return lang;
                }
                if (next && next.length >= j && compareArrays(split, next, true) >= j - 1) {
                    //the next array item is better than a shallower substring of this one
                    break;
                }
                j--;
            }
            i++;
        }
        return moment.fn._lang;
    }

    /************************************
        Formatting
    ************************************/


    function removeFormattingTokens(input) {
        if (input.match(/\[[\s\S]/)) {
            return input.replace(/^\[|\]$/g, "");
        }
        return input.replace(/\\/g, "");
    }

    function makeFormatFunction(format) {
        var array = format.match(formattingTokens), i, length;

        for (i = 0, length = array.length; i < length; i++) {
            if (formatTokenFunctions[array[i]]) {
                array[i] = formatTokenFunctions[array[i]];
            } else {
                array[i] = removeFormattingTokens(array[i]);
            }
        }

        return function (mom) {
            var output = "";
            for (i = 0; i < length; i++) {
                output += array[i] instanceof Function ? array[i].call(mom, format) : array[i];
            }
            return output;
        };
    }

    // format date using native date object
    function formatMoment(m, format) {

        if (!m.isValid()) {
            return m.lang().invalidDate();
        }

        format = expandFormat(format, m.lang());

        if (!formatFunctions[format]) {
            formatFunctions[format] = makeFormatFunction(format);
        }

        return formatFunctions[format](m);
    }

    function expandFormat(format, lang) {
        var i = 5;

        function replaceLongDateFormatTokens(input) {
            return lang.longDateFormat(input) || input;
        }

        localFormattingTokens.lastIndex = 0;
        while (i >= 0 && localFormattingTokens.test(format)) {
            format = format.replace(localFormattingTokens, replaceLongDateFormatTokens);
            localFormattingTokens.lastIndex = 0;
            i -= 1;
        }

        return format;
    }


    /************************************
        Parsing
    ************************************/


    // get the regex to find the next token
    function getParseRegexForToken(token, config) {
        var a, strict = config._strict;
        switch (token) {
        case 'DDDD':
            return parseTokenThreeDigits;
        case 'YYYY':
        case 'GGGG':
        case 'gggg':
            return strict ? parseTokenFourDigits : parseTokenOneToFourDigits;
        case 'Y':
        case 'G':
        case 'g':
            return parseTokenSignedNumber;
        case 'YYYYYY':
        case 'YYYYY':
        case 'GGGGG':
        case 'ggggg':
            return strict ? parseTokenSixDigits : parseTokenOneToSixDigits;
        case 'S':
            if (strict) { return parseTokenOneDigit; }
            /* falls through */
        case 'SS':
            if (strict) { return parseTokenTwoDigits; }
            /* falls through */
        case 'SSS':
            if (strict) { return parseTokenThreeDigits; }
            /* falls through */
        case 'DDD':
            return parseTokenOneToThreeDigits;
        case 'MMM':
        case 'MMMM':
        case 'dd':
        case 'ddd':
        case 'dddd':
            return parseTokenWord;
        case 'a':
        case 'A':
            return getLangDefinition(config._l)._meridiemParse;
        case 'X':
            return parseTokenTimestampMs;
        case 'Z':
        case 'ZZ':
            return parseTokenTimezone;
        case 'T':
            return parseTokenT;
        case 'SSSS':
            return parseTokenDigits;
        case 'MM':
        case 'DD':
        case 'YY':
        case 'GG':
        case 'gg':
        case 'HH':
        case 'hh':
        case 'mm':
        case 'ss':
        case 'ww':
        case 'WW':
            return strict ? parseTokenTwoDigits : parseTokenOneOrTwoDigits;
        case 'M':
        case 'D':
        case 'd':
        case 'H':
        case 'h':
        case 'm':
        case 's':
        case 'w':
        case 'W':
        case 'e':
        case 'E':
            return parseTokenOneOrTwoDigits;
        default :
            a = new RegExp(regexpEscape(unescapeFormat(token.replace('\\', '')), "i"));
            return a;
        }
    }

    function timezoneMinutesFromString(string) {
        string = string || "";
        var possibleTzMatches = (string.match(parseTokenTimezone) || []),
            tzChunk = possibleTzMatches[possibleTzMatches.length - 1] || [],
            parts = (tzChunk + '').match(parseTimezoneChunker) || ['-', 0, 0],
            minutes = +(parts[1] * 60) + toInt(parts[2]);

        return parts[0] === '+' ? -minutes : minutes;
    }

    // function to convert string input to date
    function addTimeToArrayFromToken(token, input, config) {
        var a, datePartArray = config._a;

        switch (token) {
        // MONTH
        case 'M' : // fall through to MM
        case 'MM' :
            if (input != null) {
                datePartArray[MONTH] = toInt(input) - 1;
            }
            break;
        case 'MMM' : // fall through to MMMM
        case 'MMMM' :
            a = getLangDefinition(config._l).monthsParse(input);
            // if we didn't find a month name, mark the date as invalid.
            if (a != null) {
                datePartArray[MONTH] = a;
            } else {
                config._pf.invalidMonth = input;
            }
            break;
        // DAY OF MONTH
        case 'D' : // fall through to DD
        case 'DD' :
            if (input != null) {
                datePartArray[DATE] = toInt(input);
            }
            break;
        // DAY OF YEAR
        case 'DDD' : // fall through to DDDD
        case 'DDDD' :
            if (input != null) {
                config._dayOfYear = toInt(input);
            }

            break;
        // YEAR
        case 'YY' :
            datePartArray[YEAR] = toInt(input) + (toInt(input) > 68 ? 1900 : 2000);
            break;
        case 'YYYY' :
        case 'YYYYY' :
        case 'YYYYYY' :
            datePartArray[YEAR] = toInt(input);
            break;
        // AM / PM
        case 'a' : // fall through to A
        case 'A' :
            config._isPm = getLangDefinition(config._l).isPM(input);
            break;
        // 24 HOUR
        case 'H' : // fall through to hh
        case 'HH' : // fall through to hh
        case 'h' : // fall through to hh
        case 'hh' :
            datePartArray[HOUR] = toInt(input);
            break;
        // MINUTE
        case 'm' : // fall through to mm
        case 'mm' :
            datePartArray[MINUTE] = toInt(input);
            break;
        // SECOND
        case 's' : // fall through to ss
        case 'ss' :
            datePartArray[SECOND] = toInt(input);
            break;
        // MILLISECOND
        case 'S' :
        case 'SS' :
        case 'SSS' :
        case 'SSSS' :
            datePartArray[MILLISECOND] = toInt(('0.' + input) * 1000);
            break;
        // UNIX TIMESTAMP WITH MS
        case 'X':
            config._d = new Date(parseFloat(input) * 1000);
            break;
        // TIMEZONE
        case 'Z' : // fall through to ZZ
        case 'ZZ' :
            config._useUTC = true;
            config._tzm = timezoneMinutesFromString(input);
            break;
        case 'w':
        case 'ww':
        case 'W':
        case 'WW':
        case 'd':
        case 'dd':
        case 'ddd':
        case 'dddd':
        case 'e':
        case 'E':
            token = token.substr(0, 1);
            /* falls through */
        case 'gg':
        case 'gggg':
        case 'GG':
        case 'GGGG':
        case 'GGGGG':
            token = token.substr(0, 2);
            if (input) {
                config._w = config._w || {};
                config._w[token] = input;
            }
            break;
        }
    }

    // convert an array to a date.
    // the array should mirror the parameters below
    // note: all values past the year are optional and will default to the lowest possible value.
    // [year, month, day , hour, minute, second, millisecond]
    function dateFromConfig(config) {
        var i, date, input = [], currentDate,
            yearToUse, fixYear, w, temp, lang, weekday, week;

        if (config._d) {
            return;
        }

        currentDate = currentDateArray(config);

        //compute day of the year from weeks and weekdays
        if (config._w && config._a[DATE] == null && config._a[MONTH] == null) {
            fixYear = function (val) {
                var int_val = parseInt(val, 10);
                return val ?
                  (val.length < 3 ? (int_val > 68 ? 1900 + int_val : 2000 + int_val) : int_val) :
                  (config._a[YEAR] == null ? moment().weekYear() : config._a[YEAR]);
            };

            w = config._w;
            if (w.GG != null || w.W != null || w.E != null) {
                temp = dayOfYearFromWeeks(fixYear(w.GG), w.W || 1, w.E, 4, 1);
            }
            else {
                lang = getLangDefinition(config._l);
                weekday = w.d != null ?  parseWeekday(w.d, lang) :
                  (w.e != null ?  parseInt(w.e, 10) + lang._week.dow : 0);

                week = parseInt(w.w, 10) || 1;

                //if we're parsing 'd', then the low day numbers may be next week
                if (w.d != null && weekday < lang._week.dow) {
                    week++;
                }

                temp = dayOfYearFromWeeks(fixYear(w.gg), week, weekday, lang._week.doy, lang._week.dow);
            }

            config._a[YEAR] = temp.year;
            config._dayOfYear = temp.dayOfYear;
        }

        //if the day of the year is set, figure out what it is
        if (config._dayOfYear) {
            yearToUse = config._a[YEAR] == null ? currentDate[YEAR] : config._a[YEAR];

            if (config._dayOfYear > daysInYear(yearToUse)) {
                config._pf._overflowDayOfYear = true;
            }

            date = makeUTCDate(yearToUse, 0, config._dayOfYear);
            config._a[MONTH] = date.getUTCMonth();
            config._a[DATE] = date.getUTCDate();
        }

        // Default to current date.
        // * if no year, month, day of month are given, default to today
        // * if day of month is given, default month and year
        // * if month is given, default only year
        // * if year is given, don't default anything
        for (i = 0; i < 3 && config._a[i] == null; ++i) {
            config._a[i] = input[i] = currentDate[i];
        }

        // Zero out whatever was not defaulted, including time
        for (; i < 7; i++) {
            config._a[i] = input[i] = (config._a[i] == null) ? (i === 2 ? 1 : 0) : config._a[i];
        }

        // add the offsets to the time to be parsed so that we can have a clean array for checking isValid
        input[HOUR] += toInt((config._tzm || 0) / 60);
        input[MINUTE] += toInt((config._tzm || 0) % 60);

        config._d = (config._useUTC ? makeUTCDate : makeDate).apply(null, input);
    }

    function dateFromObject(config) {
        var normalizedInput;

        if (config._d) {
            return;
        }

        normalizedInput = normalizeObjectUnits(config._i);
        config._a = [
            normalizedInput.year,
            normalizedInput.month,
            normalizedInput.day,
            normalizedInput.hour,
            normalizedInput.minute,
            normalizedInput.second,
            normalizedInput.millisecond
        ];

        dateFromConfig(config);
    }

    function currentDateArray(config) {
        var now = new Date();
        if (config._useUTC) {
            return [
                now.getUTCFullYear(),
                now.getUTCMonth(),
                now.getUTCDate()
            ];
        } else {
            return [now.getFullYear(), now.getMonth(), now.getDate()];
        }
    }

    // date from string and format string
    function makeDateFromStringAndFormat(config) {

        config._a = [];
        config._pf.empty = true;

        // This array is used to make a Date, either with `new Date` or `Date.UTC`
        var lang = getLangDefinition(config._l),
            string = '' + config._i,
            i, parsedInput, tokens, token, skipped,
            stringLength = string.length,
            totalParsedInputLength = 0;

        tokens = expandFormat(config._f, lang).match(formattingTokens) || [];

        for (i = 0; i < tokens.length; i++) {
            token = tokens[i];
            parsedInput = (string.match(getParseRegexForToken(token, config)) || [])[0];
            if (parsedInput) {
                skipped = string.substr(0, string.indexOf(parsedInput));
                if (skipped.length > 0) {
                    config._pf.unusedInput.push(skipped);
                }
                string = string.slice(string.indexOf(parsedInput) + parsedInput.length);
                totalParsedInputLength += parsedInput.length;
            }
            // don't parse if it's not a known token
            if (formatTokenFunctions[token]) {
                if (parsedInput) {
                    config._pf.empty = false;
                }
                else {
                    config._pf.unusedTokens.push(token);
                }
                addTimeToArrayFromToken(token, parsedInput, config);
            }
            else if (config._strict && !parsedInput) {
                config._pf.unusedTokens.push(token);
            }
        }

        // add remaining unparsed input length to the string
        config._pf.charsLeftOver = stringLength - totalParsedInputLength;
        if (string.length > 0) {
            config._pf.unusedInput.push(string);
        }

        // handle am pm
        if (config._isPm && config._a[HOUR] < 12) {
            config._a[HOUR] += 12;
        }
        // if is 12 am, change hours to 0
        if (config._isPm === false && config._a[HOUR] === 12) {
            config._a[HOUR] = 0;
        }

        dateFromConfig(config);
        checkOverflow(config);
    }

    function unescapeFormat(s) {
        return s.replace(/\\(\[)|\\(\])|\[([^\]\[]*)\]|\\(.)/g, function (matched, p1, p2, p3, p4) {
            return p1 || p2 || p3 || p4;
        });
    }

    // Code from http://stackoverflow.com/questions/3561493/is-there-a-regexp-escape-function-in-javascript
    function regexpEscape(s) {
        return s.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
    }

    // date from string and array of format strings
    function makeDateFromStringAndArray(config) {
        var tempConfig,
            bestMoment,

            scoreToBeat,
            i,
            currentScore;

        if (config._f.length === 0) {
            config._pf.invalidFormat = true;
            config._d = new Date(NaN);
            return;
        }

        for (i = 0; i < config._f.length; i++) {
            currentScore = 0;
            tempConfig = extend({}, config);
            tempConfig._pf = defaultParsingFlags();
            tempConfig._f = config._f[i];
            makeDateFromStringAndFormat(tempConfig);

            if (!isValid(tempConfig)) {
                continue;
            }

            // if there is any input that was not parsed add a penalty for that format
            currentScore += tempConfig._pf.charsLeftOver;

            //or tokens
            currentScore += tempConfig._pf.unusedTokens.length * 10;

            tempConfig._pf.score = currentScore;

            if (scoreToBeat == null || currentScore < scoreToBeat) {
                scoreToBeat = currentScore;
                bestMoment = tempConfig;
            }
        }

        extend(config, bestMoment || tempConfig);
    }

    // date from iso format
    function makeDateFromString(config) {
        var i, l,
            string = config._i,
            match = isoRegex.exec(string);

        if (match) {
            config._pf.iso = true;
            for (i = 0, l = isoDates.length; i < l; i++) {
                if (isoDates[i][1].exec(string)) {
                    // match[5] should be "T" or undefined
                    config._f = isoDates[i][0] + (match[6] || " ");
                    break;
                }
            }
            for (i = 0, l = isoTimes.length; i < l; i++) {
                if (isoTimes[i][1].exec(string)) {
                    config._f += isoTimes[i][0];
                    break;
                }
            }
            if (string.match(parseTokenTimezone)) {
                config._f += "Z";
            }
            makeDateFromStringAndFormat(config);
        }
        else {
            config._d = new Date(string);
        }
    }

    function makeDateFromInput(config) {
        var input = config._i,
            matched = aspNetJsonRegex.exec(input);

        if (input === undefined) {
            config._d = new Date();
        } else if (matched) {
            config._d = new Date(+matched[1]);
        } else if (typeof input === 'string') {
            makeDateFromString(config);
        } else if (isArray(input)) {
            config._a = input.slice(0);
            dateFromConfig(config);
        } else if (isDate(input)) {
            config._d = new Date(+input);
        } else if (typeof(input) === 'object') {
            dateFromObject(config);
        } else {
            config._d = new Date(input);
        }
    }

    function makeDate(y, m, d, h, M, s, ms) {
        //can't just apply() to create a date:
        //http://stackoverflow.com/questions/181348/instantiating-a-javascript-object-by-calling-prototype-constructor-apply
        var date = new Date(y, m, d, h, M, s, ms);

        //the date constructor doesn't accept years < 1970
        if (y < 1970) {
            date.setFullYear(y);
        }
        return date;
    }

    function makeUTCDate(y) {
        var date = new Date(Date.UTC.apply(null, arguments));
        if (y < 1970) {
            date.setUTCFullYear(y);
        }
        return date;
    }

    function parseWeekday(input, language) {
        if (typeof input === 'string') {
            if (!isNaN(input)) {
                input = parseInt(input, 10);
            }
            else {
                input = language.weekdaysParse(input);
                if (typeof input !== 'number') {
                    return null;
                }
            }
        }
        return input;
    }

    /************************************
        Relative Time
    ************************************/


    // helper function for moment.fn.from, moment.fn.fromNow, and moment.duration.fn.humanize
    function substituteTimeAgo(string, number, withoutSuffix, isFuture, lang) {
        return lang.relativeTime(number || 1, !!withoutSuffix, string, isFuture);
    }

    function relativeTime(milliseconds, withoutSuffix, lang) {
        var seconds = round(Math.abs(milliseconds) / 1000),
            minutes = round(seconds / 60),
            hours = round(minutes / 60),
            days = round(hours / 24),
            years = round(days / 365),
            args = seconds < 45 && ['s', seconds] ||
                minutes === 1 && ['m'] ||
                minutes < 45 && ['mm', minutes] ||
                hours === 1 && ['h'] ||
                hours < 22 && ['hh', hours] ||
                days === 1 && ['d'] ||
                days <= 25 && ['dd', days] ||
                days <= 45 && ['M'] ||
                days < 345 && ['MM', round(days / 30)] ||
                years === 1 && ['y'] || ['yy', years];
        args[2] = withoutSuffix;
        args[3] = milliseconds > 0;
        args[4] = lang;
        return substituteTimeAgo.apply({}, args);
    }


    /************************************
        Week of Year
    ************************************/


    // firstDayOfWeek       0 = sun, 6 = sat
    //                      the day of the week that starts the week
    //                      (usually sunday or monday)
    // firstDayOfWeekOfYear 0 = sun, 6 = sat
    //                      the first week is the week that contains the first
    //                      of this day of the week
    //                      (eg. ISO weeks use thursday (4))
    function weekOfYear(mom, firstDayOfWeek, firstDayOfWeekOfYear) {
        var end = firstDayOfWeekOfYear - firstDayOfWeek,
            daysToDayOfWeek = firstDayOfWeekOfYear - mom.day(),
            adjustedMoment;


        if (daysToDayOfWeek > end) {
            daysToDayOfWeek -= 7;
        }

        if (daysToDayOfWeek < end - 7) {
            daysToDayOfWeek += 7;
        }

        adjustedMoment = moment(mom).add('d', daysToDayOfWeek);
        return {
            week: Math.ceil(adjustedMoment.dayOfYear() / 7),
            year: adjustedMoment.year()
        };
    }

    //http://en.wikipedia.org/wiki/ISO_week_date#Calculating_a_date_given_the_year.2C_week_number_and_weekday
    function dayOfYearFromWeeks(year, week, weekday, firstDayOfWeekOfYear, firstDayOfWeek) {
        var d = makeUTCDate(year, 0, 1).getUTCDay(), daysToAdd, dayOfYear;

        weekday = weekday != null ? weekday : firstDayOfWeek;
        daysToAdd = firstDayOfWeek - d + (d > firstDayOfWeekOfYear ? 7 : 0) - (d < firstDayOfWeek ? 7 : 0);
        dayOfYear = 7 * (week - 1) + (weekday - firstDayOfWeek) + daysToAdd + 1;

        return {
            year: dayOfYear > 0 ? year : year - 1,
            dayOfYear: dayOfYear > 0 ?  dayOfYear : daysInYear(year - 1) + dayOfYear
        };
    }

    /************************************
        Top Level Functions
    ************************************/

    function makeMoment(config) {
        var input = config._i,
            format = config._f;

        if (input === null) {
            return moment.invalid({nullInput: true});
        }

        if (typeof input === 'string') {
            config._i = input = getLangDefinition().preparse(input);
        }

        if (moment.isMoment(input)) {
            config = cloneMoment(input);

            config._d = new Date(+input._d);
        } else if (format) {
            if (isArray(format)) {
                makeDateFromStringAndArray(config);
            } else {
                makeDateFromStringAndFormat(config);
            }
        } else {
            makeDateFromInput(config);
        }

        return new Moment(config);
    }

    moment = function (input, format, lang, strict) {
        var c;

        if (typeof(lang) === "boolean") {
            strict = lang;
            lang = undefined;
        }
        // object construction must be done this way.
        // https://github.com/moment/moment/issues/1423
        c = {};
        c._isAMomentObject = true;
        c._i = input;
        c._f = format;
        c._l = lang;
        c._strict = strict;
        c._isUTC = false;
        c._pf = defaultParsingFlags();

        return makeMoment(c);
    };

    // creating with utc
    moment.utc = function (input, format, lang, strict) {
        var c;

        if (typeof(lang) === "boolean") {
            strict = lang;
            lang = undefined;
        }
        // object construction must be done this way.
        // https://github.com/moment/moment/issues/1423
        c = {};
        c._isAMomentObject = true;
        c._useUTC = true;
        c._isUTC = true;
        c._l = lang;
        c._i = input;
        c._f = format;
        c._strict = strict;
        c._pf = defaultParsingFlags();

        return makeMoment(c).utc();
    };

    // creating with unix timestamp (in seconds)
    moment.unix = function (input) {
        return moment(input * 1000);
    };

    // duration
    moment.duration = function (input, key) {
        var duration = input,
            // matching against regexp is expensive, do it on demand
            match = null,
            sign,
            ret,
            parseIso;

        if (moment.isDuration(input)) {
            duration = {
                ms: input._milliseconds,
                d: input._days,
                M: input._months
            };
        } else if (typeof input === 'number') {
            duration = {};
            if (key) {
                duration[key] = input;
            } else {
                duration.milliseconds = input;
            }
        } else if (!!(match = aspNetTimeSpanJsonRegex.exec(input))) {
            sign = (match[1] === "-") ? -1 : 1;
            duration = {
                y: 0,
                d: toInt(match[DATE]) * sign,
                h: toInt(match[HOUR]) * sign,
                m: toInt(match[MINUTE]) * sign,
                s: toInt(match[SECOND]) * sign,
                ms: toInt(match[MILLISECOND]) * sign
            };
        } else if (!!(match = isoDurationRegex.exec(input))) {
            sign = (match[1] === "-") ? -1 : 1;
            parseIso = function (inp) {
                // We'd normally use ~~inp for this, but unfortunately it also
                // converts floats to ints.
                // inp may be undefined, so careful calling replace on it.
                var res = inp && parseFloat(inp.replace(',', '.'));
                // apply sign while we're at it
                return (isNaN(res) ? 0 : res) * sign;
            };
            duration = {
                y: parseIso(match[2]),
                M: parseIso(match[3]),
                d: parseIso(match[4]),
                h: parseIso(match[5]),
                m: parseIso(match[6]),
                s: parseIso(match[7]),
                w: parseIso(match[8])
            };
        }

        ret = new Duration(duration);

        if (moment.isDuration(input) && input.hasOwnProperty('_lang')) {
            ret._lang = input._lang;
        }

        return ret;
    };

    // version number
    moment.version = VERSION;

    // default format
    moment.defaultFormat = isoFormat;

    // This function will be called whenever a moment is mutated.
    // It is intended to keep the offset in sync with the timezone.
    moment.updateOffset = function () {};

    // This function will load languages and then set the global language.  If
    // no arguments are passed in, it will simply return the current global
    // language key.
    moment.lang = function (key, values) {
        var r;
        if (!key) {
            return moment.fn._lang._abbr;
        }
        if (values) {
            loadLang(normalizeLanguage(key), values);
        } else if (values === null) {
            unloadLang(key);
            key = 'en';
        } else if (!languages[key]) {
            getLangDefinition(key);
        }
        r = moment.duration.fn._lang = moment.fn._lang = getLangDefinition(key);
        return r._abbr;
    };

    // returns language data
    moment.langData = function (key) {
        if (key && key._lang && key._lang._abbr) {
            key = key._lang._abbr;
        }
        return getLangDefinition(key);
    };

    // compare moment object
    moment.isMoment = function (obj) {
        return obj instanceof Moment ||
            (obj != null &&  obj.hasOwnProperty('_isAMomentObject'));
    };

    // for typechecking Duration objects
    moment.isDuration = function (obj) {
        return obj instanceof Duration;
    };

    for (i = lists.length - 1; i >= 0; --i) {
        makeList(lists[i]);
    }

    moment.normalizeUnits = function (units) {
        return normalizeUnits(units);
    };

    moment.invalid = function (flags) {
        var m = moment.utc(NaN);
        if (flags != null) {
            extend(m._pf, flags);
        }
        else {
            m._pf.userInvalidated = true;
        }

        return m;
    };

    moment.parseZone = function (input) {
        return moment(input).parseZone();
    };

    /************************************
        Moment Prototype
    ************************************/


    extend(moment.fn = Moment.prototype, {

        clone : function () {
            return moment(this);
        },

        valueOf : function () {
            return +this._d + ((this._offset || 0) * 60000);
        },

        unix : function () {
            return Math.floor(+this / 1000);
        },

        toString : function () {
            return this.clone().lang('en').format("ddd MMM DD YYYY HH:mm:ss [GMT]ZZ");
        },

        toDate : function () {
            return this._offset ? new Date(+this) : this._d;
        },

        toISOString : function () {
            var m = moment(this).utc();
            if (0 < m.year() && m.year() <= 9999) {
                return formatMoment(m, 'YYYY-MM-DD[T]HH:mm:ss.SSS[Z]');
            } else {
                return formatMoment(m, 'YYYYYY-MM-DD[T]HH:mm:ss.SSS[Z]');
            }
        },

        toArray : function () {
            var m = this;
            return [
                m.year(),
                m.month(),
                m.date(),
                m.hours(),
                m.minutes(),
                m.seconds(),
                m.milliseconds()
            ];
        },

        isValid : function () {
            return isValid(this);
        },

        isDSTShifted : function () {

            if (this._a) {
                return this.isValid() && compareArrays(this._a, (this._isUTC ? moment.utc(this._a) : moment(this._a)).toArray()) > 0;
            }

            return false;
        },

        parsingFlags : function () {
            return extend({}, this._pf);
        },

        invalidAt: function () {
            return this._pf.overflow;
        },

        utc : function () {
            return this.zone(0);
        },

        local : function () {
            this.zone(0);
            this._isUTC = false;
            return this;
        },

        format : function (inputString) {
            var output = formatMoment(this, inputString || moment.defaultFormat);
            return this.lang().postformat(output);
        },

        add : function (input, val) {
            var dur;
            // switch args to support add('s', 1) and add(1, 's')
            if (typeof input === 'string') {
                dur = moment.duration(+val, input);
            } else {
                dur = moment.duration(input, val);
            }
            addOrSubtractDurationFromMoment(this, dur, 1);
            return this;
        },

        subtract : function (input, val) {
            var dur;
            // switch args to support subtract('s', 1) and subtract(1, 's')
            if (typeof input === 'string') {
                dur = moment.duration(+val, input);
            } else {
                dur = moment.duration(input, val);
            }
            addOrSubtractDurationFromMoment(this, dur, -1);
            return this;
        },

        diff : function (input, units, asFloat) {
            var that = makeAs(input, this),
                zoneDiff = (this.zone() - that.zone()) * 6e4,
                diff, output;

            units = normalizeUnits(units);

            if (units === 'year' || units === 'month') {
                // average number of days in the months in the given dates
                diff = (this.daysInMonth() + that.daysInMonth()) * 432e5; // 24 * 60 * 60 * 1000 / 2
                // difference in months
                output = ((this.year() - that.year()) * 12) + (this.month() - that.month());
                // adjust by taking difference in days, average number of days
                // and dst in the given months.
                output += ((this - moment(this).startOf('month')) -
                        (that - moment(that).startOf('month'))) / diff;
                // same as above but with zones, to negate all dst
                output -= ((this.zone() - moment(this).startOf('month').zone()) -
                        (that.zone() - moment(that).startOf('month').zone())) * 6e4 / diff;
                if (units === 'year') {
                    output = output / 12;
                }
            } else {
                diff = (this - that);
                output = units === 'second' ? diff / 1e3 : // 1000
                    units === 'minute' ? diff / 6e4 : // 1000 * 60
                    units === 'hour' ? diff / 36e5 : // 1000 * 60 * 60
                    units === 'day' ? (diff - zoneDiff) / 864e5 : // 1000 * 60 * 60 * 24, negate dst
                    units === 'week' ? (diff - zoneDiff) / 6048e5 : // 1000 * 60 * 60 * 24 * 7, negate dst
                    diff;
            }
            return asFloat ? output : absRound(output);
        },

        from : function (time, withoutSuffix) {
            return moment.duration(this.diff(time)).lang(this.lang()._abbr).humanize(!withoutSuffix);
        },

        fromNow : function (withoutSuffix) {
            return this.from(moment(), withoutSuffix);
        },

        calendar : function () {
            // We want to compare the start of today, vs this.
            // Getting start-of-today depends on whether we're zone'd or not.
            var sod = makeAs(moment(), this).startOf('day'),
                diff = this.diff(sod, 'days', true),
                format = diff < -6 ? 'sameElse' :
                    diff < -1 ? 'lastWeek' :
                    diff < 0 ? 'lastDay' :
                    diff < 1 ? 'sameDay' :
                    diff < 2 ? 'nextDay' :
                    diff < 7 ? 'nextWeek' : 'sameElse';
            return this.format(this.lang().calendar(format, this));
        },

        isLeapYear : function () {
            return isLeapYear(this.year());
        },

        isDST : function () {
            return (this.zone() < this.clone().month(0).zone() ||
                this.zone() < this.clone().month(5).zone());
        },

        day : function (input) {
            var day = this._isUTC ? this._d.getUTCDay() : this._d.getDay();
            if (input != null) {
                input = parseWeekday(input, this.lang());
                return this.add({ d : input - day });
            } else {
                return day;
            }
        },

        month : function (input) {
            var utc = this._isUTC ? 'UTC' : '',
                dayOfMonth;

            if (input != null) {
                if (typeof input === 'string') {
                    input = this.lang().monthsParse(input);
                    if (typeof input !== 'number') {
                        return this;
                    }
                }

                dayOfMonth = this.date();
                this.date(1);
                this._d['set' + utc + 'Month'](input);
                this.date(Math.min(dayOfMonth, this.daysInMonth()));

                moment.updateOffset(this);
                return this;
            } else {
                return this._d['get' + utc + 'Month']();
            }
        },

        startOf: function (units) {
            units = normalizeUnits(units);
            // the following switch intentionally omits break keywords
            // to utilize falling through the cases.
            switch (units) {
            case 'year':
                this.month(0);
                /* falls through */
            case 'month':
                this.date(1);
                /* falls through */
            case 'week':
            case 'isoWeek':
            case 'day':
                this.hours(0);
                /* falls through */
            case 'hour':
                this.minutes(0);
                /* falls through */
            case 'minute':
                this.seconds(0);
                /* falls through */
            case 'second':
                this.milliseconds(0);
                /* falls through */
            }

            // weeks are a special case
            if (units === 'week') {
                this.weekday(0);
            } else if (units === 'isoWeek') {
                this.isoWeekday(1);
            }

            return this;
        },

        endOf: function (units) {
            units = normalizeUnits(units);
            return this.startOf(units).add((units === 'isoWeek' ? 'week' : units), 1).subtract('ms', 1);
        },

        isAfter: function (input, units) {
            units = typeof units !== 'undefined' ? units : 'millisecond';
            return +this.clone().startOf(units) > +moment(input).startOf(units);
        },

        isBefore: function (input, units) {
            units = typeof units !== 'undefined' ? units : 'millisecond';
            return +this.clone().startOf(units) < +moment(input).startOf(units);
        },

        isSame: function (input, units) {
            units = units || 'ms';
            return +this.clone().startOf(units) === +makeAs(input, this).startOf(units);
        },

        min: function (other) {
            other = moment.apply(null, arguments);
            return other < this ? this : other;
        },

        max: function (other) {
            other = moment.apply(null, arguments);
            return other > this ? this : other;
        },

        zone : function (input) {
            var offset = this._offset || 0;
            if (input != null) {
                if (typeof input === "string") {
                    input = timezoneMinutesFromString(input);
                }
                if (Math.abs(input) < 16) {
                    input = input * 60;
                }
                this._offset = input;
                this._isUTC = true;
                if (offset !== input) {
                    addOrSubtractDurationFromMoment(this, moment.duration(offset - input, 'm'), 1, true);
                }
            } else {
                return this._isUTC ? offset : this._d.getTimezoneOffset();
            }
            return this;
        },

        zoneAbbr : function () {
            return this._isUTC ? "UTC" : "";
        },

        zoneName : function () {
            return this._isUTC ? "Coordinated Universal Time" : "";
        },

        parseZone : function () {
            if (this._tzm) {
                this.zone(this._tzm);
            } else if (typeof this._i === 'string') {
                this.zone(this._i);
            }
            return this;
        },

        hasAlignedHourOffset : function (input) {
            if (!input) {
                input = 0;
            }
            else {
                input = moment(input).zone();
            }

            return (this.zone() - input) % 60 === 0;
        },

        daysInMonth : function () {
            return daysInMonth(this.year(), this.month());
        },

        dayOfYear : function (input) {
            var dayOfYear = round((moment(this).startOf('day') - moment(this).startOf('year')) / 864e5) + 1;
            return input == null ? dayOfYear : this.add("d", (input - dayOfYear));
        },

        quarter : function () {
            return Math.ceil((this.month() + 1.0) / 3.0);
        },

        weekYear : function (input) {
            var year = weekOfYear(this, this.lang()._week.dow, this.lang()._week.doy).year;
            return input == null ? year : this.add("y", (input - year));
        },

        isoWeekYear : function (input) {
            var year = weekOfYear(this, 1, 4).year;
            return input == null ? year : this.add("y", (input - year));
        },

        week : function (input) {
            var week = this.lang().week(this);
            return input == null ? week : this.add("d", (input - week) * 7);
        },

        isoWeek : function (input) {
            var week = weekOfYear(this, 1, 4).week;
            return input == null ? week : this.add("d", (input - week) * 7);
        },

        weekday : function (input) {
            var weekday = (this.day() + 7 - this.lang()._week.dow) % 7;
            return input == null ? weekday : this.add("d", input - weekday);
        },

        isoWeekday : function (input) {
            // behaves the same as moment#day except
            // as a getter, returns 7 instead of 0 (1-7 range instead of 0-6)
            // as a setter, sunday should belong to the previous week.
            return input == null ? this.day() || 7 : this.day(this.day() % 7 ? input : input - 7);
        },

        get : function (units) {
            units = normalizeUnits(units);
            return this[units]();
        },

        set : function (units, value) {
            units = normalizeUnits(units);
            if (typeof this[units] === 'function') {
                this[units](value);
            }
            return this;
        },

        // If passed a language key, it will set the language for this
        // instance.  Otherwise, it will return the language configuration
        // variables for this instance.
        lang : function (key) {
            if (key === undefined) {
                return this._lang;
            } else {
                this._lang = getLangDefinition(key);
                return this;
            }
        }
    });

    // helper for adding shortcuts
    function makeGetterAndSetter(name, key) {
        moment.fn[name] = moment.fn[name + 's'] = function (input) {
            var utc = this._isUTC ? 'UTC' : '';
            if (input != null) {
                this._d['set' + utc + key](input);
                moment.updateOffset(this);
                return this;
            } else {
                return this._d['get' + utc + key]();
            }
        };
    }

    // loop through and add shortcuts (Month, Date, Hours, Minutes, Seconds, Milliseconds)
    for (i = 0; i < proxyGettersAndSetters.length; i ++) {
        makeGetterAndSetter(proxyGettersAndSetters[i].toLowerCase().replace(/s$/, ''), proxyGettersAndSetters[i]);
    }

    // add shortcut for year (uses different syntax than the getter/setter 'year' == 'FullYear')
    makeGetterAndSetter('year', 'FullYear');

    // add plural methods
    moment.fn.days = moment.fn.day;
    moment.fn.months = moment.fn.month;
    moment.fn.weeks = moment.fn.week;
    moment.fn.isoWeeks = moment.fn.isoWeek;

    // add aliased format methods
    moment.fn.toJSON = moment.fn.toISOString;

    /************************************
        Duration Prototype
    ************************************/


    extend(moment.duration.fn = Duration.prototype, {

        _bubble : function () {
            var milliseconds = this._milliseconds,
                days = this._days,
                months = this._months,
                data = this._data,
                seconds, minutes, hours, years;

            // The following code bubbles up values, see the tests for
            // examples of what that means.
            data.milliseconds = milliseconds % 1000;

            seconds = absRound(milliseconds / 1000);
            data.seconds = seconds % 60;

            minutes = absRound(seconds / 60);
            data.minutes = minutes % 60;

            hours = absRound(minutes / 60);
            data.hours = hours % 24;

            days += absRound(hours / 24);
            data.days = days % 30;

            months += absRound(days / 30);
            data.months = months % 12;

            years = absRound(months / 12);
            data.years = years;
        },

        weeks : function () {
            return absRound(this.days() / 7);
        },

        valueOf : function () {
            return this._milliseconds +
              this._days * 864e5 +
              (this._months % 12) * 2592e6 +
              toInt(this._months / 12) * 31536e6;
        },

        humanize : function (withSuffix) {
            var difference = +this,
                output = relativeTime(difference, !withSuffix, this.lang());

            if (withSuffix) {
                output = this.lang().pastFuture(difference, output);
            }

            return this.lang().postformat(output);
        },

        add : function (input, val) {
            // supports only 2.0-style add(1, 's') or add(moment)
            var dur = moment.duration(input, val);

            this._milliseconds += dur._milliseconds;
            this._days += dur._days;
            this._months += dur._months;

            this._bubble();

            return this;
        },

        subtract : function (input, val) {
            var dur = moment.duration(input, val);

            this._milliseconds -= dur._milliseconds;
            this._days -= dur._days;
            this._months -= dur._months;

            this._bubble();

            return this;
        },

        get : function (units) {
            units = normalizeUnits(units);
            return this[units.toLowerCase() + 's']();
        },

        as : function (units) {
            units = normalizeUnits(units);
            return this['as' + units.charAt(0).toUpperCase() + units.slice(1) + 's']();
        },

        lang : moment.fn.lang,

        toIsoString : function () {
            // inspired by https://github.com/dordille/moment-isoduration/blob/master/moment.isoduration.js
            var years = Math.abs(this.years()),
                months = Math.abs(this.months()),
                days = Math.abs(this.days()),
                hours = Math.abs(this.hours()),
                minutes = Math.abs(this.minutes()),
                seconds = Math.abs(this.seconds() + this.milliseconds() / 1000);

            if (!this.asSeconds()) {
                // this is the same as C#'s (Noda) and python (isodate)...
                // but not other JS (goog.date)
                return 'P0D';
            }

            return (this.asSeconds() < 0 ? '-' : '') +
                'P' +
                (years ? years + 'Y' : '') +
                (months ? months + 'M' : '') +
                (days ? days + 'D' : '') +
                ((hours || minutes || seconds) ? 'T' : '') +
                (hours ? hours + 'H' : '') +
                (minutes ? minutes + 'M' : '') +
                (seconds ? seconds + 'S' : '');
        }
    });

    function makeDurationGetter(name) {
        moment.duration.fn[name] = function () {
            return this._data[name];
        };
    }

    function makeDurationAsGetter(name, factor) {
        moment.duration.fn['as' + name] = function () {
            return +this / factor;
        };
    }

    for (i in unitMillisecondFactors) {
        if (unitMillisecondFactors.hasOwnProperty(i)) {
            makeDurationAsGetter(i, unitMillisecondFactors[i]);
            makeDurationGetter(i.toLowerCase());
        }
    }

    makeDurationAsGetter('Weeks', 6048e5);
    moment.duration.fn.asMonths = function () {
        return (+this - this.years() * 31536e6) / 2592e6 + this.years() * 12;
    };


    /************************************
        Default Lang
    ************************************/


    // Set default language, other languages will inherit from English.
    moment.lang('en', {
        ordinal : function (number) {
            var b = number % 10,
                output = (toInt(number % 100 / 10) === 1) ? 'th' :
                (b === 1) ? 'st' :
                (b === 2) ? 'nd' :
                (b === 3) ? 'rd' : 'th';
            return number + output;
        }
    });

    // moment.js language configuration
// language : Moroccan Arabic (ar-ma)
// author : ElFadili Yassine : https://github.com/ElFadiliY
// author : Abdel Said : https://github.com/abdelsaid

(function (factory) {
    factory(moment);
}(function (moment) {
    return moment.lang('ar-ma', {
        months : "ЩЉЩ†Ш§ЩЉШ±_ЩЃШЁШ±Ш§ЩЉШ±_Щ…Ш§Ш±Ші_ШЈШЁШ±ЩЉЩ„_Щ…Ш§ЩЉ_ЩЉЩ€Щ†ЩЉЩ€_ЩЉЩ€Щ„ЩЉЩ€ШІ_ШєШґШЄ_ШґШЄЩ†ШЁШ±_ШЈЩѓШЄЩ€ШЁШ±_Щ†Щ€Щ†ШЁШ±_ШЇШ¬Щ†ШЁШ±".split("_"),
        monthsShort : "ЩЉЩ†Ш§ЩЉШ±_ЩЃШЁШ±Ш§ЩЉШ±_Щ…Ш§Ш±Ші_ШЈШЁШ±ЩЉЩ„_Щ…Ш§ЩЉ_ЩЉЩ€Щ†ЩЉЩ€_ЩЉЩ€Щ„ЩЉЩ€ШІ_ШєШґШЄ_ШґШЄЩ†ШЁШ±_ШЈЩѓШЄЩ€ШЁШ±_Щ†Щ€Щ†ШЁШ±_ШЇШ¬Щ†ШЁШ±".split("_"),
        weekdays : "Ш§Щ„ШЈШ­ШЇ_Ш§Щ„ШҐШЄЩ†ЩЉЩ†_Ш§Щ„Ш«Щ„Ш§Ш«Ш§ШЎ_Ш§Щ„ШЈШ±ШЁШ№Ш§ШЎ_Ш§Щ„Ш®Щ…ЩЉШі_Ш§Щ„Ш¬Щ…Ш№Ш©_Ш§Щ„ШіШЁШЄ".split("_"),
        weekdaysShort : "Ш§Ш­ШЇ_Ш§ШЄЩ†ЩЉЩ†_Ш«Щ„Ш§Ш«Ш§ШЎ_Ш§Ш±ШЁШ№Ш§ШЎ_Ш®Щ…ЩЉШі_Ш¬Щ…Ш№Ш©_ШіШЁШЄ".split("_"),
        weekdaysMin : "Ш­_Щ†_Ш«_Ш±_Ш®_Ш¬_Ші".split("_"),
        longDateFormat : {
            LT : "HH:mm",
            L : "DD/MM/YYYY",
            LL : "D MMMM YYYY",
            LLL : "D MMMM YYYY LT",
            LLLL : "dddd D MMMM YYYY LT"
        },
        calendar : {
            sameDay: "[Ш§Щ„ЩЉЩ€Щ… Ш№Щ„Щ‰ Ш§Щ„ШіШ§Ш№Ш©] LT",
            nextDay: '[ШєШЇШ§ Ш№Щ„Щ‰ Ш§Щ„ШіШ§Ш№Ш©] LT',
            nextWeek: 'dddd [Ш№Щ„Щ‰ Ш§Щ„ШіШ§Ш№Ш©] LT',
            lastDay: '[ШЈЩ…Ші Ш№Щ„Щ‰ Ш§Щ„ШіШ§Ш№Ш©] LT',
            lastWeek: 'dddd [Ш№Щ„Щ‰ Ш§Щ„ШіШ§Ш№Ш©] LT',
            sameElse: 'L'
        },
        relativeTime : {
            future : "ЩЃЩЉ %s",
            past : "Щ…Щ†Ш° %s",
            s : "Ш«Щ€Ш§Щ†",
            m : "ШЇЩ‚ЩЉЩ‚Ш©",
            mm : "%d ШЇЩ‚Ш§Ш¦Щ‚",
            h : "ШіШ§Ш№Ш©",
            hh : "%d ШіШ§Ш№Ш§ШЄ",
            d : "ЩЉЩ€Щ…",
            dd : "%d ШЈЩЉШ§Щ…",
            M : "ШґЩ‡Ш±",
            MM : "%d ШЈШґЩ‡Ш±",
            y : "ШіЩ†Ш©",
            yy : "%d ШіЩ†Щ€Ш§ШЄ"
        },
        week : {
            dow : 6, // Saturday is the first day of the week.
            doy : 12  // The week that contains Jan 1st is the first week of the year.
        }
    });
}));
// moment.js language configuration
// language : Arabic (ar)
// author : Abdel Said : https://github.com/abdelsaid
// changes in months, weekdays : Ahmed Elkhatib

(function (factory) {
    factory(moment);
}(function (moment) {
    return moment.lang('ar', {
        months : "ЩЉЩ†Ш§ЩЉШ±/ ЩѓШ§Щ†Щ€Щ† Ш§Щ„Ш«Ш§Щ†ЩЉ_ЩЃШЁШ±Ш§ЩЉШ±/ ШґШЁШ§Ш·_Щ…Ш§Ш±Ші/ ШўШ°Ш§Ш±_ШЈШЁШ±ЩЉЩ„/ Щ†ЩЉШіШ§Щ†_Щ…Ш§ЩЉЩ€/ ШЈЩЉШ§Ш±_ЩЉЩ€Щ†ЩЉЩ€/ Ш­ШІЩЉШ±Ш§Щ†_ЩЉЩ€Щ„ЩЉЩ€/ ШЄЩ…Щ€ШІ_ШЈШєШіШ·Ші/ ШўШЁ_ШіШЁШЄЩ…ШЁШ±/ ШЈЩЉЩ„Щ€Щ„_ШЈЩѓШЄЩ€ШЁШ±/ ШЄШґШ±ЩЉЩ† Ш§Щ„ШЈЩ€Щ„_Щ†Щ€ЩЃЩ…ШЁШ±/ ШЄШґШ±ЩЉЩ† Ш§Щ„Ш«Ш§Щ†ЩЉ_ШЇЩЉШіЩ…ШЁШ±/ ЩѓШ§Щ†Щ€Щ† Ш§Щ„ШЈЩ€Щ„".split("_"),
        monthsShort : "ЩЉЩ†Ш§ЩЉШ±/ ЩѓШ§Щ†Щ€Щ† Ш§Щ„Ш«Ш§Щ†ЩЉ_ЩЃШЁШ±Ш§ЩЉШ±/ ШґШЁШ§Ш·_Щ…Ш§Ш±Ші/ ШўШ°Ш§Ш±_ШЈШЁШ±ЩЉЩ„/ Щ†ЩЉШіШ§Щ†_Щ…Ш§ЩЉЩ€/ ШЈЩЉШ§Ш±_ЩЉЩ€Щ†ЩЉЩ€/ Ш­ШІЩЉШ±Ш§Щ†_ЩЉЩ€Щ„ЩЉЩ€/ ШЄЩ…Щ€ШІ_ШЈШєШіШ·Ші/ ШўШЁ_ШіШЁШЄЩ…ШЁШ±/ ШЈЩЉЩ„Щ€Щ„_ШЈЩѓШЄЩ€ШЁШ±/ ШЄШґШ±ЩЉЩ† Ш§Щ„ШЈЩ€Щ„_Щ†Щ€ЩЃЩ…ШЁШ±/ ШЄШґШ±ЩЉЩ† Ш§Щ„Ш«Ш§Щ†ЩЉ_ШЇЩЉШіЩ…ШЁШ±/ ЩѓШ§Щ†Щ€Щ† Ш§Щ„ШЈЩ€Щ„".split("_"),
        weekdays : "Ш§Щ„ШЈШ­ШЇ_Ш§Щ„ШҐШ«Щ†ЩЉЩ†_Ш§Щ„Ш«Щ„Ш§Ш«Ш§ШЎ_Ш§Щ„ШЈШ±ШЁШ№Ш§ШЎ_Ш§Щ„Ш®Щ…ЩЉШі_Ш§Щ„Ш¬Щ…Ш№Ш©_Ш§Щ„ШіШЁШЄ".split("_"),
        weekdaysShort : "Ш§Щ„ШЈШ­ШЇ_Ш§Щ„ШҐШ«Щ†ЩЉЩ†_Ш§Щ„Ш«Щ„Ш§Ш«Ш§ШЎ_Ш§Щ„ШЈШ±ШЁШ№Ш§ШЎ_Ш§Щ„Ш®Щ…ЩЉШі_Ш§Щ„Ш¬Щ…Ш№Ш©_Ш§Щ„ШіШЁШЄ".split("_"),
        weekdaysMin : "Ш­_Щ†_Ш«_Ш±_Ш®_Ш¬_Ші".split("_"),
        longDateFormat : {
            LT : "HH:mm",
            L : "DD/MM/YYYY",
            LL : "D MMMM YYYY",
            LLL : "D MMMM YYYY LT",
            LLLL : "dddd D MMMM YYYY LT"
        },
        calendar : {
            sameDay: "[Ш§Щ„ЩЉЩ€Щ… Ш№Щ„Щ‰ Ш§Щ„ШіШ§Ш№Ш©] LT",
            nextDay: '[ШєШЇШ§ Ш№Щ„Щ‰ Ш§Щ„ШіШ§Ш№Ш©] LT',
            nextWeek: 'dddd [Ш№Щ„Щ‰ Ш§Щ„ШіШ§Ш№Ш©] LT',
            lastDay: '[ШЈЩ…Ші Ш№Щ„Щ‰ Ш§Щ„ШіШ§Ш№Ш©] LT',
            lastWeek: 'dddd [Ш№Щ„Щ‰ Ш§Щ„ШіШ§Ш№Ш©] LT',
            sameElse: 'L'
        },
        relativeTime : {
            future : "ЩЃЩЉ %s",
            past : "Щ…Щ†Ш° %s",
            s : "Ш«Щ€Ш§Щ†",
            m : "ШЇЩ‚ЩЉЩ‚Ш©",
            mm : "%d ШЇЩ‚Ш§Ш¦Щ‚",
            h : "ШіШ§Ш№Ш©",
            hh : "%d ШіШ§Ш№Ш§ШЄ",
            d : "ЩЉЩ€Щ…",
            dd : "%d ШЈЩЉШ§Щ…",
            M : "ШґЩ‡Ш±",
            MM : "%d ШЈШґЩ‡Ш±",
            y : "ШіЩ†Ш©",
            yy : "%d ШіЩ†Щ€Ш§ШЄ"
        },
        week : {
            dow : 6, // Saturday is the first day of the week.
            doy : 12  // The week that contains Jan 1st is the first week of the year.
        }
    });
}));
// moment.js language configuration
// language : bulgarian (bg)
// author : Krasen Borisov : https://github.com/kraz

(function (factory) {
    factory(moment);
}(function (moment) {
    return moment.lang('bg', {
        months : "СЏРЅСѓР°СЂРё_С„РµРІСЂСѓР°СЂРё_РјР°СЂС‚_Р°РїСЂРёР»_РјР°Р№_СЋРЅРё_СЋР»Рё_Р°РІРіСѓСЃС‚_СЃРµРїС‚РµРјРІСЂРё_РѕРєС‚РѕРјРІСЂРё_РЅРѕРµРјРІСЂРё_РґРµРєРµРјРІСЂРё".split("_"),
        monthsShort : "СЏРЅСЂ_С„РµРІ_РјР°СЂ_Р°РїСЂ_РјР°Р№_СЋРЅРё_СЋР»Рё_Р°РІРі_СЃРµРї_РѕРєС‚_РЅРѕРµ_РґРµРє".split("_"),
        weekdays : "РЅРµРґРµР»СЏ_РїРѕРЅРµРґРµР»РЅРёРє_РІС‚РѕСЂРЅРёРє_СЃСЂСЏРґР°_С‡РµС‚РІСЉСЂС‚СЉРє_РїРµС‚СЉРє_СЃСЉР±РѕС‚Р°".split("_"),
        weekdaysShort : "РЅРµРґ_РїРѕРЅ_РІС‚Рѕ_СЃСЂСЏ_С‡РµС‚_РїРµС‚_СЃСЉР±".split("_"),
        weekdaysMin : "РЅРґ_РїРЅ_РІС‚_СЃСЂ_С‡С‚_РїС‚_СЃР±".split("_"),
        longDateFormat : {
            LT : "H:mm",
            L : "D.MM.YYYY",
            LL : "D MMMM YYYY",
            LLL : "D MMMM YYYY LT",
            LLLL : "dddd, D MMMM YYYY LT"
        },
        calendar : {
            sameDay : '[Р”РЅРµСЃ РІ] LT',
            nextDay : '[РЈС‚СЂРµ РІ] LT',
            nextWeek : 'dddd [РІ] LT',
            lastDay : '[Р’С‡РµСЂР° РІ] LT',
            lastWeek : function () {
                switch (this.day()) {
                case 0:
                case 3:
                case 6:
                    return '[Р’ РёР·РјРёРЅР°Р»Р°С‚Р°] dddd [РІ] LT';
                case 1:
                case 2:
                case 4:
                case 5:
                    return '[Р’ РёР·РјРёРЅР°Р»РёСЏ] dddd [РІ] LT';
                }
            },
            sameElse : 'L'
        },
        relativeTime : {
            future : "СЃР»РµРґ %s",
            past : "РїСЂРµРґРё %s",
            s : "РЅСЏРєРѕР»РєРѕ СЃРµРєСѓРЅРґРё",
            m : "РјРёРЅСѓС‚Р°",
            mm : "%d РјРёРЅСѓС‚Рё",
            h : "С‡Р°СЃ",
            hh : "%d С‡Р°СЃР°",
            d : "РґРµРЅ",
            dd : "%d РґРЅРё",
            M : "РјРµСЃРµС†",
            MM : "%d РјРµСЃРµС†Р°",
            y : "РіРѕРґРёРЅР°",
            yy : "%d РіРѕРґРёРЅРё"
        },
        ordinal : function (number) {
            var lastDigit = number % 10,
                last2Digits = number % 100;
            if (number === 0) {
                return number + '-РµРІ';
            } else if (last2Digits === 0) {
                return number + '-РµРЅ';
            } else if (last2Digits > 10 && last2Digits < 20) {
                return number + '-С‚Рё';
            } else if (lastDigit === 1) {
                return number + '-РІРё';
            } else if (lastDigit === 2) {
                return number + '-СЂРё';
            } else if (lastDigit === 7 || lastDigit === 8) {
                return number + '-РјРё';
            } else {
                return number + '-С‚Рё';
            }
        },
        week : {
            dow : 1, // Monday is the first day of the week.
            doy : 7  // The week that contains Jan 1st is the first week of the year.
        }
    });
}));
// moment.js language configuration
// language : breton (br)
// author : Jean-Baptiste Le Duigou : https://github.com/jbleduigou

(function (factory) {
    factory(moment);
}(function (moment) {
    function relativeTimeWithMutation(number, withoutSuffix, key) {
        var format = {
            'mm': "munutenn",
            'MM': "miz",
            'dd': "devezh"
        };
        return number + ' ' + mutation(format[key], number);
    }

    function specialMutationForYears(number) {
        switch (lastNumber(number)) {
        case 1:
        case 3:
        case 4:
        case 5:
        case 9:
            return number + ' bloaz';
        default:
            return number + ' vloaz';
        }
    }

    function lastNumber(number) {
        if (number > 9) {
            return lastNumber(number % 10);
        }
        return number;
    }

    function mutation(text, number) {
        if (number === 2) {
            return softMutation(text);
        }
        return text;
    }

    function softMutation(text) {
        var mutationTable = {
            'm': 'v',
            'b': 'v',
            'd': 'z'
        };
        if (mutationTable[text.charAt(0)] === undefined) {
            return text;
        }
        return mutationTable[text.charAt(0)] + text.substring(1);
    }

    return moment.lang('br', {
        months : "Genver_C'hwevrer_Meurzh_Ebrel_Mae_Mezheven_Gouere_Eost_Gwengolo_Here_Du_Kerzu".split("_"),
        monthsShort : "Gen_C'hwe_Meu_Ebr_Mae_Eve_Gou_Eos_Gwe_Her_Du_Ker".split("_"),
        weekdays : "Sul_Lun_Meurzh_Merc'her_Yaou_Gwener_Sadorn".split("_"),
        weekdaysShort : "Sul_Lun_Meu_Mer_Yao_Gwe_Sad".split("_"),
        weekdaysMin : "Su_Lu_Me_Mer_Ya_Gw_Sa".split("_"),
        longDateFormat : {
            LT : "h[e]mm A",
            L : "DD/MM/YYYY",
            LL : "D [a viz] MMMM YYYY",
            LLL : "D [a viz] MMMM YYYY LT",
            LLLL : "dddd, D [a viz] MMMM YYYY LT"
        },
        calendar : {
            sameDay : '[Hiziv da] LT',
            nextDay : '[Warc\'hoazh da] LT',
            nextWeek : 'dddd [da] LT',
            lastDay : '[Dec\'h da] LT',
            lastWeek : 'dddd [paset da] LT',
            sameElse : 'L'
        },
        relativeTime : {
            future : "a-benn %s",
            past : "%s 'zo",
            s : "un nebeud segondennoГ№",
            m : "ur vunutenn",
            mm : relativeTimeWithMutation,
            h : "un eur",
            hh : "%d eur",
            d : "un devezh",
            dd : relativeTimeWithMutation,
            M : "ur miz",
            MM : relativeTimeWithMutation,
            y : "ur bloaz",
            yy : specialMutationForYears
        },
        ordinal : function (number) {
            var output = (number === 1) ? 'aГ±' : 'vet';
            return number + output;
        },
        week : {
            dow : 1, // Monday is the first day of the week.
            doy : 4  // The week that contains Jan 4th is the first week of the year.
        }
    });
}));
// moment.js language configuration
// language : bosnian (bs)
// author : Nedim Cholich : https://github.com/frontyard
// based on (hr) translation by Bojan MarkoviД‡

(function (factory) {
    factory(moment);
}(function (moment) {

    function translate(number, withoutSuffix, key) {
        var result = number + " ";
        switch (key) {
        case 'm':
            return withoutSuffix ? 'jedna minuta' : 'jedne minute';
        case 'mm':
            if (number === 1) {
                result += 'minuta';
            } else if (number === 2 || number === 3 || number === 4) {
                result += 'minute';
            } else {
                result += 'minuta';
            }
            return result;
        case 'h':
            return withoutSuffix ? 'jedan sat' : 'jednog sata';
        case 'hh':
            if (number === 1) {
                result += 'sat';
            } else if (number === 2 || number === 3 || number === 4) {
                result += 'sata';
            } else {
                result += 'sati';
            }
            return result;
        case 'dd':
            if (number === 1) {
                result += 'dan';
            } else {
                result += 'dana';
            }
            return result;
        case 'MM':
            if (number === 1) {
                result += 'mjesec';
            } else if (number === 2 || number === 3 || number === 4) {
                result += 'mjeseca';
            } else {
                result += 'mjeseci';
            }
            return result;
        case 'yy':
            if (number === 1) {
                result += 'godina';
            } else if (number === 2 || number === 3 || number === 4) {
                result += 'godine';
            } else {
                result += 'godina';
            }
            return result;
        }
    }

    return moment.lang('bs', {
		months : "januar_februar_mart_april_maj_juni_juli_avgust_septembar_oktobar_novembar_decembar".split("_"),
		monthsShort : "jan._feb._mar._apr._maj._jun._jul._avg._sep._okt._nov._dec.".split("_"),
        weekdays : "nedjelja_ponedjeljak_utorak_srijeda_ДЌetvrtak_petak_subota".split("_"),
        weekdaysShort : "ned._pon._uto._sri._ДЌet._pet._sub.".split("_"),
        weekdaysMin : "ne_po_ut_sr_ДЌe_pe_su".split("_"),
        longDateFormat : {
            LT : "H:mm",
            L : "DD. MM. YYYY",
            LL : "D. MMMM YYYY",
            LLL : "D. MMMM YYYY LT",
            LLLL : "dddd, D. MMMM YYYY LT"
        },
        calendar : {
            sameDay  : '[danas u] LT',
            nextDay  : '[sutra u] LT',

            nextWeek : function () {
                switch (this.day()) {
                case 0:
                    return '[u] [nedjelju] [u] LT';
                case 3:
                    return '[u] [srijedu] [u] LT';
                case 6:
                    return '[u] [subotu] [u] LT';
                case 1:
                case 2:
                case 4:
                case 5:
                    return '[u] dddd [u] LT';
                }
            },
            lastDay  : '[juДЌer u] LT',
            lastWeek : function () {
                switch (this.day()) {
                case 0:
                case 3:
                    return '[proЕЎlu] dddd [u] LT';
                case 6:
                    return '[proЕЎle] [subote] [u] LT';
                case 1:
                case 2:
                case 4:
                case 5:
                    return '[proЕЎli] dddd [u] LT';
                }
            },
            sameElse : 'L'
        },
        relativeTime : {
            future : "za %s",
            past   : "prije %s",
            s      : "par sekundi",
            m      : translate,
            mm     : translate,
            h      : translate,
            hh     : translate,
            d      : "dan",
            dd     : translate,
            M      : "mjesec",
            MM     : translate,
            y      : "godinu",
            yy     : translate
        },
        ordinal : '%d.',
        week : {
            dow : 1, // Monday is the first day of the week.
            doy : 7  // The week that contains Jan 1st is the first week of the year.
        }
    });
}));
// moment.js language configuration
// language : catalan (ca)
// author : Juan G. Hurtado : https://github.com/juanghurtado

(function (factory) {
    factory(moment);
}(function (moment) {
    return moment.lang('ca', {
        months : "gener_febrer_marГ§_abril_maig_juny_juliol_agost_setembre_octubre_novembre_desembre".split("_"),
        monthsShort : "gen._febr._mar._abr._mai._jun._jul._ag._set._oct._nov._des.".split("_"),
        weekdays : "diumenge_dilluns_dimarts_dimecres_dijous_divendres_dissabte".split("_"),
        weekdaysShort : "dg._dl._dt._dc._dj._dv._ds.".split("_"),
        weekdaysMin : "Dg_Dl_Dt_Dc_Dj_Dv_Ds".split("_"),
        longDateFormat : {
            LT : "H:mm",
            L : "DD/MM/YYYY",
            LL : "D MMMM YYYY",
            LLL : "D MMMM YYYY LT",
            LLLL : "dddd D MMMM YYYY LT"
        },
        calendar : {
            sameDay : function () {
                return '[avui a ' + ((this.hours() !== 1) ? 'les' : 'la') + '] LT';
            },
            nextDay : function () {
                return '[demГ  a ' + ((this.hours() !== 1) ? 'les' : 'la') + '] LT';
            },
            nextWeek : function () {
                return 'dddd [a ' + ((this.hours() !== 1) ? 'les' : 'la') + '] LT';
            },
            lastDay : function () {
                return '[ahir a ' + ((this.hours() !== 1) ? 'les' : 'la') + '] LT';
            },
            lastWeek : function () {
                return '[el] dddd [passat a ' + ((this.hours() !== 1) ? 'les' : 'la') + '] LT';
            },
            sameElse : 'L'
        },
        relativeTime : {
            future : "en %s",
            past : "fa %s",
            s : "uns segons",
            m : "un minut",
            mm : "%d minuts",
            h : "una hora",
            hh : "%d hores",
            d : "un dia",
            dd : "%d dies",
            M : "un mes",
            MM : "%d mesos",
            y : "un any",
            yy : "%d anys"
        },
        ordinal : '%dВє',
        week : {
            dow : 1, // Monday is the first day of the week.
            doy : 4  // The week that contains Jan 4th is the first week of the year.
        }
    });
}));
// moment.js language configuration
// language : czech (cs)
// author : petrbela : https://github.com/petrbela

(function (factory) {
    factory(moment);
}(function (moment) {
    var months = "leden_Гєnor_bЕ™ezen_duben_kvД›ten_ДЌerven_ДЌervenec_srpen_zГЎЕ™Г­_Е™Г­jen_listopad_prosinec".split("_"),
        monthsShort = "led_Гєno_bЕ™e_dub_kvД›_ДЌvn_ДЌvc_srp_zГЎЕ™_Е™Г­j_lis_pro".split("_");

    function plural(n) {
        return (n > 1) && (n < 5) && (~~(n / 10) !== 1);
    }

    function translate(number, withoutSuffix, key, isFuture) {
        var result = number + " ";
        switch (key) {
        case 's':  // a few seconds / in a few seconds / a few seconds ago
            return (withoutSuffix || isFuture) ? 'pГЎr vteЕ™in' : 'pГЎr vteЕ™inami';
        case 'm':  // a minute / in a minute / a minute ago
            return withoutSuffix ? 'minuta' : (isFuture ? 'minutu' : 'minutou');
        case 'mm': // 9 minutes / in 9 minutes / 9 minutes ago
            if (withoutSuffix || isFuture) {
                return result + (plural(number) ? 'minuty' : 'minut');
            } else {
                return result + 'minutami';
            }
            break;
        case 'h':  // an hour / in an hour / an hour ago
            return withoutSuffix ? 'hodina' : (isFuture ? 'hodinu' : 'hodinou');
        case 'hh': // 9 hours / in 9 hours / 9 hours ago
            if (withoutSuffix || isFuture) {
                return result + (plural(number) ? 'hodiny' : 'hodin');
            } else {
                return result + 'hodinami';
            }
            break;
        case 'd':  // a day / in a day / a day ago
            return (withoutSuffix || isFuture) ? 'den' : 'dnem';
        case 'dd': // 9 days / in 9 days / 9 days ago
            if (withoutSuffix || isFuture) {
                return result + (plural(number) ? 'dny' : 'dnГ­');
            } else {
                return result + 'dny';
            }
            break;
        case 'M':  // a month / in a month / a month ago
            return (withoutSuffix || isFuture) ? 'mД›sГ­c' : 'mД›sГ­cem';
        case 'MM': // 9 months / in 9 months / 9 months ago
            if (withoutSuffix || isFuture) {
                return result + (plural(number) ? 'mД›sГ­ce' : 'mД›sГ­cЕЇ');
            } else {
                return result + 'mД›sГ­ci';
            }
            break;
        case 'y':  // a year / in a year / a year ago
            return (withoutSuffix || isFuture) ? 'rok' : 'rokem';
        case 'yy': // 9 years / in 9 years / 9 years ago
            if (withoutSuffix || isFuture) {
                return result + (plural(number) ? 'roky' : 'let');
            } else {
                return result + 'lety';
            }
            break;
        }
    }

    return moment.lang('cs', {
        months : months,
        monthsShort : monthsShort,
        monthsParse : (function (months, monthsShort) {
            var i, _monthsParse = [];
            for (i = 0; i < 12; i++) {
                // use custom parser to solve problem with July (ДЌervenec)
                _monthsParse[i] = new RegExp('^' + months[i] + '$|^' + monthsShort[i] + '$', 'i');
            }
            return _monthsParse;
        }(months, monthsShort)),
        weekdays : "nedД›le_pondД›lГ­_ГєterГЅ_stЕ™eda_ДЌtvrtek_pГЎtek_sobota".split("_"),
        weekdaysShort : "ne_po_Гєt_st_ДЌt_pГЎ_so".split("_"),
        weekdaysMin : "ne_po_Гєt_st_ДЌt_pГЎ_so".split("_"),
        longDateFormat : {
            LT: "H:mm",
            L : "DD.MM.YYYY",
            LL : "D. MMMM YYYY",
            LLL : "D. MMMM YYYY LT",
            LLLL : "dddd D. MMMM YYYY LT"
        },
        calendar : {
            sameDay: "[dnes v] LT",
            nextDay: '[zГ­tra v] LT',
            nextWeek: function () {
                switch (this.day()) {
                case 0:
                    return '[v nedД›li v] LT';
                case 1:
                case 2:
                    return '[v] dddd [v] LT';
                case 3:
                    return '[ve stЕ™edu v] LT';
                case 4:
                    return '[ve ДЌtvrtek v] LT';
                case 5:
                    return '[v pГЎtek v] LT';
                case 6:
                    return '[v sobotu v] LT';
                }
            },
            lastDay: '[vДЌera v] LT',
            lastWeek: function () {
                switch (this.day()) {
                case 0:
                    return '[minulou nedД›li v] LT';
                case 1:
                case 2:
                    return '[minulГ©] dddd [v] LT';
                case 3:
                    return '[minulou stЕ™edu v] LT';
                case 4:
                case 5:
                    return '[minulГЅ] dddd [v] LT';
                case 6:
                    return '[minulou sobotu v] LT';
                }
            },
            sameElse: "L"
        },
        relativeTime : {
            future : "za %s",
            past : "pЕ™ed %s",
            s : translate,
            m : translate,
            mm : translate,
            h : translate,
            hh : translate,
            d : translate,
            dd : translate,
            M : translate,
            MM : translate,
            y : translate,
            yy : translate
        },
        ordinal : '%d.',
        week : {
            dow : 1, // Monday is the first day of the week.
            doy : 4  // The week that contains Jan 4th is the first week of the year.
        }
    });
}));
// moment.js language configuration
// language : chuvash (cv)
// author : Anatoly Mironov : https://github.com/mirontoli

(function (factory) {
    factory(moment);
}(function (moment) {
    return moment.lang('cv', {
        months : "РєДѓСЂР»Р°С‡_РЅР°СЂДѓСЃ_РїСѓС€_Р°РєР°_РјР°Р№_Г§Д•СЂС‚РјРµ_СѓС‚Дѓ_Г§СѓСЂР»Р°_Р°РІДѓРЅ_СЋРїР°_С‡УіРє_СЂР°С€С‚Р°РІ".split("_"),
        monthsShort : "РєДѓСЂ_РЅР°СЂ_РїСѓС€_Р°РєР°_РјР°Р№_Г§Д•СЂ_СѓС‚Дѓ_Г§СѓСЂ_Р°РІ_СЋРїР°_С‡УіРє_СЂР°С€".split("_"),
        weekdays : "РІС‹СЂСЃР°СЂРЅРёРєСѓРЅ_С‚СѓРЅС‚РёРєСѓРЅ_С‹С‚Р»Р°СЂРёРєСѓРЅ_СЋРЅРєСѓРЅ_РєД•Г§РЅРµСЂРЅРёРєСѓРЅ_СЌСЂРЅРµРєСѓРЅ_С€ДѓРјР°С‚РєСѓРЅ".split("_"),
        weekdaysShort : "РІС‹СЂ_С‚СѓРЅ_С‹С‚Р»_СЋРЅ_РєД•Г§_СЌСЂРЅ_С€ДѓРј".split("_"),
        weekdaysMin : "РІСЂ_С‚РЅ_С‹С‚_СЋРЅ_РєГ§_СЌСЂ_С€Рј".split("_"),
        longDateFormat : {
            LT : "HH:mm",
            L : "DD-MM-YYYY",
            LL : "YYYY [Г§СѓР»С…Рё] MMMM [СѓР№ДѓС…Д•РЅ] D[-РјД•С€Д•]",
            LLL : "YYYY [Г§СѓР»С…Рё] MMMM [СѓР№ДѓС…Д•РЅ] D[-РјД•С€Д•], LT",
            LLLL : "dddd, YYYY [Г§СѓР»С…Рё] MMMM [СѓР№ДѓС…Д•РЅ] D[-РјД•С€Д•], LT"
        },
        calendar : {
            sameDay: '[РџР°СЏРЅ] LT [СЃРµС…РµС‚СЂРµ]',
            nextDay: '[Р«СЂР°РЅ] LT [СЃРµС…РµС‚СЂРµ]',
            lastDay: '[Д”РЅРµСЂ] LT [СЃРµС…РµС‚СЂРµ]',
            nextWeek: '[Г‡РёС‚РµСЃ] dddd LT [СЃРµС…РµС‚СЂРµ]',
            lastWeek: '[РСЂС‚РЅД•] dddd LT [СЃРµС…РµС‚СЂРµ]',
            sameElse: 'L'
        },
        relativeTime : {
            future : function (output) {
                var affix = /СЃРµС…РµС‚$/i.exec(output) ? "СЂРµРЅ" : /Г§СѓР»$/i.exec(output) ? "С‚Р°РЅ" : "СЂР°РЅ";
                return output + affix;
            },
            past : "%s РєР°СЏР»Р»Р°",
            s : "РїД•СЂ-РёРє Г§РµРєРєСѓРЅС‚",
            m : "РїД•СЂ РјРёРЅСѓС‚",
            mm : "%d РјРёРЅСѓС‚",
            h : "РїД•СЂ СЃРµС…РµС‚",
            hh : "%d СЃРµС…РµС‚",
            d : "РїД•СЂ РєСѓРЅ",
            dd : "%d РєСѓРЅ",
            M : "РїД•СЂ СѓР№ДѓС…",
            MM : "%d СѓР№ДѓС…",
            y : "РїД•СЂ Г§СѓР»",
            yy : "%d Г§СѓР»"
        },
        ordinal : '%d-РјД•С€',
        week : {
            dow : 1, // Monday is the first day of the week.
            doy : 7  // The week that contains Jan 1st is the first week of the year.
        }
    });
}));
// moment.js language configuration
// language : Welsh (cy)
// author : Robert Allen

(function (factory) {
    factory(moment);
}(function (moment) {
    return moment.lang("cy", {
        months: "Ionawr_Chwefror_Mawrth_Ebrill_Mai_Mehefin_Gorffennaf_Awst_Medi_Hydref_Tachwedd_Rhagfyr".split("_"),
        monthsShort: "Ion_Chwe_Maw_Ebr_Mai_Meh_Gor_Aws_Med_Hyd_Tach_Rhag".split("_"),
        weekdays: "Dydd Sul_Dydd Llun_Dydd Mawrth_Dydd Mercher_Dydd Iau_Dydd Gwener_Dydd Sadwrn".split("_"),
        weekdaysShort: "Sul_Llun_Maw_Mer_Iau_Gwe_Sad".split("_"),
        weekdaysMin: "Su_Ll_Ma_Me_Ia_Gw_Sa".split("_"),
        // time formats are the same as en-gb
        longDateFormat: {
            LT: "HH:mm",
            L: "DD/MM/YYYY",
            LL: "D MMMM YYYY",
            LLL: "D MMMM YYYY LT",
            LLLL: "dddd, D MMMM YYYY LT"
        },
        calendar: {
            sameDay: '[Heddiw am] LT',
            nextDay: '[Yfory am] LT',
            nextWeek: 'dddd [am] LT',
            lastDay: '[Ddoe am] LT',
            lastWeek: 'dddd [diwethaf am] LT',
            sameElse: 'L'
        },
        relativeTime: {
            future: "mewn %s",
            past: "%s yn Г l",
            s: "ychydig eiliadau",
            m: "munud",
            mm: "%d munud",
            h: "awr",
            hh: "%d awr",
            d: "diwrnod",
            dd: "%d diwrnod",
            M: "mis",
            MM: "%d mis",
            y: "blwyddyn",
            yy: "%d flynedd"
        },
        // traditional ordinal numbers above 31 are not commonly used in colloquial Welsh
        ordinal: function (number) {
            var b = number,
                output = '',
                lookup = [
                    '', 'af', 'il', 'ydd', 'ydd', 'ed', 'ed', 'ed', 'fed', 'fed', 'fed', // 1af to 10fed
                    'eg', 'fed', 'eg', 'eg', 'fed', 'eg', 'eg', 'fed', 'eg', 'fed' // 11eg to 20fed
                ];

            if (b > 20) {
                if (b === 40 || b === 50 || b === 60 || b === 80 || b === 100) {
                    output = 'fed'; // not 30ain, 70ain or 90ain
                } else {
                    output = 'ain';
                }
            } else if (b > 0) {
                output = lookup[b];
            }

            return number + output;
        },
        week : {
            dow : 1, // Monday is the first day of the week.
            doy : 4  // The week that contains Jan 4th is the first week of the year.
        }
    });
}));
// moment.js language configuration
// language : danish (da)
// author : Ulrik Nielsen : https://github.com/mrbase

(function (factory) {
    factory(moment);
}(function (moment) {
    return moment.lang('da', {
        months : "januar_februar_marts_april_maj_juni_juli_august_september_oktober_november_december".split("_"),
        monthsShort : "jan_feb_mar_apr_maj_jun_jul_aug_sep_okt_nov_dec".split("_"),
        weekdays : "sГёndag_mandag_tirsdag_onsdag_torsdag_fredag_lГёrdag".split("_"),
        weekdaysShort : "sГёn_man_tir_ons_tor_fre_lГёr".split("_"),
        weekdaysMin : "sГё_ma_ti_on_to_fr_lГё".split("_"),
        longDateFormat : {
            LT : "HH:mm",
            L : "DD/MM/YYYY",
            LL : "D MMMM YYYY",
            LLL : "D MMMM YYYY LT",
            LLLL : "dddd D. MMMM, YYYY LT"
        },
        calendar : {
            sameDay : '[I dag kl.] LT',
            nextDay : '[I morgen kl.] LT',
            nextWeek : 'dddd [kl.] LT',
            lastDay : '[I gГҐr kl.] LT',
            lastWeek : '[sidste] dddd [kl] LT',
            sameElse : 'L'
        },
        relativeTime : {
            future : "om %s",
            past : "%s siden",
            s : "fГҐ sekunder",
            m : "et minut",
            mm : "%d minutter",
            h : "en time",
            hh : "%d timer",
            d : "en dag",
            dd : "%d dage",
            M : "en mГҐned",
            MM : "%d mГҐneder",
            y : "et ГҐr",
            yy : "%d ГҐr"
        },
        ordinal : '%d.',
        week : {
            dow : 1, // Monday is the first day of the week.
            doy : 4  // The week that contains Jan 4th is the first week of the year.
        }
    });
}));
// moment.js language configuration
// language : german (de)
// author : lluchs : https://github.com/lluchs
// author: Menelion ElensГєle: https://github.com/Oire

(function (factory) {
    factory(moment);
}(function (moment) {
    function processRelativeTime(number, withoutSuffix, key, isFuture) {
        var format = {
            'm': ['eine Minute', 'einer Minute'],
            'h': ['eine Stunde', 'einer Stunde'],
            'd': ['ein Tag', 'einem Tag'],
            'dd': [number + ' Tage', number + ' Tagen'],
            'M': ['ein Monat', 'einem Monat'],
            'MM': [number + ' Monate', number + ' Monaten'],
            'y': ['ein Jahr', 'einem Jahr'],
            'yy': [number + ' Jahre', number + ' Jahren']
        };
        return withoutSuffix ? format[key][0] : format[key][1];
    }

    return moment.lang('de', {
        months : "Januar_Februar_MГ¤rz_April_Mai_Juni_Juli_August_September_Oktober_November_Dezember".split("_"),
        monthsShort : "Jan._Febr._Mrz._Apr._Mai_Jun._Jul._Aug._Sept._Okt._Nov._Dez.".split("_"),
        weekdays : "Sonntag_Montag_Dienstag_Mittwoch_Donnerstag_Freitag_Samstag".split("_"),
        weekdaysShort : "So._Mo._Di._Mi._Do._Fr._Sa.".split("_"),
        weekdaysMin : "So_Mo_Di_Mi_Do_Fr_Sa".split("_"),
        longDateFormat : {
            LT: "H:mm [Uhr]",
            L : "DD.MM.YYYY",
            LL : "D. MMMM YYYY",
            LLL : "D. MMMM YYYY LT",
            LLLL : "dddd, D. MMMM YYYY LT"
        },
        calendar : {
            sameDay: "[Heute um] LT",
            sameElse: "L",
            nextDay: '[Morgen um] LT',
            nextWeek: 'dddd [um] LT',
            lastDay: '[Gestern um] LT',
            lastWeek: '[letzten] dddd [um] LT'
        },
        relativeTime : {
            future : "in %s",
            past : "vor %s",
            s : "ein paar Sekunden",
            m : processRelativeTime,
            mm : "%d Minuten",
            h : processRelativeTime,
            hh : "%d Stunden",
            d : processRelativeTime,
            dd : processRelativeTime,
            M : processRelativeTime,
            MM : processRelativeTime,
            y : processRelativeTime,
            yy : processRelativeTime
        },
        ordinal : '%d.',
        week : {
            dow : 1, // Monday is the first day of the week.
            doy : 4  // The week that contains Jan 4th is the first week of the year.
        }
    });
}));
// moment.js language configuration
// language : modern greek (el)
// author : Aggelos Karalias : https://github.com/mehiel

(function (factory) {
    factory(moment);
}(function (moment) {
    return moment.lang('el', {
        monthsNominativeEl : "О™О±ОЅОїП…О¬ПЃО№ОїП‚_О¦ОµОІПЃОїП…О¬ПЃО№ОїП‚_ОњО¬ПЃП„О№ОїП‚_О‘ПЂПЃОЇО»О№ОїП‚_ОњО¬О№ОїП‚_О™ОїПЌОЅО№ОїП‚_О™ОїПЌО»О№ОїП‚_О‘ПЌОіОїП…ПѓП„ОїП‚_ОЈОµПЂП„О­ОјОІПЃО№ОїП‚_ОџОєП„ПЋОІПЃО№ОїП‚_ОќОїО­ОјОІПЃО№ОїП‚_О”ОµОєО­ОјОІПЃО№ОїП‚".split("_"),
        monthsGenitiveEl : "О™О±ОЅОїП…О±ПЃОЇОїП…_О¦ОµОІПЃОїП…О±ПЃОЇОїП…_ОњО±ПЃП„ОЇОїП…_О‘ПЂПЃО№О»ОЇОїП…_ОњО±ОђОїП…_О™ОїП…ОЅОЇОїП…_О™ОїП…О»ОЇОїП…_О‘П…ОіОїПЌПѓП„ОїП…_ОЈОµПЂП„ОµОјОІПЃОЇОїП…_ОџОєП„П‰ОІПЃОЇОїП…_ОќОїОµОјОІПЃОЇОїП…_О”ОµОєОµОјОІПЃОЇОїП…".split("_"),
        months : function (momentToFormat, format) {
            if (/D/.test(format.substring(0, format.indexOf("MMMM")))) { // if there is a day number before 'MMMM'
                return this._monthsGenitiveEl[momentToFormat.month()];
            } else {
                return this._monthsNominativeEl[momentToFormat.month()];
            }
        },
        monthsShort : "О™О±ОЅ_О¦ОµОІ_ОњО±ПЃ_О‘ПЂПЃ_ОњО±ПЉ_О™ОїП…ОЅ_О™ОїП…О»_О‘П…Оі_ОЈОµПЂ_ОџОєП„_ОќОїОµ_О”ОµОє".split("_"),
        weekdays : "ОљП…ПЃО№О±ОєО®_О”ОµП…П„О­ПЃО±_О¤ПЃОЇП„О·_О¤ОµП„О¬ПЃП„О·_О О­ОјПЂП„О·_О О±ПЃО±ПѓОєОµП…О®_ОЈО¬ОІОІО±П„Ої".split("_"),
        weekdaysShort : "ОљП…ПЃ_О”ОµП…_О¤ПЃО№_О¤ОµП„_О ОµОј_О О±ПЃ_ОЈО±ОІ".split("_"),
        weekdaysMin : "ОљП…_О”Оµ_О¤ПЃ_О¤Оµ_О Оµ_О О±_ОЈО±".split("_"),
        meridiem : function (hours, minutes, isLower) {
            if (hours > 11) {
                return isLower ? 'ОјОј' : 'ОњОњ';
            } else {
                return isLower ? 'ПЂОј' : 'О Оњ';
            }
        },
        longDateFormat : {
            LT : "h:mm A",
            L : "DD/MM/YYYY",
            LL : "D MMMM YYYY",
            LLL : "D MMMM YYYY LT",
            LLLL : "dddd, D MMMM YYYY LT"
        },
        calendarEl : {
            sameDay : '[ОЈО®ОјОµПЃО± {}] LT',
            nextDay : '[О‘ПЌПЃО№Ої {}] LT',
            nextWeek : 'dddd [{}] LT',
            lastDay : '[О§ОёОµП‚ {}] LT',
            lastWeek : '[П„О·ОЅ ПЂПЃОїО·ОіОїПЌОјОµОЅО·] dddd [{}] LT',
            sameElse : 'L'
        },
        calendar : function (key, mom) {
            var output = this._calendarEl[key],
                hours = mom && mom.hours();

            return output.replace("{}", (hours % 12 === 1 ? "ПѓП„О·" : "ПѓП„О№П‚"));
        },
        relativeTime : {
            future : "ПѓОµ %s",
            past : "%s ПЂПЃО№ОЅ",
            s : "ОґОµП…П„ОµПЃПЊО»ОµПЂП„О±",
            m : "О­ОЅО± О»ОµПЂП„ПЊ",
            mm : "%d О»ОµПЂП„О¬",
            h : "ОјОЇО± ПЋПЃО±",
            hh : "%d ПЋПЃОµП‚",
            d : "ОјОЇО± ОјО­ПЃО±",
            dd : "%d ОјО­ПЃОµП‚",
            M : "О­ОЅО±П‚ ОјО®ОЅО±П‚",
            MM : "%d ОјО®ОЅОµП‚",
            y : "О­ОЅО±П‚ П‡ПЃПЊОЅОїП‚",
            yy : "%d П‡ПЃПЊОЅО№О±"
        },
        ordinal : function (number) {
            return number + 'О·';
        },
        week : {
            dow : 1, // Monday is the first day of the week.
            doy : 4  // The week that contains Jan 4st is the first week of the year.
        }
    });
}));
// moment.js language configuration
// language : australian english (en-au)

(function (factory) {
    factory(moment);
}(function (moment) {
    return moment.lang('en-au', {
        months : "January_February_March_April_May_June_July_August_September_October_November_December".split("_"),
        monthsShort : "Jan_Feb_Mar_Apr_May_Jun_Jul_Aug_Sep_Oct_Nov_Dec".split("_"),
        weekdays : "Sunday_Monday_Tuesday_Wednesday_Thursday_Friday_Saturday".split("_"),
        weekdaysShort : "Sun_Mon_Tue_Wed_Thu_Fri_Sat".split("_"),
        weekdaysMin : "Su_Mo_Tu_We_Th_Fr_Sa".split("_"),
        longDateFormat : {
            LT : "h:mm A",
            L : "DD/MM/YYYY",
            LL : "D MMMM YYYY",
            LLL : "D MMMM YYYY LT",
            LLLL : "dddd, D MMMM YYYY LT"
        },
        calendar : {
            sameDay : '[Today at] LT',
            nextDay : '[Tomorrow at] LT',
            nextWeek : 'dddd [at] LT',
            lastDay : '[Yesterday at] LT',
            lastWeek : '[Last] dddd [at] LT',
            sameElse : 'L'
        },
        relativeTime : {
            future : "in %s",
            past : "%s ago",
            s : "a few seconds",
            m : "a minute",
            mm : "%d minutes",
            h : "an hour",
            hh : "%d hours",
            d : "a day",
            dd : "%d days",
            M : "a month",
            MM : "%d months",
            y : "a year",
            yy : "%d years"
        },
        ordinal : function (number) {
            var b = number % 10,
                output = (~~ (number % 100 / 10) === 1) ? 'th' :
                (b === 1) ? 'st' :
                (b === 2) ? 'nd' :
                (b === 3) ? 'rd' : 'th';
            return number + output;
        },
        week : {
            dow : 1, // Monday is the first day of the week.
            doy : 4  // The week that contains Jan 4th is the first week of the year.
        }
    });
}));
// moment.js language configuration
// language : canadian english (en-ca)
// author : Jonathan Abourbih : https://github.com/jonbca

(function (factory) {
    factory(moment);
}(function (moment) {
    return moment.lang('en-ca', {
        months : "January_February_March_April_May_June_July_August_September_October_November_December".split("_"),
        monthsShort : "Jan_Feb_Mar_Apr_May_Jun_Jul_Aug_Sep_Oct_Nov_Dec".split("_"),
        weekdays : "Sunday_Monday_Tuesday_Wednesday_Thursday_Friday_Saturday".split("_"),
        weekdaysShort : "Sun_Mon_Tue_Wed_Thu_Fri_Sat".split("_"),
        weekdaysMin : "Su_Mo_Tu_We_Th_Fr_Sa".split("_"),
        longDateFormat : {
            LT : "h:mm A",
            L : "YYYY-MM-DD",
            LL : "D MMMM, YYYY",
            LLL : "D MMMM, YYYY LT",
            LLLL : "dddd, D MMMM, YYYY LT"
        },
        calendar : {
            sameDay : '[Today at] LT',
            nextDay : '[Tomorrow at] LT',
            nextWeek : 'dddd [at] LT',
            lastDay : '[Yesterday at] LT',
            lastWeek : '[Last] dddd [at] LT',
            sameElse : 'L'
        },
        relativeTime : {
            future : "in %s",
            past : "%s ago",
            s : "a few seconds",
            m : "a minute",
            mm : "%d minutes",
            h : "an hour",
            hh : "%d hours",
            d : "a day",
            dd : "%d days",
            M : "a month",
            MM : "%d months",
            y : "a year",
            yy : "%d years"
        },
        ordinal : function (number) {
            var b = number % 10,
                output = (~~ (number % 100 / 10) === 1) ? 'th' :
                (b === 1) ? 'st' :
                (b === 2) ? 'nd' :
                (b === 3) ? 'rd' : 'th';
            return number + output;
        }
    });
}));
// moment.js language configuration
// language : great britain english (en-gb)
// author : Chris Gedrim : https://github.com/chrisgedrim

(function (factory) {
    factory(moment);
}(function (moment) {
    return moment.lang('en-gb', {
        months : "January_February_March_April_May_June_July_August_September_October_November_December".split("_"),
        monthsShort : "Jan_Feb_Mar_Apr_May_Jun_Jul_Aug_Sep_Oct_Nov_Dec".split("_"),
        weekdays : "Sunday_Monday_Tuesday_Wednesday_Thursday_Friday_Saturday".split("_"),
        weekdaysShort : "Sun_Mon_Tue_Wed_Thu_Fri_Sat".split("_"),
        weekdaysMin : "Su_Mo_Tu_We_Th_Fr_Sa".split("_"),
        longDateFormat : {
            LT : "HH:mm",
            L : "DD/MM/YYYY",
            LL : "D MMMM YYYY",
            LLL : "D MMMM YYYY LT",
            LLLL : "dddd, D MMMM YYYY LT"
        },
        calendar : {
            sameDay : '[Today at] LT',
            nextDay : '[Tomorrow at] LT',
            nextWeek : 'dddd [at] LT',
            lastDay : '[Yesterday at] LT',
            lastWeek : '[Last] dddd [at] LT',
            sameElse : 'L'
        },
        relativeTime : {
            future : "in %s",
            past : "%s ago",
            s : "a few seconds",
            m : "a minute",
            mm : "%d minutes",
            h : "an hour",
            hh : "%d hours",
            d : "a day",
            dd : "%d days",
            M : "a month",
            MM : "%d months",
            y : "a year",
            yy : "%d years"
        },
        ordinal : function (number) {
            var b = number % 10,
                output = (~~ (number % 100 / 10) === 1) ? 'th' :
                (b === 1) ? 'st' :
                (b === 2) ? 'nd' :
                (b === 3) ? 'rd' : 'th';
            return number + output;
        },
        week : {
            dow : 1, // Monday is the first day of the week.
            doy : 4  // The week that contains Jan 4th is the first week of the year.
        }
    });
}));
// moment.js language configuration
// language : esperanto (eo)
// author : Colin Dean : https://github.com/colindean
// komento: Mi estas malcerta se mi korekte traktis akuzativojn en tiu traduko.
//          Se ne, bonvolu korekti kaj avizi min por ke mi povas lerni!

(function (factory) {
    factory(moment);
}(function (moment) {
    return moment.lang('eo', {
        months : "januaro_februaro_marto_aprilo_majo_junio_julio_aЕ­gusto_septembro_oktobro_novembro_decembro".split("_"),
        monthsShort : "jan_feb_mar_apr_maj_jun_jul_aЕ­g_sep_okt_nov_dec".split("_"),
        weekdays : "DimanД‰o_Lundo_Mardo_Merkredo_ДґaЕ­do_Vendredo_Sabato".split("_"),
        weekdaysShort : "Dim_Lun_Mard_Merk_ДґaЕ­_Ven_Sab".split("_"),
        weekdaysMin : "Di_Lu_Ma_Me_Дґa_Ve_Sa".split("_"),
        longDateFormat : {
            LT : "HH:mm",
            L : "YYYY-MM-DD",
            LL : "D[-an de] MMMM, YYYY",
            LLL : "D[-an de] MMMM, YYYY LT",
            LLLL : "dddd, [la] D[-an de] MMMM, YYYY LT"
        },
        meridiem : function (hours, minutes, isLower) {
            if (hours > 11) {
                return isLower ? 'p.t.m.' : 'P.T.M.';
            } else {
                return isLower ? 'a.t.m.' : 'A.T.M.';
            }
        },
        calendar : {
            sameDay : '[HodiaЕ­ je] LT',
            nextDay : '[MorgaЕ­ je] LT',
            nextWeek : 'dddd [je] LT',
            lastDay : '[HieraЕ­ je] LT',
            lastWeek : '[pasinta] dddd [je] LT',
            sameElse : 'L'
        },
        relativeTime : {
            future : "je %s",
            past : "antaЕ­ %s",
            s : "sekundoj",
            m : "minuto",
            mm : "%d minutoj",
            h : "horo",
            hh : "%d horoj",
            d : "tago",//ne 'diurno', Д‰ar estas uzita por proksimumo
            dd : "%d tagoj",
            M : "monato",
            MM : "%d monatoj",
            y : "jaro",
            yy : "%d jaroj"
        },
        ordinal : "%da",
        week : {
            dow : 1, // Monday is the first day of the week.
            doy : 7  // The week that contains Jan 1st is the first week of the year.
        }
    });
}));
// moment.js language configuration
// language : spanish (es)
// author : Julio NapurГ­ : https://github.com/julionc

(function (factory) {
    factory(moment);
}(function (moment) {
    return moment.lang('es', {
        months : "enero_febrero_marzo_abril_mayo_junio_julio_agosto_septiembre_octubre_noviembre_diciembre".split("_"),
        monthsShort : "ene._feb._mar._abr._may._jun._jul._ago._sep._oct._nov._dic.".split("_"),
        weekdays : "domingo_lunes_martes_miГ©rcoles_jueves_viernes_sГЎbado".split("_"),
        weekdaysShort : "dom._lun._mar._miГ©._jue._vie._sГЎb.".split("_"),
        weekdaysMin : "Do_Lu_Ma_Mi_Ju_Vi_SГЎ".split("_"),
        longDateFormat : {
            LT : "H:mm",
            L : "DD/MM/YYYY",
            LL : "D [de] MMMM [de] YYYY",
            LLL : "D [de] MMMM [de] YYYY LT",
            LLLL : "dddd, D [de] MMMM [de] YYYY LT"
        },
        calendar : {
            sameDay : function () {
                return '[hoy a la' + ((this.hours() !== 1) ? 's' : '') + '] LT';
            },
            nextDay : function () {
                return '[maГ±ana a la' + ((this.hours() !== 1) ? 's' : '') + '] LT';
            },
            nextWeek : function () {
                return 'dddd [a la' + ((this.hours() !== 1) ? 's' : '') + '] LT';
            },
            lastDay : function () {
                return '[ayer a la' + ((this.hours() !== 1) ? 's' : '') + '] LT';
            },
            lastWeek : function () {
                return '[el] dddd [pasado a la' + ((this.hours() !== 1) ? 's' : '') + '] LT';
            },
            sameElse : 'L'
        },
        relativeTime : {
            future : "en %s",
            past : "hace %s",
            s : "unos segundos",
            m : "un minuto",
            mm : "%d minutos",
            h : "una hora",
            hh : "%d horas",
            d : "un dГ­a",
            dd : "%d dГ­as",
            M : "un mes",
            MM : "%d meses",
            y : "un aГ±o",
            yy : "%d aГ±os"
        },
        ordinal : '%dВє',
        week : {
            dow : 1, // Monday is the first day of the week.
            doy : 4  // The week that contains Jan 4th is the first week of the year.
        }
    });
}));
// moment.js language configuration
// language : estonian (et)
// author : Henry Kehlmann : https://github.com/madhenry
// improvements : Illimar Tambek : https://github.com/ragulka

(function (factory) {
    factory(moment);
}(function (moment) {
    function processRelativeTime(number, withoutSuffix, key, isFuture) {
        var format = {
            's' : ['mГµne sekundi', 'mГµni sekund', 'paar sekundit'],
            'm' : ['Гјhe minuti', 'Гјks minut'],
            'mm': [number + ' minuti', number + ' minutit'],
            'h' : ['Гјhe tunni', 'tund aega', 'Гјks tund'],
            'hh': [number + ' tunni', number + ' tundi'],
            'd' : ['Гјhe pГ¤eva', 'Гјks pГ¤ev'],
            'M' : ['kuu aja', 'kuu aega', 'Гјks kuu'],
            'MM': [number + ' kuu', number + ' kuud'],
            'y' : ['Гјhe aasta', 'aasta', 'Гјks aasta'],
            'yy': [number + ' aasta', number + ' aastat']
        };
        if (withoutSuffix) {
            return format[key][2] ? format[key][2] : format[key][1];
        }
        return isFuture ? format[key][0] : format[key][1];
    }

    return moment.lang('et', {
        months        : "jaanuar_veebruar_mГ¤rts_aprill_mai_juuni_juuli_august_september_oktoober_november_detsember".split("_"),
        monthsShort   : "jaan_veebr_mГ¤rts_apr_mai_juuni_juuli_aug_sept_okt_nov_dets".split("_"),
        weekdays      : "pГјhapГ¤ev_esmaspГ¤ev_teisipГ¤ev_kolmapГ¤ev_neljapГ¤ev_reede_laupГ¤ev".split("_"),
        weekdaysShort : "P_E_T_K_N_R_L".split("_"),
        weekdaysMin   : "P_E_T_K_N_R_L".split("_"),
        longDateFormat : {
            LT   : "H:mm",
            L    : "DD.MM.YYYY",
            LL   : "D. MMMM YYYY",
            LLL  : "D. MMMM YYYY LT",
            LLLL : "dddd, D. MMMM YYYY LT"
        },
        calendar : {
            sameDay  : '[TГ¤na,] LT',
            nextDay  : '[Homme,] LT',
            nextWeek : '[JГ¤rgmine] dddd LT',
            lastDay  : '[Eile,] LT',
            lastWeek : '[Eelmine] dddd LT',
            sameElse : 'L'
        },
        relativeTime : {
            future : "%s pГ¤rast",
            past   : "%s tagasi",
            s      : processRelativeTime,
            m      : processRelativeTime,
            mm     : processRelativeTime,
            h      : processRelativeTime,
            hh     : processRelativeTime,
            d      : processRelativeTime,
            dd     : '%d pГ¤eva',
            M      : processRelativeTime,
            MM     : processRelativeTime,
            y      : processRelativeTime,
            yy     : processRelativeTime
        },
        ordinal : '%d.',
        week : {
            dow : 1, // Monday is the first day of the week.
            doy : 4  // The week that contains Jan 4th is the first week of the year.
        }
    });
}));
// moment.js language configuration
// language : euskara (eu)
// author : Eneko Illarramendi : https://github.com/eillarra

(function (factory) {
    factory(moment);
}(function (moment) {
    return moment.lang('eu', {
        months : "urtarrila_otsaila_martxoa_apirila_maiatza_ekaina_uztaila_abuztua_iraila_urria_azaroa_abendua".split("_"),
        monthsShort : "urt._ots._mar._api._mai._eka._uzt._abu._ira._urr._aza._abe.".split("_"),
        weekdays : "igandea_astelehena_asteartea_asteazkena_osteguna_ostirala_larunbata".split("_"),
        weekdaysShort : "ig._al._ar._az._og._ol._lr.".split("_"),
        weekdaysMin : "ig_al_ar_az_og_ol_lr".split("_"),
        longDateFormat : {
            LT : "HH:mm",
            L : "YYYY-MM-DD",
            LL : "YYYY[ko] MMMM[ren] D[a]",
            LLL : "YYYY[ko] MMMM[ren] D[a] LT",
            LLLL : "dddd, YYYY[ko] MMMM[ren] D[a] LT",
            l : "YYYY-M-D",
            ll : "YYYY[ko] MMM D[a]",
            lll : "YYYY[ko] MMM D[a] LT",
            llll : "ddd, YYYY[ko] MMM D[a] LT"
        },
        calendar : {
            sameDay : '[gaur] LT[etan]',
            nextDay : '[bihar] LT[etan]',
            nextWeek : 'dddd LT[etan]',
            lastDay : '[atzo] LT[etan]',
            lastWeek : '[aurreko] dddd LT[etan]',
            sameElse : 'L'
        },
        relativeTime : {
            future : "%s barru",
            past : "duela %s",
            s : "segundo batzuk",
            m : "minutu bat",
            mm : "%d minutu",
            h : "ordu bat",
            hh : "%d ordu",
            d : "egun bat",
            dd : "%d egun",
            M : "hilabete bat",
            MM : "%d hilabete",
            y : "urte bat",
            yy : "%d urte"
        },
        ordinal : '%d.',
        week : {
            dow : 1, // Monday is the first day of the week.
            doy : 7  // The week that contains Jan 1st is the first week of the year.
        }
    });
}));
// moment.js language configuration
// language : Persian Language
// author : Ebrahim Byagowi : https://github.com/ebraminio

(function (factory) {
    factory(moment);
}(function (moment) {
    var symbolMap = {
        '1': 'Ы±',
        '2': 'ЫІ',
        '3': 'Ыі',
        '4': 'Ыґ',
        '5': 'Ыµ',
        '6': 'Ы¶',
        '7': 'Ы·',
        '8': 'Ыё',
        '9': 'Ы№',
        '0': 'Ы°'
    }, numberMap = {
        'Ы±': '1',
        'ЫІ': '2',
        'Ыі': '3',
        'Ыґ': '4',
        'Ыµ': '5',
        'Ы¶': '6',
        'Ы·': '7',
        'Ыё': '8',
        'Ы№': '9',
        'Ы°': '0'
    };

    return moment.lang('fa', {
        months : 'ЪШ§Щ†Щ€ЫЊЩ‡_ЩЃЩ€Ш±ЫЊЩ‡_Щ…Ш§Ш±Ші_ШўЩ€Ш±ЫЊЩ„_Щ…Щ‡_ЪЩ€Ш¦Щ†_ЪЩ€Ш¦ЫЊЩ‡_Ш§Щ€ШЄ_ШіЩѕШЄШ§Щ…ШЁШ±_Ш§Ъ©ШЄШЁШ±_Щ†Щ€Ш§Щ…ШЁШ±_ШЇШіШ§Щ…ШЁШ±'.split('_'),
        monthsShort : 'ЪШ§Щ†Щ€ЫЊЩ‡_ЩЃЩ€Ш±ЫЊЩ‡_Щ…Ш§Ш±Ші_ШўЩ€Ш±ЫЊЩ„_Щ…Щ‡_ЪЩ€Ш¦Щ†_ЪЩ€Ш¦ЫЊЩ‡_Ш§Щ€ШЄ_ШіЩѕШЄШ§Щ…ШЁШ±_Ш§Ъ©ШЄШЁШ±_Щ†Щ€Ш§Щ…ШЁШ±_ШЇШіШ§Щ…ШЁШ±'.split('_'),
        weekdays : 'ЫЊЪ©\u200cШґЩ†ШЁЩ‡_ШЇЩ€ШґЩ†ШЁЩ‡_ШіЩ‡\u200cШґЩ†ШЁЩ‡_Ъ†Щ‡Ш§Ш±ШґЩ†ШЁЩ‡_ЩѕЩ†Ш¬\u200cШґЩ†ШЁЩ‡_Ш¬Щ…Ш№Щ‡_ШґЩ†ШЁЩ‡'.split('_'),
        weekdaysShort : 'ЫЊЪ©\u200cШґЩ†ШЁЩ‡_ШЇЩ€ШґЩ†ШЁЩ‡_ШіЩ‡\u200cШґЩ†ШЁЩ‡_Ъ†Щ‡Ш§Ш±ШґЩ†ШЁЩ‡_ЩѕЩ†Ш¬\u200cШґЩ†ШЁЩ‡_Ш¬Щ…Ш№Щ‡_ШґЩ†ШЁЩ‡'.split('_'),
        weekdaysMin : 'ЫЊ_ШЇ_Ші_Ъ†_Щѕ_Ш¬_Шґ'.split('_'),
        longDateFormat : {
            LT : 'HH:mm',
            L : 'DD/MM/YYYY',
            LL : 'D MMMM YYYY',
            LLL : 'D MMMM YYYY LT',
            LLLL : 'dddd, D MMMM YYYY LT'
        },
        meridiem : function (hour, minute, isLower) {
            if (hour < 12) {
                return "Щ‚ШЁЩ„ Ш§ШІ ШёЩ‡Ш±";
            } else {
                return "ШЁШ№ШЇ Ш§ШІ ШёЩ‡Ш±";
            }
        },
        calendar : {
            sameDay : '[Ш§Щ…Ш±Щ€ШІ ШіШ§Ш№ШЄ] LT',
            nextDay : '[ЩЃШ±ШЇШ§ ШіШ§Ш№ШЄ] LT',
            nextWeek : 'dddd [ШіШ§Ш№ШЄ] LT',
            lastDay : '[ШЇЫЊШ±Щ€ШІ ШіШ§Ш№ШЄ] LT',
            lastWeek : 'dddd [ЩѕЫЊШґ] [ШіШ§Ш№ШЄ] LT',
            sameElse : 'L'
        },
        relativeTime : {
            future : 'ШЇШ± %s',
            past : '%s ЩѕЫЊШґ',
            s : 'Ъ†Щ†ШЇЫЊЩ† Ш«Ш§Щ†ЫЊЩ‡',
            m : 'ЫЊЪ© ШЇЩ‚ЫЊЩ‚Щ‡',
            mm : '%d ШЇЩ‚ЫЊЩ‚Щ‡',
            h : 'ЫЊЪ© ШіШ§Ш№ШЄ',
            hh : '%d ШіШ§Ш№ШЄ',
            d : 'ЫЊЪ© Ш±Щ€ШІ',
            dd : '%d Ш±Щ€ШІ',
            M : 'ЫЊЪ© Щ…Ш§Щ‡',
            MM : '%d Щ…Ш§Щ‡',
            y : 'ЫЊЪ© ШіШ§Щ„',
            yy : '%d ШіШ§Щ„'
        },
        preparse: function (string) {
            return string.replace(/[Ы°-Ы№]/g, function (match) {
                return numberMap[match];
            }).replace(/ШЊ/g, ',');
        },
        postformat: function (string) {
            return string.replace(/\d/g, function (match) {
                return symbolMap[match];
            }).replace(/,/g, 'ШЊ');
        },
        ordinal : '%dЩ…',
        week : {
            dow : 6, // Saturday is the first day of the week.
            doy : 12 // The week that contains Jan 1st is the first week of the year.
        }
    });
}));
// moment.js language configuration
// language : finnish (fi)
// author : Tarmo Aidantausta : https://github.com/bleadof

(function (factory) {
    factory(moment);
}(function (moment) {
    var numbers_past = 'nolla yksi kaksi kolme neljГ¤ viisi kuusi seitsemГ¤n kahdeksan yhdeksГ¤n'.split(' '),
        numbers_future = ['nolla', 'yhden', 'kahden', 'kolmen', 'neljГ¤n', 'viiden', 'kuuden',
                          numbers_past[7], numbers_past[8], numbers_past[9]];

    function translate(number, withoutSuffix, key, isFuture) {
        var result = "";
        switch (key) {
        case 's':
            return isFuture ? 'muutaman sekunnin' : 'muutama sekunti';
        case 'm':
            return isFuture ? 'minuutin' : 'minuutti';
        case 'mm':
            result = isFuture ? 'minuutin' : 'minuuttia';
            break;
        case 'h':
            return isFuture ? 'tunnin' : 'tunti';
        case 'hh':
            result = isFuture ? 'tunnin' : 'tuntia';
            break;
        case 'd':
            return isFuture ? 'pГ¤ivГ¤n' : 'pГ¤ivГ¤';
        case 'dd':
            result = isFuture ? 'pГ¤ivГ¤n' : 'pГ¤ivГ¤Г¤';
            break;
        case 'M':
            return isFuture ? 'kuukauden' : 'kuukausi';
        case 'MM':
            result = isFuture ? 'kuukauden' : 'kuukautta';
            break;
        case 'y':
            return isFuture ? 'vuoden' : 'vuosi';
        case 'yy':
            result = isFuture ? 'vuoden' : 'vuotta';
            break;
        }
        result = verbal_number(number, isFuture) + " " + result;
        return result;
    }

    function verbal_number(number, isFuture) {
        return number < 10 ? (isFuture ? numbers_future[number] : numbers_past[number]) : number;
    }

    return moment.lang('fi', {
        months : "tammikuu_helmikuu_maaliskuu_huhtikuu_toukokuu_kesГ¤kuu_heinГ¤kuu_elokuu_syyskuu_lokakuu_marraskuu_joulukuu".split("_"),
        monthsShort : "tammi_helmi_maalis_huhti_touko_kesГ¤_heinГ¤_elo_syys_loka_marras_joulu".split("_"),
        weekdays : "sunnuntai_maanantai_tiistai_keskiviikko_torstai_perjantai_lauantai".split("_"),
        weekdaysShort : "su_ma_ti_ke_to_pe_la".split("_"),
        weekdaysMin : "su_ma_ti_ke_to_pe_la".split("_"),
        longDateFormat : {
            LT : "HH.mm",
            L : "DD.MM.YYYY",
            LL : "Do MMMM[ta] YYYY",
            LLL : "Do MMMM[ta] YYYY, [klo] LT",
            LLLL : "dddd, Do MMMM[ta] YYYY, [klo] LT",
            l : "D.M.YYYY",
            ll : "Do MMM YYYY",
            lll : "Do MMM YYYY, [klo] LT",
            llll : "ddd, Do MMM YYYY, [klo] LT"
        },
        calendar : {
            sameDay : '[tГ¤nГ¤Г¤n] [klo] LT',
            nextDay : '[huomenna] [klo] LT',
            nextWeek : 'dddd [klo] LT',
            lastDay : '[eilen] [klo] LT',
            lastWeek : '[viime] dddd[na] [klo] LT',
            sameElse : 'L'
        },
        relativeTime : {
            future : "%s pГ¤Г¤stГ¤",
            past : "%s sitten",
            s : translate,
            m : translate,
            mm : translate,
            h : translate,
            hh : translate,
            d : translate,
            dd : translate,
            M : translate,
            MM : translate,
            y : translate,
            yy : translate
        },
        ordinal : "%d.",
        week : {
            dow : 1, // Monday is the first day of the week.
            doy : 4  // The week that contains Jan 4th is the first week of the year.
        }
    });
}));
// moment.js language configuration
// language : faroese (fo)
// author : Ragnar Johannesen : https://github.com/ragnar123

(function (factory) {
    factory(moment);
}(function (moment) {
    return moment.lang('fo', {
        months : "januar_februar_mars_aprГ­l_mai_juni_juli_august_september_oktober_november_desember".split("_"),
        monthsShort : "jan_feb_mar_apr_mai_jun_jul_aug_sep_okt_nov_des".split("_"),
        weekdays : "sunnudagur_mГЎnadagur_tГЅsdagur_mikudagur_hГіsdagur_frГ­ggjadagur_leygardagur".split("_"),
        weekdaysShort : "sun_mГЎn_tГЅs_mik_hГіs_frГ­_ley".split("_"),
        weekdaysMin : "su_mГЎ_tГЅ_mi_hГі_fr_le".split("_"),
        longDateFormat : {
            LT : "HH:mm",
            L : "DD/MM/YYYY",
            LL : "D MMMM YYYY",
            LLL : "D MMMM YYYY LT",
            LLLL : "dddd D. MMMM, YYYY LT"
        },
        calendar : {
            sameDay : '[ГЌ dag kl.] LT',
            nextDay : '[ГЌ morgin kl.] LT',
            nextWeek : 'dddd [kl.] LT',
            lastDay : '[ГЌ gjГЎr kl.] LT',
            lastWeek : '[sГ­Г°stu] dddd [kl] LT',
            sameElse : 'L'
        },
        relativeTime : {
            future : "um %s",
            past : "%s sГ­Г°ani",
            s : "fГЎ sekund",
            m : "ein minutt",
            mm : "%d minuttir",
            h : "ein tГ­mi",
            hh : "%d tГ­mar",
            d : "ein dagur",
            dd : "%d dagar",
            M : "ein mГЎnaГ°i",
            MM : "%d mГЎnaГ°ir",
            y : "eitt ГЎr",
            yy : "%d ГЎr"
        },
        ordinal : '%d.',
        week : {
            dow : 1, // Monday is the first day of the week.
            doy : 4  // The week that contains Jan 4th is the first week of the year.
        }
    });
}));
// moment.js language configuration
// language : canadian french (fr-ca)
// author : Jonathan Abourbih : https://github.com/jonbca

(function (factory) {
    factory(moment);
}(function (moment) {
    return moment.lang('fr-ca', {
        months : "janvier_fГ©vrier_mars_avril_mai_juin_juillet_aoГ»t_septembre_octobre_novembre_dГ©cembre".split("_"),
        monthsShort : "janv._fГ©vr._mars_avr._mai_juin_juil._aoГ»t_sept._oct._nov._dГ©c.".split("_"),
        weekdays : "dimanche_lundi_mardi_mercredi_jeudi_vendredi_samedi".split("_"),
        weekdaysShort : "dim._lun._mar._mer._jeu._ven._sam.".split("_"),
        weekdaysMin : "Di_Lu_Ma_Me_Je_Ve_Sa".split("_"),
        longDateFormat : {
            LT : "HH:mm",
            L : "YYYY-MM-DD",
            LL : "D MMMM YYYY",
            LLL : "D MMMM YYYY LT",
            LLLL : "dddd D MMMM YYYY LT"
        },
        calendar : {
            sameDay: "[Aujourd'hui Г ] LT",
            nextDay: '[Demain Г ] LT',
            nextWeek: 'dddd [Г ] LT',
            lastDay: '[Hier Г ] LT',
            lastWeek: 'dddd [dernier Г ] LT',
            sameElse: 'L'
        },
        relativeTime : {
            future : "dans %s",
            past : "il y a %s",
            s : "quelques secondes",
            m : "une minute",
            mm : "%d minutes",
            h : "une heure",
            hh : "%d heures",
            d : "un jour",
            dd : "%d jours",
            M : "un mois",
            MM : "%d mois",
            y : "un an",
            yy : "%d ans"
        },
        ordinal : function (number) {
            return number + (number === 1 ? 'er' : '');
        }
    });
}));
// moment.js language configuration
// language : french (fr)
// author : John Fischer : https://github.com/jfroffice

(function (factory) {
    factory(moment);
}(function (moment) {
    return moment.lang('fr', {
        months : "janvier_fГ©vrier_mars_avril_mai_juin_juillet_aoГ»t_septembre_octobre_novembre_dГ©cembre".split("_"),
        monthsShort : "janv._fГ©vr._mars_avr._mai_juin_juil._aoГ»t_sept._oct._nov._dГ©c.".split("_"),
        weekdays : "dimanche_lundi_mardi_mercredi_jeudi_vendredi_samedi".split("_"),
        weekdaysShort : "dim._lun._mar._mer._jeu._ven._sam.".split("_"),
        weekdaysMin : "Di_Lu_Ma_Me_Je_Ve_Sa".split("_"),
        longDateFormat : {
            LT : "HH:mm",
            L : "DD/MM/YYYY",
            LL : "D MMMM YYYY",
            LLL : "D MMMM YYYY LT",
            LLLL : "dddd D MMMM YYYY LT"
        },
        calendar : {
            sameDay: "[Aujourd'hui Г ] LT",
            nextDay: '[Demain Г ] LT',
            nextWeek: 'dddd [Г ] LT',
            lastDay: '[Hier Г ] LT',
            lastWeek: 'dddd [dernier Г ] LT',
            sameElse: 'L'
        },
        relativeTime : {
            future : "dans %s",
            past : "il y a %s",
            s : "quelques secondes",
            m : "une minute",
            mm : "%d minutes",
            h : "une heure",
            hh : "%d heures",
            d : "un jour",
            dd : "%d jours",
            M : "un mois",
            MM : "%d mois",
            y : "un an",
            yy : "%d ans"
        },
        ordinal : function (number) {
            return number + (number === 1 ? 'er' : '');
        },
        week : {
            dow : 1, // Monday is the first day of the week.
            doy : 4  // The week that contains Jan 4th is the first week of the year.
        }
    });
}));
// moment.js language configuration
// language : galician (gl)
// author : Juan G. Hurtado : https://github.com/juanghurtado

(function (factory) {
    factory(moment);
}(function (moment) {
    return moment.lang('gl', {
        months : "Xaneiro_Febreiro_Marzo_Abril_Maio_XuГ±o_Xullo_Agosto_Setembro_Outubro_Novembro_Decembro".split("_"),
        monthsShort : "Xan._Feb._Mar._Abr._Mai._XuГ±._Xul._Ago._Set._Out._Nov._Dec.".split("_"),
        weekdays : "Domingo_Luns_Martes_MГ©rcores_Xoves_Venres_SГЎbado".split("_"),
        weekdaysShort : "Dom._Lun._Mar._MГ©r._Xov._Ven._SГЎb.".split("_"),
        weekdaysMin : "Do_Lu_Ma_MГ©_Xo_Ve_SГЎ".split("_"),
        longDateFormat : {
            LT : "H:mm",
            L : "DD/MM/YYYY",
            LL : "D MMMM YYYY",
            LLL : "D MMMM YYYY LT",
            LLLL : "dddd D MMMM YYYY LT"
        },
        calendar : {
            sameDay : function () {
                return '[hoxe ' + ((this.hours() !== 1) ? 'ГЎs' : 'ГЎ') + '] LT';
            },
            nextDay : function () {
                return '[maГ±ГЎ ' + ((this.hours() !== 1) ? 'ГЎs' : 'ГЎ') + '] LT';
            },
            nextWeek : function () {
                return 'dddd [' + ((this.hours() !== 1) ? 'ГЎs' : 'a') + '] LT';
            },
            lastDay : function () {
                return '[onte ' + ((this.hours() !== 1) ? 'ГЎ' : 'a') + '] LT';
            },
            lastWeek : function () {
                return '[o] dddd [pasado ' + ((this.hours() !== 1) ? 'ГЎs' : 'a') + '] LT';
            },
            sameElse : 'L'
        },
        relativeTime : {
            future : function (str) {
                if (str === "uns segundos") {
                    return "nuns segundos";
                }
                return "en " + str;
            },
            past : "hai %s",
            s : "uns segundos",
            m : "un minuto",
            mm : "%d minutos",
            h : "unha hora",
            hh : "%d horas",
            d : "un dГ­a",
            dd : "%d dГ­as",
            M : "un mes",
            MM : "%d meses",
            y : "un ano",
            yy : "%d anos"
        },
        ordinal : '%dВє',
        week : {
            dow : 1, // Monday is the first day of the week.
            doy : 7  // The week that contains Jan 1st is the first week of the year.
        }
    });
}));
// moment.js language configuration
// language : Hebrew (he)
// author : Tomer Cohen : https://github.com/tomer
// author : Moshe Simantov : https://github.com/DevelopmentIL
// author : Tal Ater : https://github.com/TalAter

(function (factory) {
    factory(moment);
}(function (moment) {
    return moment.lang('he', {
        months : "Ч™Ч Ч•ЧђЧЁ_Ч¤Ч‘ЧЁЧ•ЧђЧЁ_ЧћЧЁЧҐ_ЧђЧ¤ЧЁЧ™Чњ_ЧћЧђЧ™_Ч™Ч•Ч Ч™_Ч™Ч•ЧњЧ™_ЧђЧ•Ч’Ч•ЧЎЧ_ЧЎЧ¤ЧЧћЧ‘ЧЁ_ЧђЧ•Ч§ЧЧ•Ч‘ЧЁ_Ч Ч•Ч‘ЧћЧ‘ЧЁ_Ч“Ч¦ЧћЧ‘ЧЁ".split("_"),
        monthsShort : "Ч™Ч Ч•Чі_Ч¤Ч‘ЧЁЧі_ЧћЧЁЧҐ_ЧђЧ¤ЧЁЧі_ЧћЧђЧ™_Ч™Ч•Ч Ч™_Ч™Ч•ЧњЧ™_ЧђЧ•Ч’Чі_ЧЎЧ¤ЧЧі_ЧђЧ•Ч§Чі_Ч Ч•Ч‘Чі_Ч“Ч¦ЧћЧі".split("_"),
        weekdays : "ЧЁЧђЧ©Ч•Чџ_Ч©Ч Ч™_Ч©ЧњЧ™Ч©Ч™_ЧЁЧ‘Ч™ЧўЧ™_Ч—ЧћЧ™Ч©Ч™_Ч©Ч™Ч©Ч™_Ч©Ч‘ЧЄ".split("_"),
        weekdaysShort : "ЧђЧі_Ч‘Чі_Ч’Чі_Ч“Чі_Ч”Чі_Ч•Чі_Ч©Чі".split("_"),
        weekdaysMin : "Чђ_Ч‘_Ч’_Ч“_Ч”_Ч•_Ч©".split("_"),
        longDateFormat : {
            LT : "HH:mm",
            L : "DD/MM/YYYY",
            LL : "D [Ч‘]MMMM YYYY",
            LLL : "D [Ч‘]MMMM YYYY LT",
            LLLL : "dddd, D [Ч‘]MMMM YYYY LT",
            l : "D/M/YYYY",
            ll : "D MMM YYYY",
            lll : "D MMM YYYY LT",
            llll : "ddd, D MMM YYYY LT"
        },
        calendar : {
            sameDay : '[Ч”Ч™Ч•Чќ Ч‘Цѕ]LT',
            nextDay : '[ЧћЧ—ЧЁ Ч‘Цѕ]LT',
            nextWeek : 'dddd [Ч‘Ч©ЧўЧ”] LT',
            lastDay : '[ЧђЧЄЧћЧ•Чњ Ч‘Цѕ]LT',
            lastWeek : '[Ч‘Ч™Ч•Чќ] dddd [Ч”ЧђЧ—ЧЁЧ•Чџ Ч‘Ч©ЧўЧ”] LT',
            sameElse : 'L'
        },
        relativeTime : {
            future : "Ч‘ЧўЧ•Ч“ %s",
            past : "ЧњЧ¤Ч Ч™ %s",
            s : "ЧћЧЎЧ¤ЧЁ Ч©Ч Ч™Ч•ЧЄ",
            m : "Ч“Ч§Ч”",
            mm : "%d Ч“Ч§Ч•ЧЄ",
            h : "Ч©ЧўЧ”",
            hh : function (number) {
                if (number === 2) {
                    return "Ч©ЧўЧЄЧ™Ч™Чќ";
                }
                return number + " Ч©ЧўЧ•ЧЄ";
            },
            d : "Ч™Ч•Чќ",
            dd : function (number) {
                if (number === 2) {
                    return "Ч™Ч•ЧћЧ™Ч™Чќ";
                }
                return number + " Ч™ЧћЧ™Чќ";
            },
            M : "Ч—Ч•Ч“Ч©",
            MM : function (number) {
                if (number === 2) {
                    return "Ч—Ч•Ч“Ч©Ч™Ч™Чќ";
                }
                return number + " Ч—Ч•Ч“Ч©Ч™Чќ";
            },
            y : "Ч©Ч Ч”",
            yy : function (number) {
                if (number === 2) {
                    return "Ч©Ч ЧЄЧ™Ч™Чќ";
                }
                return number + " Ч©Ч Ч™Чќ";
            }
        }
    });
}));
// moment.js language configuration
// language : hindi (hi)
// author : Mayank Singhal : https://github.com/mayanksinghal

(function (factory) {
    factory(moment);
}(function (moment) {
    var symbolMap = {
        '1': 'аҐ§',
        '2': 'аҐЁ',
        '3': 'аҐ©',
        '4': 'аҐЄ',
        '5': 'аҐ«',
        '6': 'аҐ¬',
        '7': 'аҐ­',
        '8': 'аҐ®',
        '9': 'аҐЇ',
        '0': 'аҐ¦'
    },
    numberMap = {
        'аҐ§': '1',
        'аҐЁ': '2',
        'аҐ©': '3',
        'аҐЄ': '4',
        'аҐ«': '5',
        'аҐ¬': '6',
        'аҐ­': '7',
        'аҐ®': '8',
        'аҐЇ': '9',
        'аҐ¦': '0'
    };

    return moment.lang('hi', {
        months : 'а¤ња¤Ёа¤µа¤°аҐЂ_а¤«а¤ја¤°а¤µа¤°аҐЂ_а¤®а¤ѕа¤°аҐЌа¤љ_а¤…а¤ЄаҐЌа¤°аҐ€а¤І_а¤®а¤€_а¤њаҐ‚а¤Ё_а¤њаҐЃа¤Іа¤ѕа¤€_а¤…а¤—а¤ёаҐЌа¤¤_а¤ёа¤їа¤¤а¤®аҐЌа¤¬а¤°_а¤…а¤•аҐЌа¤џаҐ‚а¤¬а¤°_а¤Ёа¤µа¤®аҐЌа¤¬а¤°_а¤¦а¤їа¤ёа¤®аҐЌа¤¬а¤°'.split("_"),
        monthsShort : 'а¤ња¤Ё._а¤«а¤ја¤°._а¤®а¤ѕа¤°аҐЌа¤љ_а¤…а¤ЄаҐЌа¤°аҐ€._а¤®а¤€_а¤њаҐ‚а¤Ё_а¤њаҐЃа¤І._а¤…а¤—._а¤ёа¤їа¤¤._а¤…а¤•аҐЌа¤џаҐ‚._а¤Ёа¤µ._а¤¦а¤їа¤ё.'.split("_"),
        weekdays : 'а¤°а¤µа¤їа¤µа¤ѕа¤°_а¤ёаҐ‹а¤®а¤µа¤ѕа¤°_а¤®а¤‚а¤—а¤Іа¤µа¤ѕа¤°_а¤¬аҐЃа¤§а¤µа¤ѕа¤°_а¤—аҐЃа¤°аҐ‚а¤µа¤ѕа¤°_а¤¶аҐЃа¤•аҐЌа¤°а¤µа¤ѕа¤°_а¤¶а¤Ёа¤їа¤µа¤ѕа¤°'.split("_"),
        weekdaysShort : 'а¤°а¤µа¤ї_а¤ёаҐ‹а¤®_а¤®а¤‚а¤—а¤І_а¤¬аҐЃа¤§_а¤—аҐЃа¤°аҐ‚_а¤¶аҐЃа¤•аҐЌа¤°_а¤¶а¤Ёа¤ї'.split("_"),
        weekdaysMin : 'а¤°_а¤ёаҐ‹_а¤®а¤‚_а¤¬аҐЃ_а¤—аҐЃ_а¤¶аҐЃ_а¤¶'.split("_"),
        longDateFormat : {
            LT : "A h:mm а¤¬а¤њаҐ‡",
            L : "DD/MM/YYYY",
            LL : "D MMMM YYYY",
            LLL : "D MMMM YYYY, LT",
            LLLL : "dddd, D MMMM YYYY, LT"
        },
        calendar : {
            sameDay : '[а¤†а¤њ] LT',
            nextDay : '[а¤•а¤І] LT',
            nextWeek : 'dddd, LT',
            lastDay : '[а¤•а¤І] LT',
            lastWeek : '[а¤Єа¤їа¤›а¤ІаҐ‡] dddd, LT',
            sameElse : 'L'
        },
        relativeTime : {
            future : "%s а¤®аҐ‡а¤‚",
            past : "%s а¤Єа¤№а¤ІаҐ‡",
            s : "а¤•аҐЃа¤› а¤№аҐЂ а¤•аҐЌа¤·а¤Ј",
            m : "а¤Џа¤• а¤®а¤їа¤Ёа¤џ",
            mm : "%d а¤®а¤їа¤Ёа¤џ",
            h : "а¤Џа¤• а¤а¤‚а¤џа¤ѕ",
            hh : "%d а¤а¤‚а¤џаҐ‡",
            d : "а¤Џа¤• а¤¦а¤їа¤Ё",
            dd : "%d а¤¦а¤їа¤Ё",
            M : "а¤Џа¤• а¤®а¤№аҐЂа¤ЁаҐ‡",
            MM : "%d а¤®а¤№аҐЂа¤ЁаҐ‡",
            y : "а¤Џа¤• а¤µа¤°аҐЌа¤·",
            yy : "%d а¤µа¤°аҐЌа¤·"
        },
        preparse: function (string) {
            return string.replace(/[аҐ§аҐЁаҐ©аҐЄаҐ«аҐ¬аҐ­аҐ®аҐЇаҐ¦]/g, function (match) {
                return numberMap[match];
            });
        },
        postformat: function (string) {
            return string.replace(/\d/g, function (match) {
                return symbolMap[match];
            });
        },
        // Hindi notation for meridiems are quite fuzzy in practice. While there exists
        // a rigid notion of a 'Pahar' it is not used as rigidly in modern Hindi.
        meridiem : function (hour, minute, isLower) {
            if (hour < 4) {
                return "а¤°а¤ѕа¤¤";
            } else if (hour < 10) {
                return "а¤ёаҐЃа¤¬а¤№";
            } else if (hour < 17) {
                return "а¤¦аҐ‹а¤Єа¤№а¤°";
            } else if (hour < 20) {
                return "а¤¶а¤ѕа¤®";
            } else {
                return "а¤°а¤ѕа¤¤";
            }
        },
        week : {
            dow : 0, // Sunday is the first day of the week.
            doy : 6  // The week that contains Jan 1st is the first week of the year.
        }
    });
}));
// moment.js language configuration
// language : hrvatski (hr)
// author : Bojan MarkoviД‡ : https://github.com/bmarkovic

// based on (sl) translation by Robert SedovЕЎek

(function (factory) {
    factory(moment);
}(function (moment) {

    function translate(number, withoutSuffix, key) {
        var result = number + " ";
        switch (key) {
        case 'm':
            return withoutSuffix ? 'jedna minuta' : 'jedne minute';
        case 'mm':
            if (number === 1) {
                result += 'minuta';
            } else if (number === 2 || number === 3 || number === 4) {
                result += 'minute';
            } else {
                result += 'minuta';
            }
            return result;
        case 'h':
            return withoutSuffix ? 'jedan sat' : 'jednog sata';
        case 'hh':
            if (number === 1) {
                result += 'sat';
            } else if (number === 2 || number === 3 || number === 4) {
                result += 'sata';
            } else {
                result += 'sati';
            }
            return result;
        case 'dd':
            if (number === 1) {
                result += 'dan';
            } else {
                result += 'dana';
            }
            return result;
        case 'MM':
            if (number === 1) {
                result += 'mjesec';
            } else if (number === 2 || number === 3 || number === 4) {
                result += 'mjeseca';
            } else {
                result += 'mjeseci';
            }
            return result;
        case 'yy':
            if (number === 1) {
                result += 'godina';
            } else if (number === 2 || number === 3 || number === 4) {
                result += 'godine';
            } else {
                result += 'godina';
            }
            return result;
        }
    }

    return moment.lang('hr', {
        months : "sjeДЌanj_veljaДЌa_oЕѕujak_travanj_svibanj_lipanj_srpanj_kolovoz_rujan_listopad_studeni_prosinac".split("_"),
        monthsShort : "sje._vel._oЕѕu._tra._svi._lip._srp._kol._ruj._lis._stu._pro.".split("_"),
        weekdays : "nedjelja_ponedjeljak_utorak_srijeda_ДЌetvrtak_petak_subota".split("_"),
        weekdaysShort : "ned._pon._uto._sri._ДЌet._pet._sub.".split("_"),
        weekdaysMin : "ne_po_ut_sr_ДЌe_pe_su".split("_"),
        longDateFormat : {
            LT : "H:mm",
            L : "DD. MM. YYYY",
            LL : "D. MMMM YYYY",
            LLL : "D. MMMM YYYY LT",
            LLLL : "dddd, D. MMMM YYYY LT"
        },
        calendar : {
            sameDay  : '[danas u] LT',
            nextDay  : '[sutra u] LT',

            nextWeek : function () {
                switch (this.day()) {
                case 0:
                    return '[u] [nedjelju] [u] LT';
                case 3:
                    return '[u] [srijedu] [u] LT';
                case 6:
                    return '[u] [subotu] [u] LT';
                case 1:
                case 2:
                case 4:
                case 5:
                    return '[u] dddd [u] LT';
                }
            },
            lastDay  : '[juДЌer u] LT',
            lastWeek : function () {
                switch (this.day()) {
                case 0:
                case 3:
                    return '[proЕЎlu] dddd [u] LT';
                case 6:
                    return '[proЕЎle] [subote] [u] LT';
                case 1:
                case 2:
                case 4:
                case 5:
                    return '[proЕЎli] dddd [u] LT';
                }
            },
            sameElse : 'L'
        },
        relativeTime : {
            future : "za %s",
            past   : "prije %s",
            s      : "par sekundi",
            m      : translate,
            mm     : translate,
            h      : translate,
            hh     : translate,
            d      : "dan",
            dd     : translate,
            M      : "mjesec",
            MM     : translate,
            y      : "godinu",
            yy     : translate
        },
        ordinal : '%d.',
        week : {
            dow : 1, // Monday is the first day of the week.
            doy : 7  // The week that contains Jan 1st is the first week of the year.
        }
    });
}));
// moment.js language configuration
// language : hungarian (hu)
// author : Adam Brunner : https://github.com/adambrunner

(function (factory) {
    factory(moment);
}(function (moment) {
    var weekEndings = 'vasГЎrnap hГ©tfЕ‘n kedden szerdГЎn csГјtГ¶rtГ¶kГ¶n pГ©nteken szombaton'.split(' ');

    function translate(number, withoutSuffix, key, isFuture) {
        var num = number,
            suffix;

        switch (key) {
        case 's':
            return (isFuture || withoutSuffix) ? 'nГ©hГЎny mГЎsodperc' : 'nГ©hГЎny mГЎsodperce';
        case 'm':
            return 'egy' + (isFuture || withoutSuffix ? ' perc' : ' perce');
        case 'mm':
            return num + (isFuture || withoutSuffix ? ' perc' : ' perce');
        case 'h':
            return 'egy' + (isFuture || withoutSuffix ? ' Гіra' : ' ГіrГЎja');
        case 'hh':
            return num + (isFuture || withoutSuffix ? ' Гіra' : ' ГіrГЎja');
        case 'd':
            return 'egy' + (isFuture || withoutSuffix ? ' nap' : ' napja');
        case 'dd':
            return num + (isFuture || withoutSuffix ? ' nap' : ' napja');
        case 'M':
            return 'egy' + (isFuture || withoutSuffix ? ' hГіnap' : ' hГіnapja');
        case 'MM':
            return num + (isFuture || withoutSuffix ? ' hГіnap' : ' hГіnapja');
        case 'y':
            return 'egy' + (isFuture || withoutSuffix ? ' Г©v' : ' Г©ve');
        case 'yy':
            return num + (isFuture || withoutSuffix ? ' Г©v' : ' Г©ve');
        }

        return '';
    }

    function week(isFuture) {
        return (isFuture ? '' : '[mГєlt] ') + '[' + weekEndings[this.day()] + '] LT[-kor]';
    }

    return moment.lang('hu', {
        months : "januГЎr_februГЎr_mГЎrcius_ГЎprilis_mГЎjus_jГєnius_jГєlius_augusztus_szeptember_oktГіber_november_december".split("_"),
        monthsShort : "jan_feb_mГЎrc_ГЎpr_mГЎj_jГєn_jГєl_aug_szept_okt_nov_dec".split("_"),
        weekdays : "vasГЎrnap_hГ©tfЕ‘_kedd_szerda_csГјtГ¶rtГ¶k_pГ©ntek_szombat".split("_"),
        weekdaysShort : "vas_hГ©t_kedd_sze_csГјt_pГ©n_szo".split("_"),
        weekdaysMin : "v_h_k_sze_cs_p_szo".split("_"),
        longDateFormat : {
            LT : "H:mm",
            L : "YYYY.MM.DD.",
            LL : "YYYY. MMMM D.",
            LLL : "YYYY. MMMM D., LT",
            LLLL : "YYYY. MMMM D., dddd LT"
        },
        calendar : {
            sameDay : '[ma] LT[-kor]',
            nextDay : '[holnap] LT[-kor]',
            nextWeek : function () {
                return week.call(this, true);
            },
            lastDay : '[tegnap] LT[-kor]',
            lastWeek : function () {
                return week.call(this, false);
            },
            sameElse : 'L'
        },
        relativeTime : {
            future : "%s mГєlva",
            past : "%s",
            s : translate,
            m : translate,
            mm : translate,
            h : translate,
            hh : translate,
            d : translate,
            dd : translate,
            M : translate,
            MM : translate,
            y : translate,
            yy : translate
        },
        ordinal : '%d.',
        week : {
            dow : 1, // Monday is the first day of the week.
            doy : 7  // The week that contains Jan 1st is the first week of the year.
        }
    });
}));
// moment.js language configuration
// language : Armenian (hy-am)
// author : Armendarabyan : https://github.com/armendarabyan

(function (factory) {
    factory(moment);
}(function (moment) {

    function monthsCaseReplace(m, format) {
        var months = {
            'nominative': 'Х°ХёЦ‚Х¶ХѕХЎЦЂ_ЦѓХҐХїЦЂХѕХЎЦЂ_ХґХЎЦЂХї_ХЎХєЦЂХ«Х¬_ХґХЎХµХ«ХЅ_Х°ХёЦ‚Х¶Х«ХЅ_Х°ХёЦ‚Х¬Х«ХЅ_Ц…ХЈХёХЅХїХёХЅ_ХЅХҐХєХїХҐХґХўХҐЦЂ_Х°ХёХЇХїХҐХґХўХҐЦЂ_Х¶ХёХµХҐХґХўХҐЦЂ_Х¤ХҐХЇХїХҐХґХўХҐЦЂ'.split('_'),
            'accusative': 'Х°ХёЦ‚Х¶ХѕХЎЦЂХ«_ЦѓХҐХїЦЂХѕХЎЦЂХ«_ХґХЎЦЂХїХ«_ХЎХєЦЂХ«Х¬Х«_ХґХЎХµХ«ХЅХ«_Х°ХёЦ‚Х¶Х«ХЅХ«_Х°ХёЦ‚Х¬Х«ХЅХ«_Ц…ХЈХёХЅХїХёХЅХ«_ХЅХҐХєХїХҐХґХўХҐЦЂХ«_Х°ХёХЇХїХҐХґХўХҐЦЂХ«_Х¶ХёХµХҐХґХўХҐЦЂХ«_Х¤ХҐХЇХїХҐХґХўХҐЦЂХ«'.split('_')
        },

        nounCase = (/D[oD]?(\[[^\[\]]*\]|\s+)+MMMM?/).test(format) ?
            'accusative' :
            'nominative';

        return months[nounCase][m.month()];
    }

    function monthsShortCaseReplace(m, format) {
        var monthsShort = 'Х°Х¶Хѕ_ЦѓХїЦЂ_ХґЦЂХї_ХЎХєЦЂ_ХґХµХЅ_Х°Х¶ХЅ_Х°Х¬ХЅ_Ц…ХЈХЅ_ХЅХєХї_Х°ХЇХї_Х¶ХґХў_Х¤ХЇХї'.split('_');

        return monthsShort[m.month()];
    }

    function weekdaysCaseReplace(m, format) {
        var weekdays = 'ХЇХ«ЦЂХЎХЇХ«_ХҐЦЂХЇХёЦ‚Х·ХЎХўХ©Х«_ХҐЦЂХҐЦ„Х·ХЎХўХ©Х«_Х№ХёЦЂХҐЦ„Х·ХЎХўХ©Х«_Х°Х«Х¶ХЈХ·ХЎХўХ©Х«_ХёЦ‚ЦЂХўХЎХ©_Х·ХЎХўХЎХ©'.split('_');

        return weekdays[m.day()];
    }

    return moment.lang('hy-am', {
        months : monthsCaseReplace,
        monthsShort : monthsShortCaseReplace,
        weekdays : weekdaysCaseReplace,
        weekdaysShort : "ХЇЦЂХЇ_ХҐЦЂХЇ_ХҐЦЂЦ„_Х№ЦЂЦ„_Х°Х¶ХЈ_ХёЦ‚ЦЂХў_Х·ХўХ©".split("_"),
        weekdaysMin : "ХЇЦЂХЇ_ХҐЦЂХЇ_ХҐЦЂЦ„_Х№ЦЂЦ„_Х°Х¶ХЈ_ХёЦ‚ЦЂХў_Х·ХўХ©".split("_"),
        longDateFormat : {
            LT : "HH:mm",
            L : "DD.MM.YYYY",
            LL : "D MMMM YYYY Х©.",
            LLL : "D MMMM YYYY Х©., LT",
            LLLL : "dddd, D MMMM YYYY Х©., LT"
        },
        calendar : {
            sameDay: '[ХЎХµХЅЦ…ЦЂ] LT',
            nextDay: '[ХѕХЎХІХЁ] LT',
            lastDay: '[ХҐЦЂХҐХЇ] LT',
            nextWeek: function () {
                return 'dddd [Ц…ЦЂХЁ ХЄХЎХґХЁ] LT';
            },
            lastWeek: function () {
                return '[ХЎХ¶ЦЃХЎХ®] dddd [Ц…ЦЂХЁ ХЄХЎХґХЁ] LT';
            },
            sameElse: 'L'
        },
        relativeTime : {
            future : "%s Х°ХҐХїХё",
            past : "%s ХЎХјХЎХ»",
            s : "ХґХ« Ц„ХЎХ¶Х« ХѕХЎХµЦЂХЇХµХЎХ¶",
            m : "ЦЂХёХєХҐ",
            mm : "%d ЦЂХёХєХҐ",
            h : "ХЄХЎХґ",
            hh : "%d ХЄХЎХґ",
            d : "Ц…ЦЂ",
            dd : "%d Ц…ЦЂ",
            M : "ХЎХґХ«ХЅ",
            MM : "%d ХЎХґХ«ХЅ",
            y : "ХїХЎЦЂХ«",
            yy : "%d ХїХЎЦЂХ«"
        },

        meridiem : function (hour) {
            if (hour < 4) {
                return "ХЈХ«Х·ХҐЦЂХѕХЎ";
            } else if (hour < 12) {
                return "ХЎХјХЎХѕХёХїХѕХЎ";
            } else if (hour < 17) {
                return "ЦЃХҐЦЂХҐХЇХѕХЎ";
            } else {
                return "ХҐЦЂХҐХЇХёХµХЎХ¶";
            }
        },

        ordinal: function (number, period) {
            switch (period) {
            case 'DDD':
            case 'w':
            case 'W':
            case 'DDDo':
                if (number === 1) {
                    return number + '-Х«Х¶';
                }
                return number + '-ЦЂХ¤';
            default:
                return number;
            }
        },

        week : {
            dow : 1, // Monday is the first day of the week.
            doy : 7  // The week that contains Jan 1st is the first week of the year.
        }
    });
}));
// moment.js language configuration
// language : Bahasa Indonesia (id)
// author : Mohammad Satrio Utomo : https://github.com/tyok
// reference: http://id.wikisource.org/wiki/Pedoman_Umum_Ejaan_Bahasa_Indonesia_yang_Disempurnakan

(function (factory) {
    factory(moment);
}(function (moment) {
    return moment.lang('id', {
        months : "Januari_Februari_Maret_April_Mei_Juni_Juli_Agustus_September_Oktober_November_Desember".split("_"),
        monthsShort : "Jan_Feb_Mar_Apr_Mei_Jun_Jul_Ags_Sep_Okt_Nov_Des".split("_"),
        weekdays : "Minggu_Senin_Selasa_Rabu_Kamis_Jumat_Sabtu".split("_"),
        weekdaysShort : "Min_Sen_Sel_Rab_Kam_Jum_Sab".split("_"),
        weekdaysMin : "Mg_Sn_Sl_Rb_Km_Jm_Sb".split("_"),
        longDateFormat : {
            LT : "HH.mm",
            L : "DD/MM/YYYY",
            LL : "D MMMM YYYY",
            LLL : "D MMMM YYYY [pukul] LT",
            LLLL : "dddd, D MMMM YYYY [pukul] LT"
        },
        meridiem : function (hours, minutes, isLower) {
            if (hours < 11) {
                return 'pagi';
            } else if (hours < 15) {
                return 'siang';
            } else if (hours < 19) {
                return 'sore';
            } else {
                return 'malam';
            }
        },
        calendar : {
            sameDay : '[Hari ini pukul] LT',
            nextDay : '[Besok pukul] LT',
            nextWeek : 'dddd [pukul] LT',
            lastDay : '[Kemarin pukul] LT',
            lastWeek : 'dddd [lalu pukul] LT',
            sameElse : 'L'
        },
        relativeTime : {
            future : "dalam %s",
            past : "%s yang lalu",
            s : "beberapa detik",
            m : "semenit",
            mm : "%d menit",
            h : "sejam",
            hh : "%d jam",
            d : "sehari",
            dd : "%d hari",
            M : "sebulan",
            MM : "%d bulan",
            y : "setahun",
            yy : "%d tahun"
        },
        week : {
            dow : 1, // Monday is the first day of the week.
            doy : 7  // The week that contains Jan 1st is the first week of the year.
        }
    });
}));
// moment.js language configuration
// language : icelandic (is)
// author : Hinrik Г–rn SigurГ°sson : https://github.com/hinrik

(function (factory) {
    factory(moment);
}(function (moment) {
    function plural(n) {
        if (n % 100 === 11) {
            return true;
        } else if (n % 10 === 1) {
            return false;
        }
        return true;
    }

    function translate(number, withoutSuffix, key, isFuture) {
        var result = number + " ";
        switch (key) {
        case 's':
            return withoutSuffix || isFuture ? 'nokkrar sekГєndur' : 'nokkrum sekГєndum';
        case 'm':
            return withoutSuffix ? 'mГ­nГєta' : 'mГ­nГєtu';
        case 'mm':
            if (plural(number)) {
                return result + (withoutSuffix || isFuture ? 'mГ­nГєtur' : 'mГ­nГєtum');
            } else if (withoutSuffix) {
                return result + 'mГ­nГєta';
            }
            return result + 'mГ­nГєtu';
        case 'hh':
            if (plural(number)) {
                return result + (withoutSuffix || isFuture ? 'klukkustundir' : 'klukkustundum');
            }
            return result + 'klukkustund';
        case 'd':
            if (withoutSuffix) {
                return 'dagur';
            }
            return isFuture ? 'dag' : 'degi';
        case 'dd':
            if (plural(number)) {
                if (withoutSuffix) {
                    return result + 'dagar';
                }
                return result + (isFuture ? 'daga' : 'dГ¶gum');
            } else if (withoutSuffix) {
                return result + 'dagur';
            }
            return result + (isFuture ? 'dag' : 'degi');
        case 'M':
            if (withoutSuffix) {
                return 'mГЎnuГ°ur';
            }
            return isFuture ? 'mГЎnuГ°' : 'mГЎnuГ°i';
        case 'MM':
            if (plural(number)) {
                if (withoutSuffix) {
                    return result + 'mГЎnuГ°ir';
                }
                return result + (isFuture ? 'mГЎnuГ°i' : 'mГЎnuГ°um');
            } else if (withoutSuffix) {
                return result + 'mГЎnuГ°ur';
            }
            return result + (isFuture ? 'mГЎnuГ°' : 'mГЎnuГ°i');
        case 'y':
            return withoutSuffix || isFuture ? 'ГЎr' : 'ГЎri';
        case 'yy':
            if (plural(number)) {
                return result + (withoutSuffix || isFuture ? 'ГЎr' : 'ГЎrum');
            }
            return result + (withoutSuffix || isFuture ? 'ГЎr' : 'ГЎri');
        }
    }

    return moment.lang('is', {
        months : "janГєar_febrГєar_mars_aprГ­l_maГ­_jГєnГ­_jГєlГ­_ГЎgГєst_september_oktГіber_nГіvember_desember".split("_"),
        monthsShort : "jan_feb_mar_apr_maГ­_jГєn_jГєl_ГЎgГє_sep_okt_nГіv_des".split("_"),
        weekdays : "sunnudagur_mГЎnudagur_ГѕriГ°judagur_miГ°vikudagur_fimmtudagur_fГ¶studagur_laugardagur".split("_"),
        weekdaysShort : "sun_mГЎn_Гѕri_miГ°_fim_fГ¶s_lau".split("_"),
        weekdaysMin : "Su_MГЎ_Гћr_Mi_Fi_FГ¶_La".split("_"),
        longDateFormat : {
            LT : "H:mm",
            L : "DD/MM/YYYY",
            LL : "D. MMMM YYYY",
            LLL : "D. MMMM YYYY [kl.] LT",
            LLLL : "dddd, D. MMMM YYYY [kl.] LT"
        },
        calendar : {
            sameDay : '[Г­ dag kl.] LT',
            nextDay : '[ГЎ morgun kl.] LT',
            nextWeek : 'dddd [kl.] LT',
            lastDay : '[Г­ gГ¦r kl.] LT',
            lastWeek : '[sГ­Г°asta] dddd [kl.] LT',
            sameElse : 'L'
        },
        relativeTime : {
            future : "eftir %s",
            past : "fyrir %s sГ­Г°an",
            s : translate,
            m : translate,
            mm : translate,
            h : "klukkustund",
            hh : translate,
            d : translate,
            dd : translate,
            M : translate,
            MM : translate,
            y : translate,
            yy : translate
        },
        ordinal : '%d.',
        week : {
            dow : 1, // Monday is the first day of the week.
            doy : 4  // The week that contains Jan 4th is the first week of the year.
        }
    });
}));
// moment.js language configuration
// language : italian (it)
// author : Lorenzo : https://github.com/aliem
// author: Mattia Larentis: https://github.com/nostalgiaz

(function (factory) {
    factory(moment);
}(function (moment) {
    return moment.lang('it', {
        months : "Gennaio_Febbraio_Marzo_Aprile_Maggio_Giugno_Luglio_Agosto_Settembre_Ottobre_Novembre_Dicembre".split("_"),
        monthsShort : "Gen_Feb_Mar_Apr_Mag_Giu_Lug_Ago_Set_Ott_Nov_Dic".split("_"),
        weekdays : "Domenica_LunedГ¬_MartedГ¬_MercoledГ¬_GiovedГ¬_VenerdГ¬_Sabato".split("_"),
        weekdaysShort : "Dom_Lun_Mar_Mer_Gio_Ven_Sab".split("_"),
        weekdaysMin : "D_L_Ma_Me_G_V_S".split("_"),
        longDateFormat : {
            LT : "HH:mm",
            L : "DD/MM/YYYY",
            LL : "D MMMM YYYY",
            LLL : "D MMMM YYYY LT",
            LLLL : "dddd, D MMMM YYYY LT"
        },
        calendar : {
            sameDay: '[Oggi alle] LT',
            nextDay: '[Domani alle] LT',
            nextWeek: 'dddd [alle] LT',
            lastDay: '[Ieri alle] LT',
            lastWeek: '[lo scorso] dddd [alle] LT',
            sameElse: 'L'
        },
        relativeTime : {
            future : function (s) {
                return ((/^[0-9].+$/).test(s) ? "tra" : "in") + " " + s;
            },
            past : "%s fa",
            s : "alcuni secondi",
            m : "un minuto",
            mm : "%d minuti",
            h : "un'ora",
            hh : "%d ore",
            d : "un giorno",
            dd : "%d giorni",
            M : "un mese",
            MM : "%d mesi",
            y : "un anno",
            yy : "%d anni"
        },
        ordinal: '%dВє',
        week : {
            dow : 1, // Monday is the first day of the week.
            doy : 4  // The week that contains Jan 4th is the first week of the year.
        }
    });
}));
// moment.js language configuration
// language : japanese (ja)
// author : LI Long : https://github.com/baryon

(function (factory) {
    factory(moment);
}(function (moment) {
    return moment.lang('ja', {
        months : "1жњ€_2жњ€_3жњ€_4жњ€_5жњ€_6жњ€_7жњ€_8жњ€_9жњ€_10жњ€_11жњ€_12жњ€".split("_"),
        monthsShort : "1жњ€_2жњ€_3жњ€_4жњ€_5жњ€_6жњ€_7жњ€_8жњ€_9жњ€_10жњ€_11жњ€_12жњ€".split("_"),
        weekdays : "ж—Ґж›њж—Ґ_жњ€ж›њж—Ґ_зЃ«ж›њж—Ґ_ж°ґж›њж—Ґ_жњЁж›њж—Ґ_й‡‘ж›њж—Ґ_ењџж›њж—Ґ".split("_"),
        weekdaysShort : "ж—Ґ_жњ€_зЃ«_ж°ґ_жњЁ_й‡‘_ењџ".split("_"),
        weekdaysMin : "ж—Ґ_жњ€_зЃ«_ж°ґ_жњЁ_й‡‘_ењџ".split("_"),
        longDateFormat : {
            LT : "Ahж™‚mе€†",
            L : "YYYY/MM/DD",
            LL : "YYYYе№ґMжњ€Dж—Ґ",
            LLL : "YYYYе№ґMжњ€Dж—ҐLT",
            LLLL : "YYYYе№ґMжњ€Dж—ҐLT dddd"
        },
        meridiem : function (hour, minute, isLower) {
            if (hour < 12) {
                return "еЌ€е‰Ќ";
            } else {
                return "еЌ€еѕЊ";
            }
        },
        calendar : {
            sameDay : '[д»Љж—Ґ] LT',
            nextDay : '[жЋж—Ґ] LT',
            nextWeek : '[жќҐйЂ±]dddd LT',
            lastDay : '[жЁж—Ґ] LT',
            lastWeek : '[е‰ЌйЂ±]dddd LT',
            sameElse : 'L'
        },
        relativeTime : {
            future : "%sеѕЊ",
            past : "%sе‰Ќ",
            s : "ж•°з§’",
            m : "1е€†",
            mm : "%dе€†",
            h : "1ж™‚й–“",
            hh : "%dж™‚й–“",
            d : "1ж—Ґ",
            dd : "%dж—Ґ",
            M : "1гѓ¶жњ€",
            MM : "%dгѓ¶жњ€",
            y : "1е№ґ",
            yy : "%dе№ґ"
        }
    });
}));
// moment.js language configuration
// language : Georgian (ka)
// author : Irakli Janiashvili : https://github.com/irakli-janiashvili

(function (factory) {
    factory(moment);
}(function (moment) {

    function monthsCaseReplace(m, format) {
        var months = {
            'nominative': 'бѓбѓђбѓњбѓ•бѓђбѓ бѓ_бѓ—бѓ”бѓ‘бѓ”бѓ бѓ•бѓђбѓљбѓ_бѓ›бѓђбѓ бѓўбѓ_бѓђбѓћбѓ бѓбѓљбѓ_бѓ›бѓђбѓбѓЎбѓ_бѓбѓ•бѓњбѓбѓЎбѓ_бѓбѓ•бѓљбѓбѓЎбѓ_бѓђбѓ’бѓ•бѓбѓЎбѓўбѓќ_бѓЎбѓ”бѓҐбѓўбѓ”бѓ›бѓ‘бѓ”бѓ бѓ_бѓќбѓҐбѓўбѓќбѓ›бѓ‘бѓ”бѓ бѓ_бѓњбѓќбѓ”бѓ›бѓ‘бѓ”бѓ бѓ_бѓ“бѓ”бѓ™бѓ”бѓ›бѓ‘бѓ”бѓ бѓ'.split('_'),
            'accusative': 'бѓбѓђбѓњбѓ•бѓђбѓ бѓЎ_бѓ—бѓ”бѓ‘бѓ”бѓ бѓ•бѓђбѓљбѓЎ_бѓ›бѓђбѓ бѓўбѓЎ_бѓђбѓћбѓ бѓбѓљбѓбѓЎ_бѓ›бѓђбѓбѓЎбѓЎ_бѓбѓ•бѓњбѓбѓЎбѓЎ_бѓбѓ•бѓљбѓбѓЎбѓЎ_бѓђбѓ’бѓ•бѓбѓЎбѓўбѓЎ_бѓЎбѓ”бѓҐбѓўбѓ”бѓ›бѓ‘бѓ”бѓ бѓЎ_бѓќбѓҐбѓўбѓќбѓ›бѓ‘бѓ”бѓ бѓЎ_бѓњбѓќбѓ”бѓ›бѓ‘бѓ”бѓ бѓЎ_бѓ“бѓ”бѓ™бѓ”бѓ›бѓ‘бѓ”бѓ бѓЎ'.split('_')
        },

        nounCase = (/D[oD] *MMMM?/).test(format) ?
            'accusative' :
            'nominative';

        return months[nounCase][m.month()];
    }

    function weekdaysCaseReplace(m, format) {
        var weekdays = {
            'nominative': 'бѓ™бѓ•бѓбѓ бѓђ_бѓќбѓ бѓЁбѓђбѓ‘бѓђбѓ—бѓ_бѓЎбѓђбѓ›бѓЁбѓђбѓ‘бѓђбѓ—бѓ_бѓќбѓ—бѓ®бѓЁбѓђбѓ‘бѓђбѓ—бѓ_бѓ®бѓЈбѓ—бѓЁбѓђбѓ‘бѓђбѓ—бѓ_бѓћбѓђбѓ бѓђбѓЎбѓ™бѓ”бѓ•бѓ_бѓЁбѓђбѓ‘бѓђбѓ—бѓ'.split('_'),
            'accusative': 'бѓ™бѓ•бѓбѓ бѓђбѓЎ_бѓќбѓ бѓЁбѓђбѓ‘бѓђбѓ—бѓЎ_бѓЎбѓђбѓ›бѓЁбѓђбѓ‘бѓђбѓ—бѓЎ_бѓќбѓ—бѓ®бѓЁбѓђбѓ‘бѓђбѓ—бѓЎ_бѓ®бѓЈбѓ—бѓЁбѓђбѓ‘бѓђбѓ—бѓЎ_бѓћбѓђбѓ бѓђбѓЎбѓ™бѓ”бѓ•бѓЎ_бѓЁбѓђбѓ‘бѓђбѓ—бѓЎ'.split('_')
        },

        nounCase = (/(бѓ¬бѓбѓњбѓђ|бѓЁбѓ”бѓ›бѓ“бѓ”бѓ’)/).test(format) ?
            'accusative' :
            'nominative';

        return weekdays[nounCase][m.day()];
    }

    return moment.lang('ka', {
        months : monthsCaseReplace,
        monthsShort : "бѓбѓђбѓњ_бѓ—бѓ”бѓ‘_бѓ›бѓђбѓ _бѓђбѓћбѓ _бѓ›бѓђбѓ_бѓбѓ•бѓњ_бѓбѓ•бѓљ_бѓђбѓ’бѓ•_бѓЎбѓ”бѓҐ_бѓќбѓҐбѓў_бѓњбѓќбѓ”_бѓ“бѓ”бѓ™".split("_"),
        weekdays : weekdaysCaseReplace,
        weekdaysShort : "бѓ™бѓ•бѓ_бѓќбѓ бѓЁ_бѓЎбѓђбѓ›_бѓќбѓ—бѓ®_бѓ®бѓЈбѓ—_бѓћбѓђбѓ _бѓЁбѓђбѓ‘".split("_"),
        weekdaysMin : "бѓ™бѓ•_бѓќбѓ _бѓЎбѓђ_бѓќбѓ—_бѓ®бѓЈ_бѓћбѓђ_бѓЁбѓђ".split("_"),
        longDateFormat : {
            LT : "h:mm A",
            L : "DD/MM/YYYY",
            LL : "D MMMM YYYY",
            LLL : "D MMMM YYYY LT",
            LLLL : "dddd, D MMMM YYYY LT"
        },
        calendar : {
            sameDay : '[бѓ“бѓ¦бѓ”бѓЎ] LT[-бѓ–бѓ”]',
            nextDay : '[бѓ®бѓ•бѓђбѓљ] LT[-бѓ–бѓ”]',
            lastDay : '[бѓ’бѓЈбѓЁбѓбѓњ] LT[-бѓ–бѓ”]',
            nextWeek : '[бѓЁбѓ”бѓ›бѓ“бѓ”бѓ’] dddd LT[-бѓ–бѓ”]',
            lastWeek : '[бѓ¬бѓбѓњбѓђ] dddd LT-бѓ–бѓ”',
            sameElse : 'L'
        },
        relativeTime : {
            future : function (s) {
                return (/(бѓ¬бѓђбѓ›бѓ|бѓ¬бѓЈбѓ—бѓ|бѓЎбѓђбѓђбѓ—бѓ|бѓ¬бѓ”бѓљбѓ)/).test(s) ?
                    s.replace(/бѓ$/, "бѓЁбѓ") :
                    s + "бѓЁбѓ";
            },
            past : function (s) {
                if ((/(бѓ¬бѓђбѓ›бѓ|бѓ¬бѓЈбѓ—бѓ|бѓЎбѓђбѓђбѓ—бѓ|бѓ“бѓ¦бѓ”|бѓ—бѓ•бѓ”)/).test(s)) {
                    return s.replace(/(бѓ|бѓ”)$/, "бѓбѓЎ бѓ¬бѓбѓњ");
                }
                if ((/бѓ¬бѓ”бѓљбѓ/).test(s)) {
                    return s.replace(/бѓ¬бѓ”бѓљбѓ$/, "бѓ¬бѓљбѓбѓЎ бѓ¬бѓбѓњ");
                }
            },
            s : "бѓ бѓђбѓ›бѓ“бѓ”бѓњбѓбѓ›бѓ” бѓ¬бѓђбѓ›бѓ",
            m : "бѓ¬бѓЈбѓ—бѓ",
            mm : "%d бѓ¬бѓЈбѓ—бѓ",
            h : "бѓЎбѓђбѓђбѓ—бѓ",
            hh : "%d бѓЎбѓђбѓђбѓ—бѓ",
            d : "бѓ“бѓ¦бѓ”",
            dd : "%d бѓ“бѓ¦бѓ”",
            M : "бѓ—бѓ•бѓ”",
            MM : "%d бѓ—бѓ•бѓ”",
            y : "бѓ¬бѓ”бѓљбѓ",
            yy : "%d бѓ¬бѓ”бѓљбѓ"
        },
        ordinal : function (number) {
            if (number === 0) {
                return number;
            }

            if (number === 1) {
                return number + "-бѓљбѓ";
            }

            if ((number < 20) || (number <= 100 && (number % 20 === 0)) || (number % 100 === 0)) {
                return "бѓ›бѓ”-" + number;
            }

            return number + "-бѓ”";
        },
        week : {
            dow : 1,
            doy : 7
        }
    });
}));
// moment.js language configuration
// language : korean (ko)
//
// authors 
//
// - Kyungwook, Park : https://github.com/kyungw00k
// - Jeeeyul Lee <jeeeyul@gmail.com>
(function (factory) {
    factory(moment);
}(function (moment) {
    return moment.lang('ko', {
        months : "1м›”_2м›”_3м›”_4м›”_5м›”_6м›”_7м›”_8м›”_9м›”_10м›”_11м›”_12м›”".split("_"),
        monthsShort : "1м›”_2м›”_3м›”_4м›”_5м›”_6м›”_7м›”_8м›”_9м›”_10м›”_11м›”_12м›”".split("_"),
        weekdays : "мќјмљ”мќј_м›”мљ”мќј_н™”мљ”мќј_м€мљ”мќј_лЄ©мљ”мќј_кё€мљ”мќј_н† мљ”мќј".split("_"),
        weekdaysShort : "мќј_м›”_н™”_м€_лЄ©_кё€_н† ".split("_"),
        weekdaysMin : "мќј_м›”_н™”_м€_лЄ©_кё€_н† ".split("_"),
        longDateFormat : {
            LT : "A hм‹њ mmл¶„",
            L : "YYYY.MM.DD",
            LL : "YYYYл…„ MMMM Dмќј",
            LLL : "YYYYл…„ MMMM Dмќј LT",
            LLLL : "YYYYл…„ MMMM Dмќј dddd LT"
        },
        meridiem : function (hour, minute, isUpper) {
            return hour < 12 ? 'м¤м „' : 'м¤н›„';
        },
        calendar : {
            sameDay : 'м¤лЉ LT',
            nextDay : 'л‚ґмќј LT',
            nextWeek : 'dddd LT',
            lastDay : 'м–ґм њ LT',
            lastWeek : 'м§Ђл‚њмЈј dddd LT',
            sameElse : 'L'
        },
        relativeTime : {
            future : "%s н›„",
            past : "%s м „",
            s : "лЄ‡мґ€",
            ss : "%dмґ€",
            m : "мќјл¶„",
            mm : "%dл¶„",
            h : "н•њм‹њк°„",
            hh : "%dм‹њк°„",
            d : "н•лЈЁ",
            dd : "%dмќј",
            M : "н•њл‹¬",
            MM : "%dл‹¬",
            y : "мќјл…„",
            yy : "%dл…„"
        },
        ordinal : '%dмќј',
        meridiemParse : /(м¤м „|м¤н›„)/,
        isPM : function (token) {
            return token === "м¤н›„";
        }
    });
}));
// moment.js language configuration
// language : Luxembourgish (lb)
// author : mweimerskirch : https://github.com/mweimerskirch

// Note: Luxembourgish has a very particular phonological rule ("Eifeler Regel") that causes the
// deletion of the final "n" in certain contexts. That's what the "eifelerRegelAppliesToWeekday"
// and "eifelerRegelAppliesToNumber" methods are meant for

(function (factory) {
    factory(moment);
}(function (moment) {
    function processRelativeTime(number, withoutSuffix, key, isFuture) {
        var format = {
            'm': ['eng Minutt', 'enger Minutt'],
            'h': ['eng Stonn', 'enger Stonn'],
            'd': ['een Dag', 'engem Dag'],
            'dd': [number + ' Deeg', number + ' Deeg'],
            'M': ['ee Mount', 'engem Mount'],
            'MM': [number + ' MГ©int', number + ' MГ©int'],
            'y': ['ee Joer', 'engem Joer'],
            'yy': [number + ' Joer', number + ' Joer']
        };
        return withoutSuffix ? format[key][0] : format[key][1];
    }

    function processFutureTime(string) {
        var number = string.substr(0, string.indexOf(' '));
        if (eifelerRegelAppliesToNumber(number)) {
            return "a " + string;
        }
        return "an " + string;
    }

    function processPastTime(string) {
        var number = string.substr(0, string.indexOf(' '));
        if (eifelerRegelAppliesToNumber(number)) {
            return "viru " + string;
        }
        return "virun " + string;
    }

    function processLastWeek(string1) {
        var weekday = this.format('d');
        if (eifelerRegelAppliesToWeekday(weekday)) {
            return '[Leschte] dddd [um] LT';
        }
        return '[Leschten] dddd [um] LT';
    }

    /**
     * Returns true if the word before the given week day loses the "-n" ending.
     * e.g. "Leschten DГ«nschdeg" but "Leschte MГ©indeg"
     *
     * @param weekday {integer}
     * @returns {boolean}
     */
    function eifelerRegelAppliesToWeekday(weekday) {
        weekday = parseInt(weekday, 10);
        switch (weekday) {
        case 0: // Sonndeg
        case 1: // MГ©indeg
        case 3: // MГ«ttwoch
        case 5: // Freideg
        case 6: // Samschdeg
            return true;
        default: // 2 DГ«nschdeg, 4 Donneschdeg
            return false;
        }
    }

    /**
     * Returns true if the word before the given number loses the "-n" ending.
     * e.g. "an 10 Deeg" but "a 5 Deeg"
     *
     * @param number {integer}
     * @returns {boolean}
     */
    function eifelerRegelAppliesToNumber(number) {
        number = parseInt(number, 10);
        if (isNaN(number)) {
            return false;
        }
        if (number < 0) {
            // Negative Number --> always true
            return true;
        } else if (number < 10) {
            // Only 1 digit
            if (4 <= number && number <= 7) {
                return true;
            }
            return false;
        } else if (number < 100) {
            // 2 digits
            var lastDigit = number % 10, firstDigit = number / 10;
            if (lastDigit === 0) {
                return eifelerRegelAppliesToNumber(firstDigit);
            }
            return eifelerRegelAppliesToNumber(lastDigit);
        } else if (number < 10000) {
            // 3 or 4 digits --> recursively check first digit
            while (number >= 10) {
                number = number / 10;
            }
            return eifelerRegelAppliesToNumber(number);
        } else {
            // Anything larger than 4 digits: recursively check first n-3 digits
            number = number / 1000;
            return eifelerRegelAppliesToNumber(number);
        }
    }

    return moment.lang('lb', {
        months: "Januar_Februar_MГ¤erz_AbrГ«ll_Mee_Juni_Juli_August_September_Oktober_November_Dezember".split("_"),
        monthsShort: "Jan._Febr._Mrz._Abr._Mee_Jun._Jul._Aug._Sept._Okt._Nov._Dez.".split("_"),
        weekdays: "Sonndeg_MГ©indeg_DГ«nschdeg_MГ«ttwoch_Donneschdeg_Freideg_Samschdeg".split("_"),
        weekdaysShort: "So._MГ©._DГ«._MГ«._Do._Fr._Sa.".split("_"),
        weekdaysMin: "So_MГ©_DГ«_MГ«_Do_Fr_Sa".split("_"),
        longDateFormat: {
            LT: "H:mm [Auer]",
            L: "DD.MM.YYYY",
            LL: "D. MMMM YYYY",
            LLL: "D. MMMM YYYY LT",
            LLLL: "dddd, D. MMMM YYYY LT"
        },
        calendar: {
            sameDay: "[Haut um] LT",
            sameElse: "L",
            nextDay: '[Muer um] LT',
            nextWeek: 'dddd [um] LT',
            lastDay: '[GГ«schter um] LT',
            lastWeek: processLastWeek
        },
        relativeTime: {
            future: processFutureTime,
            past: processPastTime,
            s: "e puer Sekonnen",
            m: processRelativeTime,
            mm: "%d Minutten",
            h: processRelativeTime,
            hh: "%d Stonnen",
            d: processRelativeTime,
            dd: processRelativeTime,
            M: processRelativeTime,
            MM: processRelativeTime,
            y: processRelativeTime,
            yy: processRelativeTime
        },
        ordinal: '%d.',
        week: {
            dow: 1, // Monday is the first day of the week.
            doy: 4  // The week that contains Jan 4th is the first week of the year.
        }
    });
}));
// moment.js language configuration
// language : Lithuanian (lt)
// author : Mindaugas MozЕ«ras : https://github.com/mmozuras

(function (factory) {
    factory(moment);
}(function (moment) {
    var units = {
        "m" : "minutД—_minutД—s_minutД™",
        "mm": "minutД—s_minuДЌiЕі_minutes",
        "h" : "valanda_valandos_valandД…",
        "hh": "valandos_valandЕі_valandas",
        "d" : "diena_dienos_dienД…",
        "dd": "dienos_dienЕі_dienas",
        "M" : "mД—nuo_mД—nesio_mД—nesДЇ",
        "MM": "mД—nesiai_mД—nesiЕі_mД—nesius",
        "y" : "metai_metЕі_metus",
        "yy": "metai_metЕі_metus"
    },
    weekDays = "pirmadienis_antradienis_treДЌiadienis_ketvirtadienis_penktadienis_ЕЎeЕЎtadienis_sekmadienis".split("_");

    function translateSeconds(number, withoutSuffix, key, isFuture) {
        if (withoutSuffix) {
            return "kelios sekundД—s";
        } else {
            return isFuture ? "keliЕі sekundЕѕiЕі" : "kelias sekundes";
        }
    }

    function translateSingular(number, withoutSuffix, key, isFuture) {
        return withoutSuffix ? forms(key)[0] : (isFuture ? forms(key)[1] : forms(key)[2]);
    }

    function special(number) {
        return number % 10 === 0 || (number > 10 && number < 20);
    }

    function forms(key) {
        return units[key].split("_");
    }

    function translate(number, withoutSuffix, key, isFuture) {
        var result = number + " ";
        if (number === 1) {
            return result + translateSingular(number, withoutSuffix, key[0], isFuture);
        } else if (withoutSuffix) {
            return result + (special(number) ? forms(key)[1] : forms(key)[0]);
        } else {
            if (isFuture) {
                return result + forms(key)[1];
            } else {
                return result + (special(number) ? forms(key)[1] : forms(key)[2]);
            }
        }
    }

    function relativeWeekDay(moment, format) {
        var nominative = format.indexOf('dddd LT') === -1,
            weekDay = weekDays[moment.weekday()];

        return nominative ? weekDay : weekDay.substring(0, weekDay.length - 2) + "ДЇ";
    }

    return moment.lang("lt", {
        months : "sausio_vasario_kovo_balandЕѕio_geguЕѕД—s_birЕѕД—lio_liepos_rugpjЕ«ДЌio_rugsД—jo_spalio_lapkriДЌio_gruodЕѕio".split("_"),
        monthsShort : "sau_vas_kov_bal_geg_bir_lie_rgp_rgs_spa_lap_grd".split("_"),
        weekdays : relativeWeekDay,
        weekdaysShort : "Sek_Pir_Ant_Tre_Ket_Pen_Е eЕЎ".split("_"),
        weekdaysMin : "S_P_A_T_K_Pn_Е ".split("_"),
        longDateFormat : {
            LT : "HH:mm",
            L : "YYYY-MM-DD",
            LL : "YYYY [m.] MMMM D [d.]",
            LLL : "YYYY [m.] MMMM D [d.], LT [val.]",
            LLLL : "YYYY [m.] MMMM D [d.], dddd, LT [val.]",
            l : "YYYY-MM-DD",
            ll : "YYYY [m.] MMMM D [d.]",
            lll : "YYYY [m.] MMMM D [d.], LT [val.]",
            llll : "YYYY [m.] MMMM D [d.], ddd, LT [val.]"
        },
        calendar : {
            sameDay : "[Е iandien] LT",
            nextDay : "[Rytoj] LT",
            nextWeek : "dddd LT",
            lastDay : "[Vakar] LT",
            lastWeek : "[PraД—jusДЇ] dddd LT",
            sameElse : "L"
        },
        relativeTime : {
            future : "po %s",
            past : "prieЕЎ %s",
            s : translateSeconds,
            m : translateSingular,
            mm : translate,
            h : translateSingular,
            hh : translate,
            d : translateSingular,
            dd : translate,
            M : translateSingular,
            MM : translate,
            y : translateSingular,
            yy : translate
        },
        ordinal : function (number) {
            return number + '-oji';
        },
        week : {
            dow : 1, // Monday is the first day of the week.
            doy : 4  // The week that contains Jan 4th is the first week of the year.
        }
    });
}));
// moment.js language configuration
// language : latvian (lv)
// author : Kristaps Karlsons : https://github.com/skakri

(function (factory) {
    factory(moment);
}(function (moment) {
    var units = {
        'mm': 'minЕ«ti_minЕ«tes_minЕ«te_minЕ«tes',
        'hh': 'stundu_stundas_stunda_stundas',
        'dd': 'dienu_dienas_diena_dienas',
        'MM': 'mД“nesi_mД“neЕЎus_mД“nesis_mД“neЕЎi',
        'yy': 'gadu_gadus_gads_gadi'
    };

    function format(word, number, withoutSuffix) {
        var forms = word.split('_');
        if (withoutSuffix) {
            return number % 10 === 1 && number !== 11 ? forms[2] : forms[3];
        } else {
            return number % 10 === 1 && number !== 11 ? forms[0] : forms[1];
        }
    }

    function relativeTimeWithPlural(number, withoutSuffix, key) {
        return number + ' ' + format(units[key], number, withoutSuffix);
    }

    return moment.lang('lv', {
        months : "janvДЃris_februДЃris_marts_aprД«lis_maijs_jЕ«nijs_jЕ«lijs_augusts_septembris_oktobris_novembris_decembris".split("_"),
        monthsShort : "jan_feb_mar_apr_mai_jЕ«n_jЕ«l_aug_sep_okt_nov_dec".split("_"),
        weekdays : "svД“tdiena_pirmdiena_otrdiena_treЕЎdiena_ceturtdiena_piektdiena_sestdiena".split("_"),
        weekdaysShort : "Sv_P_O_T_C_Pk_S".split("_"),
        weekdaysMin : "Sv_P_O_T_C_Pk_S".split("_"),
        longDateFormat : {
            LT : "HH:mm",
            L : "DD.MM.YYYY",
            LL : "YYYY. [gada] D. MMMM",
            LLL : "YYYY. [gada] D. MMMM, LT",
            LLLL : "YYYY. [gada] D. MMMM, dddd, LT"
        },
        calendar : {
            sameDay : '[Е odien pulksten] LT',
            nextDay : '[RД«t pulksten] LT',
            nextWeek : 'dddd [pulksten] LT',
            lastDay : '[Vakar pulksten] LT',
            lastWeek : '[PagДЃjuЕЎДЃ] dddd [pulksten] LT',
            sameElse : 'L'
        },
        relativeTime : {
            future : "%s vД“lДЃk",
            past : "%s agrДЃk",
            s : "daЕѕas sekundes",
            m : "minЕ«ti",
            mm : relativeTimeWithPlural,
            h : "stundu",
            hh : relativeTimeWithPlural,
            d : "dienu",
            dd : relativeTimeWithPlural,
            M : "mД“nesi",
            MM : relativeTimeWithPlural,
            y : "gadu",
            yy : relativeTimeWithPlural
        },
        ordinal : '%d.',
        week : {
            dow : 1, // Monday is the first day of the week.
            doy : 4  // The week that contains Jan 4th is the first week of the year.
        }
    });
}));
// moment.js language configuration
// language : macedonian (mk)
// author : Borislav Mickov : https://github.com/B0k0

(function (factory) {
    factory(moment);
}(function (moment) {
    return moment.lang('mk', {
        months : "СР°РЅСѓР°СЂРё_С„РµРІСЂСѓР°СЂРё_РјР°СЂС‚_Р°РїСЂРёР»_РјР°С_ССѓРЅРё_ССѓР»Рё_Р°РІРіСѓСЃС‚_СЃРµРїС‚РµРјРІСЂРё_РѕРєС‚РѕРјРІСЂРё_РЅРѕРµРјРІСЂРё_РґРµРєРµРјРІСЂРё".split("_"),
        monthsShort : "СР°РЅ_С„РµРІ_РјР°СЂ_Р°РїСЂ_РјР°С_ССѓРЅ_ССѓР»_Р°РІРі_СЃРµРї_РѕРєС‚_РЅРѕРµ_РґРµРє".split("_"),
        weekdays : "РЅРµРґРµР»Р°_РїРѕРЅРµРґРµР»РЅРёРє_РІС‚РѕСЂРЅРёРє_СЃСЂРµРґР°_С‡РµС‚РІСЂС‚РѕРє_РїРµС‚РѕРє_СЃР°Р±РѕС‚Р°".split("_"),
        weekdaysShort : "РЅРµРґ_РїРѕРЅ_РІС‚Рѕ_СЃСЂРµ_С‡РµС‚_РїРµС‚_СЃР°Р±".split("_"),
        weekdaysMin : "РЅe_Рїo_РІС‚_СЃСЂ_С‡Рµ_РїРµ_СЃa".split("_"),
        longDateFormat : {
            LT : "H:mm",
            L : "D.MM.YYYY",
            LL : "D MMMM YYYY",
            LLL : "D MMMM YYYY LT",
            LLLL : "dddd, D MMMM YYYY LT"
        },
        calendar : {
            sameDay : '[Р”РµРЅРµСЃ РІРѕ] LT',
            nextDay : '[РЈС‚СЂРµ РІРѕ] LT',
            nextWeek : 'dddd [РІРѕ] LT',
            lastDay : '[Р’С‡РµСЂР° РІРѕ] LT',
            lastWeek : function () {
                switch (this.day()) {
                case 0:
                case 3:
                case 6:
                    return '[Р’Рѕ РёР·РјРёРЅР°С‚Р°С‚Р°] dddd [РІРѕ] LT';
                case 1:
                case 2:
                case 4:
                case 5:
                    return '[Р’Рѕ РёР·РјРёРЅР°С‚РёРѕС‚] dddd [РІРѕ] LT';
                }
            },
            sameElse : 'L'
        },
        relativeTime : {
            future : "РїРѕСЃР»Рµ %s",
            past : "РїСЂРµРґ %s",
            s : "РЅРµРєРѕР»РєСѓ СЃРµРєСѓРЅРґРё",
            m : "РјРёРЅСѓС‚Р°",
            mm : "%d РјРёРЅСѓС‚Рё",
            h : "С‡Р°СЃ",
            hh : "%d С‡Р°СЃР°",
            d : "РґРµРЅ",
            dd : "%d РґРµРЅР°",
            M : "РјРµСЃРµС†",
            MM : "%d РјРµСЃРµС†Рё",
            y : "РіРѕРґРёРЅР°",
            yy : "%d РіРѕРґРёРЅРё"
        },
        ordinal : function (number) {
            var lastDigit = number % 10,
                last2Digits = number % 100;
            if (number === 0) {
                return number + '-РµРІ';
            } else if (last2Digits === 0) {
                return number + '-РµРЅ';
            } else if (last2Digits > 10 && last2Digits < 20) {
                return number + '-С‚Рё';
            } else if (lastDigit === 1) {
                return number + '-РІРё';
            } else if (lastDigit === 2) {
                return number + '-СЂРё';
            } else if (lastDigit === 7 || lastDigit === 8) {
                return number + '-РјРё';
            } else {
                return number + '-С‚Рё';
            }
        },
        week : {
            dow : 1, // Monday is the first day of the week.
            doy : 7  // The week that contains Jan 1st is the first week of the year.
        }
    });
}));
// moment.js language configuration
// language : malayalam (ml)
// author : Floyd Pink : https://github.com/floydpink

(function (factory) {
    factory(moment);
}(function (moment) {
    return moment.lang('ml', {
        months : 'аґњаґЁаµЃаґµаґ°аґї_аґ«аµ†аґ¬аµЌаґ°аµЃаґµаґ°аґї_аґ®аґѕаµјаґљаµЌаґљаµЌ_аґЏаґЄаµЌаґ°аґїаµЅ_аґ®аµ‡аґЇаµЌ_аґњаµ‚аµє_аґњаµ‚аґІаµ€_аґ“аґ—аґёаµЌаґ±аµЌаґ±аµЌ_аґёаµ†аґЄаµЌаґ±аµЌаґ±аґ‚аґ¬аµј_аґ’аґ•аµЌаґџаµ‹аґ¬аµј_аґЁаґµаґ‚аґ¬аµј_аґЎаґїаґёаґ‚аґ¬аµј'.split("_"),
        monthsShort : 'аґњаґЁаµЃ._аґ«аµ†аґ¬аµЌаґ°аµЃ._аґ®аґѕаµј._аґЏаґЄаµЌаґ°аґї._аґ®аµ‡аґЇаµЌ_аґњаµ‚аµє_аґњаµ‚аґІаµ€._аґ“аґ—._аґёаµ†аґЄаµЌаґ±аµЌаґ±._аґ’аґ•аµЌаґџаµ‹._аґЁаґµаґ‚._аґЎаґїаґёаґ‚.'.split("_"),
        weekdays : 'аґћаґѕаґЇаґ±аґѕаґґаµЌаґљ_аґ¤аґїаґ™аµЌаґ•аґіаґѕаґґаµЌаґљ_аґљаµЉаґµаµЌаґµаґѕаґґаµЌаґљ_аґ¬аµЃаґ§аґЁаґѕаґґаµЌаґљ_аґµаµЌаґЇаґѕаґґаґѕаґґаµЌаґљ_аґµаµ†аґіаµЌаґіаґїаґЇаґѕаґґаµЌаґљ_аґ¶аґЁаґїаґЇаґѕаґґаµЌаґљ'.split("_"),
        weekdaysShort : 'аґћаґѕаґЇаµј_аґ¤аґїаґ™аµЌаґ•аµѕ_аґљаµЉаґµаµЌаґµ_аґ¬аµЃаґ§аµ»_аґµаµЌаґЇаґѕаґґаґ‚_аґµаµ†аґіаµЌаґіаґї_аґ¶аґЁаґї'.split("_"),
        weekdaysMin : 'аґћаґѕ_аґ¤аґї_аґљаµЉ_аґ¬аµЃ_аґµаµЌаґЇаґѕ_аґµаµ†_аґ¶'.split("_"),
        longDateFormat : {
            LT : "A h:mm -аґЁаµЃ",
            L : "DD/MM/YYYY",
            LL : "D MMMM YYYY",
            LLL : "D MMMM YYYY, LT",
            LLLL : "dddd, D MMMM YYYY, LT"
        },
        calendar : {
            sameDay : '[аґ‡аґЁаµЌаґЁаµЌ] LT',
            nextDay : '[аґЁаґѕаґіаµ†] LT',
            nextWeek : 'dddd, LT',
            lastDay : '[аґ‡аґЁаµЌаґЁаґІаµ†] LT',
            lastWeek : '[аґ•аґґаґїаґћаµЌаґћ] dddd, LT',
            sameElse : 'L'
        },
        relativeTime : {
            future : "%s аґ•аґґаґїаґћаµЌаґћаµЌ",
            past : "%s аґ®аµЃаµ»аґЄаµЌ",
            s : "аґ…аµЅаґЄ аґЁаґїаґ®аґїаґ·аґ™аµЌаґ™аµѕ",
            m : "аґ’аґ°аµЃ аґ®аґїаґЁаґїаґ±аµЌаґ±аµЌ",
            mm : "%d аґ®аґїаґЁаґїаґ±аµЌаґ±аµЌ",
            h : "аґ’аґ°аµЃ аґ®аґЈаґїаґ•аµЌаґ•аµ‚аµј",
            hh : "%d аґ®аґЈаґїаґ•аµЌаґ•аµ‚аµј",
            d : "аґ’аґ°аµЃ аґ¦аґїаґµаґёаґ‚",
            dd : "%d аґ¦аґїаґµаґёаґ‚",
            M : "аґ’аґ°аµЃ аґ®аґѕаґёаґ‚",
            MM : "%d аґ®аґѕаґёаґ‚",
            y : "аґ’аґ°аµЃ аґµаµјаґ·аґ‚",
            yy : "%d аґµаµјаґ·аґ‚"
        },
        meridiem : function (hour, minute, isLower) {
            if (hour < 4) {
                return "аґ°аґѕаґ¤аµЌаґ°аґї";
            } else if (hour < 12) {
                return "аґ°аґѕаґµаґїаґІаµ†";
            } else if (hour < 17) {
                return "аґ‰аґљаµЌаґљ аґ•аґґаґїаґћаµЌаґћаµЌ";
            } else if (hour < 20) {
                return "аґµаµ€аґ•аµЃаґЁаµЌаґЁаµ‡аґ°аґ‚";
            } else {
                return "аґ°аґѕаґ¤аµЌаґ°аґї";
            }
        }
    });
}));
// moment.js language configuration
// language : Marathi (mr)
// author : Harshad Kale : https://github.com/kalehv

(function (factory) {
    factory(moment);
}(function (moment) {
    var symbolMap = {
        '1': 'аҐ§',
        '2': 'аҐЁ',
        '3': 'аҐ©',
        '4': 'аҐЄ',
        '5': 'аҐ«',
        '6': 'аҐ¬',
        '7': 'аҐ­',
        '8': 'аҐ®',
        '9': 'аҐЇ',
        '0': 'аҐ¦'
    },
    numberMap = {
        'аҐ§': '1',
        'аҐЁ': '2',
        'аҐ©': '3',
        'аҐЄ': '4',
        'аҐ«': '5',
        'аҐ¬': '6',
        'аҐ­': '7',
        'аҐ®': '8',
        'аҐЇ': '9',
        'аҐ¦': '0'
    };

    return moment.lang('mr', {
        months : 'а¤ња¤ѕа¤ЁаҐ‡а¤µа¤ѕа¤°аҐЂ_а¤«аҐ‡а¤¬аҐЌа¤°аҐЃа¤µа¤ѕа¤°аҐЂ_а¤®а¤ѕа¤°аҐЌа¤љ_а¤Џа¤ЄаҐЌа¤°а¤їа¤І_а¤®аҐ‡_а¤њаҐ‚а¤Ё_а¤њаҐЃа¤ІаҐ€_а¤‘а¤—а¤ёаҐЌа¤џ_а¤ёа¤ЄаҐЌа¤џаҐ‡а¤‚а¤¬а¤°_а¤‘а¤•аҐЌа¤џаҐ‹а¤¬а¤°_а¤ЁаҐ‹а¤µаҐЌа¤№аҐ‡а¤‚а¤¬а¤°_а¤Ўа¤їа¤ёаҐ‡а¤‚а¤¬а¤°'.split("_"),
        monthsShort: 'а¤ња¤ѕа¤ЁаҐ‡._а¤«аҐ‡а¤¬аҐЌа¤°аҐЃ._а¤®а¤ѕа¤°аҐЌа¤љ._а¤Џа¤ЄаҐЌа¤°а¤ї._а¤®аҐ‡._а¤њаҐ‚а¤Ё._а¤њаҐЃа¤ІаҐ€._а¤‘а¤—._а¤ёа¤ЄаҐЌа¤џаҐ‡а¤‚._а¤‘а¤•аҐЌа¤џаҐ‹._а¤ЁаҐ‹а¤µаҐЌа¤№аҐ‡а¤‚._а¤Ўа¤їа¤ёаҐ‡а¤‚.'.split("_"),
        weekdays : 'а¤°а¤µа¤їа¤µа¤ѕа¤°_а¤ёаҐ‹а¤®а¤µа¤ѕа¤°_а¤®а¤‚а¤—а¤іа¤µа¤ѕа¤°_а¤¬аҐЃа¤§а¤µа¤ѕа¤°_а¤—аҐЃа¤°аҐ‚а¤µа¤ѕа¤°_а¤¶аҐЃа¤•аҐЌа¤°а¤µа¤ѕа¤°_а¤¶а¤Ёа¤їа¤µа¤ѕа¤°'.split("_"),
        weekdaysShort : 'а¤°а¤µа¤ї_а¤ёаҐ‹а¤®_а¤®а¤‚а¤—а¤і_а¤¬аҐЃа¤§_а¤—аҐЃа¤°аҐ‚_а¤¶аҐЃа¤•аҐЌа¤°_а¤¶а¤Ёа¤ї'.split("_"),
        weekdaysMin : 'а¤°_а¤ёаҐ‹_а¤®а¤‚_а¤¬аҐЃ_а¤—аҐЃ_а¤¶аҐЃ_а¤¶'.split("_"),
        longDateFormat : {
            LT : "A h:mm а¤µа¤ѕа¤ња¤¤а¤ѕ",
            L : "DD/MM/YYYY",
            LL : "D MMMM YYYY",
            LLL : "D MMMM YYYY, LT",
            LLLL : "dddd, D MMMM YYYY, LT"
        },
        calendar : {
            sameDay : '[а¤†а¤њ] LT',
            nextDay : '[а¤‰а¤¦аҐЌа¤Їа¤ѕ] LT',
            nextWeek : 'dddd, LT',
            lastDay : '[а¤•а¤ѕа¤І] LT',
            lastWeek: '[а¤®а¤ѕа¤—аҐЂа¤І] dddd, LT',
            sameElse : 'L'
        },
        relativeTime : {
            future : "%s а¤Ёа¤‚а¤¤а¤°",
            past : "%s а¤ЄаҐ‚а¤°аҐЌа¤µаҐЂ",
            s : "а¤ёаҐ‡а¤•а¤‚а¤¦",
            m: "а¤Џа¤• а¤®а¤їа¤Ёа¤їа¤џ",
            mm: "%d а¤®а¤їа¤Ёа¤їа¤џаҐ‡",
            h : "а¤Џа¤• а¤¤а¤ѕа¤ё",
            hh : "%d а¤¤а¤ѕа¤ё",
            d : "а¤Џа¤• а¤¦а¤їа¤µа¤ё",
            dd : "%d а¤¦а¤їа¤µа¤ё",
            M : "а¤Џа¤• а¤®а¤№а¤їа¤Ёа¤ѕ",
            MM : "%d а¤®а¤№а¤їа¤ЁаҐ‡",
            y : "а¤Џа¤• а¤µа¤°аҐЌа¤·",
            yy : "%d а¤µа¤°аҐЌа¤·аҐ‡"
        },
        preparse: function (string) {
            return string.replace(/[аҐ§аҐЁаҐ©аҐЄаҐ«аҐ¬аҐ­аҐ®аҐЇаҐ¦]/g, function (match) {
                return numberMap[match];
            });
        },
        postformat: function (string) {
            return string.replace(/\d/g, function (match) {
                return symbolMap[match];
            });
        },
        meridiem: function (hour, minute, isLower)
        {
            if (hour < 4) {
                return "а¤°а¤ѕа¤¤аҐЌа¤°аҐЂ";
            } else if (hour < 10) {
                return "а¤ёа¤•а¤ѕа¤іаҐЂ";
            } else if (hour < 17) {
                return "а¤¦аҐЃа¤Єа¤ѕа¤°аҐЂ";
            } else if (hour < 20) {
                return "а¤ёа¤ѕа¤Їа¤‚а¤•а¤ѕа¤іаҐЂ";
            } else {
                return "а¤°а¤ѕа¤¤аҐЌа¤°аҐЂ";
            }
        },
        week : {
            dow : 0, // Sunday is the first day of the week.
            doy : 6  // The week that contains Jan 1st is the first week of the year.
        }
    });
}));
// moment.js language configuration
// language : Bahasa Malaysia (ms-MY)
// author : Weldan Jamili : https://github.com/weldan

(function (factory) {
    factory(moment);
}(function (moment) {
    return moment.lang('ms-my', {
        months : "Januari_Februari_Mac_April_Mei_Jun_Julai_Ogos_September_Oktober_November_Disember".split("_"),
        monthsShort : "Jan_Feb_Mac_Apr_Mei_Jun_Jul_Ogs_Sep_Okt_Nov_Dis".split("_"),
        weekdays : "Ahad_Isnin_Selasa_Rabu_Khamis_Jumaat_Sabtu".split("_"),
        weekdaysShort : "Ahd_Isn_Sel_Rab_Kha_Jum_Sab".split("_"),
        weekdaysMin : "Ah_Is_Sl_Rb_Km_Jm_Sb".split("_"),
        longDateFormat : {
            LT : "HH.mm",
            L : "DD/MM/YYYY",
            LL : "D MMMM YYYY",
            LLL : "D MMMM YYYY [pukul] LT",
            LLLL : "dddd, D MMMM YYYY [pukul] LT"
        },
        meridiem : function (hours, minutes, isLower) {
            if (hours < 11) {
                return 'pagi';
            } else if (hours < 15) {
                return 'tengahari';
            } else if (hours < 19) {
                return 'petang';
            } else {
                return 'malam';
            }
        },
        calendar : {
            sameDay : '[Hari ini pukul] LT',
            nextDay : '[Esok pukul] LT',
            nextWeek : 'dddd [pukul] LT',
            lastDay : '[Kelmarin pukul] LT',
            lastWeek : 'dddd [lepas pukul] LT',
            sameElse : 'L'
        },
        relativeTime : {
            future : "dalam %s",
            past : "%s yang lepas",
            s : "beberapa saat",
            m : "seminit",
            mm : "%d minit",
            h : "sejam",
            hh : "%d jam",
            d : "sehari",
            dd : "%d hari",
            M : "sebulan",
            MM : "%d bulan",
            y : "setahun",
            yy : "%d tahun"
        },
        week : {
            dow : 1, // Monday is the first day of the week.
            doy : 7  // The week that contains Jan 1st is the first week of the year.
        }
    });
}));
// moment.js language configuration
// language : norwegian bokmГҐl (nb)
// authors : Espen Hovlandsdal : https://github.com/rexxars
//           Sigurd Gartmann : https://github.com/sigurdga

(function (factory) {
    factory(moment);
}(function (moment) {
    return moment.lang('nb', {
        months : "januar_februar_mars_april_mai_juni_juli_august_september_oktober_november_desember".split("_"),
        monthsShort : "jan._feb._mars_april_mai_juni_juli_aug._sep._okt._nov._des.".split("_"),
        weekdays : "sГёndag_mandag_tirsdag_onsdag_torsdag_fredag_lГёrdag".split("_"),
        weekdaysShort : "sГё._ma._ti._on._to._fr._lГё.".split("_"),
        weekdaysMin : "sГё_ma_ti_on_to_fr_lГё".split("_"),
        longDateFormat : {
            LT : "H.mm",
            L : "DD.MM.YYYY",
            LL : "D. MMMM YYYY",
            LLL : "D. MMMM YYYY [kl.] LT",
            LLLL : "dddd D. MMMM YYYY [kl.] LT"
        },
        calendar : {
            sameDay: '[i dag kl.] LT',
            nextDay: '[i morgen kl.] LT',
            nextWeek: 'dddd [kl.] LT',
            lastDay: '[i gГҐr kl.] LT',
            lastWeek: '[forrige] dddd [kl.] LT',
            sameElse: 'L'
        },
        relativeTime : {
            future : "om %s",
            past : "for %s siden",
            s : "noen sekunder",
            m : "ett minutt",
            mm : "%d minutter",
            h : "en time",
            hh : "%d timer",
            d : "en dag",
            dd : "%d dager",
            M : "en mГҐned",
            MM : "%d mГҐneder",
            y : "ett ГҐr",
            yy : "%d ГҐr"
        },
        ordinal : '%d.',
        week : {
            dow : 1, // Monday is the first day of the week.
            doy : 4  // The week that contains Jan 4th is the first week of the year.
        }
    });
}));
// moment.js language configuration
// language : nepali/nepalese
// author : suvash : https://github.com/suvash

(function (factory) {
    factory(moment);
}(function (moment) {
    var symbolMap = {
        '1': 'аҐ§',
        '2': 'аҐЁ',
        '3': 'аҐ©',
        '4': 'аҐЄ',
        '5': 'аҐ«',
        '6': 'аҐ¬',
        '7': 'аҐ­',
        '8': 'аҐ®',
        '9': 'аҐЇ',
        '0': 'аҐ¦'
    },
    numberMap = {
        'аҐ§': '1',
        'аҐЁ': '2',
        'аҐ©': '3',
        'аҐЄ': '4',
        'аҐ«': '5',
        'аҐ¬': '6',
        'аҐ­': '7',
        'аҐ®': '8',
        'аҐЇ': '9',
        'аҐ¦': '0'
    };

    return moment.lang('ne', {
        months : 'а¤ња¤Ёа¤µа¤°аҐЂ_а¤«аҐ‡а¤¬аҐЌа¤°аҐЃа¤µа¤°аҐЂ_а¤®а¤ѕа¤°аҐЌа¤љ_а¤…а¤ЄаҐЌа¤°а¤їа¤І_а¤®а¤€_а¤њаҐЃа¤Ё_а¤њаҐЃа¤Іа¤ѕа¤€_а¤…а¤—а¤·аҐЌа¤џ_а¤ёаҐ‡а¤ЄаҐЌа¤џаҐ‡а¤®аҐЌа¤¬а¤°_а¤…а¤•аҐЌа¤џаҐ‹а¤¬а¤°_а¤ЁаҐ‹а¤­аҐ‡а¤®аҐЌа¤¬а¤°_а¤Ўа¤їа¤ёаҐ‡а¤®аҐЌа¤¬а¤°'.split("_"),
        monthsShort : 'а¤ња¤Ё._а¤«аҐ‡а¤¬аҐЌа¤°аҐЃ._а¤®а¤ѕа¤°аҐЌа¤љ_а¤…а¤ЄаҐЌа¤°а¤ї._а¤®а¤€_а¤њаҐЃа¤Ё_а¤њаҐЃа¤Іа¤ѕа¤€._а¤…а¤—._а¤ёаҐ‡а¤ЄаҐЌа¤џ._а¤…а¤•аҐЌа¤џаҐ‹._а¤ЁаҐ‹а¤­аҐ‡._а¤Ўа¤їа¤ёаҐ‡.'.split("_"),
        weekdays : 'а¤†а¤‡а¤¤а¤¬а¤ѕа¤°_а¤ёаҐ‹а¤®а¤¬а¤ѕа¤°_а¤®а¤™аҐЌа¤—а¤Іа¤¬а¤ѕа¤°_а¤¬аҐЃа¤§а¤¬а¤ѕа¤°_а¤¬а¤їа¤№а¤їа¤¬а¤ѕа¤°_а¤¶аҐЃа¤•аҐЌа¤°а¤¬а¤ѕа¤°_а¤¶а¤Ёа¤їа¤¬а¤ѕа¤°'.split("_"),
        weekdaysShort : 'а¤†а¤‡а¤¤._а¤ёаҐ‹а¤®._а¤®а¤™аҐЌа¤—а¤І._а¤¬аҐЃа¤§._а¤¬а¤їа¤№а¤ї._а¤¶аҐЃа¤•аҐЌа¤°._а¤¶а¤Ёа¤ї.'.split("_"),
        weekdaysMin : 'а¤†а¤‡._а¤ёаҐ‹._а¤®а¤™аҐЌ_а¤¬аҐЃ._а¤¬а¤ї._а¤¶аҐЃ._а¤¶.'.split("_"),
        longDateFormat : {
            LT : "Aа¤•аҐ‹ h:mm а¤¬а¤њаҐ‡",
            L : "DD/MM/YYYY",
            LL : "D MMMM YYYY",
            LLL : "D MMMM YYYY, LT",
            LLLL : "dddd, D MMMM YYYY, LT"
        },
        preparse: function (string) {
            return string.replace(/[аҐ§аҐЁаҐ©аҐЄаҐ«аҐ¬аҐ­аҐ®аҐЇаҐ¦]/g, function (match) {
                return numberMap[match];
            });
        },
        postformat: function (string) {
            return string.replace(/\d/g, function (match) {
                return symbolMap[match];
            });
        },
        meridiem : function (hour, minute, isLower) {
            if (hour < 3) {
                return "а¤°а¤ѕа¤¤аҐЂ";
            } else if (hour < 10) {
                return "а¤¬а¤їа¤№а¤ѕа¤Ё";
            } else if (hour < 15) {
                return "а¤¦а¤їа¤‰а¤Ѓа¤ёаҐ‹";
            } else if (hour < 18) {
                return "а¤¬аҐ‡а¤ІаҐЃа¤•а¤ѕ";
            } else if (hour < 20) {
                return "а¤ёа¤ѕа¤Ѓа¤ќ";
            } else {
                return "а¤°а¤ѕа¤¤аҐЂ";
            }
        },
        calendar : {
            sameDay : '[а¤†а¤њ] LT',
            nextDay : '[а¤­аҐ‹а¤ІаҐЂ] LT',
            nextWeek : '[а¤†а¤‰а¤Ѓа¤¦аҐ‹] dddd[,] LT',
            lastDay : '[а¤№а¤їа¤њаҐ‹] LT',
            lastWeek : '[а¤—а¤Џа¤•аҐ‹] dddd[,] LT',
            sameElse : 'L'
        },
        relativeTime : {
            future : "%sа¤®а¤ѕ",
            past : "%s а¤…а¤—а¤ѕа¤ЎаҐЂ",
            s : "а¤•аҐ‡а¤№аҐЂ а¤ёа¤®а¤Ї",
            m : "а¤Џа¤• а¤®а¤їа¤ЁаҐ‡а¤џ",
            mm : "%d а¤®а¤їа¤ЁаҐ‡а¤џ",
            h : "а¤Џа¤• а¤а¤ЈаҐЌа¤џа¤ѕ",
            hh : "%d а¤а¤ЈаҐЌа¤џа¤ѕ",
            d : "а¤Џа¤• а¤¦а¤їа¤Ё",
            dd : "%d а¤¦а¤їа¤Ё",
            M : "а¤Џа¤• а¤®а¤№а¤їа¤Ёа¤ѕ",
            MM : "%d а¤®а¤№а¤їа¤Ёа¤ѕ",
            y : "а¤Џа¤• а¤¬а¤°аҐЌа¤·",
            yy : "%d а¤¬а¤°аҐЌа¤·"
        },
        week : {
            dow : 1, // Monday is the first day of the week.
            doy : 7  // The week that contains Jan 1st is the first week of the year.
        }
    });
}));
// moment.js language configuration
// language : dutch (nl)
// author : Joris RГ¶ling : https://github.com/jjupiter

(function (factory) {
    factory(moment);
}(function (moment) {
    var monthsShortWithDots = "jan._feb._mrt._apr._mei_jun._jul._aug._sep._okt._nov._dec.".split("_"),
        monthsShortWithoutDots = "jan_feb_mrt_apr_mei_jun_jul_aug_sep_okt_nov_dec".split("_");

    return moment.lang('nl', {
        months : "januari_februari_maart_april_mei_juni_juli_augustus_september_oktober_november_december".split("_"),
        monthsShort : function (m, format) {
            if (/-MMM-/.test(format)) {
                return monthsShortWithoutDots[m.month()];
            } else {
                return monthsShortWithDots[m.month()];
            }
        },
        weekdays : "zondag_maandag_dinsdag_woensdag_donderdag_vrijdag_zaterdag".split("_"),
        weekdaysShort : "zo._ma._di._wo._do._vr._za.".split("_"),
        weekdaysMin : "Zo_Ma_Di_Wo_Do_Vr_Za".split("_"),
        longDateFormat : {
            LT : "HH:mm",
            L : "DD-MM-YYYY",
            LL : "D MMMM YYYY",
            LLL : "D MMMM YYYY LT",
            LLLL : "dddd D MMMM YYYY LT"
        },
        calendar : {
            sameDay: '[vandaag om] LT',
            nextDay: '[morgen om] LT',
            nextWeek: 'dddd [om] LT',
            lastDay: '[gisteren om] LT',
            lastWeek: '[afgelopen] dddd [om] LT',
            sameElse: 'L'
        },
        relativeTime : {
            future : "over %s",
            past : "%s geleden",
            s : "een paar seconden",
            m : "Г©Г©n minuut",
            mm : "%d minuten",
            h : "Г©Г©n uur",
            hh : "%d uur",
            d : "Г©Г©n dag",
            dd : "%d dagen",
            M : "Г©Г©n maand",
            MM : "%d maanden",
            y : "Г©Г©n jaar",
            yy : "%d jaar"
        },
        ordinal : function (number) {
            return number + ((number === 1 || number === 8 || number >= 20) ? 'ste' : 'de');
        },
        week : {
            dow : 1, // Monday is the first day of the week.
            doy : 4  // The week that contains Jan 4th is the first week of the year.
        }
    });
}));
// moment.js language configuration
// language : norwegian nynorsk (nn)
// author : https://github.com/mechuwind

(function (factory) {
    factory(moment);
}(function (moment) {
    return moment.lang('nn', {
        months : "januar_februar_mars_april_mai_juni_juli_august_september_oktober_november_desember".split("_"),
        monthsShort : "jan_feb_mar_apr_mai_jun_jul_aug_sep_okt_nov_des".split("_"),
        weekdays : "sundag_mГҐndag_tysdag_onsdag_torsdag_fredag_laurdag".split("_"),
        weekdaysShort : "sun_mГҐn_tys_ons_tor_fre_lau".split("_"),
        weekdaysMin : "su_mГҐ_ty_on_to_fr_lГё".split("_"),
        longDateFormat : {
            LT : "HH:mm",
            L : "DD.MM.YYYY",
            LL : "D MMMM YYYY",
            LLL : "D MMMM YYYY LT",
            LLLL : "dddd D MMMM YYYY LT"
        },
        calendar : {
            sameDay: '[I dag klokka] LT',
            nextDay: '[I morgon klokka] LT',
            nextWeek: 'dddd [klokka] LT',
            lastDay: '[I gГҐr klokka] LT',
            lastWeek: '[FГёregГҐende] dddd [klokka] LT',
            sameElse: 'L'
        },
        relativeTime : {
            future : "om %s",
            past : "for %s siden",
            s : "noen sekund",
            m : "ett minutt",
            mm : "%d minutt",
            h : "en time",
            hh : "%d timar",
            d : "en dag",
            dd : "%d dagar",
            M : "en mГҐnad",
            MM : "%d mГҐnader",
            y : "ett ГҐr",
            yy : "%d ГҐr"
        },
        ordinal : '%d.',
        week : {
            dow : 1, // Monday is the first day of the week.
            doy : 4  // The week that contains Jan 4th is the first week of the year.
        }
    });
}));
// moment.js language configuration
// language : polish (pl)
// author : Rafal Hirsz : https://github.com/evoL

(function (factory) {
    factory(moment);
}(function (moment) {
    var monthsNominative = "styczeЕ„_luty_marzec_kwiecieЕ„_maj_czerwiec_lipiec_sierpieЕ„_wrzesieЕ„_paЕєdziernik_listopad_grudzieЕ„".split("_"),
        monthsSubjective = "stycznia_lutego_marca_kwietnia_maja_czerwca_lipca_sierpnia_wrzeЕ›nia_paЕєdziernika_listopada_grudnia".split("_");

    function plural(n) {
        return (n % 10 < 5) && (n % 10 > 1) && ((~~(n / 10) % 10) !== 1);
    }

    function translate(number, withoutSuffix, key) {
        var result = number + " ";
        switch (key) {
        case 'm':
            return withoutSuffix ? 'minuta' : 'minutД™';
        case 'mm':
            return result + (plural(number) ? 'minuty' : 'minut');
        case 'h':
            return withoutSuffix  ? 'godzina'  : 'godzinД™';
        case 'hh':
            return result + (plural(number) ? 'godziny' : 'godzin');
        case 'MM':
            return result + (plural(number) ? 'miesiД…ce' : 'miesiД™cy');
        case 'yy':
            return result + (plural(number) ? 'lata' : 'lat');
        }
    }

    return moment.lang('pl', {
        months : function (momentToFormat, format) {
            if (/D MMMM/.test(format)) {
                return monthsSubjective[momentToFormat.month()];
            } else {
                return monthsNominative[momentToFormat.month()];
            }
        },
        monthsShort : "sty_lut_mar_kwi_maj_cze_lip_sie_wrz_paЕє_lis_gru".split("_"),
        weekdays : "niedziela_poniedziaЕ‚ek_wtorek_Е›roda_czwartek_piД…tek_sobota".split("_"),
        weekdaysShort : "nie_pon_wt_Е›r_czw_pt_sb".split("_"),
        weekdaysMin : "N_Pn_Wt_Ељr_Cz_Pt_So".split("_"),
        longDateFormat : {
            LT : "HH:mm",
            L : "DD.MM.YYYY",
            LL : "D MMMM YYYY",
            LLL : "D MMMM YYYY LT",
            LLLL : "dddd, D MMMM YYYY LT"
        },
        calendar : {
            sameDay: '[DziЕ› o] LT',
            nextDay: '[Jutro o] LT',
            nextWeek: '[W] dddd [o] LT',
            lastDay: '[Wczoraj o] LT',
            lastWeek: function () {
                switch (this.day()) {
                case 0:
                    return '[W zeszЕ‚Д… niedzielД™ o] LT';
                case 3:
                    return '[W zeszЕ‚Д… Е›rodД™ o] LT';
                case 6:
                    return '[W zeszЕ‚Д… sobotД™ o] LT';
                default:
                    return '[W zeszЕ‚y] dddd [o] LT';
                }
            },
            sameElse: 'L'
        },
        relativeTime : {
            future : "za %s",
            past : "%s temu",
            s : "kilka sekund",
            m : translate,
            mm : translate,
            h : translate,
            hh : translate,
            d : "1 dzieЕ„",
            dd : '%d dni',
            M : "miesiД…c",
            MM : translate,
            y : "rok",
            yy : translate
        },
        ordinal : '%d.',
        week : {
            dow : 1, // Monday is the first day of the week.
            doy : 4  // The week that contains Jan 4th is the first week of the year.
        }
    });
}));
// moment.js language configuration
// language : brazilian portuguese (pt-br)
// author : Caio Ribeiro Pereira : https://github.com/caio-ribeiro-pereira

(function (factory) {
    factory(moment);
}(function (moment) {
    return moment.lang('pt-br', {
        months : "Janeiro_Fevereiro_MarГ§o_Abril_Maio_Junho_Julho_Agosto_Setembro_Outubro_Novembro_Dezembro".split("_"),
        monthsShort : "Jan_Fev_Mar_Abr_Mai_Jun_Jul_Ago_Set_Out_Nov_Dez".split("_"),
        weekdays : "Domingo_Segunda-feira_TerГ§a-feira_Quarta-feira_Quinta-feira_Sexta-feira_SГЎbado".split("_"),
        weekdaysShort : "Dom_Seg_Ter_Qua_Qui_Sex_SГЎb".split("_"),
        weekdaysMin : "Dom_2ВЄ_3ВЄ_4ВЄ_5ВЄ_6ВЄ_SГЎb".split("_"),
        longDateFormat : {
            LT : "HH:mm",
            L : "DD/MM/YYYY",
            LL : "D [de] MMMM [de] YYYY",
            LLL : "D [de] MMMM [de] YYYY LT",
            LLLL : "dddd, D [de] MMMM [de] YYYY LT"
        },
        calendar : {
            sameDay: '[Hoje Г s] LT',
            nextDay: '[AmanhГЈ Г s] LT',
            nextWeek: 'dddd [Г s] LT',
            lastDay: '[Ontem Г s] LT',
            lastWeek: function () {
                return (this.day() === 0 || this.day() === 6) ?
                    '[Гљltimo] dddd [Г s] LT' : // Saturday + Sunday
                    '[Гљltima] dddd [Г s] LT'; // Monday - Friday
            },
            sameElse: 'L'
        },
        relativeTime : {
            future : "em %s",
            past : "%s atrГЎs",
            s : "segundos",
            m : "um minuto",
            mm : "%d minutos",
            h : "uma hora",
            hh : "%d horas",
            d : "um dia",
            dd : "%d dias",
            M : "um mГЄs",
            MM : "%d meses",
            y : "um ano",
            yy : "%d anos"
        },
        ordinal : '%dВє'
    });
}));
// moment.js language configuration
// language : portuguese (pt)
// author : Jefferson : https://github.com/jalex79

(function (factory) {
    factory(moment);
}(function (moment) {
    return moment.lang('pt', {
        months : "Janeiro_Fevereiro_MarГ§o_Abril_Maio_Junho_Julho_Agosto_Setembro_Outubro_Novembro_Dezembro".split("_"),
        monthsShort : "Jan_Fev_Mar_Abr_Mai_Jun_Jul_Ago_Set_Out_Nov_Dez".split("_"),
        weekdays : "Domingo_Segunda-feira_TerГ§a-feira_Quarta-feira_Quinta-feira_Sexta-feira_SГЎbado".split("_"),
        weekdaysShort : "Dom_Seg_Ter_Qua_Qui_Sex_SГЎb".split("_"),
        weekdaysMin : "Dom_2ВЄ_3ВЄ_4ВЄ_5ВЄ_6ВЄ_SГЎb".split("_"),
        longDateFormat : {
            LT : "HH:mm",
            L : "DD/MM/YYYY",
            LL : "D [de] MMMM [de] YYYY",
            LLL : "D [de] MMMM [de] YYYY LT",
            LLLL : "dddd, D [de] MMMM [de] YYYY LT"
        },
        calendar : {
            sameDay: '[Hoje Г s] LT',
            nextDay: '[AmanhГЈ Г s] LT',
            nextWeek: 'dddd [Г s] LT',
            lastDay: '[Ontem Г s] LT',
            lastWeek: function () {
                return (this.day() === 0 || this.day() === 6) ?
                    '[Гљltimo] dddd [Г s] LT' : // Saturday + Sunday
                    '[Гљltima] dddd [Г s] LT'; // Monday - Friday
            },
            sameElse: 'L'
        },
        relativeTime : {
            future : "em %s",
            past : "%s atrГЎs",
            s : "segundos",
            m : "um minuto",
            mm : "%d minutos",
            h : "uma hora",
            hh : "%d horas",
            d : "um dia",
            dd : "%d dias",
            M : "um mГЄs",
            MM : "%d meses",
            y : "um ano",
            yy : "%d anos"
        },
        ordinal : '%dВє',
        week : {
            dow : 1, // Monday is the first day of the week.
            doy : 4  // The week that contains Jan 4th is the first week of the year.
        }
    });
}));
// moment.js language configuration
// language : romanian (ro)
// author : Vlad Gurdiga : https://github.com/gurdiga
// author : Valentin Agachi : https://github.com/avaly

(function (factory) {
    factory(moment);
}(function (moment) {
    function relativeTimeWithPlural(number, withoutSuffix, key) {
        var format = {
            'mm': 'minute',
            'hh': 'ore',
            'dd': 'zile',
            'MM': 'luni',
            'yy': 'ani'
        },
            separator = ' ';
        if (number % 100 >= 20 || (number >= 100 && number % 100 === 0)) {
            separator = ' de ';
        }

        return number + separator + format[key];
    }

    return moment.lang('ro', {
        months : "ianuarie_februarie_martie_aprilie_mai_iunie_iulie_august_septembrie_octombrie_noiembrie_decembrie".split("_"),
        monthsShort : "ian_feb_mar_apr_mai_iun_iul_aug_sep_oct_noi_dec".split("_"),
        weekdays : "duminicДѓ_luni_marИ›i_miercuri_joi_vineri_sГўmbДѓtДѓ".split("_"),
        weekdaysShort : "Dum_Lun_Mar_Mie_Joi_Vin_SГўm".split("_"),
        weekdaysMin : "Du_Lu_Ma_Mi_Jo_Vi_SГў".split("_"),
        longDateFormat : {
            LT : "H:mm",
            L : "DD.MM.YYYY",
            LL : "D MMMM YYYY",
            LLL : "D MMMM YYYY H:mm",
            LLLL : "dddd, D MMMM YYYY H:mm"
        },
        calendar : {
            sameDay: "[azi la] LT",
            nextDay: '[mГўine la] LT',
            nextWeek: 'dddd [la] LT',
            lastDay: '[ieri la] LT',
            lastWeek: '[fosta] dddd [la] LT',
            sameElse: 'L'
        },
        relativeTime : {
            future : "peste %s",
            past : "%s Г®n urmДѓ",
            s : "cГўteva secunde",
            m : "un minut",
            mm : relativeTimeWithPlural,
            h : "o orДѓ",
            hh : relativeTimeWithPlural,
            d : "o zi",
            dd : relativeTimeWithPlural,
            M : "o lunДѓ",
            MM : relativeTimeWithPlural,
            y : "un an",
            yy : relativeTimeWithPlural
        },
        week : {
            dow : 1, // Monday is the first day of the week.
            doy : 7  // The week that contains Jan 1st is the first week of the year.
        }
    });
}));
// moment.js language configuration
// language : serbian (rs)
// author : Limon Monte : https://github.com/limonte
// based on (bs) translation by Nedim Cholich

(function (factory) {
    factory(moment);
}(function (moment) {

    function translate(number, withoutSuffix, key) {
        var result = number + " ";
        switch (key) {
        case 'm':
            return withoutSuffix ? 'jedna minuta' : 'jedne minute';
        case 'mm':
            if (number === 1) {
                result += 'minuta';
            } else if (number === 2 || number === 3 || number === 4) {
                result += 'minute';
            } else {
                result += 'minuta';
            }
            return result;
        case 'h':
            return withoutSuffix ? 'jedan sat' : 'jednog sata';
        case 'hh':
            if (number === 1) {
                result += 'sat';
            } else if (number === 2 || number === 3 || number === 4) {
                result += 'sata';
            } else {
                result += 'sati';
            }
            return result;
        case 'dd':
            if (number === 1) {
                result += 'dan';
            } else {
                result += 'dana';
            }
            return result;
        case 'MM':
            if (number === 1) {
                result += 'mesec';
            } else if (number === 2 || number === 3 || number === 4) {
                result += 'meseca';
            } else {
                result += 'meseci';
            }
            return result;
        case 'yy':
            if (number === 1) {
                result += 'godina';
            } else if (number === 2 || number === 3 || number === 4) {
                result += 'godine';
            } else {
                result += 'godina';
            }
            return result;
        }
    }

    return moment.lang('rs', {
        months : "januar_februar_mart_april_maj_jun_jul_avgust_septembar_oktobar_novembar_decembar".split("_"),
        monthsShort : "jan._feb._mar._apr._maj._jun._jul._avg._sep._okt._nov._dec.".split("_"),
        weekdays : "nedelja_ponedeljak_utorak_sreda_ДЌetvrtak_petak_subota".split("_"),
        weekdaysShort : "ned._pon._uto._sre._ДЌet._pet._sub.".split("_"),
        weekdaysMin : "ne_po_ut_sr_ДЌe_pe_su".split("_"),
        longDateFormat : {
            LT : "H:mm",
            L : "DD. MM. YYYY",
            LL : "D. MMMM YYYY",
            LLL : "D. MMMM YYYY LT",
            LLLL : "dddd, D. MMMM YYYY LT"
        },
        calendar : {
            sameDay  : '[danas u] LT',
            nextDay  : '[sutra u] LT',

            nextWeek : function () {
                switch (this.day()) {
                case 0:
                    return '[u] [nedelju] [u] LT';
                case 3:
                    return '[u] [sredu] [u] LT';
                case 6:
                    return '[u] [subotu] [u] LT';
                case 1:
                case 2:
                case 4:
                case 5:
                    return '[u] dddd [u] LT';
                }
            },
            lastDay  : '[juДЌe u] LT',
            lastWeek : function () {
                switch (this.day()) {
                case 0:
                case 3:
                    return '[proЕЎlu] dddd [u] LT';
                case 6:
                    return '[proЕЎle] [subote] [u] LT';
                case 1:
                case 2:
                case 4:
                case 5:
                    return '[proЕЎli] dddd [u] LT';
                }
            },
            sameElse : 'L'
        },
        relativeTime : {
            future : "za %s",
            past   : "pre %s",
            s      : "par sekundi",
            m      : translate,
            mm     : translate,
            h      : translate,
            hh     : translate,
            d      : "dan",
            dd     : translate,
            M      : "mesec",
            MM     : translate,
            y      : "godinu",
            yy     : translate
        },
        ordinal : '%d.',
        week : {
            dow : 1, // Monday is the first day of the week.
            doy : 7  // The week that contains Jan 1st is the first week of the year.
        }
    });
}));
// moment.js language configuration
// language : russian (ru)
// author : Viktorminator : https://github.com/Viktorminator
// Author : Menelion ElensГєle : https://github.com/Oire

(function (factory) {
    factory(moment);
}(function (moment) {
    function plural(word, num) {
        var forms = word.split('_');
        return num % 10 === 1 && num % 100 !== 11 ? forms[0] : (num % 10 >= 2 && num % 10 <= 4 && (num % 100 < 10 || num % 100 >= 20) ? forms[1] : forms[2]);
    }

    function relativeTimeWithPlural(number, withoutSuffix, key) {
        var format = {
            'mm': 'РјРёРЅСѓС‚Р°_РјРёРЅСѓС‚С‹_РјРёРЅСѓС‚',
            'hh': 'С‡Р°СЃ_С‡Р°СЃР°_С‡Р°СЃРѕРІ',
            'dd': 'РґРµРЅСЊ_РґРЅСЏ_РґРЅРµР№',
            'MM': 'РјРµСЃСЏС†_РјРµСЃСЏС†Р°_РјРµСЃСЏС†РµРІ',
            'yy': 'РіРѕРґ_РіРѕРґР°_Р»РµС‚'
        };
        if (key === 'm') {
            return withoutSuffix ? 'РјРёРЅСѓС‚Р°' : 'РјРёРЅСѓС‚Сѓ';
        }
        else {
            return number + ' ' + plural(format[key], +number);
        }
    }

    function monthsCaseReplace(m, format) {
        var months = {
            'nominative': 'СЏРЅРІР°СЂСЊ_С„РµРІСЂР°Р»СЊ_РјР°СЂС‚_Р°РїСЂРµР»СЊ_РјР°Р№_РёСЋРЅСЊ_РёСЋР»СЊ_Р°РІРіСѓСЃС‚_СЃРµРЅС‚СЏР±СЂСЊ_РѕРєС‚СЏР±СЂСЊ_РЅРѕСЏР±СЂСЊ_РґРµРєР°Р±СЂСЊ'.split('_'),
            'accusative': 'СЏРЅРІР°СЂСЏ_С„РµРІСЂР°Р»СЏ_РјР°СЂС‚Р°_Р°РїСЂРµР»СЏ_РјР°СЏ_РёСЋРЅСЏ_РёСЋР»СЏ_Р°РІРіСѓСЃС‚Р°_СЃРµРЅС‚СЏР±СЂСЏ_РѕРєС‚СЏР±СЂСЏ_РЅРѕСЏР±СЂСЏ_РґРµРєР°Р±СЂСЏ'.split('_')
        },

        nounCase = (/D[oD]?(\[[^\[\]]*\]|\s+)+MMMM?/).test(format) ?
            'accusative' :
            'nominative';

        return months[nounCase][m.month()];
    }

    function monthsShortCaseReplace(m, format) {
        var monthsShort = {
            'nominative': 'СЏРЅРІ_С„РµРІ_РјР°СЂ_Р°РїСЂ_РјР°Р№_РёСЋРЅСЊ_РёСЋР»СЊ_Р°РІРі_СЃРµРЅ_РѕРєС‚_РЅРѕСЏ_РґРµРє'.split('_'),
            'accusative': 'СЏРЅРІ_С„РµРІ_РјР°СЂ_Р°РїСЂ_РјР°СЏ_РёСЋРЅСЏ_РёСЋР»СЏ_Р°РІРі_СЃРµРЅ_РѕРєС‚_РЅРѕСЏ_РґРµРє'.split('_')
        },

        nounCase = (/D[oD]?(\[[^\[\]]*\]|\s+)+MMMM?/).test(format) ?
            'accusative' :
            'nominative';

        return monthsShort[nounCase][m.month()];
    }

    function weekdaysCaseReplace(m, format) {
        var weekdays = {
            'nominative': 'РІРѕСЃРєСЂРµСЃРµРЅСЊРµ_РїРѕРЅРµРґРµР»СЊРЅРёРє_РІС‚РѕСЂРЅРёРє_СЃСЂРµРґР°_С‡РµС‚РІРµСЂРі_РїСЏС‚РЅРёС†Р°_СЃСѓР±Р±РѕС‚Р°'.split('_'),
            'accusative': 'РІРѕСЃРєСЂРµСЃРµРЅСЊРµ_РїРѕРЅРµРґРµР»СЊРЅРёРє_РІС‚РѕСЂРЅРёРє_СЃСЂРµРґСѓ_С‡РµС‚РІРµСЂРі_РїСЏС‚РЅРёС†Сѓ_СЃСѓР±Р±РѕС‚Сѓ'.split('_')
        },

        nounCase = (/\[ ?[Р’РІ] ?(?:РїСЂРѕС€Р»СѓСЋ|СЃР»РµРґСѓСЋС‰СѓСЋ)? ?\] ?dddd/).test(format) ?
            'accusative' :
            'nominative';

        return weekdays[nounCase][m.day()];
    }

    return moment.lang('ru', {
        months : monthsCaseReplace,
        monthsShort : monthsShortCaseReplace,
        weekdays : weekdaysCaseReplace,
        weekdaysShort : "РІСЃ_РїРЅ_РІС‚_СЃСЂ_С‡С‚_РїС‚_СЃР±".split("_"),
        weekdaysMin : "РІСЃ_РїРЅ_РІС‚_СЃСЂ_С‡С‚_РїС‚_СЃР±".split("_"),
        monthsParse : [/^СЏРЅРІ/i, /^С„РµРІ/i, /^РјР°СЂ/i, /^Р°РїСЂ/i, /^РјР°[Р№|СЏ]/i, /^РёСЋРЅ/i, /^РёСЋР»/i, /^Р°РІРі/i, /^СЃРµРЅ/i, /^РѕРєС‚/i, /^РЅРѕСЏ/i, /^РґРµРє/i],
        longDateFormat : {
            LT : "HH:mm",
            L : "DD.MM.YYYY",
            LL : "D MMMM YYYY Рі.",
            LLL : "D MMMM YYYY Рі., LT",
            LLLL : "dddd, D MMMM YYYY Рі., LT"
        },
        calendar : {
            sameDay: '[РЎРµРіРѕРґРЅСЏ РІ] LT',
            nextDay: '[Р—Р°РІС‚СЂР° РІ] LT',
            lastDay: '[Р’С‡РµСЂР° РІ] LT',
            nextWeek: function () {
                return this.day() === 2 ? '[Р’Рѕ] dddd [РІ] LT' : '[Р’] dddd [РІ] LT';
            },
            lastWeek: function () {
                switch (this.day()) {
                case 0:
                    return '[Р’ РїСЂРѕС€Р»РѕРµ] dddd [РІ] LT';
                case 1:
                case 2:
                case 4:
                    return '[Р’ РїСЂРѕС€Р»С‹Р№] dddd [РІ] LT';
                case 3:
                case 5:
                case 6:
                    return '[Р’ РїСЂРѕС€Р»СѓСЋ] dddd [РІ] LT';
                }
            },
            sameElse: 'L'
        },
        relativeTime : {
            future : "С‡РµСЂРµР· %s",
            past : "%s РЅР°Р·Р°Рґ",
            s : "РЅРµСЃРєРѕР»СЊРєРѕ СЃРµРєСѓРЅРґ",
            m : relativeTimeWithPlural,
            mm : relativeTimeWithPlural,
            h : "С‡Р°СЃ",
            hh : relativeTimeWithPlural,
            d : "РґРµРЅСЊ",
            dd : relativeTimeWithPlural,
            M : "РјРµСЃСЏС†",
            MM : relativeTimeWithPlural,
            y : "РіРѕРґ",
            yy : relativeTimeWithPlural
        },

        // M. E.: those two are virtually unused but a user might want to implement them for his/her website for some reason

        meridiem : function (hour, minute, isLower) {
            if (hour < 4) {
                return "РЅРѕС‡Рё";
            } else if (hour < 12) {
                return "СѓС‚СЂР°";
            } else if (hour < 17) {
                return "РґРЅСЏ";
            } else {
                return "РІРµС‡РµСЂР°";
            }
        },

        ordinal: function (number, period) {
            switch (period) {
            case 'M':
            case 'd':
            case 'DDD':
                return number + '-Р№';
            case 'D':
                return number + '-РіРѕ';
            case 'w':
            case 'W':
                return number + '-СЏ';
            default:
                return number;
            }
        },

        week : {
            dow : 1, // Monday is the first day of the week.
            doy : 7  // The week that contains Jan 1st is the first week of the year.
        }
    });
}));
// moment.js language configuration
// language : slovak (sk)
// author : Martin Minka : https://github.com/k2s
// based on work of petrbela : https://github.com/petrbela

(function (factory) {
    factory(moment);
}(function (moment) {
    var months = "januГЎr_februГЎr_marec_aprГ­l_mГЎj_jГєn_jГєl_august_september_oktГіber_november_december".split("_"),
        monthsShort = "jan_feb_mar_apr_mГЎj_jГєn_jГєl_aug_sep_okt_nov_dec".split("_");

    function plural(n) {
        return (n > 1) && (n < 5);
    }

    function translate(number, withoutSuffix, key, isFuture) {
        var result = number + " ";
        switch (key) {
        case 's':  // a few seconds / in a few seconds / a few seconds ago
            return (withoutSuffix || isFuture) ? 'pГЎr sekГєnd' : 'pГЎr sekundami';
        case 'm':  // a minute / in a minute / a minute ago
            return withoutSuffix ? 'minГєta' : (isFuture ? 'minГєtu' : 'minГєtou');
        case 'mm': // 9 minutes / in 9 minutes / 9 minutes ago
            if (withoutSuffix || isFuture) {
                return result + (plural(number) ? 'minГєty' : 'minГєt');
            } else {
                return result + 'minГєtami';
            }
            break;
        case 'h':  // an hour / in an hour / an hour ago
            return withoutSuffix ? 'hodina' : (isFuture ? 'hodinu' : 'hodinou');
        case 'hh': // 9 hours / in 9 hours / 9 hours ago
            if (withoutSuffix || isFuture) {
                return result + (plural(number) ? 'hodiny' : 'hodГ­n');
            } else {
                return result + 'hodinami';
            }
            break;
        case 'd':  // a day / in a day / a day ago
            return (withoutSuffix || isFuture) ? 'deЕ€' : 'dЕ€om';
        case 'dd': // 9 days / in 9 days / 9 days ago
            if (withoutSuffix || isFuture) {
                return result + (plural(number) ? 'dni' : 'dnГ­');
            } else {
                return result + 'dЕ€ami';
            }
            break;
        case 'M':  // a month / in a month / a month ago
            return (withoutSuffix || isFuture) ? 'mesiac' : 'mesiacom';
        case 'MM': // 9 months / in 9 months / 9 months ago
            if (withoutSuffix || isFuture) {
                return result + (plural(number) ? 'mesiace' : 'mesiacov');
            } else {
                return result + 'mesiacmi';
            }
            break;
        case 'y':  // a year / in a year / a year ago
            return (withoutSuffix || isFuture) ? 'rok' : 'rokom';
        case 'yy': // 9 years / in 9 years / 9 years ago
            if (withoutSuffix || isFuture) {
                return result + (plural(number) ? 'roky' : 'rokov');
            } else {
                return result + 'rokmi';
            }
            break;
        }
    }

    return moment.lang('sk', {
        months : months,
        monthsShort : monthsShort,
        monthsParse : (function (months, monthsShort) {
            var i, _monthsParse = [];
            for (i = 0; i < 12; i++) {
                // use custom parser to solve problem with July (ДЌervenec)
                _monthsParse[i] = new RegExp('^' + months[i] + '$|^' + monthsShort[i] + '$', 'i');
            }
            return _monthsParse;
        }(months, monthsShort)),
        weekdays : "nedeДѕa_pondelok_utorok_streda_ЕЎtvrtok_piatok_sobota".split("_"),
        weekdaysShort : "ne_po_ut_st_ЕЎt_pi_so".split("_"),
        weekdaysMin : "ne_po_ut_st_ЕЎt_pi_so".split("_"),
        longDateFormat : {
            LT: "H:mm",
            L : "DD.MM.YYYY",
            LL : "D. MMMM YYYY",
            LLL : "D. MMMM YYYY LT",
            LLLL : "dddd D. MMMM YYYY LT"
        },
        calendar : {
            sameDay: "[dnes o] LT",
            nextDay: '[zajtra o] LT',
            nextWeek: function () {
                switch (this.day()) {
                case 0:
                    return '[v nedeДѕu o] LT';
                case 1:
                case 2:
                    return '[v] dddd [o] LT';
                case 3:
                    return '[v stredu o] LT';
                case 4:
                    return '[vo ЕЎtvrtok o] LT';
                case 5:
                    return '[v piatok o] LT';
                case 6:
                    return '[v sobotu o] LT';
                }
            },
            lastDay: '[vДЌera o] LT',
            lastWeek: function () {
                switch (this.day()) {
                case 0:
                    return '[minulГє nedeДѕu o] LT';
                case 1:
                case 2:
                    return '[minulГЅ] dddd [o] LT';
                case 3:
                    return '[minulГє stredu o] LT';
                case 4:
                case 5:
                    return '[minulГЅ] dddd [o] LT';
                case 6:
                    return '[minulГє sobotu o] LT';
                }
            },
            sameElse: "L"
        },
        relativeTime : {
            future : "za %s",
            past : "pred %s",
            s : translate,
            m : translate,
            mm : translate,
            h : translate,
            hh : translate,
            d : translate,
            dd : translate,
            M : translate,
            MM : translate,
            y : translate,
            yy : translate
        },
        ordinal : '%d.',
        week : {
            dow : 1, // Monday is the first day of the week.
            doy : 4  // The week that contains Jan 4th is the first week of the year.
        }
    });
}));
// moment.js language configuration
// language : slovenian (sl)
// author : Robert SedovЕЎek : https://github.com/sedovsek

(function (factory) {
    factory(moment);
}(function (moment) {
    function translate(number, withoutSuffix, key) {
        var result = number + " ";
        switch (key) {
        case 'm':
            return withoutSuffix ? 'ena minuta' : 'eno minuto';
        case 'mm':
            if (number === 1) {
                result += 'minuta';
            } else if (number === 2) {
                result += 'minuti';
            } else if (number === 3 || number === 4) {
                result += 'minute';
            } else {
                result += 'minut';
            }
            return result;
        case 'h':
            return withoutSuffix ? 'ena ura' : 'eno uro';
        case 'hh':
            if (number === 1) {
                result += 'ura';
            } else if (number === 2) {
                result += 'uri';
            } else if (number === 3 || number === 4) {
                result += 'ure';
            } else {
                result += 'ur';
            }
            return result;
        case 'dd':
            if (number === 1) {
                result += 'dan';
            } else {
                result += 'dni';
            }
            return result;
        case 'MM':
            if (number === 1) {
                result += 'mesec';
            } else if (number === 2) {
                result += 'meseca';
            } else if (number === 3 || number === 4) {
                result += 'mesece';
            } else {
                result += 'mesecev';
            }
            return result;
        case 'yy':
            if (number === 1) {
                result += 'leto';
            } else if (number === 2) {
                result += 'leti';
            } else if (number === 3 || number === 4) {
                result += 'leta';
            } else {
                result += 'let';
            }
            return result;
        }
    }

    return moment.lang('sl', {
        months : "januar_februar_marec_april_maj_junij_julij_avgust_september_oktober_november_december".split("_"),
        monthsShort : "jan._feb._mar._apr._maj._jun._jul._avg._sep._okt._nov._dec.".split("_"),
        weekdays : "nedelja_ponedeljek_torek_sreda_ДЌetrtek_petek_sobota".split("_"),
        weekdaysShort : "ned._pon._tor._sre._ДЌet._pet._sob.".split("_"),
        weekdaysMin : "ne_po_to_sr_ДЌe_pe_so".split("_"),
        longDateFormat : {
            LT : "H:mm",
            L : "DD. MM. YYYY",
            LL : "D. MMMM YYYY",
            LLL : "D. MMMM YYYY LT",
            LLLL : "dddd, D. MMMM YYYY LT"
        },
        calendar : {
            sameDay  : '[danes ob] LT',
            nextDay  : '[jutri ob] LT',

            nextWeek : function () {
                switch (this.day()) {
                case 0:
                    return '[v] [nedeljo] [ob] LT';
                case 3:
                    return '[v] [sredo] [ob] LT';
                case 6:
                    return '[v] [soboto] [ob] LT';
                case 1:
                case 2:
                case 4:
                case 5:
                    return '[v] dddd [ob] LT';
                }
            },
            lastDay  : '[vДЌeraj ob] LT',
            lastWeek : function () {
                switch (this.day()) {
                case 0:
                case 3:
                case 6:
                    return '[prejЕЎnja] dddd [ob] LT';
                case 1:
                case 2:
                case 4:
                case 5:
                    return '[prejЕЎnji] dddd [ob] LT';
                }
            },
            sameElse : 'L'
        },
        relativeTime : {
            future : "ДЌez %s",
            past   : "%s nazaj",
            s      : "nekaj sekund",
            m      : translate,
            mm     : translate,
            h      : translate,
            hh     : translate,
            d      : "en dan",
            dd     : translate,
            M      : "en mesec",
            MM     : translate,
            y      : "eno leto",
            yy     : translate
        },
        ordinal : '%d.',
        week : {
            dow : 1, // Monday is the first day of the week.
            doy : 7  // The week that contains Jan 1st is the first week of the year.
        }
    });
}));
// moment.js language configuration
// language : Albanian (sq)
// author : FlakГ«rim Ismani : https://github.com/flakerimi
// author: Menelion ElensГєle: https://github.com/Oire (tests)

(function (factory) {
    factory(moment);
}(function (moment) {
    return moment.lang('sq', {
        months : "Janar_Shkurt_Mars_Prill_Maj_Qershor_Korrik_Gusht_Shtator_Tetor_NГ«ntor_Dhjetor".split("_"),
        monthsShort : "Jan_Shk_Mar_Pri_Maj_Qer_Kor_Gus_Sht_Tet_NГ«n_Dhj".split("_"),
        weekdays : "E Diel_E HГ«nГ«_E Marte_E MГ«rkure_E Enjte_E Premte_E ShtunГ«".split("_"),
        weekdaysShort : "Die_HГ«n_Mar_MГ«r_Enj_Pre_Sht".split("_"),
        weekdaysMin : "D_H_Ma_MГ«_E_P_Sh".split("_"),
        longDateFormat : {
            LT : "HH:mm",
            L : "DD/MM/YYYY",
            LL : "D MMMM YYYY",
            LLL : "D MMMM YYYY LT",
            LLLL : "dddd, D MMMM YYYY LT"
        },
        calendar : {
            sameDay : '[Sot nГ«] LT',
            nextDay : '[Neser nГ«] LT',
            nextWeek : 'dddd [nГ«] LT',
            lastDay : '[Dje nГ«] LT',
            lastWeek : 'dddd [e kaluar nГ«] LT',
            sameElse : 'L'
        },
        relativeTime : {
            future : "nГ« %s",
            past : "%s me parГ«",
            s : "disa sekonda",
            m : "njГ« minut",
            mm : "%d minuta",
            h : "njГ« orГ«",
            hh : "%d orГ«",
            d : "njГ« ditГ«",
            dd : "%d ditГ«",
            M : "njГ« muaj",
            MM : "%d muaj",
            y : "njГ« vit",
            yy : "%d vite"
        },
        ordinal : '%d.',
        week : {
            dow : 1, // Monday is the first day of the week.
            doy : 4  // The week that contains Jan 4th is the first week of the year.
        }
    });
}));
// moment.js language configuration
// language : swedish (sv)
// author : Jens Alm : https://github.com/ulmus

(function (factory) {
    factory(moment);
}(function (moment) {
    return moment.lang('sv', {
        months : "januari_februari_mars_april_maj_juni_juli_augusti_september_oktober_november_december".split("_"),
        monthsShort : "jan_feb_mar_apr_maj_jun_jul_aug_sep_okt_nov_dec".split("_"),
        weekdays : "sГ¶ndag_mГҐndag_tisdag_onsdag_torsdag_fredag_lГ¶rdag".split("_"),
        weekdaysShort : "sГ¶n_mГҐn_tis_ons_tor_fre_lГ¶r".split("_"),
        weekdaysMin : "sГ¶_mГҐ_ti_on_to_fr_lГ¶".split("_"),
        longDateFormat : {
            LT : "HH:mm",
            L : "YYYY-MM-DD",
            LL : "D MMMM YYYY",
            LLL : "D MMMM YYYY LT",
            LLLL : "dddd D MMMM YYYY LT"
        },
        calendar : {
            sameDay: '[Idag] LT',
            nextDay: '[Imorgon] LT',
            lastDay: '[IgГҐr] LT',
            nextWeek: 'dddd LT',
            lastWeek: '[FГ¶rra] dddd[en] LT',
            sameElse: 'L'
        },
        relativeTime : {
            future : "om %s",
            past : "fГ¶r %s sedan",
            s : "nГҐgra sekunder",
            m : "en minut",
            mm : "%d minuter",
            h : "en timme",
            hh : "%d timmar",
            d : "en dag",
            dd : "%d dagar",
            M : "en mГҐnad",
            MM : "%d mГҐnader",
            y : "ett ГҐr",
            yy : "%d ГҐr"
        },
        ordinal : function (number) {
            var b = number % 10,
                output = (~~ (number % 100 / 10) === 1) ? 'e' :
                (b === 1) ? 'a' :
                (b === 2) ? 'a' :
                (b === 3) ? 'e' : 'e';
            return number + output;
        },
        week : {
            dow : 1, // Monday is the first day of the week.
            doy : 4  // The week that contains Jan 4th is the first week of the year.
        }
    });
}));
// moment.js language configuration
// language : tamil (ta)
// author : Arjunkumar Krishnamoorthy : https://github.com/tk120404

(function (factory) {
    factory(moment);
}(function (moment) {
    /*var symbolMap = {
            '1': 'аЇ§',
            '2': 'аЇЁ',
            '3': 'аЇ©',
            '4': 'аЇЄ',
            '5': 'аЇ«',
            '6': 'аЇ¬',
            '7': 'аЇ­',
            '8': 'аЇ®',
            '9': 'аЇЇ',
            '0': 'аЇ¦'
        },
        numberMap = {
            'аЇ§': '1',
            'аЇЁ': '2',
            'аЇ©': '3',
            'аЇЄ': '4',
            'аЇ«': '5',
            'аЇ¬': '6',
            'аЇ­': '7',
            'аЇ®': '8',
            'аЇЇ': '9',
            'аЇ¦': '0'
        }; */

    return moment.lang('ta', {
        months : 'а®ња®©а®µа®°а®ї_а®Єа®їа®ЄаЇЌа®°а®µа®°а®ї_а®®а®ѕа®°аЇЌа®љаЇЌ_а®Џа®ЄаЇЌа®°а®ІаЇЌ_а®®аЇ‡_а®њаЇ‚а®©аЇЌ_а®њаЇ‚а®ІаЇ€_а®†а®•а®ёаЇЌа®џаЇЌ_а®љаЇ†а®ЄаЇЌа®џаЇ†а®®аЇЌа®Єа®°аЇЌ_а®…а®•аЇЌа®џаЇ‡а®ѕа®Єа®°аЇЌ_а®Ёа®µа®®аЇЌа®Єа®°аЇЌ_а®џа®їа®ља®®аЇЌа®Єа®°аЇЌ'.split("_"),
        monthsShort : 'а®ња®©а®µа®°а®ї_а®Єа®їа®ЄаЇЌа®°а®µа®°а®ї_а®®а®ѕа®°аЇЌа®љаЇЌ_а®Џа®ЄаЇЌа®°а®ІаЇЌ_а®®аЇ‡_а®њаЇ‚а®©аЇЌ_а®њаЇ‚а®ІаЇ€_а®†а®•а®ёаЇЌа®џаЇЌ_а®љаЇ†а®ЄаЇЌа®џаЇ†а®®аЇЌа®Єа®°аЇЌ_а®…а®•аЇЌа®џаЇ‡а®ѕа®Єа®°аЇЌ_а®Ёа®µа®®аЇЌа®Єа®°аЇЌ_а®џа®їа®ља®®аЇЌа®Єа®°аЇЌ'.split("_"),
        weekdays : 'а®ћа®ѕа®Їа®їа®±аЇЌа®±аЇЃа®•аЇЌа®•а®їа®ґа®®аЇ€_а®¤а®їа®™аЇЌа®•а®џаЇЌа®•а®їа®ґа®®аЇ€_а®љаЇ†а®µаЇЌа®µа®ѕа®ЇаЇЌа®•а®їа®ґа®®аЇ€_а®ЄаЇЃа®¤а®©аЇЌа®•а®їа®ґа®®аЇ€_а®µа®їа®Їа®ѕа®ґа®•аЇЌа®•а®їа®ґа®®аЇ€_а®µаЇ†а®іаЇЌа®іа®їа®•аЇЌа®•а®їа®ґа®®аЇ€_а®ља®©а®їа®•аЇЌа®•а®їа®ґа®®аЇ€'.split("_"),
        weekdaysShort : 'а®ћа®ѕа®Їа®їа®±аЇЃ_а®¤а®їа®™аЇЌа®•а®іаЇЌ_а®љаЇ†а®µаЇЌа®µа®ѕа®ЇаЇЌ_а®ЄаЇЃа®¤а®©аЇЌ_а®µа®їа®Їа®ѕа®ґа®©аЇЌ_а®µаЇ†а®іаЇЌа®іа®ї_а®ља®©а®ї'.split("_"),
        weekdaysMin : 'а®ћа®ѕ_а®¤а®ї_а®љаЇ†_а®ЄаЇЃ_а®µа®ї_а®µаЇ†_а®љ'.split("_"),
        longDateFormat : {
            LT : "HH:mm",
            L : "DD/MM/YYYY",
            LL : "D MMMM YYYY",
            LLL : "D MMMM YYYY, LT",
            LLLL : "dddd, D MMMM YYYY, LT"
        },
        calendar : {
            sameDay : '[а®‡а®©аЇЌа®±аЇЃ] LT',
            nextDay : '[а®Ёа®ѕа®іаЇ€] LT',
            nextWeek : 'dddd, LT',
            lastDay : '[а®ЁаЇ‡а®±аЇЌа®±аЇЃ] LT',
            lastWeek : '[а®•а®џа®ЁаЇЌа®¤ а®µа®ѕа®°а®®аЇЌ] dddd, LT',
            sameElse : 'L'
        },
        relativeTime : {
            future : "%s а®‡а®ІаЇЌ",
            past : "%s а®®аЇЃа®©аЇЌ",
            s : "а®’а®°аЇЃ а®ља®їа®І а®µа®їа®Ёа®ѕа®џа®їа®•а®іаЇЌ",
            m : "а®’а®°аЇЃ а®Ёа®їа®®а®їа®џа®®аЇЌ",
            mm : "%d а®Ёа®їа®®а®їа®џа®™аЇЌа®•а®іаЇЌ",
            h : "а®’а®°аЇЃ а®®а®Ја®ї а®ЁаЇ‡а®°а®®аЇЌ",
            hh : "%d а®®а®Ја®ї а®ЁаЇ‡а®°а®®аЇЌ",
            d : "а®’а®°аЇЃ а®Ёа®ѕа®іаЇЌ",
            dd : "%d а®Ёа®ѕа®џаЇЌа®•а®іаЇЌ",
            M : "а®’а®°аЇЃ а®®а®ѕа®¤а®®аЇЌ",
            MM : "%d а®®а®ѕа®¤а®™аЇЌа®•а®іаЇЌ",
            y : "а®’а®°аЇЃ а®µа®°аЇЃа®џа®®аЇЌ",
            yy : "%d а®†а®ЈаЇЌа®џаЇЃа®•а®іаЇЌ"
        },
/*        preparse: function (string) {
            return string.replace(/[аЇ§аЇЁаЇ©аЇЄаЇ«аЇ¬аЇ­аЇ®аЇЇаЇ¦]/g, function (match) {
                return numberMap[match];
            });
        },
        postformat: function (string) {
            return string.replace(/\d/g, function (match) {
                return symbolMap[match];
            });
        },*/
        ordinal : function (number) {
            return number + 'а®µа®¤аЇЃ';
        },


// refer http://ta.wikipedia.org/s/1er1      

        meridiem : function (hour, minute, isLower) {
            if (hour >= 6 && hour <= 10) {
                return " а®•а®ѕа®ІаЇ€";
            } else   if (hour >= 10 && hour <= 14) {
                return " а®Ёа®ЈаЇЌа®Єа®•а®ІаЇЌ";
            } else    if (hour >= 14 && hour <= 18) {
                return " а®Ћа®±аЇЌа®Єа®ѕа®џаЇЃ";
            } else   if (hour >= 18 && hour <= 20) {
                return " а®®а®ѕа®ІаЇ€";
            } else  if (hour >= 20 && hour <= 24) {
                return " а®‡а®°а®µаЇЃ";
            } else  if (hour >= 0 && hour <= 6) {
                return " а®µаЇ€а®•а®±аЇ€";
            }
        },
        week : {
            dow : 0, // Sunday is the first day of the week.
            doy : 6  // The week that contains Jan 1st is the first week of the year.
        }
    });
}));
// moment.js language configuration
// language : thai (th)
// author : Kridsada Thanabulpong : https://github.com/sirn

(function (factory) {
    factory(moment);
}(function (moment) {
    return moment.lang('th', {
        months : "аёЎаёЃаёЈаёІаё„аёЎ_аёЃаёёаёЎаё аёІаёћаё±аё™аёа№Њ_аёЎаёµаё™аёІаё„аёЎ_а№ЂаёЎаё©аёІаёўаё™_аёћаё¤аё©аё аёІаё„аёЎ_аёЎаёґаё–аёёаё™аёІаёўаё™_аёЃаёЈаёЃаёЋаёІаё„аёЎ_аёЄаёґаё‡аё«аёІаё„аёЎ_аёЃаё±аё™аёўаёІаёўаё™_аё•аёёаёҐаёІаё„аёЎ_аёћаё¤аёЁаё€аёґаёЃаёІаёўаё™_аёаё±аё™аё§аёІаё„аёЎ".split("_"),
        monthsShort : "аёЎаёЃаёЈаёІ_аёЃаёёаёЎаё аёІ_аёЎаёµаё™аёІ_а№ЂаёЎаё©аёІ_аёћаё¤аё©аё аёІ_аёЎаёґаё–аёёаё™аёІ_аёЃаёЈаёЃаёЋаёІ_аёЄаёґаё‡аё«аёІ_аёЃаё±аё™аёўаёІ_аё•аёёаёҐаёІ_аёћаё¤аёЁаё€аёґаёЃаёІ_аёаё±аё™аё§аёІ".split("_"),
        weekdays : "аё­аёІаё—аёґаё•аёўа№Њ_аё€аё±аё™аё—аёЈа№Њ_аё­аё±аё‡аё„аёІаёЈ_аёћаёёаё_аёћаё¤аё«аё±аёЄаёљаё”аёµ_аёЁаёёаёЃаёЈа№Њ_а№ЂаёЄаёІаёЈа№Њ".split("_"),
        weekdaysShort : "аё­аёІаё—аёґаё•аёўа№Њ_аё€аё±аё™аё—аёЈа№Њ_аё­аё±аё‡аё„аёІаёЈ_аёћаёёаё_аёћаё¤аё«аё±аёЄ_аёЁаёёаёЃаёЈа№Њ_а№ЂаёЄаёІаёЈа№Њ".split("_"), // yes, three characters difference
        weekdaysMin : "аё­аёІ._аё€._аё­._аёћ._аёћаё¤._аёЁ._аёЄ.".split("_"),
        longDateFormat : {
            LT : "H аё™аёІаё¬аёґаёЃаёІ m аё™аёІаё—аёµ",
            L : "YYYY/MM/DD",
            LL : "D MMMM YYYY",
            LLL : "D MMMM YYYY а№Ђаё§аёҐаёІ LT",
            LLLL : "аё§аё±аё™ddddаё—аёµа№€ D MMMM YYYY а№Ђаё§аёҐаёІ LT"
        },
        meridiem : function (hour, minute, isLower) {
            if (hour < 12) {
                return "аёЃа№€аё­аё™а№Ђаё—аёµа№€аёўаё‡";
            } else {
                return "аё«аёҐаё±аё‡а№Ђаё—аёµа№€аёўаё‡";
            }
        },
        calendar : {
            sameDay : '[аё§аё±аё™аё™аёµа№‰ а№Ђаё§аёҐаёІ] LT',
            nextDay : '[аёћаёЈаёёа№€аё‡аё™аёµа№‰ а№Ђаё§аёҐаёІ] LT',
            nextWeek : 'dddd[аё«аё™а№‰аёІ а№Ђаё§аёҐаёІ] LT',
            lastDay : '[а№ЂаёЎаё·а№€аё­аё§аёІаё™аё™аёµа№‰ а№Ђаё§аёҐаёІ] LT',
            lastWeek : '[аё§аё±аё™]dddd[аё—аёµа№€а№ЃаёҐа№‰аё§ а№Ђаё§аёҐаёІ] LT',
            sameElse : 'L'
        },
        relativeTime : {
            future : "аё­аёµаёЃ %s",
            past : "%sаё—аёµа№€а№ЃаёҐа№‰аё§",
            s : "а№„аёЎа№€аёЃаёµа№€аё§аёґаё™аёІаё—аёµ",
            m : "1 аё™аёІаё—аёµ",
            mm : "%d аё™аёІаё—аёµ",
            h : "1 аёЉаё±а№€аё§а№‚аёЎаё‡",
            hh : "%d аёЉаё±а№€аё§а№‚аёЎаё‡",
            d : "1 аё§аё±аё™",
            dd : "%d аё§аё±аё™",
            M : "1 а№Ђаё”аё·аё­аё™",
            MM : "%d а№Ђаё”аё·аё­аё™",
            y : "1 аё›аёµ",
            yy : "%d аё›аёµ"
        }
    });
}));
// moment.js language configuration
// language : Tagalog/Filipino (tl-ph)
// author : Dan Hagman

(function (factory) {
    factory(moment);
}(function (moment) {
    return moment.lang('tl-ph', {
        months : "Enero_Pebrero_Marso_Abril_Mayo_Hunyo_Hulyo_Agosto_Setyembre_Oktubre_Nobyembre_Disyembre".split("_"),
        monthsShort : "Ene_Peb_Mar_Abr_May_Hun_Hul_Ago_Set_Okt_Nob_Dis".split("_"),
        weekdays : "Linggo_Lunes_Martes_Miyerkules_Huwebes_Biyernes_Sabado".split("_"),
        weekdaysShort : "Lin_Lun_Mar_Miy_Huw_Biy_Sab".split("_"),
        weekdaysMin : "Li_Lu_Ma_Mi_Hu_Bi_Sab".split("_"),
        longDateFormat : {
            LT : "HH:mm",
            L : "MM/D/YYYY",
            LL : "MMMM D, YYYY",
            LLL : "MMMM D, YYYY LT",
            LLLL : "dddd, MMMM DD, YYYY LT"
        },
        calendar : {
            sameDay: "[Ngayon sa] LT",
            nextDay: '[Bukas sa] LT',
            nextWeek: 'dddd [sa] LT',
            lastDay: '[Kahapon sa] LT',
            lastWeek: 'dddd [huling linggo] LT',
            sameElse: 'L'
        },
        relativeTime : {
            future : "sa loob ng %s",
            past : "%s ang nakalipas",
            s : "ilang segundo",
            m : "isang minuto",
            mm : "%d minuto",
            h : "isang oras",
            hh : "%d oras",
            d : "isang araw",
            dd : "%d araw",
            M : "isang buwan",
            MM : "%d buwan",
            y : "isang taon",
            yy : "%d taon"
        },
        ordinal : function (number) {
            return number;
        },
        week : {
            dow : 1, // Monday is the first day of the week.
            doy : 4  // The week that contains Jan 4th is the first week of the year.
        }
    });
}));
// moment.js language configuration
// language : turkish (tr)
// authors : Erhan Gundogan : https://github.com/erhangundogan,
//           Burak YiДџit Kaya: https://github.com/BYK

(function (factory) {
    factory(moment);
}(function (moment) {

    var suffixes = {
        1: "'inci",
        5: "'inci",
        8: "'inci",
        70: "'inci",
        80: "'inci",

        2: "'nci",
        7: "'nci",
        20: "'nci",
        50: "'nci",

        3: "'ГјncГј",
        4: "'ГјncГј",
        100: "'ГјncГј",

        6: "'ncД±",

        9: "'uncu",
        10: "'uncu",
        30: "'uncu",

        60: "'Д±ncД±",
        90: "'Д±ncД±"
    };

    return moment.lang('tr', {
        months : "Ocak_Ећubat_Mart_Nisan_MayД±s_Haziran_Temmuz_AДџustos_EylГјl_Ekim_KasД±m_AralД±k".split("_"),
        monthsShort : "Oca_Ећub_Mar_Nis_May_Haz_Tem_AДџu_Eyl_Eki_Kas_Ara".split("_"),
        weekdays : "Pazar_Pazartesi_SalД±_Г‡arЕџamba_PerЕџembe_Cuma_Cumartesi".split("_"),
        weekdaysShort : "Paz_Pts_Sal_Г‡ar_Per_Cum_Cts".split("_"),
        weekdaysMin : "Pz_Pt_Sa_Г‡a_Pe_Cu_Ct".split("_"),
        longDateFormat : {
            LT : "HH:mm",
            L : "DD.MM.YYYY",
            LL : "D MMMM YYYY",
            LLL : "D MMMM YYYY LT",
            LLLL : "dddd, D MMMM YYYY LT"
        },
        calendar : {
            sameDay : '[bugГјn saat] LT',
            nextDay : '[yarД±n saat] LT',
            nextWeek : '[haftaya] dddd [saat] LT',
            lastDay : '[dГјn] LT',
            lastWeek : '[geГ§en hafta] dddd [saat] LT',
            sameElse : 'L'
        },
        relativeTime : {
            future : "%s sonra",
            past : "%s Г¶nce",
            s : "birkaГ§ saniye",
            m : "bir dakika",
            mm : "%d dakika",
            h : "bir saat",
            hh : "%d saat",
            d : "bir gГјn",
            dd : "%d gГјn",
            M : "bir ay",
            MM : "%d ay",
            y : "bir yД±l",
            yy : "%d yД±l"
        },
        ordinal : function (number) {
            if (number === 0) {  // special case for zero
                return number + "'Д±ncД±";
            }
            var a = number % 10,
                b = number % 100 - a,
                c = number >= 100 ? 100 : null;

            return number + (suffixes[a] || suffixes[b] || suffixes[c]);
        },
        week : {
            dow : 1, // Monday is the first day of the week.
            doy : 7  // The week that contains Jan 1st is the first week of the year.
        }
    });
}));
// moment.js language configuration
// language : Morocco Central Atlas TamaziЙЈt in Latin (tzm-la)
// author : Abdel Said : https://github.com/abdelsaid

(function (factory) {
    factory(moment);
}(function (moment) {
    return moment.lang('tzm-la', {
        months : "innayr_brЛ¤ayrЛ¤_marЛ¤sЛ¤_ibrir_mayyw_ywnyw_ywlywz_ЙЈwЕЎt_ЕЎwtanbir_ktЛ¤wbrЛ¤_nwwanbir_dwjnbir".split("_"),
        monthsShort : "innayr_brЛ¤ayrЛ¤_marЛ¤sЛ¤_ibrir_mayyw_ywnyw_ywlywz_ЙЈwЕЎt_ЕЎwtanbir_ktЛ¤wbrЛ¤_nwwanbir_dwjnbir".split("_"),
        weekdays : "asamas_aynas_asinas_akras_akwas_asimwas_asiбёЌyas".split("_"),
        weekdaysShort : "asamas_aynas_asinas_akras_akwas_asimwas_asiбёЌyas".split("_"),
        weekdaysMin : "asamas_aynas_asinas_akras_akwas_asimwas_asiбёЌyas".split("_"),
        longDateFormat : {
            LT : "HH:mm",
            L : "DD/MM/YYYY",
            LL : "D MMMM YYYY",
            LLL : "D MMMM YYYY LT",
            LLLL : "dddd D MMMM YYYY LT"
        },
        calendar : {
            sameDay: "[asdkh g] LT",
            nextDay: '[aska g] LT',
            nextWeek: 'dddd [g] LT',
            lastDay: '[assant g] LT',
            lastWeek: 'dddd [g] LT',
            sameElse: 'L'
        },
        relativeTime : {
            future : "dadkh s yan %s",
            past : "yan %s",
            s : "imik",
            m : "minuбёЌ",
            mm : "%d minuбёЌ",
            h : "saЙ›a",
            hh : "%d tassaЙ›in",
            d : "ass",
            dd : "%d ossan",
            M : "ayowr",
            MM : "%d iyyirn",
            y : "asgas",
            yy : "%d isgasn"
        },
        week : {
            dow : 6, // Saturday is the first day of the week.
            doy : 12  // The week that contains Jan 1st is the first week of the year.
        }
    });
}));
// moment.js language configuration
// language : Morocco Central Atlas TamaziЙЈt (tzm)
// author : Abdel Said : https://github.com/abdelsaid

(function (factory) {
    factory(moment);
}(function (moment) {
    return moment.lang('tzm', {
        months : "вµ‰вµЏвµЏвґ°вµўвµ”_вґ±вµ•вґ°вµўвµ•_вµЋвґ°вµ•вµљ_вµ‰вґ±вµ”вµ‰вµ”_вµЋвґ°вµўвµўвµ“_вµўвµ“вµЏвµўвµ“_вµўвµ“вµЌвµўвµ“вµЈ_вµ–вµ“вµ›вµњ_вµ›вµ“вµњвґ°вµЏвґ±вµ‰вµ”_вґЅвµџвµ“вґ±вµ•_вµЏвµ“вµЎвґ°вµЏвґ±вµ‰вµ”_вґ·вµ“вµЉвµЏвґ±вµ‰вµ”".split("_"),
        monthsShort : "вµ‰вµЏвµЏвґ°вµўвµ”_вґ±вµ•вґ°вµўвµ•_вµЋвґ°вµ•вµљ_вµ‰вґ±вµ”вµ‰вµ”_вµЋвґ°вµўвµўвµ“_вµўвµ“вµЏвµўвµ“_вµўвµ“вµЌвµўвµ“вµЈ_вµ–вµ“вµ›вµњ_вµ›вµ“вµњвґ°вµЏвґ±вµ‰вµ”_вґЅвµџвµ“вґ±вµ•_вµЏвµ“вµЎвґ°вµЏвґ±вµ‰вµ”_вґ·вµ“вµЉвµЏвґ±вµ‰вµ”".split("_"),
        weekdays : "вґ°вµ™вґ°вµЋвґ°вµ™_вґ°вµўвµЏвґ°вµ™_вґ°вµ™вµ‰вµЏвґ°вµ™_вґ°вґЅвµ”вґ°вµ™_вґ°вґЅвµЎвґ°вµ™_вґ°вµ™вµ‰вµЋвµЎвґ°вµ™_вґ°вµ™вµ‰вґ№вµўвґ°вµ™".split("_"),
        weekdaysShort : "вґ°вµ™вґ°вµЋвґ°вµ™_вґ°вµўвµЏвґ°вµ™_вґ°вµ™вµ‰вµЏвґ°вµ™_вґ°вґЅвµ”вґ°вµ™_вґ°вґЅвµЎвґ°вµ™_вґ°вµ™вµ‰вµЋвµЎвґ°вµ™_вґ°вµ™вµ‰вґ№вµўвґ°вµ™".split("_"),
        weekdaysMin : "вґ°вµ™вґ°вµЋвґ°вµ™_вґ°вµўвµЏвґ°вµ™_вґ°вµ™вµ‰вµЏвґ°вµ™_вґ°вґЅвµ”вґ°вµ™_вґ°вґЅвµЎвґ°вµ™_вґ°вµ™вµ‰вµЋвµЎвґ°вµ™_вґ°вµ™вµ‰вґ№вµўвґ°вµ™".split("_"),
        longDateFormat : {
            LT : "HH:mm",
            L : "DD/MM/YYYY",
            LL : "D MMMM YYYY",
            LLL : "D MMMM YYYY LT",
            LLLL : "dddd D MMMM YYYY LT"
        },
        calendar : {
            sameDay: "[вґ°вµ™вґ·вµ… вґґ] LT",
            nextDay: '[вґ°вµ™вґЅвґ° вґґ] LT',
            nextWeek: 'dddd [вґґ] LT',
            lastDay: '[вґ°вµљвґ°вµЏвµњ вґґ] LT',
            lastWeek: 'dddd [вґґ] LT',
            sameElse: 'L'
        },
        relativeTime : {
            future : "вґ·вґ°вґ·вµ… вµ™ вµўвґ°вµЏ %s",
            past : "вµўвґ°вµЏ %s",
            s : "вµ‰вµЋвµ‰вґЅ",
            m : "вµЋвµ‰вµЏвµ“вґє",
            mm : "%d вµЋвµ‰вµЏвµ“вґє",
            h : "вµ™вґ°вµ„вґ°",
            hh : "%d вµњвґ°вµ™вµ™вґ°вµ„вµ‰вµЏ",
            d : "вґ°вµ™вµ™",
            dd : "%d oвµ™вµ™вґ°вµЏ",
            M : "вґ°вµўoвµ“вµ”",
            MM : "%d вµ‰вµўвµўвµ‰вµ”вµЏ",
            y : "вґ°вµ™вґівґ°вµ™",
            yy : "%d вµ‰вµ™вґівґ°вµ™вµЏ"
        },
        week : {
            dow : 6, // Saturday is the first day of the week.
            doy : 12  // The week that contains Jan 1st is the first week of the year.
        }
    });
}));
// moment.js language configuration
// language : ukrainian (uk)
// author : zemlanin : https://github.com/zemlanin
// Author : Menelion ElensГєle : https://github.com/Oire

(function (factory) {
    factory(moment);
}(function (moment) {
    function plural(word, num) {
        var forms = word.split('_');
        return num % 10 === 1 && num % 100 !== 11 ? forms[0] : (num % 10 >= 2 && num % 10 <= 4 && (num % 100 < 10 || num % 100 >= 20) ? forms[1] : forms[2]);
    }

    function relativeTimeWithPlural(number, withoutSuffix, key) {
        var format = {
            'mm': 'С…РІРёР»РёРЅР°_С…РІРёР»РёРЅРё_С…РІРёР»РёРЅ',
            'hh': 'РіРѕРґРёРЅР°_РіРѕРґРёРЅРё_РіРѕРґРёРЅ',
            'dd': 'РґРµРЅСЊ_РґРЅС–_РґРЅС–РІ',
            'MM': 'РјС–СЃСЏС†СЊ_РјС–СЃСЏС†С–_РјС–СЃСЏС†С–РІ',
            'yy': 'СЂС–Рє_СЂРѕРєРё_СЂРѕРєС–РІ'
        };
        if (key === 'm') {
            return withoutSuffix ? 'С…РІРёР»РёРЅР°' : 'С…РІРёР»РёРЅСѓ';
        }
        else if (key === 'h') {
            return withoutSuffix ? 'РіРѕРґРёРЅР°' : 'РіРѕРґРёРЅСѓ';
        }
        else {
            return number + ' ' + plural(format[key], +number);
        }
    }

    function monthsCaseReplace(m, format) {
        var months = {
            'nominative': 'СЃС–С‡РµРЅСЊ_Р»СЋС‚РёР№_Р±РµСЂРµР·РµРЅСЊ_РєРІС–С‚РµРЅСЊ_С‚СЂР°РІРµРЅСЊ_С‡РµСЂРІРµРЅСЊ_Р»РёРїРµРЅСЊ_СЃРµСЂРїРµРЅСЊ_РІРµСЂРµСЃРµРЅСЊ_Р¶РѕРІС‚РµРЅСЊ_Р»РёСЃС‚РѕРїР°Рґ_РіСЂСѓРґРµРЅСЊ'.split('_'),
            'accusative': 'СЃС–С‡РЅСЏ_Р»СЋС‚РѕРіРѕ_Р±РµСЂРµР·РЅСЏ_РєРІС–С‚РЅСЏ_С‚СЂР°РІРЅСЏ_С‡РµСЂРІРЅСЏ_Р»РёРїРЅСЏ_СЃРµСЂРїРЅСЏ_РІРµСЂРµСЃРЅСЏ_Р¶РѕРІС‚РЅСЏ_Р»РёСЃС‚РѕРїР°РґР°_РіСЂСѓРґРЅСЏ'.split('_')
        },

        nounCase = (/D[oD]? *MMMM?/).test(format) ?
            'accusative' :
            'nominative';

        return months[nounCase][m.month()];
    }

    function weekdaysCaseReplace(m, format) {
        var weekdays = {
            'nominative': 'РЅРµРґС–Р»СЏ_РїРѕРЅРµРґС–Р»РѕРє_РІС–РІС‚РѕСЂРѕРє_СЃРµСЂРµРґР°_С‡РµС‚РІРµСЂ_РївЂ™СЏС‚РЅРёС†СЏ_СЃСѓР±РѕС‚Р°'.split('_'),
            'accusative': 'РЅРµРґС–Р»СЋ_РїРѕРЅРµРґС–Р»РѕРє_РІС–РІС‚РѕСЂРѕРє_СЃРµСЂРµРґСѓ_С‡РµС‚РІРµСЂ_РївЂ™СЏС‚РЅРёС†СЋ_СЃСѓР±РѕС‚Сѓ'.split('_'),
            'genitive': 'РЅРµРґС–Р»С–_РїРѕРЅРµРґС–Р»РєР°_РІС–РІС‚РѕСЂРєР°_СЃРµСЂРµРґРё_С‡РµС‚РІРµСЂРіР°_РївЂ™СЏС‚РЅРёС†С–_СЃСѓР±РѕС‚Рё'.split('_')
        },

        nounCase = (/(\[[Р’РІРЈСѓ]\]) ?dddd/).test(format) ?
            'accusative' :
            ((/\[?(?:РјРёРЅСѓР»РѕС—|РЅР°СЃС‚СѓРїРЅРѕС—)? ?\] ?dddd/).test(format) ?
                'genitive' :
                'nominative');

        return weekdays[nounCase][m.day()];
    }

    function processHoursFunction(str) {
        return function () {
            return str + 'Рѕ' + (this.hours() === 11 ? 'Р±' : '') + '] LT';
        };
    }

    return moment.lang('uk', {
        months : monthsCaseReplace,
        monthsShort : "СЃС–С‡_Р»СЋС‚_Р±РµСЂ_РєРІС–С‚_С‚СЂР°РІ_С‡РµСЂРІ_Р»РёРї_СЃРµСЂРї_РІРµСЂ_Р¶РѕРІС‚_Р»РёСЃС‚_РіСЂСѓРґ".split("_"),
        weekdays : weekdaysCaseReplace,
        weekdaysShort : "РЅРґ_РїРЅ_РІС‚_СЃСЂ_С‡С‚_РїС‚_СЃР±".split("_"),
        weekdaysMin : "РЅРґ_РїРЅ_РІС‚_СЃСЂ_С‡С‚_РїС‚_СЃР±".split("_"),
        longDateFormat : {
            LT : "HH:mm",
            L : "DD.MM.YYYY",
            LL : "D MMMM YYYY СЂ.",
            LLL : "D MMMM YYYY СЂ., LT",
            LLLL : "dddd, D MMMM YYYY СЂ., LT"
        },
        calendar : {
            sameDay: processHoursFunction('[РЎСЊРѕРіРѕРґРЅС– '),
            nextDay: processHoursFunction('[Р—Р°РІС‚СЂР° '),
            lastDay: processHoursFunction('[Р’С‡РѕСЂР° '),
            nextWeek: processHoursFunction('[РЈ] dddd ['),
            lastWeek: function () {
                switch (this.day()) {
                case 0:
                case 3:
                case 5:
                case 6:
                    return processHoursFunction('[РњРёРЅСѓР»РѕС—] dddd [').call(this);
                case 1:
                case 2:
                case 4:
                    return processHoursFunction('[РњРёРЅСѓР»РѕРіРѕ] dddd [').call(this);
                }
            },
            sameElse: 'L'
        },
        relativeTime : {
            future : "Р·Р° %s",
            past : "%s С‚РѕРјСѓ",
            s : "РґРµРєС–Р»СЊРєР° СЃРµРєСѓРЅРґ",
            m : relativeTimeWithPlural,
            mm : relativeTimeWithPlural,
            h : "РіРѕРґРёРЅСѓ",
            hh : relativeTimeWithPlural,
            d : "РґРµРЅСЊ",
            dd : relativeTimeWithPlural,
            M : "РјС–СЃСЏС†СЊ",
            MM : relativeTimeWithPlural,
            y : "СЂС–Рє",
            yy : relativeTimeWithPlural
        },

        // M. E.: those two are virtually unused but a user might want to implement them for his/her website for some reason

        meridiem : function (hour, minute, isLower) {
            if (hour < 4) {
                return "РЅРѕС‡С–";
            } else if (hour < 12) {
                return "СЂР°РЅРєСѓ";
            } else if (hour < 17) {
                return "РґРЅСЏ";
            } else {
                return "РІРµС‡РѕСЂР°";
            }
        },

        ordinal: function (number, period) {
            switch (period) {
            case 'M':
            case 'd':
            case 'DDD':
            case 'w':
            case 'W':
                return number + '-Р№';
            case 'D':
                return number + '-РіРѕ';
            default:
                return number;
            }
        },

        week : {
            dow : 1, // Monday is the first day of the week.
            doy : 7  // The week that contains Jan 1st is the first week of the year.
        }
    });
}));
// moment.js language configuration
// language : uzbek
// author : Sardor Muminov : https://github.com/muminoff

(function (factory) {
    factory(moment);
}(function (moment) {
    return moment.lang('uz', {
        months : "СЏРЅРІР°СЂСЊ_С„РµРІСЂР°Р»СЊ_РјР°СЂС‚_Р°РїСЂРµР»СЊ_РјР°Р№_РёСЋРЅСЊ_РёСЋР»СЊ_Р°РІРіСѓСЃС‚_СЃРµРЅС‚СЏР±СЂСЊ_РѕРєС‚СЏР±СЂСЊ_РЅРѕСЏР±СЂСЊ_РґРµРєР°Р±СЂСЊ".split("_"),
        monthsShort : "СЏРЅРІ_С„РµРІ_РјР°СЂ_Р°РїСЂ_РјР°Р№_РёСЋРЅ_РёСЋР»_Р°РІРі_СЃРµРЅ_РѕРєС‚_РЅРѕСЏ_РґРµРє".split("_"),
        weekdays : "РЇРєС€Р°РЅР±Р°_Р”СѓС€Р°РЅР±Р°_РЎРµС€Р°РЅР±Р°_Р§РѕСЂС€Р°РЅР±Р°_РџР°Р№С€Р°РЅР±Р°_Р–СѓРјР°_РЁР°РЅР±Р°".split("_"),
        weekdaysShort : "РЇРєС€_Р”СѓС€_РЎРµС€_Р§РѕСЂ_РџР°Р№_Р–СѓРј_РЁР°РЅ".split("_"),
        weekdaysMin : "РЇРє_Р”Сѓ_РЎРµ_Р§Рѕ_РџР°_Р–Сѓ_РЁР°".split("_"),
        longDateFormat : {
            LT : "HH:mm",
            L : "DD/MM/YYYY",
            LL : "D MMMM YYYY",
            LLL : "D MMMM YYYY LT",
            LLLL : "D MMMM YYYY, dddd LT"
        },
        calendar : {
            sameDay : '[Р‘СѓРіСѓРЅ СЃРѕР°С‚] LT [РґР°]',
            nextDay : '[Р­СЂС‚Р°РіР°] LT [РґР°]',
            nextWeek : 'dddd [РєСѓРЅРё СЃРѕР°С‚] LT [РґР°]',
            lastDay : '[РљРµС‡Р° СЃРѕР°С‚] LT [РґР°]',
            lastWeek : '[РЈС‚РіР°РЅ] dddd [РєСѓРЅРё СЃРѕР°С‚] LT [РґР°]',
            sameElse : 'L'
        },
        relativeTime : {
            future : "РЇРєРёРЅ %s РёС‡РёРґР°",
            past : "Р‘РёСЂ РЅРµС‡Р° %s РѕР»РґРёРЅ",
            s : "С„СѓСЂСЃР°С‚",
            m : "Р±РёСЂ РґР°РєРёРєР°",
            mm : "%d РґР°РєРёРєР°",
            h : "Р±РёСЂ СЃРѕР°С‚",
            hh : "%d СЃРѕР°С‚",
            d : "Р±РёСЂ РєСѓРЅ",
            dd : "%d РєСѓРЅ",
            M : "Р±РёСЂ РѕР№",
            MM : "%d РѕР№",
            y : "Р±РёСЂ Р№РёР»",
            yy : "%d Р№РёР»"
        },
        week : {
            dow : 1, // Monday is the first day of the week.
            doy : 7  // The week that contains Jan 4th is the first week of the year.
        }
    });
}));
// moment.js language configuration
// language : vietnamese (vn)
// author : Bang Nguyen : https://github.com/bangnk

(function (factory) {
    factory(moment);
}(function (moment) {
    return moment.lang('vn', {
        months : "thГЎng 1_thГЎng 2_thГЎng 3_thГЎng 4_thГЎng 5_thГЎng 6_thГЎng 7_thГЎng 8_thГЎng 9_thГЎng 10_thГЎng 11_thГЎng 12".split("_"),
        monthsShort : "Th01_Th02_Th03_Th04_Th05_Th06_Th07_Th08_Th09_Th10_Th11_Th12".split("_"),
        weekdays : "chб»§ nhбє­t_thб»© hai_thб»© ba_thб»© tЖ°_thб»© nДѓm_thб»© sГЎu_thб»© bбєЈy".split("_"),
        weekdaysShort : "CN_T2_T3_T4_T5_T6_T7".split("_"),
        weekdaysMin : "CN_T2_T3_T4_T5_T6_T7".split("_"),
        longDateFormat : {
            LT : "HH:mm",
            L : "DD/MM/YYYY",
            LL : "D MMMM [nДѓm] YYYY",
            LLL : "D MMMM [nДѓm] YYYY LT",
            LLLL : "dddd, D MMMM [nДѓm] YYYY LT",
            l : "DD/M/YYYY",
            ll : "D MMM YYYY",
            lll : "D MMM YYYY LT",
            llll : "ddd, D MMM YYYY LT"
        },
        calendar : {
            sameDay: "[HГґm nay lГєc] LT",
            nextDay: '[NgГ y mai lГєc] LT',
            nextWeek: 'dddd [tuбє§n tб»›i lГєc] LT',
            lastDay: '[HГґm qua lГєc] LT',
            lastWeek: 'dddd [tuбє§n rб»“i lГєc] LT',
            sameElse: 'L'
        },
        relativeTime : {
            future : "%s tб»›i",
            past : "%s trЖ°б»›c",
            s : "vГ i giГўy",
            m : "mб»™t phГєt",
            mm : "%d phГєt",
            h : "mб»™t giб»ќ",
            hh : "%d giб»ќ",
            d : "mб»™t ngГ y",
            dd : "%d ngГ y",
            M : "mб»™t thГЎng",
            MM : "%d thГЎng",
            y : "mб»™t nДѓm",
            yy : "%d nДѓm"
        },
        ordinal : function (number) {
            return number;
        },
        week : {
            dow : 1, // Monday is the first day of the week.
            doy : 4  // The week that contains Jan 4th is the first week of the year.
        }
    });
}));
// moment.js language configuration
// language : chinese
// author : suupic : https://github.com/suupic
// author : Zeno Zeng : https://github.com/zenozeng

(function (factory) {
    factory(moment);
}(function (moment) {
    return moment.lang('zh-cn', {
        months : "дёЂжњ€_дєЊжњ€_дё‰жњ€_е››жњ€_дє”жњ€_е…­жњ€_дёѓжњ€_е…«жњ€_д№ќжњ€_еЌЃжњ€_еЌЃдёЂжњ€_еЌЃдєЊжњ€".split("_"),
        monthsShort : "1жњ€_2жњ€_3жњ€_4жњ€_5жњ€_6жњ€_7жњ€_8жњ€_9жњ€_10жњ€_11жњ€_12жњ€".split("_"),
        weekdays : "жџжњџж—Ґ_жџжњџдёЂ_жџжњџдєЊ_жџжњџдё‰_жџжњџе››_жџжњџдє”_жџжњџе…­".split("_"),
        weekdaysShort : "е‘Ёж—Ґ_е‘ЁдёЂ_е‘ЁдєЊ_е‘Ёдё‰_е‘Ёе››_е‘Ёдє”_е‘Ёе…­".split("_"),
        weekdaysMin : "ж—Ґ_дёЂ_дєЊ_дё‰_е››_дє”_е…­".split("_"),
        longDateFormat : {
            LT : "Ahз‚№mm",
            L : "YYYY-MM-DD",
            LL : "YYYYе№ґMMMDж—Ґ",
            LLL : "YYYYе№ґMMMDж—ҐLT",
            LLLL : "YYYYе№ґMMMDж—ҐddddLT",
            l : "YYYY-MM-DD",
            ll : "YYYYе№ґMMMDж—Ґ",
            lll : "YYYYе№ґMMMDж—ҐLT",
            llll : "YYYYе№ґMMMDж—ҐddddLT"
        },
        meridiem : function (hour, minute, isLower) {
            var hm = hour * 100 + minute;
            if (hm < 600) {
                return "е‡Њж™Ё";
            } else if (hm < 900) {
                return "ж—©дёЉ";
            } else if (hm < 1130) {
                return "дёЉеЌ€";
            } else if (hm < 1230) {
                return "дё­еЌ€";
            } else if (hm < 1800) {
                return "дё‹еЌ€";
            } else {
                return "ж™љдёЉ";
            }
        },
        calendar : {
            sameDay : function () {
                return this.minutes() === 0 ? "[д»Ље¤©]Ah[з‚№ж•ґ]" : "[д»Ље¤©]LT";
            },
            nextDay : function () {
                return this.minutes() === 0 ? "[жЋе¤©]Ah[з‚№ж•ґ]" : "[жЋе¤©]LT";
            },
            lastDay : function () {
                return this.minutes() === 0 ? "[жЁе¤©]Ah[з‚№ж•ґ]" : "[жЁе¤©]LT";
            },
            nextWeek : function () {
                var startOfWeek, prefix;
                startOfWeek = moment().startOf('week');
                prefix = this.unix() - startOfWeek.unix() >= 7 * 24 * 3600 ? '[дё‹]' : '[жњ¬]';
                return this.minutes() === 0 ? prefix + "dddAhз‚№ж•ґ" : prefix + "dddAhз‚№mm";
            },
            lastWeek : function () {
                var startOfWeek, prefix;
                startOfWeek = moment().startOf('week');
                prefix = this.unix() < startOfWeek.unix()  ? '[дёЉ]' : '[жњ¬]';
                return this.minutes() === 0 ? prefix + "dddAhз‚№ж•ґ" : prefix + "dddAhз‚№mm";
            },
            sameElse : 'LL'
        },
        ordinal : function (number, period) {
            switch (period) {
            case "d":
            case "D":
            case "DDD":
                return number + "ж—Ґ";
            case "M":
                return number + "жњ€";
            case "w":
            case "W":
                return number + "е‘Ё";
            default:
                return number;
            }
        },
        relativeTime : {
            future : "%sе†…",
            past : "%sе‰Ќ",
            s : "е‡ з§’",
            m : "1е€†й’џ",
            mm : "%dе€†й’џ",
            h : "1е°Џж—¶",
            hh : "%dе°Џж—¶",
            d : "1е¤©",
            dd : "%dе¤©",
            M : "1дёЄжњ€",
            MM : "%dдёЄжњ€",
            y : "1е№ґ",
            yy : "%dе№ґ"
        },
        week : {
            // GB/T 7408-1994гЂЉж•°жЌ®е…ѓе’Њдє¤жЌўж јејЏВ·дїЎжЃЇдє¤жЌўВ·ж—Ґжњџе’Њж—¶й—ґиЎЁз¤єжі•гЂ‹дёЋISO 8601:1988з­‰ж•€
            dow : 1, // Monday is the first day of the week.
            doy : 4  // The week that contains Jan 4th is the first week of the year.
        }
    });
}));
// moment.js language configuration
// language : traditional chinese (zh-tw)
// author : Ben : https://github.com/ben-lin

(function (factory) {
    factory(moment);
}(function (moment) {
    return moment.lang('zh-tw', {
        months : "дёЂжњ€_дєЊжњ€_дё‰жњ€_е››жњ€_дє”жњ€_е…­жњ€_дёѓжњ€_е…«жњ€_д№ќжњ€_еЌЃжњ€_еЌЃдёЂжњ€_еЌЃдєЊжњ€".split("_"),
        monthsShort : "1жњ€_2жњ€_3жњ€_4жњ€_5жњ€_6жњ€_7жњ€_8жњ€_9жњ€_10жњ€_11жњ€_12жњ€".split("_"),
        weekdays : "жџжњџж—Ґ_жџжњџдёЂ_жџжњџдєЊ_жџжњџдё‰_жџжњџе››_жџжњџдє”_жџжњџе…­".split("_"),
        weekdaysShort : "йЂ±ж—Ґ_йЂ±дёЂ_йЂ±дєЊ_йЂ±дё‰_йЂ±е››_йЂ±дє”_йЂ±е…­".split("_"),
        weekdaysMin : "ж—Ґ_дёЂ_дєЊ_дё‰_е››_дє”_е…­".split("_"),
        longDateFormat : {
            LT : "Ahй»ћmm",
            L : "YYYYе№ґMMMDж—Ґ",
            LL : "YYYYе№ґMMMDж—Ґ",
            LLL : "YYYYе№ґMMMDж—ҐLT",
            LLLL : "YYYYе№ґMMMDж—ҐddddLT",
            l : "YYYYе№ґMMMDж—Ґ",
            ll : "YYYYе№ґMMMDж—Ґ",
            lll : "YYYYе№ґMMMDж—ҐLT",
            llll : "YYYYе№ґMMMDж—ҐddddLT"
        },
        meridiem : function (hour, minute, isLower) {
            var hm = hour * 100 + minute;
            if (hm < 900) {
                return "ж—©дёЉ";
            } else if (hm < 1130) {
                return "дёЉеЌ€";
            } else if (hm < 1230) {
                return "дё­еЌ€";
            } else if (hm < 1800) {
                return "дё‹еЌ€";
            } else {
                return "ж™љдёЉ";
            }
        },
        calendar : {
            sameDay : '[д»Ље¤©]LT',
            nextDay : '[жЋе¤©]LT',
            nextWeek : '[дё‹]ddddLT',
            lastDay : '[жЁе¤©]LT',
            lastWeek : '[дёЉ]ddddLT',
            sameElse : 'L'
        },
        ordinal : function (number, period) {
            switch (period) {
            case "d" :
            case "D" :
            case "DDD" :
                return number + "ж—Ґ";
            case "M" :
                return number + "жњ€";
            case "w" :
            case "W" :
                return number + "йЂ±";
            default :
                return number;
            }
        },
        relativeTime : {
            future : "%sе…§",
            past : "%sе‰Ќ",
            s : "е№ѕз§’",
            m : "дёЂе€†йђ",
            mm : "%dе€†йђ",
            h : "дёЂе°Џж™‚",
            hh : "%dе°Џж™‚",
            d : "дёЂе¤©",
            dd : "%dе¤©",
            M : "дёЂеЂ‹жњ€",
            MM : "%dеЂ‹жњ€",
            y : "дёЂе№ґ",
            yy : "%dе№ґ"
        }
    });
}));

    moment.lang('en');


    /************************************
        Exposing Moment
    ************************************/

    function makeGlobal(deprecate) {
        var warned = false, local_moment = moment;
        /*global ender:false */
        if (typeof ender !== 'undefined') {
            return;
        }
        // here, `this` means `window` in the browser, or `global` on the server
        // add `moment` as a global object via a string identifier,
        // for Closure Compiler "advanced" mode
        if (deprecate) {
            global.moment = function () {
                if (!warned && console && console.warn) {
                    warned = true;
                    console.warn(
                            "Accessing Moment through the global scope is " +
                            "deprecated, and will be removed in an upcoming " +
                            "release.");
                }
                return local_moment.apply(null, arguments);
            };
            extend(global.moment, local_moment);
        } else {
            global['moment'] = moment;
        }
    }

    // CommonJS module is defined
    if (hasModule) {
        module.exports = moment;
        makeGlobal(true);
    } else if (typeof define === "function" && define.amd) {
        define("moment", function (require, exports, module) {
            if (module.config && module.config() && module.config().noGlobal !== true) {
                // If user provided noGlobal, he is aware of global
                makeGlobal(module.config().noGlobal === undefined);
            }

            return moment;
        });
    } else {
        makeGlobal();
    }
}).call(this);