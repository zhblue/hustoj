ace.define("ace/snippets/jsx",[], function(require, exports, module) {
"use strict";

exports.snippetText = "";
exports.scope = "jsx";

});
                (function() {
                    ace.require(["ace/snippets/jsx"], function(m) {
                        if (typeof module == "object" && typeof exports == "object" && module) {
                            module.exports = m;
                        }
                    });
                })();
            