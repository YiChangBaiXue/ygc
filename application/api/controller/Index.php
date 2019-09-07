<?php
namespace app\api\controller;

class Index extends Base
{

   public function getGoods()
   {
        $member_id = input('id');
    
        $member_goods_model = model('member_goods');

        $info = $member_goods_model -> where('member_id',$member_id) -> field('group_concat(goods_id) as con') -> find();
   
        if($info  && $info['con']){
            $goods_member_model = model('goods_member');
            //取出权重 最大的一条商品数据
            $goods_info = $goods_member_model -> where("goods_id in (".$info['con'].")") -> order('sort desc') -> find();
            
            if($goods_info && $goods_info['member_ids']){
                $arr = explode(",",$goods_info['member_ids']);
                if(count($arr) == 1){
                    //数据 只有 当前用户
                    //去除 当前 随机推送一条
                    $info = $this->randData($goods_info['id']);
                }else{
                    //获取商品 绑定会员集合 绑定的 商品ids
                    $goods_ids_list = $member_goods_model -> where('member_id in ('.$goods_info['member_ids'].')') -> field('group_concat( distinct(goods_id)) as con') -> find();
                   
                    if(empty($goods_ids_list['con'])){
                        //随机推送一条
                        $info = $this->randData();
                    }else{
                       
                        $goods_ids_arr = explode(",",$goods_ids_list['con']);
                        $member_goods_ids_arr = explode(",",$info['con']);

                        //求差集
                        $res = array_diff($goods_ids_arr,$member_goods_ids_arr);

                        
                        if(empty($res)){
                            //随机推送一条
                            $info = $this->randData();
                        }else{
                            $count = count($res);

                            $mt_rand = mt_rand(1,$count) -1;
                            $goods_id = $res[$mt_rand];
                            
                            $goods_model = model('goods');        
                       
                            $info = $goods_model -> where('id',$goods_id) -> find();
                        }
                    }
                }
            }else{
                //随机推送一条
                $info = $this->randData();
                
            }


            if( $info ){
                $this->_mess(1,'ok!',$info);
            }else{
                $this->_mess(0,'数据异常!');
            }
           // $this->_mess(1,'ok!',$list);
        }else{

            $info = $this->randData();
            if( $info ){
                $this->_mess(1,'ok!',$info);
            }else{
                $this->_mess(0,'数据异常!');
            }
        }
   }

   protected function randData($id = 0){
        //如果没有喜欢 则 从 商品列表中 随机获取一个 
        $goods_model = model('goods');        
        $total = $goods_model -> count();

        $info = [];
        if( $total ){
            $rang = mt_rand(1,$total);
            if($id && $rang == $id){
                $this-> randData($id);
            }
            $info = $goods_model -> where('id',$rang) -> find();
        }
        return $info;
   }


   //提交 会员是否喜欢菜品
   public function postResult()
   {
        $post = self::$_data;

        if(!$post['goods_id'] || !$post['member_id'])
        {
            $this->_mess(0,'参数异常!');
        }

        $status = $post['status'] ?? 0;

        if($status)
        {
            $this->goodsLike($post['member_id'],$post['goods_id'],1);
        } 
        else 
        {
            $this->goodsDislike($post['member_id'],$post['goods_id'],0);
        }

        $this->_mess(1,'ok!');
   }


   protected function goodsLike($member_id,$goods_id)
   {
        //喜欢
        $member_goods_model = model('member_goods');
        $goods_member_model = model('goods_member');

        $info = $member_goods_model -> where('member_id',$member_id)-> where('goods_id',$goods_id )  -> find();
        if(empty($info)){
            $add__data['member_id'] = $member_id;
            $add__data['goods_id'] = $goods_id;
            $member_goods_model ->allowField(true) ->  save($add__data);
        }
            
        $goods_info = $goods_member_model -> where("goods_id",$goods_id) -> find();
        if($goods_info){
            $member_ids_arr = explode(',',$goods_info['member_ids']);
            if($member_ids_arr){
                if(!in_array($member_id,$member_ids_arr)){
                    $new_arr = $this->addArrVal($member_ids_arr,$member_id);
                    if($new_arr){ 
                        //更新
                        $new_str = implode(',',$new_arr);
                        $up_data['member_ids'] = $new_str;
                        $up_data['sort'] = $goods_info['sort']+1;
                        $res = $goods_member_model -> allowField(true)->save($up_data,['id' => $goods_info['id']]);
                    }else{
                        //直接删除 信息
                        $res = $goods_member_model -> where("id",$goods_info['id']) -> delete();
                    }
                }
            }
        }else{
            $add__data['member_ids'] = $member_id;
            $add__data['goods_id'] = $goods_id;
            $add__data['sort'] = 1;
            $goods_member_model ->allowField(true) ->  save($add__data);
        }
   }


   protected function goodsDislike($member_id,$goods_id)
   {
        //喜欢
        $member_goods_model = model('member_goods');
        $goods_member_model = model('goods_member');

        //不喜欢， 如果没有 相对应的数据 则不变  如果有则删除 相应数据 并权限
        $info = $member_goods_model -> where('member_id',$member_id)-> where('goods_id',$goods_id )  -> find();
        if($info){
            $member_goods_model -> where('member_id',$member_id)-> where('goods_id',$goods_id )  -> delete();
        }

        
        
        $goods_info = $goods_member_model -> where("goods_id",$goods_id) -> find();
        if($goods_info){
            $member_ids_arr = explode(',',$goods_info['member_ids']);
            if($member_ids_arr){
                if(in_array($member_id,$member_ids_arr)){
                    $new_arr = $this->delArrVal($member_ids_arr,$member_id);
                    if($new_arr){ 
                        //有值 更新
                        $new_str = implode(',',$new_arr);

                        $up_data['member_ids'] = $new_str;
                        $up_data['sort'] = $goods_info['sort'] - 1;
                        $res = $goods_member_model -> allowField(true)->save($up_data,['id' => $goods_info['id']]);
                    }else{
                        //直接删除 信息
                        $res = $goods_member_model -> where("id",$goods_info['id']) -> delete();
                    }
                }
            }
        }
   }
}
