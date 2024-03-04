webpackJsonp([0], [function(t, e, n) {
	function i() {
		var t = a("#nav_box"),
			e = t.find("li a"),
			n = t.find(".ic_line");
		t.on("mouseleave", function() {
			n.hide()
		}), e.on("mouseenter", function() {
			for(var t = e.index(a(this)), i = e.eq(t).width(), o = 0, s = 0; s < t; s++) o += e.eq(s).outerWidth() + 20;
			o += 16, n.show(), n.css({
				left: o,
				width: i
			})
		});
		var i, o = a("#wrap"),
			s = function() {
				var t = document.createElement("div").style;
				for(var e in t)
					if(e.toLowerCase().indexOf("animation") >= 0) return !0;
				return !1
			}(),
			r = a("#page_wp").find(".page"),
			h = a("#star_wp"),
			c = a("#btn_control").find("a"),
			d = [5e3, 5e3, 5e3, 1e4];
		if(s) o.addClass("css3");
		else {
			for(var p = r.find(".img_box"), u = 1; u < 5; u++) p.eq(u - 1).find("img")[0].src = "img/0" + u + ".jpg";
			window.onresize = function() {
				var t = a(window).width(),
					e = a(window).height();
				t / e < 1920 / 1080 ? p.height(e).css({
					width: "auto",
					margin: -.5 * e + "px 0 0 " + -960 / 1080 * e + "px"
				}) : p.width(t).css({
					height: "auto",
					margin: -.5 * t * 1080 / 1920 + "px 0 0 " + -.5 * t + "px"
				})
			}
		}
		var l = {
				one: function() {
					r.removeClass("show").eq(0).addClass("show"), s ? (h.removeClass("show"), cvsEffect.one()) : r.stop().eq(0).css({
						opacity: 0
					}).animate({
						opacity: 1
					}, 500)
				},
				two: function() {
					s ? (r.eq(0).hasClass("show") && r.eq(0).addClass("hide"), h.removeClass("show"), cvsEffect.two(), setTimeout(function() {
						r.eq(0).removeClass("hide")
					}, 400)) : r.stop().eq(1).css({
						opacity: 0
					}).animate({
						opacity: 1
					}, 500), r.removeClass("show").eq(1).addClass("show")
				},
				three: function() {
					r.removeClass("show").eq(2).addClass("show"), s ? (cvsEffect.three(), h.addClass("show")) : r.stop().eq(2).css({
						opacity: 0
					}).animate({
						opacity: 1
					}, 500)
				},
				four: function() {
					r.removeClass("show").eq(3).addClass("show"), s ? (cvsEffect.four(), h.addClass("show")) : r.stop().eq(3).css({
						opacity: 0
					}).animate({
						opacity: 1
					}, 500)
				}
			},
			f = {
				next: function() {
					var t = r.index(a("#page_wp").find(".page.show"));
					t = (t + 1) % r.length, f.changeByIdx(t)
				},
				prev: function() {
					var t = r.index(a("#page_wp").find(".page.show"));
					t = (t + r.length - 1) % r.length, f.changeByIdx(t)
				},
				changeByIdx: function(t) {
					switch(clearTimeout(i), t) {
						case 0:
							l.one();
							break;
						case 1:
							l.two();
							break;
						case 2:
							l.three();
							break;
						case 3:
							l.four()
					}
					c.removeClass("cur").eq(t).addClass("cur"), f.changeInterval()
				},
				changeInterval: function() {
					var t = r.index(a("#page_wp").find(".page.show"));
					i = setTimeout(f.next, d[t])
				}
			};
		c.click(function() {
			var t = c.index(a(this));
			f.changeByIdx(t)
		}), a(document).on("mousewheel DOMMouseScroll", function(t) {
			var e = t.originalEvent.wheelDelta || t.originalEvent.detail * -1;
			e > 0 ? f.prev() : f.next()
		}), s && cvsEffect.start(), f.changeByIdx(0)
	}

	function o() {
		function t() {
			return a.ajax({
				url: n,
				dataType: "jsonp",
				jsonpCallback: "xl_callback"
			}).then(function(t) {
				return localStorage.setItem(n, JSON.stringify(t)), t
			}, function() {
				var t = localStorage.getItem(n);
				return t && JSON.parse(t)
			})
		}

		function e(t, e) {
			return a.ajax({
				url: e,
				dataType: "jsonp",
				jsonpCallback: "xl_" + t + "_callback",
				cache: !0
			}).then(function(e) {
				return localStorage.setItem(t, JSON.stringify(e)), e
			}, function() {
				var e = localStorage.getItem(t);
				return e && JSON.parse(e)
			})
		}
		var n = "{}";
		t().then(function(t) {
			// t && t.download_link && e("download_link", t.download_link).then(function(t) {
			// 	t && t.xl9_download && a(".JS-btn-download").attr("href", 'javascript:;')
			// })
		})
	}
	var a = (n(1), n(4));
	n(5),
		function() {
			i(), o()
		}()
}, function(t, e, n) {
	n(2)
}, function(module, exports, __webpack_require__) {
	function objevent(objname, objevent, objfun) {
		var objname = String(objname);
		if("" == objevent) objevent = "onclick";
		else var objevent = String(objevent);
		var objfun = String(objfun),
			thisevent = function(evt) {
				evt = evt || window.event;
				var obj = evt.target || evt.srcElement,
					objnametemp1 = String(obj.tagName),
					objnametemp2 = String(objname);
				if(objnametemp1 == objnametemp2 || "" == objname || obj.parentNode.tagName == objnametemp2 || obj.parentNode.parentNode.tagName == objnametemp2) {
					if(objnametemp1 != objnametemp2)
						if(obj.parentNode.tagName == objnametemp2) obj = obj.parentNode;
						else if(obj.parentNode.parentNode.tagName == objnametemp2) obj = obj.parentNode.parentNode;
					else {
						if(obj.parentNode.parentNode.parentNode.tagName != objnametemp2) return;
						obj = obj.parentNode.parentNode.parentNode
					}
					eval(objfun)(obj)
				}
			},
			thiseventtemp = "document.body." + objevent + "=" + thisevent,
			evalobj = eval(thiseventtemp)
	}

	function _clickon(t) {
		//send_web_click(t)
	}

	function kk_click_pv_rebind_capture() {
		window.attachEvent ? (window.detachEvent("onload", _kk_click_pv_clickon_handler), window.attachEvent("onload", _kk_click_pv_clickon_handler)) : (window.removeEventListener("load", _kk_click_pv_clickon_handler, !0), window.addEventListener("load", _kk_click_pv_clickon_handler, !0))
	}

	function send_web_pv() {
		/*
		var t = new Date,
			e = t.getTime(),
			n = "http://stat.download.xunlei.com:8099/?xlbtid=" + gOption.xlbtid + "&datatype=pageshow&url=" + url_e + "&ref=" + ref_e + "&useragent=" + userAgent_e + "&userid=" + userid + "&cookieid=" + habo_web_uid + "&sessionid=" + habo_web_sessionid + "&appid=" + gOption.appid + "&r=" + 1e5 * Math.random() + "&time=" + e,
			i = new Image;
		i.src = n
               */
	}

	function jumpurl(t) {
		location.href = t
	}

	function send_web_click(t) {
		target = t.target;
		var e = t.getAttribute("_click_rcv_url");
		e && "undefined" != e || (e = t.href);
		var n = encodeURIComponent(e),
			i = t.getAttribute("blockid"),
			o = t.getAttribute("clickid");
		if(o || "undefined" == typeof o || 0 == o || i || "undefined" == typeof i || 0 == i) {
			var a = t.getAttribute("event_id"),
				s = t.getAttribute("var1"),
				r = t.getAttribute("var2"),
				h = t.getAttribute("var3"),
				c = new Date,
				d = c.getTime(),
				p = "/?xlbtid=" + gOption.xlbtid + "&datatype=click&url=" + url_e + "&useragent=" + userAgent_e + "&userid=" + userid + "&cookieid=" + habo_web_uid + "&sessionid=" + habo_web_sessionid + "&appid=" + gOption.appid + "&clickurl=" + n + "&blockid=" + i + "&clickid=" + o + "&r=" + 1e5 * Math.random() + "&time=" + d + "&eventid=" + a + "&var1=" + s + "&var2=" + r + "&var3=" + h,
				u = new Image;
			u.src = p, "_self" == target && setTimeout("jumpurl('" + e + "');", 100)
		}
	}
	var md5 = __webpack_require__(3),
		hex_md5 = md5.hex_md5,
		gOption = {
			appid: "",
			domain: "",
			xlbtid: ""
		},
		c_getCookie = function(t) {
			var e = t + "=",
				n = document.cookie.indexOf(e);
			if(n != -1) {
				n += e.length;
				var i = document.cookie.indexOf(";", n);
				return i == -1 && (i = document.cookie.length), unescape(document.cookie.substring(n, i))
			}
			return ""
		},
		c_setCookie = function(t, e, n, i) {
			if(arguments.length > 3) {
				var o = new Date((new Date).getTime() + 36e5 * i);
				document.cookie = e + "=" + escape(n) + ";path=/;domain=" + t + ";expires=" + o.toGMTString()
			} else document.cookie = e + "=" + escape(n) + ";path=/;domain=" + t
		};
	if(habo_web_uid = c_getCookie("HABOWEBUID"), !habo_web_uid || "undefined" == habo_web_uid) {
		var random = Math.random(),
			browser = navigator.appName + "_" + navigator.appVersion + "_" + navigator.userAgent + "_" + navigator.appCodeName + "_" + navigator.platform,
			nowtime = new Date,
			nowtime_sec = nowtime.valueOf();
		habo_web_uid = hex_md5(nowtime_sec.toString() + browser + random.toString()), c_setCookie(gOption.domain, "HABOWEBUID", habo_web_uid, 87600)
	}
	var habo_web_sessionid = c_getCookie("HABOWEBSESSIONID");
	if(!habo_web_sessionid || "undefined" == habo_web_sessionid) {
		var random = Math.random(),
			browser = navigator.appName + "_" + navigator.appVersion + "_" + navigator.userAgent + "_" + navigator.appCodeName + "_" + navigator.platform,
			nowtime = new Date,
			nowtime_sec = nowtime.valueOf();
		habo_web_sessionid = hex_md5(nowtime_sec.toString() + browser + random.toString()), c_setCookie(gOption.domain, "HABOWEBSESSIONID", habo_web_sessionid, .5)
	}
	var userid = c_getCookie("userid") && c_getCookie("HABOWEBSESSIONID") ? parseInt(c_getCookie("userid")) : 0,
		userAgent = navigator.userAgent,
		userAgent_e = encodeURIComponent(userAgent),
		url = document.location.href,
		ref = document.referrer,
		ref_e = encodeURIComponent(ref),
		url_e = encodeURIComponent(url);
	try {
		var _kk_click_pv_clickon_handler = function() {
			objevent("A", "onmouseup", "_clickon"), objevent("A", "onkeydown", "_clickon")
		};
		kk_click_pv_rebind_capture()
	} catch(e) {}
	//send_web_pv()
}, function(t, e) {
	function n(t) {
		return u(i(p(t), t.length * f))
	}

	function i(t, e) {
		t[e >> 5] |= 128 << e % 32, t[(e + 64 >>> 9 << 4) + 14] = e;
		for(var n = 1732584193, i = -271733879, o = -1732584194, d = 271733878, p = 0; p < t.length; p += 16) {
			var u = n,
				l = i,
				f = o,
				m = d;
			n = a(n, i, o, d, t[p + 0], 7, -680876936), d = a(d, n, i, o, t[p + 1], 12, -389564586), o = a(o, d, n, i, t[p + 2], 17, 606105819), i = a(i, o, d, n, t[p + 3], 22, -1044525330), n = a(n, i, o, d, t[p + 4], 7, -176418897), d = a(d, n, i, o, t[p + 5], 12, 1200080426), o = a(o, d, n, i, t[p + 6], 17, -1473231341), i = a(i, o, d, n, t[p + 7], 22, -45705983), n = a(n, i, o, d, t[p + 8], 7, 1770035416), d = a(d, n, i, o, t[p + 9], 12, -1958414417), o = a(o, d, n, i, t[p + 10], 17, -42063), i = a(i, o, d, n, t[p + 11], 22, -1990404162), n = a(n, i, o, d, t[p + 12], 7, 1804603682), d = a(d, n, i, o, t[p + 13], 12, -40341101), o = a(o, d, n, i, t[p + 14], 17, -1502002290), i = a(i, o, d, n, t[p + 15], 22, 1236535329), n = s(n, i, o, d, t[p + 1], 5, -165796510), d = s(d, n, i, o, t[p + 6], 9, -1069501632), o = s(o, d, n, i, t[p + 11], 14, 643717713), i = s(i, o, d, n, t[p + 0], 20, -373897302), n = s(n, i, o, d, t[p + 5], 5, -701558691), d = s(d, n, i, o, t[p + 10], 9, 38016083), o = s(o, d, n, i, t[p + 15], 14, -660478335), i = s(i, o, d, n, t[p + 4], 20, -405537848), n = s(n, i, o, d, t[p + 9], 5, 568446438), d = s(d, n, i, o, t[p + 14], 9, -1019803690), o = s(o, d, n, i, t[p + 3], 14, -187363961), i = s(i, o, d, n, t[p + 8], 20, 1163531501), n = s(n, i, o, d, t[p + 13], 5, -1444681467), d = s(d, n, i, o, t[p + 2], 9, -51403784), o = s(o, d, n, i, t[p + 7], 14, 1735328473), i = s(i, o, d, n, t[p + 12], 20, -1926607734), n = r(n, i, o, d, t[p + 5], 4, -378558), d = r(d, n, i, o, t[p + 8], 11, -2022574463), o = r(o, d, n, i, t[p + 11], 16, 1839030562), i = r(i, o, d, n, t[p + 14], 23, -35309556), n = r(n, i, o, d, t[p + 1], 4, -1530992060), d = r(d, n, i, o, t[p + 4], 11, 1272893353), o = r(o, d, n, i, t[p + 7], 16, -155497632), i = r(i, o, d, n, t[p + 10], 23, -1094730640), n = r(n, i, o, d, t[p + 13], 4, 681279174), d = r(d, n, i, o, t[p + 0], 11, -358537222), o = r(o, d, n, i, t[p + 3], 16, -722521979), i = r(i, o, d, n, t[p + 6], 23, 76029189), n = r(n, i, o, d, t[p + 9], 4, -640364487), d = r(d, n, i, o, t[p + 12], 11, -421815835), o = r(o, d, n, i, t[p + 15], 16, 530742520), i = r(i, o, d, n, t[p + 2], 23, -995338651), n = h(n, i, o, d, t[p + 0], 6, -198630844), d = h(d, n, i, o, t[p + 7], 10, 1126891415), o = h(o, d, n, i, t[p + 14], 15, -1416354905), i = h(i, o, d, n, t[p + 5], 21, -57434055), n = h(n, i, o, d, t[p + 12], 6, 1700485571), d = h(d, n, i, o, t[p + 3], 10, -1894986606), o = h(o, d, n, i, t[p + 10], 15, -1051523), i = h(i, o, d, n, t[p + 1], 21, -2054922799), n = h(n, i, o, d, t[p + 8], 6, 1873313359), d = h(d, n, i, o, t[p + 15], 10, -30611744), o = h(o, d, n, i, t[p + 6], 15, -1560198380), i = h(i, o, d, n, t[p + 13], 21, 1309151649), n = h(n, i, o, d, t[p + 4], 6, -145523070), d = h(d, n, i, o, t[p + 11], 10, -1120210379), o = h(o, d, n, i, t[p + 2], 15, 718787259), i = h(i, o, d, n, t[p + 9], 21, -343485551), n = c(n, u), i = c(i, l), o = c(o, f), d = c(d, m)
		}
		return Array(n, i, o, d)
	}

	function o(t, e, n, i, o, a) {
		return c(d(c(c(e, t), c(i, a)), o), n)
	}

	function a(t, e, n, i, a, s, r) {
		return o(e & n | ~e & i, t, e, a, s, r)
	}

	function s(t, e, n, i, a, s, r) {
		return o(e & i | n & ~i, t, e, a, s, r)
	}

	function r(t, e, n, i, a, s, r) {
		return o(e ^ n ^ i, t, e, a, s, r)
	}

	function h(t, e, n, i, a, s, r) {
		return o(n ^ (e | ~i), t, e, a, s, r)
	}

	function c(t, e) {
		var n = (65535 & t) + (65535 & e),
			i = (t >> 16) + (e >> 16) + (n >> 16);
		return i << 16 | 65535 & n
	}

	function d(t, e) {
		return t << e | t >>> 32 - e
	}

	function p(t) {
		for(var e = Array(), n = (1 << f) - 1, i = 0; i < t.length * f; i += f) e[i >> 5] |= (t.charCodeAt(i / f) & n) << i % 32;
		return e
	}

	function u(t) {
		for(var e = l ? "0123456789ABCDEF" : "0123456789abcdef", n = "", i = 0; i < 4 * t.length; i++) n += e.charAt(t[i >> 2] >> i % 4 * 8 + 4 & 15) + e.charAt(t[i >> 2] >> i % 4 * 8 & 15);
		return n
	}
	var l = 0,
		f = 8;
	t.exports = {
		hex_md5: n
	}
}, , function(t, e) {
	! function() {
		function t(e, n) {
			this.point = {
				x: 0,
				y: 0
			}, this.point.x = e.x, this.point.y = e.y, this.len = n || 1, this.lWidth = 1, this.speed = f.iSpeed, this.opc = .5, this.isEnd = !1, "function" != typeof this.nextFps && (t.prototype.nextFps = function() {
				var t = this.point.y / Math.abs(this.point.y) * this.speed;
				this.point.x = this.point.x / this.point.y * (this.point.y + t), this.point.y += t, this.speed += f.dSpeed, this.len += .7, Math.abs(this.point.y) > h / 2 * .7 ? this.lWidth = 5 : Math.abs(this.point.y) > h / 2 * .5 ? this.lWidth = 4 : Math.abs(this.point.y) > h / 2 * .3 ? this.lWidth = 3 : Math.abs(this.point.y) > h / 2 * .2 && (this.lWidth = 2), this.opc = this.opc < .1 ? .1 : this.opc - .02, (Math.abs(this.point.x) > r / 2 || Math.abs(this.point.y) > h / 2) && (this.isEnd = !0)
			}), "function" != typeof this.draw && (t.prototype.draw = function(t) {
				t.save(), t.globalAlpha = this.opc, t.translate(r / 2, h / 2), t.lineWidth = this.lWidth, t.strokeStyle = "#fff", t.lineCap = "round";
				var e = f.newPoint(this.point, this.len);
				t.beginPath(), t.moveTo(this.point.x, this.point.y), t.lineTo(e.x, e.y), t.stroke(), t.restore()
			})
		}

		function e(t) {
			this.point = t || {
				x: 0,
				y: 0
			}, this.rad = Math.random(), Math.random() > .9 && (this.rad = 2), this.speed = 1 * Math.random() + .1, this.opc = Math.random() / 2 + .3, this.R = Math.sqrt(this.point.x * this.point.x + this.point.y * this.point.y), "function" != typeof this.nextFps && (e.prototype.nextFps = function() {
				if(1 == v.way) {
					if(this.point.x = (Math.abs(this.point.x) + this.speed * (Math.abs(this.point.x) / (Math.abs(this.point.x) + Math.abs(this.point.y)))) * (this.point.x / Math.abs(this.point.x)), this.point.y = (Math.abs(this.point.y) + this.speed * (Math.abs(this.point.y) / (Math.abs(this.point.x) + Math.abs(this.point.y)))) * (this.point.y / Math.abs(this.point.y)), this.point.x < r * -.5 || this.point.x > .5 * r || this.point.y < h * -.5 || this.point.y > .5 * h) {
						var t = Math.min(Math.abs(this.point.x), Math.abs(this.point.y)) / 40;
						this.point.x /= t, this.point.y /= t
					}
				} else if(0 == v.way) {
					var e = (this.R - Math.abs(this.point.x)) / this.R * (this.speed + 1 + 3 * Math.random());
					e = e <= .05 ? .05 : e, e = e >= 1 ? 1 : e, this.point.y > 0 ? this.point.x + e <= this.R ? (this.point.x += e, this.point.y = Math.sqrt(this.R * this.R - this.point.x * this.point.x)) : (this.point.x = this.R, this.point.y = 0) : this.point.x - e >= this.R * -1 ? (this.point.x -= e, this.point.y = Math.sqrt(this.R * this.R - this.point.x * this.point.x) * -1) : this.point.y *= -1
				}
			}), "function" != typeof this.draw && (e.prototype.draw = function(t) {
				t.save(), t.globalAlpha = this.opc, t.translate(r / 2, h / 2), t.fillStyle = "#fff", t.beginPath(), t.arc(this.point.x, this.point.y, this.rad, 0, 2 * Math.PI), t.fill(), t.restore()
			})
		}

		function n(t) {
			this.point = t || {
				x: 0,
				y: 0
			}, this.rad = Math.random(), Math.random() > .9 && (this.rad = 5), this.speed = 2, this.opc = Math.random() / 2 + .3, this.isEnd = !1, "function" != typeof this.nextFps && (n.prototype.nextFps = function() {
				this.speed += 2, this.rad += .3, this.point.x = (Math.abs(this.point.x) + this.speed * (Math.abs(this.point.x) / (Math.abs(this.point.x) + Math.abs(this.point.y)))) * (this.point.x / Math.abs(this.point.x)), this.point.y = (Math.abs(this.point.y) + this.speed * (Math.abs(this.point.y) / (Math.abs(this.point.x) + Math.abs(this.point.y)))) * (this.point.y / Math.abs(this.point.y)), (this.point.x < r * -.5 || this.point.x > .5 * r || this.point.y < h * -.5 || this.point.y > .5 * h) && (this.isEnd = !0)
			}), "function" != typeof this.draw && (n.prototype.draw = function(t) {
				t.save(), t.globalAlpha = this.opc, t.translate(r / 2, h / 2), t.fillStyle = "#fff", t.beginPath(), t.arc(this.point.x, this.point.y, this.rad, 0, 2 * Math.PI), t.fill(), t.restore()
			})
		}

		function i() {
			this.step = .05, this.isEnd = !1, this.opc = 1, "function" != typeof this.nextFps && (i.prototype.nextFps = function() {
				this.step < .5 ? this.step += .01 : this.step < 1 && (this.step += .006, this.opc > .01 && (this.opc -= .01)), this.step >= 1 && (this.isEnd = !0)
			}), "function" != typeof this.draw && (i.prototype.draw = function(t) {
				t.save();
				var e = t.createRadialGradient(r / 2, h / 2, 0, r / 2, h / 2, Math.max(r, h));
				e.addColorStop(0, "rgba(11,45,106,0)"), e.addColorStop(this.step, "rgba(11,45,106,.4)"), e.addColorStop(1, "rgba(11,45,106,.1)"), t.globalAlpha = this.opc, t.fillStyle = e, t.rect(0, 0, r, h), t.fill(), t.restore()
			})
		}
		window.requestAnimFrame = function() {
			return function(t) {
				window.setTimeout(t, 40)
			}
		}();
		var o = function() {
			try {
				return document.createElement("canvas").getContext("2d"), !0
			} catch(t) {
				return !1
			}
		}();
		if(o) {
			var a = document.getElementById("canvas"),
				s = a.getContext("2d"),
				r = document.body.offsetWidth,
				h = document.body.offsetHeight;
			a.setAttribute("width", r), a.setAttribute("height", h);
			var c = document.createElement("canvas"),
				d = c.getContext("2d");
			c.width = r, c.height = h;
			var p, u = {
					createSign: function() {
						return Math.random() > .5 ? -1 : 1
					},
					createPoints: function(t, e, n, i, o) {
						for(var a = [], s = 0; s < o; s++) a.push({
							x: t + Math.random() * (n - t),
							y: e + Math.random() * (i - e)
						});
						return a
					}
				},
				l = [],
				f = {
					avgMax: 2,
					iSpeed: 1.5,
					dSpeed: .8,
					newPoint: function(t, e) {
						var n = t.x * t.x + t.y * t.y + e * e + 2 * e * Math.sqrt(t.x * t.x + t.y * t.y);
						return {
							x: Math.sqrt(n / (1 + t.y * t.y / (t.x * t.x))) * (t.x / Math.abs(t.x)),
							y: Math.sqrt(n / (1 + t.x * t.x / (t.y * t.y))) * (t.y / Math.abs(t.y))
						}
					},
					createRay: function(e) {
						for(var n, i = {
								x: 0,
								y: 0
							}, o = 15, a = 0; a < e; a++) i.x = Math.random() * o * u.createSign(), i.y = Math.sqrt(o * o - i.x * i.x) * u.createSign(), n = Math.ceil(3 * Math.random()), l.push(new t(i, n))
					}
				},
				m = [],
				b = [],
				v = {
					way: 0,
					createParticle: function() {
						for(var t = [], n = 0; n < 10; n++)
							for(var i = 0; i < 5; i++) {
								t = u.createPoints(-.5 * r + .1 * n * r, -.5 * h + .2 * i * h, -.5 * r + .1 * (n + 1) * r, -.5 * h + .2 * (i + 1) * h, 6);
								for(var o = 0; o < t.length; o++) 0 != t[o].x && 0 != t[o].y && m.push(new e(t[o]))
							}
					},
					createParticle2: function() {
						for(var t = [], e = 0; e < 5; e++)
							for(var i = 0; i < 2; i++) {
								t = u.createPoints(-.5 * r + .2 * e * r, -.5 * h + .5 * i * h, -.5 * r + .2 * (e + 1) * r, -.5 * h + .5 * (i + 1) * h, 1);
								for(var o = 0; o < t.length; o++) 0 != t[o].x && 0 != t[o].y && b.push(new n(t[o]))
							}
					}
				},
				w = new i,
				g = {
					drawParticle: function() {
						for(var t = 0; t < m.length; t++) m[t].nextFps(), m[t].draw(d)
					},
					drawParticle2: function() {
						for(var t = 0; t < b.length; t++) b[t].nextFps(), b[t].isEnd ? (b.splice(t, 1), t--) : b[t].draw(d)
					},
					drawRay: function() {
						for(var t = 0; t < l.length; t++) l[t].nextFps(), l[t].isEnd ? (l.splice(t, 1), t--) : l[t].draw(d)
					}
				},
				_ = function() {
					switch(d.clearRect(0, 0, r, h), p) {
						case 1:
							g.drawParticle();
							break;
						case 2:
							g.drawParticle(), g.drawParticle2(), f.createRay(Math.ceil(Math.random() * f.avgMax)), g.drawRay(), w.nextFps(), w.isEnd || w.draw(d);
							break;
						case 3:
							g.drawParticle(), g.drawRay();
							break;
						case 4:
							g.drawParticle()
					}
					s.clearRect(0, 0, r, h), s.drawImage(c, 0, 0), requestAnimFrame(_)
				};
			window.onresize = function() {
				r = document.body.offsetWidth, h = document.body.offsetHeight, a.setAttribute("width", r), a.setAttribute("height", h), c.width = r, c.height = h, m = [], v.createParticle()
			}, window.cvsEffect = function(t, e) {}, cvsEffect.start = function() {
				requestAnimFrame(_)
			}, cvsEffect.one = function() {
				p = 1, v.way = 0, m = [], v.createParticle()
			}, cvsEffect.two = function() {
				p = 2, v.way = 1, m = [], v.createParticle(), b = [], v.createParticle2(), w.isEnd = !0, setTimeout(function() {
					w.step = .05, w.opc = 1, w.isEnd = !1
				}, 50)
			}, cvsEffect.three = function() {
				p = 3, 0 == m.length && v.createParticle(), v.way = 1
			}, cvsEffect.four = function() {
				0 == m.length && v.createParticle(), v.way = 1, p = 4
			}
		}
	}()
}]);
