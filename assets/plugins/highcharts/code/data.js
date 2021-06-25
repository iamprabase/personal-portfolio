/*
 Highcharts JS v8.0.0 (2019-12-10)

 Data module

 (c) 2012-2019 Torstein Honsi

 License: www.highcharts.com/license
*/
(function (d) { "object" === typeof module && module.exports ? (d["default"] = d, module.exports = d) : "function" === typeof define && define.amd ? define("highcharts/modules/data", ["highcharts"], function (v) { d(v); d.Highcharts = v; return d }) : d("undefined" !== typeof Highcharts ? Highcharts : void 0) })(function (d) {
  function v(z, d, v, y) { z.hasOwnProperty(d) || (z[d] = y.apply(null, v)) } d = d ? d._modules : {}; v(d, "mixins/ajax.js", [d["parts/Globals.js"], d["parts/Utilities.js"]], function (d, x) {
    var z = x.objectEach; d.ajax = function (y) {
      var n = d.merge(!0,
        { url: !1, type: "get", dataType: "json", success: !1, error: !1, data: !1, headers: {} }, y); y = { json: "application/json", xml: "application/xml", text: "text/plain", octet: "application/octet-stream" }; var q = new XMLHttpRequest; if (!n.url) return !1; q.open(n.type.toUpperCase(), n.url, !0); n.headers["Content-Type"] || q.setRequestHeader("Content-Type", y[n.dataType] || y.text); z(n.headers, function (d, n) { q.setRequestHeader(n, d) }); q.onreadystatechange = function () {
          if (4 === q.readyState) {
            if (200 === q.status) {
              var d = q.responseText; if ("json" ===
                n.dataType) try { d = JSON.parse(d) } catch (D) { n.error && n.error(q, D); return } return n.success && n.success(d)
            } n.error && n.error(q, q.responseText)
          }
        }; try { n.data = JSON.stringify(n.data) } catch (J) { } q.send(n.data || !0)
    }; d.getJSON = function (z, n) { d.ajax({ url: z, success: n, dataType: "json", headers: { "Content-Type": "text/plain" } }) }
  }); v(d, "modules/data.src.js", [d["parts/Globals.js"], d["parts/Utilities.js"]], function (d, x) {
    var v = x.defined, y = x.extend, n = x.isNumber, q = x.objectEach, z = x.pick, D = x.splat; x = d.addEvent; var G = d.Chart, H = d.win.document,
      B = d.merge, I = d.fireEvent, C = function (a, c, b) { this.init(a, c, b) }; y(C.prototype, {
        init: function (a, c, b) {
          var e = a.decimalPoint; c && (this.chartOptions = c); b && (this.chart = b); "." !== e && "," !== e && (e = void 0); this.options = a; this.columns = a.columns || this.rowsToColumns(a.rows) || []; this.firstRowAsNames = z(a.firstRowAsNames, this.firstRowAsNames, !0); this.decimalRegex = e && new RegExp("^(-?[0-9]+)" + e + "([0-9]+)$"); this.rawColumns = []; if (this.columns.length) { this.dataFound(); var f = !0 } this.hasURLOption(a) && (clearTimeout(this.liveDataTimeout),
            f = !1); f || (f = this.fetchLiveData()); f || (f = !!this.parseCSV().length); f || (f = !!this.parseTable().length); f || (f = this.parseGoogleSpreadsheet()); !f && a.afterComplete && a.afterComplete()
        }, hasURLOption: function (a) { return !(!a || !(a.rowsURL || a.csvURL || a.columnsURL)) }, getColumnDistribution: function () {
          var a = this.chartOptions, c = this.options, b = [], e = function (a) { return (d.seriesTypes[a || "line"].prototype.pointArrayMap || [0]).length }, f = a && a.chart && a.chart.type, g = [], m = [], h = 0; c = c && c.seriesMapping || a && a.series && a.series.map(function () { return { x: 0 } }) ||
            []; var k; (a && a.series || []).forEach(function (a) { g.push(e(a.type || f)) }); c.forEach(function (a) { b.push(a.x || 0) }); 0 === b.length && b.push(0); c.forEach(function (c) { var b = new A, t = g[h] || e(f), r = (a && a.series || [])[h] || {}, u = d.seriesTypes[r.type || f || "line"].prototype.pointArrayMap, n = u || ["y"]; (v(c.x) || r.isCartesian || !u) && b.addColumnReader(c.x, "x"); q(c, function (a, c) { "x" !== c && b.addColumnReader(a, c) }); for (k = 0; k < t; k++)b.hasReader(n[k]) || b.addColumnReader(void 0, n[k]); m.push(b); h++ }); c = d.seriesTypes[f || "line"].prototype.pointArrayMap;
          "undefined" === typeof c && (c = ["y"]); this.valueCount = { global: e(f), xColumns: b, individual: g, seriesBuilders: m, globalPointArrayMap: c }
        }, dataFound: function () { this.options.switchRowsAndColumns && (this.columns = this.rowsToColumns(this.columns)); this.getColumnDistribution(); this.parseTypes(); !1 !== this.parsed() && this.complete() }, parseCSV: function (a) {
          function c(a, c, b, e) {
            function f(c) { h = a[c]; p = a[c - 1]; F = a[c + 1] } function g(a) { t.length < r + 1 && t.push([a]); t[r][t[r].length - 1] !== a && t[r].push(a) } function d() {
            k > w || w > n ? (++w,
              m = "") : (!isNaN(parseFloat(m)) && isFinite(m) ? (m = parseFloat(m), g("number")) : isNaN(Date.parse(m)) ? g("string") : (m = m.replace(/\//g, "-"), g("date")), u.length < r + 1 && u.push([]), b || (u[r][c] = m), m = "", ++r, ++w)
            } var l = 0, h = "", p = "", F = "", m = "", w = 0, r = 0; if (a.trim().length && "#" !== a.trim()[0]) { for (; l < a.length; l++) { f(l); if ("#" === h) { d(); return } if ('"' === h) for (f(++l); l < a.length && ('"' !== h || '"' === p || '"' === F);) { if ('"' !== h || '"' === h && '"' !== p) m += h; f(++l) } else e && e[h] ? e[h](h, m) && d() : h === E ? d() : m += h } d() }
          } function b(a) {
            var c = 0, b = 0, e = !1;
            a.some(function (a, e) { var f = !1, g = ""; if (13 < e) return !0; for (var d = 0; d < a.length; d++) { e = a[d]; var h = a[d + 1]; var m = a[d - 1]; if ("#" === e) break; if ('"' === e) if (f) { if ('"' !== m && '"' !== h) { for (; " " === h && d < a.length;)h = a[++d]; "undefined" !== typeof r[h] && r[h]++; f = !1 } } else f = !0; else "undefined" !== typeof r[e] ? (g = g.trim(), isNaN(Date.parse(g)) ? !isNaN(g) && isFinite(g) || r[e]++ : r[e]++ , g = "") : g += e; "," === e && b++; "." === e && c++ } }); e = r[";"] > r[","] ? ";" : ","; g.decimalPoint || (g.decimalPoint = c > b ? "." : ",", f.decimalRegex = new RegExp("^(-?[0-9]+)" +
              g.decimalPoint + "([0-9]+)$")); return e
          } function e(a, c) {
            var b = [], e = 0, d = !1, h = [], m = [], l; if (!c || c > a.length) c = a.length; for (; e < c; e++)if ("undefined" !== typeof a[e] && a[e] && a[e].length) {
              var k = a[e].trim().replace(/\//g, " ").replace(/\-/g, " ").replace(/\./g, " ").split(" "); b = ["", "", ""]; for (l = 0; l < k.length; l++)l < b.length && (k[l] = parseInt(k[l], 10), k[l] && (m[l] = !m[l] || m[l] < k[l] ? k[l] : m[l], "undefined" !== typeof h[l] ? h[l] !== k[l] && (h[l] = !1) : h[l] = k[l], 31 < k[l] ? b[l] = 100 > k[l] ? "YY" : "YYYY" : 12 < k[l] && 31 >= k[l] ? (b[l] = "dd", d = !0) : b[l].length ||
                (b[l] = "mm")))
            } if (d) { for (l = 0; l < h.length; l++)!1 !== h[l] ? 12 < m[l] && "YY" !== b[l] && "YYYY" !== b[l] && (b[l] = "YY") : 12 < m[l] && "mm" === b[l] && (b[l] = "dd"); 3 === b.length && "dd" === b[1] && "dd" === b[2] && (b[2] = "YY"); a = b.join("/"); return (g.dateFormats || f.dateFormats)[a] ? a : (I("deduceDateFailed"), "YYYY/mm/dd") } return "YYYY/mm/dd"
          } var f = this, g = a || this.options, d = g.csv; a = "undefined" !== typeof g.startRow && g.startRow ? g.startRow : 0; var h = g.endRow || Number.MAX_VALUE, k = "undefined" !== typeof g.startColumn && g.startColumn ? g.startColumn : 0, n = g.endColumn ||
            Number.MAX_VALUE, p = 0, t = [], r = { ",": 0, ";": 0, "\t": 0 }; var u = this.columns = []; d && g.beforeParse && (d = g.beforeParse.call(this, d)); if (d) { d = d.replace(/\r\n/g, "\n").replace(/\r/g, "\n").split(g.lineDelimiter || "\n"); if (!a || 0 > a) a = 0; if (!h || h >= d.length) h = d.length - 1; if (g.itemDelimiter) var E = g.itemDelimiter; else E = null, E = b(d); var w = 0; for (p = a; p <= h; p++)"#" === d[p][0] ? w++ : c(d[p], p - a - w); g.columnTypes && 0 !== g.columnTypes.length || !t.length || !t[0].length || "date" !== t[0][1] || g.dateFormat || (g.dateFormat = e(u[0])); this.dataFound() } return u
        },
        parseTable: function () { var a = this.options, c = a.table, b = this.columns, e = a.startRow || 0, f = a.endRow || Number.MAX_VALUE, g = a.startColumn || 0, d = a.endColumn || Number.MAX_VALUE; c && ("string" === typeof c && (c = H.getElementById(c)), [].forEach.call(c.getElementsByTagName("tr"), function (a, c) { c >= e && c <= f && [].forEach.call(a.children, function (a, f) { ("TD" === a.tagName || "TH" === a.tagName) && f >= g && f <= d && (b[f - g] || (b[f - g] = []), b[f - g][c - e] = a.innerHTML) }) }), this.dataFound()); return b }, fetchLiveData: function () {
          function a(k) {
            function n(h,
              n, r) { function p() { g && b.liveDataURL === h && (c.liveDataTimeout = setTimeout(a, m)) } if (!h || 0 !== h.indexOf("http")) return h && e.error && e.error("Invalid URL"), !1; k && (clearTimeout(c.liveDataTimeout), b.liveDataURL = h); d.ajax({ url: h, dataType: r || "json", success: function (a) { b && b.series && n(a); p() }, error: function (a, b) { 3 > ++f && p(); return e.error && e.error(b, a) } }); return !0 } n(h.csvURL, function (a) { b.update({ data: { csv: a } }) }, "text") || n(h.rowsURL, function (a) { b.update({ data: { rows: a } }) }) || n(h.columnsURL, function (a) { b.update({ data: { columns: a } }) })
          }
          var c = this, b = this.chart, e = this.options, f = 0, g = e.enablePolling, m = 1E3 * (e.dataRefreshRate || 2), h = B(e); if (!this.hasURLOption(e)) return !1; 1E3 > m && (m = 1E3); delete e.csvURL; delete e.rowsURL; delete e.columnsURL; a(!0); return this.hasURLOption(e)
        }, parseGoogleSpreadsheet: function () {
          function a(c) {
            var f = ["https://spreadsheets.google.com/feeds/cells", e, g, "public/values?alt=json"].join("/"); d.ajax({
              url: f, dataType: "json", success: function (e) { c(e); b.enablePolling && setTimeout(function () { a(c) }, 1E3 * (b.dataRefreshRate || 2)) },
              error: function (a, c) { return b.error && b.error(c, a) }
            })
          } var c = this, b = this.options, e = b.googleSpreadsheetKey, f = this.chart, g = b.googleSpreadsheetWorksheet || 1, m = b.startRow || 0, h = b.endRow || Number.MAX_VALUE, k = b.startColumn || 0, n = b.endColumn || Number.MAX_VALUE, p = 1E3 * (b.dataRefreshRate || 2); 4E3 > p && (p = 4E3); e && (delete b.googleSpreadsheetKey, a(function (a) {
            var b = []; a = a.feed.entry; var e = (a || []).length, g = 0, d; if (!a || 0 === a.length) return !1; for (d = 0; d < e; d++) { var p = a[d]; g = Math.max(g, p.gs$cell.col) } for (d = 0; d < g; d++)d >= k && d <= n &&
              (b[d - k] = []); for (d = 0; d < e; d++) { p = a[d]; g = p.gs$cell.row - 1; var t = p.gs$cell.col - 1; if (t >= k && t <= n && g >= m && g <= h) { var q = p.gs$cell || p.content; p = null; q.numericValue ? p = 0 <= q.$t.indexOf("/") || 0 <= q.$t.indexOf("-") ? q.$t : 0 < q.$t.indexOf("%") ? 100 * parseFloat(q.numericValue) : parseFloat(q.numericValue) : q.$t && q.$t.length && (p = q.$t); b[t - k][g - m] = p } } b.forEach(function (a) { for (d = 0; d < a.length; d++)"undefined" === typeof a[d] && (a[d] = null) }); f && f.series ? f.update({ data: { columns: b } }) : (c.columns = b, c.dataFound())
          })); return !1
        }, trim: function (a,
          c) { "string" === typeof a && (a = a.replace(/^\s+|\s+$/g, ""), c && /^[0-9\s]+$/.test(a) && (a = a.replace(/\s/g, "")), this.decimalRegex && (a = a.replace(this.decimalRegex, "$1.$2"))); return a }, parseTypes: function () { for (var a = this.columns, c = a.length; c--;)this.parseColumn(a[c], c) }, parseColumn: function (a, c) {
            var b = this.rawColumns, e = this.columns, f = a.length, d = this.firstRowAsNames, m = -1 !== this.valueCount.xColumns.indexOf(c), h, k = [], q = this.chartOptions, p, t = (this.options.columnTypes || [])[c]; q = m && (q && q.xAxis && "category" === D(q.xAxis)[0].type ||
              "string" === t); for (b[c] || (b[c] = []); f--;) {
                var r = k[f] || a[f]; var u = this.trim(r); var v = this.trim(r, !0); var w = parseFloat(v); "undefined" === typeof b[c][f] && (b[c][f] = u); q || 0 === f && d ? a[f] = "" + u : +v === w ? (a[f] = w, 31536E6 < w && "float" !== t ? a.isDatetime = !0 : a.isNumeric = !0, "undefined" !== typeof a[f + 1] && (p = w > a[f + 1])) : (u && u.length && (h = this.parseDate(r)), m && n(h) && "float" !== t ? (k[f] = r, a[f] = h, a.isDatetime = !0, "undefined" !== typeof a[f + 1] && (r = h > a[f + 1], r !== p && "undefined" !== typeof p && (this.alternativeFormat ? (this.dateFormat = this.alternativeFormat,
                  f = a.length, this.alternativeFormat = this.dateFormats[this.dateFormat].alternative) : a.unsorted = !0), p = r)) : (a[f] = "" === u ? null : u, 0 !== f && (a.isDatetime || a.isNumeric) && (a.mixed = !0)))
              } m && a.mixed && (e[c] = b[c]); if (m && p && this.options.sort) for (c = 0; c < e.length; c++)e[c].reverse(), d && e[c].unshift(e[c].pop())
          }, dateFormats: {
            "YYYY/mm/dd": { regex: /^([0-9]{4})[\-\/\.]([0-9]{1,2})[\-\/\.]([0-9]{1,2})$/, parser: function (a) { return Date.UTC(+a[1], a[2] - 1, +a[3]) } }, "dd/mm/YYYY": {
              regex: /^([0-9]{1,2})[\-\/\.]([0-9]{1,2})[\-\/\.]([0-9]{4})$/,
              parser: function (a) { return Date.UTC(+a[3], a[2] - 1, +a[1]) }, alternative: "mm/dd/YYYY"
            }, "mm/dd/YYYY": { regex: /^([0-9]{1,2})[\-\/\.]([0-9]{1,2})[\-\/\.]([0-9]{4})$/, parser: function (a) { return Date.UTC(+a[3], a[1] - 1, +a[2]) } }, "dd/mm/YY": { regex: /^([0-9]{1,2})[\-\/\.]([0-9]{1,2})[\-\/\.]([0-9]{2})$/, parser: function (a) { var c = +a[3]; c = c > (new Date).getFullYear() - 2E3 ? c + 1900 : c + 2E3; return Date.UTC(c, a[2] - 1, +a[1]) }, alternative: "mm/dd/YY" }, "mm/dd/YY": {
              regex: /^([0-9]{1,2})[\-\/\.]([0-9]{1,2})[\-\/\.]([0-9]{2})$/, parser: function (a) {
                return Date.UTC(+a[3] +
                  2E3, a[1] - 1, +a[2])
              }
            }
          }, parseDate: function (a) {
            var c = this.options.parseDate, b, e = this.options.dateFormat || this.dateFormat, f; if (c) var d = c(a); else if ("string" === typeof a) {
              if (e) (c = this.dateFormats[e]) || (c = this.dateFormats["YYYY/mm/dd"]), (f = a.match(c.regex)) && (d = c.parser(f)); else for (b in this.dateFormats) if (c = this.dateFormats[b], f = a.match(c.regex)) { this.dateFormat = b; this.alternativeFormat = c.alternative; d = c.parser(f); break } f || (f = Date.parse(a), "object" === typeof f && null !== f && f.getTime ? d = f.getTime() - 6E4 * f.getTimezoneOffset() :
                n(f) && (d = f - 6E4 * (new Date(f)).getTimezoneOffset()))
            } return d
          }, rowsToColumns: function (a) { var c, b; if (a) { var e = []; var d = a.length; for (c = 0; c < d; c++) { var g = a[c].length; for (b = 0; b < g; b++)e[b] || (e[b] = []), e[b][c] = a[c][b] } } return e }, getData: function () { if (this.columns) return this.rowsToColumns(this.columns).slice(1) }, parsed: function () { if (this.options.parsed) return this.options.parsed.call(this, this.columns) }, getFreeIndexes: function (a, c) {
            var b, e = [], d = []; for (b = 0; b < a; b += 1)e.push(!0); for (a = 0; a < c.length; a += 1) {
              var g =
                c[a].getReferencedColumnIndexes(); for (b = 0; b < g.length; b += 1)e[g[b]] = !1
            } for (b = 0; b < e.length; b += 1)e[b] && d.push(b); return d
          }, complete: function () {
            var a = this.columns, c, b = this.options, e, d, g = []; if (b.complete || b.afterComplete) {
              if (this.firstRowAsNames) for (e = 0; e < a.length; e++)a[e].name = a[e].shift(); var m = []; var h = this.getFreeIndexes(a.length, this.valueCount.seriesBuilders); for (e = 0; e < this.valueCount.seriesBuilders.length; e++) { var k = this.valueCount.seriesBuilders[e]; k.populateColumns(h) && g.push(k) } for (; 0 < h.length;) {
                k =
                new A; k.addColumnReader(0, "x"); e = h.indexOf(0); -1 !== e && h.splice(e, 1); for (e = 0; e < this.valueCount.global; e++)k.addColumnReader(void 0, this.valueCount.globalPointArrayMap[e]); k.populateColumns(h) && g.push(k)
              } 0 < g.length && 0 < g[0].readers.length && (k = a[g[0].readers[0].columnIndex], "undefined" !== typeof k && (k.isDatetime ? c = "datetime" : k.isNumeric || (c = "category"))); if ("category" === c) for (e = 0; e < g.length; e++)for (k = g[e], h = 0; h < k.readers.length; h++)"x" === k.readers[h].configName && (k.readers[h].configName = "name"); for (e =
                0; e < g.length; e++) { k = g[e]; h = []; for (d = 0; d < a[0].length; d++)h[d] = k.read(a, d); m[e] = { data: h }; k.name && (m[e].name = k.name); "category" === c && (m[e].turboThreshold = 0) } a = { series: m }; c && (a.xAxis = { type: c }, "category" === c && (a.xAxis.uniqueNames = !1)); b.complete && b.complete(a); b.afterComplete && b.afterComplete(a)
            }
          }, update: function (a, c) { var b = this.chart; a && (a.afterComplete = function (a) { a && (a.xAxis && b.xAxis[0] && a.xAxis.type === b.xAxis[0].options.type && delete a.xAxis, b.update(a, c, !0)) }, B(!0, b.options.data, a), this.init(b.options.data)) }
      });
    d.Data = C; d.data = function (a, c, b) { return new C(a, c, b) }; x(G, "init", function (a) { var c = this, b = a.args[0] || {}, d = a.args[1]; b && b.data && !c.hasDataDef && (c.hasDataDef = !0, c.data = new C(y(b.data, { afterComplete: function (a) { var e; if (Object.hasOwnProperty.call(b, "series")) if ("object" === typeof b.series) for (e = Math.max(b.series.length, a && a.series ? a.series.length : 0); e--;) { var f = b.series[e] || {}; b.series[e] = B(f, a && a.series ? a.series[e] : {}) } else delete b.series; b = B(a, b); c.init(b, d) } }), b, c), a.preventDefault()) }); var A = function () {
    this.readers =
      []; this.pointIsArray = !0
    }; A.prototype.populateColumns = function (a) { var c = !0; this.readers.forEach(function (b) { "undefined" === typeof b.columnIndex && (b.columnIndex = a.shift()) }); this.readers.forEach(function (a) { "undefined" === typeof a.columnIndex && (c = !1) }); return c }; A.prototype.read = function (a, c) {
      var b = this.pointIsArray, e = b ? [] : {}; this.readers.forEach(function (f) { var g = a[f.columnIndex][c]; b ? e.push(g) : 0 < f.configName.indexOf(".") ? d.Point.prototype.setNestedProperty(e, g, f.configName) : e[f.configName] = g }); if ("undefined" ===
        typeof this.name && 2 <= this.readers.length) { var f = this.getReferencedColumnIndexes(); 2 <= f.length && (f.shift(), f.sort(function (a, b) { return a - b }), this.name = a[f.shift()].name) } return e
    }; A.prototype.addColumnReader = function (a, c) { this.readers.push({ columnIndex: a, configName: c }); "x" !== c && "y" !== c && "undefined" !== typeof c && (this.pointIsArray = !1) }; A.prototype.getReferencedColumnIndexes = function () { var a, c = []; for (a = 0; a < this.readers.length; a += 1) { var b = this.readers[a]; "undefined" !== typeof b.columnIndex && c.push(b.columnIndex) } return c };
    A.prototype.hasReader = function (a) { var c; for (c = 0; c < this.readers.length; c += 1) { var b = this.readers[c]; if (b.configName === a) return !0 } }
  }); v(d, "masters/modules/data.src.js", [], function () { })
});
//# sourceMappingURL=data.js.map