ace.define("ace/snippets/autohotkey",[], function(require, exports, module) {
"use strict";

exports.snippetText = "";
exports.scope = "autohotkey";

});
                (function() {
                    ace.require(["ace/snippets/autohotkey"], function(m) {
                        if (typeof module == "object" && typeof exports == "object" && module) {
                            module.exports = m;
                        }
                    });
                })();
            