! function(e, t) {
    "object" == typeof exports && "undefined" != typeof module ? module.exports = t() : "function" == typeof define && define.amd ? define(t) : (e = "undefined" != typeof globalThis ? globalThis : e || self).bootstrap = t()
}(this, function() {
    "use strict";
    const o = new Map,
        z = {
            set(e, t, i) {
                o.has(e) || o.set(e, new Map);
                e = o.get(e);
                !e.has(t) && 0 !== e.size || e.set(t, i)
            },
            get: (e, t) => o.has(e) && o.get(e).get(t) || null,
            remove(e, t) {
                var i;
                o.has(e) && ((i = o.get(e)).delete(t), 0 === i.size) && o.delete(e)
            }
        },
        j = "transitionend",
        H = e => e = e && window.CSS && window.CSS.escape ? e.replace(/#([^\s"#']+)/g, (e, t) => "#" + CSS.escape(t)) : e,
        N = e => {
            e.dispatchEvent(new Event(j))
        },
        r = e => !(!e || "object" != typeof e) && void 0 !== (e = void 0 !== e.jquery ? e[0] : e).nodeType,
        n = e => r(e) ? e.jquery ? e[0] : e : "string" == typeof e && 0 < e.length ? document.querySelector(H(e)) : null,
        s = e => {
            if (!r(e) || 0 === e.getClientRects().length) return !1;
            const t = "visible" === getComputedStyle(e).getPropertyValue("visibility"),
                i = e.closest("details:not([open])");
            if (i && i !== e) {
                const t = e.closest("summary");
                if (t && t.parentNode !== i) return !1;
                if (null === t) return !1
            }
            return t
        },
        a = e => !e || e.nodeType !== Node.ELEMENT_NODE || !!e.classList.contains("disabled") || (void 0 !== e.disabled ? e.disabled : e.hasAttribute("disabled") && "false" !== e.getAttribute("disabled")),
        W = e => {
            var t;
            return document.documentElement.attachShadow ? "function" == typeof e.getRootNode ? (t = e.getRootNode()) instanceof ShadowRoot ? t : null : e instanceof ShadowRoot ? e : e.parentNode ? W(e.parentNode) : null : null
        },
        B = () => {},
        q = e => {
            e.offsetHeight
        },
        R = () => window.jQuery && !document.body.hasAttribute("data-bs-no-jquery") ? window.jQuery : null,
        F = [],
        l = () => "rtl" === document.documentElement.dir,
        e = o => {
            var e = () => {
                const e = R();
                if (e) {
                    const t = o.NAME,
                        i = e.fn[t];
                    e.fn[t] = o.jQueryInterface, e.fn[t].Constructor = o, e.fn[t].noConflict = () => (e.fn[t] = i, o.jQueryInterface)
                }
            };
            "loading" === document.readyState ? (F.length || document.addEventListener("DOMContentLoaded", () => {
                for (const e of F) e()
            }), F.push(e)) : e()
        },
        c = (e, t = [], i = e) => "function" == typeof e ? e(...t) : i,
        U = (i, n, e = !0) => {
            if (e) {
                e = (() => {
                    if (!n) return 0;
                    let {
                        transitionDuration: e,
                        transitionDelay: t
                    } = window.getComputedStyle(n);
                    var i = Number.parseFloat(e),
                        o = Number.parseFloat(t);
                    return i || o ? (e = e.split(",")[0], t = t.split(",")[0], 1e3 * (Number.parseFloat(e) + Number.parseFloat(t))) : 0
                })() + 5;
                let t = !1;
                const o = ({
                    target: e
                }) => {
                    e === n && (t = !0, n.removeEventListener(j, o), c(i))
                };
                n.addEventListener(j, o), setTimeout(() => {
                    t || N(n)
                }, e)
            } else c(i)
        },
        V = (e, t, i, o) => {
            var n = e.length;
            let s = e.indexOf(t);
            return -1 === s ? !i && o ? e[n - 1] : e[0] : (s += i ? 1 : -1, o && (s = (s + n) % n), e[Math.max(0, Math.min(s, n - 1))])
        },
        Y = /[^.]*(?=\..*)\.|.*/,
        X = /\..*/,
        K = /::\d+$/,
        Q = {};
    let G = 1;
    const Z = {
            mouseenter: "mouseover",
            mouseleave: "mouseout"
        },
        J = new Set(["click", "dblclick", "mouseup", "mousedown", "contextmenu", "mousewheel", "DOMMouseScroll", "mouseover", "mouseout", "mousemove", "selectstart", "selectend", "keydown", "keypress", "keyup", "orientationchange", "touchstart", "touchmove", "touchend", "touchcancel", "pointerdown", "pointermove", "pointerup", "pointerleave", "pointercancel", "gesturestart", "gesturechange", "gestureend", "focus", "blur", "change", "reset", "select", "submit", "focusin", "focusout", "load", "unload", "beforeunload", "resize", "move", "DOMContentLoaded", "readystatechange", "error", "abort", "scroll"]);

    function ee(e, t) {
        return t && t + "::" + G++ || e.uidEvent || G++
    }

    function te(e) {
        var t = ee(e);
        return e.uidEvent = t, Q[t] = Q[t] || {}, Q[t]
    }

    function ie(e, t, i = null) {
        return Object.values(e).find(e => e.callable === t && e.delegationSelector === i)
    }

    function oe(e, t, i) {
        var o = "string" == typeof t,
            t = !o && t || i;
        let n = re(e);
        return [o, t, n = J.has(n) ? n : e]
    }

    function ne(o, n, s, r, a) {
        if ("string" == typeof n && o) {
            let [e, t, i] = oe(n, s, r);
            if (n in Z) {
                const o = t => function(e) {
                    if (!e.relatedTarget || e.relatedTarget !== e.delegateTarget && !e.delegateTarget.contains(e.relatedTarget)) return t.call(this, e)
                };
                t = o(t)
            }
            var r = te(o),
                r = r[i] || (r[i] = {}),
                l = ie(r, t, e ? s : null);
            if (l) return l.oneOff = l.oneOff && a;
            var c, d, u, p, h, l = ee(t, n.replace(Y, "")),
                n = e ? (u = o, p = s, h = t, function t(i) {
                    var o = u.querySelectorAll(p);
                    for (let e = i["target"]; e && e !== this; e = e.parentNode)
                        for (const n of o)
                            if (n === e) return ae(i, {
                                delegateTarget: e
                            }), t.oneOff && f.off(u, i.type, p, h), h.apply(e, [i])
                }) : (c = o, d = t, function e(t) {
                    return ae(t, {
                        delegateTarget: c
                    }), e.oneOff && f.off(c, t.type, d), d.apply(c, [t])
                });
            n.delegationSelector = e ? s : null, n.callable = t, n.oneOff = a, r[n.uidEvent = l] = n, o.addEventListener(i, n, e)
        }
    }

    function se(e, t, i, o, n) {
        o = ie(t[i], o, n);
        o && (e.removeEventListener(i, o, Boolean(n)), delete t[i][o.uidEvent])
    }

    function re(e) {
        return e = e.replace(X, ""), Z[e] || e
    }
    const f = {
        on(e, t, i, o) {
            ne(e, t, i, o, !1)
        },
        one(e, t, i, o) {
            ne(e, t, i, o, !0)
        },
        off(e, t, i, o) {
            if ("string" == typeof t && e) {
                const [u, p, h] = oe(t, i, o), f = h !== t, m = te(e), g = m[h] || {}, v = t.startsWith(".");
                if (void 0 === p) {
                    if (v)
                        for (const i of Object.keys(m)) {
                            n = void 0;
                            s = void 0;
                            r = void 0;
                            a = void 0;
                            d = void 0;
                            l = void 0;
                            c = void 0;
                            var n = e;
                            var s = m;
                            var r = i;
                            var a = t.slice(1);
                            var l, c, d = s[r] || {};
                            for ([l, c] of Object.entries(d)) l.includes(a) && se(n, s, r, c.callable, c.delegationSelector)
                        }
                    for (const [i, o] of Object.entries(g)) {
                        const u = i.replace(K, "");
                        f && !t.includes(u) || se(e, m, h, o.callable, o.delegationSelector)
                    }
                } else Object.keys(g).length && se(e, m, h, p, u ? i : null)
            }
        },
        trigger(e, t, i) {
            if ("string" != typeof t || !e) return null;
            var o = R();
            let n = null,
                s = !0,
                r = !0,
                a = !1;
            t !== re(t) && o && (n = o.Event(t, i), o(e).trigger(n), s = !n.isPropagationStopped(), r = !n.isImmediatePropagationStopped(), a = n.isDefaultPrevented());
            o = ae(new Event(t, {
                bubbles: s,
                cancelable: !0
            }), i);
            return a && o.preventDefault(), r && e.dispatchEvent(o), o.defaultPrevented && n && n.preventDefault(), o
        }
    };

    function ae(e, t = {}) {
        for (const [i, o] of Object.entries(t)) try {
            e[i] = o
        } catch (t) {
            Object.defineProperty(e, i, {
                configurable: !0,
                get: () => o
            })
        }
        return e
    }

    function le(t) {
        if ("true" === t) return !0;
        if ("false" === t) return !1;
        if (t === Number(t).toString()) return Number(t);
        if ("" === t || "null" === t) return null;
        if ("string" != typeof t) return t;
        try {
            return JSON.parse(decodeURIComponent(t))
        } catch (e) {
            return t
        }
    }

    function ce(e) {
        return e.replace(/[A-Z]/g, e => "-" + e.toLowerCase())
    }
    const d = {
        setDataAttribute(e, t, i) {
            e.setAttribute("data-bs-" + ce(t), i)
        },
        removeDataAttribute(e, t) {
            e.removeAttribute("data-bs-" + ce(t))
        },
        getDataAttributes(t) {
            if (!t) return {};
            var i = {};
            for (const o of Object.keys(t.dataset).filter(e => e.startsWith("bs") && !e.startsWith("bsConfig"))) {
                let e = o.replace(/^bs/, "");
                i[e = e.charAt(0).toLowerCase() + e.slice(1, e.length)] = le(t.dataset[o])
            }
            return i
        },
        getDataAttribute: (e, t) => le(e.getAttribute("data-bs-" + ce(t)))
    };
    class de {
        static get Default() {
            return {}
        }
        static get DefaultType() {
            return {}
        }
        static get NAME() {
            throw new Error('You have to implement the static method "NAME", for each component!')
        }
        _getConfig(e) {
            return e = this._mergeConfigObj(e), e = this._configAfterMerge(e), this._typeCheckConfig(e), e
        }
        _configAfterMerge(e) {
            return e
        }
        _mergeConfigObj(e, t) {
            var i = r(t) ? d.getDataAttribute(t, "config") : {};
            return {
                ...this.constructor.Default,
                ..."object" == typeof i ? i : {},
                ...r(t) ? d.getDataAttributes(t) : {},
                ..."object" == typeof e ? e : {}
            }
        }
        _typeCheckConfig(e, t = this.constructor.DefaultType) {
            for (var [i, o] of Object.entries(t)) {
                const t = e[i],
                    s = r(t) ? "element" : null == (n = t) ? "" + n : Object.prototype.toString.call(n).match(/\s([a-z]+)/i)[1].toLowerCase();
                if (!new RegExp(o).test(s)) throw new TypeError(`${this.constructor.NAME.toUpperCase()}: Option "${i}" provided type "${s}" but expected type "${o}".`)
            }
            var n
        }
    }
    class t extends de {
        constructor(e, t) {
            super(), (e = n(e)) && (this._element = e, this._config = this._getConfig(t), z.set(this._element, this.constructor.DATA_KEY, this))
        }
        dispose() {
            z.remove(this._element, this.constructor.DATA_KEY), f.off(this._element, this.constructor.EVENT_KEY);
            for (const e of Object.getOwnPropertyNames(this)) this[e] = null
        }
        _queueCallback(e, t, i = !0) {
            U(e, t, i)
        }
        _getConfig(e) {
            return e = this._mergeConfigObj(e, this._element), e = this._configAfterMerge(e), this._typeCheckConfig(e), e
        }
        static getInstance(e) {
            return z.get(n(e), this.DATA_KEY)
        }
        static getOrCreateInstance(e, t = {}) {
            return this.getInstance(e) || new this(e, "object" == typeof t ? t : null)
        }
        static get VERSION() {
            return "5.3.0"
        }
        static get DATA_KEY() {
            return "bs." + this.NAME
        }
        static get EVENT_KEY() {
            return "." + this.DATA_KEY
        }
        static eventName(e) {
            return "" + e + this.EVENT_KEY
        }
    }
    const ue = t => {
            let i = t.getAttribute("data-bs-target");
            if (!i || "#" === i) {
                let e = t.getAttribute("href");
                if (!e || !e.includes("#") && !e.startsWith(".")) return null;
                e.includes("#") && !e.startsWith("#") && (e = "#" + e.split("#")[1]), i = e && "#" !== e ? e.trim() : null
            }
            return H(i)
        },
        u = {
            find: (e, t = document.documentElement) => [].concat(...Element.prototype.querySelectorAll.call(t, e)),
            findOne: (e, t = document.documentElement) => Element.prototype.querySelector.call(t, e),
            children: (e, t) => [].concat(...e.children).filter(e => e.matches(t)),
            parents(e, t) {
                var i = [];
                let o = e.parentNode.closest(t);
                for (; o;) i.push(o), o = o.parentNode.closest(t);
                return i
            },
            prev(e, t) {
                let i = e.previousElementSibling;
                for (; i;) {
                    if (i.matches(t)) return [i];
                    i = i.previousElementSibling
                }
                return []
            },
            next(e, t) {
                let i = e.nextElementSibling;
                for (; i;) {
                    if (i.matches(t)) return [i];
                    i = i.nextElementSibling
                }
                return []
            },
            focusableChildren(e) {
                var t = ["a", "button", "input", "textarea", "select", "details", "[tabindex]", '[contenteditable="true"]'].map(e => e + ':not([tabindex^="-"])').join(",");
                return this.find(t, e).filter(e => !a(e) && s(e))
            },
            getSelectorFromElement(e) {
                e = ue(e);
                return e && u.findOne(e) ? e : null
            },
            getElementFromSelector(e) {
                e = ue(e);
                return e ? u.findOne(e) : null
            },
            getMultipleElementsFromSelector(e) {
                e = ue(e);
                return e ? u.find(e) : []
            }
        },
        pe = (t, i = "hide") => {
            const e = "click.dismiss" + t.EVENT_KEY,
                o = t.NAME;
            f.on(document, e, `[data-bs-dismiss="${o}"]`, function(e) {
                ["A", "AREA"].includes(this.tagName) && e.preventDefault(), a(this) || (e = u.getElementFromSelector(this) || this.closest("." + o), t.getOrCreateInstance(e)[i]())
            })
        };
    class he extends t {
        static get NAME() {
            return "alert"
        }
        close() {
            var e;
            f.trigger(this._element, "close.bs.alert").defaultPrevented || (this._element.classList.remove("show"), e = this._element.classList.contains("fade"), this._queueCallback(() => this._destroyElement(), this._element, e))
        }
        _destroyElement() {
            this._element.remove(), f.trigger(this._element, "closed.bs.alert"), this.dispose()
        }
        static jQueryInterface(t) {
            return this.each(function() {
                var e = he.getOrCreateInstance(this);
                if ("string" == typeof t) {
                    if (void 0 === e[t] || t.startsWith("_") || "constructor" === t) throw new TypeError(`No method named "${t}"`);
                    e[t](this)
                }
            })
        }
    }
    pe(he, "close"), e(he);
    const fe = '[data-bs-toggle="button"]';
    class me extends t {
        static get NAME() {
            return "button"
        }
        toggle() {
            this._element.setAttribute("aria-pressed", this._element.classList.toggle("active"))
        }
        static jQueryInterface(t) {
            return this.each(function() {
                var e = me.getOrCreateInstance(this);
                "toggle" === t && e[t]()
            })
        }
    }
    f.on(document, "click.bs.button.data-api", fe, e => {
        e.preventDefault();
        e = e.target.closest(fe);
        me.getOrCreateInstance(e).toggle()
    }), e(me);
    const ge = {
            endCallback: null,
            leftCallback: null,
            rightCallback: null
        },
        ve = {
            endCallback: "(function|null)",
            leftCallback: "(function|null)",
            rightCallback: "(function|null)"
        };
    class ye extends de {
        constructor(e, t) {
            super(), (this._element = e) && ye.isSupported() && (this._config = this._getConfig(t), this._deltaX = 0, this._supportPointerEvents = Boolean(window.PointerEvent), this._initEvents())
        }
        static get Default() {
            return ge
        }
        static get DefaultType() {
            return ve
        }
        static get NAME() {
            return "swipe"
        }
        dispose() {
            f.off(this._element, ".bs.swipe")
        }
        _start(e) {
            this._supportPointerEvents ? this._eventIsPointerPenTouch(e) && (this._deltaX = e.clientX) : this._deltaX = e.touches[0].clientX
        }
        _end(e) {
            this._eventIsPointerPenTouch(e) && (this._deltaX = e.clientX - this._deltaX), this._handleSwipe(), c(this._config.endCallback)
        }
        _move(e) {
            this._deltaX = e.touches && 1 < e.touches.length ? 0 : e.touches[0].clientX - this._deltaX
        }
        _handleSwipe() {
            var e = Math.abs(this._deltaX);
            e <= 40 || (e = e / this._deltaX, this._deltaX = 0, e && c(0 < e ? this._config.rightCallback : this._config.leftCallback))
        }
        _initEvents() {
            this._supportPointerEvents ? (f.on(this._element, "pointerdown.bs.swipe", e => this._start(e)), f.on(this._element, "pointerup.bs.swipe", e => this._end(e)), this._element.classList.add("pointer-event")) : (f.on(this._element, "touchstart.bs.swipe", e => this._start(e)), f.on(this._element, "touchmove.bs.swipe", e => this._move(e)), f.on(this._element, "touchend.bs.swipe", e => this._end(e)))
        }
        _eventIsPointerPenTouch(e) {
            return this._supportPointerEvents && ("pen" === e.pointerType || "touch" === e.pointerType)
        }
        static isSupported() {
            return "ontouchstart" in document.documentElement || 0 < navigator.maxTouchPoints
        }
    }
    const be = "next",
        p = "prev",
        i = "left",
        we = "right",
        _e = "slid.bs.carousel",
        xe = "carousel",
        ke = "active",
        Te = {
            ArrowLeft: we,
            ArrowRight: i
        },
        Ce = {
            interval: 5e3,
            keyboard: !0,
            pause: "hover",
            ride: !1,
            touch: !0,
            wrap: !0
        },
        Se = {
            interval: "(number|boolean)",
            keyboard: "boolean",
            pause: "(string|boolean)",
            ride: "(boolean|string)",
            touch: "boolean",
            wrap: "boolean"
        };
    class Ee extends t {
        constructor(e, t) {
            super(e, t), this._interval = null, this._activeElement = null, this._isSliding = !1, this.touchTimeout = null, this._swipeHelper = null, this._indicatorsElement = u.findOne(".carousel-indicators", this._element), this._addEventListeners(), this._config.ride === xe && this.cycle()
        }
        static get Default() {
            return Ce
        }
        static get DefaultType() {
            return Se
        }
        static get NAME() {
            return "carousel"
        }
        next() {
            this._slide(be)
        }
        nextWhenVisible() {
            !document.hidden && s(this._element) && this.next()
        }
        prev() {
            this._slide(p)
        }
        pause() {
            this._isSliding && N(this._element), this._clearInterval()
        }
        cycle() {
            this._clearInterval(), this._updateInterval(), this._interval = setInterval(() => this.nextWhenVisible(), this._config.interval)
        }
        _maybeEnableCycle() {
            this._config.ride && (this._isSliding ? f.one(this._element, _e, () => this.cycle()) : this.cycle())
        }
        to(e) {
            var t, i = this._getItems();
            e > i.length - 1 || e < 0 || (this._isSliding ? f.one(this._element, _e, () => this.to(e)) : (t = this._getItemIndex(this._getActive())) !== e && (t = t < e ? be : p, this._slide(t, i[e])))
        }
        dispose() {
            this._swipeHelper && this._swipeHelper.dispose(), super.dispose()
        }
        _configAfterMerge(e) {
            return e.defaultInterval = e.interval, e
        }
        _addEventListeners() {
            this._config.keyboard && f.on(this._element, "keydown.bs.carousel", e => this._keydown(e)), "hover" === this._config.pause && (f.on(this._element, "mouseenter.bs.carousel", () => this.pause()), f.on(this._element, "mouseleave.bs.carousel", () => this._maybeEnableCycle())), this._config.touch && ye.isSupported() && this._addTouchEventListeners()
        }
        _addTouchEventListeners() {
            for (const e of u.find(".carousel-item img", this._element)) f.on(e, "dragstart.bs.carousel", e => e.preventDefault());
            const e = {
                leftCallback: () => this._slide(this._directionToOrder(i)),
                rightCallback: () => this._slide(this._directionToOrder(we)),
                endCallback: () => {
                    "hover" === this._config.pause && (this.pause(), this.touchTimeout && clearTimeout(this.touchTimeout), this.touchTimeout = setTimeout(() => this._maybeEnableCycle(), 500 + this._config.interval))
                }
            };
            this._swipeHelper = new ye(this._element, e)
        }
        _keydown(e) {
            var t;
            /input|textarea/i.test(e.target.tagName) || (t = Te[e.key]) && (e.preventDefault(), this._slide(this._directionToOrder(t)))
        }
        _getItemIndex(e) {
            return this._getItems().indexOf(e)
        }
        _setActiveIndicatorElement(e) {
            var t;
            this._indicatorsElement && ((t = u.findOne(".active", this._indicatorsElement)).classList.remove(ke), t.removeAttribute("aria-current"), t = u.findOne(`[data-bs-slide-to="${e}"]`, this._indicatorsElement)) && (t.classList.add(ke), t.setAttribute("aria-current", "true"))
        }
        _updateInterval() {
            var e = this._activeElement || this._getActive();
            e && (e = Number.parseInt(e.getAttribute("data-bs-interval"), 10), this._config.interval = e || this._config.defaultInterval)
        }
        _slide(t, e = null) {
            if (!this._isSliding) {
                const i = this._getActive(),
                    o = t === be,
                    n = e || V(this._getItems(), i, o, this._config.wrap);
                if (n !== i) {
                    const s = this._getItemIndex(n),
                        r = e => f.trigger(this._element, e, {
                            relatedTarget: n,
                            direction: this._orderToDirection(t),
                            from: this._getItemIndex(i),
                            to: s
                        });
                    if (!r("slide.bs.carousel").defaultPrevented && i && n) {
                        e = Boolean(this._interval);
                        this.pause(), this._isSliding = !0, this._setActiveIndicatorElement(s), this._activeElement = n;
                        const a = o ? "carousel-item-start" : "carousel-item-end",
                            l = o ? "carousel-item-next" : "carousel-item-prev";
                        n.classList.add(l), q(n), i.classList.add(a), n.classList.add(a), this._queueCallback(() => {
                            n.classList.remove(a, l), n.classList.add(ke), i.classList.remove(ke, l, a), this._isSliding = !1, r(_e)
                        }, i, this._isAnimated()), e && this.cycle()
                    }
                }
            }
        }
        _isAnimated() {
            return this._element.classList.contains("slide")
        }
        _getActive() {
            return u.findOne(".active.carousel-item", this._element)
        }
        _getItems() {
            return u.find(".carousel-item", this._element)
        }
        _clearInterval() {
            this._interval && (clearInterval(this._interval), this._interval = null)
        }
        _directionToOrder(e) {
            return l() ? e === i ? p : be : e === i ? be : p
        }
        _orderToDirection(e) {
            return l() ? e === p ? i : we : e === p ? we : i
        }
        static jQueryInterface(t) {
            return this.each(function() {
                var e = Ee.getOrCreateInstance(this, t);
                if ("number" != typeof t) {
                    if ("string" == typeof t) {
                        if (void 0 === e[t] || t.startsWith("_") || "constructor" === t) throw new TypeError(`No method named "${t}"`);
                        e[t]()
                    }
                } else e.to(t)
            })
        }
    }
    f.on(document, "click.bs.carousel.data-api", "[data-bs-slide], [data-bs-slide-to]", function(e) {
        var t = u.getElementFromSelector(this);
        t && t.classList.contains(xe) && (e.preventDefault(), e = Ee.getOrCreateInstance(t), (t = this.getAttribute("data-bs-slide-to")) ? e.to(t) : "next" === d.getDataAttribute(this, "slide") ? e.next() : e.prev(), e._maybeEnableCycle())
    }), f.on(window, "load.bs.carousel.data-api", () => {
        for (const e of u.find('[data-bs-ride="carousel"]')) Ee.getOrCreateInstance(e)
    }), e(Ee);
    const Ae = "show",
        Oe = "collapse",
        $e = "collapsing",
        Le = '[data-bs-toggle="collapse"]',
        Ie = {
            parent: null,
            toggle: !0
        },
        Pe = {
            parent: "(null|element)",
            toggle: "boolean"
        };
    class Me extends t {
        constructor(e, t) {
            super(e, t), this._isTransitioning = !1, this._triggerArray = [];
            const i = u.find(Le);
            for (const e of i) {
                const t = u.getSelectorFromElement(e),
                    i = u.find(t).filter(e => e === this._element);
                null !== t && i.length && this._triggerArray.push(e)
            }
            this._initializeChildren(), this._config.parent || this._addAriaAndCollapsedClass(this._triggerArray, this._isShown()), this._config.toggle && this.toggle()
        }
        static get Default() {
            return Ie
        }
        static get DefaultType() {
            return Pe
        }
        static get NAME() {
            return "collapse"
        }
        toggle() {
            this._isShown() ? this.hide() : this.show()
        }
        show() {
            if (!this._isTransitioning && !this._isShown()) {
                let e = [];
                if (!((e = this._config.parent ? this._getFirstLevelChildren(".collapse.show, .collapse.collapsing").filter(e => e !== this._element).map(e => Me.getOrCreateInstance(e, {
                        toggle: !1
                    })) : e).length && e[0]._isTransitioning || f.trigger(this._element, "show.bs.collapse").defaultPrevented)) {
                    for (const i of e) i.hide();
                    const i = this._getDimension();
                    this._element.classList.remove(Oe), this._element.classList.add($e), this._element.style[i] = 0, this._addAriaAndCollapsedClass(this._triggerArray, !0), this._isTransitioning = !0;
                    var t = "scroll" + (i[0].toUpperCase() + i.slice(1));
                    this._queueCallback(() => {
                        this._isTransitioning = !1, this._element.classList.remove($e), this._element.classList.add(Oe, Ae), this._element.style[i] = "", f.trigger(this._element, "shown.bs.collapse")
                    }, this._element, !0), this._element.style[i] = this._element[t] + "px"
                }
            }
        }
        hide() {
            if (!this._isTransitioning && this._isShown() && !f.trigger(this._element, "hide.bs.collapse").defaultPrevented) {
                const t = this._getDimension();
                this._element.style[t] = this._element.getBoundingClientRect()[t] + "px", q(this._element), this._element.classList.add($e), this._element.classList.remove(Oe, Ae);
                for (const t of this._triggerArray) {
                    var e = u.getElementFromSelector(t);
                    e && !this._isShown(e) && this._addAriaAndCollapsedClass([t], !1)
                }
                this._isTransitioning = !0, this._element.style[t] = "", this._queueCallback(() => {
                    this._isTransitioning = !1, this._element.classList.remove($e), this._element.classList.add(Oe), f.trigger(this._element, "hidden.bs.collapse")
                }, this._element, !0)
            }
        }
        _isShown(e = this._element) {
            return e.classList.contains(Ae)
        }
        _configAfterMerge(e) {
            return e.toggle = Boolean(e.toggle), e.parent = n(e.parent), e
        }
        _getDimension() {
            return this._element.classList.contains("collapse-horizontal") ? "width" : "height"
        }
        _initializeChildren() {
            if (this._config.parent) {
                const e = this._getFirstLevelChildren(Le);
                for (const t of e) {
                    const e = u.getElementFromSelector(t);
                    e && this._addAriaAndCollapsedClass([t], this._isShown(e))
                }
            }
        }
        _getFirstLevelChildren(e) {
            const t = u.find(":scope .collapse .collapse", this._config.parent);
            return u.find(e, this._config.parent).filter(e => !t.includes(e))
        }
        _addAriaAndCollapsedClass(e, t) {
            if (e.length)
                for (const i of e) i.classList.toggle("collapsed", !t), i.setAttribute("aria-expanded", t)
        }
        static jQueryInterface(t) {
            const i = {};
            return "string" == typeof t && /show|hide/.test(t) && (i.toggle = !1), this.each(function() {
                var e = Me.getOrCreateInstance(this, i);
                if ("string" == typeof t) {
                    if (void 0 === e[t]) throw new TypeError(`No method named "${t}"`);
                    e[t]()
                }
            })
        }
    }
    f.on(document, "click.bs.collapse.data-api", Le, function(e) {
        ("A" === e.target.tagName || e.delegateTarget && "A" === e.delegateTarget.tagName) && e.preventDefault();
        for (const e of u.getMultipleElementsFromSelector(this)) Me.getOrCreateInstance(e, {
            toggle: !1
        }).toggle()
    }), e(Me);
    var E = "top",
        A = "bottom",
        O = "right",
        $ = "left",
        De = "auto",
        L = [E, A, O, $],
        I = "start",
        b = "end",
        ze = "clippingParents",
        je = "viewport",
        g = "popper",
        He = "reference",
        Ne = L.reduce(function(e, t) {
            return e.concat([t + "-" + I, t + "-" + b])
        }, []),
        We = [].concat(L, [De]).reduce(function(e, t) {
            return e.concat([t, t + "-" + I, t + "-" + b])
        }, []),
        Be = "beforeRead",
        qe = "afterRead",
        Re = "beforeMain",
        Fe = "afterMain",
        Ue = "beforeWrite",
        Ve = "afterWrite",
        Ye = [Be, "read", qe, Re, "main", Fe, Ue, "write", Ve];

    function v(e) {
        return e ? (e.nodeName || "").toLowerCase() : null
    }

    function w(e) {
        var t;
        return null == e ? window : "[object Window]" !== e.toString() ? (t = e.ownerDocument) && t.defaultView || window : e
    }

    function y(e) {
        return e instanceof w(e).Element || e instanceof Element
    }

    function _(e) {
        return e instanceof w(e).HTMLElement || e instanceof HTMLElement
    }

    function Xe(e) {
        return "undefined" != typeof ShadowRoot && (e instanceof w(e).ShadowRoot || e instanceof ShadowRoot)
    }
    var Ke = {
        name: "applyStyles",
        enabled: !0,
        phase: "write",
        fn: function(e) {
            var n = e.state;
            Object.keys(n.elements).forEach(function(e) {
                var t = n.styles[e] || {},
                    i = n.attributes[e] || {},
                    o = n.elements[e];
                _(o) && v(o) && (Object.assign(o.style, t), Object.keys(i).forEach(function(e) {
                    var t = i[e];
                    !1 === t ? o.removeAttribute(e) : o.setAttribute(e, !0 === t ? "" : t)
                }))
            })
        },
        effect: function(e) {
            var o = e.state,
                n = {
                    popper: {
                        position: o.options.strategy,
                        left: "0",
                        top: "0",
                        margin: "0"
                    },
                    arrow: {
                        position: "absolute"
                    },
                    reference: {}
                };
            return Object.assign(o.elements.popper.style, n.popper), o.styles = n, o.elements.arrow && Object.assign(o.elements.arrow.style, n.arrow),
                function() {
                    Object.keys(o.elements).forEach(function(e) {
                        var t = o.elements[e],
                            i = o.attributes[e] || {},
                            e = Object.keys((o.styles.hasOwnProperty(e) ? o.styles : n)[e]).reduce(function(e, t) {
                                return e[t] = "", e
                            }, {});
                        _(t) && v(t) && (Object.assign(t.style, e), Object.keys(i).forEach(function(e) {
                            t.removeAttribute(e)
                        }))
                    })
                }
        },
        requires: ["computeStyles"]
    };

    function P(e) {
        return e.split("-")[0]
    }
    var S = Math.max,
        Qe = Math.min,
        x = Math.round;

    function Ge() {
        var e = navigator.userAgentData;
        return null != e && e.brands && Array.isArray(e.brands) ? e.brands.map(function(e) {
            return e.brand + "/" + e.version
        }).join(" ") : navigator.userAgent
    }

    function Ze() {
        return !/^((?!chrome|android).)*safari/i.test(Ge())
    }

    function k(e, t, i) {
        void 0 === t && (t = !1), void 0 === i && (i = !1);
        var o = e.getBoundingClientRect(),
            n = 1,
            s = 1,
            t = (t && _(e) && (n = 0 < e.offsetWidth && x(o.width) / e.offsetWidth || 1, s = 0 < e.offsetHeight && x(o.height) / e.offsetHeight || 1), (y(e) ? w(e) : window).visualViewport),
            e = !Ze() && i,
            i = (o.left + (e && t ? t.offsetLeft : 0)) / n,
            e = (o.top + (e && t ? t.offsetTop : 0)) / s,
            t = o.width / n,
            n = o.height / s;
        return {
            width: t,
            height: n,
            top: e,
            right: i + t,
            bottom: e + n,
            left: i,
            x: i,
            y: e
        }
    }

    function Je(e) {
        var t = k(e),
            i = e.offsetWidth,
            o = e.offsetHeight;
        return Math.abs(t.width - i) <= 1 && (i = t.width), Math.abs(t.height - o) <= 1 && (o = t.height), {
            x: e.offsetLeft,
            y: e.offsetTop,
            width: i,
            height: o
        }
    }

    function et(e, t) {
        var i = t.getRootNode && t.getRootNode();
        if (e.contains(t)) return !0;
        if (i && Xe(i)) {
            var o = t;
            do {
                if (o && e.isSameNode(o)) return !0
            } while (o = o.parentNode || o.host)
        }
        return !1
    }

    function T(e) {
        return w(e).getComputedStyle(e)
    }

    function C(e) {
        return ((y(e) ? e.ownerDocument : e.document) || window.document).documentElement
    }

    function tt(e) {
        return "html" === v(e) ? e : e.assignedSlot || e.parentNode || (Xe(e) ? e.host : null) || C(e)
    }

    function it(e) {
        return _(e) && "fixed" !== T(e).position ? e.offsetParent : null
    }

    function ot(e) {
        for (var t, i = w(e), o = it(e); o && (t = o, 0 <= ["table", "td", "th"].indexOf(v(t))) && "static" === T(o).position;) o = it(o);
        return (!o || "html" !== v(o) && ("body" !== v(o) || "static" !== T(o).position)) && (o || function(e) {
            var t = /firefox/i.test(Ge());
            if (!/Trident/i.test(Ge()) || !_(e) || "fixed" !== T(e).position) {
                var i = tt(e);
                for (Xe(i) && (i = i.host); _(i) && ["html", "body"].indexOf(v(i)) < 0;) {
                    var o = T(i);
                    if ("none" !== o.transform || "none" !== o.perspective || "paint" === o.contain || -1 !== ["transform", "perspective"].indexOf(o.willChange) || t && "filter" === o.willChange || t && o.filter && "none" !== o.filter) return i;
                    i = i.parentNode
                }
            }
            return null
        }(e)) || i
    }

    function nt(e) {
        return 0 <= ["top", "bottom"].indexOf(e) ? "x" : "y"
    }

    function st(e, t, i) {
        return S(e, Qe(t, i))
    }

    function rt(e) {
        return Object.assign({}, {
            top: 0,
            right: 0,
            bottom: 0,
            left: 0
        }, e)
    }

    function at(i, e) {
        return e.reduce(function(e, t) {
            return e[t] = i, e
        }, {})
    }
    var lt = {
        name: "arrow",
        enabled: !0,
        phase: "main",
        fn: function(e) {
            var t, i, o, n, s = e.state,
                r = e.name,
                e = e.options,
                a = s.elements.arrow,
                l = s.modifiersData.popperOffsets,
                c = P(s.placement),
                d = nt(c),
                c = 0 <= [$, O].indexOf(c) ? "height" : "width";
            a && l && (e = rt("number" != typeof(e = "function" == typeof(e = e.padding) ? e(Object.assign({}, s.rects, {
                placement: s.placement
            })) : e) ? e : at(e, L)), t = Je(a), n = "y" === d ? E : $, o = "y" === d ? A : O, i = s.rects.reference[c] + s.rects.reference[d] - l[d] - s.rects.popper[c], l = l[d] - s.rects.reference[d], a = (a = ot(a)) ? "y" === d ? a.clientHeight || 0 : a.clientWidth || 0 : 0, n = e[n], e = a - t[c] - e[o], n = st(n, o = a / 2 - t[c] / 2 + (i / 2 - l / 2), e), s.modifiersData[r] = ((a = {})[d] = n, a.centerOffset = n - o, a))
        },
        effect: function(e) {
            var t = e.state,
                e = e.options.element,
                e = void 0 === e ? "[data-popper-arrow]" : e;
            null != e && ("string" != typeof e || (e = t.elements.popper.querySelector(e))) && et(t.elements.popper, e) && (t.elements.arrow = e)
        },
        requires: ["popperOffsets"],
        requiresIfExists: ["preventOverflow"]
    };

    function M(e) {
        return e.split("-")[1]
    }
    var ct = {
        top: "auto",
        right: "auto",
        bottom: "auto",
        left: "auto"
    };

    function dt(e) {
        var t = e.popper,
            i = e.popperRect,
            o = e.placement,
            n = e.variation,
            s = e.offsets,
            r = e.position,
            a = e.gpuAcceleration,
            l = e.adaptive,
            c = e.roundOffsets,
            e = e.isFixed,
            d = s.x,
            d = void 0 === d ? 0 : d,
            u = s.y,
            u = void 0 === u ? 0 : u,
            p = "function" == typeof c ? c({
                x: d,
                y: u
            }) : {
                x: d,
                y: u
            },
            d = p.x,
            u = p.y,
            p = s.hasOwnProperty("x"),
            s = s.hasOwnProperty("y"),
            h = $,
            f = E,
            m = window;
        l && (v = "clientHeight", g = "clientWidth", (y = ot(t)) === w(t) && "static" !== T(y = C(t)).position && "absolute" === r && (v = "scrollHeight", g = "scrollWidth"), o !== E && (o !== $ && o !== O || n !== b) || (f = A, u = (u - ((e && y === m && m.visualViewport ? m.visualViewport.height : y[v]) - i.height)) * (a ? 1 : -1)), o !== $ && (o !== E && o !== A || n !== b) || (h = O, d = (d - ((e && y === m && m.visualViewport ? m.visualViewport.width : y[g]) - i.width)) * (a ? 1 : -1)));
        var g, v = Object.assign({
                position: r
            }, l && ct),
            y = !0 === c ? (o = {
                x: d,
                y: u
            }, n = w(t), e = o.y, n = n.devicePixelRatio || 1, {
                x: x(o.x * n) / n || 0,
                y: x(e * n) / n || 0
            }) : {
                x: d,
                y: u
            };
        return d = y.x, u = y.y, a ? Object.assign({}, v, ((g = {})[f] = s ? "0" : "", g[h] = p ? "0" : "", g.transform = (m.devicePixelRatio || 1) <= 1 ? "translate(" + d + "px, " + u + "px)" : "translate3d(" + d + "px, " + u + "px, 0)", g)) : Object.assign({}, v, ((i = {})[f] = s ? u + "px" : "", i[h] = p ? d + "px" : "", i.transform = "", i))
    }
    var ut = {
            name: "computeStyles",
            enabled: !0,
            phase: "beforeWrite",
            fn: function(e) {
                var t = e.state,
                    e = e.options,
                    i = e.gpuAcceleration,
                    i = void 0 === i || i,
                    o = e.adaptive,
                    o = void 0 === o || o,
                    e = e.roundOffsets,
                    e = void 0 === e || e,
                    i = {
                        placement: P(t.placement),
                        variation: M(t.placement),
                        popper: t.elements.popper,
                        popperRect: t.rects.popper,
                        gpuAcceleration: i,
                        isFixed: "fixed" === t.options.strategy
                    };
                null != t.modifiersData.popperOffsets && (t.styles.popper = Object.assign({}, t.styles.popper, dt(Object.assign({}, i, {
                    offsets: t.modifiersData.popperOffsets,
                    position: t.options.strategy,
                    adaptive: o,
                    roundOffsets: e
                })))), null != t.modifiersData.arrow && (t.styles.arrow = Object.assign({}, t.styles.arrow, dt(Object.assign({}, i, {
                    offsets: t.modifiersData.arrow,
                    position: "absolute",
                    adaptive: !1,
                    roundOffsets: e
                })))), t.attributes.popper = Object.assign({}, t.attributes.popper, {
                    "data-popper-placement": t.placement
                })
            },
            data: {}
        },
        pt = {
            passive: !0
        },
        ht = {
            name: "eventListeners",
            enabled: !0,
            phase: "write",
            fn: function() {},
            effect: function(e) {
                var t = e.state,
                    i = e.instance,
                    e = e.options,
                    o = e.scroll,
                    n = void 0 === o || o,
                    o = e.resize,
                    s = void 0 === o || o,
                    r = w(t.elements.popper),
                    a = [].concat(t.scrollParents.reference, t.scrollParents.popper);
                return n && a.forEach(function(e) {
                        e.addEventListener("scroll", i.update, pt)
                    }), s && r.addEventListener("resize", i.update, pt),
                    function() {
                        n && a.forEach(function(e) {
                            e.removeEventListener("scroll", i.update, pt)
                        }), s && r.removeEventListener("resize", i.update, pt)
                    }
            },
            data: {}
        },
        ft = {
            left: "right",
            right: "left",
            bottom: "top",
            top: "bottom"
        };

    function mt(e) {
        return e.replace(/left|right|bottom|top/g, function(e) {
            return ft[e]
        })
    }
    var gt = {
        start: "end",
        end: "start"
    };

    function vt(e) {
        return e.replace(/start|end/g, function(e) {
            return gt[e]
        })
    }

    function yt(e) {
        e = w(e);
        return {
            scrollLeft: e.pageXOffset,
            scrollTop: e.pageYOffset
        }
    }

    function bt(e) {
        return k(C(e)).left + yt(e).scrollLeft
    }

    function wt(e) {
        var e = T(e),
            t = e.overflow,
            i = e.overflowX,
            e = e.overflowY;
        return /auto|scroll|overlay|hidden/.test(t + e + i)
    }

    function _t(e, t) {
        void 0 === t && (t = []);
        var i = function e(t) {
                return 0 <= ["html", "body", "#document"].indexOf(v(t)) ? t.ownerDocument.body : _(t) && wt(t) ? t : e(tt(t))
            }(e),
            e = i === (null == (e = e.ownerDocument) ? void 0 : e.body),
            o = w(i),
            o = e ? [o].concat(o.visualViewport || [], wt(i) ? i : []) : i,
            i = t.concat(o);
        return e ? i : i.concat(_t(tt(o)))
    }

    function xt(e) {
        return Object.assign({}, e, {
            left: e.x,
            top: e.y,
            right: e.x + e.width,
            bottom: e.y + e.height
        })
    }

    function kt(e, t, i) {
        return t === je ? xt((n = i, r = w(o = e), a = C(o), r = r.visualViewport, l = a.clientWidth, a = a.clientHeight, d = c = 0, r && (l = r.width, a = r.height, (s = Ze()) || !s && "fixed" === n) && (c = r.offsetLeft, d = r.offsetTop), {
            width: l,
            height: a,
            x: c + bt(o),
            y: d
        })) : y(t) ? ((n = k(s = t, !1, "fixed" === i)).top = n.top + s.clientTop, n.left = n.left + s.clientLeft, n.bottom = n.top + s.clientHeight, n.right = n.left + s.clientWidth, n.width = s.clientWidth, n.height = s.clientHeight, n.x = n.left, n.y = n.top, n) : xt((r = C(e), l = C(r), a = yt(r), c = null == (c = r.ownerDocument) ? void 0 : c.body, o = S(l.scrollWidth, l.clientWidth, c ? c.scrollWidth : 0, c ? c.clientWidth : 0), d = S(l.scrollHeight, l.clientHeight, c ? c.scrollHeight : 0, c ? c.clientHeight : 0), r = -a.scrollLeft + bt(r), a = -a.scrollTop, "rtl" === T(c || l).direction && (r += S(l.clientWidth, c ? c.clientWidth : 0) - o), {
            width: o,
            height: d,
            x: r,
            y: a
        }));
        var o, n, s, r, a, l, c, d
    }

    function Tt(e) {
        var t, i = e.reference,
            o = e.element,
            e = e.placement,
            n = e ? P(e) : null,
            e = e ? M(e) : null,
            s = i.x + i.width / 2 - o.width / 2,
            r = i.y + i.height / 2 - o.height / 2;
        switch (n) {
            case E:
                t = {
                    x: s,
                    y: i.y - o.height
                };
                break;
            case A:
                t = {
                    x: s,
                    y: i.y + i.height
                };
                break;
            case O:
                t = {
                    x: i.x + i.width,
                    y: r
                };
                break;
            case $:
                t = {
                    x: i.x - o.width,
                    y: r
                };
                break;
            default:
                t = {
                    x: i.x,
                    y: i.y
                }
        }
        var a = n ? nt(n) : null;
        if (null != a) {
            var l = "y" === a ? "height" : "width";
            switch (e) {
                case I:
                    t[a] = t[a] - (i[l] / 2 - o[l] / 2);
                    break;
                case b:
                    t[a] = t[a] + (i[l] / 2 - o[l] / 2)
            }
        }
        return t
    }

    function Ct(e, t) {
        var i, o, n, s, r, a, t = t = void 0 === t ? {} : t,
            l = t.placement,
            l = void 0 === l ? e.placement : l,
            c = t.strategy,
            c = void 0 === c ? e.strategy : c,
            d = t.boundary,
            d = void 0 === d ? ze : d,
            u = t.rootBoundary,
            u = void 0 === u ? je : u,
            p = t.elementContext,
            p = void 0 === p ? g : p,
            h = t.altBoundary,
            h = void 0 !== h && h,
            t = t.padding,
            t = void 0 === t ? 0 : t,
            t = rt("number" != typeof t ? t : at(t, L)),
            f = e.rects.popper,
            h = e.elements[h ? p === g ? He : g : p],
            c = (i = y(h) ? h : h.contextElement || C(e.elements.popper), h = u, o = c, s = "clippingParents" === (u = d) ? (r = _t(tt(s = i)), y(n = 0 <= ["absolute", "fixed"].indexOf(T(s).position) && _(s) ? ot(s) : s) ? r.filter(function(e) {
                return y(e) && et(e, n) && "body" !== v(e)
            }) : []) : [].concat(u), r = [].concat(s, [h]), u = r[0], (h = r.reduce(function(e, t) {
                t = kt(i, t, o);
                return e.top = S(t.top, e.top), e.right = Qe(t.right, e.right), e.bottom = Qe(t.bottom, e.bottom), e.left = S(t.left, e.left), e
            }, kt(i, u, o))).width = h.right - h.left, h.height = h.bottom - h.top, h.x = h.left, h.y = h.top, h),
            d = k(e.elements.reference),
            u = Tt({
                reference: d,
                element: f,
                strategy: "absolute",
                placement: l
            }),
            h = xt(Object.assign({}, f, u)),
            f = p === g ? h : d,
            m = {
                top: c.top - f.top + t.top,
                bottom: f.bottom - c.bottom + t.bottom,
                left: c.left - f.left + t.left,
                right: f.right - c.right + t.right
            },
            u = e.modifiersData.offset;
        return p === g && u && (a = u[l], Object.keys(m).forEach(function(e) {
            var t = 0 <= [O, A].indexOf(e) ? 1 : -1,
                i = 0 <= [E, A].indexOf(e) ? "y" : "x";
            m[e] += a[i] * t
        })), m
    }
    var St = {
        name: "flip",
        enabled: !0,
        phase: "main",
        fn: function(e) {
            var u = e.state,
                t = e.options,
                e = e.name;
            if (!u.modifiersData[e]._skip) {
                for (var i = t.mainAxis, o = void 0 === i || i, i = t.altAxis, n = void 0 === i || i, i = t.fallbackPlacements, p = t.padding, h = t.boundary, f = t.rootBoundary, s = t.altBoundary, r = t.flipVariations, m = void 0 === r || r, g = t.allowedAutoPlacements, r = u.options.placement, t = P(r), i = i || (t !== r && m ? P(i = r) === De ? [] : (t = mt(i), [vt(i), t, vt(t)]) : [mt(r)]), a = [r].concat(i).reduce(function(e, t) {
                        return e.concat(P(t) === De ? (i = u, o = (e = e = void 0 === (e = {
                            placement: t,
                            boundary: h,
                            rootBoundary: f,
                            padding: p,
                            flipVariations: m,
                            allowedAutoPlacements: g
                        }) ? {} : e).placement, n = e.boundary, s = e.rootBoundary, r = e.padding, a = e.flipVariations, l = void 0 === (e = e.allowedAutoPlacements) ? We : e, c = M(o), e = c ? a ? Ne : Ne.filter(function(e) {
                            return M(e) === c
                        }) : L, d = (o = 0 === (o = e.filter(function(e) {
                            return 0 <= l.indexOf(e)
                        })).length ? e : o).reduce(function(e, t) {
                            return e[t] = Ct(i, {
                                placement: t,
                                boundary: n,
                                rootBoundary: s,
                                padding: r
                            })[P(t)], e
                        }, {}), Object.keys(d).sort(function(e, t) {
                            return d[e] - d[t]
                        })) : t);
                        var i, o, n, s, r, a, l, c, d
                    }, []), l = u.rects.reference, c = u.rects.popper, d = new Map, v = !0, y = a[0], b = 0; b < a.length; b++) {
                    var w = a[b],
                        _ = P(w),
                        x = M(w) === I,
                        k = 0 <= [E, A].indexOf(_),
                        T = k ? "width" : "height",
                        C = Ct(u, {
                            placement: w,
                            boundary: h,
                            rootBoundary: f,
                            altBoundary: s,
                            padding: p
                        }),
                        k = k ? x ? O : $ : x ? A : E,
                        x = (l[T] > c[T] && (k = mt(k)), mt(k)),
                        T = [];
                    if (o && T.push(C[_] <= 0), n && T.push(C[k] <= 0, C[x] <= 0), T.every(function(e) {
                            return e
                        })) {
                        y = w, v = !1;
                        break
                    }
                    d.set(w, T)
                }
                if (v)
                    for (var S = m ? 3 : 1; 0 < S && "break" !== function(t) {
                            var e = a.find(function(e) {
                                e = d.get(e);
                                if (e) return e.slice(0, t).every(function(e) {
                                    return e
                                })
                            });
                            if (e) return y = e, "break"
                        }(S); S--);
                u.placement !== y && (u.modifiersData[e]._skip = !0, u.placement = y, u.reset = !0)
            }
        },
        requiresIfExists: ["offset"],
        data: {
            _skip: !1
        }
    };

    function Et(e, t, i) {
        return {
            top: e.top - t.height - (i = void 0 === i ? {
                x: 0,
                y: 0
            } : i).y,
            right: e.right - t.width + i.x,
            bottom: e.bottom - t.height + i.y,
            left: e.left - t.width - i.x
        }
    }

    function At(t) {
        return [E, O, A, $].some(function(e) {
            return 0 <= t[e]
        })
    }
    var Ot = {
            name: "hide",
            enabled: !0,
            phase: "main",
            requiresIfExists: ["preventOverflow"],
            fn: function(e) {
                var t = e.state,
                    e = e.name,
                    i = t.rects.reference,
                    o = t.rects.popper,
                    n = t.modifiersData.preventOverflow,
                    s = Ct(t, {
                        elementContext: "reference"
                    }),
                    r = Ct(t, {
                        altBoundary: !0
                    }),
                    s = Et(s, i),
                    i = Et(r, o, n),
                    r = At(s),
                    o = At(i);
                t.modifiersData[e] = {
                    referenceClippingOffsets: s,
                    popperEscapeOffsets: i,
                    isReferenceHidden: r,
                    hasPopperEscaped: o
                }, t.attributes.popper = Object.assign({}, t.attributes.popper, {
                    "data-popper-reference-hidden": r,
                    "data-popper-escaped": o
                })
            }
        },
        $t = {
            name: "offset",
            enabled: !0,
            phase: "main",
            requires: ["popperOffsets"],
            fn: function(e) {
                var r = e.state,
                    t = e.options,
                    e = e.name,
                    t = t.offset,
                    a = void 0 === t ? [0, 0] : t,
                    t = We.reduce(function(e, t) {
                        return e[t] = (t = t, i = r.rects, o = a, n = P(t), s = 0 <= [$, E].indexOf(n) ? -1 : 1, i = "function" == typeof o ? o(Object.assign({}, i, {
                            placement: t
                        })) : o, t = i[0] || 0, o = (i[1] || 0) * s, 0 <= [$, O].indexOf(n) ? {
                            x: o,
                            y: t
                        } : {
                            x: t,
                            y: o
                        }), e;
                        var i, o, n, s
                    }, {}),
                    i = t[r.placement],
                    o = i.x,
                    i = i.y;
                null != r.modifiersData.popperOffsets && (r.modifiersData.popperOffsets.x += o, r.modifiersData.popperOffsets.y += i), r.modifiersData[e] = t
            }
        },
        Lt = {
            name: "popperOffsets",
            enabled: !0,
            phase: "read",
            fn: function(e) {
                var t = e.state,
                    e = e.name;
                t.modifiersData[e] = Tt({
                    reference: t.rects.reference,
                    element: t.rects.popper,
                    strategy: "absolute",
                    placement: t.placement
                })
            },
            data: {}
        },
        It = {
            name: "preventOverflow",
            enabled: !0,
            phase: "main",
            fn: function(e) {
                var t, i, o, n, s, r, a, l, c, d = e.state,
                    u = e.options,
                    e = e.name,
                    p = u.mainAxis,
                    p = void 0 === p || p,
                    h = u.altAxis,
                    h = void 0 !== h && h,
                    f = u.boundary,
                    m = u.rootBoundary,
                    g = u.altBoundary,
                    v = u.padding,
                    y = u.tether,
                    y = void 0 === y || y,
                    u = u.tetherOffset,
                    u = void 0 === u ? 0 : u,
                    f = Ct(d, {
                        boundary: f,
                        rootBoundary: m,
                        padding: v,
                        altBoundary: g
                    }),
                    m = P(d.placement),
                    v = M(d.placement),
                    g = !v,
                    b = nt(m),
                    w = "x" === b ? "y" : "x",
                    _ = d.modifiersData.popperOffsets,
                    x = d.rects.reference,
                    k = d.rects.popper,
                    u = "function" == typeof u ? u(Object.assign({}, d.rects, {
                        placement: d.placement
                    })) : u,
                    u = "number" == typeof u ? {
                        mainAxis: u,
                        altAxis: u
                    } : Object.assign({
                        mainAxis: 0,
                        altAxis: 0
                    }, u),
                    T = d.modifiersData.offset ? d.modifiersData.offset[d.placement] : null,
                    C = {
                        x: 0,
                        y: 0
                    };
                _ && (p && (p = "y" === b ? "height" : "width", r = (a = _[b]) + f[i = "y" === b ? E : $], l = a - f[c = "y" === b ? A : O], t = y ? -k[p] / 2 : 0, n = (v === I ? x : k)[p], v = v === I ? -k[p] : -x[p], s = d.elements.arrow, s = y && s ? Je(s) : {
                    width: 0,
                    height: 0
                }, i = (o = d.modifiersData["arrow#persistent"] ? d.modifiersData["arrow#persistent"].padding : {
                    top: 0,
                    right: 0,
                    bottom: 0,
                    left: 0
                })[i], o = o[c], c = st(0, x[p], s[p]), s = g ? x[p] / 2 - t - c - i - u.mainAxis : n - c - i - u.mainAxis, n = g ? -x[p] / 2 + t + c + o + u.mainAxis : v + c + o + u.mainAxis, g = (i = d.elements.arrow && ot(d.elements.arrow)) ? "y" === b ? i.clientTop || 0 : i.clientLeft || 0 : 0, v = a + n - (t = null != (p = null == T ? void 0 : T[b]) ? p : 0), c = st(y ? Qe(r, a + s - t - g) : r, a, y ? S(l, v) : l), _[b] = c, C[b] = c - a), h && (o = "y" == w ? "height" : "width", n = (i = _[w]) + f["x" === b ? E : $], p = i - f["x" === b ? A : O], s = -1 !== [E, $].indexOf(m), g = null != (t = null == T ? void 0 : T[w]) ? t : 0, r = s ? n : i - x[o] - k[o] - g + u.altAxis, v = s ? i + x[o] + k[o] - g - u.altAxis : p, a = y && s ? (c = st(r, i, l = v), l < c ? l : c) : st(y ? r : n, i, y ? v : p), _[w] = a, C[w] = a - i), d.modifiersData[e] = C)
            },
            requiresIfExists: ["offset"]
        };

    function Pt(e) {
        var i = new Map,
            o = new Set,
            n = [];
        return e.forEach(function(e) {
            i.set(e.name, e)
        }), e.forEach(function(e) {
            o.has(e.name) || function t(e) {
                o.add(e.name), [].concat(e.requires || [], e.requiresIfExists || []).forEach(function(e) {
                    o.has(e) || (e = i.get(e)) && t(e)
                }), n.push(e)
            }(e)
        }), n
    }
    var Mt = {
        placement: "bottom",
        modifiers: [],
        strategy: "absolute"
    };

    function Dt() {
        for (var e = arguments.length, t = new Array(e), i = 0; i < e; i++) t[i] = arguments[i];
        return !t.some(function(e) {
            return !(e && "function" == typeof e.getBoundingClientRect)
        })
    }

    function zt(e) {
        var e = e = void 0 === e ? {} : e,
            t = e.defaultModifiers,
            l = void 0 === t ? [] : t,
            t = e.defaultOptions,
            c = void 0 === t ? Mt : t;
        return function(o, n, t) {
            void 0 === t && (t = c);
            var i, s, h = {
                    placement: "bottom",
                    orderedModifiers: [],
                    options: Object.assign({}, Mt, c),
                    modifiersData: {},
                    elements: {
                        reference: o,
                        popper: n
                    },
                    attributes: {},
                    styles: {}
                },
                r = [],
                f = !1,
                m = {
                    state: h,
                    setOptions: function(e) {
                        e = "function" == typeof e ? e(h.options) : e;
                        a(), h.options = Object.assign({}, c, h.options, e), h.scrollParents = {
                            reference: y(o) ? _t(o) : o.contextElement ? _t(o.contextElement) : [],
                            popper: _t(n)
                        };
                        e = [].concat(l, h.options.modifiers), t = e.reduce(function(e, t) {
                            var i = e[t.name];
                            return e[t.name] = i ? Object.assign({}, i, t, {
                                options: Object.assign({}, i.options, t.options),
                                data: Object.assign({}, i.data, t.data)
                            }) : t, e
                        }, {}), i = Pt(Object.keys(t).map(function(e) {
                            return t[e]
                        }));
                        var t, i, e = Ye.reduce(function(e, t) {
                            return e.concat(i.filter(function(e) {
                                return e.phase === t
                            }))
                        }, []);
                        return h.orderedModifiers = e.filter(function(e) {
                            return e.enabled
                        }), h.orderedModifiers.forEach(function(e) {
                            var t = e.name,
                                i = e.options,
                                e = e.effect;
                            "function" == typeof e && (e = e({
                                state: h,
                                name: t,
                                instance: m,
                                options: void 0 === i ? {} : i
                            }), r.push(e || function() {}))
                        }), m.update()
                    },
                    forceUpdate: function() {
                        if (!f) {
                            var e = h.elements,
                                t = e.reference,
                                e = e.popper;
                            if (Dt(t, e)) {
                                h.rects = {
                                    reference: (t = t, r = ot(e), void 0 === (a = "fixed" === h.options.strategy) && (a = !1), l = _(r), c = _(r) && (u = (c = r).getBoundingClientRect(), d = x(u.width) / c.offsetWidth || 1, u = x(u.height) / c.offsetHeight || 1, 1 !== d || 1 !== u), d = C(r), u = k(t, c, a), t = {
                                        scrollLeft: 0,
                                        scrollTop: 0
                                    }, p = {
                                        x: 0,
                                        y: 0
                                    }, !l && a || ("body" === v(r) && !wt(d) || (t = (l = r) !== w(l) && _(l) ? {
                                        scrollLeft: l.scrollLeft,
                                        scrollTop: l.scrollTop
                                    } : yt(l)), _(r) ? ((p = k(r, !0)).x += r.clientLeft, p.y += r.clientTop) : d && (p.x = bt(d))), {
                                        x: u.left + t.scrollLeft - p.x,
                                        y: u.top + t.scrollTop - p.y,
                                        width: u.width,
                                        height: u.height
                                    }),
                                    popper: Je(e)
                                }, h.reset = !1, h.placement = h.options.placement, h.orderedModifiers.forEach(function(e) {
                                    return h.modifiersData[e.name] = Object.assign({}, e.data)
                                });
                                for (var i, o, n, s = 0; s < h.orderedModifiers.length; s++) !0 !== h.reset ? (i = (n = h.orderedModifiers[s]).fn, o = n.options, n = n.name, "function" == typeof i && (h = i({
                                    state: h,
                                    options: void 0 === o ? {} : o,
                                    name: n,
                                    instance: m
                                }) || h)) : (h.reset = !1, s = -1)
                            }
                        }
                        var r, a, l, c, d, u, p
                    },
                    update: (i = function() {
                        return new Promise(function(e) {
                            m.forceUpdate(), e(h)
                        })
                    }, function() {
                        return s = s || new Promise(function(e) {
                            Promise.resolve().then(function() {
                                s = void 0, e(i())
                            })
                        })
                    }),
                    destroy: function() {
                        a(), f = !0
                    }
                };
            return Dt(o, n) && m.setOptions(t).then(function(e) {
                !f && t.onFirstUpdate && t.onFirstUpdate(e)
            }), m;

            function a() {
                r.forEach(function(e) {
                    return e()
                }), r = []
            }
        }
    }
    var jt = zt(),
        Ht = zt({
            defaultModifiers: [ht, Lt, ut, Ke]
        }),
        Nt = zt({
            defaultModifiers: [ht, Lt, ut, Ke, $t, St, It, lt, Ot]
        });
    const Wt = Object.freeze(Object.defineProperty({
            __proto__: null,
            afterMain: Fe,
            afterRead: qe,
            afterWrite: Ve,
            applyStyles: Ke,
            arrow: lt,
            auto: De,
            basePlacements: L,
            beforeMain: Re,
            beforeRead: Be,
            beforeWrite: Ue,
            bottom: A,
            clippingParents: ze,
            computeStyles: ut,
            createPopper: Nt,
            createPopperBase: jt,
            createPopperLite: Ht,
            detectOverflow: Ct,
            end: b,
            eventListeners: ht,
            flip: St,
            hide: Ot,
            left: $,
            main: "main",
            modifierPhases: Ye,
            offset: $t,
            placements: We,
            popper: g,
            popperGenerator: zt,
            popperOffsets: Lt,
            preventOverflow: It,
            read: "read",
            reference: He,
            right: O,
            start: I,
            top: E,
            variationPlacements: Ne,
            viewport: je,
            write: "write"
        }, Symbol.toStringTag, {
            value: "Module"
        })),
        Bt = "dropdown",
        qt = "ArrowDown",
        Rt = "click.bs.dropdown.data-api",
        Ft = "keydown.bs.dropdown.data-api",
        Ut = "show",
        h = '[data-bs-toggle="dropdown"]:not(.disabled):not(:disabled)',
        Vt = (h, ".dropdown-menu"),
        Yt = l() ? "top-end" : "top-start",
        Xt = l() ? "top-start" : "top-end",
        Kt = l() ? "bottom-end" : "bottom-start",
        Qt = l() ? "bottom-start" : "bottom-end",
        Gt = l() ? "left-start" : "right-start",
        Zt = l() ? "right-start" : "left-start",
        Jt = {
            autoClose: !0,
            boundary: "clippingParents",
            display: "dynamic",
            offset: [0, 2],
            popperConfig: null,
            reference: "toggle"
        },
        ei = {
            autoClose: "(boolean|string)",
            boundary: "(string|element)",
            display: "string",
            offset: "(array|string|function)",
            popperConfig: "(null|object|function)",
            reference: "(string|element|object)"
        };
    class m extends t {
        constructor(e, t) {
            super(e, t), this._popper = null, this._parent = this._element.parentNode, this._menu = u.next(this._element, Vt)[0] || u.prev(this._element, Vt)[0] || u.findOne(Vt, this._parent), this._inNavbar = this._detectNavbar()
        }
        static get Default() {
            return Jt
        }
        static get DefaultType() {
            return ei
        }
        static get NAME() {
            return Bt
        }
        toggle() {
            return this._isShown() ? this.hide() : this.show()
        }
        show() {
            if (!a(this._element) && !this._isShown()) {
                const e = {
                    relatedTarget: this._element
                };
                if (!f.trigger(this._element, "show.bs.dropdown", e).defaultPrevented) {
                    if (this._createPopper(), "ontouchstart" in document.documentElement && !this._parent.closest(".navbar-nav"))
                        for (const e of [].concat(...document.body.children)) f.on(e, "mouseover", B);
                    this._element.focus(), this._element.setAttribute("aria-expanded", !0), this._menu.classList.add(Ut), this._element.classList.add(Ut), f.trigger(this._element, "shown.bs.dropdown", e)
                }
            }
        }
        hide() {
            var e;
            !a(this._element) && this._isShown() && (e = {
                relatedTarget: this._element
            }, this._completeHide(e))
        }
        dispose() {
            this._popper && this._popper.destroy(), super.dispose()
        }
        update() {
            this._inNavbar = this._detectNavbar(), this._popper && this._popper.update()
        }
        _completeHide(e) {
            if (!f.trigger(this._element, "hide.bs.dropdown", e).defaultPrevented) {
                if ("ontouchstart" in document.documentElement)
                    for (const e of [].concat(...document.body.children)) f.off(e, "mouseover", B);
                this._popper && this._popper.destroy(), this._menu.classList.remove(Ut), this._element.classList.remove(Ut), this._element.setAttribute("aria-expanded", "false"), d.removeDataAttribute(this._menu, "popper"), f.trigger(this._element, "hidden.bs.dropdown", e)
            }
        }
        _getConfig(e) {
            if ("object" != typeof(e = super._getConfig(e)).reference || r(e.reference) || "function" == typeof e.reference.getBoundingClientRect) return e;
            throw new TypeError(Bt.toUpperCase() + ': Option "reference" provided type "object" without a required "getBoundingClientRect" method.')
        }
        _createPopper() {
            if (void 0 === Wt) throw new TypeError("Bootstrap's dropdowns require Popper (https://popper.js.org)");
            let e = this._element;
            "parent" === this._config.reference ? e = this._parent : r(this._config.reference) ? e = n(this._config.reference) : "object" == typeof this._config.reference && (e = this._config.reference);
            var t = this._getPopperConfig();
            this._popper = Nt(e, this._menu, t)
        }
        _isShown() {
            return this._menu.classList.contains(Ut)
        }
        _getPlacement() {
            var e, t = this._parent;
            return t.classList.contains("dropend") ? Gt : t.classList.contains("dropstart") ? Zt : t.classList.contains("dropup-center") ? "top" : t.classList.contains("dropdown-center") ? "bottom" : (e = "end" === getComputedStyle(this._menu).getPropertyValue("--bs-position").trim(), t.classList.contains("dropup") ? e ? Xt : Yt : e ? Qt : Kt)
        }
        _detectNavbar() {
            return null !== this._element.closest(".navbar")
        }
        _getOffset() {
            const t = this._config["offset"];
            return "string" == typeof t ? t.split(",").map(e => Number.parseInt(e, 10)) : "function" == typeof t ? e => t(e, this._element) : t
        }
        _getPopperConfig() {
            var e = {
                placement: this._getPlacement(),
                modifiers: [{
                    name: "preventOverflow",
                    options: {
                        boundary: this._config.boundary
                    }
                }, {
                    name: "offset",
                    options: {
                        offset: this._getOffset()
                    }
                }]
            };
            return !this._inNavbar && "static" !== this._config.display || (d.setDataAttribute(this._menu, "popper", "static"), e.modifiers = [{
                name: "applyStyles",
                enabled: !1
            }]), {
                ...e,
                ...c(this._config.popperConfig, [e])
            }
        }
        _selectMenuItem({
            key: e,
            target: t
        }) {
            var i = u.find(".dropdown-menu .dropdown-item:not(.disabled):not(:disabled)", this._menu).filter(e => s(e));
            i.length && V(i, t, e === qt, !i.includes(t)).focus()
        }
        static jQueryInterface(t) {
            return this.each(function() {
                var e = m.getOrCreateInstance(this, t);
                if ("string" == typeof t) {
                    if (void 0 === e[t]) throw new TypeError(`No method named "${t}"`);
                    e[t]()
                }
            })
        }
        static clearMenus(e) {
            if (2 !== e.button && ("keyup" !== e.type || "Tab" === e.key)) {
                const o = u.find('[data-bs-toggle="dropdown"]:not(.disabled):not(:disabled).show');
                for (const n of o) {
                    const o = m.getInstance(n);
                    var t, i;
                    o && !1 !== o._config.autoClose && (t = (i = e.composedPath()).includes(o._menu), i.includes(o._element) || "inside" === o._config.autoClose && !t || "outside" === o._config.autoClose && t || o._menu.contains(e.target) && ("keyup" === e.type && "Tab" === e.key || /input|select|option|textarea|form/i.test(e.target.tagName)) || (i = {
                        relatedTarget: o._element
                    }, "click" === e.type && (i.clickEvent = e), o._completeHide(i)))
                }
            }
        }
        static dataApiKeydownHandler(e) {
            var t = /input|textarea/i.test(e.target.tagName),
                i = "Escape" === e.key,
                o = ["ArrowUp", qt].includes(e.key);
            !o && !i || t && !i || (e.preventDefault(), t = this.matches(h) ? this : u.prev(this, h)[0] || u.next(this, h)[0] || u.findOne(h, e.delegateTarget.parentNode), i = m.getOrCreateInstance(t), o ? (e.stopPropagation(), i.show(), i._selectMenuItem(e)) : i._isShown() && (e.stopPropagation(), i.hide(), t.focus()))
        }
    }
    f.on(document, Ft, h, m.dataApiKeydownHandler), f.on(document, Ft, Vt, m.dataApiKeydownHandler), f.on(document, Rt, m.clearMenus), f.on(document, "keyup.bs.dropdown.data-api", m.clearMenus), f.on(document, Rt, h, function(e) {
        e.preventDefault(), m.getOrCreateInstance(this).toggle()
    }), e(m);
    const ti = "mousedown.bs.backdrop",
        ii = {
            className: "modal-backdrop",
            clickCallback: null,
            isAnimated: !1,
            isVisible: !0,
            rootElement: "body"
        },
        oi = {
            className: "string",
            clickCallback: "(function|null)",
            isAnimated: "boolean",
            isVisible: "boolean",
            rootElement: "(element|string)"
        };
    class ni extends de {
        constructor(e) {
            super(), this._config = this._getConfig(e), this._isAppended = !1, this._element = null
        }
        static get Default() {
            return ii
        }
        static get DefaultType() {
            return oi
        }
        static get NAME() {
            return "backdrop"
        }
        show(e) {
            var t;
            this._config.isVisible ? (this._append(), t = this._getElement(), this._config.isAnimated && q(t), t.classList.add("show"), this._emulateAnimation(() => {
                c(e)
            })) : c(e)
        }
        hide(e) {
            this._config.isVisible ? (this._getElement().classList.remove("show"), this._emulateAnimation(() => {
                this.dispose(), c(e)
            })) : c(e)
        }
        dispose() {
            this._isAppended && (f.off(this._element, ti), this._element.remove(), this._isAppended = !1)
        }
        _getElement() {
            var e;
            return this._element || ((e = document.createElement("div")).className = this._config.className, this._config.isAnimated && e.classList.add("fade"), this._element = e), this._element
        }
        _configAfterMerge(e) {
            return e.rootElement = n(e.rootElement), e
        }
        _append() {
            var e;
            this._isAppended || (e = this._getElement(), this._config.rootElement.append(e), f.on(e, ti, () => {
                c(this._config.clickCallback)
            }), this._isAppended = !0)
        }
        _emulateAnimation(e) {
            U(e, this._getElement(), this._config.isAnimated)
        }
    }
    const si = ".bs.focustrap",
        ri = "backward",
        ai = {
            autofocus: !0,
            trapElement: null
        },
        li = {
            autofocus: "boolean",
            trapElement: "element"
        };
    class ci extends de {
        constructor(e) {
            super(), this._config = this._getConfig(e), this._isActive = !1, this._lastTabNavDirection = null
        }
        static get Default() {
            return ai
        }
        static get DefaultType() {
            return li
        }
        static get NAME() {
            return "focustrap"
        }
        activate() {
            this._isActive || (this._config.autofocus && this._config.trapElement.focus(), f.off(document, si), f.on(document, "focusin.bs.focustrap", e => this._handleFocusin(e)), f.on(document, "keydown.tab.bs.focustrap", e => this._handleKeydown(e)), this._isActive = !0)
        }
        deactivate() {
            this._isActive && (this._isActive = !1, f.off(document, si))
        }
        _handleFocusin(e) {
            var t = this._config["trapElement"];
            e.target === document || e.target === t || t.contains(e.target) || (0 === (e = u.focusableChildren(t)).length ? t : this._lastTabNavDirection === ri ? e[e.length - 1] : e[0]).focus()
        }
        _handleKeydown(e) {
            "Tab" === e.key && (this._lastTabNavDirection = e.shiftKey ? ri : "forward")
        }
    }
    const di = ".fixed-top, .fixed-bottom, .is-fixed, .sticky-top",
        ui = ".sticky-top",
        pi = "padding-right",
        hi = "margin-right";
    class fi {
        constructor() {
            this._element = document.body
        }
        getWidth() {
            var e = document.documentElement.clientWidth;
            return Math.abs(window.innerWidth - e)
        }
        hide() {
            const t = this.getWidth();
            this._disableOverFlow(), this._setElementAttributes(this._element, pi, e => e + t), this._setElementAttributes(di, pi, e => e + t), this._setElementAttributes(ui, hi, e => e - t)
        }
        reset() {
            this._resetElementAttributes(this._element, "overflow"), this._resetElementAttributes(this._element, pi), this._resetElementAttributes(di, pi), this._resetElementAttributes(ui, hi)
        }
        isOverflowing() {
            return 0 < this.getWidth()
        }
        _disableOverFlow() {
            this._saveInitialAttribute(this._element, "overflow"), this._element.style.overflow = "hidden"
        }
        _setElementAttributes(e, i, o) {
            const n = this.getWidth();
            this._applyManipulationCallback(e, e => {
                var t;
                e !== this._element && window.innerWidth > e.clientWidth + n || (this._saveInitialAttribute(e, i), t = window.getComputedStyle(e).getPropertyValue(i), e.style.setProperty(i, o(Number.parseFloat(t)) + "px"))
            })
        }
        _saveInitialAttribute(e, t) {
            var i = e.style.getPropertyValue(t);
            i && d.setDataAttribute(e, t, i)
        }
        _resetElementAttributes(e, i) {
            this._applyManipulationCallback(e, e => {
                var t = d.getDataAttribute(e, i);
                null !== t ? (d.removeDataAttribute(e, i), e.style.setProperty(i, t)) : e.style.removeProperty(i)
            })
        }
        _applyManipulationCallback(e, t) {
            if (r(e)) t(e);
            else
                for (const i of u.find(e, this._element)) t(i)
        }
    }
    const mi = ".bs.modal",
        gi = "hidden.bs.modal",
        vi = "show.bs.modal",
        yi = "modal-open",
        bi = "modal-static",
        wi = {
            backdrop: !0,
            focus: !0,
            keyboard: !0
        },
        _i = {
            backdrop: "(boolean|string)",
            focus: "boolean",
            keyboard: "boolean"
        };
    class xi extends t {
        constructor(e, t) {
            super(e, t), this._dialog = u.findOne(".modal-dialog", this._element), this._backdrop = this._initializeBackDrop(), this._focustrap = this._initializeFocusTrap(), this._isShown = !1, this._isTransitioning = !1, this._scrollBar = new fi, this._addEventListeners()
        }
        static get Default() {
            return wi
        }
        static get DefaultType() {
            return _i
        }
        static get NAME() {
            return "modal"
        }
        toggle(e) {
            return this._isShown ? this.hide() : this.show(e)
        }
        show(e) {
            this._isShown || this._isTransitioning || f.trigger(this._element, vi, {
                relatedTarget: e
            }).defaultPrevented || (this._isShown = !0, this._isTransitioning = !0, this._scrollBar.hide(), document.body.classList.add(yi), this._adjustDialog(), this._backdrop.show(() => this._showElement(e)))
        }
        hide() {
            !this._isShown || this._isTransitioning || f.trigger(this._element, "hide.bs.modal").defaultPrevented || (this._isShown = !1, this._isTransitioning = !0, this._focustrap.deactivate(), this._element.classList.remove("show"), this._queueCallback(() => this._hideModal(), this._element, this._isAnimated()))
        }
        dispose() {
            f.off(window, mi), f.off(this._dialog, mi), this._backdrop.dispose(), this._focustrap.deactivate(), super.dispose()
        }
        handleUpdate() {
            this._adjustDialog()
        }
        _initializeBackDrop() {
            return new ni({
                isVisible: Boolean(this._config.backdrop),
                isAnimated: this._isAnimated()
            })
        }
        _initializeFocusTrap() {
            return new ci({
                trapElement: this._element
            })
        }
        _showElement(e) {
            document.body.contains(this._element) || document.body.append(this._element), this._element.style.display = "block", this._element.removeAttribute("aria-hidden"), this._element.setAttribute("aria-modal", !0), this._element.setAttribute("role", "dialog"), this._element.scrollTop = 0;
            var t = u.findOne(".modal-body", this._dialog);
            t && (t.scrollTop = 0), q(this._element), this._element.classList.add("show"), this._queueCallback(() => {
                this._config.focus && this._focustrap.activate(), this._isTransitioning = !1, f.trigger(this._element, "shown.bs.modal", {
                    relatedTarget: e
                })
            }, this._dialog, this._isAnimated())
        }
        _addEventListeners() {
            f.on(this._element, "keydown.dismiss.bs.modal", e => {
                "Escape" === e.key && (this._config.keyboard ? this.hide() : this._triggerBackdropTransition())
            }), f.on(window, "resize.bs.modal", () => {
                this._isShown && !this._isTransitioning && this._adjustDialog()
            }), f.on(this._element, "mousedown.dismiss.bs.modal", t => {
                f.one(this._element, "click.dismiss.bs.modal", e => {
                    this._element === t.target && this._element === e.target && ("static" !== this._config.backdrop ? this._config.backdrop && this.hide() : this._triggerBackdropTransition())
                })
            })
        }
        _hideModal() {
            this._element.style.display = "none", this._element.setAttribute("aria-hidden", !0), this._element.removeAttribute("aria-modal"), this._element.removeAttribute("role"), this._isTransitioning = !1, this._backdrop.hide(() => {
                document.body.classList.remove(yi), this._resetAdjustments(), this._scrollBar.reset(), f.trigger(this._element, gi)
            })
        }
        _isAnimated() {
            return this._element.classList.contains("fade")
        }
        _triggerBackdropTransition() {
            if (!f.trigger(this._element, "hidePrevented.bs.modal").defaultPrevented) {
                const e = this._element.scrollHeight > document.documentElement.clientHeight,
                    t = this._element.style.overflowY;
                "hidden" === t || this._element.classList.contains(bi) || (e || (this._element.style.overflowY = "hidden"), this._element.classList.add(bi), this._queueCallback(() => {
                    this._element.classList.remove(bi), this._queueCallback(() => {
                        this._element.style.overflowY = t
                    }, this._dialog)
                }, this._dialog), this._element.focus())
            }
        }
        _adjustDialog() {
            const e = this._element.scrollHeight > document.documentElement.clientHeight,
                t = this._scrollBar.getWidth(),
                i = 0 < t;
            if (i && !e) {
                const e = l() ? "paddingLeft" : "paddingRight";
                this._element.style[e] = t + "px"
            }
            if (!i && e) {
                const e = l() ? "paddingRight" : "paddingLeft";
                this._element.style[e] = t + "px"
            }
        }
        _resetAdjustments() {
            this._element.style.paddingLeft = "", this._element.style.paddingRight = ""
        }
        static jQueryInterface(t, i) {
            return this.each(function() {
                var e = xi.getOrCreateInstance(this, t);
                if ("string" == typeof t) {
                    if (void 0 === e[t]) throw new TypeError(`No method named "${t}"`);
                    e[t](i)
                }
            })
        }
    }
    f.on(document, "click.bs.modal.data-api", '[data-bs-toggle="modal"]', function(e) {
        const t = u.getElementFromSelector(this);
        ["A", "AREA"].includes(this.tagName) && e.preventDefault(), f.one(t, vi, e => {
            e.defaultPrevented || f.one(t, gi, () => {
                s(this) && this.focus()
            })
        });
        e = u.findOne(".modal.show");
        e && xi.getInstance(e).hide(), xi.getOrCreateInstance(t).toggle(this)
    }), pe(xi), e(xi);
    const ki = "showing",
        Ti = ".offcanvas.show",
        Ci = "hidePrevented.bs.offcanvas",
        Si = "hidden.bs.offcanvas",
        Ei = {
            backdrop: !0,
            keyboard: !0,
            scroll: !1
        },
        Ai = {
            backdrop: "(boolean|string)",
            keyboard: "boolean",
            scroll: "boolean"
        };
    class D extends t {
        constructor(e, t) {
            super(e, t), this._isShown = !1, this._backdrop = this._initializeBackDrop(), this._focustrap = this._initializeFocusTrap(), this._addEventListeners()
        }
        static get Default() {
            return Ei
        }
        static get DefaultType() {
            return Ai
        }
        static get NAME() {
            return "offcanvas"
        }
        toggle(e) {
            return this._isShown ? this.hide() : this.show(e)
        }
        show(e) {
            this._isShown || f.trigger(this._element, "show.bs.offcanvas", {
                relatedTarget: e
            }).defaultPrevented || (this._isShown = !0, this._backdrop.show(), this._config.scroll || (new fi).hide(), this._element.setAttribute("aria-modal", !0), this._element.setAttribute("role", "dialog"), this._element.classList.add(ki), this._queueCallback(() => {
                this._config.scroll && !this._config.backdrop || this._focustrap.activate(), this._element.classList.add("show"), this._element.classList.remove(ki), f.trigger(this._element, "shown.bs.offcanvas", {
                    relatedTarget: e
                })
            }, this._element, !0))
        }
        hide() {
            !this._isShown || f.trigger(this._element, "hide.bs.offcanvas").defaultPrevented || (this._focustrap.deactivate(), this._element.blur(), this._isShown = !1, this._element.classList.add("hiding"), this._backdrop.hide(), this._queueCallback(() => {
                this._element.classList.remove("show", "hiding"), this._element.removeAttribute("aria-modal"), this._element.removeAttribute("role"), this._config.scroll || (new fi).reset(), f.trigger(this._element, Si)
            }, this._element, !0))
        }
        dispose() {
            this._backdrop.dispose(), this._focustrap.deactivate(), super.dispose()
        }
        _initializeBackDrop() {
            var e = Boolean(this._config.backdrop);
            return new ni({
                className: "offcanvas-backdrop",
                isVisible: e,
                isAnimated: !0,
                rootElement: this._element.parentNode,
                clickCallback: e ? () => {
                    "static" !== this._config.backdrop ? this.hide() : f.trigger(this._element, Ci)
                } : null
            })
        }
        _initializeFocusTrap() {
            return new ci({
                trapElement: this._element
            })
        }
        _addEventListeners() {
            f.on(this._element, "keydown.dismiss.bs.offcanvas", e => {
                "Escape" === e.key && (this._config.keyboard ? this.hide() : f.trigger(this._element, Ci))
            })
        }
        static jQueryInterface(t) {
            return this.each(function() {
                var e = D.getOrCreateInstance(this, t);
                if ("string" == typeof t) {
                    if (void 0 === e[t] || t.startsWith("_") || "constructor" === t) throw new TypeError(`No method named "${t}"`);
                    e[t](this)
                }
            })
        }
    }
    f.on(document, "click.bs.offcanvas.data-api", '[data-bs-toggle="offcanvas"]', function(e) {
        var t = u.getElementFromSelector(this);
        ["A", "AREA"].includes(this.tagName) && e.preventDefault(), a(this) || (f.one(t, Si, () => {
            s(this) && this.focus()
        }), (e = u.findOne(Ti)) && e !== t && D.getInstance(e).hide(), D.getOrCreateInstance(t).toggle(this))
    }), f.on(window, "load.bs.offcanvas.data-api", () => {
        for (const e of u.find(Ti)) D.getOrCreateInstance(e).show()
    }), f.on(window, "resize.bs.offcanvas", () => {
        for (const e of u.find("[aria-modal][class*=show][class*=offcanvas-]")) "fixed" !== getComputedStyle(e).position && D.getOrCreateInstance(e).hide()
    }), pe(D), e(D);
    const Oi = {
            "*": ["class", "dir", "id", "lang", "role", /^aria-[\w-]*$/i],
            a: ["target", "href", "title", "rel"],
            area: [],
            b: [],
            br: [],
            col: [],
            code: [],
            div: [],
            em: [],
            hr: [],
            h1: [],
            h2: [],
            h3: [],
            h4: [],
            h5: [],
            h6: [],
            i: [],
            img: ["src", "srcset", "alt", "title", "width", "height"],
            li: [],
            ol: [],
            p: [],
            pre: [],
            s: [],
            small: [],
            span: [],
            sub: [],
            sup: [],
            strong: [],
            u: [],
            ul: []
        },
        $i = new Set(["background", "cite", "href", "itemtype", "longdesc", "poster", "src", "xlink:href"]),
        Li = /^(?!javascript:)(?:[a-z0-9+.-]+:|[^&:/?#]*(?:[/?#]|$))/i,
        Ii = {
            allowList: Oi,
            content: {},
            extraClass: "",
            html: !1,
            sanitize: !0,
            sanitizeFn: null,
            template: "<div></div>"
        },
        Pi = {
            allowList: "object",
            content: "object",
            extraClass: "(string|function)",
            html: "boolean",
            sanitize: "boolean",
            sanitizeFn: "(null|function)",
            template: "string"
        },
        Mi = {
            entry: "(string|element|function|null)",
            selector: "(string|element)"
        };
    class Di extends de {
        constructor(e) {
            super(), this._config = this._getConfig(e)
        }
        static get Default() {
            return Ii
        }
        static get DefaultType() {
            return Pi
        }
        static get NAME() {
            return "TemplateFactory"
        }
        getContent() {
            return Object.values(this._config.content).map(e => this._resolvePossibleFunction(e)).filter(Boolean)
        }
        hasContent() {
            return 0 < this.getContent().length
        }
        changeContent(e) {
            return this._checkContent(e), this._config.content = {
                ...this._config.content,
                ...e
            }, this
        }
        toHtml() {
            var e = document.createElement("div");
            e.innerHTML = this._maybeSanitize(this._config.template);
            for (const [t, i] of Object.entries(this._config.content)) this._setContent(e, i, t);
            const t = e.children[0],
                i = this._resolvePossibleFunction(this._config.extraClass);
            return i && t.classList.add(...i.split(" ")), t
        }
        _typeCheckConfig(e) {
            super._typeCheckConfig(e), this._checkContent(e.content)
        }
        _checkContent(e) {
            for (var [t, i] of Object.entries(e)) super._typeCheckConfig({
                selector: t,
                entry: i
            }, Mi)
        }
        _setContent(e, t, i) {
            i = u.findOne(i, e);
            i && ((t = this._resolvePossibleFunction(t)) ? r(t) ? this._putElementInTemplate(n(t), i) : this._config.html ? i.innerHTML = this._maybeSanitize(t) : i.textContent = t : i.remove())
        }
        _maybeSanitize(e) {
            if (this._config.sanitize) {
                var t = e,
                    i = this._config.allowList,
                    o = this._config.sanitizeFn;
                if (!t.length) return t;
                if (o && "function" == typeof o) return o(t);
                const n = (new window.DOMParser).parseFromString(t, "text/html"),
                    s = [].concat(...n.body.querySelectorAll("*"));
                for (const t of s) {
                    const o = t.nodeName.toLowerCase();
                    if (Object.keys(i).includes(o)) {
                        const n = [].concat(...t.attributes),
                            s = [].concat(i["*"] || [], i[o] || []);
                        for (const i of n)((e, t) => {
                            const i = e.nodeName.toLowerCase();
                            return t.includes(i) ? !$i.has(i) || Boolean(Li.test(e.nodeValue)) : t.filter(e => e instanceof RegExp).some(e => e.test(i))
                        })(i, s) || t.removeAttribute(i.nodeName)
                    } else t.remove()
                }
                return n.body.innerHTML
            }
            return e
        }
        _resolvePossibleFunction(e) {
            return c(e, [this])
        }
        _putElementInTemplate(e, t) {
            this._config.html ? (t.innerHTML = "", t.append(e)) : t.textContent = e.textContent
        }
    }
    const zi = new Set(["sanitize", "allowList", "sanitizeFn"]),
        ji = "fade",
        Hi = "show",
        Ni = "hide.bs.modal",
        Wi = "hover",
        Bi = {
            AUTO: "auto",
            TOP: "top",
            RIGHT: l() ? "left" : "right",
            BOTTOM: "bottom",
            LEFT: l() ? "right" : "left"
        },
        qi = {
            allowList: Oi,
            animation: !0,
            boundary: "clippingParents",
            container: !1,
            customClass: "",
            delay: 0,
            fallbackPlacements: ["top", "right", "bottom", "left"],
            html: !1,
            offset: [0, 6],
            placement: "top",
            popperConfig: null,
            sanitize: !0,
            sanitizeFn: null,
            selector: !1,
            template: '<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',
            title: "",
            trigger: "hover focus"
        },
        Ri = {
            allowList: "object",
            animation: "boolean",
            boundary: "(string|element)",
            container: "(string|element|boolean)",
            customClass: "(string|function)",
            delay: "(number|object)",
            fallbackPlacements: "array",
            html: "boolean",
            offset: "(array|string|function)",
            placement: "(string|function)",
            popperConfig: "(null|object|function)",
            sanitize: "boolean",
            sanitizeFn: "(null|function)",
            selector: "(string|boolean)",
            template: "string",
            title: "(string|element|function)",
            trigger: "string"
        };
    class Fi extends t {
        constructor(e, t) {
            if (void 0 === Wt) throw new TypeError("Bootstrap's tooltips require Popper (https://popper.js.org)");
            super(e, t), this._isEnabled = !0, this._timeout = 0, this._isHovered = null, this._activeTrigger = {}, this._popper = null, this._templateFactory = null, this._newContent = null, this.tip = null, this._setListeners(), this._config.selector || this._fixTitle()
        }
        static get Default() {
            return qi
        }
        static get DefaultType() {
            return Ri
        }
        static get NAME() {
            return "tooltip"
        }
        enable() {
            this._isEnabled = !0
        }
        disable() {
            this._isEnabled = !1
        }
        toggleEnabled() {
            this._isEnabled = !this._isEnabled
        }
        toggle() {
            this._isEnabled && (this._activeTrigger.click = !this._activeTrigger.click, this._isShown() ? this._leave() : this._enter())
        }
        dispose() {
            clearTimeout(this._timeout), f.off(this._element.closest(".modal"), Ni, this._hideModalHandler), this._element.getAttribute("data-bs-original-title") && this._element.setAttribute("title", this._element.getAttribute("data-bs-original-title")), this._disposePopper(), super.dispose()
        }
        show() {
            if ("none" === this._element.style.display) throw new Error("Please use show on visible elements");
            if (this._isWithContent() && this._isEnabled) {
                const i = f.trigger(this._element, this.constructor.eventName("show")),
                    o = (W(this._element) || this._element.ownerDocument.documentElement).contains(this._element);
                if (!i.defaultPrevented && o) {
                    this._disposePopper();
                    var e = this._getTipElement(),
                        t = (this._element.setAttribute("aria-describedby", e.getAttribute("id")), this._config)["container"];
                    if (this._element.ownerDocument.documentElement.contains(this.tip) || (t.append(e), f.trigger(this._element, this.constructor.eventName("inserted"))), this._popper = this._createPopper(e), e.classList.add(Hi), "ontouchstart" in document.documentElement)
                        for (const i of [].concat(...document.body.children)) f.on(i, "mouseover", B);
                    this._queueCallback(() => {
                        f.trigger(this._element, this.constructor.eventName("shown")), !1 === this._isHovered && this._leave(), this._isHovered = !1
                    }, this.tip, this._isAnimated())
                }
            }
        }
        hide() {
            if (this._isShown() && !f.trigger(this._element, this.constructor.eventName("hide")).defaultPrevented) {
                if (this._getTipElement().classList.remove(Hi), "ontouchstart" in document.documentElement)
                    for (const e of [].concat(...document.body.children)) f.off(e, "mouseover", B);
                this._activeTrigger.click = !1, this._activeTrigger.focus = !1, this._activeTrigger.hover = !1, this._isHovered = null, this._queueCallback(() => {
                    this._isWithActiveTrigger() || (this._isHovered || this._disposePopper(), this._element.removeAttribute("aria-describedby"), f.trigger(this._element, this.constructor.eventName("hidden")))
                }, this.tip, this._isAnimated())
            }
        }
        update() {
            this._popper && this._popper.update()
        }
        _isWithContent() {
            return Boolean(this._getTitle())
        }
        _getTipElement() {
            return this.tip || (this.tip = this._createTipElement(this._newContent || this._getContentForTemplate())), this.tip
        }
        _createTipElement(e) {
            e = this._getTemplateFactory(e).toHtml();
            if (!e) return null;
            e.classList.remove(ji, Hi), e.classList.add(`bs-${this.constructor.NAME}-auto`);
            var t = (e => {
                for (; e += Math.floor(1e6 * Math.random()), document.getElementById(e););
                return e
            })(this.constructor.NAME).toString();
            return e.setAttribute("id", t), this._isAnimated() && e.classList.add(ji), e
        }
        setContent(e) {
            this._newContent = e, this._isShown() && (this._disposePopper(), this.show())
        }
        _getTemplateFactory(e) {
            return this._templateFactory ? this._templateFactory.changeContent(e) : this._templateFactory = new Di({
                ...this._config,
                content: e,
                extraClass: this._resolvePossibleFunction(this._config.customClass)
            }), this._templateFactory
        }
        _getContentForTemplate() {
            return {
                ".tooltip-inner": this._getTitle()
            }
        }
        _getTitle() {
            return this._resolvePossibleFunction(this._config.title) || this._element.getAttribute("data-bs-original-title")
        }
        _initializeOnDelegatedTarget(e) {
            return this.constructor.getOrCreateInstance(e.delegateTarget, this._getDelegateConfig())
        }
        _isAnimated() {
            return this._config.animation || this.tip && this.tip.classList.contains(ji)
        }
        _isShown() {
            return this.tip && this.tip.classList.contains(Hi)
        }
        _createPopper(e) {
            var t = c(this._config.placement, [this, e, this._element]),
                t = Bi[t.toUpperCase()];
            return Nt(this._element, e, this._getPopperConfig(t))
        }
        _getOffset() {
            const t = this._config["offset"];
            return "string" == typeof t ? t.split(",").map(e => Number.parseInt(e, 10)) : "function" == typeof t ? e => t(e, this._element) : t
        }
        _resolvePossibleFunction(e) {
            return c(e, [this._element])
        }
        _getPopperConfig(e) {
            e = {
                placement: e,
                modifiers: [{
                    name: "flip",
                    options: {
                        fallbackPlacements: this._config.fallbackPlacements
                    }
                }, {
                    name: "offset",
                    options: {
                        offset: this._getOffset()
                    }
                }, {
                    name: "preventOverflow",
                    options: {
                        boundary: this._config.boundary
                    }
                }, {
                    name: "arrow",
                    options: {
                        element: `.${this.constructor.NAME}-arrow`
                    }
                }, {
                    name: "preSetPlacement",
                    enabled: !0,
                    phase: "beforeMain",
                    fn: e => {
                        this._getTipElement().setAttribute("data-popper-placement", e.state.placement)
                    }
                }]
            };
            return {
                ...e,
                ...c(this._config.popperConfig, [e])
            }
        }
        _setListeners() {
            const e = this._config.trigger.split(" ");
            for (const t of e)
                if ("click" === t) f.on(this._element, this.constructor.eventName("click"), this._config.selector, e => {
                    this._initializeOnDelegatedTarget(e).toggle()
                });
                else if ("manual" !== t) {
                const e = t === Wi ? this.constructor.eventName("mouseenter") : this.constructor.eventName("focusin"),
                    i = t === Wi ? this.constructor.eventName("mouseleave") : this.constructor.eventName("focusout");
                f.on(this._element, e, this._config.selector, e => {
                    var t = this._initializeOnDelegatedTarget(e);
                    t._activeTrigger["focusin" === e.type ? "focus" : Wi] = !0, t._enter()
                }), f.on(this._element, i, this._config.selector, e => {
                    var t = this._initializeOnDelegatedTarget(e);
                    t._activeTrigger["focusout" === e.type ? "focus" : Wi] = t._element.contains(e.relatedTarget), t._leave()
                })
            }
            this._hideModalHandler = () => {
                this._element && this.hide()
            }, f.on(this._element.closest(".modal"), Ni, this._hideModalHandler)
        }
        _fixTitle() {
            var e = this._element.getAttribute("title");
            e && (this._element.getAttribute("aria-label") || this._element.textContent.trim() || this._element.setAttribute("aria-label", e), this._element.setAttribute("data-bs-original-title", e), this._element.removeAttribute("title"))
        }
        _enter() {
            this._isShown() || this._isHovered ? this._isHovered = !0 : (this._isHovered = !0, this._setTimeout(() => {
                this._isHovered && this.show()
            }, this._config.delay.show))
        }
        _leave() {
            this._isWithActiveTrigger() || (this._isHovered = !1, this._setTimeout(() => {
                this._isHovered || this.hide()
            }, this._config.delay.hide))
        }
        _setTimeout(e, t) {
            clearTimeout(this._timeout), this._timeout = setTimeout(e, t)
        }
        _isWithActiveTrigger() {
            return Object.values(this._activeTrigger).includes(!0)
        }
        _getConfig(e) {
            var t = d.getDataAttributes(this._element);
            for (const e of Object.keys(t)) zi.has(e) && delete t[e];
            return e = {
                ...t,
                ..."object" == typeof e && e ? e : {}
            }, e = this._mergeConfigObj(e), e = this._configAfterMerge(e), this._typeCheckConfig(e), e
        }
        _configAfterMerge(e) {
            return e.container = !1 === e.container ? document.body : n(e.container), "number" == typeof e.delay && (e.delay = {
                show: e.delay,
                hide: e.delay
            }), "number" == typeof e.title && (e.title = e.title.toString()), "number" == typeof e.content && (e.content = e.content.toString()), e
        }
        _getDelegateConfig() {
            var e, t, i = {};
            for ([e, t] of Object.entries(this._config)) this.constructor.Default[e] !== t && (i[e] = t);
            return i.selector = !1, i.trigger = "manual", i
        }
        _disposePopper() {
            this._popper && (this._popper.destroy(), this._popper = null), this.tip && (this.tip.remove(), this.tip = null)
        }
        static jQueryInterface(t) {
            return this.each(function() {
                var e = Fi.getOrCreateInstance(this, t);
                if ("string" == typeof t) {
                    if (void 0 === e[t]) throw new TypeError(`No method named "${t}"`);
                    e[t]()
                }
            })
        }
    }
    e(Fi);
    const Ui = {
            ...Fi.Default,
            content: "",
            offset: [0, 8],
            placement: "right",
            template: '<div class="popover" role="tooltip"><div class="popover-arrow"></div><h3 class="popover-header"></h3><div class="popover-body"></div></div>',
            trigger: "click"
        },
        Vi = {
            ...Fi.DefaultType,
            content: "(null|string|element|function)"
        };
    class Yi extends Fi {
        static get Default() {
            return Ui
        }
        static get DefaultType() {
            return Vi
        }
        static get NAME() {
            return "popover"
        }
        _isWithContent() {
            return this._getTitle() || this._getContent()
        }
        _getContentForTemplate() {
            return {
                ".popover-header": this._getTitle(),
                ".popover-body": this._getContent()
            }
        }
        _getContent() {
            return this._resolvePossibleFunction(this._config.content)
        }
        static jQueryInterface(t) {
            return this.each(function() {
                var e = Yi.getOrCreateInstance(this, t);
                if ("string" == typeof t) {
                    if (void 0 === e[t]) throw new TypeError(`No method named "${t}"`);
                    e[t]()
                }
            })
        }
    }
    e(Yi);
    const Xi = "click.bs.scrollspy",
        Ki = "active",
        Qi = {
            offset: null,
            rootMargin: "0px 0px -25%",
            smoothScroll: !1,
            target: null,
            threshold: [.1, .5, 1]
        },
        Gi = {
            offset: "(number|null)",
            rootMargin: "string",
            smoothScroll: "boolean",
            target: "element",
            threshold: "array"
        };
    class Zi extends t {
        constructor(e, t) {
            super(e, t), this._targetLinks = new Map, this._observableSections = new Map, this._rootElement = "visible" === getComputedStyle(this._element).overflowY ? null : this._element, this._activeTarget = null, this._observer = null, this._previousScrollData = {
                visibleEntryTop: 0,
                parentScrollTop: 0
            }, this.refresh()
        }
        static get Default() {
            return Qi
        }
        static get DefaultType() {
            return Gi
        }
        static get NAME() {
            return "scrollspy"
        }
        refresh() {
            this._initializeTargetsAndObservables(), this._maybeEnableSmoothScroll(), this._observer ? this._observer.disconnect() : this._observer = this._getNewObserver();
            for (const e of this._observableSections.values()) this._observer.observe(e)
        }
        dispose() {
            this._observer.disconnect(), super.dispose()
        }
        _configAfterMerge(e) {
            return e.target = n(e.target) || document.body, e.rootMargin = e.offset ? e.offset + "px 0px -30%" : e.rootMargin, "string" == typeof e.threshold && (e.threshold = e.threshold.split(",").map(e => Number.parseFloat(e))), e
        }
        _maybeEnableSmoothScroll() {
            this._config.smoothScroll && (f.off(this._config.target, Xi), f.on(this._config.target, Xi, "[href]", e => {
                var t = this._observableSections.get(e.target.hash);
                t && (e.preventDefault(), e = this._rootElement || window, t = t.offsetTop - this._element.offsetTop, e.scrollTo ? e.scrollTo({
                    top: t,
                    behavior: "smooth"
                }) : e.scrollTop = t)
            }))
        }
        _getNewObserver() {
            var e = {
                root: this._rootElement,
                threshold: this._config.threshold,
                rootMargin: this._config.rootMargin
            };
            return new IntersectionObserver(e => this._observerCallback(e), e)
        }
        _observerCallback(e) {
            const t = e => this._targetLinks.get("#" + e.target.id),
                i = e => {
                    this._previousScrollData.visibleEntryTop = e.target.offsetTop, this._process(t(e))
                },
                o = (this._rootElement || document.documentElement).scrollTop,
                n = o >= this._previousScrollData.parentScrollTop;
            this._previousScrollData.parentScrollTop = o;
            for (const s of e)
                if (s.isIntersecting) {
                    const e = s.target.offsetTop >= this._previousScrollData.visibleEntryTop;
                    if (n && e) {
                        if (i(s), !o) return
                    } else n || e || i(s)
                } else this._activeTarget = null, this._clearActiveClass(t(s))
        }
        _initializeTargetsAndObservables() {
            this._targetLinks = new Map, this._observableSections = new Map;
            const e = u.find("[href]", this._config.target);
            for (const t of e)
                if (t.hash && !a(t)) {
                    const e = u.findOne(decodeURI(t.hash), this._element);
                    s(e) && (this._targetLinks.set(decodeURI(t.hash), t), this._observableSections.set(t.hash, e))
                }
        }
        _process(e) {
            this._activeTarget !== e && (this._clearActiveClass(this._config.target), (this._activeTarget = e).classList.add(Ki), this._activateParents(e), f.trigger(this._element, "activate.bs.scrollspy", {
                relatedTarget: e
            }))
        }
        _activateParents(e) {
            if (e.classList.contains("dropdown-item")) u.findOne(".dropdown-toggle", e.closest(".dropdown")).classList.add(Ki);
            else
                for (const t of u.parents(e, ".nav, .list-group"))
                    for (const e of u.prev(t, ".nav-link, .nav-item > .nav-link, .list-group-item")) e.classList.add(Ki)
        }
        _clearActiveClass(e) {
            e.classList.remove(Ki);
            var t = u.find("[href].active", e);
            for (const e of t) e.classList.remove(Ki)
        }
        static jQueryInterface(t) {
            return this.each(function() {
                var e = Zi.getOrCreateInstance(this, t);
                if ("string" == typeof t) {
                    if (void 0 === e[t] || t.startsWith("_") || "constructor" === t) throw new TypeError(`No method named "${t}"`);
                    e[t]()
                }
            })
        }
    }
    f.on(window, "load.bs.scrollspy.data-api", () => {
        for (const e of u.find('[data-bs-spy="scroll"]')) Zi.getOrCreateInstance(e)
    }), e(Zi);
    const Ji = "ArrowRight",
        eo = "ArrowDown",
        to = "active",
        io = "show",
        oo = '[data-bs-toggle="tab"], [data-bs-toggle="pill"], [data-bs-toggle="list"]',
        no = '.nav-link:not(.dropdown-toggle), .list-group-item:not(.dropdown-toggle), [role="tab"]:not(.dropdown-toggle), ' + oo;
    class so extends t {
        constructor(e) {
            super(e), this._parent = this._element.closest('.list-group, .nav, [role="tablist"]'), this._parent && (this._setInitialAttributes(this._parent, this._getChildren()), f.on(this._element, "keydown.bs.tab", e => this._keydown(e)))
        }
        static get NAME() {
            return "tab"
        }
        show() {
            var e, t, i = this._element;
            this._elemIsActive(i) || (t = (e = this._getActiveElem()) ? f.trigger(e, "hide.bs.tab", {
                relatedTarget: i
            }) : null, f.trigger(i, "show.bs.tab", {
                relatedTarget: e
            }).defaultPrevented) || t && t.defaultPrevented || (this._deactivate(e, i), this._activate(i, e))
        }
        _activate(e, t) {
            e && (e.classList.add(to), this._activate(u.getElementFromSelector(e)), this._queueCallback(() => {
                "tab" === e.getAttribute("role") ? (e.removeAttribute("tabindex"), e.setAttribute("aria-selected", !0), this._toggleDropDown(e, !0), f.trigger(e, "shown.bs.tab", {
                    relatedTarget: t
                })) : e.classList.add(io)
            }, e, e.classList.contains("fade")))
        }
        _deactivate(e, t) {
            e && (e.classList.remove(to), e.blur(), this._deactivate(u.getElementFromSelector(e)), this._queueCallback(() => {
                "tab" === e.getAttribute("role") ? (e.setAttribute("aria-selected", !1), e.setAttribute("tabindex", "-1"), this._toggleDropDown(e, !1), f.trigger(e, "hidden.bs.tab", {
                    relatedTarget: t
                })) : e.classList.remove(io)
            }, e, e.classList.contains("fade")))
        }
        _keydown(e) {
            var t;
            ["ArrowLeft", Ji, "ArrowUp", eo].includes(e.key) && (e.stopPropagation(), e.preventDefault(), t = [Ji, eo].includes(e.key), e = V(this._getChildren().filter(e => !a(e)), e.target, t, !0)) && (e.focus({
                preventScroll: !0
            }), so.getOrCreateInstance(e).show())
        }
        _getChildren() {
            return u.find(no, this._parent)
        }
        _getActiveElem() {
            return this._getChildren().find(e => this._elemIsActive(e)) || null
        }
        _setInitialAttributes(e, t) {
            this._setAttributeIfNotExists(e, "role", "tablist");
            for (const e of t) this._setInitialAttributesOnChild(e)
        }
        _setInitialAttributesOnChild(e) {
            e = this._getInnerElement(e);
            var t = this._elemIsActive(e),
                i = this._getOuterElement(e);
            e.setAttribute("aria-selected", t), i !== e && this._setAttributeIfNotExists(i, "role", "presentation"), t || e.setAttribute("tabindex", "-1"), this._setAttributeIfNotExists(e, "role", "tab"), this._setInitialAttributesOnTargetPanel(e)
        }
        _setInitialAttributesOnTargetPanel(e) {
            var t = u.getElementFromSelector(e);
            t && (this._setAttributeIfNotExists(t, "role", "tabpanel"), e.id) && this._setAttributeIfNotExists(t, "aria-labelledby", "" + e.id)
        }
        _toggleDropDown(e, i) {
            const o = this._getOuterElement(e);
            o.classList.contains("dropdown") && ((e = (e, t) => {
                e = u.findOne(e, o);
                e && e.classList.toggle(t, i)
            })(".dropdown-toggle", to), e(".dropdown-menu", io), o.setAttribute("aria-expanded", i))
        }
        _setAttributeIfNotExists(e, t, i) {
            e.hasAttribute(t) || e.setAttribute(t, i)
        }
        _elemIsActive(e) {
            return e.classList.contains(to)
        }
        _getInnerElement(e) {
            return e.matches(no) ? e : u.findOne(no, e)
        }
        _getOuterElement(e) {
            return e.closest(".nav-item, .list-group-item") || e
        }
        static jQueryInterface(t) {
            return this.each(function() {
                var e = so.getOrCreateInstance(this);
                if ("string" == typeof t) {
                    if (void 0 === e[t] || t.startsWith("_") || "constructor" === t) throw new TypeError(`No method named "${t}"`);
                    e[t]()
                }
            })
        }
    }
    f.on(document, "click.bs.tab", oo, function(e) {
        ["A", "AREA"].includes(this.tagName) && e.preventDefault(), a(this) || so.getOrCreateInstance(this).show()
    }), f.on(window, "load.bs.tab", () => {
        for (const e of u.find('.active[data-bs-toggle="tab"], .active[data-bs-toggle="pill"], .active[data-bs-toggle="list"]')) so.getOrCreateInstance(e)
    }), e(so);
    const ro = "show",
        ao = "showing",
        lo = {
            animation: "boolean",
            autohide: "boolean",
            delay: "number"
        },
        co = {
            animation: !0,
            autohide: !0,
            delay: 5e3
        };
    class uo extends t {
        constructor(e, t) {
            super(e, t), this._timeout = null, this._hasMouseInteraction = !1, this._hasKeyboardInteraction = !1, this._setListeners()
        }
        static get Default() {
            return co
        }
        static get DefaultType() {
            return lo
        }
        static get NAME() {
            return "toast"
        }
        show() {
            f.trigger(this._element, "show.bs.toast").defaultPrevented || (this._clearTimeout(), this._config.animation && this._element.classList.add("fade"), this._element.classList.remove("hide"), q(this._element), this._element.classList.add(ro, ao), this._queueCallback(() => {
                this._element.classList.remove(ao), f.trigger(this._element, "shown.bs.toast"), this._maybeScheduleHide()
            }, this._element, this._config.animation))
        }
        hide() {
            !this.isShown() || f.trigger(this._element, "hide.bs.toast").defaultPrevented || (this._element.classList.add(ao), this._queueCallback(() => {
                this._element.classList.add("hide"), this._element.classList.remove(ao, ro), f.trigger(this._element, "hidden.bs.toast")
            }, this._element, this._config.animation))
        }
        dispose() {
            this._clearTimeout(), this.isShown() && this._element.classList.remove(ro), super.dispose()
        }
        isShown() {
            return this._element.classList.contains(ro)
        }
        _maybeScheduleHide() {
            !this._config.autohide || this._hasMouseInteraction || this._hasKeyboardInteraction || (this._timeout = setTimeout(() => {
                this.hide()
            }, this._config.delay))
        }
        _onInteraction(e, t) {
            switch (e.type) {
                case "mouseover":
                case "mouseout":
                    this._hasMouseInteraction = t;
                    break;
                case "focusin":
                case "focusout":
                    this._hasKeyboardInteraction = t
            }
            t ? this._clearTimeout() : (e = e.relatedTarget, this._element === e || this._element.contains(e) || this._maybeScheduleHide())
        }
        _setListeners() {
            f.on(this._element, "mouseover.bs.toast", e => this._onInteraction(e, !0)), f.on(this._element, "mouseout.bs.toast", e => this._onInteraction(e, !1)), f.on(this._element, "focusin.bs.toast", e => this._onInteraction(e, !0)), f.on(this._element, "focusout.bs.toast", e => this._onInteraction(e, !1))
        }
        _clearTimeout() {
            clearTimeout(this._timeout), this._timeout = null
        }
        static jQueryInterface(t) {
            return this.each(function() {
                var e = uo.getOrCreateInstance(this, t);
                if ("string" == typeof t) {
                    if (void 0 === e[t]) throw new TypeError(`No method named "${t}"`);
                    e[t](this)
                }
            })
        }
    }
    return pe(uo), e(uo), {
        Alert: he,
        Button: me,
        Carousel: Ee,
        Collapse: Me,
        Dropdown: m,
        Modal: xi,
        Offcanvas: D,
        Popover: Yi,
        ScrollSpy: Zi,
        Tab: so,
        Toast: uo,
        Tooltip: Fi
    }
}),
function(e, t) {
    "function" == typeof define && define.amd ? define(t) : "object" == typeof exports ? module.exports = t() : e.Blazy = t()
}(this, function() {
    function o(e) {
        var t = e._util;
        t.elements = function(e) {
            for (var t = [], i = (e = e.root.querySelectorAll(e.selector)).length; i--; t.unshift(e[i]));
            return t
        }(e.options), t.count = t.elements.length, t.destroyed && (t.destroyed = !1, e.options.container && g(e.options.container, function(e) {
            f(e, "scroll", t.validateT)
        }), f(window, "resize", t.saveViewportOffsetT), f(window, "resize", t.validateT), f(window, "scroll", t.validateT)), n(e)
    }

    function n(e) {
        for (var t = e._util, i = 0; i < t.count; i++) {
            var o = t.elements[i],
                n = o,
                s = e.options,
                r = n.getBoundingClientRect();
            s.container && w && (n = n.closest(s.containerClass)) ? s = !!a(n = n.getBoundingClientRect(), y) && a(r, {
                top: n.top - s.offset,
                right: n.right + s.offset,
                bottom: n.bottom + s.offset,
                left: n.left - s.offset
            }) : s = a(r, y), (s || p(o, e.options.successClass)) && (e.load(o), t.elements.splice(i, 1), t.count--, i--)
        }
        0 === t.count && e.destroy()
    }

    function a(e, t) {
        return e.right >= t.left && e.bottom >= t.top && e.left <= t.right && e.top <= t.bottom
    }

    function r(e, t, o) {
        var i, n, s, r, a, l, c;
        !p(e, o.successClass) && (t || o.loadInvisible || 0 < e.offsetWidth && 0 < e.offsetHeight) && ((t = e.getAttribute(v) || e.getAttribute(o.src)) ? (i = (t = t.split(o.separator))[b && 1 < t.length ? 1 : 0], n = e.getAttribute(o.srcset), s = "img" === e.nodeName.toLowerCase(), r = (t = e.parentNode) && "picture" === t.nodeName.toLowerCase(), s || void 0 === e.src ? (a = new Image, l = function() {
            o.error && o.error(e, "invalid"), h(e, o.errorClass), m(a, "error", l), m(a, "load", c)
        }, c = function() {
            s ? r || u(e, i, n) : e.style.backgroundImage = 'url("' + i + '")', d(e, o), m(a, "load", c), m(a, "error", l)
        }, r && (a = e, g(t.getElementsByTagName("source"), function(e) {
            var t = o.srcset,
                i = e.getAttribute(t);
            i && (e.setAttribute("srcset", i), e.removeAttribute(t))
        })), f(a, "error", l), f(a, "load", c), u(a, i, n)) : (e.src = i, d(e, o))) : "video" === e.nodeName.toLowerCase() ? (g(e.getElementsByTagName("source"), function(e) {
            var t = o.src,
                i = e.getAttribute(t);
            i && (e.setAttribute("src", i), e.removeAttribute(t))
        }), e.load(), d(e, o)) : (o.error && o.error(e, "missing"), h(e, o.errorClass)))
    }

    function d(t, e) {
        h(t, e.successClass), e.success && e.success(t), t.removeAttribute(e.src), t.removeAttribute(e.srcset), g(e.breakpoints, function(e) {
            t.removeAttribute(e.src)
        })
    }

    function u(e, t, i) {
        i && e.setAttribute("srcset", i), e.src = t
    }

    function p(e, t) {
        return -1 !== (" " + e.className + " ").indexOf(" " + t + " ")
    }

    function h(e, t) {
        p(e, t) || (e.className += " " + t)
    }

    function l(e) {
        y.bottom = (window.innerHeight || document.documentElement.clientHeight) + e, y.right = (window.innerWidth || document.documentElement.clientWidth) + e
    }

    function f(e, t, i) {
        e.attachEvent ? e.attachEvent && e.attachEvent("on" + t, i) : e.addEventListener(t, i, {
            capture: !1,
            passive: !0
        })
    }

    function m(e, t, i) {
        e.detachEvent ? e.detachEvent && e.detachEvent("on" + t, i) : e.removeEventListener(t, i, {
            capture: !1,
            passive: !0
        })
    }

    function g(e, t) {
        if (e && t)
            for (var i = e.length, o = 0; o < i && !1 !== t(e[o], o); o++);
    }

    function c(t, i, o) {
        var n = 0;
        return function() {
            var e = +new Date;
            e - n < i || (n = e, t.apply(o, arguments))
        }
    }
    var v, y, b, w;
    return function(e) {
        document.querySelectorAll || (s = document.createStyleSheet(), document.querySelectorAll = function(e, t, i, o, n) {
            for (n = document.all, t = [], i = (e = e.replace(/\[for\b/gi, "[htmlFor").split(",")).length; i--;) {
                for (s.addRule(e[i], "k:v"), o = n.length; o--;) n[o].currentStyle.k && t.push(n[o]);
                s.removeRule(0)
            }
            return t
        });
        var s, t = this,
            i = t._util = {
                elements: [],
                destroyed: !0
            };
        t.options = e || {}, t.options.error = t.options.error || !1, t.options.offset = t.options.offset || 100, t.options.root = t.options.root || document, t.options.success = t.options.success || !1, t.options.selector = t.options.selector || ".b-lazy", t.options.separator = t.options.separator || "|", t.options.containerClass = t.options.container, t.options.container = !!t.options.containerClass && document.querySelectorAll(t.options.containerClass), t.options.errorClass = t.options.errorClass || "b-error", t.options.breakpoints = t.options.breakpoints || !1, t.options.loadInvisible = t.options.loadInvisible || !1, t.options.successClass = t.options.successClass || "b-loaded", t.options.validateDelay = t.options.validateDelay || 25, t.options.saveViewportOffsetDelay = t.options.saveViewportOffsetDelay || 50, t.options.srcset = t.options.srcset || "data-srcset", t.options.src = v = t.options.src || "data-src", w = Element.prototype.closest, b = 1 < window.devicePixelRatio, (y = {}).top = 0 - t.options.offset, y.left = 0 - t.options.offset, t.revalidate = function() {
            o(t)
        }, t.load = function(e, t) {
            var i = this.options;
            void 0 === e.length ? r(e, t, i) : g(e, function(e) {
                r(e, t, i)
            })
        }, t.destroy = function() {
            var t = this._util;
            this.options.container && g(this.options.container, function(e) {
                m(e, "scroll", t.validateT)
            }), m(window, "scroll", t.validateT), m(window, "resize", t.validateT), m(window, "resize", t.saveViewportOffsetT), t.count = 0, t.elements.length = 0, t.destroyed = !0
        }, i.validateT = c(function() {
            n(t)
        }, t.options.validateDelay, t), i.saveViewportOffsetT = c(function() {
            l(t.options.offset)
        }, t.options.saveViewportOffsetDelay, t), l(t.options.offset), g(t.options.breakpoints, function(e) {
            if (e.width >= window.screen.width) return v = e.src, !1
        }), setTimeout(function() {
            o(t)
        })
    }
}),
function(t, i) {
    "function" == typeof define && define.amd ? define("jquery-bridget/jquery-bridget", ["jquery"], function(e) {
        return i(t, e)
    }) : "object" == typeof module && module.exports ? module.exports = i(t, require("jquery")) : t.jQueryBridget = i(t, t.jQuery)
}(window, function(e, t) {
    "use strict";

    function i(l, c, d) {
        (d = d || t || e.jQuery) && (c.prototype.option || (c.prototype.option = function(e) {
            d.isPlainObject(e) && (this.options = d.extend(!0, this.options, e))
        }), d.fn[l] = function(e) {
            var t, o, n, s, r, a;
            return "string" == typeof e ? (t = u.call(arguments, 1), n = t, r = "$()." + l + '("' + (o = e) + '")', (t = this).each(function(e, t) {
                var i, t = d.data(t, l);
                t ? (i = t[o]) && "_" != o.charAt(0) ? (i = i.apply(t, n), s = void 0 === s ? i : s) : p(r + " is not a valid method") : p(l + " not initialized. Cannot call methods, i.e. " + r)
            }), void 0 !== s ? s : t) : (a = e, this.each(function(e, t) {
                var i = d.data(t, l);
                i ? (i.option(a), i._init()) : (i = new c(t, a), d.data(t, l, i))
            }), this)
        }, o(d))
    }

    function o(e) {
        e && !e.bridget && (e.bridget = i)
    }
    var u = Array.prototype.slice,
        n = e.console,
        p = void 0 === n ? function() {} : function(e) {
            n.error(e)
        };
    return o(t || e.jQuery), i
}),
function(e, t) {
    "function" == typeof define && define.amd ? define("ev-emitter/ev-emitter", t) : "object" == typeof module && module.exports ? module.exports = t() : e.EvEmitter = t()
}("undefined" != typeof window ? window : this, function() {
    function e() {}
    var t = e.prototype;
    return t.on = function(e, t) {
        var i;
        if (e && t) return -1 == (i = (i = this._events = this._events || {})[e] = i[e] || []).indexOf(t) && i.push(t), this
    }, t.once = function(e, t) {
        var i;
        if (e && t) return this.on(e, t), ((i = this._onceEvents = this._onceEvents || {})[e] = i[e] || {})[t] = !0, this
    }, t.off = function(e, t) {
        e = this._events && this._events[e];
        if (e && e.length) return -1 != (t = e.indexOf(t)) && e.splice(t, 1), this
    }, t.emitEvent = function(e, t) {
        var i = this._events && this._events[e];
        if (i && i.length) {
            i = i.slice(0), t = t || [];
            for (var o = this._onceEvents && this._onceEvents[e], n = 0; n < i.length; n++) {
                var s = i[n];
                o && o[s] && (this.off(e, s), delete o[s]), s.apply(this, t)
            }
            return this
        }
    }, t.allOff = function() {
        delete this._events, delete this._onceEvents
    }, e
}),
function(e, t) {
    "function" == typeof define && define.amd ? define("get-size/get-size", t) : "object" == typeof module && module.exports ? module.exports = t() : e.getSize = t()
}(window, function() {
    "use strict";

    function g(e) {
        var t = parseFloat(e);
        return -1 == e.indexOf("%") && !isNaN(t) && t
    }

    function v(e) {
        e = getComputedStyle(e);
        return e || t("Style returned " + e + ". Are you running this code in a hidden iframe on Firefox? See https://bit.ly/getsizebug1"), e
    }

    function y(e) {
        if (x || (x = !0, (d = document.createElement("div")).style.width = "200px", d.style.padding = "1px 2px 3px 4px", d.style.borderStyle = "solid", d.style.borderWidth = "1px 2px 3px 4px", d.style.boxSizing = "border-box", (c = document.body || document.documentElement).appendChild(d), s = v(d), b = 200 == Math.round(g(s.width)), y.isBoxSizeOuter = b, c.removeChild(d)), (e = "string" == typeof e ? document.querySelector(e) : e) && "object" == typeof e && e.nodeType) {
            var t = v(e);
            if ("none" == t.display) {
                for (var i = {
                        width: 0,
                        height: 0,
                        innerWidth: 0,
                        innerHeight: 0,
                        outerWidth: 0,
                        outerHeight: 0
                    }, o = 0; o < _; o++) i[w[o]] = 0;
                return i
            }
            var n = {};
            n.width = e.offsetWidth, n.height = e.offsetHeight;
            for (var s = n.isBorderBox = "border-box" == t.boxSizing, r = 0; r < _; r++) {
                var a = w[r],
                    l = t[a],
                    l = parseFloat(l);
                n[a] = isNaN(l) ? 0 : l
            }
            var c = n.paddingLeft + n.paddingRight,
                d = n.paddingTop + n.paddingBottom,
                e = n.marginLeft + n.marginRight,
                u = n.marginTop + n.marginBottom,
                p = n.borderLeftWidth + n.borderRightWidth,
                h = n.borderTopWidth + n.borderBottomWidth,
                f = s && b,
                m = g(t.width),
                m = (!1 !== m && (n.width = m + (f ? 0 : c + p)), g(t.height));
            return !1 !== m && (n.height = m + (f ? 0 : d + h)), n.innerWidth = n.width - (c + p), n.innerHeight = n.height - (d + h), n.outerWidth = n.width + e, n.outerHeight = n.height + u, n
        }
        var d, c, s
    }
    var b, t = "undefined" == typeof console ? function() {} : function(e) {},
        w = ["paddingLeft", "paddingRight", "paddingTop", "paddingBottom", "marginLeft", "marginRight", "marginTop", "marginBottom", "borderLeftWidth", "borderRightWidth", "borderTopWidth", "borderBottomWidth"],
        _ = w.length,
        x = !1;
    return y
}),
function(e, t) {
    "use strict";
    "function" == typeof define && define.amd ? define("desandro-matches-selector/matches-selector", t) : "object" == typeof module && module.exports ? module.exports = t() : e.matchesSelector = t()
}(window, function() {
    "use strict";
    var i = function() {
        var e = window.Element.prototype;
        if (e.matches) return "matches";
        if (e.matchesSelector) return "matchesSelector";
        for (var t = ["webkit", "moz", "ms", "o"], i = 0; i < t.length; i++) {
            var o = t[i] + "MatchesSelector";
            if (e[o]) return o
        }
    }();
    return function(e, t) {
        return e[i](t)
    }
}),
function(t, i) {
    "function" == typeof define && define.amd ? define("fizzy-ui-utils/utils", ["desandro-matches-selector/matches-selector"], function(e) {
        return i(t, e)
    }) : "object" == typeof module && module.exports ? module.exports = i(t, require("desandro-matches-selector")) : t.fizzyUIUtils = i(t, t.matchesSelector)
}(window, function(i, s) {
    var l = {
            extend: function(e, t) {
                for (var i in t) e[i] = t[i];
                return e
            },
            modulo: function(e, t) {
                return (e % t + t) % t
            }
        },
        t = Array.prototype.slice,
        c = (l.makeArray = function(e) {
            return Array.isArray(e) ? e : null == e ? [] : "object" == typeof e && "number" == typeof e.length ? t.call(e) : [e]
        }, l.removeFrom = function(e, t) {
            t = e.indexOf(t); - 1 != t && e.splice(t, 1)
        }, l.getParent = function(e, t) {
            for (; e.parentNode && e != document.body;)
                if (e = e.parentNode, s(e, t)) return e
        }, l.getQueryElement = function(e) {
            return "string" == typeof e ? document.querySelector(e) : e
        }, l.handleEvent = function(e) {
            var t = "on" + e.type;
            this[t] && this[t](e)
        }, l.filterFindElements = function(e, o) {
            e = l.makeArray(e);
            var n = [];
            return e.forEach(function(e) {
                if (e instanceof HTMLElement)
                    if (o) {
                        s(e, o) && n.push(e);
                        for (var t = e.querySelectorAll(o), i = 0; i < t.length; i++) n.push(t[i])
                    } else n.push(e)
            }), n
        }, l.debounceMethod = function(e, t, o) {
            o = o || 100;
            var n = e.prototype[t],
                s = t + "Timeout";
            e.prototype[t] = function() {
                var e = this[s],
                    t = (clearTimeout(e), arguments),
                    i = this;
                this[s] = setTimeout(function() {
                    n.apply(i, t), delete i[s]
                }, o)
            }
        }, l.docReady = function(e) {
            var t = document.readyState;
            "complete" == t || "interactive" == t ? setTimeout(e) : document.addEventListener("DOMContentLoaded", e)
        }, l.toDashed = function(e) {
            return e.replace(/(.)([A-Z])/g, function(e, t, i) {
                return t + "-" + i
            }).toLowerCase()
        }, i.console);
    return l.htmlInit = function(r, a) {
        l.docReady(function() {
            var e = l.toDashed(a),
                o = "data-" + e,
                t = document.querySelectorAll("[" + o + "]"),
                e = document.querySelectorAll(".js-" + e),
                t = l.makeArray(t).concat(l.makeArray(e)),
                n = o + "-options",
                s = i.jQuery;
            t.forEach(function(t) {
                var e, i = t.getAttribute(o) || t.getAttribute(n);
                try {
                    e = i && JSON.parse(i)
                } catch (e) {
                    return void(c && c.error("Error parsing " + o + " on " + t.className + ": " + e))
                }
                i = new r(t, e);
                s && s.data(t, a, i)
            })
        })
    }, l
}),
function(e, t) {
    "function" == typeof define && define.amd ? define("outlayer/item", ["ev-emitter/ev-emitter", "get-size/get-size"], t) : "object" == typeof module && module.exports ? module.exports = t(require("ev-emitter"), require("get-size")) : (e.Outlayer = {}, e.Outlayer.Item = t(e.EvEmitter, e.getSize))
}(window, function(e, t) {
    "use strict";

    function i(e, t) {
        e && (this.element = e, this.layout = t, this.position = {
            x: 0,
            y: 0
        }, this._create())
    }
    var o = document.documentElement.style,
        n = "string" == typeof o.transition ? "transition" : "WebkitTransition",
        o = "string" == typeof o.transform ? "transform" : "WebkitTransform",
        s = {
            WebkitTransition: "webkitTransitionEnd",
            transition: "transitionend"
        } [n],
        r = {
            transform: o,
            transition: n,
            transitionDuration: n + "Duration",
            transitionProperty: n + "Property",
            transitionDelay: n + "Delay"
        },
        e = i.prototype = Object.create(e.prototype),
        a = (e.constructor = i, e._create = function() {
            this._transn = {
                ingProperties: {},
                clean: {},
                onEnd: {}
            }, this.css({
                position: "absolute"
            })
        }, e.handleEvent = function(e) {
            var t = "on" + e.type;
            this[t] && this[t](e)
        }, e.getSize = function() {
            this.size = t(this.element)
        }, e.css = function(e) {
            var t, i = this.element.style;
            for (t in e) i[r[t] || t] = e[t]
        }, e.getPosition = function() {
            var e = getComputedStyle(this.element),
                t = this.layout._getOption("originLeft"),
                i = this.layout._getOption("originTop"),
                o = e[t ? "left" : "right"],
                e = e[i ? "top" : "bottom"],
                n = parseFloat(o),
                s = parseFloat(e),
                r = this.layout.size; - 1 != o.indexOf("%") && (n = n / 100 * r.width), -1 != e.indexOf("%") && (s = s / 100 * r.height), n = isNaN(n) ? 0 : n, s = isNaN(s) ? 0 : s, n -= t ? r.paddingLeft : r.paddingRight, s -= i ? r.paddingTop : r.paddingBottom, this.position.x = n, this.position.y = s
        }, e.layoutPosition = function() {
            var e = this.layout.size,
                t = {},
                i = this.layout._getOption("originLeft"),
                o = this.layout._getOption("originTop"),
                n = i ? "right" : "left",
                s = this.position.x + e[i ? "paddingLeft" : "paddingRight"],
                i = (t[i ? "left" : "right"] = this.getXValue(s), t[n] = "", o ? "paddingTop" : "paddingBottom"),
                s = o ? "bottom" : "top",
                n = this.position.y + e[i];
            t[o ? "top" : "bottom"] = this.getYValue(n), t[s] = "", this.css(t), this.emitEvent("layout", [this])
        }, e.getXValue = function(e) {
            var t = this.layout._getOption("horizontal");
            return this.layout.options.percentPosition && !t ? e / this.layout.size.width * 100 + "%" : e + "px"
        }, e.getYValue = function(e) {
            var t = this.layout._getOption("horizontal");
            return this.layout.options.percentPosition && t ? e / this.layout.size.height * 100 + "%" : e + "px"
        }, e._transitionTo = function(e, t) {
            this.getPosition();
            var i = this.position.x,
                o = this.position.y,
                n = e == this.position.x && t == this.position.y;
            this.setPosition(e, t), n && !this.isTransitioning ? this.layoutPosition() : ((n = {}).transform = this.getTranslate(e - i, t - o), this.transition({
                to: n,
                onTransitionEnd: {
                    transform: this.layoutPosition
                },
                isCleaning: !0
            }))
        }, e.getTranslate = function(e, t) {
            return "translate3d(" + (e = this.layout._getOption("originLeft") ? e : -e) + "px, " + (t = this.layout._getOption("originTop") ? t : -t) + "px, 0)"
        }, e.goTo = function(e, t) {
            this.setPosition(e, t), this.layoutPosition()
        }, e.moveTo = e._transitionTo, e.setPosition = function(e, t) {
            this.position.x = parseFloat(e), this.position.y = parseFloat(t)
        }, e._nonTransition = function(e) {
            for (var t in this.css(e.to), e.isCleaning && this._removeStyles(e.to), e.onTransitionEnd) e.onTransitionEnd[t].call(this)
        }, e.transition = function(e) {
            if (parseFloat(this.layout.options.transitionDuration)) {
                var t, i = this._transn;
                for (t in e.onTransitionEnd) i.onEnd[t] = e.onTransitionEnd[t];
                for (t in e.to) i.ingProperties[t] = !0, e.isCleaning && (i.clean[t] = !0);
                e.from && (this.css(e.from), this.element.offsetHeight, 0), this.enableTransition(e.to), this.css(e.to), this.isTransitioning = !0
            } else this._nonTransition(e)
        }, "opacity," + o.replace(/([A-Z])/g, function(e) {
            return "-" + e.toLowerCase()
        })),
        l = (e.enableTransition = function() {
            var e;
            this.isTransitioning || (e = this.layout.options.transitionDuration, this.css({
                transitionProperty: a,
                transitionDuration: e = "number" == typeof e ? e + "ms" : e,
                transitionDelay: this.staggerDelay || 0
            }), this.element.addEventListener(s, this, !1))
        }, e.onwebkitTransitionEnd = function(e) {
            this.ontransitionend(e)
        }, e.onotransitionend = function(e) {
            this.ontransitionend(e)
        }, {
            "-webkit-transform": "transform"
        }),
        c = (e.ontransitionend = function(e) {
            var t, i;
            e.target === this.element && (t = this._transn, i = l[e.propertyName] || e.propertyName, delete t.ingProperties[i], function(e) {
                for (var t in e) return;
                return 1
            }(t.ingProperties) && this.disableTransition(), i in t.clean && (this.element.style[e.propertyName] = "", delete t.clean[i]), i in t.onEnd && (t.onEnd[i].call(this), delete t.onEnd[i]), this.emitEvent("transitionEnd", [this]))
        }, e.disableTransition = function() {
            this.removeTransitionStyles(), this.element.removeEventListener(s, this, !1), this.isTransitioning = !1
        }, e._removeStyles = function(e) {
            var t, i = {};
            for (t in e) i[t] = "";
            this.css(i)
        }, {
            transitionProperty: "",
            transitionDuration: "",
            transitionDelay: ""
        });
    return e.removeTransitionStyles = function() {
        this.css(c)
    }, e.stagger = function(e) {
        e = isNaN(e) ? 0 : e, this.staggerDelay = e + "ms"
    }, e.removeElem = function() {
        this.element.parentNode.removeChild(this.element), this.css({
            display: ""
        }), this.emitEvent("remove", [this])
    }, e.remove = function() {
        return n && parseFloat(this.layout.options.transitionDuration) ? (this.once("transitionEnd", function() {
            this.removeElem()
        }), void this.hide()) : void this.removeElem()
    }, e.reveal = function() {
        delete this.isHidden, this.css({
            display: ""
        });
        var e = this.layout.options,
            t = {};
        t[this.getHideRevealTransitionEndProperty("visibleStyle")] = this.onRevealTransitionEnd, this.transition({
            from: e.hiddenStyle,
            to: e.visibleStyle,
            isCleaning: !0,
            onTransitionEnd: t
        })
    }, e.onRevealTransitionEnd = function() {
        this.isHidden || this.emitEvent("reveal")
    }, e.getHideRevealTransitionEndProperty = function(e) {
        var t, e = this.layout.options[e];
        if (e.opacity) return "opacity";
        for (t in e) return t
    }, e.hide = function() {
        this.isHidden = !0, this.css({
            display: ""
        });
        var e = this.layout.options,
            t = {};
        t[this.getHideRevealTransitionEndProperty("hiddenStyle")] = this.onHideTransitionEnd, this.transition({
            from: e.visibleStyle,
            to: e.hiddenStyle,
            isCleaning: !0,
            onTransitionEnd: t
        })
    }, e.onHideTransitionEnd = function() {
        this.isHidden && (this.css({
            display: "none"
        }), this.emitEvent("hide"))
    }, e.destroy = function() {
        this.css({
            position: "",
            left: "",
            right: "",
            top: "",
            bottom: "",
            transition: "",
            transform: ""
        })
    }, i
}),
function(n, s) {
    "use strict";
    "function" == typeof define && define.amd ? define("outlayer/outlayer", ["ev-emitter/ev-emitter", "get-size/get-size", "fizzy-ui-utils/utils", "./item"], function(e, t, i, o) {
        return s(n, e, t, i, o)
    }) : "object" == typeof module && module.exports ? module.exports = s(n, require("ev-emitter"), require("get-size"), require("fizzy-ui-utils"), require("./item")) : n.Outlayer = s(n, n.EvEmitter, n.getSize, n.fizzyUIUtils, n.Outlayer.Item)
}(window, function(e, t, n, o, s) {
    "use strict";

    function r(e, t) {
        var i = o.getQueryElement(e);
        i ? (this.element = i, c && (this.$element = c(this.element)), this.options = o.extend({}, this.constructor.defaults), this.option(t), t = ++d, this.element.outlayerGUID = t, (u[t] = this)._create(), this._getOption("initLayout") && this.layout()) : l && l.error("Bad element for " + this.constructor.namespace + ": " + (i || e))
    }

    function a(e) {
        function t() {
            e.apply(this, arguments)
        }
        return (t.prototype = Object.create(e.prototype)).constructor = t
    }

    function i() {}
    var l = e.console,
        c = e.jQuery,
        d = 0,
        u = {},
        p = (r.namespace = "outlayer", r.Item = s, r.defaults = {
            containerStyle: {
                position: "relative"
            },
            initLayout: !0,
            originLeft: !0,
            originTop: !0,
            resize: !0,
            resizeContainer: !0,
            transitionDuration: "0.4s",
            hiddenStyle: {
                opacity: 0,
                transform: "scale(0.001)"
            },
            visibleStyle: {
                opacity: 1,
                transform: "scale(1)"
            }
        }, r.prototype),
        h = (o.extend(p, t.prototype), p.option = function(e) {
            o.extend(this.options, e)
        }, p._getOption = function(e) {
            var t = this.constructor.compatOptions[e];
            return t && void 0 !== this.options[t] ? this.options[t] : this.options[e]
        }, r.compatOptions = {
            initLayout: "isInitLayout",
            horizontal: "isHorizontal",
            layoutInstant: "isLayoutInstant",
            originLeft: "isOriginLeft",
            originTop: "isOriginTop",
            resize: "isResizeBound",
            resizeContainer: "isResizingContainer"
        }, p._create = function() {
            this.reloadItems(), this.stamps = [], this.stamp(this.options.stamp), o.extend(this.element.style, this.options.containerStyle), this._getOption("resize") && this.bindResize()
        }, p.reloadItems = function() {
            this.items = this._itemize(this.element.children)
        }, p._itemize = function(e) {
            for (var t = this._filterFindItemElements(e), i = this.constructor.Item, o = [], n = 0; n < t.length; n++) {
                var s = new i(t[n], this);
                o.push(s)
            }
            return o
        }, p._filterFindItemElements = function(e) {
            return o.filterFindElements(e, this.options.itemSelector)
        }, p.getItemElements = function() {
            return this.items.map(function(e) {
                return e.element
            })
        }, p.layout = function() {
            this._resetLayout(), this._manageStamps();
            var e = this._getOption("layoutInstant"),
                e = void 0 !== e ? e : !this._isLayoutInited;
            this.layoutItems(this.items, e), this._isLayoutInited = !0
        }, p._init = p.layout, p._resetLayout = function() {
            this.getSize()
        }, p.getSize = function() {
            this.size = n(this.element)
        }, p._getMeasurement = function(e, t) {
            var i, o = this.options[e];
            o ? ("string" == typeof o ? i = this.element.querySelector(o) : o instanceof HTMLElement && (i = o), this[e] = i ? n(i)[t] : o) : this[e] = 0
        }, p.layoutItems = function(e, t) {
            e = this._getItemsForLayout(e), this._layoutItems(e, t), this._postLayout()
        }, p._getItemsForLayout = function(e) {
            return e.filter(function(e) {
                return !e.isIgnored
            })
        }, p._layoutItems = function(e, i) {
            var o;
            this._emitCompleteOnItems("layout", e), e && e.length && (o = [], e.forEach(function(e) {
                var t = this._getItemLayoutPosition(e);
                t.item = e, t.isInstant = i || e.isLayoutInstant, o.push(t)
            }, this), this._processLayoutQueue(o))
        }, p._getItemLayoutPosition = function() {
            return {
                x: 0,
                y: 0
            }
        }, p._processLayoutQueue = function(e) {
            this.updateStagger(), e.forEach(function(e, t) {
                this._positionItem(e.item, e.x, e.y, e.isInstant, t)
            }, this)
        }, p.updateStagger = function() {
            var e, t = this.options.stagger;
            return null == t ? void(this.stagger = 0) : (this.stagger = "number" == typeof(t = t) ? t : (e = (t = t.match(/(^\d*\.?\d*)(\w*)/)) && t[1], t = t && t[2], e.length ? (e = parseFloat(e)) * (h[t] || 1) : 0), this.stagger)
        }, p._positionItem = function(e, t, i, o, n) {
            o ? e.goTo(t, i) : (e.stagger(n * this.stagger), e.moveTo(t, i))
        }, p._postLayout = function() {
            this.resizeContainer()
        }, p.resizeContainer = function() {
            var e;
            this._getOption("resizeContainer") && (e = this._getContainerSize()) && (this._setContainerMeasure(e.width, !0), this._setContainerMeasure(e.height, !1))
        }, p._getContainerSize = i, p._setContainerMeasure = function(e, t) {
            var i;
            void 0 !== e && ((i = this.size).isBorderBox && (e += t ? i.paddingLeft + i.paddingRight + i.borderLeftWidth + i.borderRightWidth : i.paddingBottom + i.paddingTop + i.borderTopWidth + i.borderBottomWidth), e = Math.max(e, 0), this.element.style[t ? "width" : "height"] = e + "px")
        }, p._emitCompleteOnItems = function(t, e) {
            function i() {
                s.dispatchEvent(t + "Complete", null, [e])
            }

            function o() {
                ++n == r && i()
            }
            var n, s = this,
                r = e.length;
            e && r ? (n = 0, e.forEach(function(e) {
                e.once(t, o)
            })) : i()
        }, p.dispatchEvent = function(e, t, i) {
            var o = t ? [t].concat(i) : i;
            this.emitEvent(e, o), c && (this.$element = this.$element || c(this.element), t ? ((o = c.Event(t)).type = e, this.$element.trigger(o, i)) : this.$element.trigger(e, i))
        }, p.ignore = function(e) {
            e = this.getItem(e);
            e && (e.isIgnored = !0)
        }, p.unignore = function(e) {
            e = this.getItem(e);
            e && delete e.isIgnored
        }, p.stamp = function(e) {
            (e = this._find(e)) && (this.stamps = this.stamps.concat(e), e.forEach(this.ignore, this))
        }, p.unstamp = function(e) {
            (e = this._find(e)) && e.forEach(function(e) {
                o.removeFrom(this.stamps, e), this.unignore(e)
            }, this)
        }, p._find = function(e) {
            return e ? ("string" == typeof e && (e = this.element.querySelectorAll(e)), o.makeArray(e)) : void 0
        }, p._manageStamps = function() {
            this.stamps && this.stamps.length && (this._getBoundingRect(), this.stamps.forEach(this._manageStamp, this))
        }, p._getBoundingRect = function() {
            var e = this.element.getBoundingClientRect(),
                t = this.size;
            this._boundingRect = {
                left: e.left + t.paddingLeft + t.borderLeftWidth,
                top: e.top + t.paddingTop + t.borderTopWidth,
                right: e.right - (t.paddingRight + t.borderRightWidth),
                bottom: e.bottom - (t.paddingBottom + t.borderBottomWidth)
            }
        }, p._manageStamp = i, p._getElementOffset = function(e) {
            var t = e.getBoundingClientRect(),
                i = this._boundingRect,
                e = n(e);
            return {
                left: t.left - i.left - e.marginLeft,
                top: t.top - i.top - e.marginTop,
                right: i.right - t.right - e.marginRight,
                bottom: i.bottom - t.bottom - e.marginBottom
            }
        }, p.handleEvent = o.handleEvent, p.bindResize = function() {
            e.addEventListener("resize", this), this.isResizeBound = !0
        }, p.unbindResize = function() {
            e.removeEventListener("resize", this), this.isResizeBound = !1
        }, p.onresize = function() {
            this.resize()
        }, o.debounceMethod(r, "onresize", 100), p.resize = function() {
            this.isResizeBound && this.needsResizeLayout() && this.layout()
        }, p.needsResizeLayout = function() {
            var e = n(this.element);
            return this.size && e && e.innerWidth !== this.size.innerWidth
        }, p.addItems = function(e) {
            e = this._itemize(e);
            return e.length && (this.items = this.items.concat(e)), e
        }, p.appended = function(e) {
            e = this.addItems(e);
            e.length && (this.layoutItems(e, !0), this.reveal(e))
        }, p.prepended = function(e) {
            var t, e = this._itemize(e);
            e.length && (t = this.items.slice(0), this.items = e.concat(t), this._resetLayout(), this._manageStamps(), this.layoutItems(e, !0), this.reveal(e), this.layoutItems(t))
        }, p.reveal = function(e) {
            var i;
            this._emitCompleteOnItems("reveal", e), e && e.length && (i = this.updateStagger(), e.forEach(function(e, t) {
                e.stagger(t * i), e.reveal()
            }))
        }, p.hide = function(e) {
            var i;
            this._emitCompleteOnItems("hide", e), e && e.length && (i = this.updateStagger(), e.forEach(function(e, t) {
                e.stagger(t * i), e.hide()
            }))
        }, p.revealItemElements = function(e) {
            e = this.getItems(e);
            this.reveal(e)
        }, p.hideItemElements = function(e) {
            e = this.getItems(e);
            this.hide(e)
        }, p.getItem = function(e) {
            for (var t = 0; t < this.items.length; t++) {
                var i = this.items[t];
                if (i.element == e) return i
            }
        }, p.getItems = function(e) {
            e = o.makeArray(e);
            var t = [];
            return e.forEach(function(e) {
                e = this.getItem(e);
                e && t.push(e)
            }, this), t
        }, p.remove = function(e) {
            e = this.getItems(e);
            this._emitCompleteOnItems("remove", e), e && e.length && e.forEach(function(e) {
                e.remove(), o.removeFrom(this.items, e)
            }, this)
        }, p.destroy = function() {
            var e = this.element.style,
                e = (e.height = "", e.position = "", e.width = "", this.items.forEach(function(e) {
                    e.destroy()
                }), this.unbindResize(), this.element.outlayerGUID);
            delete u[e], delete this.element.outlayerGUID, c && c.removeData(this.element, this.constructor.namespace)
        }, r.data = function(e) {
            e = (e = o.getQueryElement(e)) && e.outlayerGUID;
            return e && u[e]
        }, r.create = function(e, t) {
            var i = a(r);
            return i.defaults = o.extend({}, r.defaults), o.extend(i.defaults, t), i.compatOptions = o.extend({}, r.compatOptions), i.namespace = e, i.data = r.data, i.Item = a(s), o.htmlInit(i, e), c && c.bridget && c.bridget(e, i), i
        }, {
            ms: 1,
            s: 1e3
        });
    return r.Item = s, r
}),
function(e, t) {
    "function" == typeof define && define.amd ? define(["outlayer/outlayer", "get-size/get-size"], t) : "object" == typeof module && module.exports ? module.exports = t(require("outlayer"), require("get-size")) : e.Masonry = t(e.Outlayer, e.getSize)
}(window, function(e, a) {
    var e = e.create("masonry"),
        t = (e.compatOptions.fitWidth = "isFitWidth", e.prototype);
    return t._resetLayout = function() {
        this.getSize(), this._getMeasurement("columnWidth", "outerWidth"), this._getMeasurement("gutter", "outerWidth"), this.measureColumns(), this.colYs = [];
        for (var e = 0; e < this.cols; e++) this.colYs.push(0);
        this.maxY = 0, this.horizontalColIndex = 0
    }, t.measureColumns = function() {
        this.getContainerWidth(), this.columnWidth || (e = (e = this.items[0]) && e.element, this.columnWidth = e && a(e).outerWidth || this.containerWidth);
        var e = this.columnWidth += this.gutter,
            t = this.containerWidth + this.gutter,
            i = t / e,
            t = e - t % e,
            i = Math[t && t < 1 ? "round" : "floor"](i);
        this.cols = Math.max(i, 1)
    }, t.getContainerWidth = function() {
        var e = this._getOption("fitWidth") ? this.element.parentNode : this.element,
            e = a(e);
        this.containerWidth = e && e.innerWidth
    }, t._getItemLayoutPosition = function(e) {
        e.getSize();
        for (var t = e.size.outerWidth % this.columnWidth, t = Math[t && t < 1 ? "round" : "ceil"](e.size.outerWidth / this.columnWidth), t = Math.min(t, this.cols), i = this[this.options.horizontalOrder ? "_getHorizontalColPosition" : "_getTopColPosition"](t, e), o = {
                x: this.columnWidth * i.col,
                y: i.y
            }, n = i.y + e.size.outerHeight, s = t + i.col, r = i.col; r < s; r++) this.colYs[r] = n;
        return o
    }, t._getTopColPosition = function(e) {
        var e = this._getTopColGroup(e),
            t = Math.min.apply(Math, e);
        return {
            col: e.indexOf(t),
            y: t
        }
    }, t._getTopColGroup = function(e) {
        if (e < 2) return this.colYs;
        for (var t = [], i = this.cols + 1 - e, o = 0; o < i; o++) t[o] = this._getColGroupY(o, e);
        return t
    }, t._getColGroupY = function(e, t) {
        return t < 2 ? this.colYs[e] : (e = this.colYs.slice(e, e + t), Math.max.apply(Math, e))
    }, t._getHorizontalColPosition = function(e, t) {
        var i = this.horizontalColIndex % this.cols,
            i = 1 < e && i + e > this.cols ? 0 : i,
            t = t.size.outerWidth && t.size.outerHeight;
        return this.horizontalColIndex = t ? i + e : this.horizontalColIndex, {
            col: i,
            y: this._getColGroupY(i, e)
        }
    }, t._manageStamp = function(e) {
        var t = a(e),
            e = this._getElementOffset(e),
            i = this._getOption("originLeft") ? e.left : e.right,
            o = i + t.outerWidth,
            i = Math.floor(i / this.columnWidth),
            i = Math.max(0, i),
            n = Math.floor(o / this.columnWidth);
        n -= o % this.columnWidth ? 0 : 1;
        for (var n = Math.min(this.cols - 1, n), s = (this._getOption("originTop") ? e.top : e.bottom) + t.outerHeight, r = i; r <= n; r++) this.colYs[r] = Math.max(s, this.colYs[r])
    }, t._getContainerSize = function() {
        this.maxY = Math.max.apply(Math, this.colYs);
        var e = {
            height: this.maxY
        };
        return this._getOption("fitWidth") && (e.width = this._getContainerFitWidth()), e
    }, t._getContainerFitWidth = function() {
        for (var e = 0, t = this.cols; --t && 0 === this.colYs[t];) e++;
        return (this.cols - e) * this.columnWidth - this.gutter
    }, t.needsResizeLayout = function() {
        var e = this.containerWidth;
        return this.getContainerWidth(), e != this.containerWidth
    }, e
}),
function(i, o, $, u) {
    "use strict";

    function p(e) {
        return e && e.hasOwnProperty && e instanceof $
    }

    function L(e) {
        return h(e) && 0 < e.indexOf("%")
    }

    function I(e, t) {
        var i = parseInt(e, 10) || 0;
        return t && L(e) && (i = M.getViewport()[t] / 100 * i), Math.ceil(i)
    }

    function P(e, t) {
        return I(e, t) + "px"
    }
    var n = $("html"),
        s = $(i),
        c = $(o),
        M = $.fancybox = function() {
            M.open.apply(this, arguments)
        },
        r = navigator.userAgent.match(/msie/i),
        a = null,
        d = o.createTouch !== u,
        h = function(e) {
            return e && "string" === $.type(e)
        };
    $.extend(M, {
        version: "2.1.5",
        defaults: {
            padding: 15,
            margin: 20,
            width: 800,
            height: 600,
            minWidth: 100,
            minHeight: 100,
            maxWidth: 9999,
            maxHeight: 9999,
            pixelRatio: 1,
            autoSize: !0,
            autoHeight: !1,
            autoWidth: !1,
            autoResize: !0,
            autoCenter: !d,
            fitToView: !0,
            aspectRatio: !1,
            topRatio: .5,
            leftRatio: .5,
            scrolling: "auto",
            wrapCSS: "",
            arrows: !0,
            closeBtn: !0,
            closeClick: !1,
            nextClick: !1,
            mouseWheel: !0,
            autoPlay: !1,
            playSpeed: 3e3,
            preload: 3,
            modal: !1,
            loop: !0,
            ajax: {
                dataType: "html",
                headers: {
                    "X-fancyBox": !0
                }
            },
            iframe: {
                scrolling: "auto",
                preload: !0
            },
            swf: {
                wmode: "transparent",
                allowfullscreen: "true",
                allowscriptaccess: "always"
            },
            keys: {
                next: {
                    13: "left",
                    34: "up",
                    39: "left",
                    40: "up"
                },
                prev: {
                    8: "right",
                    33: "down",
                    37: "right",
                    38: "down"
                },
                close: [27],
                play: [32],
                toggle: [70]
            },
            direction: {
                next: "left",
                prev: "right"
            },
            scrollOutside: !0,
            index: 0,
            type: null,
            href: null,
            content: null,
            title: null,
            tpl: {
                wrap: '<div class="fancybox-wrap" tabIndex="-1"><div class="fancybox-skin"><div class="fancybox-outer"><div class="fancybox-inner"></div></div></div></div>',
                image: '<img class="fancybox-image" src="{href}" alt="" />',
                iframe: '<iframe id="fancybox-frame{rnd}" name="fancybox-frame{rnd}" class="fancybox-iframe" frameborder="0" vspace="0" hspace="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen' + (r ? ' allowtransparency="true"' : "") + "></iframe>",
                error: '<p class="fancybox-error">The requested content cannot be loaded.<br/>Please try again later.</p>',
                closeBtn: '<a title="Close" class="fancybox-item fancybox-close" href="javascript:;"></a>',
                next: '<a title="Next" class="fancybox-nav fancybox-next" href="javascript:;"><span></span></a>',
                prev: '<a title="Previous" class="fancybox-nav fancybox-prev" href="javascript:;"><span></span></a>'
            },
            openEffect: "fade",
            openSpeed: 250,
            openEasing: "swing",
            openOpacity: !0,
            openMethod: "zoomIn",
            closeEffect: "fade",
            closeSpeed: 250,
            closeEasing: "swing",
            closeOpacity: !0,
            closeMethod: "zoomOut",
            nextEffect: "elastic",
            nextSpeed: 250,
            nextEasing: "swing",
            nextMethod: "changeIn",
            prevEffect: "elastic",
            prevSpeed: 250,
            prevEasing: "swing",
            prevMethod: "changeOut",
            helpers: {
                overlay: !0,
                title: !0
            },
            onCancel: $.noop,
            beforeLoad: $.noop,
            afterLoad: $.noop,
            beforeShow: $.noop,
            afterShow: $.noop,
            beforeChange: $.noop,
            beforeClose: $.noop,
            afterClose: $.noop
        },
        group: {},
        opts: {},
        previous: null,
        coming: null,
        current: null,
        isActive: !1,
        isOpen: !1,
        isOpened: !1,
        wrap: null,
        skin: null,
        outer: null,
        inner: null,
        player: {
            timer: null,
            isActive: !1
        },
        ajaxLoad: null,
        imgPreload: null,
        transitions: {},
        helpers: {},
        open: function(c, d) {
            if (c && ($.isPlainObject(d) || (d = {}), !1 !== M.close(!0))) return $.isArray(c) || (c = p(c) ? $(c).get() : [c]), $.each(c, function(e, t) {
                var i, o, n, s, r, a, l = {};
                "object" === $.type(t) && (t.nodeType && (t = $(t)), p(t) ? (l = {
                    href: t.data("fancybox-href") || t.attr("href"),
                    title: t.data("fancybox-title") || t.attr("title"),
                    isDom: !0,
                    element: t
                }, $.metadata && $.extend(!0, l, t.metadata())) : l = t), i = d.href || l.href || (h(t) ? t : null), o = d.title !== u ? d.title : l.title || "", !(s = (n = d.content || l.content) ? "html" : d.type || l.type) && l.isDom && (s = (s = t.data("fancybox-type")) || ((r = t.prop("class").match(/fancybox\.(\w+)/)) ? r[1] : null)), h(i) && (s || (M.isImage(i) ? s = "image" : M.isSWF(i) ? s = "swf" : "#" === i.charAt(0) ? s = "inline" : h(t) && (s = "html", n = t)), "ajax" === s) && (i = (r = i.split(/\s+/, 2)).shift(), a = r.shift()), n || ("inline" === s ? i ? n = $(h(i) ? i.replace(/.*(?=#[^\s]+$)/, "") : i) : l.isDom && (n = t) : "html" === s ? n = i : s || i || !l.isDom || (s = "inline", n = t)), $.extend(l, {
                    href: i,
                    type: s,
                    content: n,
                    title: o,
                    selector: a
                }), c[e] = l
            }), M.opts = $.extend(!0, {}, M.defaults, d), d.keys !== u && (M.opts.keys = !!d.keys && $.extend({}, M.defaults.keys, d.keys)), M.group = c, M._start(M.opts.index)
        },
        cancel: function() {
            var e = M.coming;
            e && !1 !== M.trigger("onCancel") && (M.hideLoading(), M.ajaxLoad && M.ajaxLoad.abort(), M.ajaxLoad = null, M.imgPreload && (M.imgPreload.onload = M.imgPreload.onerror = null), e.wrap && e.wrap.stop(!0, !0).trigger("onReset").remove(), M.coming = null, M.current || M._afterZoomOut(e))
        },
        close: function(e) {
            M.cancel(), !1 !== M.trigger("beforeClose") && (M.unbindEvents(), M.isActive) && (M.isOpen && !0 !== e ? (M.isOpen = M.isOpened = !1, M.isClosing = !0, $(".fancybox-item, .fancybox-nav").remove(), M.wrap.stop(!0, !0).removeClass("fancybox-opened"), M.transitions[M.current.closeMethod]()) : ($(".fancybox-wrap").stop(!0).trigger("onReset").remove(), M._afterZoomOut()))
        },
        play: function(e) {
            function t() {
                o(), M.current && M.player.isActive && (M.player.timer = setTimeout(M.next, M.current.playSpeed))
            }

            function i() {
                o(), c.unbind(".player"), M.player.isActive = !1, M.trigger("onPlayEnd")
            }
            var o = function() {
                clearTimeout(M.player.timer)
            };
            !0 === e || !M.player.isActive && !1 !== e ? M.current && (M.current.loop || M.current.index < M.group.length - 1) && (M.player.isActive = !0, c.bind({
                "onCancel.player beforeClose.player": i,
                "onUpdate.player": t,
                "beforeLoad.player": o
            }), t(), M.trigger("onPlayStart")) : i()
        },
        next: function(e) {
            var t = M.current;
            t && (h(e) || (e = t.direction.next), M.jumpto(t.index + 1, e, "next"))
        },
        prev: function(e) {
            var t = M.current;
            t && (h(e) || (e = t.direction.prev), M.jumpto(t.index - 1, e, "prev"))
        },
        jumpto: function(e, t, i) {
            var o = M.current;
            o && (e = I(e), M.direction = t || o.direction[e >= o.index ? "next" : "prev"], M.router = i || "jumpto", o.loop && (e < 0 && (e = o.group.length + e % o.group.length), e %= o.group.length), o.group[e] !== u) && (M.cancel(), M._start(e))
        },
        reposition: function(e, t) {
            var i = M.current,
                o = i ? i.wrap : null;
            o && (t = M._getPosition(t), e && "scroll" === e.type ? (delete t.position, o.stop(!0, !0).animate(t, 200)) : (o.css(t), i.pos = $.extend({}, i.dim, t)))
        },
        update: function(t) {
            var i = t && t.type,
                o = !i || "orientationchange" === i;
            o && (clearTimeout(a), a = null), M.isOpen && (a = a || setTimeout(function() {
                var e = M.current;
                e && !M.isClosing && (M.wrap.removeClass("fancybox-tmp"), (o || "load" === i || "resize" === i && e.autoResize) && M._setDimension(), "scroll" === i && e.canShrink || M.reposition(t), M.trigger("onUpdate"), a = null)
            }, o && !d ? 0 : 300))
        },
        toggle: function(e) {
            M.isOpen && (M.current.fitToView = "boolean" === $.type(e) ? e : !M.current.fitToView, d && (M.wrap.removeAttr("style").addClass("fancybox-tmp"), M.trigger("onUpdate")), M.update())
        },
        hideLoading: function() {
            c.unbind(".loading"), $("#fancybox-loading").remove()
        },
        showLoading: function() {
            var e, t;
            M.hideLoading(), e = $('<div id="fancybox-loading" class="loader-container main-loader TL-loader TL-loader_green"><div class="TL-loader-ani"><span class="TL-loader-fx"></span></div></div>').click(M.cancel).appendTo("body"), c.bind("keydown.loading", function(e) {
                27 === (e.which || e.keyCode) && (e.preventDefault(), M.cancel())
            }), M.defaults.fixed || (t = M.getViewport(), e.css({
                position: "absolute",
                top: .5 * t.h + t.y,
                left: .5 * t.w + t.x
            }))
        },
        getViewport: function() {
            var e = M.current && M.current.locked || !1,
                t = {
                    x: s.scrollLeft(),
                    y: s.scrollTop()
                };
            return e ? (t.w = e[0].clientWidth, t.h = e[0].clientHeight) : (t.w = d && i.innerWidth ? i.innerWidth : s.width(), t.h = d && i.innerHeight ? i.innerHeight : s.height()), t
        },
        unbindEvents: function() {
            M.wrap && p(M.wrap) && M.wrap.unbind(".fb"), c.unbind(".fb"), s.unbind(".fb")
        },
        bindEvents: function() {
            var t, l = M.current;
            l && (s.bind("orientationchange.fb" + (d ? "" : " resize.fb") + (l.autoCenter && !l.locked ? " scroll.fb" : ""), M.update), (t = l.keys) && c.bind("keydown.fb", function(i) {
                var o = i.which || i.keyCode,
                    e = i.target || i.srcElement;
                if (27 === o && M.coming) return !1;
                i.ctrlKey || i.altKey || i.shiftKey || i.metaKey || e && (e.type || $(e).is("[contenteditable]")) || $.each(t, function(e, t) {
                    return 1 < l.group.length && t[o] !== u ? (M[e](t[o]), i.preventDefault(), !1) : -1 < $.inArray(o, t) ? (M[e](), i.preventDefault(), !1) : void 0
                })
            }), $.fn.mousewheel) && l.mouseWheel && M.wrap.bind("mousewheel.fb", function(e, t, i, o) {
                for (var n, s = e.target || null, r = $(s), a = !1; r.length && !(a || r.is(".fancybox-skin") || r.is(".fancybox-wrap"));) a = (n = r[0]) && !(n.style.overflow && "hidden" === n.style.overflow) && (n.clientWidth && n.scrollWidth > n.clientWidth || n.clientHeight && n.scrollHeight > n.clientHeight), r = $(r).parent();
                0 === t || a || 1 < M.group.length && !l.canShrink && (0 < o || 0 < i ? M.prev(0 < o ? "down" : "left") : (o < 0 || i < 0) && M.next(o < 0 ? "up" : "right"), e.preventDefault())
            })
        },
        trigger: function(i, e) {
            var t, o = e || M.coming || M.current;
            if (o) {
                if (!1 === (t = $.isFunction(o[i]) ? o[i].apply(o, Array.prototype.slice.call(arguments, 1)) : t)) return !1;
                o.helpers && $.each(o.helpers, function(e, t) {
                    t && M.helpers[e] && $.isFunction(M.helpers[e][i]) && M.helpers[e][i]($.extend(!0, {}, M.helpers[e].defaults, t), o)
                }), c.trigger(i)
            }
        },
        isImage: function(e) {
            return h(e) && e.match(/(^data:image\/.*,)|(\.(jp(e|g|eg)|gif|png|bmp|webp|svg)((\?|#).*)?$)/i)
        },
        isSWF: function(e) {
            return h(e) && e.match(/\.(swf)((\?|#).*)?$/i)
        },
        _start: function(e) {
            var t, i, o = {};
            if (e = I(e), !(t = M.group[e] || null)) return !1;
            if (t = (o = $.extend(!0, {}, M.opts, t)).margin, i = o.padding, "number" === $.type(t) && (o.margin = [t, t, t, t]), "number" === $.type(i) && (o.padding = [i, i, i, i]), o.modal && $.extend(!0, o, {
                    closeBtn: !1,
                    closeClick: !1,
                    nextClick: !1,
                    arrows: !1,
                    mouseWheel: !1,
                    keys: null,
                    helpers: {
                        overlay: {
                            closeClick: !1
                        }
                    }
                }), o.autoSize && (o.autoWidth = o.autoHeight = !0), "auto" === o.width && (o.autoWidth = !0), "auto" === o.height && (o.autoHeight = !0), o.group = M.group, o.index = e, M.coming = o, !1 === M.trigger("beforeLoad")) M.coming = null;
            else {
                if (t = o.type, i = o.href, !t) return M.coming = null, !(!M.current || !M.router || "jumpto" === M.router) && (M.current.index = e, M[M.router](M.direction));
                if (M.isActive = !0, "image" !== t && "swf" !== t || (o.autoHeight = o.autoWidth = !1, o.scrolling = "visible"), "image" === t && (o.aspectRatio = !0), "iframe" === t && d && (o.scrolling = "scroll"), o.wrap = $(o.tpl.wrap).addClass("fancybox-" + (d ? "mobile" : "desktop") + " fancybox-type-" + t + " fancybox-tmp " + o.wrapCSS).appendTo(o.parent || "body"), $.extend(o, {
                        skin: $(".fancybox-skin", o.wrap),
                        outer: $(".fancybox-outer", o.wrap),
                        inner: $(".fancybox-inner", o.wrap)
                    }), $.each(["Top", "Right", "Bottom", "Left"], function(e, t) {
                        o.skin.css("padding" + t, P(o.padding[e]))
                    }), M.trigger("onReady"), "inline" === t || "html" === t) {
                    if (!o.content || !o.content.length) return M._error("content")
                } else if (!i) return M._error("href");
                "image" === t ? M._loadImage() : "ajax" === t ? M._loadAjax() : "iframe" === t ? M._loadIframe() : M._afterLoad()
            }
        },
        _error: function(e) {
            $.extend(M.coming, {
                type: "html",
                autoWidth: !0,
                autoHeight: !0,
                minWidth: 0,
                minHeight: 0,
                scrolling: "no",
                hasError: e,
                content: M.coming.tpl.error
            }), M._afterLoad()
        },
        _loadImage: function() {
            var e = M.imgPreload = new Image;
            e.onload = function() {
                this.onload = this.onerror = null, M.coming.width = this.width / M.opts.pixelRatio, M.coming.height = this.height / M.opts.pixelRatio, M._afterLoad()
            }, e.onerror = function() {
                this.onload = this.onerror = null, M._error("image")
            }, e.src = M.coming.href, !0 !== e.complete && M.showLoading()
        },
        _loadAjax: function() {
            var i = M.coming;
            M.showLoading(), M.ajaxLoad = $.ajax($.extend({}, i.ajax, {
                url: i.href,
                error: function(e, t) {
                    M.coming && "abort" !== t ? M._error("ajax", e) : M.hideLoading()
                },
                success: function(e, t) {
                    "success" === t && (i.content = e, M._afterLoad())
                }
            }))
        },
        _loadIframe: function() {
            var e = M.coming,
                t = $(e.tpl.iframe.replace(/\{rnd\}/g, (new Date).getTime())).attr("scrolling", d ? "auto" : e.iframe.scrolling).attr("src", e.href);
            $(e.wrap).bind("onReset", function() {
                try {
                    $(this).find("iframe").hide().attr("src", "//about:blank").end().empty()
                } catch (e) {}
            }), e.iframe.preload && (M.showLoading(), t.one("load", function() {
                $(this).data("ready", 1), d || $(this).bind("load.fb", M.update), $(this).parents(".fancybox-wrap").width("100%").removeClass("fancybox-tmp").show(), M._afterLoad()
            })), e.content = t.appendTo(e.inner), e.iframe.preload || M._afterLoad()
        },
        _preloadImages: function() {
            for (var e, t = M.group, i = M.current, o = t.length, n = i.preload ? Math.min(i.preload, o - 1) : 0, s = 1; s <= n; s += 1) "image" === (e = t[(i.index + s) % o]).type && e.href && ((new Image).src = e.href)
        },
        _afterLoad: function() {
            var e, i, t, o, n, s = M.coming,
                r = M.current,
                a = "fancybox-placeholder";
            if (M.hideLoading(), s && !1 !== M.isActive)
                if (!1 === M.trigger("afterLoad", s, r)) s.wrap.stop(!0).trigger("onReset").remove(), M.coming = null;
                else {
                    switch (r && (M.trigger("beforeChange", r), r.wrap.stop(!0).removeClass("fancybox-opened").find(".fancybox-item, .fancybox-nav").remove()), M.unbindEvents(), i = (e = s).content, t = s.type, s = s.scrolling, $.extend(M, {
                            wrap: e.wrap,
                            skin: e.skin,
                            outer: e.outer,
                            inner: e.inner,
                            current: e,
                            previous: r
                        }), o = e.href, t) {
                        case "inline":
                        case "ajax":
                        case "html":
                            e.selector ? i = $("<div>").html(i).find(e.selector) : p(i) && (i.data(a) || i.data(a, $('<div class="' + a + '"></div>').insertAfter(i).hide()), i = i.show().detach(), e.wrap.bind("onReset", function() {
                                $(this).find(i).length && i.hide().replaceAll(i.data(a)).data(a, !1)
                            }));
                            break;
                        case "image":
                            i = e.tpl.image.replace("{href}", o);
                            break;
                        case "swf":
                            i = '<object id="fancybox-swf" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="100%" height="100%"><param name="movie" value="' + o + '"></param>', n = "", $.each(e.swf, function(e, t) {
                                i += '<param name="' + e + '" value="' + t + '"></param>', n += " " + e + '="' + t + '"'
                            }), i += '<embed src="' + o + '" type="application/x-shockwave-flash" width="100%" height="100%"' + n + "></embed></object>"
                    }
                    p(i) && i.parent().is(e.inner) || e.inner.append(i), M.trigger("beforeShow"), e.inner.css("overflow", "yes" === s ? "scroll" : "no" === s ? "hidden" : s), M._setDimension(), M.reposition(), M.isOpen = !1, M.coming = null, M.bindEvents(), M.isOpened ? r.prevMethod && M.transitions[r.prevMethod]() : $(".fancybox-wrap").not(e.wrap).stop(!0).trigger("onReset").remove(), M.transitions[M.isOpened ? e.nextMethod : e.openMethod](), M._preloadImages()
                }
        },
        _setDimension: function() {
            var e, t, i, o, n, s, r, a, l, c, d, u, p, h, f = M.getViewport(),
                m = 0,
                g = M.wrap,
                v = M.skin,
                y = M.inner,
                b = M.current,
                w = b.width,
                _ = b.height,
                x = b.minWidth,
                k = b.minHeight,
                T = b.maxWidth,
                C = b.maxHeight,
                S = b.scrolling,
                E = b.scrollOutside ? b.scrollbarWidth : 0,
                A = b.margin,
                O = I(A[1] + A[3]),
                A = I(A[0] + A[2]);
            if (g.add(v).add(y).width("auto").height("auto").removeClass("fancybox-tmp"), i = O + (e = I(v.outerWidth(!0) - v.width())), o = A + (t = I(v.outerHeight(!0) - v.height())), n = L(w) ? (f.w - i) * I(w) / 100 : w, s = L(_) ? (f.h - o) * I(_) / 100 : _, "iframe" === b.type) {
                if (p = b.content, b.autoHeight && 1 === p.data("ready")) try {
                    p[0].contentWindow.document.location && (y.width(n).height(9999), h = p.contents().find("body"), E && h.css("overflow-x", "hidden"), s = h.outerHeight(!0))
                } catch (e) {}
            } else(b.autoWidth || b.autoHeight) && (y.addClass("fancybox-tmp"), b.autoWidth || y.width(n), b.autoHeight || y.height(s), b.autoWidth && (n = y.width()), b.autoHeight && (s = y.height()), y.removeClass("fancybox-tmp"));
            if (w = I(n), _ = I(s), a = n / s, x = I(L(x) ? I(x, "w") - i : x), T = I(L(T) ? I(T, "w") - i : T), k = I(L(k) ? I(k, "h") - o : k), h = T, r = C = I(L(C) ? I(C, "h") - o : C), b.fitToView && (T = Math.min(f.w - i, T), C = Math.min(f.h - o, C)), d = f.w - O, u = f.h - A, b.aspectRatio ? (_ = (w = C < (_ = T < w ? I((w = T) / a) : _) ? I((_ = C) * a) : w) < x ? I((w = x) / a) : _) < k && (w = I((_ = k) * a)) : (w = Math.max(x, Math.min(w, T)), b.autoHeight && "iframe" !== b.type && (y.width(w), _ = y.height()), _ = Math.max(k, Math.min(_, C))), b.fitToView)
                if (y.width(w).height(_), g.width(w + e), l = g.width(), c = g.height(), b.aspectRatio)
                    for (;
                        (d < l || u < c) && x < w && k < _ && !(19 < m++);) _ = Math.max(k, Math.min(C, _ - 10)), (w = I(_ * a)) < x && (_ = I((w = x) / a)), T < w && (_ = I((w = T) / a)), y.width(w).height(_), g.width(w + e), l = g.width(), c = g.height();
                else w = Math.max(x, Math.min(w, w - (l - d))), _ = Math.max(k, Math.min(_, _ - (c - u)));
            E && "auto" === S && _ < s && w + e + E < d && (w += E), y.width(w).height(_), g.width(w + e), l = g.width(), c = g.height(), i = (d < l || u < c) && x < w && k < _, o = b.aspectRatio ? w < h && _ < r && w < n && _ < s : (w < h || _ < r) && (w < n || _ < s), $.extend(b, {
                dim: {
                    width: P(l),
                    height: P(c)
                },
                origWidth: n,
                origHeight: s,
                canShrink: i,
                canExpand: o,
                wPadding: e,
                hPadding: t,
                wrapSpace: c - v.outerHeight(!0),
                skinSpace: v.height() - _
            }), !p && b.autoHeight && k < _ && _ < C && !o && y.height("auto")
        },
        _getPosition: function(e) {
            var t = M.current,
                i = M.getViewport(),
                o = t.margin,
                n = M.wrap.width() + o[1] + o[3],
                s = M.wrap.height() + o[0] + o[2],
                o = {
                    position: "absolute",
                    top: o[0],
                    left: o[3]
                };
            return t.autoCenter && t.fixed && !e && s <= i.h && n <= i.w ? o.position = "fixed" : t.locked || (o.top += i.y, o.left += i.x), o.top = P(Math.max(o.top, o.top + (i.h - s) * t.topRatio)), o.left = P(Math.max(o.left, o.left + (i.w - n) * t.leftRatio)), o
        },
        _afterZoomIn: function() {
            var t = M.current;
            t && (M.isOpen = M.isOpened = !0, M.wrap.css("overflow", "visible").addClass("fancybox-opened"), M.update(), (t.closeClick || t.nextClick && 1 < M.group.length) && M.inner.css("cursor", "pointer").bind("click.fb", function(e) {
                $(e.target).is("a") || $(e.target).parent().is("a") || (e.preventDefault(), M[t.closeClick ? "close" : "next"]())
            }), t.closeBtn && $(t.tpl.closeBtn).appendTo(M.skin).bind("click.fb", function(e) {
                e.preventDefault(), M.close()
            }), t.arrows && 1 < M.group.length && ((t.loop || 0 < t.index) && $(t.tpl.prev).appendTo(M.outer).bind("click.fb", M.prev), t.loop || t.index < M.group.length - 1) && $(t.tpl.next).appendTo(M.outer).bind("click.fb", M.next), M.trigger("afterShow"), t.loop || t.index !== t.group.length - 1 ? M.opts.autoPlay && !M.player.isActive && (M.opts.autoPlay = !1, M.play()) : M.play(!1))
        },
        _afterZoomOut: function(e) {
            e = e || M.current, $(".fancybox-wrap").trigger("onReset").remove(), $.extend(M, {
                group: {},
                opts: {},
                router: !1,
                current: null,
                isActive: !1,
                isOpened: !1,
                isOpen: !1,
                isClosing: !1,
                wrap: null,
                skin: null,
                outer: null,
                inner: null
            }), M.trigger("afterClose", e)
        }
    }), M.transitions = {
        getOrigPosition: function() {
            var e = M.current,
                t = e.element,
                i = e.orig,
                o = {},
                n = 50,
                s = 50,
                r = e.hPadding,
                a = e.wPadding,
                l = M.getViewport();
            return !i && e.isDom && t.is(":visible") && !(i = t.find("img:first")).length && (i = t), p(i) ? (o = i.offset(), i.is("img") && (n = i.outerWidth(), s = i.outerHeight())) : (o.top = l.y + (l.h - s) * e.topRatio, o.left = l.x + (l.w - n) * e.leftRatio), "fixed" !== M.wrap.css("position") && !e.locked || (o.top -= l.y, o.left -= l.x), o = {
                top: P(o.top - r * e.topRatio),
                left: P(o.left - a * e.leftRatio),
                width: P(n + a),
                height: P(s + r)
            }
        },
        step: function(e, t) {
            var i = t.prop,
                o = M.current,
                n = o.wrapSpace,
                s = o.skinSpace;
            "width" !== i && "height" !== i || (t = t.end === t.start ? 1 : (e - t.start) / (t.end - t.start), M.isClosing && (t = 1 - t), e = e - ("width" === i ? o.wPadding : o.hPadding), M.skin[i](I("width" === i ? e : e - n * t)), M.inner[i](I("width" === i ? e : e - n * t - s * t)))
        },
        zoomIn: function() {
            var e = M.current,
                t = e.pos,
                i = e.openEffect,
                o = "elastic" === i,
                n = $.extend({
                    opacity: 1
                }, t);
            delete n.position, o ? (t = this.getOrigPosition(), e.openOpacity && (t.opacity = .1)) : "fade" === i && (t.opacity = .1), M.wrap.css(t).animate(n, {
                duration: "none" === i ? 0 : e.openSpeed,
                easing: e.openEasing,
                step: o ? this.step : null,
                complete: M._afterZoomIn
            })
        },
        zoomOut: function() {
            var e = M.current,
                t = e.closeEffect,
                i = "elastic" === t,
                o = {
                    opacity: .1
                };
            i && (o = this.getOrigPosition(), e.closeOpacity) && (o.opacity = .1), M.wrap.animate(o, {
                duration: "none" === t ? 0 : e.closeSpeed,
                easing: e.closeEasing,
                step: i ? this.step : null,
                complete: M._afterZoomOut
            })
        },
        changeIn: function() {
            var e, t = M.current,
                i = t.nextEffect,
                o = t.pos,
                n = {
                    opacity: 1
                },
                s = M.direction;
            o.opacity = .1, "elastic" === i && (e = "down" === s || "up" === s ? "top" : "left", "down" === s || "right" === s ? (o[e] = P(I(o[e]) - 200), n[e] = "+=200px") : (o[e] = P(I(o[e]) + 200), n[e] = "-=200px")), "none" === i ? M._afterZoomIn() : M.wrap.css(o).animate(n, {
                duration: t.nextSpeed,
                easing: t.nextEasing,
                complete: M._afterZoomIn
            })
        },
        changeOut: function() {
            var e = M.previous,
                t = e.prevEffect,
                i = {
                    opacity: .1
                },
                o = M.direction;
            "elastic" === t && (i["down" === o || "up" === o ? "top" : "left"] = ("up" === o || "left" === o ? "-" : "+") + "=200px"), e.wrap.animate(i, {
                duration: "none" === t ? 0 : e.prevSpeed,
                easing: e.prevEasing,
                complete: function() {
                    $(this).trigger("onReset").remove()
                }
            })
        }
    }, M.helpers.overlay = {
        defaults: {
            closeClick: !0,
            speedOut: 200,
            showEarly: !0,
            css: {},
            locked: !d,
            fixed: !0
        },
        overlay: null,
        fixed: !1,
        el: $("html"),
        create: function(e) {
            e = $.extend({}, this.defaults, e), this.overlay && this.close(), this.overlay = $('<div class="fancybox-overlay"></div>').appendTo((M.coming || e).parent), this.fixed = !1, e.fixed && M.defaults.fixed && (this.overlay.addClass("fancybox-overlay-fixed"), this.fixed = !0)
        },
        open: function(e) {
            var t = this;
            e = $.extend({}, this.defaults, e), this.overlay ? this.overlay.unbind(".overlay").width("auto").height("auto") : this.create(e), this.fixed || (s.bind("resize.overlay", $.proxy(this.update, this)), this.update()), e.closeClick && this.overlay.bind("click.overlay", function(e) {
                if ($(e.target).hasClass("fancybox-overlay")) return (M.isActive ? M : t).close(), !1
            }), this.overlay.css(e.css).show()
        },
        close: function() {
            var e, t;
            s.unbind("resize.overlay"), this.el.hasClass("fancybox-lock") && ($(".fancybox-margin").removeClass("fancybox-margin"), e = s.scrollTop(), t = s.scrollLeft(), this.el.removeClass("fancybox-lock"), s.scrollTop(e).scrollLeft(t)), $(".fancybox-overlay").remove().hide(), $.extend(this, {
                overlay: null,
                fixed: !1
            })
        },
        update: function() {
            var e, t = "100%";
            this.overlay.width(t).height("100%"), r ? (e = Math.max(o.documentElement.offsetWidth, o.body.offsetWidth), c.width() > e && (t = c.width())) : c.width() > s.width() && (t = c.width()), this.overlay.width(t).height(c.height())
        },
        onReady: function(e, t) {
            var i = this.overlay;
            $(".fancybox-overlay").stop(!0, !0), i || this.create(e), e.locked && this.fixed && t.fixed && (i || (this.margin = c.height() > s.height() && $("html").css("margin-right").replace("px", "")), t.locked = this.overlay.append(t.wrap), t.fixed = !1), !0 === e.showEarly && this.beforeShow.apply(this, arguments)
        },
        beforeShow: function(e, t) {
            var i;
            t.locked && (!1 !== this.margin && ($("*").filter(function() {
                return "fixed" === $(this).css("position") && !$(this).hasClass("fancybox-overlay") && !$(this).hasClass("fancybox-wrap")
            }).addClass("fancybox-margin"), this.el.addClass("fancybox-margin")), t = s.scrollTop(), i = s.scrollLeft(), this.el.addClass("fancybox-lock"), s.scrollTop(t).scrollLeft(i)), this.open(e)
        },
        onUpdate: function() {
            this.fixed || this.update()
        },
        afterClose: function(e) {
            this.overlay && !M.coming && this.overlay.fadeOut(e.speedOut, $.proxy(this.close, this))
        }
    }, M.helpers.title = {
        defaults: {
            type: "float",
            position: "bottom"
        },
        beforeShow: function(e) {
            var t, i, o = M.current,
                n = o.title,
                s = e.type;
            if ($.isFunction(n) && (n = n.call(o.element, o)), h(n) && "" !== $.trim(n)) {
                switch (t = $('<div class="fancybox-title fancybox-title-' + s + '-wrap">' + n + "</div>"), s) {
                    case "inside":
                        i = M.skin;
                        break;
                    case "outside":
                        i = M.wrap;
                        break;
                    case "over":
                        i = M.inner;
                        break;
                    default:
                        i = M.skin, t.appendTo("body"), r && t.width(t.width()), t.wrapInner('<span class="child"></span>'), M.current.margin[2] += Math.abs(I(t.css("margin-bottom")))
                }
                t["top" === e.position ? "prependTo" : "appendTo"](i)
            }
        }
    }, $.fn.fancybox = function(s) {
        function e(e) {
            var t, i, o = $(this).blur(),
                n = l;
            e.ctrlKey || e.altKey || e.shiftKey || e.metaKey || o.is(".fancybox-wrap") || (t = s.groupAttr || "data-fancybox-group", (i = o.attr(t)) || (t = "rel", i = o.get(0)[t]), i && "" !== i && "nofollow" !== i && (n = (o = (o = a.length ? $(a) : r).filter("[" + t + '="' + i + '"]')).index(this)), s.index = n, !1 !== M.open(o, s) && e.preventDefault())
        }
        var r = $(this),
            a = this.selector || "",
            l = (s = s || {}).index || 0;
        return a && !1 !== s.live ? c.undelegate(a, "click.fb-start").delegate(a + ":not('.fancybox-item, .fancybox-nav')", "click.fb-start", e) : r.unbind("click.fb-start").bind("click.fb-start", e), this.filter("[data-fancybox-start=1]").trigger("click"), this
    }, c.ready(function() {
        var e, t;
        $.scrollbarWidth === u && ($.scrollbarWidth = function() {
            var e = $('<div style="width:50px;height:50px;overflow:auto"><div/></div>').appendTo("body"),
                t = e.children(),
                t = t.innerWidth() - t.height(99).innerWidth();
            return e.remove(), t
        }), $.support.fixedPosition === u && ($.support.fixedPosition = (e = $('<div style="position:fixed;top:20px;"></div>').appendTo("body"), t = 20 === e[0].offsetTop || 15 === e[0].offsetTop, e.remove(), t)), $.extend(M.defaults, {
            scrollbarWidth: $.scrollbarWidth(),
            fixed: $.support.fixedPosition,
            parent: $("body")
        }), e = $(i).width(), n.addClass("fancybox-lock-test"), t = $(i).width(), n.removeClass("fancybox-lock-test"), $("<style type='text/css'>.fancybox-margin{margin-right:" + (t - e) + "px;}</style>").appendTo("head")
    })
}(window, document, jQuery),
function(w, _, x) {
    "use strict";
    ! function o(n, s, r) {
        function a(i, e) {
            if (!s[i]) {
                if (!n[i]) {
                    var t = "function" == typeof require && require;
                    if (!e && t) return t(i, !0);
                    if (l) return l(i, !0);
                    e = new Error("Cannot find module '" + i + "'");
                    throw e.code = "MODULE_NOT_FOUND", e
                }
                t = s[i] = {
                    exports: {}
                };
                n[i][0].call(t.exports, function(e) {
                    var t = n[i][1][e];
                    return a(t || e)
                }, t, t.exports, o, n, s, r)
            }
            return s[i].exports
        }
        for (var l = "function" == typeof require && require, e = 0; e < r.length; e++) a(r[e]);
        return a
    }({
        1: [function(e, t, i) {
            function o(e) {
                return e && e.__esModule ? e : {
                    default: e
                }
            }
            Object.defineProperty(i, "__esModule", {
                value: !0
            });
            var d, u, n, p, h = e("./modules/handle-dom"),
                f = e("./modules/utils"),
                m = e("./modules/handle-swal-dom"),
                g = e("./modules/handle-click"),
                v = o(e("./modules/handle-key")),
                y = o(e("./modules/default-params")),
                b = o(e("./modules/set-params"));
            i.default = n = p = function() {
                function e(e) {
                    return (t[e] === x ? y.default : t)[e]
                }
                var t = arguments[0];
                if (h.addClass(_.body, "stop-scrolling"), m.resetInput(), t === x) return f.logStr("SweetAlert expects at least 1 attribute!"), !1;
                var i = f.extend({}, y.default);
                switch (typeof t) {
                    case "string":
                        i.title = t, i.text = arguments[1] || "", i.type = arguments[2] || "";
                        break;
                    case "object":
                        if (t.title === x) return f.logStr('Missing "title" argument!'), !1;
                        for (var o in i.title = t.title, y.default) i[o] = e(o);
                        i.confirmButtonText = i.showCancelButton ? "Confirm" : y.default.confirmButtonText, i.confirmButtonText = e("confirmButtonText"), i.doneFunction = arguments[1] || null;
                        break;
                    default:
                        return f.logStr('Unexpected type of argument! Expected "string" or "object", got ' + typeof t), !1
                }
                b.default(i), m.fixVerticalPosition(), m.openModal(arguments[1]);
                for (var n = m.getModal(), s = n.querySelectorAll("button"), r = ["onclick", "onmouseover", "onmouseout", "onmousedown", "onmouseup", "onfocus"], a = function(e) {
                        return g.handleButton(e, i, n)
                    }, l = 0; l < s.length; l++)
                    for (var c = 0; c < r.length; c++) s[l][r[c]] = a;
                m.getOverlay().onclick = a, d = w.onkeydown;
                w.onkeydown = function(e) {
                    return v.default(e, i, n)
                }, w.onfocus = function() {
                    setTimeout(function() {
                        u !== x && (u.focus(), u = x)
                    }, 0)
                }, p.enableButtons()
            }, n.setDefaults = p.setDefaults = function(e) {
                if (!e) throw new Error("userParams is required");
                if ("object" != typeof e) throw new Error("userParams has to be a object");
                f.extend(y.default, e)
            }, n.close = p.close = function() {
                var t = m.getModal(),
                    e = (h.fadeOut(m.getOverlay(), 5), h.fadeOut(t, 5), h.removeClass(t, "showSweetAlert"), h.addClass(t, "hideSweetAlert"), h.removeClass(t, "visible"), t.querySelector(".sa-icon.sa-success")),
                    e = (h.removeClass(e, "animate"), h.removeClass(e.querySelector(".sa-tip"), "animateSuccessTip"), h.removeClass(e.querySelector(".sa-long"), "animateSuccessLong"), t.querySelector(".sa-icon.sa-error")),
                    e = (h.removeClass(e, "animateErrorIcon"), h.removeClass(e.querySelector(".sa-x-mark"), "animateXMark"), t.querySelector(".sa-icon.sa-warning"));
                return h.removeClass(e, "pulseWarning"), h.removeClass(e.querySelector(".sa-body"), "pulseWarningIns"), h.removeClass(e.querySelector(".sa-dot"), "pulseWarningIns"), setTimeout(function() {
                    var e = t.getAttribute("data-custom-class");
                    h.removeClass(t, e)
                }, 300), h.removeClass(_.body, "stop-scrolling"), w.onkeydown = d, w.previousActiveElement && w.previousActiveElement.focus(), u = x, clearTimeout(t.timeout), !0
            }, n.showInputError = p.showInputError = function(e) {
                var t = m.getModal(),
                    i = t.querySelector(".sa-input-error"),
                    i = (h.addClass(i, "show"), t.querySelector(".sa-error-container"));
                h.addClass(i, "show"), i.querySelector("p").innerHTML = e, setTimeout(function() {
                    n.enableButtons()
                }, 1), t.querySelector("input").focus()
            }, n.resetInputError = p.resetInputError = function(e) {
                if (e && 13 === e.keyCode) return !1;
                var e = m.getModal(),
                    t = e.querySelector(".sa-input-error"),
                    t = (h.removeClass(t, "show"), e.querySelector(".sa-error-container"));
                h.removeClass(t, "show")
            }, n.disableButtons = p.disableButtons = function() {
                var e = m.getModal(),
                    t = e.querySelector("button.confirm"),
                    e = e.querySelector("button.cancel");
                t.disabled = !0, e.disabled = !0
            }, n.enableButtons = p.enableButtons = function() {
                var e = m.getModal(),
                    t = e.querySelector("button.confirm"),
                    e = e.querySelector("button.cancel");
                t.disabled = !1, e.disabled = !1
            }, void 0 !== w ? w.sweetAlert = w.swal = n : f.logStr("SweetAlert is a frontend module!"), t.exports = i.default
        }, {
            "./modules/default-params": 2,
            "./modules/handle-click": 3,
            "./modules/handle-dom": 4,
            "./modules/handle-key": 5,
            "./modules/handle-swal-dom": 6,
            "./modules/set-params": 8,
            "./modules/utils": 9
        }],
        2: [function(e, t, i) {
            Object.defineProperty(i, "__esModule", {
                value: !0
            });
            i.default = {
                title: "",
                text: "",
                type: null,
                allowOutsideClick: !1,
                showConfirmButton: !0,
                showCancelButton: !1,
                closeOnConfirm: !0,
                closeOnCancel: !0,
                confirmButtonText: "OK",
                confirmButtonColor: "#8CD4F5",
                cancelButtonText: "Cancel",
                imageUrl: null,
                imageSize: null,
                timer: null,
                customClass: "",
                html: !1,
                animation: !0,
                allowEscapeKey: !0,
                inputType: "text",
                inputPlaceholder: "",
                inputValue: "",
                showLoaderOnConfirm: !1
            }, t.exports = i.default
        }, {}],
        3: [function(e, t, i) {
            Object.defineProperty(i, "__esModule", {
                value: !0
            });

            function f(e, t) {
                var i = !0;
                v.hasClass(e, "show-input") && (i = (i = e.querySelector("input").value) || ""), t.doneFunction(i), t.closeOnConfirm && sweetAlert.close(), t.showLoaderOnConfirm && sweetAlert.disableButtons()
            }

            function m(e, t) {
                var i = String(t.doneFunction).replace(/\s/g, "");
                "function(" === i.substring(0, 9) && ")" !== i.substring(9, 10) && t.doneFunction(!1), t.closeOnCancel && sweetAlert.close()
            }
            var g = e("./utils"),
                v = (e("./handle-swal-dom"), e("./handle-dom"));
            i.default = {
                handleButton: function(e, t, i) {
                    function o(e) {
                        l && t.confirmButtonColor && (a.style.backgroundColor = e)
                    }
                    var n, s, r, e = e || w.event,
                        a = e.target || e.srcElement,
                        l = -1 !== a.className.indexOf("confirm"),
                        c = -1 !== a.className.indexOf("sweet-overlay"),
                        d = v.hasClass(i, "visible"),
                        u = t.doneFunction && "true" === i.getAttribute("data-has-done-function");
                    switch (l && t.confirmButtonColor && (n = t.confirmButtonColor, s = g.colorLuminance(n, -.04), r = g.colorLuminance(n, -.14)), e.type) {
                        case "mouseover":
                            o(s);
                            break;
                        case "mouseout":
                            o(n);
                            break;
                        case "mousedown":
                            o(r);
                            break;
                        case "mouseup":
                            o(s);
                            break;
                        case "focus":
                            var p = i.querySelector("button.confirm"),
                                h = i.querySelector("button.cancel");
                            l ? h.style.boxShadow = "none" : p.style.boxShadow = "none";
                            break;
                        case "click":
                            h = i === a, p = v.isDescendant(i, a);
                            (h || p || !d || t.allowOutsideClick) && (l && u && d ? f(i, t) : u && d || c ? m(0, t) : v.isDescendant(i, a) && "BUTTON" === a.tagName && sweetAlert.close())
                    }
                },
                handleConfirm: f,
                handleCancel: m
            }, t.exports = i.default
        }, {
            "./handle-dom": 4,
            "./handle-swal-dom": 6,
            "./utils": 9
        }],
        4: [function(e, t, i) {
            Object.defineProperty(i, "__esModule", {
                value: !0
            });

            function o(e, t) {
                return new RegExp(" " + t + " ").test(" " + e.className + " ")
            }

            function n(e) {
                e.style.opacity = "", e.style.display = "block"
            }

            function s(e) {
                e.style.opacity = "", e.style.display = "none"
            }
            i.hasClass = o, i.addClass = function(e, t) {
                o(e, t) || (e.className += " " + t)
            }, i.removeClass = function(e, t) {
                var i = " " + e.className.replace(/[\t\r\n]/g, " ") + " ";
                if (o(e, t)) {
                    for (; 0 <= i.indexOf(" " + t + " ");) i = i.replace(" " + t + " ", " ");
                    e.className = i.replace(/^\s+|\s+$/g, "")
                }
            }, i.escapeHtml = function(e) {
                var t = _.createElement("div");
                return t.appendChild(_.createTextNode(e)), t.innerHTML
            }, i._show = n, i.show = function(e) {
                if (e && !e.length) return n(e);
                for (var t = 0; t < e.length; ++t) n(e[t])
            }, i._hide = s, i.hide = function(e) {
                if (e && !e.length) return s(e);
                for (var t = 0; t < e.length; ++t) s(e[t])
            }, i.isDescendant = function(e, t) {
                for (var i = t.parentNode; null !== i;) {
                    if (i === e) return !0;
                    i = i.parentNode
                }
                return !1
            }, i.getTopMargin = function(e) {
                e.style.left = "-9999px", e.style.display = "block";
                var t = e.clientHeight,
                    i = "undefined" != typeof getComputedStyle ? parseInt(getComputedStyle(e).getPropertyValue("padding-top"), 10) : parseInt(e.currentStyle.padding);
                return e.style.left = "", e.style.display = "none", "-" + parseInt((t + i) / 2) + "px"
            }, i.fadeIn = function(e, t) {
                var i, o, n;

                function s() {
                    return n.apply(this, arguments)
                } + e.style.opacity < 1 && (t = t || 16, e.style.opacity = 0, e.style.display = "block", i = +new Date, n = function() {
                    e.style.opacity = +e.style.opacity + (new Date - i) / 100, i = +new Date, +e.style.opacity < 1 && setTimeout(o, t)
                }, s.toString = function() {
                    return n.toString()
                }, (o = s)()), e.style.display = "block"
            }, i.fadeOut = function(e, t) {
                t = t || 16, e.style.opacity = 1;
                var i, o = +new Date,
                    n = (i = function() {
                        e.style.opacity = +e.style.opacity - (new Date - o) / 100, o = +new Date, 0 < +e.style.opacity ? setTimeout(n, t) : e.style.display = "none"
                    }, s.toString = function() {
                        return i.toString()
                    }, s);

                function s() {
                    return i.apply(this, arguments)
                }
                n()
            }, i.fireClick = function(e) {
                var t;
                "function" == typeof MouseEvent ? (t = new MouseEvent("click", {
                    view: w,
                    bubbles: !1,
                    cancelable: !0
                }), e.dispatchEvent(t)) : _.createEvent ? ((t = _.createEvent("MouseEvents")).initEvent("click", !1, !1), e.dispatchEvent(t)) : _.createEventObject ? e.fireEvent("onclick") : "function" == typeof e.onclick && e.onclick()
            }, i.stopEventPropagation = function(e) {
                "function" == typeof e.stopPropagation ? (e.stopPropagation(), e.preventDefault()) : w.event && w.event.hasOwnProperty("cancelBubble") && (w.event.cancelBubble = !0)
            }
        }, {}],
        5: [function(e, t, i) {
            Object.defineProperty(i, "__esModule", {
                value: !0
            });
            var d = e("./handle-dom"),
                u = e("./handle-swal-dom");
            i.default = function(e, t, i) {
                var e = e || w.event,
                    o = e.keyCode || e.which,
                    n = i.querySelector("button.confirm"),
                    s = i.querySelector("button.cancel"),
                    r = i.querySelectorAll("button[tabindex]");
                if (-1 !== [9, 13, 32, 27].indexOf(o)) {
                    for (var a = e.target || e.srcElement, l = -1, c = 0; c < r.length; c++)
                        if (a === r[c]) {
                            l = c;
                            break
                        } 9 === o ? (a = -1 === l ? n : l === r.length - 1 ? r[0] : r[l + 1], d.stopEventPropagation(e), a.focus(), t.confirmButtonColor && u.setFocusStyle(a, t.confirmButtonColor)) : 13 === o ? "INPUT" === a.tagName && (a = n).focus() : 27 === o && !0 === t.allowEscapeKey && d.fireClick(a = s, e)
                }
            }, t.exports = i.default
        }, {
            "./handle-dom": 4,
            "./handle-swal-dom": 6
        }],
        6: [function(e, t, i) {
            function o(e) {
                return e && e.__esModule ? e : {
                    default: e
                }
            }

            function n() {
                var e = _.createElement("div");
                for (e.innerHTML = p.default; e.firstChild;) _.body.appendChild(e.firstChild)
            }

            function s() {
                var e = h();
                return e ? e.querySelector("input") : void 0
            }

            function r() {
                return _.querySelector(".sweet-overlay")
            }

            function a(e) {
                if (e && 13 === e.keyCode) return !1;
                var t = (e = h()).querySelector(".sa-input-error"),
                    t = (d.removeClass(t, "show"), e.querySelector(".sa-error-container"));
                d.removeClass(t, "show")
            }
            Object.defineProperty(i, "__esModule", {
                value: !0
            });
            var l, c = e("./utils"),
                d = e("./handle-dom"),
                u = o(e("./default-params")),
                p = o(e("./injected-html")),
                h = (l = function() {
                    var e = _.querySelector(".sweet-alert");
                    return e || (n(), e = h()), e
                }, f.toString = function() {
                    return l.toString()
                }, f);

            function f() {
                return l.apply(this, arguments)
            }
            i.sweetAlertInitialize = n, i.getModal = h, i.getOverlay = r, i.getInput = s, i.setFocusStyle = function(e, t) {
                t = c.hexToRgb(t);
                e.style.boxShadow = "0 0 2px rgba(" + t + ", 0.8), inset 0 0 0 1px rgba(0, 0, 0, 0.05)"
            }, i.openModal = function(e) {
                var t = h();
                d.fadeIn(r(), 10), d.show(t), d.addClass(t, "showSweetAlert"), d.removeClass(t, "hideSweetAlert"), w.previousActiveElement = _.activeElement;
                t.querySelector("button.confirm").focus(), setTimeout(function() {
                    d.addClass(t, "visible")
                }, 500);
                var i, o = t.getAttribute("data-timer");
                "null" !== o && "" !== o && (i = e, t.timeout = setTimeout(function() {
                    (i ? "true" === t.getAttribute("data-has-done-function") : null) ? i(null): sweetAlert.close()
                }, o))
            }, i.resetInput = function() {
                var e = h(),
                    t = s();
                d.removeClass(e, "show-input"), t.value = u.default.inputValue, t.setAttribute("type", u.default.inputType), t.setAttribute("placeholder", u.default.inputPlaceholder), a()
            }, i.resetInputError = a, i.fixVerticalPosition = function() {
                h().style.marginTop = d.getTopMargin(h())
            }
        }, {
            "./default-params": 2,
            "./handle-dom": 4,
            "./injected-html": 7,
            "./utils": 9
        }],
        7: [function(e, t, i) {
            Object.defineProperty(i, "__esModule", {
                value: !0
            });
            i.default = '<div class="sweet-overlay" tabIndex="-1"></div><div class="sweet-alert"><div class="sa-icon sa-error">\n      <span class="sa-x-mark">\n        <span class="sa-line sa-left"></span>\n        <span class="sa-line sa-right"></span>\n      </span>\n    </div><div class="sa-icon sa-warning">\n      <span class="sa-body"></span>\n      <span class="sa-dot"></span>\n    </div><div class="sa-icon sa-info"></div><div class="sa-icon sa-success">\n      <span class="sa-line sa-tip"></span>\n      <span class="sa-line sa-long"></span>\n\n      <div class="sa-placeholder"></div>\n      <div class="sa-fix"></div>\n    </div><div class="sa-icon sa-custom"></div><h2>Title</h2>\n    <p>Text</p>\n    <fieldset>\n      <input type="text" tabIndex="3" />\n      <div class="sa-input-error"></div>\n    </fieldset><div class="sa-error-container">\n      <div class="icon">!</div>\n      <p>Not valid!</p>\n    </div><div class="sa-button-container">\n      <button class="cancel" tabIndex="2">Cancel</button>\n      <div class="sa-confirm-button-container">\n        <button class="confirm" tabIndex="1">OK</button><div class="la-ball-fall">\n          <div></div>\n          <div></div>\n          <div></div>\n        </div>\n      </div>\n    </div></div>', t.exports = i.default
        }, {}],
        8: [function(e, t, i) {
            Object.defineProperty(i, "__esModule", {
                value: !0
            });
            var c = e("./utils"),
                d = e("./handle-swal-dom"),
                u = e("./handle-dom"),
                p = ["error", "warning", "info", "success", "input", "prompt"];
            i.default = function(n) {
                var e, t, s = d.getModal(),
                    i = s.querySelector("h2"),
                    o = s.querySelector("p"),
                    r = s.querySelector("button.cancel"),
                    a = s.querySelector("button.confirm");
                if (i.innerHTML = n.html ? n.title : u.escapeHtml(n.title).split("\n").join("<br>"), o.innerHTML = n.html ? n.text : u.escapeHtml(n.text || "").split("\n").join("<br>"), n.text && u.show(o), n.customClass ? (u.addClass(s, n.customClass), s.setAttribute("data-custom-class", n.customClass)) : (i = s.getAttribute("data-custom-class"), u.removeClass(s, i), s.setAttribute("data-custom-class", "")), u.hide(s.querySelectorAll(".sa-icon")), n.type && !c.isIE8()) {
                    var o = function() {
                        for (var e = !1, t = 0; t < p.length; t++)
                            if (n.type === p[t]) {
                                e = !0;
                                break
                            } if (!e) return logStr("Unknown alert type: " + n.type), {
                            v: !1
                        };
                        var i = x,
                            o = (-1 !== ["success", "error", "warning", "info"].indexOf(n.type) && (i = s.querySelector(".sa-icon.sa-" + n.type), u.show(i)), d.getInput());
                        switch (n.type) {
                            case "success":
                                u.addClass(i, "animate"), u.addClass(i.querySelector(".sa-tip"), "animateSuccessTip"), u.addClass(i.querySelector(".sa-long"), "animateSuccessLong");
                                break;
                            case "error":
                                u.addClass(i, "animateErrorIcon"), u.addClass(i.querySelector(".sa-x-mark"), "animateXMark");
                                break;
                            case "warning":
                                u.addClass(i, "pulseWarning"), u.addClass(i.querySelector(".sa-body"), "pulseWarningIns"), u.addClass(i.querySelector(".sa-dot"), "pulseWarningIns");
                                break;
                            case "input":
                            case "prompt":
                                o.setAttribute("type", n.inputType), o.value = n.inputValue, o.setAttribute("placeholder", n.inputPlaceholder), u.addClass(s, "show-input"), setTimeout(function() {
                                    o.focus(), o.addEventListener("keyup", swal.resetInputError)
                                }, 400)
                        }
                    }();
                    if ("object" == typeof o) return o.v
                }
                n.imageUrl && ((i = s.querySelector(".sa-icon.sa-custom")).style.backgroundImage = "url(" + n.imageUrl + ")", u.show(i), e = o = 80, n.imageSize && (l = (t = n.imageSize.toString().split("x"))[0], t = t[1], l && t ? (o = l, e = t) : logStr("Parameter imageSize expects value with format WIDTHxHEIGHT, got " + n.imageSize)), i.setAttribute("style", i.getAttribute("style") + "width:" + o + "px; height:" + e + "px")), s.setAttribute("data-has-cancel-button", n.showCancelButton), n.showCancelButton ? r.style.display = "inline-block" : u.hide(r), s.setAttribute("data-has-confirm-button", n.showConfirmButton), n.showConfirmButton ? a.style.display = "inline-block" : u.hide(a), n.cancelButtonText && (r.innerHTML = u.escapeHtml(n.cancelButtonText)), n.confirmButtonText && (a.innerHTML = u.escapeHtml(n.confirmButtonText)), n.confirmButtonColor && (a.style.backgroundColor = n.confirmButtonColor, a.style.borderLeftColor = n.confirmLoadingButtonColor, a.style.borderRightColor = n.confirmLoadingButtonColor, d.setFocusStyle(a, n.confirmButtonColor)), s.setAttribute("data-allow-outside-click", n.allowOutsideClick);
                var l = !!n.doneFunction;
                s.setAttribute("data-has-done-function", l), n.animation ? "string" == typeof n.animation ? s.setAttribute("data-animation", n.animation) : s.setAttribute("data-animation", "pop") : s.setAttribute("data-animation", "none"), s.setAttribute("data-timer", n.timer)
            }, t.exports = i.default
        }, {
            "./handle-dom": 4,
            "./handle-swal-dom": 6,
            "./utils": 9
        }],
        9: [function(e, t, i) {
            Object.defineProperty(i, "__esModule", {
                value: !0
            });
            i.extend = function(e, t) {
                for (var i in t) t.hasOwnProperty(i) && (e[i] = t[i]);
                return e
            }, i.hexToRgb = function(e) {
                e = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(e);
                return e ? parseInt(e[1], 16) + ", " + parseInt(e[2], 16) + ", " + parseInt(e[3], 16) : null
            }, i.isIE8 = function() {
                return w.attachEvent && !w.addEventListener
            }, i.logStr = function(e) {
                w.console && w.console.log("SweetAlert: " + e)
            }, i.colorLuminance = function(e, t) {
                (e = String(e).replace(/[^0-9a-f]/gi, "")).length < 6 && (e = e[0] + e[0] + e[1] + e[1] + e[2] + e[2]), t = t || 0;
                for (var i, o = "#", n = 0; n < 3; n++) i = parseInt(e.substr(2 * n, 2), 16), o += ("00" + (i = Math.round(Math.min(Math.max(0, i + i * t), 255)).toString(16))).substr(i.length);
                return o
            }
        }, {}]
    }, {}, [1]), "function" == typeof define && define.amd ? define(function() {
        return sweetAlert
    }) : "undefined" != typeof module && module.exports && (module.exports = sweetAlert)
}(window, document),
function(e) {
    "use strict";
    "function" == typeof define && define.amd ? define(["jquery"], e) : "undefined" != typeof exports ? module.exports = e(require("jquery")) : e(jQuery)
}(function(c) {
    "use strict";
    var o, r = window.Slick || {};
    o = 0, (r = function(e, t) {
        var i = this;
        i.defaults = {
            accessibility: !0,
            adaptiveHeight: !1,
            appendArrows: c(e),
            appendDots: c(e),
            arrows: !0,
            asNavFor: null,
            prevArrow: '<button class="slick-prev" aria-label="Previous" type="button">Previous</button>',
            nextArrow: '<button class="slick-next" aria-label="Next" type="button">Next</button>',
            autoplay: !1,
            autoplaySpeed: 3e3,
            centerMode: !1,
            centerPadding: "50px",
            cssEase: "ease",
            customPaging: function(e, t) {
                return c('<button type="button" />').text(t + 1)
            },
            dots: !1,
            dotsClass: "slick-dots",
            draggable: !0,
            easing: "linear",
            edgeFriction: .35,
            fade: !1,
            focusOnSelect: !1,
            focusOnChange: !1,
            infinite: !0,
            initialSlide: 0,
            lazyLoad: "ondemand",
            mobileFirst: !1,
            pauseOnHover: !0,
            pauseOnFocus: !0,
            pauseOnDotsHover: !1,
            respondTo: "window",
            responsive: null,
            rows: 1,
            rtl: !1,
            slide: "",
            slidesPerRow: 1,
            slidesToShow: 1,
            slidesToScroll: 1,
            speed: 500,
            swipe: !0,
            swipeToSlide: !1,
            touchMove: !0,
            touchThreshold: 5,
            useCSS: !0,
            useTransform: !0,
            variableWidth: !1,
            vertical: !1,
            verticalSwiping: !1,
            waitForAnimate: !0,
            zIndex: 1e3
        }, i.initials = {
            animating: !1,
            dragging: !1,
            autoPlayTimer: null,
            currentDirection: 0,
            currentLeft: null,
            currentSlide: 0,
            direction: 1,
            $dots: null,
            listWidth: null,
            listHeight: null,
            loadIndex: 0,
            $nextArrow: null,
            $prevArrow: null,
            scrolling: !1,
            slideCount: null,
            slideWidth: null,
            $slideTrack: null,
            $slides: null,
            sliding: !1,
            slideOffset: 0,
            swipeLeft: null,
            swiping: !1,
            $list: null,
            touchObject: {},
            transformsEnabled: !1,
            unslicked: !1
        }, c.extend(i, i.initials), i.activeBreakpoint = null, i.animType = null, i.animProp = null, i.breakpoints = [], i.breakpointSettings = [], i.cssTransitions = !1, i.focussed = !1, i.interrupted = !1, i.hidden = "hidden", i.paused = !0, i.positionProp = null, i.respondTo = null, i.rowCount = 1, i.shouldClick = !0, i.$slider = c(e), i.$slidesCache = null, i.transformType = null, i.transitionType = null, i.visibilityChange = "visibilitychange", i.windowWidth = 0, i.windowTimer = null, e = c(e).data("slick") || {}, i.options = c.extend({}, i.defaults, t, e), i.currentSlide = i.options.initialSlide, i.originalSettings = i.options, void 0 !== document.mozHidden ? (i.hidden = "mozHidden", i.visibilityChange = "mozvisibilitychange") : void 0 !== document.webkitHidden && (i.hidden = "webkitHidden", i.visibilityChange = "webkitvisibilitychange"), i.autoPlay = c.proxy(i.autoPlay, i), i.autoPlayClear = c.proxy(i.autoPlayClear, i), i.autoPlayIterator = c.proxy(i.autoPlayIterator, i), i.changeSlide = c.proxy(i.changeSlide, i), i.clickHandler = c.proxy(i.clickHandler, i), i.selectHandler = c.proxy(i.selectHandler, i), i.setPosition = c.proxy(i.setPosition, i), i.swipeHandler = c.proxy(i.swipeHandler, i), i.dragHandler = c.proxy(i.dragHandler, i), i.keyHandler = c.proxy(i.keyHandler, i), i.instanceUid = o++, i.htmlExpr = /^(?:\s*(<[\w\W]+>)[^>]*)$/, i.registerBreakpoints(), i.init(!0)
    }).prototype.activateADA = function() {
        this.$slideTrack.find(".slick-active").attr({
            "aria-hidden": "false"
        }).find("a, input, button, select").attr({
            tabindex: "0"
        })
    }, r.prototype.addSlide = r.prototype.slickAdd = function(e, t, i) {
        var o = this;
        if ("boolean" == typeof t) i = t, t = null;
        else if (t < 0 || t >= o.slideCount) return !1;
        o.unload(), "number" == typeof t ? 0 === t && 0 === o.$slides.length ? c(e).appendTo(o.$slideTrack) : i ? c(e).insertBefore(o.$slides.eq(t)) : c(e).insertAfter(o.$slides.eq(t)) : !0 === i ? c(e).prependTo(o.$slideTrack) : c(e).appendTo(o.$slideTrack), o.$slides = o.$slideTrack.children(this.options.slide), o.$slideTrack.children(this.options.slide).detach(), o.$slideTrack.append(o.$slides), o.$slides.each(function(e, t) {
            c(t).attr("data-slick-index", e)
        }), o.$slidesCache = o.$slides, o.reinit()
    }, r.prototype.animateHeight = function() {
        var e, t = this;
        1 === t.options.slidesToShow && !0 === t.options.adaptiveHeight && !1 === t.options.vertical && (e = t.$slides.eq(t.currentSlide).outerHeight(!0), t.$list.animate({
            height: e
        }, t.options.speed))
    }, r.prototype.animateSlide = function(e, t) {
        var i = {},
            o = this;
        o.animateHeight(), !0 === o.options.rtl && !1 === o.options.vertical && (e = -e), !1 === o.transformsEnabled ? !1 === o.options.vertical ? o.$slideTrack.animate({
            left: e
        }, o.options.speed, o.options.easing, t) : o.$slideTrack.animate({
            top: e
        }, o.options.speed, o.options.easing, t) : !1 === o.cssTransitions ? (!0 === o.options.rtl && (o.currentLeft = -o.currentLeft), c({
            animStart: o.currentLeft
        }).animate({
            animStart: e
        }, {
            duration: o.options.speed,
            easing: o.options.easing,
            step: function(e) {
                e = Math.ceil(e), !1 === o.options.vertical ? i[o.animType] = "translate(" + e + "px, 0px)" : i[o.animType] = "translate(0px," + e + "px)", o.$slideTrack.css(i)
            },
            complete: function() {
                t && t.call()
            }
        })) : (o.applyTransition(), e = Math.ceil(e), !1 === o.options.vertical ? i[o.animType] = "translate3d(" + e + "px, 0px, 0px)" : i[o.animType] = "translate3d(0px," + e + "px, 0px)", o.$slideTrack.css(i), t && setTimeout(function() {
            o.disableTransition(), t.call()
        }, o.options.speed))
    }, r.prototype.getNavTarget = function() {
        var e = this.options.asNavFor;
        return e = e && null !== e ? c(e).not(this.$slider) : e
    }, r.prototype.asNavFor = function(t) {
        var e = this.getNavTarget();
        null !== e && "object" == typeof e && e.each(function() {
            var e = c(this).slick("getSlick");
            e.unslicked || e.slideHandler(t, !0)
        })
    }, r.prototype.applyTransition = function(e) {
        var t = this,
            i = {};
        !1 === t.options.fade ? i[t.transitionType] = t.transformType + " " + t.options.speed + "ms " + t.options.cssEase : i[t.transitionType] = "opacity " + t.options.speed + "ms " + t.options.cssEase, (!1 === t.options.fade ? t.$slideTrack : t.$slides.eq(e)).css(i)
    }, r.prototype.autoPlay = function() {
        var e = this;
        e.autoPlayClear(), e.slideCount > e.options.slidesToShow && (e.autoPlayTimer = setInterval(e.autoPlayIterator, e.options.autoplaySpeed))
    }, r.prototype.autoPlayClear = function() {
        this.autoPlayTimer && clearInterval(this.autoPlayTimer)
    }, r.prototype.autoPlayIterator = function() {
        var e = this,
            t = e.currentSlide + e.options.slidesToScroll;
        e.paused || e.interrupted || e.focussed || (!1 === e.options.infinite && (1 === e.direction && e.currentSlide + 1 === e.slideCount - 1 ? e.direction = 0 : 0 === e.direction && (t = e.currentSlide - e.options.slidesToScroll, e.currentSlide - 1 == 0) && (e.direction = 1)), e.slideHandler(t))
    }, r.prototype.buildArrows = function() {
        var e = this;
        !0 === e.options.arrows && (e.$prevArrow = c(e.options.prevArrow).addClass("slick-arrow"), e.$nextArrow = c(e.options.nextArrow).addClass("slick-arrow"), e.slideCount > e.options.slidesToShow ? (e.$prevArrow.removeClass("slick-hidden").removeAttr("aria-hidden tabindex"), e.$nextArrow.removeClass("slick-hidden").removeAttr("aria-hidden tabindex"), e.htmlExpr.test(e.options.prevArrow) && e.$prevArrow.prependTo(e.options.appendArrows), e.htmlExpr.test(e.options.nextArrow) && e.$nextArrow.appendTo(e.options.appendArrows), !0 !== e.options.infinite && e.$prevArrow.addClass("slick-disabled").attr("aria-disabled", "true")) : e.$prevArrow.add(e.$nextArrow).addClass("slick-hidden").attr({
            "aria-disabled": "true",
            tabindex: "-1"
        }))
    }, r.prototype.buildDots = function() {
        var e, t, i = this;
        if (!0 === i.options.dots) {
            for (i.$slider.addClass("slick-dotted"), t = c("<ul />").addClass(i.options.dotsClass), e = 0; e <= i.getDotCount(); e += 1) t.append(c("<li />").append(i.options.customPaging.call(this, i, e)));
            i.$dots = t.appendTo(i.options.appendDots), i.$dots.find("li").first().addClass("slick-active")
        }
    }, r.prototype.buildOut = function() {
        var e = this;
        e.$slides = e.$slider.children(e.options.slide + ":not(.slick-cloned)").addClass("slick-slide"), e.slideCount = e.$slides.length, e.$slides.each(function(e, t) {
            c(t).attr("data-slick-index", e).data("originalStyling", c(t).attr("style") || "")
        }), e.$slider.addClass("slick-slider"), e.$slideTrack = 0 === e.slideCount ? c('<div class="slick-track"/>').appendTo(e.$slider) : e.$slides.wrapAll('<div class="slick-track"/>').parent(), e.$list = e.$slideTrack.wrap('<div class="slick-list"/>').parent(), e.$slideTrack.css("opacity", 0), !0 !== e.options.centerMode && !0 !== e.options.swipeToSlide || (e.options.slidesToScroll = 1), c("img[data-lazy]", e.$slider).not("[src]").addClass("slick-loading"), e.setupInfinite(), e.buildArrows(), e.buildDots(), e.updateDots(), e.setSlideClasses("number" == typeof e.currentSlide ? e.currentSlide : 0), !0 === e.options.draggable && e.$list.addClass("draggable")
    }, r.prototype.buildRows = function() {
        var e, t, i, o = this,
            n = document.createDocumentFragment(),
            s = o.$slider.children();
        if (1 < o.options.rows) {
            for (i = o.options.slidesPerRow * o.options.rows, t = Math.ceil(s.length / i), e = 0; e < t; e++) {
                for (var r = document.createElement("div"), a = 0; a < o.options.rows; a++) {
                    for (var l = document.createElement("div"), c = 0; c < o.options.slidesPerRow; c++) {
                        var d = e * i + (a * o.options.slidesPerRow + c);
                        s.get(d) && l.appendChild(s.get(d))
                    }
                    r.appendChild(l)
                }
                n.appendChild(r)
            }
            o.$slider.empty().append(n), o.$slider.children().children().children().css({
                width: 100 / o.options.slidesPerRow + "%",
                display: "inline-block"
            })
        }
    }, r.prototype.checkResponsive = function(e, t) {
        var i, o, n, s = this,
            r = !1,
            a = s.$slider.width(),
            l = window.innerWidth || c(window).width();
        if ("window" === s.respondTo ? n = l : "slider" === s.respondTo ? n = a : "min" === s.respondTo && (n = Math.min(l, a)), s.options.responsive && s.options.responsive.length && null !== s.options.responsive) {
            for (i in o = null, s.breakpoints) s.breakpoints.hasOwnProperty(i) && (!1 === s.originalSettings.mobileFirst ? n < s.breakpoints[i] && (o = s.breakpoints[i]) : n > s.breakpoints[i] && (o = s.breakpoints[i]));
            null !== o ? null !== s.activeBreakpoint && o === s.activeBreakpoint && !t || (s.activeBreakpoint = o, "unslick" === s.breakpointSettings[o] ? s.unslick(o) : (s.options = c.extend({}, s.originalSettings, s.breakpointSettings[o]), !0 === e && (s.currentSlide = s.options.initialSlide), s.refresh(e)), r = o) : null !== s.activeBreakpoint && (s.activeBreakpoint = null, s.options = s.originalSettings, !0 === e && (s.currentSlide = s.options.initialSlide), s.refresh(e), r = o), e || !1 === r || s.$slider.trigger("breakpoint", [s, r])
        }
    }, r.prototype.changeSlide = function(e, t) {
        var i, o = this,
            n = c(e.currentTarget);
        switch (n.is("a") && e.preventDefault(), n.is("li") || (n = n.closest("li")), i = o.slideCount % o.options.slidesToScroll != 0 ? 0 : (o.slideCount - o.currentSlide) % o.options.slidesToScroll, e.data.message) {
            case "previous":
                s = 0 == i ? o.options.slidesToScroll : o.options.slidesToShow - i, o.slideCount > o.options.slidesToShow && o.slideHandler(o.currentSlide - s, !1, t);
                break;
            case "next":
                s = 0 == i ? o.options.slidesToScroll : i, o.slideCount > o.options.slidesToShow && o.slideHandler(o.currentSlide + s, !1, t);
                break;
            case "index":
                var s = 0 === e.data.index ? 0 : e.data.index || n.index() * o.options.slidesToScroll;
                o.slideHandler(o.checkNavigable(s), !1, t), n.children().trigger("focus");
                break;
            default:
                return
        }
    }, r.prototype.checkNavigable = function(e) {
        var t = this.getNavigableIndexes(),
            i = 0;
        if (e > t[t.length - 1]) e = t[t.length - 1];
        else
            for (var o in t) {
                if (e < t[o]) {
                    e = i;
                    break
                }
                i = t[o]
            }
        return e
    }, r.prototype.cleanUpEvents = function() {
        var e = this;
        e.options.dots && null !== e.$dots && (c("li", e.$dots).off("click.slick", e.changeSlide).off("mouseenter.slick", c.proxy(e.interrupt, e, !0)).off("mouseleave.slick", c.proxy(e.interrupt, e, !1)), !0 === e.options.accessibility) && e.$dots.off("keydown.slick", e.keyHandler), e.$slider.off("focus.slick blur.slick"), !0 === e.options.arrows && e.slideCount > e.options.slidesToShow && (e.$prevArrow && e.$prevArrow.off("click.slick", e.changeSlide), e.$nextArrow && e.$nextArrow.off("click.slick", e.changeSlide), !0 === e.options.accessibility) && (e.$prevArrow && e.$prevArrow.off("keydown.slick", e.keyHandler), e.$nextArrow) && e.$nextArrow.off("keydown.slick", e.keyHandler), e.$list.off("touchstart.slick mousedown.slick", e.swipeHandler), e.$list.off("touchmove.slick mousemove.slick", e.swipeHandler), e.$list.off("touchend.slick mouseup.slick", e.swipeHandler), e.$list.off("touchcancel.slick mouseleave.slick", e.swipeHandler), e.$list.off("click.slick", e.clickHandler), c(document).off(e.visibilityChange, e.visibility), e.cleanUpSlideEvents(), !0 === e.options.accessibility && e.$list.off("keydown.slick", e.keyHandler), !0 === e.options.focusOnSelect && c(e.$slideTrack).children().off("click.slick", e.selectHandler), c(window).off("orientationchange.slick.slick-" + e.instanceUid, e.orientationChange), c(window).off("resize.slick.slick-" + e.instanceUid, e.resize), c("[draggable!=true]", e.$slideTrack).off("dragstart", e.preventDefault), c(window).off("load.slick.slick-" + e.instanceUid, e.setPosition)
    }, r.prototype.cleanUpSlideEvents = function() {
        var e = this;
        e.$list.off("mouseenter.slick", c.proxy(e.interrupt, e, !0)), e.$list.off("mouseleave.slick", c.proxy(e.interrupt, e, !1))
    }, r.prototype.cleanUpRows = function() {
        var e;
        1 < this.options.rows && ((e = this.$slides.children().children()).removeAttr("style"), this.$slider.empty().append(e))
    }, r.prototype.clickHandler = function(e) {
        !1 === this.shouldClick && (e.stopImmediatePropagation(), e.stopPropagation(), e.preventDefault())
    }, r.prototype.destroy = function(e) {
        var t = this;
        t.autoPlayClear(), t.touchObject = {}, t.cleanUpEvents(), c(".slick-cloned", t.$slider).detach(), t.$dots && t.$dots.remove(), t.$prevArrow && t.$prevArrow.length && (t.$prevArrow.removeClass("slick-disabled slick-arrow slick-hidden").removeAttr("aria-hidden aria-disabled tabindex").css("display", ""), t.htmlExpr.test(t.options.prevArrow)) && t.$prevArrow.remove(), t.$nextArrow && t.$nextArrow.length && (t.$nextArrow.removeClass("slick-disabled slick-arrow slick-hidden").removeAttr("aria-hidden aria-disabled tabindex").css("display", ""), t.htmlExpr.test(t.options.nextArrow)) && t.$nextArrow.remove(), t.$slides && (t.$slides.removeClass("slick-slide slick-active slick-center slick-visible slick-current").removeAttr("aria-hidden").removeAttr("data-slick-index").each(function() {
            c(this).attr("style", c(this).data("originalStyling"))
        }), t.$slideTrack.children(this.options.slide).detach(), t.$slideTrack.detach(), t.$list.detach(), t.$slider.append(t.$slides)), t.cleanUpRows(), t.$slider.removeClass("slick-slider"), t.$slider.removeClass("slick-initialized"), t.$slider.removeClass("slick-dotted"), t.unslicked = !0, e || t.$slider.trigger("destroy", [t])
    }, r.prototype.disableTransition = function(e) {
        var t = {};
        t[this.transitionType] = "", (!1 === this.options.fade ? this.$slideTrack : this.$slides.eq(e)).css(t)
    }, r.prototype.fadeSlide = function(e, t) {
        var i = this;
        !1 === i.cssTransitions ? (i.$slides.eq(e).css({
            zIndex: i.options.zIndex
        }), i.$slides.eq(e).animate({
            opacity: 1
        }, i.options.speed, i.options.easing, t)) : (i.applyTransition(e), i.$slides.eq(e).css({
            opacity: 1,
            zIndex: i.options.zIndex
        }), t && setTimeout(function() {
            i.disableTransition(e), t.call()
        }, i.options.speed))
    }, r.prototype.fadeSlideOut = function(e) {
        var t = this;
        !1 === t.cssTransitions ? t.$slides.eq(e).animate({
            opacity: 0,
            zIndex: t.options.zIndex - 2
        }, t.options.speed, t.options.easing) : (t.applyTransition(e), t.$slides.eq(e).css({
            opacity: 0,
            zIndex: t.options.zIndex - 2
        }))
    }, r.prototype.filterSlides = r.prototype.slickFilter = function(e) {
        var t = this;
        null !== e && (t.$slidesCache = t.$slides, t.unload(), t.$slideTrack.children(this.options.slide).detach(), t.$slidesCache.filter(e).appendTo(t.$slideTrack), t.reinit())
    }, r.prototype.focusHandler = function() {
        var i = this;
        i.$slider.off("focus.slick blur.slick").on("focus.slick blur.slick", "*", function(e) {
            e.stopImmediatePropagation();
            var t = c(this);
            setTimeout(function() {
                i.options.pauseOnFocus && (i.focussed = t.is(":focus"), i.autoPlay())
            }, 0)
        })
    }, r.prototype.getCurrent = r.prototype.slickCurrentSlide = function() {
        return this.currentSlide
    }, r.prototype.getDotCount = function() {
        var e = this,
            t = 0,
            i = 0,
            o = 0;
        if (!0 === e.options.infinite)
            if (e.slideCount <= e.options.slidesToShow) ++o;
            else
                for (; t < e.slideCount;) ++o, t = i + e.options.slidesToScroll, i += e.options.slidesToScroll <= e.options.slidesToShow ? e.options.slidesToScroll : e.options.slidesToShow;
        else if (!0 === e.options.centerMode) o = e.slideCount;
        else if (e.options.asNavFor)
            for (; t < e.slideCount;) ++o, t = i + e.options.slidesToScroll, i += e.options.slidesToScroll <= e.options.slidesToShow ? e.options.slidesToScroll : e.options.slidesToShow;
        else o = 1 + Math.ceil((e.slideCount - e.options.slidesToShow) / e.options.slidesToScroll);
        return o - 1
    }, r.prototype.getLeft = function(e) {
        var t, i, o = this,
            n = 0;
        return o.slideOffset = 0, t = o.$slides.first().outerHeight(!0), !0 === o.options.infinite ? (o.slideCount > o.options.slidesToShow && (o.slideOffset = o.slideWidth * o.options.slidesToShow * -1, i = -1, !0 === o.options.vertical && !0 === o.options.centerMode && (2 === o.options.slidesToShow ? i = -1.5 : 1 === o.options.slidesToShow && (i = -2)), n = t * o.options.slidesToShow * i), o.slideCount % o.options.slidesToScroll != 0 && e + o.options.slidesToScroll > o.slideCount && o.slideCount > o.options.slidesToShow && (n = e > o.slideCount ? (o.slideOffset = (o.options.slidesToShow - (e - o.slideCount)) * o.slideWidth * -1, (o.options.slidesToShow - (e - o.slideCount)) * t * -1) : (o.slideOffset = o.slideCount % o.options.slidesToScroll * o.slideWidth * -1, o.slideCount % o.options.slidesToScroll * t * -1))) : e + o.options.slidesToShow > o.slideCount && (o.slideOffset = (e + o.options.slidesToShow - o.slideCount) * o.slideWidth, n = (e + o.options.slidesToShow - o.slideCount) * t), o.slideCount <= o.options.slidesToShow && (n = o.slideOffset = 0), !0 === o.options.centerMode && o.slideCount <= o.options.slidesToShow ? o.slideOffset = o.slideWidth * Math.floor(o.options.slidesToShow) / 2 - o.slideWidth * o.slideCount / 2 : !0 === o.options.centerMode && !0 === o.options.infinite ? o.slideOffset += o.slideWidth * Math.floor(o.options.slidesToShow / 2) - o.slideWidth : !0 === o.options.centerMode && (o.slideOffset = 0, o.slideOffset += o.slideWidth * Math.floor(o.options.slidesToShow / 2)), i = !1 === o.options.vertical ? e * o.slideWidth * -1 + o.slideOffset : e * t * -1 + n, !0 === o.options.variableWidth && (t = o.slideCount <= o.options.slidesToShow || !1 === o.options.infinite ? o.$slideTrack.children(".slick-slide").eq(e) : o.$slideTrack.children(".slick-slide").eq(e + o.options.slidesToShow), i = !0 === o.options.rtl ? t[0] ? -1 * (o.$slideTrack.width() - t[0].offsetLeft - t.width()) : 0 : t[0] ? -1 * t[0].offsetLeft : 0, !0 === o.options.centerMode) && (t = o.slideCount <= o.options.slidesToShow || !1 === o.options.infinite ? o.$slideTrack.children(".slick-slide").eq(e) : o.$slideTrack.children(".slick-slide").eq(e + o.options.slidesToShow + 1), i = !0 === o.options.rtl ? t[0] ? -1 * (o.$slideTrack.width() - t[0].offsetLeft - t.width()) : 0 : t[0] ? -1 * t[0].offsetLeft : 0, i += (o.$list.width() - t.outerWidth()) / 2), i
    }, r.prototype.getOption = r.prototype.slickGetOption = function(e) {
        return this.options[e]
    }, r.prototype.getNavigableIndexes = function() {
        for (var e = this, t = 0, i = 0, o = [], n = !1 === e.options.infinite ? e.slideCount : (t = -1 * e.options.slidesToScroll, i = -1 * e.options.slidesToScroll, 2 * e.slideCount); t < n;) o.push(t), t = i + e.options.slidesToScroll, i += e.options.slidesToScroll <= e.options.slidesToShow ? e.options.slidesToScroll : e.options.slidesToShow;
        return o
    }, r.prototype.getSlick = function() {
        return this
    }, r.prototype.getSlideCount = function() {
        var i, o = this,
            n = !0 === o.options.centerMode ? o.slideWidth * Math.floor(o.options.slidesToShow / 2) : 0;
        return !0 === o.options.swipeToSlide ? (o.$slideTrack.find(".slick-slide").each(function(e, t) {
            if (t.offsetLeft - n + c(t).outerWidth() / 2 > -1 * o.swipeLeft) return i = t, !1
        }), Math.abs(c(i).attr("data-slick-index") - o.currentSlide) || 1) : o.options.slidesToScroll
    }, r.prototype.goTo = r.prototype.slickGoTo = function(e, t) {
        this.changeSlide({
            data: {
                message: "index",
                index: parseInt(e)
            }
        }, t)
    }, r.prototype.init = function(e) {
        var t = this;
        c(t.$slider).hasClass("slick-initialized") || (c(t.$slider).addClass("slick-initialized"), t.buildRows(), t.buildOut(), t.setProps(), t.startLoad(), t.loadSlider(), t.initializeEvents(), t.updateArrows(), t.updateDots(), t.checkResponsive(!0), t.focusHandler()), e && t.$slider.trigger("init", [t]), !0 === t.options.accessibility && t.initADA(), t.options.autoplay && (t.paused = !1, t.autoPlay())
    }, r.prototype.initADA = function() {
        var i = this,
            o = Math.ceil(i.slideCount / i.options.slidesToShow),
            n = i.getNavigableIndexes().filter(function(e) {
                return 0 <= e && e < i.slideCount
            });
        i.$slides.add(i.$slideTrack.find(".slick-cloned")).attr({
            "aria-hidden": "true",
            tabindex: "-1"
        }).find("a, input, button, select").attr({
            tabindex: "-1"
        }), null !== i.$dots && (i.$slides.not(i.$slideTrack.find(".slick-cloned")).each(function(e) {
            var t = n.indexOf(e);
            c(this).attr({
                role: "tabpanel",
                id: "slick-slide" + i.instanceUid + e,
                tabindex: -1
            }), -1 !== t && c(this).attr({
                "aria-describedby": "slick-slide-control" + i.instanceUid + t
            })
        }), i.$dots.attr("role", "tablist").find("li").each(function(e) {
            var t = n[e];
            c(this).attr({
                role: "presentation"
            }), c(this).find("button").first().attr({
                role: "tab",
                id: "slick-slide-control" + i.instanceUid + e,
                "aria-controls": "slick-slide" + i.instanceUid + t,
                "aria-label": e + 1 + " of " + o,
                "aria-selected": null,
                tabindex: "-1"
            })
        }).eq(i.currentSlide).find("button").attr({
            "aria-selected": "true",
            tabindex: "0"
        }).end());
        for (var e = i.currentSlide, t = e + i.options.slidesToShow; e < t; e++) i.$slides.eq(e).attr("tabindex", 0);
        i.activateADA()
    }, r.prototype.initArrowEvents = function() {
        var e = this;
        !0 === e.options.arrows && e.slideCount > e.options.slidesToShow && (e.$prevArrow.off("click.slick").on("click.slick", {
            message: "previous"
        }, e.changeSlide), e.$nextArrow.off("click.slick").on("click.slick", {
            message: "next"
        }, e.changeSlide), !0 === e.options.accessibility) && (e.$prevArrow.on("keydown.slick", e.keyHandler), e.$nextArrow.on("keydown.slick", e.keyHandler))
    }, r.prototype.initDotEvents = function() {
        var e = this;
        !0 === e.options.dots && (c("li", e.$dots).on("click.slick", {
            message: "index"
        }, e.changeSlide), !0 === e.options.accessibility) && e.$dots.on("keydown.slick", e.keyHandler), !0 === e.options.dots && !0 === e.options.pauseOnDotsHover && c("li", e.$dots).on("mouseenter.slick", c.proxy(e.interrupt, e, !0)).on("mouseleave.slick", c.proxy(e.interrupt, e, !1))
    }, r.prototype.initSlideEvents = function() {
        var e = this;
        e.options.pauseOnHover && (e.$list.on("mouseenter.slick", c.proxy(e.interrupt, e, !0)), e.$list.on("mouseleave.slick", c.proxy(e.interrupt, e, !1)))
    }, r.prototype.initializeEvents = function() {
        var e = this;
        e.initArrowEvents(), e.initDotEvents(), e.initSlideEvents(), e.$list.on("touchstart.slick mousedown.slick", {
            action: "start"
        }, e.swipeHandler), e.$list.on("touchmove.slick mousemove.slick", {
            action: "move"
        }, e.swipeHandler), e.$list.on("touchend.slick mouseup.slick", {
            action: "end"
        }, e.swipeHandler), e.$list.on("touchcancel.slick mouseleave.slick", {
            action: "end"
        }, e.swipeHandler), e.$list.on("click.slick", e.clickHandler), c(document).on(e.visibilityChange, c.proxy(e.visibility, e)), !0 === e.options.accessibility && e.$list.on("keydown.slick", e.keyHandler), !0 === e.options.focusOnSelect && c(e.$slideTrack).children().on("click.slick", e.selectHandler), c(window).on("orientationchange.slick.slick-" + e.instanceUid, c.proxy(e.orientationChange, e)), c(window).on("resize.slick.slick-" + e.instanceUid, c.proxy(e.resize, e)), c("[draggable!=true]", e.$slideTrack).on("dragstart", e.preventDefault), c(window).on("load.slick.slick-" + e.instanceUid, e.setPosition), c(e.setPosition)
    }, r.prototype.initUI = function() {
        var e = this;
        !0 === e.options.arrows && e.slideCount > e.options.slidesToShow && (e.$prevArrow.show(), e.$nextArrow.show()), !0 === e.options.dots && e.slideCount > e.options.slidesToShow && e.$dots.show()
    }, r.prototype.keyHandler = function(e) {
        var t = this;
        e.target.tagName.match("TEXTAREA|INPUT|SELECT") || (37 === e.keyCode && !0 === t.options.accessibility ? t.changeSlide({
            data: {
                message: !0 === t.options.rtl ? "next" : "previous"
            }
        }) : 39 === e.keyCode && !0 === t.options.accessibility && t.changeSlide({
            data: {
                message: !0 === t.options.rtl ? "previous" : "next"
            }
        }))
    }, r.prototype.lazyLoad = function() {
        function e(e) {
            c("img[data-lazy]", e).each(function() {
                var e = c(this),
                    t = c(this).attr("data-lazy"),
                    i = c(this).attr("data-srcset"),
                    o = c(this).attr("data-sizes") || s.$slider.attr("data-sizes"),
                    n = document.createElement("img");
                n.onload = function() {
                    e.animate({
                        opacity: 0
                    }, 100, function() {
                        i && (e.attr("srcset", i), o) && e.attr("sizes", o), e.attr("src", t).animate({
                            opacity: 1
                        }, 200, function() {
                            e.removeAttr("data-lazy data-srcset data-sizes").removeClass("slick-loading")
                        }), s.$slider.trigger("lazyLoaded", [s, e, t])
                    })
                }, n.onerror = function() {
                    e.removeAttr("data-lazy").removeClass("slick-loading").addClass("slick-lazyload-error"), s.$slider.trigger("lazyLoadError", [s, e, t])
                }, n.src = t
            })
        }
        var t, i, o, s = this;
        if (!0 === s.options.centerMode ? o = !0 === s.options.infinite ? (i = s.currentSlide + (s.options.slidesToShow / 2 + 1)) + s.options.slidesToShow + 2 : (i = Math.max(0, s.currentSlide - (s.options.slidesToShow / 2 + 1)), s.options.slidesToShow / 2 + 1 + 2 + s.currentSlide) : (i = s.options.infinite ? s.options.slidesToShow + s.currentSlide : s.currentSlide, o = Math.ceil(i + s.options.slidesToShow), !0 === s.options.fade && (0 < i && i--, o <= s.slideCount) && o++), t = s.$slider.find(".slick-slide").slice(i, o), "anticipated" === s.options.lazyLoad)
            for (var n = i - 1, r = o, a = s.$slider.find(".slick-slide"), l = 0; l < s.options.slidesToScroll; l++) n < 0 && (n = s.slideCount - 1), t = (t = t.add(a.eq(n))).add(a.eq(r)), n--, r++;
        e(t), s.slideCount <= s.options.slidesToShow ? e(s.$slider.find(".slick-slide")) : s.currentSlide >= s.slideCount - s.options.slidesToShow ? e(s.$slider.find(".slick-cloned").slice(0, s.options.slidesToShow)) : 0 === s.currentSlide && e(s.$slider.find(".slick-cloned").slice(-1 * s.options.slidesToShow))
    }, r.prototype.loadSlider = function() {
        var e = this;
        e.setPosition(), e.$slideTrack.css({
            opacity: 1
        }), e.$slider.removeClass("slick-loading"), e.initUI(), "progressive" === e.options.lazyLoad && e.progressiveLazyLoad()
    }, r.prototype.next = r.prototype.slickNext = function() {
        this.changeSlide({
            data: {
                message: "next"
            }
        })
    }, r.prototype.orientationChange = function() {
        this.checkResponsive(), this.setPosition()
    }, r.prototype.pause = r.prototype.slickPause = function() {
        this.autoPlayClear(), this.paused = !0
    }, r.prototype.play = r.prototype.slickPlay = function() {
        var e = this;
        e.autoPlay(), e.options.autoplay = !0, e.paused = !1, e.focussed = !1, e.interrupted = !1
    }, r.prototype.postSlide = function(e) {
        var t = this;
        t.unslicked || (t.$slider.trigger("afterChange", [t, e]), t.animating = !1, t.slideCount > t.options.slidesToShow && t.setPosition(), t.swipeLeft = null, t.options.autoplay && t.autoPlay(), !0 === t.options.accessibility && (t.initADA(), t.options.focusOnChange) && c(t.$slides.get(t.currentSlide)).attr("tabindex", 0).focus())
    }, r.prototype.prev = r.prototype.slickPrev = function() {
        this.changeSlide({
            data: {
                message: "previous"
            }
        })
    }, r.prototype.preventDefault = function(e) {
        e.preventDefault()
    }, r.prototype.progressiveLazyLoad = function(e) {
        e = e || 1;
        var t, i, o, n, s = this,
            r = c("img[data-lazy]", s.$slider);
        r.length ? (t = r.first(), i = t.attr("data-lazy"), o = t.attr("data-srcset"), n = t.attr("data-sizes") || s.$slider.attr("data-sizes"), (r = document.createElement("img")).onload = function() {
            o && (t.attr("srcset", o), n) && t.attr("sizes", n), t.attr("src", i).removeAttr("data-lazy data-srcset data-sizes").removeClass("slick-loading"), !0 === s.options.adaptiveHeight && s.setPosition(), s.$slider.trigger("lazyLoaded", [s, t, i]), s.progressiveLazyLoad()
        }, r.onerror = function() {
            e < 3 ? setTimeout(function() {
                s.progressiveLazyLoad(e + 1)
            }, 500) : (t.removeAttr("data-lazy").removeClass("slick-loading").addClass("slick-lazyload-error"), s.$slider.trigger("lazyLoadError", [s, t, i]), s.progressiveLazyLoad())
        }, r.src = i) : s.$slider.trigger("allImagesLoaded", [s])
    }, r.prototype.refresh = function(e) {
        var t = this,
            i = t.slideCount - t.options.slidesToShow;
        !t.options.infinite && t.currentSlide > i && (t.currentSlide = i), t.slideCount <= t.options.slidesToShow && (t.currentSlide = 0), i = t.currentSlide, t.destroy(!0), c.extend(t, t.initials, {
            currentSlide: i
        }), t.init(), e || t.changeSlide({
            data: {
                message: "index",
                index: i
            }
        }, !1)
    }, r.prototype.registerBreakpoints = function() {
        var e, t, i, o = this,
            n = o.options.responsive || null;
        if ("array" === c.type(n) && n.length) {
            for (e in o.respondTo = o.options.respondTo || "window", n)
                if (i = o.breakpoints.length - 1, n.hasOwnProperty(e)) {
                    for (t = n[e].breakpoint; 0 <= i;) o.breakpoints[i] && o.breakpoints[i] === t && o.breakpoints.splice(i, 1), i--;
                    o.breakpoints.push(t), o.breakpointSettings[t] = n[e].settings
                } o.breakpoints.sort(function(e, t) {
                return o.options.mobileFirst ? e - t : t - e
            })
        }
    }, r.prototype.reinit = function() {
        var e = this;
        e.$slides = e.$slideTrack.children(e.options.slide).addClass("slick-slide"), e.slideCount = e.$slides.length, e.currentSlide >= e.slideCount && 0 !== e.currentSlide && (e.currentSlide = e.currentSlide - e.options.slidesToScroll), e.slideCount <= e.options.slidesToShow && (e.currentSlide = 0), e.registerBreakpoints(), e.setProps(), e.setupInfinite(), e.buildArrows(), e.updateArrows(), e.initArrowEvents(), e.buildDots(), e.updateDots(), e.initDotEvents(), e.cleanUpSlideEvents(), e.initSlideEvents(), e.checkResponsive(!1, !0), !0 === e.options.focusOnSelect && c(e.$slideTrack).children().on("click.slick", e.selectHandler), e.setSlideClasses("number" == typeof e.currentSlide ? e.currentSlide : 0), e.setPosition(), e.focusHandler(), e.paused = !e.options.autoplay, e.autoPlay(), e.$slider.trigger("reInit", [e])
    }, r.prototype.resize = function() {
        var e = this;
        c(window).width() !== e.windowWidth && (clearTimeout(e.windowDelay), e.windowDelay = window.setTimeout(function() {
            e.windowWidth = c(window).width(), e.checkResponsive(), e.unslicked || e.setPosition()
        }, 50))
    }, r.prototype.removeSlide = r.prototype.slickRemove = function(e, t, i) {
        var o = this;
        if (e = "boolean" == typeof e ? !0 === (t = e) ? 0 : o.slideCount - 1 : !0 === t ? --e : e, o.slideCount < 1 || e < 0 || e > o.slideCount - 1) return !1;
        o.unload(), (!0 === i ? o.$slideTrack.children() : o.$slideTrack.children(this.options.slide).eq(e)).remove(), o.$slides = o.$slideTrack.children(this.options.slide), o.$slideTrack.children(this.options.slide).detach(), o.$slideTrack.append(o.$slides), o.$slidesCache = o.$slides, o.reinit()
    }, r.prototype.setCSS = function(e) {
        var t, i, o = this,
            n = {};
        !0 === o.options.rtl && (e = -e), t = "left" == o.positionProp ? Math.ceil(e) + "px" : "0px", i = "top" == o.positionProp ? Math.ceil(e) + "px" : "0px", n[o.positionProp] = e, !1 !== o.transformsEnabled && (!(n = {}) === o.cssTransitions ? n[o.animType] = "translate(" + t + ", " + i + ")" : n[o.animType] = "translate3d(" + t + ", " + i + ", 0px)"), o.$slideTrack.css(n)
    }, r.prototype.setDimensions = function() {
        var e = this,
            t = (!1 === e.options.vertical ? !0 === e.options.centerMode && e.$list.css({
                padding: "0px " + e.options.centerPadding
            }) : (e.$list.height(e.$slides.first().outerHeight(!0) * e.options.slidesToShow), !0 === e.options.centerMode && e.$list.css({
                padding: e.options.centerPadding + " 0px"
            })), e.listWidth = e.$list.width(), e.listHeight = e.$list.height(), !1 === e.options.vertical && !1 === e.options.variableWidth ? (e.slideWidth = Math.ceil(e.listWidth / e.options.slidesToShow), e.$slideTrack.width(Math.ceil(e.slideWidth * e.$slideTrack.children(".slick-slide").length))) : !0 === e.options.variableWidth ? e.$slideTrack.width(5e3 * e.slideCount) : (e.slideWidth = Math.ceil(e.listWidth), e.$slideTrack.height(Math.ceil(e.$slides.first().outerHeight(!0) * e.$slideTrack.children(".slick-slide").length))), e.$slides.first().outerWidth(!0) - e.$slides.first().width());
        !1 === e.options.variableWidth && e.$slideTrack.children(".slick-slide").width(e.slideWidth - t)
    }, r.prototype.setFade = function() {
        var i, o = this;
        o.$slides.each(function(e, t) {
            i = o.slideWidth * e * -1, !0 === o.options.rtl ? c(t).css({
                position: "relative",
                right: i,
                top: 0,
                zIndex: o.options.zIndex - 2,
                opacity: 0
            }) : c(t).css({
                position: "relative",
                left: i,
                top: 0,
                zIndex: o.options.zIndex - 2,
                opacity: 0
            })
        }), o.$slides.eq(o.currentSlide).css({
            zIndex: o.options.zIndex - 1,
            opacity: 1
        })
    }, r.prototype.setHeight = function() {
        var e, t = this;
        1 === t.options.slidesToShow && !0 === t.options.adaptiveHeight && !1 === t.options.vertical && (e = t.$slides.eq(t.currentSlide).outerHeight(!0), t.$list.css("height", e))
    }, r.prototype.setOption = r.prototype.slickSetOption = function() {
        var e, t, i, o, n, s = this,
            r = !1;
        if ("object" === c.type(arguments[0]) ? (i = arguments[0], r = arguments[1], n = "multiple") : "string" === c.type(arguments[0]) && (i = arguments[0], o = arguments[1], r = arguments[2], "responsive" === arguments[0] && "array" === c.type(arguments[1]) ? n = "responsive" : void 0 !== arguments[1] && (n = "single")), "single" === n) s.options[i] = o;
        else if ("multiple" === n) c.each(i, function(e, t) {
            s.options[e] = t
        });
        else if ("responsive" === n)
            for (t in o)
                if ("array" !== c.type(s.options.responsive)) s.options.responsive = [o[t]];
                else {
                    for (e = s.options.responsive.length - 1; 0 <= e;) s.options.responsive[e].breakpoint === o[t].breakpoint && s.options.responsive.splice(e, 1), e--;
                    s.options.responsive.push(o[t])
                } r && (s.unload(), s.reinit())
    }, r.prototype.setPosition = function() {
        var e = this;
        e.setDimensions(), e.setHeight(), !1 === e.options.fade ? e.setCSS(e.getLeft(e.currentSlide)) : e.setFade(), e.$slider.trigger("setPosition", [e])
    }, r.prototype.setProps = function() {
        var e = this,
            t = document.body.style;
        e.positionProp = !0 === e.options.vertical ? "top" : "left", "top" === e.positionProp ? e.$slider.addClass("slick-vertical") : e.$slider.removeClass("slick-vertical"), void 0 === t.WebkitTransition && void 0 === t.MozTransition && void 0 === t.msTransition || !0 === e.options.useCSS && (e.cssTransitions = !0), e.options.fade && ("number" == typeof e.options.zIndex ? e.options.zIndex < 3 && (e.options.zIndex = 3) : e.options.zIndex = e.defaults.zIndex), void 0 !== t.OTransform && (e.animType = "OTransform", e.transformType = "-o-transform", e.transitionType = "OTransition", void 0 === t.perspectiveProperty) && void 0 === t.webkitPerspective && (e.animType = !1), void 0 !== t.MozTransform && (e.animType = "MozTransform", e.transformType = "-moz-transform", e.transitionType = "MozTransition", void 0 === t.perspectiveProperty) && void 0 === t.MozPerspective && (e.animType = !1), void 0 !== t.webkitTransform && (e.animType = "webkitTransform", e.transformType = "-webkit-transform", e.transitionType = "webkitTransition", void 0 === t.perspectiveProperty) && void 0 === t.webkitPerspective && (e.animType = !1), void 0 !== t.msTransform && (e.animType = "msTransform", e.transformType = "-ms-transform", e.transitionType = "msTransition", void 0 === t.msTransform) && (e.animType = !1), void 0 !== t.transform && !1 !== e.animType && (e.animType = "transform", e.transformType = "transform", e.transitionType = "transition"), e.transformsEnabled = e.options.useTransform && null !== e.animType && !1 !== e.animType
    }, r.prototype.setSlideClasses = function(e) {
        var t, i, o, n = this,
            s = n.$slider.find(".slick-slide").removeClass("slick-active slick-center slick-current").attr("aria-hidden", "true");
        n.$slides.eq(e).addClass("slick-current"), !0 === n.options.centerMode ? (i = n.options.slidesToShow % 2 == 0 ? 1 : 0, o = Math.floor(n.options.slidesToShow / 2), !0 === n.options.infinite && ((o <= e && e <= n.slideCount - 1 - o ? n.$slides.slice(e - o + i, e + o + 1) : (t = n.options.slidesToShow + e, s.slice(t - o + 1 + i, t + o + 2))).addClass("slick-active").attr("aria-hidden", "false"), 0 === e ? s.eq(s.length - 1 - n.options.slidesToShow).addClass("slick-center") : e === n.slideCount - 1 && s.eq(n.options.slidesToShow).addClass("slick-center")), n.$slides.eq(e).addClass("slick-center")) : (0 <= e && e <= n.slideCount - n.options.slidesToShow ? n.$slides.slice(e, e + n.options.slidesToShow) : s.length <= n.options.slidesToShow ? s : (i = n.slideCount % n.options.slidesToShow, t = !0 === n.options.infinite ? n.options.slidesToShow + e : e, n.options.slidesToShow == n.options.slidesToScroll && n.slideCount - e < n.options.slidesToShow ? s.slice(t - (n.options.slidesToShow - i), t + i) : s.slice(t, t + n.options.slidesToShow))).addClass("slick-active").attr("aria-hidden", "false"), "ondemand" !== n.options.lazyLoad && "anticipated" !== n.options.lazyLoad || n.lazyLoad()
    }, r.prototype.setupInfinite = function() {
        var e, t, i, o = this;
        if (!0 === o.options.fade && (o.options.centerMode = !1), !0 === o.options.infinite && !1 === o.options.fade && (t = null, o.slideCount > o.options.slidesToShow)) {
            for (i = !0 === o.options.centerMode ? o.options.slidesToShow + 1 : o.options.slidesToShow, e = o.slideCount; e > o.slideCount - i; --e) c(o.$slides[t = e - 1]).clone(!0).attr("id", "").attr("data-slick-index", t - o.slideCount).prependTo(o.$slideTrack).addClass("slick-cloned");
            for (e = 0; e < i + o.slideCount; e += 1) t = e, c(o.$slides[t]).clone(!0).attr("id", "").attr("data-slick-index", t + o.slideCount).appendTo(o.$slideTrack).addClass("slick-cloned");
            o.$slideTrack.find(".slick-cloned").find("[id]").each(function() {
                c(this).attr("id", "")
            })
        }
    }, r.prototype.interrupt = function(e) {
        e || this.autoPlay(), this.interrupted = e
    }, r.prototype.selectHandler = function(e) {
        e = c(e.target).is(".slick-slide") ? c(e.target) : c(e.target).parents(".slick-slide"), e = (e = parseInt(e.attr("data-slick-index"))) || 0;
        this.slideCount <= this.options.slidesToShow ? this.slideHandler(e, !1, !0) : this.slideHandler(e)
    }, r.prototype.slideHandler = function(e, t, i) {
        var o, n, s, r = this;
        t = t || !1, !0 === r.animating && !0 === r.options.waitForAnimate || !0 === r.options.fade && r.currentSlide === e || (!1 === t && r.asNavFor(e), o = e, t = r.getLeft(o), s = r.getLeft(r.currentSlide), r.currentLeft = null === r.swipeLeft ? s : r.swipeLeft, !1 === r.options.infinite && !1 === r.options.centerMode && (e < 0 || e > r.getDotCount() * r.options.slidesToScroll) || !1 === r.options.infinite && !0 === r.options.centerMode && (e < 0 || e > r.slideCount - r.options.slidesToScroll) ? !1 === r.options.fade && (o = r.currentSlide, !0 !== i ? r.animateSlide(s, function() {
            r.postSlide(o)
        }) : r.postSlide(o)) : (r.options.autoplay && clearInterval(r.autoPlayTimer), n = o < 0 ? r.slideCount % r.options.slidesToScroll != 0 ? r.slideCount - r.slideCount % r.options.slidesToScroll : r.slideCount + o : o >= r.slideCount ? r.slideCount % r.options.slidesToScroll != 0 ? 0 : o - r.slideCount : o, r.animating = !0, r.$slider.trigger("beforeChange", [r, r.currentSlide, n]), e = r.currentSlide, r.currentSlide = n, r.setSlideClasses(r.currentSlide), r.options.asNavFor && (s = (s = r.getNavTarget()).slick("getSlick")).slideCount <= s.options.slidesToShow && s.setSlideClasses(r.currentSlide), r.updateDots(), r.updateArrows(), !0 === r.options.fade ? (!0 !== i ? (r.fadeSlideOut(e), r.fadeSlide(n, function() {
            r.postSlide(n)
        })) : r.postSlide(n), r.animateHeight()) : !0 !== i ? r.animateSlide(t, function() {
            r.postSlide(n)
        }) : r.postSlide(n)))
    }, r.prototype.startLoad = function() {
        var e = this;
        !0 === e.options.arrows && e.slideCount > e.options.slidesToShow && (e.$prevArrow.hide(), e.$nextArrow.hide()), !0 === e.options.dots && e.slideCount > e.options.slidesToShow && e.$dots.hide(), e.$slider.addClass("slick-loading")
    }, r.prototype.swipeDirection = function() {
        var e = this,
            t = e.touchObject.startX - e.touchObject.curX,
            i = e.touchObject.startY - e.touchObject.curY,
            i = Math.atan2(i, t);
        return (t = (t = Math.round(180 * i / Math.PI)) < 0 ? 360 - Math.abs(t) : t) <= 45 && 0 <= t || t <= 360 && 315 <= t ? !1 === e.options.rtl ? "left" : "right" : 135 <= t && t <= 225 ? !1 === e.options.rtl ? "right" : "left" : !0 === e.options.verticalSwiping ? 35 <= t && t <= 135 ? "down" : "up" : "vertical"
    }, r.prototype.swipeEnd = function(e) {
        var t, i, o = this;
        if (o.dragging = !1, o.swiping = !1, o.scrolling) return o.scrolling = !1;
        if (o.interrupted = !1, o.shouldClick = !(10 < o.touchObject.swipeLength), void 0 === o.touchObject.curX) return !1;
        if (!0 === o.touchObject.edgeHit && o.$slider.trigger("edge", [o, o.swipeDirection()]), o.touchObject.swipeLength >= o.touchObject.minSwipe) {
            switch (i = o.swipeDirection()) {
                case "left":
                case "down":
                    t = o.options.swipeToSlide ? o.checkNavigable(o.currentSlide + o.getSlideCount()) : o.currentSlide + o.getSlideCount(), o.currentDirection = 0;
                    break;
                case "right":
                case "up":
                    t = o.options.swipeToSlide ? o.checkNavigable(o.currentSlide - o.getSlideCount()) : o.currentSlide - o.getSlideCount(), o.currentDirection = 1
            }
            "vertical" != i && (o.slideHandler(t), o.touchObject = {}, o.$slider.trigger("swipe", [o, i]))
        } else o.touchObject.startX !== o.touchObject.curX && (o.slideHandler(o.currentSlide), o.touchObject = {})
    }, r.prototype.swipeHandler = function(e) {
        var t = this;
        if (!(!1 === t.options.swipe || "ontouchend" in document && !1 === t.options.swipe || !1 === t.options.draggable && -1 !== e.type.indexOf("mouse"))) switch (t.touchObject.fingerCount = e.originalEvent && void 0 !== e.originalEvent.touches ? e.originalEvent.touches.length : 1, t.touchObject.minSwipe = t.listWidth / t.options.touchThreshold, !0 === t.options.verticalSwiping && (t.touchObject.minSwipe = t.listHeight / t.options.touchThreshold), e.data.action) {
            case "start":
                t.swipeStart(e);
                break;
            case "move":
                t.swipeMove(e);
                break;
            case "end":
                t.swipeEnd(e)
        }
    }, r.prototype.swipeMove = function(e) {
        var t, i, o = this,
            n = void 0 !== e.originalEvent ? e.originalEvent.touches : null;
        return !(!o.dragging || o.scrolling || n && 1 !== n.length) && (t = o.getLeft(o.currentSlide), o.touchObject.curX = void 0 !== n ? n[0].pageX : e.clientX, o.touchObject.curY = void 0 !== n ? n[0].pageY : e.clientY, o.touchObject.swipeLength = Math.round(Math.sqrt(Math.pow(o.touchObject.curX - o.touchObject.startX, 2))), n = Math.round(Math.sqrt(Math.pow(o.touchObject.curY - o.touchObject.startY, 2))), !o.options.verticalSwiping && !o.swiping && 4 < n ? !(o.scrolling = !0) : (!0 === o.options.verticalSwiping && (o.touchObject.swipeLength = n), n = o.swipeDirection(), void 0 !== e.originalEvent && 4 < o.touchObject.swipeLength && (o.swiping = !0, e.preventDefault()), e = (!1 === o.options.rtl ? 1 : -1) * (o.touchObject.curX > o.touchObject.startX ? 1 : -1), !0 === o.options.verticalSwiping && (e = o.touchObject.curY > o.touchObject.startY ? 1 : -1), i = o.touchObject.swipeLength, (o.touchObject.edgeHit = !1) === o.options.infinite && (0 === o.currentSlide && "right" === n || o.currentSlide >= o.getDotCount() && "left" === n) && (i = o.touchObject.swipeLength * o.options.edgeFriction, o.touchObject.edgeHit = !0), !1 === o.options.vertical ? o.swipeLeft = t + i * e : o.swipeLeft = t + i * (o.$list.height() / o.listWidth) * e, !0 === o.options.verticalSwiping && (o.swipeLeft = t + i * e), !0 !== o.options.fade && !1 !== o.options.touchMove && (!0 === o.animating ? (o.swipeLeft = null, !1) : void o.setCSS(o.swipeLeft))))
    }, r.prototype.swipeStart = function(e) {
        var t, i = this;
        if (i.interrupted = !0, 1 !== i.touchObject.fingerCount || i.slideCount <= i.options.slidesToShow) return !(i.touchObject = {});
        void 0 !== e.originalEvent && void 0 !== e.originalEvent.touches && (t = e.originalEvent.touches[0]), i.touchObject.startX = i.touchObject.curX = void 0 !== t ? t.pageX : e.clientX, i.touchObject.startY = i.touchObject.curY = void 0 !== t ? t.pageY : e.clientY, i.dragging = !0
    }, r.prototype.unfilterSlides = r.prototype.slickUnfilter = function() {
        var e = this;
        null !== e.$slidesCache && (e.unload(), e.$slideTrack.children(this.options.slide).detach(), e.$slidesCache.appendTo(e.$slideTrack), e.reinit())
    }, r.prototype.unload = function() {
        var e = this;
        c(".slick-cloned", e.$slider).remove(), e.$dots && e.$dots.remove(), e.$prevArrow && e.htmlExpr.test(e.options.prevArrow) && e.$prevArrow.remove(), e.$nextArrow && e.htmlExpr.test(e.options.nextArrow) && e.$nextArrow.remove(), e.$slides.removeClass("slick-slide slick-active slick-visible slick-current").attr("aria-hidden", "true").css("width", "")
    }, r.prototype.unslick = function(e) {
        this.$slider.trigger("unslick", [this, e]), this.destroy()
    }, r.prototype.updateArrows = function() {
        var e = this;
        Math.floor(e.options.slidesToShow / 2), !0 === e.options.arrows && e.slideCount > e.options.slidesToShow && !e.options.infinite && (e.$prevArrow.removeClass("slick-disabled").attr("aria-disabled", "false"), e.$nextArrow.removeClass("slick-disabled").attr("aria-disabled", "false"), 0 === e.currentSlide ? (e.$prevArrow.addClass("slick-disabled").attr("aria-disabled", "true"), e.$nextArrow.removeClass("slick-disabled").attr("aria-disabled", "false")) : (e.currentSlide >= e.slideCount - e.options.slidesToShow && !1 === e.options.centerMode || e.currentSlide >= e.slideCount - 1 && !0 === e.options.centerMode) && (e.$nextArrow.addClass("slick-disabled").attr("aria-disabled", "true"), e.$prevArrow.removeClass("slick-disabled").attr("aria-disabled", "false")))
    }, r.prototype.updateDots = function() {
        var e = this;
        null !== e.$dots && (e.$dots.find("li").removeClass("slick-active").end(), e.$dots.find("li").eq(Math.floor(e.currentSlide / e.options.slidesToScroll)).addClass("slick-active"))
    }, r.prototype.visibility = function() {
        this.options.autoplay && (document[this.hidden] ? this.interrupted = !0 : this.interrupted = !1)
    }, c.fn.slick = function() {
        for (var e, t = this, i = arguments[0], o = Array.prototype.slice.call(arguments, 1), n = t.length, s = 0; s < n; s++)
            if ("object" == typeof i || void 0 === i ? t[s].slick = new r(t[s], i) : e = t[s].slick[i].apply(t[s].slick, o), void 0 !== e) return e;
        return t
    }
}), $(document).ready(function() {
    $("body").on("click", ".dvLightBox-close-btn", function(e) {
        $.fancybox.close(!1)
    }), $("body").on("click", ".lightboxNavPrev", function(e) {
        $.fancybox.prev()
    }), $("body").on("click", ".lightboxNavNext", function(e) {
        $.fancybox.next()
    }), $(".imgZoomLarge").fancybox({
        padding: 0,
        autoCenter: !0,
        tpl: {
            closeBtn: "",
            next: "",
            prev: ""
        },
        autoSize: !0,
        openEffect: "fade",
        closeEffect: "fade",
        afterShow: function() {
            0 == $(".dvLightBox-close-btn").length && (1 < this.group.length ? $(".fancybox-overlay").prepend('<div style="z-index:9999" class="dvLightBox-close-btn"><img src="/assets/img/icon-close.png" /></div> <div style="z-index:9999" class="dvLightBox-nav-btn lightboxNavPrev"><img src="/assets/img/icon-left-arrow.png" /></div><div style="z-index:9999" class="dvLightBox-nav-btn lightboxNavNext"><img src="/assets/img/icon-right-arrow.png" /></div>') : $(".fancybox-overlay").prepend('<div style="z-index:9999" class="dvLightBox-close-btn"><img src="/assets/img/icon-close.png" /></div>'), $(".fancybox-overlay").css("overflow-x", "hidden"))
        },
        helpers: {
            title: {
                type: "inside"
            },
            overlay: {
                locked: !0,
                css: {
                    background: "rgba(0, 0, 0, 0.8)"
                }
            }
        }
    }), $("#frmContactUsPage").submit(function(e) {
        return form = $(this), e.isDefaultPrevented() || (e.preventDefault(), e.isDefaultPrevented(), $(".btnContactUsPageSubmit").prop("disabled", !0), $(".btnContactUsPageSubmit").html("please wait..."), $.ajax({
            type: form.attr("method"),
            url: form.attr("action"),
            data: form.serialize()
        }).done(function(e) {
            $(".btnContactUsPageSubmit").prop("disabled", !1), $(".btnContactUsPageSubmit").html("Send"), $(".btnResetContactForm").click(), setTimeout(function() {
                sweetAlert({
                    title: "Message Sent!",
                    text: e,
                    html: !0,
                    type: "success",
                    confirmButtonText: "OK",
                    confirmButtonColor: "#0072ae"
                })
            }, 100);
            var t = $("#widgetCaptchaContainer").attr("data-widget-id");
            grecaptcha.reset(t)
        }).fail(function(e) {
            var t = $("#widgetCaptchaContainer").attr("data-widget-id");
            grecaptcha.reset(t);
            var i = JSON.parse(e.responseText).messages,
                o = "";
            Object.keys(i).forEach(e => {
                o = o + "<li>" + i[e] + "</li>"
            }), sweetAlert({
                title: "Sorry!",
                text: '<div style="display: inline-block"><div class="text-left"><small><strong>Please check the following:</strong></small><div class="errorData"><ul>' + o + "</ul></div></div></div>",
                html: !0,
                type: "error",
                confirmButtonText: "OK",
                confirmButtonColor: "#0072ae"
            }), $(".btnContactUsPageSubmit").prop("disabled", !1), $(".btnContactUsPageSubmit").html("Send")
        })), !1
    }), $("#frmShippingMark").submit(function(e) {
        return $(".historyContainer").hide(), $(".imgLoader").show(), $(".shippingMark").removeClass("is-valid"), $(".historyContainer").html(""), form = $(this), e.isDefaultPrevented() || (e.preventDefault(), e.isDefaultPrevented(), $(".btnSearchShippingMark").prop("disabled", !0), $(".btnSearchShippingMark").html("please wait..."), $.ajax({
            type: form.attr("method"),
            url: form.attr("action"),
            data: form.serialize()
        }).done(function(e) {
            $(".btnSearchShippingMark").prop("disabled", !1), $(".btnSearchShippingMark").html("Search");
            for (var t = $("#widgetCaptchaContainer").attr("data-widget-id"), i = (grecaptcha.reset(t), $(".shippingMark").addClass("is-valid"), e), o = "", n = "", s = "", r = "", a = 0; a < i.length; a++) r = n = s = "", i[a].description && (n = "<span>" + i[a].description + " <br></span>"), i[a].title && (s = "<strong>" + i[a].title + "</strong>"), "0" == i[a].status ? r = "pending-color" : "1" == i[a].status ? r = "agent-viewed-color" : "2" == i[a].status ? r = "processing-color" : "3" == i[a].status ? r = "ongoing-color" : "4" == i[a].status ? r = "for-invoice-color" : "5" == i[a].status ? r = "invoice-created-color" : "6" == i[a].status ? r = "invoice-sent-color" : "7" == i[a].status ? r = "for-delivery-color" : "8" == i[a].status || "9" == i[a].status ? r = "success" : "10" == i[a].status && (r = "red"), o = '<div class="v-timeline v-timeline--align-top v-timeline--dense theme--light">', o = (o = (o = (o = (o += '  <div class="v-timeline-item mt-n6 theme--light" dense="">') + '    <div class="v-timeline-item__body">      ' + s) + '      <div class="text-caption">        ' + n) + "        " + i[a].created_at + '      </div>    </div>    <div class="v-timeline-item__divider">') + '      <div class="v-timeline-item__dot v-timeline-item__dot--small">        <div class="v-timeline-item__inner-dot ' + r + '"></div>      </div>    </div>  </div></div>', $(".historyContainer").append(o);
            $(".imgLoader").fadeOut(1e3, function() {
                $(".historyContainer").fadeIn("slow")
            })
        }).fail(function(e) {
            var t = $("#widgetCaptchaContainer").attr("data-widget-id");
            grecaptcha.reset(t);
            var i = JSON.parse(e.responseText).messages,
                o = "";
            Object.keys(i).forEach(e => {
                o = o + "<li>" + i[e] + "</li>"
            }), sweetAlert({
                title: "Sorry!",
                text: '<div style="display: inline-block"><div class="text-left"><small><strong>Please check the following:</strong></small><div class="errorData"><ul>' + o + "</ul></div></div></div>",
                html: !0,
                type: "error",
                confirmButtonText: "OK",
                confirmButtonColor: "#0072ae"
            }), $(".btnSearchShippingMark").prop("disabled", !1), $(".btnSearchShippingMark").html("Search")
        })), !1
    });
    var e = new Blazy({
        success: function(e) {
            $(e).parent()
        }
    });

    function t() {
        setTimeout(function() {
            var e = 0;
            $(".card-body .card-text").each(function() {
                e < $(this).innerHeight() && (e = $(this).innerHeight())
            }), $(".card-body .card-text").each(function() {
                $(this).css("height", e + "px")
            })
        }, 1e3)
    }
    $(window).ready(function() {
        $(".main-loader-wrapper").fadeOut(100, function() {
            e.revalidate()
        }), $(".dvLoader").fadeOut(1e3, function() {})
    }), $(window).resize(t).ready(t), $(".testimonial-items").slick({
        infinite: !0,
        slidesToShow: 1,
        slidesToScroll: 1,
        dots: !1,
        autoplay: !0,
        arrows: !1,
        autoplaySpeed: 5e3,
        focusOnSelect: !0,
        variableWidth: !0
    }), $(".testimonialNext").click(function() {
        $(".testimonial-items").slick("slickNext")
    }), $(".testimonialPrev").click(function() {
        $(".testimonial-items").slick("slickPrev")
    }), $(".heroSliderContainer").slick({
        infinite: !0,
        slidesToShow: 1,
        slidesToScroll: 1,
        dots: !1,
        autoplay: !0,
        arrows: !1,
        focusOnSelect: !0,
        speed: 1e3,
        autoplaySpeed: 1e4
    }), $(".heroSliderContainer").on("afterChange", function(e, t, i) {
        var o = $(t.$slides[i]).attr("data-page");
        (1 == (o = parseInt(o)) ? $(".sliderDetails" + (o + 1)) : $(".sliderDetails" + (o - 1))).fadeOut(500, function() {
            $(".sliderDetails" + o).fadeIn(1e3, function() {})
        })
    });
    var i = $(".navContainer").innerHeight(),
        o = (jQuery(window).scroll(function() {
            (jQuery(window).scrollTop() > i ? ($(".navContainer .navbar").removeClass("navNormalbg"), $(".navContainer .navbar").addClass("navbg"), $(".imgLogo").hide(), $(".imgLogoWhite")) : ($(".navContainer .navbar").removeClass("navbg"), $(".navContainer .navbar").addClass("navNormalbg"), $(".imgLogoWhite").hide(), $(".imgLogo"))).show()
        }), $(window).innerHeight());
    $("dvMenuAL").css("height", o + "px"), $(".mnuMain").click(function() {
        $(".dvMenuAL").fadeIn(300, function() {})
    }), $(".mnuClose").click(function() {
        $(".dvMenuAL").fadeOut(300, function() {})
    })
});