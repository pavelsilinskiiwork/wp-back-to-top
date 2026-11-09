/**
 * Pavel Silinskii Back to Top — settings page live preview.
 * Vanilla JS; jQuery is used only to start the core wp-color-picker.
 */
(function () {
	'use strict';

	var PREFIX = 'psbtt_settings';
	var ARROW = '<svg viewBox="0 0 24 24" aria-hidden="true"><polyline points="18 15 12 9 6 15"></polyline></svg>';

	var form, box, btn, debounceTimer;

	function field(key) {
		return form.elements[PREFIX + '[' + key + ']'];
	}

	function value(key) {
		var el = field(key);
		if (!el) {
			return undefined;
		}
		// RadioNodeList or hidden+checkbox pair.
		if (typeof el.length === 'number' && !el.tagName) {
			var last = el[el.length - 1];
			if (last.type === 'checkbox') {
				return last.checked;
			}
			return el.value;
		}
		if (el.type === 'checkbox') {
			return el.checked;
		}
		return el.value;
	}

	function collectSettings() {
		return {
			position: value('position'),
			shape: value('shape'),
			size: parseInt(value('size'), 10) || 48,
			bottomOffset: parseInt(value('bottom_offset'), 10) || 0,
			sideOffset: parseInt(value('side_offset'), 10) || 0,
			bgColor: value('bg_color'),
			textColor: value('text_color'),
			bgColorHover: value('bg_color_hover'),
			textColorHover: value('text_color_hover'),
			opacity: parseFloat(value('opacity')) || 0.85,
			iconType: value('icon_type'),
			iconText: value('icon_text') || '',
			customSvg: value('custom_svg') || '',
			smoothScroll: value('smooth_scroll'),
			showOn: value('show_on')
		};
	}

	// Parse user SVG and drop anything executable before showing it.
	function safeSvg(markup) {
		if (!markup.trim()) {
			return null;
		}
		var doc = new DOMParser().parseFromString(markup, 'image/svg+xml');
		var svg = doc.documentElement;
		if (!svg || svg.nodeName.toLowerCase() !== 'svg' || doc.getElementsByTagName('parsererror').length) {
			return null;
		}
		var allowed = ['svg', 'g', 'path', 'polyline', 'polygon', 'line', 'circle', 'rect'];
		var all = [svg].concat(Array.prototype.slice.call(svg.querySelectorAll('*')));
		for (var i = 0; i < all.length; i++) {
			var el = all[i];
			if (allowed.indexOf(el.nodeName.toLowerCase()) === -1) {
				el.parentNode && el.parentNode.removeChild(el);
				continue;
			}
			for (var j = el.attributes.length - 1; j >= 0; j--) {
				var name = el.attributes[j].name.toLowerCase();
				if (name.indexOf('on') === 0 || name === 'href' || name === 'xlink:href' || name === 'style') {
					el.removeAttribute(el.attributes[j].name);
				}
			}
		}
		return document.importNode(svg, true);
	}

	function updatePreview(s) {
		var vars = {
			'--psbtt-bg': s.bgColor,
			'--psbtt-text': s.textColor,
			'--psbtt-bg-hover': s.bgColorHover,
			'--psbtt-text-hover': s.textColorHover,
			'--psbtt-size': s.size + 'px',
			'--psbtt-opacity': String(s.opacity),
			// Scale offsets down a little so they fit the small preview box.
			'--psbtt-bottom': Math.min(s.bottomOffset, 150) + 'px',
			'--psbtt-side': Math.min(s.sideOffset, 150) + 'px'
		};
		Object.keys(vars).forEach(function (k) {
			if (vars[k]) {
				box.style.setProperty(k, vars[k]);
			}
		});

		btn.classList.remove('psbtt-shape-circle', 'psbtt-shape-rounded', 'psbtt-shape-square');
		btn.classList.add('psbtt-shape-' + (s.shape || 'circle'));
		btn.classList.toggle('psbtt-pos-left', s.position === 'left');

		btn.textContent = '';
		if (s.iconType === 'text') {
			var span = document.createElement('span');
			span.textContent = s.iconText;
			btn.appendChild(span);
		} else if (s.iconType === 'custom_svg') {
			var svg = safeSvg(s.customSvg);
			if (svg) {
				btn.appendChild(svg);
			} else {
				btn.innerHTML = ARROW;
			}
		} else {
			btn.innerHTML = ARROW;
		}
	}

	function setRowVisible(key, visible) {
		var row = form.querySelector('.psbtt-row-' + key);
		if (row) {
			row.classList.toggle('psbtt-hidden', !visible);
		}
	}

	function updateVisibility(s) {
		setRowVisible('icon_text', s.iconType === 'text');
		setRowVisible('custom_svg', s.iconType === 'custom_svg');
		setRowVisible('scroll_duration', !!s.smoothScroll);
		// Post-type filtering only matters when the button is shown at all.
		setRowVisible('post_types', !!value('enabled'));
	}

	function updateOutputs() {
		form.querySelectorAll('.psbtt-range input[type="range"]').forEach(function (range) {
			var out = range.parentNode.querySelector('output');
			if (out) {
				out.textContent = range.value + (out.getAttribute('data-unit') || '');
			}
		});
	}

	function refresh(immediate) {
		updateOutputs();
		var s = collectSettings();
		updateVisibility(s);
		clearTimeout(debounceTimer);
		if (immediate) {
			updatePreview(s);
		} else {
			debounceTimer = setTimeout(function () {
				updatePreview(s);
			}, 300);
		}
	}

	function initColorPickers() {
		if (!window.jQuery || !window.jQuery.fn.wpColorPicker) {
			return;
		}
		window.jQuery('.psbtt-color').wpColorPicker({
			change: function (event, ui) {
				// The input value updates after this callback; set it first.
				event.target.value = ui.color.toString();
				refresh(false);
			},
			clear: function () {
				setTimeout(function () {
					refresh(false);
				}, 0);
			}
		});
	}

	function init() {
		form = document.getElementById('psbtt-form');
		box = document.getElementById('psbtt-preview-box');
		btn = document.getElementById('psbtt-preview-btn');
		if (!form || !box || !btn) {
			return;
		}

		initColorPickers();

		form.addEventListener('input', function () {
			refresh(false);
		});
		form.addEventListener('change', function () {
			refresh(false);
		});

		refresh(true);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
