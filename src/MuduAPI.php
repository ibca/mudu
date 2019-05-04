<?php

namespace Ibca\Mudu;

use GuzzleHttp\Client;
use Illuminate\Foundation\Testing\HttpException;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;

/**
 * 目睹直播server API 接口 1.0
 * Class ServerAPI.
 * User: jackyang
 * Date: 19-4-4
 * Time: 上午9:47
 ***/
class MuduAPI
{

    private $http_header;
    private $client;

    /**
     * MuduAPI constructor.
     * @param \Illuminate\Config\Repository|mixed $access_token
     */
    public function __construct($access_token)
    {
        $this->http_header = array(
            "Authorization" => "Bearer ". $access_token,
            "Content-Type" => "application/json"
        );
        $this->client = new Client(['base_uri' => config('mudu.api_url'),'headers' => $this->http_header]);
    }

    /**
     * 获得频道列表
     * api: /v1/activities
     * 可选参数
     * live=直播状态&manager=频道管理员ID&p=页码
     * live    直播状态，1为正在直播    integer
     * manager    频道管理员ID    integer
     * p    页码（每页30条数据）    integer
     * @return $result [返回json]
     */
    public function getActivities($live = '', $manager = '', $p = '')
    {
        $api = '/v1/activities';
        $options = array();
        if ($live !== '') {
            $options = array_merge(['live' => $live], $options);
        } else if ($manager !== '') {
            $options = array_merge(['manager' => $manager], $options);
        } else if ($p !== ''){
            $options = array_merge(['p' => $p], $options);
        }
        $response = $this->client->request('GET', $api,[
            'query' => $options
        ]);
        return  json_decode($response->getBody()->getContents());
    }

    /**
     * 获取指定频道
     * api: /v1/activities/{频道id}
     * @return $result [返回json]
     */
    public function getActivity($id){
        $api = '/v1/activities/'.$id;
        $response = $this->client->request('GET', $api);
        return  json_decode($response->getBody()->getContents());
    }

    /**
     * 创建频道
     * api: /v1/activities
     * name	频道名称	string	是
     * start_time	直播开始时间	datetime	否
     * act_manager_id	频道管理员ID	integer	否
     * @return $result [返回json]
     */
    public function createActivity($name = '',$start_time = '',$act_manager_id = ''){

        $api = '/v1/activities';
        $options = array();
        if ($act_manager_id !== ''){
            $options = array_merge(['act_manager_id' => $act_manager_id], $options);
        }

        if ($start_time !== '') {
            $options = array_merge(['start_time' => $start_time], $options);
        }
        if ($name !== '') {
            $options = array_merge(['name' => $name], $options);
        } else {
            return ['success' => false , 'message' => '频道名称必填'];
        }

        $response = $this->client->request('POST', $api, [
            'body' => json_encode($options)
        ]);

        return  json_decode($response->getBody()->getContents());
    }

    /**
     * 删除频道
     * @param $频道id
     * @return $result [返回json]
     */
    public function deleteActivity ($id = '') {
        $api = '/v1/activities/'. $id;
        if ($id === '') {
            return ['success' => false , 'message' => '频道id:必填'];
        }
        $response = $this->client->request('DELETE', $api);
        return  json_decode($response->getBody()->getContents());

    }

    /**
     * 修改频道信息
     * @param $频道id
     * @return $result [返回json]
     */
    public function updateActivity ($id = '', $name ='') {
        $api = '/v1/activities/' . $id;
        $options = array();
        if ($name !== '') {
            $options = array_merge(['name' => $name], $options);
        } else {
            return ['success' => false , 'message' => '频道名称必填'];
        }
        $response = $this->client->request('PUT', $api, [
            'body' => json_encode($options)
        ]);

        return  json_decode($response->getBody()->getContents());
    }

    /**
     * 修改频道观看页面信息
     * @param $频道id
     * start_time	直播开始时间	datetime	否
     * pc_logo	PC端视频LOGO地址	string	否
     * mobile_logo	移动端视频LOGO地址	string	否
     * banner	banner地址	string	否
     * cover_image	频道图标地址	string	否
     * live_img	直播窗口背景地址	string	否
     * footer	底部版权信息	string	否
     * bg_color	观看页背景色	string	否	请传RGB颜色，如：rgb(255,255,255,1)或十六进制颜色码
     * show_qrcode	是否显示手机观看二维码	bool	否	传true为显示，false为不显示
     *theme	观看页主题	string	否	传default为默认主题，tech为科技版，默认为default
     * @return $result [返回json]
     */
    public function updateActivityPage ($id = '', $start_time = '' , $pc_logo = '',
                                        $mobile_logo = '', $banner = '', $cover_image = '',
                                        $live_img = '', $footer = '', $bg_color = '#FFFFFF',
                                        $show_qrcode = true, $theme = 'default') {
        $api = '/v1/activities/'. $id .'/page';
        if ($id === '') {
            return ['success' => false , 'message' => '频道id:必填'];
        }
        $options = array('start_time' => $start_time, 'pc_logo' => $pc_logo,
            'mobile_logo' => $mobile_logo, 'banner' => $banner,
            'cover_image' => $cover_image, 'live_img' => $live_img,
            'footer' => $footer, 'bg_color' => $bg_color,
            'show_qrcode' => $show_qrcode, 'theme' => $theme);

        $response = $this->client->request('PUT', $api, [
            'body' => json_encode($options)
        ]);
        return  json_decode($response->getBody()->getContents());

    }

    /**
     * 获取频道报表
     * @param $频道id
     * @return $result [返回json]
     */
    public function getActivityReport($id = ''){
        $api = '/v1/activities/' . $id . '/report';
        $response = $this->client->request('GET', $api);
        return  json_decode($response->getBody()->getContents());
    }

    /**
     * 创建管理员
     * 仅企业版可用
     * api: /v1/account/createActManager
     * username	管理员用户名	string	是
     * cost_type	消费上限类型	integer	是	1表示时长限制，2表示流量限制
     * cost_limit	消费上限量	integer	是	当cost_type为1时单位为分钟， cost_type为2时，单位为GB
     * @return $result [返回json]
     */
    public function createManager($username = '' ,$cost_type = '', $cost_limit = ''){
        $api = '/v1/account/createActManager';
        if ($username === '' || $cost_type === '' || $cost_limit === '' ) {
            return ['success' => false , 'message' => '管理员用户名,消费上限类型,消费上限量:必填'];
        }
        $options = array('username' => $username, 'cost_type' => $cost_type ,'cost_limit' => $cost_limit);
        $response = $this->client->request('PUT', $api, [
            'body' => json_encode($options)
        ]);
        return  json_decode($response->getBody()->getContents());

    }

    /**
     * 删除管理员
     * 仅企业版可用
     * api: /v1/account/deleteActManager
     * manager_id	要删除的管理员ID	integer	是
     * is_delete_act	是否删除该管理员创建的频道	integer	是	1表示删除，0表示不删除
     * @param string $is_delete_act
     */
    public function deleteManager($manager_id = '', $is_delete_act = ''){
        $api = '/v1/account/deleteActManager';
        if ($manager_id === '' || $is_delete_act === '') {
            return ['success' => false , 'message' => '要删除的管理员ID,是否删除该管理员创建的频道:必填'];
        }
        $options = array('manager_id' => $manager_id, 'is_delete_act' => $is_delete_act);
        $response = $this->client->request('DELETE', $api, [
            'body' => json_encode($options)
        ]);
        return  json_decode($response->getBody()->getContents());
    }

    /*****
     * 获取评论列表
     * @param $activity_id频道id
     * @param $page页码
     */
    public function getComments($activity_id, $page = 1){
        $api = '/v1/'. $activity_id .'/comments/' . $page;
        if ($activity_id === '') {
            return ['success' => false , 'message' => '频道id:必填'];
        }
        $response = $this->client->request('GET', $api);
        return  json_decode($response->getBody()->getContents());
    }


    /***
     * 删除评论
     * @param $comment_id评论id
     * @return array|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deleteComment($comment_id){
        $api = '/v1/comment/'. $comment_id;
        if ($comment_id === '') {
            return ['success' => false , 'message' => '评论id:必填'];
        }
        $response = $this->client->request('DELETE', $api);
        return  json_decode($response->getBody()->getContents());
    }

    /****
     * 设置置顶评论
     * @param $comment_id评论id
     * @param int $top 1 置顶 0 取消置顶
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function setCommentTop($comment_id, $top = 1){
        $api = '/v1/comment/'. $comment_id .'/top';
        $options = array();
        if ($top !== '') {
            $options = array_merge(['top' => $top], $options);
        }
        $response = $this->client->request('POST', $api, [
            'body' => json_encode($options)
        ]);
        return  json_decode($response->getBody()->getContents());
    }


    /***
     * 观众禁言
     * @param $activity_id频道id
     * @param $visitor_id观众id
     * @param int $mute 1 禁言 0 取消禁言
     * @param $user 观众昵称
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function muteComment($activity_id, $visitor_id, $mute = 1, $user){
        $api = '/v1/'. $activity_id .'/comment/'. $visitor_id .'/mute';
        $options = array();
        if ($mute !== '') {
            $options = array_merge(['mute' => $mute], $options);
        }
        if ($user !== '') {
            $options = array_merge(['user' => $user], $options);
        }
        $response = $this->client->request('POST', $api, [
            'body' => json_encode($options)
        ]);
        return  json_decode($response->getBody()->getContents());
    }


}
