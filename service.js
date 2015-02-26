var url  = require('url'),
    http = require('http'),
    fs = require('fs'),
    qs = require('qs');



function isContained(a, b) {
    if ( ! (a instanceof Array) || !(b instanceof Array)) return false;
    if (a.length < b.length) return false;
    var aStr = a.toString();
    for(var i = 0, len = b.length; i < len; i++){
        if(aStr.indexOf(b[i]) == -1) return false;
    }
    return true;
}

function replaceRequestKeyword(httprequest, string, keyword) {
    var match = keyword.replace("{$", "").replace("}", "").split('.');
    // console.log(string, match);
    if ( match[0] != 'request' ) {
        return string;
    }

    if ( match[1] == 'query' ) {
        if ( undefined != typeof httprequest.query[match[2]] ) {
            string = string.replace(keyword, httprequest.query[match[2]]);
        }
    }
    replaceRequestKeywordByMethod();

    if ( match[1] == 'post' ) {
        if ( undefined != typeof httprequest.post[match[2]] ) {
            string = string.replace(keyword, httprequest.post[match[2]]);
        }
    }
    return string;
}

function replaceRequestKeywordByMethod() {
    //console.log(httprequest, mock);
}

function replaceRequest(httprequest, string) {
    if ( 'string' != typeof string) {
        return string;
    }

    var regex   = /{\$[a-zA-Z1-9_.]*}/g;
    var matches = string.match(regex);
    for (m in matches) {
        var keyword = matches[m];
        string = replaceRequestKeyword(httprequest, string, keyword)
    }
    return string;
}

function server(request, response) {
    MS = new MockServer(request, response);
    MS.listen();
}
MockServer = function( request, response ) {
    this.request  = request;
    this.response = response;
    this.mockfile = null;
    this.urlinfo  = null;
    this.init();
}

MockServer.prototype = {
    init: function() {
        this.request.setEncoding('utf-8');
        this.urlinfo  = url.parse(this.request.url);
        this.mockfile = require('path').resolve() + "/mock" + this.urlinfo.pathname + '.js';
        this.mocklist = [];
        this.httppoststring = '';
        this.httprequest = {};
    }
    , listen: function() {
        var self = this;
        if ( '/favicon.ico' == this.request.url ) {
            return false;
        }

        if ( ! fs.existsSync( this.mockfile ) ) {
            responseError("Mock config file not exists!");
            return false;
        }

        this.request.addListener("data", function(data) {
            self.httppoststring += data;
        });

        this.request.addListener("end", function() {
            self.httprequest.query = qs.parse(self.urlinfo.query);
            self.httprequest.post  = qs.parse(self.httppoststring);

            delete require.cache[ self.mockfile ];

            if ( false === ( self.mocklist = self.getMockByHttpRequest() ) ) {
                self.responseError("Mock request not exists!");
                return false;
            }

            self.responseSuccess()
        });
    }
    , getMockByHttpRequest: function() {
        list = this.getMockList();
        method = ['query', 'post'];
        mock = false;
        find = true;
        for ( var i in list ) {
            var find = true;
            for (var k = 0; k < method.length; k++) {
                for ( var key in list[i].request[method[k]] ) {
                    if ( list[i].request[method[k]][key] instanceof Array ) {
                        if ( ! isContained(this.httprequest[method[k]][key], list[i].request[method[k]][key]) ) {
                            find = false;
                            continue;
                        }
                    } else if ( list[i].request[method[k]][key] != this.httprequest[method[k]][key] ) {
                        find = false;
                        continue;
                    }
                }
            };
            if ( find ) {
                mock = list[i];
                break;
            }
        }
        return mock;
    }
    , getMockList: function() {
        return require(this.mockfile).mock;
    }
    , responseError: function(message) {
        this.response.statusCode = 404;
        this.response.write(message);
        this.response.end();
    }
    , responseSuccess: function() {
        MC = new MockControl( this.httprequest, this.mocklist );
        var self = this;
        this.response.setTimeout(MC.getMockResponse().getTimeout(), function() {
            self.responseSuccessDetail();
        });
    }
    , responseSuccessDetail: function() {
        MC.replaceKeyword();
        this.response.statusCode = MC.getMockResponse().getStatusCode();

        for( key in MC.getMockResponse().header ) {
            this.response.setHeader( key, MC.getMockResponse().header[key] );
        }
        this.response.write( MC.getMockResponse().body );
        this.response.end();
    }
}

MockControl = function( httprequest, mockexport ) {
    this.httprequest    = new HttpRequest(httprequest);
    this.requirerequest = new MockRequest(mockexport.request);
    this.mockresponse   = new MockResponse(mockexport.response);
}

MockControl.prototype = {
    getHttpRequest: function() {
        return this.httprequest;
    }
    , getMockRequest: function() {
        return this.requirerequest;
    }
    , getMockResponse: function() {
        return this.mockresponse;
    }
    , replaceKeyword: function() {
        for ( key in this.mockresponse.header ) {
            this.mockresponse.header[key] = this.replaceRequest(this.mockresponse.header[key] );
        }
    }
    , replaceRequest: function(string) {
        if ( 'string' != typeof string) {
            return string;
        }

        var regex   = /{\$[a-zA-Z1-9_.]*}/g;
        var matches = string.match(regex);
        for (m in matches) {
            var keyword = matches[m];
            string = this.replaceRequestKeyword(string, keyword)
        }
        return string;
    }
    , replaceRequestKeyword: function(string, keyword) {
        var match = keyword.replace("{$", "").replace("}", "").split('.');
        // console.log(string, match);
        if ( match[0] != 'request' ) {
            return string;
        }

        if ( match[1] == 'query' ) {
            if ( undefined != typeof this.httprequest.query[match[2]] ) {
                string = string.replace(keyword, this.httprequest.query[match[2]]);
            }
        }

        if ( match[1] == 'post' ) {
            if ( undefined != typeof this.httprequest.post[match[2]] ) {
                string = string.replace(keyword, this.httprequest.post[match[2]]);
            }
        }
        return string;
    }
}

MockResponse = function( response ) {
    this.header     = response.header;
    this.delay      = typeof response.delay == 'undefined' ? 1 : response.delay;
    this.statusCode = response.statusCode;
    this.body       = response.body;
}

MockResponse.prototype = {
    getTimeout: function() {
        return this.delay;
    }
    , getStatusCode: function() {
        return this.statusCode ? this.statusCode : 200;
    }
}

MockRequest = function( request ) {
    this.query = request.query;
    this.post  = request.post;
}

HttpRequest = function( request ) {
    this.query = request.query;
    this.post  = request.post;
}

httpserver = http.createServer(server);
httpserver.listen('8096', "0.0.0.0");