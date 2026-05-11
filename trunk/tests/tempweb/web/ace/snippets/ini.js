ace.define("ace/snippets/ini",[], function(require, exports, module) {
"use strict";

exports.snippetText = "";
exports.scope = "ini";

});
                (function() {
                    ace.require(["ace/snippets/ini"], function(m) {
                        if (typeof module == "object" && typeof exports == "object" && module) {
                            module.exports = m;
                        }
                    });
                })();
            