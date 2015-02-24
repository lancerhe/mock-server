var url  = require('url'),
    http = require('http'),
    fs = require('fs'),
    qs = require('qs');

function getMock(httprequest, list) {
    mock = false;
    find = true;
    for ( var i in list ) {
        var find = true;
        for ( var key in list[i].request.query ) {
            if ( list[i].request.query[key] instanceof Array ) {
                if ( ! isContained(httprequest.query[key], list[i].request.query[key]) ) {
                    find = false;
                    continue;
                }
            } else if ( list[i].request.query[key] != httprequest.query[key] ) {
                find = false;
                continue;
            }
        }


        for ( var key in list[i].request.post ) {
            if ( list[i].request.post[key] instanceof Array ) {
                if ( ! isContained(httprequest.post[key], list[i].request.post[key]) ) {
                    find = false;
                    continue;
                }
            } else if ( list[i].request.post[key] != httprequest.post[key] ) {
                find = false;
                continue;
            }
        }

        if ( find ) {
            mock = list[i];
            break;
        }
    }
    return mock;
}

function isContained(a, b) {
    if ( ! (a instanceof Array) || !(b instanceof Array)) return false;
    if (a.length < b.length) return false;
    var aStr = a.toString();
    for(var i = 0, len = b.length; i < len; i++){
        if(aStr.indexOf(b[i]) == -1) return false;
    }
    return true;
}

function replaceRequest(httprequest, string) {
    if ( 'string' != typeof string) {
        return string;
    }

    var regex   = /{\$[a-zA-Z1-9_.]*}/g;
    var matches = string.match(regex);
    for (m in matches) {
        var keyword = matches[m];
        var match   = keyword.replace("{$", "").replace("}", "").split('.');
        if ( match[0] != 'request' ) {
            continue;
        }

        if ( match[1] == 'query' ) {
            if ( undefined != typeof httprequest.query[match[2]] ) {
                string = string.replace(keyword, httprequest.query[match[2]]);
            }
        }

        if ( match[1] == 'post' ) {
            if ( undefined != typeof httprequest.post[match[2]] ) {
                string = string.replace(keyword, httprequest.post[match[2]]);
            }
        }
    }
    return string;
}

function server(request, response) {
    if ( '/favicon.ico' == request.url ) {
        return false;
    }
    request.setEncoding('utf-8');

    urlinfo  = url.parse(request.url);
    mockfile = require('path').resolve() + "/mock" + urlinfo.pathname + '.js';

    if ( ! fs.existsSync(mockfile) ) {
        response.write("Mock config file not exists!");
        response.end();
        return false;
    }

    var httprequest = {
        post : '',
        query: ''
    }

    request.addListener("data", function(data) {
        httprequest.post += data;
    });

    request.addListener("end", function() {
        httprequest.query = qs.parse(urlinfo.query);
        httprequest.post  = qs.parse(httprequest.post)

        delete require.cache[mockfile];
        mock  = getMock( httprequest, require(mockfile).mock );
        if ( ! mock ) {
            response.write("Mock request not exists!");
            response.end();
            return false;
        }

        var timeout = mock.response.delay ? mock.response.delay : 1;

        response.setTimeout(timeout, function() {
            if ( mock.response.statusCode ) {
                response.statusCode = mock.response.statusCode;
            }
            if ( mock.response.header ) {
                for( key in mock.response.header ) {
                    response.setHeader( key, replaceRequest(httprequest, mock.response.header[key] ) );
                }
            }
            if ( mock.response.body ) {
                // response.write( JSON.stringify( mock.response.body ) );
                response.write( mock.response.body );
            }

            response.end();
        });
    });
}

httpserver = http.createServer(server);
httpserver.listen('8096', "0.0.0.0");