import promiseFinally from 'promise.prototype.finally';

import mixinAxios from './axios';
import mixinComponent from './component';
import mixinExtension from './extension';
import mixinLocal from './local';
import mixinModule from './module';
import mixinRouter from './router';
import mixinUse from './use';

promiseFinally.shim();

export {
	mixinAxios,
	mixinComponent,
	mixinExtension,
	mixinLocal,
	mixinModule,
	mixinRouter,
	mixinUse,
};