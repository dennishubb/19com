function getCurrentUri() {
    return window.location.pathname;
}

function getHost() {
    return window.location.protocol + "//" + window.location.host;
}

function getBackendHost() {
    return window.location.protocol + "//app.19.com/";
}

function getAssetsHost() {
    return window.location.protocol + "//app.19.com/";
}

function redirect_to($url) {
    window.location.href = $url;
}

function getCurrentFullUri() {
    return getCurrentUri() + window.location.search;
}

function getQueryString(variable, element = "") {
    var urlParams = new URLSearchParams(window.location.search);
    var param = urlParams.get(variable);

    if (element) {
        $(element).val(param)
        return true;
    } else {
        return param;
    }
}

function getUrlVars() {
	var vars = {};
	var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
		vars[key] = value;
	});
	return vars;
}
