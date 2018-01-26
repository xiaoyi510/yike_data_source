/*! WebUploader 0.1.6 */


/**
 * @fileOverview 让内部各个部件的代码可以用[amd](https://github.com/amdjs/amdjs-api/wiki/AMD)模块定义方式组织起来。
 *
 * AMD API 内部的简单不完全实现，请忽略。只有当WebUploader被合并成一个文件的时候才会引入。
 */
(function( root, factory ) {
    var modules = {},

        // 内部require, 简单不完全实现。
        // https://github.com/amdjs/amdjs-api/wiki/require
        _require = function( deps, callback ) {
            var args, len, i;

            // 如果deps不是数组，则直接返回指定module
            if ( typeof deps === 'string' ) {
                return getModule( deps );
            } else {
                args = [];
                for( len = deps.length, i = 0; i < len; i++ ) {
                    args.push( getModule( deps[ i ] ) );
                }

                return callback.apply( null, args );
            }
        },

        // 内部define，暂时不支持不指定id.
        _define = function( id, deps, factory ) {
            if ( arguments.length === 2 ) {
                factory = deps;
                deps = null;
            }

            _require( deps || [], function() {
                setModule( id, factory, arguments );
            });
        },

        // 设置module, 兼容CommonJs写法。
        setModule = function( id, factory, args ) {
            var module = {
                    exports: factory
                },
                returned;

            if ( typeof factory === 'function' ) {
                args.length || (args = [ _require, module.exports, module ]);
                returned = factory.apply( null, args );
                returned !== undefined && (module.exports = returned);
            }

            modules[ id ] = module.exports;
        },

        // 根据id获取module
        getModule = function( id ) {
            var module = modules[ id ] || root[ id ];

            if ( !module ) {
                throw new Error( '`' + id + '` is undefined' );
            }

            return module;
        },

        // 将所有modules，将路径ids装换成对象。
        exportsTo = function( obj ) {
            var key, host, parts, part, last, ucFirst;

            // make the first character upper case.
            ucFirst = function( str ) {
                return str && (str.charAt( 0 ).toUpperCase() + str.substr( 1 ));
            };

            for ( key in modules ) {
                host = obj;

                if ( !modules.hasOwnProperty( key ) ) {
                    continue;
                }

                parts = key.split('/');
                last = ucFirst( parts.pop() );

                while( (part = ucFirst( parts.shift() )) ) {
                    host[ part ] = host[ part ] || {};
                    host = host[ part ];
                }

                host[ last ] = modules[ key ];
            }

            return obj;
        },

        makeExport = function( dollar ) {
            root.__dollar = dollar;

            // exports every module.
            return exportsTo( factory( root, _define, _require ) );
        },

        origin;

    if ( typeof module === 'object' && typeof module.exports === 'object' ) {

        // For CommonJS and CommonJS-like environments where a proper window is present,
        module.exports = makeExport();
    } else if ( typeof define === 'function' && define.amd ) {

        // Allow using this built library as an AMD module
        // in another project. That other project will only
        // see this AMD call, not the internal modules in
        // the closure below.
        define([ 'jquery' ], makeExport );
    } else {

        // Browser globals case. Just assign the
        // result to a property on the global.
        origin = root.WebUploader;
        root.WebUploader = makeExport();
        root.WebUploader.noConflict = function() {
            root.WebUploader = origin;
        };
    }
})( window, function( window, define, require ) {


    /**
     * @fileOverview  jq-bridge 主要实现像jQuery一样的功能方法，可以替换成jQuery，
     * 这里只实现了此组件所需的部分。
     *
     * **此文件的代码还不可用，还是直接用jquery吧**
     * @beta
     */
    define('dollar-builtin',[],function() {
        var doc = window.document,
            emptyArray = [],
            slice = emptyArray.slice,
            class2type = {},
            hasOwn = class2type.hasOwnProperty,
            toString = class2type.toString,
            rId = /^#(.*)$/;
    
        function each( obj, iterator ) {
            var i;
    
            //add guard here
            if(!obj) {
                return;
            }
    
            // like array
            if ( typeof obj !== 'function' && typeof obj.length === 'number' ) {
                for ( i = 0; i < obj.length; i++ ) {
                    if ( iterator.call( obj[ i ], i, obj[ i ] ) === false ) {
                        return obj;
                    }
                }
            } else {
                for ( i in obj ) {
                    if ( hasOwn.call( obj, i ) && iterator.call( obj[ i ], i,
                            obj[ i ] ) === false ) {
                        return obj;
                    }
                }
            }
    
            return obj;
        }
    
        function extend( target, source, deep ) {
            each( source, function( key, val ) {
                if ( deep && typeof val === 'object' ) {
                    if ( typeof target[ key ] !== 'object' ) {
                        target[ key ] = type( val ) === 'array' ? [] : {};
                    }
                    extend( target[ key ], val, deep );
                } else {
                    target[ key ] = val;
                }
            });
        }
    
        each( ('Boolean Number String Function Array Date RegExp Object' +
                ' Error').split(' '), function( i, name ) {
            class2type[ '[object ' + name + ']' ] = name.toLowerCase();
        });
    
        function setAttribute( node, name, value ) {
            value == null ? node.removeAttribute( name ) :
                    node.setAttribute( name, value );
        }
    
        /**
         * 只支持ID选择。
         */
        function $( elem ) {
            var api = {};
    
            elem = typeof elem === 'string' && rId.test( elem ) ?
                    doc.getElementById( RegExp.$1 ) : elem;
    
            if ( elem ) {
                api[ 0 ] = elem;
                api.length = 1;
            }
    
            return $.extend( api, {
                _wrap: true,
    
                get: function() {
                    return elem;
                },
    
                /**
                 * 添加className
                 */
                addClass: function( classname ) {
                    elem.classList.add( classname );
                    return this;
                },
    
                removeClass: function( classname ) {
                    elem.classList.remove( classname );
                    return this;
                },
    
                //$(...).each is used in the source
                each: function(callback){
                  [].every.call(this, function(el, idx){
                    return callback.call(el, idx, el) !== false
                  })
                  return this
                },
    
                html: function( html ) {
                    if ( html ) {
                        elem.innerHTML = html;
                    }
                    return elem.innerHTML;
                },
    
                attr: function( key, val ) {
                    if ( $.isObject( key ) ) {
                        $.each( key, function( k, v ) {
                            setAttribute( elem, k, v );
                        });
                    } else {
                        setAttribute( elem, key, val );
                    }
                },
    
                empty: function() {
                    elem.innerHTML = '';
                    return this;
                },
    
                before: function( el ) {
                    elem.parentNode.insertBefore( el, elem );
                },
    
                append: function( el ) {
                    el = el._wrap ? el.get() : el;
                    elem.appendChild( el );
                },
    
                text: function() {
                    return elem.textContent;
                },
    
                // on
                on: function( type, fn ) {
                    if ( elem.addEventListener ) {
                        elem.addEventListener( type, fn, false );
                    } else if ( elem.attachEvent ) {
                        elem.attachEvent( 'on' + type, fn );
                    }
    
                    return this;
                },
    
                // off
                off: function( type, fn ) {
                    if ( elem.removeEventListener ) {
                        elem.removeEventListener( type, fn, false );
                    } else if ( elem.attachEvent ) {
                        elem.detachEvent( 'on' + type, fn );
                    }
                    return this;
                }
    
            });
        }
    
        $.each = each;
        $.extend = function( /*[deep, ]*/target/*, source...*/ ) {
            var args = slice.call( arguments, 1 ),
                deep;
    
            if ( typeof target === 'boolean' ) {
                deep = target;
                target = args.shift();
            }
    
            args.forEach(function( arg ) {
                arg && extend( target, arg, deep );
            });
    
            return target;
        };
    
        function type( obj ) {
    
            /*jshint eqnull:true*/
            return obj == null ? String( obj ) :
                    class2type[ toString.call( obj ) ] || 'object';
        }
        $.type = type;
    
        //$.grep is used in the source
        $.grep = function( elems, callback, invert ) {
            var callbackInverse,
                matches = [],
                i = 0,
                length = elems.length,
                callbackExpect = !invert;
    
            // Go through the array, only saving the items
            // that pass the validator function
            for ( ; i < length; i++ ) {
                callbackInverse = !callback( elems[ i ], i );
                if ( callbackInverse !== callbackExpect ) {
                    matches.push( elems[ i ] );
                }
            }
    
            return matches;
        }
    
        $.isWindow = function( obj ) {
            return obj && obj.window === obj;
        };
    
        $.isPlainObject = function( obj ) {
            if ( type( obj ) !== 'object' || obj.nodeType || $.isWindow( obj ) ) {
                return false;
            }
    
            try {
                if ( obj.constructor && !hasOwn.call( obj.constructor.prototype,
                        'isPrototypeOf' ) ) {
                    return false;
                }
            } catch ( ex ) {
                return false;
            }
    
            return true;
        };
    
        $.isObject = function( anything ) {
            return type( anything ) === 'object';
        };
    
        $.trim = function( str ) {
            return str ? str.trim() : '';
        };
    
        $.isFunction = function( obj ) {
            return type( obj ) === 'function';
        };
    
        emptyArray = null;
    
        return $;
    });
    
    define('dollar',[
        'dollar-builtin'
    ], function( $ ) {
        return $;
    });
    /**
     * 直接来源于jquery的代码。
     * @fileOverview Promise/A+
     * @beta
     */
    define('promise-builtin',[
        'dollar'
    ], function( $ ) {
    
        var api;
    
        // 简单版Callbacks, 默认memory，可选once.
        function Callbacks( once ) {
            var list = [],
                stack = !once && [],
                fire = function( data ) {
                    memory = data;
                    fired = true;
                    firingIndex = firingStart || 0;
                    firingStart = 0;
                    firingLength = list.length;
                    firing = true;
    
                    for ( ; list && firingIndex < firingLength; firingIndex++ ) {
                        list[ firingIndex ].apply( data[ 0 ], data[ 1 ] );
                    }
                    firing = false;
    
                    if ( list ) {
                        if ( stack ) {
                            stack.length && fire( stack.shift() );
                        }  else {
                            list = [];
                        }
                    }
                },
                self = {
                    add: function() {
                        if ( list ) {
                            var start = list.length;
                            (function add ( args ) {
                                $.each( args, function( _, arg ) {
                                    var type = $.type( arg );
                                    if ( type === 'function' ) {
                                        list.push( arg );
                                    } else if ( arg && arg.length &&
                                            type !== 'string' ) {
    
                                        add( arg );
                                    }
                                });
                            })( arguments );
    
                            if ( firing ) {
                                firingLength = list.length;
                            } else if ( memory ) {
                                firingStart = start;
                                fire( memory );
                            }
                        }
                        return this;
                    },
    
                    disable: function() {
                        list = stack = memory = undefined;
                        return this;
                    },
    
                    // Lock the list in its current state
                    lock: function() {
                        stack = undefined;
                        if ( !memory ) {
                            self.disable();
                        }
                        return this;
                    },
    
                    fireWith: function( context, args ) {
                        if ( list && (!fired || stack) ) {
                            args = args || [];
                            args = [ context, args.slice ? args.slice() : args ];
                            if ( firing ) {
                                stack.push( args );
                            } else {
                                fire( args );
                            }
                        }
                        return this;
                    },
    
                    fire: function() {
                        self.fireWith( this, arguments );
                        return this;
                    }
                },
    
                fired, firing, firingStart, firingLength, firingIndex, memory;
    
            return self;
        }
    
        function Deferred( func ) {
            var tuples = [
                    // action, add listener, listener list, final state
                    [ 'resolve', 'done', Callbacks( true ), 'resolved' ],
                    [ 'reject', 'fail', Callbacks( true ), 'rejected' ],
                    [ 'notify', 'progress', Callbacks() ]
                ],
                state = 'pending',
                promise = {
                    state: function() {
                        return state;
                    },
                    always: function() {
                        deferred.done( arguments ).fail( arguments );
                        return this;
                    },
                    then: function( /* fnDone, fnFail, fnProgress */ ) {
                        var fns = arguments;
                        return Deferred(function( newDefer ) {
                            $.each( tuples, function( i, tuple ) {
                                var action = tuple[ 0 ],
                                    fn = $.isFunction( fns[ i ] ) && fns[ i ];
    
                                // deferred[ done | fail | progress ] for
                                // forwarding actions to newDefer
                                deferred[ tuple[ 1 ] ](function() {
                                    var returned;
    
                                    returned = fn && fn.apply( this, arguments );
    
                                    if ( returned &&
                                            $.isFunction( returned.promise ) ) {
    
                                        returned.promise()
                                                .done( newDefer.resolve )
                                                .fail( newDefer.reject )
                                                .progress( newDefer.notify );
                                    } else {
                                        newDefer[ action + 'With' ](
                                                this === promise ?
                                                newDefer.promise() :
                                                this,
                                                fn ? [ returned ] : arguments );
                                    }
                                });
                            });
                            fns = null;
                        }).promise();
                    },
    
                    // Get a promise for this deferred
                    // If obj is provided, the promise aspect is added to the object
                    promise: function( obj ) {
    
                        return obj != null ? $.extend( obj, promise ) : promise;
                    }
                },
                deferred = {};
    
            // Keep pipe for back-compat
            promise.pipe = promise.then;
    
            // Add list-specific methods
            $.each( tuples, function( i, tuple ) {
                var list = tuple[ 2 ],
                    stateString = tuple[ 3 ];
    
                // promise[ done | fail | progress ] = list.add
                promise[ tuple[ 1 ] ] = list.add;
    
                // Handle state
                if ( stateString ) {
                    list.add(function() {
                        // state = [ resolved | rejected ]
                        state = stateString;
    
                    // [ reject_list | resolve_list ].disable; progress_list.lock
                    }, tuples[ i ^ 1 ][ 2 ].disable, tuples[ 2 ][ 2 ].lock );
                }
    
                // deferred[ resolve | reject | notify ]
                deferred[ tuple[ 0 ] ] = function() {
                    deferred[ tuple[ 0 ] + 'With' ]( this === deferred ? promise :
                            this, arguments );
                    return this;
                };
                deferred[ tuple[ 0 ] + 'With' ] = list.fireWith;
            });
    
            // Make the deferred a promise
            promise.promise( deferred );
    
            // Call given func if any
            if ( func ) {
                func.call( deferred, deferred );
            }
    
            // All done!
            return deferred;
        }
    
        api = {
            /**
             * 创建一个[Deferred](http://api.jquery.com/category/deferred-object/)对象。
             * 详细的Deferred用法说明，请参照jQuery的API文档。
             *
             * Deferred对象在钩子回掉函数中经常要用到，用来处理需要等待的异步操作。
             *
             * @for  Base
             * @method Deferred
             * @grammar Base.Deferred() => Deferred
             * @example
             * // 在文件开始发送前做些异步操作。
             * // WebUploader会等待此异步操作完成后，开始发送文件。
             * Uploader.register({
             *     'before-send-file': 'doSomthingAsync'
             * }, {
             *
             *     doSomthingAsync: function() {
             *         var deferred = Base.Deferred();
             *
             *         // 模拟一次异步操作。
             *         setTimeout(deferred.resolve, 2000);
             *
             *         return deferred.promise();
             *     }
             * });
             */
            Deferred: Deferred,
    
            /**
             * 判断传入的参数是否为一个promise对象。
             * @method isPromise
             * @grammar Base.isPromise( anything ) => Boolean
             * @param  {*}  anything 检测对象。
             * @return {Boolean}
             * @for  Base
             * @example
             * console.log( Base.isPromise() );    // => false
             * console.log( Base.isPromise({ key: '123' }) );    // => false
             * console.log( Base.isPromise( Base.Deferred().promise() ) );    // => true
             *
             * // Deferred也是一个Promise
             * console.log( Base.isPromise( Base.Deferred() ) );    // => true
             */
            isPromise: function( anything ) {
                return anything && typeof anything.then === 'function';
            },
    
            /**
             * 返回一个promise，此promise在所有传入的promise都完成了后完成。
             * 详细请查看[这里](http://api.jquery.com/jQuery.when/)。
             *
             * @method when
             * @for  Base
             * @grammar Base.when( promise1[, promise2[, promise3...]] ) => Promise
             */
            when: function( subordinate /* , ..., subordinateN */ ) {
                var i = 0,
                    slice = [].slice,
                    resolveValues = slice.call( arguments ),
                    length = resolveValues.length,
    
                    // the count of uncompleted subordinates
                    remaining = length !== 1 || (subordinate &&
                        $.isFunction( subordinate.promise )) ? length : 0,
    
                    // the master Deferred. If resolveValues consist of
                    // only a single Deferred, just use that.
                    deferred = remaining === 1 ? subordinate : Deferred(),
    
                    // Update function for both resolve and progress values
                    updateFunc = function( i, contexts, values ) {
                        return function( value ) {
                            contexts[ i ] = this;
                            values[ i ] = arguments.length > 1 ?
                                    slice.call( arguments ) : value;
    
                            if ( values === progressValues ) {
                                deferred.notifyWith( contexts, values );
                            } else if ( !(--remaining) ) {
                                deferred.resolveWith( contexts, values );
                            }
                        };
                    },
    
                    progressValues, progressContexts, resolveContexts;
    
                // add listeners to Deferred subordinates; treat others as resolved
                if ( length > 1 ) {
                    progressValues = new Array( length );
                    progressContexts = new Array( length );
                    resolveContexts = new Array( length );
                    for ( ; i < length; i++ ) {
                        if ( resolveValues[ i ] &&
                                $.isFunction( resolveValues[ i ].promise ) ) {
    
                            resolveValues[ i ].promise()
                                    .done( updateFunc( i, resolveContexts,
                                            resolveValues ) )
                                    .fail( deferred.reject )
                                    .progress( updateFunc( i, progressContexts,
                                            progressValues ) );
                        } else {
                            --remaining;
                        }
                    }
                }
    
                // if we're not waiting on anything, resolve the master
                if ( !remaining ) {
                    deferred.resolveWith( resolveContexts, resolveValues );
                }
    
                return deferred.promise();
            }
        };
    
        return api;
    });
    define('promise',[
        'promise-builtin'
    ], function( $ ) {
        return $;
    });
    /**
     * @fileOverview 基础类方法。
     */
    
    /**
     * Web Uploader内部类的详细说明，以下提及的功能类，都可以在`WebUploader`这个变量中访问到。
     *
     * As you know, Web Uploader的每个文件都是用过[AMD](https://github.com/amdjs/amdjs-api/wiki/AMD)规范中的`define`组织起来的, 每个Module都会有个module id.
     * 默认module id为该文件的路径，而此路径将会转化成名字空间存放在WebUploader中。如：
     *
     * * module `base`：WebUploader.Base
     * * module `file`: WebUploader.File
     * * module `lib/dnd`: WebUploader.Lib.Dnd
     * * module `runtime/html5/dnd`: WebUploader.Runtime.Html5.Dnd
     *
     *
     * 以下文档中对类的使用可能省略掉了`WebUploader`前缀。
     * @module WebUploader
     * @title WebUploader API文档
     */
    define('base',[
        'dollar',
        'promise'
    ], function( $, promise ) {
    
        var noop = function() {},
            call = Function.call;
    
        // http://jsperf.com/uncurrythis
        // 反科里化
        function uncurryThis( fn ) {
            return function() {
                return call.apply( fn, arguments );
            };
        }
    
        function bindFn( fn, context ) {
            return function() {
                return fn.apply( context, arguments );
            };
        }
    
        function createObject( proto ) {
            var f;
    
            if ( Object.create ) {
                return Object.create( proto );
            } else {
                f = function() {};
                f.prototype = proto;
                return new f();
            }
        }
    
    
        /**
         * 基础类，提供一些简单常用的方法。
         * @class Base
         */
        return {
    
            /**
             * @property {String} version 当前版本号。
             */
            version: '0.1.6',
    
            /**
             * @property {jQuery|Zepto} $ 引用依赖的jQuery或者Zepto对象。
             */
            $: $,
    
            Deferred: promise.Deferred,
    
            isPromise: promise.isPromise,
    
            when: promise.when,
    
            /**
             * @description  简单的浏览器检查结果。
             *
             * * `webkit`  webkit版本号，如果浏览器为非webkit内核，此属性为`undefined`。
             * * `chrome`  chrome浏览器版本号，如果浏览器为chrome，此属性为`undefined`。
             * * `ie`  ie浏览器版本号，如果浏览器为非ie，此属性为`undefined`。**暂不支持ie10+**
             * * `firefox`  firefox浏览器版本号，如果浏览器为非firefox，此属性为`undefined`。
             * * `safari`  safari浏览器版本号，如果浏览器为非safari，此属性为`undefined`。
             * * `opera`  opera浏览器版本号，如果浏览器为非opera，此属性为`undefined`。
             *
             * @property {Object} [browser]
             */
            browser: (function( ua ) {
                var ret = {},
                    webkit = ua.match( /WebKit\/([\d.]+)/ ),
                    chrome = ua.match( /Chrome\/([\d.]+)/ ) ||
                        ua.match( /CriOS\/([\d.]+)/ ),
    
                    ie = ua.match( /MSIE\s([\d\.]+)/ ) ||
                        ua.match( /(?:trident)(?:.*rv:([\w.]+))?/i ),
                    firefox = ua.match( /Firefox\/([\d.]+)/ ),
                    safari = ua.match( /Safari\/([\d.]+)/ ),
                    opera = ua.match( /OPR\/([\d.]+)/ );
    
                webkit && (ret.webkit = parseFloat( webkit[ 1 ] ));
                chrome && (ret.chrome = parseFloat( chrome[ 1 ] ));
                ie && (ret.ie = parseFloat( ie[ 1 ] ));
                firefox && (ret.firefox = parseFloat( firefox[ 1 ] ));
                safari && (ret.safari = parseFloat( safari[ 1 ] ));
                opera && (ret.opera = parseFloat( opera[ 1 ] ));
    
                return ret;
            })( navigator.userAgent ),
    
            /**
             * @description  操作系统检查结果。
             *
             * * `android`  如果在android浏览器环境下，此值为对应的android版本号，否则为`undefined`。
             * * `ios` 如果在ios浏览器环境下，此值为对应的ios版本号，否则为`undefined`。
             * @property {Object} [os]
             */
            os: (function( ua ) {
                var ret = {},
    
                    // osx = !!ua.match( /\(Macintosh\; Intel / ),
                    android = ua.match( /(?:Android);?[\s\/]+([\d.]+)?/ ),
                    ios = ua.match( /(?:iPad|iPod|iPhone).*OS\s([\d_]+)/ );
    
                // osx && (ret.osx = true);
                android && (ret.android = parseFloat( android[ 1 ] ));
                ios && (ret.ios = parseFloat( ios[ 1 ].replace( /_/g, '.' ) ));
    
                return ret;
            })( navigator.userAgent ),
    
            /**
             * 实现类与类之间的继承。
             * @method inherits
             * @grammar Base.inherits( super ) => child
             * @grammar Base.inherits( super, protos ) => child
             * @grammar Base.inherits( super, protos, statics ) => child
             * @param  {Class} super 父类
             * @param  {Object | Function} [protos] 子类或者对象。如果对象中包含constructor，子类将是用此属性值。
             * @param  {Function} [protos.constructor] 子类构造器，不指定的话将创建个临时的直接执行父类构造器的方法。
             * @param  {Object} [statics] 静态属性或方法。
             * @return {Class} 返回子类。
             * @example
             * function Person() {
             *     console.log( 'Super' );
             * }
             * Person.prototype.hello = function() {
             *     console.log( 'hello' );
             * };
             *
             * var Manager = Base.inherits( Person, {
             *     world: function() {
             *         console.log( 'World' );
             *     }
             * });
             *
             * // 因为没有指定构造器，父类的构造器将会执行。
             * var instance = new Manager();    // => Super
             *
             * // 继承子父类的方法
             * instance.hello();    // => hello
             * instance.world();    // => World
             *
             * // 子类的__super__属性指向父类
             * console.log( Manager.__super__ === Person );    // => true
             */
            inherits: function( Super, protos, staticProtos ) {
                var child;
    
                if ( typeof protos === 'function' ) {
                    child = protos;
                    protos = null;
                } else if ( protos && protos.hasOwnProperty('constructor') ) {
                    child = protos.constructor;
                } else {
                    child = function() {
                        return Super.apply( this, arguments );
                    };
                }
    
                // 复制静态方法
                $.extend( true, child, Super, staticProtos || {} );
    
                /* jshint camelcase: false */
    
                // 让子类的__super__属性指向父类。
                child.__super__ = Super.prototype;
    
                // 构建原型，添加原型方法或属性。
                // 暂时用Object.create实现。
                child.prototype = createObject( Super.prototype );
                protos && $.extend( true, child.prototype, protos );
    
                return child;
            },
    
            /**
             * 一个不做任何事情的方法。可以用来赋值给默认的callback.
             * @method noop
             */
            noop: noop,
    
            /**
             * 返回一个新的方法，此方法将已指定的`context`来执行。
             * @grammar Base.bindFn( fn, context ) => Function
             * @method bindFn
             * @example
             * var doSomething = function() {
             *         console.log( this.name );
             *     },
             *     obj = {
             *         name: 'Object Name'
             *     },
             *     aliasFn = Base.bind( doSomething, obj );
             *
             *  aliasFn();    // => Object Name
             *
             */
            bindFn: bindFn,
    
            /**
             * 引用Console.log如果存在的话，否则引用一个[空函数noop](#WebUploader:Base.noop)。
             * @grammar Base.log( args... ) => undefined
             * @method log
             */
            log: (function() {
                if ( window.console ) {
                    return bindFn( console.log, console );
                }
                return noop;
            })(),
    
            nextTick: (function() {
    
                return function( cb ) {
                    setTimeout( cb, 1 );
                };
    
                // @bug 当浏览器不在当前窗口时就停了。
                // var next = window.requestAnimationFrame ||
                //     window.webkitRequestAnimationFrame ||
                //     window.mozRequestAnimationFrame ||
                //     function( cb ) {
                //         window.setTimeout( cb, 1000 / 60 );
                //     };
    
                // // fix: Uncaught TypeError: Illegal invocation
                // return bindFn( next, window );
            })(),
    
            /**
             * 被[uncurrythis](http://www.2ality.com/2011/11/uncurrying-this.html)的数组slice方法。
             * 将用来将非数组对象转化成数组对象。
             * @grammar Base.slice( target, start[, end] ) => Array
             * @method slice
             * @example
             * function doSomthing() {
             *     var args = Base.slice( arguments, 1 );
             *     console.log( args );
             * }
             *
             * doSomthing( 'ignored', 'arg2', 'arg3' );    // => Array ["arg2", "arg3"]
             */
            slice: uncurryThis( [].slice ),
    
            /**
             * 生成唯一的ID
             * @method guid
             * @grammar Base.guid() => String
             * @grammar Base.guid( prefx ) => String
             */
            guid: (function() {
                var counter = 0;
    
                return function( prefix ) {
                    var guid = (+new Date()).toString( 32 ),
                        i = 0;
    
                    for ( ; i < 5; i++ ) {
                        guid += Math.floor( Math.random() * 65535 ).toString( 32 );
                    }
    
                    return (prefix || 'wu_') + guid + (counter++).toString( 32 );
                };
            })(),
    
            /**
             * 格式化文件大小, 输出成带单位的字符串
             * @method formatSize
             * @grammar Base.formatSize( size ) => String
             * @grammar Base.formatSize( size, pointLength ) => String
             * @grammar Base.formatSize( size, pointLength, units ) => String
             * @param {Number} size 文件大小
             * @param {Number} [pointLength=2] 精确到的小数点数。
             * @param {Array} [units=[ 'B', 'K', 'M', 'G', 'TB' ]] 单位数组。从字节，到千字节，一直往上指定。如果单位数组里面只指定了到了K(千字节)，同时文件大小大于M, 此方法的输出将还是显示成多少K.
             * @example
             * console.log( Base.formatSize( 100 ) );    // => 100B
             * console.log( Base.formatSize( 1024 ) );    // => 1.00K
             * console.log( Base.formatSize( 1024, 0 ) );    // => 1K
             * console.log( Base.formatSize( 1024 * 1024 ) );    // => 1.00M
             * console.log( Base.formatSize( 1024 * 1024 * 1024 ) );    // => 1.00G
             * console.log( Base.formatSize( 1024 * 1024 * 1024, 0, ['B', 'KB', 'MB'] ) );    // => 1024MB
             */
            formatSize: function( size, pointLength, units ) {
                var unit;
    
                units = units || [ 'B', 'K', 'M', 'G', 'TB' ];
    
                while ( (unit = units.shift()) && size > 1024 ) {
                    size = size / 1024;
                }
    
                return (unit === 'B' ? size : size.toFixed( pointLength || 2 )) +
                        unit;
            }
        };
    });
    /**
     * 事件处理类，可以独立使用，也可以扩展给对象使用。
     * @fileOverview Mediator
     */
    define('mediator',[
        'base'
    ], function( Base ) {
        var $ = Base.$,
            slice = [].slice,
            separator = /\s+/,
            protos;
    
        // 根据条件过滤出事件handlers.
        function findHandlers( arr, name, callback, context ) {
            return $.grep( arr, function( handler ) {
                return handler &&
                        (!name || handler.e === name) &&
                        (!callback || handler.cb === callback ||
                        handler.cb._cb === callback) &&
                        (!context || handler.ctx === context);
            });
        }
    
        function eachEvent( events, callback, iterator ) {
            // 不支持对象，只支持多个event用空格隔开
            $.each( (events || '').split( separator ), function( _, key ) {
                iterator( key, callback );
            });
        }
    
        function triggerHanders( events, args ) {
            var stoped = false,
                i = -1,
                len = events.length,
                handler;
    
            while ( ++i < len ) {
                handler = events[ i ];
    
                if ( handler.cb.apply( handler.ctx2, args ) === false ) {
                    stoped = true;
                    break;
                }
            }
    
            return !stoped;
        }
    
        protos = {
    
            /**
             * 绑定事件。
             *
             * `callback`方法在执行时，arguments将会来源于trigger的时候携带的参数。如
             * ```javascript
             * var obj = {};
             *
             * // 使得obj有事件行为
             * Mediator.installTo( obj );
             *
             * obj.on( 'testa', function( arg1, arg2 ) {
             *     console.log( arg1, arg2 ); // => 'arg1', 'arg2'
             * });
             *
             * obj.trigger( 'testa', 'arg1', 'arg2' );
             * ```
             *
             * 如果`callback`中，某一个方法`return false`了，则后续的其他`callback`都不会被执行到。
             * 切会影响到`trigger`方法的返回值，为`false`。
             *
             * `on`还可以用来添加一个特殊事件`all`, 这样所有的事件触发都会响应到。同时此类`callback`中的arguments有一个不同处，
             * 就是第一个参数为`type`，记录当前是什么事件在触发。此类`callback`的优先级比脚低，会再正常`callback`执行完后触发。
             * ```javascript
             * obj.on( 'all', function( type, arg1, arg2 ) {
             *     console.log( type, arg1, arg2 ); // => 'testa', 'arg1', 'arg2'
             * });
             * ```
             *
             * @method on
             * @grammar on( name, callback[, context] ) => self
             * @param  {String}   name     事件名，支持多个事件用空格隔开
             * @param  {Function} callback 事件处理器
             * @param  {Object}   [context]  事件处理器的上下文。
             * @return {self} 返回自身，方便链式
             * @chainable
             * @class Mediator
             */
            on: function( name, callback, context ) {
                var me = this,
                    set;
    
                if ( !callback ) {
                    return this;
                }
    
                set = this._events || (this._events = []);
    
                eachEvent( name, callback, function( name, callback ) {
                    var handler = { e: name };
    
                    handler.cb = callback;
                    handler.ctx = context;
                    handler.ctx2 = context || me;
                    handler.id = set.length;
    
                    set.push( handler );
                });
    
                return this;
            },
    
            /**
             * 绑定事件，且当handler执行完后，自动解除绑定。
             * @method once
             * @grammar once( name, callback[, context] ) => self
             * @param  {String}   name     事件名
             * @param  {Function} callback 事件处理器
             * @param  {Object}   [context]  事件处理器的上下文。
             * @return {self} 返回自身，方便链式
             * @chainable
             */
            once: function( name, callback, context ) {
                var me = this;
    
                if ( !callback ) {
                    return me;
                }
    
                eachEvent( name, callback, function( name, callback ) {
                    var once = function() {
                            me.off( name, once );
                            return callback.apply( context || me, arguments );
                        };
    
                    once._cb = callback;
                    me.on( name, once, context );
                });
    
                return me;
            },
    
            /**
             * 解除事件绑定
             * @method off
             * @grammar off( [name[, callback[, context] ] ] ) => self
             * @param  {String}   [name]     事件名
             * @param  {Function} [callback] 事件处理器
             * @param  {Object}   [context]  事件处理器的上下文。
             * @return {self} 返回自身，方便链式
             * @chainable
             */
            off: function( name, cb, ctx ) {
                var events = this._events;
    
                if ( !events ) {
                    return this;
                }
    
                if ( !name && !cb && !ctx ) {
                    this._events = [];
                    return this;
                }
    
                eachEvent( name, cb, function( name, cb ) {
                    $.each( findHandlers( events, name, cb, ctx ), function() {
                        delete events[ this.id ];
                    });
                });
    
                return this;
            },
    
            /**
             * 触发事件
             * @method trigger
             * @grammar trigger( name[, args...] ) => self
             * @param  {String}   type     事件名
             * @param  {*} [...] 任意参数
             * @return {Boolean} 如果handler中return false了，则返回false, 否则返回true
             */
            trigger: function( type ) {
                var args, events, allEvents;
    
                if ( !this._events || !type ) {
                    return this;
                }
    
                args = slice.call( arguments, 1 );
                events = findHandlers( this._events, type );
                allEvents = findHandlers( this._events, 'all' );
    
                return triggerHanders( events, args ) &&
                        triggerHanders( allEvents, arguments );
            }
        };
    
        /**
         * 中介者，它本身是个单例，但可以通过[installTo](#WebUploader:Mediator:installTo)方法，使任何对象具备事件行为。
         * 主要目的是负责模块与模块之间的合作，降低耦合度。
         *
         * @class Mediator
         */
        return $.extend({
    
            /**
             * 可以通过这个接口，使任何对象具备事件功能。
             * @method installTo
             * @param  {Object} obj 需要具备事件行为的对象。
             * @return {Object} 返回obj.
             */
            installTo: function( obj ) {
                return $.extend( obj, protos );
            }
    
        }, protos );
    });
    /**
     * @fileOverview Uploader上传类
     */
    define('uploader',[
        'base',
        'mediator'
    ], function( Base, Mediator ) {
    
        var $ = Base.$;
    
        /**
         * 上传入口类。
         * @class Uploader
         * @constructor
         * @grammar new Uploader( opts ) => Uploader
         * @example
         * var uploader = WebUploader.Uploader({
         *     swf: 'path_of_swf/Uploader.swf',
         *
         *     // 开起分片上传。
         *     chunked: true
         * });
         */
        function Uploader( opts ) {
            this.options = $.extend( true, {}, Uploader.options, opts );
            this._init( this.options );
        }
    
        // default Options
        // widgets中有相应扩展
        Uploader.options = {};
        Mediator.installTo( Uploader.prototype );
    
        // 批量添加纯命令式方法。
        $.each({
            upload: 'start-upload',
            stop: 'stop-upload',
            getFile: 'get-file',
            getFiles: 'get-files',
            addFile: 'add-file',
            addFiles: 'add-file',
            sort: 'sort-files',
            removeFile: 'remove-file',
            cancelFile: 'cancel-file',
            skipFile: 'skip-file',
            retry: 'retry',
            isInProgress: 'is-in-progress',
            makeThumb: 'make-thumb',
            md5File: 'md5-file',
            getDimension: 'get-dimension',
            addButton: 'add-btn',
            predictRuntimeType: 'predict-runtime-type',
            refresh: 'refresh',
            disable: 'disable',
            enable: 'enable',
            reset: 'reset'
        }, function( fn, command ) {
            Uploader.prototype[ fn ] = function() {
                return this.request( command, arguments );
            };
        });
    
        $.extend( Uploader.prototype, {
            state: 'pending',
    
            _init: function( opts ) {
                var me = this;
    
                me.request( 'init', opts, function() {
                    me.state = 'ready';
                    me.trigger('ready');
                });
            },
    
            /**
             * 获取或者设置Uploader配置项。
             * @method option
             * @grammar option( key ) => *
             * @grammar option( key, val ) => self
             * @example
             *
             * // 初始状态图片上传前不会压缩
             * var uploader = new WebUploader.Uploader({
             *     compress: null;
             * });
             *
             * // 修改后图片上传前，尝试将图片压缩到1600 * 1600
             * uploader.option( 'compress', {
             *     width: 1600,
             *     height: 1600
             * });
             */
            option: function( key, val ) {
                var opts = this.options;
    
                // setter
                if ( arguments.length > 1 ) {
    
                    if ( $.isPlainObject( val ) &&
                            $.isPlainObject( opts[ key ] ) ) {
                        $.extend( opts[ key ], val );
                    } else {
                        opts[ key ] = val;
                    }
    
                } else {    // getter
                    return key ? opts[ key ] : opts;
                }
            },
    
            /**
             * 获取文件统计信息。返回一个包含一下信息的对象。
             * * `successNum` 上传成功的文件数
             * * `progressNum` 上传中的文件数
             * * `cancelNum` 被删除的文件数
             * * `invalidNum` 无效的文件数
             * * `uploadFailNum` 上传失败的文件数
             * * `queueNum` 还在队列中的文件数
             * * `interruptNum` 被暂停的文件数
             * @method getStats
             * @grammar getStats() => Object
             */
            getStats: function() {
                // return this._mgr.getStats.apply( this._mgr, arguments );
                var stats = this.request('get-stats');
    
                return stats ? {
             