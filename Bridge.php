<?php

class Bridge {
    
    private $servicePort = 5700;
    
    private $address = "127.0.0.1";
    
    private $socket;
    
    private function connect() 
    {
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_set_option(
            $this->socket, 
            SOL_SOCKET, 
            SO_RCVTIMEO, 
            array("sec" => 3, "usec" => 0)
        );
        socket_connect($this->socket, $this->address, $this->servicePort);
    }
    
    private function disconnect() 
    {
        socket_close($this->socket);
    }
    
    private function sendCommand(
        $command, 
        $key = null, 
        $value = null, 
        $data = null
    ) {
        $this->connect();
        $this->writeSocket(
            array(
                'command' => $command,
                'key' => $key,
                'value' => $value,
                'data' => $data,
            )
        );
        $jsonResponse = $this->readSocket();
        $this->disconnect();
        
        return $jsonResponse;
    }
    
    private function readSocket()
    {
        $jsonReceive = '';
        $obraces = 0;
        $cbraces = 0;
        do {
            socket_recv($this->socket, $buffer, 1, 0);
            $jsonReceive .= $buffer;
            if ($buffer == '{') {
                $obraces++;
            }
            if ($buffer == '}') {
                $cbraces++;
            }
        } while ($obraces != $cbraces);
        
        return $jsonReceive;
    }
    
    private function writeSocket(array $params)
    {
        $jsonsend = json_encode(
            array_filter(
                $params,
                function($value) {
                    return ($value !== null && $value !== false && $value !== ''); 
                }
            )
        );
        socket_write($this->socket, $jsonsend, strlen($jsonsend));
    }
    
    
    
    public function get($key = null) 
    {
        if ($key !== null) {
            return $this->sendCommand('get', $key);
        }
        $res = $this->sendCommand('get');
        for($i = 0; $i < 10; $i++) {
            $res = $this->sendCommand('get');
            if ($res->{"response"} === 'get') {
                return $res;
            }
        }
        return $this->sendCommand('get');
    }
    
    public function put($key, $value) 
    {
        return $this->sendCommand('put', $key, $value);
    }
    
    public function delete($key) 
    {
        return $this->sendCommand('delete', $key);
    }
}
