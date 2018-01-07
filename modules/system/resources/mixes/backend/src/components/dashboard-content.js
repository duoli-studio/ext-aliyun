export default {
	methods : {
		injection(render, template, injection) {
			const self = this;
			Object.keys(template).forEach(index => {
				const item = template[index];
				if (typeof item === 'string') {
					injection.push(item);
				}
				else {
					const injections = [];
					if (item.content) {
						self.injection(render, item.content, injections);
					}
					injection.push(render(item.tag, {
						attrs : item.attrs ? item.attrs : {},
						props : item.props ? item.props : {},
						style : item.style ? item.style : {},
					}, injections));
				}
			});
		},
	},
	props   : {
		template : Array,
	},
	render(h) {
		const self = this;
		const injections = [];
		self.injection(h, self.template, injections);

		return h('div', injections);
	},
};