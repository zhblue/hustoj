/*
 ** LICENSE for the sqlite3 WebAssembly/JavaScript APIs.
 **
 ** This bundle (typically released as sqlite3.js or sqlite3.mjs)
 ** is an amalgamation of JavaScript source code from two projects:
 **
 ** 1) https://emscripten.org: the Emscripten "glue code" is covered by
 **    the terms of the MIT license and University of Illinois/NCSA
 **    Open Source License, as described at:
 **
 **    https://emscripten.org/docs/introducing_emscripten/emscripten_license.html
 **
 ** 2) https://sqlite.org: all code and documentation labeled as being
 **    from this source are released under the same terms as the sqlite3
 **    C library:
 **
 ** 2022-10-16
 **
 ** The author disclaims copyright to this source code.  In place of a
 ** legal notice, here is a blessing:
 **
 ** *   May you do good and not evil.
 ** *   May you find forgiveness for yourself and forgive others.
 ** *   May you share freely, never taking more than you give.
 */
/*
 ** This code was built from sqlite3 version...
 **
 ** SQLITE_VERSION "3.49.2"
 ** SQLITE_VERSION_NUMBER 3049002
 ** SQLITE_SOURCE_ID "2025-05-07 10:39:52 17144570b0d96ae63cd6f3edca39e27ebd74925252bbaf6723bcb2f6b4861fb1"
 **
 ** Using the Emscripten SDK version 3.1.70.
 */

var sqlite3InitModule = (() => {
  var _scriptName = import.meta.url;

  return function (moduleArg = {}) {
    var moduleRtn;

    var Module = moduleArg;

    var readyPromiseResolve, readyPromiseReject;
    var readyPromise = new Promise((resolve, reject) => {
      readyPromiseResolve = resolve;
      readyPromiseReject = reject;
    });

    var ENVIRONMENT_IS_WEB = typeof window == 'object';
    var ENVIRONMENT_IS_WORKER = typeof importScripts == 'function';

    var ENVIRONMENT_IS_NODE =
      typeof process == 'object' &&
      typeof process.versions == 'object' &&
      typeof process.versions.node == 'string' &&
      process.type != 'renderer';
    var ENVIRONMENT_IS_SHELL =
      !ENVIRONMENT_IS_WEB && !ENVIRONMENT_IS_NODE && !ENVIRONMENT_IS_WORKER;

    const sqlite3InitModuleState =
      globalThis.sqlite3InitModuleState ||
      Object.assign(Object.create(null), {
        debugModule: () => {},
      });
    delete globalThis.sqlite3InitModuleState;
    sqlite3InitModuleState.debugModule(
      'globalThis.location =',
      globalThis.location,
    );

    var moduleOverrides = Object.assign({}, Module);

    var arguments_ = [];
    var thisProgram = './this.program';
    var quit_ = (status, toThrow) => {
      throw toThrow;
    };

    var scriptDirectory = '';
    function locateFile(path) {
      if (Module['locateFile']) {
        return Module['locateFile'](path, scriptDirectory);
      }
      return scriptDirectory + path;
    }

    var readAsync, readBinary;

    if (ENVIRONMENT_IS_WEB || ENVIRONMENT_IS_WORKER) {
      if (ENVIRONMENT_IS_WORKER) {
        scriptDirectory = self.location.href;
      } else if (typeof document != 'undefined' && document.currentScript) {
        scriptDirectory = document.currentScript.src;
      }

      if (_scriptName) {
        scriptDirectory = _scriptName;
      }

      if (scriptDirectory.startsWith('blob:')) {
        scriptDirectory = '';
      } else {
        scriptDirectory = scriptDirectory.substr(
          0,
          scriptDirectory.replace(/[?#].*/, '').lastIndexOf('/') + 1,
        );
      }

      {
        if (ENVIRONMENT_IS_WORKER) {
          readBinary = (url) => {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', url, false);
            xhr.responseType = 'arraybuffer';
            xhr.send(null);
            return new Uint8Array(xhr.response);
          };
        }

        readAsync = (url) => {
          return fetch(url, { credentials: 'same-origin' }).then((response) => {
            if (response.ok) {
              return response.arrayBuffer();
            }
            return Promise.reject(
              new Error(response.status + ' : ' + response.url),
            );
          });
        };
      }
    } else {
    }

    var out = Module['print'] || console.log.bind(console);
    var err = Module['printErr'] || console.error.bind(console);

    Object.assign(Module, moduleOverrides);

    moduleOverrides = null;

    if (Module['arguments']) arguments_ = Module['arguments'];

    if (Module['thisProgram']) thisProgram = Module['thisProgram'];

    var wasmBinary = Module['wasmBinary'];

    var wasmMemory;

    var ABORT = false;

    var EXITSTATUS;

    function assert(condition, text) {
      if (!condition) {
        abort(text);
      }
    }

    var HEAP,
      HEAP8,
      HEAPU8,
      HEAP16,
      HEAPU16,
      HEAP32,
      HEAPU32,
      HEAPF32,
      HEAP64,
      HEAPU64,
      HEAPF64;

    function updateMemoryViews() {
      var b = wasmMemory.buffer;
      Module['HEAP8'] = HEAP8 = new Int8Array(b);
      Module['HEAP16'] = HEAP16 = new Int16Array(b);
      Module['HEAPU8'] = HEAPU8 = new Uint8Array(b);
      Module['HEAPU16'] = HEAPU16 = new Uint16Array(b);
      Module['HEAP32'] = HEAP32 = new Int32Array(b);
      Module['HEAPU32'] = HEAPU32 = new Uint32Array(b);
      Module['HEAPF32'] = HEAPF32 = new Float32Array(b);
      Module['HEAPF64'] = HEAPF64 = new Float64Array(b);
      Module['HEAP64'] = HEAP64 = new BigInt64Array(b);
      Module['HEAPU64'] = HEAPU64 = new BigUint64Array(b);
    }

    if (Module['wasmMemory']) {
      wasmMemory = Module['wasmMemory'];
    } else {
      var INITIAL_MEMORY = Module['INITIAL_MEMORY'] || 16777216;

      wasmMemory = new WebAssembly.Memory({
        initial: INITIAL_MEMORY / 65536,

        maximum: 32768,
      });
    }

    updateMemoryViews();

    var __ATPRERUN__ = [];
    var __ATINIT__ = [];
    var __ATEXIT__ = [];
    var __ATPOSTRUN__ = [];

    var runtimeInitialized = false;

    function preRun() {
      var preRuns = Module['preRun'];
      if (preRuns) {
        if (typeof preRuns == 'function') preRuns = [preRuns];
        preRuns.forEach(addOnPreRun);
      }
      callRuntimeCallbacks(__ATPRERUN__);
    }

    function initRuntime() {
      runtimeInitialized = true;

      if (!Module['noFSInit'] && !FS.initialized) FS.init();
      FS.ignorePermissions = false;

      TTY.init();
      callRuntimeCallbacks(__ATINIT__);
    }

    function postRun() {
      var postRuns = Module['postRun'];
      if (postRuns) {
        if (typeof postRuns == 'function') postRuns = [postRuns];
        postRuns.forEach(addOnPostRun);
      }

      callRuntimeCallbacks(__ATPOSTRUN__);
    }

    function addOnPreRun(cb) {
      __ATPRERUN__.unshift(cb);
    }

    function addOnInit(cb) {
      __ATINIT__.unshift(cb);
    }

    function addOnExit(cb) {}

    function addOnPostRun(cb) {
      __ATPOSTRUN__.unshift(cb);
    }

    var runDependencies = 0;
    var runDependencyWatcher = null;
    var dependenciesFulfilled = null;

    function getUniqueRunDependency(id) {
      return id;
    }

    function addRunDependency(id) {
      runDependencies++;

      Module['monitorRunDependencies']?.(runDependencies);
    }

    function removeRunDependency(id) {
      runDependencies--;

      Module['monitorRunDependencies']?.(runDependencies);

      if (runDependencies == 0) {
        if (runDependencyWatcher !== null) {
          clearInterval(runDependencyWatcher);
          runDependencyWatcher = null;
        }
        if (dependenciesFulfilled) {
          var callback = dependenciesFulfilled;
          dependenciesFulfilled = null;
          callback();
        }
      }
    }

    function abort(what) {
      Module['onAbort']?.(what);

      what = 'Aborted(' + what + ')';

      err(what);

      ABORT = true;

      what += '. Build with -sASSERTIONS for more info.';

      var e = new WebAssembly.RuntimeError(what);

      readyPromiseReject(e);

      throw e;
    }

    var dataURIPrefix = 'data:application/octet-stream;base64,';

    var isDataURI = (filename) => filename.startsWith(dataURIPrefix);

    var isFileURI = (filename) => filename.startsWith('file://');

    function findWasmBinary() {
      if (Module['locateFile']) {
        var f = 'sqlite3.wasm';
        if (!isDataURI(f)) {
          return locateFile(f);
        }
        return f;
      }

      return new URL('sqlite3.wasm', import.meta.url).href;
    }

    var wasmBinaryFile;

    function getBinarySync(file) {
      if (file == wasmBinaryFile && wasmBinary) {
        return new Uint8Array(wasmBinary);
      }
      if (readBinary) {
        return readBinary(file);
      }
      throw 'both async and sync fetching of the wasm failed';
    }

    function getBinaryPromise(binaryFile) {
      if (!wasmBinary) {
        return readAsync(binaryFile).then(
          (response) => new Uint8Array(response),

          () => getBinarySync(binaryFile),
        );
      }

      return Promise.resolve().then(() => getBinarySync(binaryFile));
    }

    function instantiateArrayBuffer(binaryFile, imports, receiver) {
      return getBinaryPromise(binaryFile)
        .then((binary) => {
          return WebAssembly.instantiate(binary, imports);
        })
        .then(receiver, (reason) => {
          err(`failed to asynchronously prepare wasm: ${reason}`);

          abort(reason);
        });
    }

    function instantiateAsync(binary, binaryFile, imports, callback) {
      if (
        !binary &&
        typeof WebAssembly.instantiateStreaming == 'function' &&
        !isDataURI(binaryFile) &&
        typeof fetch == 'function'
      ) {
        return fetch(binaryFile, { credentials: 'same-origin' }).then(
          (response) => {
            var result = WebAssembly.instantiateStreaming(response, imports);

            return result.then(callback, function (reason) {
              //err(`wasm streaming compile failed: ${reason}`);
              //err('falling back to ArrayBuffer instantiation');
              return instantiateArrayBuffer(binaryFile, imports, callback);
            });
          },
        );
      }
      return instantiateArrayBuffer(binaryFile, imports, callback);
    }

    function getWasmImports() {
      return {
        env: wasmImports,
        wasi_snapshot_preview1: wasmImports,
      };
    }

    function createWasm() {
      var info = getWasmImports();

      function receiveInstance(instance, module) {
        wasmExports = instance.exports;

        addOnInit(wasmExports['__wasm_call_ctors']);

        removeRunDependency('wasm-instantiate');
        return wasmExports;
      }

      addRunDependency('wasm-instantiate');

      function receiveInstantiationResult(result) {
        receiveInstance(result['instance']);
      }

      if (Module['instantiateWasm']) {
        try {
          return Module['instantiateWasm'](info, receiveInstance);
        } catch (e) {
          err(`Module.instantiateWasm callback failed with error: ${e}`);

          readyPromiseReject(e);
        }
      }

      wasmBinaryFile ??= findWasmBinary();

      instantiateAsync(
        wasmBinary,
        wasmBinaryFile,
        info,
        receiveInstantiationResult,
      ).catch(readyPromiseReject);
      return {};
    }

    function ExitStatus(status) {
      this.name = 'ExitStatus';
      this.message = `Program terminated with exit(${status})`;
      this.status = status;
    }

    var callRuntimeCallbacks = (callbacks) => {
      callbacks.forEach((f) => f(Module));
    };

    function getValue(ptr, type = 'i8') {
      if (type.endsWith('*')) type = '*';
      switch (type) {
        case 'i1':
          return HEAP8[ptr];
        case 'i8':
          return HEAP8[ptr];
        case 'i16':
          return HEAP16[ptr >> 1];
        case 'i32':
          return HEAP32[ptr >> 2];
        case 'i64':
          return HEAP64[ptr >> 3];
        case 'float':
          return HEAPF32[ptr >> 2];
        case 'double':
          return HEAPF64[ptr >> 3];
        case '*':
          return HEAPU32[ptr >> 2];
        default:
          abort(`invalid type for getValue: ${type}`);
      }
    }

    var noExitRuntime = Module['noExitRuntime'] || true;

    function setValue(ptr, value, type = 'i8') {
      if (type.endsWith('*')) type = '*';
      switch (type) {
        case 'i1':
          HEAP8[ptr] = value;
          break;
        case 'i8':
          HEAP8[ptr] = value;
          break;
        case 'i16':
          HEAP16[ptr >> 1] = value;
          break;
        case 'i32':
          HEAP32[ptr >> 2] = value;
          break;
        case 'i64':
          HEAP64[ptr >> 3] = BigInt(value);
          break;
        case 'float':
          HEAPF32[ptr >> 2] = value;
          break;
        case 'double':
          HEAPF64[ptr >> 3] = value;
          break;
        case '*':
          HEAPU32[ptr >> 2] = value;
          break;
        default:
          abort(`invalid type for setValue: ${type}`);
      }
    }

    var stackRestore = (val) => __emscripten_stack_restore(val);

    var stackSave = () => _emscripten_stack_get_current();

    var PATH = {
      isAbs: (path) => path.charAt(0) === '/',
      splitPath: (filename) => {
        var splitPathRe =
          /^(\/?|)([\s\S]*?)((?:\.{1,2}|[^\/]+?|)(\.[^.\/]*|))(?:[\/]*)$/;
        return splitPathRe.exec(filename).slice(1);
      },
      normalizeArray: (parts, allowAboveRoot) => {
        var up = 0;
        for (var i = parts.length - 1; i >= 0; i--) {
          var last = parts[i];
          if (last === '.') {
            parts.splice(i, 1);
          } else if (last === '..') {
            parts.splice(i, 1);
            up++;
          } else if (up) {
            parts.splice(i, 1);
            up--;
          }
        }

        if (allowAboveRoot) {
          for (; up; up--) {
            parts.unshift('..');
          }
        }
        return parts;
      },
      normalize: (path) => {
        var isAbsolute = PATH.isAbs(path),
          trailingSlash = path.substr(-1) === '/';

        path = PATH.normalizeArray(
          path.split('/').filter((p) => !!p),
          !isAbsolute,
        ).join('/');
        if (!path && !isAbsolute) {
          path = '.';
        }
        if (path && trailingSlash) {
          path += '/';
        }
        return (isAbsolute ? '/' : '') + path;
      },
      dirname: (path) => {
        var result = PATH.splitPath(path),
          root = result[0],
          dir = result[1];
        if (!root && !dir) {
          return '.';
        }
        if (dir) {
          dir = dir.substr(0, dir.length - 1);
        }
        return root + dir;
      },
      basename: (path) => {
        if (path === '/') return '/';
        path = PATH.normalize(path);
        path = path.replace(/\/$/, '');
        var lastSlash = path.lastIndexOf('/');
        if (lastSlash === -1) return path;
        return path.substr(lastSlash + 1);
      },
      join: (...paths) => PATH.normalize(paths.join('/')),
      join2: (l, r) => PATH.normalize(l + '/' + r),
    };

    var initRandomFill = () => {
      if (
        typeof crypto == 'object' &&
        typeof crypto['getRandomValues'] == 'function'
      ) {
        return (view) => crypto.getRandomValues(view);
      } else abort('initRandomDevice');
    };
    var randomFill = (view) => {
      return (randomFill = initRandomFill())(view);
    };

    var PATH_FS = {
      resolve: (...args) => {
        var resolvedPath = '',
          resolvedAbsolute = false;
        for (var i = args.length - 1; i >= -1 && !resolvedAbsolute; i--) {
          var path = i >= 0 ? args[i] : FS.cwd();

          if (typeof path != 'string') {
            throw new TypeError('Arguments to path.resolve must be strings');
          } else if (!path) {
            return '';
          }
          resolvedPath = path + '/' + resolvedPath;
          resolvedAbsolute = PATH.isAbs(path);
        }

        resolvedPath = PATH.normalizeArray(
          resolvedPath.split('/').filter((p) => !!p),
          !resolvedAbsolute,
        ).join('/');
        return (resolvedAbsolute ? '/' : '') + resolvedPath || '.';
      },
      relative: (from, to) => {
        from = PATH_FS.resolve(from).substr(1);
        to = PATH_FS.resolve(to).substr(1);
        function trim(arr) {
          var start = 0;
          for (; start < arr.length; start++) {
            if (arr[start] !== '') break;
          }
          var end = arr.length - 1;
          for (; end >= 0; end--) {
            if (arr[end] !== '') break;
          }
          if (start > end) return [];
          return arr.slice(start, end - start + 1);
        }
        var fromParts = trim(from.split('/'));
        var toParts = trim(to.split('/'));
        var length = Math.min(fromParts.length, toParts.length);
        var samePartsLength = length;
        for (var i = 0; i < length; i++) {
          if (fromParts[i] !== toParts[i]) {
            samePartsLength = i;
            break;
          }
        }
        var outputParts = [];
        for (var i = samePartsLength; i < fromParts.length; i++) {
          outputParts.push('..');
        }
        outputParts = outputParts.concat(toParts.slice(samePartsLength));
        return outputParts.join('/');
      },
    };

    var UTF8Decoder =
      typeof TextDecoder != 'undefined' ? new TextDecoder() : undefined;

    var UTF8ArrayToString = (heapOrArray, idx = 0, maxBytesToRead = NaN) => {
      var endIdx = idx + maxBytesToRead;
      var endPtr = idx;

      while (heapOrArray[endPtr] && !(endPtr >= endIdx)) ++endPtr;

      if (endPtr - idx > 16 && heapOrArray.buffer && UTF8Decoder) {
        return UTF8Decoder.decode(heapOrArray.subarray(idx, endPtr));
      }
      var str = '';

      while (idx < endPtr) {
        var u0 = heapOrArray[idx++];
        if (!(u0 & 0x80)) {
          str += String.fromCharCode(u0);
          continue;
        }
        var u1 = heapOrArray[idx++] & 63;
        if ((u0 & 0xe0) == 0xc0) {
          str += String.fromCharCode(((u0 & 31) << 6) | u1);
          continue;
        }
        var u2 = heapOrArray[idx++] & 63;
        if ((u0 & 0xf0) == 0xe0) {
          u0 = ((u0 & 15) << 12) | (u1 << 6) | u2;
        } else {
          u0 =
            ((u0 & 7) << 18) |
            (u1 << 12) |
            (u2 << 6) |
            (heapOrArray[idx++] & 63);
        }

        if (u0 < 0x10000) {
          str += String.fromCharCode(u0);
        } else {
          var ch = u0 - 0x10000;
          str += String.fromCharCode(
            0xd800 | (ch >> 10),
            0xdc00 | (ch & 0x3ff),
          );
        }
      }
      return str;
    };

    var FS_stdin_getChar_buffer = [];

    var lengthBytesUTF8 = (str) => {
      var len = 0;
      for (var i = 0; i < str.length; ++i) {
        var c = str.charCodeAt(i);
        if (c <= 0x7f) {
          len++;
        } else if (c <= 0x7ff) {
          len += 2;
        } else if (c >= 0xd800 && c <= 0xdfff) {
          len += 4;
          ++i;
        } else {
          len += 3;
        }
      }
      return len;
    };

    var stringToUTF8Array = (str, heap, outIdx, maxBytesToWrite) => {
      if (!(maxBytesToWrite > 0)) return 0;

      var startIdx = outIdx;
      var endIdx = outIdx + maxBytesToWrite - 1;
      for (var i = 0; i < str.length; ++i) {
        var u = str.charCodeAt(i);
        if (u >= 0xd800 && u <= 0xdfff) {
          var u1 = str.charCodeAt(++i);
          u = (0x10000 + ((u & 0x3ff) << 10)) | (u1 & 0x3ff);
        }
        if (u <= 0x7f) {
          if (outIdx >= endIdx) break;
          heap[outIdx++] = u;
        } else if (u <= 0x7ff) {
          if (outIdx + 1 >= endIdx) break;
          heap[outIdx++] = 0xc0 | (u >> 6);
          heap[outIdx++] = 0x80 | (u & 63);
        } else if (u <= 0xffff) {
          if (outIdx + 2 >= endIdx) break;
          heap[outIdx++] = 0xe0 | (u >> 12);
          heap[outIdx++] = 0x80 | ((u >> 6) & 63);
          heap[outIdx++] = 0x80 | (u & 63);
        } else {
          if (outIdx + 3 >= endIdx) break;
          heap[outIdx++] = 0xf0 | (u >> 18);
          heap[outIdx++] = 0x80 | ((u >> 12) & 63);
          heap[outIdx++] = 0x80 | ((u >> 6) & 63);
          heap[outIdx++] = 0x80 | (u & 63);
        }
      }

      heap[outIdx] = 0;
      return outIdx - startIdx;
    };

    function intArrayFromString(stringy, dontAddNull, length) {
      var len = length > 0 ? length : lengthBytesUTF8(stringy) + 1;
      var u8array = new Array(len);
      var numBytesWritten = stringToUTF8Array(
        stringy,
        u8array,
        0,
        u8array.length,
      );
      if (dontAddNull) u8array.length = numBytesWritten;
      return u8array;
    }
    var FS_stdin_getChar = () => {
      if (!FS_stdin_getChar_buffer.length) {
        var result = null;
        if (
          typeof window != 'undefined' &&
          typeof window.prompt == 'function'
        ) {
          result = window.prompt('Input: ');
          if (result !== null) {
            result += '\n';
          }
        } else {
        }
        if (!result) {
          return null;
        }
        FS_stdin_getChar_buffer = intArrayFromString(result, true);
      }
      return FS_stdin_getChar_buffer.shift();
    };
    var TTY = {
      ttys: [],
      init() {},
      shutdown() {},
      register(dev, ops) {
        TTY.ttys[dev] = { input: [], output: [], ops: ops };
        FS.registerDevice(dev, TTY.stream_ops);
      },
      stream_ops: {
        open(stream) {
          var tty = TTY.ttys[stream.node.rdev];
          if (!tty) {
            throw new FS.ErrnoError(43);
          }
          stream.tty = tty;
          stream.seekable = false;
        },
        close(stream) {
          stream.tty.ops.fsync(stream.tty);
        },
        fsync(stream) {
          stream.tty.ops.fsync(stream.tty);
        },
        read(stream, buffer, offset, length, pos) {
          if (!stream.tty || !stream.tty.ops.get_char) {
            throw new FS.ErrnoError(60);
          }
          var bytesRead = 0;
          for (var i = 0; i < length; i++) {
            var result;
            try {
              result = stream.tty.ops.get_char(stream.tty);
            } catch (e) {
              throw new FS.ErrnoError(29);
            }
            if (result === undefined && bytesRead === 0) {
              throw new FS.ErrnoError(6);
            }
            if (result === null || result === undefined) break;
            bytesRead++;
            buffer[offset + i] = result;
          }
          if (bytesRead) {
            stream.node.timestamp = Date.now();
          }
          return bytesRead;
        },
        write(stream, buffer, offset, length, pos) {
          if (!stream.tty || !stream.tty.ops.put_char) {
            throw new FS.ErrnoError(60);
          }
          try {
            for (var i = 0; i < length; i++) {
              stream.tty.ops.put_char(stream.tty, buffer[offset + i]);
            }
          } catch (e) {
            throw new FS.ErrnoError(29);
          }
          if (length) {
            stream.node.timestamp = Date.now();
          }
          return i;
        },
      },
      default_tty_ops: {
        get_char(tty) {
          return FS_stdin_getChar();
        },
        put_char(tty, val) {
          if (val === null || val === 10) {
            out(UTF8ArrayToString(tty.output));
            tty.output = [];
          } else {
            if (val != 0) tty.output.push(val);
          }
        },
        fsync(tty) {
          if (tty.output && tty.output.length > 0) {
            out(UTF8ArrayToString(tty.output));
            tty.output = [];
          }
        },
        ioctl_tcgets(tty) {
          return {
            c_iflag: 25856,
            c_oflag: 5,
            c_cflag: 191,
            c_lflag: 35387,
            c_cc: [
              0x03, 0x1c, 0x7f, 0x15, 0x04, 0x00, 0x01, 0x00, 0x11, 0x13, 0x1a,
              0x00, 0x12, 0x0f, 0x17, 0x16, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00,
              0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00,
            ],
          };
        },
        ioctl_tcsets(tty, optional_actions, data) {
          return 0;
        },
        ioctl_tiocgwinsz(tty) {
          return [24, 80];
        },
      },
      default_tty1_ops: {
        put_char(tty, val) {
          if (val === null || val === 10) {
            err(UTF8ArrayToString(tty.output));
            tty.output = [];
          } else {
            if (val != 0) tty.output.push(val);
          }
        },
        fsync(tty) {
          if (tty.output && tty.output.length > 0) {
            err(UTF8ArrayToString(tty.output));
            tty.output = [];
          }
        },
      },
    };

    var zeroMemory = (address, size) => {
      HEAPU8.fill(0, address, address + size);
    };

    var alignMemory = (size, alignment) => {
      return Math.ceil(size / alignment) * alignment;
    };
    var mmapAlloc = (size) => {
      size = alignMemory(size, 65536);
      var ptr = _emscripten_builtin_memalign(65536, size);
      if (ptr) zeroMemory(ptr, size);
      return ptr;
    };
    var MEMFS = {
      ops_table: null,
      mount(mount) {
        return MEMFS.createNode(null, '/', 16384 | 511, 0);
      },
      createNode(parent, name, mode, dev) {
        if (FS.isBlkdev(mode) || FS.isFIFO(mode)) {
          throw new FS.ErrnoError(63);
        }
        MEMFS.ops_table ||= {
          dir: {
            node: {
              getattr: MEMFS.node_ops.getattr,
              setattr: MEMFS.node_ops.setattr,
              lookup: MEMFS.node_ops.lookup,
              mknod: MEMFS.node_ops.mknod,
              rename: MEMFS.node_ops.rename,
              unlink: MEMFS.node_ops.unlink,
              rmdir: MEMFS.node_ops.rmdir,
              readdir: MEMFS.node_ops.readdir,
              symlink: MEMFS.node_ops.symlink,
            },
            stream: {
              llseek: MEMFS.stream_ops.llseek,
            },
          },
          file: {
            node: {
              getattr: MEMFS.node_ops.getattr,
              setattr: MEMFS.node_ops.setattr,
            },
            stream: {
              llseek: MEMFS.stream_ops.llseek,
              read: MEMFS.stream_ops.read,
              write: MEMFS.stream_ops.write,
              allocate: MEMFS.stream_ops.allocate,
              mmap: MEMFS.stream_ops.mmap,
              msync: MEMFS.stream_ops.msync,
            },
          },
          link: {
            node: {
              getattr: MEMFS.node_ops.getattr,
              setattr: MEMFS.node_ops.setattr,
              readlink: MEMFS.node_ops.readlink,
            },
            stream: {},
          },
          chrdev: {
            node: {
              getattr: MEMFS.node_ops.getattr,
              setattr: MEMFS.node_ops.setattr,
            },
            stream: FS.chrdev_stream_ops,
          },
        };
        var node = FS.createNode(parent, name, mode, dev);
        if (FS.isDir(node.mode)) {
          node.node_ops = MEMFS.ops_table.dir.node;
          node.stream_ops = MEMFS.ops_table.dir.stream;
          node.contents = {};
        } else if (FS.isFile(node.mode)) {
          node.node_ops = MEMFS.ops_table.file.node;
          node.stream_ops = MEMFS.ops_table.file.stream;
          node.usedBytes = 0;

          node.contents = null;
        } else if (FS.isLink(node.mode)) {
          node.node_ops = MEMFS.ops_table.link.node;
          node.stream_ops = MEMFS.ops_table.link.stream;
        } else if (FS.isChrdev(node.mode)) {
          node.node_ops = MEMFS.ops_table.chrdev.node;
          node.stream_ops = MEMFS.ops_table.chrdev.stream;
        }
        node.timestamp = Date.now();

        if (parent) {
          parent.contents[name] = node;
          parent.timestamp = node.timestamp;
        }
        return node;
      },
      getFileDataAsTypedArray(node) {
        if (!node.contents) return new Uint8Array(0);
        if (node.contents.subarray)
          return node.contents.subarray(0, node.usedBytes);
        return new Uint8Array(node.contents);
      },
      expandFileStorage(node, newCapacity) {
        var prevCapacity = node.contents ? node.contents.length : 0;
        if (prevCapacity >= newCapacity) return;

        var CAPACITY_DOUBLING_MAX = 1024 * 1024;
        newCapacity = Math.max(
          newCapacity,
          (prevCapacity *
            (prevCapacity < CAPACITY_DOUBLING_MAX ? 2.0 : 1.125)) >>>
            0,
        );
        if (prevCapacity != 0) newCapacity = Math.max(newCapacity, 256);
        var oldContents = node.contents;
        node.contents = new Uint8Array(newCapacity);
        if (node.usedBytes > 0)
          node.contents.set(oldContents.subarray(0, node.usedBytes), 0);
      },
      resizeFileStorage(node, newSize) {
        if (node.usedBytes == newSize) return;
        if (newSize == 0) {
          node.contents = null;
          node.usedBytes = 0;
        } else {
          var oldContents = node.contents;
          node.contents = new Uint8Array(newSize);
          if (oldContents) {
            node.contents.set(
              oldContents.subarray(0, Math.min(newSize, node.usedBytes)),
            );
          }
          node.usedBytes = newSize;
        }
      },
      node_ops: {
        getattr(node) {
          var attr = {};

          attr.dev = FS.isChrdev(node.mode) ? node.id : 1;
          attr.ino = node.id;
          attr.mode = node.mode;
          attr.nlink = 1;
          attr.uid = 0;
          attr.gid = 0;
          attr.rdev = node.rdev;
          if (FS.isDir(node.mode)) {
            attr.size = 4096;
          } else if (FS.isFile(node.mode)) {
            attr.size = node.usedBytes;
          } else if (FS.isLink(node.mode)) {
            attr.size = node.link.length;
          } else {
            attr.size = 0;
          }
          attr.atime = new Date(node.timestamp);
          attr.mtime = new Date(node.timestamp);
          attr.ctime = new Date(node.timestamp);

          attr.blksize = 4096;
          attr.blocks = Math.ceil(attr.size / attr.blksize);
          return attr;
        },
        setattr(node, attr) {
          if (attr.mode !== undefined) {
            node.mode = attr.mode;
          }
          if (attr.timestamp !== undefined) {
            node.timestamp = attr.timestamp;
          }
          if (attr.size !== undefined) {
            MEMFS.resizeFileStorage(node, attr.size);
          }
        },
        lookup(parent, name) {
          throw FS.genericErrors[44];
        },
        mknod(parent, name, mode, dev) {
          return MEMFS.createNode(parent, name, mode, dev);
        },
        rename(old_node, new_dir, new_name) {
          if (FS.isDir(old_node.mode)) {
            var new_node;
            try {
              new_node = FS.lookupNode(new_dir, new_name);
            } catch (e) {}
            if (new_node) {
              for (var i in new_node.contents) {
                throw new FS.ErrnoError(55);
              }
            }
          }

          delete old_node.parent.contents[old_node.name];
          old_node.parent.timestamp = Date.now();
          old_node.name = new_name;
          new_dir.contents[new_name] = old_node;
          new_dir.timestamp = old_node.parent.timestamp;
        },
        unlink(parent, name) {
          delete parent.contents[name];
          parent.timestamp = Date.now();
        },
        rmdir(parent, name) {
          var node = FS.lookupNode(parent, name);
          for (var i in node.contents) {
            throw new FS.ErrnoError(55);
          }
          delete parent.contents[name];
          parent.timestamp = Date.now();
        },
        readdir(node) {
          var entries = ['.', '..'];
          for (var key of Object.keys(node.contents)) {
            entries.push(key);
          }
          return entries;
        },
        symlink(parent, newname, oldpath) {
          var node = MEMFS.createNode(parent, newname, 511 | 40960, 0);
          node.link = oldpath;
          return node;
        },
        readlink(node) {
          if (!FS.isLink(node.mode)) {
            throw new FS.ErrnoError(28);
          }
          return node.link;
        },
      },
      stream_ops: {
        read(stream, buffer, offset, length, position) {
          var contents = stream.node.contents;
          if (position >= stream.node.usedBytes) return 0;
          var size = Math.min(stream.node.usedBytes - position, length);
          if (size > 8 && contents.subarray) {
            buffer.set(contents.subarray(position, position + size), offset);
          } else {
            for (var i = 0; i < size; i++)
              buffer[offset + i] = contents[position + i];
          }
          return size;
        },
        write(stream, buffer, offset, length, position, canOwn) {
          if (buffer.buffer === HEAP8.buffer) {
            canOwn = false;
          }

          if (!length) return 0;
          var node = stream.node;
          node.timestamp = Date.now();

          if (buffer.subarray && (!node.contents || node.contents.subarray)) {
            if (canOwn) {
              node.contents = buffer.subarray(offset, offset + length);
              node.usedBytes = length;
              return length;
            } else if (node.usedBytes === 0 && position === 0) {
              node.contents = buffer.slice(offset, offset + length);
              node.usedBytes = length;
              return length;
            } else if (position + length <= node.usedBytes) {
              node.contents.set(
                buffer.subarray(offset, offset + length),
                position,
              );
              return length;
            }
          }

          MEMFS.expandFileStorage(node, position + length);
          if (node.contents.subarray && buffer.subarray) {
            node.contents.set(
              buffer.subarray(offset, offset + length),
              position,
            );
          } else {
            for (var i = 0; i < length; i++) {
              node.contents[position + i] = buffer[offset + i];
            }
          }
          node.usedBytes = Math.max(node.usedBytes, position + length);
          return length;
        },
        llseek(stream, offset, whence) {
          var position = offset;
          if (whence === 1) {
            position += stream.position;
          } else if (whence === 2) {
            if (FS.isFile(stream.node.mode)) {
              position += stream.node.usedBytes;
            }
          }
          if (position < 0) {
            throw new FS.ErrnoError(28);
          }
          return position;
        },
        allocate(stream, offset, length) {
          MEMFS.expandFileStorage(stream.node, offset + length);
          stream.node.usedBytes = Math.max(
            stream.node.usedBytes,
            offset + length,
          );
        },
        mmap(stream, length, position, prot, flags) {
          if (!FS.isFile(stream.node.mode)) {
            throw new FS.ErrnoError(43);
          }
          var ptr;
          var allocated;
          var contents = stream.node.contents;

          if (!(flags & 2) && contents && contents.buffer === HEAP8.buffer) {
            allocated = false;
            ptr = contents.byteOffset;
          } else {
            allocated = true;
            ptr = mmapAlloc(length);
            if (!ptr) {
              throw new FS.ErrnoError(48);
            }
            if (contents) {
              if (position > 0 || position + length < contents.length) {
                if (contents.subarray) {
                  contents = contents.subarray(position, position + length);
                } else {
                  contents = Array.prototype.slice.call(
                    contents,
                    position,
                    position + length,
                  );
                }
              }
              HEAP8.set(contents, ptr);
            }
          }
          return { ptr, allocated };
        },
        msync(stream, buffer, offset, length, mmapFlags) {
          MEMFS.stream_ops.write(stream, buffer, 0, length, offset, false);

          return 0;
        },
      },
    };

    var asyncLoad = (url, onload, onerror, noRunDep) => {
      var dep = !noRunDep ? getUniqueRunDependency(`al ${url}`) : '';
      readAsync(url).then(
        (arrayBuffer) => {
          onload(new Uint8Array(arrayBuffer));
          if (dep) removeRunDependency(dep);
        },
        (err) => {
          if (onerror) {
            onerror();
          } else {
            throw `Loading data file "${url}" failed.`;
          }
        },
      );
      if (dep) addRunDependency(dep);
    };

    var FS_createDataFile = (
      parent,
      name,
      fileData,
      canRead,
      canWrite,
      canOwn,
    ) => {
      FS.createDataFile(parent, name, fileData, canRead, canWrite, canOwn);
    };

    var preloadPlugins = Module['preloadPlugins'] || [];
    var FS_handledByPreloadPlugin = (byteArray, fullname, finish, onerror) => {
      if (typeof Browser != 'undefined') Browser.init();

      var handled = false;
      preloadPlugins.forEach((plugin) => {
        if (handled) return;
        if (plugin['canHandle'](fullname)) {
          plugin['handle'](byteArray, fullname, finish, onerror);
          handled = true;
        }
      });
      return handled;
    };
    var FS_createPreloadedFile = (
      parent,
      name,
      url,
      canRead,
      canWrite,
      onload,
      onerror,
      dontCreateFile,
      canOwn,
      preFinish,
    ) => {
      var fullname = name ? PATH_FS.resolve(PATH.join2(parent, name)) : parent;
      var dep = getUniqueRunDependency(`cp ${fullname}`);
      function processData(byteArray) {
        function finish(byteArray) {
          preFinish?.();
          if (!dontCreateFile) {
            FS_createDataFile(
              parent,
              name,
              byteArray,
              canRead,
              canWrite,
              canOwn,
            );
          }
          onload?.();
          removeRunDependency(dep);
        }
        if (
          FS_handledByPreloadPlugin(byteArray, fullname, finish, () => {
            onerror?.();
            removeRunDependency(dep);
          })
        ) {
          return;
        }
        finish(byteArray);
      }
      addRunDependency(dep);
      if (typeof url == 'string') {
        asyncLoad(url, processData, onerror);
      } else {
        processData(url);
      }
    };

    var FS_modeStringToFlags = (str) => {
      var flagModes = {
        r: 0,
        'r+': 2,
        w: 512 | 64 | 1,
        'w+': 512 | 64 | 2,
        a: 1024 | 64 | 1,
        'a+': 1024 | 64 | 2,
      };
      var flags = flagModes[str];
      if (typeof flags == 'undefined') {
        throw new Error(`Unknown file open mode: ${str}`);
      }
      return flags;
    };

    var FS_getMode = (canRead, canWrite) => {
      var mode = 0;
      if (canRead) mode |= 292 | 73;
      if (canWrite) mode |= 146;
      return mode;
    };

    var FS = {
      root: null,
      mounts: [],
      devices: {},
      streams: [],
      nextInode: 1,
      nameTable: null,
      currentPath: '/',
      initialized: false,
      ignorePermissions: true,
      ErrnoError: class {
        constructor(errno) {
          this.name = 'ErrnoError';
          this.errno = errno;
        }
      },
      genericErrors: {},
      filesystems: null,
      syncFSRequests: 0,
      readFiles: {},
      FSStream: class {
        constructor() {
          this.shared = {};
        }
        get object() {
          return this.node;
        }
        set object(val) {
          this.node = val;
        }
        get isRead() {
          return (this.flags & 2097155) !== 1;
        }
        get isWrite() {
          return (this.flags & 2097155) !== 0;
        }
        get isAppend() {
          return this.flags & 1024;
        }
        get flags() {
          return this.shared.flags;
        }
        set flags(val) {
          this.shared.flags = val;
        }
        get position() {
          return this.shared.position;
        }
        set position(val) {
          this.shared.position = val;
        }
      },
      FSNode: class {
        constructor(parent, name, mode, rdev) {
          if (!parent) {
            parent = this;
          }
          this.parent = parent;
          this.mount = parent.mount;
          this.mounted = null;
          this.id = FS.nextInode++;
          this.name = name;
          this.mode = mode;
          this.node_ops = {};
          this.stream_ops = {};
          this.rdev = rdev;
          this.readMode = 292 | 73;
          this.writeMode = 146;
        }
        get read() {
          return (this.mode & this.readMode) === this.readMode;
        }
        set read(val) {
          val ? (this.mode |= this.readMode) : (this.mode &= ~this.readMode);
        }
        get write() {
          return (this.mode & this.writeMode) === this.writeMode;
        }
        set write(val) {
          val ? (this.mode |= this.writeMode) : (this.mode &= ~this.writeMode);
        }
        get isFolder() {
          return FS.isDir(this.mode);
        }
        get isDevice() {
          return FS.isChrdev(this.mode);
        }
      },
      lookupPath(path, opts = {}) {
        path = PATH_FS.resolve(path);

        if (!path) return { path: '', node: null };

        var defaults = {
          follow_mount: true,
          recurse_count: 0,
        };
        opts = Object.assign(defaults, opts);

        if (opts.recurse_count > 8) {
          throw new FS.ErrnoError(32);
        }

        var parts = path.split('/').filter((p) => !!p);

        var current = FS.root;
        var current_path = '/';

        for (var i = 0; i < parts.length; i++) {
          var islast = i === parts.length - 1;
          if (islast && opts.parent) {
            break;
          }

          current = FS.lookupNode(current, parts[i]);
          current_path = PATH.join2(current_path, parts[i]);

          if (FS.isMountpoint(current)) {
            if (!islast || (islast && opts.follow_mount)) {
              current = current.mounted.root;
            }
          }

          if (!islast || opts.follow) {
            var count = 0;
            while (FS.isLink(current.mode)) {
              var link = FS.readlink(current_path);
              current_path = PATH_FS.resolve(PATH.dirname(current_path), link);

              var lookup = FS.lookupPath(current_path, {
                recurse_count: opts.recurse_count + 1,
              });
              current = lookup.node;

              if (count++ > 40) {
                throw new FS.ErrnoError(32);
              }
            }
          }
        }

        return { path: current_path, node: current };
      },
      getPath(node) {
        var path;
        while (true) {
          if (FS.isRoot(node)) {
            var mount = node.mount.mountpoint;
            if (!path) return mount;
            return mount[mount.length - 1] !== '/'
              ? `${mount}/${path}`
              : mount + path;
          }
          path = path ? `${node.name}/${path}` : node.name;
          node = node.parent;
        }
      },
      hashName(parentid, name) {
        var hash = 0;

        for (var i = 0; i < name.length; i++) {
          hash = ((hash << 5) - hash + name.charCodeAt(i)) | 0;
        }
        return ((parentid + hash) >>> 0) % FS.nameTable.length;
      },
      hashAddNode(node) {
        var hash = FS.hashName(node.parent.id, node.name);
        node.name_next = FS.nameTable[hash];
        FS.nameTable[hash] = node;
      },
      hashRemoveNode(node) {
        var hash = FS.hashName(node.parent.id, node.name);
        if (FS.nameTable[hash] === node) {
          FS.nameTable[hash] = node.name_next;
        } else {
          var current = FS.nameTable[hash];
          while (current) {
            if (current.name_next === node) {
              current.name_next = node.name_next;
              break;
            }
            current = current.name_next;
          }
        }
      },
      lookupNode(parent, name) {
        var errCode = FS.mayLookup(parent);
        if (errCode) {
          throw new FS.ErrnoError(errCode);
        }
        var hash = FS.hashName(parent.id, name);
        for (var node = FS.nameTable[hash]; node; node = node.name_next) {
          var nodeName = node.name;
          if (node.parent.id === parent.id && nodeName === name) {
            return node;
          }
        }

        return FS.lookup(parent, name);
      },
      createNode(parent, name, mode, rdev) {
        var node = new FS.FSNode(parent, name, mode, rdev);

        FS.hashAddNode(node);

        return node;
      },
      destroyNode(node) {
        FS.hashRemoveNode(node);
      },
      isRoot(node) {
        return node === node.parent;
      },
      isMountpoint(node) {
        return !!node.mounted;
      },
      isFile(mode) {
        return (mode & 61440) === 32768;
      },
      isDir(mode) {
        return (mode & 61440) === 16384;
      },
      isLink(mode) {
        return (mode & 61440) === 40960;
      },
      isChrdev(mode) {
        return (mode & 61440) === 8192;
      },
      isBlkdev(mode) {
        return (mode & 61440) === 24576;
      },
      isFIFO(mode) {
        return (mode & 61440) === 4096;
      },
      isSocket(mode) {
        return (mode & 49152) === 49152;
      },
      flagsToPermissionString(flag) {
        var perms = ['r', 'w', 'rw'][flag & 3];
        if (flag & 512) {
          perms += 'w';
        }
        return perms;
      },
      nodePermissions(node, perms) {
        if (FS.ignorePermissions) {
          return 0;
        }

        if (perms.includes('r') && !(node.mode & 292)) {
          return 2;
        } else if (perms.includes('w') && !(node.mode & 146)) {
          return 2;
        } else if (perms.includes('x') && !(node.mode & 73)) {
          return 2;
        }
        return 0;
      },
      mayLookup(dir) {
        if (!FS.isDir(dir.mode)) return 54;
        var errCode = FS.nodePermissions(dir, 'x');
        if (errCode) return errCode;
        if (!dir.node_ops.lookup) return 2;
        return 0;
      },
      mayCreate(dir, name) {
        try {
          var node = FS.lookupNode(dir, name);
          return 20;
        } catch (e) {}
        return FS.nodePermissions(dir, 'wx');
      },
      mayDelete(dir, name, isdir) {
        var node;
        try {
          node = FS.lookupNode(dir, name);
        } catch (e) {
          return e.errno;
        }
        var errCode = FS.nodePermissions(dir, 'wx');
        if (errCode) {
          return errCode;
        }
        if (isdir) {
          if (!FS.isDir(node.mode)) {
            return 54;
          }
          if (FS.isRoot(node) || FS.getPath(node) === FS.cwd()) {
            return 10;
          }
        } else {
          if (FS.isDir(node.mode)) {
            return 31;
          }
        }
        return 0;
      },
      mayOpen(node, flags) {
        if (!node) {
          return 44;
        }
        if (FS.isLink(node.mode)) {
          return 32;
        } else if (FS.isDir(node.mode)) {
          if (FS.flagsToPermissionString(flags) !== 'r' || flags & 512) {
            return 31;
          }
        }
        return FS.nodePermissions(node, FS.flagsToPermissionString(flags));
      },
      MAX_OPEN_FDS: 4096,
      nextfd() {
        for (var fd = 0; fd <= FS.MAX_OPEN_FDS; fd++) {
          if (!FS.streams[fd]) {
            return fd;
          }
        }
        throw new FS.ErrnoError(33);
      },
      getStreamChecked(fd) {
        var stream = FS.getStream(fd);
        if (!stream) {
          throw new FS.ErrnoError(8);
        }
        return stream;
      },
      getStream: (fd) => FS.streams[fd],
      createStream(stream, fd = -1) {
        stream = Object.assign(new FS.FSStream(), stream);
        if (fd == -1) {
          fd = FS.nextfd();
        }
        stream.fd = fd;
        FS.streams[fd] = stream;
        return stream;
      },
      closeStream(fd) {
        FS.streams[fd] = null;
      },
      dupStream(origStream, fd = -1) {
        var stream = FS.createStream(origStream, fd);
        stream.stream_ops?.dup?.(stream);
        return stream;
      },
      chrdev_stream_ops: {
        open(stream) {
          var device = FS.getDevice(stream.node.rdev);

          stream.stream_ops = device.stream_ops;

          stream.stream_ops.open?.(stream);
        },
        llseek() {
          throw new FS.ErrnoError(70);
        },
      },
      major: (dev) => dev >> 8,
      minor: (dev) => dev & 0xff,
      makedev: (ma, mi) => (ma << 8) | mi,
      registerDevice(dev, ops) {
        FS.devices[dev] = { stream_ops: ops };
      },
      getDevice: (dev) => FS.devices[dev],
      getMounts(mount) {
        var mounts = [];
        var check = [mount];

        while (check.length) {
          var m = check.pop();

          mounts.push(m);

          check.push(...m.mounts);
        }

        return mounts;
      },
      syncfs(populate, callback) {
        if (typeof populate == 'function') {
          callback = populate;
          populate = false;
        }

        FS.syncFSRequests++;

        if (FS.syncFSRequests > 1) {
          err(
            `warning: ${FS.syncFSRequests} FS.syncfs operations in flight at once, probably just doing extra work`,
          );
        }

        var mounts = FS.getMounts(FS.root.mount);
        var completed = 0;

        function doCallback(errCode) {
          FS.syncFSRequests--;
          return callback(errCode);
        }

        function done(errCode) {
          if (errCode) {
            if (!done.errored) {
              done.errored = true;
              return doCallback(errCode);
            }
            return;
          }
          if (++completed >= mounts.length) {
            doCallback(null);
          }
        }

        mounts.forEach((mount) => {
          if (!mount.type.syncfs) {
            return done(null);
          }
          mount.type.syncfs(mount, populate, done);
        });
      },
      mount(type, opts, mountpoint) {
        var root = mountpoint === '/';
        var pseudo = !mountpoint;
        var node;

        if (root && FS.root) {
          throw new FS.ErrnoError(10);
        } else if (!root && !pseudo) {
          var lookup = FS.lookupPath(mountpoint, { follow_mount: false });

          mountpoint = lookup.path;
          node = lookup.node;

          if (FS.isMountpoint(node)) {
            throw new FS.ErrnoError(10);
          }

          if (!FS.isDir(node.mode)) {
            throw new FS.ErrnoError(54);
          }
        }

        var mount = {
          type,
          opts,
          mountpoint,
          mounts: [],
        };

        var mountRoot = type.mount(mount);
        mountRoot.mount = mount;
        mount.root = mountRoot;

        if (root) {
          FS.root = mountRoot;
        } else if (node) {
          node.mounted = mount;

          if (node.mount) {
            node.mount.mounts.push(mount);
          }
        }

        return mountRoot;
      },
      unmount(mountpoint) {
        var lookup = FS.lookupPath(mountpoint, { follow_mount: false });

        if (!FS.isMountpoint(lookup.node)) {
          throw new FS.ErrnoError(28);
        }

        var node = lookup.node;
        var mount = node.mounted;
        var mounts = FS.getMounts(mount);

        Object.keys(FS.nameTable).forEach((hash) => {
          var current = FS.nameTable[hash];

          while (current) {
            var next = current.name_next;

            if (mounts.includes(current.mount)) {
              FS.destroyNode(current);
            }

            current = next;
          }
        });

        node.mounted = null;

        var idx = node.mount.mounts.indexOf(mount);
        node.mount.mounts.splice(idx, 1);
      },
      lookup(parent, name) {
        return parent.node_ops.lookup(parent, name);
      },
      mknod(path, mode, dev) {
        var lookup = FS.lookupPath(path, { parent: true });
        var parent = lookup.node;
        var name = PATH.basename(path);
        if (!name || name === '.' || name === '..') {
          throw new FS.ErrnoError(28);
        }
        var errCode = FS.mayCreate(parent, name);
        if (errCode) {
          throw new FS.ErrnoError(errCode);
        }
        if (!parent.node_ops.mknod) {
          throw new FS.ErrnoError(63);
        }
        return parent.node_ops.mknod(parent, name, mode, dev);
      },
      create(path, mode) {
        mode = mode !== undefined ? mode : 438;
        mode &= 4095;
        mode |= 32768;
        return FS.mknod(path, mode, 0);
      },
      mkdir(path, mode) {
        mode = mode !== undefined ? mode : 511;
        mode &= 511 | 512;
        mode |= 16384;
        return FS.mknod(path, mode, 0);
      },
      mkdirTree(path, mode) {
        var dirs = path.split('/');
        var d = '';
        for (var i = 0; i < dirs.length; ++i) {
          if (!dirs[i]) continue;
          d += '/' + dirs[i];
          try {
            FS.mkdir(d, mode);
          } catch (e) {
            if (e.errno != 20) throw e;
          }
        }
      },
      mkdev(path, mode, dev) {
        if (typeof dev == 'undefined') {
          dev = mode;
          mode = 438;
        }
        mode |= 8192;
        return FS.mknod(path, mode, dev);
      },
      symlink(oldpath, newpath) {
        if (!PATH_FS.resolve(oldpath)) {
          throw new FS.ErrnoError(44);
        }
        var lookup = FS.lookupPath(newpath, { parent: true });
        var parent = lookup.node;
        if (!parent) {
          throw new FS.ErrnoError(44);
        }
        var newname = PATH.basename(newpath);
        var errCode = FS.mayCreate(parent, newname);
        if (errCode) {
          throw new FS.ErrnoError(errCode);
        }
        if (!parent.node_ops.symlink) {
          throw new FS.ErrnoError(63);
        }
        return parent.node_ops.symlink(parent, newname, oldpath);
      },
      rename(old_path, new_path) {
        var old_dirname = PATH.dirname(old_path);
        var new_dirname = PATH.dirname(new_path);
        var old_name = PATH.basename(old_path);
        var new_name = PATH.basename(new_path);

        var lookup, old_dir, new_dir;

        lookup = FS.lookupPath(old_path, { parent: true });
        old_dir = lookup.node;
        lookup = FS.lookupPath(new_path, { parent: true });
        new_dir = lookup.node;

        if (!old_dir || !new_dir) throw new FS.ErrnoError(44);

        if (old_dir.mount !== new_dir.mount) {
          throw new FS.ErrnoError(75);
        }

        var old_node = FS.lookupNode(old_dir, old_name);

        var relative = PATH_FS.relative(old_path, new_dirname);
        if (relative.charAt(0) !== '.') {
          throw new FS.ErrnoError(28);
        }

        relative = PATH_FS.relative(new_path, old_dirname);
        if (relative.charAt(0) !== '.') {
          throw new FS.ErrnoError(55);
        }

        var new_node;
        try {
          new_node = FS.lookupNode(new_dir, new_name);
        } catch (e) {}

        if (old_node === new_node) {
          return;
        }

        var isdir = FS.isDir(old_node.mode);
        var errCode = FS.mayDelete(old_dir, old_name, isdir);
        if (errCode) {
          throw new FS.ErrnoError(errCode);
        }

        errCode = new_node
          ? FS.mayDelete(new_dir, new_name, isdir)
          : FS.mayCreate(new_dir, new_name);
        if (errCode) {
          throw new FS.ErrnoError(errCode);
        }
        if (!old_dir.node_ops.rename) {
          throw new FS.ErrnoError(63);
        }
        if (
          FS.isMountpoint(old_node) ||
          (new_node && FS.isMountpoint(new_node))
        ) {
          throw new FS.ErrnoError(10);
        }

        if (new_dir !== old_dir) {
          errCode = FS.nodePermissions(old_dir, 'w');
          if (errCode) {
            throw new FS.ErrnoError(errCode);
          }
        }

        FS.hashRemoveNode(old_node);

        try {
          old_dir.node_ops.rename(old_node, new_dir, new_name);

          old_node.parent = new_dir;
        } catch (e) {
          throw e;
        } finally {
          FS.hashAddNode(old_node);
        }
      },
      rmdir(path) {
        var lookup = FS.lookupPath(path, { parent: true });
        var parent = lookup.node;
        var name = PATH.basename(path);
        var node = FS.lookupNode(parent, name);
        var errCode = FS.mayDelete(parent, name, true);
        if (errCode) {
          throw new FS.ErrnoError(errCode);
        }
        if (!parent.node_ops.rmdir) {
          throw new FS.ErrnoError(63);
        }
        if (FS.isMountpoint(node)) {
          throw new FS.ErrnoError(10);
        }
        parent.node_ops.rmdir(parent, name);
        FS.destroyNode(node);
      },
      readdir(path) {
        var lookup = FS.lookupPath(path, { follow: true });
        var node = lookup.node;
        if (!node.node_ops.readdir) {
          throw new FS.ErrnoError(54);
        }
        return node.node_ops.readdir(node);
      },
      unlink(path) {
        var lookup = FS.lookupPath(path, { parent: true });
        var parent = lookup.node;
        if (!parent) {
          throw new FS.ErrnoError(44);
        }
        var name = PATH.basename(path);
        var node = FS.lookupNode(parent, name);
        var errCode = FS.mayDelete(parent, name, false);
        if (errCode) {
          throw new FS.ErrnoError(errCode);
        }
        if (!parent.node_ops.unlink) {
          throw new FS.ErrnoError(63);
        }
        if (FS.isMountpoint(node)) {
          throw new FS.ErrnoError(10);
        }
        parent.node_ops.unlink(parent, name);
        FS.destroyNode(node);
      },
      readlink(path) {
        var lookup = FS.lookupPath(path);
        var link = lookup.node;
        if (!link) {
          throw new FS.ErrnoError(44);
        }
        if (!link.node_ops.readlink) {
          throw new FS.ErrnoError(28);
        }
        return PATH_FS.resolve(
          FS.getPath(link.parent),
          link.node_ops.readlink(link),
        );
      },
      stat(path, dontFollow) {
        var lookup = FS.lookupPath(path, { follow: !dontFollow });
        var node = lookup.node;
        if (!node) {
          throw new FS.ErrnoError(44);
        }
        if (!node.node_ops.getattr) {
          throw new FS.ErrnoError(63);
        }
        return node.node_ops.getattr(node);
      },
      lstat(path) {
        return FS.stat(path, true);
      },
      chmod(path, mode, dontFollow) {
        var node;
        if (typeof path == 'string') {
          var lookup = FS.lookupPath(path, { follow: !dontFollow });
          node = lookup.node;
        } else {
          node = path;
        }
        if (!node.node_ops.setattr) {
          throw new FS.ErrnoError(63);
        }
        node.node_ops.setattr(node, {
          mode: (mode & 4095) | (node.mode & ~4095),
          timestamp: Date.now(),
        });
      },
      lchmod(path, mode) {
        FS.chmod(path, mode, true);
      },
      fchmod(fd, mode) {
        var stream = FS.getStreamChecked(fd);
        FS.chmod(stream.node, mode);
      },
      chown(path, uid, gid, dontFollow) {
        var node;
        if (typeof path == 'string') {
          var lookup = FS.lookupPath(path, { follow: !dontFollow });
          node = lookup.node;
        } else {
          node = path;
        }
        if (!node.node_ops.setattr) {
          throw new FS.ErrnoError(63);
        }
        node.node_ops.setattr(node, {
          timestamp: Date.now(),
        });
      },
      lchown(path, uid, gid) {
        FS.chown(path, uid, gid, true);
      },
      fchown(fd, uid, gid) {
        var stream = FS.getStreamChecked(fd);
        FS.chown(stream.node, uid, gid);
      },
      truncate(path, len) {
        if (len < 0) {
          throw new FS.ErrnoError(28);
        }
        var node;
        if (typeof path == 'string') {
          var lookup = FS.lookupPath(path, { follow: true });
          node = lookup.node;
        } else {
          node = path;
        }
        if (!node.node_ops.setattr) {
          throw new FS.ErrnoError(63);
        }
        if (FS.isDir(node.mode)) {
          throw new FS.ErrnoError(31);
        }
        if (!FS.isFile(node.mode)) {
          throw new FS.ErrnoError(28);
        }
        var errCode = FS.nodePermissions(node, 'w');
        if (errCode) {
          throw new FS.ErrnoError(errCode);
        }
        node.node_ops.setattr(node, {
          size: len,
          timestamp: Date.now(),
        });
      },
      ftruncate(fd, len) {
        var stream = FS.getStreamChecked(fd);
        if ((stream.flags & 2097155) === 0) {
          throw new FS.ErrnoError(28);
        }
        FS.truncate(stream.node, len);
      },
      utime(path, atime, mtime) {
        var lookup = FS.lookupPath(path, { follow: true });
        var node = lookup.node;
        node.node_ops.setattr(node, {
          timestamp: Math.max(atime, mtime),
        });
      },
      open(path, flags, mode) {
        if (path === '') {
          throw new FS.ErrnoError(44);
        }
        flags = typeof flags == 'string' ? FS_modeStringToFlags(flags) : flags;
        if (flags & 64) {
          mode = typeof mode == 'undefined' ? 438 : mode;
          mode = (mode & 4095) | 32768;
        } else {
          mode = 0;
        }
        var node;
        if (typeof path == 'object') {
          node = path;
        } else {
          path = PATH.normalize(path);
          try {
            var lookup = FS.lookupPath(path, {
              follow: !(flags & 131072),
            });
            node = lookup.node;
          } catch (e) {}
        }

        var created = false;
        if (flags & 64) {
          if (node) {
            if (flags & 128) {
              throw new FS.ErrnoError(20);
            }
          } else {
            node = FS.mknod(path, mode, 0);
            created = true;
          }
        }
        if (!node) {
          throw new FS.ErrnoError(44);
        }

        if (FS.isChrdev(node.mode)) {
          flags &= ~512;
        }

        if (flags & 65536 && !FS.isDir(node.mode)) {
          throw new FS.ErrnoError(54);
        }

        if (!created) {
          var errCode = FS.mayOpen(node, flags);
          if (errCode) {
            throw new FS.ErrnoError(errCode);
          }
        }

        if (flags & 512 && !created) {
          FS.truncate(node, 0);
        }

        flags &= ~(128 | 512 | 131072);

        var stream = FS.createStream({
          node,
          path: FS.getPath(node),
          flags,
          seekable: true,
          position: 0,
          stream_ops: node.stream_ops,

          ungotten: [],
          error: false,
        });

        if (stream.stream_ops.open) {
          stream.stream_ops.open(stream);
        }
        if (Module['logReadFiles'] && !(flags & 1)) {
          if (!(path in FS.readFiles)) {
            FS.readFiles[path] = 1;
          }
        }
        return stream;
      },
      close(stream) {
        if (FS.isClosed(stream)) {
          throw new FS.ErrnoError(8);
        }
        if (stream.getdents) stream.getdents = null;
        try {
          if (stream.stream_ops.close) {
            stream.stream_ops.close(stream);
          }
        } catch (e) {
          throw e;
        } finally {
          FS.closeStream(stream.fd);
        }
        stream.fd = null;
      },
      isClosed(stream) {
        return stream.fd === null;
      },
      llseek(stream, offset, whence) {
        if (FS.isClosed(stream)) {
          throw new FS.ErrnoError(8);
        }
        if (!stream.seekable || !stream.stream_ops.llseek) {
          throw new FS.ErrnoError(70);
        }
        if (whence != 0 && whence != 1 && whence != 2) {
          throw new FS.ErrnoError(28);
        }
        stream.position = stream.stream_ops.llseek(stream, offset, whence);
        stream.ungotten = [];
        return stream.position;
      },
      read(stream, buffer, offset, length, position) {
        if (length < 0 || position < 0) {
          throw new FS.ErrnoError(28);
        }
        if (FS.isClosed(stream)) {
          throw new FS.ErrnoError(8);
        }
        if ((stream.flags & 2097155) === 1) {
          throw new FS.ErrnoError(8);
        }
        if (FS.isDir(stream.node.mode)) {
          throw new FS.ErrnoError(31);
        }
        if (!stream.stream_ops.read) {
          throw new FS.ErrnoError(28);
        }
        var seeking = typeof position != 'undefined';
        if (!seeking) {
          position = stream.position;
        } else if (!stream.seekable) {
          throw new FS.ErrnoError(70);
        }
        var bytesRead = stream.stream_ops.read(
          stream,
          buffer,
          offset,
          length,
          position,
        );
        if (!seeking) stream.position += bytesRead;
        return bytesRead;
      },
      write(stream, buffer, offset, length, position, canOwn) {
        if (length < 0 || position < 0) {
          throw new FS.ErrnoError(28);
        }
        if (FS.isClosed(stream)) {
          throw new FS.ErrnoError(8);
        }
        if ((stream.flags & 2097155) === 0) {
          throw new FS.ErrnoError(8);
        }
        if (FS.isDir(stream.node.mode)) {
          throw new FS.ErrnoError(31);
        }
        if (!stream.stream_ops.write) {
          throw new FS.ErrnoError(28);
        }
        if (stream.seekable && stream.flags & 1024) {
          FS.llseek(stream, 0, 2);
        }
        var seeking = typeof position != 'undefined';
        if (!seeking) {
          position = stream.position;
        } else if (!stream.seekable) {
          throw new FS.ErrnoError(70);
        }
        var bytesWritten = stream.stream_ops.write(
          stream,
          buffer,
          offset,
          length,
          position,
          canOwn,
        );
        if (!seeking) stream.position += bytesWritten;
        return bytesWritten;
      },
      allocate(stream, offset, length) {
        if (FS.isClosed(stream)) {
          throw new FS.ErrnoError(8);
        }
        if (offset < 0 || length <= 0) {
          throw new FS.ErrnoError(28);
        }
        if ((stream.flags & 2097155) === 0) {
          throw new FS.ErrnoError(8);
        }
        if (!FS.isFile(stream.node.mode) && !FS.isDir(stream.node.mode)) {
          throw new FS.ErrnoError(43);
        }
        if (!stream.stream_ops.allocate) {
          throw new FS.ErrnoError(138);
        }
        stream.stream_ops.allocate(stream, offset, length);
      },
      mmap(stream, length, position, prot, flags) {
        if (
          (prot & 2) !== 0 &&
          (flags & 2) === 0 &&
          (stream.flags & 2097155) !== 2
        ) {
          throw new FS.ErrnoError(2);
        }
        if ((stream.flags & 2097155) === 1) {
          throw new FS.ErrnoError(2);
        }
        if (!stream.stream_ops.mmap) {
          throw new FS.ErrnoError(43);
        }
        if (!length) {
          throw new FS.ErrnoError(28);
        }
        return stream.stream_ops.mmap(stream, length, position, prot, flags);
      },
      msync(stream, buffer, offset, length, mmapFlags) {
        if (!stream.stream_ops.msync) {
          return 0;
        }
        return stream.stream_ops.msync(
          stream,
          buffer,
          offset,
          length,
          mmapFlags,
        );
      },
      ioctl(stream, cmd, arg) {
        if (!stream.stream_ops.ioctl) {
          throw new FS.ErrnoError(59);
        }
        return stream.stream_ops.ioctl(stream, cmd, arg);
      },
      readFile(path, opts = {}) {
        opts.flags = opts.flags || 0;
        opts.encoding = opts.encoding || 'binary';
        if (opts.encoding !== 'utf8' && opts.encoding !== 'binary') {
          throw new Error(`Invalid encoding type "${opts.encoding}"`);
        }
        var ret;
        var stream = FS.open(path, opts.flags);
        var stat = FS.stat(path);
        var length = stat.size;
        var buf = new Uint8Array(length);
        FS.read(stream, buf, 0, length, 0);
        if (opts.encoding === 'utf8') {
          ret = UTF8ArrayToString(buf);
        } else if (opts.encoding === 'binary') {
          ret = buf;
        }
        FS.close(stream);
        return ret;
      },
      writeFile(path, data, opts = {}) {
        opts.flags = opts.flags || 577;
        var stream = FS.open(path, opts.flags, opts.mode);
        if (typeof data == 'string') {
          var buf = new Uint8Array(lengthBytesUTF8(data) + 1);
          var actualNumBytes = stringToUTF8Array(data, buf, 0, buf.length);
          FS.write(stream, buf, 0, actualNumBytes, undefined, opts.canOwn);
        } else if (ArrayBuffer.isView(data)) {
          FS.write(stream, data, 0, data.byteLength, undefined, opts.canOwn);
        } else {
          throw new Error('Unsupported data type');
        }
        FS.close(stream);
      },
      cwd: () => FS.currentPath,
      chdir(path) {
        var lookup = FS.lookupPath(path, { follow: true });
        if (lookup.node === null) {
          throw new FS.ErrnoError(44);
        }
        if (!FS.isDir(lookup.node.mode)) {
          throw new FS.ErrnoError(54);
        }
        var errCode = FS.nodePermissions(lookup.node, 'x');
        if (errCode) {
          throw new FS.ErrnoError(errCode);
        }
        FS.currentPath = lookup.path;
      },
      createDefaultDirectories() {
        FS.mkdir('/tmp');
        FS.mkdir('/home');
        FS.mkdir('/home/web_user');
      },
      createDefaultDevices() {
        FS.mkdir('/dev');

        FS.registerDevice(FS.makedev(1, 3), {
          read: () => 0,
          write: (stream, buffer, offset, length, pos) => length,
        });
        FS.mkdev('/dev/null', FS.makedev(1, 3));

        TTY.register(FS.makedev(5, 0), TTY.default_tty_ops);
        TTY.register(FS.makedev(6, 0), TTY.default_tty1_ops);
        FS.mkdev('/dev/tty', FS.makedev(5, 0));
        FS.mkdev('/dev/tty1', FS.makedev(6, 0));

        var randomBuffer = new Uint8Array(1024),
          randomLeft = 0;
        var randomByte = () => {
          if (randomLeft === 0) {
            randomLeft = randomFill(randomBuffer).byteLength;
          }
          return randomBuffer[--randomLeft];
        };
        FS.createDevice('/dev', 'random', randomByte);
        FS.createDevice('/dev', 'urandom', randomByte);

        FS.mkdir('/dev/shm');
        FS.mkdir('/dev/shm/tmp');
      },
      createSpecialDirectories() {
        FS.mkdir('/proc');
        var proc_self = FS.mkdir('/proc/self');
        FS.mkdir('/proc/self/fd');
        FS.mount(
          {
            mount() {
              var node = FS.createNode(proc_self, 'fd', 16384 | 511, 73);
              node.node_ops = {
                lookup(parent, name) {
                  var fd = +name;
                  var stream = FS.getStreamChecked(fd);
                  var ret = {
                    parent: null,
                    mount: { mountpoint: 'fake' },
                    node_ops: { readlink: () => stream.path },
                  };
                  ret.parent = ret;
                  return ret;
                },
              };
              return node;
            },
          },
          {},
          '/proc/self/fd',
        );
      },
      createStandardStreams(input, output, error) {
        if (input) {
          FS.createDevice('/dev', 'stdin', input);
        } else {
          FS.symlink('/dev/tty', '/dev/stdin');
        }
        if (output) {
          FS.createDevice('/dev', 'stdout', null, output);
        } else {
          FS.symlink('/dev/tty', '/dev/stdout');
        }
        if (error) {
          FS.createDevice('/dev', 'stderr', null, error);
        } else {
          FS.symlink('/dev/tty1', '/dev/stderr');
        }

        var stdin = FS.open('/dev/stdin', 0);
        var stdout = FS.open('/dev/stdout', 1);
        var stderr = FS.open('/dev/stderr', 1);
      },
      staticInit() {
        [44].forEach((code) => {
          FS.genericErrors[code] = new FS.ErrnoError(code);
          FS.genericErrors[code].stack = '<generic error, no stack>';
        });

        FS.nameTable = new Array(4096);

        FS.mount(MEMFS, {}, '/');

        FS.createDefaultDirectories();
        FS.createDefaultDevices();
        FS.createSpecialDirectories();

        FS.filesystems = {
          MEMFS: MEMFS,
        };
      },
      init(input, output, error) {
        FS.initialized = true;

        input ??= Module['stdin'];
        output ??= Module['stdout'];
        error ??= Module['stderr'];

        FS.createStandardStreams(input, output, error);
      },
      quit() {
        FS.initialized = false;

        for (var i = 0; i < FS.streams.length; i++) {
          var stream = FS.streams[i];
          if (!stream) {
            continue;
          }
          FS.close(stream);
        }
      },
      findObject(path, dontResolveLastLink) {
        var ret = FS.analyzePath(path, dontResolveLastLink);
        if (!ret.exists) {
          return null;
        }
        return ret.object;
      },
      analyzePath(path, dontResolveLastLink) {
        try {
          var lookup = FS.lookupPath(path, { follow: !dontResolveLastLink });
          path = lookup.path;
        } catch (e) {}
        var ret = {
          isRoot: false,
          exists: false,
          error: 0,
          name: null,
          path: null,
          object: null,
          parentExists: false,
          parentPath: null,
          parentObject: null,
        };
        try {
          var lookup = FS.lookupPath(path, { parent: true });
          ret.parentExists = true;
          ret.parentPath = lookup.path;
          ret.parentObject = lookup.node;
          ret.name = PATH.basename(path);
          lookup = FS.lookupPath(path, { follow: !dontResolveLastLink });
          ret.exists = true;
          ret.path = lookup.path;
          ret.object = lookup.node;
          ret.name = lookup.node.name;
          ret.isRoot = lookup.path === '/';
        } catch (e) {
          ret.error = e.errno;
        }
        return ret;
      },
      createPath(parent, path, canRead, canWrite) {
        parent = typeof parent == 'string' ? parent : FS.getPath(parent);
        var parts = path.split('/').reverse();
        while (parts.length) {
          var part = parts.pop();
          if (!part) continue;
          var current = PATH.join2(parent, part);
          try {
            FS.mkdir(current);
          } catch (e) {}
          parent = current;
        }
        return current;
      },
      createFile(parent, name, properties, canRead, canWrite) {
        var path = PATH.join2(
          typeof parent == 'string' ? parent : FS.getPath(parent),
          name,
        );
        var mode = FS_getMode(canRead, canWrite);
        return FS.create(path, mode);
      },
      createDataFile(parent, name, data, canRead, canWrite, canOwn) {
        var path = name;
        if (parent) {
          parent = typeof parent == 'string' ? parent : FS.getPath(parent);
          path = name ? PATH.join2(parent, name) : parent;
        }
        var mode = FS_getMode(canRead, canWrite);
        var node = FS.create(path, mode);
        if (data) {
          if (typeof data == 'string') {
            var arr = new Array(data.length);
            for (var i = 0, len = data.length; i < len; ++i)
              arr[i] = data.charCodeAt(i);
            data = arr;
          }

          FS.chmod(node, mode | 146);
          var stream = FS.open(node, 577);
          FS.write(stream, data, 0, data.length, 0, canOwn);
          FS.close(stream);
          FS.chmod(node, mode);
        }
      },
      createDevice(parent, name, input, output) {
        var path = PATH.join2(
          typeof parent == 'string' ? parent : FS.getPath(parent),
          name,
        );
        var mode = FS_getMode(!!input, !!output);
        FS.createDevice.major ??= 64;
        var dev = FS.makedev(FS.createDevice.major++, 0);

        FS.registerDevice(dev, {
          open(stream) {
            stream.seekable = false;
          },
          close(stream) {
            if (output?.buffer?.length) {
              output(10);
            }
          },
          read(stream, buffer, offset, length, pos) {
            var bytesRead = 0;
            for (var i = 0; i < length; i++) {
              var result;
              try {
                result = input();
              } catch (e) {
                throw new FS.ErrnoError(29);
              }
              if (result === undefined && bytesRead === 0) {
                throw new FS.ErrnoError(6);
              }
              if (result === null || result === undefined) break;
              bytesRead++;
              buffer[offset + i] = result;
            }
            if (bytesRead) {
              stream.node.timestamp = Date.now();
            }
            return bytesRead;
          },
          write(stream, buffer, offset, length, pos) {
            for (var i = 0; i < length; i++) {
              try {
                output(buffer[offset + i]);
              } catch (e) {
                throw new FS.ErrnoError(29);
              }
            }
            if (length) {
              stream.node.timestamp = Date.now();
            }
            return i;
          },
        });
        return FS.mkdev(path, mode, dev);
      },
      forceLoadFile(obj) {
        if (obj.isDevice || obj.isFolder || obj.link || obj.contents)
          return true;
        if (typeof XMLHttpRequest != 'undefined') {
          throw new Error(
            'Lazy loading should have been performed (contents set) in createLazyFile, but it was not. Lazy loading only works in web workers. Use --embed-file or --preload-file in emcc on the main thread.',
          );
        } else {
          try {
            obj.contents = readBinary(obj.url);
            obj.usedBytes = obj.contents.length;
          } catch (e) {
            throw new FS.ErrnoError(29);
          }
        }
      },
      createLazyFile(parent, name, url, canRead, canWrite) {
        class LazyUint8Array {
          constructor() {
            this.lengthKnown = false;
            this.chunks = [];
          }
          get(idx) {
            if (idx > this.length - 1 || idx < 0) {
              return undefined;
            }
            var chunkOffset = idx % this.chunkSize;
            var chunkNum = (idx / this.chunkSize) | 0;
            return this.getter(chunkNum)[chunkOffset];
          }
          setDataGetter(getter) {
            this.getter = getter;
          }
          cacheLength() {
            var xhr = new XMLHttpRequest();
            xhr.open('HEAD', url, false);
            xhr.send(null);
            if (
              !((xhr.status >= 200 && xhr.status < 300) || xhr.status === 304)
            )
              throw new Error(
                "Couldn't load " + url + '. Status: ' + xhr.status,
              );
            var datalength = Number(xhr.getResponseHeader('Content-length'));
            var header;
            var hasByteServing =
              (header = xhr.getResponseHeader('Accept-Ranges')) &&
              header === 'bytes';
            var usesGzip =
              (header = xhr.getResponseHeader('Content-Encoding')) &&
              header === 'gzip';

            var chunkSize = 1024 * 1024;

            if (!hasByteServing) chunkSize = datalength;

            var doXHR = (from, to) => {
              if (from > to)
                throw new Error(
                  'invalid range (' +
                    from +
                    ', ' +
                    to +
                    ') or no bytes requested!',
                );
              if (to > datalength - 1)
                throw new Error(
                  'only ' + datalength + ' bytes available! programmer error!',
                );

              var xhr = new XMLHttpRequest();
              xhr.open('GET', url, false);
              if (datalength !== chunkSize)
                xhr.setRequestHeader('Range', 'bytes=' + from + '-' + to);

              xhr.responseType = 'arraybuffer';
              if (xhr.overrideMimeType) {
                xhr.overrideMimeType('text/plain; charset=x-user-defined');
              }

              xhr.send(null);
              if (
                !((xhr.status >= 200 && xhr.status < 300) || xhr.status === 304)
              )
                throw new Error(
                  "Couldn't load " + url + '. Status: ' + xhr.status,
                );
              if (xhr.response !== undefined) {
                return new Uint8Array(xhr.response || []);
              }
              return intArrayFromString(xhr.responseText || '', true);
            };
            var lazyArray = this;
            lazyArray.setDataGetter((chunkNum) => {
              var start = chunkNum * chunkSize;
              var end = (chunkNum + 1) * chunkSize - 1;
              end = Math.min(end, datalength - 1);
              if (typeof lazyArray.chunks[chunkNum] == 'undefined') {
                lazyArray.chunks[chunkNum] = doXHR(start, end);
              }
              if (typeof lazyArray.chunks[chunkNum] == 'undefined')
                throw new Error('doXHR failed!');
              return lazyArray.chunks[chunkNum];
            });

            if (usesGzip || !datalength) {
              chunkSize = datalength = 1;
              datalength = this.getter(0).length;
              chunkSize = datalength;
              out(
                'LazyFiles on gzip forces download of the whole file when length is accessed',
              );
            }

            this._length = datalength;
            this._chunkSize = chunkSize;
            this.lengthKnown = true;
          }
          get length() {
            if (!this.lengthKnown) {
              this.cacheLength();
            }
            return this._length;
          }
          get chunkSize() {
            if (!this.lengthKnown) {
              this.cacheLength();
            }
            return this._chunkSize;
          }
        }

        if (typeof XMLHttpRequest != 'undefined') {
          if (!ENVIRONMENT_IS_WORKER)
            throw 'Cannot do synchronous binary XHRs outside webworkers in modern browsers. Use --embed-file or --preload-file in emcc';
          var lazyArray = new LazyUint8Array();
          var properties = { isDevice: false, contents: lazyArray };
        } else {
          var properties = { isDevice: false, url: url };
        }

        var node = FS.createFile(parent, name, properties, canRead, canWrite);

        if (properties.contents) {
          node.contents = properties.contents;
        } else if (properties.url) {
          node.contents = null;
          node.url = properties.url;
        }

        Object.defineProperties(node, {
          usedBytes: {
            get: function () {
              return this.contents.length;
            },
          },
        });

        var stream_ops = {};
        var keys = Object.keys(node.stream_ops);
        keys.forEach((key) => {
          var fn = node.stream_ops[key];
          stream_ops[key] = (...args) => {
            FS.forceLoadFile(node);
            return fn(...args);
          };
        });
        function writeChunks(stream, buffer, offset, length, position) {
          var contents = stream.node.contents;
          if (position >= contents.length) return 0;
          var size = Math.min(contents.length - position, length);
          if (contents.slice) {
            for (var i = 0; i < size; i++) {
              buffer[offset + i] = contents[position + i];
            }
          } else {
            for (var i = 0; i < size; i++) {
              buffer[offset + i] = contents.get(position + i);
            }
          }
          return size;
        }

        stream_ops.read = (stream, buffer, offset, length, position) => {
          FS.forceLoadFile(node);
          return writeChunks(stream, buffer, offset, length, position);
        };

        stream_ops.mmap = (stream, length, position, prot, flags) => {
          FS.forceLoadFile(node);
          var ptr = mmapAlloc(length);
          if (!ptr) {
            throw new FS.ErrnoError(48);
          }
          writeChunks(stream, HEAP8, ptr, length, position);
          return { ptr, allocated: true };
        };
        node.stream_ops = stream_ops;
        return node;
      },
    };

    var UTF8ToString = (ptr, maxBytesToRead) => {
      return ptr ? UTF8ArrayToString(HEAPU8, ptr, maxBytesToRead) : '';
    };
    var SYSCALLS = {
      DEFAULT_POLLMASK: 5,
      calculateAt(dirfd, path, allowEmpty) {
        if (PATH.isAbs(path)) {
          return path;
        }

        var dir;
        if (dirfd === -100) {
          dir = FS.cwd();
        } else {
          var dirstream = SYSCALLS.getStreamFromFD(dirfd);
          dir = dirstream.path;
        }
        if (path.length == 0) {
          if (!allowEmpty) {
            throw new FS.ErrnoError(44);
          }
          return dir;
        }
        return PATH.join2(dir, path);
      },
      doStat(func, path, buf) {
        var stat = func(path);
        HEAP32[buf >> 2] = stat.dev;
        HEAP32[(buf + 4) >> 2] = stat.mode;
        HEAPU32[(buf + 8) >> 2] = stat.nlink;
        HEAP32[(buf + 12) >> 2] = stat.uid;
        HEAP32[(buf + 16) >> 2] = stat.gid;
        HEAP32[(buf + 20) >> 2] = stat.rdev;
        HEAP64[(buf + 24) >> 3] = BigInt(stat.size);
        HEAP32[(buf + 32) >> 2] = 4096;
        HEAP32[(buf + 36) >> 2] = stat.blocks;
        var atime = stat.atime.getTime();
        var mtime = stat.mtime.getTime();
        var ctime = stat.ctime.getTime();
        HEAP64[(buf + 40) >> 3] = BigInt(Math.floor(atime / 1000));
        HEAPU32[(buf + 48) >> 2] = (atime % 1000) * 1000 * 1000;
        HEAP64[(buf + 56) >> 3] = BigInt(Math.floor(mtime / 1000));
        HEAPU32[(buf + 64) >> 2] = (mtime % 1000) * 1000 * 1000;
        HEAP64[(buf + 72) >> 3] = BigInt(Math.floor(ctime / 1000));
        HEAPU32[(buf + 80) >> 2] = (ctime % 1000) * 1000 * 1000;
        HEAP64[(buf + 88) >> 3] = BigInt(stat.ino);
        return 0;
      },
      doMsync(addr, stream, len, flags, offset) {
        if (!FS.isFile(stream.node.mode)) {
          throw new FS.ErrnoError(43);
        }
        if (flags & 2) {
          return 0;
        }
        var buffer = HEAPU8.slice(addr, addr + len);
        FS.msync(stream, buffer, offset, len, flags);
      },
      getStreamFromFD(fd) {
        var stream = FS.getStreamChecked(fd);
        return stream;
      },
      varargs: undefined,
      getStr(ptr) {
        var ret = UTF8ToString(ptr);
        return ret;
      },
    };
    function ___syscall_chmod(path, mode) {
      try {
        path = SYSCALLS.getStr(path);
        FS.chmod(path, mode);
        return 0;
      } catch (e) {
        if (typeof FS == 'undefined' || !(e.name === 'ErrnoError')) throw e;
        return -e.errno;
      }
    }

    function ___syscall_faccessat(dirfd, path, amode, flags) {
      try {
        path = SYSCALLS.getStr(path);
        path = SYSCALLS.calculateAt(dirfd, path);
        if (amode & ~7) {
          return -28;
        }
        var lookup = FS.lookupPath(path, { follow: true });
        var node = lookup.node;
        if (!node) {
          return -44;
        }
        var perms = '';
        if (amode & 4) perms += 'r';
        if (amode & 2) perms += 'w';
        if (amode & 1) perms += 'x';
        if (perms && FS.nodePermissions(node, perms)) {
          return -2;
        }
        return 0;
      } catch (e) {
        if (typeof FS == 'undefined' || !(e.name === 'ErrnoError')) throw e;
        return -e.errno;
      }
    }

    function ___syscall_fchmod(fd, mode) {
      try {
        FS.fchmod(fd, mode);
        return 0;
      } catch (e) {
        if (typeof FS == 'undefined' || !(e.name === 'ErrnoError')) throw e;
        return -e.errno;
      }
    }

    function ___syscall_fchown32(fd, owner, group) {
      try {
        FS.fchown(fd, owner, group);
        return 0;
      } catch (e) {
        if (typeof FS == 'undefined' || !(e.name === 'ErrnoError')) throw e;
        return -e.errno;
      }
    }

    function syscallGetVarargI() {
      var ret = HEAP32[+SYSCALLS.varargs >> 2];
      SYSCALLS.varargs += 4;
      return ret;
    }
    var syscallGetVarargP = syscallGetVarargI;

    function ___syscall_fcntl64(fd, cmd, varargs) {
      SYSCALLS.varargs = varargs;
      try {
        var stream = SYSCALLS.getStreamFromFD(fd);
        switch (cmd) {
          case 0: {
            var arg = syscallGetVarargI();
            if (arg < 0) {
              return -28;
            }
            while (FS.streams[arg]) {
              arg++;
            }
            var newStream;
            newStream = FS.dupStream(stream, arg);
            return newStream.fd;
          }
          case 1:
          case 2:
            return 0;
          case 3:
            return stream.flags;
          case 4: {
            var arg = syscallGetVarargI();
            stream.flags |= arg;
            return 0;
          }
          case 12: {
            var arg = syscallGetVarargP();
            var offset = 0;

            HEAP16[(arg + offset) >> 1] = 2;
            return 0;
          }
          case 13:
          case 14:
            return 0;
        }
        return -28;
      } catch (e) {
        if (typeof FS == 'undefined' || !(e.name === 'ErrnoError')) throw e;
        return -e.errno;
      }
    }

    function ___syscall_fstat64(fd, buf) {
      try {
        var stream = SYSCALLS.getStreamFromFD(fd);
        return SYSCALLS.doStat(FS.stat, stream.path, buf);
      } catch (e) {
        if (typeof FS == 'undefined' || !(e.name === 'ErrnoError')) throw e;
        return -e.errno;
      }
    }

    var INT53_MAX = 9007199254740992;

    var INT53_MIN = -9007199254740992;
    var bigintToI53Checked = (num) =>
      num < INT53_MIN || num > INT53_MAX ? NaN : Number(num);
    function ___syscall_ftruncate64(fd, length) {
      length = bigintToI53Checked(length);

      try {
        if (isNaN(length)) return 61;
        FS.ftruncate(fd, length);
        return 0;
      } catch (e) {
        if (typeof FS == 'undefined' || !(e.name === 'ErrnoError')) throw e;
        return -e.errno;
      }
    }

    var stringToUTF8 = (str, outPtr, maxBytesToWrite) => {
      return stringToUTF8Array(str, HEAPU8, outPtr, maxBytesToWrite);
    };
    function ___syscall_getcwd(buf, size) {
      try {
        if (size === 0) return -28;
        var cwd = FS.cwd();
        var cwdLengthInBytes = lengthBytesUTF8(cwd) + 1;
        if (size < cwdLengthInBytes) return -68;
        stringToUTF8(cwd, buf, size);
        return cwdLengthInBytes;
      } catch (e) {
        if (typeof FS == 'undefined' || !(e.name === 'ErrnoError')) throw e;
        return -e.errno;
      }
    }

    function ___syscall_ioctl(fd, op, varargs) {
      SYSCALLS.varargs = varargs;
      try {
        var stream = SYSCALLS.getStreamFromFD(fd);
        switch (op) {
          case 21509: {
            if (!stream.tty) return -59;
            return 0;
          }
          case 21505: {
            if (!stream.tty) return -59;
            if (stream.tty.ops.ioctl_tcgets) {
              var termios = stream.tty.ops.ioctl_tcgets(stream);
              var argp = syscallGetVarargP();
              HEAP32[argp >> 2] = termios.c_iflag || 0;
              HEAP32[(argp + 4) >> 2] = termios.c_oflag || 0;
              HEAP32[(argp + 8) >> 2] = termios.c_cflag || 0;
              HEAP32[(argp + 12) >> 2] = termios.c_lflag || 0;
              for (var i = 0; i < 32; i++) {
                HEAP8[argp + i + 17] = termios.c_cc[i] || 0;
              }
              return 0;
            }
            return 0;
          }
          case 21510:
          case 21511:
          case 21512: {
            if (!stream.tty) return -59;
            return 0;
          }
          case 21506:
          case 21507:
          case 21508: {
            if (!stream.tty) return -59;
            if (stream.tty.ops.ioctl_tcsets) {
              var argp = syscallGetVarargP();
              var c_iflag = HEAP32[argp >> 2];
              var c_oflag = HEAP32[(argp + 4) >> 2];
              var c_cflag = HEAP32[(argp + 8) >> 2];
              var c_lflag = HEAP32[(argp + 12) >> 2];
              var c_cc = [];
              for (var i = 0; i < 32; i++) {
                c_cc.push(HEAP8[argp + i + 17]);
              }
              return stream.tty.ops.ioctl_tcsets(stream.tty, op, {
                c_iflag,
                c_oflag,
                c_cflag,
                c_lflag,
                c_cc,
              });
            }
            return 0;
          }
          case 21519: {
            if (!stream.tty) return -59;
            var argp = syscallGetVarargP();
            HEAP32[argp >> 2] = 0;
            return 0;
          }
          case 21520: {
            if (!stream.tty) return -59;
            return -28;
          }
          case 21531: {
            var argp = syscallGetVarargP();
            return FS.ioctl(stream, op, argp);
          }
          case 21523: {
            if (!stream.tty) return -59;
            if (stream.tty.ops.ioctl_tiocgwinsz) {
              var winsize = stream.tty.ops.ioctl_tiocgwinsz(stream.tty);
              var argp = syscallGetVarargP();
              HEAP16[argp >> 1] = winsize[0];
              HEAP16[(argp + 2) >> 1] = winsize[1];
            }
            return 0;
          }
          case 21524: {
            if (!stream.tty) return -59;
            return 0;
          }
          case 21515: {
            if (!stream.tty) return -59;
            return 0;
          }
          default:
            return -28;
        }
      } catch (e) {
        if (typeof FS == 'undefined' || !(e.name === 'ErrnoError')) throw e;
        return -e.errno;
      }
    }

    function ___syscall_lstat64(path, buf) {
      try {
        path = SYSCALLS.getStr(path);
        return SYSCALLS.doStat(FS.lstat, path, buf);
      } catch (e) {
        if (typeof FS == 'undefined' || !(e.name === 'ErrnoError')) throw e;
        return -e.errno;
      }
    }

    function ___syscall_mkdirat(dirfd, path, mode) {
      try {
        path = SYSCALLS.getStr(path);
        path = SYSCALLS.calculateAt(dirfd, path);

        path = PATH.normalize(path);
        if (path[path.length - 1] === '/')
          path = path.substr(0, path.length - 1);
        FS.mkdir(path, mode, 0);
        return 0;
      } catch (e) {
        if (typeof FS == 'undefined' || !(e.name === 'ErrnoError')) throw e;
        return -e.errno;
      }
    }

    function ___syscall_newfstatat(dirfd, path, buf, flags) {
      try {
        path = SYSCALLS.getStr(path);
        var nofollow = flags & 256;
        var allowEmpty = flags & 4096;
        flags = flags & ~6400;
        path = SYSCALLS.calculateAt(dirfd, path, allowEmpty);
        return SYSCALLS.doStat(nofollow ? FS.lstat : FS.stat, path, buf);
      } catch (e) {
        if (typeof FS == 'undefined' || !(e.name === 'ErrnoError')) throw e;
        return -e.errno;
      }
    }

    function ___syscall_openat(dirfd, path, flags, varargs) {
      SYSCALLS.varargs = varargs;
      try {
        path = SYSCALLS.getStr(path);
        path = SYSCALLS.calculateAt(dirfd, path);
        var mode = varargs ? syscallGetVarargI() : 0;
        return FS.open(path, flags, mode).fd;
      } catch (e) {
        if (typeof FS == 'undefined' || !(e.name === 'ErrnoError')) throw e;
        return -e.errno;
      }
    }

    function ___syscall_readlinkat(dirfd, path, buf, bufsize) {
      try {
        path = SYSCALLS.getStr(path);
        path = SYSCALLS.calculateAt(dirfd, path);
        if (bufsize <= 0) return -28;
        var ret = FS.readlink(path);

        var len = Math.min(bufsize, lengthBytesUTF8(ret));
        var endChar = HEAP8[buf + len];
        stringToUTF8(ret, buf, bufsize + 1);

        HEAP8[buf + len] = endChar;
        return len;
      } catch (e) {
        if (typeof FS == 'undefined' || !(e.name === 'ErrnoError')) throw e;
        return -e.errno;
      }
    }

    function ___syscall_rmdir(path) {
      try {
        path = SYSCALLS.getStr(path);
        FS.rmdir(path);
        return 0;
      } catch (e) {
        if (typeof FS == 'undefined' || !(e.name === 'ErrnoError')) throw e;
        return -e.errno;
      }
    }

    function ___syscall_stat64(path, buf) {
      try {
        path = SYSCALLS.getStr(path);
        return SYSCALLS.doStat(FS.stat, path, buf);
      } catch (e) {
        if (typeof FS == 'undefined' || !(e.name === 'ErrnoError')) throw e;
        return -e.errno;
      }
    }

    function ___syscall_unlinkat(dirfd, path, flags) {
      try {
        path = SYSCALLS.getStr(path);
        path = SYSCALLS.calculateAt(dirfd, path);
        if (flags === 0) {
          FS.unlink(path);
        } else if (flags === 512) {
          FS.rmdir(path);
        } else {
          abort('Invalid flags passed to unlinkat');
        }
        return 0;
      } catch (e) {
        if (typeof FS == 'undefined' || !(e.name === 'ErrnoError')) throw e;
        return -e.errno;
      }
    }

    var readI53FromI64 = (ptr) => {
      return HEAPU32[ptr >> 2] + HEAP32[(ptr + 4) >> 2] * 4294967296;
    };

    function ___syscall_utimensat(dirfd, path, times, flags) {
      try {
        path = SYSCALLS.getStr(path);
        path = SYSCALLS.calculateAt(dirfd, path, true);
        var now = Date.now(),
          atime,
          mtime;
        if (!times) {
          atime = now;
          mtime = now;
        } else {
          var seconds = readI53FromI64(times);
          var nanoseconds = HEAP32[(times + 8) >> 2];
          if (nanoseconds == 1073741823) {
            atime = now;
          } else if (nanoseconds == 1073741822) {
            atime = -1;
          } else {
            atime = seconds * 1000 + nanoseconds / (1000 * 1000);
          }
          times += 16;
          seconds = readI53FromI64(times);
          nanoseconds = HEAP32[(times + 8) >> 2];
          if (nanoseconds == 1073741823) {
            mtime = now;
          } else if (nanoseconds == 1073741822) {
            mtime = -1;
          } else {
            mtime = seconds * 1000 + nanoseconds / (1000 * 1000);
          }
        }

        if (mtime != -1 || atime != -1) {
          FS.utime(path, atime, mtime);
        }
        return 0;
      } catch (e) {
        if (typeof FS == 'undefined' || !(e.name === 'ErrnoError')) throw e;
        return -e.errno;
      }
    }

    var nowIsMonotonic = 1;
    var __emscripten_get_now_is_monotonic = () => nowIsMonotonic;

    var isLeapYear = (year) =>
      year % 4 === 0 && (year % 100 !== 0 || year % 400 === 0);

    var MONTH_DAYS_LEAP_CUMULATIVE = [
      0, 31, 60, 91, 121, 152, 182, 213, 244, 274, 305, 335,
    ];

    var MONTH_DAYS_REGULAR_CUMULATIVE = [
      0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334,
    ];
    var ydayFromDate = (date) => {
      var leap = isLeapYear(date.getFullYear());
      var monthDaysCumulative = leap
        ? MONTH_DAYS_LEAP_CUMULATIVE
        : MONTH_DAYS_REGULAR_CUMULATIVE;
      var yday = monthDaysCumulative[date.getMonth()] + date.getDate() - 1;

      return yday;
    };

    function __localtime_js(time, tmPtr) {
      time = bigintToI53Checked(time);

      var date = new Date(time * 1000);
      HEAP32[tmPtr >> 2] = date.getSeconds();
      HEAP32[(tmPtr + 4) >> 2] = date.getMinutes();
      HEAP32[(tmPtr + 8) >> 2] = date.getHours();
      HEAP32[(tmPtr + 12) >> 2] = date.getDate();
      HEAP32[(tmPtr + 16) >> 2] = date.getMonth();
      HEAP32[(tmPtr + 20) >> 2] = date.getFullYear() - 1900;
      HEAP32[(tmPtr + 24) >> 2] = date.getDay();

      var yday = ydayFromDate(date) | 0;
      HEAP32[(tmPtr + 28) >> 2] = yday;
      HEAP32[(tmPtr + 36) >> 2] = -(date.getTimezoneOffset() * 60);

      var start = new Date(date.getFullYear(), 0, 1);
      var summerOffset = new Date(date.getFullYear(), 6, 1).getTimezoneOffset();
      var winterOffset = start.getTimezoneOffset();
      var dst =
        (summerOffset != winterOffset &&
          date.getTimezoneOffset() == Math.min(winterOffset, summerOffset)) | 0;
      HEAP32[(tmPtr + 32) >> 2] = dst;
    }

    function __mmap_js(len, prot, flags, fd, offset, allocated, addr) {
      offset = bigintToI53Checked(offset);

      try {
        if (isNaN(offset)) return 61;
        var stream = SYSCALLS.getStreamFromFD(fd);
        var res = FS.mmap(stream, len, offset, prot, flags);
        var ptr = res.ptr;
        HEAP32[allocated >> 2] = res.allocated;
        HEAPU32[addr >> 2] = ptr;
        return 0;
      } catch (e) {
        if (typeof FS == 'undefined' || !(e.name === 'ErrnoError')) throw e;
        return -e.errno;
      }
    }

    function __munmap_js(addr, len, prot, flags, fd, offset) {
      offset = bigintToI53Checked(offset);

      try {
        var stream = SYSCALLS.getStreamFromFD(fd);
        if (prot & 2) {
          SYSCALLS.doMsync(addr, stream, len, flags, offset);
        }
      } catch (e) {
        if (typeof FS == 'undefined' || !(e.name === 'ErrnoError')) throw e;
        return -e.errno;
      }
    }

    var __tzset_js = (timezone, daylight, std_name, dst_name) => {
      var currentYear = new Date().getFullYear();
      var winter = new Date(currentYear, 0, 1);
      var summer = new Date(currentYear, 6, 1);
      var winterOffset = winter.getTimezoneOffset();
      var summerOffset = summer.getTimezoneOffset();

      var stdTimezoneOffset = Math.max(winterOffset, summerOffset);

      HEAPU32[timezone >> 2] = stdTimezoneOffset * 60;

      HEAP32[daylight >> 2] = Number(winterOffset != summerOffset);

      var extractZone = (timezoneOffset) => {
        var sign = timezoneOffset >= 0 ? '-' : '+';

        var absOffset = Math.abs(timezoneOffset);
        var hours = String(Math.floor(absOffset / 60)).padStart(2, '0');
        var minutes = String(absOffset % 60).padStart(2, '0');

        return `UTC${sign}${hours}${minutes}`;
      };

      var winterName = extractZone(winterOffset);
      var summerName = extractZone(summerOffset);
      if (summerOffset < winterOffset) {
        stringToUTF8(winterName, std_name, 17);
        stringToUTF8(summerName, dst_name, 17);
      } else {
        stringToUTF8(winterName, dst_name, 17);
        stringToUTF8(summerName, std_name, 17);
      }
    };

    var _emscripten_date_now = () => Date.now();

    var _emscripten_get_now = () => performance.now();

    var getHeapMax = () => 2147483648;

    var growMemory = (size) => {
      var b = wasmMemory.buffer;
      var pages = ((size - b.byteLength + 65535) / 65536) | 0;
      try {
        wasmMemory.grow(pages);
        updateMemoryViews();
        return 1;
      } catch (e) {}
    };
    var _emscripten_resize_heap = (requestedSize) => {
      var oldSize = HEAPU8.length;

      requestedSize >>>= 0;

      var maxHeapSize = getHeapMax();
      if (requestedSize > maxHeapSize) {
        return false;
      }

      for (var cutDown = 1; cutDown <= 4; cutDown *= 2) {
        var overGrownHeapSize = oldSize * (1 + 0.2 / cutDown);

        overGrownHeapSize = Math.min(
          overGrownHeapSize,
          requestedSize + 100663296,
        );

        var newSize = Math.min(
          maxHeapSize,
          alignMemory(Math.max(requestedSize, overGrownHeapSize), 65536),
        );

        var replacement = growMemory(newSize);
        if (replacement) {
          return true;
        }
      }
      return false;
    };

    var ENV = {};

    var getExecutableName = () => {
      return thisProgram || './this.program';
    };
    var getEnvStrings = () => {
      if (!getEnvStrings.strings) {
        var lang =
          (
            (typeof navigator == 'object' &&
              navigator.languages &&
              navigator.languages[0]) ||
            'C'
          ).replace('-', '_') + '.UTF-8';
        var env = {
          USER: 'web_user',
          LOGNAME: 'web_user',
          PATH: '/',
          PWD: '/',
          HOME: '/home/web_user',
          LANG: lang,
          _: getExecutableName(),
        };

        for (var x in ENV) {
          if (ENV[x] === undefined) delete env[x];
          else env[x] = ENV[x];
        }
        var strings = [];
        for (var x in env) {
          strings.push(`${x}=${env[x]}`);
        }
        getEnvStrings.strings = strings;
      }
      return getEnvStrings.strings;
    };

    var stringToAscii = (str, buffer) => {
      for (var i = 0; i < str.length; ++i) {
        HEAP8[buffer++] = str.charCodeAt(i);
      }

      HEAP8[buffer] = 0;
    };
    var _environ_get = (__environ, environ_buf) => {
      var bufSize = 0;
      getEnvStrings().forEach((string, i) => {
        var ptr = environ_buf + bufSize;
        HEAPU32[(__environ + i * 4) >> 2] = ptr;
        stringToAscii(string, ptr);
        bufSize += string.length + 1;
      });
      return 0;
    };

    var _environ_sizes_get = (penviron_count, penviron_buf_size) => {
      var strings = getEnvStrings();
      HEAPU32[penviron_count >> 2] = strings.length;
      var bufSize = 0;
      strings.forEach((string) => (bufSize += string.length + 1));
      HEAPU32[penviron_buf_size >> 2] = bufSize;
      return 0;
    };

    function _fd_close(fd) {
      try {
        var stream = SYSCALLS.getStreamFromFD(fd);
        FS.close(stream);
        return 0;
      } catch (e) {
        if (typeof FS == 'undefined' || !(e.name === 'ErrnoError')) throw e;
        return e.errno;
      }
    }

    function _fd_fdstat_get(fd, pbuf) {
      try {
        var rightsBase = 0;
        var rightsInheriting = 0;
        var flags = 0;
        {
          var stream = SYSCALLS.getStreamFromFD(fd);

          var type = stream.tty
            ? 2
            : FS.isDir(stream.mode)
              ? 3
              : FS.isLink(stream.mode)
                ? 7
                : 4;
        }
        HEAP8[pbuf] = type;
        HEAP16[(pbuf + 2) >> 1] = flags;
        HEAP64[(pbuf + 8) >> 3] = BigInt(rightsBase);
        HEAP64[(pbuf + 16) >> 3] = BigInt(rightsInheriting);
        return 0;
      } catch (e) {
        if (typeof FS == 'undefined' || !(e.name === 'ErrnoError')) throw e;
        return e.errno;
      }
    }

    var doReadv = (stream, iov, iovcnt, offset) => {
      var ret = 0;
      for (var i = 0; i < iovcnt; i++) {
        var ptr = HEAPU32[iov >> 2];
        var len = HEAPU32[(iov + 4) >> 2];
        iov += 8;
        var curr = FS.read(stream, HEAP8, ptr, len, offset);
        if (curr < 0) return -1;
        ret += curr;
        if (curr < len) break;
        if (typeof offset != 'undefined') {
          offset += curr;
        }
      }
      return ret;
    };

    function _fd_read(fd, iov, iovcnt, pnum) {
      try {
        var stream = SYSCALLS.getStreamFromFD(fd);
        var num = doReadv(stream, iov, iovcnt);
        HEAPU32[pnum >> 2] = num;
        return 0;
      } catch (e) {
        if (typeof FS == 'undefined' || !(e.name === 'ErrnoError')) throw e;
        return e.errno;
      }
    }

    function _fd_seek(fd, offset, whence, newOffset) {
      offset = bigintToI53Checked(offset);

      try {
        if (isNaN(offset)) return 61;
        var stream = SYSCALLS.getStreamFromFD(fd);
        FS.llseek(stream, offset, whence);
        HEAP64[newOffset >> 3] = BigInt(stream.position);
        if (stream.getdents && offset === 0 && whence === 0)
          stream.getdents = null;
        return 0;
      } catch (e) {
        if (typeof FS == 'undefined' || !(e.name === 'ErrnoError')) throw e;
        return e.errno;
      }
    }

    function _fd_sync(fd) {
      try {
        var stream = SYSCALLS.getStreamFromFD(fd);
        if (stream.stream_ops?.fsync) {
          return stream.stream_ops.fsync(stream);
        }
        return 0;
      } catch (e) {
        if (typeof FS == 'undefined' || !(e.name === 'ErrnoError')) throw e;
        return e.errno;
      }
    }

    var doWritev = (stream, iov, iovcnt, offset) => {
      var ret = 0;
      for (var i = 0; i < iovcnt; i++) {
        var ptr = HEAPU32[iov >> 2];
        var len = HEAPU32[(iov + 4) >> 2];
        iov += 8;
        var curr = FS.write(stream, HEAP8, ptr, len, offset);
        if (curr < 0) return -1;
        ret += curr;
        if (curr < len) {
          break;
        }
        if (typeof offset != 'undefined') {
          offset += curr;
        }
      }
      return ret;
    };

    function _fd_write(fd, iov, iovcnt, pnum) {
      try {
        var stream = SYSCALLS.getStreamFromFD(fd);
        var num = doWritev(stream, iov, iovcnt);
        HEAPU32[pnum >> 2] = num;
        return 0;
      } catch (e) {
        if (typeof FS == 'undefined' || !(e.name === 'ErrnoError')) throw e;
        return e.errno;
      }
    }

    FS.createPreloadedFile = FS_createPreloadedFile;
    FS.staticInit();

    var wasmImports = {
      __syscall_chmod: ___syscall_chmod,

      __syscall_faccessat: ___syscall_faccessat,

      __syscall_fchmod: ___syscall_fchmod,

      __syscall_fchown32: ___syscall_fchown32,

      __syscall_fcntl64: ___syscall_fcntl64,

      __syscall_fstat64: ___syscall_fstat64,

      __syscall_ftruncate64: ___syscall_ftruncate64,

      __syscall_getcwd: ___syscall_getcwd,

      __syscall_ioctl: ___syscall_ioctl,

      __syscall_lstat64: ___syscall_lstat64,

      __syscall_mkdirat: ___syscall_mkdirat,

      __syscall_newfstatat: ___syscall_newfstatat,

      __syscall_openat: ___syscall_openat,

      __syscall_readlinkat: ___syscall_readlinkat,

      __syscall_rmdir: ___syscall_rmdir,

      __syscall_stat64: ___syscall_stat64,

      __syscall_unlinkat: ___syscall_unlinkat,

      __syscall_utimensat: ___syscall_utimensat,

      _emscripten_get_now_is_monotonic: __emscripten_get_now_is_monotonic,

      _localtime_js: __localtime_js,

      _mmap_js: __mmap_js,

      _munmap_js: __munmap_js,

      _tzset_js: __tzset_js,

      emscripten_date_now: _emscripten_date_now,

      emscripten_get_now: _emscripten_get_now,

      emscripten_resize_heap: _emscripten_resize_heap,

      environ_get: _environ_get,

      environ_sizes_get: _environ_sizes_get,

      fd_close: _fd_close,

      fd_fdstat_get: _fd_fdstat_get,

      fd_read: _fd_read,

      fd_seek: _fd_seek,

      fd_sync: _fd_sync,

      fd_write: _fd_write,

      memory: wasmMemory,
    };
    var wasmExports = createWasm();
    var ___wasm_call_ctors = () =>
      (___wasm_call_ctors = wasmExports['__wasm_call_ctors'])();
    var _sqlite3_status64 = (Module['_sqlite3_status64'] = (a0, a1, a2, a3) =>
      (_sqlite3_status64 = Module['_sqlite3_status64'] =
        wasmExports['sqlite3_status64'])(a0, a1, a2, a3));
    var _sqlite3_status = (Module['_sqlite3_status'] = (a0, a1, a2, a3) =>
      (_sqlite3_status = Module['_sqlite3_status'] =
        wasmExports['sqlite3_status'])(a0, a1, a2, a3));
    var _sqlite3_db_status = (Module['_sqlite3_db_status'] = (
      a0,
      a1,
      a2,
      a3,
      a4,
    ) =>
      (_sqlite3_db_status = Module['_sqlite3_db_status'] =
        wasmExports['sqlite3_db_status'])(a0, a1, a2, a3, a4));
    var _sqlite3_msize = (Module['_sqlite3_msize'] = (a0) =>
      (_sqlite3_msize = Module['_sqlite3_msize'] =
        wasmExports['sqlite3_msize'])(a0));
    var _sqlite3_vfs_find = (Module['_sqlite3_vfs_find'] = (a0) =>
      (_sqlite3_vfs_find = Module['_sqlite3_vfs_find'] =
        wasmExports['sqlite3_vfs_find'])(a0));
    var _sqlite3_initialize = (Module['_sqlite3_initialize'] = () =>
      (_sqlite3_initialize = Module['_sqlite3_initialize'] =
        wasmExports['sqlite3_initialize'])());
    var _sqlite3_malloc = (Module['_sqlite3_malloc'] = (a0) =>
      (_sqlite3_malloc = Module['_sqlite3_malloc'] =
        wasmExports['sqlite3_malloc'])(a0));
    var _sqlite3_free = (Module['_sqlite3_free'] = (a0) =>
      (_sqlite3_free = Module['_sqlite3_free'] = wasmExports['sqlite3_free'])(
        a0,
      ));
    var _sqlite3_vfs_register = (Module['_sqlite3_vfs_register'] = (a0, a1) =>
      (_sqlite3_vfs_register = Module['_sqlite3_vfs_register'] =
        wasmExports['sqlite3_vfs_register'])(a0, a1));
    var _sqlite3_vfs_unregister = (Module['_sqlite3_vfs_unregister'] = (a0) =>
      (_sqlite3_vfs_unregister = Module['_sqlite3_vfs_unregister'] =
        wasmExports['sqlite3_vfs_unregister'])(a0));
    var _sqlite3_malloc64 = (Module['_sqlite3_malloc64'] = (a0) =>
      (_sqlite3_malloc64 = Module['_sqlite3_malloc64'] =
        wasmExports['sqlite3_malloc64'])(a0));
    var _sqlite3_realloc = (Module['_sqlite3_realloc'] = (a0, a1) =>
      (_sqlite3_realloc = Module['_sqlite3_realloc'] =
        wasmExports['sqlite3_realloc'])(a0, a1));
    var _sqlite3_realloc64 = (Module['_sqlite3_realloc64'] = (a0, a1) =>
      (_sqlite3_realloc64 = Module['_sqlite3_realloc64'] =
        wasmExports['sqlite3_realloc64'])(a0, a1));
    var _sqlite3_value_text = (Module['_sqlite3_value_text'] = (a0) =>
      (_sqlite3_value_text = Module['_sqlite3_value_text'] =
        wasmExports['sqlite3_value_text'])(a0));
    var _sqlite3_randomness = (Module['_sqlite3_randomness'] = (a0, a1) =>
      (_sqlite3_randomness = Module['_sqlite3_randomness'] =
        wasmExports['sqlite3_randomness'])(a0, a1));
    var _sqlite3_stricmp = (Module['_sqlite3_stricmp'] = (a0, a1) =>
      (_sqlite3_stricmp = Module['_sqlite3_stricmp'] =
        wasmExports['sqlite3_stricmp'])(a0, a1));
    var _sqlite3_strnicmp = (Module['_sqlite3_strnicmp'] = (a0, a1, a2) =>
      (_sqlite3_strnicmp = Module['_sqlite3_strnicmp'] =
        wasmExports['sqlite3_strnicmp'])(a0, a1, a2));
    var _sqlite3_uri_parameter = (Module['_sqlite3_uri_parameter'] = (a0, a1) =>
      (_sqlite3_uri_parameter = Module['_sqlite3_uri_parameter'] =
        wasmExports['sqlite3_uri_parameter'])(a0, a1));
    var _sqlite3_uri_boolean = (Module['_sqlite3_uri_boolean'] = (a0, a1, a2) =>
      (_sqlite3_uri_boolean = Module['_sqlite3_uri_boolean'] =
        wasmExports['sqlite3_uri_boolean'])(a0, a1, a2));
    var _sqlite3_serialize = (Module['_sqlite3_serialize'] = (a0, a1, a2, a3) =>
      (_sqlite3_serialize = Module['_sqlite3_serialize'] =
        wasmExports['sqlite3_serialize'])(a0, a1, a2, a3));
    var _sqlite3_prepare_v2 = (Module['_sqlite3_prepare_v2'] = (
      a0,
      a1,
      a2,
      a3,
      a4,
    ) =>
      (_sqlite3_prepare_v2 = Module['_sqlite3_prepare_v2'] =
        wasmExports['sqlite3_prepare_v2'])(a0, a1, a2, a3, a4));
    var _sqlite3_step = (Module['_sqlite3_step'] = (a0) =>
      (_sqlite3_step = Module['_sqlite3_step'] = wasmExports['sqlite3_step'])(
        a0,
      ));
    var _sqlite3_column_int64 = (Module['_sqlite3_column_int64'] = (a0, a1) =>
      (_sqlite3_column_int64 = Module['_sqlite3_column_int64'] =
        wasmExports['sqlite3_column_int64'])(a0, a1));
    var _sqlite3_reset = (Module['_sqlite3_reset'] = (a0) =>
      (_sqlite3_reset = Module['_sqlite3_reset'] =
        wasmExports['sqlite3_reset'])(a0));
    var _sqlite3_exec = (Module['_sqlite3_exec'] = (a0, a1, a2, a3, a4) =>
      (_sqlite3_exec = Module['_sqlite3_exec'] = wasmExports['sqlite3_exec'])(
        a0,
        a1,
        a2,
        a3,
        a4,
      ));
    var _sqlite3_column_int = (Module['_sqlite3_column_int'] = (a0, a1) =>
      (_sqlite3_column_int = Module['_sqlite3_column_int'] =
        wasmExports['sqlite3_column_int'])(a0, a1));
    var _sqlite3_finalize = (Module['_sqlite3_finalize'] = (a0) =>
      (_sqlite3_finalize = Module['_sqlite3_finalize'] =
        wasmExports['sqlite3_finalize'])(a0));
    var _sqlite3_file_control = (Module['_sqlite3_file_control'] = (
      a0,
      a1,
      a2,
      a3,
    ) =>
      (_sqlite3_file_control = Module['_sqlite3_file_control'] =
        wasmExports['sqlite3_file_control'])(a0, a1, a2, a3));
    var _sqlite3_column_name = (Module['_sqlite3_column_name'] = (a0, a1) =>
      (_sqlite3_column_name = Module['_sqlite3_column_name'] =
        wasmExports['sqlite3_column_name'])(a0, a1));
    var _sqlite3_column_text = (Module['_sqlite3_column_text'] = (a0, a1) =>
      (_sqlite3_column_text = Module['_sqlite3_column_text'] =
        wasmExports['sqlite3_column_text'])(a0, a1));
    var _sqlite3_column_type = (Module['_sqlite3_column_type'] = (a0, a1) =>
      (_sqlite3_column_type = Module['_sqlite3_column_type'] =
        wasmExports['sqlite3_column_type'])(a0, a1));
    var _sqlite3_errmsg = (Module['_sqlite3_errmsg'] = (a0) =>
      (_sqlite3_errmsg = Module['_sqlite3_errmsg'] =
        wasmExports['sqlite3_errmsg'])(a0));
    var _sqlite3_deserialize = (Module['_sqlite3_deserialize'] = (
      a0,
      a1,
      a2,
      a3,
      a4,
      a5,
    ) =>
      (_sqlite3_deserialize = Module['_sqlite3_deserialize'] =
        wasmExports['sqlite3_deserialize'])(a0, a1, a2, a3, a4, a5));
    var _sqlite3_clear_bindings = (Module['_sqlite3_clear_bindings'] = (a0) =>
      (_sqlite3_clear_bindings = Module['_sqlite3_clear_bindings'] =
        wasmExports['sqlite3_clear_bindings'])(a0));
    var _sqlite3_value_blob = (Module['_sqlite3_value_blob'] = (a0) =>
      (_sqlite3_value_blob = Module['_sqlite3_value_blob'] =
        wasmExports['sqlite3_value_blob'])(a0));
    var _sqlite3_value_bytes = (Module['_sqlite3_value_bytes'] = (a0) =>
      (_sqlite3_value_bytes = Module['_sqlite3_value_bytes'] =
        wasmExports['sqlite3_value_bytes'])(a0));
    var _sqlite3_value_double = (Module['_sqlite3_value_double'] = (a0) =>
      (_sqlite3_value_double = Module['_sqlite3_value_double'] =
        wasmExports['sqlite3_value_double'])(a0));
    var _sqlite3_value_int = (Module['_sqlite3_value_int'] = (a0) =>
      (_sqlite3_value_int = Module['_sqlite3_value_int'] =
        wasmExports['sqlite3_value_int'])(a0));
    var _sqlite3_value_int64 = (Module['_sqlite3_value_int64'] = (a0) =>
      (_sqlite3_value_int64 = Module['_sqlite3_value_int64'] =
        wasmExports['sqlite3_value_int64'])(a0));
    var _sqlite3_value_subtype = (Module['_sqlite3_value_subtype'] = (a0) =>
      (_sqlite3_value_subtype = Module['_sqlite3_value_subtype'] =
        wasmExports['sqlite3_value_subtype'])(a0));
    var _sqlite3_value_pointer = (Module['_sqlite3_value_pointer'] = (a0, a1) =>
      (_sqlite3_value_pointer = Module['_sqlite3_value_pointer'] =
        wasmExports['sqlite3_value_pointer'])(a0, a1));
    var _sqlite3_value_type = (Module['_sqlite3_value_type'] = (a0) =>
      (_sqlite3_value_type = Module['_sqlite3_value_type'] =
        wasmExports['sqlite3_value_type'])(a0));
    var _sqlite3_value_nochange = (Module['_sqlite3_value_nochange'] = (a0) =>
      (_sqlite3_value_nochange = Module['_sqlite3_value_nochange'] =
        wasmExports['sqlite3_value_nochange'])(a0));
    var _sqlite3_value_frombind = (Module['_sqlite3_value_frombind'] = (a0) =>
      (_sqlite3_value_frombind = Module['_sqlite3_value_frombind'] =
        wasmExports['sqlite3_value_frombind'])(a0));
    var _sqlite3_value_dup = (Module['_sqlite3_value_dup'] = (a0) =>
      (_sqlite3_value_dup = Module['_sqlite3_value_dup'] =
        wasmExports['sqlite3_value_dup'])(a0));
    var _sqlite3_value_free = (Module['_sqlite3_value_free'] = (a0) =>
      (_sqlite3_value_free = Module['_sqlite3_value_free'] =
        wasmExports['sqlite3_value_free'])(a0));
    var _sqlite3_result_blob = (Module['_sqlite3_result_blob'] = (
      a0,
      a1,
      a2,
      a3,
    ) =>
      (_sqlite3_result_blob = Module['_sqlite3_result_blob'] =
        wasmExports['sqlite3_result_blob'])(a0, a1, a2, a3));
    var _sqlite3_result_error_toobig = (Module['_sqlite3_result_error_toobig'] =
      (a0) =>
        (_sqlite3_result_error_toobig = Module['_sqlite3_result_error_toobig'] =
          wasmExports['sqlite3_result_error_toobig'])(a0));
    var _sqlite3_result_error_nomem = (Module['_sqlite3_result_error_nomem'] = (
      a0,
    ) =>
      (_sqlite3_result_error_nomem = Module['_sqlite3_result_error_nomem'] =
        wasmExports['sqlite3_result_error_nomem'])(a0));
    var _sqlite3_result_double = (Module['_sqlite3_result_double'] = (a0, a1) =>
      (_sqlite3_result_double = Module['_sqlite3_result_double'] =
        wasmExports['sqlite3_result_double'])(a0, a1));
    var _sqlite3_result_error = (Module['_sqlite3_result_error'] = (
      a0,
      a1,
      a2,
    ) =>
      (_sqlite3_result_error = Module['_sqlite3_result_error'] =
        wasmExports['sqlite3_result_error'])(a0, a1, a2));
    var _sqlite3_result_int = (Module['_sqlite3_result_int'] = (a0, a1) =>
      (_sqlite3_result_int = Module['_sqlite3_result_int'] =
        wasmExports['sqlite3_result_int'])(a0, a1));
    var _sqlite3_result_int64 = (Module['_sqlite3_result_int64'] = (a0, a1) =>
      (_sqlite3_result_int64 = Module['_sqlite3_result_int64'] =
        wasmExports['sqlite3_result_int64'])(a0, a1));
    var _sqlite3_result_null = (Module['_sqlite3_result_null'] = (a0) =>
      (_sqlite3_result_null = Module['_sqlite3_result_null'] =
        wasmExports['sqlite3_result_null'])(a0));
    var _sqlite3_result_pointer = (Module['_sqlite3_result_pointer'] = (
      a0,
      a1,
      a2,
      a3,
    ) =>
      (_sqlite3_result_pointer = Module['_sqlite3_result_pointer'] =
        wasmExports['sqlite3_result_pointer'])(a0, a1, a2, a3));
    var _sqlite3_result_subtype = (Module['_sqlite3_result_subtype'] = (
      a0,
      a1,
    ) =>
      (_sqlite3_result_subtype = Module['_sqlite3_result_subtype'] =
        wasmExports['sqlite3_result_subtype'])(a0, a1));
    var _sqlite3_result_text = (Module['_sqlite3_result_text'] = (
      a0,
      a1,
      a2,
      a3,
    ) =>
      (_sqlite3_result_text = Module['_sqlite3_result_text'] =
        wasmExports['sqlite3_result_text'])(a0, a1, a2, a3));
    var _sqlite3_result_zeroblob = (Module['_sqlite3_result_zeroblob'] = (
      a0,
      a1,
    ) =>
      (_sqlite3_result_zeroblob = Module['_sqlite3_result_zeroblob'] =
        wasmExports['sqlite3_result_zeroblob'])(a0, a1));
    var _sqlite3_result_zeroblob64 = (Module['_sqlite3_result_zeroblob64'] = (
      a0,
      a1,
    ) =>
      (_sqlite3_result_zeroblob64 = Module['_sqlite3_result_zeroblob64'] =
        wasmExports['sqlite3_result_zeroblob64'])(a0, a1));
    var _sqlite3_result_error_code = (Module['_sqlite3_result_error_code'] = (
      a0,
      a1,
    ) =>
      (_sqlite3_result_error_code = Module['_sqlite3_result_error_code'] =
        wasmExports['sqlite3_result_error_code'])(a0, a1));
    var _sqlite3_user_data = (Module['_sqlite3_user_data'] = (a0) =>
      (_sqlite3_user_data = Module['_sqlite3_user_data'] =
        wasmExports['sqlite3_user_data'])(a0));
    var _sqlite3_context_db_handle = (Module['_sqlite3_context_db_handle'] = (
      a0,
    ) =>
      (_sqlite3_context_db_handle = Module['_sqlite3_context_db_handle'] =
        wasmExports['sqlite3_context_db_handle'])(a0));
    var _sqlite3_vtab_nochange = (Module['_sqlite3_vtab_nochange'] = (a0) =>
      (_sqlite3_vtab_nochange = Module['_sqlite3_vtab_nochange'] =
        wasmExports['sqlite3_vtab_nochange'])(a0));
    var _sqlite3_vtab_in_first = (Module['_sqlite3_vtab_in_first'] = (a0, a1) =>
      (_sqlite3_vtab_in_first = Module['_sqlite3_vtab_in_first'] =
        wasmExports['sqlite3_vtab_in_first'])(a0, a1));
    var _sqlite3_vtab_in_next = (Module['_sqlite3_vtab_in_next'] = (a0, a1) =>
      (_sqlite3_vtab_in_next = Module['_sqlite3_vtab_in_next'] =
        wasmExports['sqlite3_vtab_in_next'])(a0, a1));
    var _sqlite3_aggregate_context = (Module['_sqlite3_aggregate_context'] = (
      a0,
      a1,
    ) =>
      (_sqlite3_aggregate_context = Module['_sqlite3_aggregate_context'] =
        wasmExports['sqlite3_aggregate_context'])(a0, a1));
    var _sqlite3_get_auxdata = (Module['_sqlite3_get_auxdata'] = (a0, a1) =>
      (_sqlite3_get_auxdata = Module['_sqlite3_get_auxdata'] =
        wasmExports['sqlite3_get_auxdata'])(a0, a1));
    var _sqlite3_set_auxdata = (Module['_sqlite3_set_auxdata'] = (
      a0,
      a1,
      a2,
      a3,
    ) =>
      (_sqlite3_set_auxdata = Module['_sqlite3_set_auxdata'] =
        wasmExports['sqlite3_set_auxdata'])(a0, a1, a2, a3));
    var _sqlite3_column_count = (Module['_sqlite3_column_count'] = (a0) =>
      (_sqlite3_column_count = Module['_sqlite3_column_count'] =
        wasmExports['sqlite3_column_count'])(a0));
    var _sqlite3_data_count = (Module['_sqlite3_data_count'] = (a0) =>
      (_sqlite3_data_count = Module['_sqlite3_data_count'] =
        wasmExports['sqlite3_data_count'])(a0));
    var _sqlite3_column_blob = (Module['_sqlite3_column_blob'] = (a0, a1) =>
      (_sqlite3_column_blob = Module['_sqlite3_column_blob'] =
        wasmExports['sqlite3_column_blob'])(a0, a1));
    var _sqlite3_column_bytes = (Module['_sqlite3_column_bytes'] = (a0, a1) =>
      (_sqlite3_column_bytes = Module['_sqlite3_column_bytes'] =
        wasmExports['sqlite3_column_bytes'])(a0, a1));
    var _sqlite3_column_double = (Module['_sqlite3_column_double'] = (a0, a1) =>
      (_sqlite3_column_double = Module['_sqlite3_column_double'] =
        wasmExports['sqlite3_column_double'])(a0, a1));
    var _sqlite3_column_value = (Module['_sqlite3_column_value'] = (a0, a1) =>
      (_sqlite3_column_value = Module['_sqlite3_column_value'] =
        wasmExports['sqlite3_column_value'])(a0, a1));
    var _sqlite3_column_decltype = (Module['_sqlite3_column_decltype'] = (
      a0,
      a1,
    ) =>
      (_sqlite3_column_decltype = Module['_sqlite3_column_decltype'] =
        wasmExports['sqlite3_column_decltype'])(a0, a1));
    var _sqlite3_bind_blob = (Module['_sqlite3_bind_blob'] = (
      a0,
      a1,
      a2,
      a3,
      a4,
    ) =>
      (_sqlite3_bind_blob = Module['_sqlite3_bind_blob'] =
        wasmExports['sqlite3_bind_blob'])(a0, a1, a2, a3, a4));
    var _sqlite3_bind_double = (Module['_sqlite3_bind_double'] = (a0, a1, a2) =>
      (_sqlite3_bind_double = Module['_sqlite3_bind_double'] =
        wasmExports['sqlite3_bind_double'])(a0, a1, a2));
    var _sqlite3_bind_int = (Module['_sqlite3_bind_int'] = (a0, a1, a2) =>
      (_sqlite3_bind_int = Module['_sqlite3_bind_int'] =
        wasmExports['sqlite3_bind_int'])(a0, a1, a2));
    var _sqlite3_bind_int64 = (Module['_sqlite3_bind_int64'] = (a0, a1, a2) =>
      (_sqlite3_bind_int64 = Module['_sqlite3_bind_int64'] =
        wasmExports['sqlite3_bind_int64'])(a0, a1, a2));
    var _sqlite3_bind_null = (Module['_sqlite3_bind_null'] = (a0, a1) =>
      (_sqlite3_bind_null = Module['_sqlite3_bind_null'] =
        wasmExports['sqlite3_bind_null'])(a0, a1));
    var _sqlite3_bind_pointer = (Module['_sqlite3_bind_pointer'] = (
      a0,
      a1,
      a2,
      a3,
      a4,
    ) =>
      (_sqlite3_bind_pointer = Module['_sqlite3_bind_pointer'] =
        wasmExports['sqlite3_bind_pointer'])(a0, a1, a2, a3, a4));
    var _sqlite3_bind_text = (Module['_sqlite3_bind_text'] = (
      a0,
      a1,
      a2,
      a3,
      a4,
    ) =>
      (_sqlite3_bind_text = Module['_sqlite3_bind_text'] =
        wasmExports['sqlite3_bind_text'])(a0, a1, a2, a3, a4));
    var _sqlite3_bind_parameter_count = (Module[
      '_sqlite3_bind_parameter_count'
    ] = (a0) =>
      (_sqlite3_bind_parameter_count = Module['_sqlite3_bind_parameter_count'] =
        wasmExports['sqlite3_bind_parameter_count'])(a0));
    var _sqlite3_bind_parameter_name = (Module['_sqlite3_bind_parameter_name'] =
      (a0, a1) =>
        (_sqlite3_bind_parameter_name = Module['_sqlite3_bind_parameter_name'] =
          wasmExports['sqlite3_bind_parameter_name'])(a0, a1));
    var _sqlite3_bind_parameter_index = (Module[
      '_sqlite3_bind_parameter_index'
    ] = (a0, a1) =>
      (_sqlite3_bind_parameter_index = Module['_sqlite3_bind_parameter_index'] =
        wasmExports['sqlite3_bind_parameter_index'])(a0, a1));
    var _sqlite3_db_handle = (Module['_sqlite3_db_handle'] = (a0) =>
      (_sqlite3_db_handle = Module['_sqlite3_db_handle'] =
        wasmExports['sqlite3_db_handle'])(a0));
    var _sqlite3_stmt_readonly = (Module['_sqlite3_stmt_readonly'] = (a0) =>
      (_sqlite3_stmt_readonly = Module['_sqlite3_stmt_readonly'] =
        wasmExports['sqlite3_stmt_readonly'])(a0));
    var _sqlite3_stmt_isexplain = (Module['_sqlite3_stmt_isexplain'] = (a0) =>
      (_sqlite3_stmt_isexplain = Module['_sqlite3_stmt_isexplain'] =
        wasmExports['sqlite3_stmt_isexplain'])(a0));
    var _sqlite3_stmt_explain = (Module['_sqlite3_stmt_explain'] = (a0, a1) =>
      (_sqlite3_stmt_explain = Module['_sqlite3_stmt_explain'] =
        wasmExports['sqlite3_stmt_explain'])(a0, a1));
    var _sqlite3_stmt_busy = (Module['_sqlite3_stmt_busy'] = (a0) =>
      (_sqlite3_stmt_busy = Module['_sqlite3_stmt_busy'] =
        wasmExports['sqlite3_stmt_busy'])(a0));
    var _sqlite3_stmt_status = (Module['_sqlite3_stmt_status'] = (a0, a1, a2) =>
      (_sqlite3_stmt_status = Module['_sqlite3_stmt_status'] =
        wasmExports['sqlite3_stmt_status'])(a0, a1, a2));
    var _sqlite3_sql = (Module['_sqlite3_sql'] = (a0) =>
      (_sqlite3_sql = Module['_sqlite3_sql'] = wasmExports['sqlite3_sql'])(a0));
    var _sqlite3_expanded_sql = (Module['_sqlite3_expanded_sql'] = (a0) =>
      (_sqlite3_expanded_sql = Module['_sqlite3_expanded_sql'] =
        wasmExports['sqlite3_expanded_sql'])(a0));
    var _sqlite3_preupdate_old = (Module['_sqlite3_preupdate_old'] = (
      a0,
      a1,
      a2,
    ) =>
      (_sqlite3_preupdate_old = Module['_sqlite3_preupdate_old'] =
        wasmExports['sqlite3_preupdate_old'])(a0, a1, a2));
    var _sqlite3_preupdate_count = (Module['_sqlite3_preupdate_count'] = (a0) =>
      (_sqlite3_preupdate_count = Module['_sqlite3_preupdate_count'] =
        wasmExports['sqlite3_preupdate_count'])(a0));
    var _sqlite3_preupdate_depth = (Module['_sqlite3_preupdate_depth'] = (a0) =>
      (_sqlite3_preupdate_depth = Module['_sqlite3_preupdate_depth'] =
        wasmExports['sqlite3_preupdate_depth'])(a0));
    var _sqlite3_preupdate_blobwrite = (Module['_sqlite3_preupdate_blobwrite'] =
      (a0) =>
        (_sqlite3_preupdate_blobwrite = Module['_sqlite3_preupdate_blobwrite'] =
          wasmExports['sqlite3_preupdate_blobwrite'])(a0));
    var _sqlite3_preupdate_new = (Module['_sqlite3_preupdate_new'] = (
      a0,
      a1,
      a2,
    ) =>
      (_sqlite3_preupdate_new = Module['_sqlite3_preupdate_new'] =
        wasmExports['sqlite3_preupdate_new'])(a0, a1, a2));
    var _sqlite3_value_numeric_type = (Module['_sqlite3_value_numeric_type'] = (
      a0,
    ) =>
      (_sqlite3_value_numeric_type = Module['_sqlite3_value_numeric_type'] =
        wasmExports['sqlite3_value_numeric_type'])(a0));
    var _sqlite3_set_authorizer = (Module['_sqlite3_set_authorizer'] = (
      a0,
      a1,
      a2,
    ) =>
      (_sqlite3_set_authorizer = Module['_sqlite3_set_authorizer'] =
        wasmExports['sqlite3_set_authorizer'])(a0, a1, a2));
    var _sqlite3_strglob = (Module['_sqlite3_strglob'] = (a0, a1) =>
      (_sqlite3_strglob = Module['_sqlite3_strglob'] =
        wasmExports['sqlite3_strglob'])(a0, a1));
    var _sqlite3_strlike = (Module['_sqlite3_strlike'] = (a0, a1, a2) =>
      (_sqlite3_strlike = Module['_sqlite3_strlike'] =
        wasmExports['sqlite3_strlike'])(a0, a1, a2));
    var _sqlite3_auto_extension = (Module['_sqlite3_auto_extension'] = (a0) =>
      (_sqlite3_auto_extension = Module['_sqlite3_auto_extension'] =
        wasmExports['sqlite3_auto_extension'])(a0));
    var _sqlite3_cancel_auto_extension = (Module[
      '_sqlite3_cancel_auto_extension'
    ] = (a0) =>
      (_sqlite3_cancel_auto_extension = Module[
        '_sqlite3_cancel_auto_extension'
      ] =
        wasmExports['sqlite3_cancel_auto_extension'])(a0));
    var _sqlite3_reset_auto_extension = (Module[
      '_sqlite3_reset_auto_extension'
    ] = () =>
      (_sqlite3_reset_auto_extension = Module['_sqlite3_reset_auto_extension'] =
        wasmExports['sqlite3_reset_auto_extension'])());
    var _sqlite3_prepare_v3 = (Module['_sqlite3_prepare_v3'] = (
      a0,
      a1,
      a2,
      a3,
      a4,
      a5,
    ) =>
      (_sqlite3_prepare_v3 = Module['_sqlite3_prepare_v3'] =
        wasmExports['sqlite3_prepare_v3'])(a0, a1, a2, a3, a4, a5));
    var _sqlite3_create_module = (Module['_sqlite3_create_module'] = (
      a0,
      a1,
      a2,
      a3,
    ) =>
      (_sqlite3_create_module = Module['_sqlite3_create_module'] =
        wasmExports['sqlite3_create_module'])(a0, a1, a2, a3));
    var _sqlite3_create_module_v2 = (Module['_sqlite3_create_module_v2'] = (
      a0,
      a1,
      a2,
      a3,
      a4,
    ) =>
      (_sqlite3_create_module_v2 = Module['_sqlite3_create_module_v2'] =
        wasmExports['sqlite3_create_module_v2'])(a0, a1, a2, a3, a4));
    var _sqlite3_drop_modules = (Module['_sqlite3_drop_modules'] = (a0, a1) =>
      (_sqlite3_drop_modules = Module['_sqlite3_drop_modules'] =
        wasmExports['sqlite3_drop_modules'])(a0, a1));
    var _sqlite3_declare_vtab = (Module['_sqlite3_declare_vtab'] = (a0, a1) =>
      (_sqlite3_declare_vtab = Module['_sqlite3_declare_vtab'] =
        wasmExports['sqlite3_declare_vtab'])(a0, a1));
    var _sqlite3_vtab_on_conflict = (Module['_sqlite3_vtab_on_conflict'] = (
      a0,
    ) =>
      (_sqlite3_vtab_on_conflict = Module['_sqlite3_vtab_on_conflict'] =
        wasmExports['sqlite3_vtab_on_conflict'])(a0));
    var _sqlite3_vtab_collation = (Module['_sqlite3_vtab_collation'] = (
      a0,
      a1,
    ) =>
      (_sqlite3_vtab_collation = Module['_sqlite3_vtab_collation'] =
        wasmExports['sqlite3_vtab_collation'])(a0, a1));
    var _sqlite3_vtab_in = (Module['_sqlite3_vtab_in'] = (a0, a1, a2) =>
      (_sqlite3_vtab_in = Module['_sqlite3_vtab_in'] =
        wasmExports['sqlite3_vtab_in'])(a0, a1, a2));
    var _sqlite3_vtab_rhs_value = (Module['_sqlite3_vtab_rhs_value'] = (
      a0,
      a1,
      a2,
    ) =>
      (_sqlite3_vtab_rhs_value = Module['_sqlite3_vtab_rhs_value'] =
        wasmExports['sqlite3_vtab_rhs_value'])(a0, a1, a2));
    var _sqlite3_vtab_distinct = (Module['_sqlite3_vtab_distinct'] = (a0) =>
      (_sqlite3_vtab_distinct = Module['_sqlite3_vtab_distinct'] =
        wasmExports['sqlite3_vtab_distinct'])(a0));
    var _sqlite3_keyword_name = (Module['_sqlite3_keyword_name'] = (
      a0,
      a1,
      a2,
    ) =>
      (_sqlite3_keyword_name = Module['_sqlite3_keyword_name'] =
        wasmExports['sqlite3_keyword_name'])(a0, a1, a2));
    var _sqlite3_keyword_count = (Module['_sqlite3_keyword_count'] = () =>
      (_sqlite3_keyword_count = Module['_sqlite3_keyword_count'] =
        wasmExports['sqlite3_keyword_count'])());
    var _sqlite3_keyword_check = (Module['_sqlite3_keyword_check'] = (a0, a1) =>
      (_sqlite3_keyword_check = Module['_sqlite3_keyword_check'] =
        wasmExports['sqlite3_keyword_check'])(a0, a1));
    var _sqlite3_complete = (Module['_sqlite3_complete'] = (a0) =>
      (_sqlite3_complete = Module['_sqlite3_complete'] =
        wasmExports['sqlite3_complete'])(a0));
    var _sqlite3_libversion = (Module['_sqlite3_libversion'] = () =>
      (_sqlite3_libversion = Module['_sqlite3_libversion'] =
        wasmExports['sqlite3_libversion'])());
    var _sqlite3_libversion_number = (Module['_sqlite3_libversion_number'] =
      () =>
        (_sqlite3_libversion_number = Module['_sqlite3_libversion_number'] =
          wasmExports['sqlite3_libversion_number'])());
    var _sqlite3_shutdown = (Module['_sqlite3_shutdown'] = () =>
      (_sqlite3_shutdown = Module['_sqlite3_shutdown'] =
        wasmExports['sqlite3_shutdown'])());
    var _sqlite3_last_insert_rowid = (Module['_sqlite3_last_insert_rowid'] = (
      a0,
    ) =>
      (_sqlite3_last_insert_rowid = Module['_sqlite3_last_insert_rowid'] =
        wasmExports['sqlite3_last_insert_rowid'])(a0));
    var _sqlite3_set_last_insert_rowid = (Module[
      '_sqlite3_set_last_insert_rowid'
    ] = (a0, a1) =>
      (_sqlite3_set_last_insert_rowid = Module[
        '_sqlite3_set_last_insert_rowid'
      ] =
        wasmExports['sqlite3_set_last_insert_rowid'])(a0, a1));
    var _sqlite3_changes64 = (Module['_sqlite3_changes64'] = (a0) =>
      (_sqlite3_changes64 = Module['_sqlite3_changes64'] =
        wasmExports['sqlite3_changes64'])(a0));
    var _sqlite3_changes = (Module['_sqlite3_changes'] = (a0) =>
      (_sqlite3_changes = Module['_sqlite3_changes'] =
        wasmExports['sqlite3_changes'])(a0));
    var _sqlite3_total_changes64 = (Module['_sqlite3_total_changes64'] = (a0) =>
      (_sqlite3_total_changes64 = Module['_sqlite3_total_changes64'] =
        wasmExports['sqlite3_total_changes64'])(a0));
    var _sqlite3_total_changes = (Module['_sqlite3_total_changes'] = (a0) =>
      (_sqlite3_total_changes = Module['_sqlite3_total_changes'] =
        wasmExports['sqlite3_total_changes'])(a0));
    var _sqlite3_txn_state = (Module['_sqlite3_txn_state'] = (a0, a1) =>
      (_sqlite3_txn_state = Module['_sqlite3_txn_state'] =
        wasmExports['sqlite3_txn_state'])(a0, a1));
    var _sqlite3_close_v2 = (Module['_sqlite3_close_v2'] = (a0) =>
      (_sqlite3_close_v2 = Module['_sqlite3_close_v2'] =
        wasmExports['sqlite3_close_v2'])(a0));
    var _sqlite3_busy_handler = (Module['_sqlite3_busy_handler'] = (
      a0,
      a1,
      a2,
    ) =>
      (_sqlite3_busy_handler = Module['_sqlite3_busy_handler'] =
        wasmExports['sqlite3_busy_handler'])(a0, a1, a2));
    var _sqlite3_progress_handler = (Module['_sqlite3_progress_handler'] = (
      a0,
      a1,
      a2,
      a3,
    ) =>
      (_sqlite3_progress_handler = Module['_sqlite3_progress_handler'] =
        wasmExports['sqlite3_progress_handler'])(a0, a1, a2, a3));
    var _sqlite3_busy_timeout = (Module['_sqlite3_busy_timeout'] = (a0, a1) =>
      (_sqlite3_busy_timeout = Module['_sqlite3_busy_timeout'] =
        wasmExports['sqlite3_busy_timeout'])(a0, a1));
    var _sqlite3_interrupt = (Module['_sqlite3_interrupt'] = (a0) =>
      (_sqlite3_interrupt = Module['_sqlite3_interrupt'] =
        wasmExports['sqlite3_interrupt'])(a0));
    var _sqlite3_is_interrupted = (Module['_sqlite3_is_interrupted'] = (a0) =>
      (_sqlite3_is_interrupted = Module['_sqlite3_is_interrupted'] =
        wasmExports['sqlite3_is_interrupted'])(a0));
    var _sqlite3_create_function = (Module['_sqlite3_create_function'] = (
      a0,
      a1,
      a2,
      a3,
      a4,
      a5,
      a6,
      a7,
    ) =>
      (_sqlite3_create_function = Module['_sqlite3_create_function'] =
        wasmExports['sqlite3_create_function'])(
        a0,
        a1,
        a2,
        a3,
        a4,
        a5,
        a6,
        a7,
      ));
    var _sqlite3_create_function_v2 = (Module['_sqlite3_create_function_v2'] = (
      a0,
      a1,
      a2,
      a3,
      a4,
      a5,
      a6,
      a7,
      a8,
    ) =>
      (_sqlite3_create_function_v2 = Module['_sqlite3_create_function_v2'] =
        wasmExports['sqlite3_create_function_v2'])(
        a0,
        a1,
        a2,
        a3,
        a4,
        a5,
        a6,
        a7,
        a8,
      ));
    var _sqlite3_create_window_function = (Module[
      '_sqlite3_create_window_function'
    ] = (a0, a1, a2, a3, a4, a5, a6, a7, a8, a9) =>
      (_sqlite3_create_window_function = Module[
        '_sqlite3_create_window_function'
      ] =
        wasmExports['sqlite3_create_window_function'])(
        a0,
        a1,
        a2,
        a3,
        a4,
        a5,
        a6,
        a7,
        a8,
        a9,
      ));
    var _sqlite3_overload_function = (Module['_sqlite3_overload_function'] = (
      a0,
      a1,
      a2,
    ) =>
      (_sqlite3_overload_function = Module['_sqlite3_overload_function'] =
        wasmExports['sqlite3_overload_function'])(a0, a1, a2));
    var _sqlite3_trace_v2 = (Module['_sqlite3_trace_v2'] = (a0, a1, a2, a3) =>
      (_sqlite3_trace_v2 = Module['_sqlite3_trace_v2'] =
        wasmExports['sqlite3_trace_v2'])(a0, a1, a2, a3));
    var _sqlite3_commit_hook = (Module['_sqlite3_commit_hook'] = (a0, a1, a2) =>
      (_sqlite3_commit_hook = Module['_sqlite3_commit_hook'] =
        wasmExports['sqlite3_commit_hook'])(a0, a1, a2));
    var _sqlite3_update_hook = (Module['_sqlite3_update_hook'] = (a0, a1, a2) =>
      (_sqlite3_update_hook = Module['_sqlite3_update_hook'] =
        wasmExports['sqlite3_update_hook'])(a0, a1, a2));
    var _sqlite3_rollback_hook = (Module['_sqlite3_rollback_hook'] = (
      a0,
      a1,
      a2,
    ) =>
      (_sqlite3_rollback_hook = Module['_sqlite3_rollback_hook'] =
        wasmExports['sqlite3_rollback_hook'])(a0, a1, a2));
    var _sqlite3_preupdate_hook = (Module['_sqlite3_preupdate_hook'] = (
      a0,
      a1,
      a2,
    ) =>
      (_sqlite3_preupdate_hook = Module['_sqlite3_preupdate_hook'] =
        wasmExports['sqlite3_preupdate_hook'])(a0, a1, a2));
    var _sqlite3_error_offset = (Module['_sqlite3_error_offset'] = (a0) =>
      (_sqlite3_error_offset = Module['_sqlite3_error_offset'] =
        wasmExports['sqlite3_error_offset'])(a0));
    var _sqlite3_errcode = (Module['_sqlite3_errcode'] = (a0) =>
      (_sqlite3_errcode = Module['_sqlite3_errcode'] =
        wasmExports['sqlite3_errcode'])(a0));
    var _sqlite3_extended_errcode = (Module['_sqlite3_extended_errcode'] = (
      a0,
    ) =>
      (_sqlite3_extended_errcode = Module['_sqlite3_extended_errcode'] =
        wasmExports['sqlite3_extended_errcode'])(a0));
    var _sqlite3_errstr = (Module['_sqlite3_errstr'] = (a0) =>
      (_sqlite3_errstr = Module['_sqlite3_errstr'] =
        wasmExports['sqlite3_errstr'])(a0));
    var _sqlite3_limit = (Module['_sqlite3_limit'] = (a0, a1, a2) =>
      (_sqlite3_limit = Module['_sqlite3_limit'] =
        wasmExports['sqlite3_limit'])(a0, a1, a2));
    var _sqlite3_open = (Module['_sqlite3_open'] = (a0, a1) =>
      (_sqlite3_open = Module['_sqlite3_open'] = wasmExports['sqlite3_open'])(
        a0,
        a1,
      ));
    var _sqlite3_open_v2 = (Module['_sqlite3_open_v2'] = (a0, a1, a2, a3) =>
      (_sqlite3_open_v2 = Module['_sqlite3_open_v2'] =
        wasmExports['sqlite3_open_v2'])(a0, a1, a2, a3));
    var _sqlite3_create_collation = (Module['_sqlite3_create_collation'] = (
      a0,
      a1,
      a2,
      a3,
      a4,
    ) =>
      (_sqlite3_create_collation = Module['_sqlite3_create_collation'] =
        wasmExports['sqlite3_create_collation'])(a0, a1, a2, a3, a4));
    var _sqlite3_create_collation_v2 = (Module['_sqlite3_create_collation_v2'] =
      (a0, a1, a2, a3, a4, a5) =>
        (_sqlite3_create_collation_v2 = Module['_sqlite3_create_collation_v2'] =
          wasmExports['sqlite3_create_collation_v2'])(a0, a1, a2, a3, a4, a5));
    var _sqlite3_collation_needed = (Module['_sqlite3_collation_needed'] = (
      a0,
      a1,
      a2,
    ) =>
      (_sqlite3_collation_needed = Module['_sqlite3_collation_needed'] =
        wasmExports['sqlite3_collation_needed'])(a0, a1, a2));
    var _sqlite3_get_autocommit = (Module['_sqlite3_get_autocommit'] = (a0) =>
      (_sqlite3_get_autocommit = Module['_sqlite3_get_autocommit'] =
        wasmExports['sqlite3_get_autocommit'])(a0));
    var _sqlite3_table_column_metadata = (Module[
      '_sqlite3_table_column_metadata'
    ] = (a0, a1, a2, a3, a4, a5, a6, a7, a8) =>
      (_sqlite3_table_column_metadata = Module[
        '_sqlite3_table_column_metadata'
      ] =
        wasmExports['sqlite3_table_column_metadata'])(
        a0,
        a1,
        a2,
        a3,
        a4,
        a5,
        a6,
        a7,
        a8,
      ));
    var _sqlite3_extended_result_codes = (Module[
      '_sqlite3_extended_result_codes'
    ] = (a0, a1) =>
      (_sqlite3_extended_result_codes = Module[
        '_sqlite3_extended_result_codes'
      ] =
        wasmExports['sqlite3_extended_result_codes'])(a0, a1));
    var _sqlite3_uri_key = (Module['_sqlite3_uri_key'] = (a0, a1) =>
      (_sqlite3_uri_key = Module['_sqlite3_uri_key'] =
        wasmExports['sqlite3_uri_key'])(a0, a1));
    var _sqlite3_uri_int64 = (Module['_sqlite3_uri_int64'] = (a0, a1, a2) =>
      (_sqlite3_uri_int64 = Module['_sqlite3_uri_int64'] =
        wasmExports['sqlite3_uri_int64'])(a0, a1, a2));
    var _sqlite3_db_name = (Module['_sqlite3_db_name'] = (a0, a1) =>
      (_sqlite3_db_name = Module['_sqlite3_db_name'] =
        wasmExports['sqlite3_db_name'])(a0, a1));
    var _sqlite3_db_filename = (Module['_sqlite3_db_filename'] = (a0, a1) =>
      (_sqlite3_db_filename = Module['_sqlite3_db_filename'] =
        wasmExports['sqlite3_db_filename'])(a0, a1));
    var _sqlite3_db_readonly = (Module['_sqlite3_db_readonly'] = (a0, a1) =>
      (_sqlite3_db_readonly = Module['_sqlite3_db_readonly'] =
        wasmExports['sqlite3_db_readonly'])(a0, a1));
    var _sqlite3_compileoption_used = (Module['_sqlite3_compileoption_used'] = (
      a0,
    ) =>
      (_sqlite3_compileoption_used = Module['_sqlite3_compileoption_used'] =
        wasmExports['sqlite3_compileoption_used'])(a0));
    var _sqlite3_compileoption_get = (Module['_sqlite3_compileoption_get'] = (
      a0,
    ) =>
      (_sqlite3_compileoption_get = Module['_sqlite3_compileoption_get'] =
        wasmExports['sqlite3_compileoption_get'])(a0));
    var _sqlite3session_diff = (Module['_sqlite3session_diff'] = (
      a0,
      a1,
      a2,
      a3,
    ) =>
      (_sqlite3session_diff = Module['_sqlite3session_diff'] =
        wasmExports['sqlite3session_diff'])(a0, a1, a2, a3));
    var _sqlite3session_attach = (Module['_sqlite3session_attach'] = (a0, a1) =>
      (_sqlite3session_attach = Module['_sqlite3session_attach'] =
        wasmExports['sqlite3session_attach'])(a0, a1));
    var _sqlite3session_create = (Module['_sqlite3session_create'] = (
      a0,
      a1,
      a2,
    ) =>
      (_sqlite3session_create = Module['_sqlite3session_create'] =
        wasmExports['sqlite3session_create'])(a0, a1, a2));
    var _sqlite3session_delete = (Module['_sqlite3session_delete'] = (a0) =>
      (_sqlite3session_delete = Module['_sqlite3session_delete'] =
        wasmExports['sqlite3session_delete'])(a0));
    var _sqlite3session_table_filter = (Module['_sqlite3session_table_filter'] =
      (a0, a1, a2) =>
        (_sqlite3session_table_filter = Module['_sqlite3session_table_filter'] =
          wasmExports['sqlite3session_table_filter'])(a0, a1, a2));
    var _sqlite3session_changeset = (Module['_sqlite3session_changeset'] = (
      a0,
      a1,
      a2,
    ) =>
      (_sqlite3session_changeset = Module['_sqlite3session_changeset'] =
        wasmExports['sqlite3session_changeset'])(a0, a1, a2));
    var _sqlite3session_changeset_strm = (Module[
      '_sqlite3session_changeset_strm'
    ] = (a0, a1, a2) =>
      (_sqlite3session_changeset_strm = Module[
        '_sqlite3session_changeset_strm'
      ] =
        wasmExports['sqlite3session_changeset_strm'])(a0, a1, a2));
    var _sqlite3session_patchset_strm = (Module[
      '_sqlite3session_patchset_strm'
    ] = (a0, a1, a2) =>
      (_sqlite3session_patchset_strm = Module['_sqlite3session_patchset_strm'] =
        wasmExports['sqlite3session_patchset_strm'])(a0, a1, a2));
    var _sqlite3session_patchset = (Module['_sqlite3session_patchset'] = (
      a0,
      a1,
      a2,
    ) =>
      (_sqlite3session_patchset = Module['_sqlite3session_patchset'] =
        wasmExports['sqlite3session_patchset'])(a0, a1, a2));
    var _sqlite3session_enable = (Module['_sqlite3session_enable'] = (a0, a1) =>
      (_sqlite3session_enable = Module['_sqlite3session_enable'] =
        wasmExports['sqlite3session_enable'])(a0, a1));
    var _sqlite3session_indirect = (Module['_sqlite3session_indirect'] = (
      a0,
      a1,
    ) =>
      (_sqlite3session_indirect = Module['_sqlite3session_indirect'] =
        wasmExports['sqlite3session_indirect'])(a0, a1));
    var _sqlite3session_isempty = (Module['_sqlite3session_isempty'] = (a0) =>
      (_sqlite3session_isempty = Module['_sqlite3session_isempty'] =
        wasmExports['sqlite3session_isempty'])(a0));
    var _sqlite3session_memory_used = (Module['_sqlite3session_memory_used'] = (
      a0,
    ) =>
      (_sqlite3session_memory_used = Module['_sqlite3session_memory_used'] =
        wasmExports['sqlite3session_memory_used'])(a0));
    var _sqlite3session_object_config = (Module[
      '_sqlite3session_object_config'
    ] = (a0, a1, a2) =>
      (_sqlite3session_object_config = Module['_sqlite3session_object_config'] =
        wasmExports['sqlite3session_object_config'])(a0, a1, a2));
    var _sqlite3session_changeset_size = (Module[
      '_sqlite3session_changeset_size'
    ] = (a0) =>
      (_sqlite3session_changeset_size = Module[
        '_sqlite3session_changeset_size'
      ] =
        wasmExports['sqlite3session_changeset_size'])(a0));
    var _sqlite3changeset_start = (Module['_sqlite3changeset_start'] = (
      a0,
      a1,
      a2,
    ) =>
      (_sqlite3changeset_start = Module['_sqlite3changeset_start'] =
        wasmExports['sqlite3changeset_start'])(a0, a1, a2));
    var _sqlite3changeset_start_v2 = (Module['_sqlite3changeset_start_v2'] = (
      a0,
      a1,
      a2,
      a3,
    ) =>
      (_sqlite3changeset_start_v2 = Module['_sqlite3changeset_start_v2'] =
        wasmExports['sqlite3changeset_start_v2'])(a0, a1, a2, a3));
    var _sqlite3changeset_start_strm = (Module['_sqlite3changeset_start_strm'] =
      (a0, a1, a2) =>
        (_sqlite3changeset_start_strm = Module['_sqlite3changeset_start_strm'] =
          wasmExports['sqlite3changeset_start_strm'])(a0, a1, a2));
    var _sqlite3changeset_start_v2_strm = (Module[
      '_sqlite3changeset_start_v2_strm'
    ] = (a0, a1, a2, a3) =>
      (_sqlite3changeset_start_v2_strm = Module[
        '_sqlite3changeset_start_v2_strm'
      ] =
        wasmExports['sqlite3changeset_start_v2_strm'])(a0, a1, a2, a3));
    var _sqlite3changeset_next = (Module['_sqlite3changeset_next'] = (a0) =>
      (_sqlite3changeset_next = Module['_sqlite3changeset_next'] =
        wasmExports['sqlite3changeset_next'])(a0));
    var _sqlite3changeset_op = (Module['_sqlite3changeset_op'] = (
      a0,
      a1,
      a2,
      a3,
      a4,
    ) =>
      (_sqlite3changeset_op = Module['_sqlite3changeset_op'] =
        wasmExports['sqlite3changeset_op'])(a0, a1, a2, a3, a4));
    var _sqlite3changeset_pk = (Module['_sqlite3changeset_pk'] = (a0, a1, a2) =>
      (_sqlite3changeset_pk = Module['_sqlite3changeset_pk'] =
        wasmExports['sqlite3changeset_pk'])(a0, a1, a2));
    var _sqlite3changeset_old = (Module['_sqlite3changeset_old'] = (
      a0,
      a1,
      a2,
    ) =>
      (_sqlite3changeset_old = Module['_sqlite3changeset_old'] =
        wasmExports['sqlite3changeset_old'])(a0, a1, a2));
    var _sqlite3changeset_new = (Module['_sqlite3changeset_new'] = (
      a0,
      a1,
      a2,
    ) =>
      (_sqlite3changeset_new = Module['_sqlite3changeset_new'] =
        wasmExports['sqlite3changeset_new'])(a0, a1, a2));
    var _sqlite3changeset_conflict = (Module['_sqlite3changeset_conflict'] = (
      a0,
      a1,
      a2,
    ) =>
      (_sqlite3changeset_conflict = Module['_sqlite3changeset_conflict'] =
        wasmExports['sqlite3changeset_conflict'])(a0, a1, a2));
    var _sqlite3changeset_fk_conflicts = (Module[
      '_sqlite3changeset_fk_conflicts'
    ] = (a0, a1) =>
      (_sqlite3changeset_fk_conflicts = Module[
        '_sqlite3changeset_fk_conflicts'
      ] =
        wasmExports['sqlite3changeset_fk_conflicts'])(a0, a1));
    var _sqlite3changeset_finalize = (Module['_sqlite3changeset_finalize'] = (
      a0,
    ) =>
      (_sqlite3changeset_finalize = Module['_sqlite3changeset_finalize'] =
        wasmExports['sqlite3changeset_finalize'])(a0));
    var _sqlite3changeset_invert = (Module['_sqlite3changeset_invert'] = (
      a0,
      a1,
      a2,
      a3,
    ) =>
      (_sqlite3changeset_invert = Module['_sqlite3changeset_invert'] =
        wasmExports['sqlite3changeset_invert'])(a0, a1, a2, a3));
    var _sqlite3changeset_invert_strm = (Module[
      '_sqlite3changeset_invert_strm'
    ] = (a0, a1, a2, a3) =>
      (_sqlite3changeset_invert_strm = Module['_sqlite3changeset_invert_strm'] =
        wasmExports['sqlite3changeset_invert_strm'])(a0, a1, a2, a3));
    var _sqlite3changeset_apply_v2 = (Module['_sqlite3changeset_apply_v2'] = (
      a0,
      a1,
      a2,
      a3,
      a4,
      a5,
      a6,
      a7,
      a8,
    ) =>
      (_sqlite3changeset_apply_v2 = Module['_sqlite3changeset_apply_v2'] =
        wasmExports['sqlite3changeset_apply_v2'])(
        a0,
        a1,
        a2,
        a3,
        a4,
        a5,
        a6,
        a7,
        a8,
      ));
    var _sqlite3changeset_apply = (Module['_sqlite3changeset_apply'] = (
      a0,
      a1,
      a2,
      a3,
      a4,
      a5,
    ) =>
      (_sqlite3changeset_apply = Module['_sqlite3changeset_apply'] =
        wasmExports['sqlite3changeset_apply'])(a0, a1, a2, a3, a4, a5));
    var _sqlite3changeset_apply_v2_strm = (Module[
      '_sqlite3changeset_apply_v2_strm'
    ] = (a0, a1, a2, a3, a4, a5, a6, a7, a8) =>
      (_sqlite3changeset_apply_v2_strm = Module[
        '_sqlite3changeset_apply_v2_strm'
      ] =
        wasmExports['sqlite3changeset_apply_v2_strm'])(
        a0,
        a1,
        a2,
        a3,
        a4,
        a5,
        a6,
        a7,
        a8,
      ));
    var _sqlite3changeset_apply_strm = (Module['_sqlite3changeset_apply_strm'] =
      (a0, a1, a2, a3, a4, a5) =>
        (_sqlite3changeset_apply_strm = Module['_sqlite3changeset_apply_strm'] =
          wasmExports['sqlite3changeset_apply_strm'])(a0, a1, a2, a3, a4, a5));
    var _sqlite3changegroup_new = (Module['_sqlite3changegroup_new'] = (a0) =>
      (_sqlite3changegroup_new = Module['_sqlite3changegroup_new'] =
        wasmExports['sqlite3changegroup_new'])(a0));
    var _sqlite3changegroup_add = (Module['_sqlite3changegroup_add'] = (
      a0,
      a1,
      a2,
    ) =>
      (_sqlite3changegroup_add = Module['_sqlite3changegroup_add'] =
        wasmExports['sqlite3changegroup_add'])(a0, a1, a2));
    var _sqlite3changegroup_output = (Module['_sqlite3changegroup_output'] = (
      a0,
      a1,
      a2,
    ) =>
      (_sqlite3changegroup_output = Module['_sqlite3changegroup_output'] =
        wasmExports['sqlite3changegroup_output'])(a0, a1, a2));
    var _sqlite3changegroup_add_strm = (Module['_sqlite3changegroup_add_strm'] =
      (a0, a1, a2) =>
        (_sqlite3changegroup_add_strm = Module['_sqlite3changegroup_add_strm'] =
          wasmExports['sqlite3changegroup_add_strm'])(a0, a1, a2));
    var _sqlite3changegroup_output_strm = (Module[
      '_sqlite3changegroup_output_strm'
    ] = (a0, a1, a2) =>
      (_sqlite3changegroup_output_strm = Module[
        '_sqlite3changegroup_output_strm'
      ] =
        wasmExports['sqlite3changegroup_output_strm'])(a0, a1, a2));
    var _sqlite3changegroup_delete = (Module['_sqlite3changegroup_delete'] = (
      a0,
    ) =>
      (_sqlite3changegroup_delete = Module['_sqlite3changegroup_delete'] =
        wasmExports['sqlite3changegroup_delete'])(a0));
    var _sqlite3changeset_concat = (Module['_sqlite3changeset_concat'] = (
      a0,
      a1,
      a2,
      a3,
      a4,
      a5,
    ) =>
      (_sqlite3changeset_concat = Module['_sqlite3changeset_concat'] =
        wasmExports['sqlite3changeset_concat'])(a0, a1, a2, a3, a4, a5));
    var _sqlite3changeset_concat_strm = (Module[
      '_sqlite3changeset_concat_strm'
    ] = (a0, a1, a2, a3, a4, a5) =>
      (_sqlite3changeset_concat_strm = Module['_sqlite3changeset_concat_strm'] =
        wasmExports['sqlite3changeset_concat_strm'])(a0, a1, a2, a3, a4, a5));
    var _sqlite3session_config = (Module['_sqlite3session_config'] = (a0, a1) =>
      (_sqlite3session_config = Module['_sqlite3session_config'] =
        wasmExports['sqlite3session_config'])(a0, a1));
    var _sqlite3_sourceid = (Module['_sqlite3_sourceid'] = () =>
      (_sqlite3_sourceid = Module['_sqlite3_sourceid'] =
        wasmExports['sqlite3_sourceid'])());
    var _sqlite3__wasm_pstack_ptr = (Module['_sqlite3__wasm_pstack_ptr'] = () =>
      (_sqlite3__wasm_pstack_ptr = Module['_sqlite3__wasm_pstack_ptr'] =
        wasmExports['sqlite3__wasm_pstack_ptr'])());
    var _sqlite3__wasm_pstack_restore = (Module[
      '_sqlite3__wasm_pstack_restore'
    ] = (a0) =>
      (_sqlite3__wasm_pstack_restore = Module['_sqlite3__wasm_pstack_restore'] =
        wasmExports['sqlite3__wasm_pstack_restore'])(a0));
    var _sqlite3__wasm_pstack_alloc = (Module['_sqlite3__wasm_pstack_alloc'] = (
      a0,
    ) =>
      (_sqlite3__wasm_pstack_alloc = Module['_sqlite3__wasm_pstack_alloc'] =
        wasmExports['sqlite3__wasm_pstack_alloc'])(a0));
    var _sqlite3__wasm_pstack_remaining = (Module[
      '_sqlite3__wasm_pstack_remaining'
    ] = () =>
      (_sqlite3__wasm_pstack_remaining = Module[
        '_sqlite3__wasm_pstack_remaining'
      ] =
        wasmExports['sqlite3__wasm_pstack_remaining'])());
    var _sqlite3__wasm_pstack_quota = (Module['_sqlite3__wasm_pstack_quota'] =
      () =>
        (_sqlite3__wasm_pstack_quota = Module['_sqlite3__wasm_pstack_quota'] =
          wasmExports['sqlite3__wasm_pstack_quota'])());
    var _sqlite3__wasm_db_error = (Module['_sqlite3__wasm_db_error'] = (
      a0,
      a1,
      a2,
    ) =>
      (_sqlite3__wasm_db_error = Module['_sqlite3__wasm_db_error'] =
        wasmExports['sqlite3__wasm_db_error'])(a0, a1, a2));
    var _sqlite3__wasm_test_struct = (Module['_sqlite3__wasm_test_struct'] = (
      a0,
    ) =>
      (_sqlite3__wasm_test_struct = Module['_sqlite3__wasm_test_struct'] =
        wasmExports['sqlite3__wasm_test_struct'])(a0));
    var _sqlite3__wasm_enum_json = (Module['_sqlite3__wasm_enum_json'] = () =>
      (_sqlite3__wasm_enum_json = Module['_sqlite3__wasm_enum_json'] =
        wasmExports['sqlite3__wasm_enum_json'])());
    var _sqlite3__wasm_vfs_unlink = (Module['_sqlite3__wasm_vfs_unlink'] = (
      a0,
      a1,
    ) =>
      (_sqlite3__wasm_vfs_unlink = Module['_sqlite3__wasm_vfs_unlink'] =
        wasmExports['sqlite3__wasm_vfs_unlink'])(a0, a1));
    var _sqlite3__wasm_db_vfs = (Module['_sqlite3__wasm_db_vfs'] = (a0, a1) =>
      (_sqlite3__wasm_db_vfs = Module['_sqlite3__wasm_db_vfs'] =
        wasmExports['sqlite3__wasm_db_vfs'])(a0, a1));
    var _sqlite3__wasm_db_reset = (Module['_sqlite3__wasm_db_reset'] = (a0) =>
      (_sqlite3__wasm_db_reset = Module['_sqlite3__wasm_db_reset'] =
        wasmExports['sqlite3__wasm_db_reset'])(a0));
    var _sqlite3__wasm_db_export_chunked = (Module[
      '_sqlite3__wasm_db_export_chunked'
    ] = (a0, a1) =>
      (_sqlite3__wasm_db_export_chunked = Module[
        '_sqlite3__wasm_db_export_chunked'
      ] =
        wasmExports['sqlite3__wasm_db_export_chunked'])(a0, a1));
    var _sqlite3__wasm_db_serialize = (Module['_sqlite3__wasm_db_serialize'] = (
      a0,
      a1,
      a2,
      a3,
      a4,
    ) =>
      (_sqlite3__wasm_db_serialize = Module['_sqlite3__wasm_db_serialize'] =
        wasmExports['sqlite3__wasm_db_serialize'])(a0, a1, a2, a3, a4));
    var _sqlite3__wasm_vfs_create_file = (Module[
      '_sqlite3__wasm_vfs_create_file'
    ] = (a0, a1, a2, a3) =>
      (_sqlite3__wasm_vfs_create_file = Module[
        '_sqlite3__wasm_vfs_create_file'
      ] =
        wasmExports['sqlite3__wasm_vfs_create_file'])(a0, a1, a2, a3));
    var _sqlite3__wasm_posix_create_file = (Module[
      '_sqlite3__wasm_posix_create_file'
    ] = (a0, a1, a2) =>
      (_sqlite3__wasm_posix_create_file = Module[
        '_sqlite3__wasm_posix_create_file'
      ] =
        wasmExports['sqlite3__wasm_posix_create_file'])(a0, a1, a2));
    var _sqlite3__wasm_kvvfsMakeKeyOnPstack = (Module[
      '_sqlite3__wasm_kvvfsMakeKeyOnPstack'
    ] = (a0, a1) =>
      (_sqlite3__wasm_kvvfsMakeKeyOnPstack = Module[
        '_sqlite3__wasm_kvvfsMakeKeyOnPstack'
      ] =
        wasmExports['sqlite3__wasm_kvvfsMakeKeyOnPstack'])(a0, a1));
    var _sqlite3__wasm_kvvfs_methods = (Module['_sqlite3__wasm_kvvfs_methods'] =
      () =>
        (_sqlite3__wasm_kvvfs_methods = Module['_sqlite3__wasm_kvvfs_methods'] =
          wasmExports['sqlite3__wasm_kvvfs_methods'])());
    var _sqlite3__wasm_vtab_config = (Module['_sqlite3__wasm_vtab_config'] = (
      a0,
      a1,
      a2,
    ) =>
      (_sqlite3__wasm_vtab_config = Module['_sqlite3__wasm_vtab_config'] =
        wasmExports['sqlite3__wasm_vtab_config'])(a0, a1, a2));
    var _sqlite3__wasm_db_config_ip = (Module['_sqlite3__wasm_db_config_ip'] = (
      a0,
      a1,
      a2,
      a3,
    ) =>
      (_sqlite3__wasm_db_config_ip = Module['_sqlite3__wasm_db_config_ip'] =
        wasmExports['sqlite3__wasm_db_config_ip'])(a0, a1, a2, a3));
    var _sqlite3__wasm_db_config_pii = (Module['_sqlite3__wasm_db_config_pii'] =
      (a0, a1, a2, a3, a4) =>
        (_sqlite3__wasm_db_config_pii = Module['_sqlite3__wasm_db_config_pii'] =
          wasmExports['sqlite3__wasm_db_config_pii'])(a0, a1, a2, a3, a4));
    var _sqlite3__wasm_db_config_s = (Module['_sqlite3__wasm_db_config_s'] = (
      a0,
      a1,
      a2,
    ) =>
      (_sqlite3__wasm_db_config_s = Module['_sqlite3__wasm_db_config_s'] =
        wasmExports['sqlite3__wasm_db_config_s'])(a0, a1, a2));
    var _sqlite3__wasm_config_i = (Module['_sqlite3__wasm_config_i'] = (
      a0,
      a1,
    ) =>
      (_sqlite3__wasm_config_i = Module['_sqlite3__wasm_config_i'] =
        wasmExports['sqlite3__wasm_config_i'])(a0, a1));
    var _sqlite3__wasm_config_ii = (Module['_sqlite3__wasm_config_ii'] = (
      a0,
      a1,
      a2,
    ) =>
      (_sqlite3__wasm_config_ii = Module['_sqlite3__wasm_config_ii'] =
        wasmExports['sqlite3__wasm_config_ii'])(a0, a1, a2));
    var _sqlite3__wasm_config_j = (Module['_sqlite3__wasm_config_j'] = (
      a0,
      a1,
    ) =>
      (_sqlite3__wasm_config_j = Module['_sqlite3__wasm_config_j'] =
        wasmExports['sqlite3__wasm_config_j'])(a0, a1));
    var _sqlite3__wasm_qfmt_token = (Module['_sqlite3__wasm_qfmt_token'] = (
      a0,
      a1,
    ) =>
      (_sqlite3__wasm_qfmt_token = Module['_sqlite3__wasm_qfmt_token'] =
        wasmExports['sqlite3__wasm_qfmt_token'])(a0, a1));
    var _sqlite3__wasm_init_wasmfs = (Module['_sqlite3__wasm_init_wasmfs'] = (
      a0,
    ) =>
      (_sqlite3__wasm_init_wasmfs = Module['_sqlite3__wasm_init_wasmfs'] =
        wasmExports['sqlite3__wasm_init_wasmfs'])(a0));
    var _sqlite3__wasm_test_intptr = (Module['_sqlite3__wasm_test_intptr'] = (
      a0,
    ) =>
      (_sqlite3__wasm_test_intptr = Module['_sqlite3__wasm_test_intptr'] =
        wasmExports['sqlite3__wasm_test_intptr'])(a0));
    var _sqlite3__wasm_test_voidptr = (Module['_sqlite3__wasm_test_voidptr'] = (
      a0,
    ) =>
      (_sqlite3__wasm_test_voidptr = Module['_sqlite3__wasm_test_voidptr'] =
        wasmExports['sqlite3__wasm_test_voidptr'])(a0));
    var _sqlite3__wasm_test_int64_max = (Module[
      '_sqlite3__wasm_test_int64_max'
    ] = () =>
      (_sqlite3__wasm_test_int64_max = Module['_sqlite3__wasm_test_int64_max'] =
        wasmExports['sqlite3__wasm_test_int64_max'])());
    var _sqlite3__wasm_test_int64_min = (Module[
      '_sqlite3__wasm_test_int64_min'
    ] = () =>
      (_sqlite3__wasm_test_int64_min = Module['_sqlite3__wasm_test_int64_min'] =
        wasmExports['sqlite3__wasm_test_int64_min'])());
    var _sqlite3__wasm_test_int64_times2 = (Module[
      '_sqlite3__wasm_test_int64_times2'
    ] = (a0) =>
      (_sqlite3__wasm_test_int64_times2 = Module[
        '_sqlite3__wasm_test_int64_times2'
      ] =
        wasmExports['sqlite3__wasm_test_int64_times2'])(a0));
    var _sqlite3__wasm_test_int64_minmax = (Module[
      '_sqlite3__wasm_test_int64_minmax'
    ] = (a0, a1) =>
      (_sqlite3__wasm_test_int64_minmax = Module[
        '_sqlite3__wasm_test_int64_minmax'
      ] =
        wasmExports['sqlite3__wasm_test_int64_minmax'])(a0, a1));
    var _sqlite3__wasm_test_int64ptr = (Module['_sqlite3__wasm_test_int64ptr'] =
      (a0) =>
        (_sqlite3__wasm_test_int64ptr = Module['_sqlite3__wasm_test_int64ptr'] =
          wasmExports['sqlite3__wasm_test_int64ptr'])(a0));
    var _sqlite3__wasm_test_stack_overflow = (Module[
      '_sqlite3__wasm_test_stack_overflow'
    ] = (a0) =>
      (_sqlite3__wasm_test_stack_overflow = Module[
        '_sqlite3__wasm_test_stack_overflow'
      ] =
        wasmExports['sqlite3__wasm_test_stack_overflow'])(a0));
    var _sqlite3__wasm_test_str_hello = (Module[
      '_sqlite3__wasm_test_str_hello'
    ] = (a0) =>
      (_sqlite3__wasm_test_str_hello = Module['_sqlite3__wasm_test_str_hello'] =
        wasmExports['sqlite3__wasm_test_str_hello'])(a0));
    var _sqlite3__wasm_SQLTester_strglob = (Module[
      '_sqlite3__wasm_SQLTester_strglob'
    ] = (a0, a1) =>
      (_sqlite3__wasm_SQLTester_strglob = Module[
        '_sqlite3__wasm_SQLTester_strglob'
      ] =
        wasmExports['sqlite3__wasm_SQLTester_strglob'])(a0, a1));
    var _malloc = (Module['_malloc'] = (a0) =>
      (_malloc = Module['_malloc'] = wasmExports['malloc'])(a0));
    var _free = (Module['_free'] = (a0) =>
      (_free = Module['_free'] = wasmExports['free'])(a0));
    var _realloc = (Module['_realloc'] = (a0, a1) =>
      (_realloc = Module['_realloc'] = wasmExports['realloc'])(a0, a1));
    var _emscripten_builtin_memalign = (a0, a1) =>
      (_emscripten_builtin_memalign =
        wasmExports['emscripten_builtin_memalign'])(a0, a1);
    var __emscripten_stack_restore = (a0) =>
      (__emscripten_stack_restore = wasmExports['_emscripten_stack_restore'])(
        a0,
      );
    var __emscripten_stack_alloc = (a0) =>
      (__emscripten_stack_alloc = wasmExports['_emscripten_stack_alloc'])(a0);
    var _emscripten_stack_get_current = () =>
      (_emscripten_stack_get_current =
        wasmExports['emscripten_stack_get_current'])();

    Module['wasmMemory'] = wasmMemory;

    var calledRun;
    var calledPrerun;

    dependenciesFulfilled = function runCaller() {
      if (!calledRun) run();
      if (!calledRun) dependenciesFulfilled = runCaller;
    };

    function run() {
      if (runDependencies > 0) {
        return;
      }

      if (!calledPrerun) {
        calledPrerun = 1;
        preRun();

        if (runDependencies > 0) {
          return;
        }
      }

      function doRun() {
        if (calledRun) return;
        calledRun = 1;
        Module['calledRun'] = 1;

        if (ABORT) return;

        initRuntime();

        readyPromiseResolve(Module);
        Module['onRuntimeInitialized']?.();

        postRun();
      }

      if (Module['setStatus']) {
        Module['setStatus']('Running...');
        setTimeout(() => {
          setTimeout(() => Module['setStatus'](''), 1);
          doRun();
        }, 1);
      } else {
        doRun();
      }
    }

    if (Module['preInit']) {
      if (typeof Module['preInit'] == 'function')
        Module['preInit'] = [Module['preInit']];
      while (Module['preInit'].length > 0) {
        Module['preInit'].pop()();
      }
    }

    run();

    Module.runSQLite3PostLoadInit = function (EmscriptenModule) {
      'use strict';

      'use strict';
      globalThis.sqlite3ApiBootstrap = function sqlite3ApiBootstrap(
        apiConfig = globalThis.sqlite3ApiConfig ||
          sqlite3ApiBootstrap.defaultConfig,
      ) {
        if (sqlite3ApiBootstrap.sqlite3) {
          (sqlite3ApiBootstrap.sqlite3.config || console).warn(
            'sqlite3ApiBootstrap() called multiple times.',
            'Config and external initializers are ignored on calls after the first.',
          );
          return sqlite3ApiBootstrap.sqlite3;
        }
        const config = Object.assign(
          Object.create(null),
          {
            exports: undefined,
            memory: undefined,
            bigIntEnabled: (() => {
              if ('undefined' !== typeof Module) {
                if (!!Module.HEAPU64) return true;
              }
              return !!globalThis.BigInt64Array;
            })(),
            debug: console.debug.bind(console),
            warn: console.warn.bind(console),
            error: console.error.bind(console),
            log: console.log.bind(console),
            wasmfsOpfsDir: '/opfs',

            useStdAlloc: false,
          },
          apiConfig || {},
        );

        Object.assign(
          config,
          {
            allocExportName: config.useStdAlloc ? 'malloc' : 'sqlite3_malloc',
            deallocExportName: config.useStdAlloc ? 'free' : 'sqlite3_free',
            reallocExportName: config.useStdAlloc
              ? 'realloc'
              : 'sqlite3_realloc',
          },
          config,
        );

        ['exports', 'memory', 'wasmfsOpfsDir'].forEach((k) => {
          if ('function' === typeof config[k]) {
            config[k] = config[k]();
          }
        });

        delete globalThis.sqlite3ApiConfig;
        delete sqlite3ApiBootstrap.defaultConfig;

        const capi = Object.create(null);

        const wasm = Object.create(null);

        const __rcStr = (rc) => {
          return (
            (capi.sqlite3_js_rc_str && capi.sqlite3_js_rc_str(rc)) ||
            'Unknown result code #' + rc
          );
        };

        const __isInt = (n) => 'number' === typeof n && n === (n | 0);

        class SQLite3Error extends Error {
          constructor(...args) {
            let rc;
            if (args.length) {
              if (__isInt(args[0])) {
                rc = args[0];
                if (1 === args.length) {
                  super(__rcStr(args[0]));
                } else {
                  const rcStr = __rcStr(rc);
                  if ('object' === typeof args[1]) {
                    super(rcStr, args[1]);
                  } else {
                    args[0] = rcStr + ':';
                    super(args.join(' '));
                  }
                }
              } else {
                if (2 === args.length && 'object' === typeof args[1]) {
                  super(...args);
                } else {
                  super(args.join(' '));
                }
              }
            }
            this.resultCode = rc || capi.SQLITE_ERROR;
            this.name = 'SQLite3Error';
          }
        }

        SQLite3Error.toss = (...args) => {
          throw new SQLite3Error(...args);
        };
        const toss3 = SQLite3Error.toss;

        if (config.wasmfsOpfsDir && !/^\/[^/]+$/.test(config.wasmfsOpfsDir)) {
          toss3(
            "config.wasmfsOpfsDir must be falsy or in the form '/dir-name'.",
          );
        }

        const isInt32 = (n) => {
          return (
            'bigint' !== typeof n &&
            !!(n === (n | 0) && n <= 2147483647 && n >= -2147483648)
          );
        };

        const bigIntFits64 = function f(b) {
          if (!f._max) {
            f._max = BigInt('0x7fffffffffffffff');
            f._min = ~f._max;
          }
          return b >= f._min && b <= f._max;
        };

        const bigIntFits32 = (b) => b >= -0x7fffffffn - 1n && b <= 0x7fffffffn;

        const bigIntFitsDouble = function f(b) {
          if (!f._min) {
            f._min = Number.MIN_SAFE_INTEGER;
            f._max = Number.MAX_SAFE_INTEGER;
          }
          return b >= f._min && b <= f._max;
        };

        const isTypedArray = (v) => {
          return v && v.constructor && isInt32(v.constructor.BYTES_PER_ELEMENT)
            ? v
            : false;
        };

        const __SAB =
          'undefined' === typeof SharedArrayBuffer
            ? function () {}
            : SharedArrayBuffer;

        const isSharedTypedArray = (aTypedArray) =>
          aTypedArray.buffer instanceof __SAB;

        const typedArrayPart = (aTypedArray, begin, end) => {
          return isSharedTypedArray(aTypedArray)
            ? aTypedArray.slice(begin, end)
            : aTypedArray.subarray(begin, end);
        };

        const isBindableTypedArray = (v) => {
          return (
            v &&
            (v instanceof Uint8Array ||
              v instanceof Int8Array ||
              v instanceof ArrayBuffer)
          );
        };

        const isSQLableTypedArray = (v) => {
          return (
            v &&
            (v instanceof Uint8Array ||
              v instanceof Int8Array ||
              v instanceof ArrayBuffer)
          );
        };

        const affirmBindableTypedArray = (v) => {
          return (
            isBindableTypedArray(v) ||
            toss3('Value is not of a supported TypedArray type.')
          );
        };

        const utf8Decoder = new TextDecoder('utf-8');

        const typedArrayToString = function (typedArray, begin, end) {
          return utf8Decoder.decode(typedArrayPart(typedArray, begin, end));
        };

        const flexibleString = function (v) {
          if (isSQLableTypedArray(v)) {
            return typedArrayToString(
              v instanceof ArrayBuffer ? new Uint8Array(v) : v,
            );
          } else if (Array.isArray(v)) return v.join('');
          else if (wasm.isPtr(v)) v = wasm.cstrToJs(v);
          return v;
        };

        class WasmAllocError extends Error {
          constructor(...args) {
            if (2 === args.length && 'object' === typeof args[1]) {
              super(...args);
            } else if (args.length) {
              super(args.join(' '));
            } else {
              super('Allocation failed.');
            }
            this.resultCode = capi.SQLITE_NOMEM;
            this.name = 'WasmAllocError';
          }
        }

        WasmAllocError.toss = (...args) => {
          throw new WasmAllocError(...args);
        };

        Object.assign(capi, {
          sqlite3_bind_blob: undefined,

          sqlite3_bind_text: undefined,

          sqlite3_create_function_v2: (
            pDb,
            funcName,
            nArg,
            eTextRep,
            pApp,
            xFunc,
            xStep,
            xFinal,
            xDestroy,
          ) => {},

          sqlite3_create_function: (
            pDb,
            funcName,
            nArg,
            eTextRep,
            pApp,
            xFunc,
            xStep,
            xFinal,
          ) => {},

          sqlite3_create_window_function: (
            pDb,
            funcName,
            nArg,
            eTextRep,
            pApp,
            xStep,
            xFinal,
            xValue,
            xInverse,
            xDestroy,
          ) => {},

          sqlite3_prepare_v3: (
            dbPtr,
            sql,
            sqlByteLen,
            prepFlags,
            stmtPtrPtr,
            strPtrPtr,
          ) => {},

          sqlite3_prepare_v2: (
            dbPtr,
            sql,
            sqlByteLen,
            stmtPtrPtr,
            strPtrPtr,
          ) => {},

          sqlite3_exec: (pDb, sql, callback, pVoid, pErrMsg) => {},

          sqlite3_randomness: (n, outPtr) => {},
        });

        const util = {
          affirmBindableTypedArray,
          flexibleString,
          bigIntFits32,
          bigIntFits64,
          bigIntFitsDouble,
          isBindableTypedArray,
          isInt32,
          isSQLableTypedArray,
          isTypedArray,
          typedArrayToString,
          isUIThread: () =>
            globalThis.window === globalThis && !!globalThis.document,

          isSharedTypedArray,
          toss: function (...args) {
            throw new Error(args.join(' '));
          },
          toss3,
          typedArrayPart,

          affirmDbHeader: function (bytes) {
            if (bytes instanceof ArrayBuffer) bytes = new Uint8Array(bytes);
            const header = 'SQLite format 3';
            if (header.length > bytes.byteLength) {
              toss3('Input does not contain an SQLite3 database header.');
            }
            for (let i = 0; i < header.length; ++i) {
              if (header.charCodeAt(i) !== bytes[i]) {
                toss3('Input does not contain an SQLite3 database header.');
              }
            }
          },

          affirmIsDb: function (bytes) {
            if (bytes instanceof ArrayBuffer) bytes = new Uint8Array(bytes);
            const n = bytes.byteLength;
            if (n < 512 || n % 512 !== 0) {
              toss3('Byte array size', n, 'is invalid for an SQLite3 db.');
            }
            util.affirmDbHeader(bytes);
          },
        };

        Object.assign(wasm, {
          ptrSizeof: config.wasmPtrSizeof || 4,

          ptrIR: config.wasmPtrIR || 'i32',

          bigIntEnabled: !!config.bigIntEnabled,

          exports:
            config.exports ||
            toss3('Missing API config.exports (WASM module exports).'),

          memory:
            config.memory ||
            config.exports['memory'] ||
            toss3(
              'API config object requires a WebAssembly.Memory object',
              'in either config.exports.memory (exported)',
              'or config.memory (imported).',
            ),

          alloc: undefined,

          realloc: undefined,

          dealloc: undefined,
        });

        wasm.allocFromTypedArray = function (srcTypedArray) {
          if (srcTypedArray instanceof ArrayBuffer) {
            srcTypedArray = new Uint8Array(srcTypedArray);
          }
          affirmBindableTypedArray(srcTypedArray);
          const pRet = wasm.alloc(srcTypedArray.byteLength || 1);
          wasm
            .heapForSize(srcTypedArray.constructor)
            .set(srcTypedArray.byteLength ? srcTypedArray : [0], pRet);
          return pRet;
        };

        {
          const keyAlloc = config.allocExportName,
            keyDealloc = config.deallocExportName,
            keyRealloc = config.reallocExportName;
          for (const key of [keyAlloc, keyDealloc, keyRealloc]) {
            const f = wasm.exports[key];
            if (!(f instanceof Function))
              toss3('Missing required exports[', key, '] function.');
          }

          wasm.alloc = function f(n) {
            return (
              f.impl(n) ||
              WasmAllocError.toss('Failed to allocate', n, ' bytes.')
            );
          };
          wasm.alloc.impl = wasm.exports[keyAlloc];
          wasm.realloc = function f(m, n) {
            const m2 = f.impl(m, n);
            return n
              ? m2 || WasmAllocError.toss('Failed to reallocate', n, ' bytes.')
              : 0;
          };
          wasm.realloc.impl = wasm.exports[keyRealloc];
          wasm.dealloc = wasm.exports[keyDealloc];
        }

        wasm.compileOptionUsed = function f(optName) {
          if (!arguments.length) {
            if (f._result) return f._result;
            else if (!f._opt) {
              f._rx = /^([^=]+)=(.+)/;
              f._rxInt = /^-?\d+$/;
              f._opt = function (opt, rv) {
                const m = f._rx.exec(opt);
                rv[0] = m ? m[1] : opt;
                rv[1] = m ? (f._rxInt.test(m[2]) ? +m[2] : m[2]) : true;
              };
            }
            const rc = {},
              ov = [0, 0];
            let i = 0,
              k;
            while ((k = capi.sqlite3_compileoption_get(i++))) {
              f._opt(k, ov);
              rc[ov[0]] = ov[1];
            }
            return (f._result = rc);
          } else if (Array.isArray(optName)) {
            const rc = {};
            optName.forEach((v) => {
              rc[v] = capi.sqlite3_compileoption_used(v);
            });
            return rc;
          } else if ('object' === typeof optName) {
            Object.keys(optName).forEach((k) => {
              optName[k] = capi.sqlite3_compileoption_used(k);
            });
            return optName;
          }
          return 'string' === typeof optName
            ? !!capi.sqlite3_compileoption_used(optName)
            : false;
        };

        wasm.pstack = Object.assign(Object.create(null), {
          restore: wasm.exports.sqlite3__wasm_pstack_restore,

          alloc: function (n) {
            if ('string' === typeof n && !(n = wasm.sizeofIR(n))) {
              WasmAllocError.toss(
                'Invalid value for pstack.alloc(',
                arguments[0],
                ')',
              );
            }
            return (
              wasm.exports.sqlite3__wasm_pstack_alloc(n) ||
              WasmAllocError.toss(
                'Could not allocate',
                n,
                'bytes from the pstack.',
              )
            );
          },

          allocChunks: function (n, sz) {
            if ('string' === typeof sz && !(sz = wasm.sizeofIR(sz))) {
              WasmAllocError.toss(
                'Invalid size value for allocChunks(',
                arguments[1],
                ')',
              );
            }
            const mem = wasm.pstack.alloc(n * sz);
            const rc = [];
            let i = 0,
              offset = 0;
            for (; i < n; ++i, offset += sz) rc.push(mem + offset);
            return rc;
          },

          allocPtr: (n = 1, safePtrSize = true) => {
            return 1 === n
              ? wasm.pstack.alloc(safePtrSize ? 8 : wasm.ptrSizeof)
              : wasm.pstack.allocChunks(n, safePtrSize ? 8 : wasm.ptrSizeof);
          },

          call: function (f) {
            const stackPos = wasm.pstack.pointer;
            try {
              return f(sqlite3);
            } finally {
              wasm.pstack.restore(stackPos);
            }
          },
        });
        Object.defineProperties(wasm.pstack, {
          pointer: {
            configurable: false,
            iterable: true,
            writeable: false,
            get: wasm.exports.sqlite3__wasm_pstack_ptr,
          },

          quota: {
            configurable: false,
            iterable: true,
            writeable: false,
            get: wasm.exports.sqlite3__wasm_pstack_quota,
          },

          remaining: {
            configurable: false,
            iterable: true,
            writeable: false,
            get: wasm.exports.sqlite3__wasm_pstack_remaining,
          },
        });

        capi.sqlite3_randomness = (...args) => {
          if (
            1 === args.length &&
            util.isTypedArray(args[0]) &&
            1 === args[0].BYTES_PER_ELEMENT
          ) {
            const ta = args[0];
            if (0 === ta.byteLength) {
              wasm.exports.sqlite3_randomness(0, 0);
              return ta;
            }
            const stack = wasm.pstack.pointer;
            try {
              let n = ta.byteLength,
                offset = 0;
              const r = wasm.exports.sqlite3_randomness;
              const heap = wasm.heap8u();
              const nAlloc = n < 512 ? n : 512;
              const ptr = wasm.pstack.alloc(nAlloc);
              do {
                const j = n > nAlloc ? nAlloc : n;
                r(j, ptr);
                ta.set(typedArrayPart(heap, ptr, ptr + j), offset);
                n -= j;
                offset += j;
              } while (n > 0);
            } catch (e) {
              console.error(
                'Highly unexpected (and ignored!) ' +
                  'exception in sqlite3_randomness():',
                e,
              );
            } finally {
              wasm.pstack.restore(stack);
            }
            return ta;
          }
          wasm.exports.sqlite3_randomness(...args);
        };

        let __wasmfsOpfsDir = undefined;

        capi.sqlite3_wasmfs_opfs_dir = function () {
          if (undefined !== __wasmfsOpfsDir) return __wasmfsOpfsDir;

          const pdir = config.wasmfsOpfsDir;
          if (
            !pdir ||
            !globalThis.FileSystemHandle ||
            !globalThis.FileSystemDirectoryHandle ||
            !globalThis.FileSystemFileHandle
          ) {
            return (__wasmfsOpfsDir = '');
          }
          try {
            if (
              pdir &&
              0 ===
                wasm.xCallWrapped(
                  'sqlite3__wasm_init_wasmfs',
                  'i32',
                  ['string'],
                  pdir,
                )
            ) {
              return (__wasmfsOpfsDir = pdir);
            } else {
              return (__wasmfsOpfsDir = '');
            }
          } catch (e) {
            return (__wasmfsOpfsDir = '');
          }
        };

        capi.sqlite3_wasmfs_filename_is_persistent = function (name) {
          const p = capi.sqlite3_wasmfs_opfs_dir();
          return p && name ? name.startsWith(p + '/') : false;
        };

        capi.sqlite3_js_db_uses_vfs = function (pDb, vfsName, dbName = 0) {
          try {
            const pK = capi.sqlite3_vfs_find(vfsName);
            if (!pK) return false;
            else if (!pDb) {
              return pK === capi.sqlite3_vfs_find(0) ? pK : false;
            } else {
              return pK === capi.sqlite3_js_db_vfs(pDb, dbName) ? pK : false;
            }
          } catch (e) {
            return false;
          }
        };

        capi.sqlite3_js_vfs_list = function () {
          const rc = [];
          let pVfs = capi.sqlite3_vfs_find(0);
          while (pVfs) {
            const oVfs = new capi.sqlite3_vfs(pVfs);
            rc.push(wasm.cstrToJs(oVfs.$zName));
            pVfs = oVfs.$pNext;
            oVfs.dispose();
          }
          return rc;
        };

        capi.sqlite3_js_db_export = function (pDb, schema = 0) {
          pDb = wasm.xWrap.testConvertArg('sqlite3*', pDb);
          if (!pDb) toss3('Invalid sqlite3* argument.');
          if (!wasm.bigIntEnabled) toss3('BigInt64 support is not enabled.');
          const scope = wasm.scopedAllocPush();
          let pOut;
          try {
            const pSize = wasm.scopedAlloc(8 + wasm.ptrSizeof);
            const ppOut = pSize + 8;

            const zSchema = schema
              ? wasm.isPtr(schema)
                ? schema
                : wasm.scopedAllocCString('' + schema)
              : 0;
            let rc = wasm.exports.sqlite3__wasm_db_serialize(
              pDb,
              zSchema,
              ppOut,
              pSize,
              0,
            );
            if (rc) {
              toss3(
                'Database serialization failed with code',
                sqlite3.capi.sqlite3_js_rc_str(rc),
              );
            }
            pOut = wasm.peekPtr(ppOut);
            const nOut = wasm.peek(pSize, 'i64');
            rc = nOut
              ? wasm.heap8u().slice(pOut, pOut + Number(nOut))
              : new Uint8Array();
            return rc;
          } finally {
            if (pOut) wasm.exports.sqlite3_free(pOut);
            wasm.scopedAllocPop(scope);
          }
        };

        capi.sqlite3_js_db_vfs = (dbPointer, dbName = 0) =>
          util.sqlite3__wasm_db_vfs(dbPointer, dbName);

        capi.sqlite3_js_aggregate_context = (pCtx, n) => {
          return (
            capi.sqlite3_aggregate_context(pCtx, n) ||
            (n
              ? WasmAllocError.toss(
                  'Cannot allocate',
                  n,
                  'bytes for sqlite3_aggregate_context()',
                )
              : 0)
          );
        };

        capi.sqlite3_js_posix_create_file = function (filename, data, dataLen) {
          let pData;
          if (data && wasm.isPtr(data)) {
            pData = data;
          } else if (
            data instanceof ArrayBuffer ||
            data instanceof Uint8Array
          ) {
            pData = wasm.allocFromTypedArray(data);
            if (arguments.length < 3 || !util.isInt32(dataLen) || dataLen < 0) {
              dataLen = data.byteLength;
            }
          } else {
            SQLite3Error.toss(
              'Invalid 2nd argument for sqlite3_js_posix_create_file().',
            );
          }
          try {
            if (!util.isInt32(dataLen) || dataLen < 0) {
              SQLite3Error.toss(
                'Invalid 3rd argument for sqlite3_js_posix_create_file().',
              );
            }
            const rc = util.sqlite3__wasm_posix_create_file(
              filename,
              pData,
              dataLen,
            );
            if (rc)
              SQLite3Error.toss(
                'Creation of file failed with sqlite3 result code',
                capi.sqlite3_js_rc_str(rc),
              );
          } finally {
            wasm.dealloc(pData);
          }
        };

        capi.sqlite3_js_vfs_create_file = function (
          vfs,
          filename,
          data,
          dataLen,
        ) {
          config.warn(
            'sqlite3_js_vfs_create_file() is deprecated and',
            'should be avoided because it can lead to C-level crashes.',
            'See its documentation for alternative options.',
          );
          let pData;
          if (data) {
            if (wasm.isPtr(data)) {
              pData = data;
            } else if (data instanceof ArrayBuffer) {
              data = new Uint8Array(data);
            }
            if (data instanceof Uint8Array) {
              pData = wasm.allocFromTypedArray(data);
              if (
                arguments.length < 4 ||
                !util.isInt32(dataLen) ||
                dataLen < 0
              ) {
                dataLen = data.byteLength;
              }
            } else {
              SQLite3Error.toss(
                'Invalid 3rd argument type for sqlite3_js_vfs_create_file().',
              );
            }
          } else {
            pData = 0;
          }
          if (!util.isInt32(dataLen) || dataLen < 0) {
            wasm.dealloc(pData);
            SQLite3Error.toss(
              'Invalid 4th argument for sqlite3_js_vfs_create_file().',
            );
          }
          try {
            const rc = util.sqlite3__wasm_vfs_create_file(
              vfs,
              filename,
              pData,
              dataLen,
            );
            if (rc)
              SQLite3Error.toss(
                'Creation of file failed with sqlite3 result code',
                capi.sqlite3_js_rc_str(rc),
              );
          } finally {
            wasm.dealloc(pData);
          }
        };

        capi.sqlite3_js_sql_to_string = (sql) => {
          if ('string' === typeof sql) {
            return sql;
          }
          const x = flexibleString(v);
          return x === v ? undefined : x;
        };

        if (util.isUIThread()) {
          const __kvvfsInfo = function (which) {
            const rc = Object.create(null);
            rc.prefix = 'kvvfs-' + which;
            rc.stores = [];
            if ('session' === which || '' === which)
              rc.stores.push(globalThis.sessionStorage);
            if ('local' === which || '' === which)
              rc.stores.push(globalThis.localStorage);
            return rc;
          };

          capi.sqlite3_js_kvvfs_clear = function (which = '') {
            let rc = 0;
            const kvinfo = __kvvfsInfo(which);
            kvinfo.stores.forEach((s) => {
              const toRm = [];
              let i;
              for (i = 0; i < s.length; ++i) {
                const k = s.key(i);
                if (k.startsWith(kvinfo.prefix)) toRm.push(k);
              }
              toRm.forEach((kk) => s.removeItem(kk));
              rc += toRm.length;
            });
            return rc;
          };

          capi.sqlite3_js_kvvfs_size = function (which = '') {
            let sz = 0;
            const kvinfo = __kvvfsInfo(which);
            kvinfo.stores.forEach((s) => {
              let i;
              for (i = 0; i < s.length; ++i) {
                const k = s.key(i);
                if (k.startsWith(kvinfo.prefix)) {
                  sz += k.length;
                  sz += s.getItem(k).length;
                }
              }
            });
            return sz * 2;
          };
        }

        capi.sqlite3_db_config = function (pDb, op, ...args) {
          switch (op) {
            case capi.SQLITE_DBCONFIG_ENABLE_FKEY:
            case capi.SQLITE_DBCONFIG_ENABLE_TRIGGER:
            case capi.SQLITE_DBCONFIG_ENABLE_FTS3_TOKENIZER:
            case capi.SQLITE_DBCONFIG_ENABLE_LOAD_EXTENSION:
            case capi.SQLITE_DBCONFIG_NO_CKPT_ON_CLOSE:
            case capi.SQLITE_DBCONFIG_ENABLE_QPSG:
            case capi.SQLITE_DBCONFIG_TRIGGER_EQP:
            case capi.SQLITE_DBCONFIG_RESET_DATABASE:
            case capi.SQLITE_DBCONFIG_DEFENSIVE:
            case capi.SQLITE_DBCONFIG_WRITABLE_SCHEMA:
            case capi.SQLITE_DBCONFIG_LEGACY_ALTER_TABLE:
            case capi.SQLITE_DBCONFIG_DQS_DML:
            case capi.SQLITE_DBCONFIG_DQS_DDL:
            case capi.SQLITE_DBCONFIG_ENABLE_VIEW:
            case capi.SQLITE_DBCONFIG_LEGACY_FILE_FORMAT:
            case capi.SQLITE_DBCONFIG_TRUSTED_SCHEMA:
            case capi.SQLITE_DBCONFIG_STMT_SCANSTATUS:
            case capi.SQLITE_DBCONFIG_REVERSE_SCANORDER:
            case capi.SQLITE_DBCONFIG_ENABLE_ATTACH_CREATE:
            case capi.SQLITE_DBCONFIG_ENABLE_ATTACH_WRITE:
            case capi.SQLITE_DBCONFIG_ENABLE_COMMENTS:
              if (!this.ip) {
                this.ip = wasm.xWrap('sqlite3__wasm_db_config_ip', 'int', [
                  'sqlite3*',
                  'int',
                  'int',
                  '*',
                ]);
              }
              return this.ip(pDb, op, args[0], args[1] || 0);
            case capi.SQLITE_DBCONFIG_LOOKASIDE:
              if (!this.pii) {
                this.pii = wasm.xWrap('sqlite3__wasm_db_config_pii', 'int', [
                  'sqlite3*',
                  'int',
                  '*',
                  'int',
                  'int',
                ]);
              }
              return this.pii(pDb, op, args[0], args[1], args[2]);
            case capi.SQLITE_DBCONFIG_MAINDBNAME:
              if (!this.s) {
                this.s = wasm.xWrap('sqlite3__wasm_db_config_s', 'int', [
                  'sqlite3*',
                  'int',
                  'string:static',
                ]);
              }
              return this.s(pDb, op, args[0]);
            default:
              return capi.SQLITE_MISUSE;
          }
        }.bind(Object.create(null));

        capi.sqlite3_value_to_js = function (
          pVal,
          throwIfCannotConvert = true,
        ) {
          let arg;
          const valType = capi.sqlite3_value_type(pVal);
          switch (valType) {
            case capi.SQLITE_INTEGER:
              if (wasm.bigIntEnabled) {
                arg = capi.sqlite3_value_int64(pVal);
                if (util.bigIntFitsDouble(arg)) arg = Number(arg);
              } else arg = capi.sqlite3_value_double(pVal);
              break;
            case capi.SQLITE_FLOAT:
              arg = capi.sqlite3_value_double(pVal);
              break;
            case capi.SQLITE_TEXT:
              arg = capi.sqlite3_value_text(pVal);
              break;
            case capi.SQLITE_BLOB: {
              const n = capi.sqlite3_value_bytes(pVal);
              const pBlob = capi.sqlite3_value_blob(pVal);
              if (n && !pBlob)
                sqlite3.WasmAllocError.toss(
                  'Cannot allocate memory for blob argument of',
                  n,
                  'byte(s)',
                );
              arg = n ? wasm.heap8u().slice(pBlob, pBlob + Number(n)) : null;
              break;
            }
            case capi.SQLITE_NULL:
              arg = null;
              break;
            default:
              if (throwIfCannotConvert) {
                toss3(
                  capi.SQLITE_MISMATCH,
                  'Unhandled sqlite3_value_type():',
                  valType,
                );
              }
              arg = undefined;
          }
          return arg;
        };

        capi.sqlite3_values_to_js = function (
          argc,
          pArgv,
          throwIfCannotConvert = true,
        ) {
          let i;
          const tgt = [];
          for (i = 0; i < argc; ++i) {
            tgt.push(
              capi.sqlite3_value_to_js(
                wasm.peekPtr(pArgv + wasm.ptrSizeof * i),
                throwIfCannotConvert,
              ),
            );
          }
          return tgt;
        };

        capi.sqlite3_result_error_js = function (pCtx, e) {
          if (e instanceof WasmAllocError) {
            capi.sqlite3_result_error_nomem(pCtx);
          } else {
            capi.sqlite3_result_error(pCtx, '' + e, -1);
          }
        };

        capi.sqlite3_result_js = function (pCtx, val) {
          if (val instanceof Error) {
            capi.sqlite3_result_error_js(pCtx, val);
            return;
          }
          try {
            switch (typeof val) {
              case 'undefined':
                break;
              case 'boolean':
                capi.sqlite3_result_int(pCtx, val ? 1 : 0);
                break;
              case 'bigint':
                if (util.bigIntFits32(val)) {
                  capi.sqlite3_result_int(pCtx, Number(val));
                } else if (util.bigIntFitsDouble(val)) {
                  capi.sqlite3_result_double(pCtx, Number(val));
                } else if (wasm.bigIntEnabled) {
                  if (util.bigIntFits64(val))
                    capi.sqlite3_result_int64(pCtx, val);
                  else
                    toss3(
                      'BigInt value',
                      val.toString(),
                      'is too BigInt for int64.',
                    );
                } else {
                  toss3('BigInt value', val.toString(), 'is too BigInt.');
                }
                break;
              case 'number': {
                let f;
                if (util.isInt32(val)) {
                  f = capi.sqlite3_result_int;
                } else if (
                  wasm.bigIntEnabled &&
                  Number.isInteger(val) &&
                  util.bigIntFits64(BigInt(val))
                ) {
                  f = capi.sqlite3_result_int64;
                } else {
                  f = capi.sqlite3_result_double;
                }
                f(pCtx, val);
                break;
              }
              case 'string': {
                const [p, n] = wasm.allocCString(val, true);
                capi.sqlite3_result_text(pCtx, p, n, capi.SQLITE_WASM_DEALLOC);
                break;
              }
              case 'object':
                if (null === val) {
                  capi.sqlite3_result_null(pCtx);
                  break;
                } else if (util.isBindableTypedArray(val)) {
                  const pBlob = wasm.allocFromTypedArray(val);
                  capi.sqlite3_result_blob(
                    pCtx,
                    pBlob,
                    val.byteLength,
                    capi.SQLITE_WASM_DEALLOC,
                  );
                  break;
                }

              default:
                toss3(
                  "Don't not how to handle this UDF result value:",
                  typeof val,
                  val,
                );
            }
          } catch (e) {
            capi.sqlite3_result_error_js(pCtx, e);
          }
        };

        capi.sqlite3_column_js = function (
          pStmt,
          iCol,
          throwIfCannotConvert = true,
        ) {
          const v = capi.sqlite3_column_value(pStmt, iCol);
          return 0 === v
            ? undefined
            : capi.sqlite3_value_to_js(v, throwIfCannotConvert);
        };

        const __newOldValue = function (pObj, iCol, impl) {
          impl = capi[impl];
          if (!this.ptr) this.ptr = wasm.allocPtr();
          else wasm.pokePtr(this.ptr, 0);
          const rc = impl(pObj, iCol, this.ptr);
          if (rc)
            return SQLite3Error.toss(
              rc,
              arguments[2] + '() failed with code ' + rc,
            );
          const pv = wasm.peekPtr(this.ptr);
          return pv ? capi.sqlite3_value_to_js(pv, true) : undefined;
        }.bind(Object.create(null));

        capi.sqlite3_preupdate_new_js = (pDb, iCol) =>
          __newOldValue(pDb, iCol, 'sqlite3_preupdate_new');

        capi.sqlite3_preupdate_old_js = (pDb, iCol) =>
          __newOldValue(pDb, iCol, 'sqlite3_preupdate_old');

        capi.sqlite3changeset_new_js = (pChangesetIter, iCol) =>
          __newOldValue(pChangesetIter, iCol, 'sqlite3changeset_new');

        capi.sqlite3changeset_old_js = (pChangesetIter, iCol) =>
          __newOldValue(pChangesetIter, iCol, 'sqlite3changeset_old');

        const sqlite3 = {
          WasmAllocError: WasmAllocError,
          SQLite3Error: SQLite3Error,
          capi,
          util,
          wasm,
          config,

          version: Object.create(null),

          client: undefined,

          asyncPostInit: async function ff() {
            if (ff.isReady instanceof Promise) return ff.isReady;
            let lia = sqlite3ApiBootstrap.initializersAsync;
            delete sqlite3ApiBootstrap.initializersAsync;
            const postInit = async () => {
              if (!sqlite3.__isUnderTest) {
                delete sqlite3.util;

                delete sqlite3.StructBinder;
              }
              return sqlite3;
            };
            const catcher = (e) => {
              config.error('an async sqlite3 initializer failed:', e);
              throw e;
            };
            if (!lia || !lia.length) {
              return (ff.isReady = postInit().catch(catcher));
            }
            lia = lia.map((f) => {
              return f instanceof Function ? async (x) => f(sqlite3) : f;
            });
            lia.push(postInit);
            let p = Promise.resolve(sqlite3);
            while (lia.length) p = p.then(lia.shift());
            return (ff.isReady = p.catch(catcher));
          },

          scriptInfo: undefined,
        };
        try {
          sqlite3ApiBootstrap.initializers.forEach((f) => {
            f(sqlite3);
          });
        } catch (e) {
          console.error('sqlite3 bootstrap initializer threw:', e);
          throw e;
        }
        delete sqlite3ApiBootstrap.initializers;
        sqlite3ApiBootstrap.sqlite3 = sqlite3;
        return sqlite3;
      };

      globalThis.sqlite3ApiBootstrap.initializers = [];

      globalThis.sqlite3ApiBootstrap.initializersAsync = [];

      globalThis.sqlite3ApiBootstrap.defaultConfig = Object.create(null);

      globalThis.sqlite3ApiBootstrap.sqlite3 = undefined;

      globalThis.WhWasmUtilInstaller = function (target) {
        'use strict';
        if (undefined === target.bigIntEnabled) {
          target.bigIntEnabled = !!globalThis['BigInt64Array'];
        }

        const toss = (...args) => {
          throw new Error(args.join(' '));
        };

        if (!target.exports) {
          Object.defineProperty(target, 'exports', {
            enumerable: true,
            configurable: true,
            get: () => target.instance && target.instance.exports,
          });
        }

        const ptrIR = target.pointerIR || 'i32';
        const ptrSizeof = (target.ptrSizeof =
          'i32' === ptrIR
            ? 4
            : 'i64' === ptrIR
              ? 8
              : toss('Unhandled ptrSizeof:', ptrIR));

        const cache = Object.create(null);

        cache.heapSize = 0;

        cache.memory = null;

        cache.freeFuncIndexes = [];

        cache.scopedAlloc = [];

        cache.utf8Decoder = new TextDecoder();
        cache.utf8Encoder = new TextEncoder('utf-8');

        target.sizeofIR = (n) => {
          switch (n) {
            case 'i8':
              return 1;
            case 'i16':
              return 2;
            case 'i32':
            case 'f32':
            case 'float':
              return 4;
            case 'i64':
            case 'f64':
            case 'double':
              return 8;
            case '*':
              return ptrSizeof;
            default:
              return ('' + n).endsWith('*') ? ptrSizeof : undefined;
          }
        };

        const heapWrappers = function () {
          if (!cache.memory) {
            cache.memory =
              target.memory instanceof WebAssembly.Memory
                ? target.memory
                : target.exports.memory;
          } else if (cache.heapSize === cache.memory.buffer.byteLength) {
            return cache;
          }

          const b = cache.memory.buffer;
          cache.HEAP8 = new Int8Array(b);
          cache.HEAP8U = new Uint8Array(b);
          cache.HEAP16 = new Int16Array(b);
          cache.HEAP16U = new Uint16Array(b);
          cache.HEAP32 = new Int32Array(b);
          cache.HEAP32U = new Uint32Array(b);
          if (target.bigIntEnabled) {
            cache.HEAP64 = new BigInt64Array(b);
            cache.HEAP64U = new BigUint64Array(b);
          }
          cache.HEAP32F = new Float32Array(b);
          cache.HEAP64F = new Float64Array(b);
          cache.heapSize = b.byteLength;
          return cache;
        };

        target.heap8 = () => heapWrappers().HEAP8;

        target.heap8u = () => heapWrappers().HEAP8U;

        target.heap16 = () => heapWrappers().HEAP16;

        target.heap16u = () => heapWrappers().HEAP16U;

        target.heap32 = () => heapWrappers().HEAP32;

        target.heap32u = () => heapWrappers().HEAP32U;

        target.heapForSize = function (n, unsigned = true) {
          let ctor;
          const c =
            cache.memory && cache.heapSize === cache.memory.buffer.byteLength
              ? cache
              : heapWrappers();
          switch (n) {
            case Int8Array:
              return c.HEAP8;
            case Uint8Array:
              return c.HEAP8U;
            case Int16Array:
              return c.HEAP16;
            case Uint16Array:
              return c.HEAP16U;
            case Int32Array:
              return c.HEAP32;
            case Uint32Array:
              return c.HEAP32U;
            case 8:
              return unsigned ? c.HEAP8U : c.HEAP8;
            case 16:
              return unsigned ? c.HEAP16U : c.HEAP16;
            case 32:
              return unsigned ? c.HEAP32U : c.HEAP32;
            case 64:
              if (c.HEAP64) return unsigned ? c.HEAP64U : c.HEAP64;
              break;
            default:
              if (target.bigIntEnabled) {
                if (n === globalThis['BigUint64Array']) return c.HEAP64U;
                else if (n === globalThis['BigInt64Array']) return c.HEAP64;
                break;
              }
          }
          toss(
            'Invalid heapForSize() size: expecting 8, 16, 32,',
            'or (if BigInt is enabled) 64.',
          );
        };

        target.functionTable = function () {
          return target.exports.__indirect_function_table;
        };

        target.functionEntry = function (fptr) {
          const ft = target.functionTable();
          return fptr < ft.length ? ft.get(fptr) : undefined;
        };

        target.jsFuncToWasm = function f(func, sig) {
          if (!f._) {
            f._ = {
              sigTypes: Object.assign(Object.create(null), {
                i: 'i32',
                p: 'i32',
                P: 'i32',
                s: 'i32',
                j: 'i64',
                f: 'f32',
                d: 'f64',
              }),

              typeCodes: Object.assign(Object.create(null), {
                f64: 0x7c,
                f32: 0x7d,
                i64: 0x7e,
                i32: 0x7f,
              }),

              uleb128Encode: function (tgt, method, n) {
                if (n < 128) tgt[method](n);
                else tgt[method](n % 128 | 128, n >> 7);
              },

              rxJSig: /^(\w)\((\w*)\)$/,

              sigParams: function (sig) {
                const m = f._.rxJSig.exec(sig);
                return m ? m[2] : sig.substr(1);
              },

              letterType: (x) =>
                f._.sigTypes[x] || toss('Invalid signature letter:', x),

              pushSigType: (dest, letter) =>
                dest.push(f._.typeCodes[f._.letterType(letter)]),
            };
          }
          if ('string' === typeof func) {
            const x = sig;
            sig = func;
            func = x;
          }
          const sigParams = f._.sigParams(sig);
          const wasmCode = [0x01, 0x60];
          f._.uleb128Encode(wasmCode, 'push', sigParams.length);
          for (const x of sigParams) f._.pushSigType(wasmCode, x);
          if ('v' === sig[0]) wasmCode.push(0);
          else {
            wasmCode.push(1);
            f._.pushSigType(wasmCode, sig[0]);
          }
          f._.uleb128Encode(wasmCode, 'unshift', wasmCode.length);
          wasmCode.unshift(
            0x00,
            0x61,
            0x73,
            0x6d,
            0x01,
            0x00,
            0x00,
            0x00,
            0x01,
          );
          wasmCode.push(
            0x02,
            0x07,

            0x01,
            0x01,
            0x65,
            0x01,
            0x66,
            0x00,
            0x00,
            0x07,
            0x05,

            0x01,
            0x01,
            0x66,
            0x00,
            0x00,
          );
          return new WebAssembly.Instance(
            new WebAssembly.Module(new Uint8Array(wasmCode)),
            {
              e: { f: func },
            },
          ).exports['f'];
        };

        const __installFunction = function f(func, sig, scoped) {
          if (scoped && !cache.scopedAlloc.length) {
            toss('No scopedAllocPush() scope is active.');
          }
          if ('string' === typeof func) {
            const x = sig;
            sig = func;
            func = x;
          }
          if ('string' !== typeof sig || !(func instanceof Function)) {
            toss(
              'Invalid arguments: expecting (function,signature) ' +
                'or (signature,function).',
            );
          }
          const ft = target.functionTable();
          const oldLen = ft.length;
          let ptr;
          while (cache.freeFuncIndexes.length) {
            ptr = cache.freeFuncIndexes.pop();
            if (ft.get(ptr)) {
              ptr = null;
              continue;
            } else {
              break;
            }
          }
          if (!ptr) {
            ptr = oldLen;
            ft.grow(1);
          }
          try {
            ft.set(ptr, func);
            if (scoped) {
              cache.scopedAlloc[cache.scopedAlloc.length - 1].push(ptr);
            }
            return ptr;
          } catch (e) {
            if (!(e instanceof TypeError)) {
              if (ptr === oldLen) cache.freeFuncIndexes.push(oldLen);
              throw e;
            }
          }

          try {
            const fptr = target.jsFuncToWasm(func, sig);
            ft.set(ptr, fptr);
            if (scoped) {
              cache.scopedAlloc[cache.scopedAlloc.length - 1].push(ptr);
            }
          } catch (e) {
            if (ptr === oldLen) cache.freeFuncIndexes.push(oldLen);
            throw e;
          }
          return ptr;
        };

        target.installFunction = (func, sig) =>
          __installFunction(func, sig, false);

        target.scopedInstallFunction = (func, sig) =>
          __installFunction(func, sig, true);

        target.uninstallFunction = function (ptr) {
          if (!ptr && 0 !== ptr) return undefined;
          const fi = cache.freeFuncIndexes;
          const ft = target.functionTable();
          fi.push(ptr);
          const rc = ft.get(ptr);
          ft.set(ptr, null);
          return rc;
        };

        target.peek = function f(ptr, type = 'i8') {
          if (type.endsWith('*')) type = ptrIR;
          const c =
            cache.memory && cache.heapSize === cache.memory.buffer.byteLength
              ? cache
              : heapWrappers();
          const list = Array.isArray(ptr) ? [] : undefined;
          let rc;
          do {
            if (list) ptr = arguments[0].shift();
            switch (type) {
              case 'i1':
              case 'i8':
                rc = c.HEAP8[ptr >> 0];
                break;
              case 'i16':
                rc = c.HEAP16[ptr >> 1];
                break;
              case 'i32':
                rc = c.HEAP32[ptr >> 2];
                break;
              case 'float':
              case 'f32':
                rc = c.HEAP32F[ptr >> 2];
                break;
              case 'double':
              case 'f64':
                rc = Number(c.HEAP64F[ptr >> 3]);
                break;
              case 'i64':
                if (target.bigIntEnabled) {
                  rc = BigInt(c.HEAP64[ptr >> 3]);
                  break;
                }

              default:
                toss('Invalid type for peek():', type);
            }
            if (list) list.push(rc);
          } while (list && arguments[0].length);
          return list || rc;
        };

        target.poke = function (ptr, value, type = 'i8') {
          if (type.endsWith('*')) type = ptrIR;
          const c =
            cache.memory && cache.heapSize === cache.memory.buffer.byteLength
              ? cache
              : heapWrappers();
          for (const p of Array.isArray(ptr) ? ptr : [ptr]) {
            switch (type) {
              case 'i1':
              case 'i8':
                c.HEAP8[p >> 0] = value;
                continue;
              case 'i16':
                c.HEAP16[p >> 1] = value;
                continue;
              case 'i32':
                c.HEAP32[p >> 2] = value;
                continue;
              case 'float':
              case 'f32':
                c.HEAP32F[p >> 2] = value;
                continue;
              case 'double':
              case 'f64':
                c.HEAP64F[p >> 3] = value;
                continue;
              case 'i64':
                if (c.HEAP64) {
                  c.HEAP64[p >> 3] = BigInt(value);
                  continue;
                }

              default:
                toss('Invalid type for poke(): ' + type);
            }
          }
          return this;
        };

        target.peekPtr = (...ptr) =>
          target.peek(1 === ptr.length ? ptr[0] : ptr, ptrIR);

        target.pokePtr = (ptr, value = 0) => target.poke(ptr, value, ptrIR);

        target.peek8 = (...ptr) =>
          target.peek(1 === ptr.length ? ptr[0] : ptr, 'i8');

        target.poke8 = (ptr, value) => target.poke(ptr, value, 'i8');

        target.peek16 = (...ptr) =>
          target.peek(1 === ptr.length ? ptr[0] : ptr, 'i16');

        target.poke16 = (ptr, value) => target.poke(ptr, value, 'i16');

        target.peek32 = (...ptr) =>
          target.peek(1 === ptr.length ? ptr[0] : ptr, 'i32');

        target.poke32 = (ptr, value) => target.poke(ptr, value, 'i32');

        target.peek64 = (...ptr) =>
          target.peek(1 === ptr.length ? ptr[0] : ptr, 'i64');

        target.poke64 = (ptr, value) => target.poke(ptr, value, 'i64');

        target.peek32f = (...ptr) =>
          target.peek(1 === ptr.length ? ptr[0] : ptr, 'f32');

        target.poke32f = (ptr, value) => target.poke(ptr, value, 'f32');

        target.peek64f = (...ptr) =>
          target.peek(1 === ptr.length ? ptr[0] : ptr, 'f64');

        target.poke64f = (ptr, value) => target.poke(ptr, value, 'f64');

        target.getMemValue = target.peek;

        target.getPtrValue = target.peekPtr;

        target.setMemValue = target.poke;

        target.setPtrValue = target.pokePtr;

        target.isPtr32 = (ptr) =>
          'number' === typeof ptr && ptr === (ptr | 0) && ptr >= 0;

        target.isPtr = target.isPtr32;

        target.cstrlen = function (ptr) {
          if (!ptr || !target.isPtr(ptr)) return null;
          const h = heapWrappers().HEAP8U;
          let pos = ptr;
          for (; h[pos] !== 0; ++pos) {}
          return pos - ptr;
        };

        const __SAB =
          'undefined' === typeof SharedArrayBuffer
            ? function () {}
            : SharedArrayBuffer;
        const __utf8Decode = function (arrayBuffer, begin, end) {
          return cache.utf8Decoder.decode(
            arrayBuffer.buffer instanceof __SAB
              ? arrayBuffer.slice(begin, end)
              : arrayBuffer.subarray(begin, end),
          );
        };

        target.cstrToJs = function (ptr) {
          const n = target.cstrlen(ptr);
          return n
            ? __utf8Decode(heapWrappers().HEAP8U, ptr, ptr + n)
            : null === n
              ? n
              : '';
        };

        target.jstrlen = function (str) {
          if ('string' !== typeof str) return null;
          const n = str.length;
          let len = 0;
          for (let i = 0; i < n; ++i) {
            let u = str.charCodeAt(i);
            if (u >= 0xd800 && u <= 0xdfff) {
              u =
                (0x10000 + ((u & 0x3ff) << 10)) | (str.charCodeAt(++i) & 0x3ff);
            }
            if (u <= 0x7f) ++len;
            else if (u <= 0x7ff) len += 2;
            else if (u <= 0xffff) len += 3;
            else len += 4;
          }
          return len;
        };

        target.jstrcpy = function (
          jstr,
          tgt,
          offset = 0,
          maxBytes = -1,
          addNul = true,
        ) {
          if (
            !tgt ||
            (!(tgt instanceof Int8Array) && !(tgt instanceof Uint8Array))
          ) {
            toss('jstrcpy() target must be an Int8Array or Uint8Array.');
          }
          if (maxBytes < 0) maxBytes = tgt.length - offset;
          if (!(maxBytes > 0) || !(offset >= 0)) return 0;
          let i = 0,
            max = jstr.length;
          const begin = offset,
            end = offset + maxBytes - (addNul ? 1 : 0);
          for (; i < max && offset < end; ++i) {
            let u = jstr.charCodeAt(i);
            if (u >= 0xd800 && u <= 0xdfff) {
              u =
                (0x10000 + ((u & 0x3ff) << 10)) |
                (jstr.charCodeAt(++i) & 0x3ff);
            }
            if (u <= 0x7f) {
              if (offset >= end) break;
              tgt[offset++] = u;
            } else if (u <= 0x7ff) {
              if (offset + 1 >= end) break;
              tgt[offset++] = 0xc0 | (u >> 6);
              tgt[offset++] = 0x80 | (u & 0x3f);
            } else if (u <= 0xffff) {
              if (offset + 2 >= end) break;
              tgt[offset++] = 0xe0 | (u >> 12);
              tgt[offset++] = 0x80 | ((u >> 6) & 0x3f);
              tgt[offset++] = 0x80 | (u & 0x3f);
            } else {
              if (offset + 3 >= end) break;
              tgt[offset++] = 0xf0 | (u >> 18);
              tgt[offset++] = 0x80 | ((u >> 12) & 0x3f);
              tgt[offset++] = 0x80 | ((u >> 6) & 0x3f);
              tgt[offset++] = 0x80 | (u & 0x3f);
            }
          }
          if (addNul) tgt[offset++] = 0;
          return offset - begin;
        };

        target.cstrncpy = function (tgtPtr, srcPtr, n) {
          if (!tgtPtr || !srcPtr)
            toss('cstrncpy() does not accept NULL strings.');
          if (n < 0) n = target.cstrlen(strPtr) + 1;
          else if (!(n > 0)) return 0;
          const heap = target.heap8u();
          let i = 0,
            ch;
          for (; i < n && (ch = heap[srcPtr + i]); ++i) {
            heap[tgtPtr + i] = ch;
          }
          if (i < n) heap[tgtPtr + i++] = 0;
          return i;
        };

        target.jstrToUintArray = (str, addNul = false) => {
          return cache.utf8Encoder.encode(addNul ? str + '\0' : str);
        };

        const __affirmAlloc = (obj, funcName) => {
          if (
            !(obj.alloc instanceof Function) ||
            !(obj.dealloc instanceof Function)
          ) {
            toss(
              'Object is missing alloc() and/or dealloc() function(s)',
              'required by',
              funcName + '().',
            );
          }
        };

        const __allocCStr = function (
          jstr,
          returnWithLength,
          allocator,
          funcName,
        ) {
          __affirmAlloc(target, funcName);
          if ('string' !== typeof jstr) return null;
          if (0) {
            const n = target.jstrlen(jstr),
              ptr = allocator(n + 1);
            target.jstrcpy(jstr, target.heap8u(), ptr, n + 1, true);
            return returnWithLength ? [ptr, n] : ptr;
          } else {
            const u = cache.utf8Encoder.encode(jstr),
              ptr = allocator(u.length + 1),
              heap = heapWrappers().HEAP8U;
            heap.set(u, ptr);
            heap[ptr + u.length] = 0;
            return returnWithLength ? [ptr, u.length] : ptr;
          }
        };

        target.allocCString = (jstr, returnWithLength = false) =>
          __allocCStr(jstr, returnWithLength, target.alloc, 'allocCString()');

        target.scopedAllocPush = function () {
          __affirmAlloc(target, 'scopedAllocPush');
          const a = [];
          cache.scopedAlloc.push(a);
          return a;
        };

        target.scopedAllocPop = function (state) {
          __affirmAlloc(target, 'scopedAllocPop');
          const n = arguments.length
            ? cache.scopedAlloc.indexOf(state)
            : cache.scopedAlloc.length - 1;
          if (n < 0) toss('Invalid state object for scopedAllocPop().');
          if (0 === arguments.length) state = cache.scopedAlloc[n];
          cache.scopedAlloc.splice(n, 1);
          for (let p; (p = state.pop()); ) {
            if (target.functionEntry(p)) {
              target.uninstallFunction(p);
            } else target.dealloc(p);
          }
        };

        target.scopedAlloc = function (n) {
          if (!cache.scopedAlloc.length) {
            toss('No scopedAllocPush() scope is active.');
          }
          const p = target.alloc(n);
          cache.scopedAlloc[cache.scopedAlloc.length - 1].push(p);
          return p;
        };

        Object.defineProperty(target.scopedAlloc, 'level', {
          configurable: false,
          enumerable: false,
          get: () => cache.scopedAlloc.length,
          set: () => toss("The 'active' property is read-only."),
        });

        target.scopedAllocCString = (jstr, returnWithLength = false) =>
          __allocCStr(
            jstr,
            returnWithLength,
            target.scopedAlloc,
            'scopedAllocCString()',
          );

        const __allocMainArgv = function (isScoped, list) {
          const pList = target[isScoped ? 'scopedAlloc' : 'alloc'](
            (list.length + 1) * target.ptrSizeof,
          );
          let i = 0;
          list.forEach((e) => {
            target.pokePtr(
              pList + target.ptrSizeof * i++,
              target[isScoped ? 'scopedAllocCString' : 'allocCString']('' + e),
            );
          });
          target.pokePtr(pList + target.ptrSizeof * i, 0);
          return pList;
        };

        target.scopedAllocMainArgv = (list) => __allocMainArgv(true, list);

        target.allocMainArgv = (list) => __allocMainArgv(false, list);

        target.cArgvToJs = (argc, pArgv) => {
          const list = [];
          for (let i = 0; i < argc; ++i) {
            const arg = target.peekPtr(pArgv + target.ptrSizeof * i);
            list.push(arg ? target.cstrToJs(arg) : null);
          }
          return list;
        };

        target.scopedAllocCall = function (func) {
          target.scopedAllocPush();
          try {
            return func();
          } finally {
            target.scopedAllocPop();
          }
        };

        const __allocPtr = function (howMany, safePtrSize, method) {
          __affirmAlloc(target, method);
          const pIr = safePtrSize ? 'i64' : ptrIR;
          let m = target[method](howMany * (safePtrSize ? 8 : ptrSizeof));
          target.poke(m, 0, pIr);
          if (1 === howMany) {
            return m;
          }
          const a = [m];
          for (let i = 1; i < howMany; ++i) {
            m += safePtrSize ? 8 : ptrSizeof;
            a[i] = m;
            target.poke(m, 0, pIr);
          }
          return a;
        };

        target.allocPtr = (howMany = 1, safePtrSize = true) =>
          __allocPtr(howMany, safePtrSize, 'alloc');

        target.scopedAllocPtr = (howMany = 1, safePtrSize = true) =>
          __allocPtr(howMany, safePtrSize, 'scopedAlloc');

        target.xGet = function (name) {
          return (
            target.exports[name] || toss('Cannot find exported symbol:', name)
          );
        };

        const __argcMismatch = (f, n) =>
          toss(f + '() requires', n, 'argument(s).');

        target.xCall = function (fname, ...args) {
          const f = fname instanceof Function ? fname : target.xGet(fname);
          if (!(f instanceof Function))
            toss('Exported symbol', fname, 'is not a function.');
          if (f.length !== args.length)
            __argcMismatch(f === fname ? f.name : fname, f.length);
          return 2 === arguments.length && Array.isArray(arguments[1])
            ? f.apply(null, arguments[1])
            : f.apply(null, args);
        };

        cache.xWrap = Object.create(null);
        cache.xWrap.convert = Object.create(null);

        cache.xWrap.convert.arg = new Map();

        cache.xWrap.convert.result = new Map();
        const xArg = cache.xWrap.convert.arg,
          xResult = cache.xWrap.convert.result;

        if (target.bigIntEnabled) {
          xArg.set('i64', (i) => BigInt(i));
        }
        const __xArgPtr =
          'i32' === ptrIR ? (i) => i | 0 : (i) => BigInt(i) | BigInt(0);
        xArg
          .set('i32', __xArgPtr)
          .set('i16', (i) => (i | 0) & 0xffff)
          .set('i8', (i) => (i | 0) & 0xff)
          .set('f32', (i) => Number(i).valueOf())
          .set('float', xArg.get('f32'))
          .set('f64', xArg.get('f32'))
          .set('double', xArg.get('f64'))
          .set('int', xArg.get('i32'))
          .set('null', (i) => i)
          .set(null, xArg.get('null'))
          .set('**', __xArgPtr)
          .set('*', __xArgPtr);
        xResult
          .set('*', __xArgPtr)
          .set('pointer', __xArgPtr)
          .set('number', (v) => Number(v))
          .set('void', (v) => undefined)
          .set('null', (v) => v)
          .set(null, xResult.get('null'));

        {
          const copyToResult = [
            'i8',
            'i16',
            'i32',
            'int',
            'f32',
            'float',
            'f64',
            'double',
          ];
          if (target.bigIntEnabled) copyToResult.push('i64');
          const adaptPtr = xArg.get(ptrIR);
          for (const t of copyToResult) {
            xArg.set(t + '*', adaptPtr);
            xResult.set(t + '*', adaptPtr);
            xResult.set(t, xArg.get(t) || toss('Missing arg converter:', t));
          }
        }

        const __xArgString = function (v) {
          if ('string' === typeof v) return target.scopedAllocCString(v);
          return v ? __xArgPtr(v) : null;
        };
        xArg
          .set('string', __xArgString)
          .set('utf8', __xArgString)
          .set('pointer', __xArgString);

        xResult
          .set('string', (i) => target.cstrToJs(i))
          .set('utf8', xResult.get('string'))
          .set('string:dealloc', (i) => {
            try {
              return i ? target.cstrToJs(i) : null;
            } finally {
              target.dealloc(i);
            }
          })
          .set('utf8:dealloc', xResult.get('string:dealloc'))
          .set('json', (i) => JSON.parse(target.cstrToJs(i)))
          .set('json:dealloc', (i) => {
            try {
              return i ? JSON.parse(target.cstrToJs(i)) : null;
            } finally {
              target.dealloc(i);
            }
          });

        const AbstractArgAdapter = class {
          constructor(opt) {
            this.name = opt.name || 'unnamed adapter';
          }

          convertArg(v, argv, argIndex) {
            toss('AbstractArgAdapter must be subclassed.');
          }
        };

        xArg.FuncPtrAdapter = class FuncPtrAdapter extends AbstractArgAdapter {
          constructor(opt) {
            super(opt);
            if (xArg.FuncPtrAdapter.warnOnUse) {
              console.warn(
                'xArg.FuncPtrAdapter is an internal-only API',
                'and is not intended to be invoked from',
                'client-level code. Invoked with:',
                opt,
              );
            }
            this.name = opt.name || 'unnamed';
            this.signature = opt.signature;
            if (opt.contextKey instanceof Function) {
              this.contextKey = opt.contextKey;
              if (!opt.bindScope) opt.bindScope = 'context';
            }
            this.bindScope =
              opt.bindScope ||
              toss(
                'FuncPtrAdapter options requires a bindScope (explicit or implied).',
              );
            if (FuncPtrAdapter.bindScopes.indexOf(opt.bindScope) < 0) {
              toss(
                'Invalid options.bindScope (' +
                  opt.bindMod +
                  ') for FuncPtrAdapter. ' +
                  'Expecting one of: (' +
                  FuncPtrAdapter.bindScopes.join(', ') +
                  ')',
              );
            }
            this.isTransient = 'transient' === this.bindScope;
            this.isContext = 'context' === this.bindScope;
            this.isPermanent = 'permanent' === this.bindScope;
            this.singleton = 'singleton' === this.bindScope ? [] : undefined;

            this.callProxy =
              opt.callProxy instanceof Function ? opt.callProxy : undefined;
          }

          contextKey(argv, argIndex) {
            return this;
          }

          contextMap(key) {
            const cm = this.__cmap || (this.__cmap = new Map());
            let rc = cm.get(key);
            if (undefined === rc) cm.set(key, (rc = []));
            return rc;
          }

          convertArg(v, argv, argIndex) {
            let pair = this.singleton;
            if (!pair && this.isContext) {
              pair = this.contextMap(this.contextKey(argv, argIndex));
            }
            if (pair && pair[0] === v) return pair[1];
            if (v instanceof Function) {
              if (this.callProxy) v = this.callProxy(v);
              const fp = __installFunction(v, this.signature, this.isTransient);
              if (FuncPtrAdapter.debugFuncInstall) {
                FuncPtrAdapter.debugOut(
                  'FuncPtrAdapter installed',
                  this,
                  this.contextKey(argv, argIndex),
                  '@' + fp,
                  v,
                );
              }
              if (pair) {
                if (pair[1]) {
                  if (FuncPtrAdapter.debugFuncInstall) {
                    FuncPtrAdapter.debugOut(
                      'FuncPtrAdapter uninstalling',
                      this,
                      this.contextKey(argv, argIndex),
                      '@' + pair[1],
                      v,
                    );
                  }
                  try {
                    cache.scopedAlloc[cache.scopedAlloc.length - 1].push(
                      pair[1],
                    );
                  } catch (e) {}
                }
                pair[0] = v;
                pair[1] = fp;
              }
              return fp;
            } else if (target.isPtr(v) || null === v || undefined === v) {
              if (pair && pair[1] && pair[1] !== v) {
                if (FuncPtrAdapter.debugFuncInstall) {
                  FuncPtrAdapter.debugOut(
                    'FuncPtrAdapter uninstalling',
                    this,
                    this.contextKey(argv, argIndex),
                    '@' + pair[1],
                    v,
                  );
                }
                try {
                  cache.scopedAlloc[cache.scopedAlloc.length - 1].push(pair[1]);
                } catch (e) {}
                pair[0] = pair[1] = v | 0;
              }
              return v || 0;
            } else {
              throw new TypeError(
                'Invalid FuncPtrAdapter argument type. ' +
                  'Expecting a function pointer or a ' +
                  (this.name ? this.name + ' ' : '') +
                  'function matching signature ' +
                  this.signature +
                  '.',
              );
            }
          }
        };

        xArg.FuncPtrAdapter.warnOnUse = false;

        xArg.FuncPtrAdapter.debugFuncInstall = false;

        xArg.FuncPtrAdapter.debugOut = console.debug.bind(console);

        xArg.FuncPtrAdapter.bindScopes = [
          'transient',
          'context',
          'singleton',
          'permanent',
        ];

        const __xArgAdapterCheck = (t) =>
          xArg.get(t) || toss('Argument adapter not found:', t);

        const __xResultAdapterCheck = (t) =>
          xResult.get(t) || toss('Result adapter not found:', t);

        cache.xWrap.convertArg = (t, ...args) => __xArgAdapterCheck(t)(...args);

        cache.xWrap.convertArgNoCheck = (t, ...args) => xArg.get(t)(...args);

        cache.xWrap.convertResult = (t, v) =>
          null === t ? v : t ? __xResultAdapterCheck(t)(v) : undefined;

        cache.xWrap.convertResultNoCheck = (t, v) =>
          null === t ? v : t ? xResult.get(t)(v) : undefined;

        target.xWrap = function (fArg, resultType, ...argTypes) {
          if (3 === arguments.length && Array.isArray(arguments[2])) {
            argTypes = arguments[2];
          }
          if (target.isPtr(fArg)) {
            fArg =
              target.functionEntry(fArg) ||
              toss('Function pointer not found in WASM function table.');
          }
          const fIsFunc = fArg instanceof Function;
          const xf = fIsFunc ? fArg : target.xGet(fArg);
          if (fIsFunc) fArg = xf.name || 'unnamed function';
          if (argTypes.length !== xf.length) __argcMismatch(fArg, xf.length);
          if (null === resultType && 0 === xf.length) {
            return xf;
          }
          if (undefined !== resultType && null !== resultType)
            __xResultAdapterCheck(resultType);
          for (const t of argTypes) {
            if (t instanceof AbstractArgAdapter)
              xArg.set(t, (...args) => t.convertArg(...args));
            else __xArgAdapterCheck(t);
          }
          const cxw = cache.xWrap;
          if (0 === xf.length) {
            return (...args) =>
              args.length
                ? __argcMismatch(fArg, xf.length)
                : cxw.convertResult(resultType, xf.call(null));
          }
          return function (...args) {
            if (args.length !== xf.length) __argcMismatch(fArg, xf.length);
            const scope = target.scopedAllocPush();
            try {
              let i = 0;
              for (; i < args.length; ++i)
                args[i] = cxw.convertArgNoCheck(argTypes[i], args[i], args, i);
              return cxw.convertResultNoCheck(resultType, xf.apply(null, args));
            } finally {
              target.scopedAllocPop(scope);
            }
          };
        };

        const __xAdapter = function (
          func,
          argc,
          typeName,
          adapter,
          modeName,
          xcvPart,
        ) {
          if ('string' === typeof typeName) {
            if (1 === argc) return xcvPart.get(typeName);
            else if (2 === argc) {
              if (!adapter) {
                xcvPart.delete(typeName);
                return func;
              } else if (!(adapter instanceof Function)) {
                toss(modeName, 'requires a function argument.');
              }
              xcvPart.set(typeName, adapter);
              return func;
            }
          }
          toss('Invalid arguments to', modeName);
        };

        target.xWrap.resultAdapter = function f(typeName, adapter) {
          return __xAdapter(
            f,
            arguments.length,
            typeName,
            adapter,
            'resultAdapter()',
            xResult,
          );
        };

        target.xWrap.argAdapter = function f(typeName, adapter) {
          return __xAdapter(
            f,
            arguments.length,
            typeName,
            adapter,
            'argAdapter()',
            xArg,
          );
        };

        target.xWrap.FuncPtrAdapter = xArg.FuncPtrAdapter;

        target.xCallWrapped = function (fArg, resultType, argTypes, ...args) {
          if (Array.isArray(arguments[3])) args = arguments[3];
          return target
            .xWrap(fArg, resultType, argTypes || [])
            .apply(null, args || []);
        };

        target.xWrap.testConvertArg = cache.xWrap.convertArg;

        target.xWrap.testConvertResult = cache.xWrap.convertResult;

        return target;
      };

      globalThis.WhWasmUtilInstaller.yawl = function (config) {
        const wfetch = () => fetch(config.uri, { credentials: 'same-origin' });
        const wui = this;
        const finalThen = function (arg) {
          if (config.wasmUtilTarget) {
            const toss = (...args) => {
              throw new Error(args.join(' '));
            };
            const tgt = config.wasmUtilTarget;
            tgt.module = arg.module;
            tgt.instance = arg.instance;

            if (!tgt.instance.exports.memory) {
              tgt.memory =
                (config.imports &&
                  config.imports.env &&
                  config.imports.env.memory) ||
                toss("Missing 'memory' object!");
            }
            if (!tgt.alloc && arg.instance.exports.malloc) {
              const exports = arg.instance.exports;
              tgt.alloc = function (n) {
                return (
                  exports.malloc(n) || toss('Allocation of', n, 'bytes failed.')
                );
              };
              tgt.dealloc = function (m) {
                exports.free(m);
              };
            }
            wui(tgt);
          }
          if (config.onload) config.onload(arg, config);
          return arg;
        };
        const loadWasm = WebAssembly.instantiateStreaming
          ? function loadWasmStreaming() {
              return WebAssembly.instantiateStreaming(
                wfetch(),
                config.imports || {},
              ).then(finalThen);
            }
          : function loadWasmOldSchool() {
              return wfetch()
                .then((response) => response.arrayBuffer())
                .then((bytes) =>
                  WebAssembly.instantiate(bytes, config.imports || {}),
                )
                .then(finalThen);
            };
        return loadWasm;
      }.bind(globalThis.WhWasmUtilInstaller);

      ('use strict');
      globalThis.Jaccwabyt = function StructBinderFactory(config) {
        const toss = (...args) => {
          throw new Error(args.join(' '));
        };

        if (
          !(config.heap instanceof WebAssembly.Memory) &&
          !(config.heap instanceof Function)
        ) {
          toss(
            'config.heap must be WebAssembly.Memory instance or a function.',
          );
        }
        ['alloc', 'dealloc'].forEach(function (k) {
          config[k] instanceof Function ||
            toss("Config option '" + k + "' must be a function.");
        });
        const SBF = StructBinderFactory;
        const heap =
            config.heap instanceof Function
              ? config.heap
              : () => new Uint8Array(config.heap.buffer),
          alloc = config.alloc,
          dealloc = config.dealloc,
          log = config.log || console.log.bind(console),
          memberPrefix = config.memberPrefix || '',
          memberSuffix = config.memberSuffix || '',
          bigIntEnabled =
            undefined === config.bigIntEnabled
              ? !!globalThis['BigInt64Array']
              : !!config.bigIntEnabled,
          BigInt = globalThis['BigInt'],
          BigInt64Array = globalThis['BigInt64Array'],
          ptrSizeof = config.ptrSizeof || 4,
          ptrIR = config.ptrIR || 'i32';
        if (!SBF.debugFlags) {
          SBF.__makeDebugFlags = function (deriveFrom = null) {
            if (deriveFrom && deriveFrom.__flags)
              deriveFrom = deriveFrom.__flags;
            const f = function f(flags) {
              if (0 === arguments.length) {
                return f.__flags;
              }
              if (flags < 0) {
                delete f.__flags.getter;
                delete f.__flags.setter;
                delete f.__flags.alloc;
                delete f.__flags.dealloc;
              } else {
                f.__flags.getter = 0 !== (0x01 & flags);
                f.__flags.setter = 0 !== (0x02 & flags);
                f.__flags.alloc = 0 !== (0x04 & flags);
                f.__flags.dealloc = 0 !== (0x08 & flags);
              }
              return f._flags;
            };
            Object.defineProperty(f, '__flags', {
              iterable: false,
              writable: false,
              value: Object.create(deriveFrom),
            });
            if (!deriveFrom) f(0);
            return f;
          };
          SBF.debugFlags = SBF.__makeDebugFlags();
        }

        const isLittleEndian = (function () {
          const buffer = new ArrayBuffer(2);
          new DataView(buffer).setInt16(0, 256, true);

          return new Int16Array(buffer)[0] === 256;
        })();

        const isFuncSig = (s) => '(' === s[1];

        const isPtrSig = (s) => 'p' === s || 'P' === s;
        const isAutoPtrSig = (s) => 'P' === s;
        const sigLetter = (s) => (isFuncSig(s) ? 'p' : s[0]);

        const sigIR = function (s) {
          switch (sigLetter(s)) {
            case 'c':
            case 'C':
              return 'i8';
            case 'i':
              return 'i32';
            case 'p':
            case 'P':
            case 's':
              return ptrIR;
            case 'j':
              return 'i64';
            case 'f':
              return 'float';
            case 'd':
              return 'double';
          }
          toss('Unhandled signature IR:', s);
        };

        const affirmBigIntArray = BigInt64Array
          ? () => true
          : () => toss('BigInt64Array is not available.');

        const sigDVGetter = function (s) {
          switch (sigLetter(s)) {
            case 'p':
            case 'P':
            case 's': {
              switch (ptrSizeof) {
                case 4:
                  return 'getInt32';
                case 8:
                  return affirmBigIntArray() && 'getBigInt64';
              }
              break;
            }
            case 'i':
              return 'getInt32';
            case 'c':
              return 'getInt8';
            case 'C':
              return 'getUint8';
            case 'j':
              return affirmBigIntArray() && 'getBigInt64';
            case 'f':
              return 'getFloat32';
            case 'd':
              return 'getFloat64';
          }
          toss('Unhandled DataView getter for signature:', s);
        };

        const sigDVSetter = function (s) {
          switch (sigLetter(s)) {
            case 'p':
            case 'P':
            case 's': {
              switch (ptrSizeof) {
                case 4:
                  return 'setInt32';
                case 8:
                  return affirmBigIntArray() && 'setBigInt64';
              }
              break;
            }
            case 'i':
              return 'setInt32';
            case 'c':
              return 'setInt8';
            case 'C':
              return 'setUint8';
            case 'j':
              return affirmBigIntArray() && 'setBigInt64';
            case 'f':
              return 'setFloat32';
            case 'd':
              return 'setFloat64';
          }
          toss('Unhandled DataView setter for signature:', s);
        };

        const sigDVSetWrapper = function (s) {
          switch (sigLetter(s)) {
            case 'i':
            case 'f':
            case 'c':
            case 'C':
            case 'd':
              return Number;
            case 'j':
              return affirmBigIntArray() && BigInt;
            case 'p':
            case 'P':
            case 's':
              switch (ptrSizeof) {
                case 4:
                  return Number;
                case 8:
                  return affirmBigIntArray() && BigInt;
              }
              break;
          }
          toss('Unhandled DataView set wrapper for signature:', s);
        };

        const sPropName = (s, k) => s + '::' + k;

        const __propThrowOnSet = function (structName, propName) {
          return () => toss(sPropName(structName, propName), 'is read-only.');
        };

        const __instancePointerMap = new WeakMap();

        const xPtrPropName = '(pointer-is-external)';

        const __freeStruct = function (ctor, obj, m) {
          if (!m) m = __instancePointerMap.get(obj);
          if (m) {
            __instancePointerMap.delete(obj);
            if (Array.isArray(obj.ondispose)) {
              let x;
              while ((x = obj.ondispose.shift())) {
                try {
                  if (x instanceof Function) x.call(obj);
                  else if (x instanceof StructType) x.dispose();
                  else if ('number' === typeof x) dealloc(x);
                } catch (e) {
                  console.warn(
                    'ondispose() for',
                    ctor.structName,
                    '@',
                    m,
                    'threw. NOT propagating it.',
                    e,
                  );
                }
              }
            } else if (obj.ondispose instanceof Function) {
              try {
                obj.ondispose();
              } catch (e) {
                console.warn(
                  'ondispose() for',
                  ctor.structName,
                  '@',
                  m,
                  'threw. NOT propagating it.',
                  e,
                );
              }
            }
            delete obj.ondispose;
            if (ctor.debugFlags.__flags.dealloc) {
              log(
                'debug.dealloc:',
                obj[xPtrPropName] ? 'EXTERNAL' : '',
                ctor.structName,
                'instance:',
                ctor.structInfo.sizeof,
                'bytes @' + m,
              );
            }
            if (!obj[xPtrPropName]) dealloc(m);
          }
        };

        const rop = (v) => {
          return {
            configurable: false,
            writable: false,
            iterable: false,
            value: v,
          };
        };

        const __allocStruct = function (ctor, obj, m) {
          let fill = !m;
          if (m) Object.defineProperty(obj, xPtrPropName, rop(m));
          else {
            m = alloc(ctor.structInfo.sizeof);
            if (!m) toss('Allocation of', ctor.structName, 'structure failed.');
          }
          try {
            if (ctor.debugFlags.__flags.alloc) {
              log(
                'debug.alloc:',
                fill ? '' : 'EXTERNAL',
                ctor.structName,
                'instance:',
                ctor.structInfo.sizeof,
                'bytes @' + m,
              );
            }
            if (fill) heap().fill(0, m, m + ctor.structInfo.sizeof);
            __instancePointerMap.set(obj, m);
          } catch (e) {
            __freeStruct(ctor, obj, m);
            throw e;
          }
        };

        const __memoryDump = function () {
          const p = this.pointer;
          return p
            ? new Uint8Array(heap().slice(p, p + this.structInfo.sizeof))
            : null;
        };

        const __memberKey = (k) => memberPrefix + k + memberSuffix;
        const __memberKeyProp = rop(__memberKey);

        const __lookupMember = function (
          structInfo,
          memberName,
          tossIfNotFound = true,
        ) {
          let m = structInfo.members[memberName];
          if (!m && (memberPrefix || memberSuffix)) {
            for (const v of Object.values(structInfo.members)) {
              if (v.key === memberName) {
                m = v;
                break;
              }
            }
            if (!m && tossIfNotFound) {
              toss(
                sPropName(structInfo.name, memberName),
                'is not a mapped struct member.',
              );
            }
          }
          return m;
        };

        const __memberSignature = function f(
          obj,
          memberName,
          emscriptenFormat = false,
        ) {
          if (!f._)
            f._ = (x) =>
              x.replace(/[^vipPsjrdcC]/g, '').replace(/[pPscC]/g, 'i');
          const m = __lookupMember(obj.structInfo, memberName, true);
          return emscriptenFormat ? f._(m.signature) : m.signature;
        };

        const __ptrPropDescriptor = {
          configurable: false,
          enumerable: false,
          get: function () {
            return __instancePointerMap.get(this);
          },
          set: () => toss("Cannot assign the 'pointer' property of a struct."),
        };

        const __structMemberKeys = rop(function () {
          const a = [];
          for (const k of Object.keys(this.structInfo.members)) {
            a.push(this.memberKey(k));
          }
          return a;
        });

        const __utf8Decoder = new TextDecoder('utf-8');
        const __utf8Encoder = new TextEncoder();

        const __SAB =
          'undefined' === typeof SharedArrayBuffer
            ? function () {}
            : SharedArrayBuffer;
        const __utf8Decode = function (arrayBuffer, begin, end) {
          return __utf8Decoder.decode(
            arrayBuffer.buffer instanceof __SAB
              ? arrayBuffer.slice(begin, end)
              : arrayBuffer.subarray(begin, end),
          );
        };

        const __memberIsString = function (
          obj,
          memberName,
          tossIfNotFound = false,
        ) {
          const m = __lookupMember(obj.structInfo, memberName, tossIfNotFound);
          return m && 1 === m.signature.length && 's' === m.signature[0]
            ? m
            : false;
        };

        const __affirmCStringSignature = function (member) {
          if ('s' === member.signature) return;
          toss(
            'Invalid member type signature for C-string value:',
            JSON.stringify(member),
          );
        };

        const __memberToJsString = function f(obj, memberName) {
          const m = __lookupMember(obj.structInfo, memberName, true);
          __affirmCStringSignature(m);
          const addr = obj[m.key];

          if (!addr) return null;
          let pos = addr;
          const mem = heap();
          for (; mem[pos] !== 0; ++pos) {}

          return addr === pos ? '' : __utf8Decode(mem, addr, pos);
        };

        const __addOnDispose = function (obj, ...v) {
          if (obj.ondispose) {
            if (!Array.isArray(obj.ondispose)) {
              obj.ondispose = [obj.ondispose];
            }
          } else {
            obj.ondispose = [];
          }
          obj.ondispose.push(...v);
        };

        const __allocCString = function (str) {
          const u = __utf8Encoder.encode(str);
          const mem = alloc(u.length + 1);
          if (!mem) toss('Allocation error while duplicating string:', str);
          const h = heap();

          h.set(u, mem);
          h[mem + u.length] = 0;

          return mem;
        };

        const __setMemberCString = function (obj, memberName, str) {
          const m = __lookupMember(obj.structInfo, memberName, true);
          __affirmCStringSignature(m);

          const mem = __allocCString(str);
          obj[m.key] = mem;
          __addOnDispose(obj, mem);
          return obj;
        };

        const StructType = function ctor(structName, structInfo) {
          if (arguments[2] !== rop) {
            toss(
              'Do not call the StructType constructor',
              'from client-level code.',
            );
          }
          Object.defineProperties(this, {
            structName: rop(structName),
            structInfo: rop(structInfo),
          });
        };

        StructType.prototype = Object.create(null, {
          dispose: rop(function () {
            __freeStruct(this.constructor, this);
          }),
          lookupMember: rop(function (memberName, tossIfNotFound = true) {
            return __lookupMember(this.structInfo, memberName, tossIfNotFound);
          }),
          memberToJsString: rop(function (memberName) {
            return __memberToJsString(this, memberName);
          }),
          memberIsString: rop(function (memberName, tossIfNotFound = true) {
            return __memberIsString(this, memberName, tossIfNotFound);
          }),
          memberKey: __memberKeyProp,
          memberKeys: __structMemberKeys,
          memberSignature: rop(function (memberName, emscriptenFormat = false) {
            return __memberSignature(this, memberName, emscriptenFormat);
          }),
          memoryDump: rop(__memoryDump),
          pointer: __ptrPropDescriptor,
          setMemberCString: rop(function (memberName, str) {
            return __setMemberCString(this, memberName, str);
          }),
        });

        Object.assign(StructType.prototype, {
          addOnDispose: function (...v) {
            __addOnDispose(this, ...v);
            return this;
          },
        });

        Object.defineProperties(StructType, {
          allocCString: rop(__allocCString),
          isA: rop((v) => v instanceof StructType),
          hasExternalPointer: rop(
            (v) => v instanceof StructType && !!v[xPtrPropName],
          ),
          memberKey: __memberKeyProp,
        });

        const isNumericValue = (v) =>
          Number.isFinite(v) || v instanceof (BigInt || Number);

        const makeMemberWrapper = function f(ctor, name, descr) {
          if (!f._) {
            f._ = { getters: {}, setters: {}, sw: {} };
            const a = ['i', 'c', 'C', 'p', 'P', 's', 'f', 'd', 'v()'];
            if (bigIntEnabled) a.push('j');
            a.forEach(function (v) {
              f._.getters[v] = sigDVGetter(v);
              f._.setters[v] = sigDVSetter(v);
              f._.sw[v] = sigDVSetWrapper(v);
            });
            const rxSig1 = /^[ipPsjfdcC]$/,
              rxSig2 = /^[vipPsjfdcC]\([ipPsjfdcC]*\)$/;
            f.sigCheck = function (obj, name, key, sig) {
              if (Object.prototype.hasOwnProperty.call(obj, key)) {
                toss(obj.structName, 'already has a property named', key + '.');
              }
              rxSig1.test(sig) ||
                rxSig2.test(sig) ||
                toss(
                  'Malformed signature for',
                  sPropName(obj.structName, name) + ':',
                  sig,
                );
            };
          }
          const key = ctor.memberKey(name);
          f.sigCheck(ctor.prototype, name, key, descr.signature);
          descr.key = key;
          descr.name = name;
          const sigGlyph = sigLetter(descr.signature);
          const xPropName = sPropName(ctor.prototype.structName, key);
          const dbg = ctor.prototype.debugFlags.__flags;

          const prop = Object.create(null);
          prop.configurable = false;
          prop.enumerable = false;
          prop.get = function () {
            if (dbg.getter) {
              log(
                'debug.getter:',
                f._.getters[sigGlyph],
                'for',
                sigIR(sigGlyph),
                xPropName,
                '@',
                this.pointer,
                '+',
                descr.offset,
                'sz',
                descr.sizeof,
              );
            }
            let rc = new DataView(
              heap().buffer,
              this.pointer + descr.offset,
              descr.sizeof,
            )[f._.getters[sigGlyph]](0, isLittleEndian);
            if (dbg.getter) log('debug.getter:', xPropName, 'result =', rc);
            return rc;
          };
          if (descr.readOnly) {
            prop.set = __propThrowOnSet(ctor.prototype.structName, key);
          } else {
            prop.set = function (v) {
              if (dbg.setter) {
                log(
                  'debug.setter:',
                  f._.setters[sigGlyph],
                  'for',
                  sigIR(sigGlyph),
                  xPropName,
                  '@',
                  this.pointer,
                  '+',
                  descr.offset,
                  'sz',
                  descr.sizeof,
                  v,
                );
              }
              if (!this.pointer) {
                toss('Cannot set struct property on disposed instance.');
              }
              if (null === v) v = 0;
              else
                while (!isNumericValue(v)) {
                  if (
                    isAutoPtrSig(descr.signature) &&
                    v instanceof StructType
                  ) {
                    v = v.pointer || 0;
                    if (dbg.setter)
                      log('debug.setter:', xPropName, 'resolved to', v);
                    break;
                  }
                  toss('Invalid value for pointer-type', xPropName + '.');
                }
              new DataView(
                heap().buffer,
                this.pointer + descr.offset,
                descr.sizeof,
              )[f._.setters[sigGlyph]](0, f._.sw[sigGlyph](v), isLittleEndian);
            };
          }
          Object.defineProperty(ctor.prototype, key, prop);
        };

        const StructBinder = function StructBinder(structName, structInfo) {
          if (1 === arguments.length) {
            structInfo = structName;
            structName = structInfo.name;
          } else if (!structInfo.name) {
            structInfo.name = structName;
          }
          if (!structName) toss('Struct name is required.');
          let lastMember = false;
          Object.keys(structInfo.members).forEach((k) => {
            const m = structInfo.members[k];
            if (!m.sizeof) toss(structName, 'member', k, 'is missing sizeof.');
            else if (m.sizeof === 1) {
              m.signature === 'c' ||
                m.signature === 'C' ||
                toss(
                  'Unexpected sizeof==1 member',
                  sPropName(structInfo.name, k),
                  'with signature',
                  m.signature,
                );
            } else {
              if (0 !== m.sizeof % 4) {
                console.warn(
                  'Invalid struct member description =',
                  m,
                  'from',
                  structInfo,
                );
                toss(
                  structName,
                  'member',
                  k,
                  'sizeof is not aligned. sizeof=' + m.sizeof,
                );
              }
              if (0 !== m.offset % 4) {
                console.warn(
                  'Invalid struct member description =',
                  m,
                  'from',
                  structInfo,
                );
                toss(
                  structName,
                  'member',
                  k,
                  'offset is not aligned. offset=' + m.offset,
                );
              }
            }
            if (!lastMember || lastMember.offset < m.offset) lastMember = m;
          });
          if (!lastMember) toss('No member property descriptions found.');
          else if (structInfo.sizeof < lastMember.offset + lastMember.sizeof) {
            toss(
              'Invalid struct config:',
              structName,
              'max member offset (' + lastMember.offset + ') ',
              'extends past end of struct (sizeof=' + structInfo.sizeof + ').',
            );
          }
          const debugFlags = rop(SBF.__makeDebugFlags(StructBinder.debugFlags));

          const StructCtor = function StructCtor(externalMemory) {
            if (!(this instanceof StructCtor)) {
              toss(
                'The',
                structName,
                "constructor may only be called via 'new'.",
              );
            } else if (arguments.length) {
              if (
                externalMemory !== (externalMemory | 0) ||
                externalMemory <= 0
              ) {
                toss('Invalid pointer value for', structName, 'constructor.');
              }
              __allocStruct(StructCtor, this, externalMemory);
            } else {
              __allocStruct(StructCtor, this);
            }
          };
          Object.defineProperties(StructCtor, {
            debugFlags: debugFlags,
            isA: rop((v) => v instanceof StructCtor),
            memberKey: __memberKeyProp,
            memberKeys: __structMemberKeys,
            methodInfoForKey: rop(function (mKey) {}),
            structInfo: rop(structInfo),
            structName: rop(structName),
          });
          StructCtor.prototype = new StructType(structName, structInfo, rop);
          Object.defineProperties(StructCtor.prototype, {
            debugFlags: debugFlags,
            constructor: rop(StructCtor),
          });
          Object.keys(structInfo.members).forEach((name) =>
            makeMemberWrapper(StructCtor, name, structInfo.members[name]),
          );
          return StructCtor;
        };
        StructBinder.StructType = StructType;
        StructBinder.config = config;
        StructBinder.allocCString = __allocCString;
        if (!StructBinder.debugFlags) {
          StructBinder.debugFlags = SBF.__makeDebugFlags(SBF.debugFlags);
        }
        return StructBinder;
      };

      globalThis.sqlite3ApiBootstrap.initializers.push(function (sqlite3) {
        'use strict';
        const toss = (...args) => {
          throw new Error(args.join(' '));
        };
        const toss3 = sqlite3.SQLite3Error.toss;
        const capi = sqlite3.capi,
          wasm = sqlite3.wasm,
          util = sqlite3.util;
        globalThis.WhWasmUtilInstaller(wasm);
        delete globalThis.WhWasmUtilInstaller;

        if (0) {
          const dealloc = wasm.exports[sqlite3.config.deallocExportName];
          const nFunc = wasm.functionTable().length;
          let i;
          for (i = 0; i < nFunc; ++i) {
            const e = wasm.functionEntry(i);
            if (dealloc === e) {
              capi.SQLITE_WASM_DEALLOC = i;
              break;
            }
          }
          if (dealloc !== wasm.functionEntry(capi.SQLITE_WASM_DEALLOC)) {
            toss(
              'Internal error: cannot find function pointer for SQLITE_WASM_DEALLOC.',
            );
          }
        }

        wasm.bindingSignatures = [
          ['sqlite3_aggregate_context', 'void*', 'sqlite3_context*', 'int'],

          ['sqlite3_bind_double', 'int', 'sqlite3_stmt*', 'int', 'f64'],
          ['sqlite3_bind_int', 'int', 'sqlite3_stmt*', 'int', 'int'],
          ['sqlite3_bind_null', undefined, 'sqlite3_stmt*', 'int'],
          ['sqlite3_bind_parameter_count', 'int', 'sqlite3_stmt*'],
          ['sqlite3_bind_parameter_index', 'int', 'sqlite3_stmt*', 'string'],
          ['sqlite3_bind_parameter_name', 'string', 'sqlite3_stmt*', 'int'],
          [
            'sqlite3_bind_pointer',
            'int',
            'sqlite3_stmt*',
            'int',
            '*',
            'string:static',
            '*',
          ],
          [
            'sqlite3_busy_handler',
            'int',
            [
              'sqlite3*',
              new wasm.xWrap.FuncPtrAdapter({
                signature: 'i(pi)',
                contextKey: (argv, argIndex) => argv[0],
              }),
              '*',
            ],
          ],
          ['sqlite3_busy_timeout', 'int', 'sqlite3*', 'int'],

          ['sqlite3_changes', 'int', 'sqlite3*'],
          ['sqlite3_clear_bindings', 'int', 'sqlite3_stmt*'],
          ['sqlite3_collation_needed', 'int', 'sqlite3*', '*', '*'],
          ['sqlite3_column_blob', '*', 'sqlite3_stmt*', 'int'],
          ['sqlite3_column_bytes', 'int', 'sqlite3_stmt*', 'int'],
          ['sqlite3_column_count', 'int', 'sqlite3_stmt*'],
          ['sqlite3_column_decltype', 'string', 'sqlite3_stmt*', 'int'],
          ['sqlite3_column_double', 'f64', 'sqlite3_stmt*', 'int'],
          ['sqlite3_column_int', 'int', 'sqlite3_stmt*', 'int'],
          ['sqlite3_column_name', 'string', 'sqlite3_stmt*', 'int'],
          ['sqlite3_column_text', 'string', 'sqlite3_stmt*', 'int'],
          ['sqlite3_column_type', 'int', 'sqlite3_stmt*', 'int'],
          ['sqlite3_column_value', 'sqlite3_value*', 'sqlite3_stmt*', 'int'],
          [
            'sqlite3_commit_hook',
            'void*',
            [
              'sqlite3*',
              new wasm.xWrap.FuncPtrAdapter({
                name: 'sqlite3_commit_hook',
                signature: 'i(p)',
                contextKey: (argv) => argv[0],
              }),
              '*',
            ],
          ],
          ['sqlite3_compileoption_get', 'string', 'int'],
          ['sqlite3_compileoption_used', 'int', 'string'],
          ['sqlite3_complete', 'int', 'string:flexible'],
          ['sqlite3_context_db_handle', 'sqlite3*', 'sqlite3_context*'],

          ['sqlite3_data_count', 'int', 'sqlite3_stmt*'],
          ['sqlite3_db_filename', 'string', 'sqlite3*', 'string'],
          ['sqlite3_db_handle', 'sqlite3*', 'sqlite3_stmt*'],
          ['sqlite3_db_name', 'string', 'sqlite3*', 'int'],
          ['sqlite3_db_readonly', 'int', 'sqlite3*', 'string'],
          ['sqlite3_db_status', 'int', 'sqlite3*', 'int', '*', '*', 'int'],
          ['sqlite3_errcode', 'int', 'sqlite3*'],
          ['sqlite3_errmsg', 'string', 'sqlite3*'],
          ['sqlite3_error_offset', 'int', 'sqlite3*'],
          ['sqlite3_errstr', 'string', 'int'],
          [
            'sqlite3_exec',
            'int',
            [
              'sqlite3*',
              'string:flexible',
              new wasm.xWrap.FuncPtrAdapter({
                signature: 'i(pipp)',
                bindScope: 'transient',
                callProxy: (callback) => {
                  let aNames;
                  return (pVoid, nCols, pColVals, pColNames) => {
                    try {
                      const aVals = wasm.cArgvToJs(nCols, pColVals);
                      if (!aNames) aNames = wasm.cArgvToJs(nCols, pColNames);
                      return callback(aVals, aNames) | 0;
                    } catch (e) {
                      return e.resultCode || capi.SQLITE_ERROR;
                    }
                  };
                },
              }),
              '*',
              '**',
            ],
          ],
          ['sqlite3_expanded_sql', 'string', 'sqlite3_stmt*'],
          ['sqlite3_extended_errcode', 'int', 'sqlite3*'],
          ['sqlite3_extended_result_codes', 'int', 'sqlite3*', 'int'],
          ['sqlite3_file_control', 'int', 'sqlite3*', 'string', 'int', '*'],
          ['sqlite3_finalize', 'int', 'sqlite3_stmt*'],
          ['sqlite3_free', undefined, '*'],
          ['sqlite3_get_autocommit', 'int', 'sqlite3*'],
          ['sqlite3_get_auxdata', '*', 'sqlite3_context*', 'int'],
          ['sqlite3_initialize', undefined],
          ['sqlite3_interrupt', undefined, 'sqlite3*'],
          ['sqlite3_is_interrupted', 'int', 'sqlite3*'],
          ['sqlite3_keyword_count', 'int'],
          ['sqlite3_keyword_name', 'int', ['int', '**', '*']],
          ['sqlite3_keyword_check', 'int', ['string', 'int']],
          ['sqlite3_libversion', 'string'],
          ['sqlite3_libversion_number', 'int'],
          ['sqlite3_limit', 'int', ['sqlite3*', 'int', 'int']],
          ['sqlite3_malloc', '*', 'int'],
          ['sqlite3_open', 'int', 'string', '*'],
          ['sqlite3_open_v2', 'int', 'string', '*', 'int', 'string'],

          ['sqlite3_realloc', '*', '*', 'int'],
          ['sqlite3_reset', 'int', 'sqlite3_stmt*'],

          [
            'sqlite3_result_blob',
            undefined,
            'sqlite3_context*',
            '*',
            'int',
            '*',
          ],
          ['sqlite3_result_double', undefined, 'sqlite3_context*', 'f64'],
          [
            'sqlite3_result_error',
            undefined,
            'sqlite3_context*',
            'string',
            'int',
          ],
          ['sqlite3_result_error_code', undefined, 'sqlite3_context*', 'int'],
          ['sqlite3_result_error_nomem', undefined, 'sqlite3_context*'],
          ['sqlite3_result_error_toobig', undefined, 'sqlite3_context*'],
          ['sqlite3_result_int', undefined, 'sqlite3_context*', 'int'],
          ['sqlite3_result_null', undefined, 'sqlite3_context*'],
          [
            'sqlite3_result_pointer',
            undefined,
            'sqlite3_context*',
            '*',
            'string:static',
            '*',
          ],
          ['sqlite3_result_subtype', undefined, 'sqlite3_value*', 'int'],
          [
            'sqlite3_result_text',
            undefined,
            'sqlite3_context*',
            'string',
            'int',
            '*',
          ],
          ['sqlite3_result_zeroblob', undefined, 'sqlite3_context*', 'int'],
          [
            'sqlite3_rollback_hook',
            'void*',
            [
              'sqlite3*',
              new wasm.xWrap.FuncPtrAdapter({
                name: 'sqlite3_rollback_hook',
                signature: 'v(p)',
                contextKey: (argv) => argv[0],
              }),
              '*',
            ],
          ],

          [
            'sqlite3_set_auxdata',
            undefined,
            [
              'sqlite3_context*',
              'int',
              '*',
              true
                ? '*'
                : new wasm.xWrap.FuncPtrAdapter({
                    name: 'xDestroyAuxData',
                    signature: 'v(p)',
                    contextKey: (argv, argIndex) => argv[0],
                  }),
            ],
          ],
          ['sqlite3_shutdown', undefined],
          ['sqlite3_sourceid', 'string'],
          ['sqlite3_sql', 'string', 'sqlite3_stmt*'],
          ['sqlite3_status', 'int', 'int', '*', '*', 'int'],
          ['sqlite3_step', 'int', 'sqlite3_stmt*'],
          ['sqlite3_stmt_busy', 'int', 'sqlite3_stmt*'],
          ['sqlite3_stmt_readonly', 'int', 'sqlite3_stmt*'],
          ['sqlite3_stmt_status', 'int', 'sqlite3_stmt*', 'int', 'int'],
          ['sqlite3_strglob', 'int', 'string', 'string'],
          ['sqlite3_stricmp', 'int', 'string', 'string'],
          ['sqlite3_strlike', 'int', 'string', 'string', 'int'],
          ['sqlite3_strnicmp', 'int', 'string', 'string', 'int'],
          [
            'sqlite3_table_column_metadata',
            'int',
            'sqlite3*',
            'string',
            'string',
            'string',
            '**',
            '**',
            '*',
            '*',
            '*',
          ],
          ['sqlite3_total_changes', 'int', 'sqlite3*'],
          [
            'sqlite3_trace_v2',
            'int',
            [
              'sqlite3*',
              'int',
              new wasm.xWrap.FuncPtrAdapter({
                name: 'sqlite3_trace_v2::callback',
                signature: 'i(ippp)',
                contextKey: (argv, argIndex) => argv[0],
              }),
              '*',
            ],
          ],
          ['sqlite3_txn_state', 'int', ['sqlite3*', 'string']],

          ['sqlite3_uri_boolean', 'int', 'sqlite3_filename', 'string', 'int'],
          ['sqlite3_uri_key', 'string', 'sqlite3_filename', 'int'],
          ['sqlite3_uri_parameter', 'string', 'sqlite3_filename', 'string'],
          ['sqlite3_user_data', 'void*', 'sqlite3_context*'],
          ['sqlite3_value_blob', '*', 'sqlite3_value*'],
          ['sqlite3_value_bytes', 'int', 'sqlite3_value*'],
          ['sqlite3_value_double', 'f64', 'sqlite3_value*'],
          ['sqlite3_value_dup', 'sqlite3_value*', 'sqlite3_value*'],
          ['sqlite3_value_free', undefined, 'sqlite3_value*'],
          ['sqlite3_value_frombind', 'int', 'sqlite3_value*'],
          ['sqlite3_value_int', 'int', 'sqlite3_value*'],
          ['sqlite3_value_nochange', 'int', 'sqlite3_value*'],
          ['sqlite3_value_numeric_type', 'int', 'sqlite3_value*'],
          ['sqlite3_value_pointer', '*', 'sqlite3_value*', 'string:static'],
          ['sqlite3_value_subtype', 'int', 'sqlite3_value*'],
          ['sqlite3_value_text', 'string', 'sqlite3_value*'],
          ['sqlite3_value_type', 'int', 'sqlite3_value*'],
          ['sqlite3_vfs_find', '*', 'string'],
          ['sqlite3_vfs_register', 'int', 'sqlite3_vfs*', 'int'],
          ['sqlite3_vfs_unregister', 'int', 'sqlite3_vfs*'],
        ];

        if (!!wasm.exports.sqlite3_progress_handler) {
          wasm.bindingSignatures.push([
            'sqlite3_progress_handler',
            undefined,
            [
              'sqlite3*',
              'int',
              new wasm.xWrap.FuncPtrAdapter({
                name: 'xProgressHandler',
                signature: 'i(p)',
                bindScope: 'context',
                contextKey: (argv, argIndex) => argv[0],
              }),
              '*',
            ],
          ]);
        }

        if (!!wasm.exports.sqlite3_stmt_explain) {
          wasm.bindingSignatures.push(
            ['sqlite3_stmt_explain', 'int', 'sqlite3_stmt*', 'int'],
            ['sqlite3_stmt_isexplain', 'int', 'sqlite3_stmt*'],
          );
        }

        if (!!wasm.exports.sqlite3_set_authorizer) {
          wasm.bindingSignatures.push([
            'sqlite3_set_authorizer',
            'int',
            [
              'sqlite3*',
              new wasm.xWrap.FuncPtrAdapter({
                name: 'sqlite3_set_authorizer::xAuth',
                signature: 'i(pi' + 'ssss)',
                contextKey: (argv, argIndex) => argv[0],
                callProxy: (callback) => {
                  return (pV, iCode, s0, s1, s2, s3) => {
                    try {
                      s0 = s0 && wasm.cstrToJs(s0);
                      s1 = s1 && wasm.cstrToJs(s1);
                      s2 = s2 && wasm.cstrToJs(s2);
                      s3 = s3 && wasm.cstrToJs(s3);
                      return callback(pV, iCode, s0, s1, s2, s3) || 0;
                    } catch (e) {
                      return e.resultCode || capi.SQLITE_ERROR;
                    }
                  };
                },
              }),
              '*',
            ],
          ]);
        }

        if (false && wasm.compileOptionUsed('SQLITE_ENABLE_NORMALIZE')) {
          wasm.bindingSignatures.push([
            'sqlite3_normalized_sql',
            'string',
            'sqlite3_stmt*',
          ]);
        }

        wasm.bindingSignatures.int64 = [
          ['sqlite3_bind_int64', 'int', ['sqlite3_stmt*', 'int', 'i64']],
          ['sqlite3_changes64', 'i64', ['sqlite3*']],
          ['sqlite3_column_int64', 'i64', ['sqlite3_stmt*', 'int']],
          [
            'sqlite3_deserialize',
            'int',
            'sqlite3*',
            'string',
            '*',
            'i64',
            'i64',
            'int',
          ],
          ['sqlite3_last_insert_rowid', 'i64', ['sqlite3*']],
          ['sqlite3_malloc64', '*', 'i64'],
          ['sqlite3_msize', 'i64', '*'],
          ['sqlite3_overload_function', 'int', ['sqlite3*', 'string', 'int']],
          ['sqlite3_realloc64', '*', '*', 'i64'],
          ['sqlite3_result_int64', undefined, '*', 'i64'],
          ['sqlite3_result_zeroblob64', 'int', '*', 'i64'],
          ['sqlite3_serialize', '*', 'sqlite3*', 'string', '*', 'int'],
          ['sqlite3_set_last_insert_rowid', undefined, ['sqlite3*', 'i64']],
          ['sqlite3_status64', 'int', 'int', '*', '*', 'int'],
          ['sqlite3_total_changes64', 'i64', ['sqlite3*']],
          [
            'sqlite3_update_hook',
            '*',
            [
              'sqlite3*',
              new wasm.xWrap.FuncPtrAdapter({
                name: 'sqlite3_update_hook',
                signature: 'v(iippj)',
                contextKey: (argv) => argv[0],
                callProxy: (callback) => {
                  return (p, op, z0, z1, rowid) => {
                    callback(
                      p,
                      op,
                      wasm.cstrToJs(z0),
                      wasm.cstrToJs(z1),
                      rowid,
                    );
                  };
                },
              }),
              '*',
            ],
          ],
          ['sqlite3_uri_int64', 'i64', ['sqlite3_filename', 'string', 'i64']],
          ['sqlite3_value_int64', 'i64', 'sqlite3_value*'],
        ];

        if (wasm.bigIntEnabled && !!wasm.exports.sqlite3_declare_vtab) {
          wasm.bindingSignatures.int64.push(
            [
              'sqlite3_create_module',
              'int',
              ['sqlite3*', 'string', 'sqlite3_module*', '*'],
            ],
            [
              'sqlite3_create_module_v2',
              'int',
              ['sqlite3*', 'string', 'sqlite3_module*', '*', '*'],
            ],
            ['sqlite3_declare_vtab', 'int', ['sqlite3*', 'string:flexible']],
            ['sqlite3_drop_modules', 'int', ['sqlite3*', '**']],
            ['sqlite3_vtab_collation', 'string', 'sqlite3_index_info*', 'int'],
            ['sqlite3_vtab_distinct', 'int', 'sqlite3_index_info*'],
            ['sqlite3_vtab_in', 'int', 'sqlite3_index_info*', 'int', 'int'],
            ['sqlite3_vtab_in_first', 'int', 'sqlite3_value*', '**'],
            ['sqlite3_vtab_in_next', 'int', 'sqlite3_value*', '**'],

            ['sqlite3_vtab_nochange', 'int', 'sqlite3_context*'],
            ['sqlite3_vtab_on_conflict', 'int', 'sqlite3*'],
            [
              'sqlite3_vtab_rhs_value',
              'int',
              'sqlite3_index_info*',
              'int',
              '**',
            ],
          );
        }

        if (wasm.bigIntEnabled && !!wasm.exports.sqlite3_preupdate_hook) {
          wasm.bindingSignatures.int64.push(
            ['sqlite3_preupdate_blobwrite', 'int', 'sqlite3*'],
            ['sqlite3_preupdate_count', 'int', 'sqlite3*'],
            ['sqlite3_preupdate_depth', 'int', 'sqlite3*'],
            [
              'sqlite3_preupdate_hook',
              '*',
              [
                'sqlite3*',
                new wasm.xWrap.FuncPtrAdapter({
                  name: 'sqlite3_preupdate_hook',
                  signature: 'v(ppippjj)',
                  contextKey: (argv) => argv[0],
                  callProxy: (callback) => {
                    return (p, db, op, zDb, zTbl, iKey1, iKey2) => {
                      callback(
                        p,
                        db,
                        op,
                        wasm.cstrToJs(zDb),
                        wasm.cstrToJs(zTbl),
                        iKey1,
                        iKey2,
                      );
                    };
                  },
                }),
                '*',
              ],
            ],
            ['sqlite3_preupdate_new', 'int', ['sqlite3*', 'int', '**']],
            ['sqlite3_preupdate_old', 'int', ['sqlite3*', 'int', '**']],
          );
        }

        if (
          wasm.bigIntEnabled &&
          !!wasm.exports.sqlite3changegroup_add &&
          !!wasm.exports.sqlite3session_create &&
          !!wasm.exports.sqlite3_preupdate_hook
        ) {
          const __ipsProxy = {
            signature: 'i(ps)',
            callProxy: (callback) => {
              return (p, s) => {
                try {
                  return callback(p, wasm.cstrToJs(s)) | 0;
                } catch (e) {
                  return e.resultCode || capi.SQLITE_ERROR;
                }
              };
            },
          };

          wasm.bindingSignatures.int64.push(
            ...[
              [
                'sqlite3changegroup_add',
                'int',
                ['sqlite3_changegroup*', 'int', 'void*'],
              ],
              [
                'sqlite3changegroup_add_strm',
                'int',
                [
                  'sqlite3_changegroup*',
                  new wasm.xWrap.FuncPtrAdapter({
                    name: 'xInput',
                    signature: 'i(ppp)',
                    bindScope: 'transient',
                  }),
                  'void*',
                ],
              ],
              [
                'sqlite3changegroup_delete',
                undefined,
                ['sqlite3_changegroup*'],
              ],
              ['sqlite3changegroup_new', 'int', ['**']],
              [
                'sqlite3changegroup_output',
                'int',
                ['sqlite3_changegroup*', 'int*', '**'],
              ],
              [
                'sqlite3changegroup_output_strm',
                'int',
                [
                  'sqlite3_changegroup*',
                  new wasm.xWrap.FuncPtrAdapter({
                    name: 'xOutput',
                    signature: 'i(ppi)',
                    bindScope: 'transient',
                  }),
                  'void*',
                ],
              ],
              [
                'sqlite3changeset_apply',
                'int',
                [
                  'sqlite3*',
                  'int',
                  'void*',
                  new wasm.xWrap.FuncPtrAdapter({
                    name: 'xFilter',
                    bindScope: 'transient',
                    ...__ipsProxy,
                  }),
                  new wasm.xWrap.FuncPtrAdapter({
                    name: 'xConflict',
                    signature: 'i(pip)',
                    bindScope: 'transient',
                  }),
                  'void*',
                ],
              ],
              [
                'sqlite3changeset_apply_strm',
                'int',
                [
                  'sqlite3*',
                  new wasm.xWrap.FuncPtrAdapter({
                    name: 'xInput',
                    signature: 'i(ppp)',
                    bindScope: 'transient',
                  }),
                  'void*',
                  new wasm.xWrap.FuncPtrAdapter({
                    name: 'xFilter',
                    bindScope: 'transient',
                    ...__ipsProxy,
                  }),
                  new wasm.xWrap.FuncPtrAdapter({
                    name: 'xConflict',
                    signature: 'i(pip)',
                    bindScope: 'transient',
                  }),
                  'void*',
                ],
              ],
              [
                'sqlite3changeset_apply_v2',
                'int',
                [
                  'sqlite3*',
                  'int',
                  'void*',
                  new wasm.xWrap.FuncPtrAdapter({
                    name: 'xFilter',
                    bindScope: 'transient',
                    ...__ipsProxy,
                  }),
                  new wasm.xWrap.FuncPtrAdapter({
                    name: 'xConflict',
                    signature: 'i(pip)',
                    bindScope: 'transient',
                  }),
                  'void*',
                  '**',
                  'int*',
                  'int',
                ],
              ],
              [
                'sqlite3changeset_apply_v2_strm',
                'int',
                [
                  'sqlite3*',
                  new wasm.xWrap.FuncPtrAdapter({
                    name: 'xInput',
                    signature: 'i(ppp)',
                    bindScope: 'transient',
                  }),
                  'void*',
                  new wasm.xWrap.FuncPtrAdapter({
                    name: 'xFilter',
                    bindScope: 'transient',
                    ...__ipsProxy,
                  }),
                  new wasm.xWrap.FuncPtrAdapter({
                    name: 'xConflict',
                    signature: 'i(pip)',
                    bindScope: 'transient',
                  }),
                  'void*',
                  '**',
                  'int*',
                  'int',
                ],
              ],
              [
                'sqlite3changeset_concat',
                'int',
                ['int', 'void*', 'int', 'void*', 'int*', '**'],
              ],
              [
                'sqlite3changeset_concat_strm',
                'int',
                [
                  new wasm.xWrap.FuncPtrAdapter({
                    name: 'xInputA',
                    signature: 'i(ppp)',
                    bindScope: 'transient',
                  }),
                  'void*',
                  new wasm.xWrap.FuncPtrAdapter({
                    name: 'xInputB',
                    signature: 'i(ppp)',
                    bindScope: 'transient',
                  }),
                  'void*',
                  new wasm.xWrap.FuncPtrAdapter({
                    name: 'xOutput',
                    signature: 'i(ppi)',
                    bindScope: 'transient',
                  }),
                  'void*',
                ],
              ],
              [
                'sqlite3changeset_conflict',
                'int',
                ['sqlite3_changeset_iter*', 'int', '**'],
              ],
              ['sqlite3changeset_finalize', 'int', ['sqlite3_changeset_iter*']],
              [
                'sqlite3changeset_fk_conflicts',
                'int',
                ['sqlite3_changeset_iter*', 'int*'],
              ],
              [
                'sqlite3changeset_invert',
                'int',
                ['int', 'void*', 'int*', '**'],
              ],
              [
                'sqlite3changeset_invert_strm',
                'int',
                [
                  new wasm.xWrap.FuncPtrAdapter({
                    name: 'xInput',
                    signature: 'i(ppp)',
                    bindScope: 'transient',
                  }),
                  'void*',
                  new wasm.xWrap.FuncPtrAdapter({
                    name: 'xOutput',
                    signature: 'i(ppi)',
                    bindScope: 'transient',
                  }),
                  'void*',
                ],
              ],
              [
                'sqlite3changeset_new',
                'int',
                ['sqlite3_changeset_iter*', 'int', '**'],
              ],
              ['sqlite3changeset_next', 'int', ['sqlite3_changeset_iter*']],
              [
                'sqlite3changeset_old',
                'int',
                ['sqlite3_changeset_iter*', 'int', '**'],
              ],
              [
                'sqlite3changeset_op',
                'int',
                ['sqlite3_changeset_iter*', '**', 'int*', 'int*', 'int*'],
              ],
              [
                'sqlite3changeset_pk',
                'int',
                ['sqlite3_changeset_iter*', '**', 'int*'],
              ],
              ['sqlite3changeset_start', 'int', ['**', 'int', '*']],
              [
                'sqlite3changeset_start_strm',
                'int',
                [
                  '**',
                  new wasm.xWrap.FuncPtrAdapter({
                    name: 'xInput',
                    signature: 'i(ppp)',
                    bindScope: 'transient',
                  }),
                  'void*',
                ],
              ],
              ['sqlite3changeset_start_v2', 'int', ['**', 'int', '*', 'int']],
              [
                'sqlite3changeset_start_v2_strm',
                'int',
                [
                  '**',
                  new wasm.xWrap.FuncPtrAdapter({
                    name: 'xInput',
                    signature: 'i(ppp)',
                    bindScope: 'transient',
                  }),
                  'void*',
                  'int',
                ],
              ],
              ['sqlite3session_attach', 'int', ['sqlite3_session*', 'string']],
              [
                'sqlite3session_changeset',
                'int',
                ['sqlite3_session*', 'int*', '**'],
              ],
              ['sqlite3session_changeset_size', 'i64', ['sqlite3_session*']],
              [
                'sqlite3session_changeset_strm',
                'int',
                [
                  'sqlite3_session*',
                  new wasm.xWrap.FuncPtrAdapter({
                    name: 'xOutput',
                    signature: 'i(ppp)',
                    bindScope: 'transient',
                  }),
                  'void*',
                ],
              ],
              ['sqlite3session_config', 'int', ['int', 'void*']],
              ['sqlite3session_create', 'int', ['sqlite3*', 'string', '**']],

              [
                'sqlite3session_diff',
                'int',
                ['sqlite3_session*', 'string', 'string', '**'],
              ],
              ['sqlite3session_enable', 'int', ['sqlite3_session*', 'int']],
              ['sqlite3session_indirect', 'int', ['sqlite3_session*', 'int']],
              ['sqlite3session_isempty', 'int', ['sqlite3_session*']],
              ['sqlite3session_memory_used', 'i64', ['sqlite3_session*']],
              [
                'sqlite3session_object_config',
                'int',
                ['sqlite3_session*', 'int', 'void*'],
              ],
              [
                'sqlite3session_patchset',
                'int',
                ['sqlite3_session*', '*', '**'],
              ],
              [
                'sqlite3session_patchset_strm',
                'int',
                [
                  'sqlite3_session*',
                  new wasm.xWrap.FuncPtrAdapter({
                    name: 'xOutput',
                    signature: 'i(ppp)',
                    bindScope: 'transient',
                  }),
                  'void*',
                ],
              ],
              [
                'sqlite3session_table_filter',
                undefined,
                [
                  'sqlite3_session*',
                  new wasm.xWrap.FuncPtrAdapter({
                    name: 'xFilter',
                    ...__ipsProxy,
                    contextKey: (argv, argIndex) => argv[0],
                  }),
                  '*',
                ],
              ],
            ],
          );
        }

        wasm.bindingSignatures.wasmInternal = [
          ['sqlite3__wasm_db_reset', 'int', 'sqlite3*'],
          ['sqlite3__wasm_db_vfs', 'sqlite3_vfs*', 'sqlite3*', 'string'],
          [
            'sqlite3__wasm_vfs_create_file',
            'int',
            'sqlite3_vfs*',
            'string',
            '*',
            'int',
          ],
          ['sqlite3__wasm_posix_create_file', 'int', 'string', '*', 'int'],
          ['sqlite3__wasm_vfs_unlink', 'int', 'sqlite3_vfs*', 'string'],
          ['sqlite3__wasm_qfmt_token', 'string:dealloc', 'string', 'int'],
        ];

        sqlite3.StructBinder = globalThis.Jaccwabyt({
          heap: 0 ? wasm.memory : wasm.heap8u,
          alloc: wasm.alloc,
          dealloc: wasm.dealloc,
          bigIntEnabled: wasm.bigIntEnabled,
          memberPrefix: '$',
        });
        delete globalThis.Jaccwabyt;

        {
          const __xString = wasm.xWrap.argAdapter('string');
          wasm.xWrap.argAdapter('string:flexible', (v) =>
            __xString(util.flexibleString(v)),
          );

          wasm.xWrap.argAdapter(
            'string:static',
            function (v) {
              if (wasm.isPtr(v)) return v;
              v = '' + v;
              let rc = this[v];
              return rc || (this[v] = wasm.allocCString(v));
            }.bind(Object.create(null)),
          );

          const __xArgPtr = wasm.xWrap.argAdapter('*');
          const nilType = function () {};
          wasm.xWrap.argAdapter('sqlite3_filename', __xArgPtr)(
            'sqlite3_context*',
            __xArgPtr,
          )('sqlite3_value*', __xArgPtr)('void*', __xArgPtr)(
            'sqlite3_changegroup*',
            __xArgPtr,
          )('sqlite3_changeset_iter*', __xArgPtr)(
            'sqlite3_session*',
            __xArgPtr,
          )('sqlite3_stmt*', (v) =>
            __xArgPtr(
              v instanceof (sqlite3?.oo1?.Stmt || nilType) ? v.pointer : v,
            ),
          )('sqlite3*', (v) =>
            __xArgPtr(
              v instanceof (sqlite3?.oo1?.DB || nilType) ? v.pointer : v,
            ),
          )('sqlite3_vfs*', (v) => {
            if ('string' === typeof v) {
              return (
                capi.sqlite3_vfs_find(v) ||
                sqlite3.SQLite3Error.toss(
                  capi.SQLITE_NOTFOUND,
                  'Unknown sqlite3_vfs name:',
                  v,
                )
              );
            }
            return __xArgPtr(
              v instanceof (capi.sqlite3_vfs || nilType) ? v.pointer : v,
            );
          });
          if (wasm.exports.sqlite3_declare_vtab) {
            wasm.xWrap.argAdapter('sqlite3_index_info*', (v) =>
              __xArgPtr(
                v instanceof (capi.sqlite3_index_info || nilType)
                  ? v.pointer
                  : v,
              ),
            )('sqlite3_module*', (v) =>
              __xArgPtr(
                v instanceof (capi.sqlite3_module || nilType) ? v.pointer : v,
              ),
            );
          }

          const __xRcPtr = wasm.xWrap.resultAdapter('*');
          wasm.xWrap.resultAdapter('sqlite3*', __xRcPtr)(
            'sqlite3_context*',
            __xRcPtr,
          )('sqlite3_stmt*', __xRcPtr)('sqlite3_value*', __xRcPtr)(
            'sqlite3_vfs*',
            __xRcPtr,
          )('void*', __xRcPtr);

          if (0 === wasm.exports.sqlite3_step.length) {
            wasm.xWrap.doArgcCheck = false;
            sqlite3.config.warn(
              'Disabling sqlite3.wasm.xWrap.doArgcCheck due to environmental quirks.',
            );
          }
          for (const e of wasm.bindingSignatures) {
            capi[e[0]] = wasm.xWrap.apply(null, e);
          }
          for (const e of wasm.bindingSignatures.wasmInternal) {
            util[e[0]] = wasm.xWrap.apply(null, e);
          }

          const fI64Disabled = function (fname) {
            return () =>
              toss(
                fname + '() is unavailable due to lack',
                'of BigInt support in this build.',
              );
          };
          for (const e of wasm.bindingSignatures.int64) {
            capi[e[0]] = wasm.bigIntEnabled
              ? wasm.xWrap.apply(null, e)
              : fI64Disabled(e[0]);
          }

          delete wasm.bindingSignatures;

          if (wasm.exports.sqlite3__wasm_db_error) {
            const __db_err = wasm.xWrap(
              'sqlite3__wasm_db_error',
              'int',
              'sqlite3*',
              'int',
              'string',
            );

            util.sqlite3__wasm_db_error = function (pDb, resultCode, message) {
              if (resultCode instanceof sqlite3.WasmAllocError) {
                resultCode = capi.SQLITE_NOMEM;
                message = 0;
              } else if (resultCode instanceof Error) {
                message = message || '' + resultCode;
                resultCode = resultCode.resultCode || capi.SQLITE_ERROR;
              }
              return pDb ? __db_err(pDb, resultCode, message) : resultCode;
            };
          } else {
            util.sqlite3__wasm_db_error = function (pDb, errCode, msg) {
              console.warn(
                'sqlite3__wasm_db_error() is not exported.',
                arguments,
              );
              return errCode;
            };
          }
        }

        {
          const cJson = wasm.xCall('sqlite3__wasm_enum_json');
          if (!cJson) {
            toss(
              "Maintenance required: increase sqlite3__wasm_enum_json()'s",
              'static buffer size!',
            );
          }

          wasm.ctype = JSON.parse(wasm.cstrToJs(cJson));

          const defineGroups = [
            'access',
            'authorizer',
            'blobFinalizers',
            'changeset',
            'config',
            'dataTypes',
            'dbConfig',
            'dbStatus',
            'encodings',
            'fcntl',
            'flock',
            'ioCap',
            'limits',
            'openFlags',
            'prepareFlags',
            'resultCodes',
            'sqlite3Status',
            'stmtStatus',
            'syncFlags',
            'trace',
            'txnState',
            'udfFlags',
            'version',
          ];
          if (wasm.bigIntEnabled) {
            defineGroups.push('serialize', 'session', 'vtab');
          }
          for (const t of defineGroups) {
            for (const e of Object.entries(wasm.ctype[t])) {
              capi[e[0]] = e[1];
            }
          }
          if (!wasm.functionEntry(capi.SQLITE_WASM_DEALLOC)) {
            toss(
              'Internal error: cannot resolve exported function',
              'entry SQLITE_WASM_DEALLOC (==' + capi.SQLITE_WASM_DEALLOC + ').',
            );
          }
          const __rcMap = Object.create(null);
          for (const t of ['resultCodes']) {
            for (const e of Object.entries(wasm.ctype[t])) {
              __rcMap[e[1]] = e[0];
            }
          }

          capi.sqlite3_js_rc_str = (rc) => __rcMap[rc];

          const notThese = Object.assign(Object.create(null), {
            WasmTestStruct: true,

            sqlite3_kvvfs_methods: !util.isUIThread(),

            sqlite3_index_info: !wasm.bigIntEnabled,
            sqlite3_index_constraint: !wasm.bigIntEnabled,
            sqlite3_index_orderby: !wasm.bigIntEnabled,
            sqlite3_index_constraint_usage: !wasm.bigIntEnabled,
          });
          for (const s of wasm.ctype.structs) {
            if (!notThese[s.name]) {
              capi[s.name] = sqlite3.StructBinder(s);
            }
          }
          if (capi.sqlite3_index_info) {
            for (const k of [
              'sqlite3_index_constraint',
              'sqlite3_index_orderby',
              'sqlite3_index_constraint_usage',
            ]) {
              capi.sqlite3_index_info[k] = capi[k];
              delete capi[k];
            }
            capi.sqlite3_vtab_config = wasm.xWrap(
              'sqlite3__wasm_vtab_config',
              'int',
              ['sqlite3*', 'int', 'int'],
            );
          }
        }

        const __dbArgcMismatch = (pDb, f, n) => {
          return util.sqlite3__wasm_db_error(
            pDb,
            capi.SQLITE_MISUSE,
            f + '() requires ' + n + ' argument' + (1 === n ? '' : 's') + '.',
          );
        };

        const __errEncoding = (pDb) => {
          return util.sqlite3__wasm_db_error(
            pDb,
            capi.SQLITE_FORMAT,
            'SQLITE_UTF8 is the only supported encoding.',
          );
        };

        const __argPDb = (pDb) => wasm.xWrap.argAdapter('sqlite3*')(pDb);
        const __argStr = (str) => (wasm.isPtr(str) ? wasm.cstrToJs(str) : str);
        const __dbCleanupMap = function (pDb, mode) {
          pDb = __argPDb(pDb);
          let m = this.dbMap.get(pDb);
          if (!mode) {
            this.dbMap.delete(pDb);
            return m;
          } else if (!m && mode > 0) {
            this.dbMap.set(pDb, (m = Object.create(null)));
          }
          return m;
        }.bind(
          Object.assign(Object.create(null), {
            dbMap: new Map(),
          }),
        );

        __dbCleanupMap.addCollation = function (pDb, name) {
          const m = __dbCleanupMap(pDb, 1);
          if (!m.collation) m.collation = new Set();
          m.collation.add(__argStr(name).toLowerCase());
        };

        __dbCleanupMap._addUDF = function (pDb, name, arity, map) {
          name = __argStr(name).toLowerCase();
          let u = map.get(name);
          if (!u) map.set(name, (u = new Set()));
          u.add(arity < 0 ? -1 : arity);
        };

        __dbCleanupMap.addFunction = function (pDb, name, arity) {
          const m = __dbCleanupMap(pDb, 1);
          if (!m.udf) m.udf = new Map();
          this._addUDF(pDb, name, arity, m.udf);
        };

        if (wasm.exports.sqlite3_create_window_function) {
          __dbCleanupMap.addWindowFunc = function (pDb, name, arity) {
            const m = __dbCleanupMap(pDb, 1);
            if (!m.wudf) m.wudf = new Map();
            this._addUDF(pDb, name, arity, m.wudf);
          };
        }

        __dbCleanupMap.cleanup = function (pDb) {
          pDb = __argPDb(pDb);

          const closeArgs = [pDb];
          for (const name of [
            'sqlite3_busy_handler',
            'sqlite3_commit_hook',
            'sqlite3_preupdate_hook',
            'sqlite3_progress_handler',
            'sqlite3_rollback_hook',
            'sqlite3_set_authorizer',
            'sqlite3_trace_v2',
            'sqlite3_update_hook',
          ]) {
            const x = wasm.exports[name];
            if (!x) {
              continue;
            }
            closeArgs.length = x.length;
            try {
              capi[name](...closeArgs);
            } catch (e) {
              sqlite3.config.warn(
                'close-time call of',
                name + '(',
                closeArgs,
                ') threw:',
                e,
              );
            }
          }
          const m = __dbCleanupMap(pDb, 0);
          if (!m) return;
          if (m.collation) {
            for (const name of m.collation) {
              try {
                capi.sqlite3_create_collation_v2(
                  pDb,
                  name,
                  capi.SQLITE_UTF8,
                  0,
                  0,
                  0,
                );
              } catch (e) {}
            }
            delete m.collation;
          }
          let i;
          for (i = 0; i < 2; ++i) {
            const fmap = i ? m.wudf : m.udf;
            if (!fmap) continue;
            const func = i
              ? capi.sqlite3_create_window_function
              : capi.sqlite3_create_function_v2;
            for (const e of fmap) {
              const name = e[0],
                arities = e[1];
              const fargs = [pDb, name, 0, capi.SQLITE_UTF8, 0, 0, 0, 0, 0];
              if (i) fargs.push(0);
              for (const arity of arities) {
                try {
                  fargs[2] = arity;
                  func.apply(null, fargs);
                } catch (e) {}
              }
              arities.clear();
            }
            fmap.clear();
          }
          delete m.udf;
          delete m.wudf;
        };

        {
          const __sqlite3CloseV2 = wasm.xWrap(
            'sqlite3_close_v2',
            'int',
            'sqlite3*',
          );
          capi.sqlite3_close_v2 = function (pDb) {
            if (1 !== arguments.length)
              return __dbArgcMismatch(pDb, 'sqlite3_close_v2', 1);
            if (pDb) {
              try {
                __dbCleanupMap.cleanup(pDb);
              } catch (e) {}
            }
            return __sqlite3CloseV2(pDb);
          };
        }

        if (capi.sqlite3session_create) {
          const __sqlite3SessionDelete = wasm.xWrap(
            'sqlite3session_delete',
            undefined,
            ['sqlite3_session*'],
          );
          capi.sqlite3session_delete = function (pSession) {
            if (1 !== arguments.length) {
              return __dbArgcMismatch(pDb, 'sqlite3session_delete', 1);
            } else if (pSession) {
              capi.sqlite3session_table_filter(pSession, 0, 0);
            }
            __sqlite3SessionDelete(pSession);
          };
        }

        {
          const contextKey = (argv, argIndex) => {
            return (
              'argv[' +
              argIndex +
              ']:' +
              argv[0] +
              ':' +
              wasm.cstrToJs(argv[1]).toLowerCase()
            );
          };
          const __sqlite3CreateCollationV2 = wasm.xWrap(
            'sqlite3_create_collation_v2',
            'int',
            [
              'sqlite3*',
              'string',
              'int',
              '*',
              new wasm.xWrap.FuncPtrAdapter({
                name: 'xCompare',
                signature: 'i(pipip)',
                contextKey,
              }),
              new wasm.xWrap.FuncPtrAdapter({
                name: 'xDestroy',
                signature: 'v(p)',
                contextKey,
              }),
            ],
          );

          capi.sqlite3_create_collation_v2 = function (
            pDb,
            zName,
            eTextRep,
            pArg,
            xCompare,
            xDestroy,
          ) {
            if (6 !== arguments.length)
              return __dbArgcMismatch(pDb, 'sqlite3_create_collation_v2', 6);
            else if (0 === (eTextRep & 0xf)) {
              eTextRep |= capi.SQLITE_UTF8;
            } else if (capi.SQLITE_UTF8 !== (eTextRep & 0xf)) {
              return __errEncoding(pDb);
            }
            try {
              const rc = __sqlite3CreateCollationV2(
                pDb,
                zName,
                eTextRep,
                pArg,
                xCompare,
                xDestroy,
              );
              if (0 === rc && xCompare instanceof Function) {
                __dbCleanupMap.addCollation(pDb, zName);
              }
              return rc;
            } catch (e) {
              return util.sqlite3__wasm_db_error(pDb, e);
            }
          };

          capi.sqlite3_create_collation = (
            pDb,
            zName,
            eTextRep,
            pArg,
            xCompare,
          ) => {
            return 5 === arguments.length
              ? capi.sqlite3_create_collation_v2(
                  pDb,
                  zName,
                  eTextRep,
                  pArg,
                  xCompare,
                  0,
                )
              : __dbArgcMismatch(pDb, 'sqlite3_create_collation', 5);
          };
        }

        {
          const contextKey = function (argv, argIndex) {
            return (
              argv[0] +
              ':' +
              (argv[2] < 0 ? -1 : argv[2]) +
              ':' +
              argIndex +
              ':' +
              wasm.cstrToJs(argv[1]).toLowerCase()
            );
          };

          const __cfProxy = Object.assign(Object.create(null), {
            xInverseAndStep: {
              signature: 'v(pip)',
              contextKey,
              callProxy: (callback) => {
                return (pCtx, argc, pArgv) => {
                  try {
                    callback(pCtx, ...capi.sqlite3_values_to_js(argc, pArgv));
                  } catch (e) {
                    capi.sqlite3_result_error_js(pCtx, e);
                  }
                };
              },
            },
            xFinalAndValue: {
              signature: 'v(p)',
              contextKey,
              callProxy: (callback) => {
                return (pCtx) => {
                  try {
                    capi.sqlite3_result_js(pCtx, callback(pCtx));
                  } catch (e) {
                    capi.sqlite3_result_error_js(pCtx, e);
                  }
                };
              },
            },
            xFunc: {
              signature: 'v(pip)',
              contextKey,
              callProxy: (callback) => {
                return (pCtx, argc, pArgv) => {
                  try {
                    capi.sqlite3_result_js(
                      pCtx,
                      callback(pCtx, ...capi.sqlite3_values_to_js(argc, pArgv)),
                    );
                  } catch (e) {
                    capi.sqlite3_result_error_js(pCtx, e);
                  }
                };
              },
            },
            xDestroy: {
              signature: 'v(p)',
              contextKey,

              callProxy: (callback) => {
                return (pVoid) => {
                  try {
                    callback(pVoid);
                  } catch (e) {
                    console.error('UDF xDestroy method threw:', e);
                  }
                };
              },
            },
          });

          const __sqlite3CreateFunction = wasm.xWrap(
            'sqlite3_create_function_v2',
            'int',
            [
              'sqlite3*',
              'string',
              'int',
              'int',
              '*',
              new wasm.xWrap.FuncPtrAdapter({
                name: 'xFunc',
                ...__cfProxy.xFunc,
              }),
              new wasm.xWrap.FuncPtrAdapter({
                name: 'xStep',
                ...__cfProxy.xInverseAndStep,
              }),
              new wasm.xWrap.FuncPtrAdapter({
                name: 'xFinal',
                ...__cfProxy.xFinalAndValue,
              }),
              new wasm.xWrap.FuncPtrAdapter({
                name: 'xDestroy',
                ...__cfProxy.xDestroy,
              }),
            ],
          );

          const __sqlite3CreateWindowFunction = wasm.exports
            .sqlite3_create_window_function
            ? wasm.xWrap('sqlite3_create_window_function', 'int', [
                'sqlite3*',
                'string',
                'int',
                'int',
                '*',
                new wasm.xWrap.FuncPtrAdapter({
                  name: 'xStep',
                  ...__cfProxy.xInverseAndStep,
                }),
                new wasm.xWrap.FuncPtrAdapter({
                  name: 'xFinal',
                  ...__cfProxy.xFinalAndValue,
                }),
                new wasm.xWrap.FuncPtrAdapter({
                  name: 'xValue',
                  ...__cfProxy.xFinalAndValue,
                }),
                new wasm.xWrap.FuncPtrAdapter({
                  name: 'xInverse',
                  ...__cfProxy.xInverseAndStep,
                }),
                new wasm.xWrap.FuncPtrAdapter({
                  name: 'xDestroy',
                  ...__cfProxy.xDestroy,
                }),
              ])
            : undefined;

          capi.sqlite3_create_function_v2 = function f(
            pDb,
            funcName,
            nArg,
            eTextRep,
            pApp,
            xFunc,
            xStep,
            xFinal,
            xDestroy,
          ) {
            if (f.length !== arguments.length) {
              return __dbArgcMismatch(
                pDb,
                'sqlite3_create_function_v2',
                f.length,
              );
            } else if (0 === (eTextRep & 0xf)) {
              eTextRep |= capi.SQLITE_UTF8;
            } else if (capi.SQLITE_UTF8 !== (eTextRep & 0xf)) {
              return __errEncoding(pDb);
            }
            try {
              const rc = __sqlite3CreateFunction(
                pDb,
                funcName,
                nArg,
                eTextRep,
                pApp,
                xFunc,
                xStep,
                xFinal,
                xDestroy,
              );
              if (
                0 === rc &&
                (xFunc instanceof Function ||
                  xStep instanceof Function ||
                  xFinal instanceof Function ||
                  xDestroy instanceof Function)
              ) {
                __dbCleanupMap.addFunction(pDb, funcName, nArg);
              }
              return rc;
            } catch (e) {
              console.error('sqlite3_create_function_v2() setup threw:', e);
              return util.sqlite3__wasm_db_error(
                pDb,
                e,
                'Creation of UDF threw: ' + e,
              );
            }
          };

          capi.sqlite3_create_function = function f(
            pDb,
            funcName,
            nArg,
            eTextRep,
            pApp,
            xFunc,
            xStep,
            xFinal,
          ) {
            return f.length === arguments.length
              ? capi.sqlite3_create_function_v2(
                  pDb,
                  funcName,
                  nArg,
                  eTextRep,
                  pApp,
                  xFunc,
                  xStep,
                  xFinal,
                  0,
                )
              : __dbArgcMismatch(pDb, 'sqlite3_create_function', f.length);
          };

          if (__sqlite3CreateWindowFunction) {
            capi.sqlite3_create_window_function = function f(
              pDb,
              funcName,
              nArg,
              eTextRep,
              pApp,
              xStep,
              xFinal,
              xValue,
              xInverse,
              xDestroy,
            ) {
              if (f.length !== arguments.length) {
                return __dbArgcMismatch(
                  pDb,
                  'sqlite3_create_window_function',
                  f.length,
                );
              } else if (0 === (eTextRep & 0xf)) {
                eTextRep |= capi.SQLITE_UTF8;
              } else if (capi.SQLITE_UTF8 !== (eTextRep & 0xf)) {
                return __errEncoding(pDb);
              }
              try {
                const rc = __sqlite3CreateWindowFunction(
                  pDb,
                  funcName,
                  nArg,
                  eTextRep,
                  pApp,
                  xStep,
                  xFinal,
                  xValue,
                  xInverse,
                  xDestroy,
                );
                if (
                  0 === rc &&
                  (xStep instanceof Function ||
                    xFinal instanceof Function ||
                    xValue instanceof Function ||
                    xInverse instanceof Function ||
                    xDestroy instanceof Function)
                ) {
                  __dbCleanupMap.addWindowFunc(pDb, funcName, nArg);
                }
                return rc;
              } catch (e) {
                console.error(
                  'sqlite3_create_window_function() setup threw:',
                  e,
                );
                return util.sqlite3__wasm_db_error(
                  pDb,
                  e,
                  'Creation of UDF threw: ' + e,
                );
              }
            };
          } else {
            delete capi.sqlite3_create_window_function;
          }

          capi.sqlite3_create_function_v2.udfSetResult =
            capi.sqlite3_create_function.udfSetResult = capi.sqlite3_result_js;
          if (capi.sqlite3_create_window_function) {
            capi.sqlite3_create_window_function.udfSetResult =
              capi.sqlite3_result_js;
          }

          capi.sqlite3_create_function_v2.udfConvertArgs =
            capi.sqlite3_create_function.udfConvertArgs =
              capi.sqlite3_values_to_js;
          if (capi.sqlite3_create_window_function) {
            capi.sqlite3_create_window_function.udfConvertArgs =
              capi.sqlite3_values_to_js;
          }

          capi.sqlite3_create_function_v2.udfSetError =
            capi.sqlite3_create_function.udfSetError =
              capi.sqlite3_result_error_js;
          if (capi.sqlite3_create_window_function) {
            capi.sqlite3_create_window_function.udfSetError =
              capi.sqlite3_result_error_js;
          }
        }

        {
          const __flexiString = (v, n) => {
            if ('string' === typeof v) {
              n = -1;
            } else if (util.isSQLableTypedArray(v)) {
              n = v.byteLength;
              v = util.typedArrayToString(
                v instanceof ArrayBuffer ? new Uint8Array(v) : v,
              );
            } else if (Array.isArray(v)) {
              v = v.join('');
              n = -1;
            }
            return [v, n];
          };

          const __prepare = {
            basic: wasm.xWrap('sqlite3_prepare_v3', 'int', [
              'sqlite3*',
              'string',
              'int',
              'int',
              '**',
              '**',
            ]),

            full: wasm.xWrap('sqlite3_prepare_v3', 'int', [
              'sqlite3*',
              '*',
              'int',
              'int',
              '**',
              '**',
            ]),
          };

          capi.sqlite3_prepare_v3 = function f(
            pDb,
            sql,
            sqlLen,
            prepFlags,
            ppStmt,
            pzTail,
          ) {
            if (f.length !== arguments.length) {
              return __dbArgcMismatch(pDb, 'sqlite3_prepare_v3', f.length);
            }
            const [xSql, xSqlLen] = __flexiString(sql, sqlLen);
            switch (typeof xSql) {
              case 'string':
                return __prepare.basic(
                  pDb,
                  xSql,
                  xSqlLen,
                  prepFlags,
                  ppStmt,
                  null,
                );
              case 'number':
                return __prepare.full(
                  pDb,
                  xSql,
                  xSqlLen,
                  prepFlags,
                  ppStmt,
                  pzTail,
                );
              default:
                return util.sqlite3__wasm_db_error(
                  pDb,
                  capi.SQLITE_MISUSE,
                  'Invalid SQL argument type for sqlite3_prepare_v2/v3().',
                );
            }
          };

          capi.sqlite3_prepare_v2 = function f(
            pDb,
            sql,
            sqlLen,
            ppStmt,
            pzTail,
          ) {
            return f.length === arguments.length
              ? capi.sqlite3_prepare_v3(pDb, sql, sqlLen, 0, ppStmt, pzTail)
              : __dbArgcMismatch(pDb, 'sqlite3_prepare_v2', f.length);
          };
        }

        {
          const __bindText = wasm.xWrap('sqlite3_bind_text', 'int', [
            'sqlite3_stmt*',
            'int',
            'string',
            'int',
            '*',
          ]);
          const __bindBlob = wasm.xWrap('sqlite3_bind_blob', 'int', [
            'sqlite3_stmt*',
            'int',
            '*',
            'int',
            '*',
          ]);

          capi.sqlite3_bind_text = function f(
            pStmt,
            iCol,
            text,
            nText,
            xDestroy,
          ) {
            if (f.length !== arguments.length) {
              return __dbArgcMismatch(
                capi.sqlite3_db_handle(pStmt),
                'sqlite3_bind_text',
                f.length,
              );
            } else if (wasm.isPtr(text) || null === text) {
              return __bindText(pStmt, iCol, text, nText, xDestroy);
            } else if (text instanceof ArrayBuffer) {
              text = new Uint8Array(text);
            } else if (Array.isArray(pMem)) {
              text = pMem.join('');
            }
            let p, n;
            try {
              if (util.isSQLableTypedArray(text)) {
                p = wasm.allocFromTypedArray(text);
                n = text.byteLength;
              } else if ('string' === typeof text) {
                [p, n] = wasm.allocCString(text);
              } else {
                return util.sqlite3__wasm_db_error(
                  capi.sqlite3_db_handle(pStmt),
                  capi.SQLITE_MISUSE,
                  'Invalid 3rd argument type for sqlite3_bind_text().',
                );
              }
              return __bindText(pStmt, iCol, p, n, capi.SQLITE_WASM_DEALLOC);
            } catch (e) {
              wasm.dealloc(p);
              return util.sqlite3__wasm_db_error(
                capi.sqlite3_db_handle(pStmt),
                e,
              );
            }
          };

          capi.sqlite3_bind_blob = function f(
            pStmt,
            iCol,
            pMem,
            nMem,
            xDestroy,
          ) {
            if (f.length !== arguments.length) {
              return __dbArgcMismatch(
                capi.sqlite3_db_handle(pStmt),
                'sqlite3_bind_blob',
                f.length,
              );
            } else if (wasm.isPtr(pMem) || null === pMem) {
              return __bindBlob(pStmt, iCol, pMem, nMem, xDestroy);
            } else if (pMem instanceof ArrayBuffer) {
              pMem = new Uint8Array(pMem);
            } else if (Array.isArray(pMem)) {
              pMem = pMem.join('');
            }
            let p, n;
            try {
              if (util.isBindableTypedArray(pMem)) {
                p = wasm.allocFromTypedArray(pMem);
                n = nMem >= 0 ? nMem : pMem.byteLength;
              } else if ('string' === typeof pMem) {
                [p, n] = wasm.allocCString(pMem);
              } else {
                return util.sqlite3__wasm_db_error(
                  capi.sqlite3_db_handle(pStmt),
                  capi.SQLITE_MISUSE,
                  'Invalid 3rd argument type for sqlite3_bind_blob().',
                );
              }
              return __bindBlob(pStmt, iCol, p, n, capi.SQLITE_WASM_DEALLOC);
            } catch (e) {
              wasm.dealloc(p);
              return util.sqlite3__wasm_db_error(
                capi.sqlite3_db_handle(pStmt),
                e,
              );
            }
          };
        }

        {
          capi.sqlite3_config = function (op, ...args) {
            if (arguments.length < 2) return capi.SQLITE_MISUSE;
            switch (op) {
              case capi.SQLITE_CONFIG_COVERING_INDEX_SCAN:
              case capi.SQLITE_CONFIG_MEMSTATUS:
              case capi.SQLITE_CONFIG_SMALL_MALLOC:
              case capi.SQLITE_CONFIG_SORTERREF_SIZE:
              case capi.SQLITE_CONFIG_STMTJRNL_SPILL:
              case capi.SQLITE_CONFIG_URI:
                return wasm.exports.sqlite3__wasm_config_i(op, args[0]);
              case capi.SQLITE_CONFIG_LOOKASIDE:
                return wasm.exports.sqlite3__wasm_config_ii(
                  op,
                  args[0],
                  args[1],
                );
              case capi.SQLITE_CONFIG_MEMDB_MAXSIZE:
                return wasm.exports.sqlite3__wasm_config_j(op, args[0]);
              case capi.SQLITE_CONFIG_GETMALLOC:
              case capi.SQLITE_CONFIG_GETMUTEX:
              case capi.SQLITE_CONFIG_GETPCACHE2:
              case capi.SQLITE_CONFIG_GETPCACHE:
              case capi.SQLITE_CONFIG_HEAP:
              case capi.SQLITE_CONFIG_LOG:
              case capi.SQLITE_CONFIG_MALLOC:
              case capi.SQLITE_CONFIG_MMAP_SIZE:
              case capi.SQLITE_CONFIG_MULTITHREAD:
              case capi.SQLITE_CONFIG_MUTEX:
              case capi.SQLITE_CONFIG_PAGECACHE:
              case capi.SQLITE_CONFIG_PCACHE2:
              case capi.SQLITE_CONFIG_PCACHE:
              case capi.SQLITE_CONFIG_PCACHE_HDRSZ:
              case capi.SQLITE_CONFIG_PMASZ:
              case capi.SQLITE_CONFIG_SERIALIZED:
              case capi.SQLITE_CONFIG_SINGLETHREAD:
              case capi.SQLITE_CONFIG_SQLLOG:
              case capi.SQLITE_CONFIG_WIN32_HEAPSIZE:
              default:
                return capi.SQLITE_NOTFOUND;
            }
          };
        }

        {
          const __autoExtFptr = new Set();

          capi.sqlite3_auto_extension = function (fPtr) {
            if (fPtr instanceof Function) {
              fPtr = wasm.installFunction('i(ppp)', fPtr);
            } else if (1 !== arguments.length || !wasm.isPtr(fPtr)) {
              return capi.SQLITE_MISUSE;
            }
            const rc = wasm.exports.sqlite3_auto_extension(fPtr);
            if (fPtr !== arguments[0]) {
              if (0 === rc) __autoExtFptr.add(fPtr);
              else wasm.uninstallFunction(fPtr);
            }
            return rc;
          };

          capi.sqlite3_cancel_auto_extension = function (fPtr) {
            if (!fPtr || 1 !== arguments.length || !wasm.isPtr(fPtr)) return 0;
            return wasm.exports.sqlite3_cancel_auto_extension(fPtr);
          };

          capi.sqlite3_reset_auto_extension = function () {
            wasm.exports.sqlite3_reset_auto_extension();
            for (const fp of __autoExtFptr) wasm.uninstallFunction(fp);
            __autoExtFptr.clear();
          };
        }

        const pKvvfs = capi.sqlite3_vfs_find('kvvfs');
        if (pKvvfs) {
          if (util.isUIThread()) {
            const kvvfsMethods = new capi.sqlite3_kvvfs_methods(
              wasm.exports.sqlite3__wasm_kvvfs_methods(),
            );
            delete capi.sqlite3_kvvfs_methods;

            const kvvfsMakeKey =
                wasm.exports.sqlite3__wasm_kvvfsMakeKeyOnPstack,
              pstack = wasm.pstack;

            const kvvfsStorage = (zClass) =>
              115 === wasm.peek(zClass) ? sessionStorage : localStorage;

            const kvvfsImpls = {
              xRead: (zClass, zKey, zBuf, nBuf) => {
                const stack = pstack.pointer,
                  astack = wasm.scopedAllocPush();
                try {
                  const zXKey = kvvfsMakeKey(zClass, zKey);
                  if (!zXKey) return -3;
                  const jKey = wasm.cstrToJs(zXKey);
                  const jV = kvvfsStorage(zClass).getItem(jKey);
                  if (!jV) return -1;
                  const nV = jV.length;
                  if (nBuf <= 0) return nV;
                  else if (1 === nBuf) {
                    wasm.poke(zBuf, 0);
                    return nV;
                  }
                  const zV = wasm.scopedAllocCString(jV);
                  if (nBuf > nV + 1) nBuf = nV + 1;
                  wasm.heap8u().copyWithin(zBuf, zV, zV + nBuf - 1);
                  wasm.poke(zBuf + nBuf - 1, 0);
                  return nBuf - 1;
                } catch (e) {
                  console.error('kvstorageRead()', e);
                  return -2;
                } finally {
                  pstack.restore(stack);
                  wasm.scopedAllocPop(astack);
                }
              },
              xWrite: (zClass, zKey, zData) => {
                const stack = pstack.pointer;
                try {
                  const zXKey = kvvfsMakeKey(zClass, zKey);
                  if (!zXKey) return 1;
                  const jKey = wasm.cstrToJs(zXKey);
                  kvvfsStorage(zClass).setItem(jKey, wasm.cstrToJs(zData));
                  return 0;
                } catch (e) {
                  console.error('kvstorageWrite()', e);
                  return capi.SQLITE_IOERR;
                } finally {
                  pstack.restore(stack);
                }
              },
              xDelete: (zClass, zKey) => {
                const stack = pstack.pointer;
                try {
                  const zXKey = kvvfsMakeKey(zClass, zKey);
                  if (!zXKey) return 1;
                  kvvfsStorage(zClass).removeItem(wasm.cstrToJs(zXKey));
                  return 0;
                } catch (e) {
                  console.error('kvstorageDelete()', e);
                  return capi.SQLITE_IOERR;
                } finally {
                  pstack.restore(stack);
                }
              },
            };
            for (const k of Object.keys(kvvfsImpls)) {
              kvvfsMethods[kvvfsMethods.memberKey(k)] = wasm.installFunction(
                kvvfsMethods.memberSignature(k),
                kvvfsImpls[k],
              );
            }
          } else {
            capi.sqlite3_vfs_unregister(pKvvfs);
          }
        }

        wasm.xWrap.FuncPtrAdapter.warnOnUse = true;

        const StructBinder = sqlite3.StructBinder;
        const installMethod = function callee(
          tgt,
          name,
          func,
          applyArgcCheck = callee.installMethodArgcCheck,
        ) {
          if (!(tgt instanceof StructBinder.StructType)) {
            toss('Usage error: target object is-not-a StructType.');
          } else if (!(func instanceof Function) && !wasm.isPtr(func)) {
            toss('Usage errror: expecting a Function or WASM pointer to one.');
          }
          if (1 === arguments.length) {
            return (n, f) => callee(tgt, n, f, applyArgcCheck);
          }
          if (!callee.argcProxy) {
            callee.argcProxy = function (tgt, funcName, func, sig) {
              return function (...args) {
                if (func.length !== arguments.length) {
                  toss(
                    'Argument mismatch for',
                    tgt.structInfo.name +
                      '::' +
                      funcName +
                      ': Native signature is:',
                    sig,
                  );
                }
                return func.apply(this, args);
              };
            };

            callee.removeFuncList = function () {
              if (this.ondispose.__removeFuncList) {
                this.ondispose.__removeFuncList.forEach((v, ndx) => {
                  if ('number' === typeof v) {
                    try {
                      wasm.uninstallFunction(v);
                    } catch (e) {}
                  }
                });
                delete this.ondispose.__removeFuncList;
              }
            };
          }
          const sigN = tgt.memberSignature(name);
          if (sigN.length < 2) {
            toss(
              'Member',
              name,
              'does not have a function pointer signature:',
              sigN,
            );
          }
          const memKey = tgt.memberKey(name);
          const fProxy =
            applyArgcCheck && !wasm.isPtr(func)
              ? callee.argcProxy(tgt, memKey, func, sigN)
              : func;
          if (wasm.isPtr(fProxy)) {
            if (fProxy && !wasm.functionEntry(fProxy)) {
              toss('Pointer', fProxy, 'is not a WASM function table entry.');
            }
            tgt[memKey] = fProxy;
          } else {
            const pFunc = wasm.installFunction(
              fProxy,
              tgt.memberSignature(name, true),
            );
            tgt[memKey] = pFunc;
            if (!tgt.ondispose || !tgt.ondispose.__removeFuncList) {
              tgt.addOnDispose(
                'ondispose.__removeFuncList handler',
                callee.removeFuncList,
              );
              tgt.ondispose.__removeFuncList = [];
            }
            tgt.ondispose.__removeFuncList.push(memKey, pFunc);
          }
          return (n, f) => callee(tgt, n, f, applyArgcCheck);
        };
        installMethod.installMethodArgcCheck = false;

        const installMethods = function (
          structInstance,
          methods,
          applyArgcCheck = installMethod.installMethodArgcCheck,
        ) {
          const seen = new Map();
          for (const k of Object.keys(methods)) {
            const m = methods[k];
            const prior = seen.get(m);
            if (prior) {
              const mkey = structInstance.memberKey(k);
              structInstance[mkey] =
                structInstance[structInstance.memberKey(prior)];
            } else {
              installMethod(structInstance, k, m, applyArgcCheck);
              seen.set(m, k);
            }
          }
          return structInstance;
        };

        StructBinder.StructType.prototype.installMethod = function callee(
          name,
          func,
          applyArgcCheck = installMethod.installMethodArgcCheck,
        ) {
          return arguments.length < 3 && name && 'object' === typeof name
            ? installMethods(this, ...arguments)
            : installMethod(this, ...arguments);
        };

        StructBinder.StructType.prototype.installMethods = function (
          methods,
          applyArgcCheck = installMethod.installMethodArgcCheck,
        ) {
          return installMethods(this, methods, applyArgcCheck);
        };
      });

      globalThis.sqlite3ApiBootstrap.initializers.push(function (sqlite3) {
        sqlite3.version = {
          libVersion: '3.49.2',
          libVersionNumber: 3049002,
          sourceId:
            '2025-05-07 10:39:52 17144570b0d96ae63cd6f3edca39e27ebd74925252bbaf6723bcb2f6b4861fb1',
          downloadVersion: 3490200,
        };
      });

      globalThis.sqlite3ApiBootstrap.initializers.push(function (sqlite3) {
        const toss = (...args) => {
          throw new Error(args.join(' '));
        };
        const toss3 = (...args) => {
          throw new sqlite3.SQLite3Error(...args);
        };

        const capi = sqlite3.capi,
          wasm = sqlite3.wasm,
          util = sqlite3.util;

        const __ptrMap = new WeakMap();

        const __stmtMap = new WeakMap();

        const getOwnOption = (opts, p, dflt) => {
          const d = Object.getOwnPropertyDescriptor(opts, p);
          return d ? d.value : dflt;
        };

        const checkSqlite3Rc = function (dbPtr, sqliteResultCode) {
          if (sqliteResultCode) {
            if (dbPtr instanceof DB) dbPtr = dbPtr.pointer;
            toss3(
              sqliteResultCode,
              'sqlite3 result code',
              sqliteResultCode + ':',
              dbPtr
                ? capi.sqlite3_errmsg(dbPtr)
                : capi.sqlite3_errstr(sqliteResultCode),
            );
          }
          return arguments[0];
        };

        const __dbTraceToConsole = wasm.installFunction(
          'i(ippp)',
          function (t, c, p, x) {
            if (capi.SQLITE_TRACE_STMT === t) {
              console.log(
                'SQL TRACE #' + ++this.counter + ' via sqlite3@' + c + ':',
                wasm.cstrToJs(x),
              );
            }
          }.bind({ counter: 0 }),
        );

        const __vfsPostOpenCallback = Object.create(null);

        const dbCtorHelper = function ctor(...args) {
          if (!ctor._name2vfs) {
            ctor._name2vfs = Object.create(null);
            const isWorkerThread =
              'function' === typeof importScripts
                ? (n) =>
                    toss3(
                      'The VFS for',
                      n,
                      'is only available in the main window thread.',
                    )
                : false;
            ctor._name2vfs[':localStorage:'] = {
              vfs: 'kvvfs',
              filename: isWorkerThread || (() => 'local'),
            };
            ctor._name2vfs[':sessionStorage:'] = {
              vfs: 'kvvfs',
              filename: isWorkerThread || (() => 'session'),
            };
          }
          const opt = ctor.normalizeArgs(...args);
          let fn = opt.filename,
            vfsName = opt.vfs,
            flagsStr = opt.flags;
          if (
            ('string' !== typeof fn && 'number' !== typeof fn) ||
            'string' !== typeof flagsStr ||
            (vfsName &&
              'string' !== typeof vfsName &&
              'number' !== typeof vfsName)
          ) {
            sqlite3.config.error('Invalid DB ctor args', opt, arguments);
            toss3('Invalid arguments for DB constructor.');
          }
          let fnJs = 'number' === typeof fn ? wasm.cstrToJs(fn) : fn;
          const vfsCheck = ctor._name2vfs[fnJs];
          if (vfsCheck) {
            vfsName = vfsCheck.vfs;
            fn = fnJs = vfsCheck.filename(fnJs);
          }
          let pDb,
            oflags = 0;
          if (flagsStr.indexOf('c') >= 0) {
            oflags |= capi.SQLITE_OPEN_CREATE | capi.SQLITE_OPEN_READWRITE;
          }
          if (flagsStr.indexOf('w') >= 0) oflags |= capi.SQLITE_OPEN_READWRITE;
          if (0 === oflags) oflags |= capi.SQLITE_OPEN_READONLY;
          oflags |= capi.SQLITE_OPEN_EXRESCODE;
          const stack = wasm.pstack.pointer;
          try {
            const pPtr = wasm.pstack.allocPtr();
            let rc = capi.sqlite3_open_v2(fn, pPtr, oflags, vfsName || 0);
            pDb = wasm.peekPtr(pPtr);
            checkSqlite3Rc(pDb, rc);
            capi.sqlite3_extended_result_codes(pDb, 1);
            if (flagsStr.indexOf('t') >= 0) {
              capi.sqlite3_trace_v2(
                pDb,
                capi.SQLITE_TRACE_STMT,
                __dbTraceToConsole,
                pDb,
              );
            }
          } catch (e) {
            if (pDb) capi.sqlite3_close_v2(pDb);
            throw e;
          } finally {
            wasm.pstack.restore(stack);
          }
          this.filename = fnJs;
          __ptrMap.set(this, pDb);
          __stmtMap.set(this, Object.create(null));
          try {
            const pVfs =
              capi.sqlite3_js_db_vfs(pDb) ||
              toss3('Internal error: cannot get VFS for new db handle.');
            const postInitSql = __vfsPostOpenCallback[pVfs];
            if (postInitSql) {
              if (postInitSql instanceof Function) {
                postInitSql(this, sqlite3);
              } else {
                checkSqlite3Rc(
                  pDb,
                  capi.sqlite3_exec(pDb, postInitSql, 0, 0, 0),
                );
              }
            }
          } catch (e) {
            this.close();
            throw e;
          }
        };

        dbCtorHelper.setVfsPostOpenCallback = function (pVfs, callback) {
          if (!(callback instanceof Function)) {
            toss3(
              'dbCtorHelper.setVfsPostOpenCallback() should not be used with ' +
                'a non-function argument.',
              arguments,
            );
          }
          __vfsPostOpenCallback[pVfs] = callback;
        };

        dbCtorHelper.normalizeArgs = function (
          filename = ':memory:',
          flags = 'c',
          vfs = null,
        ) {
          const arg = {};
          if (
            1 === arguments.length &&
            arguments[0] &&
            'object' === typeof arguments[0]
          ) {
            Object.assign(arg, arguments[0]);
            if (undefined === arg.flags) arg.flags = 'c';
            if (undefined === arg.vfs) arg.vfs = null;
            if (undefined === arg.filename) arg.filename = ':memory:';
          } else {
            arg.filename = filename;
            arg.flags = flags;
            arg.vfs = vfs;
          }
          return arg;
        };

        const DB = function (...args) {
          dbCtorHelper.apply(this, args);
        };
        DB.dbCtorHelper = dbCtorHelper;

        const BindTypes = {
          null: 1,
          number: 2,
          string: 3,
          boolean: 4,
          blob: 5,
        };
        BindTypes['undefined'] == BindTypes.null;
        if (wasm.bigIntEnabled) {
          BindTypes.bigint = BindTypes.number;
        }

        const Stmt = function () {
          if (BindTypes !== arguments[2]) {
            toss3(
              capi.SQLITE_MISUSE,
              'Do not call the Stmt constructor directly. Use DB.prepare().',
            );
          }
          this.db = arguments[0];
          __ptrMap.set(this, arguments[1]);
          this.parameterCount = capi.sqlite3_bind_parameter_count(this.pointer);
        };

        const affirmDbOpen = function (db) {
          if (!db.pointer) toss3('DB has been closed.');
          return db;
        };

        const affirmColIndex = function (stmt, ndx) {
          if (ndx !== (ndx | 0) || ndx < 0 || ndx >= stmt.columnCount) {
            toss3('Column index', ndx, 'is out of range.');
          }
          return stmt;
        };

        const parseExecArgs = function (db, args) {
          const out = Object.create(null);
          out.opt = Object.create(null);
          switch (args.length) {
            case 1:
              if (
                'string' === typeof args[0] ||
                util.isSQLableTypedArray(args[0])
              ) {
                out.sql = args[0];
              } else if (Array.isArray(args[0])) {
                out.sql = args[0];
              } else if (args[0] && 'object' === typeof args[0]) {
                out.opt = args[0];
                out.sql = out.opt.sql;
              }
              break;
            case 2:
              out.sql = args[0];
              out.opt = args[1];
              break;
            default:
              toss3('Invalid argument count for exec().');
          }
          out.sql = util.flexibleString(out.sql);
          if ('string' !== typeof out.sql) {
            toss3('Missing SQL argument or unsupported SQL value type.');
          }
          const opt = out.opt;
          switch (opt.returnValue) {
            case 'resultRows':
              if (!opt.resultRows) opt.resultRows = [];
              out.returnVal = () => opt.resultRows;
              break;
            case 'saveSql':
              if (!opt.saveSql) opt.saveSql = [];
              out.returnVal = () => opt.saveSql;
              break;
            case undefined:
            case 'this':
              out.returnVal = () => db;
              break;
            default:
              toss3('Invalid returnValue value:', opt.returnValue);
          }
          if (!opt.callback && !opt.returnValue && undefined !== opt.rowMode) {
            if (!opt.resultRows) opt.resultRows = [];
            out.returnVal = () => opt.resultRows;
          }
          if (opt.callback || opt.resultRows) {
            switch (undefined === opt.rowMode ? 'array' : opt.rowMode) {
              case 'object':
                out.cbArg = (stmt, cache) => {
                  if (!cache.columnNames)
                    cache.columnNames = stmt.getColumnNames([]);

                  const row = stmt.get([]);
                  const rv = Object.create(null);
                  for (const i in cache.columnNames)
                    rv[cache.columnNames[i]] = row[i];
                  return rv;
                };
                break;
              case 'array':
                out.cbArg = (stmt) => stmt.get([]);
                break;
              case 'stmt':
                if (Array.isArray(opt.resultRows)) {
                  toss3(
                    'exec(): invalid rowMode for a resultRows array: must',
                    "be one of 'array', 'object',",
                    'a result column number, or column name reference.',
                  );
                }
                out.cbArg = (stmt) => stmt;
                break;
              default:
                if (util.isInt32(opt.rowMode)) {
                  out.cbArg = (stmt) => stmt.get(opt.rowMode);
                  break;
                } else if (
                  'string' === typeof opt.rowMode &&
                  opt.rowMode.length > 1 &&
                  '$' === opt.rowMode[0]
                ) {
                  const $colName = opt.rowMode.substr(1);
                  out.cbArg = (stmt) => {
                    const rc = stmt.get(Object.create(null))[$colName];
                    return undefined === rc
                      ? toss3(
                          capi.SQLITE_NOTFOUND,
                          'exec(): unknown result column:',
                          $colName,
                        )
                      : rc;
                  };
                  break;
                }
                toss3('Invalid rowMode:', opt.rowMode);
            }
          }
          return out;
        };

        const __selectFirstRow = (db, sql, bind, ...getArgs) => {
          const stmt = db.prepare(sql);
          try {
            const rc = stmt.bind(bind).step()
              ? stmt.get(...getArgs)
              : undefined;
            stmt.reset();
            return rc;
          } finally {
            stmt.finalize();
          }
        };

        const __selectAll = (db, sql, bind, rowMode) =>
          db.exec({
            sql,
            bind,
            rowMode,
            returnValue: 'resultRows',
          });

        DB.checkRc = (db, resultCode) => checkSqlite3Rc(db, resultCode);

        DB.prototype = {
          isOpen: function () {
            return !!this.pointer;
          },

          affirmOpen: function () {
            return affirmDbOpen(this);
          },

          close: function () {
            if (this.pointer) {
              if (this.onclose && this.onclose.before instanceof Function) {
                try {
                  this.onclose.before(this);
                } catch (e) {}
              }
              const pDb = this.pointer;
              Object.keys(__stmtMap.get(this)).forEach((k, s) => {
                if (s && s.pointer) {
                  try {
                    s.finalize();
                  } catch (e) {}
                }
              });
              __ptrMap.delete(this);
              __stmtMap.delete(this);
              capi.sqlite3_close_v2(pDb);
              if (this.onclose && this.onclose.after instanceof Function) {
                try {
                  this.onclose.after(this);
                } catch (e) {}
              }
              delete this.filename;
            }
          },

          changes: function (total = false, sixtyFour = false) {
            const p = affirmDbOpen(this).pointer;
            if (total) {
              return sixtyFour
                ? capi.sqlite3_total_changes64(p)
                : capi.sqlite3_total_changes(p);
            } else {
              return sixtyFour
                ? capi.sqlite3_changes64(p)
                : capi.sqlite3_changes(p);
            }
          },

          dbFilename: function (dbName = 'main') {
            return capi.sqlite3_db_filename(affirmDbOpen(this).pointer, dbName);
          },

          dbName: function (dbNumber = 0) {
            return capi.sqlite3_db_name(affirmDbOpen(this).pointer, dbNumber);
          },

          dbVfsName: function (dbName = 0) {
            let rc;
            const pVfs = capi.sqlite3_js_db_vfs(
              affirmDbOpen(this).pointer,
              dbName,
            );
            if (pVfs) {
              const v = new capi.sqlite3_vfs(pVfs);
              try {
                rc = wasm.cstrToJs(v.$zName);
              } finally {
                v.dispose();
              }
            }
            return rc;
          },

          prepare: function (sql) {
            affirmDbOpen(this);
            const stack = wasm.pstack.pointer;
            let ppStmt, pStmt;
            try {
              ppStmt = wasm.pstack.alloc(8);
              DB.checkRc(
                this,
                capi.sqlite3_prepare_v2(this.pointer, sql, -1, ppStmt, null),
              );
              pStmt = wasm.peekPtr(ppStmt);
            } finally {
              wasm.pstack.restore(stack);
            }
            if (!pStmt) toss3('Cannot prepare empty SQL.');
            const stmt = new Stmt(this, pStmt, BindTypes);
            __stmtMap.get(this)[pStmt] = stmt;
            return stmt;
          },

          exec: function () {
            affirmDbOpen(this);
            const arg = parseExecArgs(this, arguments);
            if (!arg.sql) {
              return toss3('exec() requires an SQL string.');
            }
            const opt = arg.opt;
            const callback = opt.callback;
            const resultRows = Array.isArray(opt.resultRows)
              ? opt.resultRows
              : undefined;
            let stmt;
            let bind = opt.bind;
            let evalFirstResult = !!(
              arg.cbArg ||
              opt.columnNames ||
              resultRows
            );
            const stack = wasm.scopedAllocPush();
            const saveSql = Array.isArray(opt.saveSql)
              ? opt.saveSql
              : undefined;
            try {
              const isTA = util.isSQLableTypedArray(arg.sql);
              let sqlByteLen = isTA
                ? arg.sql.byteLength
                : wasm.jstrlen(arg.sql);
              const ppStmt = wasm.scopedAlloc(
                2 * wasm.ptrSizeof + (sqlByteLen + 1),
              );
              const pzTail = ppStmt + wasm.ptrSizeof;
              let pSql = pzTail + wasm.ptrSizeof;
              const pSqlEnd = pSql + sqlByteLen;
              if (isTA) wasm.heap8().set(arg.sql, pSql);
              else wasm.jstrcpy(arg.sql, wasm.heap8(), pSql, sqlByteLen, false);
              wasm.poke(pSql + sqlByteLen, 0);
              while (pSql && wasm.peek(pSql, 'i8')) {
                wasm.pokePtr([ppStmt, pzTail], 0);
                DB.checkRc(
                  this,
                  capi.sqlite3_prepare_v3(
                    this.pointer,
                    pSql,
                    sqlByteLen,
                    0,
                    ppStmt,
                    pzTail,
                  ),
                );
                const pStmt = wasm.peekPtr(ppStmt);
                pSql = wasm.peekPtr(pzTail);
                sqlByteLen = pSqlEnd - pSql;
                if (!pStmt) continue;
                if (saveSql) saveSql.push(capi.sqlite3_sql(pStmt).trim());
                stmt = new Stmt(this, pStmt, BindTypes);
                if (bind && stmt.parameterCount) {
                  stmt.bind(bind);
                  bind = null;
                }
                if (evalFirstResult && stmt.columnCount) {
                  let gotColNames = Array.isArray(opt.columnNames) ? 0 : 1;
                  evalFirstResult = false;
                  if (arg.cbArg || resultRows) {
                    const cbArgCache = Object.create(null);
                    for (; stmt.step(); stmt._lockedByExec = false) {
                      if (0 === gotColNames++) {
                        stmt.getColumnNames(
                          (cbArgCache.columnNames = opt.columnNames || []),
                        );
                      }
                      stmt._lockedByExec = true;
                      const row = arg.cbArg(stmt, cbArgCache);
                      if (resultRows) resultRows.push(row);
                      if (callback && false === callback.call(opt, row, stmt)) {
                        break;
                      }
                    }
                    stmt._lockedByExec = false;
                  }
                  if (0 === gotColNames) {
                    stmt.getColumnNames(opt.columnNames);
                  }
                } else {
                  stmt.step();
                }
                stmt.reset().finalize();
                stmt = null;
              }
            } finally {
              wasm.scopedAllocPop(stack);
              if (stmt) {
                delete stmt._lockedByExec;
                stmt.finalize();
              }
            }
            return arg.returnVal();
          },

          createFunction: function f(name, xFunc, opt) {
            const isFunc = (f) => f instanceof Function;
            switch (arguments.length) {
              case 1:
                opt = name;
                name = opt.name;
                xFunc = opt.xFunc || 0;
                break;
              case 2:
                if (!isFunc(xFunc)) {
                  opt = xFunc;
                  xFunc = opt.xFunc || 0;
                }
                break;
              case 3:
                break;
              default:
                break;
            }
            if (!opt) opt = {};
            if ('string' !== typeof name) {
              toss3('Invalid arguments: missing function name.');
            }
            let xStep = opt.xStep || 0;
            let xFinal = opt.xFinal || 0;
            const xValue = opt.xValue || 0;
            const xInverse = opt.xInverse || 0;
            let isWindow = undefined;
            if (isFunc(xFunc)) {
              isWindow = false;
              if (isFunc(xStep) || isFunc(xFinal)) {
                toss3('Ambiguous arguments: scalar or aggregate?');
              }
              xStep = xFinal = null;
            } else if (isFunc(xStep)) {
              if (!isFunc(xFinal)) {
                toss3('Missing xFinal() callback for aggregate or window UDF.');
              }
              xFunc = null;
            } else if (isFunc(xFinal)) {
              toss3('Missing xStep() callback for aggregate or window UDF.');
            } else {
              toss3('Missing function-type properties.');
            }
            if (false === isWindow) {
              if (isFunc(xValue) || isFunc(xInverse)) {
                toss3(
                  'xValue and xInverse are not permitted for non-window UDFs.',
                );
              }
            } else if (isFunc(xValue)) {
              if (!isFunc(xInverse)) {
                toss3('xInverse must be provided if xValue is.');
              }
              isWindow = true;
            } else if (isFunc(xInverse)) {
              toss3('xValue must be provided if xInverse is.');
            }
            const pApp = opt.pApp;
            if (
              undefined !== pApp &&
              null !== pApp &&
              ('number' !== typeof pApp || !util.isInt32(pApp))
            ) {
              toss3(
                'Invalid value for pApp property. Must be a legal WASM pointer value.',
              );
            }
            const xDestroy = opt.xDestroy || 0;
            if (xDestroy && !isFunc(xDestroy)) {
              toss3('xDestroy property must be a function.');
            }
            let fFlags = 0;
            if (getOwnOption(opt, 'deterministic'))
              fFlags |= capi.SQLITE_DETERMINISTIC;
            if (getOwnOption(opt, 'directOnly'))
              fFlags |= capi.SQLITE_DIRECTONLY;
            if (getOwnOption(opt, 'innocuous')) fFlags |= capi.SQLITE_INNOCUOUS;
            name = name.toLowerCase();
            const xArity = xFunc || xStep;
            const arity = getOwnOption(opt, 'arity');
            const arityArg =
              'number' === typeof arity
                ? arity
                : xArity.length
                  ? xArity.length - 1
                  : 0;
            let rc;
            if (isWindow) {
              rc = capi.sqlite3_create_window_function(
                this.pointer,
                name,
                arityArg,
                capi.SQLITE_UTF8 | fFlags,
                pApp || 0,
                xStep,
                xFinal,
                xValue,
                xInverse,
                xDestroy,
              );
            } else {
              rc = capi.sqlite3_create_function_v2(
                this.pointer,
                name,
                arityArg,
                capi.SQLITE_UTF8 | fFlags,
                pApp || 0,
                xFunc,
                xStep,
                xFinal,
                xDestroy,
              );
            }
            DB.checkRc(this, rc);
            return this;
          },

          selectValue: function (sql, bind, asType) {
            return __selectFirstRow(this, sql, bind, 0, asType);
          },

          selectValues: function (sql, bind, asType) {
            const stmt = this.prepare(sql),
              rc = [];
            try {
              stmt.bind(bind);
              while (stmt.step()) rc.push(stmt.get(0, asType));
              stmt.reset();
            } finally {
              stmt.finalize();
            }
            return rc;
          },

          selectArray: function (sql, bind) {
            return __selectFirstRow(this, sql, bind, []);
          },

          selectObject: function (sql, bind) {
            return __selectFirstRow(this, sql, bind, {});
          },

          selectArrays: function (sql, bind) {
            return __selectAll(this, sql, bind, 'array');
          },

          selectObjects: function (sql, bind) {
            return __selectAll(this, sql, bind, 'object');
          },

          openStatementCount: function () {
            return this.pointer ? Object.keys(__stmtMap.get(this)).length : 0;
          },

          transaction: function (callback) {
            let opener = 'BEGIN';
            if (arguments.length > 1) {
              if (/[^a-zA-Z]/.test(arguments[0])) {
                toss3(
                  capi.SQLITE_MISUSE,
                  'Invalid argument for BEGIN qualifier.',
                );
              }
              opener += ' ' + arguments[0];
              callback = arguments[1];
            }
            affirmDbOpen(this).exec(opener);
            try {
              const rc = callback(this);
              this.exec('COMMIT');
              return rc;
            } catch (e) {
              this.exec('ROLLBACK');
              throw e;
            }
          },

          savepoint: function (callback) {
            affirmDbOpen(this).exec('SAVEPOINT oo1');
            try {
              const rc = callback(this);
              this.exec('RELEASE oo1');
              return rc;
            } catch (e) {
              this.exec('ROLLBACK to SAVEPOINT oo1; RELEASE SAVEPOINT oo1');
              throw e;
            }
          },

          checkRc: function (resultCode) {
            return checkSqlite3Rc(this, resultCode);
          },
        };

        const affirmStmtOpen = function (stmt) {
          if (!stmt.pointer) toss3('Stmt has been closed.');
          return stmt;
        };

        const isSupportedBindType = function (v) {
          let t = BindTypes[null === v || undefined === v ? 'null' : typeof v];
          switch (t) {
            case BindTypes.boolean:
            case BindTypes.null:
            case BindTypes.number:
            case BindTypes.string:
              return t;
            case BindTypes.bigint:
              if (wasm.bigIntEnabled) return t;

            default:
              return util.isBindableTypedArray(v) ? BindTypes.blob : undefined;
          }
        };

        const affirmSupportedBindType = function (v) {
          return (
            isSupportedBindType(v) ||
            toss3('Unsupported bind() argument type:', typeof v)
          );
        };

        const affirmParamIndex = function (stmt, key) {
          const n =
            'number' === typeof key
              ? key
              : capi.sqlite3_bind_parameter_index(stmt.pointer, key);
          if (0 === n || !util.isInt32(n)) {
            toss3('Invalid bind() parameter name: ' + key);
          } else if (n < 1 || n > stmt.parameterCount)
            toss3('Bind index', key, 'is out of range.');
          return n;
        };

        const affirmNotLockedByExec = function (stmt, currentOpName) {
          if (stmt._lockedByExec) {
            toss3(
              'Operation is illegal when statement is locked:',
              currentOpName,
            );
          }
          return stmt;
        };

        const bindOne = function f(stmt, ndx, bindType, val) {
          affirmNotLockedByExec(affirmStmtOpen(stmt), 'bind()');
          if (!f._) {
            f._tooBigInt = (v) =>
              toss3(
                'BigInt value is too big to store without precision loss:',
                v,
              );
            f._ = {
              string: function (stmt, ndx, val, asBlob) {
                const [pStr, n] = wasm.allocCString(val, true);
                const f = asBlob
                  ? capi.sqlite3_bind_blob
                  : capi.sqlite3_bind_text;
                return f(stmt.pointer, ndx, pStr, n, capi.SQLITE_WASM_DEALLOC);
              },
            };
          }
          affirmSupportedBindType(val);
          ndx = affirmParamIndex(stmt, ndx);
          let rc = 0;
          switch (
            null === val || undefined === val ? BindTypes.null : bindType
          ) {
            case BindTypes.null:
              rc = capi.sqlite3_bind_null(stmt.pointer, ndx);
              break;
            case BindTypes.string:
              rc = f._.string(stmt, ndx, val, false);
              break;
            case BindTypes.number: {
              let m;
              if (util.isInt32(val)) m = capi.sqlite3_bind_int;
              else if ('bigint' === typeof val) {
                if (!util.bigIntFits64(val)) {
                  f._tooBigInt(val);
                } else if (wasm.bigIntEnabled) {
                  m = capi.sqlite3_bind_int64;
                } else if (util.bigIntFitsDouble(val)) {
                  val = Number(val);
                  m = capi.sqlite3_bind_double;
                } else {
                  f._tooBigInt(val);
                }
              } else {
                val = Number(val);
                if (wasm.bigIntEnabled && Number.isInteger(val)) {
                  m = capi.sqlite3_bind_int64;
                } else {
                  m = capi.sqlite3_bind_double;
                }
              }
              rc = m(stmt.pointer, ndx, val);
              break;
            }
            case BindTypes.boolean:
              rc = capi.sqlite3_bind_int(stmt.pointer, ndx, val ? 1 : 0);
              break;
            case BindTypes.blob: {
              if ('string' === typeof val) {
                rc = f._.string(stmt, ndx, val, true);
                break;
              } else if (val instanceof ArrayBuffer) {
                val = new Uint8Array(val);
              } else if (!util.isBindableTypedArray(val)) {
                toss3(
                  'Binding a value as a blob requires',
                  'that it be a string, Uint8Array, Int8Array, or ArrayBuffer.',
                );
              }
              const pBlob = wasm.alloc(val.byteLength || 1);
              wasm.heap8().set(val.byteLength ? val : [0], pBlob);
              rc = capi.sqlite3_bind_blob(
                stmt.pointer,
                ndx,
                pBlob,
                val.byteLength,
                capi.SQLITE_WASM_DEALLOC,
              );
              break;
            }
            default:
              sqlite3.config.warn('Unsupported bind() argument type:', val);
              toss3('Unsupported bind() argument type: ' + typeof val);
          }
          if (rc) DB.checkRc(stmt.db.pointer, rc);
          stmt._mayGet = false;
          return stmt;
        };

        Stmt.prototype = {
          finalize: function () {
            if (this.pointer) {
              affirmNotLockedByExec(this, 'finalize()');
              const rc = capi.sqlite3_finalize(this.pointer);
              delete __stmtMap.get(this.db)[this.pointer];
              __ptrMap.delete(this);
              delete this._mayGet;
              delete this.parameterCount;
              delete this._lockedByExec;
              delete this.db;
              return rc;
            }
          },

          clearBindings: function () {
            affirmNotLockedByExec(affirmStmtOpen(this), 'clearBindings()');
            capi.sqlite3_clear_bindings(this.pointer);
            this._mayGet = false;
            return this;
          },

          reset: function (alsoClearBinds) {
            affirmNotLockedByExec(this, 'reset()');
            if (alsoClearBinds) this.clearBindings();
            const rc = capi.sqlite3_reset(affirmStmtOpen(this).pointer);
            this._mayGet = false;
            checkSqlite3Rc(this.db, rc);
            return this;
          },

          bind: function () {
            affirmStmtOpen(this);
            let ndx, arg;
            switch (arguments.length) {
              case 1:
                ndx = 1;
                arg = arguments[0];
                break;
              case 2:
                ndx = arguments[0];
                arg = arguments[1];
                break;
              default:
                toss3('Invalid bind() arguments.');
            }
            if (undefined === arg) {
              return this;
            } else if (!this.parameterCount) {
              toss3('This statement has no bindable parameters.');
            }
            this._mayGet = false;
            if (null === arg) {
              return bindOne(this, ndx, BindTypes.null, arg);
            } else if (Array.isArray(arg)) {
              if (1 !== arguments.length) {
                toss3(
                  'When binding an array, an index argument is not permitted.',
                );
              }
              arg.forEach((v, i) =>
                bindOne(this, i + 1, affirmSupportedBindType(v), v),
              );
              return this;
            } else if (arg instanceof ArrayBuffer) {
              arg = new Uint8Array(arg);
            }
            if ('object' === typeof arg && !util.isBindableTypedArray(arg)) {
              if (1 !== arguments.length) {
                toss3(
                  'When binding an object, an index argument is not permitted.',
                );
              }
              Object.keys(arg).forEach((k) =>
                bindOne(this, k, affirmSupportedBindType(arg[k]), arg[k]),
              );
              return this;
            } else {
              return bindOne(this, ndx, affirmSupportedBindType(arg), arg);
            }
            toss3('Should not reach this point.');
          },

          bindAsBlob: function (ndx, arg) {
            affirmStmtOpen(this);
            if (1 === arguments.length) {
              arg = ndx;
              ndx = 1;
            }
            const t = affirmSupportedBindType(arg);
            if (
              BindTypes.string !== t &&
              BindTypes.blob !== t &&
              BindTypes.null !== t
            ) {
              toss3('Invalid value type for bindAsBlob()');
            }
            return bindOne(this, ndx, BindTypes.blob, arg);
          },

          step: function () {
            affirmNotLockedByExec(this, 'step()');
            const rc = capi.sqlite3_step(affirmStmtOpen(this).pointer);
            switch (rc) {
              case capi.SQLITE_DONE:
                return (this._mayGet = false);
              case capi.SQLITE_ROW:
                return (this._mayGet = true);
              default:
                this._mayGet = false;
                sqlite3.config.warn(
                  'sqlite3_step() rc=',
                  rc,
                  capi.sqlite3_js_rc_str(rc),
                  'SQL =',
                  capi.sqlite3_sql(this.pointer),
                );
                DB.checkRc(this.db.pointer, rc);
            }
          },

          stepReset: function () {
            this.step();
            return this.reset();
          },

          stepFinalize: function () {
            try {
              const rc = this.step();
              this.reset();
              return rc;
            } finally {
              try {
                this.finalize();
              } catch (e) {}
            }
          },

          get: function (ndx, asType) {
            if (!affirmStmtOpen(this)._mayGet) {
              toss3('Stmt.step() has not (recently) returned true.');
            }
            if (Array.isArray(ndx)) {
              let i = 0;
              const n = this.columnCount;
              while (i < n) {
                ndx[i] = this.get(i++);
              }
              return ndx;
            } else if (ndx && 'object' === typeof ndx) {
              let i = 0;
              const n = this.columnCount;
              while (i < n) {
                ndx[capi.sqlite3_column_name(this.pointer, i)] = this.get(i++);
              }
              return ndx;
            }
            affirmColIndex(this, ndx);
            switch (
              undefined === asType
                ? capi.sqlite3_column_type(this.pointer, ndx)
                : asType
            ) {
              case capi.SQLITE_NULL:
                return null;
              case capi.SQLITE_INTEGER: {
                if (wasm.bigIntEnabled) {
                  const rc = capi.sqlite3_column_int64(this.pointer, ndx);
                  if (
                    rc >= Number.MIN_SAFE_INTEGER &&
                    rc <= Number.MAX_SAFE_INTEGER
                  ) {
                    return Number(rc).valueOf();
                  }
                  return rc;
                } else {
                  const rc = capi.sqlite3_column_double(this.pointer, ndx);
                  if (
                    rc > Number.MAX_SAFE_INTEGER ||
                    rc < Number.MIN_SAFE_INTEGER
                  ) {
                    toss3(
                      'Integer is out of range for JS integer range: ' + rc,
                    );
                  }

                  return util.isInt32(rc) ? rc | 0 : rc;
                }
              }
              case capi.SQLITE_FLOAT:
                return capi.sqlite3_column_double(this.pointer, ndx);
              case capi.SQLITE_TEXT:
                return capi.sqlite3_column_text(this.pointer, ndx);
              case capi.SQLITE_BLOB: {
                const n = capi.sqlite3_column_bytes(this.pointer, ndx),
                  ptr = capi.sqlite3_column_blob(this.pointer, ndx),
                  rc = new Uint8Array(n);

                if (n) rc.set(wasm.heap8u().slice(ptr, ptr + n), 0);

                if (n && this.db._blobXfer instanceof Array) {
                  this.db._blobXfer.push(rc.buffer);
                }
                return rc;
              }
              default:
                toss3(
                  "Don't know how to translate",
                  'type of result column #' + ndx + '.',
                );
            }
            toss3('Not reached.');
          },

          getInt: function (ndx) {
            return this.get(ndx, capi.SQLITE_INTEGER);
          },

          getFloat: function (ndx) {
            return this.get(ndx, capi.SQLITE_FLOAT);
          },

          getString: function (ndx) {
            return this.get(ndx, capi.SQLITE_TEXT);
          },

          getBlob: function (ndx) {
            return this.get(ndx, capi.SQLITE_BLOB);
          },

          getJSON: function (ndx) {
            const s = this.get(ndx, capi.SQLITE_STRING);
            return null === s ? s : JSON.parse(s);
          },

          getColumnName: function (ndx) {
            return capi.sqlite3_column_name(
              affirmColIndex(affirmStmtOpen(this), ndx).pointer,
              ndx,
            );
          },

          getColumnNames: function (tgt = []) {
            affirmColIndex(affirmStmtOpen(this), 0);
            const n = this.columnCount;
            for (let i = 0; i < n; ++i) {
              tgt.push(capi.sqlite3_column_name(this.pointer, i));
            }
            return tgt;
          },

          getParamIndex: function (name) {
            return affirmStmtOpen(this).parameterCount
              ? capi.sqlite3_bind_parameter_index(this.pointer, name)
              : undefined;
          },

          getParamName: function (ndx) {
            return affirmStmtOpen(this).parameterCount
              ? capi.sqlite3_bind_parameter_name(this.pointer, ndx)
              : undefined;
          },

          isBusy: function () {
            return 0 !== capi.sqlite3_stmt_busy(affirmStmtOpen(this));
          },

          isReadOnly: function () {
            return 0 !== capi.sqlite3_stmt_readonly(affirmStmtOpen(this));
          },
        };

        {
          const prop = {
            enumerable: true,
            get: function () {
              return __ptrMap.get(this);
            },
            set: () => toss3('The pointer property is read-only.'),
          };
          Object.defineProperty(Stmt.prototype, 'pointer', prop);
          Object.defineProperty(DB.prototype, 'pointer', prop);
        }

        Object.defineProperty(Stmt.prototype, 'columnCount', {
          enumerable: false,
          get: function () {
            return capi.sqlite3_column_count(this.pointer);
          },
          set: () => toss3('The columnCount property is read-only.'),
        });

        sqlite3.oo1 = {
          DB,
          Stmt,
        };

        if (util.isUIThread()) {
          sqlite3.oo1.JsStorageDb = function (storageName = 'session') {
            const opt = dbCtorHelper.normalizeArgs(...arguments);
            storageName = opt.filename;
            if ('session' !== storageName && 'local' !== storageName) {
              toss3("JsStorageDb db name must be one of 'session' or 'local'.");
            }
            opt.vfs = 'kvvfs';
            dbCtorHelper.call(this, opt);
          };
          const jdb = sqlite3.oo1.JsStorageDb;
          jdb.prototype = Object.create(DB.prototype);

          jdb.clearStorage = capi.sqlite3_js_kvvfs_clear;

          jdb.prototype.clearStorage = function () {
            return jdb.clearStorage(affirmDbOpen(this).filename);
          };

          jdb.storageSize = capi.sqlite3_js_kvvfs_size;

          jdb.prototype.storageSize = function () {
            return jdb.storageSize(affirmDbOpen(this).filename);
          };
        }
      });

      globalThis.sqlite3ApiBootstrap.initializers.push(function (sqlite3) {
        const util = sqlite3.util;
        sqlite3.initWorker1API = function () {
          'use strict';
          const toss = (...args) => {
            throw new Error(args.join(' '));
          };
          if (!(globalThis.WorkerGlobalScope instanceof Function)) {
            toss('initWorker1API() must be run from a Worker thread.');
          }
          const sqlite3 = this.sqlite3 || toss('Missing this.sqlite3 object.');
          const DB = sqlite3.oo1.DB;

          const getDbId = function (db) {
            let id = wState.idMap.get(db);
            if (id) return id;
            id = 'db#' + ++wState.idSeq + '@' + db.pointer;

            wState.idMap.set(db, id);
            return id;
          };

          const wState = {
            dbList: [],

            idSeq: 0,

            idMap: new WeakMap(),

            xfer: [],
            open: function (opt) {
              const db = new DB(opt);
              this.dbs[getDbId(db)] = db;
              if (this.dbList.indexOf(db) < 0) this.dbList.push(db);
              return db;
            },
            close: function (db, alsoUnlink) {
              if (db) {
                delete this.dbs[getDbId(db)];
                const filename = db.filename;
                const pVfs = util.sqlite3__wasm_db_vfs(db.pointer, 0);
                db.close();
                const ddNdx = this.dbList.indexOf(db);
                if (ddNdx >= 0) this.dbList.splice(ddNdx, 1);
                if (alsoUnlink && filename && pVfs) {
                  util.sqlite3__wasm_vfs_unlink(pVfs, filename);
                }
              }
            },

            post: function (msg, xferList) {
              if (xferList && xferList.length) {
                globalThis.postMessage(msg, Array.from(xferList));
                xferList.length = 0;
              } else {
                globalThis.postMessage(msg);
              }
            },

            dbs: Object.create(null),

            getDb: function (id, require = true) {
              return (
                this.dbs[id] ||
                (require ? toss('Unknown (or closed) DB ID:', id) : undefined)
              );
            },
          };

          const affirmDbOpen = function (db = wState.dbList[0]) {
            return db && db.pointer ? db : toss('DB is not opened.');
          };

          const getMsgDb = function (msgData, affirmExists = true) {
            const db = wState.getDb(msgData.dbId, false) || wState.dbList[0];
            return affirmExists ? affirmDbOpen(db) : db;
          };

          const getDefaultDbId = function () {
            return wState.dbList[0] && getDbId(wState.dbList[0]);
          };

          const isSpecialDbFilename = (n) => {
            return '' === n || ':' === n[0];
          };

          const wMsgHandler = {
            open: function (ev) {
              const oargs = Object.create(null),
                args = ev.args || Object.create(null);
              if (args.simulateError) {
                toss('Throwing because of simulateError flag.');
              }
              const rc = Object.create(null);
              oargs.vfs = args.vfs;
              oargs.filename = args.filename || '';
              const db = wState.open(oargs);
              rc.filename = db.filename;
              rc.persistent = !!sqlite3.capi.sqlite3_js_db_uses_vfs(
                db.pointer,
                'opfs',
              );
              rc.dbId = getDbId(db);
              rc.vfs = db.dbVfsName();
              return rc;
            },

            close: function (ev) {
              const db = getMsgDb(ev, false);
              const response = {
                filename: db && db.filename,
              };
              if (db) {
                const doUnlink =
                  ev.args && 'object' === typeof ev.args
                    ? !!ev.args.unlink
                    : false;
                wState.close(db, doUnlink);
              }
              return response;
            },

            exec: function (ev) {
              const rc =
                'string' === typeof ev.args
                  ? { sql: ev.args }
                  : ev.args || Object.create(null);
              if ('stmt' === rc.rowMode) {
                toss(
                  "Invalid rowMode for 'exec': stmt mode",
                  'does not work in the Worker API.',
                );
              } else if (!rc.sql) {
                toss("'exec' requires input SQL.");
              }
              const db = getMsgDb(ev);
              if (rc.callback || Array.isArray(rc.resultRows)) {
                db._blobXfer = wState.xfer;
              }
              const theCallback = rc.callback;
              let rowNumber = 0;
              const hadColNames = !!rc.columnNames;
              if ('string' === typeof theCallback) {
                if (!hadColNames) rc.columnNames = [];

                rc.callback = function (row, stmt) {
                  wState.post(
                    {
                      type: theCallback,
                      columnNames: rc.columnNames,
                      rowNumber: ++rowNumber,
                      row: row,
                    },
                    wState.xfer,
                  );
                };
              }
              try {
                const changeCount = !!rc.countChanges
                  ? db.changes(true, 64 === rc.countChanges)
                  : undefined;
                db.exec(rc);
                if (undefined !== changeCount) {
                  rc.changeCount =
                    db.changes(true, 64 === rc.countChanges) - changeCount;
                }
                if (rc.callback instanceof Function) {
                  rc.callback = theCallback;

                  wState.post({
                    type: theCallback,
                    columnNames: rc.columnNames,
                    rowNumber: null,
                    row: undefined,
                  });
                }
              } finally {
                delete db._blobXfer;
                if (rc.callback) rc.callback = theCallback;
              }
              return rc;
            },

            'config-get': function () {
              const rc = Object.create(null),
                src = sqlite3.config;
              ['bigIntEnabled'].forEach(function (k) {
                if (Object.getOwnPropertyDescriptor(src, k)) rc[k] = src[k];
              });
              rc.version = sqlite3.version;
              rc.vfsList = sqlite3.capi.sqlite3_js_vfs_list();
              return rc;
            },

            export: function (ev) {
              const db = getMsgDb(ev);
              const response = {
                byteArray: sqlite3.capi.sqlite3_js_db_export(db.pointer),
                filename: db.filename,
                mimetype: 'application/x-sqlite3',
              };
              wState.xfer.push(response.byteArray.buffer);
              return response;
            },

            toss: function (ev) {
              toss('Testing worker exception');
            },
          };

          globalThis.onmessage = async function (ev) {
            ev = ev.data;
            let result,
              dbId = ev.dbId,
              evType = ev.type;
            const arrivalTime = performance.now();
            try {
              if (
                wMsgHandler.hasOwnProperty(evType) &&
                wMsgHandler[evType] instanceof Function
              ) {
                result = await wMsgHandler[evType](ev);
              } else {
                toss('Unknown db worker message type:', ev.type);
              }
            } catch (err) {
              evType = 'error';
              result = {
                operation: ev.type,
                message: err.message,
                errorClass: err.name,
                input: ev,
              };
              if (err.stack) {
                result.stack =
                  'string' === typeof err.stack
                    ? err.stack.split(/\n\s*/)
                    : err.stack;
              }
              if (0)
                sqlite3.config.warn(
                  'Worker is propagating an exception to main thread.',
                  'Reporting it _here_ for the stack trace:',
                  err,
                  result,
                );
            }
            if (!dbId) {
              dbId = result.dbId || getDefaultDbId();
            }

            wState.post(
              {
                type: evType,
                dbId: dbId,
                messageId: ev.messageId,
                workerReceivedTime: arrivalTime,
                workerRespondTime: performance.now(),
                departureTime: ev.departureTime,

                result: result,
              },
              wState.xfer,
            );
          };
          globalThis.postMessage({
            type: 'sqlite3-api',
            result: 'worker1-ready',
          });
        }.bind({ sqlite3 });
      });

      ('use strict');
      globalThis.sqlite3ApiBootstrap.initializers.push(function (sqlite3) {
        const wasm = sqlite3.wasm,
          capi = sqlite3.capi,
          toss = sqlite3.util.toss3;
        const vfs = Object.create(null);
        sqlite3.vfs = vfs;

        capi.sqlite3_vfs.prototype.registerVfs = function (asDefault = false) {
          if (!(this instanceof sqlite3.capi.sqlite3_vfs)) {
            toss('Expecting a sqlite3_vfs-type argument.');
          }
          const rc = capi.sqlite3_vfs_register(this, asDefault ? 1 : 0);
          if (rc) {
            toss('sqlite3_vfs_register(', this, ') failed with rc', rc);
          }
          if (this.pointer !== capi.sqlite3_vfs_find(this.$zName)) {
            toss(
              'BUG: sqlite3_vfs_find(vfs.$zName) failed for just-installed VFS',
              this,
            );
          }
          return this;
        };

        vfs.installVfs = function (opt) {
          let count = 0;
          const propList = ['io', 'vfs'];
          for (const key of propList) {
            const o = opt[key];
            if (o) {
              ++count;
              o.struct.installMethods(o.methods, !!o.applyArgcCheck);
              if ('vfs' === key) {
                if (!o.struct.$zName && 'string' === typeof o.name) {
                  o.struct.addOnDispose(
                    (o.struct.$zName = wasm.allocCString(o.name)),
                  );
                }
                o.struct.registerVfs(!!o.asDefault);
              }
            }
          }
          if (!count)
            toss(
              'Misuse: installVfs() options object requires at least',
              'one of:',
              propList,
            );
          return this;
        };
      });

      ('use strict');
      globalThis.sqlite3ApiBootstrap.initializers.push(function (sqlite3) {
        if (!sqlite3.wasm.exports.sqlite3_declare_vtab) {
          return;
        }
        const wasm = sqlite3.wasm,
          capi = sqlite3.capi,
          toss = sqlite3.util.toss3;
        const vtab = Object.create(null);
        sqlite3.vtab = vtab;

        const sii = capi.sqlite3_index_info;

        sii.prototype.nthConstraint = function (n, asPtr = false) {
          if (n < 0 || n >= this.$nConstraint) return false;
          const ptr =
            this.$aConstraint +
            sii.sqlite3_index_constraint.structInfo.sizeof * n;
          return asPtr ? ptr : new sii.sqlite3_index_constraint(ptr);
        };

        sii.prototype.nthConstraintUsage = function (n, asPtr = false) {
          if (n < 0 || n >= this.$nConstraint) return false;
          const ptr =
            this.$aConstraintUsage +
            sii.sqlite3_index_constraint_usage.structInfo.sizeof * n;
          return asPtr ? ptr : new sii.sqlite3_index_constraint_usage(ptr);
        };

        sii.prototype.nthOrderBy = function (n, asPtr = false) {
          if (n < 0 || n >= this.$nOrderBy) return false;
          const ptr =
            this.$aOrderBy + sii.sqlite3_index_orderby.structInfo.sizeof * n;
          return asPtr ? ptr : new sii.sqlite3_index_orderby(ptr);
        };

        const __xWrapFactory = function (methodName, StructType) {
          return function (ptr, removeMapping = false) {
            if (0 === arguments.length) ptr = new StructType();
            if (ptr instanceof StructType) {
              this.set(ptr.pointer, ptr);
              return ptr;
            } else if (!wasm.isPtr(ptr)) {
              sqlite3.SQLite3Error.toss(
                'Invalid argument to',
                methodName + '()',
              );
            }
            let rc = this.get(ptr);
            if (removeMapping) this.delete(ptr);
            return rc;
          }.bind(new Map());
        };

        const StructPtrMapper = function (name, StructType) {
          const __xWrap = __xWrapFactory(name, StructType);

          return Object.assign(Object.create(null), {
            StructType,

            create: (ppOut) => {
              const rc = __xWrap();
              wasm.pokePtr(ppOut, rc.pointer);
              return rc;
            },

            get: (pCObj) => __xWrap(pCObj),

            unget: (pCObj) => __xWrap(pCObj, true),

            dispose: (pCObj) => {
              const o = __xWrap(pCObj, true);
              if (o) o.dispose();
            },
          });
        };

        vtab.xVtab = StructPtrMapper('xVtab', capi.sqlite3_vtab);

        vtab.xCursor = StructPtrMapper('xCursor', capi.sqlite3_vtab_cursor);

        vtab.xIndexInfo = (pIdxInfo) => new capi.sqlite3_index_info(pIdxInfo);

        vtab.xError = function f(methodName, err, defaultRc) {
          if (f.errorReporter instanceof Function) {
            try {
              f.errorReporter(
                'sqlite3_module::' + methodName + '(): ' + err.message,
              );
            } catch (e) {}
          }
          let rc;
          if (err instanceof sqlite3.WasmAllocError) rc = capi.SQLITE_NOMEM;
          else if (arguments.length > 2) rc = defaultRc;
          else if (err instanceof sqlite3.SQLite3Error) rc = err.resultCode;
          return rc || capi.SQLITE_ERROR;
        };
        vtab.xError.errorReporter = 1 ? console.error.bind(console) : false;

        vtab.xRowid = (ppRowid64, value) => wasm.poke(ppRowid64, value, 'i64');

        vtab.setupModule = function (opt) {
          let createdMod = false;
          const mod =
            this instanceof capi.sqlite3_module
              ? this
              : opt.struct || (createdMod = new capi.sqlite3_module());
          try {
            const methods = opt.methods || toss("Missing 'methods' object.");
            for (const e of Object.entries({
              xConnect: 'xCreate',
              xDisconnect: 'xDestroy',
            })) {
              const k = e[0],
                v = e[1];
              if (true === methods[k]) methods[k] = methods[v];
              else if (true === methods[v]) methods[v] = methods[k];
            }
            if (opt.catchExceptions) {
              const fwrap = function (methodName, func) {
                if (['xConnect', 'xCreate'].indexOf(methodName) >= 0) {
                  return function (pDb, pAux, argc, argv, ppVtab, pzErr) {
                    try {
                      return func(...arguments) || 0;
                    } catch (e) {
                      if (!(e instanceof sqlite3.WasmAllocError)) {
                        wasm.dealloc(wasm.peekPtr(pzErr));
                        wasm.pokePtr(pzErr, wasm.allocCString(e.message));
                      }
                      return vtab.xError(methodName, e);
                    }
                  };
                } else {
                  return function (...args) {
                    try {
                      return func(...args) || 0;
                    } catch (e) {
                      return vtab.xError(methodName, e);
                    }
                  };
                }
              };
              const mnames = [
                'xCreate',
                'xConnect',
                'xBestIndex',
                'xDisconnect',
                'xDestroy',
                'xOpen',
                'xClose',
                'xFilter',
                'xNext',
                'xEof',
                'xColumn',
                'xRowid',
                'xUpdate',
                'xBegin',
                'xSync',
                'xCommit',
                'xRollback',
                'xFindFunction',
                'xRename',
                'xSavepoint',
                'xRelease',
                'xRollbackTo',
                'xShadowName',
              ];
              const remethods = Object.create(null);
              for (const k of mnames) {
                const m = methods[k];
                if (!(m instanceof Function)) continue;
                else if ('xConnect' === k && methods.xCreate === m) {
                  remethods[k] = methods.xCreate;
                } else if ('xCreate' === k && methods.xConnect === m) {
                  remethods[k] = methods.xConnect;
                } else {
                  remethods[k] = fwrap(k, m);
                }
              }
              mod.installMethods(remethods, false);
            } else {
              mod.installMethods(methods, !!opt.applyArgcCheck);
            }
            if (0 === mod.$iVersion) {
              let v;
              if ('number' === typeof opt.iVersion) v = opt.iVersion;
              else if (mod.$xShadowName) v = 3;
              else if (mod.$xSavePoint || mod.$xRelease || mod.$xRollbackTo)
                v = 2;
              else v = 1;
              mod.$iVersion = v;
            }
          } catch (e) {
            if (createdMod) createdMod.dispose();
            throw e;
          }
          return mod;
        };

        capi.sqlite3_module.prototype.setupModule = function (opt) {
          return vtab.setupModule.call(this, opt);
        };
      });

      ('use strict');
      globalThis.sqlite3ApiBootstrap.initializers.push(function (sqlite3) {
        const installOpfsVfs = function callee(options) {
          if (!globalThis.SharedArrayBuffer || !globalThis.Atomics) {
            return Promise.reject(
              new Error(
                'Cannot install OPFS: Missing SharedArrayBuffer and/or Atomics. ' +
                  'The server must emit the COOP/COEP response headers to enable those. ' +
                  'See https://sqlite.org/wasm/doc/trunk/persistence.md#coop-coep',
              ),
            );
          } else if ('undefined' === typeof WorkerGlobalScope) {
            return Promise.reject(
              new Error(
                'The OPFS sqlite3_vfs cannot run in the main thread ' +
                  'because it requires Atomics.wait().',
              ),
            );
          } else if (
            !globalThis.FileSystemHandle ||
            !globalThis.FileSystemDirectoryHandle ||
            !globalThis.FileSystemFileHandle ||
            !globalThis.FileSystemFileHandle.prototype.createSyncAccessHandle ||
            !navigator?.storage?.getDirectory
          ) {
            return Promise.reject(new Error('Missing required OPFS APIs.'));
          }
          if (!options || 'object' !== typeof options) {
            options = Object.create(null);
          }
          const urlParams = new URL(globalThis.location.href).searchParams;
          if (urlParams.has('opfs-disable')) {
            return Promise.resolve(sqlite3);
          }
          if (undefined === options.verbose) {
            options.verbose = urlParams.has('opfs-verbose')
              ? +urlParams.get('opfs-verbose') || 2
              : 1;
          }
          if (undefined === options.sanityChecks) {
            options.sanityChecks = urlParams.has('opfs-sanity-check');
          }
          if (undefined === options.proxyUri) {
            options.proxyUri = callee.defaultProxyUri;
          }

          if ('function' === typeof options.proxyUri) {
            options.proxyUri = options.proxyUri();
          }
          const thePromise = new Promise(function (
            promiseResolve_,
            promiseReject_,
          ) {
            const loggers = [
              sqlite3.config.error,
              sqlite3.config.warn,
              sqlite3.config.log,
            ];
            const logImpl = (level, ...args) => {
              if (options.verbose > level)
                loggers[level]('OPFS syncer:', ...args);
            };
            const log = (...args) => logImpl(2, ...args);
            const warn = (...args) => logImpl(1, ...args);
            const error = (...args) => logImpl(0, ...args);
            const toss = sqlite3.util.toss;
            const capi = sqlite3.capi;
            const util = sqlite3.util;
            const wasm = sqlite3.wasm;
            const sqlite3_vfs = capi.sqlite3_vfs;
            const sqlite3_file = capi.sqlite3_file;
            const sqlite3_io_methods = capi.sqlite3_io_methods;

            const opfsUtil = Object.create(null);

            const thisThreadHasOPFS = () => {
              return (
                globalThis.FileSystemHandle &&
                globalThis.FileSystemDirectoryHandle &&
                globalThis.FileSystemFileHandle &&
                globalThis.FileSystemFileHandle.prototype
                  .createSyncAccessHandle &&
                navigator?.storage?.getDirectory
              );
            };

            opfsUtil.metrics = {
              dump: function () {
                let k,
                  n = 0,
                  t = 0,
                  w = 0;
                for (k in state.opIds) {
                  const m = metrics[k];
                  n += m.count;
                  t += m.time;
                  w += m.wait;
                  m.avgTime = m.count && m.time ? m.time / m.count : 0;
                  m.avgWait = m.count && m.wait ? m.wait / m.count : 0;
                }
                sqlite3.config.log(
                  globalThis.location.href,
                  'metrics for',
                  globalThis.location.href,
                  ':',
                  metrics,
                  '\nTotal of',
                  n,
                  'op(s) for',
                  t,
                  'ms (incl. ' + w + ' ms of waiting on the async side)',
                );
                sqlite3.config.log('Serialization metrics:', metrics.s11n);
                W.postMessage({ type: 'opfs-async-metrics' });
              },
              reset: function () {
                let k;
                const r = (m) => (m.count = m.time = m.wait = 0);
                for (k in state.opIds) {
                  r((metrics[k] = Object.create(null)));
                }
                let s = (metrics.s11n = Object.create(null));
                s = s.serialize = Object.create(null);
                s.count = s.time = 0;
                s = metrics.s11n.deserialize = Object.create(null);
                s.count = s.time = 0;
              },
            };
            const opfsIoMethods = new sqlite3_io_methods();
            const opfsVfs = new sqlite3_vfs().addOnDispose(() =>
              opfsIoMethods.dispose(),
            );
            let promiseWasRejected = undefined;
            const promiseReject = (err) => {
              promiseWasRejected = true;
              opfsVfs.dispose();
              return promiseReject_(err);
            };
            const promiseResolve = () => {
              promiseWasRejected = false;
              return promiseResolve_(sqlite3);
            };
            const W = new Worker(
              new URL('sqlite3-opfs-async-proxy.js', import.meta.url),
            );
            setTimeout(() => {
              if (undefined === promiseWasRejected) {
                promiseReject(
                  new Error(
                    'Timeout while waiting for OPFS async proxy worker.',
                  ),
                );
              }
            }, 4000);
            W._originalOnError = W.onerror;
            W.onerror = function (err) {
              error('Error initializing OPFS asyncer:', err);
              promiseReject(
                new Error(
                  'Loading OPFS async Worker failed for unknown reasons.',
                ),
              );
            };
            const pDVfs = capi.sqlite3_vfs_find(null);
            const dVfs = pDVfs ? new sqlite3_vfs(pDVfs) : null;
            opfsIoMethods.$iVersion = 1;
            opfsVfs.$iVersion = 2;
            opfsVfs.$szOsFile = capi.sqlite3_file.structInfo.sizeof;
            opfsVfs.$mxPathname = 1024;
            opfsVfs.$zName = wasm.allocCString('opfs');

            opfsVfs.$xDlOpen =
              opfsVfs.$xDlError =
              opfsVfs.$xDlSym =
              opfsVfs.$xDlClose =
                null;
            opfsVfs.addOnDispose(
              '$zName',
              opfsVfs.$zName,
              'cleanup default VFS wrapper',
              () => (dVfs ? dVfs.dispose() : null),
            );

            const state = Object.create(null);
            state.verbose = options.verbose;
            state.littleEndian = (() => {
              const buffer = new ArrayBuffer(2);
              new DataView(buffer).setInt16(0, 256, true);

              return new Int16Array(buffer)[0] === 256;
            })();

            state.asyncIdleWaitTime = 150;

            state.asyncS11nExceptions = 1;

            state.fileBufferSize = 1024 * 64;
            state.sabS11nOffset = state.fileBufferSize;

            state.sabS11nSize = opfsVfs.$mxPathname * 2;

            state.sabIO = new SharedArrayBuffer(
              state.fileBufferSize + state.sabS11nSize,
            );
            state.opIds = Object.create(null);
            const metrics = Object.create(null);
            {
              let i = 0;

              state.opIds.whichOp = i++;

              state.opIds.rc = i++;

              state.opIds.xAccess = i++;
              state.opIds.xClose = i++;
              state.opIds.xDelete = i++;
              state.opIds.xDeleteNoWait = i++;
              state.opIds.xFileSize = i++;
              state.opIds.xLock = i++;
              state.opIds.xOpen = i++;
              state.opIds.xRead = i++;
              state.opIds.xSleep = i++;
              state.opIds.xSync = i++;
              state.opIds.xTruncate = i++;
              state.opIds.xUnlock = i++;
              state.opIds.xWrite = i++;
              state.opIds.mkdir = i++;
              state.opIds['opfs-async-metrics'] = i++;
              state.opIds['opfs-async-shutdown'] = i++;

              state.opIds.retry = i++;
              state.sabOP = new SharedArrayBuffer(i * 4);
              opfsUtil.metrics.reset();
            }

            state.sq3Codes = Object.create(null);
            [
              'SQLITE_ACCESS_EXISTS',
              'SQLITE_ACCESS_READWRITE',
              'SQLITE_BUSY',
              'SQLITE_CANTOPEN',
              'SQLITE_ERROR',
              'SQLITE_IOERR',
              'SQLITE_IOERR_ACCESS',
              'SQLITE_IOERR_CLOSE',
              'SQLITE_IOERR_DELETE',
              'SQLITE_IOERR_FSYNC',
              'SQLITE_IOERR_LOCK',
              'SQLITE_IOERR_READ',
              'SQLITE_IOERR_SHORT_READ',
              'SQLITE_IOERR_TRUNCATE',
              'SQLITE_IOERR_UNLOCK',
              'SQLITE_IOERR_WRITE',
              'SQLITE_LOCK_EXCLUSIVE',
              'SQLITE_LOCK_NONE',
              'SQLITE_LOCK_PENDING',
              'SQLITE_LOCK_RESERVED',
              'SQLITE_LOCK_SHARED',
              'SQLITE_LOCKED',
              'SQLITE_MISUSE',
              'SQLITE_NOTFOUND',
              'SQLITE_OPEN_CREATE',
              'SQLITE_OPEN_DELETEONCLOSE',
              'SQLITE_OPEN_MAIN_DB',
              'SQLITE_OPEN_READONLY',
            ].forEach((k) => {
              if (undefined === (state.sq3Codes[k] = capi[k])) {
                toss('Maintenance required: not found:', k);
              }
            });
            state.opfsFlags = Object.assign(Object.create(null), {
              OPFS_UNLOCK_ASAP: 0x01,

              OPFS_UNLINK_BEFORE_OPEN: 0x02,

              defaultUnlockAsap: false,
            });

            const opRun = (op, ...args) => {
              const opNdx = state.opIds[op] || toss('Invalid op ID:', op);
              state.s11n.serialize(...args);
              Atomics.store(state.sabOPView, state.opIds.rc, -1);
              Atomics.store(state.sabOPView, state.opIds.whichOp, opNdx);
              Atomics.notify(state.sabOPView, state.opIds.whichOp);
              const t = performance.now();
              while (
                'not-equal' !==
                Atomics.wait(state.sabOPView, state.opIds.rc, -1)
              ) {}

              const rc = Atomics.load(state.sabOPView, state.opIds.rc);
              metrics[op].wait += performance.now() - t;
              if (rc && state.asyncS11nExceptions) {
                const err = state.s11n.deserialize();
                if (err) error(op + '() async error:', ...err);
              }
              return rc;
            };

            opfsUtil.debug = {
              asyncShutdown: () => {
                warn(
                  'Shutting down OPFS async listener. The OPFS VFS will no longer work.',
                );
                opRun('opfs-async-shutdown');
              },
              asyncRestart: () => {
                warn(
                  'Attempting to restart OPFS VFS async listener. Might work, might not.',
                );
                W.postMessage({ type: 'opfs-async-restart' });
              },
            };

            const initS11n = () => {
              if (state.s11n) return state.s11n;
              const textDecoder = new TextDecoder(),
                textEncoder = new TextEncoder('utf-8'),
                viewU8 = new Uint8Array(
                  state.sabIO,
                  state.sabS11nOffset,
                  state.sabS11nSize,
                ),
                viewDV = new DataView(
                  state.sabIO,
                  state.sabS11nOffset,
                  state.sabS11nSize,
                );
              state.s11n = Object.create(null);

              const TypeIds = Object.create(null);
              TypeIds.number = {
                id: 1,
                size: 8,
                getter: 'getFloat64',
                setter: 'setFloat64',
              };
              TypeIds.bigint = {
                id: 2,
                size: 8,
                getter: 'getBigInt64',
                setter: 'setBigInt64',
              };
              TypeIds.boolean = {
                id: 3,
                size: 4,
                getter: 'getInt32',
                setter: 'setInt32',
              };
              TypeIds.string = { id: 4 };

              const getTypeId = (v) =>
                TypeIds[typeof v] ||
                toss(
                  'Maintenance required: this value type cannot be serialized.',
                  v,
                );
              const getTypeIdById = (tid) => {
                switch (tid) {
                  case TypeIds.number.id:
                    return TypeIds.number;
                  case TypeIds.bigint.id:
                    return TypeIds.bigint;
                  case TypeIds.boolean.id:
                    return TypeIds.boolean;
                  case TypeIds.string.id:
                    return TypeIds.string;
                  default:
                    toss('Invalid type ID:', tid);
                }
              };

              state.s11n.deserialize = function (clear = false) {
                ++metrics.s11n.deserialize.count;
                const t = performance.now();
                const argc = viewU8[0];
                const rc = argc ? [] : null;
                if (argc) {
                  const typeIds = [];
                  let offset = 1,
                    i,
                    n,
                    v;
                  for (i = 0; i < argc; ++i, ++offset) {
                    typeIds.push(getTypeIdById(viewU8[offset]));
                  }
                  for (i = 0; i < argc; ++i) {
                    const t = typeIds[i];
                    if (t.getter) {
                      v = viewDV[t.getter](offset, state.littleEndian);
                      offset += t.size;
                    } else {
                      n = viewDV.getInt32(offset, state.littleEndian);
                      offset += 4;
                      v = textDecoder.decode(viewU8.slice(offset, offset + n));
                      offset += n;
                    }
                    rc.push(v);
                  }
                }
                if (clear) viewU8[0] = 0;

                metrics.s11n.deserialize.time += performance.now() - t;
                return rc;
              };

              state.s11n.serialize = function (...args) {
                const t = performance.now();
                ++metrics.s11n.serialize.count;
                if (args.length) {
                  const typeIds = [];
                  let i = 0,
                    offset = 1;
                  viewU8[0] = args.length & 0xff;
                  for (; i < args.length; ++i, ++offset) {
                    typeIds.push(getTypeId(args[i]));
                    viewU8[offset] = typeIds[i].id;
                  }
                  for (i = 0; i < args.length; ++i) {
                    const t = typeIds[i];
                    if (t.setter) {
                      viewDV[t.setter](offset, args[i], state.littleEndian);
                      offset += t.size;
                    } else {
                      const s = textEncoder.encode(args[i]);
                      viewDV.setInt32(offset, s.byteLength, state.littleEndian);
                      offset += 4;
                      viewU8.set(s, offset);
                      offset += s.byteLength;
                    }
                  }
                } else {
                  viewU8[0] = 0;
                }
                metrics.s11n.serialize.time += performance.now() - t;
              };
              return state.s11n;
            };

            const randomFilename = function f(len = 16) {
              if (!f._chars) {
                f._chars =
                  'abcdefghijklmnopqrstuvwxyz' +
                  'ABCDEFGHIJKLMNOPQRSTUVWXYZ' +
                  '012346789';
                f._n = f._chars.length;
              }
              const a = [];
              let i = 0;
              for (; i < len; ++i) {
                const ndx = (Math.random() * (f._n * 64)) % f._n | 0;
                a[i] = f._chars[ndx];
              }
              return a.join('');
            };

            const __openFiles = Object.create(null);

            const opTimer = Object.create(null);
            opTimer.op = undefined;
            opTimer.start = undefined;
            const mTimeStart = (op) => {
              opTimer.start = performance.now();
              opTimer.op = op;
              ++metrics[op].count;
            };
            const mTimeEnd = () =>
              (metrics[opTimer.op].time += performance.now() - opTimer.start);

            const ioSyncWrappers = {
              xCheckReservedLock: function (pFile, pOut) {
                wasm.poke(pOut, 0, 'i32');
                return 0;
              },
              xClose: function (pFile) {
                mTimeStart('xClose');
                let rc = 0;
                const f = __openFiles[pFile];
                if (f) {
                  delete __openFiles[pFile];
                  rc = opRun('xClose', pFile);
                  if (f.sq3File) f.sq3File.dispose();
                }
                mTimeEnd();
                return rc;
              },
              xDeviceCharacteristics: function (pFile) {
                return capi.SQLITE_IOCAP_UNDELETABLE_WHEN_OPEN;
              },
              xFileControl: function (pFile, opId, pArg) {
                return capi.SQLITE_NOTFOUND;
              },
              xFileSize: function (pFile, pSz64) {
                mTimeStart('xFileSize');
                let rc = opRun('xFileSize', pFile);
                if (0 == rc) {
                  try {
                    const sz = state.s11n.deserialize()[0];
                    wasm.poke(pSz64, sz, 'i64');
                  } catch (e) {
                    error('Unexpected error reading xFileSize() result:', e);
                    rc = state.sq3Codes.SQLITE_IOERR;
                  }
                }
                mTimeEnd();
                return rc;
              },
              xLock: function (pFile, lockType) {
                mTimeStart('xLock');
                const f = __openFiles[pFile];
                let rc = 0;

                if (!f.lockType) {
                  rc = opRun('xLock', pFile, lockType);
                  if (0 === rc) f.lockType = lockType;
                } else {
                  f.lockType = lockType;
                }
                mTimeEnd();
                return rc;
              },
              xRead: function (pFile, pDest, n, offset64) {
                mTimeStart('xRead');
                const f = __openFiles[pFile];
                let rc;
                try {
                  rc = opRun('xRead', pFile, n, Number(offset64));
                  if (0 === rc || capi.SQLITE_IOERR_SHORT_READ === rc) {
                    wasm.heap8u().set(f.sabView.subarray(0, n), pDest);
                  }
                } catch (e) {
                  error('xRead(', arguments, ') failed:', e, f);
                  rc = capi.SQLITE_IOERR_READ;
                }
                mTimeEnd();
                return rc;
              },
              xSync: function (pFile, flags) {
                mTimeStart('xSync');
                ++metrics.xSync.count;
                const rc = opRun('xSync', pFile, flags);
                mTimeEnd();
                return rc;
              },
              xTruncate: function (pFile, sz64) {
                mTimeStart('xTruncate');
                const rc = opRun('xTruncate', pFile, Number(sz64));
                mTimeEnd();
                return rc;
              },
              xUnlock: function (pFile, lockType) {
                mTimeStart('xUnlock');
                const f = __openFiles[pFile];
                let rc = 0;
                if (capi.SQLITE_LOCK_NONE === lockType && f.lockType) {
                  rc = opRun('xUnlock', pFile, lockType);
                }
                if (0 === rc) f.lockType = lockType;
                mTimeEnd();
                return rc;
              },
              xWrite: function (pFile, pSrc, n, offset64) {
                mTimeStart('xWrite');
                const f = __openFiles[pFile];
                let rc;
                try {
                  f.sabView.set(wasm.heap8u().subarray(pSrc, pSrc + n));
                  rc = opRun('xWrite', pFile, n, Number(offset64));
                } catch (e) {
                  error('xWrite(', arguments, ') failed:', e, f);
                  rc = capi.SQLITE_IOERR_WRITE;
                }
                mTimeEnd();
                return rc;
              },
            };

            const vfsSyncWrappers = {
              xAccess: function (pVfs, zName, flags, pOut) {
                mTimeStart('xAccess');
                const rc = opRun('xAccess', wasm.cstrToJs(zName));
                wasm.poke(pOut, rc ? 0 : 1, 'i32');
                mTimeEnd();
                return 0;
              },
              xCurrentTime: function (pVfs, pOut) {
                wasm.poke(
                  pOut,
                  2440587.5 + new Date().getTime() / 86400000,
                  'double',
                );
                return 0;
              },
              xCurrentTimeInt64: function (pVfs, pOut) {
                wasm.poke(
                  pOut,
                  2440587.5 * 86400000 + new Date().getTime(),
                  'i64',
                );
                return 0;
              },
              xDelete: function (pVfs, zName, doSyncDir) {
                mTimeStart('xDelete');
                const rc = opRun(
                  'xDelete',
                  wasm.cstrToJs(zName),
                  doSyncDir,
                  false,
                );
                mTimeEnd();
                return rc;
              },
              xFullPathname: function (pVfs, zName, nOut, pOut) {
                const i = wasm.cstrncpy(pOut, zName, nOut);
                return i < nOut ? 0 : capi.SQLITE_CANTOPEN;
              },
              xGetLastError: function (pVfs, nOut, pOut) {
                warn('OPFS xGetLastError() has nothing sensible to return.');
                return 0;
              },

              xOpen: function f(pVfs, zName, pFile, flags, pOutFlags) {
                mTimeStart('xOpen');
                let opfsFlags = 0;
                if (0 === zName) {
                  zName = randomFilename();
                } else if (wasm.isPtr(zName)) {
                  if (capi.sqlite3_uri_boolean(zName, 'opfs-unlock-asap', 0)) {
                    opfsFlags |= state.opfsFlags.OPFS_UNLOCK_ASAP;
                  }
                  if (
                    capi.sqlite3_uri_boolean(zName, 'delete-before-open', 0)
                  ) {
                    opfsFlags |= state.opfsFlags.OPFS_UNLINK_BEFORE_OPEN;
                  }
                  zName = wasm.cstrToJs(zName);
                }
                const fh = Object.create(null);
                fh.fid = pFile;
                fh.filename = zName;
                fh.sab = new SharedArrayBuffer(state.fileBufferSize);
                fh.flags = flags;
                fh.readOnly =
                  !(sqlite3.SQLITE_OPEN_CREATE & flags) &&
                  !!(flags & capi.SQLITE_OPEN_READONLY);
                const rc = opRun('xOpen', pFile, zName, flags, opfsFlags);
                if (!rc) {
                  if (fh.readOnly) {
                    wasm.poke(pOutFlags, capi.SQLITE_OPEN_READONLY, 'i32');
                  }
                  __openFiles[pFile] = fh;
                  fh.sabView = state.sabFileBufView;
                  fh.sq3File = new sqlite3_file(pFile);
                  fh.sq3File.$pMethods = opfsIoMethods.pointer;
                  fh.lockType = capi.SQLITE_LOCK_NONE;
                }
                mTimeEnd();
                return rc;
              },
            };

            if (dVfs) {
              opfsVfs.$xRandomness = dVfs.$xRandomness;
              opfsVfs.$xSleep = dVfs.$xSleep;
            }
            if (!opfsVfs.$xRandomness) {
              vfsSyncWrappers.xRandomness = function (pVfs, nOut, pOut) {
                const heap = wasm.heap8u();
                let i = 0;
                for (; i < nOut; ++i)
                  heap[pOut + i] = (Math.random() * 255000) & 0xff;
                return i;
              };
            }
            if (!opfsVfs.$xSleep) {
              vfsSyncWrappers.xSleep = function (pVfs, ms) {
                Atomics.wait(state.sabOPView, state.opIds.xSleep, 0, ms);
                return 0;
              };
            }

            opfsUtil.getResolvedPath = function (filename, splitIt) {
              const p = new URL(filename, 'file://irrelevant').pathname;
              return splitIt ? p.split('/').filter((v) => !!v) : p;
            };

            opfsUtil.getDirForFilename = async function f(
              absFilename,
              createDirs = false,
            ) {
              const path = opfsUtil.getResolvedPath(absFilename, true);
              const filename = path.pop();
              let dh = opfsUtil.rootDirectory;
              for (const dirName of path) {
                if (dirName) {
                  dh = await dh.getDirectoryHandle(dirName, {
                    create: !!createDirs,
                  });
                }
              }
              return [dh, filename];
            };

            opfsUtil.mkdir = async function (absDirName) {
              try {
                await opfsUtil.getDirForFilename(
                  absDirName + '/filepart',
                  true,
                );
                return true;
              } catch (e) {
                return false;
              }
            };

            opfsUtil.entryExists = async function (fsEntryName) {
              try {
                const [dh, fn] = await opfsUtil.getDirForFilename(fsEntryName);
                await dh.getFileHandle(fn);
                return true;
              } catch (e) {
                return false;
              }
            };

            opfsUtil.randomFilename = randomFilename;

            opfsUtil.treeList = async function () {
              const doDir = async function callee(dirHandle, tgt) {
                tgt.name = dirHandle.name;
                tgt.dirs = [];
                tgt.files = [];
                for await (const handle of dirHandle.values()) {
                  if ('directory' === handle.kind) {
                    const subDir = Object.create(null);
                    tgt.dirs.push(subDir);
                    await callee(handle, subDir);
                  } else {
                    tgt.files.push(handle.name);
                  }
                }
              };
              const root = Object.create(null);
              await doDir(opfsUtil.rootDirectory, root);
              return root;
            };

            opfsUtil.rmfr = async function () {
              const dir = opfsUtil.rootDirectory,
                opt = { recurse: true };
              for await (const handle of dir.values()) {
                dir.removeEntry(handle.name, opt);
              }
            };

            opfsUtil.unlink = async function (
              fsEntryName,
              recursive = false,
              throwOnError = false,
            ) {
              try {
                const [hDir, filenamePart] = await opfsUtil.getDirForFilename(
                  fsEntryName,
                  false,
                );
                await hDir.removeEntry(filenamePart, { recursive });
                return true;
              } catch (e) {
                if (throwOnError) {
                  throw new Error(
                    'unlink(',
                    arguments[0],
                    ') failed: ' + e.message,
                    {
                      cause: e,
                    },
                  );
                }
                return false;
              }
            };

            opfsUtil.traverse = async function (opt) {
              const defaultOpt = {
                recursive: true,
                directory: opfsUtil.rootDirectory,
              };
              if ('function' === typeof opt) {
                opt = { callback: opt };
              }
              opt = Object.assign(defaultOpt, opt || {});
              const doDir = async function callee(dirHandle, depth) {
                for await (const handle of dirHandle.values()) {
                  if (false === opt.callback(handle, dirHandle, depth))
                    return false;
                  else if (opt.recursive && 'directory' === handle.kind) {
                    if (false === (await callee(handle, depth + 1))) break;
                  }
                }
              };
              doDir(opt.directory, 0);
            };

            const importDbChunked = async function (filename, callback) {
              const [hDir, fnamePart] = await opfsUtil.getDirForFilename(
                filename,
                true,
              );
              const hFile = await hDir.getFileHandle(fnamePart, {
                create: true,
              });
              let sah = await hFile.createSyncAccessHandle();
              let nWrote = 0,
                chunk,
                checkedHeader = false,
                err = false;
              try {
                sah.truncate(0);
                while (undefined !== (chunk = await callback())) {
                  if (chunk instanceof ArrayBuffer)
                    chunk = new Uint8Array(chunk);
                  if (0 === nWrote && chunk.byteLength >= 15) {
                    util.affirmDbHeader(chunk);
                    checkedHeader = true;
                  }
                  sah.write(chunk, { at: nWrote });
                  nWrote += chunk.byteLength;
                }
                if (nWrote < 512 || 0 !== nWrote % 512) {
                  toss(
                    'Input size',
                    nWrote,
                    'is not correct for an SQLite database.',
                  );
                }
                if (!checkedHeader) {
                  const header = new Uint8Array(20);
                  sah.read(header, { at: 0 });
                  util.affirmDbHeader(header);
                }
                sah.write(new Uint8Array([1, 1]), { at: 18 });
                return nWrote;
              } catch (e) {
                await sah.close();
                sah = undefined;
                await hDir.removeEntry(fnamePart).catch(() => {});
                throw e;
              } finally {
                if (sah) await sah.close();
              }
            };

            opfsUtil.importDb = async function (filename, bytes) {
              if (bytes instanceof Function) {
                return importDbChunked(filename, bytes);
              }
              if (bytes instanceof ArrayBuffer) bytes = new Uint8Array(bytes);
              util.affirmIsDb(bytes);
              const n = bytes.byteLength;
              const [hDir, fnamePart] = await opfsUtil.getDirForFilename(
                filename,
                true,
              );
              let sah,
                err,
                nWrote = 0;
              try {
                const hFile = await hDir.getFileHandle(fnamePart, {
                  create: true,
                });
                sah = await hFile.createSyncAccessHandle();
                sah.truncate(0);
                nWrote = sah.write(bytes, { at: 0 });
                if (nWrote != n) {
                  toss(
                    'Expected to write ' +
                      n +
                      ' bytes but wrote ' +
                      nWrote +
                      '.',
                  );
                }
                sah.write(new Uint8Array([1, 1]), { at: 18 });
                return nWrote;
              } catch (e) {
                if (sah) {
                  await sah.close();
                  sah = undefined;
                }
                await hDir.removeEntry(fnamePart).catch(() => {});
                throw e;
              } finally {
                if (sah) await sah.close();
              }
            };

            if (sqlite3.oo1) {
              const OpfsDb = function (...args) {
                const opt = sqlite3.oo1.DB.dbCtorHelper.normalizeArgs(...args);
                opt.vfs = opfsVfs.$zName;
                sqlite3.oo1.DB.dbCtorHelper.call(this, opt);
              };
              OpfsDb.prototype = Object.create(sqlite3.oo1.DB.prototype);
              sqlite3.oo1.OpfsDb = OpfsDb;
              OpfsDb.importDb = opfsUtil.importDb;
              sqlite3.oo1.DB.dbCtorHelper.setVfsPostOpenCallback(
                opfsVfs.pointer,
                function (oo1Db, sqlite3) {
                  sqlite3.capi.sqlite3_busy_timeout(oo1Db, 10000);
                },
              );
            }

            const sanityCheck = function () {
              const scope = wasm.scopedAllocPush();
              const sq3File = new sqlite3_file();
              try {
                const fid = sq3File.pointer;
                const openFlags =
                  capi.SQLITE_OPEN_CREATE |
                  capi.SQLITE_OPEN_READWRITE |
                  capi.SQLITE_OPEN_MAIN_DB;
                const pOut = wasm.scopedAlloc(8);
                const dbFile = '/sanity/check/file' + randomFilename(8);
                const zDbFile = wasm.scopedAllocCString(dbFile);
                let rc;
                state.s11n.serialize('This is ä string.');
                rc = state.s11n.deserialize();
                log('deserialize() says:', rc);
                if ('This is ä string.' !== rc[0]) toss('String d13n error.');
                vfsSyncWrappers.xAccess(opfsVfs.pointer, zDbFile, 0, pOut);
                rc = wasm.peek(pOut, 'i32');
                log('xAccess(', dbFile, ') exists ?=', rc);
                rc = vfsSyncWrappers.xOpen(
                  opfsVfs.pointer,
                  zDbFile,
                  fid,
                  openFlags,
                  pOut,
                );
                log(
                  'open rc =',
                  rc,
                  'state.sabOPView[xOpen] =',
                  state.sabOPView[state.opIds.xOpen],
                );
                if (0 !== rc) {
                  error('open failed with code', rc);
                  return;
                }
                vfsSyncWrappers.xAccess(opfsVfs.pointer, zDbFile, 0, pOut);
                rc = wasm.peek(pOut, 'i32');
                if (!rc) toss('xAccess() failed to detect file.');
                rc = ioSyncWrappers.xSync(sq3File.pointer, 0);
                if (rc) toss('sync failed w/ rc', rc);
                rc = ioSyncWrappers.xTruncate(sq3File.pointer, 1024);
                if (rc) toss('truncate failed w/ rc', rc);
                wasm.poke(pOut, 0, 'i64');
                rc = ioSyncWrappers.xFileSize(sq3File.pointer, pOut);
                if (rc) toss('xFileSize failed w/ rc', rc);
                log('xFileSize says:', wasm.peek(pOut, 'i64'));
                rc = ioSyncWrappers.xWrite(sq3File.pointer, zDbFile, 10, 1);
                if (rc) toss('xWrite() failed!');
                const readBuf = wasm.scopedAlloc(16);
                rc = ioSyncWrappers.xRead(sq3File.pointer, readBuf, 6, 2);
                wasm.poke(readBuf + 6, 0);
                let jRead = wasm.cstrToJs(readBuf);
                log('xRead() got:', jRead);
                if ('sanity' !== jRead) toss('Unexpected xRead() value.');
                if (vfsSyncWrappers.xSleep) {
                  log('xSleep()ing before close()ing...');
                  vfsSyncWrappers.xSleep(opfsVfs.pointer, 2000);
                  log('waking up from xSleep()');
                }
                rc = ioSyncWrappers.xClose(fid);
                log('xClose rc =', rc, 'sabOPView =', state.sabOPView);
                log('Deleting file:', dbFile);
                vfsSyncWrappers.xDelete(opfsVfs.pointer, zDbFile, 0x1234);
                vfsSyncWrappers.xAccess(opfsVfs.pointer, zDbFile, 0, pOut);
                rc = wasm.peek(pOut, 'i32');
                if (rc)
                  toss(
                    'Expecting 0 from xAccess(',
                    dbFile,
                    ') after xDelete().',
                  );
                warn('End of OPFS sanity checks.');
              } finally {
                sq3File.dispose();
                wasm.scopedAllocPop(scope);
              }
            };

            W.onmessage = function ({ data }) {
              switch (data.type) {
                case 'opfs-unavailable':
                  promiseReject(new Error(data.payload.join(' ')));
                  break;
                case 'opfs-async-loaded':
                  W.postMessage({ type: 'opfs-async-init', args: state });
                  break;
                case 'opfs-async-inited': {
                  if (true === promiseWasRejected) {
                    break;
                  }
                  try {
                    sqlite3.vfs.installVfs({
                      io: { struct: opfsIoMethods, methods: ioSyncWrappers },
                      vfs: { struct: opfsVfs, methods: vfsSyncWrappers },
                    });
                    state.sabOPView = new Int32Array(state.sabOP);
                    state.sabFileBufView = new Uint8Array(
                      state.sabIO,
                      0,
                      state.fileBufferSize,
                    );
                    state.sabS11nView = new Uint8Array(
                      state.sabIO,
                      state.sabS11nOffset,
                      state.sabS11nSize,
                    );
                    initS11n();
                    if (options.sanityChecks) {
                      warn(
                        'Running sanity checks because of opfs-sanity-check URL arg...',
                      );
                      sanityCheck();
                    }
                    if (thisThreadHasOPFS()) {
                      navigator.storage
                        .getDirectory()
                        .then((d) => {
                          W.onerror = W._originalOnError;
                          delete W._originalOnError;
                          sqlite3.opfs = opfsUtil;
                          opfsUtil.rootDirectory = d;
                          log('End of OPFS sqlite3_vfs setup.', opfsVfs);
                          promiseResolve();
                        })
                        .catch(promiseReject);
                    } else {
                      promiseResolve();
                    }
                  } catch (e) {
                    error(e);
                    promiseReject(e);
                  }
                  break;
                }
                default: {
                  const errMsg =
                    'Unexpected message from the OPFS async worker: ' +
                    JSON.stringify(data);
                  error(errMsg);
                  promiseReject(new Error(errMsg));
                  break;
                }
              }
            };
          });
          return thePromise;
        };
        installOpfsVfs.defaultProxyUri = 'sqlite3-opfs-async-proxy.js';
        globalThis.sqlite3ApiBootstrap.initializersAsync.push(
          async (sqlite3) => {
            try {
              let proxyJs = installOpfsVfs.defaultProxyUri;
              if (sqlite3.scriptInfo.sqlite3Dir) {
                installOpfsVfs.defaultProxyUri =
                  sqlite3.scriptInfo.sqlite3Dir + proxyJs;
              }
              return installOpfsVfs().catch((e) => {
                sqlite3.config.warn(
                  'Ignoring inability to install OPFS sqlite3_vfs:',
                  e.message,
                );
              });
            } catch (e) {
              sqlite3.config.error('installOpfsVfs() exception:', e);
              return Promise.reject(e);
            }
          },
        );
      });

      globalThis.sqlite3ApiBootstrap.initializers.push(function (sqlite3) {
        'use strict';
        const toss = sqlite3.util.toss;
        const toss3 = sqlite3.util.toss3;
        const initPromises = Object.create(null);
        const capi = sqlite3.capi;
        const util = sqlite3.util;
        const wasm = sqlite3.wasm;

        const SECTOR_SIZE = 4096;
        const HEADER_MAX_PATH_SIZE = 512;
        const HEADER_FLAGS_SIZE = 4;
        const HEADER_DIGEST_SIZE = 8;
        const HEADER_CORPUS_SIZE = HEADER_MAX_PATH_SIZE + HEADER_FLAGS_SIZE;
        const HEADER_OFFSET_FLAGS = HEADER_MAX_PATH_SIZE;
        const HEADER_OFFSET_DIGEST = HEADER_CORPUS_SIZE;
        const HEADER_OFFSET_DATA = SECTOR_SIZE;

        const PERSISTENT_FILE_TYPES =
          capi.SQLITE_OPEN_MAIN_DB |
          capi.SQLITE_OPEN_MAIN_JOURNAL |
          capi.SQLITE_OPEN_SUPER_JOURNAL |
          capi.SQLITE_OPEN_WAL;

        const OPAQUE_DIR_NAME = '.opaque';

        const getRandomName = () => Math.random().toString(36).slice(2);

        const textDecoder = new TextDecoder();
        const textEncoder = new TextEncoder();

        const optionDefaults = Object.assign(Object.create(null), {
          name: 'opfs-sahpool',
          directory: undefined,
          initialCapacity: 6,
          clearOnInit: false,

          verbosity: 2,
          forceReinitIfPreviouslyFailed: false,
        });

        const loggers = [
          sqlite3.config.error,
          sqlite3.config.warn,
          sqlite3.config.log,
        ];
        const log = sqlite3.config.log;
        const warn = sqlite3.config.warn;
        const error = sqlite3.config.error;

        const __mapVfsToPool = new Map();
        const getPoolForVfs = (pVfs) => __mapVfsToPool.get(pVfs);
        const setPoolForVfs = (pVfs, pool) => {
          if (pool) __mapVfsToPool.set(pVfs, pool);
          else __mapVfsToPool.delete(pVfs);
        };

        const __mapSqlite3File = new Map();
        const getPoolForPFile = (pFile) => __mapSqlite3File.get(pFile);
        const setPoolForPFile = (pFile, pool) => {
          if (pool) __mapSqlite3File.set(pFile, pool);
          else __mapSqlite3File.delete(pFile);
        };

        const ioMethods = {
          xCheckReservedLock: function (pFile, pOut) {
            const pool = getPoolForPFile(pFile);
            pool.log('xCheckReservedLock');
            pool.storeErr();
            wasm.poke32(pOut, 1);
            return 0;
          },
          xClose: function (pFile) {
            const pool = getPoolForPFile(pFile);
            pool.storeErr();
            const file = pool.getOFileForS3File(pFile);
            if (file) {
              try {
                pool.log(`xClose ${file.path}`);
                pool.mapS3FileToOFile(pFile, false);
                file.sah.flush();
                if (file.flags & capi.SQLITE_OPEN_DELETEONCLOSE) {
                  pool.deletePath(file.path);
                }
              } catch (e) {
                return pool.storeErr(e, capi.SQLITE_IOERR);
              }
            }
            return 0;
          },
          xDeviceCharacteristics: function (pFile) {
            return capi.SQLITE_IOCAP_UNDELETABLE_WHEN_OPEN;
          },
          xFileControl: function (pFile, opId, pArg) {
            return capi.SQLITE_NOTFOUND;
          },
          xFileSize: function (pFile, pSz64) {
            const pool = getPoolForPFile(pFile);
            pool.log(`xFileSize`);
            const file = pool.getOFileForS3File(pFile);
            const size = file.sah.getSize() - HEADER_OFFSET_DATA;

            wasm.poke64(pSz64, BigInt(size));
            return 0;
          },
          xLock: function (pFile, lockType) {
            const pool = getPoolForPFile(pFile);
            pool.log(`xLock ${lockType}`);
            pool.storeErr();
            const file = pool.getOFileForS3File(pFile);
            file.lockType = lockType;
            return 0;
          },
          xRead: function (pFile, pDest, n, offset64) {
            const pool = getPoolForPFile(pFile);
            pool.storeErr();
            const file = pool.getOFileForS3File(pFile);
            pool.log(`xRead ${file.path} ${n} @ ${offset64}`);
            try {
              const nRead = file.sah.read(
                wasm.heap8u().subarray(pDest, pDest + n),
                { at: HEADER_OFFSET_DATA + Number(offset64) },
              );
              if (nRead < n) {
                wasm.heap8u().fill(0, pDest + nRead, pDest + n);
                return capi.SQLITE_IOERR_SHORT_READ;
              }
              return 0;
            } catch (e) {
              return pool.storeErr(e, capi.SQLITE_IOERR);
            }
          },
          xSectorSize: function (pFile) {
            return SECTOR_SIZE;
          },
          xSync: function (pFile, flags) {
            const pool = getPoolForPFile(pFile);
            pool.log(`xSync ${flags}`);
            pool.storeErr();
            const file = pool.getOFileForS3File(pFile);

            try {
              file.sah.flush();
              return 0;
            } catch (e) {
              return pool.storeErr(e, capi.SQLITE_IOERR);
            }
          },
          xTruncate: function (pFile, sz64) {
            const pool = getPoolForPFile(pFile);
            pool.log(`xTruncate ${sz64}`);
            pool.storeErr();
            const file = pool.getOFileForS3File(pFile);

            try {
              file.sah.truncate(HEADER_OFFSET_DATA + Number(sz64));
              return 0;
            } catch (e) {
              return pool.storeErr(e, capi.SQLITE_IOERR);
            }
          },
          xUnlock: function (pFile, lockType) {
            const pool = getPoolForPFile(pFile);
            pool.log('xUnlock');
            const file = pool.getOFileForS3File(pFile);
            file.lockType = lockType;
            return 0;
          },
          xWrite: function (pFile, pSrc, n, offset64) {
            const pool = getPoolForPFile(pFile);
            pool.storeErr();
            const file = pool.getOFileForS3File(pFile);
            pool.log(`xWrite ${file.path} ${n} ${offset64}`);
            try {
              const nBytes = file.sah.write(
                wasm.heap8u().subarray(pSrc, pSrc + n),
                { at: HEADER_OFFSET_DATA + Number(offset64) },
              );
              return n === nBytes ? 0 : toss('Unknown write() failure.');
            } catch (e) {
              return pool.storeErr(e, capi.SQLITE_IOERR);
            }
          },
        };

        const opfsIoMethods = new capi.sqlite3_io_methods();
        opfsIoMethods.$iVersion = 1;
        sqlite3.vfs.installVfs({
          io: { struct: opfsIoMethods, methods: ioMethods },
        });

        const vfsMethods = {
          xAccess: function (pVfs, zName, flags, pOut) {
            const pool = getPoolForVfs(pVfs);
            pool.storeErr();
            try {
              const name = pool.getPath(zName);
              wasm.poke32(pOut, pool.hasFilename(name) ? 1 : 0);
            } catch (e) {
              wasm.poke32(pOut, 0);
            }
            return 0;
          },
          xCurrentTime: function (pVfs, pOut) {
            wasm.poke(
              pOut,
              2440587.5 + new Date().getTime() / 86400000,
              'double',
            );
            return 0;
          },
          xCurrentTimeInt64: function (pVfs, pOut) {
            wasm.poke(pOut, 2440587.5 * 86400000 + new Date().getTime(), 'i64');
            return 0;
          },
          xDelete: function (pVfs, zName, doSyncDir) {
            const pool = getPoolForVfs(pVfs);
            pool.log(`xDelete ${wasm.cstrToJs(zName)}`);
            pool.storeErr();
            try {
              pool.deletePath(pool.getPath(zName));
              return 0;
            } catch (e) {
              pool.storeErr(e);
              return capi.SQLITE_IOERR_DELETE;
            }
          },
          xFullPathname: function (pVfs, zName, nOut, pOut) {
            const i = wasm.cstrncpy(pOut, zName, nOut);
            return i < nOut ? 0 : capi.SQLITE_CANTOPEN;
          },
          xGetLastError: function (pVfs, nOut, pOut) {
            const pool = getPoolForVfs(pVfs);
            const e = pool.popErr();
            pool.log(`xGetLastError ${nOut} e =`, e);
            if (e) {
              const scope = wasm.scopedAllocPush();
              try {
                const [cMsg, n] = wasm.scopedAllocCString(e.message, true);
                wasm.cstrncpy(pOut, cMsg, nOut);
                if (n > nOut) wasm.poke8(pOut + nOut - 1, 0);
              } catch (e) {
                return capi.SQLITE_NOMEM;
              } finally {
                wasm.scopedAllocPop(scope);
              }
            }
            return e ? e.sqlite3Rc || capi.SQLITE_IOERR : 0;
          },

          xOpen: function f(pVfs, zName, pFile, flags, pOutFlags) {
            const pool = getPoolForVfs(pVfs);
            try {
              pool.log(`xOpen ${wasm.cstrToJs(zName)} ${flags}`);

              const path =
                zName && wasm.peek8(zName)
                  ? pool.getPath(zName)
                  : getRandomName();
              let sah = pool.getSAHForPath(path);
              if (!sah && flags & capi.SQLITE_OPEN_CREATE) {
                if (pool.getFileCount() < pool.getCapacity()) {
                  sah = pool.nextAvailableSAH();
                  pool.setAssociatedPath(sah, path, flags);
                } else {
                  toss('SAH pool is full. Cannot create file', path);
                }
              }
              if (!sah) {
                toss('file not found:', path);
              }

              const file = { path, flags, sah };
              pool.mapS3FileToOFile(pFile, file);
              file.lockType = capi.SQLITE_LOCK_NONE;
              const sq3File = new capi.sqlite3_file(pFile);
              sq3File.$pMethods = opfsIoMethods.pointer;
              sq3File.dispose();
              wasm.poke32(pOutFlags, flags);
              return 0;
            } catch (e) {
              pool.storeErr(e);
              return capi.SQLITE_CANTOPEN;
            }
          },
        };

        const createOpfsVfs = function (vfsName) {
          if (sqlite3.capi.sqlite3_vfs_find(vfsName)) {
            toss3('VFS name is already registered:', vfsName);
          }
          const opfsVfs = new capi.sqlite3_vfs();

          const pDVfs = capi.sqlite3_vfs_find(null);
          const dVfs = pDVfs ? new capi.sqlite3_vfs(pDVfs) : null;
          opfsVfs.$iVersion = 2;
          opfsVfs.$szOsFile = capi.sqlite3_file.structInfo.sizeof;
          opfsVfs.$mxPathname = HEADER_MAX_PATH_SIZE;
          opfsVfs.addOnDispose(
            (opfsVfs.$zName = wasm.allocCString(vfsName)),
            () => setPoolForVfs(opfsVfs.pointer, 0),
          );

          if (dVfs) {
            opfsVfs.$xRandomness = dVfs.$xRandomness;
            opfsVfs.$xSleep = dVfs.$xSleep;
            dVfs.dispose();
          }
          if (!opfsVfs.$xRandomness && !vfsMethods.xRandomness) {
            vfsMethods.xRandomness = function (pVfs, nOut, pOut) {
              const heap = wasm.heap8u();
              let i = 0;
              for (; i < nOut; ++i)
                heap[pOut + i] = (Math.random() * 255000) & 0xff;
              return i;
            };
          }
          if (!opfsVfs.$xSleep && !vfsMethods.xSleep) {
            vfsMethods.xSleep = (pVfs, ms) => 0;
          }
          sqlite3.vfs.installVfs({
            vfs: { struct: opfsVfs, methods: vfsMethods },
          });
          return opfsVfs;
        };

        class OpfsSAHPool {
          vfsDir;

          #dhVfsRoot;

          #dhOpaque;

          #dhVfsParent;

          #mapSAHToName = new Map();

          #mapFilenameToSAH = new Map();

          #availableSAH = new Set();

          #mapS3FileToOFile_ = new Map();

          #apBody = new Uint8Array(HEADER_CORPUS_SIZE);

          #dvBody;

          #cVfs;

          #verbosity;

          constructor(options = Object.create(null)) {
            this.#verbosity = options.verbosity ?? optionDefaults.verbosity;
            this.vfsName = options.name || optionDefaults.name;
            this.#cVfs = createOpfsVfs(this.vfsName);
            setPoolForVfs(this.#cVfs.pointer, this);
            this.vfsDir = options.directory || '.' + this.vfsName;
            this.#dvBody = new DataView(
              this.#apBody.buffer,
              this.#apBody.byteOffset,
            );
            this.isReady = this.reset(
              !!(options.clearOnInit ?? optionDefaults.clearOnInit),
            ).then(() => {
              if (this.$error) throw this.$error;
              return this.getCapacity()
                ? Promise.resolve(undefined)
                : this.addCapacity(
                    options.initialCapacity || optionDefaults.initialCapacity,
                  );
            });
          }

          #logImpl(level, ...args) {
            if (this.#verbosity > level)
              loggers[level](this.vfsName + ':', ...args);
          }
          log(...args) {
            this.#logImpl(2, ...args);
          }
          warn(...args) {
            this.#logImpl(1, ...args);
          }
          error(...args) {
            this.#logImpl(0, ...args);
          }

          getVfs() {
            return this.#cVfs;
          }

          getCapacity() {
            return this.#mapSAHToName.size;
          }

          getFileCount() {
            return this.#mapFilenameToSAH.size;
          }

          getFileNames() {
            const rc = [];
            const iter = this.#mapFilenameToSAH.keys();
            for (const n of iter) rc.push(n);
            return rc;
          }

          async addCapacity(n) {
            for (let i = 0; i < n; ++i) {
              const name = getRandomName();
              const h = await this.#dhOpaque.getFileHandle(name, {
                create: true,
              });
              const ah = await h.createSyncAccessHandle();
              this.#mapSAHToName.set(ah, name);
              this.setAssociatedPath(ah, '', 0);
            }
            return this.getCapacity();
          }

          async reduceCapacity(n) {
            let nRm = 0;
            for (const ah of Array.from(this.#availableSAH)) {
              if (nRm === n || this.getFileCount() === this.getCapacity()) {
                break;
              }
              const name = this.#mapSAHToName.get(ah);

              ah.close();
              await this.#dhOpaque.removeEntry(name);
              this.#mapSAHToName.delete(ah);
              this.#availableSAH.delete(ah);
              ++nRm;
            }
            return nRm;
          }

          releaseAccessHandles() {
            for (const ah of this.#mapSAHToName.keys()) ah.close();
            this.#mapSAHToName.clear();
            this.#mapFilenameToSAH.clear();
            this.#availableSAH.clear();
          }

          async acquireAccessHandles(clearFiles) {
            const files = [];
            for await (const [name, h] of this.#dhOpaque) {
              if ('file' === h.kind) {
                files.push([name, h]);
              }
            }
            return Promise.all(
              files.map(async ([name, h]) => {
                try {
                  const ah = await h.createSyncAccessHandle();
                  this.#mapSAHToName.set(ah, name);
                  if (clearFiles) {
                    ah.truncate(HEADER_OFFSET_DATA);
                    this.setAssociatedPath(ah, '', 0);
                  } else {
                    const path = this.getAssociatedPath(ah);
                    if (path) {
                      this.#mapFilenameToSAH.set(path, ah);
                    } else {
                      this.#availableSAH.add(ah);
                    }
                  }
                } catch (e) {
                  this.storeErr(e);
                  this.releaseAccessHandles();
                  throw e;
                }
              }),
            );
          }

          getAssociatedPath(sah) {
            sah.read(this.#apBody, { at: 0 });

            const flags = this.#dvBody.getUint32(HEADER_OFFSET_FLAGS);
            if (
              this.#apBody[0] &&
              (flags & capi.SQLITE_OPEN_DELETEONCLOSE ||
                (flags & PERSISTENT_FILE_TYPES) === 0)
            ) {
              warn(
                `Removing file with unexpected flags ${flags.toString(16)}`,
                this.#apBody,
              );
              this.setAssociatedPath(sah, '', 0);
              return '';
            }

            const fileDigest = new Uint32Array(HEADER_DIGEST_SIZE / 4);
            sah.read(fileDigest, { at: HEADER_OFFSET_DIGEST });
            const compDigest = this.computeDigest(this.#apBody);
            if (fileDigest.every((v, i) => v === compDigest[i])) {
              const pathBytes = this.#apBody.findIndex((v) => 0 === v);
              if (0 === pathBytes) {
                sah.truncate(HEADER_OFFSET_DATA);
              }
              return pathBytes
                ? textDecoder.decode(this.#apBody.subarray(0, pathBytes))
                : '';
            } else {
              warn('Disassociating file with bad digest.');
              this.setAssociatedPath(sah, '', 0);
              return '';
            }
          }

          setAssociatedPath(sah, path, flags) {
            const enc = textEncoder.encodeInto(path, this.#apBody);
            if (HEADER_MAX_PATH_SIZE <= enc.written + 1) {
              toss('Path too long:', path);
            }
            this.#apBody.fill(0, enc.written, HEADER_MAX_PATH_SIZE);
            this.#dvBody.setUint32(HEADER_OFFSET_FLAGS, flags);

            const digest = this.computeDigest(this.#apBody);
            sah.write(this.#apBody, { at: 0 });
            sah.write(digest, { at: HEADER_OFFSET_DIGEST });
            sah.flush();

            if (path) {
              this.#mapFilenameToSAH.set(path, sah);
              this.#availableSAH.delete(sah);
            } else {
              sah.truncate(HEADER_OFFSET_DATA);
              this.#availableSAH.add(sah);
            }
          }

          computeDigest(byteArray) {
            let h1 = 0xdeadbeef;
            let h2 = 0x41c6ce57;
            for (const v of byteArray) {
              h1 = 31 * h1 + v * 307;
              h2 = 31 * h2 + v * 307;
            }
            return new Uint32Array([h1 >>> 0, h2 >>> 0]);
          }

          async reset(clearFiles) {
            await this.isReady;
            let h = await navigator.storage.getDirectory();
            let prev, prevName;
            for (const d of this.vfsDir.split('/')) {
              if (d) {
                prev = h;
                h = await h.getDirectoryHandle(d, { create: true });
              }
            }
            this.#dhVfsRoot = h;
            this.#dhVfsParent = prev;
            this.#dhOpaque = await this.#dhVfsRoot.getDirectoryHandle(
              OPAQUE_DIR_NAME,
              { create: true },
            );
            this.releaseAccessHandles();
            return this.acquireAccessHandles(clearFiles);
          }

          getPath(arg) {
            if (wasm.isPtr(arg)) arg = wasm.cstrToJs(arg);
            return (
              arg instanceof URL ? arg : new URL(arg, 'file://localhost/')
            ).pathname;
          }

          deletePath(path) {
            const sah = this.#mapFilenameToSAH.get(path);
            if (sah) {
              this.#mapFilenameToSAH.delete(path);
              this.setAssociatedPath(sah, '', 0);
            }
            return !!sah;
          }

          storeErr(e, code) {
            if (e) {
              e.sqlite3Rc = code || capi.SQLITE_IOERR;
              this.error(e);
            }
            this.$error = e;
            return code;
          }

          popErr() {
            const rc = this.$error;
            this.$error = undefined;
            return rc;
          }

          nextAvailableSAH() {
            const [rc] = this.#availableSAH.keys();
            return rc;
          }

          getOFileForS3File(pFile) {
            return this.#mapS3FileToOFile_.get(pFile);
          }

          mapS3FileToOFile(pFile, file) {
            if (file) {
              this.#mapS3FileToOFile_.set(pFile, file);
              setPoolForPFile(pFile, this);
            } else {
              this.#mapS3FileToOFile_.delete(pFile);
              setPoolForPFile(pFile, false);
            }
          }

          hasFilename(name) {
            return this.#mapFilenameToSAH.has(name);
          }

          getSAHForPath(path) {
            return this.#mapFilenameToSAH.get(path);
          }

          async removeVfs() {
            if (!this.#cVfs.pointer || !this.#dhOpaque) return false;
            capi.sqlite3_vfs_unregister(this.#cVfs.pointer);
            this.#cVfs.dispose();
            delete initPromises[this.vfsName];
            try {
              this.releaseAccessHandles();
              await this.#dhVfsRoot.removeEntry(OPAQUE_DIR_NAME, {
                recursive: true,
              });
              this.#dhOpaque = undefined;
              await this.#dhVfsParent.removeEntry(this.#dhVfsRoot.name, {
                recursive: true,
              });
              this.#dhVfsRoot = this.#dhVfsParent = undefined;
            } catch (e) {
              sqlite3.config.error(this.vfsName, 'removeVfs() failed:', e);
            }
            return true;
          }

          exportFile(name) {
            const sah =
              this.#mapFilenameToSAH.get(name) || toss('File not found:', name);
            const n = sah.getSize() - HEADER_OFFSET_DATA;
            const b = new Uint8Array(n > 0 ? n : 0);
            if (n > 0) {
              const nRead = sah.read(b, { at: HEADER_OFFSET_DATA });
              if (nRead != n) {
                toss(
                  'Expected to read ' + n + ' bytes but read ' + nRead + '.',
                );
              }
            }
            return b;
          }

          async importDbChunked(name, callback) {
            const sah =
              this.#mapFilenameToSAH.get(name) ||
              this.nextAvailableSAH() ||
              toss('No available handles to import to.');
            sah.truncate(0);
            let nWrote = 0,
              chunk,
              checkedHeader = false,
              err = false;
            try {
              while (undefined !== (chunk = await callback())) {
                if (chunk instanceof ArrayBuffer) chunk = new Uint8Array(chunk);
                if (0 === nWrote && chunk.byteLength >= 15) {
                  util.affirmDbHeader(chunk);
                  checkedHeader = true;
                }
                sah.write(chunk, { at: HEADER_OFFSET_DATA + nWrote });
                nWrote += chunk.byteLength;
              }
              if (nWrote < 512 || 0 !== nWrote % 512) {
                toss(
                  'Input size',
                  nWrote,
                  'is not correct for an SQLite database.',
                );
              }
              if (!checkedHeader) {
                const header = new Uint8Array(20);
                sah.read(header, { at: 0 });
                util.affirmDbHeader(header);
              }
              sah.write(new Uint8Array([1, 1]), {
                at: HEADER_OFFSET_DATA + 18,
              });
            } catch (e) {
              this.setAssociatedPath(sah, '', 0);
              throw e;
            }
            this.setAssociatedPath(sah, name, capi.SQLITE_OPEN_MAIN_DB);
            return nWrote;
          }

          importDb(name, bytes) {
            if (bytes instanceof ArrayBuffer) bytes = new Uint8Array(bytes);
            else if (bytes instanceof Function)
              return this.importDbChunked(name, bytes);
            const sah =
              this.#mapFilenameToSAH.get(name) ||
              this.nextAvailableSAH() ||
              toss('No available handles to import to.');
            const n = bytes.byteLength;
            if (n < 512 || n % 512 != 0) {
              toss('Byte array size is invalid for an SQLite db.');
            }
            const header = 'SQLite format 3';
            for (let i = 0; i < header.length; ++i) {
              if (header.charCodeAt(i) !== bytes[i]) {
                toss('Input does not contain an SQLite database header.');
              }
            }
            const nWrote = sah.write(bytes, { at: HEADER_OFFSET_DATA });
            if (nWrote != n) {
              this.setAssociatedPath(sah, '', 0);
              toss(
                'Expected to write ' + n + ' bytes but wrote ' + nWrote + '.',
              );
            } else {
              sah.write(new Uint8Array([1, 1]), {
                at: HEADER_OFFSET_DATA + 18,
              });
              this.setAssociatedPath(sah, name, capi.SQLITE_OPEN_MAIN_DB);
            }
            return nWrote;
          }
        }

        class OpfsSAHPoolUtil {
          #p;

          constructor(sahPool) {
            this.#p = sahPool;
            this.vfsName = sahPool.vfsName;
          }

          async addCapacity(n) {
            return this.#p.addCapacity(n);
          }

          async reduceCapacity(n) {
            return this.#p.reduceCapacity(n);
          }

          getCapacity() {
            return this.#p.getCapacity(this.#p);
          }

          getFileCount() {
            return this.#p.getFileCount();
          }
          getFileNames() {
            return this.#p.getFileNames();
          }

          async reserveMinimumCapacity(min) {
            const c = this.#p.getCapacity();
            return c < min ? this.#p.addCapacity(min - c) : c;
          }

          exportFile(name) {
            return this.#p.exportFile(name);
          }

          importDb(name, bytes) {
            return this.#p.importDb(name, bytes);
          }

          async wipeFiles() {
            return this.#p.reset(true);
          }

          unlink(filename) {
            return this.#p.deletePath(filename);
          }

          async removeVfs() {
            return this.#p.removeVfs();
          }
        }

        const apiVersionCheck = async () => {
          const dh = await navigator.storage.getDirectory();
          const fn = '.opfs-sahpool-sync-check-' + getRandomName();
          const fh = await dh.getFileHandle(fn, { create: true });
          const ah = await fh.createSyncAccessHandle();
          const close = ah.close();
          await close;
          await dh.removeEntry(fn);
          if (close?.then) {
            toss(
              'The local OPFS API is too old for opfs-sahpool:',
              'it has an async FileSystemSyncAccessHandle.close() method.',
            );
          }
          return true;
        };

        sqlite3.installOpfsSAHPoolVfs = async function (
          options = Object.create(null),
        ) {
          options = Object.assign(
            Object.create(null),
            optionDefaults,
            options || {},
          );
          const vfsName = options.name;
          if (options.$testThrowPhase1) {
            throw options.$testThrowPhase1;
          }
          if (initPromises[vfsName]) {
            try {
              const p = await initPromises[vfsName];

              return p;
            } catch (e) {
              if (options.forceReinitIfPreviouslyFailed) {
                delete initPromises[vfsName];
              } else {
                throw e;
              }
            }
          }
          if (
            !globalThis.FileSystemHandle ||
            !globalThis.FileSystemDirectoryHandle ||
            !globalThis.FileSystemFileHandle ||
            !globalThis.FileSystemFileHandle.prototype.createSyncAccessHandle ||
            !navigator?.storage?.getDirectory
          ) {
            return (initPromises[vfsName] = Promise.reject(
              new Error('Missing required OPFS APIs.'),
            ));
          }

          return (initPromises[vfsName] = apiVersionCheck()
            .then(async function () {
              if (options.$testThrowPhase2) {
                throw options.$testThrowPhase2;
              }
              const thePool = new OpfsSAHPool(options);
              return thePool.isReady
                .then(async () => {
                  const poolUtil = new OpfsSAHPoolUtil(thePool);
                  if (sqlite3.oo1) {
                    const oo1 = sqlite3.oo1;
                    const theVfs = thePool.getVfs();
                    const OpfsSAHPoolDb = function (...args) {
                      const opt = oo1.DB.dbCtorHelper.normalizeArgs(...args);
                      opt.vfs = theVfs.$zName;
                      oo1.DB.dbCtorHelper.call(this, opt);
                    };
                    OpfsSAHPoolDb.prototype = Object.create(oo1.DB.prototype);
                    poolUtil.OpfsSAHPoolDb = OpfsSAHPoolDb;
                  }
                  thePool.log('VFS initialized.');
                  return poolUtil;
                })
                .catch(async (e) => {
                  await thePool.removeVfs().catch(() => {});
                  throw e;
                });
            })
            .catch((err) => {
              return (initPromises[vfsName] = Promise.reject(err));
            }));
        };
      });

      ('use strict');
      if ('undefined' !== typeof Module) {
        const SABC = Object.assign(
          Object.create(null),
          {
            exports:
              'undefined' === typeof wasmExports ? Module['asm'] : wasmExports,
            memory: Module.wasmMemory,
          },
          globalThis.sqlite3ApiConfig || {},
        );

        globalThis.sqlite3ApiConfig = SABC;
        let sqlite3;
        try {
          sqlite3 = globalThis.sqlite3ApiBootstrap();
        } catch (e) {
          console.error('sqlite3ApiBootstrap() error:', e);
          throw e;
        } finally {
          delete globalThis.sqlite3ApiBootstrap;
          delete globalThis.sqlite3ApiConfig;
        }

        Module.sqlite3 = sqlite3;
      } else {
        console.warn(
          'This is not running in an Emscripten module context, so',
          'globalThis.sqlite3ApiBootstrap() is _not_ being called due to lack',
          'of config info for the WASM environment.',
          'It must be called manually.',
        );
      }
    };

    moduleRtn = readyPromise;

    return moduleRtn;
  };
})();

const toExportForESM = (function () {
  const originalInit = sqlite3InitModule;
  if (!originalInit) {
    throw new Error(
      'Expecting globalThis.sqlite3InitModule to be defined by the Emscripten build.',
    );
  }

  const initModuleState = (globalThis.sqlite3InitModuleState = Object.assign(
    Object.create(null),
    {
      moduleScript: globalThis?.document?.currentScript,
      isWorker: 'undefined' !== typeof WorkerGlobalScope,
      location: globalThis.location,
      urlParams: globalThis?.location?.href
        ? new URL(globalThis.location.href).searchParams
        : new URLSearchParams(),
    },
  ));
  initModuleState.debugModule = initModuleState.urlParams.has(
    'sqlite3.debugModule',
  )
    ? (...args) => console.warn('sqlite3.debugModule:', ...args)
    : () => {};

  if (initModuleState.urlParams.has('sqlite3.dir')) {
    initModuleState.sqlite3Dir =
      initModuleState.urlParams.get('sqlite3.dir') + '/';
  } else if (initModuleState.moduleScript) {
    const li = initModuleState.moduleScript.src.split('/');
    li.pop();
    initModuleState.sqlite3Dir = li.join('/') + '/';
  }

  globalThis.sqlite3InitModule = function ff(...args) {
    return originalInit(...args)
      .then((EmscriptenModule) => {
        EmscriptenModule.runSQLite3PostLoadInit(EmscriptenModule);
        const s = EmscriptenModule.sqlite3;
        s.scriptInfo = initModuleState;

        if (ff.__isUnderTest) s.__isUnderTest = true;
        const f = s.asyncPostInit;
        delete s.asyncPostInit;
        const rv = f();
        return rv;
      })
      .catch((e) => {
        console.error('Exception loading sqlite3 module:', e);
        throw e;
      });
  };
  globalThis.sqlite3InitModule.ready = originalInit.ready;

  if (globalThis.sqlite3InitModuleState.moduleScript) {
    const sim = globalThis.sqlite3InitModuleState;
    let src = sim.moduleScript.src.split('/');
    src.pop();
    sim.scriptDir = src.join('/') + '/';
  }
  initModuleState.debugModule('sqlite3InitModuleState =', initModuleState);
  if (0) {
    console.warn('Replaced sqlite3InitModule()');
    console.warn('globalThis.location.href =', globalThis.location.href);
    if ('undefined' !== typeof document) {
      console.warn(
        'document.currentScript.src =',
        document?.currentScript?.src,
      );
    }
  }
  return globalThis.sqlite3InitModule;
})();
sqlite3InitModule = toExportForESM;
export default sqlite3InitModule;
