/** @format */
/*jshint esversion: 6 */

(function () {
	var Needle,
		arc,
		arcEndRad,
		arcStartRad,
		barWidth,
		chart,
		chartInset,
		degToRad,
		el,
		endPadRad,
		height,
		i,
		margin,
		needle,
		numSections,
		padRad,
		percentToDeg,
		percentToRad,
		percent,
		radius,
		ref,
		sectionIndx,
		sectionPercent,
		startPadRad,
		svg,
		totalPercent,
		elements,
		windowHeight,
		width;

	percent = initVal / 100;

	barWidth = 40;

	numSections = 5;

	// / 2 for HALF circle
	sectionPercent = 1 / numSections / 2;

	padRad = 0.05;

	chartInset = 10;

	// start at 270deg
	totalPercent = 0.75;

	el = d3.select(".chart-gauge");

	margin = {
		top: 20,
		right: 20,
		bottom: 30,
		left: 20,
	};

	width = el[0][0].offsetWidth - margin.left - margin.right;

	height = width / 2;

	radius = Math.min(width, height);

	percentToDeg = function (percent) {
		return percent * 360;
	};

	percentToRad = function (percent) {
		return degToRad(percentToDeg(percent));
	};

	degToRad = function (deg) {
		return (deg * Math.PI) / 180;
	};

	svg = el
		.append("svg")
		.attr("width", "100%")
		.attr("height", "100%")
		.attr("preserveAspectRatio", "xMinYMin meet");

	chart = svg
		.append("g")
		.attr(
			"transform",
			`translate(${(width + margin.left + margin.right) / 2}, ${
				height + margin.top + margin.bottom - 20
			})`
		);

	// build gauge bg
	for (
		sectionIndx = i = 1, ref = numSections;
		1 <= ref ? i <= ref : i >= ref;
		sectionIndx = 1 <= ref ? ++i : --i
	) {
		arcStartRad = percentToRad(totalPercent);
		arcEndRad = arcStartRad + percentToRad(sectionPercent);
		totalPercent += sectionPercent;
		startPadRad = sectionIndx === 0 ? 0 : padRad / 2;
		endPadRad = sectionIndx === numSections ? 0 : padRad / 2;
		arc = d3.svg
			.arc()
			.outerRadius(radius - chartInset)
			.innerRadius(radius - chartInset - barWidth)
			.startAngle(arcStartRad + startPadRad)
			.endAngle(arcEndRad - endPadRad);
		chart
			.append("path")
			.attr("class", `arc chart-color${sectionIndx}`)
			.attr("d", arc);
	}

	function init() {
		elements = document.querySelectorAll(".scoreGauge");
		windowHeight = window.innerHeight;
	}
	Needle = class Needle {
		constructor(len, radius1) {
			this.len = len;
			this.radius = radius1;
		}

		drawOn(el, percent) {
			el.append("circle")
				.attr("class", "needle-center")
				.attr("cx", 0)
				.attr("cy", 0)
				.attr("r", this.radius);
			return el
				.append("path")
				.attr("class", "needle")
				.attr("d", this.mkCmd(percent));
		}

		animateOn(el, percent) {
			var self;
			self = this;
			return el
				.transition()
				.delay(500)
				.ease("bounce")
				.duration(6000)
				.selectAll(".needle")
				.tween("progress", function () {
					return function (percentOfPercent) {
						var progress;
						progress = percentOfPercent * percent;
						return d3.select(this).attr("d", self.mkCmd(progress));
					};
				});
		}

		mkCmd(percent) {
			var centerX, centerY, leftX, leftY, rightX, rightY, thetaRad, topX, topY;
			thetaRad = percentToRad(percent / 2); // half circle
			centerX = 0;
			centerY = 0;
			topX = centerX - this.len * Math.cos(thetaRad);
			topY = centerY - this.len * Math.sin(thetaRad);
			leftX = centerX - this.radius * Math.cos(thetaRad - Math.PI / 2);
			leftY = centerY - this.radius * Math.sin(thetaRad - Math.PI / 2);
			rightX = centerX - this.radius * Math.cos(thetaRad + Math.PI / 2);
			rightY = centerY - this.radius * Math.sin(thetaRad + Math.PI / 2);
			return `M ${leftX} ${leftY} L ${topX} ${topY} L ${rightX} ${rightY}`;
		}
	};

	// find the netpromoter scoreGauge element and set the height attribute to the height variable
	// this is to make the gauge responsive
	$(".scoreGauge").attr(
		"style",
		`height: ${height + margin.top + margin.bottom}px;`
	);

	needle = new Needle(width / 2 - 10, 10);

	needle.drawOn(chart, 0);

	function checkPosition() {
		for (var i = 0; i < elements.length; i++) {
			var element = elements[i];
			var positionFromTop = elements[i].getBoundingClientRect().top;

			// check if the animation has already been triggered
			if (element.classList.contains("played")) {
			} else {
				if (positionFromTop - windowHeight <= 0) {
					element.classList.add("fade-in-element");
					element.classList.add("in-view");
					element.classList.remove("hidden");

					// animate the needle
					needle.animateOn(chart, percent);
					element.classList.add("played");
				}
			}
		}
	}
	window.addEventListener("scroll", checkPosition);
	window.addEventListener("resize", init);
	init();
	checkPosition();
	// if the window is resized, or zoomed, re-calculate the height and width variables and re-draw the needle
	window.addEventListener("resize", function () {
		init();
		$(".scoreGauge").attr(
			"style",
			`height: ${height + margin.top + margin.bottom}px;`
		);
		width = el[0][0].offsetWidth - margin.left - margin.right;
		height = width / 2;
		radius = Math.min(width, height);
		needle = new Needle(width / 2 - 10, 10);
		needle.drawOn(chart, 0);
		checkPosition();
		needle.animateOn(chart, percent);
	});
	init();
	checkPosition();
}.call(this));

//# sourceURL=coffeescript
