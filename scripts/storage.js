/**
 * Created by bohdan on 10.09.15.
 */
function addToStorage(key, value) {
    if (typeof(localStorage) != "undefined") {
        localStorage.setItem(key,value);
        return true;
    }
    return false;
}

function getFromStorage(key) {
    if (typeof(localStorage) != "undefined") {
        var value = localStorage.getItem(key);
        return value;
    }
    return false;
}