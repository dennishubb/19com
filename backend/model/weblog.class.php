<?php

class weblog {

    public function updateData($data_out, $process_time, $user_id = false) {
        $this->setUserId($user_id);
        $this->setDataOut(json_encode($data_out));
        $this->setTimeTaken($process_time);
        $this->save();
    }

    public function insertData($params, $routes, $userId = 0, $request_method = 'POST') {
        $this->setCommand($routes);
        $this->setDataIn(json_encode($params));
        $this->setIp(get_client_ip());
        $this->setDevice(get_client_device());
		$this->setUserId($userId);
		$this->setRequestMethod($request_method);
		$this->setTimeTaken(0);
        $this->create();
    }

    private function create(){

        $data=array(
            "user_id"=>$this->getUserId(),
            "command"=>$this->getCommand(),
            "data_in"=>$this->getDataIn(),
            "data_out"=>$this->getDataOut(),
            "ip"=>$this->getIp(),
            "device"=>$this->getDevice(),
            "code"=>$this->getCode(),
            "status"=>$this->getStatus(),
            "time_taken"=>$this->getTimeTaken()
        );

        if (!$this->dbc->tableExists('weblog_' . $this->date)) {
            $this->dbc->rawQuery('
                CREATE TABLE IF NOT EXISTS weblog_' . $this->date . ' LIKE weblog
            ');
        }

        $id = $this->dbc->insert('weblog_' . $this->date, $data);

        return $this->load($id);

    }

    public $date="";

    private $dbc=NULL;
    private $id="";
    private $user_id="";
    private $command="";
    private $data_in="";
    private $data_out="";
    private $ip="";
    private $device="";
    private $code="";
    private $status="";
    private $created_at="";
    private $updated_at="";
    private $time_taken="";
	private $request_method="";

    function __construct($dbc=NULL) {
        $this->dbc=$dbc;
        $this->date=date("Ymd");
    }

    public function getId(){
        return $this->id;
    }

    public function setId($id){
        $this->id = $id;
    }

    public function getUserId(){
        return $this->user_id;
    }

    public function setUserId($user_id){
        $this->user_id = $user_id;
    }
	
	public function getRequestMethod(){
        return $this->request_method;
    }

    public function setRequestMethod($request_method){
        $this->request_method = $request_method;
    }

    public function getCommand(){
        return $this->command;
    }

    public function setCommand($command){
        $this->command = $command;
    }

    public function getDataIn(){
        return $this->data_in;
    }

    public function setDataIn($data_in){
        $this->data_in = $data_in;
    }

    public function getDataOut(){
        return $this->data_out;
    }

    public function setDataOut($data_out){
        $this->data_out = $data_out;
    }

    public function getIp(){
        return $this->ip;
    }

    public function setIp($ip){
        $this->ip = $ip;
    }

    public function getDevice(){
        return $this->device;
    }

    public function setDevice($device){
        $this->device = $device;
    }

    public function getCode(){
        return $this->code;
    }

    public function setCode($code){
        $this->code = $code;
    }

    public function getStatus(){
        return $this->status;
    }

    public function setStatus($status){
        $this->status = $status;
    }

    public function getCreatedAt(){
        return $this->created_at;
    }

    public function setCreatedAt($created_at){
        $this->created_at = $created_at;
    }

    public function getUpdatedAt(){
        return $this->updated_at;
    }

    public function setUpdatedAt($updated_at){
        $this->updated_at = $updated_at;
    }

    public function getTimeTaken(){
        return $this->time_taken;
    }

    public function setTimeTaken($time_taken){
        $this->time_taken = $time_taken;
    }

    public function load($id){

        $this->dbc->where('id='.$id);
        $row = $this->dbc->getOne('weblog_' . $this->date);

        if(!empty($row)){

            $this->setId($row['id']);
            $this->setUserId($row['user_id']);
            $this->setCommand($row['command']);
            $this->setDataIn($row['data_in']);
            $this->setDataOut($row['data_out']);
            $this->setIp($row['ip']);
            $this->setDevice($row['device']);
            $this->setCode($row['code']);
            $this->setStatus($row['status']);
            $this->setCreatedAt($row['created_at']);
            $this->setUpdatedAt($row['updated_at']);
            $this->setTimeTaken($row['time_taken']);

            return true;
        }else{
            return false;
        }

    }


    function save(){

        $data = array(
            'user_id'     => $this->getUserId(),
			'request_method' => $this->getRequestMethod(),
            'command'     => $this->getCommand(),
            'data_in'     => $this->getDataIn(),
            'data_out'     => $this->getDataOut(),
            'ip'     => $this->getIp(),
            'device'     => $this->getDevice(),
            'code'     => $this->getCode(),
            'status'     => $this->getStatus(),
            'created_at'     => $this->getCreatedAt(),
            'updated_at'     => $this->getUpdatedAt(),
            'time_taken'     => $this->getTimeTaken()
        );

        $this->dbc->where('id='.$this->getId());
        $this->dbc->update('weblog_' . $this->date,$data);

    }


    public function remove(){

        $this->dbc->where('id='.$this->getId());
        $this->dbc->delete('weblog_' . $this->date);

    }


}