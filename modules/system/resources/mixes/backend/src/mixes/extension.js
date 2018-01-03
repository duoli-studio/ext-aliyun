export default function(injection) {
	const list = [];
	window.console.log('Loaded Extensions:', injection.extensions);
	Object.keys(injection.extensions).forEach(index => {
		const extension = injection.extensions[index];
		injection.use(extension);
		list.push(extension);
	});

	Object.defineProperties(injection, {
		extensions : {
			get() {
				return list;
			},
		},
	});
}