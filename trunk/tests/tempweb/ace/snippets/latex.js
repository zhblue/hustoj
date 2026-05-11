ace.define("ace/snippets/latex",[], function(require, exports, module) {
"use strict";

exports.snippetText = "";
exports.scope = "latex";

});
                (function() {
                    ace.require(["ace/snippets/latex"], function(m) {
                        if (typeof module == "object" && typeof exports == "object" && module) {
                            module.exports = m;
                        }
                    });
                })();
            