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

MockFactor = function( request, response ) {
    this.request  = request;
    this.response = response;
    this._init();
}

MockFactor.prototype = {
    _init: function() {
        this.request.setEncoding('utf-8');
        this.urlinfo  = url.parse(this.request.url);
        this.mockfile = require('path').resolve() + "/mock" + this.urlinfo.pathname + '.js';
        this.httppoststring = '';
        this.httprequest = {};
        this.httprequest.query = qs.parse(this.urlinfo.query);
    }
    , createServer: function() {
        if ( '/favicon.ico' == this.request.url ) {
            return false;
        }

        if ( ! fs.existsSync( this.mockfile ) ) {
            return this.responseError("Error: Mock config file not exists!");
        }

        var self = this;
        this.request.addListener("data", function(data) {
            self.httppoststring += data;
        });

        this.request.addListener("end", function() {
            delete require.cache[ self.mockfile ];
            self.httprequest.post = qs.parse(self.httppoststring);
            self.mockconfig       = self.getMockByHttpRequest();

            if ( false === self.mockconfig ) {
                return self.responseError("Error: Mock request not exists!");
            }

            self.responseSuccess()
        });
    }
    , getMockByHttpRequest: function() {
        var list = this.getMockList();
        var method = ['query', 'post'];
        var mock = false;
        var find = true;
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
        var dispatcher = new MockDispatcher( this.httprequest, this.mockconfig );
        var self = this;
        this.response.setTimeout(dispatcher.getMockResponse().delay, function() {
            dispatcher.replaceKeyword();
            self.response.statusCode = dispatcher.getMockResponse().statusCode;

            for( key in dispatcher.getMockResponse().header ) {
                self.response.setHeader( key, dispatcher.getMockResponse().header[key] );
            }
            self.response.write( dispatcher.getMockResponse().body );
            self.response.end();
        });
    }
}

MockDispatcher = function( httprequest, mockconfig ) {
    this.httprequest    = new HttpRequest(httprequest);
    this.requirerequest = new MockRequest(mockconfig.request);
    this.mockresponse   = new MockResponse(mockconfig.response);
}

MockDispatcher.prototype = {
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
        var method = ['query', 'post'];

        if ( match[0] != 'request' ) {
            return string;
        }

        for (var k = 0; k < method.length; k++) {
            if ( match[1] == method[k] ) {
                if ( undefined != typeof this.httprequest[method[k]][match[2]] ) {
                    string = string.replace(keyword, this.httprequest[method[k]][match[2]]);
                }
            }
        }
        return string;
    }
}

MockResponse = function( response ) {
    this.header     = response.header;
    this.delay      = typeof response.delay == 'undefined' ? 1 : response.delay;
    this.statusCode = typeof response.statusCode == 'undefined' ? 200 : response.statusCode;
    this.body       = response.body;
}

MockRequest = function( request ) {
    this.query = request.query;
    this.post  = request.post;
}

HttpRequest = function( request ) {
    this.query = request.query;
    this.post  = request.post;
}


httpserver = http.createServer(function (request, response) {
    factor = new MockFactor(request, response);
    factor.createServer();
});
httpserver.listen('8096', "0.0.0.0");