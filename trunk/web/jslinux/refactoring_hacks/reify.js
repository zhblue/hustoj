/*
JS Reify

Uses JSShaper to parse javascript files and replace symbol names

>node reify.js [filename] old_symbolname new_symbolname

- it barfs output to stdout
- doesn't work w. stdin, node.js can't do sync stdin,
so write to tmpfile for external feeding of this animal)
*/
"use strict"; "use restrict";
var require = require || function(f) { load(f); };
require.paths && typeof __dirname !== "undefined" && require.paths.unshift(__dirname);
var args = (typeof process !== "undefined" && process.argv !== undefined) ?
    process.argv.slice(2) : arguments;
var log = (typeof console !== "undefined") && console.log || print;

//if (args.length > 0 && args[0] === "--") {
//    args.shift();
//}

if (args.length <= 0) {
    log("run-shaper: filename OLDNAME NEWNAME");
    (typeof quit === "undefined" ? process.exit : quit)(0);
}
var Shaper = Shaper || require("shaper.js") || Shaper;

var filename = args.shift();
var ORIGNAME = args.shift();
var NEWNAME  = args.shift();

//define reify transformer
Shaper("reify", function(root) {
    return Shaper.traverse(root, {pre: function(node, ref) {
        if (node.type === tkn.IDENTIFIER) { // or: if (Shaper.match("$", node)) {
            if(node.value === ORIGNAME ){
                Shaper.renameIdentifier(node, NEWNAME);
            }
        }
    }});
});

//specify the transform pipeline
var pipeline = ["reify","source"];

// read: js/d8/v8 || rhino || node
var read = read || typeof readFile !== "undefined" && readFile || require("fs").readFileSync;
var src = read(filename);
var root = Shaper.parseScript(src, filename);
root = Shaper.run(root, pipeline);
