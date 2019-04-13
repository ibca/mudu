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
     * 获得频道
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
        $options = array('$manager_id' => $manager_id, '$is_delete_act' => $is_delete_act);
        $response = $this->client->request('DELETE', $api, [
            'body' => json_encode($options)
        ]);
        return  json_decode($response->getBody()->getContents());
    }


}
