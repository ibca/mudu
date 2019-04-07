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
            'Authorization:Bearer ' . $access_token,
            'Content-Type:application/json'
        );
    }

    /**
     * 获得频道列表
     * api: /v1/activities
     * 可选参数
     * live=直播状态&manager=频道管理员ID&p=页码
     * live    直播状态，1为正在直播    integer
     * manager    频道管理员ID    integer
     * p    页码（每页30条数据）    integer
     * @return $result [返回array数组对象]
     */
    public function getActivities($live = '', $manager = '', $p = '')
    {
        $api = '/v1/activities';
        $options = array();
        if ($live !== '') {
            $options = array_merge(['live' => $live], $options);
        }else if ($manager !== '') {
            $options = array_merge(['manager' => $manager], $options);
        }else if ($p !== ''){
            $options = array_merge(['p' => $p], $options);
        }
        return $this->get(config('mudu.api_url') . $api , $options);
    }

    /**
     * 获得频道
     * api: /v1/activities/{频道id}
     * @return $result [返回array数组对象]
     */
    public function getActivity($id){
        $api = '/v1/activities';
        $options = array();
        if ($id !== '') {
            $options = array_merge(['id' => $id], $options);
        }
        return $this->get(config('mudu.api_url') . $api , $options);
    }

    /**
     * 创建频道
     * api: /v1/activities
     * name	频道名称	string	是
     * start_time	直播开始时间	datetime	否
     * act_manager_id	频道管理员ID	integer	否
     * @return $result [返回array数组对象]
     */
    public function createActivity($name = '',$start_time = '',$act_manager_id = ''){
        $api = '/v1/activities';
        $options = array();
        if ($name !== '') {
            $options = array_merge(['name' => $name], $options);
        }else if ($start_time !== '') {
            $options = array_merge(['start_time' => $start_time], $options);
        }else if ($act_manager_id !== ''){
            $options = array_merge(['act_manager_id' => $act_manager_id], $options);
        }
        return $this->post(config('mudu.api_url') . $api , $options);
    }

    /**
     * 获取频道报表
     * @param $频道id
     * @return $result [返回array数组对象]
     */
    public function getActivityReport($id = ''){
        if ($id !== '') {
            $api = '/v1/activities/'.$id.'/report';
            return $this->get(config('mudu.api_url') . $api);
        }else{
            return [ 'success' => false,'errorMessage' => '频道ID不能为空'];
        }
    }

    /**
     * 创建管理员
     * api: /v1/account/createActManager
     * username	管理员用户名	string	是
     * cost_type	消费上限类型	integer	是	1表示时长限制，2表示流量限制
     * cost_limit	消费上限量	integer	是	当cost_type为1时单位为分钟， cost_type为2时，单位为GB
     * @return $result [返回array数组对象]
     */
    public function createManager($username = '' ,$cost_type = '', $cost_limit = ''){
        $api = '/v1/account/createActManager';
        $options = array();
        if ($username !== '') {
            $options = array_merge(['username' => $username], $options);
        }else if ($cost_type !== '') {
            $options = array_merge(['cost_type' => $cost_type], $options);
        }else if ($cost_limit !== ''){
            $options = array_merge(['cost_limit' => $cost_limit], $options);
        }
        return $this->put(config('mudu.api_url') . $api , $options);
    }

    /**
     * 删除管理员
     * api: /v1/account/deleteActManager
     * manager_id	要删除的管理员ID	integer	是
     * is_delete_act	是否删除该管理员创建的频道	integer	是	1表示删除，0表示不删除
     * @param string $is_delete_act
     */
    public function deleteManager($manager_id = '', $is_delete_act = ''){
        $api = '/v1/account/deleteActManager';
        $options = array();
        if ($manager_id !== '') {
            $options = array_merge(['manager_id' => $manager_id], $options);
        }else if ($is_delete_act !== '') {
            $options = array_merge(['is_delete_act' => is_delete_act], $options);
        }
        return $this->delete(config('mudu.api_url') . $api , $options);
    }



    /**
     * Return GuzzleHttp\Client instance.
     *
     * @return \GuzzleHttp\Client
     */
    public function getClient()
    {
        if (!($this->client instanceof Client)) {
            $this->client = new Client(['headers' => $this->http_header]);
        }

        return $this->client;
    }

    /**
     * GET request.
     *
     * @param string $url
     * @param array $options
     *
     * @return ResponseInterface
     *
     * @throws HttpException
     */
    public function get($url, array $options = [])
    {
        return $this->request('GET', $url, ['query' => $options]);
    }

    /**
     * POST request.
     *
     * @param string $url
     * @param array $form
     *
     * @return ResponseInterface
     */
    public function post($url, array $form = [])
    {
        return $this->request('POST', $url, ['form_params' => $form]);
    }

    /**
     * PUT request.
     *
     * @param string $url
     * @param array body
     *
     * @return ResponseInterface
     */
    public function put($url, array $body = [])
    {
        return $this->request('PUT', $url, ['body' => $body]);
    }

    /**
     * DELETE request.
     *
     * @param string $url
     * @param array $json
     *
     * @return ResponseInterface
     */
    public function delete($url, array $json = [])
    {
        return $this->request('DELETE', $url, ['json' => json_encode($json)]);
    }


    /**
     * Make a request.
     *
     * @param string $url
     * @param string $method
     * @param array $options
     *
     * @return ResponseInterface
     */
    public function request($method, $url, $options = [])
    {
        $method = strtoupper($method);

        Log::debug('Client Request:', compact('url', 'method', 'options'));

        $response = $this->getClient()->request($method, $url,$options);

        Log::debug('API response:', [
            'Status' => $response->getStatusCode(),
            'Reason' => $response->getReasonPhrase(),
            'Headers' => $response->getHeaders(),
            'Body' => strval($response->getBody()),
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }


}
