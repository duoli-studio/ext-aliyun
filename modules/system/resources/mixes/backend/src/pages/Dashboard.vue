<script>
	import injection, {trans} from '../helpers/injection';

	export default {
		beforeRouteEnter(to, from, next) {
			injection.loading.start();
			injection.http.get(`${window.api}backend/system/layout/dashboards`).then(response => {
				next(vm => {
					vm.dashboards.hidden = response.data.data.hidden;
					vm.dashboards.left = response.data.data.left;
					vm.dashboards.right = response.data.data.right;
					injection.loading.finish();
				});
			}).catch(() => {
				injection.loading.error();
			});
		},
		computed : {
			design : {
				get() {
					return this.$store.state.design;
				},
				set(value) {
					this.$store.commit('design', value);
				},
			},
		},
		data() {
			return {
				dashboards : {
					hidden : [],
					left   : [],
					right  : [],
				},
			};
		},
		methods  : {
			hiddenDashboard(position, index) {
				const self = this;
				switch (position) {
					case 'left':
						self.dashboards.hidden =
							self.dashboards.hidden.concat(self.dashboards.left.splice(index, 1));
						break;
					case 'right':
						self.dashboards.hidden =
							self.dashboards.hidden.concat(self.dashboards.right.splice(index, 1));
						break;
					default:
						break;
				}
			},
			saveDashboard() {
				const self = this;
				injection.http.post(`${window.api}backend/system/layout/dashboards`, self.dashboards).then(() => {
					self.$notice.open({
						title : '保存仪表盘页面布局成功！',
					});
				}).catch(() => {
					self.$notice.error({
						title : '保存仪表盘页面布局失败！',
					});
				}).finally(() => {
					self.design = !self.design;
				});
			},
		},
		mounted() {
			this.$store.commit('title', trans('system.seo.system.system_dashboard'));
		},
		name     : 'Administration-Dashboard',
	};
</script>
<style scoped>
    .design {
        background     : #e3e8ee;
        padding-bottom : 100px;
        padding-top    : 10px;
    }
    .design-wrap {
        bottom   : 0;
        left     : 300px;
        padding  : 40px 20px;
        position : fixed;
        width    : calc(100% - 300px);
    }
    .design-wrap > h3 {
        border-top  : 4px solid #e3e8ee;
        color       : #657180;
        font-size   : 18px;
        height      : 70px;
        line-height : 40px;
        padding-top : 30px;
    }
    .design-wrap > h3 > em {
        color       : rgba(101, 113, 128, 0.5);
        font-size   : 12px;
        font-style  : normal;
        margin-left : 14px;
    }
    .design-wrap > .design-content {
        margin     : 0 -10px;
        min-height : 70px;
    }
    .design-wrap > .design-content > .hidden-drag {
        background    : #fff;
        border-radius : 4px;
        color         : #657180;
        cursor        : move;
        float         : left;
        font-size     : 14px;
        font-weight   : bold;
        height        : 50px;
        line-height   : 50px;
        margin        : 10px;
        padding       : 0 20px;
    }
    .design-wrap > .design-content > .hidden-drag:hover {
        box-shadow : 0 3px 5px 0 rgba(0, 0, 0, 0.05);
    }
    .design-wrap > .design-content > .hidden-drag > span {
        background    : #3399ff;
        border-radius : 4px;
        display       : block;
        float         : left;
        height        : 12px;
        margin-right  : 6px;
        margin-top    : 19px;
        width         : 12px;
    }
</style>
<template>
    <div class="bashboard-wrap">
        <row :gutter="20" :class="{
            design: design
        }">
            <i-col span="12">
                <template v-if="design">
                    <dashboard :options="{ group: 'dashboards' }" v-model="dashboards.left" style="min-height: 100px;">
                        <card :bordered="false" :key="index" v-for="(dashboard, index) in dashboards.left"
                              style="margin-bottom: 20px;">
                            <a slot="extra" @click="hiddenDashboard('left', index)">隐藏</a>
                            <p slot="title">{{ dashboard.title }}</p>
                            <dashboard-content :template="dashboard.template"></dashboard-content>
                        </card>
                    </dashboard>
                </template>
                <template v-else>
                    <card :bordered="false" :key="index" v-for="(dashboard, index) in dashboards.left"
                          style="margin-bottom: 20px;">
                        <p slot="title">{{ dashboard.title }}</p>
                        <dashboard-content :template="dashboard.template"></dashboard-content>
                    </card>
                </template>
            </i-col>
            <i-col span="12">
                <template v-if="design">
                    <dashboard :options="{ group: 'dashboards' }" v-model="dashboards.right" style="min-height: 100px;">
                        <card :bordered="false" :key="index" v-for="(dashboard, index) in dashboards.right"
                              style="margin-bottom: 20px;">
                            <a slot="extra" @click="hiddenDashboard('right', index)">隐藏</a>
                            <p slot="title">{{ dashboard.title }}</p>
                            <dashboard-content :template="dashboard.template"></dashboard-content>
                        </card>
                    </dashboard>
                </template>
                <template v-else>
                    <card :bordered="false" :key="index" v-for="(dashboard, index) in dashboards.right"
                          style="margin-bottom: 20px;">
                        <p slot="title">{{ dashboard.title }}</p>
                        <dashboard-content :template="dashboard.template"></dashboard-content>
                    </card>
                </template>
            </i-col>
        </row>
        <div class="design-wrap" v-if="design">
            <h3>已隐藏模块<em>向上拖动可恢复相应模块</em></h3>
            <dashboard class="design-content" :options="{ group: 'dashboards' }" v-model="dashboards.hidden">
                <div class="hidden-drag" :key="index" v-for="(dashboard, index) in dashboards.hidden"><span></span>{{
                    dashboard.title }}
                </div>
            </dashboard>
        </div>
    </div>
</template>