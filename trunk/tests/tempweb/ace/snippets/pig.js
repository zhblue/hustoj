ace.define("ace/snippets/pig",[], function(require, exports, module) {
"use strict";

exports.snippetText = "";
exports.scope = "pig";

});
                (function() {
                    ace.require(["ace/snippets/pig"], function(m) {
                        if (typeof module == "object" && typeof exports == "object" && module) {
                            module.exports = m;
                        }
                    });
                })();
            