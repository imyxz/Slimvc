<?php
class indexs extends SlimvcController{
    function IndexAction()
    {
        $this->outputJson($this->model("index_model")->showTable("servos"));
    }
}