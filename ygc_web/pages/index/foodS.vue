<template>
	<view class="content">
		<view class="cardS" @tap="goShopList" :id="card.txt">
			<image class="img" :src="card.img" mode="widthFix"></image>
			<view class="txt">
				{{card.name}}
			</view>
		</view>
		<view class="fixed_bottom">
			<view class="round icon-hate" :id="card.id" @tap="hates" />
			<view class="round icon-like" :id="card.id" @tap="likes" />
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				card:{
					id: '1231123',
					img: 'https://ss2.bdstatic.com/70cFvnSh_Q1YnxGkpoWK1HF6hhy/it/u=4162264519,731856317&fm=26&gp=0.jpg',
					name: '麻辣烫'
				}
			}
		},
		onLoad() {
			uni.getStorage({
				key: 'UserId',
				success: (res) => {
					console.log(res)
					this.userid = res.data
					let id = res.data
					uni.request({
						url:'http://ygc.huiqian99.com/api/index/getGoods',
						data:{
							id:id
						},
						success: (e) => {
							// var 
							console.log('this food',e)
						}
					})
				}
			})
		},
		onShow() {
		},
		methods: {
			goShopList(e) {
				console.log(e.currentTarget.id)
				uni.setStorageSync('fondNames', e.currentTarget.id)
				uni.navigateTo({
					url: 'shopList',
				});
			},
			likes(e) {
				console.log('thisId', e.currentTarget.id)
				uni.showToast({
					title: 'yes you like'
				});
				uni.showLoading({
					title: 'Loading',
					mask: true
				});
			},
			hates(e) {
				console.log('thisId', e.currentTarget.id)
				uni.showToast({
					title: 'no you hates'
				});
				uni.showLoading({
					title: 'Loading',
					mask: true
				});
			}
		}
	}
</script>

<style>
	page {
		background: #F1F1F1;
	}

	.cardS {
		background: #fff;
		border-radius: 12rpx;
		width: 80vw;
		margin: 10vw;
		text-align: center;
		justify-content: center;
	}

	.cardS .txt {
		height: 100rpx;
		line-height: 100rpx;
		text-align: center;
		font-size: 36rpx;
	}

	.cardS .img {
		width: 100%;
		border-radius: 12rpx 12rpx 0rpx 0rpx;
	}

	.fixed_bottom {
		width: 100%;
		position: fixed;
		bottom: 100rpx;
		display: flex;
	}

	.fixed_bottom .round {
		width: 100rpx;
		height: 100rpx;
		position: absolute;
		line-height: 100rpx;
		font-size: 60rpx;
		bottom: 88rpx;
		text-align: center;
		justify-content: center;
		border-radius: 50%;
		background: #007AFF;
	}

	.fixed_bottom .round:nth-child(1) {
		left: 230rpx;
		background: #FFFFFF;
		color: #808080;
	}

	.fixed_bottom .round:nth-child(2) {
		right: 230rpx;
		background: #FFFFFF;
		color: #DD524D;
	}
</style>
