<?php
$connect = mysqli_connect('localhost', 'root', '', 'short-links');

if(!$connect) die("Ошибка подключения к БД: \n" . mysqli_connect_error());

$data = json_decode(file_get_contents("php://input"), true);

if(isset($data)){
    if(isset($data['link'])){
        $request = trim($data['link']);
        $request = mysqli_real_escape_string($connect,$request);

        $controller = new Controller;
        $response = $controller->shortLink($connect, $request);

        echo json_encode($response);
    }
    if(isset($data['url'])){
        $request = trim($data['url']);
        $request = mysqli_real_escape_string($connect,$request);

        $controller = new Controller;
        $response = $controller->redirect($connect, $request);

        echo json_encode($response);
    }
}

class Controller {
    public function shortLink ($connect, $request) {
        $search_bool = false;

        while(!$search_bool){
            $short = $this->short_gen();

            $sql = $this->select($connect, 'short', $short);

            if(!mysqli_num_rows($sql)){
                $search_bool = true;
            }
        }

        $sql =  $this->select($connect, 'link',  $request);
        
        if(!mysqli_num_rows($sql) && $search_bool){
            $this->insert($connect, $request, $short);
            $result = $_SERVER['SERVER_NAME'] . '/' . $short;
        }else{
            $row = mysqli_fetch_assoc($sql);
            $result = $_SERVER['SERVER_NAME'] . '/' . $row['short'];
        }

        return $result;
    }

    public function redirect ($connect, $request) {
        $short = substr($request, iconv_strlen($_SERVER['SERVER_NAME']));
    
        if(iconv_strlen($short)){
            $sql = $this->select($connect, 'short', $short);

            $result = null;
            if(mysqli_num_rows($sql)){
                $row = mysqli_fetch_assoc($sql);
                $result = $row['link'];
            }
        }

        return $result;
    }

    private function short_gen(){
        $min = 5;
        $max = 9;
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $arr_chars = str_split($chars);
    
        $short = '';
        $rand_length = mt_rand($min, $max);
    
        for ($i = 0; $i < $rand_length; $i++){
            $short .= $arr_chars[mt_rand(0, sizeof($arr_chars) - 1)];
        }
    
        return $short;
    }

    private function select($connect, $where, $attr){
        $result = mysqli_query($connect, "SELECT * FROM `links` WHERE `".$where."` = '".$attr."'");
    
        return $result;
    }
    
    private function insert($connect, $request, $short){
        $result = mysqli_query($connect, "INSERT INTO `links` (`link`,`short`) VALUES ('".$request."','".$short."')");
    
        return $result;
    }
}
