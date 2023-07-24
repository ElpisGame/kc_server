import AMapLoader from '@amap/amap-jsapi-loader';


export function amap() {
    return new Promise((resolve, reject) => {
        AMapLoader.load({
            "key": "dcbf0403b511489619abc7cb48a104f4",
            "version": "2.0",
            "plugins": ["AMap.Driving", "AMap.Geocoder", "AMap.Geolocation", "AMap.AutoComplete"]
        }).then((AMap) => {
            resolve(AMap);
        }).catch(e => {
            reject(e);
        });
    });
}

export function parseUrlParams(url) {
    if (url && url.startsWith("http")) {
        var str = url.split('?')[1];
        var result = {};
        var temp = str.split('&');
        for (var i = 0; i < temp.length; i++) {
            var temp2 = temp[i].split('=');
            result[temp2[0]] = temp2[1];
        }
        return result;
    }
    return null;
}

export function isiPhone() {
    let userAgent = window.navigator.userAgent;
    return userAgent.indexOf('iPhone') > -1 || /iPad/gi.test(userAgent);
}

const formatTime = date => {
    let year = date.getFullYear();
    let month = date.getMonth() + 1;
    let day = date.getDate();
    let hour = date.getHours();
    let minute = date.getMinutes();
    let second = date.getSeconds();

    return [year, month, day].map(formatNumber).join('-') + ' ' + [hour, minute, second].map(formatNumber).join(':')
};

const formatDate = date => {
    let year = date.getFullYear();
    let month = date.getMonth() + 1;
    let day = date.getDate();
    return [year, month, day].map(formatNumber).join('-');
};

const formatNumber = n => {
    n = n.toString();
    return n[1] ? n : '0' + n;
};

export default {
    amap,
    isiPhone,
    formatDate,
    formatTime,
    parseUrlParams
}
