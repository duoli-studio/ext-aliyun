function has(browser) {
	const ua = navigator.userAgent;
	if (browser === 'ie') {
		const isIE = ua.indexOf('compatible') > -1 && ua.indexOf('MSIE') > -1;
		if (isIE) {
			const reIE = new RegExp('MSIE (\\d+\\.\\d+);');
			reIE.test(ua);
			return parseFloat(RegExp.$1);
		}
		return false;
	}
	return ua.indexOf(browser) > -1;
}

const file = {
	isIE11() {
		let iev = 0;
		const ieold = (/MSIE (\d+\.\d+);/.test(navigator.userAgent));
		const trident = !!navigator.userAgent.match(/Trident\/7.0/);
		const rv = navigator.userAgent.indexOf('rv:11.0');

		if (ieold) {
			iev = Number(RegExp.$1);
		}
		if (navigator.appVersion.indexOf('MSIE 10') !== -1) {
			iev = 10;
		}
		if (trident && rv !== -1) {
			iev = 11;
		}

		return iev === 11;
	},
	isEdge() {
		return /Edge/.test(navigator.userAgent);
	},
	getDownloadUrl(text, mime) {
		if (window.Blob && window.URL && window.URL.createObjectURL && !has('Safari')) {
			const csvData = new Blob([text], {type : `text/${mime}`});
			return URL.createObjectURL(csvData);
		}
		return `data:attachment/${mime};charset=utf-8,${encodeURIComponent(text)}`;
	},
	download(filename, text, mime) {
		if (has('ie') && has('ie') < 10) {
			// has module unable identify ie11 and Edge
			const oWin = window.top.open('about:blank', '_blank');
			oWin.document.charset = 'utf-8';
			oWin.document.write(text);
			oWin.document.close();
			oWin.document.execCommand('SaveAs', filename);
			oWin.close();
		}
		else if (has('ie') === 10 || this.isIE11() || this.isEdge()) {
			const data = new Blob([text], {type : `text/${mime}`});
			navigator.msSaveBlob(data, filename);
		}
		else {
			const link = document.createElement('a');
			link.download = filename;
			link.href = this.getDownloadUrl(text);
			link.target = '_blank';
			document.body.appendChild(link);
			link.click();
			document.body.removeChild(link);
		}
	},
};

export default file;