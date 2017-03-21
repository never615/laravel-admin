/**
 * js一些公用方法
 * Created by never615 on 06/11/2016.
 */

;(function ($, window) {

    /**
     * 封装ajax请求
     * @param url
     * @param type
     * @param data1
     * @param successCallBack
     * @param async
     */
    window.doAjax = function (url, type, data1, successCallBack, async) {

        $.ajax({
            type: type || 'POST',
            url: url,
            async: async || true,
            dataType: "json",
            // data: data + "&iddd=" + Math.random(),
            data: Object.assign({}, {iddd: Math.random()}, data1),
            headers: {
                'X-CSRF-TOKEN': LA.token,
                'REQUEST-TYPE': 'WEB'
            },
            success: function (data) {
                successHandler(data, successCallBack);
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                errorHandler(XMLHttpRequest);
            }
        });
    };


    /**
     * ajax请求成功处理者
     * @param data
     * @param successCallBack
     */
    window.successHandler = function (data, successCallBack) {
        console.log(data);

        if (data && data.redirectUrl != null && data.redirectUrl != "") {
            window.location.href = data.redirectUrl;
        } else {
            if (typeof successCallBack === "function") {
                successCallBack(data);
            }
        }
    };


    /**
     * ajax请求失败处理者
     * @param XMLHttpRequest
     */
    window.errorHandler = function (XMLHttpRequest) {
        console.log(XMLHttpRequest);
        var msg = '';
        if (XMLHttpRequest && XMLHttpRequest.responseText) { //ajax error, errors = xhr object
            if (XMLHttpRequest.responseJSON && XMLHttpRequest.responseJSON.error) {
                msg += XMLHttpRequest.responseJSON.error;
            } else {
                if (XMLHttpRequest.status == 422) {
                    var erroMsg = JSON.parse(XMLHttpRequest.responseText);
                    $.each(erroMsg, function (k, v) {
                        // msg += k + ": " + v[0] + "\n";
                        msg += v[0] + "\n";
                    });
                } else {
                    msg += XMLHttpRequest.status + ":" + XMLHttpRequest.statusText + ":" + XMLHttpRequest.responseText;
                }
            }
        } else { //validation error (client-side or server-side)
            $.each(XMLHttpRequest, function (k, v) {
                msg += k + ": " + v + "\n";
            });
        }
        notify.alert(3, msg, 5);
    };


    /**
     * 数据或者json 根据value找对应的key
     * @param arr
     * @param search_key
     * @returns {string}
     */
    window.getKeyFromValue = function (arr, search_key) {
        var tempKey = '';
        for (var key in arr) {

            if (arr[key] == search_key) {

                tempKey = key;
                break;
            }
        }
        return tempKey;
    };

    /**
     * 数组转json字符串,使用单引号
     * @param o
     * @returns {string}
     */
    window.json2strRP = function (o) {
        var json = json2str(o);
        json = json.substr(1, json.length - 2);
        return '[' + json + ']';
    };

    var json2str = function (o) {
        var arr = [];
        var fmt = function (s) {
            if (typeof s == 'object' && s != null) return json2str(s);

            return /^(string|number)$/.test(typeof s) ? "'" + s + "'" : s;
        };
        for (var i in o) {
            if (isNaN(i)) {
                arr.push("'" + i + "':" + fmt(o[i]));

            }
            else {
                arr.push(fmt(o[i]));
            }

        }
        return '{' + arr.join(',') + '}';
    };


    /**
     * 后去指定参数的值
     * 使用方法:GetParameterValueByName("id")
     * @param parametername
     * @returns {null}
     * @constructor
     */
    function GetParameterValueByName(parametername) {
        var reg = new RegExp("(^|&)" + parametername + "=([^&]*)(&|$)");
        var r = window.location.search.substr(1).match(reg);
        if (r != null) return unescape(r[2]);
        return null;
    }

    /**
     * js解析后台时间格式的json
     * 在后台返回DateTime格式的json是总是出现的是/Date(1421245202219)/ 所以用js写了个方法进行转换
     *
     * 使用方法:dateJsonToDate(data.Now, true, true, true, true)
     *
     * @param val
     * @param hour
     * @param minute
     * @param second
     * @param ms
     * @returns {string}
     */
    function dateJsonToDate(val, hour, minute, second, ms) {
        var dataStr = "";
        if (val != null && val != "") {
            var date = new Date(parseInt(val.replace("/Date(", "").replace(")/", ""), 10));
            var month = date.getMonth() + 1 < 10 ? "0" + (date.getMonth() + 1) : date.getMonth() + 1;
            var currentDate = date.getDate() < 10 ? "0" + date.getDate() : date.getDate();
            dataStr += date.getFullYear() + "-" + month + "-" + currentDate;
            if (hour) {
                var currenthour = date.getHours() < 10 ? "0" + date.getHours() : date.getHours();
                dataStr += " " + currenthour;
                if (minute) {
                    var currentminute = date.getMinutes() < 10 ? "0" + date.getMinutes() : date.getMinutes();
                    dataStr += ":" + currentminute;
                    if (second) {
                        var currentsecond = date.getSeconds() < 10 ? "0" + date.getSeconds() : date.getSeconds();
                        dataStr += ":" + currentsecond;
                        if (ms) {
                            var currentms = date.getMilliseconds() < 10 ? "0" + date.getMilliseconds() : date.getMilliseconds();
                            dataStr += "." + currentms;
                        }
                    }
                }
            }
        }
        return dataStr;
    }


    /**
     * Js获取Url参数
     * @returns {{}}
     * @constructor
     */
    function GetRequest() {
        var url = location.search; //获取url中"?"符后的字串
        var theRequest = {};
        if (url.indexOf("?") != -1) {
            var str = url.substr(1);
            strs = str.split("&");
            for (var i = 0; i < strs.length; i++) {
                theRequest[strs[i].split("=")[0]] = unescape(strs[i].split("=")[1]);
            }
        }
        return theRequest;
    }

    /**
     * 保留指定位小数
     * @param src
     * @param pos
     * @returns {number}
     */
    function fomatFloat(src, pos) {
        return Math.round(src * Math.pow(10, pos)) / Math.pow(10, pos);
    }

    /**
     * 数组删除指定元素
     * @returns {Array}
     */
    Array.prototype.delRepet = function () {
        //            this = this || [];
        var a = {};
        for (var i = 0; i < this.length; i++) {
            var v = this[i];
            if (typeof (a[v]) == 'undefined') {
                a[v] = 1;
            }
        }
        this.length = 0;
        for (var i in a) {
            this[this.length] = i;
        }
        return this;
    };

    /**
     * 日期格式转换(yyyy - mm - ddhh:mm)
     * @return {string}
     */
    function ChangeDateFormatHM(date) {
        var da = new Date(parseInt(date.replace("/Date(", "").replace(")/", "").split("+")[0]));
        var month = da.getMonth() + 1 < 10 ? "0" + (da.getMonth() + 1) : da.getMonth() + 1;
        var currentDate = da.getDate() < 10 ? "0" + da.getDate() : da.getDate();
        var hour = da.getHours() < 10 ? "0" + da.getHours() : da.getHours()
        var minute = da.getMinutes() < 10 ? "0" + da.getMinutes() : da.getMinutes();
        return da.getFullYear() + "-" + month + "-" + currentDate + " " + hour + ":" + minute;
    }

    /**
     * 日期格式转换(hh:mm:ss)
     * @param jsondate
     * @returns {string}
     * @constructor
     */
    function ChangeDateFormatShortHMS(jsondate) {
        var date = new Date(parseInt(jsondate.replace("/Date(", "").replace(")/", "").split("+")[0]));
        var currentDate = date.getDate() < 10 ? "0" + date.getDate() : date.getDate();
        var hour = date.getHours() < 10 ? "0" + date.getHours() : date.getHours();
        var minute = date.getMinutes() < 10 ? "0" + date.getMinutes() : date.getMinutes();
        var second = date.getSeconds() < 10 ? "0" + date.getSeconds() : date.getSeconds();
        return hour + ":" + minute + ":" + second;
    }

    /**
     * 日期格式转换(yyyy-mm-dd hh:mm:ss)
     * @param jsondate
     * @returns {*}
     * @constructor
     */
    function ChangeDateFormatHMS(jsondate) {
        if (jsondate == undefined) {
            return "1990-01-01 A";
        }
        jsondate = jsondate.replace("/Date(", "").replace(")/", "");
        if (jsondate.indexOf("+") > 0) {
            jsondate = jsondate.substring(0, jsondate.indexOf("+"));
        }
        else if (jsondate.indexOf("-") > 0) {
            jsondate = jsondate.substring(0, jsondate.indexOf("-"));
        }

        var date = new Date(parseInt(jsondate, 10));
        var month = date.getMonth() + 1 < 10 ? "0" + (date.getMonth() + 1) : date.getMonth() + 1;
        var currentDate = date.getDate() < 10 ? "0" + date.getDate() : date.getDate();
        var hour = date.getHours() < 10 ? "0" + date.getHours() : date.getHours();
        var minute = date.getMinutes() < 10 ? "0" + date.getMinutes() : date.getMinutes();
        var second = date.getSeconds() < 10 ? "0" + date.getSeconds() : date.getSeconds();

        return date.getFullYear() + "-" + month + "-" + currentDate + " " + hour + ":" + minute + ":" + second;
    }

    /**
     * 日期格式转换(yyyy-mm-dd)
     * @param jsondate
     * @returns {*}
     * @constructor
     */
    function ChangeDateFormat(jsondate) {
        if (jsondate == undefined) {
            return "1990-01-01";
        }
        jsondate = jsondate.replace("/Date(", "").replace(")/", "");
        if (jsondate.indexOf("+") > 0) {
            jsondate = jsondate.substring(0, jsondate.indexOf("+"));
        }
        else if (jsondate.indexOf("-") > 0) {
            jsondate = jsondate.substring(0, jsondate.indexOf("-"));
        }

        var date = new Date(parseInt(jsondate, 10));
        var month = date.getMonth() + 1 < 10 ? "0" + (date.getMonth() + 1) : date.getMonth() + 1;
        var currentDate = date.getDate() < 10 ? "0" + date.getDate() : date.getDate();
        return date.getFullYear() + "-" + month + "-" + currentDate;
    }

    /**
     * 获取字符串指定长度的字符
     * @param Str
     * @param size
     * @returns {*}
     * @constructor
     */
    function GetString(Str, size) {
        if (Str.toString().length > size) {
            return Str.substring(0, size) + "...";
        }
        else {
            return Str;
        }
    };


    /**
     * 获取指定值的索引
     * @param val
     * @returns {number}
     */
    Array.prototype.indexOf = function (val) {
        for (var i = 0; i < this.length; i++) {
            if (this[i] == val) return i;
        }
        return -1;
    };
    /**
     * 数组删除指定值
     * @param val
     */
    Array.prototype.remove = function (val) {
        var index = this.indexOf(val);
        if (index > -1) {
            this.splice(index, 1);
        }
    };


    /**
     * Array.prototype.[method name] allows you to define/overwrite an objects method
     * needle is the item you are searching for
     * this is a special variable that refers to "this" instance of an Array.
     * returns true if needle is in the array, and false otherwise
     */
    Array.prototype.contains = function (needle) {
        for (i in this) {
            if (this[i] == needle) return true;
        }
        return false;
    };

    /**
     * 去除字符串中所有的空格
     *
     * @param str
     * @param is_global
     * @returns {*|XML|string|void}
     * @constructor
     */
    window.trimAll = function (str, is_global) {
        var result;
        result = str.replace(/(^\s+)|(\s+$)/g, "");
        if (is_global) {
            result = result.replace(/\s/g, "");
        }
        return result;
    }


})(jQuery, window);
