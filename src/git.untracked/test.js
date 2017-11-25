if (function (t, e) {
        "use strict";
        "object" == typeof module && "object" == typeof module.exports ? module.exports = t.document ? e(t, !0) : function (t) {
            if (!t.document)throw new Error("jQuery requires a window with a document");
            return e(t)
        } : e(t)
    }("undefined" != typeof window ? window : this, function (t, e) {
        "use strict";
        function n(t, e) {
            var n = (e = e || et).createElement("script");
            n.text = t, e.head.appendChild(n).parentNode.removeChild(n)
        }

        function i(t) {
            var e = !!t && "length" in t && t.length, n = dt.type(t);
            return "function" !== n && !dt.isWindow(t) && ("array" === n || 0 === e || "number" == typeof e && e > 0 && e - 1 in t)
        }

        function o(t, e) {
            return t.nodeName && t.nodeName.toLowerCase() === e.toLowerCase()
        }

        function r(t, e, n) {
            return dt.isFunction(e) ? dt.grep(t, function (t, i) {
                return !!e.call(t, i, t) !== n
            }) : e.nodeType ? dt.grep(t, function (t) {
                return t === e !== n
            }) : "string" != typeof e ? dt.grep(t, function (t) {
                return st.call(e, t) > -1 !== n
            }) : Ct.test(e) ? dt.filter(e, t, n) : (e = dt.filter(e, t), dt.grep(t, function (t) {
                return st.call(e, t) > -1 !== n && 1 === t.nodeType
            }))
        }

        function s(t, e) {
            for (; (t = t[e]) && 1 !== t.nodeType;);
            return t
        }

        function a(t) {
            var e = {};
            return dt.each(t.match(Dt) || [], function (t, n) {
                e[n] = !0
            }), e
        }

        function l(t) {
            return t
        }

        function u(t) {
            throw t
        }

        function c(t, e, n, i) {
            var o;
            try {
                t && dt.isFunction(o = t.promise) ? o.call(t).done(e).fail(n) : t && dt.isFunction(o = t.then) ? o.call(t, e, n) : e.apply(void 0, [t].slice(i))
            } catch (t) {
                n.apply(void 0, [t])
            }
        }

        function f() {
            et.removeEventListener("DOMContentLoaded", f), t.removeEventListener("load", f), dt.ready()
        }

        function p() {
            this.expando = dt.expando + p.uid++
        }

        function d(t) {
            return "true" === t || "false" !== t && ("null" === t ? null : t === +t + "" ? +t : Rt.test(t) ? JSON.parse(t) : t)
        }

        function h(t, e, n) {
            var i;
            if (void 0 === n && 1 === t.nodeType)if (i = "data-" + e.replace(qt, "-$&").toLowerCase(), "string" == typeof(n = t.getAttribute(i))) {
                try {
                    n = d(n)
                } catch (t) {
                }
                Lt.set(t, e, n)
            } else n = void 0;
            return n
        }

        function g(t, e, n, i) {
            var o, r = 1, s = 20, a = i ? function () {
                return i.cur()
            } : function () {
                return dt.css(t, e, "")
            }, l = a(), u = n && n[3] || (dt.cssNumber[e] ? "" : "px"), c = (dt.cssNumber[e] || "px" !== u && +l) && Ht.exec(dt.css(t, e));
            if (c && c[3] !== u) {
                u = u || c[3], n = n || [], c = +l || 1;
                do {
                    c /= r = r || ".5", dt.style(t, e, c + u)
                } while (r !== (r = a() / l) && 1 !== r && --s)
            }
            return n && (c = +c || +l || 0, o = n[1] ? c + (n[1] + 1) * n[2] : +n[2], i && (i.unit = u, i.start = c, i.end = o)), o
        }

        function m(t) {
            var e, n = t.ownerDocument, i = t.nodeName, o = Bt[i];
            return o || (e = n.body.appendChild(n.createElement(i)), o = dt.css(e, "display"), e.parentNode.removeChild(e), "none" === o && (o = "block"), Bt[i] = o, o)
        }

        function v(t, e) {
            for (var n, i, o = [], r = 0, s = t.length; r < s; r++)(i = t[r]).style && (n = i.style.display, e ? ("none" === n && (o[r] = It.get(i, "display") || null, o[r] || (i.style.display = "")), "" === i.style.display && Wt(i) && (o[r] = m(i))) : "none" !== n && (o[r] = "none", It.set(i, "display", n)));
            for (r = 0; r < s; r++)null != o[r] && (t[r].style.display = o[r]);
            return t
        }

        function y(t, e) {
            var n;
            return n = void 0 !== t.getElementsByTagName ? t.getElementsByTagName(e || "*") : void 0 !== t.querySelectorAll ? t.querySelectorAll(e || "*") : [], void 0 === e || e && o(t, e) ? dt.merge([t], n) : n
        }

        function b(t, e) {
            for (var n = 0, i = t.length; n < i; n++)It.set(t[n], "globalEval", !e || It.get(e[n], "globalEval"))
        }

        function x(t, e, n, i, o) {
            for (var r, s, a, l, u, c, f = e.createDocumentFragment(), p = [], d = 0, h = t.length; d < h; d++)if ((r = t[d]) || 0 === r)if ("object" === dt.type(r))dt.merge(p, r.nodeType ? [r] : r); else if (Qt.test(r)) {
                for (s = s || f.appendChild(e.createElement("div")), a = (_t.exec(r) || ["", ""])[1].toLowerCase(), l = Vt[a] || Vt._default, s.innerHTML = l[1] + dt.htmlPrefilter(r) + l[2], c = l[0]; c--;)s = s.lastChild;
                dt.merge(p, s.childNodes), (s = f.firstChild).textContent = ""
            } else p.push(e.createTextNode(r));
            for (f.textContent = "", d = 0; r = p[d++];)if (i && dt.inArray(r, i) > -1)o && o.push(r); else if (u = dt.contains(r.ownerDocument, r), s = y(f.appendChild(r), "script"), u && b(s), n)for (c = 0; r = s[c++];)zt.test(r.type || "") && n.push(r);
            return f
        }

        function w() {
            return !0
        }

        function T() {
            return !1
        }

        function C() {
            try {
                return et.activeElement
            } catch (t) {
            }
        }

        function E(t, e, n, i, o, r) {
            var s, a;
            if ("object" == typeof e) {
                "string" != typeof n && (i = i || n, n = void 0);
                for (a in e)E(t, a, n, i, e[a], r);
                return t
            }
            if (null == i && null == o ? (o = n, i = n = void 0) : null == o && ("string" == typeof n ? (o = i, i = void 0) : (o = i, i = n, n = void 0)), !1 === o)o = T; else if (!o)return t;
            return 1 === r && (s = o, (o = function (t) {
                return dt().off(t), s.apply(this, arguments)
            }).guid = s.guid || (s.guid = dt.guid++)), t.each(function () {
                dt.event.add(this, e, o, i, n)
            })
        }

        function S(t, e) {
            return o(t, "table") && o(11 !== e.nodeType ? e : e.firstChild, "tr") ? dt(">tbody", t)[0] || t : t
        }

        function $(t) {
            return t.type = (null !== t.getAttribute("type")) + "/" + t.type, t
        }

        function k(t) {
            var e = ee.exec(t.type);
            return e ? t.type = e[1] : t.removeAttribute("type"), t
        }

        function D(t, e) {
            var n, i, o, r, s, a, l, u;
            if (1 === e.nodeType) {
                if (It.hasData(t) && (r = It.access(t), s = It.set(e, r), u = r.events)) {
                    delete s.handle, s.events = {};
                    for (o in u)for (n = 0, i = u[o].length; n < i; n++)dt.event.add(e, o, u[o][n])
                }
                Lt.hasData(t) && (a = Lt.access(t), l = dt.extend({}, a), Lt.set(e, l))
            }
        }

        function N(t, e) {
            var n = e.nodeName.toLowerCase();
            "input" === n && Ut.test(t.type) ? e.checked = t.checked : "input" !== n && "textarea" !== n || (e.defaultValue = t.defaultValue)
        }

        function A(t, e, i, o) {
            e = ot.apply([], e);
            var r, s, a, l, u, c, f = 0, p = t.length, d = p - 1, h = e[0], g = dt.isFunction(h);
            if (g || p > 1 && "string" == typeof h && !pt.checkClone && te.test(h))return t.each(function (n) {
                var r = t.eq(n);
                g && (e[0] = h.call(this, n, r.html())), A(r, e, i, o)
            });
            if (p && (r = x(e, t[0].ownerDocument, !1, t, o), s = r.firstChild, 1 === r.childNodes.length && (r = s), s || o)) {
                for (l = (a = dt.map(y(r, "script"), $)).length; f < p; f++)u = r, f !== d && (u = dt.clone(u, !0, !0), l && dt.merge(a, y(u, "script"))), i.call(t[f], u, f);
                if (l)for (c = a[a.length - 1].ownerDocument, dt.map(a, k), f = 0; f < l; f++)u = a[f], zt.test(u.type || "") && !It.access(u, "globalEval") && dt.contains(c, u) && (u.src ? dt._evalUrl && dt._evalUrl(u.src) : n(u.textContent.replace(ne, ""), c))
            }
            return t
        }

        function j(t, e, n) {
            for (var i, o = e ? dt.filter(e, t) : t, r = 0; null != (i = o[r]); r++)n || 1 !== i.nodeType || dt.cleanData(y(i)), i.parentNode && (n && dt.contains(i.ownerDocument, i) && b(y(i, "script")), i.parentNode.removeChild(i));
            return t
        }

        function O(t, e, n) {
            var i, o, r, s, a = t.style;
            return (n = n || re(t)) && ("" !== (s = n.getPropertyValue(e) || n[e]) || dt.contains(t.ownerDocument, t) || (s = dt.style(t, e)), !pt.pixelMarginRight() && oe.test(s) && ie.test(e) && (i = a.width, o = a.minWidth, r = a.maxWidth, a.minWidth = a.maxWidth = a.width = s, s = n.width, a.width = i, a.minWidth = o, a.maxWidth = r)), void 0 !== s ? s + "" : s
        }

        function I(t, e) {
            return {
                get: function () {
                    if (!t())return (this.get = e).apply(this, arguments);
                    delete this.get
                }
            }
        }

        function L(t) {
            if (t in fe)return t;
            for (var e = t[0].toUpperCase() + t.slice(1), n = ce.length; n--;)if ((t = ce[n] + e) in fe)return t
        }

        function R(t) {
            var e = dt.cssProps[t];
            return e || (e = dt.cssProps[t] = L(t) || t), e
        }

        function q(t, e, n) {
            var i = Ht.exec(e);
            return i ? Math.max(0, i[2] - (n || 0)) + (i[3] || "px") : e
        }

        function P(t, e, n, i, o) {
            var r, s = 0;
            for (r = n === (i ? "border" : "content") ? 4 : "width" === e ? 1 : 0; r < 4; r += 2)"margin" === n && (s += dt.css(t, n + Ft[r], !0, o)), i ? ("content" === n && (s -= dt.css(t, "padding" + Ft[r], !0, o)), "margin" !== n && (s -= dt.css(t, "border" + Ft[r] + "Width", !0, o))) : (s += dt.css(t, "padding" + Ft[r], !0, o), "padding" !== n && (s += dt.css(t, "border" + Ft[r] + "Width", !0, o)));
            return s
        }

        function H(t, e, n) {
            var i, o = re(t), r = O(t, e, o), s = "border-box" === dt.css(t, "boxSizing", !1, o);
            return oe.test(r) ? r : (i = s && (pt.boxSizingReliable() || r === t.style[e]), "auto" === r && (r = t["offset" + e[0].toUpperCase() + e.slice(1)]), (r = parseFloat(r) || 0) + P(t, e, n || (s ? "border" : "content"), i, o) + "px")
        }

        function F(t, e, n, i, o) {
            return new F.prototype.init(t, e, n, i, o)
        }

        function W() {
            de && (!1 === et.hidden && t.requestAnimationFrame ? t.requestAnimationFrame(W) : t.setTimeout(W, dt.fx.interval), dt.fx.tick())
        }

        function M() {
            return t.setTimeout(function () {
                pe = void 0
            }), pe = dt.now()
        }

        function B(t, e) {
            var n, i = 0, o = {height: t};
            for (e = e ? 1 : 0; i < 4; i += 2 - e)o["margin" + (n = Ft[i])] = o["padding" + n] = t;
            return e && (o.opacity = o.width = t), o
        }

        function U(t, e, n) {
            for (var i, o = (z.tweeners[e] || []).concat(z.tweeners["*"]), r = 0, s = o.length; r < s; r++)if (i = o[r].call(n, e, t))return i
        }

        function _(t, e) {
            var n, i, o, r, s;
            for (n in t)if (i = dt.camelCase(n), o = e[i], r = t[n], Array.isArray(r) && (o = r[1], r = t[n] = r[0]), n !== i && (t[i] = r, delete t[n]), (s = dt.cssHooks[i]) && "expand" in s) {
                r = s.expand(r), delete t[i];
                for (n in r)n in t || (t[n] = r[n], e[n] = o)
            } else e[i] = o
        }

        function z(t, e, n) {
            var i, o, r = 0, s = z.prefilters.length, a = dt.Deferred().always(function () {
                delete l.elem
            }), l = function () {
                if (o)return !1;
                for (var e = pe || M(), n = Math.max(0, u.startTime + u.duration - e), i = 1 - (n / u.duration || 0), r = 0, s = u.tweens.length; r < s; r++)u.tweens[r].run(i);
                return a.notifyWith(t, [u, i, n]), i < 1 && s ? n : (s || a.notifyWith(t, [u, 1, 0]), a.resolveWith(t, [u]), !1)
            }, u = a.promise({
                elem: t, props: dt.extend({}, e), opts: dt.extend(!0, {specialEasing: {}, easing: dt.easing._default}, n), originalProperties: e, originalOptions: n, startTime: pe || M(), duration: n.duration, tweens: [], createTween: function (e, n) {
                    var i = dt.Tween(t, u.opts, e, n, u.opts.specialEasing[e] || u.opts.easing);
                    return u.tweens.push(i), i
                }, stop: function (e) {
                    var n = 0, i = e ? u.tweens.length : 0;
                    if (o)return this;
                    for (o = !0; n < i; n++)u.tweens[n].run(1);
                    return e ? (a.notifyWith(t, [u, 1, 0]), a.resolveWith(t, [u, e])) : a.rejectWith(t, [u, e]), this
                }
            }), c = u.props;
            for (_(c, u.opts.specialEasing); r < s; r++)if (i = z.prefilters[r].call(u, t, c, u.opts))return dt.isFunction(i.stop) && (dt._queueHooks(u.elem, u.opts.queue).stop = dt.proxy(i.stop, i)), i;
            return dt.map(c, U, u), dt.isFunction(u.opts.start) && u.opts.start.call(t, u), u.progress(u.opts.progress).done(u.opts.done, u.opts.complete).fail(u.opts.fail).always(u.opts.always), dt.fx.timer(dt.extend(l, {elem: t, anim: u, queue: u.opts.queue})), u
        }

        function V(t) {
            return (t.match(Dt) || []).join(" ")
        }

        function Q(t) {
            return t.getAttribute && t.getAttribute("class") || ""
        }

        function X(t, e, n, i) {
            var o;
            if (Array.isArray(e))dt.each(e, function (e, o) {
                n || Se.test(t) ? i(t, o) : X(t + "[" + ("object" == typeof o && null != o ? e : "") + "]", o, n, i)
            }); else if (n || "object" !== dt.type(e))i(t, e); else for (o in e)X(t + "[" + o + "]", e[o], n, i)
        }

        function G(t) {
            return function (e, n) {
                "string" != typeof e && (n = e, e = "*");
                var i, o = 0, r = e.toLowerCase().match(Dt) || [];
                if (dt.isFunction(n))for (; i = r[o++];)"+" === i[0] ? (i = i.slice(1) || "*", (t[i] = t[i] || []).unshift(n)) : (t[i] = t[i] || []).push(n)
            }
        }

        function Y(t, e, n, i) {
            function o(a) {
                var l;
                return r[a] = !0, dt.each(t[a] || [], function (t, a) {
                    var u = a(e, n, i);
                    return "string" != typeof u || s || r[u] ? s ? !(l = u) : void 0 : (e.dataTypes.unshift(u), o(u), !1)
                }), l
            }

            var r = {}, s = t === qe;
            return o(e.dataTypes[0]) || !r["*"] && o("*")
        }

        function J(t, e) {
            var n, i, o = dt.ajaxSettings.flatOptions || {};
            for (n in e)void 0 !== e[n] && ((o[n] ? t : i || (i = {}))[n] = e[n]);
            return i && dt.extend(!0, t, i), t
        }

        function K(t, e, n) {
            for (var i, o, r, s, a = t.contents, l = t.dataTypes; "*" === l[0];)l.shift(), void 0 === i && (i = t.mimeType || e.getResponseHeader("Content-Type"));
            if (i)for (o in a)if (a[o] && a[o].test(i)) {
                l.unshift(o);
                break
            }
            if (l[0] in n)r = l[0]; else {
                for (o in n) {
                    if (!l[0] || t.converters[o + " " + l[0]]) {
                        r = o;
                        break
                    }
                    s || (s = o)
                }
                r = r || s
            }
            if (r)return r !== l[0] && l.unshift(r), n[r]
        }

        function Z(t, e, n, i) {
            var o, r, s, a, l, u = {}, c = t.dataTypes.slice();
            if (c[1])for (s in t.converters)u[s.toLowerCase()] = t.converters[s];
            for (r = c.shift(); r;)if (t.responseFields[r] && (n[t.responseFields[r]] = e), !l && i && t.dataFilter && (e = t.dataFilter(e, t.dataType)), l = r, r = c.shift())if ("*" === r)r = l; else if ("*" !== l && l !== r) {
                if (!(s = u[l + " " + r] || u["* " + r]))for (o in u)if ((a = o.split(" "))[1] === r && (s = u[l + " " + a[0]] || u["* " + a[0]])) {
                    !0 === s ? s = u[o] : !0 !== u[o] && (r = a[0], c.unshift(a[1]));
                    break
                }
                if (!0 !== s)if (s && t.throws)e = s(e); else try {
                    e = s(e)
                } catch (t) {
                    return {state: "parsererror", error: s ? t : "No conversion from " + l + " to " + r}
                }
            }
            return {state: "success", data: e}
        }

        var tt = [], et = t.document, nt = Object.getPrototypeOf, it = tt.slice, ot = tt.concat, rt = tt.push, st = tt.indexOf, at = {}, lt = at.toString, ut = at.hasOwnProperty, ct = ut.toString, ft = ct.call(Object), pt = {}, dt = function (t, e) {
            return new dt.fn.init(t, e)
        }, ht = /^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g, gt = /^-ms-/, mt = /-([a-z])/g, vt = function (t, e) {
            return e.toUpperCase()
        };
        dt.fn = dt.prototype = {
            jquery: "3.2.1", constructor: dt, length: 0, toArray: function () {
                return it.call(this)
            }, get: function (t) {
                return null == t ? it.call(this) : t < 0 ? this[t + this.length] : this[t]
            }, pushStack: function (t) {
                var e = dt.merge(this.constructor(), t);
                return e.prevObject = this, e
            }, each: function (t) {
                return dt.each(this, t)
            }, map: function (t) {
                return this.pushStack(dt.map(this, function (e, n) {
                    return t.call(e, n, e)
                }))
            }, slice: function () {
                return this.pushStack(it.apply(this, arguments))
            }, first: function () {
                return this.eq(0)
            }, last: function () {
                return this.eq(-1)
            }, eq: function (t) {
                var e = this.length, n = +t + (t < 0 ? e : 0);
                return this.pushStack(n >= 0 && n < e ? [this[n]] : [])
            }, end: function () {
                return this.prevObject || this.constructor()
            }, push: rt, sort: tt.sort, splice: tt.splice
        }, dt.extend = dt.fn.extend = function () {
            var t, e, n, i, o, r, s = arguments[0] || {}, a = 1, l = arguments.length, u = !1;
            for ("boolean" == typeof s && (u = s, s = arguments[a] || {}, a++), "object" == typeof s || dt.isFunction(s) || (s = {}), a === l && (s = this, a--); a < l; a++)if (null != (t = arguments[a]))for (e in t)n = s[e], s !== (i = t[e]) && (u && i && (dt.isPlainObject(i) || (o = Array.isArray(i))) ? (o ? (o = !1, r = n && Array.isArray(n) ? n : []) : r = n && dt.isPlainObject(n) ? n : {}, s[e] = dt.extend(u, r, i)) : void 0 !== i && (s[e] = i));
            return s
        }, dt.extend({
            expando: "jQuery" + ("3.2.1" + Math.random()).replace(/\D/g, ""), isReady: !0, error: function (t) {
                throw new Error(t)
            }, noop: function () {
            }, isFunction: function (t) {
                return "function" === dt.type(t)
            }, isWindow: function (t) {
                return null != t && t === t.window
            }, isNumeric: function (t) {
                var e = dt.type(t);
                return ("number" === e || "string" === e) && !isNaN(t - parseFloat(t))
            }, isPlainObject: function (t) {
                var e, n;
                return !(!t || "[object Object]" !== lt.call(t)) && (!(e = nt(t)) || "function" == typeof(n = ut.call(e, "constructor") && e.constructor) && ct.call(n) === ft)
            }, isEmptyObject: function (t) {
                var e;
                for (e in t)return !1;
                return !0
            }, type: function (t) {
                return null == t ? t + "" : "object" == typeof t || "function" == typeof t ? at[lt.call(t)] || "object" : typeof t
            }, globalEval: function (t) {
                n(t)
            }, camelCase: function (t) {
                return t.replace(gt, "ms-").replace(mt, vt)
            }, each: function (t, e) {
                var n, o = 0;
                if (i(t))for (n = t.length; o < n && !1 !== e.call(t[o], o, t[o]); o++); else for (o in t)if (!1 === e.call(t[o], o, t[o]))break;
                return t
            }, trim: function (t) {
                return null == t ? "" : (t + "").replace(ht, "")
            }, makeArray: function (t, e) {
                var n = e || [];
                return null != t && (i(Object(t)) ? dt.merge(n, "string" == typeof t ? [t] : t) : rt.call(n, t)), n
            }, inArray: function (t, e, n) {
                return null == e ? -1 : st.call(e, t, n)
            }, merge: function (t, e) {
                for (var n = +e.length, i = 0, o = t.length; i < n; i++)t[o++] = e[i];
                return t.length = o, t
            }, grep: function (t, e, n) {
                for (var i = [], o = 0, r = t.length, s = !n; o < r; o++)!e(t[o], o) !== s && i.push(t[o]);
                return i
            }, map: function (t, e, n) {
                var o, r, s = 0, a = [];
                if (i(t))for (o = t.length; s < o; s++)null != (r = e(t[s], s, n)) && a.push(r); else for (s in t)null != (r = e(t[s], s, n)) && a.push(r);
                return ot.apply([], a)
            }, guid: 1, proxy: function (t, e) {
                var n, i, o;
                if ("string" == typeof e && (n = t[e], e = t, t = n), dt.isFunction(t))return i = it.call(arguments, 2), o = function () {
                    return t.apply(e || this, i.concat(it.call(arguments)))
                }, o.guid = t.guid = t.guid || dt.guid++, o
            }, now: Date.now, support: pt
        }), "function" == typeof Symbol && (dt.fn[Symbol.iterator] = tt[Symbol.iterator]), dt.each("Boolean Number String Function Array Date RegExp Object Error Symbol".split(" "), function (t, e) {
            at["[object " + e + "]"] = e.toLowerCase()
        });
        var yt = function (t) {
            function e(t, e, n, i) {
                var o, r, s, a, l, c, p, d = e && e.ownerDocument, h = e ? e.nodeType : 9;
                if (n = n || [], "string" != typeof t || !t || 1 !== h && 9 !== h && 11 !== h)return n;
                if (!i && ((e ? e.ownerDocument || e : F) !== j && A(e), e = e || j, I)) {
                    if (11 !== h && (l = gt.exec(t)))if (o = l[1]) {
                        if (9 === h) {
                            if (!(s = e.getElementById(o)))return n;
                            if (s.id === o)return n.push(s), n
                        } else if (d && (s = d.getElementById(o)) && P(e, s) && s.id === o)return n.push(s), n
                    } else {
                        if (l[2])return Y.apply(n, e.getElementsByTagName(t)), n;
                        if ((o = l[3]) && x.getElementsByClassName && e.getElementsByClassName)return Y.apply(n, e.getElementsByClassName(o)), n
                    }
                    if (x.qsa && !_[t + " "] && (!L || !L.test(t))) {
                        if (1 !== h)d = e, p = t; else if ("object" !== e.nodeName.toLowerCase()) {
                            for ((a = e.getAttribute("id")) ? a = a.replace(bt, xt) : e.setAttribute("id", a = H), r = (c = E(t)).length; r--;)c[r] = "#" + a + " " + f(c[r]);
                            p = c.join(","), d = mt.test(t) && u(e.parentNode) || e
                        }
                        if (p)try {
                            return Y.apply(n, d.querySelectorAll(p)), n
                        } catch (t) {
                        } finally {
                            a === H && e.removeAttribute("id")
                        }
                    }
                }
                return $(t.replace(rt, "$1"), e, n, i)
            }

            function n() {
                function t(n, i) {
                    return e.push(n + " ") > w.cacheLength && delete t[e.shift()], t[n + " "] = i
                }

                var e = [];
                return t
            }

            function i(t) {
                return t[H] = !0, t
            }

            function o(t) {
                var e = j.createElement("fieldset");
                try {
                    return !!t(e)
                } catch (t) {
                    return !1
                } finally {
                    e.parentNode && e.parentNode.removeChild(e), e = null
                }
            }

            function r(t, e) {
                for (var n = t.split("|"), i = n.length; i--;)w.attrHandle[n[i]] = e
            }

            function s(t, e) {
                var n = e && t, i = n && 1 === t.nodeType && 1 === e.nodeType && t.sourceIndex - e.sourceIndex;
                if (i)return i;
                if (n)for (; n = n.nextSibling;)if (n === e)return -1;
                return t ? 1 : -1
            }

            function a(t) {
                return function (e) {
                    return "form" in e ? e.parentNode && !1 === e.disabled ? "label" in e ? "label" in e.parentNode ? e.parentNode.disabled === t : e.disabled === t : e.isDisabled === t || e.isDisabled !== !t && Tt(e) === t : e.disabled === t : "label" in e && e.disabled === t
                }
            }

            function l(t) {
                return i(function (e) {
                    return e = +e, i(function (n, i) {
                        for (var o, r = t([], n.length, e), s = r.length; s--;)n[o = r[s]] && (n[o] = !(i[o] = n[o]))
                    })
                })
            }

            function u(t) {
                return t && void 0 !== t.getElementsByTagName && t
            }

            function c() {
            }

            function f(t) {
                for (var e = 0, n = t.length, i = ""; e < n; e++)i += t[e].value;
                return i
            }

            function p(t, e, n) {
                var i = e.dir, o = e.next, r = o || i, s = n && "parentNode" === r, a = M++;
                return e.first ? function (e, n, o) {
                    for (; e = e[i];)if (1 === e.nodeType || s)return t(e, n, o);
                    return !1
                } : function (e, n, l) {
                    var u, c, f, p = [W, a];
                    if (l) {
                        for (; e = e[i];)if ((1 === e.nodeType || s) && t(e, n, l))return !0
                    } else for (; e = e[i];)if (1 === e.nodeType || s)if (f = e[H] || (e[H] = {}), c = f[e.uniqueID] || (f[e.uniqueID] = {}), o && o === e.nodeName.toLowerCase())e = e[i] || e; else {
                        if ((u = c[r]) && u[0] === W && u[1] === a)return p[2] = u[2];
                        if (c[r] = p, p[2] = t(e, n, l))return !0
                    }
                    return !1
                }
            }

            function d(t) {
                return t.length > 1 ? function (e, n, i) {
                    for (var o = t.length; o--;)if (!t[o](e, n, i))return !1;
                    return !0
                } : t[0]
            }

            function h(t, n, i) {
                for (var o = 0, r = n.length; o < r; o++)e(t, n[o], i);
                return i
            }

            function g(t, e, n, i, o) {
                for (var r, s = [], a = 0, l = t.length, u = null != e; a < l; a++)(r = t[a]) && (n && !n(r, i, o) || (s.push(r), u && e.push(a)));
                return s
            }

            function m(t, e, n, o, r, s) {
                return o && !o[H] && (o = m(o)), r && !r[H] && (r = m(r, s)), i(function (i, s, a, l) {
                    var u, c, f, p = [], d = [], m = s.length, v = i || h(e || "*", a.nodeType ? [a] : a, []), y = !t || !i && e ? v : g(v, p, t, a, l), b = n ? r || (i ? t : m || o) ? [] : s : y;
                    if (n && n(y, b, a, l), o)for (u = g(b, d), o(u, [], a, l), c = u.length; c--;)(f = u[c]) && (b[d[c]] = !(y[d[c]] = f));
                    if (i) {
                        if (r || t) {
                            if (r) {
                                for (u = [], c = b.length; c--;)(f = b[c]) && u.push(y[c] = f);
                                r(null, b = [], u, l)
                            }
                            for (c = b.length; c--;)(f = b[c]) && (u = r ? K(i, f) : p[c]) > -1 && (i[u] = !(s[u] = f))
                        }
                    } else b = g(b === s ? b.splice(m, b.length) : b), r ? r(null, s, b, l) : Y.apply(s, b)
                })
            }

            function v(t) {
                for (var e, n, i, o = t.length, r = w.relative[t[0].type], s = r || w.relative[" "], a = r ? 1 : 0, l = p(function (t) {
                    return t === e
                }, s, !0), u = p(function (t) {
                    return K(e, t) > -1
                }, s, !0), c = [function (t, n, i) {
                    var o = !r && (i || n !== k) || ((e = n).nodeType ? l(t, n, i) : u(t, n, i));
                    return e = null, o
                }]; a < o; a++)if (n = w.relative[t[a].type])c = [p(d(c), n)]; else {
                    if ((n = w.filter[t[a].type].apply(null, t[a].matches))[H]) {
                        for (i = ++a; i < o && !w.relative[t[i].type]; i++);
                        return m(a > 1 && d(c), a > 1 && f(t.slice(0, a - 1).concat({value: " " === t[a - 2].type ? "*" : ""})).replace(rt, "$1"), n, a < i && v(t.slice(a, i)), i < o && v(t = t.slice(i)), i < o && f(t))
                    }
                    c.push(n)
                }
                return d(c)
            }

            function y(t, n) {
                var o = n.length > 0, r = t.length > 0, s = function (i, s, a, l, u) {
                    var c, f, p, d = 0, h = "0", m = i && [], v = [], y = k, b = i || r && w.find.TAG("*", u), x = W += null == y ? 1 : Math.random() || .1, T = b.length;
                    for (u && (k = s === j || s || u); h !== T && null != (c = b[h]); h++) {
                        if (r && c) {
                            for (f = 0, s || c.ownerDocument === j || (A(c), a = !I); p = t[f++];)if (p(c, s || j, a)) {
                                l.push(c);
                                break
                            }
                            u && (W = x)
                        }
                        o && ((c = !p && c) && d--, i && m.push(c))
                    }
                    if (d += h, o && h !== d) {
                        for (f = 0; p = n[f++];)p(m, v, s, a);
                        if (i) {
                            if (d > 0)for (; h--;)m[h] || v[h] || (v[h] = X.call(l));
                            v = g(v)
                        }
                        Y.apply(l, v), u && !i && v.length > 0 && d + n.length > 1 && e.uniqueSort(l)
                    }
                    return u && (W = x, k = y), m
                };
                return o ? i(s) : s
            }

            var b, x, w, T, C, E, S, $, k, D, N, A, j, O, I, L, R, q, P, H = "sizzle" + 1 * new Date, F = t.document, W = 0, M = 0, B = n(), U = n(), _ = n(), z = function (t, e) {
                return t === e && (N = !0), 0
            }, V = {}.hasOwnProperty, Q = [], X = Q.pop, G = Q.push, Y = Q.push, J = Q.slice, K = function (t, e) {
                for (var n = 0, i = t.length; n < i; n++)if (t[n] === e)return n;
                return -1
            }, Z = "checked|selected|async|autofocus|autoplay|controls|defer|disabled|hidden|ismap|loop|multiple|open|readonly|required|scoped", tt = "[\\x20\\t\\r\\n\\f]", et = "(?:\\\\.|[\\w-]|[^\0-\\xa0])+", nt = "\\[" + tt + "*(" + et + ")(?:" + tt + "*([*^$|!~]?=)" + tt + "*(?:'((?:\\\\.|[^\\\\'])*)'|\"((?:\\\\.|[^\\\\\"])*)\"|(" + et + "))|)" + tt + "*\\]", it = ":(" + et + ")(?:\\((('((?:\\\\.|[^\\\\'])*)'|\"((?:\\\\.|[^\\\\\"])*)\")|((?:\\\\.|[^\\\\()[\\]]|" + nt + ")*)|.*)\\)|)", ot = new RegExp(tt + "+", "g"), rt = new RegExp("^" + tt + "+|((?:^|[^\\\\])(?:\\\\.)*)" + tt + "+$", "g"), st = new RegExp("^" + tt + "*," + tt + "*"), at = new RegExp("^" + tt + "*([>+~]|" + tt + ")" + tt + "*"), lt = new RegExp("=" + tt + "*([^\\]'\"]*?)" + tt + "*\\]", "g"), ut = new RegExp(it), ct = new RegExp("^" + et + "$"), ft = {
                ID: new RegExp("^#(" + et + ")"),
                CLASS: new RegExp("^\\.(" + et + ")"),
                TAG: new RegExp("^(" + et + "|[*])"),
                ATTR: new RegExp("^" + nt),
                PSEUDO: new RegExp("^" + it),
                CHILD: new RegExp("^:(only|first|last|nth|nth-last)-(child|of-type)(?:\\(" + tt + "*(even|odd|(([+-]|)(\\d*)n|)" + tt + "*(?:([+-]|)" + tt + "*(\\d+)|))" + tt + "*\\)|)", "i"),
                bool: new RegExp("^(?:" + Z + ")$", "i"),
                needsContext: new RegExp("^" + tt + "*[>+~]|:(even|odd|eq|gt|lt|nth|first|last)(?:\\(" + tt + "*((?:-\\d)?\\d*)" + tt + "*\\)|)(?=[^-]|$)", "i")
            }, pt = /^(?:input|select|textarea|button)$/i, dt = /^h\d$/i, ht = /^[^{]+\{\s*\[native \w/, gt = /^(?:#([\w-]+)|(\w+)|\.([\w-]+))$/, mt = /[+~]/, vt = new RegExp("\\\\([\\da-f]{1,6}" + tt + "?|(" + tt + ")|.)", "ig"), yt = function (t, e, n) {
                var i = "0x" + e - 65536;
                return i != i || n ? e : i < 0 ? String.fromCharCode(i + 65536) : String.fromCharCode(i >> 10 | 55296, 1023 & i | 56320)
            }, bt = /([\0-\x1f\x7f]|^-?\d)|^-$|[^\0-\x1f\x7f-\uFFFF\w-]/g, xt = function (t, e) {
                return e ? "\0" === t ? "ï¿½" : t.slice(0, -1) + "\\" + t.charCodeAt(t.length - 1).toString(16) + " " : "\\" + t
            }, wt = function () {
                A()
            }, Tt = p(function (t) {
                return !0 === t.disabled && ("form" in t || "label" in t)
            }, {dir: "parentNode", next: "legend"});
            try {
                Y.apply(Q = J.call(F.childNodes), F.childNodes), Q[F.childNodes.length].nodeType
            } catch (t) {
                Y = {
                    apply: Q.length ? function (t, e) {
                        G.apply(t, J.call(e))
                    } : function (t, e) {
                        for (var n = t.length, i = 0; t[n++] = e[i++];);
                        t.length = n - 1
                    }
                }
            }
            x = e.support = {}, C = e.isXML = function (t) {
                var e = t && (t.ownerDocument || t).documentElement;
                return !!e && "HTML" !== e.nodeName
            }, A = e.setDocument = function (t) {
                var e, n, i = t ? t.ownerDocument || t : F;
                return i !== j && 9 === i.nodeType && i.documentElement ? (j = i, O = j.documentElement, I = !C(j), F !== j && (n = j.defaultView) && n.top !== n && (n.addEventListener ? n.addEventListener("unload", wt, !1) : n.attachEvent && n.attachEvent("onunload", wt)), x.attributes = o(function (t) {
                    return t.className = "i", !t.getAttribute("className")
                }), x.getElementsByTagName = o(function (t) {
                    return t.appendChild(j.createComment("")), !t.getElementsByTagName("*").length
                }), x.getElementsByClassName = ht.test(j.getElementsByClassName), x.getById = o(function (t) {
                    return O.appendChild(t).id = H, !j.getElementsByName || !j.getElementsByName(H).length
                }), x.getById ? (w.filter.ID = function (t) {
                    var e = t.replace(vt, yt);
                    return function (t) {
                        return t.getAttribute("id") === e
                    }
                }, w.find.ID = function (t, e) {
                    if (void 0 !== e.getElementById && I) {
                        var n = e.getElementById(t);
                        return n ? [n] : []
                    }
                }) : (w.filter.ID = function (t) {
                    var e = t.replace(vt, yt);
                    return function (t) {
                        var n = void 0 !== t.getAttributeNode && t.getAttributeNode("id");
                        return n && n.value === e
                    }
                }, w.find.ID = function (t, e) {
                    if (void 0 !== e.getElementById && I) {
                        var n, i, o, r = e.getElementById(t);
                        if (r) {
                            if ((n = r.getAttributeNode("id")) && n.value === t)return [r];
                            for (o = e.getElementsByName(t), i = 0; r = o[i++];)if ((n = r.getAttributeNode("id")) && n.value === t)return [r]
                        }
                        return []
                    }
                }), w.find.TAG = x.getElementsByTagName ? function (t, e) {
                    return void 0 !== e.getElementsByTagName ? e.getElementsByTagName(t) : x.qsa ? e.querySelectorAll(t) : void 0
                } : function (t, e) {
                    var n, i = [], o = 0, r = e.getElementsByTagName(t);
                    if ("*" === t) {
                        for (; n = r[o++];)1 === n.nodeType && i.push(n);
                        return i
                    }
                    return r
                }, w.find.CLASS = x.getElementsByClassName && function (t, e) {
                        if (void 0 !== e.getElementsByClassName && I)return e.getElementsByClassName(t)
                    }, R = [], L = [], (x.qsa = ht.test(j.querySelectorAll)) && (o(function (t) {
                    O.appendChild(t).innerHTML = "<a id='" + H + "'></a><select id='" + H + "-\r\\' msallowcapture=''><option selected=''></option></select>", t.querySelectorAll("[msallowcapture^='']").length && L.push("[*^$]=" + tt + "*(?:''|\"\")"), t.querySelectorAll("[selected]").length || L.push("\\[" + tt + "*(?:value|" + Z + ")"), t.querySelectorAll("[id~=" + H + "-]").length || L.push("~="), t.querySelectorAll(":checked").length || L.push(":checked"), t.querySelectorAll("a#" + H + "+*").length || L.push(".#.+[+~]")
                }), o(function (t) {
                    t.innerHTML = "<a href='' disabled='disabled'></a><select disabled='disabled'><option/></select>";
                    var e = j.createElement("input");
                    e.setAttribute("type", "hidden"), t.appendChild(e).setAttribute("name", "D"), t.querySelectorAll("[name=d]").length && L.push("name" + tt + "*[*^$|!~]?="), 2 !== t.querySelectorAll(":enabled").length && L.push(":enabled", ":disabled"), O.appendChild(t).disabled = !0, 2 !== t.querySelectorAll(":disabled").length && L.push(":enabled", ":disabled"), t.querySelectorAll("*,:x"), L.push(",.*:")
                })), (x.matchesSelector = ht.test(q = O.matches || O.webkitMatchesSelector || O.mozMatchesSelector || O.oMatchesSelector || O.msMatchesSelector)) && o(function (t) {
                    x.disconnectedMatch = q.call(t, "*"), q.call(t, "[s!='']:x"), R.push("!=", it)
                }), L = L.length && new RegExp(L.join("|")), R = R.length && new RegExp(R.join("|")), e = ht.test(O.compareDocumentPosition), P = e || ht.test(O.contains) ? function (t, e) {
                    var n = 9 === t.nodeType ? t.documentElement : t, i = e && e.parentNode;
                    return t === i || !(!i || 1 !== i.nodeType || !(n.contains ? n.contains(i) : t.compareDocumentPosition && 16 & t.compareDocumentPosition(i)))
                } : function (t, e) {
                    if (e)for (; e = e.parentNode;)if (e === t)return !0;
                    return !1
                }, z = e ? function (t, e) {
                    if (t === e)return N = !0, 0;
                    var n = !t.compareDocumentPosition - !e.compareDocumentPosition;
                    return n || (1 & (n = (t.ownerDocument || t) === (e.ownerDocument || e) ? t.compareDocumentPosition(e) : 1) || !x.sortDetached && e.compareDocumentPosition(t) === n ? t === j || t.ownerDocument === F && P(F, t) ? -1 : e === j || e.ownerDocument === F && P(F, e) ? 1 : D ? K(D, t) - K(D, e) : 0 : 4 & n ? -1 : 1)
                } : function (t, e) {
                    if (t === e)return N = !0, 0;
                    var n, i = 0, o = t.parentNode, r = e.parentNode, a = [t], l = [e];
                    if (!o || !r)return t === j ? -1 : e === j ? 1 : o ? -1 : r ? 1 : D ? K(D, t) - K(D, e) : 0;
                    if (o === r)return s(t, e);
                    for (n = t; n = n.parentNode;)a.unshift(n);
                    for (n = e; n = n.parentNode;)l.unshift(n);
                    for (; a[i] === l[i];)i++;
                    return i ? s(a[i], l[i]) : a[i] === F ? -1 : l[i] === F ? 1 : 0
                }, j) : j
            }, e.matches = function (t, n) {
                return e(t, null, null, n)
            }, e.matchesSelector = function (t, n) {
                if ((t.ownerDocument || t) !== j && A(t), n = n.replace(lt, "='$1']"), x.matchesSelector && I && !_[n + " "] && (!R || !R.test(n)) && (!L || !L.test(n)))try {
                    var i = q.call(t, n);
                    if (i || x.disconnectedMatch || t.document && 11 !== t.document.nodeType)return i
                } catch (t) {
                }
                return e(n, j, null, [t]).length > 0
            }, e.contains = function (t, e) {
                return (t.ownerDocument || t) !== j && A(t), P(t, e)
            }, e.attr = function (t, e) {
                (t.ownerDocument || t) !== j && A(t);
                var n = w.attrHandle[e.toLowerCase()], i = n && V.call(w.attrHandle, e.toLowerCase()) ? n(t, e, !I) : void 0;
                return void 0 !== i ? i : x.attributes || !I ? t.getAttribute(e) : (i = t.getAttributeNode(e)) && i.specified ? i.value : null
            }, e.escape = function (t) {
                return (t + "").replace(bt, xt)
            }, e.error = function (t) {
                throw new Error("Syntax error, unrecognized expression: " + t)
            }, e.uniqueSort = function (t) {
                var e, n = [], i = 0, o = 0;
                if (N = !x.detectDuplicates, D = !x.sortStable && t.slice(0), t.sort(z), N) {
                    for (; e = t[o++];)e === t[o] && (i = n.push(o));
                    for (; i--;)t.splice(n[i], 1)
                }
                return D = null, t
            }, T = e.getText = function (t) {
                var e, n = "", i = 0, o = t.nodeType;
                if (o) {
                    if (1 === o || 9 === o || 11 === o) {
                        if ("string" == typeof t.textContent)return t.textContent;
                        for (t = t.firstChild; t; t = t.nextSibling)n += T(t)
                    } else if (3 === o || 4 === o)return t.nodeValue
                } else for (; e = t[i++];)n += T(e);
                return n
            }, (w = e.selectors = {
                cacheLength: 50, createPseudo: i, match: ft, attrHandle: {}, find: {}, relative: {">": {dir: "parentNode", first: !0}, " ": {dir: "parentNode"}, "+": {dir: "previousSibling", first: !0}, "~": {dir: "previousSibling"}}, preFilter: {
                    ATTR: function (t) {
                        return t[1] = t[1].replace(vt, yt), t[3] = (t[3] || t[4] || t[5] || "").replace(vt, yt), "~=" === t[2] && (t[3] = " " + t[3] + " "), t.slice(0, 4)
                    }, CHILD: function (t) {
                        return t[1] = t[1].toLowerCase(), "nth" === t[1].slice(0, 3) ? (t[3] || e.error(t[0]), t[4] = +(t[4] ? t[5] + (t[6] || 1) : 2 * ("even" === t[3] || "odd" === t[3])), t[5] = +(t[7] + t[8] || "odd" === t[3])) : t[3] && e.error(t[0]), t
                    }, PSEUDO: function (t) {
                        var e, n = !t[6] && t[2];
                        return ft.CHILD.test(t[0]) ? null : (t[3] ? t[2] = t[4] || t[5] || "" : n && ut.test(n) && (e = E(n, !0)) && (e = n.indexOf(")", n.length - e) - n.length) && (t[0] = t[0].slice(0, e), t[2] = n.slice(0, e)), t.slice(0, 3))
                    }
                }, filter: {
                    TAG: function (t) {
                        var e = t.replace(vt, yt).toLowerCase();
                        return "*" === t ? function () {
                            return !0
                        } : function (t) {
                            return t.nodeName && t.nodeName.toLowerCase() === e
                        }
                    }, CLASS: function (t) {
                        var e = B[t + " "];
                        return e || (e = new RegExp("(^|" + tt + ")" + t + "(" + tt + "|$)")) && B(t, function (t) {
                                return e.test("string" == typeof t.className && t.className || void 0 !== t.getAttribute && t.getAttribute("class") || "")
                            })
                    }, ATTR: function (t, n, i) {
                        return function (o) {
                            var r = e.attr(o, t);
                            return null == r ? "!=" === n : !n || (r += "", "=" === n ? r === i : "!=" === n ? r !== i : "^=" === n ? i && 0 === r.indexOf(i) : "*=" === n ? i && r.indexOf(i) > -1 : "$=" === n ? i && r.slice(-i.length) === i : "~=" === n ? (" " + r.replace(ot, " ") + " ").indexOf(i) > -1 : "|=" === n && (r === i || r.slice(0, i.length + 1) === i + "-"))
                        }
                    }, CHILD: function (t, e, n, i, o) {
                        var r = "nth" !== t.slice(0, 3), s = "last" !== t.slice(-4), a = "of-type" === e;
                        return 1 === i && 0 === o ? function (t) {
                            return !!t.parentNode
                        } : function (e, n, l) {
                            var u, c, f, p, d, h, g = r !== s ? "nextSibling" : "previousSibling", m = e.parentNode, v = a && e.nodeName.toLowerCase(), y = !l && !a, b = !1;
                            if (m) {
                                if (r) {
                                    for (; g;) {
                                        for (p = e; p = p[g];)if (a ? p.nodeName.toLowerCase() === v : 1 === p.nodeType)return !1;
                                        h = g = "only" === t && !h && "nextSibling"
                                    }
                                    return !0
                                }
                                if (h = [s ? m.firstChild : m.lastChild], s && y) {
                                    for (b = (d = (u = (c = (f = (p = m)[H] || (p[H] = {}))[p.uniqueID] || (f[p.uniqueID] = {}))[t] || [])[0] === W && u[1]) && u[2], p = d && m.childNodes[d]; p = ++d && p && p[g] || (b = d = 0) || h.pop();)if (1 === p.nodeType && ++b && p === e) {
                                        c[t] = [W, d, b];
                                        break
                                    }
                                } else if (y && (b = d = (u = (c = (f = (p = e)[H] || (p[H] = {}))[p.uniqueID] || (f[p.uniqueID] = {}))[t] || [])[0] === W && u[1]), !1 === b)for (; (p = ++d && p && p[g] || (b = d = 0) || h.pop()) && ((a ? p.nodeName.toLowerCase() !== v : 1 !== p.nodeType) || !++b || (y && ((c = (f = p[H] || (p[H] = {}))[p.uniqueID] || (f[p.uniqueID] = {}))[t] = [W, b]), p !== e)););
                                return (b -= o) === i || b % i == 0 && b / i >= 0
                            }
                        }
                    }, PSEUDO: function (t, n) {
                        var o, r = w.pseudos[t] || w.setFilters[t.toLowerCase()] || e.error("unsupported pseudo: " + t);
                        return r[H] ? r(n) : r.length > 1 ? (o = [t, t, "", n], w.setFilters.hasOwnProperty(t.toLowerCase()) ? i(function (t, e) {
                            for (var i, o = r(t, n), s = o.length; s--;)t[i = K(t, o[s])] = !(e[i] = o[s])
                        }) : function (t) {
                            return r(t, 0, o)
                        }) : r
                    }
                }, pseudos: {
                    not: i(function (t) {
                        var e = [], n = [], o = S(t.replace(rt, "$1"));
                        return o[H] ? i(function (t, e, n, i) {
                            for (var r, s = o(t, null, i, []), a = t.length; a--;)(r = s[a]) && (t[a] = !(e[a] = r))
                        }) : function (t, i, r) {
                            return e[0] = t, o(e, null, r, n), e[0] = null, !n.pop()
                        }
                    }), has: i(function (t) {
                        return function (n) {
                            return e(t, n).length > 0
                        }
                    }), contains: i(function (t) {
                        return t = t.replace(vt, yt), function (e) {
                            return (e.textContent || e.innerText || T(e)).indexOf(t) > -1
                        }
                    }), lang: i(function (t) {
                        return ct.test(t || "") || e.error("unsupported lang: " + t), t = t.replace(vt, yt).toLowerCase(), function (e) {
                            var n;
                            do {
                                if (n = I ? e.lang : e.getAttribute("xml:lang") || e.getAttribute("lang"))return (n = n.toLowerCase()) === t || 0 === n.indexOf(t + "-")
                            } while ((e = e.parentNode) && 1 === e.nodeType);
                            return !1
                        }
                    }), target: function (e) {
                        var n = t.location && t.location.hash;
                        return n && n.slice(1) === e.id
                    }, root: function (t) {
                        return t === O
                    }, focus: function (t) {
                        return t === j.activeElement && (!j.hasFocus || j.hasFocus()) && !!(t.type || t.href || ~t.tabIndex)
                    }, enabled: a(!1), disabled: a(!0), checked: function (t) {
                        var e = t.nodeName.toLowerCase();
                        return "input" === e && !!t.checked || "option" === e && !!t.selected
                    }, selected: function (t) {
                        return t.parentNode && t.parentNode.selectedIndex, !0 === t.selected
                    }, empty: function (t) {
                        for (t = t.firstChild; t; t = t.nextSibling)if (t.nodeType < 6)return !1;
                        return !0
                    }, parent: function (t) {
                        return !w.pseudos.empty(t)
                    }, header: function (t) {
                        return dt.test(t.nodeName)
                    }, input: function (t) {
                        return pt.test(t.nodeName)
                    }, button: function (t) {
                        var e = t.nodeName.toLowerCase();
                        return "input" === e && "button" === t.type || "button" === e
                    }, text: function (t) {
                        var e;
                        return "input" === t.nodeName.toLowerCase() && "text" === t.type && (null == (e = t.getAttribute("type")) || "text" === e.toLowerCase())
                    }, first: l(function () {
                        return [0]
                    }), last: l(function (t, e) {
                        return [e - 1]
                    }), eq: l(function (t, e, n) {
                        return [n < 0 ? n + e : n]
                    }), even: l(function (t, e) {
                        for (var n = 0; n < e; n += 2)t.push(n);
                        return t
                    }), odd: l(function (t, e) {
                        for (var n = 1; n < e; n += 2)t.push(n);
                        return t
                    }), lt: l(function (t, e, n) {
                        for (var i = n < 0 ? n + e : n; --i >= 0;)t.push(i);
                        return t
                    }), gt: l(function (t, e, n) {
                        for (var i = n < 0 ? n + e : n; ++i < e;)t.push(i);
                        return t
                    })
                }
            }).pseudos.nth = w.pseudos.eq;
            for (b in{radio: !0, checkbox: !0, file: !0, password: !0, image: !0})w.pseudos[b] = function (t) {
                return function (e) {
                    return "input" === e.nodeName.toLowerCase() && e.type === t
                }
            }(b);
            for (b in{submit: !0, reset: !0})w.pseudos[b] = function (t) {
                return function (e) {
                    var n = e.nodeName.toLowerCase();
                    return ("input" === n || "button" === n) && e.type === t
                }
            }(b);
            return c.prototype = w.filters = w.pseudos, w.setFilters = new c, E = e.tokenize = function (t, n) {
                var i, o, r, s, a, l, u, c = U[t + " "];
                if (c)return n ? 0 : c.slice(0);
                for (a = t, l = [], u = w.preFilter; a;) {
                    i && !(o = st.exec(a)) || (o && (a = a.slice(o[0].length) || a), l.push(r = [])), i = !1, (o = at.exec(a)) && (i = o.shift(), r.push({value: i, type: o[0].replace(rt, " ")}), a = a.slice(i.length));
                    for (s in w.filter)!(o = ft[s].exec(a)) || u[s] && !(o = u[s](o)) || (i = o.shift(), r.push({value: i, type: s, matches: o}), a = a.slice(i.length));
                    if (!i)break
                }
                return n ? a.length : a ? e.error(t) : U(t, l).slice(0)
            }, S = e.compile = function (t, e) {
                var n, i = [], o = [], r = _[t + " "];
                if (!r) {
                    for (e || (e = E(t)), n = e.length; n--;)(r = v(e[n]))[H] ? i.push(r) : o.push(r);
                    (r = _(t, y(o, i))).selector = t
                }
                return r
            }, $ = e.select = function (t, e, n, i) {
                var o, r, s, a, l, c = "function" == typeof t && t, p = !i && E(t = c.selector || t);
                if (n = n || [], 1 === p.length) {
                    if ((r = p[0] = p[0].slice(0)).length > 2 && "ID" === (s = r[0]).type && 9 === e.nodeType && I && w.relative[r[1].type]) {
                        if (!(e = (w.find.ID(s.matches[0].replace(vt, yt), e) || [])[0]))return n;
                        c && (e = e.parentNode), t = t.slice(r.shift().value.length)
                    }
                    for (o = ft.needsContext.test(t) ? 0 : r.length; o-- && (s = r[o], !w.relative[a = s.type]);)if ((l = w.find[a]) && (i = l(s.matches[0].replace(vt, yt), mt.test(r[0].type) && u(e.parentNode) || e))) {
                        if (r.splice(o, 1), !(t = i.length && f(r)))return Y.apply(n, i), n;
                        break
                    }
                }
                return (c || S(t, p))(i, e, !I, n, !e || mt.test(t) && u(e.parentNode) || e), n
            }, x.sortStable = H.split("").sort(z).join("") === H, x.detectDuplicates = !!N, A(), x.sortDetached = o(function (t) {
                return 1 & t.compareDocumentPosition(j.createElement("fieldset"))
            }), o(function (t) {
                return t.innerHTML = "<a href='#'></a>", "#" === t.firstChild.getAttribute("href")
            }) || r("type|href|height|width", function (t, e, n) {
                if (!n)return t.getAttribute(e, "type" === e.toLowerCase() ? 1 : 2)
            }), x.attributes && o(function (t) {
                return t.innerHTML = "<input/>", t.firstChild.setAttribute("value", ""), "" === t.firstChild.getAttribute("value")
            }) || r("value", function (t, e, n) {
                if (!n && "input" === t.nodeName.toLowerCase())return t.defaultValue
            }), o(function (t) {
                return null == t.getAttribute("disabled")
            }) || r(Z, function (t, e, n) {
                var i;
                if (!n)return !0 === t[e] ? e.toLowerCase() : (i = t.getAttributeNode(e)) && i.specified ? i.value : null
            }), e
        }(t);
        dt.find = yt, dt.expr = yt.selectors, dt.expr[":"] = dt.expr.pseudos, dt.uniqueSort = dt.unique = yt.uniqueSort, dt.text = yt.getText, dt.isXMLDoc = yt.isXML, dt.contains = yt.contains, dt.escapeSelector = yt.escape;
        var bt = function (t, e, n) {
            for (var i = [], o = void 0 !== n; (t = t[e]) && 9 !== t.nodeType;)if (1 === t.nodeType) {
                if (o && dt(t).is(n))break;
                i.push(t)
            }
            return i
        }, xt = function (t, e) {
            for (var n = []; t; t = t.nextSibling)1 === t.nodeType && t !== e && n.push(t);
            return n
        }, wt = dt.expr.match.needsContext, Tt = /^<([a-z][^\/\0>:\x20\t\r\n\f]*)[\x20\t\r\n\f]*\/?>(?:<\/\1>|)$/i, Ct = /^.[^:#\[\.,]*$/;
        dt.filter = function (t, e, n) {
            var i = e[0];
            return n && (t = ":not(" + t + ")"), 1 === e.length && 1 === i.nodeType ? dt.find.matchesSelector(i, t) ? [i] : [] : dt.find.matches(t, dt.grep(e, function (t) {
                return 1 === t.nodeType
            }))
        }, dt.fn.extend({
            find: function (t) {
                var e, n, i = this.length, o = this;
                if ("string" != typeof t)return this.pushStack(dt(t).filter(function () {
                    for (e = 0; e < i; e++)if (dt.contains(o[e], this))return !0
                }));
                for (n = this.pushStack([]), e = 0; e < i; e++)dt.find(t, o[e], n);
                return i > 1 ? dt.uniqueSort(n) : n
            }, filter: function (t) {
                return this.pushStack(r(this, t || [], !1))
            }, not: function (t) {
                return this.pushStack(r(this, t || [], !0))
            }, is: function (t) {
                return !!r(this, "string" == typeof t && wt.test(t) ? dt(t) : t || [], !1).length
            }
        });
        var Et, St = /^(?:\s*(<[\w\W]+>)[^>]*|#([\w-]+))$/;
        (dt.fn.init = function (t, e, n) {
            var i, o;
            if (!t)return this;
            if (n = n || Et, "string" == typeof t) {
                if (!(i = "<" === t[0] && ">" === t[t.length - 1] && t.length >= 3 ? [null, t, null] : St.exec(t)) || !i[1] && e)return !e || e.jquery ? (e || n).find(t) : this.constructor(e).find(t);
                if (i[1]) {
                    if (e = e instanceof dt ? e[0] : e, dt.merge(this, dt.parseHTML(i[1], e && e.nodeType ? e.ownerDocument || e : et, !0)), Tt.test(i[1]) && dt.isPlainObject(e))for (i in e)dt.isFunction(this[i]) ? this[i](e[i]) : this.attr(i, e[i]);
                    return this
                }
                return (o = et.getElementById(i[2])) && (this[0] = o, this.length = 1), this
            }
            return t.nodeType ? (this[0] = t, this.length = 1, this) : dt.isFunction(t) ? void 0 !== n.ready ? n.ready(t) : t(dt) : dt.makeArray(t, this)
        }).prototype = dt.fn, Et = dt(et);
        var $t = /^(?:parents|prev(?:Until|All))/, kt = {children: !0, contents: !0, next: !0, prev: !0};
        dt.fn.extend({
            has: function (t) {
                var e = dt(t, this), n = e.length;
                return this.filter(function () {
                    for (var t = 0; t < n; t++)if (dt.contains(this, e[t]))return !0
                })
            }, closest: function (t, e) {
                var n, i = 0, o = this.length, r = [], s = "string" != typeof t && dt(t);
                if (!wt.test(t))for (; i < o; i++)for (n = this[i]; n && n !== e; n = n.parentNode)if (n.nodeType < 11 && (s ? s.index(n) > -1 : 1 === n.nodeType && dt.find.matchesSelector(n, t))) {
                    r.push(n);
                    break
                }
                return this.pushStack(r.length > 1 ? dt.uniqueSort(r) : r)
            }, index: function (t) {
                return t ? "string" == typeof t ? st.call(dt(t), this[0]) : st.call(this, t.jquery ? t[0] : t) : this[0] && this[0].parentNode ? this.first().prevAll().length : -1
            }, add: function (t, e) {
                return this.pushStack(dt.uniqueSort(dt.merge(this.get(), dt(t, e))))
            }, addBack: function (t) {
                return this.add(null == t ? this.prevObject : this.prevObject.filter(t))
            }
        }), dt.each({
            parent: function (t) {
                var e = t.parentNode;
                return e && 11 !== e.nodeType ? e : null
            }, parents: function (t) {
                return bt(t, "parentNode")
            }, parentsUntil: function (t, e, n) {
                return bt(t, "parentNode", n)
            }, next: function (t) {
                return s(t, "nextSibling")
            }, prev: function (t) {
                return s(t, "previousSibling")
            }, nextAll: function (t) {
                return bt(t, "nextSibling")
            }, prevAll: function (t) {
                return bt(t, "previousSibling")
            }, nextUntil: function (t, e, n) {
                return bt(t, "nextSibling", n)
            }, prevUntil: function (t, e, n) {
                return bt(t, "previousSibling", n)
            }, siblings: function (t) {
                return xt((t.parentNode || {}).firstChild, t)
            }, children: function (t) {
                return xt(t.firstChild)
            }, contents: function (t) {
                return o(t, "iframe") ? t.contentDocument : (o(t, "template") && (t = t.content || t), dt.merge([], t.childNodes))
            }
        }, function (t, e) {
            dt.fn[t] = function (n, i) {
                var o = dt.map(this, e, n);
                return "Until" !== t.slice(-5) && (i = n), i && "string" == typeof i && (o = dt.filter(i, o)), this.length > 1 && (kt[t] || dt.uniqueSort(o), $t.test(t) && o.reverse()), this.pushStack(o)
            }
        });
        var Dt = /[^\x20\t\r\n\f]+/g;
        dt.Callbacks = function (t) {
            t = "string" == typeof t ? a(t) : dt.extend({}, t);
            var e, n, i, o, r = [], s = [], l = -1, u = function () {
                for (o = o || t.once, i = e = !0; s.length; l = -1)for (n = s.shift(); ++l < r.length;)!1 === r[l].apply(n[0], n[1]) && t.stopOnFalse && (l = r.length, n = !1);
                t.memory || (n = !1), e = !1, o && (r = n ? [] : "")
            }, c = {
                add: function () {
                    return r && (n && !e && (l = r.length - 1, s.push(n)), function e(n) {
                        dt.each(n, function (n, i) {
                            dt.isFunction(i) ? t.unique && c.has(i) || r.push(i) : i && i.length && "string" !== dt.type(i) && e(i)
                        })
                    }(arguments), n && !e && u()), this
                }, remove: function () {
                    return dt.each(arguments, function (t, e) {
                        for (var n; (n = dt.inArray(e, r, n)) > -1;)r.splice(n, 1), n <= l && l--
                    }), this
                }, has: function (t) {
                    return t ? dt.inArray(t, r) > -1 : r.length > 0
                }, empty: function () {
                    return r && (r = []), this
                }, disable: function () {
                    return o = s = [], r = n = "", this
                }, disabled: function () {
                    return !r
                }, lock: function () {
                    return o = s = [], n || e || (r = n = ""), this
                }, locked: function () {
                    return !!o
                }, fireWith: function (t, n) {
                    return o || (n = [t, (n = n || []).slice ? n.slice() : n], s.push(n), e || u()), this
                }, fire: function () {
                    return c.fireWith(this, arguments), this
                }, fired: function () {
                    return !!i
                }
            };
            return c
        }, dt.extend({
            Deferred: function (e) {
                var n = [["notify", "progress", dt.Callbacks("memory"), dt.Callbacks("memory"), 2], ["resolve", "done", dt.Callbacks("once memory"), dt.Callbacks("once memory"), 0, "resolved"], ["reject", "fail", dt.Callbacks("once memory"), dt.Callbacks("once memory"), 1, "rejected"]], i = "pending", o = {
                    state: function () {
                        return i
                    }, always: function () {
                        return r.done(arguments).fail(arguments), this
                    }, catch: function (t) {
                        return o.then(null, t)
                    }, pipe: function () {
                        var t = arguments;
                        return dt.Deferred(function (e) {
                            dt.each(n, function (n, i) {
                                var o = dt.isFunction(t[i[4]]) && t[i[4]];
                                r[i[1]](function () {
                                    var t = o && o.apply(this, arguments);
                                    t && dt.isFunction(t.promise) ? t.promise().progress(e.notify).done(e.resolve).fail(e.reject) : e[i[0] + "With"](this, o ? [t] : arguments)
                                })
                            }), t = null
                        }).promise()
                    }, then: function (e, i, o) {
                        function r(e, n, i, o) {
                            return function () {
                                var a = this, c = arguments, f = function () {
                                    var t, f;
                                    if (!(e < s)) {
                                        if ((t = i.apply(a, c)) === n.promise())throw new TypeError("Thenable self-resolution");
                                        f = t && ("object" == typeof t || "function" == typeof t) && t.then, dt.isFunction(f) ? o ? f.call(t, r(s, n, l, o), r(s, n, u, o)) : (s++, f.call(t, r(s, n, l, o), r(s, n, u, o), r(s, n, l, n.notifyWith))) : (i !== l && (a = void 0, c = [t]), (o || n.resolveWith)(a, c))
                                    }
                                }, p = o ? f : function () {
                                    try {
                                        f()
                                    } catch (t) {
                                        dt.Deferred.exceptionHook && dt.Deferred.exceptionHook(t, p.stackTrace), e + 1 >= s && (i !== u && (a = void 0, c = [t]), n.rejectWith(a, c))
                                    }
                                };
                                e ? p() : (dt.Deferred.getStackHook && (p.stackTrace = dt.Deferred.getStackHook()), t.setTimeout(p))
                            }
                        }

                        var s = 0;
                        return dt.Deferred(function (t) {
                            n[0][3].add(r(0, t, dt.isFunction(o) ? o : l, t.notifyWith)), n[1][3].add(r(0, t, dt.isFunction(e) ? e : l)), n[2][3].add(r(0, t, dt.isFunction(i) ? i : u))
                        }).promise()
                    }, promise: function (t) {
                        return null != t ? dt.extend(t, o) : o
                    }
                }, r = {};
                return dt.each(n, function (t, e) {
                    var s = e[2], a = e[5];
                    o[e[1]] = s.add, a && s.add(function () {
                        i = a
                    }, n[3 - t][2].disable, n[0][2].lock), s.add(e[3].fire), r[e[0]] = function () {
                        return r[e[0] + "With"](this === r ? void 0 : this, arguments), this
                    }, r[e[0] + "With"] = s.fireWith
                }), o.promise(r), e && e.call(r, r), r
            }, when: function (t) {
                var e = arguments.length, n = e, i = Array(n), o = it.call(arguments), r = dt.Deferred(), s = function (t) {
                    return function (n) {
                        i[t] = this, o[t] = arguments.length > 1 ? it.call(arguments) : n, --e || r.resolveWith(i, o)
                    }
                };
                if (e <= 1 && (c(t, r.done(s(n)).resolve, r.reject, !e), "pending" === r.state() || dt.isFunction(o[n] && o[n].then)))return r.then();
                for (; n--;)c(o[n], s(n), r.reject);
                return r.promise()
            }
        });
        var Nt = /^(Eval|Internal|Range|Reference|Syntax|Type|URI)Error$/;
        dt.Deferred.exceptionHook = function (e, n) {
            t.console && t.console.warn && e && Nt.test(e.name) && t.console.warn("jQuery.Deferred exception: " + e.message, e.stack, n)
        }, dt.readyException = function (e) {
            t.setTimeout(function () {
                throw e
            })
        };
        var At = dt.Deferred();
        dt.fn.ready = function (t) {
            return At.then(t).catch(function (t) {
                dt.readyException(t)
            }), this
        }, dt.extend({
            isReady: !1, readyWait: 1, ready: function (t) {
                (!0 === t ? --dt.readyWait : dt.isReady) || (dt.isReady = !0, !0 !== t && --dt.readyWait > 0 || At.resolveWith(et, [dt]))
            }
        }), dt.ready.then = At.then, "complete" === et.readyState || "loading" !== et.readyState && !et.documentElement.doScroll ? t.setTimeout(dt.ready) : (et.addEventListener("DOMContentLoaded", f), t.addEventListener("load", f));
        var jt = function (t, e, n, i, o, r, s) {
            var a = 0, l = t.length, u = null == n;
            if ("object" === dt.type(n)) {
                o = !0;
                for (a in n)jt(t, e, a, n[a], !0, r, s)
            } else if (void 0 !== i && (o = !0, dt.isFunction(i) || (s = !0), u && (s ? (e.call(t, i), e = null) : (u = e, e = function (t, e, n) {
                    return u.call(dt(t), n)
                })), e))for (; a < l; a++)e(t[a], n, s ? i : i.call(t[a], a, e(t[a], n)));
            return o ? t : u ? e.call(t) : l ? e(t[0], n) : r
        }, Ot = function (t) {
            return 1 === t.nodeType || 9 === t.nodeType || !+t.nodeType
        };
        p.uid = 1, p.prototype = {
            cache: function (t) {
                var e = t[this.expando];
                return e || (e = {}, Ot(t) && (t.nodeType ? t[this.expando] = e : Object.defineProperty(t, this.expando, {value: e, configurable: !0}))), e
            }, set: function (t, e, n) {
                var i, o = this.cache(t);
                if ("string" == typeof e)o[dt.camelCase(e)] = n; else for (i in e)o[dt.camelCase(i)] = e[i];
                return o
            }, get: function (t, e) {
                return void 0 === e ? this.cache(t) : t[this.expando] && t[this.expando][dt.camelCase(e)]
            }, access: function (t, e, n) {
                return void 0 === e || e && "string" == typeof e && void 0 === n ? this.get(t, e) : (this.set(t, e, n), void 0 !== n ? n : e)
            }, remove: function (t, e) {
                var n, i = t[this.expando];
                if (void 0 !== i) {
                    if (void 0 !== e) {
                        n = (e = Array.isArray(e) ? e.map(dt.camelCase) : (e = dt.camelCase(e)) in i ? [e] : e.match(Dt) || []).length;
                        for (; n--;)delete i[e[n]]
                    }
                    (void 0 === e || dt.isEmptyObject(i)) && (t.nodeType ? t[this.expando] = void 0 : delete t[this.expando])
                }
            }, hasData: function (t) {
                var e = t[this.expando];
                return void 0 !== e && !dt.isEmptyObject(e)
            }
        };
        var It = new p, Lt = new p, Rt = /^(?:\{[\w\W]*\}|\[[\w\W]*\])$/, qt = /[A-Z]/g;
        dt.extend({
            hasData: function (t) {
                return Lt.hasData(t) || It.hasData(t)
            }, data: function (t, e, n) {
                return Lt.access(t, e, n)
            }, removeData: function (t, e) {
                Lt.remove(t, e)
            }, _data: function (t, e, n) {
                return It.access(t, e, n)
            }, _removeData: function (t, e) {
                It.remove(t, e)
            }
        }), dt.fn.extend({
            data: function (t, e) {
                var n, i, o, r = this[0], s = r && r.attributes;
                if (void 0 === t) {
                    if (this.length && (o = Lt.get(r), 1 === r.nodeType && !It.get(r, "hasDataAttrs"))) {
                        for (n = s.length; n--;)s[n] && 0 === (i = s[n].name).indexOf("data-") && (i = dt.camelCase(i.slice(5)), h(r, i, o[i]));
                        It.set(r, "hasDataAttrs", !0)
                    }
                    return o
                }
                return "object" == typeof t ? this.each(function () {
                    Lt.set(this, t)
                }) : jt(this, function (e) {
                    var n;
                    if (r && void 0 === e) {
                        if (void 0 !== (n = Lt.get(r, t)))return n;
                        if (void 0 !== (n = h(r, t)))return n
                    } else this.each(function () {
                        Lt.set(this, t, e)
                    })
                }, null, e, arguments.length > 1, null, !0)
            }, removeData: function (t) {
                return this.each(function () {
                    Lt.remove(this, t)
                })
            }
        }), dt.extend({
            queue: function (t, e, n) {
                var i;
                if (t)return e = (e || "fx") + "queue", i = It.get(t, e), n && (!i || Array.isArray(n) ? i = It.access(t, e, dt.makeArray(n)) : i.push(n)), i || []
            }, dequeue: function (t, e) {
                e = e || "fx";
                var n = dt.queue(t, e), i = n.length, o = n.shift(), r = dt._queueHooks(t, e);
                "inprogress" === o && (o = n.shift(), i--), o && ("fx" === e && n.unshift("inprogress"), delete r.stop, o.call(t, function () {
                    dt.dequeue(t, e)
                }, r)), !i && r && r.empty.fire()
            }, _queueHooks: function (t, e) {
                var n = e + "queueHooks";
                return It.get(t, n) || It.access(t, n, {
                        empty: dt.Callbacks("once memory").add(function () {
                            It.remove(t, [e + "queue", n])
                        })
                    })
            }
        }), dt.fn.extend({
            queue: function (t, e) {
                var n = 2;
                return "string" != typeof t && (e = t, t = "fx", n--), arguments.length < n ? dt.queue(this[0], t) : void 0 === e ? this : this.each(function () {
                    var n = dt.queue(this, t, e);
                    dt._queueHooks(this, t), "fx" === t && "inprogress" !== n[0] && dt.dequeue(this, t)
                })
            }, dequeue: function (t) {
                return this.each(function () {
                    dt.dequeue(this, t)
                })
            }, clearQueue: function (t) {
                return this.queue(t || "fx", [])
            }, promise: function (t, e) {
                var n, i = 1, o = dt.Deferred(), r = this, s = this.length, a = function () {
                    --i || o.resolveWith(r, [r])
                };
                for ("string" != typeof t && (e = t, t = void 0), t = t || "fx"; s--;)(n = It.get(r[s], t + "queueHooks")) && n.empty && (i++, n.empty.add(a));
                return a(), o.promise(e)
            }
        });
        var Pt = /[+-]?(?:\d*\.|)\d+(?:[eE][+-]?\d+|)/.source, Ht = new RegExp("^(?:([+-])=|)(" + Pt + ")([a-z%]*)$", "i"), Ft = ["Top", "Right", "Bottom", "Left"], Wt = function (t, e) {
            return "none" === (t = e || t).style.display || "" === t.style.display && dt.contains(t.ownerDocument, t) && "none" === dt.css(t, "display")
        }, Mt = function (t, e, n, i) {
            var o, r, s = {};
            for (r in e)s[r] = t.style[r], t.style[r] = e[r];
            o = n.apply(t, i || []);
            for (r in e)t.style[r] = s[r];
            return o
        }, Bt = {};
        dt.fn.extend({
            show: function () {
                return v(this, !0)
            }, hide: function () {
                return v(this)
            }, toggle: function (t) {
                return "boolean" == typeof t ? t ? this.show() : this.hide() : this.each(function () {
                    Wt(this) ? dt(this).show() : dt(this).hide()
                })
            }
        });
        var Ut = /^(?:checkbox|radio)$/i, _t = /<([a-z][^\/\0>\x20\t\r\n\f]+)/i, zt = /^$|\/(?:java|ecma)script/i, Vt = {option: [1, "<select multiple='multiple'>", "</select>"], thead: [1, "<table>", "</table>"], col: [2, "<table><colgroup>", "</colgroup></table>"], tr: [2, "<table><tbody>", "</tbody></table>"], td: [3, "<table><tbody><tr>", "</tr></tbody></table>"], _default: [0, "", ""]};
        Vt.optgroup = Vt.option, Vt.tbody = Vt.tfoot = Vt.colgroup = Vt.caption = Vt.thead, Vt.th = Vt.td;
        var Qt = /<|&#?\w+;/;
        !function () {
            var t = et.createDocumentFragment().appendChild(et.createElement("div")), e = et.createElement("input");
            e.setAttribute("type", "radio"), e.setAttribute("checked", "checked"), e.setAttribute("name", "t"), t.appendChild(e), pt.checkClone = t.cloneNode(!0).cloneNode(!0).lastChild.checked, t.innerHTML = "<textarea>x</textarea>", pt.noCloneChecked = !!t.cloneNode(!0).lastChild.defaultValue
        }();
        var Xt = et.documentElement, Gt = /^key/, Yt = /^(?:mouse|pointer|contextmenu|drag|drop)|click/, Jt = /^([^.]*)(?:\.(.+)|)/;
        dt.event = {
            global: {}, add: function (t, e, n, i, o) {
                var r, s, a, l, u, c, f, p, d, h, g, m = It.get(t);
                if (m)for (n.handler && (n = (r = n).handler, o = r.selector), o && dt.find.matchesSelector(Xt, o), n.guid || (n.guid = dt.guid++), (l = m.events) || (l = m.events = {}), (s = m.handle) || (s = m.handle = function (e) {
                    return void 0 !== dt && dt.event.triggered !== e.type ? dt.event.dispatch.apply(t, arguments) : void 0
                }), u = (e = (e || "").match(Dt) || [""]).length; u--;)d = g = (a = Jt.exec(e[u]) || [])[1], h = (a[2] || "").split(".").sort(), d && (f = dt.event.special[d] || {}, d = (o ? f.delegateType : f.bindType) || d, f = dt.event.special[d] || {}, c = dt.extend({type: d, origType: g, data: i, handler: n, guid: n.guid, selector: o, needsContext: o && dt.expr.match.needsContext.test(o), namespace: h.join(".")}, r), (p = l[d]) || ((p = l[d] = []).delegateCount = 0, f.setup && !1 !== f.setup.call(t, i, h, s) || t.addEventListener && t.addEventListener(d, s)), f.add && (f.add.call(t, c), c.handler.guid || (c.handler.guid = n.guid)), o ? p.splice(p.delegateCount++, 0, c) : p.push(c), dt.event.global[d] = !0)
            }, remove: function (t, e, n, i, o) {
                var r, s, a, l, u, c, f, p, d, h, g, m = It.hasData(t) && It.get(t);
                if (m && (l = m.events)) {
                    for (u = (e = (e || "").match(Dt) || [""]).length; u--;)if (a = Jt.exec(e[u]) || [], d = g = a[1], h = (a[2] || "").split(".").sort(), d) {
                        for (f = dt.event.special[d] || {}, p = l[d = (i ? f.delegateType : f.bindType) || d] || [], a = a[2] && new RegExp("(^|\\.)" + h.join("\\.(?:.*\\.|)") + "(\\.|$)"), s = r = p.length; r--;)c = p[r], !o && g !== c.origType || n && n.guid !== c.guid || a && !a.test(c.namespace) || i && i !== c.selector && ("**" !== i || !c.selector) || (p.splice(r, 1), c.selector && p.delegateCount--, f.remove && f.remove.call(t, c));
                        s && !p.length && (f.teardown && !1 !== f.teardown.call(t, h, m.handle) || dt.removeEvent(t, d, m.handle), delete l[d])
                    } else for (d in l)dt.event.remove(t, d + e[u], n, i, !0);
                    dt.isEmptyObject(l) && It.remove(t, "handle events")
                }
            }, dispatch: function (t) {
                var e, n, i, o, r, s, a = dt.event.fix(t), l = new Array(arguments.length), u = (It.get(this, "events") || {})[a.type] || [], c = dt.event.special[a.type] || {};
                for (l[0] = a, e = 1; e < arguments.length; e++)l[e] = arguments[e];
                if (a.delegateTarget = this, !c.preDispatch || !1 !== c.preDispatch.call(this, a)) {
                    for (s = dt.event.handlers.call(this, a, u), e = 0; (o = s[e++]) && !a.isPropagationStopped();)for (a.currentTarget = o.elem, n = 0; (r = o.handlers[n++]) && !a.isImmediatePropagationStopped();)a.rnamespace && !a.rnamespace.test(r.namespace) || (a.handleObj = r, a.data = r.data, void 0 !== (i = ((dt.event.special[r.origType] || {}).handle || r.handler).apply(o.elem, l)) && !1 === (a.result = i) && (a.preventDefault(), a.stopPropagation()));
                    return c.postDispatch && c.postDispatch.call(this, a), a.result
                }
            }, handlers: function (t, e) {
                var n, i, o, r, s, a = [], l = e.delegateCount, u = t.target;
                if (l && u.nodeType && !("click" === t.type && t.button >= 1))for (; u !== this; u = u.parentNode || this)if (1 === u.nodeType && ("click" !== t.type || !0 !== u.disabled)) {
                    for (r = [], s = {}, n = 0; n < l; n++)void 0 === s[o = (i = e[n]).selector + " "] && (s[o] = i.needsContext ? dt(o, this).index(u) > -1 : dt.find(o, this, null, [u]).length), s[o] && r.push(i);
                    r.length && a.push({elem: u, handlers: r})
                }
                return u = this, l < e.length && a.push({elem: u, handlers: e.slice(l)}), a
            }, addProp: function (t, e) {
                Object.defineProperty(dt.Event.prototype, t, {
                    enumerable: !0, configurable: !0, get: dt.isFunction(e) ? function () {
                        if (this.originalEvent)return e(this.originalEvent)
                    } : function () {
                        if (this.originalEvent)return this.originalEvent[t]
                    }, set: function (e) {
                        Object.defineProperty(this, t, {enumerable: !0, configurable: !0, writable: !0, value: e})
                    }
                })
            }, fix: function (t) {
                return t[dt.expando] ? t : new dt.Event(t)
            }, special: {
                load: {noBubble: !0}, focus: {
                    trigger: function () {
                        if (this !== C() && this.focus)return this.focus(), !1
                    }, delegateType: "focusin"
                }, blur: {
                    trigger: function () {
                        if (this === C() && this.blur)return this.blur(), !1
                    }, delegateType: "focusout"
                }, click: {
                    trigger: function () {
                        if ("checkbox" === this.type && this.click && o(this, "input"))return this.click(), !1
                    }, _default: function (t) {
                        return o(t.target, "a")
                    }
                }, beforeunload: {
                    postDispatch: function (t) {
                        void 0 !== t.result && t.originalEvent && (t.originalEvent.returnValue = t.result)
                    }
                }
            }
        }, dt.removeEvent = function (t, e, n) {
            t.removeEventListener && t.removeEventListener(e, n)
        }, dt.Event = function (t, e) {
            if (!(this instanceof dt.Event))return new dt.Event(t, e);
            t && t.type ? (this.originalEvent = t, this.type = t.type, this.isDefaultPrevented = t.defaultPrevented || void 0 === t.defaultPrevented && !1 === t.returnValue ? w : T, this.target = t.target && 3 === t.target.nodeType ? t.target.parentNode : t.target, this.currentTarget = t.currentTarget, this.relatedTarget = t.relatedTarget) : this.type = t, e && dt.extend(this, e), this.timeStamp = t && t.timeStamp || dt.now(), this[dt.expando] = !0
        }, dt.Event.prototype = {
            constructor: dt.Event, isDefaultPrevented: T, isPropagationStopped: T, isImmediatePropagationStopped: T, isSimulated: !1, preventDefault: function () {
                var t = this.originalEvent;
                this.isDefaultPrevented = w, t && !this.isSimulated && t.preventDefault()
            }, stopPropagation: function () {
                var t = this.originalEvent;
                this.isPropagationStopped = w, t && !this.isSimulated && t.stopPropagation()
            }, stopImmediatePropagation: function () {
                var t = this.originalEvent;
                this.isImmediatePropagationStopped = w, t && !this.isSimulated && t.stopImmediatePropagation(), this.stopPropagation()
            }
        }, dt.each({
            altKey: !0, bubbles: !0, cancelable: !0, changedTouches: !0, ctrlKey: !0, detail: !0, eventPhase: !0, metaKey: !0, pageX: !0, pageY: !0, shiftKey: !0, view: !0, char: !0, charCode: !0, key: !0, keyCode: !0, button: !0, buttons: !0, clientX: !0, clientY: !0, offsetX: !0, offsetY: !0, pointerId: !0, pointerType: !0, screenX: !0, screenY: !0, targetTouches: !0, toElement: !0, touches: !0, which: function (t) {
                var e = t.button;
                return null == t.which && Gt.test(t.type) ? null != t.charCode ? t.charCode : t.keyCode : !t.which && void 0 !== e && Yt.test(t.type) ? 1 & e ? 1 : 2 & e ? 3 : 4 & e ? 2 : 0 : t.which
            }
        }, dt.event.addProp), dt.each({mouseenter: "mouseover", mouseleave: "mouseout", pointerenter: "pointerover", pointerleave: "pointerout"}, function (t, e) {
            dt.event.special[t] = {
                delegateType: e, bindType: e, handle: function (t) {
                    var n, i = this, o = t.relatedTarget, r = t.handleObj;
                    return o && (o === i || dt.contains(i, o)) || (t.type = r.origType, n = r.handler.apply(this, arguments), t.type = e), n
                }
            }
        }), dt.fn.extend({
            on: function (t, e, n, i) {
                return E(this, t, e, n, i)
            }, one: function (t, e, n, i) {
                return E(this, t, e, n, i, 1)
            }, off: function (t, e, n) {
                var i, o;
                if (t && t.preventDefault && t.handleObj)return i = t.handleObj, dt(t.delegateTarget).off(i.namespace ? i.origType + "." + i.namespace : i.origType, i.selector, i.handler), this;
                if ("object" == typeof t) {
                    for (o in t)this.off(o, e, t[o]);
                    return this
                }
                return !1 !== e && "function" != typeof e || (n = e, e = void 0), !1 === n && (n = T), this.each(function () {
                    dt.event.remove(this, t, n, e)
                })
            }
        });
        var Kt = /<(?!area|br|col|embed|hr|img|input|link|meta|param)(([a-z][^\/\0>\x20\t\r\n\f]*)[^>]*)\/>/gi, Zt = /<script|<style|<link/i, te = /checked\s*(?:[^=]|=\s*.checked.)/i, ee = /^true\/(.*)/, ne = /^\s*<!(?:\[CDATA\[|--)|(?:\]\]|--)>\s*$/g;
        dt.extend({
            htmlPrefilter: function (t) {
                return t.replace(Kt, "<$1></$2>")
            }, clone: function (t, e, n) {
                var i, o, r, s, a = t.cloneNode(!0), l = dt.contains(t.ownerDocument, t);
                if (!(pt.noCloneChecked || 1 !== t.nodeType && 11 !== t.nodeType || dt.isXMLDoc(t)))for (s = y(a), i = 0, o = (r = y(t)).length; i < o; i++)N(r[i], s[i]);
                if (e)if (n)for (r = r || y(t), s = s || y(a), i = 0, o = r.length; i < o; i++)D(r[i], s[i]); else D(t, a);
                return (s = y(a, "script")).length > 0 && b(s, !l && y(t, "script")), a
            }, cleanData: function (t) {
                for (var e, n, i, o = dt.event.special, r = 0; void 0 !== (n = t[r]); r++)if (Ot(n)) {
                    if (e = n[It.expando]) {
                        if (e.events)for (i in e.events)o[i] ? dt.event.remove(n, i) : dt.removeEvent(n, i, e.handle);
                        n[It.expando] = void 0
                    }
                    n[Lt.expando] && (n[Lt.expando] = void 0)
                }
            }
        }), dt.fn.extend({
            detach: function (t) {
                return j(this, t, !0)
            }, remove: function (t) {
                return j(this, t)
            }, text: function (t) {
                return jt(this, function (t) {
                    return void 0 === t ? dt.text(this) : this.empty().each(function () {
                        1 !== this.nodeType && 11 !== this.nodeType && 9 !== this.nodeType || (this.textContent = t)
                    })
                }, null, t, arguments.length)
            }, append: function () {
                return A(this, arguments, function (t) {
                    1 !== this.nodeType && 11 !== this.nodeType && 9 !== this.nodeType || S(this, t).appendChild(t)
                })
            }, prepend: function () {
                return A(this, arguments, function (t) {
                    if (1 === this.nodeType || 11 === this.nodeType || 9 === this.nodeType) {
                        var e = S(this, t);
                        e.insertBefore(t, e.firstChild)
                    }
                })
            }, before: function () {
                return A(this, arguments, function (t) {
                    this.parentNode && this.parentNode.insertBefore(t, this)
                })
            }, after: function () {
                return A(this, arguments, function (t) {
                    this.parentNode && this.parentNode.insertBefore(t, this.nextSibling)
                })
            }, empty: function () {
                for (var t, e = 0; null != (t = this[e]); e++)1 === t.nodeType && (dt.cleanData(y(t, !1)), t.textContent = "");
                return this
            }, clone: function (t, e) {
                return t = null != t && t, e = null == e ? t : e, this.map(function () {
                    return dt.clone(this, t, e)
                })
            }, html: function (t) {
                return jt(this, function (t) {
                    var e = this[0] || {}, n = 0, i = this.length;
                    if (void 0 === t && 1 === e.nodeType)return e.innerHTML;
                    if ("string" == typeof t && !Zt.test(t) && !Vt[(_t.exec(t) || ["", ""])[1].toLowerCase()]) {
                        t = dt.htmlPrefilter(t);
                        try {
                            for (; n < i; n++)1 === (e = this[n] || {}).nodeType && (dt.cleanData(y(e, !1)), e.innerHTML = t);
                            e = 0
                        } catch (t) {
                        }
                    }
                    e && this.empty().append(t)
                }, null, t, arguments.length)
            }, replaceWith: function () {
                var t = [];
                return A(this, arguments, function (e) {
                    var n = this.parentNode;
                    dt.inArray(this, t) < 0 && (dt.cleanData(y(this)), n && n.replaceChild(e, this))
                }, t)
            }
        }), dt.each({appendTo: "append", prependTo: "prepend", insertBefore: "before", insertAfter: "after", replaceAll: "replaceWith"}, function (t, e) {
            dt.fn[t] = function (t) {
                for (var n, i = [], o = dt(t), r = o.length - 1, s = 0; s <= r; s++)n = s === r ? this : this.clone(!0), dt(o[s])[e](n), rt.apply(i, n.get());
                return this.pushStack(i)
            }
        });
        var ie = /^margin/, oe = new RegExp("^(" + Pt + ")(?!px)[a-z%]+$", "i"), re = function (e) {
            var n = e.ownerDocument.defaultView;
            return n && n.opener || (n = t), n.getComputedStyle(e)
        };
        !function () {
            function e() {
                if (a) {
                    a.style.cssText = "box-sizing:border-box;position:relative;display:block;margin:auto;border:1px;padding:1px;top:1%;width:50%", a.innerHTML = "", Xt.appendChild(s);
                    var e = t.getComputedStyle(a);
                    n = "1%" !== e.top, r = "2px" === e.marginLeft, i = "4px" === e.width, a.style.marginRight = "50%", o = "4px" === e.marginRight, Xt.removeChild(s), a = null
                }
            }

            var n, i, o, r, s = et.createElement("div"), a = et.createElement("div");
            a.style && (a.style.backgroundClip = "content-box", a.cloneNode(!0).style.backgroundClip = "", pt.clearCloneStyle = "content-box" === a.style.backgroundClip, s.style.cssText = "border:0;width:8px;height:0;top:0;left:-9999px;padding:0;margin-top:1px;position:absolute", s.appendChild(a), dt.extend(pt, {
                pixelPosition: function () {
                    return e(), n
                }, boxSizingReliable: function () {
                    return e(), i
                }, pixelMarginRight: function () {
                    return e(), o
                }, reliableMarginLeft: function () {
                    return e(), r
                }
            }))
        }();
        var se = /^(none|table(?!-c[ea]).+)/, ae = /^--/, le = {position: "absolute", visibility: "hidden", display: "block"}, ue = {letterSpacing: "0", fontWeight: "400"}, ce = ["Webkit", "Moz", "ms"], fe = et.createElement("div").style;
        dt.extend({
            cssHooks: {
                opacity: {
                    get: function (t, e) {
                        if (e) {
                            var n = O(t, "opacity");
                            return "" === n ? "1" : n
                        }
                    }
                }
            }, cssNumber: {animationIterationCount: !0, columnCount: !0, fillOpacity: !0, flexGrow: !0, flexShrink: !0, fontWeight: !0, lineHeight: !0, opacity: !0, order: !0, orphans: !0, widows: !0, zIndex: !0, zoom: !0}, cssProps: {float: "cssFloat"}, style: function (t, e, n, i) {
                if (t && 3 !== t.nodeType && 8 !== t.nodeType && t.style) {
                    var o, r, s, a = dt.camelCase(e), l = ae.test(e), u = t.style;
                    if (l || (e = R(a)), s = dt.cssHooks[e] || dt.cssHooks[a], void 0 === n)return s && "get" in s && void 0 !== (o = s.get(t, !1, i)) ? o : u[e];
                    "string" == (r = typeof n) && (o = Ht.exec(n)) && o[1] && (n = g(t, e, o), r = "number"), null != n && n == n && ("number" === r && (n += o && o[3] || (dt.cssNumber[a] ? "" : "px")), pt.clearCloneStyle || "" !== n || 0 !== e.indexOf("background") || (u[e] = "inherit"), s && "set" in s && void 0 === (n = s.set(t, n, i)) || (l ? u.setProperty(e, n) : u[e] = n))
                }
            }, css: function (t, e, n, i) {
                var o, r, s, a = dt.camelCase(e);
                return ae.test(e) || (e = R(a)), (s = dt.cssHooks[e] || dt.cssHooks[a]) && "get" in s && (o = s.get(t, !0, n)), void 0 === o && (o = O(t, e, i)), "normal" === o && e in ue && (o = ue[e]), "" === n || n ? (r = parseFloat(o), !0 === n || isFinite(r) ? r || 0 : o) : o
            }
        }), dt.each(["height", "width"], function (t, e) {
            dt.cssHooks[e] = {
                get: function (t, n, i) {
                    if (n)return !se.test(dt.css(t, "display")) || t.getClientRects().length && t.getBoundingClientRect().width ? H(t, e, i) : Mt(t, le, function () {
                        return H(t, e, i)
                    })
                }, set: function (t, n, i) {
                    var o, r = i && re(t), s = i && P(t, e, i, "border-box" === dt.css(t, "boxSizing", !1, r), r);
                    return s && (o = Ht.exec(n)) && "px" !== (o[3] || "px") && (t.style[e] = n, n = dt.css(t, e)), q(0, n, s)
                }
            }
        }), dt.cssHooks.marginLeft = I(pt.reliableMarginLeft, function (t, e) {
            if (e)return (parseFloat(O(t, "marginLeft")) || t.getBoundingClientRect().left - Mt(t, {marginLeft: 0}, function () {
                    return t.getBoundingClientRect().left
                })) + "px"
        }), dt.each({margin: "", padding: "", border: "Width"}, function (t, e) {
            dt.cssHooks[t + e] = {
                expand: function (n) {
                    for (var i = 0, o = {}, r = "string" == typeof n ? n.split(" ") : [n]; i < 4; i++)o[t + Ft[i] + e] = r[i] || r[i - 2] || r[0];
                    return o
                }
            }, ie.test(t) || (dt.cssHooks[t + e].set = q)
        }), dt.fn.extend({
            css: function (t, e) {
                return jt(this, function (t, e, n) {
                    var i, o, r = {}, s = 0;
                    if (Array.isArray(e)) {
                        for (i = re(t), o = e.length; s < o; s++)r[e[s]] = dt.css(t, e[s], !1, i);
                        return r
                    }
                    return void 0 !== n ? dt.style(t, e, n) : dt.css(t, e)
                }, t, e, arguments.length > 1)
            }
        }), dt.Tween = F, F.prototype = {
            constructor: F, init: function (t, e, n, i, o, r) {
                this.elem = t, this.prop = n, this.easing = o || dt.easing._default, this.options = e, this.start = this.now = this.cur(), this.end = i, this.unit = r || (dt.cssNumber[n] ? "" : "px")
            }, cur: function () {
                var t = F.propHooks[this.prop];
                return t && t.get ? t.get(this) : F.propHooks._default.get(this)
            }, run: function (t) {
                var e, n = F.propHooks[this.prop];
                return this.options.duration ? this.pos = e = dt.easing[this.easing](t, this.options.duration * t, 0, 1, this.options.duration) : this.pos = e = t, this.now = (this.end - this.start) * e + this.start, this.options.step && this.options.step.call(this.elem, this.now, this), n && n.set ? n.set(this) : F.propHooks._default.set(this), this
            }
        }, F.prototype.init.prototype = F.prototype, F.propHooks = {
            _default: {
                get: function (t) {
                    var e;
                    return 1 !== t.elem.nodeType || null != t.elem[t.prop] && null == t.elem.style[t.prop] ? t.elem[t.prop] : (e = dt.css(t.elem, t.prop, "")) && "auto" !== e ? e : 0
                }, set: function (t) {
                    dt.fx.step[t.prop] ? dt.fx.step[t.prop](t) : 1 !== t.elem.nodeType || null == t.elem.style[dt.cssProps[t.prop]] && !dt.cssHooks[t.prop] ? t.elem[t.prop] = t.now : dt.style(t.elem, t.prop, t.now + t.unit)
                }
            }
        }, F.propHooks.scrollTop = F.propHooks.scrollLeft = {
            set: function (t) {
                t.elem.nodeType && t.elem.parentNode && (t.elem[t.prop] = t.now)
            }
        }, dt.easing = {
            linear: function (t) {
                return t
            }, swing: function (t) {
                return .5 - Math.cos(t * Math.PI) / 2
            }, _default: "swing"
        }, dt.fx = F.prototype.init, dt.fx.step = {};
        var pe, de, he = /^(?:toggle|show|hide)$/, ge = /queueHooks$/;
        dt.Animation = dt.extend(z, {
            tweeners: {
                "*": [function (t, e) {
                    var n = this.createTween(t, e);
                    return g(n.elem, t, Ht.exec(e), n), n
                }]
            }, tweener: function (t, e) {
                dt.isFunction(t) ? (e = t, t = ["*"]) : t = t.match(Dt);
                for (var n, i = 0, o = t.length; i < o; i++)n = t[i], z.tweeners[n] = z.tweeners[n] || [], z.tweeners[n].unshift(e)
            }, prefilters: [function (t, e, n) {
                var i, o, r, s, a, l, u, c, f = "width" in e || "height" in e, p = this, d = {}, h = t.style, g = t.nodeType && Wt(t), m = It.get(t, "fxshow");
                n.queue || (null == (s = dt._queueHooks(t, "fx")).unqueued && (s.unqueued = 0, a = s.empty.fire, s.empty.fire = function () {
                    s.unqueued || a()
                }), s.unqueued++, p.always(function () {
                    p.always(function () {
                        s.unqueued--, dt.queue(t, "fx").length || s.empty.fire()
                    })
                }));
                for (i in e)if (o = e[i], he.test(o)) {
                    if (delete e[i], r = r || "toggle" === o, o === (g ? "hide" : "show")) {
                        if ("show" !== o || !m || void 0 === m[i])continue;
                        g = !0
                    }
                    d[i] = m && m[i] || dt.style(t, i)
                }
                if ((l = !dt.isEmptyObject(e)) || !dt.isEmptyObject(d)) {
                    f && 1 === t.nodeType && (n.overflow = [h.overflow, h.overflowX, h.overflowY], null == (u = m && m.display) && (u = It.get(t, "display")), "none" === (c = dt.css(t, "display")) && (u ? c = u : (v([t], !0), u = t.style.display || u, c = dt.css(t, "display"), v([t]))), ("inline" === c || "inline-block" === c && null != u) && "none" === dt.css(t, "float") && (l || (p.done(function () {
                        h.display = u
                    }), null == u && (c = h.display, u = "none" === c ? "" : c)), h.display = "inline-block")), n.overflow && (h.overflow = "hidden", p.always(function () {
                        h.overflow = n.overflow[0], h.overflowX = n.overflow[1], h.overflowY = n.overflow[2]
                    })), l = !1;
                    for (i in d)l || (m ? "hidden" in m && (g = m.hidden) : m = It.access(t, "fxshow", {display: u}), r && (m.hidden = !g), g && v([t], !0), p.done(function () {
                        g || v([t]), It.remove(t, "fxshow");
                        for (i in d)dt.style(t, i, d[i])
                    })), l = U(g ? m[i] : 0, i, p), i in m || (m[i] = l.start, g && (l.end = l.start, l.start = 0))
                }
            }], prefilter: function (t, e) {
                e ? z.prefilters.unshift(t) : z.prefilters.push(t)
            }
        }), dt.speed = function (t, e, n) {
            var i = t && "object" == typeof t ? dt.extend({}, t) : {complete: n || !n && e || dt.isFunction(t) && t, duration: t, easing: n && e || e && !dt.isFunction(e) && e};
            return dt.fx.off ? i.duration = 0 : "number" != typeof i.duration && (i.duration in dt.fx.speeds ? i.duration = dt.fx.speeds[i.duration] : i.duration = dt.fx.speeds._default), null != i.queue && !0 !== i.queue || (i.queue = "fx"), i.old = i.complete, i.complete = function () {
                dt.isFunction(i.old) && i.old.call(this), i.queue && dt.dequeue(this, i.queue)
            }, i
        }, dt.fn.extend({
            fadeTo: function (t, e, n, i) {
                return this.filter(Wt).css("opacity", 0).show().end().animate({opacity: e}, t, n, i)
            }, animate: function (t, e, n, i) {
                var o = dt.isEmptyObject(t), r = dt.speed(e, n, i), s = function () {
                    var e = z(this, dt.extend({}, t), r);
                    (o || It.get(this, "finish")) && e.stop(!0)
                };
                return s.finish = s, o || !1 === r.queue ? this.each(s) : this.queue(r.queue, s)
            }, stop: function (t, e, n) {
                var i = function (t) {
                    var e = t.stop;
                    delete t.stop, e(n)
                };
                return "string" != typeof t && (n = e, e = t, t = void 0), e && !1 !== t && this.queue(t || "fx", []), this.each(function () {
                    var e = !0, o = null != t && t + "queueHooks", r = dt.timers, s = It.get(this);
                    if (o)s[o] && s[o].stop && i(s[o]); else for (o in s)s[o] && s[o].stop && ge.test(o) && i(s[o]);
                    for (o = r.length; o--;)r[o].elem !== this || null != t && r[o].queue !== t || (r[o].anim.stop(n), e = !1, r.splice(o, 1));
                    !e && n || dt.dequeue(this, t)
                })
            }, finish: function (t) {
                return !1 !== t && (t = t || "fx"), this.each(function () {
                    var e, n = It.get(this), i = n[t + "queue"], o = n[t + "queueHooks"], r = dt.timers, s = i ? i.length : 0;
                    for (n.finish = !0, dt.queue(this, t, []), o && o.stop && o.stop.call(this, !0), e = r.length; e--;)r[e].elem === this && r[e].queue === t && (r[e].anim.stop(!0), r.splice(e, 1));
                    for (e = 0; e < s; e++)i[e] && i[e].finish && i[e].finish.call(this);
                    delete n.finish
                })
            }
        }), dt.each(["toggle", "show", "hide"], function (t, e) {
            var n = dt.fn[e];
            dt.fn[e] = function (t, i, o) {
                return null == t || "boolean" == typeof t ? n.apply(this, arguments) : this.animate(B(e, !0), t, i, o)
            }
        }), dt.each({slideDown: B("show"), slideUp: B("hide"), slideToggle: B("toggle"), fadeIn: {opacity: "show"}, fadeOut: {opacity: "hide"}, fadeToggle: {opacity: "toggle"}}, function (t, e) {
            dt.fn[t] = function (t, n, i) {
                return this.animate(e, t, n, i)
            }
        }), dt.timers = [], dt.fx.tick = function () {
            var t, e = 0, n = dt.timers;
            for (pe = dt.now(); e < n.length; e++)(t = n[e])() || n[e] !== t || n.splice(e--, 1);
            n.length || dt.fx.stop(), pe = void 0
        }, dt.fx.timer = function (t) {
            dt.timers.push(t), dt.fx.start()
        }, dt.fx.interval = 13, dt.fx.start = function () {
            de || (de = !0, W())
        }, dt.fx.stop = function () {
            de = null
        }, dt.fx.speeds = {slow: 600, fast: 200, _default: 400}, dt.fn.delay = function (e, n) {
            return e = dt.fx ? dt.fx.speeds[e] || e : e, n = n || "fx", this.queue(n, function (n, i) {
                var o = t.setTimeout(n, e);
                i.stop = function () {
                    t.clearTimeout(o)
                }
            })
        }, function () {
            var t = et.createElement("input"), e = et.createElement("select").appendChild(et.createElement("option"));
            t.type = "checkbox", pt.checkOn = "" !== t.value, pt.optSelected = e.selected, (t = et.createElement("input")).value = "t", t.type = "radio", pt.radioValue = "t" === t.value
        }();
        var me, ve = dt.expr.attrHandle;
        dt.fn.extend({
            attr: function (t, e) {
                return jt(this, dt.attr, t, e, arguments.length > 1)
            }, removeAttr: function (t) {
                return this.each(function () {
                    dt.removeAttr(this, t)
                })
            }
        }), dt.extend({
            attr: function (t, e, n) {
                var i, o, r = t.nodeType;
                if (3 !== r && 8 !== r && 2 !== r)return void 0 === t.getAttribute ? dt.prop(t, e, n) : (1 === r && dt.isXMLDoc(t) || (o = dt.attrHooks[e.toLowerCase()] || (dt.expr.match.bool.test(e) ? me : void 0)), void 0 !== n ? null === n ? void dt.removeAttr(t, e) : o && "set" in o && void 0 !== (i = o.set(t, n, e)) ? i : (t.setAttribute(e, n + ""), n) : o && "get" in o && null !== (i = o.get(t, e)) ? i : null == (i = dt.find.attr(t, e)) ? void 0 : i)
            }, attrHooks: {
                type: {
                    set: function (t, e) {
                        if (!pt.radioValue && "radio" === e && o(t, "input")) {
                            var n = t.value;
                            return t.setAttribute("type", e), n && (t.value = n), e
                        }
                    }
                }
            }, removeAttr: function (t, e) {
                var n, i = 0, o = e && e.match(Dt);
                if (o && 1 === t.nodeType)for (; n = o[i++];)t.removeAttribute(n)
            }
        }), me = {
            set: function (t, e, n) {
                return !1 === e ? dt.removeAttr(t, n) : t.setAttribute(n, n), n
            }
        }, dt.each(dt.expr.match.bool.source.match(/\w+/g), function (t, e) {
            var n = ve[e] || dt.find.attr;
            ve[e] = function (t, e, i) {
                var o, r, s = e.toLowerCase();
                return i || (r = ve[s], ve[s] = o, o = null != n(t, e, i) ? s : null, ve[s] = r), o
            }
        });
        var ye = /^(?:input|select|textarea|button)$/i, be = /^(?:a|area)$/i;
        dt.fn.extend({
            prop: function (t, e) {
                return jt(this, dt.prop, t, e, arguments.length > 1)
            }, removeProp: function (t) {
                return this.each(function () {
                    delete this[dt.propFix[t] || t]
                })
            }
        }), dt.extend({
            prop: function (t, e, n) {
                var i, o, r = t.nodeType;
                if (3 !== r && 8 !== r && 2 !== r)return 1 === r && dt.isXMLDoc(t) || (e = dt.propFix[e] || e, o = dt.propHooks[e]), void 0 !== n ? o && "set" in o && void 0 !== (i = o.set(t, n, e)) ? i : t[e] = n : o && "get" in o && null !== (i = o.get(t, e)) ? i : t[e]
            }, propHooks: {
                tabIndex: {
                    get: function (t) {
                        var e = dt.find.attr(t, "tabindex");
                        return e ? parseInt(e, 10) : ye.test(t.nodeName) || be.test(t.nodeName) && t.href ? 0 : -1
                    }
                }
            }, propFix: {for: "htmlFor", class: "className"}
        }), pt.optSelected || (dt.propHooks.selected = {
            get: function (t) {
                var e = t.parentNode;
                return e && e.parentNode && e.parentNode.selectedIndex, null
            }, set: function (t) {
                var e = t.parentNode;
                e && (e.selectedIndex, e.parentNode && e.parentNode.selectedIndex)
            }
        }), dt.each(["tabIndex", "readOnly", "maxLength", "cellSpacing", "cellPadding", "rowSpan", "colSpan", "useMap", "frameBorder", "contentEditable"], function () {
            dt.propFix[this.toLowerCase()] = this
        }), dt.fn.extend({
            addClass: function (t) {
                var e, n, i, o, r, s, a, l = 0;
                if (dt.isFunction(t))return this.each(function (e) {
                    dt(this).addClass(t.call(this, e, Q(this)))
                });
                if ("string" == typeof t && t)for (e = t.match(Dt) || []; n = this[l++];)if (o = Q(n), i = 1 === n.nodeType && " " + V(o) + " ") {
                    for (s = 0; r = e[s++];)i.indexOf(" " + r + " ") < 0 && (i += r + " ");
                    o !== (a = V(i)) && n.setAttribute("class", a)
                }
                return this
            }, removeClass: function (t) {
                var e, n, i, o, r, s, a, l = 0;
                if (dt.isFunction(t))return this.each(function (e) {
                    dt(this).removeClass(t.call(this, e, Q(this)))
                });
                if (!arguments.length)return this.attr("class", "");
                if ("string" == typeof t && t)for (e = t.match(Dt) || []; n = this[l++];)if (o = Q(n), i = 1 === n.nodeType && " " + V(o) + " ") {
                    for (s = 0; r = e[s++];)for (; i.indexOf(" " + r + " ") > -1;)i = i.replace(" " + r + " ", " ");
                    o !== (a = V(i)) && n.setAttribute("class", a)
                }
                return this
            }, toggleClass: function (t, e) {
                var n = typeof t;
                return "boolean" == typeof e && "string" === n ? e ? this.addClass(t) : this.removeClass(t) : dt.isFunction(t) ? this.each(function (n) {
                    dt(this).toggleClass(t.call(this, n, Q(this), e), e)
                }) : this.each(function () {
                    var e, i, o, r;
                    if ("string" === n)for (i = 0, o = dt(this), r = t.match(Dt) || []; e = r[i++];)o.hasClass(e) ? o.removeClass(e) : o.addClass(e); else void 0 !== t && "boolean" !== n || ((e = Q(this)) && It.set(this, "__className__", e), this.setAttribute && this.setAttribute("class", e || !1 === t ? "" : It.get(this, "__className__") || ""))
                })
            }, hasClass: function (t) {
                var e, n, i = 0;
                for (e = " " + t + " "; n = this[i++];)if (1 === n.nodeType && (" " + V(Q(n)) + " ").indexOf(e) > -1)return !0;
                return !1
            }
        });
        var xe = /\r/g;
        dt.fn.extend({
            val: function (t) {
                var e, n, i, o = this[0];
                {
                    if (arguments.length)return i = dt.isFunction(t), this.each(function (n) {
                        var o;
                        1 === this.nodeType && (null == (o = i ? t.call(this, n, dt(this).val()) : t) ? o = "" : "number" == typeof o ? o += "" : Array.isArray(o) && (o = dt.map(o, function (t) {
                            return null == t ? "" : t + ""
                        })), (e = dt.valHooks[this.type] || dt.valHooks[this.nodeName.toLowerCase()]) && "set" in e && void 0 !== e.set(this, o, "value") || (this.value = o))
                    });
                    if (o)return (e = dt.valHooks[o.type] || dt.valHooks[o.nodeName.toLowerCase()]) && "get" in e && void 0 !== (n = e.get(o, "value")) ? n : "string" == typeof(n = o.value) ? n.replace(xe, "") : null == n ? "" : n
                }
            }
        }), dt.extend({
            valHooks: {
                option: {
                    get: function (t) {
                        var e = dt.find.attr(t, "value");
                        return null != e ? e : V(dt.text(t))
                    }
                }, select: {
                    get: function (t) {
                        var e, n, i, r = t.options, s = t.selectedIndex, a = "select-one" === t.type, l = a ? null : [], u = a ? s + 1 : r.length;
                        for (i = s < 0 ? u : a ? s : 0; i < u; i++)if (((n = r[i]).selected || i === s) && !n.disabled && (!n.parentNode.disabled || !o(n.parentNode, "optgroup"))) {
                            if (e = dt(n).val(), a)return e;
                            l.push(e)
                        }
                        return l
                    }, set: function (t, e) {
                        for (var n, i, o = t.options, r = dt.makeArray(e), s = o.length; s--;)((i = o[s]).selected = dt.inArray(dt.valHooks.option.get(i), r) > -1) && (n = !0);
                        return n || (t.selectedIndex = -1), r
                    }
                }
            }
        }), dt.each(["radio", "checkbox"], function () {
            dt.valHooks[this] = {
                set: function (t, e) {
                    if (Array.isArray(e))return t.checked = dt.inArray(dt(t).val(), e) > -1
                }
            }, pt.checkOn || (dt.valHooks[this].get = function (t) {
                return null === t.getAttribute("value") ? "on" : t.value
            })
        });
        var we = /^(?:focusinfocus|focusoutblur)$/;
        dt.extend(dt.event, {
            trigger: function (e, n, i, o) {
                var r, s, a, l, u, c, f, p = [i || et], d = ut.call(e, "type") ? e.type : e, h = ut.call(e, "namespace") ? e.namespace.split(".") : [];
                if (s = a = i = i || et, 3 !== i.nodeType && 8 !== i.nodeType && !we.test(d + dt.event.triggered) && (d.indexOf(".") > -1 && (d = (h = d.split(".")).shift(), h.sort()), u = d.indexOf(":") < 0 && "on" + d, e = e[dt.expando] ? e : new dt.Event(d, "object" == typeof e && e), e.isTrigger = o ? 2 : 3, e.namespace = h.join("."), e.rnamespace = e.namespace ? new RegExp("(^|\\.)" + h.join("\\.(?:.*\\.|)") + "(\\.|$)") : null, e.result = void 0, e.target || (e.target = i), n = null == n ? [e] : dt.makeArray(n, [e]), f = dt.event.special[d] || {}, o || !f.trigger || !1 !== f.trigger.apply(i, n))) {
                    if (!o && !f.noBubble && !dt.isWindow(i)) {
                        for (l = f.delegateType || d, we.test(l + d) || (s = s.parentNode); s; s = s.parentNode)p.push(s), a = s;
                        a === (i.ownerDocument || et) && p.push(a.defaultView || a.parentWindow || t)
                    }
                    for (r = 0; (s = p[r++]) && !e.isPropagationStopped();)e.type = r > 1 ? l : f.bindType || d, (c = (It.get(s, "events") || {})[e.type] && It.get(s, "handle")) && c.apply(s, n), (c = u && s[u]) && c.apply && Ot(s) && (e.result = c.apply(s, n), !1 === e.result && e.preventDefault());
                    return e.type = d, o || e.isDefaultPrevented() || f._default && !1 !== f._default.apply(p.pop(), n) || !Ot(i) || u && dt.isFunction(i[d]) && !dt.isWindow(i) && ((a = i[u]) && (i[u] = null), dt.event.triggered = d, i[d](), dt.event.triggered = void 0, a && (i[u] = a)), e.result
                }
            }, simulate: function (t, e, n) {
                var i = dt.extend(new dt.Event, n, {type: t, isSimulated: !0});
                dt.event.trigger(i, null, e)
            }
        }), dt.fn.extend({
            trigger: function (t, e) {
                return this.each(function () {
                    dt.event.trigger(t, e, this)
                })
            }, triggerHandler: function (t, e) {
                var n = this[0];
                if (n)return dt.event.trigger(t, e, n, !0)
            }
        }), dt.each("blur focus focusin focusout resize scroll click dblclick mousedown mouseup mousemove mouseover mouseout mouseenter mouseleave change select submit keydown keypress keyup contextmenu".split(" "), function (t, e) {
            dt.fn[e] = function (t, n) {
                return arguments.length > 0 ? this.on(e, null, t, n) : this.trigger(e)
            }
        }), dt.fn.extend({
            hover: function (t, e) {
                return this.mouseenter(t).mouseleave(e || t)
            }
        }), pt.focusin = "onfocusin" in t, pt.focusin || dt.each({focus: "focusin", blur: "focusout"}, function (t, e) {
            var n = function (t) {
                dt.event.simulate(e, t.target, dt.event.fix(t))
            };
            dt.event.special[e] = {
                setup: function () {
                    var i = this.ownerDocument || this, o = It.access(i, e);
                    o || i.addEventListener(t, n, !0), It.access(i, e, (o || 0) + 1)
                }, teardown: function () {
                    var i = this.ownerDocument || this, o = It.access(i, e) - 1;
                    o ? It.access(i, e, o) : (i.removeEventListener(t, n, !0), It.remove(i, e))
                }
            }
        });
        var Te = t.location, Ce = dt.now(), Ee = /\?/;
        dt.parseXML = function (e) {
            var n;
            if (!e || "string" != typeof e)return null;
            try {
                n = (new t.DOMParser).parseFromString(e, "text/xml")
            } catch (t) {
                n = void 0
            }
            return n && !n.getElementsByTagName("parsererror").length || dt.error("Invalid XML: " + e), n
        };
        var Se = /\[\]$/, $e = /\r?\n/g, ke = /^(?:submit|button|image|reset|file)$/i, De = /^(?:input|select|textarea|keygen)/i;
        dt.param = function (t, e) {
            var n, i = [], o = function (t, e) {
                var n = dt.isFunction(e) ? e() : e;
                i[i.length] = encodeURIComponent(t) + "=" + encodeURIComponent(null == n ? "" : n)
            };
            if (Array.isArray(t) || t.jquery && !dt.isPlainObject(t))dt.each(t, function () {
                o(this.name, this.value)
            }); else for (n in t)X(n, t[n], e, o);
            return i.join("&")
        }, dt.fn.extend({
            serialize: function () {
                return dt.param(this.serializeArray())
            }, serializeArray: function () {
                return this.map(function () {
                    var t = dt.prop(this, "elements");
                    return t ? dt.makeArray(t) : this
                }).filter(function () {
                    var t = this.type;
                    return this.name && !dt(this).is(":disabled") && De.test(this.nodeName) && !ke.test(t) && (this.checked || !Ut.test(t))
                }).map(function (t, e) {
                    var n = dt(this).val();
                    return null == n ? null : Array.isArray(n) ? dt.map(n, function (t) {
                        return {name: e.name, value: t.replace($e, "\r\n")}
                    }) : {name: e.name, value: n.replace($e, "\r\n")}
                }).get()
            }
        });
        var Ne = /%20/g, Ae = /#.*$/, je = /([?&])_=[^&]*/, Oe = /^(.*?):[ \t]*([^\r\n]*)$/gm, Ie = /^(?:GET|HEAD)$/, Le = /^\/\//, Re = {}, qe = {}, Pe = "*/".concat("*"), He = et.createElement("a");
        He.href = Te.href, dt.extend({
            active: 0, lastModified: {}, etag: {}, ajaxSettings: {url: Te.href, type: "GET", isLocal: /^(?:about|app|app-storage|.+-extension|file|res|widget):$/.test(Te.protocol), global: !0, processData: !0, async: !0, contentType: "application/x-www-form-urlencoded; charset=UTF-8", accepts: {"*": Pe, text: "text/plain", html: "text/html", xml: "application/xml, text/xml", json: "application/json, text/javascript"}, contents: {xml: /\bxml\b/, html: /\bhtml/, json: /\bjson\b/}, responseFields: {xml: "responseXML", text: "responseText", json: "responseJSON"}, converters: {"* text": String, "text html": !0, "text json": JSON.parse, "text xml": dt.parseXML}, flatOptions: {url: !0, context: !0}}, ajaxSetup: function (t, e) {
                return e ? J(J(t, dt.ajaxSettings), e) : J(dt.ajaxSettings, t)
            }, ajaxPrefilter: G(Re), ajaxTransport: G(qe), ajax: function (e, n) {
                function i(e, n, i, a) {
                    var u, p, d, x, w, T = n;
                    c || (c = !0, l && t.clearTimeout(l), o = void 0, s = a || "", C.readyState = e > 0 ? 4 : 0, u = e >= 200 && e < 300 || 304 === e, i && (x = K(h, C, i)), x = Z(h, x, C, u), u ? (h.ifModified && ((w = C.getResponseHeader("Last-Modified")) && (dt.lastModified[r] = w), (w = C.getResponseHeader("etag")) && (dt.etag[r] = w)), 204 === e || "HEAD" === h.type ? T = "nocontent" : 304 === e ? T = "notmodified" : (T = x.state, p = x.data, u = !(d = x.error))) : (d = T, !e && T || (T = "error", e < 0 && (e = 0))), C.status = e, C.statusText = (n || T) + "", u ? v.resolveWith(g, [p, T, C]) : v.rejectWith(g, [C, T, d]), C.statusCode(b), b = void 0, f && m.trigger(u ? "ajaxSuccess" : "ajaxError", [C, h, u ? p : d]), y.fireWith(g, [C, T]), f && (m.trigger("ajaxComplete", [C, h]), --dt.active || dt.event.trigger("ajaxStop")))
                }

                "object" == typeof e && (n = e, e = void 0), n = n || {};
                var o, r, s, a, l, u, c, f, p, d, h = dt.ajaxSetup({}, n), g = h.context || h, m = h.context && (g.nodeType || g.jquery) ? dt(g) : dt.event, v = dt.Deferred(), y = dt.Callbacks("once memory"), b = h.statusCode || {}, x = {}, w = {}, T = "canceled", C = {
                    readyState: 0, getResponseHeader: function (t) {
                        var e;
                        if (c) {
                            if (!a)for (a = {}; e = Oe.exec(s);)a[e[1].toLowerCase()] = e[2];
                            e = a[t.toLowerCase()]
                        }
                        return null == e ? null : e
                    }, getAllResponseHeaders: function () {
                        return c ? s : null
                    }, setRequestHeader: function (t, e) {
                        return null == c && (t = w[t.toLowerCase()] = w[t.toLowerCase()] || t, x[t] = e), this
                    }, overrideMimeType: function (t) {
                        return null == c && (h.mimeType = t), this
                    }, statusCode: function (t) {
                        var e;
                        if (t)if (c)C.always(t[C.status]); else for (e in t)b[e] = [b[e], t[e]];
                        return this
                    }, abort: function (t) {
                        var e = t || T;
                        return o && o.abort(e), i(0, e), this
                    }
                };
                if (v.promise(C), h.url = ((e || h.url || Te.href) + "").replace(Le, Te.protocol + "//"), h.type = n.method || n.type || h.method || h.type, h.dataTypes = (h.dataType || "*").toLowerCase().match(Dt) || [""], null == h.crossDomain) {
                    u = et.createElement("a");
                    try {
                        u.href = h.url, u.href = u.href, h.crossDomain = He.protocol + "//" + He.host != u.protocol + "//" + u.host
                    } catch (t) {
                        h.crossDomain = !0
                    }
                }
                if (h.data && h.processData && "string" != typeof h.data && (h.data = dt.param(h.data, h.traditional)), Y(Re, h, n, C), c)return C;
                (f = dt.event && h.global) && 0 == dt.active++ && dt.event.trigger("ajaxStart"), h.type = h.type.toUpperCase(), h.hasContent = !Ie.test(h.type), r = h.url.replace(Ae, ""), h.hasContent ? h.data && h.processData && 0 === (h.contentType || "").indexOf("application/x-www-form-urlencoded") && (h.data = h.data.replace(Ne, "+")) : (d = h.url.slice(r.length), h.data && (r += (Ee.test(r) ? "&" : "?") + h.data, delete h.data), !1 === h.cache && (r = r.replace(je, "$1"), d = (Ee.test(r) ? "&" : "?") + "_=" + Ce++ + d), h.url = r + d), h.ifModified && (dt.lastModified[r] && C.setRequestHeader("If-Modified-Since", dt.lastModified[r]), dt.etag[r] && C.setRequestHeader("If-None-Match", dt.etag[r])), (h.data && h.hasContent && !1 !== h.contentType || n.contentType) && C.setRequestHeader("Content-Type", h.contentType), C.setRequestHeader("Accept", h.dataTypes[0] && h.accepts[h.dataTypes[0]] ? h.accepts[h.dataTypes[0]] + ("*" !== h.dataTypes[0] ? ", " + Pe + "; q=0.01" : "") : h.accepts["*"]);
                for (p in h.headers)C.setRequestHeader(p, h.headers[p]);
                if (h.beforeSend && (!1 === h.beforeSend.call(g, C, h) || c))return C.abort();
                if (T = "abort", y.add(h.complete), C.done(h.success), C.fail(h.error), o = Y(qe, h, n, C)) {
                    if (C.readyState = 1, f && m.trigger("ajaxSend", [C, h]), c)return C;
                    h.async && h.timeout > 0 && (l = t.setTimeout(function () {
                        C.abort("timeout")
                    }, h.timeout));
                    try {
                        c = !1, o.send(x, i)
                    } catch (t) {
                        if (c)throw t;
                        i(-1, t)
                    }
                } else i(-1, "No Transport");
                return C
            }, getJSON: function (t, e, n) {
                return dt.get(t, e, n, "json")
            }, getScript: function (t, e) {
                return dt.get(t, void 0, e, "script")
            }
        }), dt.each(["get", "post"], function (t, e) {
            dt[e] = function (t, n, i, o) {
                return dt.isFunction(n) && (o = o || i, i = n, n = void 0), dt.ajax(dt.extend({url: t, type: e, dataType: o, data: n, success: i}, dt.isPlainObject(t) && t))
            }
        }), dt._evalUrl = function (t) {
            return dt.ajax({url: t, type: "GET", dataType: "script", cache: !0, async: !1, global: !1, throws: !0})
        }, dt.fn.extend({
            wrapAll: function (t) {
                var e;
                return this[0] && (dt.isFunction(t) && (t = t.call(this[0])), e = dt(t, this[0].ownerDocument).eq(0).clone(!0), this[0].parentNode && e.insertBefore(this[0]), e.map(function () {
                    for (var t = this; t.firstElementChild;)t = t.firstElementChild;
                    return t
                }).append(this)), this
            }, wrapInner: function (t) {
                return dt.isFunction(t) ? this.each(function (e) {
                    dt(this).wrapInner(t.call(this, e))
                }) : this.each(function () {
                    var e = dt(this), n = e.contents();
                    n.length ? n.wrapAll(t) : e.append(t)
                })
            }, wrap: function (t) {
                var e = dt.isFunction(t);
                return this.each(function (n) {
                    dt(this).wrapAll(e ? t.call(this, n) : t)
                })
            }, unwrap: function (t) {
                return this.parent(t).not("body").each(function () {
                    dt(this).replaceWith(this.childNodes)
                }), this
            }
        }), dt.expr.pseudos.hidden = function (t) {
            return !dt.expr.pseudos.visible(t)
        }, dt.expr.pseudos.visible = function (t) {
            return !!(t.offsetWidth || t.offsetHeight || t.getClientRects().length)
        }, dt.ajaxSettings.xhr = function () {
            try {
                return new t.XMLHttpRequest
            } catch (t) {
            }
        };
        var Fe = {0: 200, 1223: 204}, We = dt.ajaxSettings.xhr();
        pt.cors = !!We && "withCredentials" in We, pt.ajax = We = !!We, dt.ajaxTransport(function (e) {
            var n, i;
            if (pt.cors || We && !e.crossDomain)return {
                send: function (o, r) {
                    var s, a = e.xhr();
                    if (a.open(e.type, e.url, e.async, e.username, e.password), e.xhrFields)for (s in e.xhrFields)a[s] = e.xhrFields[s];
                    e.mimeType && a.overrideMimeType && a.overrideMimeType(e.mimeType), e.crossDomain || o["X-Requested-With"] || (o["X-Requested-With"] = "XMLHttpRequest");
                    for (s in o)a.setRequestHeader(s, o[s]);
                    n = function (t) {
                        return function () {
                            n && (n = i = a.onload = a.onerror = a.onabort = a.onreadystatechange = null, "abort" === t ? a.abort() : "error" === t ? "number" != typeof a.status ? r(0, "error") : r(a.status, a.statusText) : r(Fe[a.status] || a.status, a.statusText, "text" !== (a.responseType || "text") || "string" != typeof a.responseText ? {binary: a.response} : {text: a.responseText}, a.getAllResponseHeaders()))
                        }
                    }, a.onload = n(), i = a.onerror = n("error"), void 0 !== a.onabort ? a.onabort = i : a.onreadystatechange = function () {
                        4 === a.readyState && t.setTimeout(function () {
                            n && i()
                        })
                    }, n = n("abort");
                    try {
                        a.send(e.hasContent && e.data || null)
                    } catch (t) {
                        if (n)throw t
                    }
                }, abort: function () {
                    n && n()
                }
            }
        }), dt.ajaxPrefilter(function (t) {
            t.crossDomain && (t.contents.script = !1)
        }), dt.ajaxSetup({
            accepts: {script: "text/javascript, application/javascript, application/ecmascript, application/x-ecmascript"}, contents: {script: /\b(?:java|ecma)script\b/}, converters: {
                "text script": function (t) {
                    return dt.globalEval(t), t
                }
            }
        }), dt.ajaxPrefilter("script", function (t) {
            void 0 === t.cache && (t.cache = !1), t.crossDomain && (t.type = "GET")
        }), dt.ajaxTransport("script", function (t) {
            if (t.crossDomain) {
                var e, n;
                return {
                    send: function (i, o) {
                        e = dt("<script>").prop({charset: t.scriptCharset, src: t.url}).on("load error", n = function (t) {
                            e.remove(), n = null, t && o("error" === t.type ? 404 : 200, t.type)
                        }), et.head.appendChild(e[0])
                    }, abort: function () {
                        n && n()
                    }
                }
            }
        });
        var Me = [], Be = /(=)\?(?=&|$)|\?\?/;
        dt.ajaxSetup({
            jsonp: "callback", jsonpCallback: function () {
                var t = Me.pop() || dt.expando + "_" + Ce++;
                return this[t] = !0, t
            }
        }), dt.ajaxPrefilter("json jsonp", function (e, n, i) {
            var o, r, s, a = !1 !== e.jsonp && (Be.test(e.url) ? "url" : "string" == typeof e.data && 0 === (e.contentType || "").indexOf("application/x-www-form-urlencoded") && Be.test(e.data) && "data");
            if (a || "jsonp" === e.dataTypes[0])return o = e.jsonpCallback = dt.isFunction(e.jsonpCallback) ? e.jsonpCallback() : e.jsonpCallback, a ? e[a] = e[a].replace(Be, "$1" + o) : !1 !== e.jsonp && (e.url += (Ee.test(e.url) ? "&" : "?") + e.jsonp + "=" + o), e.converters["script json"] = function () {
                return s || dt.error(o + " was not called"), s[0]
            }, e.dataTypes[0] = "json", r = t[o], t[o] = function () {
                s = arguments
            }, i.always(function () {
                void 0 === r ? dt(t).removeProp(o) : t[o] = r, e[o] && (e.jsonpCallback = n.jsonpCallback, Me.push(o)), s && dt.isFunction(r) && r(s[0]), s = r = void 0
            }), "script"
        }), pt.createHTMLDocument = function () {
            var t = et.implementation.createHTMLDocument("").body;
            return t.innerHTML = "<form></form><form></form>", 2 === t.childNodes.length
        }(), dt.parseHTML = function (t, e, n) {
            if ("string" != typeof t)return [];
            "boolean" == typeof e && (n = e, e = !1);
            var i, o, r;
            return e || (pt.createHTMLDocument ? ((i = (e = et.implementation.createHTMLDocument("")).createElement("base")).href = et.location.href, e.head.appendChild(i)) : e = et), o = Tt.exec(t), r = !n && [], o ? [e.createElement(o[1])] : (o = x([t], e, r), r && r.length && dt(r).remove(), dt.merge([], o.childNodes))
        }, dt.fn.load = function (t, e, n) {
            var i, o, r, s = this, a = t.indexOf(" ");
            return a > -1 && (i = V(t.slice(a)), t = t.slice(0, a)), dt.isFunction(e) ? (n = e, e = void 0) : e && "object" == typeof e && (o = "POST"), s.length > 0 && dt.ajax({url: t, type: o || "GET", dataType: "html", data: e}).done(function (t) {
                r = arguments, s.html(i ? dt("<div>").append(dt.parseHTML(t)).find(i) : t)
            }).always(n && function (t, e) {
                    s.each(function () {
                        n.apply(this, r || [t.responseText, e, t])
                    })
                }), this
        }, dt.each(["ajaxStart", "ajaxStop", "ajaxComplete", "ajaxError", "ajaxSuccess", "ajaxSend"], function (t, e) {
            dt.fn[e] = function (t) {
                return this.on(e, t)
            }
        }), dt.expr.pseudos.animated = function (t) {
            return dt.grep(dt.timers, function (e) {
                return t === e.elem
            }).length
        }, dt.offset = {
            setOffset: function (t, e, n) {
                var i, o, r, s, a, l, u = dt.css(t, "position"), c = dt(t), f = {};
                "static" === u && (t.style.position = "relative"), a = c.offset(), r = dt.css(t, "top"), l = dt.css(t, "left"), ("absolute" === u || "fixed" === u) && (r + l).indexOf("auto") > -1 ? (s = (i = c.position()).top, o = i.left) : (s = parseFloat(r) || 0, o = parseFloat(l) || 0), dt.isFunction(e) && (e = e.call(t, n, dt.extend({}, a))), null != e.top && (f.top = e.top - a.top + s), null != e.left && (f.left = e.left - a.left + o), "using" in e ? e.using.call(t, f) : c.css(f)
            }
        }, dt.fn.extend({
            offset: function (t) {
                if (arguments.length)return void 0 === t ? this : this.each(function (e) {
                    dt.offset.setOffset(this, t, e)
                });
                var e, n, i, o, r = this[0];
                if (r)return r.getClientRects().length ? (i = r.getBoundingClientRect(), e = r.ownerDocument, n = e.documentElement, o = e.defaultView, {top: i.top + o.pageYOffset - n.clientTop, left: i.left + o.pageXOffset - n.clientLeft}) : {top: 0, left: 0}
            }, position: function () {
                if (this[0]) {
                    var t, e, n = this[0], i = {top: 0, left: 0};
                    return "fixed" === dt.css(n, "position") ? e = n.getBoundingClientRect() : (t = this.offsetParent(), e = this.offset(), o(t[0], "html") || (i = t.offset()), i = {top: i.top + dt.css(t[0], "borderTopWidth", !0), left: i.left + dt.css(t[0], "borderLeftWidth", !0)}), {top: e.top - i.top - dt.css(n, "marginTop", !0), left: e.left - i.left - dt.css(n, "marginLeft", !0)}
                }
            }, offsetParent: function () {
                return this.map(function () {
                    for (var t = this.offsetParent; t && "static" === dt.css(t, "position");)t = t.offsetParent;
                    return t || Xt
                })
            }
        }), dt.each({scrollLeft: "pageXOffset", scrollTop: "pageYOffset"}, function (t, e) {
            var n = "pageYOffset" === e;
            dt.fn[t] = function (i) {
                return jt(this, function (t, i, o) {
                    var r;
                    if (dt.isWindow(t) ? r = t : 9 === t.nodeType && (r = t.defaultView), void 0 === o)return r ? r[e] : t[i];
                    r ? r.scrollTo(n ? r.pageXOffset : o, n ? o : r.pageYOffset) : t[i] = o
                }, t, i, arguments.length)
            }
        }), dt.each(["top", "left"], function (t, e) {
            dt.cssHooks[e] = I(pt.pixelPosition, function (t, n) {
                if (n)return n = O(t, e), oe.test(n) ? dt(t).position()[e] + "px" : n
            })
        }), dt.each({Height: "height", Width: "width"}, function (t, e) {
            dt.each({padding: "inner" + t, content: e, "": "outer" + t}, function (n, i) {
                dt.fn[i] = function (o, r) {
                    var s = arguments.length && (n || "boolean" != typeof o), a = n || (!0 === o || !0 === r ? "margin" : "border");
                    return jt(this, function (e, n, o) {
                        var r;
                        return dt.isWindow(e) ? 0 === i.indexOf("outer") ? e["inner" + t] : e.document.documentElement["client" + t] : 9 === e.nodeType ? (r = e.documentElement, Math.max(e.body["scroll" + t], r["scroll" + t], e.body["offset" + t], r["offset" + t], r["client" + t])) : void 0 === o ? dt.css(e, n, a) : dt.style(e, n, o, a)
                    }, e, s ? o : void 0, s)
                }
            })
        }), dt.fn.extend({
            bind: function (t, e, n) {
                return this.on(t, null, e, n)
            }, unbind: function (t, e) {
                return this.off(t, null, e)
            }, delegate: function (t, e, n, i) {
                return this.on(e, t, n, i)
            }, undelegate: function (t, e, n) {
                return 1 === arguments.length ? this.off(t, "**") : this.off(e, t || "**", n)
            }
        }), dt.holdReady = function (t) {
            t ? dt.readyWait++ : dt.ready(!0)
        }, dt.isArray = Array.isArray, dt.parseJSON = JSON.parse, dt.nodeName = o, "function" == typeof define && define.amd && define("jquery", [], function () {
            return dt
        });
        var Ue = t.jQuery, _e = t.$;
        return dt.noConflict = function (e) {
            return t.$ === dt && (t.$ = _e), e && t.jQuery === dt && (t.jQuery = Ue), dt
        }, e || (t.jQuery = t.$ = dt), dt
    }), "undefined" == typeof jQuery)throw new Error("Bootstrap's JavaScript requires jQuery");
+function (t) {
    "use strict";
    var e = jQuery.fn.jquery.split(" ")[0].split(".");
    if (e[0] < 2 && e[1] < 9 || 1 == e[0] && 9 == e[1] && e[2] < 1 || e[0] > 3)throw new Error("Bootstrap's JavaScript requires jQuery version 1.9.1 or higher, but lower than version 4")
}(), function (t) {
    "use strict";
    function e() {
        var t = document.createElement("bootstrap"), e = {WebkitTransition: "webkitTransitionEnd", MozTransition: "transitionend", OTransition: "oTransitionEnd otransitionend", transition: "transitionend"};
        for (var n in e)if (void 0 !== t.style[n])return {end: e[n]};
        return !1
    }

    t.fn.emulateTransitionEnd = function (e) {
        var n = !1, i = this;
        t(this).one("bsTransitionEnd", function () {
            n = !0
        });
        return setTimeout(function () {
            n || t(i).trigger(t.support.transition.end)
        }, e), this
    }, t(function () {
        t.support.transition = e(), t.support.transition && (t.event.special.bsTransitionEnd = {
            bindType: t.support.transition.end, delegateType: t.support.transition.end, handle: function (e) {
                if (t(e.target).is(this))return e.handleObj.handler.apply(this, arguments)
            }
        })
    })
}(jQuery), function (t) {
    "use strict";
    var e = '[data-dismiss="alert"]', n = function (n) {
        t(n).on("click", e, this.close)
    };
    n.VERSION = "3.3.7", n.TRANSITION_DURATION = 150, n.prototype.close = function (e) {
        function i() {
            s.detach().trigger("closed.bs.alert").remove()
        }

        var o = t(this), r = o.attr("data-target");
        r || (r = (r = o.attr("href")) && r.replace(/.*(?=#[^\s]*$)/, ""));
        var s = t("#" === r ? [] : r);
        e && e.preventDefault(), s.length || (s = o.closest(".alert")), s.trigger(e = t.Event("close.bs.alert")), e.isDefaultPrevented() || (s.removeClass("in"), t.support.transition && s.hasClass("fade") ? s.one("bsTransitionEnd", i).emulateTransitionEnd(n.TRANSITION_DURATION) : i())
    };
    var i = t.fn.alert;
    t.fn.alert = function (e) {
        return this.each(function () {
            var i = t(this), o = i.data("bs.alert");
            o || i.data("bs.alert", o = new n(this)), "string" == typeof e && o[e].call(i)
        })
    }, t.fn.alert.Constructor = n, t.fn.alert.noConflict = function () {
        return t.fn.alert = i, this
    }, t(document).on("click.bs.alert.data-api", e, n.prototype.close)
}(jQuery), function (t) {
    "use strict";
    function e(e) {
        return this.each(function () {
            var i = t(this), o = i.data("bs.button"), r = "object" == typeof e && e;
            o || i.data("bs.button", o = new n(this, r)), "toggle" == e ? o.toggle() : e && o.setState(e)
        })
    }

    var n = function (e, i) {
        this.$element = t(e), this.options = t.extend({}, n.DEFAULTS, i), this.isLoading = !1
    };
    n.VERSION = "3.3.7", n.DEFAULTS = {loadingText: "loading..."}, n.prototype.setState = function (e) {
        var n = "disabled", i = this.$element, o = i.is("input") ? "val" : "html", r = i.data();
        e += "Text", null == r.resetText && i.data("resetText", i[o]()), setTimeout(t.proxy(function () {
            i[o](null == r[e] ? this.options[e] : r[e]), "loadingText" == e ? (this.isLoading = !0, i.addClass(n).attr(n, n).prop(n, !0)) : this.isLoading && (this.isLoading = !1, i.removeClass(n).removeAttr(n).prop(n, !1))
        }, this), 0)
    }, n.prototype.toggle = function () {
        var t = !0, e = this.$element.closest('[data-toggle="buttons"]');
        if (e.length) {
            var n = this.$element.find("input");
            "radio" == n.prop("type") ? (n.prop("checked") && (t = !1), e.find(".active").removeClass("active"), this.$element.addClass("active")) : "checkbox" == n.prop("type") && (n.prop("checked") !== this.$element.hasClass("active") && (t = !1), this.$element.toggleClass("active")), n.prop("checked", this.$element.hasClass("active")), t && n.trigger("change")
        } else this.$element.attr("aria-pressed", !this.$element.hasClass("active")), this.$element.toggleClass("active")
    };
    var i = t.fn.button;
    t.fn.button = e, t.fn.button.Constructor = n, t.fn.button.noConflict = function () {
        return t.fn.button = i, this
    }, t(document).on("click.bs.button.data-api", '[data-toggle^="button"]', function (n) {
        var i = t(n.target).closest(".btn");
        e.call(i, "toggle"), t(n.target).is('input[type="radio"], input[type="checkbox"]') || (n.preventDefault(), i.is("input,button") ? i.trigger("focus") : i.find("input:visible,button:visible").first().trigger("focus"))
    }).on("focus.bs.button.data-api blur.bs.button.data-api", '[data-toggle^="button"]', function (e) {
        t(e.target).closest(".btn").toggleClass("focus", /^focus(in)?$/.test(e.type))
    })
}(jQuery), function (t) {
    "use strict";
    function e(e) {
        return this.each(function () {
            var i = t(this), o = i.data("bs.carousel"), r = t.extend({}, n.DEFAULTS, i.data(), "object" == typeof e && e), s = "string" == typeof e ? e : r.slide;
            o || i.data("bs.carousel", o = new n(this, r)), "number" == typeof e ? o.to(e) : s ? o[s]() : r.interval && o.pause().cycle()
        })
    }

    var n = function (e, n) {
        this.$element = t(e), this.$indicators = this.$element.find(".carousel-indicators"), this.options = n, this.paused = null, this.sliding = null, this.interval = null, this.$active = null, this.$items = null, this.options.keyboard && this.$element.on("keydown.bs.carousel", t.proxy(this.keydown, this)), "hover" == this.options.pause && !("ontouchstart" in document.documentElement) && this.$element.on("mouseenter.bs.carousel", t.proxy(this.pause, this)).on("mouseleave.bs.carousel", t.proxy(this.cycle, this))
    };
    n.VERSION = "3.3.7", n.TRANSITION_DURATION = 600, n.DEFAULTS = {interval: 5e3, pause: "hover", wrap: !0, keyboard: !0}, n.prototype.keydown = function (t) {
        if (!/input|textarea/i.test(t.target.tagName)) {
            switch (t.which) {
                case 37:
                    this.prev();
                    break;
                case 39:
                    this.next();
                    break;
                default:
                    return
            }
            t.preventDefault()
        }
    }, n.prototype.cycle = function (e) {
        return e || (this.paused = !1), this.interval && clearInterval(this.interval), this.options.interval && !this.paused && (this.interval = setInterval(t.proxy(this.next, this), this.options.interval)), this
    }, n.prototype.getItemIndex = function (t) {
        return this.$items = t.parent().children(".item"), this.$items.index(t || this.$active)
    }, n.prototype.getItemForDirection = function (t, e) {
        var n = this.getItemIndex(e);
        if (("prev" == t && 0 === n || "next" == t && n == this.$items.length - 1) && !this.options.wrap)return e;
        var i = (n + ("prev" == t ? -1 : 1)) % this.$items.length;
        return this.$items.eq(i)
    }, n.prototype.to = function (t) {
        var e = this, n = this.getItemIndex(this.$active = this.$element.find(".item.active"));
        if (!(t > this.$items.length - 1 || t < 0))return this.sliding ? this.$element.one("slid.bs.carousel", function () {
            e.to(t)
        }) : n == t ? this.pause().cycle() : this.slide(t > n ? "next" : "prev", this.$items.eq(t))
    }, n.prototype.pause = function (e) {
        return e || (this.paused = !0), this.$element.find(".next, .prev").length && t.support.transition && (this.$element.trigger(t.support.transition.end), this.cycle(!0)), this.interval = clearInterval(this.interval), this
    }, n.prototype.next = function () {
        if (!this.sliding)return this.slide("next")
    }, n.prototype.prev = function () {
        if (!this.sliding)return this.slide("prev")
    }, n.prototype.slide = function (e, i) {
        var o = this.$element.find(".item.active"), r = i || this.getItemForDirection(e, o), s = this.interval, a = "next" == e ? "left" : "right", l = this;
        if (r.hasClass("active"))return this.sliding = !1;
        var u = r[0], c = t.Event("slide.bs.carousel", {relatedTarget: u, direction: a});
        if (this.$element.trigger(c), !c.isDefaultPrevented()) {
            if (this.sliding = !0, s && this.pause(), this.$indicators.length) {
                this.$indicators.find(".active").removeClass("active");
                var f = t(this.$indicators.children()[this.getItemIndex(r)]);
                f && f.addClass("active")
            }
            var p = t.Event("slid.bs.carousel", {relatedTarget: u, direction: a});
            return t.support.transition && this.$element.hasClass("slide") ? (r.addClass(e), r[0].offsetWidth, o.addClass(a), r.addClass(a), o.one("bsTransitionEnd", function () {
                r.removeClass([e, a].join(" ")).addClass("active"), o.removeClass(["active", a].join(" ")), l.sliding = !1, setTimeout(function () {
                    l.$element.trigger(p)
                }, 0)
            }).emulateTransitionEnd(n.TRANSITION_DURATION)) : (o.removeClass("active"), r.addClass("active"), this.sliding = !1, this.$element.trigger(p)), s && this.cycle(), this
        }
    };
    var i = t.fn.carousel;
    t.fn.carousel = e, t.fn.carousel.Constructor = n, t.fn.carousel.noConflict = function () {
        return t.fn.carousel = i, this
    };
    var o = function (n) {
        var i, o = t(this), r = t(o.attr("data-target") || (i = o.attr("href")) && i.replace(/.*(?=#[^\s]+$)/, ""));
        if (r.hasClass("carousel")) {
            var s = t.extend({}, r.data(), o.data()), a = o.attr("data-slide-to");
            a && (s.interval = !1), e.call(r, s), a && r.data("bs.carousel").to(a), n.preventDefault()
        }
    };
    t(document).on("click.bs.carousel.data-api", "[data-slide]", o).on("click.bs.carousel.data-api", "[data-slide-to]", o), t(window).on("load", function () {
        t('[data-ride="carousel"]').each(function () {
            var n = t(this);
            e.call(n, n.data())
        })
    })
}(jQuery), function (t) {
    "use strict";
    function e(e) {
        var n, i = e.attr("data-target") || (n = e.attr("href")) && n.replace(/.*(?=#[^\s]+$)/, "");
        return t(i)
    }

    function n(e) {
        return this.each(function () {
            var n = t(this), o = n.data("bs.collapse"), r = t.extend({}, i.DEFAULTS, n.data(), "object" == typeof e && e);
            !o && r.toggle && /show|hide/.test(e) && (r.toggle = !1), o || n.data("bs.collapse", o = new i(this, r)), "string" == typeof e && o[e]()
        })
    }

    var i = function (e, n) {
        this.$element = t(e), this.options = t.extend({}, i.DEFAULTS, n), this.$trigger = t('[data-toggle="collapse"][href="#' + e.id + '"],[data-toggle="collapse"][data-target="#' + e.id + '"]'), this.transitioning = null, this.options.parent ? this.$parent = this.getParent() : this.addAriaAndCollapsedClass(this.$element, this.$trigger), this.options.toggle && this.toggle()
    };
    i.VERSION = "3.3.7", i.TRANSITION_DURATION = 350, i.DEFAULTS = {toggle: !0}, i.prototype.dimension = function () {
        return this.$element.hasClass("width") ? "width" : "height"
    }, i.prototype.show = function () {
        if (!this.transitioning && !this.$element.hasClass("in")) {
            var e, o = this.$parent && this.$parent.children(".panel").children(".in, .collapsing");
            if (!(o && o.length && (e = o.data("bs.collapse")) && e.transitioning)) {
                var r = t.Event("show.bs.collapse");
                if (this.$element.trigger(r), !r.isDefaultPrevented()) {
                    o && o.length && (n.call(o, "hide"), e || o.data("bs.collapse", null));
                    var s = this.dimension();
                    this.$element.removeClass("collapse").addClass("collapsing")[s](0).attr("aria-expanded", !0), this.$trigger.removeClass("collapsed").attr("aria-expanded", !0), this.transitioning = 1;
                    var a = function () {
                        this.$element.removeClass("collapsing").addClass("collapse in")[s](""), this.transitioning = 0, this.$element.trigger("shown.bs.collapse")
                    };
                    if (!t.support.transition)return a.call(this);
                    var l = t.camelCase(["scroll", s].join("-"));
                    this.$element.one("bsTransitionEnd", t.proxy(a, this)).emulateTransitionEnd(i.TRANSITION_DURATION)[s](this.$element[0][l])
                }
            }
        }
    }, i.prototype.hide = function () {
        if (!this.transitioning && this.$element.hasClass("in")) {
            var e = t.Event("hide.bs.collapse");
            if (this.$element.trigger(e), !e.isDefaultPrevented()) {
                var n = this.dimension();
                this.$element[n](this.$element[n]())[0].offsetHeight, this.$element.addClass("collapsing").removeClass("collapse in").attr("aria-expanded", !1), this.$trigger.addClass("collapsed").attr("aria-expanded", !1), this.transitioning = 1;
                var o = function () {
                    this.transitioning = 0, this.$element.removeClass("collapsing").addClass("collapse").trigger("hidden.bs.collapse")
                };
                if (!t.support.transition)return o.call(this);
                this.$element[n](0).one("bsTransitionEnd", t.proxy(o, this)).emulateTransitionEnd(i.TRANSITION_DURATION)
            }
        }
    }, i.prototype.toggle = function () {
        this[this.$element.hasClass("in") ? "hide" : "show"]()
    }, i.prototype.getParent = function () {
        return t(this.options.parent).find('[data-toggle="collapse"][data-parent="' + this.options.parent + '"]').each(t.proxy(function (n, i) {
            var o = t(i);
            this.addAriaAndCollapsedClass(e(o), o)
        }, this)).end()
    }, i.prototype.addAriaAndCollapsedClass = function (t, e) {
        var n = t.hasClass("in");
        t.attr("aria-expanded", n), e.toggleClass("collapsed", !n).attr("aria-expanded", n)
    };
    var o = t.fn.collapse;
    t.fn.collapse = n, t.fn.collapse.Constructor = i, t.fn.collapse.noConflict = function () {
        return t.fn.collapse = o, this
    }, t(document).on("click.bs.collapse.data-api", '[data-toggle="collapse"]', function (i) {
        var o = t(this);
        o.attr("data-target") || i.preventDefault();
        var r = e(o), s = r.data("bs.collapse") ? "toggle" : o.data();
        n.call(r, s)
    })
}(jQuery), function (t) {
    "use strict";
    function e(e) {
        var n = e.attr("data-target");
        n || (n = (n = e.attr("href")) && /#[A-Za-z]/.test(n) && n.replace(/.*(?=#[^\s]*$)/, ""));
        var i = n && t(n);
        return i && i.length ? i : e.parent()
    }

    function n(n) {
        n && 3 === n.which || (t(i).remove(), t(o).each(function () {
            var i = t(this), o = e(i), r = {relatedTarget: this};
            o.hasClass("open") && (n && "click" == n.type && /input|textarea/i.test(n.target.tagName) && t.contains(o[0], n.target) || (o.trigger(n = t.Event("hide.bs.dropdown", r)), n.isDefaultPrevented() || (i.attr("aria-expanded", "false"), o.removeClass("open").trigger(t.Event("hidden.bs.dropdown", r)))))
        }))
    }

    var i = ".dropdown-backdrop", o = '[data-toggle="dropdown"]', r = function (e) {
        t(e).on("click.bs.dropdown", this.toggle)
    };
    r.VERSION = "3.3.7", r.prototype.toggle = function (i) {
        var o = t(this);
        if (!o.is(".disabled, :disabled")) {
            var r = e(o), s = r.hasClass("open");
            if (n(), !s) {
                "ontouchstart" in document.documentElement && !r.closest(".navbar-nav").length && t(document.createElement("div")).addClass("dropdown-backdrop").insertAfter(t(this)).on("click", n);
                var a = {relatedTarget: this};
                if (r.trigger(i = t.Event("show.bs.dropdown", a)), i.isDefaultPrevented())return;
                o.trigger("focus").attr("aria-expanded", "true"), r.toggleClass("open").trigger(t.Event("shown.bs.dropdown", a))
            }
            return !1
        }
    }, r.prototype.keydown = function (n) {
        if (/(38|40|27|32)/.test(n.which) && !/input|textarea/i.test(n.target.tagName)) {
            var i = t(this);
            if (n.preventDefault(), n.stopPropagation(), !i.is(".disabled, :disabled")) {
                var r = e(i), s = r.hasClass("open");
                if (!s && 27 != n.which || s && 27 == n.which)return 27 == n.which && r.find(o).trigger("focus"), i.trigger("click");
                var a = r.find(".dropdown-menu li:not(.disabled):visible a");
                if (a.length) {
                    var l = a.index(n.target);
                    38 == n.which && l > 0 && l--, 40 == n.which && l < a.length - 1 && l++, ~l || (l = 0), a.eq(l).trigger("focus")
                }
            }
        }
    };
    var s = t.fn.dropdown;
    t.fn.dropdown = function (e) {
        return this.each(function () {
            var n = t(this), i = n.data("bs.dropdown");
            i || n.data("bs.dropdown", i = new r(this)), "string" == typeof e && i[e].call(n)
        })
    }, t.fn.dropdown.Constructor = r, t.fn.dropdown.noConflict = function () {
        return t.fn.dropdown = s, this
    }, t(document).on("click.bs.dropdown.data-api", n).on("click.bs.dropdown.data-api", ".dropdown form", function (t) {
        t.stopPropagation()
    }).on("click.bs.dropdown.data-api", o, r.prototype.toggle).on("keydown.bs.dropdown.data-api", o, r.prototype.keydown).on("keydown.bs.dropdown.data-api", ".dropdown-menu", r.prototype.keydown)
}(jQuery), function (t) {
    "use strict";
    function e(e, i) {
        return this.each(function () {
            var o = t(this), r = o.data("bs.modal"), s = t.extend({}, n.DEFAULTS, o.data(), "object" == typeof e && e);
            r || o.data("bs.modal", r = new n(this, s)), "string" == typeof e ? r[e](i) : s.show && r.show(i)
        })
    }

    var n = function (e, n) {
        this.options = n, this.$body = t(document.body), this.$element = t(e), this.$dialog = this.$element.find(".modal-dialog"), this.$backdrop = null, this.isShown = null, this.originalBodyPad = null, this.scrollbarWidth = 0, this.ignoreBackdropClick = !1, this.options.remote && this.$element.find(".modal-content").load(this.options.remote, t.proxy(function () {
            this.$element.trigger("loaded.bs.modal")
        }, this))
    };
    n.VERSION = "3.3.7", n.TRANSITION_DURATION = 300, n.BACKDROP_TRANSITION_DURATION = 150, n.DEFAULTS = {backdrop: !0, keyboard: !0, show: !0}, n.prototype.toggle = function (t) {
        return this.isShown ? this.hide() : this.show(t)
    }, n.prototype.show = function (e) {
        var i = this, o = t.Event("show.bs.modal", {relatedTarget: e});
        this.$element.trigger(o), this.isShown || o.isDefaultPrevented() || (this.isShown = !0, this.checkScrollbar(), this.setScrollbar(), this.$body.addClass("modal-open"), this.escape(), this.resize(), this.$element.on("click.dismiss.bs.modal", '[data-dismiss="modal"]', t.proxy(this.hide, this)), this.$dialog.on("mousedown.dismiss.bs.modal", function () {
            i.$element.one("mouseup.dismiss.bs.modal", function (e) {
                t(e.target).is(i.$element) && (i.ignoreBackdropClick = !0)
            })
        }), this.backdrop(function () {
            var o = t.support.transition && i.$element.hasClass("fade");
            i.$element.parent().length || i.$element.appendTo(i.$body), i.$element.show().scrollTop(0), i.adjustDialog(), o && i.$element[0].offsetWidth, i.$element.addClass("in"), i.enforceFocus();
            var r = t.Event("shown.bs.modal", {relatedTarget: e});
            o ? i.$dialog.one("bsTransitionEnd", function () {
                i.$element.trigger("focus").trigger(r)
            }).emulateTransitionEnd(n.TRANSITION_DURATION) : i.$element.trigger("focus").trigger(r)
        }))
    }, n.prototype.hide = function (e) {
        e && e.preventDefault(), e = t.Event("hide.bs.modal"), this.$element.trigger(e), this.isShown && !e.isDefaultPrevented() && (this.isShown = !1, this.escape(), this.resize(), t(document).off("focusin.bs.modal"), this.$element.removeClass("in").off("click.dismiss.bs.modal").off("mouseup.dismiss.bs.modal"), this.$dialog.off("mousedown.dismiss.bs.modal"), t.support.transition && this.$element.hasClass("fade") ? this.$element.one("bsTransitionEnd", t.proxy(this.hideModal, this)).emulateTransitionEnd(n.TRANSITION_DURATION) : this.hideModal())
    }, n.prototype.enforceFocus = function () {
        t(document).off("focusin.bs.modal").on("focusin.bs.modal", t.proxy(function (t) {
            document === t.target || this.$element[0] === t.target || this.$element.has(t.target).length || this.$element.trigger("focus")
        }, this))
    }, n.prototype.escape = function () {
        this.isShown && this.options.keyboard ? this.$element.on("keydown.dismiss.bs.modal", t.proxy(function (t) {
            27 == t.which && this.hide()
        }, this)) : this.isShown || this.$element.off("keydown.dismiss.bs.modal")
    }, n.prototype.resize = function () {
        this.isShown ? t(window).on("resize.bs.modal", t.proxy(this.handleUpdate, this)) : t(window).off("resize.bs.modal")
    }, n.prototype.hideModal = function () {
        var t = this;
        this.$element.hide(), this.backdrop(function () {
            t.$body.removeClass("modal-open"), t.resetAdjustments(), t.resetScrollbar(), t.$element.trigger("hidden.bs.modal")
        })
    }, n.prototype.removeBackdrop = function () {
        this.$backdrop && this.$backdrop.remove(), this.$backdrop = null
    }, n.prototype.backdrop = function (e) {
        var i = this, o = this.$element.hasClass("fade") ? "fade" : "";
        if (this.isShown && this.options.backdrop) {
            var r = t.support.transition && o;
            if (this.$backdrop = t(document.createElement("div")).addClass("modal-backdrop " + o).appendTo(this.$body), this.$element.on("click.dismiss.bs.modal", t.proxy(function (t) {
                    this.ignoreBackdropClick ? this.ignoreBackdropClick = !1 : t.target === t.currentTarget && ("static" == this.options.backdrop ? this.$element[0].focus() : this.hide())
                }, this)), r && this.$backdrop[0].offsetWidth, this.$backdrop.addClass("in"), !e)return;
            r ? this.$backdrop.one("bsTransitionEnd", e).emulateTransitionEnd(n.BACKDROP_TRANSITION_DURATION) : e()
        } else if (!this.isShown && this.$backdrop) {
            this.$backdrop.removeClass("in");
            var s = function () {
                i.removeBackdrop(), e && e()
            };
            t.support.transition && this.$element.hasClass("fade") ? this.$backdrop.one("bsTransitionEnd", s).emulateTransitionEnd(n.BACKDROP_TRANSITION_DURATION) : s()
        } else e && e()
    }, n.prototype.handleUpdate = function () {
        this.adjustDialog()
    }, n.prototype.adjustDialog = function () {
        var t = this.$element[0].scrollHeight > document.documentElement.clientHeight;
        this.$element.css({paddingLeft: !this.bodyIsOverflowing && t ? this.scrollbarWidth : "", paddingRight: this.bodyIsOverflowing && !t ? this.scrollbarWidth : ""})
    }, n.prototype.resetAdjustments = function () {
        this.$element.css({paddingLeft: "", paddingRight: ""})
    }, n.prototype.checkScrollbar = function () {
        var t = window.innerWidth;
        if (!t) {
            var e = document.documentElement.getBoundingClientRect();
            t = e.right - Math.abs(e.left)
        }
        this.bodyIsOverflowing = document.body.clientWidth < t, this.scrollbarWidth = this.measureScrollbar()
    }, n.prototype.setScrollbar = function () {
        var t = parseInt(this.$body.css("padding-right") || 0, 10);
        this.originalBodyPad = document.body.style.paddingRight || "", this.bodyIsOverflowing && this.$body.css("padding-right", t + this.scrollbarWidth)
    }, n.prototype.resetScrollbar = function () {
        this.$body.css("padding-right", this.originalBodyPad)
    }, n.prototype.measureScrollbar = function () {
        var t = document.createElement("div");
        t.className = "modal-scrollbar-measure", this.$body.append(t);
        var e = t.offsetWidth - t.clientWidth;
        return this.$body[0].removeChild(t), e
    };
    var i = t.fn.modal;
    t.fn.modal = e, t.fn.modal.Constructor = n, t.fn.modal.noConflict = function () {
        return t.fn.modal = i, this
    }, t(document).on("click.bs.modal.data-api", '[data-toggle="modal"]', function (n) {
        var i = t(this), o = i.attr("href"), r = t(i.attr("data-target") || o && o.replace(/.*(?=#[^\s]+$)/, "")), s = r.data("bs.modal") ? "toggle" : t.extend({remote: !/#/.test(o) && o}, r.data(), i.data());
        i.is("a") && n.preventDefault(), r.one("show.bs.modal", function (t) {
            t.isDefaultPrevented() || r.one("hidden.bs.modal", function () {
                i.is(":visible") && i.trigger("focus")
            })
        }), e.call(r, s, this)
    })
}(jQuery), function (t) {
    "use strict";
    var e = function (t, e) {
        this.type = null, this.options = null, this.enabled = null, this.timeout = null, this.hoverState = null, this.$element = null, this.inState = null, this.init("tooltip", t, e)
    };
    e.VERSION = "3.3.7", e.TRANSITION_DURATION = 150, e.DEFAULTS = {animation: !0, placement: "top", selector: !1, template: '<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>', trigger: "hover focus", title: "", delay: 0, html: !1, container: !1, viewport: {selector: "body", padding: 0}}, e.prototype.init = function (e, n, i) {
        if (this.enabled = !0, this.type = e, this.$element = t(n), this.options = this.getOptions(i), this.$viewport = this.options.viewport && t(t.isFunction(this.options.viewport) ? this.options.viewport.call(this, this.$element) : this.options.viewport.selector || this.options.viewport), this.inState = {click: !1, hover: !1, focus: !1}, this.$element[0] instanceof document.constructor && !this.options.selector)throw new Error("`selector` option must be specified when initializing " + this.type + " on the window.document object!");
        for (var o = this.options.trigger.split(" "), r = o.length; r--;) {
            var s = o[r];
            if ("click" == s)this.$element.on("click." + this.type, this.options.selector, t.proxy(this.toggle, this)); else if ("manual" != s) {
                var a = "hover" == s ? "mouseenter" : "focusin", l = "hover" == s ? "mouseleave" : "focusout";
                this.$element.on(a + "." + this.type, this.options.selector, t.proxy(this.enter, this)), this.$element.on(l + "." + this.type, this.options.selector, t.proxy(this.leave, this))
            }
        }
        this.options.selector ? this._options = t.extend({}, this.options, {trigger: "manual", selector: ""}) : this.fixTitle()
    }, e.prototype.getDefaults = function () {
        return e.DEFAULTS
    }, e.prototype.getOptions = function (e) {
        return (e = t.extend({}, this.getDefaults(), this.$element.data(), e)).delay && "number" == typeof e.delay && (e.delay = {show: e.delay, hide: e.delay}), e
    }, e.prototype.getDelegateOptions = function () {
        var e = {}, n = this.getDefaults();
        return this._options && t.each(this._options, function (t, i) {
            n[t] != i && (e[t] = i)
        }), e
    }, e.prototype.enter = function (e) {
        var n = e instanceof this.constructor ? e : t(e.currentTarget).data("bs." + this.type);
        if (n || (n = new this.constructor(e.currentTarget, this.getDelegateOptions()), t(e.currentTarget).data("bs." + this.type, n)), e instanceof t.Event && (n.inState["focusin" == e.type ? "focus" : "hover"] = !0), n.tip().hasClass("in") || "in" == n.hoverState)n.hoverState = "in"; else {
            if (clearTimeout(n.timeout), n.hoverState = "in", !n.options.delay || !n.options.delay.show)return n.show();
            n.timeout = setTimeout(function () {
                "in" == n.hoverState && n.show()
            }, n.options.delay.show)
        }
    }, e.prototype.isInStateTrue = function () {
        for (var t in this.inState)if (this.inState[t])return !0;
        return !1
    }, e.prototype.leave = function (e) {
        var n = e instanceof this.constructor ? e : t(e.currentTarget).data("bs." + this.type);
        if (n || (n = new this.constructor(e.currentTarget, this.getDelegateOptions()), t(e.currentTarget).data("bs." + this.type, n)), e instanceof t.Event && (n.inState["focusout" == e.type ? "focus" : "hover"] = !1), !n.isInStateTrue()) {
            if (clearTimeout(n.timeout), n.hoverState = "out", !n.options.delay || !n.options.delay.hide)return n.hide();
            n.timeout = setTimeout(function () {
                "out" == n.hoverState && n.hide()
            }, n.options.delay.hide)
        }
    }, e.prototype.show = function () {
        var n = t.Event("show.bs." + this.type);
        if (this.hasContent() && this.enabled) {
            this.$element.trigger(n);
            var i = t.contains(this.$element[0].ownerDocument.documentElement, this.$element[0]);
            if (n.isDefaultPrevented() || !i)return;
            var o = this, r = this.tip(), s = this.getUID(this.type);
            this.setContent(), r.attr("id", s), this.$element.attr("aria-describedby", s), this.options.animation && r.addClass("fade");
            var a = "function" == typeof this.options.placement ? this.options.placement.call(this, r[0], this.$element[0]) : this.options.placement, l = /\s?auto?\s?/i, u = l.test(a);
            u && (a = a.replace(l, "") || "top"), r.detach().css({top: 0, left: 0, display: "block"}).addClass(a).data("bs." + this.type, this), this.options.container ? r.appendTo(this.options.container) : r.insertAfter(this.$element), this.$element.trigger("inserted.bs." + this.type);
            var c = this.getPosition(), f = r[0].offsetWidth, p = r[0].offsetHeight;
            if (u) {
                var d = a, h = this.getPosition(this.$viewport);
                a = "bottom" == a && c.bottom + p > h.bottom ? "top" : "top" == a && c.top - p < h.top ? "bottom" : "right" == a && c.right + f > h.width ? "left" : "left" == a && c.left - f < h.left ? "right" : a, r.removeClass(d).addClass(a)
            }
            var g = this.getCalculatedOffset(a, c, f, p);
            this.applyPlacement(g, a);
            var m = function () {
                var t = o.hoverState;
                o.$element.trigger("shown.bs." + o.type), o.hoverState = null, "out" == t && o.leave(o)
            };
            t.support.transition && this.$tip.hasClass("fade") ? r.one("bsTransitionEnd", m).emulateTransitionEnd(e.TRANSITION_DURATION) : m()
        }
    }, e.prototype.applyPlacement = function (e, n) {
        var i = this.tip(), o = i[0].offsetWidth, r = i[0].offsetHeight, s = parseInt(i.css("margin-top"), 10), a = parseInt(i.css("margin-left"), 10);
        isNaN(s) && (s = 0), isNaN(a) && (a = 0), e.top += s, e.left += a, t.offset.setOffset(i[0], t.extend({
            using: function (t) {
                i.css({top: Math.round(t.top), left: Math.round(t.left)})
            }
        }, e), 0), i.addClass("in");
        var l = i[0].offsetWidth, u = i[0].offsetHeight;
        "top" == n && u != r && (e.top = e.top + r - u);
        var c = this.getViewportAdjustedDelta(n, e, l, u);
        c.left ? e.left += c.left : e.top += c.top;
        var f = /top|bottom/.test(n), p = f ? 2 * c.left - o + l : 2 * c.top - r + u, d = f ? "offsetWidth" : "offsetHeight";
        i.offset(e), this.replaceArrow(p, i[0][d], f)
    }, e.prototype.replaceArrow = function (t, e, n) {
        this.arrow().css(n ? "left" : "top", 50 * (1 - t / e) + "%").css(n ? "top" : "left", "")
    }, e.prototype.setContent = function () {
        var t = this.tip(), e = this.getTitle();
        t.find(".tooltip-inner")[this.options.html ? "html" : "text"](e), t.removeClass("fade in top bottom left right")
    }, e.prototype.hide = function (n) {
        function i() {
            "in" != o.hoverState && r.detach(), o.$element && o.$element.removeAttr("aria-describedby").trigger("hidden.bs." + o.type), n && n()
        }

        var o = this, r = t(this.$tip), s = t.Event("hide.bs." + this.type);
        if (this.$element.trigger(s), !s.isDefaultPrevented())return r.removeClass("in"), t.support.transition && r.hasClass("fade") ? r.one("bsTransitionEnd", i).emulateTransitionEnd(e.TRANSITION_DURATION) : i(), this.hoverState = null, this
    }, e.prototype.fixTitle = function () {
        var t = this.$element;
        (t.attr("title") || "string" != typeof t.attr("data-original-title")) && t.attr("data-original-title", t.attr("title") || "").attr("title", "")
    }, e.prototype.hasContent = function () {
        return this.getTitle()
    }, e.prototype.getPosition = function (e) {
        var n = (e = e || this.$element)[0], i = "BODY" == n.tagName, o = n.getBoundingClientRect();
        null == o.width && (o = t.extend({}, o, {width: o.right - o.left, height: o.bottom - o.top}));
        var r = window.SVGElement && n instanceof window.SVGElement, s = i ? {top: 0, left: 0} : r ? null : e.offset(), a = {scroll: i ? document.documentElement.scrollTop || document.body.scrollTop : e.scrollTop()}, l = i ? {width: t(window).width(), height: t(window).height()} : null;
        return t.extend({}, o, a, l, s)
    }, e.prototype.getCalculatedOffset = function (t, e, n, i) {
        return "bottom" == t ? {top: e.top + e.height, left: e.left + e.width / 2 - n / 2} : "top" == t ? {top: e.top - i, left: e.left + e.width / 2 - n / 2} : "left" == t ? {top: e.top + e.height / 2 - i / 2, left: e.left - n} : {top: e.top + e.height / 2 - i / 2, left: e.left + e.width}
    }, e.prototype.getViewportAdjustedDelta = function (t, e, n, i) {
        var o = {top: 0, left: 0};
        if (!this.$viewport)return o;
        var r = this.options.viewport && this.options.viewport.padding || 0, s = this.getPosition(this.$viewport);
        if (/right|left/.test(t)) {
            var a = e.top - r - s.scroll, l = e.top + r - s.scroll + i;
            a < s.top ? o.top = s.top - a : l > s.top + s.height && (o.top = s.top + s.height - l)
        } else {
            var u = e.left - r, c = e.left + r + n;
            u < s.left ? o.left = s.left - u : c > s.right && (o.left = s.left + s.width - c)
        }
        return o
    }, e.prototype.getTitle = function () {
        var t = this.$element, e = this.options;
        return t.attr("data-original-title") || ("function" == typeof e.title ? e.title.call(t[0]) : e.title)
    }, e.prototype.getUID = function (t) {
        do {
            t += ~~(1e6 * Math.random())
        } while (document.getElementById(t));
        return t
    }, e.prototype.tip = function () {
        if (!this.$tip && (this.$tip = t(this.options.template), 1 != this.$tip.length))throw new Error(this.type + " `template` option must consist of exactly 1 top-level element!");
        return this.$tip
    }, e.prototype.arrow = function () {
        return this.$arrow = this.$arrow || this.tip().find(".tooltip-arrow")
    }, e.prototype.enable = function () {
        this.enabled = !0
    }, e.prototype.disable = function () {
        this.enabled = !1
    }, e.prototype.toggleEnabled = function () {
        this.enabled = !this.enabled
    }, e.prototype.toggle = function (e) {
        var n = this;
        e && ((n = t(e.currentTarget).data("bs." + this.type)) || (n = new this.constructor(e.currentTarget, this.getDelegateOptions()), t(e.currentTarget).data("bs." + this.type, n))), e ? (n.inState.click = !n.inState.click, n.isInStateTrue() ? n.enter(n) : n.leave(n)) : n.tip().hasClass("in") ? n.leave(n) : n.enter(n)
    }, e.prototype.destroy = function () {
        var t = this;
        clearTimeout(this.timeout), this.hide(function () {
            t.$element.off("." + t.type).removeData("bs." + t.type), t.$tip && t.$tip.detach(), t.$tip = null, t.$arrow = null, t.$viewport = null, t.$element = null
        })
    };
    var n = t.fn.tooltip;
    t.fn.tooltip = function (n) {
        return this.each(function () {
            var i = t(this), o = i.data("bs.tooltip"), r = "object" == typeof n && n;
            !o && /destroy|hide/.test(n) || (o || i.data("bs.tooltip", o = new e(this, r)), "string" == typeof n && o[n]())
        })
    }, t.fn.tooltip.Constructor = e, t.fn.tooltip.noConflict = function () {
        return t.fn.tooltip = n, this
    }
}(jQuery), function (t) {
    "use strict";
    var e = function (t, e) {
        this.init("popover", t, e)
    };
    if (!t.fn.tooltip)throw new Error("Popover requires tooltip.js");
    e.VERSION = "3.3.7", e.DEFAULTS = t.extend({}, t.fn.tooltip.Constructor.DEFAULTS, {placement: "right", trigger: "click", content: "", template: '<div class="popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'}), e.prototype = t.extend({}, t.fn.tooltip.Constructor.prototype), e.prototype.constructor = e, e.prototype.getDefaults = function () {
        return e.DEFAULTS
    }, e.prototype.setContent = function () {
        var t = this.tip(), e = this.getTitle(), n = this.getContent();
        t.find(".popover-title")[this.options.html ? "html" : "text"](e), t.find(".popover-content").children().detach().end()[this.options.html ? "string" == typeof n ? "html" : "append" : "text"](n), t.removeClass("fade top bottom left right in"), t.find(".popover-title").html() || t.find(".popover-title").hide()
    }, e.prototype.hasContent = function () {
        return this.getTitle() || this.getContent()
    }, e.prototype.getContent = function () {
        var t = this.$element, e = this.options;
        return t.attr("data-content") || ("function" == typeof e.content ? e.content.call(t[0]) : e.content)
    }, e.prototype.arrow = function () {
        return this.$arrow = this.$arrow || this.tip().find(".arrow")
    };
    var n = t.fn.popover;
    t.fn.popover = function (n) {
        return this.each(function () {
            var i = t(this), o = i.data("bs.popover"), r = "object" == typeof n && n;
            !o && /destroy|hide/.test(n) || (o || i.data("bs.popover", o = new e(this, r)), "string" == typeof n && o[n]())
        })
    }, t.fn.popover.Constructor = e, t.fn.popover.noConflict = function () {
        return t.fn.popover = n, this
    }
}(jQuery), function (t) {
    "use strict";
    function e(n, i) {
        this.$body = t(document.body), this.$scrollElement = t(t(n).is(document.body) ? window : n), this.options = t.extend({}, e.DEFAULTS, i), this.selector = (this.options.target || "") + " .nav li > a", this.offsets = [], this.targets = [], this.activeTarget = null, this.scrollHeight = 0, this.$scrollElement.on("scroll.bs.scrollspy", t.proxy(this.process, this)), this.refresh(), this.process()
    }

    function n(n) {
        return this.each(function () {
            var i = t(this), o = i.data("bs.scrollspy"), r = "object" == typeof n && n;
            o || i.data("bs.scrollspy", o = new e(this, r)), "string" == typeof n && o[n]()
        })
    }

    e.VERSION = "3.3.7", e.DEFAULTS = {offset: 10}, e.prototype.getScrollHeight = function () {
        return this.$scrollElement[0].scrollHeight || Math.max(this.$body[0].scrollHeight, document.documentElement.scrollHeight)
    }, e.prototype.refresh = function () {
        var e = this, n = "offset", i = 0;
        this.offsets = [], this.targets = [], this.scrollHeight = this.getScrollHeight(), t.isWindow(this.$scrollElement[0]) || (n = "position", i = this.$scrollElement.scrollTop()), this.$body.find(this.selector).map(function () {
            var e = t(this), o = e.data("target") || e.attr("href"), r = /^#./.test(o) && t(o);
            return r && r.length && r.is(":visible") && [[r[n]().top + i, o]] || null
        }).sort(function (t, e) {
            return t[0] - e[0]
        }).each(function () {
            e.offsets.push(this[0]), e.targets.push(this[1])
        })
    }, e.prototype.process = function () {
        var t, e = this.$scrollElement.scrollTop() + this.options.offset, n = this.getScrollHeight(), i = this.options.offset + n - this.$scrollElement.height(), o = this.offsets, r = this.targets, s = this.activeTarget;
        if (this.scrollHeight != n && this.refresh(), e >= i)return s != (t = r[r.length - 1]) && this.activate(t);
        if (s && e < o[0])return this.activeTarget = null, this.clear();
        for (t = o.length; t--;)s != r[t] && e >= o[t] && (void 0 === o[t + 1] || e < o[t + 1]) && this.activate(r[t])
    }, e.prototype.activate = function (e) {
        this.activeTarget = e, this.clear();
        var n = this.selector + '[data-target="' + e + '"],' + this.selector + '[href="' + e + '"]', i = t(n).parents("li").addClass("active");
        i.parent(".dropdown-menu").length && (i = i.closest("li.dropdown").addClass("active")), i.trigger("activate.bs.scrollspy")
    }, e.prototype.clear = function () {
        t(this.selector).parentsUntil(this.options.target, ".active").removeClass("active")
    };
    var i = t.fn.scrollspy;
    t.fn.scrollspy = n, t.fn.scrollspy.Constructor = e, t.fn.scrollspy.noConflict = function () {
        return t.fn.scrollspy = i, this
    }, t(window).on("load.bs.scrollspy.data-api", function () {
        t('[data-spy="scroll"]').each(function () {
            var e = t(this);
            n.call(e, e.data())
        })
    })
}(jQuery), function (t) {
    "use strict";
    function e(e) {
        return this.each(function () {
            var i = t(this), o = i.data("bs.tab");
            o || i.data("bs.tab", o = new n(this)), "string" == typeof e && o[e]()
        })
    }

    var n = function (e) {
        this.element = t(e)
    };
    n.VERSION = "3.3.7", n.TRANSITION_DURATION = 150, n.prototype.show = function () {
        var e = this.element, n = e.closest("ul:not(.dropdown-menu)"), i = e.data("target");
        if (i || (i = (i = e.attr("href")) && i.replace(/.*(?=#[^\s]*$)/, "")), !e.parent("li").hasClass("active")) {
            var o = n.find(".active:last a"), r = t.Event("hide.bs.tab", {relatedTarget: e[0]}), s = t.Event("show.bs.tab", {relatedTarget: o[0]});
            if (o.trigger(r), e.trigger(s), !s.isDefaultPrevented() && !r.isDefaultPrevented()) {
                var a = t(i);
                this.activate(e.closest("li"), n), this.activate(a, a.parent(), function () {
                    o.trigger({type: "hidden.bs.tab", relatedTarget: e[0]}), e.trigger({type: "shown.bs.tab", relatedTarget: o[0]})
                })
            }
        }
    }, n.prototype.activate = function (e, i, o) {
        function r() {
            s.removeClass("active").find("> .dropdown-menu > .active").removeClass("active").end().find('[data-toggle="tab"]').attr("aria-expanded", !1), e.addClass("active").find('[data-toggle="tab"]').attr("aria-expanded", !0), a ? (e[0].offsetWidth, e.addClass("in")) : e.removeClass("fade"), e.parent(".dropdown-menu").length && e.closest("li.dropdown").addClass("active").end().find('[data-toggle="tab"]').attr("aria-expanded", !0), o && o()
        }

        var s = i.find("> .active"), a = o && t.support.transition && (s.length && s.hasClass("fade") || !!i.find("> .fade").length);
        s.length && a ? s.one("bsTransitionEnd", r).emulateTransitionEnd(n.TRANSITION_DURATION) : r(), s.removeClass("in")
    };
    var i = t.fn.tab;
    t.fn.tab = e, t.fn.tab.Constructor = n, t.fn.tab.noConflict = function () {
        return t.fn.tab = i, this
    };
    var o = function (n) {
        n.preventDefault(), e.call(t(this), "show")
    };
    t(document).on("click.bs.tab.data-api", '[data-toggle="tab"]', o).on("click.bs.tab.data-api", '[data-toggle="pill"]', o)
}(jQuery), function (t) {
    "use strict";
    function e(e) {
        return this.each(function () {
            var i = t(this), o = i.data("bs.affix"), r = "object" == typeof e && e;
            o || i.data("bs.affix", o = new n(this, r)), "string" == typeof e && o[e]()
        })
    }

    var n = function (e, i) {
        this.options = t.extend({}, n.DEFAULTS, i), this.$target = t(this.options.target).on("scroll.bs.affix.data-api", t.proxy(this.checkPosition, this)).on("click.bs.affix.data-api", t.proxy(this.checkPositionWithEventLoop, this)), this.$element = t(e), this.affixed = null, this.unpin = null, this.pinnedOffset = null, this.checkPosition()
    };
    n.VERSION = "3.3.7", n.RESET = "affix affix-top affix-bottom", n.DEFAULTS = {offset: 0, target: window}, n.prototype.getState = function (t, e, n, i) {
        var o = this.$target.scrollTop(), r = this.$element.offset(), s = this.$target.height();
        if (null != n && "top" == this.affixed)return o < n && "top";
        if ("bottom" == this.affixed)return null != n ? !(o + this.unpin <= r.top) && "bottom" : !(o + s <= t - i) && "bottom";
        var a = null == this.affixed, l = a ? o : r.top;
        return null != n && o <= n ? "top" : null != i && l + (a ? s : e) >= t - i && "bottom"
    }, n.prototype.getPinnedOffset = function () {
        if (this.pinnedOffset)return this.pinnedOffset;
        this.$element.removeClass(n.RESET).addClass("affix");
        var t = this.$target.scrollTop(), e = this.$element.offset();
        return this.pinnedOffset = e.top - t
    }, n.prototype.checkPositionWithEventLoop = function () {
        setTimeout(t.proxy(this.checkPosition, this), 1)
    }, n.prototype.checkPosition = function () {
        if (this.$element.is(":visible")) {
            var e = this.$element.height(), i = this.options.offset, o = i.top, r = i.bottom, s = Math.max(t(document).height(), t(document.body).height());
            "object" != typeof i && (r = o = i), "function" == typeof o && (o = i.top(this.$element)), "function" == typeof r && (r = i.bottom(this.$element));
            var a = this.getState(s, e, o, r);
            if (this.affixed != a) {
                null != this.unpin && this.$element.css("top", "");
                var l = "affix" + (a ? "-" + a : ""), u = t.Event(l + ".bs.affix");
                if (this.$element.trigger(u), u.isDefaultPrevented())return;
                this.affixed = a, this.unpin = "bottom" == a ? this.getPinnedOffset() : null, this.$element.removeClass(n.RESET).addClass(l).trigger(l.replace("affix", "affixed") + ".bs.affix")
            }
            "bottom" == a && this.$element.offset({top: s - e - r})
        }
    };
    var i = t.fn.affix;
    t.fn.affix = e, t.fn.affix.Constructor = n, t.fn.affix.noConflict = function () {
        return t.fn.affix = i, this
    }, t(window).on("load", function () {
        t('[data-spy="affix"]').each(function () {
            var n = t(this), i = n.data();
            i.offset = i.offset || {}, null != i.offsetBottom && (i.offset.bottom = i.offsetBottom), null != i.offsetTop && (i.offset.top = i.offsetTop), e.call(n, i)
        })
    })
}(jQuery);