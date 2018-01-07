<script>
	import {swiper, swiperSlide} from 'vue-awesome-swiper';

	export default {
		components : {
			swiper,
			swiperSlide,
		},
		computed   : {
			swiperOption() {
				const slides = this.slidesPerView;
				const space = this.spaceBetween;
				return {
					paginationClickable : true,
					slidesPerView       : slides,
					spaceBetween        : space,
					observeParents      : true,
					prevButton          : '.swiper-button-prev',
					nextButton          : '.swiper-button-next',
				};
			},
		},
		data() {
			return {
				active      : {},
				activeIndex : 0,
			};
		},
		methods    : {
			switchActive(index) {
				this.activeIndex = index;
				this.active = this.list[index];
			},
		},
		mounted() {
			const [something] = this.list;
			this.active = something;
		},
		props      : {
			auxiliaryList : {
				type     : Array,
				required : true,
			},
			list          : {
				type     : Array,
				required : true,
			},
			slidesPerView : {
				type    : Number,
				default : 6,
			},
			spaceBetween  : {
				type    : Number,
				default : 20,
			},
		},
	};
</script>
<style lang="less">
    .team-wraper {
        color   : #657180;
        padding : 24px 24px 0 24px;
        width   : 100% -80px;
        .team-inner {
            border-bottom  : 1px solid #e3e8ee;
            padding-bottom : 40px;
        }
        .swiper-box {
            padding  : 0 42px;
            position : relative;
        }
        .swiper-container {
            width      : 100%;
            height     : 100%;
            margin     : 0 auto;
            overflow-x : hidden;
        }

        .swiper-wrapper {
            position                    : relative;
            width                       : 100%;
            height                      : 100%;
            z-index                     : 1;
            display                     : -webkit-box;
            display                     : -moz-box;
            display                     : -ms-flexbox;
            display                     : -webkit-flex;
            display                     : flex;
            -webkit-transition-property : -webkit-transform;
            -moz-transition-property    : -moz-transform;
            -o-transition-property      : -o-transform;
            -ms-transition-property     : -ms-transform;
            transition-property         : transform;
            -webkit-box-sizing          : content-box;
            -moz-box-sizing             : content-box;
            box-sizing                  : content-box;
        }

        .swiper-slide {
            -webkit-flex-shrink : 0;
            -ms-flex            : 0 0 auto;
            flex-shrink         : 0;
            width               : 100%;
            height              : 100%;
            position            : relative;
            padding             : 16px 0 15px 0;
            text-align          : center;
            img {
                border-radius : 50%;
                width         : 48px;
                margin        : 0 auto;
            }
            p {
                text-align : center;
                width      : 100%;
                margin     : 0;
                margin-top : 10px;
            }
            &.active-slide {
                background : rgba(0, 0, 0, 0.05);
            }
        }
        .swiper-button-next, .swiper-button-prev {
            position                : absolute;
            top                     : 50%;
            width                   : 12px;
            height                  : 16px;
            margin-top              : -8px;
            z-index                 : 10;
            cursor                  : pointer;
            -moz-background-size    : 27px 44px;
            -webkit-background-size : 27px 44px;
            background-size         : 27px 44px;
            background-position     : center;
            background-repeat       : no-repeat;
        }

        .swiper-button-prev, .swiper-container-rtl .swiper-button-next {
            left          : 0;
            right         : auto;
            width         : 0;
            height        : 0;
            border-top    : 8px solid transparent;
            border-bottom : 8px solid transparent;
            border-right  : 12px solid #3399ff;
            &.swiper-button-disabled {
                border-right : 12px solid #ccc;
            }
        }

        .swiper-button-next, .swiper-container-rtl .swiper-button-prev {
            right         : 0;
            left          : auto;
            width         : 0;
            height        : 0;
            border-top    : 8px solid transparent;
            border-bottom : 8px solid transparent;
            border-left   : 12px solid #3399ff;
            &.swiper-button-disabled {
                border-left : 12px solid #ccc;
            }
        }
        .active-swiper {
            margin-bottom : 40px;
            img {
                float         : left;
                width         : 96px;
                height        : 96px;
                border-radius : 50%;
            }
            &:after,
            &:before {
                display     : table;
                line-height : 0;
                content     : "";
            }
            &::after {
                clear : both;
            }
            .active-info {
                float       : left;
                margin-left : 20px;
                width       : inherit;
                > div {
                    margin : 17px 0 17px 0;
                    span {
                        font-size  : 24px;
                        text-align : left;
                    }
                    a {
                        color       : #3399ff;
                        margin-left : 14px;
                    }
                }
            }
        }
        a {
            color           : #657180;
            text-decoration : none;
            text-align      : left;
        }
        .thanks {
            margin      : 0;
            text-align  : left;
            line-height : 53px;
            a {
                color : #3399ff;
            }
            span:after {
                content : '、';
            }
            span:last-child:after {
                content : '';
            }
            span:nth-last-child(2):after {
                content : '/';
            }
        }
    }
</style>
<template>
    <div class="team-wraper">
        <div class="team-inner">
            <div class="active-swiper">
                <img :src="active.image" alt="">
                <div class="active-info">
                    <div><span>{{ active.name }}</span><a :href="active.url" target="_blank">个人主页</a></div>
                    <a :href="active.url" target="_blank">{{ active.url }}</a>
                </div>
            </div>
            <div class="swiper-box">
                <swiper :options="swiperOption">
                    <swiper-slide v-for="(slide, index) in list"
                                  :key="index"
                                  :class="{'active-slide': activeIndex === index}"
                                  @mouseover.native="switchActive(index)"
                                  @click.native="switchActive(index)">
                        <img :src="slide.image" alt="">
                        <p>{{ slide.name }}</p>
                    </swiper-slide>
                    <div class="swiper-button-prev hidden-xs" slot="button-prev"></div>
                    <div class="swiper-button-next hidden-xs" slot="button-next"></div>
                </swiper>
            </div>
        </div>
        <p class="thanks">
            感谢: <span v-for="item in auxiliaryList"><a :href="item.url" target="_blank"> {{ item.name }} </a></span>
        </p>
    </div>
</template>