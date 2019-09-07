<template>
	<view class="content">
		<form @submit="formSubmit" @reset="formReset">
			<view class="margin">
				<view class="section_title">input your ID</view>
				<input style="margin-top: 30rpx;" maxlength="8" name="section_title" placeholder="please input here" />
			</view>
			
			<view class="center">
				<button class="okbtn" formType="submit">Sure</button>
			</view>
		</form>
	</view>
</template>

<script>
	export default {
		data() {
			return {

			}
		},
		onLoad() {

		},
		methods: {
			formSubmit(res) {
				let uid = res.detail.value.section_title
				if (!uid) {
					uni.showToast({
						title: 'ID cannot be empty!'
					});
					return false
				} else {
					uni.setStorage({
						key: 'UserId',
						data: uid
					})
					uni.showToast({
						title: 'Your ID is' + uid
					});
					setTimeout(function() {
						uni.navigateTo({
							url: '/pages/index/foodS',
						});
					}, 1000);
				}
				console.log(this.$data)
			},
			// 随机加载食物
			loadFood() {
				var url = 'http://ygc.huiqian99.com/api/index/getGoods'
				let data = {
					id: 1
				}
				uni.request({
					url: url,
					method: 'GET',
					data: {},
					success: res => {
						console.log(res)
					},
					fail: () => {},
					complete: () => {}
				});
			},
			postFood() {
				var url = 'https://www.baidu.com'
				let data = {
					uid: 1
				}
				uni.request({
					url: url,
					method: 'PSOT',
					data: data,
					success: res => {
						console.log(res)
					},
					fail: () => {},
					complete: () => {}
				});
			}
		}
	}
</script>

<style>
	/* 	.content {
		display: flex;
	} */

	.margin {
		margin: 30rpx;
	}

	.center {
		display: flex;
		flex-direction: column;
		justify-content: center;
		text-align: center;
	}

	.okbtn {
		background: #4CD964;
		color: #2C405A;
	}

	.content {
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
	}
</style>
