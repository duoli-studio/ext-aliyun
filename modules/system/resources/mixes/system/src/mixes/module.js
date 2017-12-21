export default function(injection) {
	const list = [];
	Object.keys(injection.modules).forEach(index => {
		const module = injection.modules[index];
		injection.use(module);
		list.push(module);
	});

	Object.defineProperties(injection, {
		modules : {
			get() {
				return list;
			},
		},
	});
}