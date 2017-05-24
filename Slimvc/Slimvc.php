<?php
/**
 * User: imyxz
 * Date: 2017/5/24
 * Time: 14:18
 * Github: https://github.com/imyxz/
 */
class Slimvc{
    static public function ErrorNotice($info)
    {
        echo $info;
        exit();
    }
}
class SlimvcController{
    public $DB;
    private $models=array();
    public function SlimycController()
    {
        $this->DB=new SlimvcDB();
        global $Config;
        $this->DB->connect($Config);

    }
    public function __set($name,$value)
    {
    }
    public function model($filename,$className=NULL)
    {
        $target=_Model . _DS_ . $filename . '.php';
        if($className==NULL)
            $className=$filename;
        if(!file_exists($target))
            Slimvc::ErrorNotice("Model File $filename not Exist!");
        include_once($target);
        if(!class_exists($className))
            Slimvc::ErrorNotice("Model Class $className not Exist!");
        if(!$this->models[$className])
        {
            $this->models[$className]=new $className;
            $this->models[$className]->Mysqli=$this->DB->mysqli;
        }
        return $this->models[$className];
    }
}
class SlimvcModel{
    public $Mysqli;
    public $InsertId;
    public $Affected;
    public $ResultSum;
    public $QueryStatus;
    public $LastError;
    public $DebugForSQL="";
    public function query($sql)
    {
        global $Config;


        try{
            if(!$this->Mysqli)
                throw new Exception("Connection Faild!");
            $result=mysqli_query($this->Mysqli,$sql);
            $this->InsertId=mysqli_insert_id($this->Mysqli);
            $this->Affected=mysqli_affected_rows($this->Mysqli);
            $this->LastError=mysqli_error($this->Mysqli);
            if($Config['DebugSql'])
                $this->DebugForSQL = $this->DebugForSQL .  "<!-- SQL:$sql ERROR: " . $this->LastError . "\n";
            if($this->LastError)
                throw new Exception("SQL QUERY ERROR:" .  $this->LastError ." #SQL: $sql");
            if(!$result)
                return false;
            else
                return new SlimvcModelResult($result);

        }
        catch(Exception $e){
            $this->_log($e->getMessage(),$e->getFile(),$e->getLine());
            return false;
        }

    }
    public function queryStmt($prepare,$types,...$values)
    {
        global $Config;

        try{
            if(!$this->Mysqli)
                throw new Exception("Connection Faild!");
            if(!$stmt=mysqli_prepare($this->Mysqli,$prepare))
                throw new Exception('STMT SQL PREPARE ERROR:' .$this->LastError . '#prepare: ' . $prepare);
            array_unshift($values,$types);
            if(!call_user_func_array(array($stmt,"bind_param"),$this->refArr($values)))
                throw new Exception('STMT SQL bind ERROR:' .$stmt->error . '#para: ' . var_export($values,true));
            if(!$stmt->execute())
                throw new Exception('STMT SQL EXECUTE ERROR:' . $stmt->error ." #preapare: $prepare #para" . var_export($values,true));
            $this->InsertId=$stmt->insert_id;
            $this->Affected=$stmt->affected_rows;
            if($Config['DebugSql'])
                $this->DebugForSQL = $this->DebugForSQL .  "<!-- SQL:$prepare\n";
            if($result=$stmt->get_result())
                return new SlimvcModelResult($result);
            else
                return true;

        }
        catch(Exception $e){
            $this->_log($e->getMessage(),$e->getFile(),$e->getLine());
            return false;
        }

    }
    protected function _log($info,$filename,$line)
    {
        var_dump($info);
    }
    private function refArr($arr)
    {
        $refs = array();
        foreach($arr as $key => $value)
            $refs[$key] = &$arr[$key];
        return $refs;
    }
}
class SlimvcModelResult{
    public $result;
    public function SlimvcModelResult(mysqli_result &$a)
    {
        $this->result=$a;
    }
    public function row()
    {
        return $this->result->fetch_assoc();
    }
    public function all()
    {
        $return = array();
        while ($row = $this->result->fetch_assoc())
            $return[] = $row;
        return $return;
    }
}
class SlimvcDB{
    public $mysqli;
    public function connect($Config)
    {
        $this->mysqli=mysqli_connect($Config['Host'], $Config['User'], $Config['Password'],$Config['DBname']);
        $CharSet = str_replace('-', '', $Config['CharSet']);
        mysqli_query($this->mysqli, "SET NAMES '$CharSet'");
        return $this->mysqli;
    }
}