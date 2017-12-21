(function (width, height, url) {
	var descriptions = '',
	    imgs         = [],
	    img_match    = /http.+png|http.+jpg|http.+gif|http.+svg|http.+jpeg/g,
	    tag_meta     = document.getElementsByTagName('meta'),
	    tag_img      = document.getElementsByTagName('img'),
	    tag_a        = document.getElementsByTagName('a'),
	    idx;
	for (idx = 0; idx < tag_meta.length; idx++) {
	    if ((tag_meta[idx].name.toLowerCase()) === 'description') {
	        descriptions = tag_meta[idx].content
	    }
	}
	for (idx = 0; idx < tag_img.length; idx++) {
	    if (tag_img[idx].src.toLowerCase().indexOf('logo') > 0) {
	        imgs.push(tag_img[idx].src)
	    }
	}
	for (idx = 0; idx < tag_a.length; idx++) {
	    var logo_class_img = tag_a[idx].getElementsByTagName('img');
	    if ((' ' + tag_a[idx].className + ' ').toLowerCase().indexOf('logo') > -1) {
	        if (logo_class_img.length > 0) {
	            imgs.push(logo_class_img[0].src)
	        } else {
	            if (tag_a[idx].currentStyle) {
	                var current_bg_img = tag_a[idx].currentStyle.backgroundImage;
	                var parent_bg_img  = tag_a[idx].parentNode.currentStyle.backgroundImage;
	                if (current_bg_img !== 'none') {
	                    current_bg_img = current_bg_img.match(img_match)[0];
	                    if (current_bg_img !== 'data') {
	                        imgs.push(current_bg_img)
	                    }
	                } else {
	                    if (parent_bg_img !== 'none') {
	                        parent_bg_img = parent_bg_img.match(img_match)[0];
	                        if (parent_bg_img !== 'data') {
	                            imgs.push(parent_bg_img)
	                        }
	                    }
	                }
	            } else {
	                var exec_bg_img        = getComputedStyle(tag_a[idx])['backgroundImage'];
	                var exec_parent_bg_img = getComputedStyle(tag_a[idx].parentNode)['backgroundImage'];
	                if (exec_bg_img !== 'none') {
	                    exec_bg_img = exec_bg_img.match(img_match)[0];
	                    if (exec_bg_img !== 'data') {
	                        imgs.push(exec_bg_img)
	                    }
	                } else {
	                    if (exec_parent_bg_img !== 'none') {
	                        exec_parent_bg_img = exec_parent_bg_img.match(img_match)[0];
	                        if (exec_parent_bg_img !== 'data') {
	                            imgs.push(exec_parent_bg_img)
	                        }
	                    }
	                }
	            }
	        }
	    } else {
	        if ((' ' + tag_a[idx].parentNode.className + ' ').toLowerCase().indexOf('logo') > -1) {
	            if (logo_class_img.length > 0) {
	                imgs.push(logo_class_img[0].src)
	            } else {
	                if (tag_a[idx].currentStyle) {
	                    var parent_bg  = tag_a[idx].parentNode.currentStyle.backgroundImage;
	                    var current_bg = tag_a[idx].currentStyle.backgroundImage;
	                    if (parent_bg !== 'none') {
	                        parent_bg = parent_bg.match(img_match)[0];
	                        if (parent_bg !== 'data') {
	                            imgs.push(parent_bg)
	                        }
	                    } else {
	                        if (current_bg !== 'none') {
	                            current_bg = current_bg.match(img_match)[0];
	                            if (current_bg !== 'data') {
	                                imgs.push(current_bg)
	                            }
	                        }
	                    }
	                } else {
	                    var exec_parent_bg = getComputedStyle(tag_a[idx].parentNode)['backgroundImage'];
	                    var exec_bg        = getComputedStyle(tag_a[idx])['backgroundImage'];
	                    if (exec_parent_bg !== 'none') {
	                        exec_parent_bg = exec_parent_bg.match(img_match)[0];
	                        if (exec_parent_bg !== 'data') {
	                            imgs.push(exec_parent_bg)
	                        }
	                    } else {
	                        if (exec_bg !== 'none') {
	                            exec_bg = exec_bg.match(img_match)[0];
	                            if (exec_bg !== 'data') {
	                                imgs.push(exec_bg)
	                            }
	                        }
	                    }
	                }
	            }
	        }
	    }
	}
	var q = [], g = {};
	for (var s = 0, u; (u = imgs[s]) != null; s++) {
	    if (!g[u]) {
	        q.push(u);
	        g[u] = true
	    }
	}
	var url_open  = url + '?url=' + encodeURIComponent(location.href) + '&title=' + encodeURIComponent(document.title) + '&description=' + encodeURIComponent(descriptions) + '&img_url=' + encodeURIComponent(q);
	var url_param = 'toolbar=0,resizable=1,scrollbars=yes,status=1,width=' + width + ',height=' + height + ',left=' + (screen.width - width) / 2 + ',top=' + (screen.height - height) / 2;
	if (!window.open(url_open, 'wulicode', url_param)) {
	    window.location.href = url_open;
	}
})(700,500, '{!! route('web:nav.url') !!}')