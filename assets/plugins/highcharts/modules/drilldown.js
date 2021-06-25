/*
 Highcharts JS v8.0.0 (2019-12-10)

 Highcharts Drilldown module

 Author: Torstein Honsi
 License: www.highcharts.com/license

*/
(function (e) { "object" === typeof module && module.exports ? (e["default"] = e, module.exports = e) : "function" === typeof define && define.amd ? define("highcharts/modules/drilldown", ["highcharts"], function (n) { e(n); e.Highcharts = n; return e }) : e("undefined" !== typeof Highcharts ? Highcharts : void 0) })(function (e) {
  function n(h, e, n, q) { h.hasOwnProperty(e) || (h[e] = q.apply(null, n)) } e = e ? e._modules : {}; n(e, "modules/drilldown.src.js", [e["parts/Globals.js"], e["parts/Utilities.js"]], function (h, e) {
    var n = e.animObject, q = e.extend, x = e.objectEach,
    u = e.pick, D = e.syncTimeout, r = h.addEvent, y = h.noop, z = h.color; e = h.defaultOptions; var E = h.format, p = h.Chart, t = h.seriesTypes, A = t.pie; t = t.column; var B = h.Tick, v = h.fireEvent, C = 1; q(e.lang, { drillUpText: "\u25c1 Back to {series.name}" }); e.drilldown = {
      activeAxisLabelStyle: { cursor: "pointer", color: "#003399", fontWeight: "bold", textDecoration: "underline" }, activeDataLabelStyle: { cursor: "pointer", color: "#003399", fontWeight: "bold", textDecoration: "underline" }, animation: { duration: 500 }, drillUpButton: {
        position: {
          align: "right",
          x: -10, y: 10
        }
      }
    }; h.SVGRenderer.prototype.Element.prototype.fadeIn = function (a) { this.attr({ opacity: .1, visibility: "inherit" }).animate({ opacity: u(this.newOpacity, 1) }, a || { duration: 250 }) }; p.prototype.addSeriesAsDrilldown = function (a, b) { this.addSingleSeriesAsDrilldown(a, b); this.applyDrilldown() }; p.prototype.addSingleSeriesAsDrilldown = function (a, b) {
      var c = a.series, d = c.xAxis, f = c.yAxis, g = [], l = [], k; var m = this.styledMode ? { colorIndex: u(a.colorIndex, c.colorIndex) } : { color: a.color || c.color }; this.drilldownLevels || (this.drilldownLevels =
        []); var e = c.options._levelNumber || 0; (k = this.drilldownLevels[this.drilldownLevels.length - 1]) && k.levelNumber !== e && (k = void 0); b = q(q({ _ddSeriesId: C++ }, m), b); var n = c.points.indexOf(a); c.chart.series.forEach(function (a) {
        a.xAxis !== d || a.isDrilling || (a.options._ddSeriesId = a.options._ddSeriesId || C++ , a.options._colorIndex = a.userOptions._colorIndex, a.options._levelNumber = a.options._levelNumber || e, k ? (g = k.levelSeries, l = k.levelSeriesOptions) : (g.push(a), a.purgedOptions = h.merge({
          _ddSeriesId: a.options._ddSeriesId,
          _levelNumber: a.options._levelNumber, selected: a.options.selected
        }, a.userOptions), l.push(a.purgedOptions)))
        }); a = q({ levelNumber: e, seriesOptions: c.options, seriesPurgedOptions: c.purgedOptions, levelSeriesOptions: l, levelSeries: g, shapeArgs: a.shapeArgs, bBox: a.graphic ? a.graphic.getBBox() : {}, color: a.isNull ? (new h.Color(z)).setOpacity(0).get() : z, lowerSeriesOptions: b, pointOptions: c.options.data[n], pointIndex: n, oldExtremes: { xMin: d && d.userMin, xMax: d && d.userMax, yMin: f && f.userMin, yMax: f && f.userMax }, resetZoomButton: this.resetZoomButton },
          m); this.drilldownLevels.push(a); d && d.names && (d.names.length = 0); b = a.lowerSeries = this.addSeries(b, !1); b.options._levelNumber = e + 1; d && (d.oldPos = d.pos, d.userMin = d.userMax = null, f.userMin = f.userMax = null); c.type === b.type && (b.animate = b.animateDrilldown || y, b.options.animation = !0)
    }; p.prototype.applyDrilldown = function () {
      var a = this.drilldownLevels; if (a && 0 < a.length) {
        var b = a[a.length - 1].levelNumber; this.drilldownLevels.forEach(function (a) {
        a.levelNumber === b && a.levelSeries.forEach(function (a) {
        a.options && a.options._levelNumber ===
          b && a.remove(!1)
        })
        })
      } this.resetZoomButton && (this.resetZoomButton.hide(), delete this.resetZoomButton); this.pointer.reset(); this.redraw(); this.showDrillUpButton(); v(this, "afterDrilldown")
    }; p.prototype.getDrilldownBackText = function () { var a = this.drilldownLevels; if (a && 0 < a.length) return a = a[a.length - 1], a.series = a.seriesOptions, E(this.options.lang.drillUpText, a) }; p.prototype.showDrillUpButton = function () {
      var a = this, b = this.getDrilldownBackText(), c = a.options.drilldown.drillUpButton, d; if (this.drillUpButton) this.drillUpButton.attr({ text: b }).align();
      else { var f = (d = c.theme) && d.states; this.drillUpButton = this.renderer.button(b, null, null, function () { a.drillUp() }, d, f && f.hover, f && f.select).addClass("highcharts-drillup-button").attr({ align: c.position.align, zIndex: 7 }).add().align(c.position, !1, c.relativeTo || "plotBox") }
    }; p.prototype.drillUp = function () {
      if (this.drilldownLevels && 0 !== this.drilldownLevels.length) {
        for (var a = this, b = a.drilldownLevels, c = b[b.length - 1].levelNumber, d = b.length, f = a.series, g, l, k, m, e = function (b) {
          f.forEach(function (a) {
            a.options._ddSeriesId ===
            b._ddSeriesId && (c = a)
          }); var c = c || a.addSeries(b, !1); c.type === k.type && c.animateDrillupTo && (c.animate = c.animateDrillupTo); b === l.seriesPurgedOptions && (m = c)
        }; d--;)if (l = b[d], l.levelNumber === c) {
          b.pop(); k = l.lowerSeries; if (!k.chart) for (g = f.length; g--;)if (f[g].options.id === l.lowerSeriesOptions.id && f[g].options._levelNumber === c + 1) { k = f[g]; break } k.xData = []; l.levelSeriesOptions.forEach(e); v(a, "drillup", { seriesOptions: l.seriesOptions }); m.type === k.type && (m.drilldownLevel = l, m.options.animation = a.options.drilldown.animation,
            k.animateDrillupFrom && k.chart && k.animateDrillupFrom(l)); m.options._levelNumber = c; k.remove(!1); m.xAxis && (g = l.oldExtremes, m.xAxis.setExtremes(g.xMin, g.xMax, !1), m.yAxis.setExtremes(g.yMin, g.yMax, !1)); l.resetZoomButton && (a.resetZoomButton = l.resetZoomButton, a.resetZoomButton.show())
        } this.redraw(); 0 === this.drilldownLevels.length ? this.drillUpButton = this.drillUpButton.destroy() : this.drillUpButton.attr({ text: this.getDrilldownBackText() }).align(); this.ddDupes.length = []; v(a, "drillupall")
      }
    }; p.prototype.callbacks.push(function () {
      var a =
        this; a.drilldown = { update: function (b, c) { h.merge(!0, a.options.drilldown, b); u(c, !0) && a.redraw() } }
    }); r(p, "beforeShowResetZoom", function () { if (this.drillUpButton) return !1 }); r(p, "render", function () {
      (this.xAxis || []).forEach(function (a) {
      a.ddPoints = {}; a.series.forEach(function (b) {
        var c, d = b.xData || [], f = b.points; for (c = 0; c < d.length; c++) {
          var g = b.options.data[c]; "number" !== typeof g && (g = b.pointClass.prototype.optionsToObject.call({ series: b }, g), g.drilldown && (a.ddPoints[d[c]] || (a.ddPoints[d[c]] = []), a.ddPoints[d[c]].push(f ?
            f[c] : !0)))
        }
      }); x(a.ticks, B.prototype.drillable)
      })
    }); t.prototype.animateDrillupTo = function (a) {
      if (!a) {
        var b = this, c = b.drilldownLevel; this.points.forEach(function (a) { var b = a.dataLabel; a.graphic && a.graphic.hide(); b && (b.hidden = "hidden" === b.attr("visibility"), b.hidden || (b.hide(), a.connector && a.connector.hide())) }); D(function () {
        b.points && b.points.forEach(function (a, b) {
          b = b === (c && c.pointIndex) ? "show" : "fadeIn"; var d = "show" === b ? !0 : void 0, f = a.dataLabel; if (a.graphic) a.graphic[b](d); f && !f.hidden && (f.fadeIn(), a.connector &&
            a.connector.fadeIn())
        })
        }, Math.max(this.chart.options.drilldown.animation.duration - 50, 0)); this.animate = y
      }
    }; t.prototype.animateDrilldown = function (a) {
      var b = this, c = this.chart, d = c.drilldownLevels, f, g = n(c.options.drilldown.animation), e = this.xAxis, k = c.styledMode; a || (d.forEach(function (a) { b.options._ddSeriesId === a.lowerSeriesOptions._ddSeriesId && (f = a.shapeArgs, k || (f.fill = a.color)) }), f.x += u(e.oldPos, e.pos) - e.pos, this.points.forEach(function (a) {
        var c = a.shapeArgs; k || (c.fill = a.color); a.graphic && a.graphic.attr(f).animate(q(a.shapeArgs,
          { fill: a.color || b.color }), g); a.dataLabel && a.dataLabel.fadeIn(g)
      }), this.animate = null)
    }; t.prototype.animateDrillupFrom = function (a) {
      var b = n(this.chart.options.drilldown.animation), c = this.group, d = c !== this.chart.columnGroup, f = this; f.trackerGroups.forEach(function (a) { if (f[a]) f[a].on("mouseover") }); d && delete this.group; this.points.forEach(function (g) {
        var e = g.graphic, k = a.shapeArgs, m = function () { e.destroy(); c && d && (c = c.destroy()) }; e && (delete g.graphic, f.chart.styledMode || (k.fill = a.color), b.duration ? e.animate(k,
          h.merge(b, { complete: m })) : (e.attr(k), m()))
      })
    }; A && q(A.prototype, {
      animateDrillupTo: t.prototype.animateDrillupTo, animateDrillupFrom: t.prototype.animateDrillupFrom, animateDrilldown: function (a) {
        var b = this.chart.drilldownLevels[this.chart.drilldownLevels.length - 1], c = this.chart.options.drilldown.animation, d = b.shapeArgs, f = d.start, e = (d.end - f) / this.points.length, l = this.chart.styledMode; a || (this.points.forEach(function (a, g) {
          var k = a.shapeArgs; l || (d.fill = b.color, k.fill = a.color); if (a.graphic) a.graphic.attr(h.merge(d,
            { start: f + g * e, end: f + (g + 1) * e }))[c ? "animate" : "attr"](k, c)
        }), this.animate = null)
      }
    }); h.Point.prototype.doDrilldown = function (a, b, c) {
      var d = this.series.chart, f = d.options.drilldown, e = (f.series || []).length; d.ddDupes || (d.ddDupes = []); for (; e-- && !h;)if (f.series[e].id === this.drilldown && -1 === d.ddDupes.indexOf(this.drilldown)) { var h = f.series[e]; d.ddDupes.push(this.drilldown) } v(d, "drilldown", { point: this, seriesOptions: h, category: b, originalEvent: c, points: "undefined" !== typeof b && this.series.xAxis.getDDPoints(b).slice(0) },
        function (b) { var c = b.point.series && b.point.series.chart, d = b.seriesOptions; c && d && (a ? c.addSingleSeriesAsDrilldown(b.point, d) : c.addSeriesAsDrilldown(b.point, d)) })
    }; h.Axis.prototype.drilldownCategory = function (a, b) { x(this.getDDPoints(a), function (c) { c && c.series && c.series.visible && c.doDrilldown && c.doDrilldown(!0, a, b) }); this.chart.applyDrilldown() }; h.Axis.prototype.getDDPoints = function (a) { return this.ddPoints && this.ddPoints[a] }; B.prototype.drillable = function () {
      var a = this.pos, b = this.label, c = this.axis, d = "xAxis" ===
        c.coll && c.getDDPoints, f = d && c.getDDPoints(a), e = c.chart.styledMode; d && (b && f && f.length ? (b.drillable = !0, b.basicStyles || e || (b.basicStyles = h.merge(b.styles)), b.addClass("highcharts-drilldown-axis-label"), b.removeOnDrillableClick = r(b.element, "click", function (b) { c.drilldownCategory(a, b) }), e || b.css(c.chart.options.drilldown.activeAxisLabelStyle)) : b && b.removeOnDrillableClick && (e || (b.styles = {}, b.css(b.basicStyles)), b.removeOnDrillableClick(), b.removeClass("highcharts-drilldown-axis-label")))
    }; r(h.Point, "afterInit",
      function () { var a = this, b = a.series; a.drilldown && r(a, "click", function (c) { b.xAxis && !1 === b.chart.options.drilldown.allowPointDrilldown ? b.xAxis.drilldownCategory(a.x, c) : a.doDrilldown(void 0, void 0, c) }); return a }); r(h.Series, "afterDrawDataLabels", function () {
        var a = this.chart.options.drilldown.activeDataLabelStyle, b = this.chart.renderer, c = this.chart.styledMode; this.points.forEach(function (d) {
          var e = d.options.dataLabels, g = u(d.dlOptions, e && e.style, {}); d.drilldown && d.dataLabel && ("contrast" !== a.color || c || (g.color =
            b.getContrast(d.color || this.color)), e && e.color && (g.color = e.color), d.dataLabel.addClass("highcharts-drilldown-data-label"), c || d.dataLabel.css(a).css(g))
        }, this)
      }); var w = function (a, b, c, d) { a[c ? "addClass" : "removeClass"]("highcharts-drilldown-point"); d || a.css({ cursor: b }) }; r(h.Series, "afterDrawTracker", function () { var a = this.chart.styledMode; this.points.forEach(function (b) { b.drilldown && b.graphic && w(b.graphic, "pointer", !0, a) }) }); r(h.Point, "afterSetState", function () {
        var a = this.series.chart.styledMode; this.drilldown &&
          this.series.halo && "hover" === this.state ? w(this.series.halo, "pointer", !0, a) : this.series.halo && w(this.series.halo, "auto", !1, a)
      })
  }); n(e, "masters/modules/drilldown.src.js", [], function () { })
});
//# sourceMappingURL=drilldown.js.map