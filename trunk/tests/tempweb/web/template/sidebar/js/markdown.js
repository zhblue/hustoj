/*
 *  This file is part of moemark-renderer.
 *
 *  Copyright (c) 2016 Menci <huanghaorui301@gmail.com>
 *
 *  moemark-renderer is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  moemark-renderer is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with moemark-renderer. If not, see <http://www.gnu.org/licenses/>.
 */

const MoeMark = require('moemark');
const katex = require('katex');
const mj = require('mathjax-node');

let defaultCache = {
    data: {},
    get(key) {
        return this.data[key];
    },
    set(key, val) {
        this.data[key] = val;
    }
};

let config = {
    highlight: require('./highlight')
};

let uuid = require('uuid');

function render(s, cb) {
    if (!s.trim()) return cb('');

    let mathCnt = 0, mathPending = 0, maths = new Array(), mathID = new Array(), hlCnt = 0, hlPending = 0, hls = new Array(), hlID = new Array(), res, callback, ss, cache = render.cache, cacheOption = render.cacheOption, finished = false;
    if (cacheOption.result) {
        let x = cache.get('RES_' + s);
        if (x !== undefined) return cb(x);
    }

    MoeMark.setOptions({
        lineNumber: false,
        math: true,
        highlight: function(code, lang) {
            if (cacheOption.highlight) {
                let x = cache.get('H_' + lang + '_' + code);
                if (x !== undefined) return x;
            }
            let id = hlCnt;
            hlCnt++, hlPending++;
            config.highlight(code, lang, res => {
                hls[id] = res;
                if (cacheOption.highlight) cache.set('H_' + lang + '_' + code, res);
                if (!--hlPending) finish();
            });
            return hlID[id] = uuid();
        },
        mathRenderer: function(str, display) {
            let mathFinish = (error, result) => {
                if (error) maths[id] = '<p><div style="display: inline-block; border: 1px solid #000; "><strong>' + data.errors.toString() + '</strong></div></p>';
                else if (display) maths[id] = '<p style="text-align: center; ">' + result + '</p>';
                else maths[id] = result;
                if (cacheOption.math) cache.set('M_' + display + '_' + str, maths[id]);
                if (!--mathPending) finish();
            }

            const id = mathCnt;
            mathCnt++, mathPending++;

            try {
                let x = cache.get('M_' + display + '_' + str);
                if (x !== undefined) process.nextTick(() => mathFinish(null, x));
                else {
                    let res = katex.renderToString(str, { displayMode: display });
                    process.nextTick(() => mathFinish(null, '<span style="zoom: 1.01; ">' + res + '</span>'));
                }
            } catch (e) {
                mj.typeset({
                    math: str,
                    format: display ? 'TeX' : 'inline-TeX',
                    svg: true,
                    width: 0
                }, data => {
                    mathFinish(data.errors, data.svg);
                });
            }
            return mathID[id] = uuid();
        }
    });

    function finish() {
        if (finished || !res || mathPending || hlPending) return;
        finished = true;
        if (maths.length || hls.length) {
            for (let i = 0; i < maths.length; i++) {
              res = res.replace(mathID[i], maths[i]);
            }
            for (let i = 0; i < hls.length; i++) {
              res = res.replace(hlID[i], hls[i]);
            }
        }
        if (cacheOption.result) cache.set('RES_' + s, res);
        cb(res);
    }

    try {
        let XSS = require('xss');
        let CSSFilter = require('cssfilter');
        let whiteList = Object.assign({}, require('xss/lib/default').whiteList);
        delete whiteList.audio;
        delete whiteList.video;
        for (let tag in whiteList) whiteList[tag] = whiteList[tag].concat(['style', 'class']);
        let xss = new XSS.FilterXSS({
          whiteList: whiteList,
          stripIgnoreTag: true,
          onTagAttr: (tag, name, value, isWhiteAttr) => {
            if (tag.toLowerCase() === 'img' && name.toLowerCase() === 'src' && value.startsWith('data:image/')) return name + '="' + XSS.escapeAttrValue(value) + '"';
          }
        });
        let replaceXSS = s => {
          s = xss.process(s);
          if (s) {
            s = `<div style="position: relative; overflow: hidden; ">${s}</div>`;
          }
          return s;
        };

        res = replaceXSS(MoeMark(s));
        if (mathPending == 0 && hlPending == 0) {
            finish();
        }
    } catch(e) {
        cb(e);
    }
};

render.moemark = MoeMark;
render.cache = defaultCache;
render.cacheOption = {
    highlight: true,
    math: true,
    result: false
};
render.config = config;

module.exports = render;
