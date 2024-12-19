<?php
namespace App\Logging;
// use Illuminate\Log\Logger;
use DB;
use Illuminate\Support\Facades\Auth;
use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;
class MySQLLoggingHandler extends AbstractProcessingHandler{
/**
 *
 * Reference:
 * https://github.com/markhilton/monolog-mysql/blob/master/src/Logger/Monolog/Handler/MysqlHandler.php
 */
    public function __construct($level = Logger::DEBUG, $bubble = true) {
        $this->table = 'system_log';
        parent::__construct($level, $bubble);
    }
    protected function write(array $record):void
    {
      $Details = json_decode($record['message']);
       unset($Details->REQUEST_BODY->password); 
       // exit();  
       $data = array(
           'message'       => json_encode($Details),
           'uri'           => $Details->URI,
           'method'        => $Details->METHOD,
           'token'         => $Details->Token,
           'ip'            => $Details->IP,
           'user_id'       => $Details->User_id,
           'email'         => $Details->email,
           'name'          => $Details->first_name.' '.$Details->last_name,
           'request'       => json_encode($Details->REQUEST_BODY),
           'context'       => json_encode($record['context']),
           'level'         => $record['level'],
           'level_name'    => $record['level_name'],
           'channel'       => $record['channel'],
           'record_datetime' => $record['datetime']->format('Y-m-d H:i:s'),
           'extra'         => json_encode($record['extra']),
           'formatted'     => $record['formatted'],
           'remote_addr'   => $_SERVER['REMOTE_ADDR'],
           'user_agent'    => $_SERVER['HTTP_USER_AGENT'],
           'created_at'    => date("Y-m-d H:i:s"),
       );
       DB::connection()->table($this->table)->insert($data);     
    }
}