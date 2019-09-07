<template>
	<view class="content">
		<text>Result on{{foodname}}</text>
		<view v-for="item in shopList" class="center">
			<view class="title">
				Shop Name:{{item.title}}
			</view>
			<view class="address">
				Shop Address:{{item.address}}
			</view>
			<view class="tel">
				shop Tel:{{item.tel}}
			</view>
		</view>
	</view>
</template>
<script>
	export default {
		data() {
			return {
				shopList:''
			}
		},
		onLoad() {
			var [that, foodname] = [this, uni.getStorageSync('fondNames')]
			this.foodname = uni.getStorageSync('fondNames')
			uni.chooseLocation({
				success: (res) => {
					let loc = {
						name: foodname,
						lat: res.latitude,
						log: res.longitude,
					}
					that.txloc(loc)
				},
				fail: (err) => {
					console.log(err)
				}
			})
		},
		onShow() {

		},
		methods: {
			txloc(e) {
				uni.request({
					url: 'https://apis.map.qq.com/place_cloud/search/nearby', //仅为示例，并非真实接口地址。
					data: {
						location: e.lat + ',' + e.log,
						radius: 10000,
						keyword: e.name,
						table_id: '5d72b082e9071f333939144c',
						key: 'UT2BZ-RTORQ-O2U5E-G22U7-XVXUV-SEFD4'
					},
					header: {
						'responseheader': 'simple response header'
					},
					success: (res) => {
						console.log(res.data.result.data);
						this.shopList = res.data.result.data;
					}
				});
			}
		}
	}
</script>

<style>
</style>
