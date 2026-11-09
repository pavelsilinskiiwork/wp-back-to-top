/**
 * Pavel Silinskii Back to Top — frontend.
 * Vanilla JS, no dependencies.
 */
(function () {
	'use strict';

	var cfg = window.psbttConfig || {};
	var scrollOffset = typeof cfg.scrollOffset === 'number' ? cfg.scrollOffset : 300;
	var smoothScroll = typeof cfg.smoothScroll === 'boolean' ? cfg.smoothScroll : true;
	var scrollDur = typeof cfg.scrollDuration === 'number' ? cfg.scrollDuration : 400;

	var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	function init() {
		var btn = document.getElementById('psbtt-btn');
		if (!btn) {
			return;
		}

		// Remove the inline display:none once; CSS classes control visibility from here.
		btn.style.display = '';

		var ticking = false;
		var visible = null;

		function toggle(show) {
			if (show === visible) {
				return;
			}
			visible = show;
			btn.classList.toggle('psbtt-visible', show);
			// Keep hidden button out of the tab order.
			btn.tabIndex = show ? 0 : -1;
			btn.setAttribute('aria-hidden', show ? 'false' : 'true');
		}

		function onScroll() {
			if (ticking) {
				return;
			}
			ticking = true;
			window.requestAnimationFrame(function () {
				toggle(window.scrollY > scrollOffset);
				ticking = false;
			});
		}

		window.addEventListener('scroll', onScroll, { passive: true });
		onScroll();

		btn.addEventListener('click', function (e) {
			if (!smoothScroll || reduceMotion) {
				window.scrollTo(0, 0);
			} else if ('scrollBehavior' in document.documentElement.style) {
				window.scrollTo({ top: 0, behavior: 'smooth' });
			} else {
				scrollToTopJS(scrollDur);
			}
			// Keyboard activation (Enter/Space reports detail 0): move focus to the top.
			if (e.detail === 0) {
				focusTop();
			}
		});
	}

	// Move keyboard focus to the top of the document after scrolling.
	function focusTop() {
		var target = document.querySelector('.skip-link, #skip-link') || document.body;
		if (target === document.body && !document.body.hasAttribute('tabindex')) {
			document.body.setAttribute('tabindex', '-1');
		}
		target.focus({ preventScroll: true });
	}

	function scrollToTopJS(duration) {
		var start = window.scrollY;
		var startTime = performance.now();

		function step(now) {
			var progress = Math.min((now - startTime) / duration, 1);
			// easeInOutCubic
			var ease = progress < 0.5
				? 4 * progress * progress * progress
				: 1 - Math.pow(-2 * progress + 2, 3) / 2;
			window.scrollTo(0, start * (1 - ease));
			if (progress < 1) {
				window.requestAnimationFrame(step);
			}
		}

		window.requestAnimationFrame(step);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
